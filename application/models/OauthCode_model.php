<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class OauthCode_model extends CI_Model
{
    const CODE_TTL_SECONDS = 60;
    const TABLE = 'seg.oauth_codes';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Almacena un authorization code de un solo uso.
     *
     * @param string $code           Código aleatorio (hex)
     * @param string $email          Email del usuario autenticado
     * @param int    $empr_id        ID de empresa resuelta
     * @param string $code_challenge PKCE challenge (S256)
     * @param string $redirect_uri   URI de redirección registrada
     * @param string $useridbpm      ID del usuario en Bonita
     * @param string $groupbpm       Nombre del grupo Bonita
     * @return bool
     */
    public function store($code, $email, $empr_id, $code_challenge, $redirect_uri, $useridbpm = '', $groupbpm = '', $empr_id_mysql = null)
    {
        $data = [
            'id_code'        => $code,
            'email'          => $email,
            'empr_id'        => $empr_id,
            'empr_id_mysql'  => $empr_id_mysql,
            'code_challenge' => $code_challenge,
            'redirect_uri'   => $redirect_uri,
            'useridbpm'      => $useridbpm,
            'groupbpm'       => $groupbpm,
            'created_at'     => date('Y-m-d H:i:s'),
        ];
        return (bool) $this->db->insert(self::TABLE, $data);
    }

    /**
     * Consume un code: retorna los datos si es válido (no usado, no expirado)
     * y lo marca como usado en un solo paso para prevenir race conditions.
     *
     * @param string $code          Código a consumir
     * @param string $redirect_uri  URI de redirección (debe coincidir)
     * @return array|false
     */
    public function consume($code, $redirect_uri)
    {
        $this->db->where('id_code', $code);
        $this->db->where('redirect_uri', $redirect_uri);
        $this->db->where('used_at IS NULL', null, false);
        $query = $this->db->get(self::TABLE);

        if ($query->num_rows() === 0) {
            return false;
        }

        $row = $query->row_array();

        // Verificar expiración
        $age = time() - strtotime($row['created_at']);
        if ($age > self::CODE_TTL_SECONDS) {
            return false;
        }

        // Marcar como usado
        $this->db->where('id_code', $code);
        $this->db->update(self::TABLE, ['used_at' => date('Y-m-d H:i:s')]);

        return $row;
    }

    /**
     * Elimina codes expirados y usados (limpieza on-the-fly).
     */
    public function purgeExpired()
    {
        $cutoff = date('Y-m-d H:i:s', time() - self::CODE_TTL_SECONDS * 10);
        $this->db->where('created_at <', $cutoff);
        $this->db->delete(self::TABLE);
    }
}
