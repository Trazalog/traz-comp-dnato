<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * OauthLogin — Pantalla de login compatible con OAuth 2.1 + PKCE.
 *
 * Flujo de autenticación:
 *
 *   GET  /oauth/login             — Formulario de credenciales
 *   POST /oauth/login/credentials — Valida email/password, resuelve empresa y emite code
 *
 * Invariantes:
 *   - 0 empresas → error "sin empresa asignada".
 *   - 1 empresa   → se resuelve sola, sin preguntar.
 *   - >1 empresas → paso 2: el usuario elige (GET/POST /oauth/login/empresa).
 *
 * Sobre TAD-IDENT-02: esa decisión fijaba "un usuario, una empresa" y hacía
 * que más de una fuera un error de configuración. Se revisó el 2026-09-02 —
 * decisión del PM— porque en la práctica hay usuarios que operan para varias
 * empresas, y el login web ya resolvía el caso con una pantalla de selección.
 * Acá se replica ese mismo comportamiento. Lo que NO cambia es que el token
 * queda atado a la empresa elegida: para cambiarla hay que reconectar el
 * cliente, porque /oauth/token todavía no emite refresh_token (ver jwt.php).
 *
 * No modifica Main::login() — los flujos web y OAuth coexisten de forma independiente.
 */
class Oauthlogin extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('User_model', 'user_model');
        $this->load->model('OauthCode_model');
        $this->load->model('Empresas');
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
     * Valida email + password y resuelve la empresa. Con una sola, emite el code
     * derecho; con varias, deriva al paso 2 para que el usuario elija.
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
            $this->_showError('El usuario no tiene empresa asignada en el sistema. Contacte al administrador.');
            return;
        }

        $this->session->set_userdata('oauth_login_state', [
            'email'     => $userInfo->email,
            'usernick'  => $userInfo->usernick,
            'role'      => $userInfo->role,
            'userIdBpm' => $userIdBpm,
        ]);
        $this->session->set_userdata('oauth_memberships', $memberships);

        // Con una sola empresa no hay nada que preguntar: se sigue derecho.
        // Con varias, el usuario elige (paso 2). Antes de esto, mas de una
        // empresa era un error de configuracion; ver la nota de TAD-IDENT-02
        // en la cabecera de esta clase.
        if ($count === 1) {
            $this->_resolveCompany($memberships[0]);
            return;
        }

        redirect(base_url() . 'oauth/login/empresa');
    }

    // -----------------------------------------------------------------------
    // GET / POST /oauth/login/empresa
    // -----------------------------------------------------------------------

    /**
     * Paso 2: el usuario elige con cual de sus empresas autoriza el acceso.
     *
     * GET  muestra las tarjetas. POST valida y emite el authorization code.
     *
     * La lista que se MUESTRA sale de PostgreSQL (getEmpresasDeUsuario trae
     * nombre y logo, que Bonita no tiene), pero la que AUTORIZA es la de
     * Bonita: solo se ofrecen —y solo se aceptan— empresas que esten en
     * oauth_memberships. Si las dos fuentes discrepan, manda Bonita.
     */
    public function empresa()
    {
        $pending     = $this->session->userdata('oauth_pending');
        $loginState  = $this->session->userdata('oauth_login_state');
        $memberships = $this->session->userdata('oauth_memberships');

        if (empty($pending) || empty($loginState) || empty($memberships)) {
            $this->_showError('Sesión expirada. Inicie el proceso nuevamente desde el cliente.');
            return;
        }

        // indice empr_id -> membership de Bonita: es la fuente de autorizacion
        $porEmprId = [];
        foreach ($memberships as $m) {
            $porEmprId[(string) $m['empr_id']] = $m;
        }

        if ($this->input->server('REQUEST_METHOD') === 'POST') {
            $this->_confirmarEmpresa($porEmprId);
            return;
        }

        $todas = $this->user_model->getEmpresasDeUsuario($loginState['email']);

        // Solo las que Bonita autoriza. Si PostgreSQL no devolvio ninguna que
        // coincida, se arman tarjetas con lo que si tenemos de Bonita para no
        // dejar al usuario sin poder elegir.
        $empresas = [];
        foreach ($todas as $e) {
            if (isset($porEmprId[(string) $e->empr_id])) {
                $empresas[] = $e;
            }
        }
        if (empty($empresas)) {
            log_message('ERROR', '#OauthLogin|empresa >> ninguna empresa de PostgreSQL coincide con las membresias de Bonita. email='
                . $loginState['email']);
            foreach ($memberships as $m) {
                $obj = new stdClass();
                $obj->empr_id     = $m['empr_id'];
                $obj->descripcion = $m['groupBpm'];
                $obj->nombre      = $m['groupBpm'];
                $obj->image       = null;
                $obj->imagepath   = null;
                $empresas[] = $obj;
            }
        }

        $logo = $this->_getLogo();

        // form/image/url ya vienen del autoload
        $this->load->view('oauth/login_empresa', [
            'empresas'     => $empresas,
            'csrf_token'   => (string) $this->session->userdata('oauth_csrf'),
            'logoEmpresa'  => $logo,
            'error'        => (string) $this->session->flashdata('oauth_error'),
        ]);
    }

    /**
     * Procesa el POST del paso 2: verifica CSRF y que el empr_id elegido sea
     * una de las empresas que Bonita le reconoce al usuario.
     *
     * @param array $porEmprId  empr_id => membership, armado desde la sesion
     */
    private function _confirmarEmpresa(array $porEmprId)
    {
        $fromForm    = (string) $this->input->post('oauth_csrf');
        $fromSession = (string) $this->session->userdata('oauth_csrf');

        if ($fromForm === '' || $fromSession === '' || !hash_equals($fromSession, $fromForm)) {
            log_message('ERROR', '#OauthLogin|_confirmarEmpresa >> CSRF invalido');
            $this->_showError('La sesión expiró o el formulario no es válido. Iniciá el proceso nuevamente desde el cliente.');
            return;
        }

        $elegida = (string) $this->input->post('empr_id');

        // No alcanza con que el empr_id exista: tiene que estar entre las
        // membresias que Bonita reporto para ESTE usuario en ESTA sesion.
        if ($elegida === '' || !isset($porEmprId[$elegida])) {
            log_message('ERROR', '#OauthLogin|_confirmarEmpresa >> empr_id fuera de las membresias del usuario. elegida=' . $elegida);
            $this->session->set_flashdata('oauth_error', 'La empresa seleccionada no es válida. Elegí una de la lista.');
            redirect(base_url() . 'oauth/login/empresa');
            return;
        }

        $this->_resolveCompany($porEmprId[$elegida]);
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

        // Resolver empr_id_mysql: id nativo en assetv2 (MySQL) para esta empresa.
        // Necesario para que el JWT lleve el ID correcto en cada sistema.
        $emprIdMysql = null;
        $empresa = $this->Empresas->getEmpresaById($emprId);
        if ($empresa && !empty($empresa->empr_id_mysql)) {
            $emprIdMysql = (int) $empresa->empr_id_mysql;
        } else {
            log_message('WARN', '#OauthLogin|_resolveCompany >> empr_id_mysql no disponible para empr_id=' . $emprId . ' — empresa sin mapping asset');
        }

        // Limpiar datos OAuth temporales de sesión antes de redirigir
        $this->session->unset_userdata([
            'oauth_pending',
            'oauth_login_state',
            'oauth_memberships',
            'oauth_csrf',
        ]);

        // PHP 5.6 no tiene random_bytes(). Este es el authorization code de OAuth:
        // si fuera predecible, se pueden robar sesiones, asi que no sirve uniqid()
        // ni mt_rand(). openssl_random_pseudo_bytes es seguro SOLO si $strong vuelve
        // true, por eso la validacion no es opcional.
        $strong  = false;
        $rawCode = openssl_random_pseudo_bytes(32, $strong);
        if ($rawCode === false || !$strong) {
            log_message('ERROR', '#OauthLogin|_resolveCompany >> generacion insegura del authorization code');
            $this->_showError('Error interno al generar el codigo de autorizacion. Intente nuevamente.');
            return;
        }
        $code   = bin2hex($rawCode);
        $stored = $this->OauthCode_model->store($code, $email, $emprId, $codeChallenge, $redirectUri, $userIdBpm, $groupBpm, $emprIdMysql);

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
                . ' code=' . (isset($result['code']) ? $result['code'] : 'N/A'));
            return false;
        }

        $decoded = json_decode($result['data']);
        if (!$decoded || !isset($decoded->payload) || !is_array($decoded->payload)) {
            log_message('ERROR', '#OauthLogin|_getMemberships >> respuesta inesperada: ' . $result['data']);
            return false;
        }

        // El proxy expande group_id como objeto (d=group_id en la query Bonita).
        // Solo se consideran grupos con formato "{empr_id_numerico}-{groupBpm}".
        // Grupos sin prefijo numérico (legacy, pruebas, etc.) se ignoran.
        // Se deduplica por empr_id: un usuario puede tener múltiples roles en el mismo grupo.
        $memberships = [];
        $seen = [];
        foreach ($decoded->payload as $m) {
            $groupObj = isset($m->group_id) && is_object($m->group_id) ? $m->group_id : null;
            $rawName  = $groupObj ? (string) $groupObj->name : '';
            if (!preg_match('/^(\d+)-(.+)$/', $rawName, $parts)) {
                continue; // ignorar grupos sin formato {id}-{nombre}
            }
            $emprId  = $parts[1];
            $groupBpm = $parts[2];
            if (isset($seen[$emprId])) {
                continue; // deduplicar: ya registrada esta empresa
            }
            $seen[$emprId] = true;
            $memberships[] = [
                'key'         => $rawName,
                'empr_id'     => $emprId,
                'groupBpm'    => trim($groupBpm),
                'displayName' => $groupObj && isset($groupObj->displayName) ? $groupObj->displayName : $rawName,
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
            return isset($tabla[0]['valor']) ? $tabla[0]['valor'] : '';
        } catch (Exception $e) {
            return '';
        }
    }
}
