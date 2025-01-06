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

    //agrega nueva empresa
    public function agregarEmpresa($d)
    {
        log_message('DEBUG','#TRAZA| TRAZ-TOOLS | EMPRESA | guardarEmpresa()  $d: >> '.json_encode($d));        
        $empresa['nombre'] = $d['nombre'];
        $empresa['cuit'] = $d['cuit'];
        $empresa['descripcion'] = $d['descripcion'];
        $empresa['telefono'] = $d['telefono'];
        $empresa['email'] = $d['email'];
        $empresa['pais_id'] = $d['pais_id'];
        $empresa['prov_id'] = $d['prov_id'];
        $empresa['loca_id'] = $d['loca_id'];
        $empresa['imagepath'] = $d['imagepath'];
        $empresa['image'] = $d['image'];
        $post['empresa'] = $empresa;
        $resource = '/empresa';
        $url = API_CORE . $resource;
        $aux = $this->rest->callApi("POST", $url, $post);
        return $aux;
    }

    //revisar que este duplicado el mail
    public function isDuplicate($email)
    {
        $this->db->get_where('core.empresas', array('email' => $email), 1);
        return $this->db->affected_rows() > 0 ? TRUE : FALSE;         
    }

}