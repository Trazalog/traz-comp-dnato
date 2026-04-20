<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Register extends CI_Controller {

    private $bpmGroupsCache = null;
    private $bpmRolesCache = null;

	function __construct(){
		parent::__construct();
		$this->load->model('User_model', 'user_model', TRUE);
		$this->load->model('Empresas');
		$this->load->model('Roles');
		$this->load->library('form_validation');
		$this->form_validation->set_error_delimiters('<div class="error">', '</div>');
		$this->load->library('bpm');
	}

	public function register_success()
	{
		try {
			require_once(APPPATH . 'modules/traz-comp-formularios/helpers/form_helper.php');
			require_once(APPPATH . 'modules/traz-comp-formularios/models/Forms.php');
			$Forms = new Forms();

			$this->aplicarEmprIdTemporalRegistro();

			$instancia = $Forms->generarInstancia(FORMULARIO_REGISTRO_ID);
			$info_id = isset($instancia['info_id']) ? $instancia['info_id'] : null;

			if ( ! $info_id) {
				log_message('error', '#TRAZA|REGISTER|register_success() >> generarInstancia no devolvió info_id');
				$this->limpiarEmprIdTemporalRegistro();
				$this->session->set_flashdata(
					'flash_message',
					'No se pudo preparar el formulario de registro. Volvé a iniciar sesión o contactá soporte.'
				);
				redirect(base_url() . 'main/');
				return;
			}

			$this->session->set_userdata('temp_info_id', $info_id);

			$data['title'] = "Registro Exitoso";
			$data['form_id'] = FORMULARIO_REGISTRO_ID;
			$data['info_id'] = $info_id;

			$this->load->view('header', $data);
			$this->load->view('formulario_page', $data);
			$this->load->view('footer');

			$this->limpiarEmprIdTemporalRegistro();
		} catch (Throwable $e) {
			$this->limpiarEmprIdTemporalRegistro();
			log_message('error', '#TRAZA|REGISTER|register_success() >> ' . $e->getMessage() . ' | ' . $e->getFile() . ':' . $e->getLine());
			$this->session->set_flashdata(
				'flash_message',
				'Ocurrió un error al cargar el siguiente paso del registro. Intentá de nuevo o contactá soporte.'
			);
			redirect(base_url() . 'main/');
		}
	}

	/**
	 * Setea un empr_id temporal en sesión durante el flujo de registro freemium,
	 * cuando el usuario aún no tiene una empresa real creada. Es requerido porque
	 * Forms::obtenerValores() usa empresa() para resolver las opciones en core.tablas.
	 * Solo se aplica si no hay una empresa real ya asociada a la sesión.
	 */
	private function aplicarEmprIdTemporalRegistro()
	{
		$emprIdActual = $this->session->userdata('empr_id');
		if (empty($emprIdActual) || (int) $emprIdActual === (int) REGISTER_TEMP_EMPR_ID) {
			$this->session->set_userdata('empr_id', REGISTER_TEMP_EMPR_ID);
			$this->session->set_userdata('empr_id_temporal_registro', true);
		}
	}

	/**
	 * Elimina el empr_id temporal únicamente si fue aplicado por el flujo de registro,
	 * para evitar contaminar la sesión del usuario más allá del formulario de registro.
	 */
	private function limpiarEmprIdTemporalRegistro()
	{
		if ($this->session->userdata('empr_id_temporal_registro')) {
			$this->session->unset_userdata('empr_id');
			$this->session->unset_userdata('empr_id_temporal_registro');
		}
	}

	/**
	 * Devuelve la descripción del país a partir del tabl_id guardado en seg.users.reg_pais_id.
	 *
	 * El CORE resuelve estados con `tabla = concat('estados_paises', :pais)` y localidades con
	 * `tabla = concat('localidades_estados_paises', :pais, :estado)`, donde `:pais` es el nombre
	 * legible del país (ej. 'Argentina'), que en core.tablas corresponde a la columna `descripcion`
	 * (NO a `valor`, que trae el código tipo 'AR'). Por eso acá devolvemos `descripcion`.
	 *
	 * @param string|null $reg_pais_id tabl_id en core.tablas (p. ej. 'paises_registracionAR')
	 * @return string descripción del país o cadena vacía si no se encuentra
	 */
	private function obtenerNombrePaisRegistroUsuario($reg_pais_id)
	{
		if ($reg_pais_id === null || $reg_pais_id === '') {
			return '';
		}
		$this->db->select('descripcion');
		$this->db->from('core.tablas');
		$this->db->where('tabl_id', $reg_pais_id);
		$this->db->where('tabla', 'paises_registracion');
		$q = $this->db->get();
		if ($q->num_rows() > 0) {
			return (string) $q->row()->descripcion;
		}
		$this->db->reset_query();
		$this->db->select('descripcion');
		$this->db->from('core.tablas');
		$this->db->where('tabl_id', $reg_pais_id);
		$this->db->where('tabla', 'paises');
		$q2 = $this->db->get();
		if ($q2->num_rows() > 0) {
			return (string) $q2->row()->descripcion;
		}
		log_message('ERROR', '#TRAZA|REGISTER|obtenerNombrePaisRegistroUsuario() >> No se encontró país para reg_pais_id=' . $reg_pais_id);
		return '';
	}

	/**
	 * El JSON del CORE a veces devuelve un solo objeto en lugar de array; el JS del ABM espera array.
	 *
	 * @param mixed $items
	 * @return array
	 */
	private function normalizarListaTablasCore($items)
	{
		if ($items === null) {
			return array();
		}
		if (is_array($items)) {
			return $items;
		}
		return array($items);
	}

	/**
	 * Obtiene el nombre del país del usuario en registro desde el valor persistido
	 * en seg.users.reg_pais_id (sin confiar en parámetros del frontend).
	 *
	 * @return string
	 */
	private function obtenerPaisNombreRegistracionActual()
	{
		$user_id = $this->session->userdata('id');
		if (!$user_id) {
			return '';
		}

		$this->db->select('reg_pais_id');
		$this->db->from('seg.users');
		$this->db->where('id', $user_id);
		$query = $this->db->get();
		$user = $query ? $query->row() : null;
		if (!$user || empty($user->reg_pais_id)) {
			return '';
		}

		return $this->obtenerNombrePaisRegistroUsuario($user->reg_pais_id);
	}
	
    public function guardarFormularioRegistro()
    {
        log_message('debug', 'guardarFormularioRegistro: Iniciando método');
        
        $user_id = $this->session->userdata('id');
        $info_id = $this->session->userdata('temp_info_id');
        
        // Si no hay sesión, usar datos de prueba
        if (!$user_id) {
            $user_id = 1; // Usuario de prueba
            log_message('debug', 'guardarFormularioRegistro: Usando usuario de prueba: ' . $user_id);
        }
        
        if (!$info_id) {
            $info_id = $this->input->post('info_id');
            log_message('debug', 'guardarFormularioRegistro: Usando info_id del POST: ' . $info_id);
        }
        
        log_message('debug', 'guardarFormularioRegistro: user_id=' . $user_id . ', info_id=' . $info_id);
        
        if (!$user_id || !$info_id) {
            log_message('error', 'guardarFormularioRegistro: Datos faltantes - user_id=' . $user_id . ', info_id=' . $info_id);
            echo json_encode(['success' => false, 'message' => 'Datos faltantes: user_id=' . $user_id . ', info_id=' . $info_id]);
            return;
        }
        
        try {
            // Cargar el helper del módulo
            require_once(APPPATH . 'modules/traz-comp-formularios/helpers/form_helper.php');
            require_once(APPPATH . 'modules/traz-comp-formularios/models/Forms.php');
            $Forms = new Forms();

            $this->aplicarEmprIdTemporalRegistro();

            // Obtener los datos del formulario
            $form_data = $this->input->post();
            log_message('debug', 'guardarFormularioRegistro: Datos recibidos: ' . json_encode($form_data));
            
            // ACTUALIZAR la instancia existente
            $result = $Forms->actualizar($info_id, $form_data);
            log_message('debug', 'guardarFormularioRegistro: Resultado actualizar: ' . json_encode($result));
            
            // Actualizar el usuario con el info_id
            $this->db->where('id', $user_id);
            $this->db->set('reg_info_id', $info_id);
            $update_result = $this->db->update('seg.users');
            log_message('debug', 'guardarFormularioRegistro: Resultado update usuario: ' . ($update_result ? 'true' : 'false'));
            
            // Limpiar la sesión temporal
            $this->session->unset_userdata('temp_info_id');
            $this->limpiarEmprIdTemporalRegistro();

            log_message('debug', 'guardarFormularioRegistro: Formulario guardado exitosamente');
            echo json_encode(['success' => true, 'message' => 'Formulario guardado correctamente', 'redirect' => base_url() . 'register/crearEmpresa']);
            
        } catch (Exception $e) {
            $this->limpiarEmprIdTemporalRegistro();
            log_message('error', 'guardarFormularioRegistro: Error - ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Error al guardar formulario: ' . $e->getMessage()]);
        }
    }
    
    public function crearEmpresa()
    {
        log_message('INFO', '#TRAZA|REGISTER|crearEmpresa() >> Iniciando');
        
        // Obtener datos del usuario desde la sesión
        $user_id = $this->session->userdata('id');
        
        if (!$user_id) {
            log_message('ERROR', '#TRAZA|REGISTER|crearEmpresa() >> No hay sesión de usuario');
            redirect(base_url() . 'main/login/');
            return;
        }
        
        // Obtener datos del usuario desde la BD
        $this->db->select('first_name, last_name, email, telefono, reg_pais_id, reg_razon_social');
        $this->db->from('seg.users');
        $this->db->where('id', $user_id);
        $query = $this->db->get();
        $user_data = $query->row();
        
        if (!$user_data) {
            log_message('ERROR', '#TRAZA|REGISTER|crearEmpresa() >> Usuario no encontrado');
            redirect(base_url() . 'main/login/');
            return;
        }
        
        $pais_nombre = $this->obtenerNombrePaisRegistroUsuario($user_data->reg_pais_id);

        log_message('INFO', '#TRAZA|REGISTER|crearEmpresa() >> user_id=' . $user_id
            . ' | reg_pais_id=' . ($user_data->reg_pais_id !== null ? $user_data->reg_pais_id : 'NULL')
            . ' | pais_nombre=' . ($pais_nombre !== '' ? $pais_nombre : 'VACIO'));

        // Preparar datos para la vista
        $data['title'] = "Completar Datos de Empresa";
        $data['user_data'] = $user_data;
        $data['pais_nombre'] = $pais_nombre;
        $data['pais_id'] = $user_data->reg_pais_id;
        
        $this->load->view('header', $data);
        $this->load->view('crear_empresa_page', $data);
        $this->load->view('footer');
    }
    
    public function guardarEmpresa()
    {
        log_message('INFO', '#TRAZA|REGISTER|guardarEmpresa() >> Iniciando | method=' . $this->input->method(true)
            . ' | post_keys=' . implode(',', array_keys($this->input->post() ?: array())));

        /* Validaciones */
        $this->form_validation->set_rules('cuit', 'Identificador Tributario', 'required');
        $this->form_validation->set_rules('prov_id', 'Provincia', 'required');
        $this->form_validation->set_rules('loca_id', 'Localidad', 'required');

        /* Obtener datos del usuario desde la sesión */
        $user_id = $this->session->userdata('id');
        log_message('INFO', '#TRAZA|REGISTER|guardarEmpresa() >> user_id=' . ($user_id ?: 'null'));

        if (!$user_id) {
            log_message('ERROR', '#TRAZA|REGISTER|guardarEmpresa() >> No hay sesión de usuario');
            redirect(base_url() . 'main/login/');
            return;
        }

        /* Obtener datos del usuario desde la BD */
        $this->db->select('email, telefono, reg_pais_id, reg_razon_social');
        $this->db->from('seg.users');
        $this->db->where('id', $user_id);
        $query = $this->db->get();
        $user_data = $query->row();

        if (!$user_data) {
            log_message('ERROR', '#TRAZA|REGISTER|guardarEmpresa() >> Usuario no encontrado');
            redirect(base_url() . 'main/login/');
            return;
        }

        log_message('INFO', '#TRAZA|REGISTER|guardarEmpresa() >> user_data OK | email=' . $user_data->email
            . ' | reg_pais_id=' . $user_data->reg_pais_id
            . ' | reg_razon_social=' . $user_data->reg_razon_social);

        if ($this->form_validation->run() == FALSE) {
            log_message('INFO', '#TRAZA|REGISTER|guardarEmpresa() >> Validación fallida: ' . validation_errors());
            // Recargar la vista con errores
            $data['title'] = "Completar Datos de Empresa";
            $data['user_data'] = $user_data;
            $data['pais_id'] = $user_data->reg_pais_id;
            
            $data['pais_nombre'] = $this->obtenerNombrePaisRegistroUsuario($user_data->reg_pais_id);
            
            $this->load->view('header', $data);
            $this->load->view('crear_empresa_page', $data);
            $this->load->view('footer');
        } else {
            log_message('INFO', '#TRAZA|REGISTER|guardarEmpresa() >> Validación OK, procesando');
            
            $post = $this->input->post(NULL, TRUE);
            $pais_id = urlencode($user_data->reg_pais_id);
            $prov_id = urldecode($this->input->post('prov_id'));
            $loca_id = urldecode($this->input->post('loca_id'));
            
            $cleanPost = $this->security->xss_clean($post);
            $cleanPost['nombre'] = $user_data->reg_razon_social;
            $cleanPost['cuit'] = $this->input->post('cuit');
            $cleanPost['descripcion'] = $user_data->reg_razon_social;
            $cleanPost['telefono'] = $user_data->telefono;
            $cleanPost['email'] = $user_data->email;
            $cleanPost['pais_id'] = $pais_id;
            $cleanPost['prov_id'] = $prov_id;
            $cleanPost['loca_id'] = $loca_id;
            
            // Manejar imagen si se subió
            if (isset($_FILES['image']) && $_FILES['image']['size'] > 0) {
                $cleanPost['imagepath'] = $_FILES['image']['name'];
                $cleanPost['image'] = base64_encode(file_get_contents($_FILES['image']['tmp_name']));
            } else {
                $cleanPost['imagepath'] = '';
                $cleanPost['image'] = '';
            }
            
            log_message('INFO', '#TRAZA|REGISTER|guardarEmpresa() >> cleanPost listo | pais_id=' . $cleanPost['pais_id']
                . ' | prov_id=' . $cleanPost['prov_id'] . ' | loca_id=' . $cleanPost['loca_id']
                . ' | cuit=' . $cleanPost['cuit'] . ' | email=' . $cleanPost['email']);

            /* Llamar al API para crear empresa */
            try {
                log_message('INFO', '#TRAZA|REGISTER|guardarEmpresa() >> Invocando Empresas->agregarEmpresa()');
                $result = $this->Empresas->agregarEmpresa($cleanPost);
                log_message('INFO', '#TRAZA|REGISTER|guardarEmpresa() >> Resultado API: ' . json_encode($result));

                if (isset($result['status']) && !$result['status']) {
                    throw new Exception('Error al crear empresa (status=false). code=' . (isset($result['code']) ? $result['code'] : 'N/A') . ' | data=' . (isset($result['data']) ? substr((string) $result['data'], 0, 500) : 'N/A'));
                }

                $this->postProcesarEmpresa($result, $user_data);

                log_message('INFO', '#TRAZA|REGISTER|guardarEmpresa() >> Empresa creada exitosamente');
                redirect(base_url() . 'register/registro_completo');

            } catch (Throwable $e) {
                log_message('ERROR', '#TRAZA|REGISTER|guardarEmpresa() >> Error: ' . $e->getMessage()
                    . ' | file=' . $e->getFile() . ':' . $e->getLine()
                    . ' | trace=' . str_replace("\n", ' | ', $e->getTraceAsString()));
                $this->session->set_flashdata('flash_message', 'Un error interno ha ocurrido, te pedimos disculpas. Por favor contacta a freemium@trazalog.com para recibir asistencia');
                
                // Recargar la vista con el error
                $data['title'] = "Completar Datos de Empresa";
                $data['user_data'] = $user_data;
                $data['pais_id'] = $user_data->reg_pais_id;
                
                $data['pais_nombre'] = $this->obtenerNombrePaisRegistroUsuario($user_data->reg_pais_id);
                
                $this->load->view('header', $data);
                $this->load->view('crear_empresa_page', $data);
                $this->load->view('footer');
            }
        }
    }
    
    public function getEstados()
    {
        log_message('INFO', '#TRAZA|REGISTER|getEstados() >> ');
        $pais = $this->obtenerPaisNombreRegistracionActual();
        log_message('INFO', '#TRAZA|REGISTER|getEstados() >> user_id=' . ($this->session->userdata('id') ?: 'null') . ' | pais=' . ($pais !== '' ? $pais : 'VACIO'));
        if ($pais === '') {
            log_message('ERROR', '#TRAZA|REGISTER|getEstados() >> País de registración vacío para usuario actual');
            $this->output->set_content_type('application/json');
            echo json_encode(array());
            return;
        }
        $resp = $this->Empresas->getEstados($pais);
        $this->output->set_content_type('application/json');
        echo json_encode($this->normalizarListaTablasCore($resp));
    }
    
    public function getLocalidades()
    {
        log_message('INFO', '#TRAZA|REGISTER|getLocalidades() >> ');
        $pais = $this->obtenerPaisNombreRegistracionActual();
        $estado = $this->input->get('id_estado');
        log_message('INFO', '#TRAZA|REGISTER|getLocalidades() >> user_id=' . ($this->session->userdata('id') ?: 'null') . ' | pais=' . ($pais !== '' ? $pais : 'VACIO') . ' | estado=' . ($estado !== null ? $estado : 'null'));
        if ($pais === '') {
            log_message('ERROR', '#TRAZA|REGISTER|getLocalidades() >> País de registración vacío para usuario actual');
            $this->output->set_content_type('application/json');
            echo json_encode(array());
            return;
        }
        $resp = $this->Empresas->getLocalidades($pais, $estado);
        $this->output->set_content_type('application/json');
        echo json_encode($this->normalizarListaTablasCore($resp));
    }
    
    public function registro_completo()
    {
        $data['title'] = "Registro Completado";
        $this->load->view('header', $data);
        $this->load->view('bienvenida_page', $data);
        $this->load->view('footer');
    }
    
    private function postProcesarEmpresa($apiResult, $userData)
    {
        $companyName = trim($userData->reg_razon_social ?? '');
        $companyEmailDomain = $this->obtenerDominioEmail($userData->email ?? '');

        if (!$companyName || !$companyEmailDomain) {
            log_message('WARNING', '#TRAZA|REGISTER|postProcesarEmpresa() >> Datos insuficientes para crear usuarios por defecto');
        }

        $bpmSession = $this->extraerBpmSessionDesdeApi($apiResult);
        if (!$bpmSession) {
            $bpmSession = $this->obtenerSesionBpmToken();
        }

        if ($companyName && $companyEmailDomain) {
            $this->crearUsuariosPorDefecto($userData, $companyEmailDomain, $companyName, $bpmSession);
            $this->asignarRolesAUsuario($userData->email, array('Administrador'), $companyName);
        }
    }

    private function crearUsuariosPorDefecto($userData, $emailDomain, $companyName, $bpmSession)
    {
        if (!defined('REGISTRACION_USUARIOS_DEFAULT') || !is_array(REGISTRACION_USUARIOS_DEFAULT)) {
            log_message('DEBUG', '#TRAZA|REGISTER|crearUsuariosPorDefecto() >> No hay configuración de usuarios por defecto');
            return;
        }

        foreach (REGISTRACION_USUARIOS_DEFAULT as $alias => $roles) {
            $emailLocalPart = strtolower(preg_replace('/[^a-z0-9]/i', '', $alias));
            if (!$emailLocalPart) {
                continue;
            }
            $email = $emailLocalPart . '@' . strtolower($emailDomain);

            if ($this->user_model->isDuplicate($email)) {
                log_message('INFO', '#TRAZA|REGISTER|crearUsuariosPorDefecto() >> Usuario ya existe: ' . $email);
                $this->asignarRolesAUsuario($email, $roles, $companyName);
                continue;
            }

            $creado = $this->crearUsuarioDefaultViaApi($alias, $email, $companyName, $userData, $bpmSession);
            if ($creado) {
                $this->asignarRolesAUsuario($email, $roles, $companyName);
            }
        }
    }

    private function crearUsuarioDefaultViaApi($alias, $email, $companyName, $userData, $bpmSession)
    {
        $password = defined('REGISTRACION_PASSWORD_DEFAULT') ? REGISTRACION_PASSWORD_DEFAULT : '123456';
        $firstName = ucfirst($alias);
        $lastName = $companyName;
        $telefono = $userData->telefono ?? '+0000000000';
        $status = isset($this->user_model->status[1]) ? $this->user_model->status[1] : ($this->user_model->status[0] ?? 'pending');
        $banned = $this->user_model->banned_users[0] ?? 'unban';
        $roleDefault = $this->user_model->roles[0] ?? '4';

        $payload = array(
            'bpmSession' => $bpmSession,
            'usuario' => array(
                'firstname' => $firstName,
                'lastname' => $lastName,
                'email' => strtolower($email),
                'password' => $password,
                'role' => $roleDefault,
                'status' => $status,
                'banned_users' => $banned,
                'telefono' => $telefono ?: '+0000000000',
                'dni' => '',
                'usernick' => strtolower($email),
                'depo_id' => null,
                'image_name' => null,
                'image' => null,
                'business' => $companyName
            )
        );

        try {
            $response = $this->rest->callAPI('POST', API_CORE . '/usuario', $payload);
            if (!$response['status']) {
                log_message('ERROR', '#TRAZA|REGISTER|crearUsuarioDefaultViaApi() >> Error HTTP creando usuario ' . $email . ' | code: ' . $response['code']);
                return false;
            }
            $body = json_decode($response['data']);
            if (!isset($body->respuesta->usr_id)) {
                log_message('ERROR', '#TRAZA|REGISTER|crearUsuarioDefaultViaApi() >> Respuesta inesperada creando usuario ' . $email . ' | body: ' . $response['data']);
                return false;
            }
            log_message('INFO', '#TRAZA|REGISTER|crearUsuarioDefaultViaApi() >> Usuario creado: ' . $email);
            return true;
        } catch (Exception $e) {
            log_message('ERROR', '#TRAZA|REGISTER|crearUsuarioDefaultViaApi() >> Excepción creando usuario ' . $email . ' | ' . $e->getMessage());
            return false;
        }
    }

    private function asignarRolesAUsuario($email, $roleBaseNames, $companyName)
    {
        if (!$email || !$companyName || empty($roleBaseNames)) {
            return;
        }

        $groupInfo = $this->obtenerGrupoBpmPorEmpresa($companyName);
        if (!$groupInfo) {
            log_message('ERROR', '#TRAZA|REGISTER|asignarRolesAUsuario() >> No se encontró grupo BPM para ' . $companyName);
            return;
        }

        $bpmSession = defined('BPM_ROLES_SESSION_URL') ? BPM_ROLES_SESSION_URL : rawurlencode('X-Bonita-API-Token=658fcd51-ef8b-48c3-9606-1d89a88cf3e5;JSESSIONID=BCDEA4A05749709F4DFBDCBB58A527E8;bonita.tenant=1;');

        foreach ($roleBaseNames as $baseName) {
            $roleFullName = trim($baseName . ' ' . $companyName);
            if (!$roleFullName) {
                continue;
            }
            if ($this->membershipExists($email, $roleFullName)) {
                continue;
            }

            $roleInfo = $this->obtenerRolBpmPorNombre($roleFullName);
            if (!$roleInfo) {
                log_message('ERROR', '#TRAZA|REGISTER|asignarRolesAUsuario() >> Rol BPM no encontrado: ' . $roleFullName);
                continue;
            }

            $payload = array(
                'email' => $email,
                'group' => $companyName,
                'role' => $roleFullName,
                'group_id' => (string) $groupInfo->id,
                'role_id' => (string) $roleInfo->id,
                'bpmSession' => $bpmSession
            );

            try {
                $response = $this->rest->callAPI('POST', API_CORE . '/rol/asignar', $payload);
                if (!$response['status'] || (isset($response['code']) && $response['code'] >= 300)) {
                    log_message('ERROR', '#TRAZA|REGISTER|asignarRolesAUsuario() >> Error API asignar rol ' . $roleFullName . ' | code: ' . ($response['code'] ?? 'N/A'));
                }
            } catch (Exception $e) {
                log_message('ERROR', '#TRAZA|REGISTER|asignarRolesAUsuario() >> Excepción asignando rol ' . $roleFullName . ' | ' . $e->getMessage());
            }
        }
    }

    private function membershipExists($email, $roleName)
    {
        $this->db->where('email', $email);
        $this->db->where('role', $roleName);
        $query = $this->db->get('seg.memberships_users');
        return $query && $query->num_rows() > 0;
    }

    private function obtenerGrupoBpmPorEmpresa($companyName)
    {
        $groups = $this->getCachedBpmGroups();
        foreach ($groups as $group) {
            $displayName = isset($group->displayName) ? trim($group->displayName) : '';
            $nameCandidate = $this->extraerNombreIdentificador($group->name ?? '');
            if (strcasecmp($displayName, $companyName) === 0 || strcasecmp($nameCandidate, $companyName) === 0) {
                return $group;
            }
        }
        return null;
    }

    private function obtenerRolBpmPorNombre($roleName)
    {
        $roles = $this->getCachedBpmRoles();
        foreach ($roles as $role) {
            $displayName = isset($role->displayName) ? trim($role->displayName) : '';
            $nameCandidate = $this->extraerNombreIdentificador($role->name ?? '');
            if (strcasecmp($displayName, $roleName) === 0 || strcasecmp($nameCandidate, $roleName) === 0) {
                return $role;
            }
        }
        return null;
    }

    private function getCachedBpmGroups()
    {
        if ($this->bpmGroupsCache === null) {
            $groups = $this->Roles->getBpmGroups();
            $this->bpmGroupsCache = is_array($groups) ? $groups : array();
        }
        return $this->bpmGroupsCache;
    }

    private function getCachedBpmRoles()
    {
        if ($this->bpmRolesCache === null) {
            $roles = $this->Roles->getBpmRoles();
            $this->bpmRolesCache = is_array($roles) ? $roles : array();
        }
        return $this->bpmRolesCache;
    }

    private function extraerNombreIdentificador($value)
    {
        if (!$value) {
            return '';
        }
        $parts = explode('-', $value, 2);
        return isset($parts[1]) ? trim($parts[1]) : trim($value);
    }

    private function obtenerDominioEmail($email)
    {
        if (!$email || strpos($email, '@') === false) {
            return null;
        }
        return substr(strrchr($email, '@'), 1);
    }

    private function extraerBpmSessionDesdeApi($apiResult)
    {
        if (!isset($apiResult['data'])) {
            return null;
        }
        $body = json_decode($apiResult['data']);
        if (!$body || !isset($body->respuesta)) {
            return null;
        }
        return $body->respuesta->bpmSession ?? null;
    }

    private function obtenerSesionBpmToken()
    {
        if (!isset($this->bpm)) {
            return BPM_SESSION_FALLBACK;
        }

        try {
            $headers = $this->bpm->loggin(BPM_ADMIN_USER, BPM_ADMIN_PASS);
            if (!is_array($headers)) {
                return BPM_SESSION_FALLBACK;
            }

            $cookieHeader = '';
            $apiToken = '';
            foreach ($headers as $header) {
                if (stripos($header, 'Cookie:') === 0) {
                    $cookieHeader = trim(substr($header, 7));
                } elseif (stripos($header, 'X-Bonita-API-Token:') === 0) {
                    $apiToken = trim(substr($header, strlen('X-Bonita-API-Token:')));
                }
            }

            $cookies = array();
            foreach (explode(';', $cookieHeader) as $chunk) {
                $parts = explode('=', trim($chunk), 2);
                if (count($parts) === 2) {
                    $cookies[$parts[0]] = $parts[1];
                }
            }

            $token = $apiToken ?: ($cookies['X-Bonita-API-Token'] ?? '');
            $sessionId = $cookies['JSESSIONID'] ?? '';
            $tenant = $cookies['bonita_tenant'] ?? ($cookies['bonita.tenant'] ?? '1');

            if ($token && $sessionId && $tenant) {
                return sprintf('"X-Bonita-API-Token=%s;JSESSIONID=%s;bonita.tenant=%s;"', $token, $sessionId, $tenant);
            }
        } catch (Exception $e) {
            log_message('ERROR', '#TRAZA|REGISTER|obtenerSesionBpmToken() >> ' . $e->getMessage());
        }

        return BPM_SESSION_FALLBACK;
    }
}

