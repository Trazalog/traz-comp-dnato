<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * OauthLogin — Pantalla de login compatible con OAuth 2.1 + PKCE.
 *
 * Implementa el flujo de autenticación en dos pasos para que Claude pueda
 * obtener un authorization code mediante el flujo PKCE:
 *
 *   GET  /oauth/login                  — Paso 1: formulario de credenciales
 *   POST /oauth/login/credentials      — Valida email/password, consulta membresías
 *   GET  /oauth/login/select-company   — Paso 2: dropdown de empresa (si N > 1)
 *   POST /oauth/login/select-company   — Confirma empresa y emite el authorization code
 *
 * No modifica Main::login() — los flujos web y OAuth coexisten de forma independiente.
 */
class OauthLogin extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('User_model', 'user_model');
        $this->load->model('OauthCode_model');
        $this->load->model('Tablas');
        $this->load->config('oauth_clients', true);
    }

    // -----------------------------------------------------------------------
    // GET /oauth/login
    // -----------------------------------------------------------------------

    /**
     * Paso 1 — muestra el formulario de credenciales.
     *
     * Acepta parámetros OAuth vía query string (cuando Claude redirige aquí
     * directamente) o los lee de oauth_pending en sesión (cuando viene de
     * Oauth::authorize()).
     */
    public function index()
    {
        $pending = $this->_resolvePendingParams();
        if ($pending === false) return;

        $this->session->set_userdata('oauth_pending', $pending);

        $csrf = bin2hex(openssl_random_pseudo_bytes(16));
        $this->session->set_userdata('oauth_csrf', $csrf);

        $clients    = $this->config->item('oauth_clients', 'oauth_clients') ?: [];
        $clientName = isset($clients[$pending['client_id']]['display_name']) ? $clients[$pending['client_id']]['display_name'] : $pending['client_id'];

        $data = [
            'client_name'  => $clientName,
            'csrf_token'   => $csrf,
            'error'        => $this->session->flashdata('oauth_error'),
            'logo_empresa' => $this->_getLogo(),
        ];

        $this->load->view('oauth/login_step1', $data);
    }

    // -----------------------------------------------------------------------
    // POST /oauth/login/credentials
    // -----------------------------------------------------------------------

    /**
     * Valida email + password con checkLogin(), luego consulta membresías vía
     * el proxy WSO2 y bifurca según el número de membresías:
     *   0  → error explícito
     *   1  → autoselección → emite el authorization code
     *   >1 → redirige a /oauth/login/select-company
     */
    public function credentials()
    {
        if ($this->input->server('REQUEST_METHOD') !== 'POST') {
            redirect(base_url('oauth/login'));
            return;
        }

        if (!$this->_checkCsrf()) {
            $this->session->set_flashdata('oauth_error', 'Token de seguridad inválido. Recargue e intente nuevamente.');
            redirect(base_url('oauth/login'));
            return;
        }

        $pending = $this->session->userdata('oauth_pending');
        if (empty($pending)) {
            $this->_showError('Sesión OAuth expirada. Inicie el proceso nuevamente desde el cliente.');
            return;
        }

        $email    = trim($this->security->xss_clean($this->input->post('email')));
        $password = $this->input->post('password');

        if (empty($email) || empty($password)) {
            $this->session->set_flashdata('oauth_error', 'Email y contraseña son requeridos.');
            redirect(base_url('oauth/login'));
            return;
        }

        $userInfo = $this->user_model->checkLogin(['email' => $email, 'password' => $password]);

        if (!$userInfo) {
            $this->session->set_flashdata('oauth_error', 'Correo o contraseña incorrectos.');
            redirect(base_url('oauth/login'));
            return;
        }

        if ($userInfo->banned_users === 'ban') {
            $this->session->set_flashdata('oauth_error', 'Su cuenta está temporalmente inhabilitada en este sistema.');
            redirect(base_url('oauth/login'));
            return;
        }

        // Obtener userIdBpm (BPM está autoloaded como $this->bpm)
        $bpmResult = $this->bpm->getUser($userInfo->usernick);
        if (!$bpmResult || !$bpmResult['status']) {
            log_message('ERROR', '#OauthLogin|credentials >> BPM::getUser falló para usernick=' . $userInfo->usernick);
            $this->_showError('Error al conectar con el sistema de procesos. Contacte al administrador.');
            return;
        }
        $userIdBpm = (string) $bpmResult['data']['id'];

        // Consultar membresías vía proxy WSO2 (decisión P03 — Sección 6.8)
        $memberships = $this->_getMemberships($userIdBpm);
        if ($memberships === false) {
            $this->_showError('Error al consultar membresías. Contacte al administrador.');
            return;
        }

        $count = count($memberships);

        if ($count === 0) {
            $this->_showError('El usuario no tiene empresa asignada en el sistema.');
            return;
        }

        // Guardar estado de login parcial (necesario para paso 2 o emisión directa)
        $this->session->set_userdata('oauth_login_state', [
            'email'     => $userInfo->email,
            'usernick'  => $userInfo->usernick,
            'role'      => $userInfo->role,
            'userIdBpm' => $userIdBpm,
        ]);
        $this->session->set_userdata('oauth_memberships', $memberships);

        if ($count === 1) {
            $this->_resolveCompany($memberships[0]);
        } else {
            redirect(base_url('oauth/login/select-company'));
        }
    }

    // -----------------------------------------------------------------------
    // GET + POST /oauth/login/select-company
    // -----------------------------------------------------------------------

    /**
     * Paso 2 — selección de empresa cuando el usuario tiene más de una membresía.
     *
     * GET:  muestra el dropdown de empresas.
     * POST: valida la selección (contra la lista en sesión + chekEmpresa en DB)
     *       y emite el authorization code.
     */
    public function select_company()
    {
        $loginState = $this->session->userdata('oauth_login_state');
        if (empty($loginState)) {
            redirect(base_url('oauth/login'));
            return;
        }

        if ($this->input->server('REQUEST_METHOD') === 'GET') {
            $memberships = $this->session->userdata('oauth_memberships') ?: [];

            $csrf = bin2hex(openssl_random_pseudo_bytes(16));
            $this->session->set_userdata('oauth_csrf', $csrf);

            $data = [
                'memberships'  => $memberships,
                'csrf_token'   => $csrf,
                'error'        => $this->session->flashdata('oauth_error'),
                'logo_empresa' => $this->_getLogo(),
            ];
            $this->load->view('oauth/login_step2', $data);
            return;
        }

        // POST
        if (!$this->_checkCsrf()) {
            $this->session->set_flashdata('oauth_error', 'Token de seguridad inválido.');
            redirect(base_url('oauth/login/select-company'));
            return;
        }

        $selectedKey = $this->security->xss_clean($this->input->post('empr_id'));
        if (empty($selectedKey)) {
            $this->session->set_flashdata('oauth_error', 'Debe seleccionar una empresa.');
            redirect(base_url('oauth/login/select-company'));
            return;
        }

        // Verificar que el key seleccionado está en la lista de sesión (anti-tampering)
        $memberships = $this->session->userdata('oauth_memberships') ?: [];
        $found = null;
        foreach ($memberships as $m) {
            if ($m['key'] === $selectedKey) {
                $found = $m;
                break;
            }
        }

        if ($found === null) {
            $this->session->set_flashdata('oauth_error', 'Empresa no válida. Seleccione una de la lista.');
            redirect(base_url('oauth/login/select-company'));
            return;
        }

        // Validación extra en BD (chekEmpresa confirma membresía real)
        if (!$this->user_model->chekEmpresa($found['groupBpm'], $loginState['email'])) {
            $this->session->set_flashdata('oauth_error', 'El usuario no corresponde a la empresa seleccionada.');
            redirect(base_url('oauth/login/select-company'));
            return;
        }

        $this->_resolveCompany($found);
    }

    // -----------------------------------------------------------------------
    // Helpers privados
    // -----------------------------------------------------------------------

    /**
     * Lee los parámetros OAuth desde query string o desde oauth_pending en sesión.
     * Valida client_id y redirect_uri contra la whitelist de oauth_clients.php.
     *
     * @return array|false  Parámetros OAuth validados, o false si hay error.
     */
    private function _resolvePendingParams()
    {
        $clients = $this->config->item('oauth_clients', 'oauth_clients') ?: [];

        $clientId = $this->input->get('client_id');

        // Sin params en URL → leer de sesión (viene de Oauth::authorize())
        if (empty($clientId)) {
            $existing = $this->session->userdata('oauth_pending');
            if (!empty($existing) && isset($clients[$existing['client_id']])) {
                return $existing;
            }
            $this->_showError('Parámetros OAuth no encontrados. Inicie el flujo desde el cliente.');
            return false;
        }

        // Params vía query string → validar
        $redirectUri         = $this->input->get('redirect_uri');
        $responseType        = $this->input->get('response_type');
        $codeChallenge       = $this->input->get('code_challenge');
        $codeChallengeMethod = $this->input->get('code_challenge_method');
        $state               = $this->input->get('state');

        if (!isset($clients[$clientId])) {
            $this->_showError('Cliente OAuth no reconocido.');
            return false;
        }
        if ($responseType !== 'code') {
            $this->_showError('response_type no soportado. Solo se acepta "code".');
            return false;
        }
        if ($codeChallengeMethod !== 'S256') {
            $this->_showError('Solo se acepta code_challenge_method=S256.');
            return false;
        }
        if (empty($codeChallenge) || empty($redirectUri)) {
            $this->_showError('Parámetros OAuth incompletos (code_challenge y redirect_uri son requeridos).');
            return false;
        }

        $allowedUris = isset($clients[$clientId]['redirect_uris']) ? $clients[$clientId]['redirect_uris'] : [];
        if (!in_array($redirectUri, $allowedUris, true)) {
            $this->_showError('redirect_uri no autorizado para este cliente.');
            return false;
        }

        return [
            'client_id'             => $clientId,
            'redirect_uri'          => $redirectUri,
            'code_challenge'        => $codeChallenge,
            'code_challenge_method' => $codeChallengeMethod,
            'state'                 => $state,
        ];
    }

    /**
     * Emite el authorization code y redirige al redirect_uri del cliente.
     * Limpia todos los datos temporales de sesión OAuth.
     */
    private function _resolveCompany(array $membership)
    {
        $pending    = $this->session->userdata('oauth_pending');
        $loginState = $this->session->userdata('oauth_login_state');

        if (empty($pending) || empty($loginState)) {
            $this->_showError('Sesión expirada. Inicie el proceso nuevamente desde el cliente.');
            return;
        }

        $email         = $loginState['email'];
        $emprId        = (int) $membership['empr_id'];
        $groupBpm      = $membership['groupBpm'];
        $userIdBpm     = $loginState['userIdBpm'];
        $codeChallenge = $pending['code_challenge'];
        $redirectUri   = $pending['redirect_uri'];
        $state         = isset($pending['state']) ? $pending['state'] : '';

        // Limpiar datos OAuth temporales de sesión antes de redirigir
        $this->session->unset_userdata([
            'oauth_pending',
            'oauth_login_state',
            'oauth_memberships',
            'oauth_csrf',
        ]);

        $code   = bin2hex(random_bytes(32));
        $stored = $this->OauthCode_model->store($code, $email, $emprId, $codeChallenge, $redirectUri, $userIdBpm, $groupBpm);

        if (!$stored) {
            log_message('ERROR', '#OauthLogin|_resolveCompany >> OauthCode_model::store falló para email=' . $email);
            $this->_showError('Error interno al generar el código de autorización. Intente nuevamente.');
            return;
        }

        log_message('INFO', '#OauthLogin|_resolveCompany >> code emitido para email=' . $email . ' empr_id=' . $emprId);

        $destination = $redirectUri . '?code=' . urlencode($code);
        if (!empty($state)) {
            $destination .= '&state=' . urlencode($state);
        }
        redirect($destination);
    }

    /**
     * Consulta las membresías del usuario vía el proxy WSO2 MI (decisión P03).
     *
     * URL: REST_BPM/memberships/xUserid/{userIdBpm}/session/dd
     *
     * @param  string       $userIdBpm  ID del usuario en Bonita.
     * @return array|false  Lista de membresías parseadas, o false si hay error.
     */
    private function _getMemberships($userIdBpm)
    {
        $url    = REST_BPM . '/memberships/xUserid/' . rawurlencode($userIdBpm) . '/session/dd';
        $result = $this->rest->callAPI('GET', $url);

        if (!$result || !$result['status'] || empty($result['data'])) {
            log_message('ERROR', '#OauthLogin|_getMemberships >> callAPI falló. userIdBpm=' . $userIdBpm
                . ' code=' . ($result['code'] ?? 'N/A'));
            return false;
        }

        $decoded = json_decode($result['data']);
        if (!$decoded || !isset($decoded->payload) || !is_array($decoded->payload)) {
            log_message('ERROR', '#OauthLogin|_getMemberships >> respuesta inesperada: ' . $result['data']);
            return false;
        }

        $memberships = [];
        foreach ($decoded->payload as $m) {
            $rawName = isset($m->name) ? $m->name : '';
            if (strpos($rawName, '-') !== false) {
                list($emprId, $groupBpm) = explode('-', $rawName, 2);
            } else {
                $emprId   = $rawName;
                $groupBpm = $rawName;
            }
            $memberships[] = [
                'key'         => $rawName,
                'empr_id'     => trim($emprId),
                'groupBpm'    => trim($groupBpm),
                'displayName' => isset($m->displayName) ? $m->displayName : $rawName,
            ];
        }

        return $memberships;
    }

    /**
     * Verifica el CSRF token del formulario contra el almacenado en sesión.
     */
    private function _checkCsrf()
    {
        $fromForm    = $this->input->post('oauth_csrf');
        $fromSession = $this->session->userdata('oauth_csrf');
        if (empty($fromForm) || empty($fromSession)) return false;
        return hash_equals($fromSession, $fromForm);
    }

    /**
     * Muestra la vista de error genérico (sin redirect).
     */
    private function _showError($message)
    {
        $this->load->view('oauth/login_error', ['error_message' => $message]);
    }

    /**
     * Obtiene la URL del logo de empresa desde la tabla de configuraciones UI.
     */
    private function _getLogo()
    {
        try {
            $tabla = $this->Tablas->obtenerTabla('configuraciones_ui');
            return $tabla[0]['valor'] ?? '';
        } catch (Exception $e) {
            return '';
        }
    }
}
