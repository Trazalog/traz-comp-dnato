<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Empresa extends CI_Controller {

    function __construct()
    {
        parent::__construct();
        $this->load->model('User_model', 'user_model', TRUE);
        $this->load->model('Empresas');
		$this->load->library('form_validation');
		$this->form_validation->set_error_delimiters('<div class="error">', '</div>');
		$this->status = $this->config->item('status');
		$this->roles = $this->config->item('roles');
		$this->load->library('userlevel');
		$this->load->config('email');
		$this->load->model('Roles');
     }
     
    public function listarEmpresas()	
    {
	    $data = $this->session->userdata;
        $data['title'] = "Lista de Empresas";
        $data['lista_empresas'] = $this->Empresas->listarEmpresas();
        $data['usersList'] = $this->user_model->getListUserData();
		$data['groupsBpm'] = $this->Roles->getBpmGroups();
		$data['emp_connect'] =  $this->user_model->gestMembershipsUserInfo($data['email'],1);

        //check user level
		if(empty($data['role'])){
            redirect(base_url().'main/login/');
        }
        
		if($data['email'] == BPM_ADMIN_MAIL){
            $this->load->view('header', $data);
            $this->load->view('navbar', $data);
            $this->load->view('container',$data);
            $this->load->view('empresas/list', $data);
            $this->load->view('footer');
        }else{
            redirect(base_url().'main/');
        }
    }

    //agrego nueva Empresa en la BD
	public function agregarEmpresa()
	{
		$data = $this->session->userdata;

		//check user level
		if(empty($data['role'])){
				redirect(base_url().'main/login/');
		}
		$dataLevel = $this->userlevel->checkLevel($data['role']);

        if($data['email'] == BPM_ADMIN_MAIL){
            $this->form_validation->set_rules('nombre', 'Nombre', 'required');
            $this->form_validation->set_rules('cuit', 'Cuit', 'required');
            $this->form_validation->set_rules('descripcion', 'Descripcion', 'required');

            $data['title'] = "Agregar Empresa";
            if ($this->form_validation->run() == FALSE) {
		        $data['listarPaises'] = $this->Empresas->listarPaises();
                
                $this->load->view('header', $data);
                $this->load->view('navbar',$data);
                $this->load->view('container');
                $this->load->view('empresas/view', $data);
                $this->load->view('footer');
            }else{
                if($this->user_model->isDuplicate($this->input->post('email'))){
                    $this->session->set_flashdata('flash_message', ' Ya existe un usuario asociado a ese Email');
                    redirect(base_url().'empresa/agregarEmpresa');
                }else{
                    $post = $this->input->post(NULL, TRUE);
                    $cleanPost = $this->security->xss_clean($post);
                    $cleanPost['nombre'] = $this->input->post('nombre');
                    $cleanPost['cuit'] = $this->input->post('cuit');
                    $cleanPost['descripcion'] = $this->input->post('descripcion');
                    $cleanPost['telefono'] = $this->input->post('telefono');
                    $cleanPost['email'] = $this->input->post('email');
                    $cleanPost['pais_id'] = $this->input->post('pais_id');
                    $cleanPost['prov_id'] = $this->input->post('prov_id');
                    $cleanPost['loca_id'] = $this->input->post('loca_id');
                    
                    //insert to database
                    $usr_id = $this->Empresas->agregarEmpresa($cleanPost);

                    //redirect(base_url().'main/users/'.$usr_id);
                    redirect(base_url().'empresa/listarEmpresas/');
                };
            }
		}else{
				redirect(base_url().'empresa/');
		}
	}

    public function getEstados()
	{
		log_message('INFO','#TRAZA| EMPRESAS | getEstados() >> ');
		$pais = $this->input->get('id_pais');
		$resp = $this->Empresas->getEstados($pais);
		if ($resp != null) {
			echo json_encode($resp);
		} else {
			echo json_encode($resp);
		}
	}

	public function getLocalidades()
	{
		log_message('INFO','#TRAZA| EMPRESAS | getLocalidades() >> ');
		$pais = $this->input->get('id_pais');
		$estado = $this->input->get('id_estado');
		$resp = $this->Empresas->getLocalidades($pais, $estado);
		if ($resp != null) {
			echo json_encode($resp);
		} else {
			echo json_encode($resp);
		}
	}

    //borrar empresa
	public function borrarEmpresa($empr_id) {

		$data = $this->session->userdata;
		if(empty($data['role'])){
				redirect(base_url().'main/login/');
		}
		$dataLevel = $this->userlevel->checkLevel($data['role']);
		$emplevel = $data['groupBpm'];

		//check is admin or not
		if($dataLevel == "is_admin"){

			$data['user'] = $this->Empresas->getUserInfo($empr_id);
			$data['memberships'] = $this->Empresas->getMembershipsUserInfoEmpresa($data['user']->email, $emplevel);

			log_message('DEBUG','#TRAZA|MAIN|borrarEmpresa()  $data[user]: >> '.json_encode($data['user'])); 
			log_message('DEBUG','#TRAZA|MAIN|borrarEmpresa()  $data[memberships]: >> '.json_encode($data['memberships'])); 
			log_message('DEBUG','#TRAZA|MAIN|borrarEmpresa()  $data[groupBpm]: >> '.json_encode($data['memberships'])); 

			if($data['memberships']){
				$this->session->set_flashdata('flash_message', 'Error, Este Usuario tiene roles de sistema en la empresa asignados!');
			}else{	
				/**Eliminar tabla seg.users_bisiness */
				$deleteUserBusines = $this->Empresas->deleteUserBusines($data['user']->email,$busines);

				if(!$deleteUserBusines ){
					$this->session->set_flashdata('flash_message', 'Error, no se puede elminar el UserBusines del usuario '.$data['user']->email);
				}else{

					/*Mejora del Eliminado*/
					$deleteUserLocal =$this->Empresas->deleteUser($empr_id);
					//log_message('DEBUG','#TRAZA|MAIN|borrarEmpresa()  $deleteUserLocal: >> '.json_encode($deleteUserLocal)); 
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
  
    public function obtener()
    {
        $res = $this->Roles->obtener();
        echo json_encode($res);
    }

    public function guardar()
    {
        $data = $this->input->post();
        $res = $this->Roles->guardar($data);
        echo json_encode($res);
    }

}