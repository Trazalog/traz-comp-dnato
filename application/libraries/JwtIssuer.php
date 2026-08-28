<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use Firebase\JWT\JWT;

class JwtIssuer
{
    private $CI;
    private $privateKey;
    private $algorithm;
    private $issuer;
    private $audience;
    private $ttl;
    private $kid;
    private $azp;

    public function __construct()
    {
        $this->CI = &get_instance();
        $this->CI->config->load('jwt', true);

        $this->privateKey = (string) $this->CI->config->item('jwt_private_key', 'jwt');
        $this->algorithm  = (string) $this->CI->config->item('jwt_algorithm', 'jwt');
        $this->issuer     = (string) $this->CI->config->item('jwt_issuer', 'jwt');
        $this->audience   = (string) $this->CI->config->item('jwt_audience', 'jwt');
        $this->ttl        = (int)    $this->CI->config->item('jwt_ttl', 'jwt');
        $this->kid        = (string) $this->CI->config->item('jwt_kid', 'jwt');
        $this->azp        = (string) $this->CI->config->item('jwt_azp', 'jwt');

        if (empty($this->privateKey)) {
            log_message('ERROR', '#JwtIssuer | Clave privada no configurada (JWT_PRIVATE_KEY_PATH)');
            show_error('JWT: clave privada no disponible', 500);
        }
    }

    /**
     * Emite un JWT firmado con RS256 conteniendo el claim empr_id.
     *
     * @param array  $userInfo  Objeto plano con campos del usuario (de checkLogin/sesión)
     * @param int    $empr_id   ID de empresa resuelta en el login
     * @param string $groupBpm  Nombre del grupo Bonita (empresa sin prefijo numérico)
     * @return string JWT firmado
     */
    public function issue(array $userInfo, $empr_id, $groupBpm, $empr_id_mysql = null)
    {
        $now = time();

        $payload = [
            'iss'          => $this->issuer,
            'aud'          => $this->audience,
            'iat'          => $now,
            'exp'          => $now + $this->ttl,
            'sub'          => isset($userInfo['usernick'])  ? $userInfo['usernick']  : '',
            'azp'          => $this->azp,
            'email'        => isset($userInfo['email'])     ? $userInfo['email']     : '',
            'empr_id'      => (string) $empr_id,
            'empr_id_mysql'=> $empr_id_mysql !== null ? (string) $empr_id_mysql : '',
            'role'         => isset($userInfo['role'])      ? $userInfo['role']      : '',
            'userIdBpm'    => isset($userInfo['userIdBpm']) ? $userInfo['userIdBpm'] : '',
            'groupBpm'     => $groupBpm,
        ];

        $headers = ['kid' => $this->kid];

        return JWT::encode($payload, $this->privateKey, $this->algorithm, null, $headers);
    }
}
