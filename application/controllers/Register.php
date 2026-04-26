<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Register extends CI_Controller {

    private $bpmGroupsCache = null;
    private $bpmRolesCache = null;

    /** @var string[] incidencias al crear usuarios / asignar roles (se muestran en bienvenida; no invalidan el alta de empresa) */
    private $provisionWarnings = array();

	function __construct(){
		parent::__construct();
		$this->load->model('User_model', 'user_model', TRUE);
		$this->load->model('Empresas');
		$this->load->model('Roles');
		$this->load->model('Establecimientos');
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
		} catch (Exception $e) {
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
	 * Setea un empr_id temporal en sesión durante el flujo de registro freemium.
	 * Siempre aplica el temporal (REGISTER_TEMP_EMPR_ID) para que Forms::obtenerValores()
	 * resuelva las opciones del formulario de registro desde core.tablas con el empr_id
	 * 9000 (donde viven las listas de valores del formulario). Si la sesión ya tenía un
	 * empr_id (ej. usuario con empresa preexistente volviendo a register_success), se
	 * guarda su valor original para restaurarlo al salir del flujo.
	 */
	private function aplicarEmprIdTemporalRegistro()
	{
		if ($this->session->userdata('empr_id_temporal_registro')) {
			return;
		}
		$emprIdActual = $this->session->userdata('empr_id');
		$this->session->set_userdata('empr_id_previo_registro', $emprIdActual);
		$this->session->set_userdata('empr_id', REGISTER_TEMP_EMPR_ID);
		$this->session->set_userdata('empr_id_temporal_registro', true);
	}

	/**
	 * Limpia el empr_id temporal aplicado por el flujo de registro y restaura el empr_id
	 * previo (si lo había) para no contaminar la sesión del usuario al salir.
	 */
	private function limpiarEmprIdTemporalRegistro()
	{
		if ( ! $this->session->userdata('empr_id_temporal_registro')) {
			return;
		}
		$emprIdPrevio = $this->session->userdata('empr_id_previo_registro');
		if ($emprIdPrevio === null || $emprIdPrevio === '') {
			$this->session->unset_userdata('empr_id');
		} else {
			$this->session->set_userdata('empr_id', $emprIdPrevio);
		}
		$this->session->unset_userdata('empr_id_previo_registro');
		$this->session->unset_userdata('empr_id_temporal_registro');
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

        $data = $this->prepararDatosVistaCrearEmpresa($user_data);

        log_message('INFO', '#TRAZA|REGISTER|crearEmpresa() >> user_id=' . $user_id
            . ' | reg_pais_id=' . ($user_data->reg_pais_id !== null ? $user_data->reg_pais_id : 'NULL')
            . ' | pais_nombre=' . ($data['pais_nombre'] !== '' ? $data['pais_nombre'] : 'VACIO')
            . ' | email_domain=' . ($data['email_domain'] ?: 'N/A')
            . ' | is_webmail=' . ($data['is_webmail'] ? 'SI' : 'NO'));

        $this->load->view('header', $data);
        $this->load->view('crear_empresa_page', $data);
        $this->load->view('footer');
    }

    private function prepararDatosVistaCrearEmpresa($user_data)
    {
        $emailDomain = $this->normalizarDominio($this->obtenerDominioEmail(isset($user_data->email) ? $user_data->email : ''));
        $isWebmail = $this->esDominioWebmail($emailDomain);

        return array(
            'title'        => 'Completar Datos de Empresa',
            'user_data'    => $user_data,
            'pais_id'      => $user_data->reg_pais_id,
            'pais_nombre'  => $this->obtenerNombrePaisRegistroUsuario($user_data->reg_pais_id),
            'email_domain' => $emailDomain,
            'is_webmail'   => $isWebmail,
        );
    }

    private function normalizarDominio($dominio)
    {
        if (!is_string($dominio) || $dominio === '') {
            return '';
        }
        $dom = strtolower(trim($dominio));
        if ($dom !== '' && $dom[0] === '@') {
            $dom = substr($dom, 1);
        }
        return rtrim($dom, '.');
    }

    private function obtenerListaDominiosWebmail()
    {
        if (defined('WEBMAIL_DOMAINS') && is_array(WEBMAIL_DOMAINS) && count(WEBMAIL_DOMAINS) > 0) {
            return WEBMAIL_DOMAINS;
        }

        // Fallback para ambientes donde WEBMAIL_DOMAINS no soporte arrays en constants.php.
        if (defined('WEBMAIL_DOMAINS_CSV')) {
            $items = explode(',', (string) WEBMAIL_DOMAINS_CSV);
            $items = array_values(array_filter(array_map('trim', $items), function ($d) {
                return $d !== '';
            }));
            if (!empty($items)) {
                return $items;
            }
        }

        // Fallback defensivo minimo para no romper el flujo de webmail.
        return array('gmail.com', 'googlemail.com', 'hotmail.com', 'outlook.com', 'live.com', 'yahoo.com');
    }

    private function esDominioWebmail($dominio)
    {
        $dom = $this->normalizarDominio($dominio);
        if ($dom === '') {
            return false;
        }
        $lista = $this->obtenerListaDominiosWebmail();
        $listaNormalizada = array_map(array($this, 'normalizarDominio'), $lista);
        return in_array($dom, $listaNormalizada, true);
    }

    private function validarDominioCorporativo($dominio)
    {
        if (!$dominio) {
            return false;
        }
        $dom = $this->normalizarDominio($dominio);
        if ($dom === '') {
            return false;
        }
        // Regex simple para dominio: letras/numeros/guiones, con al menos un punto y TLD >= 2 chars.
        if (!preg_match('/^(?=.{1,253}$)([a-z0-9]([a-z0-9-]{0,61}[a-z0-9])?\.)+[a-z]{2,}$/i', $dom)) {
            return false;
        }
        // Un dominio "corporativo" no puede ser a su vez un webmail publico.
        if ($this->esDominioWebmail($dom)) {
            return false;
        }
        return true;
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

        $viewData = $this->prepararDatosVistaCrearEmpresa($user_data);

        if ($this->form_validation->run() == FALSE) {
            log_message('INFO', '#TRAZA|REGISTER|guardarEmpresa() >> Validación fallida: ' . validation_errors());
            $this->load->view('header', $viewData);
            $this->load->view('crear_empresa_page', $viewData);
            $this->load->view('footer');
            return;
        }

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

        // Resolver dominio a usar para los usuarios por defecto:
        //   - Si el email del usuario es un webmail publico, se exige que ingrese el
        //     dominio corporativo en el formulario (campo "company_domain").
        //   - Caso contrario, se reutiliza el dominio del email del usuario.
        $companyDomain = $viewData['email_domain'];
        if ($viewData['is_webmail']) {
            $inputDomain = $this->normalizarDominio((string) $this->input->post('company_domain', true));
            if (!$this->validarDominioCorporativo($inputDomain)) {
                log_message('WARNING', '#TRAZA|REGISTER|guardarEmpresa() >> company_domain invalido | valor=' . $inputDomain);
                $this->session->set_flashdata('flash_message', 'Ingresá un dominio de empresa valido (por ejemplo: rtools.ca). No se permiten dominios de webmail publicos.');
                $this->load->view('header', $viewData);
                $this->load->view('crear_empresa_page', $viewData);
                $this->load->view('footer');
                return;
            }
            $companyDomain = $inputDomain;
        }

        log_message('INFO', '#TRAZA|REGISTER|guardarEmpresa() >> cleanPost listo | pais_id=' . $cleanPost['pais_id']
            . ' | prov_id=' . $cleanPost['prov_id'] . ' | loca_id=' . $cleanPost['loca_id']
            . ' | cuit=' . $cleanPost['cuit'] . ' | email=' . $cleanPost['email']
            . ' | company_domain=' . $companyDomain
            . ' | is_webmail=' . ($viewData['is_webmail'] ? 'SI' : 'NO'));

        // Validar duplicado real en core.empresas antes de invocar el alta en API
        if ($this->user_model->existeRazonSocial($cleanPost['nombre'], $cleanPost['pais_id'], $cleanPost['cuit'])) {
            log_message('WARNING', '#TRAZA|REGISTER|guardarEmpresa() >> Empresa duplicada detectada | razon=' . $cleanPost['nombre'] . ' | pais_id=' . $cleanPost['pais_id'] . ' | cuit=' . $cleanPost['cuit']);
            $this->session->set_flashdata('flash_message', 'La empresa ya existe para el pais y CUIT indicados.');
            $this->load->view('header', $viewData);
            $this->load->view('crear_empresa_page', $viewData);
            $this->load->view('footer');
            return;
        }

        /* Llamar al API para crear empresa */
        try {
            log_message('INFO', '#TRAZA|REGISTER|guardarEmpresa() >> Invocando Empresas->agregarEmpresa()');
            $result = $this->Empresas->agregarEmpresa($cleanPost);
            log_message('INFO', '#TRAZA|REGISTER|guardarEmpresa() >> Resultado API (meta): status='
                . (isset($result['status']) ? ($result['status'] ? 'true' : 'false') : 'null')
                . ' | code=' . (isset($result['code']) ? $result['code'] : 'null'));

            if (isset($result['status']) && !$result['status']) {
                log_message('ERROR', '#TRAZA|REGISTER|guardarEmpresa() >> Fallo HTTP/transporte | body_snip='
                    . (isset($result['data']) ? substr((string) $result['data'], 0, 800) : ''));
                $this->session->set_flashdata('flash_message', $this->mensajeFalloTransporteEmpresa($result));
                $this->load->view('header', $viewData);
                $this->load->view('crear_empresa_page', $viewData);
                $this->load->view('footer');
                return;
            }

            $check = $this->validarRespuestaApiCrearEmpresa($result);
            if (!$check['ok']) {
                log_message('WARNING', '#TRAZA|REGISTER|guardarEmpresa() >> Respuesta API no válida o error de negocio | '
                    . (isset($check['mensaje']) ? $check['mensaje'] : ''));
                $this->session->set_flashdata('flash_message', $check['mensaje']);
                $this->load->view('header', $viewData);
                $this->load->view('crear_empresa_page', $viewData);
                $this->load->view('footer');
                return;
            }

            $emprId = isset($check['empr_id']) ? trim((string) $check['empr_id']) : '';
            $provision = $this->postProcesarEmpresa($result, $user_data, $companyDomain, $emprId);

            if (empty($provision['ok'])) {
                $detalle = $this->formatearTextoIncidenciasProvision(isset($provision['warnings']) ? $provision['warnings'] : array());
                log_message('ERROR', '#TRAZA|REGISTER|guardarEmpresa() >> postProcesarEmpresa falló. empr_id=' . $emprId . ' | ' . $detalle);
                $mensajeUsuario = 'No se pudo completar el alta: ' . $detalle;
                if ($emprId !== '') {
                    $rev = $this->Empresas->eliminarEmpresa($emprId);
                    if (!empty($rev['status'])) {
                        log_message('INFO', '#TRAZA|REGISTER|guardarEmpresa() >> Rollback empresa OK | empr_id=' . $emprId);
                        $mensajeUsuario .= ' Se revirtió la empresa creada (alta anulada).';
                    } else {
                        $body = isset($rev['data']) ? substr((string) $rev['data'], 0, 400) : '';
                        log_message('ERROR', '#TRAZA|REGISTER|guardarEmpresa() >> Rollback empresa falló | empr_id=' . $emprId
                            . ' | code=' . (isset($rev['code']) ? $rev['code'] : 'N/A') . ' | body=' . $body);
                        $mensajeUsuario .= ' La reversión automática falló (empr_id ' . $emprId . '). Contactá soporte para limpiar datos huérfanos.';
                    }
                } else {
                    $mensajeUsuario .= ' No se obtuvo empr_id para revertir; contactá soporte si ves datos inconsistentes.';
                }
                $this->session->set_flashdata('flash_message', $mensajeUsuario);
                $this->load->view('header', $viewData);
                $this->load->view('crear_empresa_page', $viewData);
                $this->load->view('footer');
                return;
            }

            log_message('INFO', '#TRAZA|REGISTER|guardarEmpresa() >> Empresa creada exitosamente');
            $this->session->set_flashdata('welcome_registro', array(
                'domain' => strtolower($companyDomain),
                'company_name' => trim(isset($user_data->reg_razon_social) ? $user_data->reg_razon_social : ''),
            ));
            redirect(base_url() . 'register/registro_completo');

        } catch (Exception $e) {
            log_message('ERROR', '#TRAZA|REGISTER|guardarEmpresa() >> Error: ' . $e->getMessage()
                . ' | file=' . $e->getFile() . ':' . $e->getLine()
                . ' | trace=' . str_replace("\n", ' | ', $e->getTraceAsString()));
            $this->session->set_flashdata('flash_message', 'Un error interno ha ocurrido, te pedimos disculpas. Por favor contacta a freemium@trazalog.com para recibir asistencia');
            $this->load->view('header', $viewData);
            $this->load->view('crear_empresa_page', $viewData);
            $this->load->view('footer');
        }
    }

    /**
     * Interpreta la respuesta JSON de POST /empresa (toolsCOREAPI). HTTP 200 no implica éxito.
     *
     * @param array $result retorno de REST::callAPI
     * @return array{ok:bool,mensaje:string}
     */
    /**
     * @return array{ok:bool,mensaje:string,empr_id:string}
     */
    private function validarRespuestaApiCrearEmpresa(array $result)
    {
        $data = isset($result['data']) ? $result['data'] : '';
        if ($data === '' || $data === false || $data === null) {
            return array(
                'ok' => false,
                'mensaje' => 'El servidor no devolvió datos al crear la empresa. Intentá de nuevo; si persiste, contactá soporte.',
                'empr_id' => '',
            );
        }

        $decoded = json_decode($data);
        if ($decoded === null && json_last_error() !== JSON_ERROR_NONE) {
            return array(
                'ok' => false,
                'mensaje' => 'La respuesta del servidor no es válida. Probá más tarde o contactá soporte.',
                'empr_id' => '',
            );
        }

        if (isset($decoded->Fault)) {
            $faultStr = isset($decoded->Fault->faultstring) ? (string) $decoded->Fault->faultstring : '';
            return array('ok' => false, 'mensaje' => $this->mensajeAmigableFaultCrearEmpresa($faultStr), 'empr_id' => '');
        }

        if (isset($decoded->respuesta)) {
            $r = $decoded->respuesta;
            if (isset($r->error) && trim((string) $r->error) !== '') {
                return array('ok' => false, 'mensaje' => $this->mensajeAmigableErrorRespuestaEmpresa((string) $r->error), 'empr_id' => '');
            }
            $emprId = isset($r->empr_id) ? trim((string) $r->empr_id) : '';
            if ($emprId !== '') {
                return array('ok' => true, 'mensaje' => '', 'empr_id' => $emprId);
            }
        }

        return array(
            'ok' => false,
            'mensaje' => 'No se pudo confirmar la creación de la empresa (falta confirmación del servidor). Contactá soporte si ves datos inconsistentes.',
            'empr_id' => '',
        );
    }

    /**
     * Fallo de red o HTTP no 2xx: intenta extraer mensaje del cuerpo JSON.
     *
     * @param array $result
     * @return string
     */
    private function mensajeFalloTransporteEmpresa(array $result)
    {
        $code = isset($result['code']) ? (int) $result['code'] : 0;
        $body = isset($result['data']) ? $result['data'] : '';
        $parsed = is_string($body) ? json_decode($body) : null;

        if ($parsed && isset($parsed->Fault)) {
            $faultStr = isset($parsed->Fault->faultstring) ? (string) $parsed->Fault->faultstring : '';
            return $this->mensajeAmigableFaultCrearEmpresa($faultStr);
        }
        if ($parsed && isset($parsed->respuesta) && isset($parsed->respuesta->error) && trim((string) $parsed->respuesta->error) !== '') {
            return $this->mensajeAmigableErrorRespuestaEmpresa((string) $parsed->respuesta->error);
        }

        if ($code === 0) {
            return 'No hubo conexión con el servidor de aplicaciones. Verificá tu red e intentá de nuevo.';
        }

        return 'El servidor respondió con error (código HTTP ' . $code . '). Intentá más tarde o contactá soporte.';
    }

    /**
     * Texto legible para Fault SOAP / DataService (sin volcar el XML completo al usuario).
     *
     * @param string $faultstring
     * @return string
     */
    private function mensajeAmigableFaultCrearEmpresa($faultstring)
    {
        $s = is_string($faultstring) ? $faultstring : '';
        if ($s === '') {
            return 'Ocurrió un error al crear la empresa en los sistemas internos. Intentá más tarde o contactá soporte.';
        }

        if (stripos($s, 'cannot be null') !== false || stripos($s, 'Column') !== false) {
            return 'Falló la grabación en base de datos (faltan datos obligatorios o son inválidos). Revisá los datos o contactá soporte técnico.';
        }
        if (stripos($s, 'empresa/asset') !== false || stripos($s, '/empresa/asset') !== false) {
            return 'Falló la sincronización de la empresa con el sistema de aplicaciones (Asset). Intentá de nuevo o contactá soporte.';
        }
        if (stripos($s, 'Actor no encontrado') !== false || stripos($s, 'actor/membership') !== false) {
            return 'Falló la configuración en BPM (actores/procesos). Contactá soporte indicando que falló el mapeo de actores.';
        }
        if (stripos($s, 'DATABASE_ERROR') !== false) {
            return 'Error de base de datos al crear la empresa. Intentá más tarde o contactá soporte.';
        }

        return 'No se pudo completar el alta de la empresa en los sistemas internos. Intentá más tarde o contactá soporte.';
    }

    /**
     * Acorta mensajes de error en JSON respuesta (evita respuestas kilométricas en pantalla).
     *
     * @param string $error
     * @return string
     */
    private function mensajeAmigableErrorRespuestaEmpresa($error)
    {
        $e = trim((string) $error);
        if ($e === '') {
            return 'La creación de empresa fue rechazada por el servidor. Contactá soporte.';
        }
        if (strlen($e) > 220) {
            $e = substr($e, 0, 217) . '...';
        }
        return 'No se pudo crear la empresa: ' . $e;
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
        $welcome = $this->session->flashdata('welcome_registro');
        $provisioningWarnings = $this->session->flashdata('provisioning_warnings');
        $domain = isset($welcome['domain']) ? (string) $welcome['domain'] : '';
        $data['title'] = "Registro Completado";
        $data['welcome_usuarios'] = $this->listaUsuariosDefaultParaBienvenida($domain);
        $data['welcome_password_hint'] = defined('REGISTRACION_PASSWORD_DEFAULT') ? REGISTRACION_PASSWORD_DEFAULT : '123456';
        $data['provisioning_warnings'] = is_array($provisioningWarnings) ? $provisioningWarnings : array();

        /* Tras crear la empresa el usuario seguía con sesión de registración (email, id, etc.).
         * Main::login() interpreta eso como "ya logueado" y redirige a DE (traz-tools) sin mostrar el formulario.
         * Cerramos sesión para que "Ir a iniciar sesión" muestre el login real y pueda elegir empresa. */
        $this->session->sess_destroy();
        $this->load->view('header', $data);
        $this->load->view('bienvenida_page', $data);
        $this->load->view('footer');
    }

    /**
     * Misma lógica de correo que crearUsuariosPorDefecto(): alias de REGISTRACION_USUARIOS_DEFAULT + dominio corporativo.
     *
     * @param string $domain dominio sin @ (ej. miempresa.com)
     * @return array<int, array{email:string, roles_label:string}>
     */
    private function listaUsuariosDefaultParaBienvenida($domain)
    {
        $domain = strtolower(trim((string) $domain));
        $out = array();
        if ($domain === '' || !defined('REGISTRACION_USUARIOS_DEFAULT') || !is_array(REGISTRACION_USUARIOS_DEFAULT)) {
            return $out;
        }
        foreach (REGISTRACION_USUARIOS_DEFAULT as $alias => $roles) {
            $emailLocalPart = strtolower(preg_replace('/[^a-z0-9]/i', '', $alias));
            if ($emailLocalPart === '') {
                continue;
            }
            $email = $emailLocalPart . '@' . $domain;
            $rolesLabel = is_array($roles) ? implode(', ', $roles) : (string) $roles;
            $out[] = array(
                'email' => $email,
                'roles_label' => $rolesLabel,
            );
        }
        return $out;
    }
    
    /**
     * @return array{ok:bool,warnings:array}
     */
    private function postProcesarEmpresa($apiResult, $userData, $overrideDomain = null, $emprId = '')
    {
        $this->provisionWarnings = array();
        $this->clearBpmCaches();

        $companyName = trim(isset($userData->reg_razon_social) ? $userData->reg_razon_social : '');
        $override = strtolower(trim((string) $overrideDomain));
        $companyEmailDomain = $override !== '' ? $override : $this->obtenerDominioEmail(isset($userData->email) ? $userData->email : '');

        if (!$companyName || !$companyEmailDomain) {
            $this->addProvisionWarning('Faltan el nombre de empresa o el dominio de correo para crear usuarios y asignar roles.');
            log_message('WARNING', '#TRAZA|REGISTER|postProcesarEmpresa() >> Datos insuficientes | company=' . $companyName . ' | domain=' . $companyEmailDomain);
            return array('ok' => false, 'warnings' => $this->provisionWarnings);
        }
        log_message('INFO', '#TRAZA|REGISTER|postProcesarEmpresa() >> Usando dominio="' . $companyEmailDomain . '" (override=' . ($override !== '' ? 'SI' : 'NO') . ')');

        $bpmSession = $this->extraerBpmSessionDesdeApi($apiResult);
        if (!$bpmSession) {
            $bpmSession = $this->obtenerSesionBpmToken();
        }

        $this->crearUsuariosPorDefecto($userData, $companyEmailDomain, $companyName, $bpmSession);
        $this->asignarRolesAUsuario($userData->email, array('Administrador'), $companyName);
        $this->crearEstablecimientoDefectoEmpresa($emprId, $companyEmailDomain);

        $ok = empty($this->provisionWarnings);
        if (!$ok) {
            log_message('ERROR', '#TRAZA|REGISTER|postProcesarEmpresa() >> Incompleto. Incidencias: ' . $this->formatearTextoIncidenciasProvision($this->provisionWarnings));
        }
        return array('ok' => $ok, 'warnings' => $this->provisionWarnings);
    }

    /**
     * Aprovisiona el Establecimiento + Depósito + encargado por defecto para una empresa recién creada.
     * Se apoya en las resources del DataService (COREDataService); ante error, intenta rollback de lo
     * parcialmente creado y agrega un warning; así guardarEmpresa() acabará ejecutando eliminarEmpresa()
     * y el alta queda sin inconsistencias visibles.
     *
     * @param string $emprId
     * @param string $companyEmailDomain dominio ya normalizado (sin @)
     */
    private function crearEstablecimientoDefectoEmpresa($emprId, $companyEmailDomain)
    {
        $emprId = trim((string) $emprId);
        if ($emprId === '') {
            $this->addProvisionWarning('No se pudo crear el Establecimiento/Depósito por defecto: falta empr_id de la empresa recién creada.');
            log_message('ERROR', '#TRAZA|REGISTER|crearEstablecimientoDefectoEmpresa() >> empr_id vacío');
            return;
        }

        $estaNombre    = defined('REGISTRACION_ESTABLECIMIENTO_DEFAULT_NOMBRE') ? REGISTRACION_ESTABLECIMIENTO_DEFAULT_NOMBRE : 'Establecimiento Principal';
        $depoNombre    = defined('REGISTRACION_DEPOSITO_DEFAULT_NOMBRE') ? REGISTRACION_DEPOSITO_DEFAULT_NOMBRE : 'Deposito 1';
        $depoDescripc  = defined('REGISTRACION_DEPOSITO_DEFAULT_DESCRIPCION') ? REGISTRACION_DEPOSITO_DEFAULT_DESCRIPCION : $depoNombre;
        $encargadoAlias = defined('REGISTRACION_DEPOSITO_DEFAULT_ENCARGADO_ALIAS') ? REGISTRACION_DEPOSITO_DEFAULT_ENCARGADO_ALIAS : 'almacen';

        $encargadoLocalPart = strtolower(preg_replace('/[^a-z0-9]/i', '', (string) $encargadoAlias));
        $encargadoEmail = $encargadoLocalPart !== '' ? ($encargadoLocalPart . '@' . strtolower($companyEmailDomain)) : '';

        $estaId = null;
        $depoId = null;

        try {
            $resEsta = $this->Establecimientos->crearEstablecimiento(array(
                'nombre'    => $estaNombre,
                'calle'     => '',
                'altura'    => '',
                'pais'      => '',
                'estado'    => '',
                'localidad' => '',
                'empr_id'   => $emprId,
            ));
            if (empty($resEsta['ok']) || empty($resEsta['esta_id'])) {
                $msg = 'No se pudo crear el Establecimiento por defecto para empr_id=' . $emprId . '. ' . (isset($resEsta['message']) ? $resEsta['message'] : '');
                log_message('ERROR', '#TRAZA|REGISTER|crearEstablecimientoDefectoEmpresa() >> ' . $msg);
                $this->addProvisionWarning($msg);
                return;
            }
            $estaId = (string) $resEsta['esta_id'];
            log_message('INFO', '#TRAZA|REGISTER|crearEstablecimientoDefectoEmpresa() >> Establecimiento creado esta_id=' . $estaId);

            $resDepo = $this->Establecimientos->crearDeposito(array(
                'descripcion' => $depoDescripc,
                'nombre'      => $depoNombre,
                'empr_id'     => $emprId,
                'esta_id'     => $estaId,
            ));
            if (empty($resDepo['ok']) || empty($resDepo['depo_id'])) {
                $msg = 'No se pudo crear el Depósito por defecto (empr_id=' . $emprId . ', esta_id=' . $estaId . '). ' . (isset($resDepo['message']) ? $resDepo['message'] : '');
                log_message('ERROR', '#TRAZA|REGISTER|crearEstablecimientoDefectoEmpresa() >> ' . $msg);
                $this->addProvisionWarning($msg);
                $this->Establecimientos->eliminarEstablecimiento($estaId);
                return;
            }
            $depoId = (string) $resDepo['depo_id'];
            log_message('INFO', '#TRAZA|REGISTER|crearEstablecimientoDefectoEmpresa() >> Depósito creado depo_id=' . $depoId);

            if ($encargadoEmail === '') {
                $this->addProvisionWarning('No se asignó encargado al Depósito: alias de encargado inválido ("' . $encargadoAlias . '").');
                return;
            }
            $userRow = $this->user_model->getUserInfoByEmail($encargadoEmail);
            if (!$userRow || empty($userRow->id)) {
                $msg = 'No se asignó encargado al Depósito: no se encontró en seg.users el usuario ' . $encargadoEmail . '.';
                log_message('ERROR', '#TRAZA|REGISTER|crearEstablecimientoDefectoEmpresa() >> ' . $msg);
                $this->addProvisionWarning($msg);
                $this->Establecimientos->eliminarDeposito($depoId);
                $this->Establecimientos->eliminarEstablecimiento($estaId);
                return;
            }

            $resEnc = $this->Establecimientos->asignarEncargadoDeposito($depoId, $userRow->id);
            if (empty($resEnc['ok'])) {
                $msg = 'No se pudo asignar encargado al Depósito (depo_id=' . $depoId . ', user_id=' . $userRow->id . '). ' . (isset($resEnc['message']) ? $resEnc['message'] : '');
                log_message('ERROR', '#TRAZA|REGISTER|crearEstablecimientoDefectoEmpresa() >> ' . $msg);
                $this->addProvisionWarning($msg);
                $this->Establecimientos->eliminarDeposito($depoId);
                $this->Establecimientos->eliminarEstablecimiento($estaId);
                return;
            }

            log_message('INFO', '#TRAZA|REGISTER|crearEstablecimientoDefectoEmpresa() >> Encargado asignado. user_id=' . $userRow->id . ' depo_id=' . $depoId . ' esta_id=' . $estaId);
        } catch (Exception $e) {
            $msg = 'Excepción creando Establecimiento/Depósito por defecto: ' . $e->getMessage();
            log_message('ERROR', '#TRAZA|REGISTER|crearEstablecimientoDefectoEmpresa() >> ' . $msg);
            $this->addProvisionWarning($msg);
            if ($depoId) {
                $this->Establecimientos->eliminarDeposito($depoId);
            }
            if ($estaId) {
                $this->Establecimientos->eliminarEstablecimiento($estaId);
            }
        }
    }

    /**
     * @param string[] $items
     * @return string
     */
    private function formatearTextoIncidenciasProvision(array $items)
    {
        $items = array_values(array_filter(array_map('trim', $items)));
        if ($items === array()) {
            return 'Error desconocido en aprovisionamiento.';
        }
        $t = implode(' ', $items);
        if (strlen($t) > 800) {
            $t = substr($t, 0, 797) . '...';
        }
        return $t;
    }

    private function crearUsuariosPorDefecto($userData, $emailDomain, $companyName, $bpmSession)
    {
        if (!defined('REGISTRACION_USUARIOS_DEFAULT') || !is_array(REGISTRACION_USUARIOS_DEFAULT)) {
            log_message('DEBUG', '#TRAZA|REGISTER|crearUsuariosPorDefecto() >> No hay configuración de usuarios por defecto');
            $this->addProvisionWarning('Falta la constante REGISTRACION_USUARIOS_DEFAULT o no es un array válido.');
            return;
        }

        foreach (REGISTRACION_USUARIOS_DEFAULT as $alias => $roles) {
            $emailLocalPart = strtolower(preg_replace('/[^a-z0-9]/i', '', $alias));
            if (!$emailLocalPart) {
                $this->addProvisionWarning('Omito alias de usuario por defecto (clave vacía o inválida tras normalizar: "' . (string) $alias . '").');
                log_message('WARNING', '#TRAZA|REGISTER|crearUsuariosPorDefecto() >> Alias inválido en REGISTRACION_USUARIOS_DEFAULT: ' . json_encode($alias));
                continue;
            }
            $email = $emailLocalPart . '@' . strtolower($emailDomain);
            log_message('INFO', '#TRAZA|REGISTER|crearUsuariosPorDefecto() >> Procesando alias=' . $alias . ' | email=' . $email);

            if ($this->user_model->isDuplicate($email)) {
                log_message('INFO', '#TRAZA|REGISTER|crearUsuariosPorDefecto() >> Usuario ya existe, se reasignan roles: ' . $email);
                $this->asignarRolesAUsuario($email, is_array($roles) ? $roles : array($roles), $companyName);
                continue;
            }

            $creado = $this->crearUsuarioDefaultViaApi($alias, $email, $companyName, $userData, $bpmSession);
            if ($creado) {
                $this->asignarRolesAUsuario($email, is_array($roles) ? $roles : array($roles), $companyName);
            } else {
                $this->addProvisionWarning('No se pudo crear en Tools el usuario ' . $email . ' (revisá el log: crearUsuarioDefaultViaApi).');
            }
        }
    }

    private function crearUsuarioDefaultViaApi($alias, $email, $companyName, $userData, $bpmSession)
    {
        $password = defined('REGISTRACION_PASSWORD_DEFAULT') ? REGISTRACION_PASSWORD_DEFAULT : '123456';
        $firstName = ucfirst($alias);
        $lastName = $companyName;
        $telefono = isset($userData->telefono) ? $userData->telefono : '+0000000000';
        $status = isset($this->user_model->status[1]) ? $this->user_model->status[1] : (isset($this->user_model->status[0]) ? $this->user_model->status[0] : 'pending');
        $banned = isset($this->user_model->banned_users[0]) ? $this->user_model->banned_users[0] : 'unban';
        $roleDefault = isset($this->user_model->roles[0]) ? $this->user_model->roles[0] : '4';

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
            $msg = 'No se encontró en BPM el grupo/empresa "' . $companyName . '" para asignar roles a ' . $email . '.';
            log_message('ERROR', '#TRAZA|REGISTER|asignarRolesAUsuario() >> ' . $msg);
            $this->addProvisionWarning($msg);
            return;
        }

        $bpmSession = defined('BPM_ROLES_SESSION_URL') ? BPM_ROLES_SESSION_URL : rawurlencode('X-Bonita-API-Token=658fcd51-ef8b-48c3-9606-1d89a88cf3e5;JSESSIONID=BCDEA4A05749709F4DFBDCBB58A527E8;bonita.tenant=1;');

        $nRolesCarga = count($this->getCachedBpmRoles());
        log_message('INFO', '#TRAZA|REGISTER|asignarRolesAUsuario() >> email=' . $email . ' | empresa=' . $companyName . ' | roles en catálogo BPM (página actual): ' . $nRolesCarga);

        foreach ($roleBaseNames as $baseName) {
            $baseName = trim((string) $baseName);
            $roleFullName = trim($baseName . ' ' . $companyName);
            if ($roleFullName === '') {
                continue;
            }
            if ($this->membershipExists($email, $roleFullName)) {
                log_message('DEBUG', '#TRAZA|REGISTER|asignarRolesAUsuario() >> Ya existe membership: ' . $email . ' | ' . $roleFullName);
                continue;
            }

            $roleInfo = $this->obtenerRolBpmPorNombre($roleFullName, $baseName, $companyName);
            if (!$roleInfo) {
                $this->bpmRolesCache = null;
                $roleInfo = $this->obtenerRolBpmPorNombre($roleFullName, $baseName, $companyName);
            }
            if (!$roleInfo) {
                $w = 'No se pudo localizar en el catálogo expuesto a la app el rol "' . $roleFullName
                    . '" (base "' . $baseName . '") para asignarlo a ' . $email
                    . '. Comprobá en Bonita/WSO2 que exista o que el GET de roles no esté limitado; ver log DEBUG.';
                log_message('ERROR', '#TRAZA|REGISTER|asignarRolesAUsuario() >> ' . $w);
                $this->addProvisionWarning($w);
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
                    $body = isset($response['data']) ? substr((string) $response['data'], 0, 600) : '';
                    log_message('ERROR', '#TRAZA|REGISTER|asignarRolesAUsuario() >> Error API asignar rol ' . $roleFullName . ' | code: ' . (isset($response['code']) ? $response['code'] : 'N/A') . ' | body: ' . $body);
                    $this->addProvisionWarning('Error al asignar el rol "' . $roleFullName . '" a ' . $email . ' (HTTP o API; ver logs).');
                } else {
                    log_message('INFO', '#TRAZA|REGISTER|asignarRolesAUsuario() >> Rol asignado: ' . $email . ' | ' . $roleFullName);
                }
            } catch (Exception $e) {
                log_message('ERROR', '#TRAZA|REGISTER|asignarRolesAUsuario() >> Excepción asignando rol ' . $roleFullName . ' | ' . $e->getMessage());
                $this->addProvisionWarning('Excepción al asignar el rol "' . $roleFullName . '" a ' . $email . '.');
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
            $nameCandidate = $this->extraerNombreIdentificador(isset($group->name) ? $group->name : '');
            if (strcasecmp($displayName, $companyName) === 0 || strcasecmp($nameCandidate, $companyName) === 0) {
                return $group;
            }
        }
        return null;
    }

    /**
     * Resuelve un rol de Bonita a partir de displayName (coincidencia estricta o aproximada).
     * El listado de Roles::getBpmRoles() a veces no alinea 1:1 con el string esperado; por eso hay fallback.
     *
     * @param string      $roleFullName  ej. "Supervisor de Mantenimiento RuizSoft Inc."
     * @param string|null $baseName      pieza de constants.php, ej. "Supervisor de Mantenimiento"
     * @param string|null $companyName   razón social, ej. "RuizSoft Inc."
     * @return object|null
     */
    private function obtenerRolBpmPorNombre($roleFullName, $baseName = null, $companyName = null)
    {
        $full = $this->normalizarTextoRol($roleFullName);
        $base = $baseName !== null && $baseName !== '' ? $this->normalizarTextoRol($baseName) : '';
        $emp = $companyName !== null && $companyName !== '' ? $this->normalizarTextoRol($companyName) : '';

        try {
            $direct = $this->Roles->getBpmRoleByName($roleFullName);
            if (is_object($direct) && isset($direct->displayName)) {
                $dn = $this->normalizarTextoRol($direct->displayName);
                if (strcasecmp($dn, $full) === 0) {
                    log_message('INFO', '#TRAZA|REGISTER|obtenerRolBpmPorNombre() >> Resolución directa (search) para "' . $roleFullName . '" id=' . (isset($direct->id) ? $direct->id : 'N/A'));
                    return $direct;
                }
                if ($base !== '' && $emp !== ''
                    && stripos($dn, $base) !== false
                    && stripos($dn, $emp) !== false) {
                    log_message('INFO', '#TRAZA|REGISTER|obtenerRolBpmPorNombre() >> Resolución directa por subcadena (search) para "' . $roleFullName . '", usado: ' . $dn);
                    return $direct;
                }
            }
        } catch (Exception $e) {
            log_message('WARN', '#TRAZA|REGISTER|obtenerRolBpmPorNombre() >> lookup directo falló, uso listado. ' . $e->getMessage());
        }

        $roles = $this->getCachedBpmRoles();
        if ($roles === array()) {
            return null;
        }

        foreach ($roles as $role) {
            if (!is_object($role)) {
                continue;
            }
            $displayName = $this->normalizarTextoRol(isset($role->displayName) ? $role->displayName : '');
            $nameCandidate = $this->normalizarTextoRol($this->extraerNombreIdentificador(isset($role->name) ? $role->name : ''));
            if (strcasecmp($displayName, $full) === 0 || strcasecmp($nameCandidate, $full) === 0) {
                return $role;
            }
        }
        if ($base !== '' && $emp !== '') {
            $best = null;
            $bestLen = 0;
            foreach ($roles as $role) {
                if (!is_object($role)) {
                    continue;
                }
                $displayName = $this->normalizarTextoRol(isset($role->displayName) ? $role->displayName : '');
                if ($displayName === '') {
                    continue;
                }
                if (function_exists('mb_stripos')) {
                    if (mb_stripos($displayName, $base, 0, 'UTF-8') === false || mb_stripos($displayName, $emp, 0, 'UTF-8') === false) {
                        continue;
                    }
                } else {
                    if (stripos($displayName, $base) === false || stripos($displayName, $emp) === false) {
                        continue;
                    }
                }
                $bl = strlen($base);
                if ($bl > $bestLen) {
                    $bestLen = $bl;
                    $best = $role;
                }
            }
            if ($best !== null) {
                $dn = $this->normalizarTextoRol(isset($best->displayName) ? $best->displayName : '');
                log_message('INFO', '#TRAZA|REGISTER|obtenerRolBpmPorNombre() >> Resolución por subcadena. Buscado "' . $roleFullName . '", usado: ' . $dn);
                return $best;
            }
        }

        $this->logMuestraRolesCercanos($roleFullName, $emp, $roles);
        return null;
    }

    private function clearBpmCaches()
    {
        $this->bpmGroupsCache = null;
        $this->bpmRolesCache = null;
    }

    private function addProvisionWarning($message)
    {
        $message = trim((string) $message);
        if ($message === '') {
            return;
        }
        $this->provisionWarnings[] = $message;
    }

    private function normalizarTextoRol($s)
    {
        $s = trim(preg_replace('/\s+/', ' ', (string) $s));
        return $s;
    }

    /**
     * @param string          $roleFullName
     * @param string          $empresaFragment
     * @param array<int, mixed> $roles
     */
    private function logMuestraRolesCercanos($roleFullName, $empresaFragment, array $roles)
    {
        $muestras = array();
        foreach ($roles as $r) {
            if (!is_object($r) || !isset($r->displayName)) {
                continue;
            }
            $dn = $this->normalizarTextoRol($r->displayName);
            if ($empresaFragment === '' || stripos($dn, $empresaFragment) !== false) {
                $muestras[] = $dn;
            }
            if (count($muestras) >= 20) {
                break;
            }
        }
        log_message('DEBUG', '#TRAZA|REGISTER|obtenerRolBpmPorNombre() >> Sin match para "' . $roleFullName
            . '". Muestra de displayNames con fragmento de empresa (máx 20): ' . json_encode($muestras, JSON_UNESCAPED_UNICODE));
    }

    private function getCachedBpmGroups()
    {
        if ($this->bpmGroupsCache === null) {
            $groups = $this->Roles->getBpmGroups();
            if (is_object($groups)) {
                $groups = (array) $groups;
            }
            $this->bpmGroupsCache = is_array($groups) ? $groups : array();
        }
        return $this->bpmGroupsCache;
    }

    private function getCachedBpmRoles()
    {
        if ($this->bpmRolesCache === null) {
            $roles = $this->Roles->getBpmRoles();
            if (is_object($roles) && $roles !== null) {
                $roles = array($roles);
            } elseif (!is_array($roles)) {
                $roles = array();
            }
            $norm = array();
            foreach ($roles as $r) {
                if (is_array($r)) {
                    $norm[] = (object) $r;
                } elseif (is_object($r)) {
                    $norm[] = $r;
                }
            }
            $this->bpmRolesCache = $norm;
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
        return (isset($body->respuesta->bpmSession)) ? $body->respuesta->bpmSession : null;
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

            $token = $apiToken ? $apiToken : (isset($cookies['X-Bonita-API-Token']) ? $cookies['X-Bonita-API-Token'] : '');
            $sessionId = isset($cookies['JSESSIONID']) ? $cookies['JSESSIONID'] : '';
            $tenant = isset($cookies['bonita_tenant']) ? $cookies['bonita_tenant'] : (isset($cookies['bonita.tenant']) ? $cookies['bonita.tenant'] : '1');

            if ($token && $sessionId && $tenant) {
                return sprintf('"X-Bonita-API-Token=%s;JSESSIONID=%s;bonita.tenant=%s;"', $token, $sessionId, $tenant);
            }
        } catch (Exception $e) {
            log_message('ERROR', '#TRAZA|REGISTER|obtenerSesionBpmToken() >> ' . $e->getMessage());
        }

        return BPM_SESSION_FALLBACK;
    }
}

