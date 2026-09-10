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

    public function getEmpresaById($emprId)
    {
        $url = REST_CORE . '/empresa/' . (int) $emprId;
        $aux = $this->rest->callApi('GET', $url);
        if (empty($aux['status']) || empty($aux['data'])) {
            return null;
        }
        $decoded = json_decode($aux['data']);
        return isset($decoded->empresa) ? $decoded->empresa : null;
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

    /**
     * Rollback: soft-delete de la empresa en core.empresas (eliminado=TRUE).
     *
     * Apunta directo al DataService (COREDataService) porque el API toolsCOREAPI
     * sólo expone /empresa en método POST (el DELETE devuelve 405 Method Not Allowed).
     * Mismo patrón que Establecimientos::eliminarEstablecimiento() y eliminarDeposito().
     *
     * @param string|int $emprId
     * @return array retorno de REST::callAPI (status, data, code)
     */
    public function eliminarEmpresa($emprId)
    {
        $emprId = trim((string) $emprId);
        if ($emprId === '') {
            log_message('ERROR', '#TRAZA|EMPRESAS|eliminarEmpresa() >> empr_id vacío');
            return array('status' => false, 'data' => '', 'code' => 0);
        }
        $post = array(
            '_delete_empresa' => array(
                'empr_id' => $emprId
            )
        );
        $url = rtrim((string) REST_CORE, '/') . '/empresa';
        log_message('INFO', '#TRAZA|EMPRESAS|eliminarEmpresa() >> DELETE ' . $url . ' empr_id=' . $emprId);
        return $this->rest->callAPI('DELETE', $url, $post);
    }

    /**
     * Crea en Bonita un rol de la empresa.
     *
     * Mismo contrato que usa la sequence toolsCreateRole del API: el rol queda con
     * name "<empr_id>-<rolBase> <empresa>" y displayName "<rolBase> <empresa>".
     *
     * @param string $emprId
     * @param string $rolBase   parte fija del rol, p.ej. "SMA - Generador"
     * @param string $empresa   razón social
     * @param string $bpmSession
     * @return array retorno de REST::callAPI (status, data, code)
     */
    public function crearRolBpm($emprId, $rolBase, $empresa, $bpmSession)
    {
        $rolCompleto = trim((string) $rolBase) . ' ' . trim((string) $empresa);
        $post = array(
            'session' => (string) $bpmSession,
            'payload' => array(
                'icon' => '',
                'name' => (string) $emprId . '-' . $rolCompleto,
                'displayName' => $rolCompleto,
                'description' => ''
            )
        );
        $url = rtrim((string) REST_BPM, '/') . '/role';
        return $this->rest->callAPI('POST', $url, $post);
    }

    /**
     * Mapea un actor de un proceso de Bonita al grupo de la empresa, opcionalmente
     * a través de un rol.
     *
     * Sin $rolBase usa POST /bpm/actor/grupo (solo grupo); con rol, /bpm/actor/membership.
     * Son los mismos endpoints que invocan las sequences toolsBpmActorGrupo y
     * toolsBpmActorMembership del API.
     *
     * @param string      $emprId
     * @param string      $empresa   razón social
     * @param string      $proceso   nombre del proceso en Bonita, habilitado
     * @param string      $actor     nombre del actor dentro del proceso
     * @param string|null $rolBase   parte fija del rol; null o '' mapea solo el grupo
     * @param string      $bpmSession
     * @return array retorno de REST::callAPI (status, data, code)
     */
    public function mapearActorBpm($emprId, $empresa, $proceso, $actor, $rolBase, $bpmSession)
    {
        $empresa = trim((string) $empresa);
        $grupo = (string) $emprId . '-' . $empresa;
        $post = array(
            'session' => (string) $bpmSession,
            'nombre_proceso' => (string) $proceso,
            'nombre_actor' => (string) $actor,
            'nombre_grupo' => $grupo
        );

        $rolBase = trim((string) $rolBase);
        if ($rolBase === '') {
            $url = rtrim((string) REST_BPM, '/') . '/actor/grupo';
        } else {
            $post['nombre_rol'] = (string) $emprId . '-' . $rolBase . ' ' . $empresa;
            $url = rtrim((string) REST_BPM, '/') . '/actor/membership';
        }

        return $this->rest->callAPI('POST', $url, $post);
    }

    //revisar que este duplicado el mail
    public function isDuplicate($email)
    {
        $this->db->get_where('core.empresas', array('email' => $email), 1);
        return $this->db->affected_rows() > 0 ? TRUE : FALSE;         
    }

}