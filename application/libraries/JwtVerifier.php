<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

/**
 * JwtVerifier — valida un JWT emitido por este mismo Dnato (JwtIssuer).
 *
 * Contraparte de JwtIssuer: misma clave (jwt_public_key), mismo algoritmo,
 * mismo emisor y misma audiencia de config/jwt.php. Lo usan las entradas que
 * no tienen sesión de pantalla y reciben el token del usuario (por ejemplo, la
 * Carga Masiva que dispara un agente con el token del administrador).
 *
 * Compatible con PHP 5.6 (composer.json fija esa plataforma) y php-jwt 5.5.
 *
 * AGENTE-CO-09 / ADR-A12.
 */
class JwtVerifier
{
    private $CI;
    private $publicKey;
    private $algorithm;
    private $issuer;
    private $audience;

    public function __construct()
    {
        $this->CI = &get_instance();
        $this->CI->config->load('jwt', TRUE);

        $this->publicKey = (string) $this->CI->config->item('jwt_public_key', 'jwt');
        $this->algorithm = (string) $this->CI->config->item('jwt_algorithm', 'jwt');
        $this->issuer    = (string) $this->CI->config->item('jwt_issuer', 'jwt');
        $this->audience  = (string) $this->CI->config->item('jwt_audience', 'jwt');
    }

    /**
     * Devuelve los claims del token si es válido; si no, lanza Exception con
     * un motivo apto para el log (nunca se devuelve al cliente tal cual).
     *
     * Valida: firma, vencimiento (lo hace php-jwt), emisor y audiencia.
     *
     * @param  string $jwt
     * @return array
     * @throws Exception
     */
    public function verificar($jwt)
    {
        if ($this->publicKey === '') {
            throw new Exception('jwt_public_key no configurada');
        }
        if (!is_string($jwt) || substr_count($jwt, '.') !== 2) {
            throw new Exception('token con formato invalido');
        }

        JWT::$leeway = 30;
        $claims = (array) JWT::decode($jwt, new Key($this->publicKey, $this->algorithm));

        if ($this->issuer !== '' && (!isset($claims['iss']) || $claims['iss'] !== $this->issuer)) {
            throw new Exception('emisor inesperado');
        }
        $aud = isset($claims['aud']) ? $claims['aud'] : null;
        $auds = is_array($aud) ? $aud : array($aud);
        if ($this->audience !== '' && !in_array($this->audience, $auds, true)) {
            throw new Exception('audiencia inesperada');
        }
        return $claims;
    }

    /**
     * El token del header Authorization ("Bearer <jwt>"), o '' si no hay.
     *
     * @return string
     */
    public function tokenDelPedido()
    {
        $h = (string) $this->CI->input->get_request_header('Authorization', TRUE);
        if ($h === '' && isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
            // Apache con CGI/FastCGI deja el header acá.
            $h = (string) $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
        }
        if (stripos($h, 'Bearer ') !== 0) {
            return '';
        }
        return trim(substr($h, 7));
    }
}
