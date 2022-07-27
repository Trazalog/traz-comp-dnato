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
        Muestra el listado de los Menues
        
    */
    public function menuesList(){

        $data = $this->session->userdata;
		$data['usersList'] = $this->user_model->getListUserData();
        $data['groupsBpm'] = $this->Roles->getBpmGroups();
        $result = $this->mmenu->getMenues();
        $data['title'] = "Listado de Menues";

        if(empty($data['role'])){
            redirect(base_url().'main/login/');
        }
        $dataLevel = $this->userlevel->checkLevel($data['role']);

        $data['menues'] = $result['datos'];
        $data['totalDatos'] = $result['totalDatos'];
 
        log_message('DEBUG','#TRAZA|Menu|menuesList()  $data: >> '.json_encode($data));
        log_message('DEBUG','#TRAZA|Menu|menuesList()  $data[email]: >> '.json_encode($data['email']));
        log_message('DEBUG','#TRAZA|Menu|menuesList()  $r[datos]: >> '.json_encode($data['menues']));
        log_message('DEBUG','#TRAZA|Menu|menuesList()  $r[totalDatos]: >> '.json_encode($data['totalDatos']));
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
    
    public function deleteMenu(){

        $dataPost['modulo'] = $this->input->post('modulo');
        $dataPost['opcion'] = $this->input->post('opcion');

        $infoDelete = $this->mmenu->deleteMenu($dataPost);

        log_message('DEBUG','#TRAZA|Menu|menuesList()  $$dataPost[modulo]: >> '.json_encode($dataPost['modulo']));
        log_message('DEBUG','#TRAZA|Menu|menuesList()  $$dataPost[opcion]: >> '.json_encode($dataPost['opcion']));
        log_message('DEBUG','#TRAZA|Menu|menuesList()  $infoDelete: >> '.$infoDelete);

        if(!$infoDelete){
            $this->session->set_flashdata('flash_message', 'Error, no se puede desactivado el registro');
        }else{
            $this->session->set_flashdata('success_message', 'Desactivado correctamente el registro.');
        }
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