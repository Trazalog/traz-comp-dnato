<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Clientes de máquina para grant_type=client_credentials (ADR-A10).
 *
 * Un cliente por empresa con los agentes de IA habilitados. El empr_id del
 * token sale de esta tabla, nunca del pedido. El secreto solo existe en claro
 * en el momento del alta: acá se guarda su hash.
 *
 * Migración: doc/identity/migrations/002_create_seg_oauth_clients.sql
 */
class OauthClient_model extends CI_Model
{
    const TABLE = 'seg.oauth_clients';

    /**
     * Hash con el que se compara cuando el client_id no existe, para que la
     * respuesta tarde lo mismo exista o no (no revela qué client_id son válidos).
     */
    const HASH_FALSO = '$2y$10$fiBxKtIshaJzO9vG95voiuycpgcjOEVVgpPFbyPp/51CqN7ds654a';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Verifica el par client_id / secreto. Devuelve la fila si es un cliente
     * activo con el secreto correcto; false en cualquier otro caso.
     *
     * @return array|false
     */
    public function autenticar($client_id, $secreto)
    {
        $fila = false;
        if (is_string($client_id) && $client_id !== '' && strlen($client_id) <= 64) {
            $this->db->where('client_id', $client_id);
            $this->db->where('activo', true);
            $query = $this->db->get(self::TABLE);
            if ($query && $query->num_rows() === 1) {
                $fila = $query->row_array();
            }
        }

        $hash = $fila ? $fila['secret_hash'] : self::HASH_FALSO;
        $ok   = password_verify((string) $secreto, $hash);

        return ($fila && $ok) ? $fila : false;
    }

    /** Registra la última emisión de token del cliente. */
    public function marcarUso($client_id)
    {
        $this->db->where('client_id', $client_id);
        $this->db->update(self::TABLE, array('fec_ultimo_uso' => date('Y-m-d H:i:s')));
    }

    /**
     * Da de alta un cliente para una empresa. Devuelve el client_id y el
     * secreto en claro: es la única vez que existe, hay que mostrarlo y
     * entregarlo en el momento.
     *
     * @return array|false  array('client_id' => ..., 'secreto' => ...)
     */
    public function alta($empr_id, $nombre)
    {
        $client_id = 'maq-' . bin2hex(openssl_random_pseudo_bytes(12));
        $secreto   = bin2hex(openssl_random_pseudo_bytes(32));

        $ok = $this->db->insert(self::TABLE, array(
            'client_id'   => $client_id,
            'secret_hash' => password_hash($secreto, PASSWORD_BCRYPT),
            'empr_id'     => (int) $empr_id,
            'nombre'      => (string) $nombre,
            'activo'      => true,
        ));

        return $ok ? array('client_id' => $client_id, 'secreto' => $secreto) : false;
    }

    /** Revoca un cliente: desde ese momento no obtiene más tokens. */
    public function baja($client_id)
    {
        $this->db->where('client_id', $client_id);
        $this->db->update(self::TABLE, array('activo' => false));
        return $this->db->affected_rows() > 0;
    }

    /** Todos los clientes, sin el hash. */
    public function lista()
    {
        $this->db->select('client_id, empr_id, nombre, activo, fec_alta, fec_ultimo_uso');
        $this->db->order_by('empr_id, fec_alta');
        $query = $this->db->get(self::TABLE);
        return $query ? $query->result_array() : array();
    }
}
