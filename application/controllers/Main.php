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
				echo 'Error de Redireccionamiento';
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
					$this->email->from($this->config->item('register'), 'New sign-in! from '.$browser.'');
					$this->email->to($to_email);
					$this->email->subject('New sign-in! from '.$browser.'');
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

			$data['title'] = "Settings";
			$data['usersList'] = $this->user_model->getListUserData();
			$data['groupsBpm'] = $this->Roles->getBpmGroups();

			$this->form_validation->set_rules('site_title', 'Site Title', 'required');
			$this->form_validation->set_rules('timezone', 'Timezone', 'required');
			$this->form_validation->set_rules('recaptcha', 'Recaptcha', 'required');
			$this->form_validation->set_rules('theme', 'Theme', 'required');

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
					$data['timezone'] = "Select a time zone";
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
									$this->session->set_flashdata('flash_message', 'There was a problem updating your data!');
							}else{
									$this->session->set_flashdata('success_message', 'Your data has been updated.');
							}
							redirect(base_url().'main/settings/');
					}
		}

	}

	//user list
	public function users()	{
		$data = $this->session->userdata;
		$data['title'] = "Lista de Usuarios";
		$data['usersList'] = $this->user_model->getListUserData();
		$data['groupsBpm'] = $this->Roles->getBpmGroups();
		$data['emp_connect'] =  $this->user_model->gestMembershipsUserInfo($data['email'],1);    //Empresas del conectado


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
					$this->form_validation->set_rules('firstname', 'First Name', 'required');
					$this->form_validation->set_rules('lastname', 'Last Name', 'required');
					$this->form_validation->set_rules('email', 'Email', 'required|valid_email');
					$this->form_validation->set_rules('role', 'role', 'required');
					$this->form_validation->set_rules('password', 'Password', 'required|min_length[5]');
					$this->form_validation->set_rules('passconf', 'Password Confirmation', 'required|matches[password]');

					
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
									$this->load->library('password');
									$post = $this->input->post(NULL, TRUE);
									$cleanPost = $this->security->xss_clean($post);
									$hashed = $this->password->create_hash($cleanPost['password']);
									$cleanPost['email'] = $this->input->post('email');
									$cleanPost['role'] = $this->input->post('role');
									$cleanPost['firstname'] = $this->input->post('firstname');
									$cleanPost['lastname'] = $this->input->post('lastname');
									$cleanPost['telefono'] = $this->input->post('telefono');
									$cleanPost['usernick'] = $this->input->post('usernick');
									
									//Codificamos imagen
									$cleanPost['image_name'] = $_FILES['image']['name'];
									$cleanPost['ext'] = $_FILES['image']['type'];	
									$cleanPost['image'] = base64_encode(file_get_contents($_FILES['image']['tmp_name']));
									
									$cleanPost['dni'] = $this->input->post('dni');
									$cleanPost['business'] = $this->input->post('business');
									$cleanPost['banned_users'] = 'unban';
									$cleanPost['password'] = $hashed;
									$cleanPost['depo_id'] = $this->input->post('depo_id');
									unset($cleanPost['passconf']);


									//log_message('DEBUG','#TRAZA|MAIN|ADDUSER() >> $cleanPost '.json_encode($cleanPost));
									//log_message('DEBUG','#TRAZA|MAIN|ADDUSER() >> $extension '.$cleanPost['ext']);
									//log_message('DEBUG','#TRAZA|MAIN|ADDUSER() >> $images '.$cleanPost['images']);
									//log_message('DEBUG','#TRAZA|MAIN|ADDUSER() >> $FILES '.json_encode($_FILES['image']));

									
									//insert to database
									$usr_id = $this->user_model->addUser($cleanPost);

									//

									//crea usr en BPM
									if($usr_id){
											$status = $this->user_model->crearUsrBPM($cleanPost);
											if ($status) {
												$this->session->set_flashdata('flash_message', 'Usuario creado exitosamente...');
												redirect(base_url().'main/users/'.$usr_id);
											} else {
												//log_message('ERROR','#TRAZA|MAIN|ADDUSER >> ERROR: NO SE PUDO CREAR USUARIO EN BPM');
												$this->session->set_flashdata('danger_message', 'Error al crear usuario en BPM');
											}
									}

									//redirect(base_url().'main/users/'.$usr_id);
									redirect(base_url().'main/users/');
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
		
		//log_message('DEBUG','#TRAZA|MAIN|changelevel()  $data: >> '.json_encode($data));
		//log_message('DEBUG','#TRAZA|MAIN|changelevel()  $data[emp_connect]: >> '.json_encode($data['emp_connect']));
		//log_message('DEBUG','#TRAZA|MAIN|changelevel() DATOS DE USUARIO TRATADO  ->$data[user]: >> '.json_encode($data['user']));
		//log_message('DEBUG','#TRAZA|MAIN|changelevel() DATOS DE USUARIO TRATADO  ->$data[mem_user]: >> '.json_encode($data['mem_user']));
		//log_message('DEBUG','#TRAZA|MAIN|changelevel() DATOS DE USUARIO TRATADO  ->$data[dd_list]: >> '.json_encode($data['dd_list']));
		//log_message('DEBUG','#TRAZA|MAIN|changelevel() DATOS DE USUARIO TRATADO  ->$data[groups]: >> '.json_encode($data['groups']));
		//log_message('DEBUG','#TRAZA|MAIN|changelevel() DATOS DE USUARIO TRATADO  ->$data[roles]: >> '.json_encode($data['roles']));
		//log_message('DEBUG','#TRAZA|MAIN|changelevel() DATOS DE USUARIO TRATADO  ->$data[emp_core]: >> '.json_encode($data['emp_core']));
		//log_message('DEBUG','#TRAZA|MAIN|changelevel() DATOS DE USUARIO TRATADO  ->$data[group]: >> '.json_encode($data['group']));

 
		//check is admin or not
		if($dataLevel == "is_admin"){

			$this->form_validation->set_rules('email', 'Your Email', 'required');
			$this->form_validation->set_rules('level', 'User Level', 'required');

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
					$this->session->set_flashdata('flash_message', 'There was a problem updating the level user');
				}else{
					$this->session->set_flashdata('success_message', 'The level user has been updated.');
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

					$this->form_validation->set_rules('email', 'Your Email', 'required');
					$this->form_validation->set_rules('level', 'User Level', 'required');

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
									$this->session->set_flashdata('flash_message', 'There was a problem updating the level user');
							}else{
									$this->session->set_flashdata('success_message', 'The level user has been updated.');
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

					$this->form_validation->set_rules('email', 'Your Email', 'required');
					$this->form_validation->set_rules('banuser', 'Ban or Unban', 'required');

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

					$this->form_validation->set_rules('email', 'Your Email', 'required');
					$this->form_validation->set_rules('banuser', 'Ban or Unban', 'required');

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

	//edit user
	public function changeuser()
	{
			$data = $this->session->userdata;
			if(empty($data['role'])){
				redirect(base_url().'main/login/');
		}

			$dataInfo = array(
					'firstName'=> $data['first_name'],
					'id'=>$data['id'],
			);

			$data['title'] = "Change Password";
			$data['usersList'] = $this->user_model->getListUserData();

			$this->form_validation->set_rules('firstname', 'First Name', 'required');
			$this->form_validation->set_rules('lastname', 'Last Name', 'required');
			$this->form_validation->set_rules('email', 'Email', 'required|valid_email');
			$this->form_validation->set_rules('password', 'Password', 'required|min_length[5]');
			$this->form_validation->set_rules('passconf', 'Password Confirmation', 'required|matches[password]');

			$data['groups'] = $this->user_model->getUserInfo($dataInfo['id']);

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
					if(!$this->user_model->updateProfile($cleanPost)){
							$this->session->set_flashdata('flash_message', 'There was a problem updating your profile');
					}else{
							$this->session->set_flashdata('success_message', 'Your profile has been updated.');
					}
					redirect(base_url().'main/');
			}
	}

	//open profile and gravatar user
	public function profile()
	{
			$data = $this->session->userdata;
			if(empty($data['role'])){
				redirect(base_url().'main/login/');
		}

			$data['title'] = "Profile";
			$data['usersList'] = $this->user_model->getListUserData();
			$data['groupsBpm'] = $this->Roles->getBpmGroups();

			$this->load->view('header', $data);
			$this->load->view('navbar', $data);
			$this->load->view('container');
			$this->load->view('profile', $data);
			$this->load->view('footer');

	}

	//delete user
	public function deleteuser($id) {

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

			//log_message('DEBUG','#TRAZA|MAIN|deleteuser()  $data[user]: >> '.json_encode($data['user'])); 
			//log_message('DEBUG','#TRAZA|MAIN|deleteuser()  $data[memberships]: >> '.json_encode($data['memberships'])); 

			if($data['memberships']){
				$this->session->set_flashdata('flash_message', 'Error, Este Usuario tiene roles de sistema en la empresa asignados!');
			}else{	
				/**Eliminar tabla seg.users_bisiness */
				$deleteUserBusines = $this->user_model->deleteUserBusines($data['user']->email,$data['groupBpm']);

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
			$this->form_validation->set_rules('firstname', 'First Name', 'required');
			$this->form_validation->set_rules('lastname', 'Last Name', 'required');
			$this->form_validation->set_rules('email', 'Email', 'required|valid_email');
			$this->form_validation->set_rules('role', 'role', 'required');
			$this->form_validation->set_rules('password', 'Password', 'required|min_length[5]');
			$this->form_validation->set_rules('passconf', 'Password Confirmation', 'required|matches[password]');

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
					
					$this->form_validation->set_rules('email', 'Email', 'required|valid_email');
					$this->form_validation->set_rules('password', 'Password', 'required|min_length[5]');
					$this->form_validation->set_rules('passconf', 'Password Confirmation', 'required|matches[password]');

					$data['title'] = "Add User";
					if ($this->form_validation->run() == false) {
							$this->load->view('header', $data);
							$this->load->view('navbar');
							$this->load->view('container');
							$this->load->view('usuario_externo', $data);
							$this->load->view('footer');
					} else {
							if ($this->user_model->isDuplicate($this->input->post('email'))) {
									$this->session->set_flashdata('flash_message', 'User email already exists');
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
											$this->session->set_flashdata('flash_message', 'There was a problem add new user');
									} else {
											$this->session->set_flashdata('success_message', 'New user has been added.');
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

		//Eliminar en Bonita
		$this->load->model('Roles');
		$infoUser = $this->user_model->getUserInfoByEmail($dataPost['email']);

		$deleteRolBpm = $this->Roles->deleteMembershipBPM($dataRoleBpm, $infoUser->usernick);
		$rspDeleteBPM = json_encode($deleteRolBpm);
		//log_message('DEBUG','#TRAZA|MAIN|deleteLevelRolUser()  $deleteRolBpm: >> '.($rspDeleteBPM) );

		if(is_null($rspDeleteBPM) ){
			//$this->session->set_flashdata('flash_message', 'Fallo eliminación de roles Bpm.');
			return false;
		}else{
			//$this->session->set_flashdata('success_message', 'Rol Bpm eliminado con exito.');
			//return true;
			$deleteRolUser = $this->user_model->borrarMembership($dataRole);
			//log_message('DEBUG','#TRAZA|MAIN|deleteLevelRolUser()  $deleteRolUser: >> '.json_encode( $deleteRolUser) );	

			if(!$deleteRolUser){
				//$this->session->set_flashdata('flash_message', 'Error Eliminación ' .$dataPost['email']); 
				return false;
			}else{
				//$this->session->set_flashdata('success_message', 'Eliminado Correctamente '.$dataPost['email']);
				return true;
			}
			

		}
		
	}
	/**
	* Asigna y Cambia Rol a un usuario nuevo
	* @param array email - level - dataRole - dataRoleBpm
	* @return 
	*/
	public function changeLevelRolUser(){
		# code...
		$data = $this->session->userdata;

		$dataPost['email'] = $this->input->post('email');
		$dataPost['level'] = $this->input->post('level');
		
		$dataRole = $this->input->post('dataRole');
		$dataRole['usuario_app'] = userNick();
		$user = userNick();

		$dataRoleBpm = $this->input->post('dataRoleBpm');


		//log_message('DEBUG','#TRAZA|MAIN|changeLevelRolUser()  $data[email]: >> '.$dataPost['email'] ); 
		//log_message('DEBUG','#TRAZA|MAIN|changeLevelRolUser()  $data[level]: >> '.$dataPost['level'] ); 
		//log_message('DEBUG','#TRAZA|MAIN|changeLevelRolUser()  $data[dataRole]: >> '.json_encode( $dataRole) );
		//log_message('DEBUG','#TRAZA|MAIN|changeLevelRolUser()  $data[dataRoleBpm]: >> '. json_encode($dataRoleBpm));
		//log_message('DEBUG','#TRAZA|MAIN|changeLevelRolUser()  $user: >> '. $user);

		$userLevel = $this->user_model->updateUserLevel($dataPost);

		if(!$userLevel){
			//$this->session->set_flashdata('flash_message', 'Fallo cambio de nivel'); 
			return false;
		}else{
			/*$this->session->set_flashdata('success_message', 'nivelCambiado con exito.'); 
			$rsp["message"] = true;
			return true;*/
			//guarda membership en BD (para menues y manejo local de usr
			$dataRole = $this->user_model->guardarMembership($dataRole);
			if(!$dataRole){
				//$this->session->set_flashdata('flash_message', 'Fallo asignacion de roles de '.$dataPost['email']); 
				return false;
			}else{
				/*$this->session->set_flashdata('success_message', 'Rol asignado con exito.'); 
				return true;*/
				//obtiene el nick de un usuario por email
				$this->load->model('Roles');
				$infoUser = $this->user_model->getUserInfoByEmail($dataPost['email']);				
				$membShipBpm = $this->Roles->guardarMembershipBPM($dataRoleBpm, $infoUser->usernick);
				//log_message('DEBUG','#TRAZA|MAIN|changeLevelRolUser()  $membShipBpm: >> '. json_encode($membShipBpm));
				//log_message('DEBUG','#TRAZA|MAIN|changeLevelRolUser()  $membShipBpm[payload]: >> '. json_encode($membShipBpm->payload));
				//log_message('DEBUG','#TRAZA|MAIN|changeLevelRolUser()  $membShipBpm: >> '. json_encode($data));
				//Verifico si guardo bien el usuario devuelve un user_id
				if(isset($membShipBpm->payload->user_id)){
					//$this->session->set_flashdata('success_message', 'Rol Bpm asignado con exito de '.$dataPost['email']);
					return true;
				}else{
					//Sino Guardó el usuario, elimine lo que guardo del mismo. 
					$deleteMemShip = $this->user_model->borrarMembership($dataRole);
					//$this->session->set_flashdata('flash_message', 'Fallo asignación de roles Bpm de '.$dataPost['email']);
					return false;
				}
			}

		}
		
	}
	//Recibe el objeto de json
	public function changeLevelRolUserObject(){
		# code...
		$data = $this->session->userdata;

		$dataPost['email'] = $this->input->post('email');
		$dataPost['level'] = $this->input->post('level');
		
		$dataRole = $this->input->post('dataRole');
		foreach($dataRole as $roleData){
			$roleData['usuario_app'] = userNick();
		}		
		$user = userNick();

		$dataRoleBpm = $this->input->post('dataRoleBpm');


		//message('DEBUG','#TRAZA|MAIN|changeLevelRolUser()  $data[email]: >> '.$dataPost['email'] ); 
		//log_message('DEBUG','#TRAZA|MAIN|changeLevelRolUser()  $data[level]: >> '.$dataPost['level'] ); 
		//log_message('DEBUG','#TRAZA|MAIN|changeLevelRolUser()  $data[dataRole]: >> '.json_encode( $dataRole) );
		//log_message('DEBUG','#TRAZA|MAIN|changeLevelRolUser()  $data[dataRoleBpm]: >> '. json_encode($dataRoleBpm));
		//log_message('DEBUG','#TRAZA|MAIN|changeLevelRolUser()  $user: >> '. $user);

		$userLevel = $this->user_model->updateUserLevel($dataPost);

		if(!$userLevel){
			$this->session->set_flashdata('flash_message', 'Fallo cambio de nivel'); 
			return false;
		}else{
			/*$this->session->set_flashdata('success_message', 'nivelCambiado con exito.'); 
			$rsp["message"] = true;
			return true;*/
			//guarda membership en BD (para menues y manejo local de usr)
			$cantRoles = count($dataRole);
			for($i=0; $i< $cantRoles; $i++){
				$dataRole = $this->user_model->guardarMembership($dataRole[$i]);

				if(!$dataRole){
					$this->session->set_flashdata('flash_message', 'Fallo asignacion de roles'); 
					return false;
				}else{
					/*$this->session->set_flashdata('success_message', 'Rol asignado con exito.'); 
					return true;*/
					//obtiene el nick de un usuario por email
					$this->load->model('Roles');
					$infoUser = $this->user_model->getUserInfoByEmail($dataPost['email']);

					$membShipBpm = $this->Roles->guardarMembershipBPM($dataRoleBpm, $infoUser->usernick);
					//log_message('DEBUG','#TRAZA|MAIN|changeLevelRolUser()  $membShipBpm: >> '. json_encode($membShipBpm));
					//log_message('DEBUG','#TRAZA|MAIN|changeLevelRolUser()  $membShipBpm: >> '. json_encode($data));
					if(!$membShipBpm){
						$this->session->set_flashdata('flash_message', 'Fallo asignación de roles Bpm.');
						return false;
					}else{
						$this->session->set_flashdata('success_message', 'Rol Bpm asignado con exito.');
						return true;
					}

				}
				
			}

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
		$membership['usuario_app'] = userNick();
		$user = userNick();

		//guarda membership en BD (para menues y manejo local de usr)
		$resp = $this->user_model->guardarMembership($membership);

		// guarda membership en BPM
		$membershipBPM = $this->input->post('membershipBPM');
		
		//obtiene el nick de un usuario por email
		$infoUser = $this->user_model->getUserInfoByEmail($membership['email']);
		$this->load->model('Roles');
		
		
		//log_message('DEBUG','#TRAZA|MAIN|guardarMembership()  membership: >> '. json_encode($membership) );
		//log_message('DEBUG','#TRAZA|MAIN|guardarMembership()  membershipBPM: >> '. json_encode($membershipBPM) );
		//log_message('DEBUG','#TRAZA|MAIN|guardarMembership()  membershipBPM: >> '.$infoUser );

		$resp = $this->Roles->guardarMembershipBPM($membershipBPM, $infoUser->usernick);

		return true;
	}

	/**
	* Borra membresia en DB
	* @param array con datos de usuario
	* @return strig true o false respuesta de borrado
	*/	
	function borrarMembership(){
		$membership = $this->input->post('membership');
		$resp = $this->user_model->borrarMembership($membership[0]);
		echo $resp;
	}

	//register new user from frontend
	public function register()
	{
			$data['title'] = "Registro Nuevo Usuario";
			$this->load->library('curl');
			$this->load->library('recaptcha');
			$this->form_validation->set_rules('firstname', 'First Name', 'required');
			$this->form_validation->set_rules('lastname', 'Last Name', 'required');
			$this->form_validation->set_rules('email', 'Email', 'required|valid_email');

			$result = $this->user_model->getAllSettings();
			$sTl = $result->site_title;
			$data['recaptcha'] = $result->recaptcha;
			// Si esprimera vez al entrar carga pantalla ara registrarse
			if ($this->form_validation->run() == FALSE) {
					// traigo los groups de BPM para lleba
					$data['empresas'] = $this->Roles->getBpmGroups();
					$this->load->view('header', $data);
					$this->load->view('container');
					$this->load->view('register');
					$this->load->view('footer');
			}else{
					if($this->user_model->isDuplicate($this->input->post('email'))){
							$this->session->set_flashdata('flash_message', 'El email que intenta registrar ya existe...');
							redirect(base_url().'main/register');
					}else{
							$post = $this->input->post(NULL, TRUE);
							$clean = $this->security->xss_clean($post);

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
											//insert to database
											$id = $this->user_model->insertUser($clean);
											$token = $this->user_model->insertToken($id);

											//generate token
											$qstring = $this->base64url_encode($token);
											$url = base_url() . 'main/complete/token/' . $qstring;
											$link = '<a href="' . $url . '">' . $url . '</a>';

											$this->load->library('email');
											$this->load->library('sendmail');
											
											$message = $this->sendmail->sendRegister($this->input->post('lastname'),$this->input->post('email'),$link, $sTl);
											$to_email = $this->input->post('email');
											$this->email->from($this->config->item('register'), 'Set Password ' . $this->input->post('firstname') .' '. $this->input->post('lastname')); //from sender, title email
											$this->email->to($to_email);
											$this->email->subject('Set Password Login');
											$this->email->message($message);
											$this->email->set_mailtype("html");

											//Sending mail
											if($this->email->send()){
													redirect(base_url().'main/successregister/');
											}else{
													$this->session->set_flashdata('flash_message', 'There was a problem sending an email.');
													exit;
											}
									}else{
											//recaptcha failed
											$this->session->set_flashdata('flash_message', 'Error...! Google Recaptcha UnSuccessful!');
											redirect(base_url().'main/register/');
											exit;
									}
							}else{

									$config = array(
											'protocol' => 'smtp',
											'smtp_host' => 'ssl://smtp.gmail.com',
											'smtp_auth' => true,
											'smtp_port' => '587',
											'smtp_user' => 'soportetrazalog24@gmail.com',
											'smtp_pass' => '123trazalog24',
											'mailtype' => 'html',
											'newline' => "\r\n",
											'crlf' => "\r\n",
											'charset' => 'utf-8',
									);
									//insert to database
									$id = $this->user_model->insertUser($clean);
									$token = $this->user_model->insertToken($id);

									//generate token
									$qstring = $this->base64url_encode($token);
									$url = base_url() . 'main/complete/token/' . $qstring;
									$link = '<a href="' . $url . '">' . $url . '</a>';

									$this->load->library('email',$config);
									$this->load->library('sendmail');

									$message = $this->sendmail->sendRegister($this->input->post('lastname'),$this->input->post('email'),$link,$sTl);
									$to_email = $this->input->post('email');
									$this->email->from($this->config->item('register'), 'Set Password ' . $this->input->post('firstname') .' '. $this->input->post('lastname')); //from sender, title email
									$this->email->to($to_email);
									$this->email->subject('Set Password Login');
									$this->email->message($message);
									$this->email->set_mailtype("html");

									
									//Sending mail
									if($this->email->send()){
											redirect(base_url().'main/successregister/');
									}else{
											show_error($this->email->print_debugger());
											$this->session->set_flashdata('flash_message', 'Hbo un problema al enviar el email.');
											exit;
									}
							}
					};
			}
	}

	//if success new user register
	public function successregister()
	{
			$data['title'] = "Success Register";
			$this->load->view('header', $data);
			$this->load->view('container');
			$this->load->view('register-info');
			$this->load->view('footer');
	}

	//if success after set password
	public function successresetpassword()
	{
			$data['title'] = "Success Reset Password";
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

			$data['title'] = "Establecer Password";

			$this->form_validation->set_rules('password', 'Password', 'required|min_length[5]');
			$this->form_validation->set_rules('passconf', 'Password Confirmation', 'required|matches[password]');

			if ($this->form_validation->run() == FALSE) {
					$this->load->view('header', $data);
					$this->load->view('container');
					$this->load->view('complete', $data);
					$this->load->view('footer');
			}else{
					$this->load->library('password');
					$post = $this->input->post(NULL, TRUE);

					$cleanPost = $this->security->xss_clean($post);

					$hashed = $this->password->create_hash($cleanPost['password']);
					$cleanPost['password'] = $hashed;
					unset($cleanPost['passconf']);
					$userInfo = $this->user_model->updateUserInfo($cleanPost);

					if(!$userInfo){
							$this->session->set_flashdata('flash_message', 'Hubo un problema actualizando su Usuario...');
							redirect(base_url().'main/login');
					}

					unset($userInfo->password);

					foreach($userInfo as $key=>$val){
							$this->session->set_userdata($key, $val);
					}
					redirect(base_url().'main/');

			}
	}

	//check login failed or success
	public function login()
	{
			$data = $this->session->userdata();
			log_message('DEBUG','#Main/login | '.json_encode($data));
			
			// si la sesion existe redirige a sistema
			if($data['email']){
				log_message('DEBUG','#Main/login Sesion Existente');
				redirect(DE);
			}else{
					$this->load->library('curl');
					$this->load->library('recaptcha');
					$this->form_validation->set_rules('email', 'Email', 'required|valid_email');
					$this->form_validation->set_rules('password', 'Password', 'required');

					$data['title'] = "Trazalog Tools!";

					// si esan vacios los campos, carga pantalla login
					if($this->form_validation->run() == FALSE) {

							//log_message('DEBUG','#Main/login | Carga Login |'. json_encode($this->form_validation->run()) . '| '.json_encode($this->input->post()));
							// traigo los groups de BPM para lleba
							$data['empresas'] = $this->Roles->getBpmGroups();
							
							$this->load->view('header', $data);
							$this->load->view('container');
							$this->load->view('login');
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
									$this->session->set_flashdata('flash_message', 'Email o contraseña erroneo.');
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
									var_dump($infoUser);
									$userbpm = $infoUser['data']['id'];
									$groupbpm = $empresa;

									if ($userbpm) {
										$userInfo->userIdBpm = $userbpm;
										$userInfo->groupBpm = $groupbpm;
									} else {
										//log_message('ERROR','#TRAZA|MAIN|LOGIN|NO HAY USUARIO EN BPM CON EL NICK >> '.$usernick);
										$this->session->set_flashdata('flash_message', 'Error en logueo de BPM...');
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
									$this->session->set_flashdata('flash_message', 'Error!');
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
			$data['title'] = "Forgot Password";
			$this->load->library('curl');
			$this->load->library('recaptcha');
			$this->form_validation->set_rules('email', 'Email', 'required|valid_email');
			
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
							$this->session->set_flashdata('flash_message', 'We cant find your email address');
							redirect(base_url().'main/login');
					}

					if($userInfo->status != $this->status[1]){ //if status is not approved
							$this->session->set_flashdata('flash_message', 'Your account is not in approved status');
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
									$this->email->from($this->config->item('forgot'), 'Reset Password! ' . $this->input->post('firstname') .' '. $this->input->post('lastname')); //from sender, title email
									$this->email->to($to_email);
									$this->email->subject('Reset Password');
									$this->email->message($message);
									$this->email->set_mailtype("html");

									if($this->email->send()){
											redirect(base_url().'main/successresetpassword/');
									}else{
											$this->session->set_flashdata('flash_message', 'There was a problem sending an email.');
											exit;
									}
							}else{
									//recaptcha failed
									$this->session->set_flashdata('flash_message', 'Error...! Google Recaptcha UnSuccessful!');
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
							$this->email->from($this->config->item('forgot'), 'Reset Password! ' . $this->input->post('firstname') .' '. $this->input->post('lastname')); //from sender, title email
							$this->email->to($to_email);
							$this->email->subject('Reset Password');
							$this->email->message($message);
							$this->email->set_mailtype("html");

							if($this->email->send()){
									redirect(base_url().'main/successresetpassword/');
							}else{
									$this->session->set_flashdata('flash_message', 'There was a problem sending an email.');
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
					$this->session->set_flashdata('flash_message', 'Token is invalid or expired');
					redirect(base_url().'main/login');
			}
			$data = array(
					'firstName'=> $user_info->first_name,
					'email'=>$user_info->email,
					//'user_id'=>$user_info->id,
					'token'=>$this->base64url_encode($token)
			);

			$data['title'] = "Reset Password";
			$this->form_validation->set_rules('password', 'Password', 'required|min_length[5]');
			$this->form_validation->set_rules('passconf', 'Password Confirmation', 'required|matches[password]');

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
							$this->session->set_flashdata('flash_message', 'There was a problem updating your password');
					}else{
							$this->session->set_flashdata('success_message', 'Your password has been updated. You may now login');
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


}