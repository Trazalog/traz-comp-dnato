<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mmenu extends CI_Model {


    function __construct(){
        // Call the Model constructor
        parent::__construct();        
        $this->status = $this->config->item('status');
        $this->roles = $this->config->item('roles');
        $this->banned_users = $this->config->item('banned_users');
    }   


    
    function getModulos(){

        
        $this->db->select("modulo");
        $this->db->distinct();
        $this->db->from('seg.menues');
        $this->db->order_by("modulo", "asc");
        $query = $this->db->get();

        return $query->result();
    }

    function getIconos(){

        $this->db->select("icono");
        $this->db->from('core.iconos');
        $this->db->order_by("icono", "asc");
        $query = $this->db->get();

        return $query->result();
    }

    function getOpcionPadre(){

        
        $this->db->select("opcion,modulo,texto");
        //$this->db->distinct();
        $this->db->from('seg.menues');
        $this->db->order_by("opcion", "asc");
        $query = $this->db->get();

        return $query->result();
    }

    function getMenues(){

        $this->db->select("*");
        $this->db->from('seg.menues');
        $this->db->order_by("texto", "asc");
        $query = $this->db->get();
        $num_results = $this->db->count_all_results();

        if($query->result()){
            $result = array (
                'datos' => $query->result(),
                'totalDatos' => $num_results
            );
            return $result;
        }
        else
            return false;
    }

    function updateMembershipsMenues($dataPost){

        $string = array(

            'opcion'=>$dataPost['opcion'],
            'modulo'=>$dataPost['modulo'],
            'fec_alta'=>date("Y-m-d H:i:s"), 	
            'usuario'=>'postgres', 							
            'usuario_app'=>'postgres'
        );

        $this->db->where('group', $dataPost['groups']);
        $this->db->where('role', $dataPost['roles']);
        $this->db->update('seg.memberships_menues', $string);
        $success = $this->db->affected_rows(); 

        log_message('DEBUG','#TRAZA|Mmenu|updateMembershipsMenues()  $this->db->affected_rows(): >> '.$this->db->affected_rows());

        if(!$success){
            return false;
        }else       
            return true;
    }

    function updateMenues($dataPost){

        $string = array(

            'modulo'=>$dataPost['modulo'],
            'opcion'=>$dataPost['opcion'],
            'texto'=>$dataPost['texto'],							
            'opcion_padre'=>$dataPost['opcion_padre'],				
            'orden'=>$dataPost['orden'],									
            'url'=>$dataPost['url'],
            'url_icono'=>$dataPost['url_icono'], 			
            'texto_onmouseover'=>$dataPost['texto_onmouseover'], 							
            'eliminado'=>'0', 							
            'javascript'=>'', 							
            'usuario'=>'postgres', 							
            'usuario_app'=>'postgres', 							
            'fec_alta'=>date("Y-m-d H:i:s"), 							
            
        );
     
        $this->db->where('modulo', $dataPost['modulo']);
        $this->db->where('opcion', $dataPost['opcion']);
        $this->db->update('seg.menues', $string);
        $success = $this->db->affected_rows(); 

        log_message('DEBUG','#TRAZA|Mmenu|updateMenues()  $this->db->affected_rows(): >> '.$this->db->affected_rows());
            
        if(!$success){
            return false;
        }else       
            return true;

    }

    function addMenuRoles($dataPost){

        $string = array(

            'group'=>$dataPost['groups'],
            'modulo'=>$dataPost['modulo'],
            'opcion'=>$dataPost['opcion'],
            'role'=>$dataPost['roles'],
            'fec_alta'=>date("Y-m-d H:i:s"), 	
            'usuario'=>'postgres', 							
            'usuario_app'=>'postgres'
        );

        $q = $this->db->insert('seg.memberships_menues',$string);

        log_message('DEBUG','#TRAZA|Mmenu|addMenuRoles()  $string >> '.$string);
        log_message('DEBUG','#TRAZA|Mmenu|addMenuRoles()  $this->db->affected_rows(): >> '.$this->db->affected_rows());

        if ($this->db->affected_rows() != 1){
            return FALSE;
        }else{
            return TRUE;
        }

    }

    function addMenues($dataPost){

        $string = array(

            'modulo'=>$dataPost['modulo'],
            'opcion'=>$dataPost['opcion'],
            'texto'=>$dataPost['texto'],							
            'opcion_padre'=>$dataPost['opcion_padre'],				
            'orden'=>$dataPost['orden'],									
            'url'=>$dataPost['url'],
            'url_icono'=>$dataPost['url_icono'], 			
            'texto_onmouseover'=>$dataPost['texto_onmouseover'], 							
            'eliminado'=>'0', 							
            'javascript'=>'', 							
            'usuario'=>'postgres', 							
            'usuario_app'=>'postgres', 							
            'fec_alta'=>date("Y-m-d H:i:s"), 							
            
        );

        $q = $this->db->insert('seg.menues',$string);

        log_message('DEBUG','#TRAZA|Mmenu|addMenus()  $string >> '.$string);
        log_message('DEBUG','#TRAZA|Mmenu|addMenus()  $this->db->affected_rows(): >> '.$this->db->affected_rows());

        if ($this->db->affected_rows() != 1){
            return FALSE;
        }else{
            return TRUE;
        }
    }

    function activeMenu($dataPost){

        $this->db->where('modulo', $dataPost['modulo']);
        $this->db->where('opcion', $dataPost['opcion']);
        $this->db->update('seg.menues', array(  'eliminado' => '0' ));

        $success = $this->db->affected_rows(); 
        if($success){
            return TRUE;
        }
        else {
            return FALSE;
        }
    }
    
    function activeMenuRole($dataPost){

        $this->db->where('group', $dataPost['group']);
        $this->db->where('modulo', $dataPost['modulo']);
        $this->db->where('opcion', $dataPost['opcion']);
        $this->db->where('role', $dataPost['role']);
        $this->db->update('seg.memberships_menues', array(  'eliminado' => '0' ));

        $success = $this->db->affected_rows(); 
        if($success){
            return TRUE;
        }
        else {
            return FALSE;
        }
    }
    
    function deleteMenu($dataPost){

        $this->db->select("*");
        $this->db->where('modulo', $dataPost['modulo']);
        $this->db->where('opcion', $dataPost['opcion']);
        $this->db->from('seg.memberships_menues');        
        $query = $this->db->get();

        log_message('DEBUG','#TRAZA|Menu|deleteMenu()  $dataPost: >> '.$query->result());
        log_message('DEBUG','#TRAZA|Menu|deleteMenu()  $dataPost: >> '.count($query->result()));

        if($query->result()){
            return -1; /* Existe*/
        }else{
        
            $this->db->where('modulo', $dataPost['modulo']);
            $this->db->where('opcion', $dataPost['opcion']);
            $this->db->update('seg.menues', array(  'eliminado' => '1' ));

            $success = $this->db->affected_rows(); 
            if($success){
                return TRUE;
            }else {
                return FALSE;
            }
        }        
    }
    
    function deleteMenuRole($dataPost){

        $this->db->where('group', $dataPost['group']);
        $this->db->where('modulo', $dataPost['modulo']);
        $this->db->where('opcion', $dataPost['opcion']);
        $this->db->where('role', $dataPost['role']);
        $this->db->update('seg.memberships_menues', array(  'eliminado' => '1' ));

        $success = $this->db->affected_rows(); 
        if($success){
            return TRUE;
        }
        else {
            return FALSE;
        }
    }
    
    function menuesPaginados($start,$length,$search){


        $this->db->select("*");
        $this->db->from('seg.memberships_menues');
        $this->db->limit($length, $start);
        $query = $this->db->get();
        
        $cant = $query->num_rows();

        $this->db->select("*");
        $this->db->from('seg.memberships_menues');
        $query = $this->db->get();
        $num_results = $this->db->count_all_results();

        if($query->result()){
            $result = array (
                'numDataTotal' => $cant,
                'datos' => $query->result(),
                'totalDatos' => $num_results
            );
            return $result;
        }
        else
            return false;

    }

    function menucountPaginados(){

    } 

    function getMenuesRoles(){

        /*$this->db->select("seg.users.email,seg.memberships_menues.group,seg.users.role,seg.memberships_menues.modulo,
                           seg.memberships_menues.opcion,seg.memberships_menues.role as rolemm");
        $this->db->from('seg.users');
        $this->db->join('seg.memberships_users', 'seg.memberships_users.email = seg.users.email');
        $this->db->join('seg.memberships_menues', 'seg.memberships_menues.group = seg.memberships_users.group 
                                                   and seg.memberships_menues.role = seg.memberships_users.role');*/

        /*$this->db->join('seg.memberships_menues', 'seg.memberships_menues.group = seg.memberships_users.group 
                                                   and seg.memberships_menues.role = seg.memberships_users.role
                                                   and seg.memberships_menues.usuario = seg.memberships_users.usuario 
                                                   and seg.memberships_menues.usuario_app = seg.memberships_users.usuario_app');*/

        $this->db->select('*');                                                   
        $this->db->from('seg.memberships_menues');
        $this->db->order_by("modulo", "asc");
        $query = $this->db->get();
        $num_results = $this->db->count_all_results();

        if($query->result()){
            $result = array (
                'datos' => $query->result(),
                'totalDatos' => $num_results
            );
            return $result;
        }
        else
            return false;
    }

}