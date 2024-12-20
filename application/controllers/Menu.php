<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Menu extends CI_Controller {

    function __construct(){

		parent::__construct();
        $this->load->model('User_model', 'user_model', TRUE);
		$this->load->model('Mmenu', 'mmenu', TRUE);
		$this->load->library('form_validation');
		$this->form_validation->set_error_delimiters('<div class="error">', '</div>');
		$this->status = $this->config->item('status');
		$this->roles = $this->config->item('roles');
		$this->load->library('userlevel');
		$this->load->config('email');
		$this->load->model('Roles');
	}

    public function index(){

        //user data from session
		$data = $this->session->userdata();
		log_message('DEBUG','#Menu/index | '.json_encode($data));

		if(empty($data['email'])){
				log_message('DEBUG','#Menu/index | No email');
				redirect(base_url().'main/login/');
		}

		//check user level
		if(empty($data['role'])){
				log_message('DEBUG','#Menu/index | No role');
				redirect(base_url().'main/login/');
		}
		$dataLevel = $this->userlevel->checkLevel($data['role']);
		//check user level

        $data['title'] = "Dashboard Admin";

        if($data['direccion']){
            log_message('DEBUG','#Menu/index | Redireccion: '.$data['direccion']);
            redirect($data['direccion']);
        }else{
                log_message('DEBUG','#Menu/index | Error de Redireccionamiento');
                echo 'Error de Redireccionamiento';
        }
    
    }

    /*  
        MenuList
        Muestra el listado Alta de los Menues
        
    */
    public function menuesList(){

        $data = $this->session->userdata;
		$data['usersList'] = $this->user_model->getListUserData();
        $data['groupsBpm'] = $this->Roles->getBpmGroups();
        $result = $this->mmenu->getMenues();
        $data['modulos'] = $this->mmenu->getModulos();
        $data['iconos'] = $this->mmenu->getIconos();
        $data['op_padres'] = $this->mmenu->getOpcionPadre();
										
        $data['title'] = "Listado de Menues";

        if(empty($data['role'])){
            redirect(base_url().'main/login/');
        }
        $dataLevel = $this->userlevel->checkLevel($data['role']);

        $data['menues'] = $result['datos'];
        $data['totalDatos'] = $result['totalDatos'];
 
        log_message('DEBUG','#TRAZA|Menu|menuesList()  $data: >> '.json_encode($data));
        log_message('DEBUG','#TRAZA|Menu|menuesList()  $data[email]: >> '.json_encode($data['email']));
        log_message('DEBUG','#TRAZA|Menu|menuesList()  $data[menues]: >> '.json_encode($data['menues']));
        log_message('DEBUG','#TRAZA|Menu|menuesList()  $data[modulos]: >> '.json_encode($data['modulos']));
        log_message('DEBUG','#TRAZA|Menu|menuesList()  $data[iconos]: >> '.json_encode($data['iconos']));
        log_message('DEBUG','#TRAZA|Menu|menuesList()  $data[op_padres]: >> '.json_encode($data['op_padres']));
        log_message('DEBUG','#TRAZA|Menu|menuesList()  $data[totalDatos]: >> '.json_encode($data['totalDatos']));
        //check user level

        //check is admin or not
        if($dataLevel == "is_admin"){
                    $this->load->view('header', $data);
                    $this->load->view('navbar', $data);
                    $this->load->view('container',$data);
                    $this->load->view('menu/menuesList', $data);
                    $this->load->view('footer');
        }else{
                redirect(base_url().'main/');
        }

    }

    public function addMenuRoles(){

        $dataPost['modulo'] = $this->input->post('modulo');
        $dataPost['opcion'] = $this->input->post('opcion_padre');
        $dataPost['roles_id'] = $this->input->post('roles');
        $dataPost['groups_id'] = $this->input->post('groups');
        $dataPost['operacion'] = $this->input->post('operacion');

        if(strpos($dataPost['groups_id'],'-')){

            $groups = explode("-", $dataPost['groups_id']);
            $dataPost['groups'] = $groups[2]; 

        }else{
            $dataPost['groups'] = $dataPost['groups_id']; 
        }
        
        if(strpos($dataPost['roles_id'],'-')){

            $roles = explode("-", $dataPost['roles_id']);
            $dataPost['roles'] = $roles[2]; 

        }else{
            $dataPost['roles'] = $dataPost['roles_id']; 
        }
        log_message('DEBUG','#TRAZA|Menu|addMenuRoles()  $dataPost: >> '.json_encode($dataPost));

        if($dataPost['operacion'] == 'insert'){

            if($this->mmenu->addMenuRoles($dataPost)){
                log_message('DEBUG','#TRAZA|Menu|addMenuRoles()  $dataPost: >> '.json_encode($dataPost));
                $this->session->set_flashdata('success_message', 'Guardado correctamente el registro.');

                redirect(base_url().'menu/rolesList');
            }else{
                log_message('DEBUG','#TRAZA|Menu|addMenuRoles()  $dataPost: >> '.json_encode($dataPost));
                $this->session->set_flashdata('flash_message', 'Error, no se puede guardar el registro');

                redirect(base_url().'menu/rolesList');
                //$this->menuesList();
            }
        }

        if($dataPost['operacion'] == 'update'){

            if($this->mmenu->updateMembershipsMenues($dataPost)){

                log_message('DEBUG','#TRAZA|Menu|updateMembershipsMenues()  $dataPost: >> '.json_encode($dataPost));
                $this->session->set_flashdata('success_message', 'Actualizado correctamente el registro.');

                redirect(base_url().'menu/rolesList');
            }else{
                log_message('DEBUG','#TRAZA|Menu|updateMembershipsMenues() $dataPost: >> '.json_encode($dataPost));
                $this->session->set_flashdata('flash_message', 'Error, no se puede actualizar el registro');

                redirect(base_url().'menu/rolesList');
            }
        }
    }

    public function addMenu(){

        $dataPost['modulo'] = $this->input->post('modulo');
        $dataPost['opcion'] = $this->input->post('opcion');
        $dataPost['texto'] = $this->input->post('texto');
        $dataPost['opcion_padre'] = $this->input->post('opcion_padre');
        $dataPost['orden'] = $this->input->post('orden');
        $dataPost['url'] = $this->input->post('url');
        $dataPost['url_icono'] = $this->input->post('url_icono');
        $dataPost['texto_onmouseover'] = $this->input->post('texto_onmouseover');
        $dataPost['operacion'] = $this->input->post('operacion');

        log_message('DEBUG','#TRAZA|Menu|addMenu()  $dataPost: >> '.json_encode($dataPost));

        if($dataPost['operacion'] == 'insert'){

            if($this->mmenu->addMenues($dataPost)){
                log_message('DEBUG','#TRAZA|Menu|addMenu()  $dataPost: >> '.json_encode($dataPost));
                $this->session->set_flashdata('success_message', 'Guardado correctamente el registro.');

                redirect(base_url().'menu/menuesList');
            }else{
                log_message('DEBUG','#TRAZA|Menu|addMenu()  $dataPost: >> '.json_encode($dataPost));
                $this->session->set_flashdata('flash_message', 'Error, no se puede guardar el registro');

                redirect(base_url().'menu/menuesList');
                //$this->menuesList();
            }
        
        }
        
        if($dataPost['operacion'] == 'update'){
                
            if($this->mmenu->updateMenues($dataPost)){
                log_message('DEBUG','#TRAZA|Menu|addMenu()  $dataPost: >> '.json_encode($dataPost));
                $this->session->set_flashdata('success_message', 'Actualizado correctamente el registro.');

                redirect(base_url().'menu/menuesList');
            }else{
                log_message('DEBUG','#TRAZA|Menu|addMenu()  $dataPost: >> '.json_encode($dataPost));
                $this->session->set_flashdata('flash_message', 'Error, no se puede actualizar el registro');

                redirect(base_url().'menu/menuesList');
            }

        }
    }

    public function activeMenu(){

        $dataPost['modulo'] = $this->input->post('modulo');
        $dataPost['opcion'] = $this->input->post('opcion');

        $infoDelete = $this->mmenu->activeMenu($dataPost);

        log_message('DEBUG','#TRAZA|Menu|menuesList()  $$dataPost[modulo]: >> '.json_encode($dataPost['modulo']));
        log_message('DEBUG','#TRAZA|Menu|menuesList()  $$dataPost[opcion]: >> '.json_encode($dataPost['opcion']));
        log_message('DEBUG','#TRAZA|Menu|menuesList()  $infoDelete: >> '.$infoDelete);

        if(!$infoDelete){
            $this->session->set_flashdata('flash_message', 'Error, no se puede activar el registro');
        }else{
            $this->session->set_flashdata('success_message', 'Activado correctamente el registro.');
        }
    }

    public function activeMenuRole(){

        $dataPost['group'] = $this->input->post('group');
        $dataPost['modulo'] = $this->input->post('modulo');
        $dataPost['opcion'] = $this->input->post('opcion');
        $dataPost['role'] = $this->input->post('role');

        $infoActive = $this->mmenu->activeMenuRole($dataPost);

        log_message('DEBUG','#TRAZA|Menu|deleteMenuRole()  $$dataPost[group]: >> '.json_encode($dataPost['group']));
        log_message('DEBUG','#TRAZA|Menu|deleteMenuRole()  $$dataPost[modulo]: >> '.json_encode($dataPost['modulo']));
        log_message('DEBUG','#TRAZA|Menu|deleteMenuRole()  $$dataPost[opcion]: >> '.json_encode($dataPost['opcion']));
        log_message('DEBUG','#TRAZA|Menu|deleteMenuRole()  $$dataPost[role]: >> '.json_encode($dataPost['role']));
        log_message('DEBUG','#TRAZA|Menu|deleteMenuRole()  $infoActive: >> '.$infoActive);

        if(!$infoActive){
            $this->session->set_flashdata('flash_message', 'Error, no se puede activar el registro');
        }else{
            $this->session->set_flashdata('success_message', 'Activado correctamente el registro.');
        }
    }
    
    public function deleteMenu(){

        $dataPost['modulo'] = $this->input->post('modulo');
        $dataPost['opcion'] = $this->input->post('opcion');

        $infoDelete = $this->mmenu->deleteMenu($dataPost);

        log_message('DEBUG','#TRAZA|Menu|deleteMenu()  $$dataPost[modulo]: >> '.json_encode($dataPost['modulo']));
        log_message('DEBUG','#TRAZA|Menu|deleteMenu()  $$dataPost[opcion]: >> '.json_encode($dataPost['opcion']));
        log_message('DEBUG','#TRAZA|Menu|deleteMenu()  $infoDelete: >> '.$infoDelete);

        if($infoDelete === FALSE){
            $this->session->set_flashdata('flash_message', 'Error, no se puede desactivado el registro');
        }else{
            if( $infoDelete === TRUE){
                $this->session->set_flashdata('success_message', 'Desactivado correctamente el registro.');
            }
            else{
                if($infoDelete === -1 ){
                    $this->session->set_flashdata('flash_message', 'Error, La opción ya se encuentra asignado a un módulo. No puede desactivarse');
                }
            }
        }
             
    }

    public function deleteMenuRole(){

        $dataPost['group'] = $this->input->post('group');
        $dataPost['modulo'] = $this->input->post('modulo');
        $dataPost['opcion'] = $this->input->post('opcion');
        $dataPost['role'] = $this->input->post('role');

        $infoDelete = $this->mmenu->deleteMenuRole($dataPost);

        log_message('DEBUG','#TRAZA|Menu|deleteMenuRole()  $$dataPost[group]: >> '.json_encode($dataPost['group']));
        log_message('DEBUG','#TRAZA|Menu|deleteMenuRole()  $$dataPost[modulo]: >> '.json_encode($dataPost['modulo']));
        log_message('DEBUG','#TRAZA|Menu|deleteMenuRole()  $$dataPost[opcion]: >> '.json_encode($dataPost['opcion']));
        log_message('DEBUG','#TRAZA|Menu|deleteMenuRole()  $$dataPost[role]: >> '.json_encode($dataPost['role']));
        log_message('DEBUG','#TRAZA|Menu|deleteMenuRole()  $infoDelete: >> '.$infoDelete);

        if(!$infoDelete){
            $this->session->set_flashdata('flash_message', 'Error, no se puede desactivado el registro');
        }else{
            $this->session->set_flashdata('success_message', 'Desactivado correctamente el registro.');
        }
    }

    public function getModulo(){

        $data = $this->session->userdata;
		$data['usersList'] = $this->user_model->getListUserData();
        $data['groupsBpm'] = $this->Roles->getBpmGroups();
        $data['emp_connect'] =  $this->user_model->gestMembershipsUserInfo($data['email'],1);           //Empresas del conectado
        $data['groups'] = $this->Roles->getBpmGroups(); 												// Grupos Bonita
        $data['roles'] = $this->Roles->getBpmRoles();   												// Roles Bonita
        $data['modulos'] = $this->mmenu->getModulos();   												// Roles 
        $result = $this->mmenu->getMenuesRoles();


    }


    /*  
        MenuRoles

    */
    public function rolesList(){

        $data = $this->session->userdata;
		$data['usersList'] = $this->user_model->getListUserData();
        $data['groupsBpm'] = $this->Roles->getBpmGroups();
        $data['emp_connect'] =  $this->user_model->gestMembershipsUserInfo($data['email'],1);           //Empresas del conectado
        $data['groups'] = $this->Roles->getBpmGroups(); 												// Grupos Bonita
        $data['roles'] = $this->Roles->getBpmRoles();   												// Roles Bonita
        $data['modulos'] = $this->mmenu->getModulos();
        $data['op_padres'] = $this->mmenu->getOpcionPadre();
        $result = $this->mmenu->getMenuesRoles();

		$data['title'] = "Listado de Menues por Roles";

        if(empty($data['role'])){
            redirect(base_url().'main/login/');
        }
        $dataLevel = $this->userlevel->checkLevel($data['role']);

        $data['mnroles'] = $result['datos'];
        $data['totalDatos'] = $result['totalDatos'];
 
        log_message('DEBUG','#TRAZA|Menu|menuesList()   $data: >> '.json_encode($data));
        log_message('DEBUG','#TRAZA|Menu|menuesList()   $data[email]: >> '.json_encode($data['email']));
        log_message('DEBUG','#TRAZA|Menu|menuesList()   $r[datos]: >> '.json_encode($data['menues']));
  		log_message('DEBUG','#TRAZA|MAIN|menuesList()   $data[emp_connect] >> '.json_encode($data['emp_connect']));
        log_message('DEBUG','#TRAZA|MAIN|menuesList()   $data[groups]: >> '.json_encode($data['groups']));
        log_message('DEBUG','#TRAZA|MAIN|menuesList()   $data[roles]: >> '.json_encode($data['roles']));
        log_message('DEBUG','#TRAZA|MAIN|menuesList()   $data[mnroles]: >> '.json_encode($data['mnroles']));
        log_message('DEBUG','#TRAZA|Menu|menuesList()   $r[totalDatos]: >> '.json_encode($data['totalDatos']));
        //check user level

        //check is admin or not
        if($dataLevel == "is_admin"){
                    $this->load->view('header', $data);
                    $this->load->view('navbar', $data);
                    $this->load->view('container',$data);
                    $this->load->view('menu/menurolesList', $data);
                    $this->load->view('footer');
        }else{
                redirect(base_url().'main/');
        }
    }
    

}