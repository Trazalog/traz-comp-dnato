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
        
        log_message('DEBUG','#Main/index | '.json_encode($data));

	    if(empty($data['email'])){
            log_message('DEBUG','#Main/index | No email');
	        redirect(base_url().'main/login/');
	    }

	    //check user level
	    if(empty($data['role'])){
            log_message('DEBUG','#Main/index | No role');
	        redirect(base_url().'main/login/');
	    }
	    $dataLevel = $this->userlevel->checkLevel($data['role']);
	    //check user level
        
	    $data['title'] = "Dashboard Admin";
	    
      
        if($data['direccion']){
            log_message('DEBUG','#Main/index | Redireccion: '.$data['direccion']);
            redirect($data['direccion']);
        }else{
            log_message('DEBUG','#Main/index | Error de Redireccionamiento');
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
	public function users()
	{
	    $data = $this->session->userdata;
	    $data['title'] = "User List";
	    $data['groups'] = $this->user_model->getUserData();

	    //check user level
	    if(empty($data['role'])){
	        redirect(base_url().'main/login/');
	    }
	    $dataLevel = $this->userlevel->checkLevel($data['role']);
	    //check user level

	    //check is admin or not
	    if($dataLevel == "is_admin"){
            $this->load->view('header', $data);
            $this->load->view('navbar', $data);
            $this->load->view('container');
            $this->load->view('user', $data);
            $this->load->view('list_usuarios_externos', $data);
            $this->load->view('footer');
	    }else{
	        redirect(base_url().'main/');
	    }
	}

    	//change level user
	public function changelevel()
	{
        $this->load->model('Roles');

        $data = $this->session->userdata;
        //check user level
	    if(empty($data['role'])){
	        redirect(base_url().'main/login/');
	    }
	    $dataLevel = $this->userlevel->checkLevel($data['role']);
	    //check user level

	    $data['title'] = "Change Level Admin";
        $data['groups'] = $this->user_model->getUserData();
        $data['dd_list'] = $this->Roles->obtener();
        

	    //check is admin or not
	    if($dataLevel == "is_admin"){

            $this->form_validation->set_rules('email', 'Your Email', 'required');
            $this->form_validation->set_rules('level', 'User Level', 'required');

            if ($this->form_validation->run() == FALSE) {
                $this->load->view('header', $data);
                $this->load->view('navbar', $data);
                $this->load->view('container');
                $this->load->view('changelevel', $data);
                $this->load->view('footer');
            }else{
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
	public function banuser() 
	{
        $data = $this->session->userdata;
        //check user level
	    if(empty($data['role'])){
	        redirect(base_url().'main/login/');
	    }
	    $dataLevel = $this->userlevel->checkLevel($data['role']);
	    //check user level

	    $data['title'] = "Ban User";
	    $data['groups'] = $this->user_model->getUserData();

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
                    $this->session->set_flashdata('flash_message', 'There was a problem updating');
                }else{
                    $this->session->set_flashdata('success_message', 'The status user has been updated.');
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
	    //check user level

	    //check is admin or not
	    if($dataLevel == "is_admin"){
    		$this->user_model->deleteUser($id);
    		if($this->user_model->deleteUser($id) == FALSE )
    		{
    		    $this->session->set_flashdata('flash_message', 'Error, cant delete the user!');
    		}
    		else
    		{
    		    $this->session->set_flashdata('success_message', 'Delete user was successful.');
    		}
    		redirect(base_url().'main/users/');
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
	    //check user level

	    //check is admin or not
	    if($dataLevel == "is_admin"){
            $this->form_validation->set_rules('firstname', 'First Name', 'required');
            $this->form_validation->set_rules('lastname', 'Last Name', 'required');
            $this->form_validation->set_rules('email', 'Email', 'required|valid_email');
            $this->form_validation->set_rules('role', 'role', 'required');
            $this->form_validation->set_rules('password', 'Password', 'required|min_length[5]');
            $this->form_validation->set_rules('passconf', 'Password Confirmation', 'required|matches[password]');

            $data['title'] = "Add User";
            if ($this->form_validation->run() == FALSE) {
                $this->load->view('header', $data);
                $this->load->view('navbar');
                $this->load->view('container');
                $this->load->view('adduser', $data);
                $this->load->view('footer');
            }else{
                if($this->user_model->isDuplicate($this->input->post('email'))){
                    $this->session->set_flashdata('flash_message', 'User email already exists');
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
                    $cleanPost['dni'] = $this->input->post('dni');
                    $cleanPost['banned_users'] = 'unban';
                    $cleanPost['password'] = $hashed;
                    unset($cleanPost['passconf']);

                    //insert to database
                    if(!$this->user_model->addUser($cleanPost)){
                        $this->session->set_flashdata('flash_message', 'There was a problem add new user');
                    }else{
                        $this->session->set_flashdata('success_message', 'New user has been added.');
                    }
                    redirect(base_url().'main/users/');
                };
            }
	    }else{
	        redirect(base_url().'main/');
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

    //register new user from frontend
    public function register()
    {
        $data['title'] = "Register to Admin";
        $this->load->library('curl');
        $this->load->library('recaptcha');
        $this->form_validation->set_rules('firstname', 'First Name', 'required');
        $this->form_validation->set_rules('lastname', 'Last Name', 'required');
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email');
        
        $result = $this->user_model->getAllSettings();
        $sTl = $result->site_title;
        $data['recaptcha'] = $result->recaptcha;

        if ($this->form_validation->run() == FALSE) {
            $this->load->view('header', $data);
            $this->load->view('container');
            $this->load->view('register');
            $this->load->view('footer');
        }else{
            if($this->user_model->isDuplicate($this->input->post('email'))){
                $this->session->set_flashdata('flash_message', 'User email already exists');
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
                        $this->session->set_flashdata('flash_message', 'There was a problem sending an email.');
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
            $this->session->set_flashdata('flash_message', 'Token is invalid or expired');
            redirect(base_url().'main/login');
        }
        $data = array(
            'firstName'=> $user_info->first_name,
            'email'=>$user_info->email,
            'user_id'=>$user_info->id,
            'token'=>$this->base64url_encode($token)
        );

        $data['title'] = "Set the Password";

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
                $this->session->set_flashdata('flash_message', 'There was a problem updating your record');
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
        if($data['email']){
            log_message('DEBUG','#Main/login Sesion Existente');
	        redirect(DE);
	    }else{
	        $this->load->library('curl');
            $this->load->library('recaptcha');
            $this->form_validation->set_rules('email', 'Email', 'required|valid_email');
            $this->form_validation->set_rules('password', 'Password', 'required');
            
            $data['title'] = "Trazalog Tools!";

            if($this->form_validation->run() == FALSE) {
                log_message('DEBUG','#Main/login | Carga Login |'. json_encode($this->form_validation->run()) . '| '.json_encode($this->input->post()));
                $this->load->view('header', $data);
                $this->load->view('container');
                $this->load->view('login');
                $this->load->view('footer');
            }else{
                $post = $this->input->post();
                $clean = $this->security->xss_clean($post);
                $userInfo = $this->user_model->checkLogin($clean);

                log_message('DEBUG','#Main/login | userInfo: '.json_encode($userInfo));
        
                if(!$userInfo)
                {
                    log_message('ERROR','#Main/login | Wrong password or email.');
                    $this->session->set_flashdata('flash_message', 'Wrong password or email.');
                    redirect(base_url().'main/login');
                }
                elseif($userInfo->banned_users == "ban")
                {
                    log_message('ERROR','#Main/login | You’re temporarily banned from our website!');
                    $this->session->set_flashdata('danger_message', 'You’re temporarily banned from our website!');
                    redirect(base_url().'main/login');
                }
                elseif($userInfo && $userInfo->banned_users == "unban") //recaptcha check, success login, ban or unban
                {
                    foreach($userInfo as $key=>$val){
                       $this->session->set_userdata($key, $val);
                    }
                    log_message('DEBUG','#Main/checkLoginUser/');
                    redirect(DE);
                }
                else
                {
                    log_message('ERROR','Something Error!');
                    $this->session->set_flashdata('flash_message', 'Something Error!');
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