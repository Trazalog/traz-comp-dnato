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
        log_message('INFO', '#TRAZA|USER_MODEL|insertUser() >> Insertando nuevo usuario');
        
        $string = array(
            'first_name' => $d['firstname'],
            'last_name' => $d['lastname'],
            'email' => $d['email'],
            'role' => $this->roles[0],
            'status' => $this->status[0],
            'banned_users' => $this->banned_users[0]
        );
        
        // Agregar campos adicionales si existen
        if (isset($d['reg_pais_id'])) {
            $string['reg_pais_id'] = $d['reg_pais_id'];
        }
        if (isset($d['reg_razon_social'])) {
            $string['reg_razon_social'] = $d['reg_razon_social'];
        }
        if (isset($d['telefono'])) {
            $string['telefono'] = $d['telefono'];
        }
        
        log_message('DEBUG', '#TRAZA|USER_MODEL|insertUser() >> Datos a insertar: ' . json_encode($string));
        
        $q = $this->db->insert_string('seg.users', $string);
        $this->db->query($q);
        
        $insert_id = $this->db->insert_id();
        log_message('INFO', '#TRAZA|USER_MODEL|insertUser() >> Usuario insertado con ID: ' . $insert_id);
        
        return $insert_id;
    }
    
    //check is duplicate
    public function isDuplicate($email)
    {
        $this->db->get_where('seg.users', array('email' => $email), 1);
        return $this->db->affected_rows() > 0 ? TRUE : FALSE;         
    }
    
    //check if razon social exists for the same country
    public function existeRazonSocial($razon_social, $pais_id, $cuit = null)
    {
        $razon = trim((string) $razon_social);
        $pais = trim((string) $pais_id);
        $cuitNorm = trim((string) $cuit);

        if ($razon === '' || $pais === '') {
            log_message('WARNING', '#TRAZA|USER_MODEL|existeRazonSocial() >> Datos incompletos | razon=' . $razon . ' | pais_id=' . $pais);
            return false;
        }

        $this->db->select('empr_id');
        $this->db->from('core.empresas');
        $this->db->where('eliminado', false);
        $this->db->where('pais_id', $pais);
        $this->db->group_start();
        $this->db->where('UPPER(TRIM(nombre))', strtoupper($razon));
        $this->db->or_where('UPPER(TRIM(descripcion))', strtoupper($razon));
        $this->db->group_end();
        if ($cuitNorm !== '') {
            $this->db->where('cuit', $cuitNorm);
        }
        $this->db->limit(1);

        $query = $this->db->get();
        $existe = $query->num_rows() > 0;

        log_message('DEBUG', '#TRAZA|USER_MODEL|existeRazonSocial() >> razon=' . $razon . ' | pais_id=' . $pais . ' | cuit=' . ($cuitNorm !== '' ? $cuitNorm : 'N/A') . ' | existe=' . ($existe ? 'SI' : 'NO'));

        return $existe;
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
    public function gestMembershipsUserInfo($email, $sw=0){
        //log_message('ERROR','#TRAZA|USER_MODEL|  gestMembershipsUserInfo($email): '. $email);

        $this->db->from('seg.memberships_users');
        $emailNorm = strtolower(trim((string) $email));
        $this->db->where(
            'LOWER(TRIM(seg.memberships_users.email)) = ' . $this->db->escape($emailNorm),
            null,
            false
        );

        if($sw == 1){
            $this->db->select('seg.memberships_users.group');
            $this->db->group_by('seg.memberships_users.group');
        }else{
            $this->db->select('*');
        }

        $query = $this->db->get();

        if($query && $query->num_rows() > 0){
            return $query->result();
        }
        error_log('no user found gestMembershipsUserInfo('.$email.')');
        return false;
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
    public function getUserAllData($email,$case=0)
    {
        if($case == 1){
            $this->db->select('id');
        }else
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
        $this->db->where('id', $post['user_id']);
        $current = $this->db->get('seg.users', 1)->row();

        $data = array(
               'password' => $post['password'],
               'last_login' => date('Y-m-d h:i:s A'),
               'status' => $this->status[1]
            );
        // BPM / WSO2 bpm-asset: el DataService usuario/usernick debe devolver nick; si quedó vacío al alta, usar email.
        if ($current && ( ! isset($current->usernick) || trim((string) $current->usernick) === '')) {
            $data['usernick'] = strtolower(trim($current->email));
        }

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
                //log_message('ERROR','#TRAZA|USER_MODEL|ERROR EN PAASSWORD >> PASSWORD-> '. $post['password']);
                return false;
            }else{
                $this->updateLoginTime($userInfo->id);
            }
        }else{
            //log_message('ERROR','#TRAZA|USER_MODEL| >> NO HAY UN USUARIO CON EL EMAIL INGRESADO: '.$post['email']);
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
				//log_message('ERROR','#TRAZA|DNATO|USER_MODEL| $empresa  >> '.json_encode($empresa));
				//log_message('ERROR','#TRAZA|DNATO|USER_MODEL| $email  >> '.json_encode($email));
        return false;
			}
		}

    //update time login
    public function updateLoginTime($id)
    {
        $this->db->where('id', $id);
        $this->db->update('seg.users', array('last_login' => date('Y-m-d h:i:s A')));
        return;
    }

    /**
		* devuelve array con info de Usuario de sistema
		* @param string email
		* @return arrray con info de usuario de sistema
		*/
    public function getUserInfoByEmail($email){
        log_message("DEBUG","#TRAZA | #TRAZ-COMP-DNATO | USER_MODEL | getUserInfoByEmail($email)");
        $q = $this->db->get_where('seg.users', array('email' => $email), 1);
        if($this->db->affected_rows() > 0){
            $row = $q->row();
            log_message("DEBUG","#TRAZA | #TRAZ-COMP-DNATO | USER_MODEL | getUserInfoByEmail($email) >> User DATA found: >>".json_encode($row) );
            return $row;
        }else{
            log_message("ERROR","#TRAZA | #TRAZ-COMP-DNATO | USER_MODEL | getUserInfoByEmail($email) >> no user found getUserInfo" );
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
    
    /**
     * @deprecated Usar crearUsuarioAPI() en su lugar. Mantenido para rollback (Fase 4).
     * add user login
     */
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
						'depo_id'=> $depo,
				);

                //$array[$key]->valor4_base64 = base64_encode(file_get_contents($_FILES[$nom]['tmp_name']));
                //imagen codificada
                $string['image_name'] = $d['image_name'];
                $string['image'] = $d['image'];
                //log_message('DEBUG','#TRAZA|USER_MODEL|addUser($d) >> $string -> '.json_encode($string));

				$q = $this->db->insert('seg.users',$string);

                //log_message('DEBUG','#TRAZA|USER_MODEL|addUser($d) >> 	$q -> '.json_encode($q));

                $error = $this->db->error();

                if($q){
                    $bsnes = array (
                        'email'=>$d['email'],	
                        'busines' =>  $d['business']              
                    );

                    $bs = $this->db->insert('seg.users_business', $bsnes);

                    $error = $this->db->error();

                    $this->db->select_max('id');						
                    $query = $this->db->get('seg.users');
                    $userInfo = $query->row('id');

                    if($userInfo){
                        return $userInfo;
                    }else{
                        //log_message('ERROR','#TRAZA|USER_MODEL|addUser($d) >> ERROR -> '.json_encode($error['message']));
                        return false;
                    }

                }else{
                    return false;
                }

           
		}

		/**
		* @deprecated Usar crearUsuarioAPI() en su lugar. Mantenido para rollback (Fase 4).
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
		* Asocia usuario a roles y grupos de BPM en la tabla seg.memberships_users
		* @param $usr_id, $group, $rol
		* @return int id de asociacion insertado
		*/
		public function guardarMembership($membership){
            log_message("DEBUG","#TRAZA | #TRAZ-COMP-DNATO | USER_MODEL | guardarMembership($membership) >> membership: ".json_encode($membership));
			$query = $this->db->insert('seg.memberships_users',$membership);
			$error = $this->db->error();

			if($error['message'] == ""){
				return true;
			}else{
				log_message('ERROR','#TRAZA | #TRAZ-COMP-DNATO | USER_MODEL | GUARDARUSRBPM($membership) >> '.json_encode($error['message']));
				return false;
			}
		}

		/**
		* Borra membership en BD
		* @param array con datos de usuario
		* @return string resultado del borrado		
		*/
		function borrarMembership($membership){
            log_message('DEBUG',"#TRAZA | #TRAZ-COMP-DNATO | USER_MODEL | borrarMembership($membership)  membership: >> ".json_encode($membership) );
			
			$this->db->where('role', trim($membership['role']));
			$this->db->where('group', $membership['group']);
			$this->db->where('email', $membership['email']);
			$this->db->delete('seg.memberships_users');
			$error = $this->db->error();

			if($this->db->affected_rows() > 0 ){
				return TRUE;
			}else{
				log_message('ERROR',"#TRAZA | #TRAZ-COMP-DNATO | USER_MODEL | borrarMembership($membership) >> ERROR -> ".json_encode($error['message']));
				return FALSE;
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
    public function updateProfile_old($post)
    {   
        $this->db->where('id', $post['user_id']);
        $this->db->update('seg.users', array(
                                            'password' => $post['password'], 
                                            'email' => $post['email'], 
                                            'first_name' => $post['firstname'], 
                                            'last_name' => $post['lastname'],
                                            'image_name' => $post['image_name'],
                                            'image' => $post['image']));
        $success = $this->db->affected_rows(); 
        
        if(!$success){
            error_log('Unable to updatePassword('.$post['user_id'].')');
            return false;
        }        
        return true;
    }
    
    public function updateProfile($post)
    {   
        if($post['image_name'] && $post['image']){
            $data = array(
                'email' => $post['email'], 
                'first_name' => $post['firstname'], 
                'last_name' => $post['lastname'],
                'image_name' => $post['image_name'],
                'image' => $post['image']);
        }else{
            $data = array(                
                'email' => $post['email'], 
                'first_name' => $post['firstname'], 
                'last_name' => $post['lastname']);
        }
        

        $this->db->where('id', $post['user_id']);
        $this->db->update('seg.users', $data);
        $success = $this->db->affected_rows(); 
        
        if(!$success){
            error_log('Unable to updatePassword('.$post['user_id'].')');
            return false;
        }        
        return true;
    }
    
    public function updatePass($post)
    {   

        $data = array(
            'password' => $post['password'], 
            'first_name' => $post['firstname'], 
            'last_name' => $post['lastname']
        );
        

        $this->db->where('id', $post['user_id']);
        $this->db->update('seg.users', $data);
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
        //log_message('DEBUG','#TRAZA|USER_MODEL|updateUserLevel() DATOS DE USUARIO A MODIFICAR->$dataLevel: >> '.json_encode($post));
        $this->db->where('email', $post['email']);
        $this->db->update('seg.users', array('role' => $post['level']));
        $success = $this->db->affected_rows();
        //log_message('DEBUG','#TRAZA|USER_MODEL|updateUserLevel() RESPUESTA BD->$success: >> '.json_encode($success));
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

        if($query->result())
            return $query->result();
        else
            return false;
    }
    
    /**
	* Devuelve los usuarios activos
	* @param
	* @return array usuarios activos
	*/
    public function getListUserData()
    {
        /* DISTINCT ON: un mismo usuario puede tener varias filas en users_business; sin esto el JOIN duplica filas. */
        $this->db->select("DISTINCT ON (seg.users.id) seg.users.id,
        seg.users.email,
        seg.users.first_name,
        seg.users.last_name,
        seg.users.role,
        seg.users.password,
        seg.users.last_login,
        seg.users.status,
        seg.users.banned_users,
        seg.users.passmd5,
        seg.users.telefono,
        seg.users.dni,
        seg.users.usernick,
        seg.users.depo_id,
        cast(seg.users.image as bytea),
        seg.users.image_name,seg.roles.*,seg.users_business.busines");
        $this->db->from('seg.users');
        $this->db->join('seg.roles', 'seg.roles.rol_id = CAST(seg.users.role AS int)', 'left');
        $this->db->join('seg.users_business', 'seg.users_business.email = seg.users.email', 'LEFT');
        $this->db->order_by('seg.users.id', 'asc');
        $this->db->order_by('seg.users_business.busines', 'asc');
        
        $query = $this->db->get();
        

        //log_message('DEBUG','#TRAZA|USER_MODEL|getListUserData() $query->result(): >> '.json_encode($query->result()));

        if($query->result())
            return $query->result();
        else
            return false;
    }

    /**
     * Usuarios visibles para un administrador según sus empresas (seg.memberships_users.group).
     * No depende de seg.users_business.busines: los usuarios dados de alta por API pueden tener
     * membresía correcta pero fila ausente o distinta en users_business.
     *
     * @param string[] $groups valores de columna "group" en seg.memberships_users (ej. nombre empresa BPM)
     * @return array<int, object>|array{} filas como getListUserData()
     */
    public function getListUserDataForGroups(array $groups)
    {
        $groups = array_values(array_unique(array_filter(array_map('strval', $groups), function ($g) {
            return $g !== '';
        })));
        if ($groups === array()) {
            return array();
        }

        $inList = implode(',', array_map(array($this->db, 'escape'), $groups));

        $this->db->select("DISTINCT ON (seg.users.id) seg.users.id,
        seg.users.email,
        seg.users.first_name,
        seg.users.last_name,
        seg.users.role,
        seg.users.password,
        seg.users.last_login,
        seg.users.status,
        seg.users.banned_users,
        seg.users.passmd5,
        seg.users.telefono,
        seg.users.dni,
        seg.users.usernick,
        seg.users.depo_id,
        cast(seg.users.image as bytea),
        seg.users.image_name,seg.roles.*,seg.users_business.busines");
        $this->db->from('seg.users');
        $this->db->join('seg.memberships_users mu', 'mu.email = seg.users.email', 'inner');
        $this->db->where('mu."group" IN (' . $inList . ')', null, false);
        $this->db->join('seg.roles', 'seg.roles.rol_id = CAST(seg.users.role AS int)', 'left');
        $this->db->join('seg.users_business', 'seg.users_business.email = seg.users.email', 'left');
        $this->db->order_by('seg.users.id', 'asc');
        $this->db->order_by('seg.users_business.busines', 'asc');

        $query = $this->db->get();
        $res = $query ? $query->result() : array();
        return is_array($res) ? $res : array();
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

    
    //delete user
    public function deleteUser($id)
    {   
        $this->db->where('id', $id);
        $this->db->delete('seg.users');
        
        if ($this->db->affected_rows() > 0) {
            return TRUE;
        }
        else {
            return FALSE;
        }
    }

    //Delete User Busines
    public function deleteUserBusines($email,$busines){ 

        $this->db->where('email', $email);
        $this->db->where('busines', $busines);
        $this->db->delete('seg.users_business');
        
        if ($this->db->affected_rows() > 0) {
            return TRUE;
        }
        else {
            return FALSE;
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

    public function obtenerBusines($id = false)
    {
        
        $query = $this->db->get('core.empresas');
        //$res = $query->result();
        
        $list = [];
        return $query->result();
        /*foreach ($res as $o) {
            $list[$o->empr_id] = $o->descripcion;
        }

        return $list;*/
    }
    /**
     * @deprecated Usar crearUsuarioAPI() en su lugar. Mantenido para rollback (Fase 4).
     * Agrega un usuario a MariaDB de Asset
     * @param array datos ingresados en formulario
     * @return 
     */
    public function addUserAsset($data){
        $post['_post_assetuser_add']= array(
            'nick' => $data['usernick'],
            'name' => $data['firstname'],
            'lastName' => $data['lastname'],
            'pass' => $data['password'],
            'image' => $data['image']
        );

        $url = REST_CORE."/assetuser/add";
        $aux = $this->rest->callAPI("POST",$url,$post);

        log_message('DEBUG', "#TRAZ-COMP-DNATO | User_model | addUserAsset()  resp: >> " . json_encode($aux));

        return $aux;
    }

    /**
     * Obtiene lista de países desde REST_CORE
     * @return array|false Lista de países o false si hay error
     */
    public function obtenerPaisesRegistracion()
    {
        log_message('INFO', '#TRAZA|USER_MODEL|obtenerPaisesRegistracion() >> Iniciando obtención de países');
        
        try {
            $this->load->library('rest');
            
            $response = $this->rest->callAPI("GET", REST_CORE_PAISES, array());
            
            if ($response && isset($response['data'])) {
                // Limpiar la respuesta antes de decodificar
                $cleanResponse = trim($response['data']);
                log_message('DEBUG', '#TRAZA|USER_MODEL|obtenerPaisesRegistracion() >> Respuesta limpia: ' . substr($cleanResponse, 0, 200) . '...');
                
                $data = json_decode($cleanResponse, true);
                
                if (json_last_error() !== JSON_ERROR_NONE) {
                    log_message('ERROR', '#TRAZA|USER_MODEL|obtenerPaisesRegistracion() >> Error JSON: ' . json_last_error_msg());
                    log_message('DEBUG', '#TRAZA|USER_MODEL|obtenerPaisesRegistracion() >> Respuesta cruda: ' . $cleanResponse);
                    return false;
                }
                
                if (isset($data['tablas']['tabla']) && is_array($data['tablas']['tabla'])) {
                    log_message('DEBUG', '#TRAZA|USER_MODEL|obtenerPaisesRegistracion() >> Países obtenidos: ' . count($data['tablas']['tabla']));
                    return $data['tablas']['tabla'];
                } else {
                    log_message('DEBUG', '#TRAZA|USER_MODEL|obtenerPaisesRegistracion() >> Estructura de respuesta: ' . json_encode($data));
                }
            } else {
                log_message('DEBUG', '#TRAZA|USER_MODEL|obtenerPaisesRegistracion() >> Respuesta completa: ' . json_encode($response));
            }
            
            log_message('ERROR', '#TRAZA|USER_MODEL|obtenerPaisesRegistracion() >> Error al obtener países');
            return false;
            
        } catch (Exception $e) {
            log_message('ERROR', '#TRAZA|USER_MODEL|obtenerPaisesRegistracion() >> Excepción: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Valida formato de teléfono según país
     * @param string $telefono
     * @param int $reg_pais_id
     * @return bool
     */
    public function validarTelefonoPorPais($telefono, $reg_pais_id)
    {
        log_message('INFO', '#TRAZA|USER_MODEL|validarTelefonoPorPais() >> Validando teléfono para país: ' . $reg_pais_id);
        
        // Patrones básicos por país usando los IDs de la base de datos
        $patrones = array(
            'paises_registracionAR' => '/^\+?54\s?9?\d{4}\s?\d{6}$/', // Argentina
            'paises_registracionBR' => '/^\+?55\s?\d{2}\s?\d{4,5}\s?\d{4}$/', // Brasil
            'paises_registracionCL' => '/^\+?56\s?9?\d{8}$/', // Chile
            'paises_registracionUY' => '/^\+?598\s?9?\d{7}$/', // Uruguay
            'paises_registracionPE' => '/^\+?51\s?9?\d{8}$/', // Perú
            'paises_registracionEC' => '/^\+?593\s?9?\d{8}$/', // Ecuador
            'paises_registracionMX' => '/^\+?52\s?9?\d{10}$/', // México
            'paises_registracionBO' => '/^\+?591\s?9?\d{8}$/', // Bolivia
        );
        
        $patron = isset($patrones[$reg_pais_id]) ? $patrones[$reg_pais_id] : '/^\+?[\d\s\-\(\)]{7,15}$/';
        
        $valido = preg_match($patron, $telefono);
        log_message('DEBUG', '#TRAZA|USER_MODEL|validarTelefonoPorPais() >> Teléfono válido: ' . ($valido ? 'Sí' : 'No'));
        
        return $valido;
    }

    /**
     * Crea un usuario usando la nueva API WSO2 (PostgreSQL + AssetPlanner + BPM si hay sesión).
     * Reemplaza el flujo addUser() + addUserAsset() + crearUsrBPM().
     *
     * @param array $data Datos del usuario. Debe incluir password en texto plano (la API lo hashea).
     * @return array|false Array con 'usr_id', 'resultado', 'bpmSession' o false si falla
     */
    public function crearUsuarioAPI($data)
    {
        $this->load->library('REST');

        $payload = array(
            'usuario' => array(
                'usernick'   => isset($data['usernick']) ? $data['usernick'] : '',
                'email'      => isset($data['email']) ? $data['email'] : '',
                'firstname'  => isset($data['firstname']) ? $data['firstname'] : '',
                'lastname'   => isset($data['lastname']) ? $data['lastname'] : '',
                'password'   => isset($data['password']) ? $data['password'] : '',
                'role'       => isset($data['role']) ? $data['role'] : '2',
                'status'     => isset($data['status']) ? $data['status'] : 'approved',
                'banned_users' => isset($data['banned_users']) ? $data['banned_users'] : 'unban',
                'telefono'   => isset($data['telefono']) ? $data['telefono'] : '',
                'dni'        => isset($data['dni']) ? $data['dni'] : '',
                'business'   => isset($data['business']) ? $data['business'] : '',
                'image_name' => isset($data['image_name']) ? $data['image_name'] : '',
                'image'      => isset($data['image']) ? $data['image'] : ''
            ),
            'bpmSession' => $this->getBpmSession()
        );

        $url = API_CORE . '/usuario';
        log_message('INFO', '#TRAZA | User_model | crearUsuarioAPI() >> URL: ' . $url);
        log_message('DEBUG', '#TRAZA | User_model | crearUsuarioAPI() >> Payload: ' . json_encode($payload));

        $result = $this->rest->callAPI('POST', $url, $payload, array('Accept: application/json'));

        if (!$result || !isset($result['status'])) {
            $errMsg = isset($result['data']) ? $result['data'] : json_encode($result);
            log_message('ERROR', '#TRAZA | User_model | crearUsuarioAPI() >> Error conexión/respuesta: ' . $errMsg);
            return array('error' => 'No se pudo conectar con el servicio de usuarios.', 'detail' => $errMsg);
        }

        $response_body = json_decode($result['data'], true);
        $httpCode = isset($result['code']) ? $result['code'] : 0;

        if (!$result['status'] || $httpCode >= 300) {
            $apiError = 'Error del servicio (HTTP ' . $httpCode . ')';
            $apiDetail = $result['data'];
            if (is_array($response_body) && isset($response_body['respuesta'])) {
                $r = $response_body['respuesta'];
                $apiError = isset($r['error']) ? $r['error'] : $apiError;
                $apiDetail = isset($r['detalle']) ? $r['detalle'] : $result['data'];
            }
            log_message('ERROR', '#TRAZA | User_model | crearUsuarioAPI() >> ' . $apiError . ' | detalle: ' . (is_string($apiDetail) ? $apiDetail : json_encode($apiDetail)));
            log_message('ERROR', 'adduser API HTTP ' . $httpCode . ' | URL=' . $url . ' | body=' . substr(is_string($result['data']) ? $result['data'] : json_encode($result['data']), 0, 800));
            return array('error' => $apiError, 'detail' => $apiDetail);
        }

        if (!is_array($response_body) || !isset($response_body['respuesta'])) {
            log_message('ERROR', '#TRAZA | User_model | crearUsuarioAPI() >> Respuesta inválida: ' . json_encode($response_body));
            return array('error' => 'Respuesta inválida del servicio.', 'detail' => $result['data']);
        }

        $resp = $response_body['respuesta'];
        if (isset($resp['resultado']) && $resp['resultado'] === 'ok' && isset($resp['usr_id'])) {
            log_message('INFO', '#TRAZA | User_model | crearUsuarioAPI() >> Usuario creado. ID: ' . $resp['usr_id']);
            return array(
                'usr_id'     => $resp['usr_id'],
                'resultado'  => 'ok',
                'bpmSession' => isset($resp['bpmSession']) ? $resp['bpmSession'] : null
            );
        }

        $apiError = isset($resp['error']) ? $resp['error'] : 'Respuesta sin éxito';
        $apiDetail = isset($resp['detalle']) ? $resp['detalle'] : json_encode($resp);
        log_message('ERROR', '#TRAZA | User_model | crearUsuarioAPI() >> Respuesta sin éxito: ' . json_encode($resp));
        return array('error' => $apiError, 'detail' => $apiDetail);
    }

    /**
     * Obtiene la sesión de BPM para la API (si se implementa).
     * Por ahora retorna null (la API no llama a BPM en ese caso).
     */
    private function getBpmSession()
    {
        return null;
    }
}
