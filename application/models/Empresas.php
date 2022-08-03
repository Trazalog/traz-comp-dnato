<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}

class Empresas extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
		* Trae listado de Empresas
		* @param
		* @return array con Empresas 
		*/
		function listarEmpresas(){
            log_message('DEBUG','#TRAZA|EMPRESAS|listarEmpresas');
            $aux = $this->rest->callAPI("GET",REST_CORE."/empresas");
            $aux =json_decode($aux["data"]);
            return $aux->empresas->empresa;
    }

    public function listarPaises() {
        $resource = "/paises";
        $url = REST_CORE . $resource;
        $aux = $this->rest->callApi('GET', $url);
        $aux = json_decode($aux["data"]);
        $paises = $aux->paises->pais;
        return $paises;
    }

    /**
     * Obtener estados dependiendo el pais seleccionado
    * @param valor de pais
    * @return array con listado de estados
    */
    public function getEstados($pais) {
        $post['_post_valor'] = $pais;
        log_message('DEBUG','#TRAZA| TRAZ-TOOLS | EMPRESAS | getEstados() $post: >> '.json_encode($post));
        $pais = urlencode($pais);
        $resource = "/estados/pais/".$pais;
        $url = REST_CORE . $resource;
        $aux = $this->rest->callApi('GET', $url); 
        $aux = json_decode($aux["data"]);
        $valores = $aux->estados->estado;
        return $valores;
    }

    /**
     * Obtener localidades dependiendo del pais y estado seleccionado
    * @param valor de pais y estado
    * @return array con listado de localidades
    */
    public function getLocalidades($pais, $estado) {
        $post['_post_pais'] = $pais;
        $post['_post_estado'] = $estado;
        log_message('DEBUG','#TRAZA| TRAZ-TOOLS | EMPRESAS | getLocalidades() $post: >> '.json_encode($post));
        $pais = urlencode($pais);
        $estado = urlencode($estado);
        $resource = '/localidades/pais/' . $pais . '/estado/' . $estado;
        $url = REST_CORE . $resource;
        $aux = $this->rest->callApi('GET', $url); 
        $aux = json_decode($aux["data"]);
        $valores = $aux->localidades->localidad;
        return $valores;
    }

    //add user login
    public function agregarEmpresa($d)
    {
        log_message('DEBUG','#TRAZA| TRAZ-TOOLS | EMPRESA | guardarEmpresa()  $d: >> '.json_encode($d));
        // if ($d['pais_id'] == NULL) {
        //     $d['pais_id'] = '';
        // }
        // if ($d['prov_id'] == NULL) {
        //     $d['prov_id'] = '';
        // }
        // if ($d['loca_id'] == NULL) {
        //     $d['loca_id'] = '';
        // }
        
        $empresa['nombre'] = $d['nombre'];
        $empresa['cuit'] = $d['cuit'];
        $empresa['descripcion'] = $d['descripcion'];
        $empresa['telefono'] = $d['telefono'];
        $empresa['email'] = $d['email'];
        $empresa['pais_id'] = $d['pais_id'];
        $empresa['prov_id'] = $d['prov_id'];
        $empresa['loca_id'] = $d['loca_id'];
    
        $post['_post_empresa'] = $empresa;

        // $post = array(
        //     'nombre'=>$d['nombre'],
        //     'cuit'=>$d['cuit'],
        //     'descripcion'=>$d['descripcion'],							
        //     'telefono'=>$d['telefono'],				
        //     'email'=>$d['email'],									
        //     'pais_id'=>$d['pais_id'],
        //     'prov_id'=>$d['prov_id'], 			
        //     'loca_id'=>$d['loca_id']
        // );

        $resource = '/empresa';
        $url = REST_CORE . $resource;
        $aux = $this->rest->callApi("POST", $url, $post); 
        // $aux = json_decode($aux["status"]);
        return $aux;

        //$array[$key]->valor4_base64 = base64_encode(file_get_contents($_FILES[$nom]['tmp_name']));
        //imagen codificada
        // $string['image_name'] = $d['image_name'];
        // $string['image'] = $d['image'];
        //log_message('DEBUG','#TRAZA|USER_MODEL|addUser($d) >> $string -> '.json_encode($string));

        // $q = $this->db->insert('seg.users',$string);

        // //log_message('DEBUG','#TRAZA|USER_MODEL|addUser($d) >> 	$q -> '.json_encode($q));

        // $error = $this->db->error();

        // if($q){
        //     $bsnes = array (
        //         'email'=>$d['email'],	
        //         'busines' =>  $d['business']              
        //     );

        //     $bs = $this->db->insert('seg.users_business', $bsnes);

        //     $error = $this->db->error();

        //     $this->db->select_max('id');						
        //     $query = $this->db->get('seg.users');
        //     $userInfo = $query->row('id');

        //     if($userInfo){
        //         return $userInfo;
        //     }else{
        //         //log_message('ERROR','#TRAZA|USER_MODEL|addUser($d) >> ERROR -> '.json_encode($error['message']));
        //         return false;
        //     }

        // }else{
        //     return false;
        // }    
    }

}