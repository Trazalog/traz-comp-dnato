<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Main extends CI_Controller {

	public $status;
	public $roles;

	function __construct(){

		parent::__construct();
		$this->load->model('User_model', 'user_model', TRUE);
		$this->load->library('form_validation');
		$this->form_validation->set_error_delimiters('<div class="error">', '</div>');
		$this->status = $this->config->item('status');
		$this->roles = $this->config->item('roles');
		$this->load->library('userlevel');
		$this->load->config('email');
		$this->load->model('Roles');
		$this->load->model('Tablas');

	}

	public function setdir()
	{
		// $this->session->set_userdata('direccion', );
		// $this->session->set_userdata('direccionsalida', );
		$this->login();
		// log_message('DEBUG','#Main/setdir | '.json_encode($this->session->userdata()));
		// redirect(base_url().'main/index');
	}

	//index dasboard
	public function index()
	{
		//user data from session
		$data = $this->session->userdata();
		//log_message('DEBUG','#Main/index | '.json_encode($data));

		if(empty($data['email'])){
				//log_message('DEBUG','#Main/index | No email');
				redirect(base_url().'main/login/');
		}

		//check user level
		if(empty($data['role'])){
				//log_message('DEBUG','#Main/index | No role');
				redirect(base_url().'main/login/');
		}
		$dataLevel = $this->userlevel->checkLevel($data['role']);
		//check user level

		$data['title'] = "Dashboard Admin";

		if($data['direccion']){
				//log_message('DEBUG','#Main/index | Redireccion: '.$data['direccion']);
				redirect($data['direccion']);
		}else{
				//log_message('DEBUG','#Main/index | Error de Redireccionamiento');
				redirect(base_url().'main/login/');
		}
	}

	public function checkLoginUser(){
			//user data from session
		$data = $this->session->userdata;
		if(empty($data)){
				redirect(base_url().'main/login/');
		}
		
		$this->load->library('user_agent');
			$browser = $this->agent->browser();
			$os = $this->agent->platform();
			$getip = $this->input->ip_address();
			
			$result = $this->user_model->getAllSettings();
			$stLe = $result->site_title;
			$tz = $result->timezone;
		
			$now = new DateTime();
			$now->setTimezone(new DateTimezone($tz));
			$dTod =  $now->format('Y-m-d');
			$dTim =  $now->format('H:i:s');
			
			$this->load->helper('cookie');
			$keyid = rand(1,9000);
			$scSh = sha1($keyid);
			$neMSC = md5($data['email']);
			$setLogin = array(
					'name'   => $neMSC,
					'value'  => $scSh,
					'expire' => strtotime("+2 year"),
			);
			$getAccess = get_cookie($neMSC);
		
			if(!$getAccess && $setLogin["name"] == $neMSC){
					$this->load->library('email');
					$this->load->library('sendmail');
					$bUrl = base_url();
					$message = $this->sendmail->secureMail($data['first_name'],$data['last_name'],$data['email'],$dTod,$dTim,$stLe,$browser,$os,$getip,$bUrl);
					$to_email = $data['email'];
					$this->email->from($this->config->item('register'), 'Nuevo inicio de sesión desde '.$browser);
					$this->email->to($to_email);
					$this->email->subject('Nuevo inicio de sesión desde '.$browser);
					$this->email->message($message);
					$this->email->set_mailtype("html");
					$this->email->send();
					
					$this->input->set_cookie($setLogin, TRUE);
					redirect(base_url().'main/');
			}else{
					$this->input->set_cookie($setLogin, TRUE);
					redirect(base_url().'main/');
			}
	}

	public function settings(){
		$data = $this->session->userdata;
			if(empty($data['role'])){
				redirect(base_url().'main/login/');
		}
		$dataLevel = $this->userlevel->checkLevel($data['role']);
		//check user level

			$data['title'] = "Configuración";
			$data['usersList'] = $this->user_model->getListUserData();
			$data['groupsBpm'] = $this->Roles->getBpmGroups();

			$this->form_validation->set_rules('site_title', 'Título del sitio', 'required');
			$this->form_validation->set_rules('timezone', 'Zona horaria', 'required');
			$this->form_validation->set_rules('recaptcha', 'Recaptcha', 'required');
			$this->form_validation->set_rules('theme', 'Tema', 'required');

			$result = $this->user_model->getAllSettings();
			$data['id'] = $result->id;
		$data['site_title'] = $result->site_title;
		$data['timezone'] = $result->timezone;
		
		if (!empty($data['timezone']))
		{
				$data['timezonevalue'] = $result->timezone;
				$data['timezone'] = $result->timezone;
		}
		else
		{
				$data['timezonevalue'] = "";
					$data['timezone'] = "Seleccioná una zona horaria";
		}

		if($dataLevel == "is_admin"){
					if ($this->form_validation->run() == FALSE) {
							$this->load->view('header', $data);
							$this->load->view('navbar', $data);
							$this->load->view('container');
							$this->load->view('settings', $data);
							$this->load->view('footer');
					}else{
							$post = $this->input->post(NULL, TRUE);
							$cleanPost = $this->security->xss_clean($post);
							$cleanPost['id'] = $this->input->post('id');
							$cleanPost['site_title'] = $this->input->post('site_title');
							$cleanPost['timezone'] = $this->input->post('timezone');
							$cleanPost['recaptcha'] = $this->input->post('recaptcha');
							$cleanPost['theme'] = $this->input->post('theme');

							if(!$this->user_model->settings($cleanPost)){
									$this->session->set_flashdata('flash_message', 'Hubo un problema al actualizar los datos.');
							}else{
									$this->session->set_flashdata('success_message', 'Los datos se actualizaron correctamente.');
							}
							redirect(base_url().'main/settings/');
					}
		}

	}

	//user list
	public function users()	{
		$data = $this->session->userdata;
		$data['title'] = "Lista de Usuarios";
		$data['groupsBpm'] = $this->Roles->getBpmGroups();
		$data['emp_connect'] =  $this->user_model->gestMembershipsUserInfo($data['email'],1);    //Empresas del conectado
		$groupNames = array();
		if (!empty($data['emp_connect']) && is_array($data['emp_connect'])) {
			foreach ($data['emp_connect'] as $ec) {
				if (isset($ec->group) && (string) $ec->group !== '') {
					$groupNames[] = $ec->group;
				}
			}
		}
		$data['usersList'] = $this->user_model->getListUserDataForGroups($groupNames);


		//log_message('DEBUG','#TRAZA|MAIN|users()  $data[title] >> '.json_encode($data));
		//log_message('DEBUG','#TRAZA|MAIN|users()  $data[emp_connect] >> '.json_encode($data['emp_connect']));
		//log_message('DEBUG','#TRAZA|MAIN|users()  $data[usersList] >> '.json_encode($data['usersList']));
		//log_message('DEBUG','#TRAZA|MAIN|users()  $data[groupsBpm] >> '.json_encode($data['groupsBpm']));

		//check user level
		if(empty($data['role'])){
				redirect(base_url().'main/login/');
		}
		$dataLevel = $this->userlevel->checkLevel($data['role']);

		//log_message('DEBUG','#TRAZA|MAIN|users()  $data: >> '.json_encode($data));
		//log_message('DEBUG','#TRAZA|MAIN|users()  $data[email]: >> '.json_encode($data['email']));
		//check user level

		//check is admin or not
		if($dataLevel == "is_admin"){
					$this->load->view('header', $data);
					$this->load->view('navbar', $data);
					$this->load->view('container',$data);
					//$this->load->view('user', $data);
					$this->load->view('usersList', $data);
					//$this->load->view('list_usuarios_externos', $data);
					$this->load->view('footer');
		}else{
				redirect(base_url().'main/');
		}
	}

	//add new user from backend
	public function adduser()
	{

		$data = $this->session->userdata;
		if(empty($data['role'])){
				redirect(base_url().'main/login/');
		}

		//check user level
		if(empty($data['role'])){
				redirect(base_url().'main/login/');
		}
		$dataLevel = $this->userlevel->checkLevel($data['role']);

		//log_message('INFO','#TRAZA|MAIN|ADDUSER() >> ');
		//$data['usersList'] = $this->user_model->getListUserData();

		//check is admin or not
		if($dataLevel == "is_admin"){
					$this->form_validation->set_rules('firstname', 'Nombre', 'required');
					$this->form_validation->set_rules('lastname', 'Apellido', 'required');
					$this->form_validation->set_rules('email', 'Correo electrónico', 'required|valid_email');
					$this->form_validation->set_rules('business', 'Empresa', 'required');
					$this->form_validation->set_rules('role', 'Rol', 'required');
					$this->form_validation->set_rules('password', 'Contraseña', 'required|min_length[10]|password_strong');
					$this->form_validation->set_rules('passconf', 'Confirmación de contraseña', 'required|matches[password]');

					
					//log_message('DEBUG','#TRAZA|MAIN|ADDUSER() >> form_validation ');

					$data['title'] = "Agregar Usuario";
					if ($this->form_validation->run() == FALSE) {
							// trae depositos para asignar a usuarios depositos
							$this->load->model('Roles');
							$data['dd_list'] = $this->Roles->obtener();
							$data['dd_business'] = $this->user_model->obtenerBusines();
							$data['emp_connect'] =  $this->user_model->gestMembershipsUserInfo($data['email'],1);    //Empresas del conectado
							//var_dump($data);
							// RRUIZ - Re- Analizar en versión 2.0 
							// $data['depo_list'] = $this->Roles->obtenerDepositos();
							$data['groups'] = $this->Roles->getBpmGroups();


							//log_message('DEBUG','#TRAZA|MAIN|ADDUSER() >> $data >> '. json_encode($data));
							//log_message('DEBUG','#TRAZA|MAIN|ADDUSER() >> $data[dd_list] >> '.json_encode($data['dd_list']));
							//log_message('DEBUG','#TRAZA|MAIN|ADDUSER() >> $data[dd_business] >> '.json_encode($data['dd_business']));
							//log_message('DEBUG','#TRAZA|MAIN|ADDUSER() >> $data[groups] >> '.json_encode($data['groups']));

							
							$this->load->view('header', $data);
							$this->load->view('navbar',$data);
							$this->load->view('container');
							$this->load->view('adduser', $data);
							$this->load->view('footer');
					}else{
							if($this->user_model->isDuplicate($this->input->post('email'))){
									$this->session->set_flashdata('flash_message', ' Ya existe un usuario asociado a ese Email');
									redirect(base_url().'main/adduser');
							}else{
									$post = $this->input->post(NULL, TRUE);
									$cleanPost = $this->security->xss_clean($post);
									// API espera contraseña en texto plano (la hashea el backend)
									$cleanPost['email'] = $this->input->post('email');
									$cleanPost['role'] = $this->input->post('role');
									$cleanPost['firstname'] = $this->input->post('firstname');
									$cleanPost['lastname'] = $this->input->post('lastname');
									$cleanPost['telefono'] = $this->input->post('telefono');
									$cleanPost['usernick'] = $this->input->post('email'); // mismo valor que email (campo usernick no se muestra en el form)
									$cleanPost['dni'] = $this->input->post('dni');
									$cleanPost['business'] = $this->input->post('business');
									$cleanPost['banned_users'] = 'unban';
									$cleanPost['password'] = $this->input->post('password');
									unset($cleanPost['passconf']);

									// Imagen opcional
									if (!empty($_FILES['image']['tmp_name']) && is_uploaded_file($_FILES['image']['tmp_name'])) {
										$cleanPost['image_name'] = $_FILES['image']['name'];
										$cleanPost['image'] = base64_encode(file_get_contents($_FILES['image']['tmp_name']));
									} else {
										$cleanPost['image_name'] = '';
										$cleanPost['image'] = '';
									}

									$result = $this->user_model->crearUsuarioAPI($cleanPost);
									if (!empty($result['usr_id'])) {
										$this->session->set_flashdata('flash_message', 'Usuario creado exitosamente...');
										$this->session->set_flashdata(
											'flash_message_hint',
											'Recuerde que para que el usuario pueda acceder al sistema, debe primero asignarle roles. Puede hacerlo en esta pantalla usando el ícono «Asignar Rol» en la columna Acciones.'
										);
										redirect(base_url().'main/users/'.$result['usr_id']);
									} else {
										$errMsg = isset($result['error']) ? $result['error'] : 'Error al crear usuario. Intente de nuevo.';
										$errDetail = isset($result['detail']) ? $result['detail'] : '';
										log_message('ERROR', '#TRAZA | MAIN | adduser() >> crearUsuarioAPI falló: ' . $errMsg . ($errDetail ? ' | ' . (is_string($errDetail) ? $errDetail : json_encode($errDetail)) : ''));
										if (ENVIRONMENT === 'development' && $errDetail !== '') {
											$this->session->set_flashdata('danger_message', $errMsg . ' Detalle: ' . (is_string($errDetail) ? $errDetail : json_encode($errDetail)));
										} else {
											$this->session->set_flashdata('danger_message', $errMsg);
										}
										redirect(base_url().'main/adduser');
									}
							};
					}
		}else{
				redirect(base_url().'main/');
		}
	}

	//change level user id
	public function changeleveluser($id){

		$this->load->model('Roles');

		$data = $this->session->userdata;
		//check user level
		if(empty($data['role'])){
				redirect(base_url().'main/login/');
		}
		$dataLevel = $this->userlevel->checkLevel($data['role']);
		$dataEmp = $this->userlevel->checkLevel($data['groupBpm']);
		//check user level

		$data['title'] = "Cambiar Niveles de Usuarios";
		$data['emp_connect'] =  $this->user_model->gestMembershipsUserInfo($data['email'],1); 			// Empresas Usuario Conectado
		$data['usersList'] = $this->user_model->getListUserData();										// Listado de Usuarios
		$data['user'] = $this->user_model->getUserInfo($id); 											// Datos Usuario Seleccionado
		$data['mem_user'] = $this->user_model->gestMembershipsUserInfo($data['user']->email,0); 		// Empresas usuario Seleccionado
		$data['dd_list'] = $this->Roles->obtener(); 													// Perfil Cn
		$data['groups'] = $this->Roles->getBpmGroups(); 												// Grupos Bonita
		$data['roles'] = $this->Roles->getBpmRoles();   												// Roles Bonita
		$data['emp_core'] = $this->user_model->getInfoEmpCore();										// Empresas
		
		log_message('DEBUG','#TRAZA|MAIN|changelevel()  $data: >> '.json_encode($data));
		log_message('DEBUG','#TRAZA|MAIN|changelevel()  $data[emp_connect]: >> '.json_encode($data['emp_connect']));
		log_message('DEBUG','#TRAZA|MAIN|changelevel() DATOS DE USUARIO TRATADO  ->$data[user]: >> '.json_encode($data['user']));
		log_message('DEBUG','#TRAZA|MAIN|changelevel() DATOS DE USUARIO TRATADO  ->$data[mem_user]: >> '.json_encode($data['mem_user']));
		log_message('DEBUG','#TRAZA|MAIN|changelevel() DATOS DE USUARIO TRATADO  ->$data[dd_list]: >> '.json_encode($data['dd_list']));
		log_message('DEBUG','#TRAZA|MAIN|changelevel() DATOS DE USUARIO TRATADO  ->$data[groups]: >> '.json_encode($data['groups']));
		log_message('DEBUG','#TRAZA|MAIN|changelevel() DATOS DE USUARIO TRATADO  ->$data[roles]: >> '.json_encode($data['roles']));
		log_message('DEBUG','#TRAZA|MAIN|changelevel() DATOS DE USUARIO TRATADO  ->$data[emp_core]: >> '.json_encode($data['emp_core']));
		log_message('DEBUG','#TRAZA|MAIN|changelevel() DATOS DE USUARIO TRATADO  ->$data[group]: >> '.json_encode($data['group']));

 
		//check is admin or not
		if($dataLevel == "is_admin"){

			$this->form_validation->set_rules('email', 'Correo electrónico', 'required');
			$this->form_validation->set_rules('level', 'Nivel de usuario', 'required');

			if ($this->form_validation->run() == FALSE) {
				//log_message('DEBUG','#TRAZA|MAIN|changelevel()-> $this->form_validation->run() >> FALSE ');
					$this->load->view('header', $data);
					$this->load->view('navbar', $data);
					$this->load->view('container');
					//$this->load->view('changelevel', $data);
					$this->load->view('changeleveluser', $data);
					$this->load->view('footer');
			}else{
				//log_message('DEBUG','#TRAZA|MAIN|changelevel()-> $this->form_validation->run() >> TRUE ');
				$cleanPost['email'] = $this->input->post('email');
				$cleanPost['level'] = $this->input->post('level');
				if(!$this->user_model->updateUserLevel($cleanPost)){
					$this->session->set_flashdata('flash_message', 'Hubo un problema al actualizar el nivel de usuario.');
				}else{
					$this->session->set_flashdata('success_message', 'El nivel del usuario se actualizó correctamente.');
				}
				redirect(base_url().'main/changeleveluser/'.$id);
			}
		}else{
				redirect(base_url().'main/');
		}


	}
	//change level user
	public function changelevel(){
		$this->load->model('Roles');

		$data = $this->session->userdata;
		//check user level
		if(empty($data['role'])){
				redirect(base_url().'main/login/');
		}
		$dataLevel = $this->userlevel->checkLevel($data['role']);
		//check user level

		$data['title'] = "Cambiar Niveles de Usuarios";
		$data['users'] = $this->user_model->getUserData();
		$data['dd_list'] = $this->Roles->obtener();
		$data['groups'] = $this->Roles->getBpmGroups();
		$data['roles'] = $this->Roles->getBpmRoles();
			//log_message('DEBUG','#TRAZA|MAIN|changelevel()  $data: >> '.json_encode($data));
			//log_message('DEBUG','#TRAZA|MAIN|changelevel() DATOS DE USUARIO LOGUEADO->$dataLevel: >> '.json_encode($dataLevel));

		//check is admin or not
		if($dataLevel == "is_admin"){

					$this->form_validation->set_rules('email', 'Correo electrónico', 'required');
					$this->form_validation->set_rules('level', 'Nivel de usuario', 'required');

					if ($this->form_validation->run() == FALSE) {
						//log_message('DEBUG','#TRAZA|MAIN|changelevel()-> $this->form_validation->run() >> FALSE ');
							$this->load->view('header', $data);
							$this->load->view('navbar', $data);
							$this->load->view('container');
							$this->load->view('changelevel', $data);
							$this->load->view('footer');
					}else{
							//log_message('DEBUG','#TRAZA|MAIN|changelevel()-> $this->form_validation->run() >> TRUE ');
							$cleanPost['email'] = $this->input->post('email');
							$cleanPost['level'] = $this->input->post('level');
							if(!$this->user_model->updateUserLevel($cleanPost)){
									$this->session->set_flashdata('flash_message', 'Hubo un problema al actualizar el nivel de usuario.');
							}else{
									$this->session->set_flashdata('success_message', 'El nivel del usuario se actualizó correctamente.');
							}
							redirect(base_url().'main/changelevel');
					}
		}else{
				redirect(base_url().'main/');
		}
	}

	//ban or unban user
	public function banuser_old()
	{
		$data = $this->session->userdata;
		//check user level
		if(empty($data['role'])){
				redirect(base_url().'main/login/');
		}
		$dataLevel = $this->userlevel->checkLevel($data['role']);
		//check user level

		$data['title'] = "Habilitar/Deshabilitar Usuarios";
		$data['groups'] = $this->user_model->getUserDataAll();												// Grupos Bonita
		$data['emp_connect'] =  $this->user_model->gestMembershipsUserInfo($data['email'],1); 				// Empresas Usuario Conectado
		$data['usersList'] = $this->user_model->getListUserData();											// Listado de Usuarios
		$data['groupsBpm'] = $this->Roles->getBpmGroups();

		//log_message('DEBUG','#TRAZA|MAIN|banuser() ->$data[groups]: >> '.json_encode($data['groups']));
		//log_message('DEBUG','#TRAZA|MAIN|banuser() ->$data[groups]: >> '.json_encode($data['usersList']));
		//log_message('DEBUG','#TRAZA|MAIN|banuser() ->$data[groups]: >> '.json_encode($data['groupsBpm']));


		//check is admin or not
		if($dataLevel == "is_admin"){

					$this->form_validation->set_rules('email', 'Correo electrónico', 'required');
					$this->form_validation->set_rules('banuser', 'Habilitar o Deshabilitar', 'required');

					if ($this->form_validation->run() == FALSE) {
							$this->load->view('header', $data);
							$this->load->view('navbar', $data);
							$this->load->view('container');
							$this->load->view('banuser', $data);
							$this->load->view('footer');
					}else{
							$post = $this->input->post(NULL, TRUE);
							$cleanPost = $this->security->xss_clean($post);
							$cleanPost['email'] = $this->input->post('email');
							$cleanPost['banuser'] = $this->input->post('banuser');
							if(!$this->user_model->updateUserban($cleanPost)){
									$this->session->set_flashdata('flash_message', 'Error al borrar usuario');
							}else{
									$this->session->set_flashdata('success_message', 'El usuario ha sido borrado exitosamente.');
							}
							redirect(base_url().'main/banuser');
					}
		}else{
				redirect(base_url().'main/');
		}
	}

	//2022 Habilitar y deshabilitar usuarios
	public function banuser()
	{
		$data = $this->session->userdata;
		//check user level
		if(empty($data['role'])){
				redirect(base_url().'main/login/');
		}
		$dataLevel = $this->userlevel->checkLevel($data['role']);
		//check user level

		$data['title'] = "Habilitar/Deshabilitar Usuarios";
		$data['emp_connect'] =  $this->user_model->gestMembershipsUserInfo($data['email'],1); 				// Empresas Usuario Conectado
		$data['usersList'] = $this->user_model->getListUserData();											// Listado de Usuarios
		$data['groupsBpm'] = $this->Roles->getBpmGroups();													// Grupos Bonita

		//log_message('DEBUG','#TRAZA|MAIN|banuser() -> $data[emp_connect]: >> '.json_encode($data['emp_connect']));
		//log_message('DEBUG','#TRAZA|MAIN|banuser() -> $data[usersList]: >> '.json_encode($data['usersList']));
		//log_message('DEBUG','#TRAZA|MAIN|banuser() -> $data[groupsBpm]: >> '.json_encode($data['groupsBpm']));


		//check is admin or not
		if($dataLevel == "is_admin"){

					$this->form_validation->set_rules('email', 'Correo electrónico', 'required');
					$this->form_validation->set_rules('banuser', 'Habilitar o Deshabilitar', 'required');

					if ($this->form_validation->run() == FALSE) {
							$this->load->view('header', $data);
							$this->load->view('navbar', $data);
							$this->load->view('container');
							$this->load->view('banuser', $data);
							$this->load->view('footer');
					}else{
							$post = $this->input->post(NULL, TRUE);
							$cleanPost = $this->security->xss_clean($post);
							$cleanPost['email'] = $this->input->post('email');
							$cleanPost['banuser'] = $this->input->post('banuser');
							if(!$this->user_model->updateUserban($cleanPost)){
									$this->session->set_flashdata('flash_message', 'Error al borrar usuario');
							}else{

								if (strpos($cleanPost['banuser'], 'unban') !== false) {
									$this->session->set_flashdata('success_message', 'El usuario fue habilitado con éxito.');
								} elseif (strpos($cleanPost['banuser'], 'ban') !== false) {
									$this->session->set_flashdata('success_message', 'El usuario ha sido inhabilitado exitosamente.');
								}
							}
							redirect(base_url().'main/banuser');
					}
		}else{
				redirect(base_url().'main/');
		}
	}

	//edit pass user
	public function changeuser(){
			$data = $this->session->userdata;
			if(empty($data['role'])){
				redirect(base_url().'main/login/');
			}

			$dataInfo = array(
					'firstName'=> $data['first_name'],
					'id'=>$data['id'],
			);

			$data['title'] = "Editar perfil";
			$data['usersList'] = $this->user_model->getListUserData();

			/*$this->form_validation->set_rules('firstname', 'First Name', 'required');
			$this->form_validation->set_rules('lastname', 'Last Name', 'required');
			$this->form_validation->set_rules('email', 'Email', 'required|valid_email');*/
			$this->form_validation->set_rules('password', 'Contraseña', 'required|min_length[10]|password_strong');
			$this->form_validation->set_rules('passconf', 'Confirmación de contraseña', 'required|matches[password]');

			$data['groups'] = $this->user_model->getUserInfo($dataInfo['id']);
			log_message('DEBUG','#TRAZA|MAIN|changeuser()  $$data[groups]: >> '.json_encode($data['groups'])); 

			if ($this->form_validation->run() == FALSE) {
					$this->load->view('header', $data);
					$this->load->view('navbar', $data);
					$this->load->view('container');
					$this->load->view('changeuser', $data);
					$this->load->view('footer');
			}else{
					$this->load->library('password');
					$post = $this->input->post(NULL, TRUE);
					$cleanPost = $this->security->xss_clean($post);
					$hashed = $this->password->create_hash($cleanPost['password']);

					$cleanPost['password'] = $hashed;
					$cleanPost['user_id'] = $dataInfo['id'];
					$cleanPost['email'] = $this->input->post('email');
					$cleanPost['firstname'] = $this->input->post('firstname');
					$cleanPost['lastname'] = $this->input->post('lastname');

					unset($cleanPost['passconf']);

					log_message('DEBUG','#TRAZA|MAIN|changeuser()  $CleanPost: >> '.json_encode($cleanPost)); 
					

					if(!$this->user_model->updatePass($cleanPost)){
						
						$this->session->set_flashdata('flash_message', 'Tu contraseña no ha podido ser actualizada');
					}else{
						$this->session->set_flashdata('success_message', 'Tu contraseña ha sido actualizada.');
					}
					redirect(base_url().'main/changeuser');
			}
	}
	//edit user
	public function updateuser(){
			$data = $this->session->userdata;
			if(empty($data['role'])){
				redirect(base_url().'main/login/');
			}

			$dataInfo = array(
					'firstName'=> $data['first_name'],
					'id'=>$data['id'],
			);

			$data['title'] = "Editar perfil";
			$data['usersList'] = $this->user_model->getListUserData();

			$this->form_validation->set_rules('firstnameuser', 'Nombre', 'required');
			$this->form_validation->set_rules('lastnameuser', 'Apellido', 'required');
			$this->form_validation->set_rules('emailuser', 'Correo electrónico', 'required|valid_email');
			/*$this->form_validation->set_rules('password', 'Password', 'required|min_length[5]');
			$this->form_validation->set_rules('passconf', 'Password Confirmation', 'required|matches[password]');*/

			$data['groups'] = $this->user_model->getUserInfo($dataInfo['id']);
			log_message('DEBUG','#TRAZA|MAIN|changeuser()  $$data[groups]: >> '.json_encode($data['groups'])); 

			if ($this->form_validation->run() == FALSE) {
					$this->load->view('header', $data);
					$this->load->view('navbar', $data);
					$this->load->view('container');
					$this->load->view('changeuser', $data);
					$this->load->view('footer');
			}else{
					/*$this->load->library('password');
					$post = $this->input->post(NULL, TRUE);
					$cleanPost = $this->security->xss_clean($post);
					$hashed = $this->password->create_hash($cleanPost['password']);

					$cleanPost['password'] = $hashed;*/
					$cleanPost['user_id'] = $dataInfo['id'];
					$cleanPost['email'] = $this->input->post('emailuser');
					$cleanPost['firstname'] = $this->input->post('firstnameuser');
					$cleanPost['lastname'] = $this->input->post('lastnameuser');

					//Codificamos imagen
					$cleanPost['image_name'] = $_FILES['image']['name'];
					$cleanPost['ext'] = $_FILES['image']['type'];	
					$cleanPost['image'] = base64_encode(file_get_contents($_FILES['image']['tmp_name']));

					/*unset($cleanPost['passconf']);*/

					log_message('DEBUG','#TRAZA|MAIN|changeuser()  $CleanPost: >> '.json_encode($cleanPost)); 
					

					if(!$this->user_model->updateProfile($cleanPost)){
						
						$this->session->set_flashdata('flash_message', 'Tu perfil no ha podido ser actualizado');
					}else{
						$this->session->set_flashdata('success_message', 'Tu perfil ha sido actualizado.');
					}
					redirect(base_url().'main/changeuser');
			}
	}

	//open profile and gravatar user
	public function profile()
	{
			$data = $this->session->userdata;
			if(empty($data['role'])){
				redirect(base_url().'main/login/');
		}

			$data['title'] = "Perfil";
			$data['usersList'] = $this->user_model->getListUserData();
			$data['groupsBpm'] = $this->Roles->getBpmGroups();

			$this->load->view('header', $data);
			$this->load->view('navbar', $data);
			$this->load->view('container');
			$this->load->view('profile', $data);
			$this->load->view('footer');

	}

	//delete user
	public function deleteuser($id,$busines) {

		$data = $this->session->userdata;
		if(empty($data['role'])){
				redirect(base_url().'main/login/');
		}
		$dataLevel = $this->userlevel->checkLevel($data['role']);
		$emplevel = $data['groupBpm'];
		
		//log_message('DEBUG','#TRAZA|MAIN|deleteuser()  $data: >> '.json_encode($data)); 
		//log_message('DEBUG','#TRAZA|MAIN|deleteuser()  $data[groupBpm] >> '.$data['groupBpm']); 
		//log_message('DEBUG','#TRAZA|MAIN|deleteuser()  $data: >> '.$emplevel); 

		//check is admin or not
		if($dataLevel == "is_admin"){

			$data['user'] = $this->user_model->getUserInfo($id);
			$data['memberships'] = $this->user_model->getMembershipsUserInfoEmpresa($data['user']->email, $emplevel);

			log_message('DEBUG','#TRAZA|MAIN|deleteuser()  $data[user]: >> '.json_encode($data['user'])); 
			log_message('DEBUG','#TRAZA|MAIN|deleteuser()  $data[memberships]: >> '.json_encode($data['memberships'])); 
			log_message('DEBUG','#TRAZA|MAIN|deleteuser()  $data[groupBpm]: >> '.json_encode($data['memberships'])); 

			if($data['memberships']){
				$this->session->set_flashdata('flash_message', 'Error, Este usuario tiene roles de sistema en la empresa asignados!');
			}else{	
				/**Eliminar tabla seg.users_bisiness */
				$deleteUserBusines = $this->user_model->deleteUserBusines($data['user']->email,$busines);

				if(!$deleteUserBusines ){
					$this->session->set_flashdata('flash_message', 'Error, no se puede elminar el UserBusines del usuario '.$data['user']->email);
				}else{

					/*Mejora del Eliminado*/
					$deleteUserLocal =$this->user_model->deleteUser($id);
					//log_message('DEBUG','#TRAZA|MAIN|deleteuser()  $deleteUserLocal: >> '.json_encode($deleteUserLocal)); 
					if(!$deleteUserLocal ){
							$this->session->set_flashdata('flash_message', 'Error, no se puede elminar el usuario '.$data['user']->email);
					}else{
							$this->session->set_flashdata('success_message', 'Eliminado Correctamente.');
					}
				}
			}
			redirect(base_url().'main/users/');
		}else{
			redirect(base_url().'main/');
		}
	}

	public function edituser($id){

		$data = $this->session->userdata;
		if(empty($data['role'])){
				redirect(base_url().'main/login/');
		}

		//check user level
		if(empty($data['role'])){
				redirect(base_url().'main/login/');
		}
		$dataLevel = $this->userlevel->checkLevel($data['role']);

		//check is admin or not
		if($dataLevel == "is_admin"){
			$this->form_validation->set_rules('firstname', 'Nombre', 'required');
			$this->form_validation->set_rules('lastname', 'Apellido', 'required');
			$this->form_validation->set_rules('email', 'Correo electrónico', 'required|valid_email');
			$this->form_validation->set_rules('role', 'Rol', 'required');
			$this->form_validation->set_rules('password', 'Contraseña', 'required|min_length[10]|password_strong');
			$this->form_validation->set_rules('passconf', 'Confirmación de contraseña', 'required|matches[password]');

			$data['title'] = "Editar Usuario";

		}

	}



	public function adduserexterno()
	{
			$data = $this->session->userdata;
			if (empty($data['role'])) {
					redirect(base_url() . 'main/login/');
			}

			//check user level
			if (empty($data['role'])) {
					redirect(base_url() . 'main/login/');
			}
			$dataLevel = $this->userlevel->checkLevel($data['role']);
			//check user level

			//check is admin or not
			if ($dataLevel == "is_admin") {
					
					$this->form_validation->set_rules('email', 'Correo electrónico', 'required|valid_email');
					$this->form_validation->set_rules('password', 'Contraseña', 'required|min_length[10]|password_strong');
					$this->form_validation->set_rules('passconf', 'Confirmación de contraseña', 'required|matches[password]');

					$data['title'] = "Agregar Usuario";
					if ($this->form_validation->run() == false) {
							$this->load->view('header', $data);
							$this->load->view('navbar');
							$this->load->view('container');
							$this->load->view('usuario_externo', $data);
							$this->load->view('footer');
					} else {
							if ($this->user_model->isDuplicate($this->input->post('email'))) {
									$this->session->set_flashdata('flash_message', 'El correo del usuario ya existe en el sistema.');
									redirect(base_url() . 'main/adduserexterno');
							} else {
									$this->load->library('password');
									$post = $this->input->post(null, true);
									$cleanPost = $this->security->xss_clean($post);
									$hashed = $this->password->create_hash($cleanPost['password']);
									$cleanPost['nombre_razon'] = $this->input->post('nombre_razon');
									$cleanPost['email'] = $this->input->post('email');
									$cleanPost['telefono'] = $this->input->post('telefono');
									$cleanPost['cuit_empresa'] = $this->input->post('cuit_empresa');
									$cleanPost['usernick'] = $this->input->post('usernick');
									$cleanPost['banned_users'] = 'unban';
									$cleanPost['password'] = $hashed;
									unset($cleanPost['passconf']);

									//insert to database
									if (!$this->user_model->addUserExterno($cleanPost)) {
											$this->session->set_flashdata('flash_message', 'Hubo un problema al agregar el nuevo usuario.');
									} else {
											$this->session->set_flashdata('success_message', 'El usuario se agregó correctamente.');
									}
									redirect(base_url() . 'main/users/');
							}
							;
					}
			} else {
					redirect(base_url() . 'main/');
			}

	}

	/**
	* Elimina los roles de un usuario
	* @param array email - groupId - roleId
	* @return
	*/
	public function deleteLevelRolUser(){
		# code...
		$data = $this->session->userdata;

		$dataPost['email'] = $this->input->post('email');
		$dataRole = $this->input->post('dataRole');
		$dataRoleBpm = $this->input->post('dataRoleBpm');
		//$id = $this->user_model->getUserAllData($dataPost['email']);


		//log_message('DEBUG','#TRAZA|MAIN|deleteLevelRolUser()  $id: >> '.json_encode($id) );
		//log_message('DEBUG','#TRAZA|MAIN|deleteLevelRolUser()  $dataPost[email]: >> '.$dataPost['email'] );
		//log_message('DEBUG','#TRAZA|MAIN|deleteLevelRolUser()  $dataRole: >> '.json_encode($dataRole) );
		//log_message('DEBUG','#TRAZA|MAIN|deleteLevelRolUser()  $dataRoleBpm: >> '.json_encode($dataRoleBpm) );


		//redirect(base_url().'main/changeleveluser/'.$id);

		$bpmSession = defined('BPM_ROLES_SESSION_URL') ? BPM_ROLES_SESSION_URL : rawurlencode('X-Bonita-API-Token=658fcd51-ef8b-48c3-9606-1d89a88cf3e5;JSESSIONID=BCDEA4A05749709F4DFBDCBB58A527E8;bonita.tenant=1;');
		$payload = array(
			'email' => $dataPost['email'],
			'group' => $dataRole['group'],
			'role' => $dataRole['role'],
			'group_id' => (string) (isset($dataRoleBpm['group_id']) ? $dataRoleBpm['group_id'] : ''),
			'role_id' => (string) (isset($dataRoleBpm['role_id']) ? $dataRoleBpm['role_id'] : ''),
			'bpmSession' => $bpmSession
		);
		try {
			$response = $this->rest->callAPI('POST', API_CORE . '/rol/desasignar', $payload);
			return $response['status'] && (!isset($response['code']) || $response['code'] < 300);
		} catch (Exception $e) {
			log_message('ERROR', '#TRAZA|MAIN|deleteLevelRolUser >> ' . $e->getMessage());
			return false;
		}
		
	}
	/**
	* Asigna y Cambia Rol a un usuario nuevo
	* @param array email - level - dataRole - dataRoleBpm
	* @return 
	*/
	public function changeLevelRolUser(){
		$data = $this->session->userdata;

		$dataPost['email'] = $this->input->post('email');
		$dataPost['level'] = $this->input->post('level');
		
		$dataRole = $this->input->post('dataRole');
		$dataRole['usuario_app'] = userNick();
		$user = userNick();
		$dataRoleBpm = $this->input->post('dataRoleBpm');

		//Updatea el rol del usuario en la tabla de seg.users por email
		$userLevel = $this->user_model->updateUserLevel($dataPost);

		if(!$userLevel){
			return false;
		}
		$_POST['membership'] = array('email' => $dataPost['email'], 'group' => $dataRole['group'], 'role' => $dataRole['role']);
		$_POST['membershipBPM'] = array('group_id' => $dataRoleBpm['group_id'], 'role_id' => $dataRoleBpm['role_id']);
		return $this->guardarMembership();
		
	}
	//Recibe el objeto de json (array de roles)
	public function changeLevelRolUserObject(){
		$data = $this->session->userdata;

		$dataPost['email'] = $this->input->post('email');
		$dataPost['level'] = $this->input->post('level');

		$dataRole = $this->input->post('dataRole');
		if (!is_array($dataRole)) {
			$dataRole = array();
		}
		foreach ($dataRole as $i => $roleData) {
			$dataRole[$i]['usuario_app'] = userNick();
		}

		$dataRoleBpm = $this->input->post('dataRoleBpm');
		if (!is_array($dataRoleBpm)) {
			$dataRoleBpm = array();
		}

		$userLevel = $this->user_model->updateUserLevel($dataPost);

		if (!$userLevel) {
			$this->session->set_flashdata('flash_message', 'Fallo cambio de nivel');
			$this->_changeLevelRolUserObjectResponse(false, 'Fallo cambio de nivel');
			return false;
		}

		$cantRoles = count($dataRole);
		if ($cantRoles === 0) {
			$this->session->set_flashdata('success_message', 'Nivel actualizado.');
			$this->_changeLevelRolUserObjectResponse(true, 'Nivel actualizado.');
			return true;
		}

		$bpmSession = defined('BPM_ROLES_SESSION_URL') ? BPM_ROLES_SESSION_URL : rawurlencode('X-Bonita-API-Token=658fcd51-ef8b-48c3-9606-1d89a88cf3e5;JSESSIONID=BCDEA4A05749709F4DFBDCBB58A527E8;bonita.tenant=1;');
		$asignados = array();
		for ($i = 0; $i < $cantRoles; $i++) {
			$dataRoleBpmItem = isset($dataRoleBpm[$i]) ? $dataRoleBpm[$i] : (is_array($dataRoleBpm) && isset($dataRoleBpm[0]) ? $dataRoleBpm[0] : $dataRoleBpm);
			$payload = array(
				'email' => $dataPost['email'],
				'group' => $dataRole[$i]['group'],
				'role' => $dataRole[$i]['role'],
				'group_id' => (string) (isset($dataRoleBpmItem['group_id']) ? $dataRoleBpmItem['group_id'] : ''),
				'role_id' => (string) (isset($dataRoleBpmItem['role_id']) ? $dataRoleBpmItem['role_id'] : ''),
				'bpmSession' => $bpmSession
			);
			try {
				$response = $this->rest->callAPI('POST', API_CORE . '/rol/asignar', $payload);
				if (!$response['status'] || (isset($response['code']) && $response['code'] >= 300)) {
					foreach ($asignados as $a) {
						$this->rest->callAPI('POST', API_CORE . '/rol/desasignar', $a);
					}
					$this->session->set_flashdata('flash_message', 'Fallo asignación de roles.');
					$this->_changeLevelRolUserObjectResponse(false, 'Fallo asignación de roles.');
					return false;
				}
				$asignados[] = $payload;
			} catch (Exception $e) {
				foreach ($asignados as $a) {
					$this->rest->callAPI('POST', API_CORE . '/rol/desasignar', $a);
				}
				$this->session->set_flashdata('flash_message', 'Error: ' . $e->getMessage());
				$this->_changeLevelRolUserObjectResponse(false, 'Error al asignar roles.');
				return false;
			}
		}

		$this->session->set_flashdata('success_message', 'Roles Bpm asignados con exito.');
		$this->_changeLevelRolUserObjectResponse(true, 'Roles Bpm asignados con exito.');
		return true;
	}

	/**
	 * Envía respuesta HTTP para changeLevelRolUserObject (AJAX)
	 */
	private function _changeLevelRolUserObjectResponse($success, $message = '') {
		if ($this->input->is_ajax_request()) {
			$msg = $message ? $message : ($success ? 'Roles guardados correctamente.' : 'Error al guardar.');
			$this->output->set_status_header($success ? 200 : 400)
				->set_content_type('application/json')
				->set_output(json_encode(array('success' => $success, 'message' => $msg)));
		}
	}

	/**
	* Cambia nivel de usuario de Login
	* @param array email y nivel usuario
	* @return
	*/
	public function cambiarNivelUsr(){

		$cleanPost['email'] = $this->input->post('email');
		$cleanPost['level'] = $this->input->post('level');

		if(!$this->user_model->updateUserLevel($cleanPost)){
				$this->session->set_flashdata('flash_message', 'Fallo cambio de nivel');
				return false;
		}else{
				$this->session->set_flashdata('success_message', 'nivelCambiado con exito.');
				return true;
		}
	}


	/**
	* View para asociar rol BPM con usuario de sistema levanta pantalla
	* @param array usuario
	* @return int 
	*/
	public function associaterol($usr_id){
		
		$data = $this->session->userdata;
		$data['groups'] = $this->Rol->getBpmGroups();
		$data['roles'] = $this->Rol->getBpmRoles();
		$data['user_id'] = $usr_id;
		$this->load->view('header', $data);
		$this->load->view('navbar');
		$this->load->view('container', $data);
		$this->load->view('membership');		
		$this->load->view('footer');
		
	}

	/**
	* Devuelve Membresias de BPM (roles y grupos todos)
	* @param 
	* @return 
	*/
	public function getBPMroles(){
		$roles = $this->user_model->getBPMroles();
		return $roles;
	}

	/**
	* Asociar id usuario con roles de BPM
	* @param 
	* @return
	*/
	function guardarMembership(){

		$membership = $this->input->post('membership');
		$membershipBPM = $this->input->post('membershipBPM');
		if (!$membership || !$membershipBPM || empty($membership['email']) || empty($membership['group']) || empty($membership['role'])) {
			$this->_guardarMembershipError('Datos de membership incompletos.');
			return false;
		}

		$bpmSession = defined('BPM_ROLES_SESSION_URL') ? BPM_ROLES_SESSION_URL : rawurlencode('X-Bonita-API-Token=658fcd51-ef8b-48c3-9606-1d89a88cf3e5;JSESSIONID=BCDEA4A05749709F4DFBDCBB58A527E8;bonita.tenant=1;');
		$payload = array(
			'email' => $membership['email'],
			'group' => $membership['group'],
			'role' => $membership['role'],
			'group_id' => (string) (isset($membershipBPM['group_id']) ? $membershipBPM['group_id'] : ''),
			'role_id' => (string) (isset($membershipBPM['role_id']) ? $membershipBPM['role_id'] : ''),
			'bpmSession' => $bpmSession
		);

		try {
			$response = $this->rest->callAPI('POST', API_CORE . '/rol/asignar', $payload);
			if (!$response['status'] || (isset($response['code']) && $response['code'] >= 300)) {
				$msg = isset($response['data']) ? json_decode($response['data']) : null;
				$errMsg = ($msg && isset($msg->mensaje)) ? $msg->mensaje : 'Fallo al asignar rol en la API.';
				$this->_guardarMembershipError($errMsg);
				return false;
			}
			$this->_guardarMembershipSuccess();
			return true;
		} catch (Exception $e) {
			$this->_guardarMembershipError('Error al asignar rol: ' . $e->getMessage());
			return false;
		}
	}

	/**
	 * Envía respuesta de error para guardarMembership (AJAX o flash)
	 */
	private function _guardarMembershipError($msg) {
		log_message('ERROR', '#TRAZA | MAIN | guardarMembership >> ' . $msg);
		$this->session->set_flashdata('flash_message', $msg);
		if ($this->input->is_ajax_request()) {
			$this->output->set_status_header(400)->set_content_type('application/json')->set_output(json_encode(array('success' => false, 'message' => $msg)));
			return;
		}
	}

	/**
	 * Envía respuesta de éxito para guardarMembership (AJAX o flash)
	 */
	private function _guardarMembershipSuccess() {
		$this->session->set_flashdata('success_message', 'Rol asignado correctamente.');
		if ($this->input->is_ajax_request()) {
			$this->output->set_status_header(200)->set_content_type('application/json')->set_output(json_encode(array('success' => true, 'message' => 'Rol asignado correctamente.')));
			return;
		}
	}

	/**
	* Borra membresia vía API /rol/desasignar
	* @param array con datos de usuario (membership)
	* @return void (echo 'true' o 'false')
	*/	
	function borrarMembership(){
		$membership = $this->input->post('membership');
		if (!$membership || !isset($membership[0])) {
			echo false;
			return;
		}
		$m = $membership[0];
		$group_id = isset($m['group_id']) ? $m['group_id'] : $this->input->post('group_id');
		$role_id = isset($m['role_id']) ? $m['role_id'] : $this->input->post('role_id');
		$group_id = $group_id !== null && $group_id !== false ? $group_id : '';
		$role_id = $role_id !== null && $role_id !== false ? $role_id : '';
		if (empty($group_id) || empty($role_id)) {
			log_message('WARNING', '#TRAZA|MAIN|borrarMembership >> group_id o role_id faltantes, intentando con API');
		}
		$bpmSession = defined('BPM_ROLES_SESSION_URL') ? BPM_ROLES_SESSION_URL : rawurlencode('X-Bonita-API-Token=658fcd51-ef8b-48c3-9606-1d89a88cf3e5;JSESSIONID=BCDEA4A05749709F4DFBDCBB58A527E8;bonita.tenant=1;');
		$payload = array(
			'email' => $m['email'],
			'group' => $m['group'],
			'role' => $m['role'],
			'group_id' => (string) $group_id,
			'role_id' => (string) $role_id,
			'bpmSession' => $bpmSession
		);
		try {
			$response = $this->rest->callAPI('POST', API_CORE . '/rol/desasignar', $payload);
			$ok = $response['status'] && (!isset($response['code']) || $response['code'] < 300);
			echo $ok ? 'true' : 'false';
		} catch (Exception $e) {
			log_message('ERROR', '#TRAZA|MAIN|borrarMembership >> ' . $e->getMessage());
			echo 'false';
		}
	}

	//register new user from frontend
	public function register()
	{
		log_message('INFO', '#TRAZA|MAIN|register() >> Iniciando proceso de registro');
		
		$data['title'] = "Registro Nuevo Usuario";
		$this->load->library('curl');
		$this->load->library('recaptcha');
		$this->load->helper('flag');
		
		// Reglas de validación
		$this->form_validation->set_rules('firstname', 'Nombre', 'required');
		$this->form_validation->set_rules('lastname', 'Apellido', 'required');
		$this->form_validation->set_rules('email', 'Correo electrónico', 'required|valid_email');
		$this->form_validation->set_rules('reg_razon_social', 'Razón Social de la Empresa', 'required');
		$this->form_validation->set_rules('telefono', 'Teléfono', 'required');
		$this->form_validation->set_rules('reg_pais_id', 'País', 'required');

		$result = $this->user_model->getAllSettings();
		$sTl = $result->site_title;
		$data['recaptcha'] = $result->recaptcha;
		
		// Si es primera vez al entrar carga pantalla para registrarse
		if ($this->form_validation->run() == FALSE) {
			log_message('INFO', '#TRAZA|MAIN|register() >> Mostrando formulario de registro');
			
			// Obtener países desde REST_CORE
			$data['paises'] = $this->user_model->obtenerPaisesRegistracion();
			
			if (!$data['paises']) {
				log_message('ERROR', '#TRAZA|MAIN|register() >> Error al obtener países');
				$this->session->set_flashdata('flash_message', 'Error al cargar lista de países. Intente nuevamente.');
			}
			
			// Mantener valores del formulario después de error
			$data['form_data'] = array(
				'firstname' => $this->input->post('firstname'),
				'lastname' => $this->input->post('lastname'),
				'email' => $this->input->post('email'),
				'reg_razon_social' => $this->input->post('reg_razon_social'),
				'telefono' => $this->input->post('telefono'),
				'reg_pais_id' => $this->input->post('reg_pais_id')
			);
			
			$this->load->view('header', $data);
			// No cargar container.php para evitar contenedores Bootstrap con fondo azul
			$this->load->view('register', $data);
			// No cargar footer.php para evitar contenedores Bootstrap
			echo '</body></html>';
		} else {
			log_message('INFO', '#TRAZA|MAIN|register() >> Procesando datos de registro');
			
			if ($this->user_model->isDuplicate($this->input->post('email'))) {
				log_message('WARNING', '#TRAZA|MAIN|register() >> Email duplicado: ' . $this->input->post('email'));
				$this->session->set_flashdata('flash_message', 'El email que intenta registrar ya existe...');
				redirect(base_url() . 'main/register');
			} else {
				$post = $this->input->post(NULL, TRUE);
				$clean = $this->security->xss_clean($post);
				
				// Validar razón social contra core.empresa
				if ($this->user_model->existeRazonSocial($clean['reg_razon_social'], $clean['reg_pais_id'])) {
					log_message('WARNING', '#TRAZA|MAIN|register() >> Razón social duplicada: ' . $clean['reg_razon_social']);
					$this->session->set_flashdata('flash_message', 'La Razón Social ingresada ya existe en el sistema para el país solicitado');
					redirect(base_url() . 'main/register');
				}
				
				// Validar teléfono según país
				if (!$this->user_model->validarTelefonoPorPais($clean['telefono'], $clean['reg_pais_id'])) {
					log_message('WARNING', '#TRAZA|MAIN|register() >> Teléfono inválido para el país seleccionado');
					$this->session->set_flashdata('flash_message', 'El formato del teléfono no es válido para el país seleccionado.');
					redirect(base_url() . 'main/register');
				}

				if ($data['recaptcha'] == 'yes') {
					// recaptcha
					$recaptchaResponse = $this->input->post('g-recaptcha-response');
					$userIp = $_SERVER['REMOTE_ADDR'];
					$key = $this->recaptcha->secret;
					$url = "https://www.google.com/recaptcha/api/siteverify?secret=" . $key . "&response=" . $recaptchaResponse . "&remoteip=" . $userIp;
					$response = $this->curl->simple_get($url);
					$status = json_decode($response, true);

					// recaptcha check
					if ($status['success']) {
						log_message('INFO', '#TRAZA|MAIN|register() >> reCAPTCHA válido, procediendo con registro');
						$this->procesarRegistro($clean);
					} else {
						log_message('WARNING', '#TRAZA|MAIN|register() >> reCAPTCHA inválido');
						$this->session->set_flashdata('flash_message', 'Error en la validación reCAPTCHA. Intente nuevamente.');
						redirect(base_url() . 'main/register');
					}
				} else {
					log_message('INFO', '#TRAZA|MAIN|register() >> Sin reCAPTCHA, procediendo con registro');
					$this->procesarRegistro($clean);
				}
			}
		}
	}

	//if success new user register
	public function successregister()
	{
			log_message('info', '=== INICIO SUCCESSREGISTER ===');
			log_message('info', 'URL actual: ' . current_url());
			log_message('info', 'User Agent: ' . $_SERVER['HTTP_USER_AGENT']);
			log_message('info', 'Referer: ' . (isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'No referer'));
			
			$data['title'] = "Registro Exitoso";
			log_message('info', 'Cargando vistas: header, container, register-info, footer');
			
			$this->load->view('header', $data);
			$this->load->view('container');
			$this->load->view('register-info');
			$this->load->view('footer');
			
			log_message('info', '=== FIN SUCCESSREGISTER ===');
	}

	//if success after set password
	public function successresetpassword()
	{
			$data['title'] = "Contraseña Restablecida";
			$this->load->view('header', $data);
			$this->load->view('container');
			$this->load->view('reset-pass-info');
			$this->load->view('footer');
	}

	protected function _islocal(){
			return strpos($_SERVER['HTTP_HOST'], 'local');
	}

	//check if complate after add new user
	public function complete()
	{
			$token = base64_decode($this->uri->segment(4));
			$cleanToken = $this->security->xss_clean($token);

			$user_info = $this->user_model->isTokenValid($cleanToken); //either false or array();

			if(!$user_info){
					$this->session->set_flashdata('flash_message', 'Token invalido o expirado...');
					redirect(base_url().'main/login');
			}
			$data = array(
					'firstName'=> $user_info->first_name,
					'email'=>$user_info->email,
					'user_id'=>$user_info->id,
					'token'=>$this->base64url_encode($token)
			);

			$data['title'] = "Establecer Contraseña";

			$this->form_validation->set_rules('password', 'Contraseña', 'required|min_length[10]|password_strong');
			$this->form_validation->set_rules('passconf', 'Confirmación de contraseña', 'required|matches[password]');

			if ($this->form_validation->run() == FALSE) {
					$this->load->view('header', $data);
					$this->load->view('complete_password', $data);
					$this->load->view('footer');
			}else{
					$this->load->library('password');
					$post = $this->input->post(NULL, TRUE);

					$cleanPost = $this->security->xss_clean($post);
					$plainPassword = $cleanPost['password'];

					$hashed = $this->password->create_hash($cleanPost['password']);
					$cleanPost['password'] = $hashed;
					unset($cleanPost['passconf']);
					$userInfo = $this->user_model->updateUserInfo($cleanPost);

                    if(!$userInfo){
                            $this->session->set_flashdata('flash_message', 'Hubo un problema actualizando su Usuario...');
                            redirect(base_url().'main/login');
                    }

					/* BPM + AssetPlanner: usuario ya existe en PostgreSQL con password */
					try {
						$this->load->library('rest');
						$bpmSession = defined('BPM_ROLES_SESSION_URL') ? BPM_ROLES_SESSION_URL : rawurlencode('X-Bonita-API-Token=658fcd51-ef8b-48c3-9606-1d89a88cf3e5;JSESSIONID=BCDEA4A05749709F4DFBDCBB58A527E8;bonita.tenant=1;');
						$usernick = isset($userInfo->usernick) && trim((string) $userInfo->usernick) !== ''
							? $userInfo->usernick
							: strtolower(trim($userInfo->email));
						$payloadBpm = array(
							'bpmSession' => $bpmSession,
							'usuario' => array(
								'email' => $userInfo->email,
								'password' => $plainPassword,
								'firstname' => $userInfo->first_name,
								'lastname' => $userInfo->last_name,
								'usernick' => $usernick
							)
						);
						$respBpm = $this->rest->callAPI('POST', API_CORE . '/usuario/bpm-asset', $payloadBpm);
						if (!$respBpm['status']) {
							log_message('ERROR', '#TRAZA|MAIN|complete() >> POST usuario/bpm-asset falló | code: ' . (isset($respBpm['code']) ? $respBpm['code'] : 'n/a') . ' | ' . (isset($respBpm['data']) ? $respBpm['data'] : ''));
							$this->session->set_flashdata(
								'flash_message',
								'Tu cuenta quedó activada, pero no se pudo sincronizar con BPM en este momento. Podés continuar; si algo falla en procesos, contactá soporte.'
							);
						}
					} catch (Exception $ex) {
						log_message('ERROR', '#TRAZA|MAIN|complete() >> Excepción usuario/bpm-asset: ' . $ex->getMessage());
						$this->session->set_flashdata(
							'flash_message',
							'Tu cuenta quedó activada, pero hubo un error al contactar BPM. Podés continuar; si algo falla, volvé a intentar más tarde.'
						);
					}

					unset($userInfo->password);

                    foreach($userInfo as $key=>$val){
                            $this->session->set_userdata($key, $val);
                    }
                    
            // Redirigir a página de éxito con formulario
            redirect(base_url() . 'register/register_success');

			}
	}

	//check login failed or success
	public function login()
	{
			$data = $this->session->userdata();
			log_message('DEBUG','#Main/login | '.json_encode($data));
			
			/* Si hay email en sesión, el usuario ya está autenticado en esta app */
			if (!empty($data['email'])) {
				log_message('DEBUG','#Main/login Sesion Existente');
				redirect(DE);
			} else {
					$this->load->library('curl');
					$this->load->library('recaptcha');
					$this->form_validation->set_rules('email', 'Correo electrónico', 'required|valid_email');
					$this->form_validation->set_rules('password', 'Contraseña', 'required');

					$data['title'] = "Trazalog Tools!";

					//logo de login configurable en core tablas
					$tabla = $this->Tablas->obtenerTabla('configuraciones_ui');
					$data['logoEmpresa'] = $tabla[0]['valor'];

					//copyright footer de login configurable en core tablas
					$tabla = $this->Tablas->obtenerTabla('configuraciones_uifotterCopyright');
					$data['copyright'] = $tabla[0]['valor'];

					// si esan vacios los campos, carga pantalla login
					if($this->form_validation->run() == FALSE) {

							//log_message('DEBUG','#Main/login | Carga Login |'. json_encode($this->form_validation->run()) . '| '.json_encode($this->input->post()));
							// traigo los groups de BPM para lleba
							$data['empresas'] = $this->Roles->getBpmGroups();

							log_message('DEBUG','#TRAZA|MAIN|changelevel() DATOS DE USUARIO TRATADO  ->$data[empresas]: >> '.json_encode($data['empresas']));
							
							$this->load->view('header', $data);
							$this->load->view('container');
							$this->load->view('login', $data);
							$this->load->view('footer');
					}else{

							// toma los datos del form loguin y los procesa
							$post = $this->input->post();
							$clean = $this->security->xss_clean($post);
							// tomo empr_id y nombre
							$nom = explode("-", $clean['empr_id']);
							$empr_id = $nom[0];
							$empresa = $nom[1];
							$email = $clean['email'];

							// chequea si pertence el usuario a la empresa
							$logEmpresa = $this->user_model->chekEmpresa($empresa, $email);
							if(!$logEmpresa)
							{
								//log_message('ERROR','#Main/login | El usuario no corresponde a la empresa .');
								$this->session->set_flashdata('flash_message', 'El usuario no corresponde a la empresa seleccionada.');
								redirect(base_url().'main/login');
							}

							// guardo info de usuario
							$userInfo = $this->user_model->checkLogin($clean);
							//log_message('DEBUG','#Main/login | userInfo: '.json_encode($userInfo));
							//email o contraseña erroneo
							if(!$userInfo)
							{
									//log_message('ERROR','#Main/login | Email o contraseña erroneo.');
									$this->session->set_flashdata('flash_message', 'Correo o contraseña incorrectos.');
									redirect(base_url().'main/login');
							}
							// usuario baneado o no
							elseif($userInfo->banned_users == "ban")
							{
									//log_message('ERROR','MAIN|LOGIN >> USUARIO BANEADO EN EL SISTEMA');
									$this->session->set_flashdata('danger_message', 'Ud se encuentra temporalmente inhabilitado para este Sistema...');
									redirect(base_url().'main/login');
							}
							// correcto el usuario y no esta baneado
							elseif($userInfo && $userInfo->banned_users == "unban") //recaptcha check, success login, ban or unban
							{		// guardo id de empresa para agregar a la variable de sesion
									$userInfo->empr_id = $empr_id;
									$usernick = $userInfo->usernick;
									// Trae id de usr en BPM a partir de Nick
									$infoUser = $this->bpm->getUser($usernick);
									$userbpm = $infoUser['data']['id'];
									$groupbpm = $empresa;

									if ($userbpm) {
										$userInfo->userIdBpm = $userbpm;
										$userInfo->groupBpm = $groupbpm;
									} else {
										//log_message('ERROR','#TRAZA|MAIN|LOGIN|NO HAY USUARIO EN BPM CON EL NICK >> '.$usernick);
										$this->session->set_flashdata('flash_message', 'Error de inicio de sesión en BPM.');
										redirect(base_url().'main/login/');
									}
									
									// guardo info en variable de sesion
									foreach($userInfo as $key=>$val){
											$this->session->set_userdata($key, $val);
									}
									//log_message('DEBUG','#Main/checkLoginUser/');
									redirect(DE);
							}
							else
							{
									//log_message('ERROR','Something Error!');
									//log_message('ERROR','#MAIN|LOGIN | .');
									$this->session->set_flashdata('flash_message', 'Ocurrió un error inesperado.');
									redirect(base_url().'main/login/');
									exit;
							}
					}
		}
	}

	//Logout
	public function logout()
	{
			$dir = DS;#$this->session->userdata['direccionsalida'];
			$this->session->sess_destroy();
			redirect($dir);
	}

	//forgot password
	public function forgot()
	{
			$data['title'] = "Recuperar Contraseña";
			$this->load->library('curl');
			$this->load->library('recaptcha');
			$this->form_validation->set_rules('email', 'Correo electrónico', 'required|valid_email');
			
			$result = $this->user_model->getAllSettings();
			$sTl = $result->site_title;
			$data['recaptcha'] = $result->recaptcha;

			if($this->form_validation->run() == FALSE) {
					$this->load->view('header', $data);
					$this->load->view('container');
					$this->load->view('forgot');
					$this->load->view('footer');
			}else{
					$email = $this->input->post('email');
					$clean = $this->security->xss_clean($email);
					$userInfo = $this->user_model->getUserInfoByEmail($clean);

					if(!$userInfo){
							$this->session->set_flashdata('flash_message', 'No encontramos esa dirección de correo en el sistema.');
							redirect(base_url().'main/login');
					}

					if($userInfo->status != $this->status[1]){ //if status is not approved
							$this->session->set_flashdata('flash_message', 'Tu cuenta aún no está aprobada.');
							redirect(base_url().'main/login');
					}

					if($data['recaptcha'] == 'yes'){
							//recaptcha
							$recaptchaResponse = $this->input->post('g-recaptcha-response');
							$userIp = $_SERVER['REMOTE_ADDR'];
							$key = $this->recaptcha->secret;
							$url = "https://www.google.com/recaptcha/api/siteverify?secret=".$key."&response=".$recaptchaResponse."&remoteip=".$userIp; //link
							$response = $this->curl->simple_get($url);
							$status= json_decode($response, true);

							//recaptcha check
							if($status['success']){

									//generate token
									$token = $this->user_model->insertToken($userInfo->id);
									$qstring = $this->base64url_encode($token);
									$url = base_url() . 'main/reset_password/token/' . $qstring;
									$link = '<a href="' . $url . '">' . $url . '</a>';

									$this->load->library('email');
									$this->load->library('sendmail');
									
									$message = $this->sendmail->sendForgot($this->input->post('lastname'),$this->input->post('email'),$link,$sTl);
									$to_email = $this->input->post('email');
									$this->email->from($this->config->item('forgot'), 'Restablecer Contraseña - ' . $this->input->post('firstname') .' '. $this->input->post('lastname')); //from sender, title email
									$this->email->to($to_email);
									$this->email->subject('Restablecer Contraseña');
									$this->email->message($message);
									$this->email->set_mailtype("html");

									if($this->email->send()){
											redirect(base_url().'main/successresetpassword/');
									}else{
											$this->session->set_flashdata('flash_message', 'Hubo un problema al enviar el correo.');
											exit;
									}
							}else{
									//recaptcha failed
									$this->session->set_flashdata('flash_message', 'La validación de Google reCAPTCHA falló. Intentá nuevamente.');
									redirect(base_url().'main/register/');
									exit;
							}
					}else{
							//generate token
							$token = $this->user_model->insertToken($userInfo->id);
							$qstring = $this->base64url_encode($token);
							$url = base_url() . 'main/reset_password/token/' . $qstring;
							$link = '<a href="' . $url . '">' . $url . '</a>';

							$this->load->library('email');
							$this->load->library('sendmail');
							
							$message = $this->sendmail->sendForgot($this->input->post('lastname'),$this->input->post('email'),$link,$sTl);
							$to_email = $this->input->post('email');
							$this->email->from($this->config->item('forgot'), 'Restablecer Contraseña - ' . $this->input->post('firstname') .' '. $this->input->post('lastname')); //from sender, title email
							$this->email->to($to_email);
							$this->email->subject('Restablecer Contraseña');
							$this->email->message($message);
							$this->email->set_mailtype("html");

							if($this->email->send()){
									redirect(base_url().'main/successresetpassword/');
							}else{
									$this->session->set_flashdata('flash_message', 'Hubo un problema al enviar el correo.');
									exit;
							}
					}
					
			}

	}

	//reset password
	public function reset_password()
	{
			$token = $this->base64url_decode($this->uri->segment(4));
			$cleanToken = $this->security->xss_clean($token);
			$user_info = $this->user_model->isTokenValid($cleanToken); //either false or array();

			if(!$user_info){
					$this->session->set_flashdata('flash_message', 'El token es inválido o expiró.');
					redirect(base_url().'main/login');
			}
			$data = array(
					'firstName'=> $user_info->first_name,
					'email'=>$user_info->email,
					//'user_id'=>$user_info->id,
					'token'=>$this->base64url_encode($token)
			);

			$data['title'] = "Restablecer Contraseña";
			$this->form_validation->set_rules('password', 'Contraseña', 'required|min_length[10]|password_strong');
			$this->form_validation->set_rules('passconf', 'Confirmación de contraseña', 'required|matches[password]');

			if ($this->form_validation->run() == FALSE) {
					$this->load->view('header', $data);
					$this->load->view('container');
					$this->load->view('reset_password', $data);
					$this->load->view('footer');
			}else{
					$this->load->library('password');
					$post = $this->input->post(NULL, TRUE);
					$cleanPost = $this->security->xss_clean($post);
					$hashed = $this->password->create_hash($cleanPost['password']);
					$cleanPost['password'] = $hashed;
					$cleanPost['user_id'] = $user_info->id;
					unset($cleanPost['passconf']);
					if(!$this->user_model->updatePassword($cleanPost)){
							$this->session->set_flashdata('flash_message', 'Hubo un problema al actualizar tu contraseña.');
					}else{
							$this->session->set_flashdata('success_message', 'Tu contraseña se actualizó correctamente. Ya podés iniciar sesión.');
					}
					redirect(base_url().'main/checkLoginUser');
			}
	}

	public function base64url_encode($data) {
		return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
	}

	public function base64url_decode($data) {
		return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
	}

	/**
	 * Procesa el registro del usuario
	 * @param array $clean Datos limpios del formulario
	 */
	public function procesarRegistro($clean)
	{
		try {
			// Evita mostrar “Registro exitoso” de un intento anterior si este POST falla después
			foreach (array('success_message', 'flash_message', 'danger_message') as $k) {
				$this->session->unset_userdata($k);
			}

			log_message('INFO', '#TRAZA|MAIN|procesarRegistro() >> INICIANDO - Procesando registro de usuario');
			log_message('INFO', '#TRAZA|MAIN|procesarRegistro() >> Datos del usuario: ' . json_encode($clean));
			
			// Validar que tenemos email
			if (empty($clean['email'])) {
				log_message('ERROR', '#TRAZA|MAIN|procesarRegistro() >> Email vacío o no proporcionado');
				throw new Exception('Email no proporcionado');
			}
			
			// insert usuario + token vía API (POST /usuario/registro + token enviado por PHP)
			log_message('INFO', '#TRAZA|MAIN|procesarRegistro() >> Creando usuario y token vía API_CORE...');
			$this->load->library('rest');
			$token_30 = substr(sha1(rand()), 0, 30);
			// El usuario que se auto-registra es el Administrador de su empresa.
			// roles[0]='4' (operativo), roles[1]='1' (administrador). Usamos '1'.
			$adminRole = isset($this->roles[1]) ? $this->roles[1] : '1';
			$payloadReg = array(
				'usuario' => array(
					'firstname' => $clean['firstname'],
					'lastname' => $clean['lastname'],
					'email' => $clean['email'],
					'telefono' => isset($clean['telefono']) ? $clean['telefono'] : '',
					'reg_pais_id' => isset($clean['reg_pais_id']) ? $clean['reg_pais_id'] : '',
					'reg_razon_social' => isset($clean['reg_razon_social']) ? $clean['reg_razon_social'] : '',
					'role' => $adminRole,
					'status' => isset($this->status[0]) ? $this->status[0] : '',
					'banned_users' => (isset($this->user_model->banned_users[0]) ? $this->user_model->banned_users[0] : 'unban'),
					'usernick' => ''
				),
				'token' => $token_30
			);
			$respReg = $this->rest->callAPI('POST', API_CORE . '/usuario/registro', $payloadReg);
			if (!$respReg['status'] || empty($respReg['data'])) {
				$snippet = (isset($respReg['data']) && is_string($respReg['data'])) ? substr($respReg['data'], 0, 800) : json_encode(isset($respReg['data']) ? $respReg['data'] : null);
				log_message('ERROR', '#TRAZA|MAIN|REGISTRO_FALLO|API| email=' . $clean['email'] . ' | HTTP=' . (isset($respReg['code']) ? $respReg['code'] : 'n/a') . ' | body=' . $snippet);
				log_message('ERROR', '#TRAZA|MAIN|procesarRegistro() >> API usuario/registro falló | code: ' . (isset($respReg['code']) ? $respReg['code'] : 'n/a') . ' | body: ' . (isset($respReg['data']) ? $respReg['data'] : ''));
				throw new Exception('Error al insertar usuario en la base de datos (API)');
			}
			$bodyReg = json_decode($respReg['data']);
			if (!$bodyReg || !isset($bodyReg->respuesta->usr_id)) {
				$snippet = (isset($respReg['data']) && is_string($respReg['data'])) ? substr($respReg['data'], 0, 800) : '';
				log_message('ERROR', '#TRAZA|MAIN|REGISTRO_FALLO|RESPUESTA| email=' . $clean['email'] . ' | json=' . $snippet);
				log_message('ERROR', '#TRAZA|MAIN|procesarRegistro() >> Respuesta inesperada API | ' . $respReg['data']);
				throw new Exception('Error al insertar usuario en la base de datos (respuesta API)');
			}
			$id = (int) $bodyReg->respuesta->usr_id;
			$token = $token_30 . $id;
			log_message('INFO', '#TRAZA|MAIN|procesarRegistro() >> Usuario insertado con ID: ' . $id . ' (token 30 chars + id)');

			// generate token
			$qstring = $this->base64url_encode($token);
			$url = base_url() . 'main/complete/token/' . $qstring;
			$link = '<a href="' . $url . '">' . $url . '</a>';
			log_message('INFO', '#TRAZA|MAIN|procesarRegistro() >> URL de activación generada: ' . $url);

			// Cargar biblioteca de email
			log_message('INFO', '#TRAZA|MAIN|procesarRegistro() >> Cargando biblioteca email...');
			$this->load->library('email');
			log_message('INFO', '#TRAZA|MAIN|procesarRegistro() >> Biblioteca email cargada');
			
			// Usar configuración global (protocol sendmail) definida en application/config/email.php
			log_message('INFO', '#TRAZA|MAIN|procesarRegistro() >> Configurando email...');
			$this->email->set_mailtype('html');
			$this->email->from('register@trazalog.com', 'Trazalog Tools');
			$this->email->to($clean['email']);
			$this->email->subject('Activar cuenta en Trazalog.com');
			
			// Crear mensaje HTML con logo y traducción
			$logo_url = base_url() . (defined('REGISTER_IMG_EMAIL_LOGO') ? REGISTER_IMG_EMAIL_LOGO : 'public/img/logotzl.png');
			$message = '
			<html>
			<head>
				<meta charset="UTF-8">
				<title>Activar cuenta en Trazalog.com</title>
			</head>
			<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
				<div style="text-align: center; margin-bottom: 30px;">
					<img src="' . $logo_url . '" alt="Trazalog Tools" style="max-width: 200px; height: auto;">
				</div>
				
				<h2 style="color: #2c3e50;">¡Hola, ' . $clean['firstname'] . '!</h2>
				
				<p>¡Bienvenido! Te has registrado en nuestro sitio web con la siguiente información:</p>
				
				<div style="background-color: #f8f9fa; padding: 15px; border-radius: 5px; margin: 20px 0;">
					<p><strong>Usuario:</strong> ' . $clean['email'] . '</p>
					<p><strong>Contraseña:</strong> (No configurada)</p>
				</div>
				
				<p>Antes de poder iniciar sesión, necesitas activar y configurar tu contraseña haciendo clic en el siguiente enlace:</p>
				
				<div style="text-align: center; margin: 30px 0;">
					<a href="' . $url . '" style="background-color: #3498db; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; display: inline-block;">Activar mi cuenta</a>
				</div>
				
				<p>O copia y pega esta URL en tu navegador:</p>
				<p style="word-break: break-all; color: #7f8c8d;">' . $url . '</p>
				
				<hr style="border: none; border-top: 1px solid #ecf0f1; margin: 30px 0;">
				
				<p style="color: #7f8c8d; font-size: 14px;">Atentamente,<br>El equipo de Trazalog Tools</p>
			</body>
			</html>';
			
			$this->email->message($message);

			log_message('INFO', '#TRAZA|MAIN|procesarRegistro() >> Intentando enviar email a: ' . $clean['email']);
			log_message('INFO', '#TRAZA|MAIN|REGISTRO_ACTIVACION_URL| ' . $url);

			// Mismo criterio que commit 033d460 (registro freemium): send() y print_debugger() solo si falla.
			// No borrar usuario/token si falla el mail (el usuario puede activar por URL desde log o reintento).
			if ($this->email->send()) {
				log_message('INFO', '#TRAZA|MAIN|procesarRegistro() >> Email de activación enviado correctamente');
				log_message('INFO', '#TRAZA|MAIN|REGISTRO_OK| email=' . $clean['email'] . ' | usr_id=' . $id);
				$this->session->unset_userdata('flash_message');
				$this->session->unset_userdata('danger_message');
				$this->session->set_flashdata('success_message', 'Registro exitoso! Revise su email para activar su cuenta.');
			} else {
				log_message('ERROR', '#TRAZA|MAIN|procesarRegistro() >> Error al enviar email: ' . $this->email->print_debugger());
				$this->session->set_flashdata('flash_message', 'Error al enviar email de activación. Contacte al administrador.');
			}

			log_message('INFO', '#TRAZA|MAIN|procesarRegistro() >> Redirigiendo a página de registro');
			redirect(base_url() . 'main/register');
			
		} catch (Exception $e) {
			$emailLog = isset($clean['email']) ? $clean['email'] : '(sin email en $clean)';
			log_message('ERROR', '#TRAZA|MAIN|REGISTRO_FALLO|EXCEPCION| email=' . $emailLog . ' | msg=' . $e->getMessage());
			log_message('ERROR', '#TRAZA|MAIN|procesarRegistro() >> EXCEPCIÓN CAPTURADA: ' . $e->getMessage());
			log_message('ERROR', '#TRAZA|MAIN|procesarRegistro() >> Archivo: ' . $e->getFile() . ' Línea: ' . $e->getLine());
			log_message('ERROR', '#TRAZA|MAIN|procesarRegistro() >> Stack trace: ' . $e->getTraceAsString());
			$this->session->unset_userdata('success_message');
			$this->session->set_flashdata('danger_message', 'No se pudo completar el registro. ' . $e->getMessage());
			redirect(base_url() . 'main/register');
		}
	}

	/**
	 * Crea una instancia del formulario de registro
	 */

	/**
	 * Guarda el formulario de registro y actualiza el usuario
	 */
	public function guardarFormularioRegistro()
	{
		$user_id = $this->session->userdata('temp_user_id');
		$info_id = $this->session->userdata('temp_info_id');
		
		if (!$user_id || !$info_id) {
			echo json_encode(['success' => false, 'message' => 'Sesión inválida o info_id faltante']);
			return;
		}
		
		try {
			// Cargar el helper y modelo del módulo
			require_once(APPPATH . 'modules/traz-comp-formularios/helpers/form_helper.php');
			$this->load->model('traz-comp-formularios/Forms');
			
			// Obtener los datos del formulario
			$form_data = $this->input->post();
			
			// ACTUALIZAR la instancia existente
			$this->Forms->actualizar($info_id, $form_data);
			
			// Actualizar el usuario con el info_id
			$this->db->where('id', $user_id);
			$this->db->set('reg_info_id', $info_id);
			$this->db->update('seg.users');
			
			// Limpiar la sesión temporal
			$this->session->unset_userdata('temp_user_id');
			$this->session->unset_userdata('temp_info_id');
			
			log_message('INFO', '#TRAZA|MAIN|guardarFormularioRegistro() >> Formulario actualizado. user_id: ' . $user_id . ', info_id: ' . $info_id);
			
			echo json_encode(['success' => true, 'message' => 'Formulario guardado correctamente']);
			
		} catch (Exception $e) {
			log_message('ERROR', '#TRAZA|MAIN|guardarFormularioRegistro() >> Excepción: ' . $e->getMessage());
			echo json_encode(['success' => false, 'message' => 'Error al guardar formulario: ' . $e->getMessage()]);
		}
	}

}