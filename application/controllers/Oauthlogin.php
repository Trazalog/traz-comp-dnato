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
 * Sobre la alineacion con el login web (2026-10-24, decision del PM):
 * las empresas que se ofrecen salen de getEmpresasDeUsuario(), la MISMA
 * consulta que usa Main::login(). Antes la lista la decidia Bonita —se pedian
 * las membresias del usuario y se intersectaban con PostgreSQL—, con tres
 * efectos que el login web no tiene y que no dejaban rastro:
 *
 *   1. un grupo de Bonita sin el prefijo "{empr_id}-" se descartaba en silencio;
 *   2. una empresa en Bonita sin fila en PostgreSQL se perdia, porque el
 *      fallback solo actuaba si la lista quedaba VACIA del todo;
 *   3. con Bonita caido no se podia entrar.
 *
 * Bonita y PostgreSQL no estan sincronizados y depurarlos no es viable hoy, asi
 * que se eligio que manden los datos de Tools. Contrapartida aceptada: las dos
 * tools que instancian procesos (man_create_ot, alm_crear_pedido_materiales)
 * pueden fallar si el usuario no es miembro de ese grupo en Bonita. Es
 * preferible a no poder entrar.
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

        // userIdBpm: viaja como claim del token y lo necesitan las dos tools que
        // instancian procesos en Bonita. NO es bloqueante: si Bonita no responde,
        // el login sigue y el claim queda vacio. Decision del PM (2026-10-24): es
        // preferible entrar y que fallen esas dos tools, a no poder entrar.
        $userIdBpm = '';
        $bpmResult = $this->bpm->getUser($userInfo->usernick);
        if ($bpmResult && !empty($bpmResult['status']) && isset($bpmResult['data']['id'])) {
            $userIdBpm = (string) $bpmResult['data']['id'];
        } else {
            log_message('WARN', '#OauthLogin|credentials >> BPM::getUser no resolvio userIdBpm para usernick='
                . $userInfo->usernick . ' — el token sale con el claim vacio y las tools que instancian procesos van a fallar');
        }

        // Las empresas salen de la MISMA fuente que el login web (Main::login):
        // seg.memberships_users JOIN core.empresas. Bonita ya no decide que se
        // ofrece — ver la nota "Sobre la alineacion con el login web" arriba.
        $empresas = $this->user_model->getEmpresasDeUsuario($userInfo->email);
        $this->_logGruposHuerfanos($userInfo->email);

        $count = count($empresas);

        if ($count === 0) {
            log_message('ERROR', '#OauthLogin|credentials >> usuario sin empresa resoluble. email=' . $userInfo->email);
            $this->_showError('El usuario no tiene empresa asignada en el sistema. Contacte al administrador.');
            return;
        }

        $this->session->set_userdata('oauth_login_state', [
            'email'     => $userInfo->email,
            'usernick'  => $userInfo->usernick,
            'role'      => $userInfo->role,
            'userIdBpm' => $userIdBpm,
        ]);

        // Con una sola empresa no hay nada que preguntar: se sigue derecho.
        // Con varias, el usuario elige (paso 2). Antes de esto, mas de una
        // empresa era un error de configuracion; ver la nota de TAD-IDENT-02
        // en la cabecera de esta clase.
        if ($count === 1) {
            $this->_resolveCompany($empresas[0]);
            return;
        }

        log_message('INFO', '#OauthLogin|credentials >> usuario con ' . $count . ' empresas, va a seleccion. email=' . $userInfo->email);
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
     * La lista sale de getEmpresasDeUsuario(), la MISMA fuente y la misma
     * consulta que usa el login web (Main::seleccionar_empresa). Se reconsulta
     * en cada request en vez de leerla de sesion, para que una sesion
     * manipulada no pueda agregar empresas.
     */
    public function empresa()
    {
        $pending     = $this->session->userdata('oauth_pending');
        $loginState  = $this->session->userdata('oauth_login_state');

        if (empty($pending) || empty($loginState)) {
            $this->_showError('Sesión expirada. Inicie el proceso nuevamente desde el cliente.');
            return;
        }

        $empresas = $this->user_model->getEmpresasDeUsuario($loginState['email']);

        if (empty($empresas)) {
            $this->_showError('El usuario no tiene empresa asignada en el sistema. Contacte al administrador.');
            return;
        }

        if ($this->input->server('REQUEST_METHOD') === 'POST') {
            $this->_confirmarEmpresa($empresas);
            return;
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
     * una de las empresas del usuario.
     *
     * @param array $empresas  Filas de getEmpresasDeUsuario(), reconsultadas
     *                         en este mismo request — no vienen de la sesion.
     */
    private function _confirmarEmpresa(array $empresas)
    {
        $fromForm    = (string) $this->input->post('oauth_csrf');
        $fromSession = (string) $this->session->userdata('oauth_csrf');

        if ($fromForm === '' || $fromSession === '' || !hash_equals($fromSession, $fromForm)) {
            log_message('ERROR', '#OauthLogin|_confirmarEmpresa >> CSRF invalido');
            $this->_showError('La sesión expiró o el formulario no es válido. Iniciá el proceso nuevamente desde el cliente.');
            return;
        }

        $elegida = (string) $this->input->post('empr_id');

        // No alcanza con que el empr_id exista: tiene que ser una de las
        // empresas que la consulta devolvio para ESTE usuario, recien ahora.
        $empresa = null;
        foreach ($empresas as $e) {
            if ((string) $e->empr_id === $elegida) {
                $empresa = $e;
                break;
            }
        }

        if ($elegida === '' || $empresa === null) {
            log_message('ERROR', '#OauthLogin|_confirmarEmpresa >> empr_id fuera de las empresas del usuario. elegida=' . $elegida);
            $this->session->set_flashdata('oauth_error', 'La empresa seleccionada no es válida. Elegí una de la lista.');
            redirect(base_url() . 'oauth/login/empresa');
            return;
        }

        $this->_resolveCompany($empresa);
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
    private function _resolveCompany($empresa)
    {
        $pending    = $this->session->userdata('oauth_pending');
        $loginState = $this->session->userdata('oauth_login_state');

        if (empty($pending) || empty($loginState)) {
            $this->_showError('Sesión expirada. Inicie el proceso nuevamente desde el cliente.');
            return;
        }

        $email         = $loginState['email'];
        $emprId        = (int) $empresa->empr_id;
        $groupBpm      = $this->_groupBpmDe($empresa);
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
     * Nombre del grupo BPM de una empresa, para el claim groupBpm del token.
     *
     * Sale de seg.memberships_users."group" (la columna `grupo` que devuelve
     * getEmpresasDeUsuario), que es el mismo string con el que el login web
     * identifica a la empresa. Si viniera vacio se cae a descripcion y despues
     * a nombre, que son los dos campos contra los que esa consulta hace el join.
     *
     * @param  object $empresa  Fila de getEmpresasDeUsuario()
     * @return string
     */
    private function _groupBpmDe($empresa)
    {
        foreach (['grupo', 'descripcion', 'nombre'] as $campo) {
            if (isset($empresa->$campo) && trim((string) $empresa->$campo) !== '') {
                return trim((string) $empresa->$campo);
            }
        }
        log_message('WARN', '#OauthLogin|_groupBpmDe >> sin nombre de grupo para empr_id='
            . (isset($empresa->empr_id) ? $empresa->empr_id : '?'));
        return '';
    }

    /**
     * Deja rastro de las membresias que no resuelven a ninguna empresa.
     *
     * Son las que el usuario tiene en seg.memberships_users pero cuyo "group"
     * no coincide con el nombre ni la descripcion de ninguna empresa vigente:
     * no se le ofrecen, y sin esto desaparecerian sin dejar registro. Mismo
     * mecanismo que Main::_logGruposHuerfanos() en el login web.
     */
    private function _logGruposHuerfanos($email)
    {
        $huerfanos = $this->user_model->gruposSinEmpresa($email);
        if (!empty($huerfanos)) {
            log_message('ERROR', '#OauthLogin|credentials >> membresias sin empresa en core.empresas (no se ofrecen al usuario). email='
                . $email . ' grupos=' . implode(' | ', $huerfanos));
        }
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
