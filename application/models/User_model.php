<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends CI_Model {

    public $status; 
    public $roles;
    
    function __construct(){
        // Call the Model constructor
        parent::__construct();        
        $this->status = $this->config->item('status');
        $this->roles = $this->config->item('roles');
        $this->banned_users = $this->config->item('banned_users');
    }    
    
    //insert user into database
    public function insertUser($d)
    {  
				$string = array(
						'first_name'=>$d['firstname'],
						'last_name'=>$d['lastname'],
						'email'=>$d['email'],
						'role'=>$this->roles[0],
						'status'=>$this->status[0],
						'banned_users'=>$this->banned_users[0]
				);
				$q = $this->db->insert_string('seg.users',$string);
				$this->db->query($q);
				return $this->db->insert_id();
    }
    
    //check is duplicate
    public function isDuplicate($email)
    {
        $this->db->get_where('seg.users', array('email' => $email), 1);
        return $this->db->affected_rows() > 0 ? TRUE : FALSE;         
    }
    
    //insert the token
    public function insertToken($user_id)
    {   
        $token = substr(sha1(rand()), 0, 30); 
        $date = date('Y-m-d');
        
        $string = array(
                'token'=> $token,
                'user_id'=>$user_id,
                'created'=>$date
            );
        $query = $this->db->insert_string('seg.tokens',$string);
        $this->db->query($query);
        return $token . $user_id;
        
    }
    
    //check if token is valid
    public function isTokenValid($token)
    {
       $tkn = substr($token,0,30);
       $uid = substr($token,30);      
       
        $q = $this->db->get_where('seg.tokens', array(
            'tokens.token' => $tkn, 
            'tokens.user_id' => $uid), 1);                         
               
        if($this->db->affected_rows() > 0){
            $row = $q->row();             
            
            $created = $row->created;
            $createdTS = strtotime($created);
            $today = date('Y-m-d'); 
            $todayTS = strtotime($today);
            
            if($createdTS != $todayTS){
                return false;
            }
            
            $user_info = $this->getUserInfo($row->user_id);
            return $user_info;
            
        }else{
            return false;
        }
        
    } 
    
    //get user memberships_users info
    public function gestMembershipsUserInfo($email, $dataEmp){
        log_message('ERROR','#TRAZA|USER_MODEL|  gestMembershipsUserInfo($email)'. $email.' '.$dataEmp);

        $this->db->from('seg.memberships_users');
        $this->db->where('email', $email );
        //$this->db->where('group', $dataEmp );
        $query = $this->db->get();

        if($this->db->affected_rows() > 0){
            return $query->row();
        }else{
            error_log('no user found gestMembershipsUserInfo('.$email.')');
            return false;
        }
        /*
        $query = $this->db->get_where('seg.memberships_users', array('email' => $email));

        if($this->db->affected_rows() > 0){
            return $query->row();
        }else{
            error_log('no user found gestMembershipsUserInfo('.$email.')');
            return false;
        }*/
    }

    //get user memberships_users info
    public function getMembershipsUserInfoEmpresa($email, $emplevel){

        $this->db->from('seg.memberships_users');
        $this->db->where('email', $email );
        $this->db->where('group', $emplevel );
        $query = $this->db->get();

        if($this->db->affected_rows() > 0){
            $row = $query->row();
            return $row;
        }else{
            error_log('no user found gestMembershipsUserInfo('.$email.')');
            return false;
        }
    }
    
    //get user info
    public function getUserInfo($id)
    {
        $q = $this->db->get_where('seg.users', array('id' => $id), 1);
        if($this->db->affected_rows() > 0){
            $row = $q->row();
            return $row;
        }else{
            error_log('no user found getUserInfo('.$id.')');
            return false;
        }
    }
    
    //getUserName
    public function getUserAllData($email)
    {
        $this->db->select('*');
        $this->db->from('seg.users');
        $this->db->where('email', $email );
        $query = $this->db->get();

        if ( $query->num_rows() > 0 )
        {
            $row = $query->row_array();
            return $row;
        }
        else
        {
            error_log('no user found getUserAllData('.$email.')');
            return false;
        }
    }
    
    //update data user
    public function updateUserInfo($post)
    {
        $data = array(
               'password' => $post['password'],
               'last_login' => date('Y-m-d h:i:s A'),
               'status' => $this->status[1]
            );
        $this->db->where('id', $post['user_id']);
        $this->db->update('seg.users', $data);
        $success = $this->db->affected_rows(); 
        
        if(!$success){
            error_log('Unable to updateUserInfo('.$post['user_id'].')');
            return false;
        }
        
        $user_info = $this->getUserInfo($post['user_id']);
        return $user_info; 
    }
    
    //check login
    public function checkLogin($post)
    {
        $this->load->library('password');
        $this->db->select('*');
        $this->db->where('email', $post['email']);
        $query = $this->db->get('seg.users');
        $userInfo = $query->row();
				$countUs = $query->num_rows();

					if( ($countUs == 1) ){
            if(!$this->password->validate_password($post['password'], $userInfo->password))
            {
                log_message('ERROR','#TRAZA|USER_MODEL|ERROR EN PAASSWORD >> PASSWORD-> '. $post['password']);
                return false;
            }else{
                $this->updateLoginTime($userInfo->id);
            }
        }else{
            log_message('ERROR','#TRAZA|USER_MODEL| >> NO HAY UN USUARIO CON EL EMAIL INGRESADO: '.$post['email']);
            error_log('NO HAY UN USUARIO CON EL EMAIL INGRESADO : ('.$post['email'].')');
            return false;
        }

				unset($userInfo->password);

        return $userInfo;
		}

		/**
		* chequea si corresponde el usuario con la empresa elegida en el login
		* @param int $empresa , varchar $email
		* @return bool true o false
		*/
		function chekEmpresa($empresa, $email){

			$this->db->select("*");
			$this->db->where("email", $email);
			$this->db->where("group", $empresa);
			$query = $this->db->get("seg.memberships_users");
			$userInfo = $query->row();
			$countUs = $query->num_rows();

			if( $countUs > 0 ){
				return true;
			}else{
				log_message('ERROR','#TRAZA|DNATO|USER_MODEL| $empresa  >> '.json_encode($empresa));
				log_message('ERROR','#TRAZA|DNATO|USER_MODEL| $email  >> '.json_encode($email));
        return false;
			}
		}

    //update time login
    public function updateLoginTime($id)
    {
        $this->db->where('id', $id);
        $this->db->update('users', array('last_login' => date('Y-m-d h:i:s A')));
        return;
    }

    /**
		* devuelve array con info de Usuario de sistema
		* @param string email
		* @return arrray con info de usuario de sistema
		*/
    public function getUserInfoByEmail($email)
    {
        $q = $this->db->get_where('seg.users', array('email' => $email), 1);
        if($this->db->affected_rows() > 0){
            $row = $q->row();
            return $row;
        }else{
            error_log('no user found getUserInfo('.$email.')');
            return false;
        }
    }
    
    //update password
    public function updatePassword($post)
    {   
        $this->db->where('id', $post['user_id']);
        $this->db->update('users', array('password' => $post['password']));
        $success = $this->db->affected_rows(); 
        
        if(!$success){
            error_log('Unable to updatePassword('.$post['user_id'].')');
            return false;
        }        
        return true;
    } 
    
    //add user login
    public function addUser($d)
    {
				if ($d['depo_id']) {
					$depo = $d['depo_id'];
				} else {
					$depo = NULL;
				}

				$string = array(
						'first_name'=>$d['firstname'],
						'last_name'=>$d['lastname'],
						'email'=>$d['email'],							
						'telefono'=>$d['telefono'],				
						'dni'=>$d['dni'],									
						'usernick'=>$d['usernick'],
						'password'=>$d['password'], 			
						'role'=>$d['role'], 							
						'status'=>'approved',
						'banned_users'=>'unban',
						'depo_id'=> $depo
				);

				$q = $this->db->insert('seg.users',$string);

				$error = $this->db->error();

				$this->db->select_max('id');						
				$query = $this->db->get('seg.users');
				$userInfo = $query->row('id');

				if($userInfo){
					return $userInfo;
				}else{
					log_message('ERROR','#TRAZA|USER_MODEL|addUser($d) >> ERROR -> '.json_encode($error['message']));
					return false;
				}
           
		}

		/**
		* Crear usuarios en BPM
		* @param array info de usr nuevos
		* @return string status de servicio
		*/
		function crearUsrBPM($cleanPost){
			
			//TODO: SACAR HARDCODEO ACA
			$session = '"X-Bonita-API-Token=658fcd51-ef8b-48c3-9606-1d89a88cf3e5;JSESSIONID=BCDEA4A05749709F4DFBDCBB58A527E8;bonita.tenant=1;"';
			$datos["userName"] = $cleanPost['usernick'];
			$datos["password"] = BPM_USER_PASS;
			$datos["password_confirm"] = BPM_USER_PASS;
			$datos["icon"] = "";
			$datos["firstname"] = $cleanPost['firstname'];
			$datos["lastname"] = $cleanPost['lastname'];
			$datos["title"] = "Sr";
			$datos["job_title"] = "Human resources benefits";
			$datos["manager_id"] = "";			
			$post["session"] = $session;
			$post["payload"] = $datos;
											
			$aux = $this->rest->callAPI("POST",REST_BPM."/users", $post);
			$aux =json_decode($aux["status"]);
			return $aux;
		}

		/**
		* Asocia usuario a roles y grupos de BPM en BD
		* @param $usr_id, $group, $rol
		* @return int id de asociacion insertado
		*/
		public function guardarMembership($membership){

			$query = $this->db->insert('seg.memberships_users',$membership);
			$error = $this->db->error();

			if($error['message'] == ""){
				return true;
			}else{
				log_message('ERROR','#TRAZA|USER_MODEL|GUARDARUSRBPM($membership) >> ERROR -> '.json_encode($error['message']));
				return false;
			}
		}

		/**
		* Borra membership en BD
		* @param array con datos de usuario
		* @return string resultado del borrado		
		*/
		function borrarMembership($membership){
			
			$this->db->where('role', $membership['role']);
			$this->db->where('group', $membership['group']);
			$this->db->where('email', $membership['email']);
			$this->db->delete('seg.memberships_users', $membership);
			$error = $this->db->error();

			if($error['message'] == ""){
				return true;
			}else{
				log_message('ERROR','#TRAZA|USER_MODEL|BORRARMEMBERSHIP($membership) >> ERROR -> '.json_encode($error['message']));
				return false;
			}

		}

    public function addUserExterno($d)
    {
        $string = array(
                'nombre_razon'=>$d['nombre_razon'],
                'email'=>$d['email'],
                'telefono'=>$d['telefono'],
                'cuit_empresa'=>$d['cuit_empresa'],
                'usernick'=>$d['usernick'],
                'password'=>$d['password'], 
                'role'=> USUARIO_EXTERNO, 
                'status'=>$this->status[1],
                'banned_users' => 'unban'
            );
            $q = $this->db->insert_string('seg.users',$string);
            $this->db->query($q);
            return $this->db->insert_id();
    }

    //update profile user
    public function updateProfile($post)
    {   
        $this->db->where('id', $post['user_id']);
        $this->db->update('seg.users', array('password' => $post['password'], 'email' => $post['email'], 'first_name' => $post['firstname'], 'last_name' => $post['lastname']));
        $success = $this->db->affected_rows(); 
        
        if(!$success){
            error_log('Unable to updatePassword('.$post['user_id'].')');
            return false;
        }        
        return true;
    }

    
    //update user level
    public function updateUserLevel($post)
    {
        log_message('DEBUG','#TRAZA|USER_MODEL|updateUserLevel() DATOS DE USUARIO A MODIFICAR->$dataLevel: >> '.json_encode($post));
        $this->db->where('email', $post['email']);
        $this->db->update('seg.users', array('role' => $post['level']));
        $success = $this->db->affected_rows();
        log_message('DEBUG','#TRAZA|USER_MODEL|updateUserLevel() RESPUESTA BD->$success: >> '.json_encode($success));
        if(!$success){
            return false;
        }        
        return true;
    }

    //update user ban
    public function updateUserban($post)
    {   
        $this->db->where('email', $post['email']);
        $this->db->update('seg.users', array('banned_users' => $post['banuser']));
        $success = $this->db->affected_rows(); 
        
        if(!$success){
            return false;
        }        
        return true;
    }

    /**
	* Devuelve los usuarios activos
	* @param
	* @return array usuarios activos
	*/
    public function getUserData()
    {
        $query = $this->db->get_where('seg.users', array('banned_users' => 'unban'));
        return $query->result();
    }
    
    /**
	* Devuelve los usuarios activos
	* @param
	* @return array usuarios activos
	*/
    public function getListUserData()
    {
        $this->db->select("seg.users.*,seg.roles.*");
        $this->db->from('seg.users');
        $this->db->join('seg.roles', 'seg.roles.rol_id = CAST(seg.users.role AS int)');
        //$this->db->join('seg.memberships_users', 'seg.memberships_users.email = seg.users.email', 'LEFT');
        $query = $this->db->get();

        if($query->result())
            return $query->result();
        else
            return false;
    }
    
    public function getInfoEmpCore()
    {
        $this->db->select("*");
        $this->db->from('core.empresas');
        $query = $this->db->get();

        if($query->result())
            return $query->result();
        else
            return false;

    }


	/**
	* Devuelve listado de TODOS los usuarios del sistema
	* @param
	* @return array con usuarios del sistema
	*/
	function getUserDataAll()
	{     
		$query = $this->db->get('seg.users');
        return $query->result();
			
	}

    /**
     * Elimina los roles de un usuario
     * @param 
     * @return array 
     */
    public function deleteUserRol(){
        
    }

    
    //delete user
    public function deleteUser($id)
    {   
        $this->db->where('id', $id);
        $this->db->delete('seg.users');
        
        if ($this->db->affected_rows() == '1') {
            return FALSE;
        }
        else {
            return TRUE;
        }
    }
    
    //get settings
    public function getAllSettings()
    {        
			$this->db->select('*');        
			$query = $this->db->get('seg.settings');
			$info = $query->result_object();
			return $info[0];
    }
    
    //do change settings
    public function settings($post)
    {   
        $this->db->where('id', $post['id']);
        $this->db->update('seg.settings', 
            array(
                'site_title' => $post['site_title'], 
                'timezone' => $post['timezone'],
                'recaptcha' => $post['recaptcha'],
                'theme' => $post['theme']
            )
        ); 
        $success = $this->db->affected_rows(); 
        if(!$success){
            return false;
        }        
        return true;
    }
}
