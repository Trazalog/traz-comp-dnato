<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use Firebase\JWT\JWT;

/**
 * Oauth — Authorization Server para el flujo OAuth 2.1 Authorization Code + PKCE.
 *
 * Endpoints:
 *   GET  /oauth/authorize          — Inicio del flujo, redirige a login si no autenticado
 *   POST /oauth/token              — Intercambio de code por JWT
 *   GET  /oauth/.well-known/jwks.json — Clave pública en formato JWKS
 */
class Oauth extends CI_Controller
{
    /** Client ID único registrado para el connector MCP. */
    const ALLOWED_CLIENT_ID = 'trazalog-mcp-connector';

    public function __construct()
    {
        parent::__construct();
        $this->load->model('user_model');
        $this->load->model('OauthCode_model');
        $this->load->library('JwtIssuer');
    }

    // -----------------------------------------------------------------------
    // GET /oauth/authorize
    // -----------------------------------------------------------------------

    /**
     * Endpoint de autorización OAuth 2.1.
     *
     * Parámetros esperados (query string):
     *   client_id              — debe ser ALLOWED_CLIENT_ID
     *   redirect_uri           — URI a la que se redirige con el code
     *   response_type          — debe ser "code"
     *   code_challenge         — PKCE challenge (base64url SHA256 del verifier)
     *   code_challenge_method  — debe ser "S256"
     *   state                  — valor opaco del cliente (se devuelve intacto)
     */
    public function authorize()
    {
        if ($this->input->server('REQUEST_METHOD') !== 'GET') {
            $this->_jsonError('method_not_allowed', 'Only GET is accepted', 405);
            return;
        }

        $client_id             = $this->input->get('client_id');
        $redirect_uri          = $this->input->get('redirect_uri');
        $response_type         = $this->input->get('response_type');
        $code_challenge        = $this->input->get('code_challenge');
        $code_challenge_method = $this->input->get('code_challenge_method');
        $state                 = $this->input->get('state');

        // Validaciones básicas
        if ($client_id !== self::ALLOWED_CLIENT_ID) {
            $this->_jsonError('unauthorized_client', 'client_id no reconocido', 400);
            return;
        }
        if ($response_type !== 'code') {
            $this->_jsonError('unsupported_response_type', 'Solo response_type=code', 400);
            return;
        }
        if ($code_challenge_method !== 'S256') {
            $this->_jsonError('invalid_request', 'Solo code_challenge_method=S256', 400);
            return;
        }
        if (empty($code_challenge) || empty($redirect_uri)) {
            $this->_jsonError('invalid_request', 'code_challenge y redirect_uri son requeridos', 400);
            return;
        }

        // Sin sesión activa → redirigir al login OAuth (E9-IDENT-04)
        $sessionEmail = $this->session->userdata('email');
        if (empty($sessionEmail)) {
            $this->session->set_userdata('oauth_pending', [
                'client_id'             => $client_id,
                'redirect_uri'          => $redirect_uri,
                'code_challenge'        => $code_challenge,
                'code_challenge_method' => $code_challenge_method,
                'state'                 => $state,
            ]);
            redirect(base_url('oauth/login'));
            return;
        }

        $this->_issueCode($sessionEmail, $redirect_uri, $code_challenge, $state);
    }

    /**
     * Llamado desde Main::login() después de una autenticación exitosa cuando
     * hay un flujo OAuth pendiente en sesión.
     * No es un endpoint público — lo llama el controlador Main internamente.
     */
    public function resume_after_login()
    {
        $pending = $this->session->userdata('oauth_pending');
        if (empty($pending)) {
            redirect(base_url());
            return;
        }

        $this->session->unset_userdata('oauth_pending');

        $email         = $this->session->userdata('email');
        $redirect_uri  = $pending['redirect_uri'];
        $code_challenge = $pending['code_challenge'];
        $state          = $pending['state'];

        $this->_issueCode($email, $redirect_uri, $code_challenge, $state);
    }

    // -----------------------------------------------------------------------
    // POST /oauth/token
    // -----------------------------------------------------------------------

