<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Cli — Controlador de línea de comandos.
 * SOLO PARA USO ADMINISTRATIVO. No exponer via HTTP en producción.
 *
 * Uso:
 *   php index.php cli issue_test_token <email> [empr_id]
 */
class Cli extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        // Bloquear acceso HTTP — solo CLI
        if (!$this->input->is_cli_request()) {
            show_error('Este endpoint solo está disponible desde CLI.', 403);
        }
    }

    /**
     * Emite un JWT de prueba para un usuario dado.
     *
     * @param string   $email    Email del usuario en seg.users
     * @param int|null $empr_id  ID de empresa (requerido si el usuario tiene >1 membership)
     */
    public function issue_test_token($email = null, $empr_id = null)
    {
        if (empty($email)) {
            fwrite(STDERR, "Uso: php index.php cli issue_test_token <email> [empr_id]\n");
            exit(1);
        }

        $this->load->model('user_model');
        $this->load->library('JwtIssuer');

        // Verificar usuario
        $userInfo = $this->user_model->getUserInfoByEmail($email);
        if (!$userInfo) {
            fwrite(STDERR, "Error: usuario '$email' no encontrado en seg.users\n");
            exit(1);
        }

        // Resolver memberships desde seg.memberships_users
        $this->db->select('group');
        $this->db->where('email', $email);
        $query = $this->db->get('seg.memberships_users');
        $memberships = $query->result_array();

        if (empty($memberships)) {
            fwrite(STDERR, "Error: usuario '$email' no tiene memberships en seg.memberships_users\n");
            exit(1);
        }

        // Resolver empr_id y groupBpm según la cantidad de memberships (decisión P02)
        if (count($memberships) === 1) {
            // P02: un solo membership → autoselección
            $group    = $memberships[0]['group'];
            $parts    = explode('-', $group, 2);
            $empr_id  = (int) $parts[0];
            $groupBpm = $parts[1] ?? $group;
        } else {
            // P02: múltiples memberships → empr_id debe pasarse explícitamente
            if ($empr_id === null) {
                fwrite(STDERR, "Error: el usuario tiene múltiples empresas. Pasar empr_id explícito:\n");
                foreach ($memberships as $m) {
                    $parts = explode('-', $m['group'], 2);
                    fwrite(STDERR, "  empr_id=" . $parts[0] . "  empresa=" . ($parts[1] ?? $m['group']) . "\n");
                }
                exit(1);
            }

            $empr_id_int = (int) $empr_id;
            $groupBpm    = '';
            foreach ($memberships as $m) {
                $parts = explode('-', $m['group'], 2);
                if ((int) $parts[0] === $empr_id_int) {
                    $groupBpm = $parts[1] ?? $m['group'];
                    break;
                }
            }
            if ($groupBpm === '') {
                fwrite(STDERR, "Error: empr_id=$empr_id_int no encontrado en los memberships del usuario\n");
                exit(1);
            }

            $empr_id = $empr_id_int;
        }

        // Resolver userIdBpm desde Bonita
        $this->load->library('BPM');
        $infoUser  = $this->bpm->getUser($userInfo->usernick);
        $userIdBpm = $infoUser['status'] ? ($infoUser['data']['id'] ?? '') : '';

        $userArray = [
            'usernick'  => $userInfo->usernick,
            'email'     => $userInfo->email,
            'role'      => $userInfo->role,
            'userIdBpm' => $userIdBpm,
        ];

        $jwt = $this->jwtissuer->issue($userArray, $empr_id, $groupBpm);

        // Output del JWT a stdout — listo para copiar en curl / Postman
        echo $jwt . PHP_EOL;
        exit(0);
    }
}
