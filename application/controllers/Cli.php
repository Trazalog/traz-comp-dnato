<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Cli — Controlador de línea de comandos.
 * SOLO PARA USO ADMINISTRATIVO. No exponer via HTTP en producción.
 *
 * Uso:
 *   php index.php cli issue_test_token <email> [empr_id]
 *   php index.php cli oauth_cliente_alta <empr_id> "<nombre>"   (ADR-A10)
 *   php index.php cli oauth_cliente_baja <client_id>
 *   php index.php cli oauth_cliente_lista
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
        // 'group' en seg.memberships_users es el nombre de la empresa (descripcion en
        // core.empresas), sin prefijo numérico -- se resuelve por lookup, no por parseo.
        if (count($memberships) === 1) {
            // P02: un solo membership → autoselección
            $groupBpm = $memberships[0]['group'];
            $empresa  = $this->db->select('empr_id')
                                  ->where('descripcion', $groupBpm)
                                  ->get('core.empresas')
                                  ->row();
            if (!$empresa) {
                fwrite(STDERR, "Error: no se encontró empr_id en core.empresas para descripcion='$groupBpm'\n");
                exit(1);
            }
            $empr_id = (int) $empresa->empr_id;
        } else {
            // P02: múltiples memberships → empr_id debe pasarse explícitamente
            if ($empr_id === null) {
                fwrite(STDERR, "Error: el usuario tiene múltiples empresas. Pasar empr_id explícito:\n");
                foreach ($memberships as $m) {
                    $empresa = $this->db->select('empr_id')
                                         ->where('descripcion', $m['group'])
                                         ->get('core.empresas')
                                         ->row();
                    $eid = $empresa ? $empresa->empr_id : '?';
                    fwrite(STDERR, "  empr_id=$eid  empresa=" . $m['group'] . "\n");
                }
                exit(1);
            }

            $empr_id_int = (int) $empr_id;
            $groupBpm    = '';
            foreach ($memberships as $m) {
                $empresa = $this->db->select('empr_id')
                                     ->where('descripcion', $m['group'])
                                     ->get('core.empresas')
                                     ->row();
                if ($empresa && (int) $empresa->empr_id === $empr_id_int) {
                    $groupBpm = $m['group'];
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
        $userIdBpm = $infoUser['status'] ? (isset($infoUser['data']['id']) ? $infoUser['data']['id'] : '') : '';

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

    // -----------------------------------------------------------------------
    // Clientes de maquina de los agentes de IA (ADR-A10)
    // -----------------------------------------------------------------------

    /**
     * Da de alta el cliente de maquina de una empresa e imprime el client_id y
     * el secreto. El secreto se muestra UNA sola vez: Dnato guarda solo su hash.
     */
    public function oauth_cliente_alta($empr_id = null, $nombre = '')
    {
        if (!ctype_digit((string) $empr_id) || (int) $empr_id <= 0) {
            fwrite(STDERR, "Uso: php index.php cli oauth_cliente_alta <empr_id> \"<nombre>\"\n");
            exit(1);
        }

        $this->load->model('Empresas');
        $empresa = $this->Empresas->getEmpresaById((int) $empr_id);
        if (!$empresa) {
            fwrite(STDERR, "Error: la empresa $empr_id no existe (o no se pudo consultar core)\n");
            exit(1);
        }

        $this->load->model('OauthClient_model');
        $alta = $this->OauthClient_model->alta((int) $empr_id, $nombre);
        if ($alta === false) {
            fwrite(STDERR, "Error: no se pudo guardar el cliente (ver log)\n");
            exit(1);
        }

        echo "Cliente de maquina creado para la empresa $empr_id\n";
        echo "client_id: " . $alta['client_id'] . "\n";
        echo "secreto:   " . $alta['secreto'] . "\n";
        echo "\nGuardalo ahora: no se vuelve a mostrar. Se carga en el agente con\n";
        echo "  python -m agente.credenciales alta --empresa $empr_id --client-id " . $alta['client_id'] . "\n";
    }

    /** Revoca el cliente: desde ese momento no obtiene mas tokens. */
    public function oauth_cliente_baja($client_id = null)
    {
        if (empty($client_id)) {
            fwrite(STDERR, "Uso: php index.php cli oauth_cliente_baja <client_id>\n");
            exit(1);
        }
        $this->load->model('OauthClient_model');
        if (!$this->OauthClient_model->baja($client_id)) {
            fwrite(STDERR, "Error: el cliente $client_id no existe\n");
            exit(1);
        }
        echo "Cliente $client_id revocado\n";
    }

    /** Lista los clientes de maquina (sin secretos). */
    public function oauth_cliente_lista()
    {
        $this->load->model('OauthClient_model');
        foreach ($this->OauthClient_model->lista() as $c) {
            echo implode("\t", array(
                $c['client_id'], 'empr_id=' . $c['empr_id'], $c['activo'] === 't' || $c['activo'] === true ? 'activo' : 'revocado',
                'alta=' . $c['fec_alta'], 'ultimo_uso=' . ($c['fec_ultimo_uso'] ? $c['fec_ultimo_uso'] : '-'), $c['nombre'],
            )) . "\n";
        }
    }
}