    /**
     * Intercambio de authorization code por JWT.
     *
     * Body (application/x-www-form-urlencoded):
     *   grant_type    — "authorization_code"
     *   code          — el code emitido por /authorize
     *   code_verifier — verifier original del cliente (PKCE)
     *   client_id     — debe coincidir con el usado en authorize
     *   redirect_uri  — debe coincidir con la usada en authorize
     */
    public function token()
    {
        if ($this->input->server('REQUEST_METHOD') !== 'POST') {
            $this->_jsonError('method_not_allowed', 'Only POST is accepted', 405);
            return;
        }

        $grant_type    = $this->input->post('grant_type');
        $code          = $this->input->post('code');
        $code_verifier = $this->input->post('code_verifier');
        $client_id     = $this->input->post('client_id');
        $redirect_uri  = $this->input->post('redirect_uri');

        if ($grant_type !== 'authorization_code') {
            $this->_jsonError('unsupported_grant_type', 'Solo grant_type=authorization_code', 400);
            return;
        }
        if ($client_id !== self::ALLOWED_CLIENT_ID) {
            $this->_jsonError('unauthorized_client', 'client_id no reconocido', 400);
            return;
        }
        if (empty($code) || empty($code_verifier) || empty($redirect_uri)) {
            $this->_jsonError('invalid_request', 'code, code_verifier y redirect_uri son requeridos', 400);
            return;
        }

        // Limpiar codes expirados on-the-fly
        $this->OauthCode_model->purgeExpired();

        // Recuperar y consumir el code
        $row = $this->OauthCode_model->consume($code, $redirect_uri);
        if ($row === false) {
            $this->_jsonError('invalid_grant', 'code inválido, expirado o ya usado', 400);
            return;
        }

        // Verificar PKCE: hash(S256) del verifier debe coincidir con el challenge
        $computed_challenge = $this->_base64url_encode(hash('sha256', $code_verifier, true));
        if (!hash_equals($row['code_challenge'], $computed_challenge)) {
            $this->_jsonError('invalid_grant', 'code_verifier incorrecto', 400);
            return;
        }

        // Recuperar datos del usuario para armar el JWT
        $userInfo = $this->user_model->getUserInfoByEmail($row['email']);
        if (!$userInfo) {
            $this->_jsonError('server_error', 'Usuario no encontrado', 500);
            return;
        }

        $userArray = [
            'usernick'  => $userInfo->usernick,
            'email'     => $userInfo->email,
            'role'      => $userInfo->role,
            'userIdBpm' => $row['useridbpm'] ?? '',
        ];

        $jwt = $this->jwtissuer->issue($userArray, (int) $row['empr_id'], $row['groupbpm'] ?? '');

        $this->output
            ->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'access_token' => $jwt,
                'token_type'   => 'Bearer',
                'expires_in'   => 3600,
            ]));
    }

    // -----------------------------------------------------------------------
    // GET /oauth/.well-known/jwks.json
    // -----------------------------------------------------------------------

    /**
     * Devuelve la clave pública en formato JWKS estándar.
     * WSO2 puede apuntar a este endpoint para validar firmas automáticamente.
     */
    public function jwks()
    {
        $this->config->load('jwt', true);
        $pubKeyPem = $this->config->item('jwt_public_key', 'jwt');
        $kid       = $this->config->item('jwt_kid', 'jwt');

        if (empty($pubKeyPem)) {
            $this->_jsonError('server_error', 'Clave pública no disponible', 500);
            return;
        }

        $pubKey = openssl_pkey_get_public($pubKeyPem);
        if (!$pubKey) {
            $this->_jsonError('server_error', 'Error leyendo clave pública', 500);
            return;
        }

        $details = openssl_pkey_get_details($pubKey);
        $rsa     = $details['rsa'];

        $jwk = [
            'kty' => 'RSA',
            'use' => 'sig',
            'alg' => 'RS256',
            'kid' => $kid,
            'n'   => $this->_base64url_encode($rsa['n']),
            'e'   => $this->_base64url_encode($rsa['e']),
        ];

        $this->output
            ->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode(['keys' => [$jwk]]));
    }

    // -----------------------------------------------------------------------
    // Helpers privados
    // -----------------------------------------------------------------------

    private function _issueCode(string $email, string $redirect_uri, string $code_challenge, ?string $state): void
    {
        $empr_id  = (int) $this->session->userdata('empr_id');
        $groupBpm = (string) $this->session->userdata('groupBpm');

        if ($empr_id === 0) {
            $this->_jsonError('server_error', 'empr_id no disponible en sesión', 500);
            return;
        }

        $code      = bin2hex(random_bytes(32));
        $userIdBpm = (string) $this->session->userdata('userIdBpm');

        $this->load->model('OauthCode_model');
        $stored = $this->OauthCode_model->store($code, $email, $empr_id, $code_challenge, $redirect_uri, $userIdBpm, $groupBpm);

        if (!$stored) {
            $this->_jsonError('server_error', 'No se pudo almacenar el authorization code', 500);
            return;
        }

        $redirect = $redirect_uri . '?code=' . urlencode($code);
        if (!empty($state)) {
            $redirect .= '&state=' . urlencode($state);
        }
        redirect($redirect);
    }

    private function _base64url_encode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    private function _jsonError(string $error, string $description, int $status = 400): void
    {
        $this->output
            ->set_status_header($status)
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'error'             => $error,
                'error_description' => $description,
            ]));
    }
}
