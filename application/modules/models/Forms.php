<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Forms extends CI_Model
{

    private $REST;

    public function __construct()
    {
        parent::__construct();
        $this->REST = &get_instance()->rest;

    }
    /**
        * Guarda la instacia del formulario dinámico
        * @param array datos de formulario
        * @return 
	*/
    public function guardar($form_id, $data = false)
    {   
        $items = $this->obtenerPlantilla($form_id);
        $array = array();
        $aux = array();
        
        foreach ($items->items as $key => $o) {

            unset($o->nombre);
            
            if ($o->name) {

                if(!is_array($data[$o->name]) && !is_array($_FILES["-file-".$o->name]['tmp_name'])){

                    $o->valor = ($o->tipo_dato == 'radio') ? $data[empresa()."-".$o->name] : $data[$o->name];
                    $o->valor4_base64 = null;
                    
                    
                    if($o->tipo_dato == 'image' || $o->tipo_dato == 'file'){
                        
                        $nom = "-file-".$o->name;
                        
                        if(!empty($_FILES[$nom]['tmp_name'])){
                            $o->valor4_base64 = base64_encode(file_get_contents($_FILES[$nom]['tmp_name']));
                        }
                    }
                    
                    array_push($array, $o);
                }else{
                    if(!empty($data[$o->name])){
                        foreach ($data[$o->name] as $i => $datos ) {
                            $datoPlantilla = clone $o;

                            $datoPlantilla->valor = $datos;
                            
                            $nom = "-file-".$datoPlantilla->name;
                            
                            if($datoPlantilla->tipo_dato == 'image' || $datoPlantilla->tipo_dato == 'file'){
            
                                if(!empty($_FILES[$nom]['tmp_name'][$i])){
                                    $datoPlantilla->valor4_base64 = base64_encode(file_get_contents($_FILES[$nom]['tmp_name'][$i]));
                                }else{
                                    $datoPlantilla->valor4_base64 = NULL;
                                }
                                
                            }else{
                                $datoPlantilla->valor4_base64 = NULL;
                            }

                            array_push($array, $datoPlantilla);
                            unset($datoPlantilla);
                        }

                        unset($o);
                    }else{
                        $o->valor = NULL;
                        $o->valor4_base64 = NULL;
                        array_push($array, $o);
                    }
                }
            } else {
                if(!empty($_FILES[$nom]['tmp_name'])){
                    $o->valor4_base64 = base64_encode(file_get_contents($_FILES[$nom]['tmp_name']));
                }else{
                    $o->valor4_base64 = NULL;
                }
                $o->valor = !empty($data[empresa()."-".$o->valo_id])? $data[empresa()."-".$o->valo_id] : '';
                unset($o->values);
                array_push($aux, $o);
            }
            unset($o);
        }
        $this->db->save_queries = FALSE;// Para que no cachee la query

        if($aux && !$this->db->insert_batch('frm.instancias_formularios', $aux)) return FALSE;
        if($array && !$this->db->insert_batch('frm.instancias_formularios', $array)) return FALSE;
        
        $newInfo = $this->db->select_max('info_id')->get('frm.instancias_formularios')->row('info_id');

        $this->instanciarVariables($form_id, $newInfo);

        log_message('DEBUG',"#TRAZA | #TRAZ-COMP-FORMULARIOS | #FORMS | guardar() >> info_id generado ". $newInfo);

        return $newInfo;
    }
    /**
        * Actualiza la instacia del formulario dinámico enviada por parámetro
        * NOTA: divide la cadena por el caracter '-' ya que los valores pueden venir anidados con la empresa
        * @param array datos de formulario
        * @return $info_id
	*/
    public function actualizar($info_id, $data){
        foreach ($data as $key => $o) {
            if(!$key) continue;
            $this->db->where('info_id', $info_id);
            if(!strpos($key,'-')){
                $this->db->where('name', $key);
            }else{
                $aux = explode('-',$key);
                $this->db->where('name', array_pop($aux));
            }
            $this->db->set('valor', $o);
            if(!empty($_FILES["-file-".$key]['tmp_name'])){
                $valor4_base64 = base64_encode(file_get_contents($_FILES["-file-".$key]['tmp_name']));
                $this->db->set('valor4_base64',$valor4_base64);
            }
            $this->db->update('frm.instancias_formularios');
        }
        log_message('DEBUG',"#TRAZA | #TRAZ-COMP-FORMULARIOS | #FORMS | actualizar() >> info_id actualizado: ". $info_id);
        return $info_id;
    }

    public function obtener($info_id)
    {
        $this->db->select('name, label,valor, requerido, valo_id, orden, A.inst_id, A.form_id, tipo_dato, C.nombre, A.valor4_base64, A.columna, A.multiple');
        $this->db->from('frm.instancias_formularios as A');
        $this->db->join('frm.formularios as C', 'C.form_id = A.form_id');
        $this->db->where('A.info_id', $info_id);
        $this->db->where('A.eliminado', false);
        $this->db->order_by('A.orden');

        $res = $this->db->get();

        $aux = new StdClass();
        $aux->info_id = $info_id;
        $aux->nombre = $res->row()->nombre;
        $aux->id = $info_id;
        $aux->items = $res->result();


        foreach ($aux->items as $key => $o) {

            if ($o->tipo_dato == 'radio' || $o->tipo_dato == 'check' || $o->tipo_dato == 'select') {
                $aux->items[$key]->values = $this->obtenerValores($o->valo_id);
            }
            if ($o->tipo_dato == 'service') {
                $aux->items[$key]->values = $this->obtenerValoresServicio($o->valo_id);
            }
        }

        return $aux;
    }

    public function obtenerPlantilla($id)
    {

        $this->db->select('name, label, requerido, valo_id, orden, A.form_id, tipo_dato, C.nombre, A.columna, A.multiple');
        $this->db->from('frm.items as A');
        $this->db->join('frm.formularios as C', 'C.form_id = A.form_id');
        $this->db->where('A.form_id', $id);
        $this->db->where('A.eliminado', false);
        $this->db->order_by('A.orden');

        $res = $this->db->get();

        $newInfo = $this->db->select_max('info_id')->get('frm.instancias_formularios')->row('info_id') + 1;
        
        $aux = new StdClass();
        $aux->info_id = false;
        $aux->form_id = $id;
        $aux->nombre = $res->row()->nombre;
        $aux->id = $newInfo; 
        $aux->items = $res->result();

        foreach ($aux->items as $key => $o) {

            if ($o->tipo_dato == 'radio' || $o->tipo_dato == 'check' || $o->tipo_dato == 'select') {
                $aux->items[$key]->values = $this->obtenerValores($o->valo_id);
            }
            if ($o->tipo_dato == 'service') {
                $aux->items[$key]->values = $this->obtenerValoresServicio($o->valo_id);
            }
        }

        return $aux;
    }

    /**
        * Obtengo los valores almacenados en core.tablas para cargar las listas de valores
        * @param id nombre columna tabla
        * @return array valores coincidentes
	*/
    public function obtenerValores($id){
        $this->db->select('tabl_id as value, descripcion as label,valor,eliminado,tabla');
        return $this->db->get_where('core.tablas', array('tabla' => empresa()."-".$id, 'eliminado' => 'false'))->result();
    }
    

    /**
        * Obtengo los valores provistos por un servicio REST
        * Se espera una respueta JSON, con un arreglo que contenga minimamente los campos label y value para cada elemento
        * @param id url
        * @return array valores coincidentes
	*/
    public function obtenerValoresServicio($id){
        try {
            //Obtengo la url
            $url = $id;
            $token = array('Authorization: Bearer '.TOKEN_API_MANAGER);
 
            //para delta
           /*  $headers = array(
                'Authorization: Bearer '.TOKEN_API_MANAGER,
                'ApiAuthorization: '.TOKEN_TANGO,
                'Company: '.COMPANY_TANGO
            );  
            $rsp = $this->REST->callAPI('GET', $url, null, $headers);
            */
                    
            /* Version para dataservice */
            $rsp = $this->REST->callAPI('GET', $url, null, $token);
           if (!$rsp['status']) {
    
                log_message('DEBUG', '#TRAZA | #FRM >> Error obteniendo valores de url '+ url );
                $aux = json_decode('[{"value":"","label":"Error al invocar el servicio","valor":"","eliminado":false,"tabla":""}]');
                return $aux;
            }

            //Limpio la respuesta de elementos raiz y me quedo con los elementos del arreglo. Si no tuviese arreglo, devolvera una lista vacia
            // Busca la posición de los caracteres "[" y "]"
             $start_pos = strpos($rsp['data'], '[');
            $end_pos = strpos($rsp['data'], ']');

            // Verifica si se encontraron los caracteres "[" y "]" en la cadena
            if ($start_pos !== false && $end_pos !== false) {
                // Extrae el texto entre los caracteres "[" y "]"
                $new_string = substr($rsp['data'], $start_pos , $end_pos - $start_pos +1);

            } else {
                // Si no se encuentran los caracteres "[" y "]" en la cadena, muestra un mensaje de error
                return null;
            }
            log_message('DEBUG', '#TRAZA DATOS RRUIZ '.$new_string);


            return json_decode($new_string); 

            /* version nueva para llamado a api delta IV (no se usa la porque api de delta es lenta) */
           /* if (!$rsp['status']) {
                log_message('DEBUG', '#TRAZA | #FRM >> Error obteniendo valores de url ' . $url );
                $aux = json_decode('[{"value":"","label":"Error al invocar el servicio","valor":"","eliminado":false,"tabla":""}]');
                return $aux;
            }
            
            // Decodifica el JSON completo
            $data = json_decode($rsp['data'], true);
            
            // Verifica que existan los datos esperados
            if (isset($data['resultData']['list']) && is_array($data['resultData']['list'])) {
                $list = $data['resultData']['list'];
                foreach ($list as &$item) {
                    $nro_pedido = isset($item['NRO_PEDIDO']) ? $item['NRO_PEDIDO'] : '';
                    // Elimina solo los guiones, mantiene los espacios
                    $nro_pedido_formateado = str_replace('-', '', $nro_pedido);
                    $razon_social = isset($item['RAZON_SOCIAL_CLIENTE']) ? trim($item['RAZON_SOCIAL_CLIENTE']) : '';
                    $item['label'] = $nro_pedido_formateado . ' - ' . $razon_social;
                    $item['value'] = $nro_pedido_formateado;
                }
                 log_message('ERROR', '#TRAZA DATOS RRUIZ '.$list);
                // Si necesitas devolverlo como objeto:
                return json_decode(json_encode($list));
            } else {
                // Si no hay datos, devuelve un array vacío o un error
                return [];
            }*/
        } catch (Exception $e) {
            log_message('ERROR', '#TRAZA | #FRM >> Error al invocar servicio '.$id);
            $aux = json_decode('[{"value":"","label":"Error al invocar el servicio","valor":"","eliminado":false,"tabla":""}]');
            return $aux;
         
        }
    }

    public function listado()
    {
        $this->db->select('B.nombre, A.form_id, A.info_id');
        $this->db->from('frm.instancias_formularios as A');
        $this->db->join('frm.formularios as B', 'B.form_id = A.form_id');
        $this->db->group_by('info_id, A.form_id, B.nombre');
        return $this->db->get()->result();
    }

    public function instanciarVariables($form_id, $info_id)
    {
        $this->db->select("name, variable, $info_id as info_id");
        $this->db->where('name is not null');
        $this->db->where('form_id', $form_id);
        $res = $this->db->get('frm.items')->result();
        
        foreach ($res as $o) {
            $this->db->where('info_id', $o->info_id);
            $this->db->where('name', $o->name);
            $this->db->set('variable', $o->variable);
            $this->db->update('frm.instancias_formularios');
        }
    }

    public function generarInstancia($form_id)
    {
        $res['info_id'] = $this->guardar($form_id);
        return $res;
    }

    public function validarVariable($info_id)
    {
        # code...
    }

    public function obtenerXEmpresa($nombre, $emprId)
    {
        $this->db->where('empr_id', $emprId);
        $this->db->where('nombre', $nombre);
        $res = $this->db->get('frm.formularios')->first_row();
        if($res){ 
            return $this->obtenerPlantilla($res->form_id);
        }
    }

    function listarFormularios()
    {
        log_message('DEBUG', 'Formularios/getFormularios');
        $resource = '/formularios/'.empresa();
        $url = REST_FRM . $resource;
        $array = $this->rest->callApi('GET', $url);
        return json_decode($array['data']);
    }
}
