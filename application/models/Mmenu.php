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
    
    function deleteMenu($dataPost){

        $this->db->where('modulo', $dataPost['modulo']);
        $this->db->where('opcion', $dataPost['opcion']);
        $this->db->update('seg.menues', array(  'eliminado' => '1' ));

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

        $this->db->select("seg.users.email,seg.memberships_menues.group,seg.users.role,seg.memberships_menues.modulo,
                           seg.memberships_menues.opcion,seg.memberships_menues.role as rolemm");
        $this->db->from('seg.users');
        $this->db->join('seg.memberships_users', 'seg.memberships_users.email = seg.users.email');
        $this->db->join('seg.memberships_menues', 'seg.memberships_menues.group = seg.memberships_users.group 
                                                   and seg.memberships_menues.role = seg.memberships_users.role');

        /*$this->db->join('seg.memberships_menues', 'seg.memberships_menues.group = seg.memberships_users.group 
                                                   and seg.memberships_menues.role = seg.memberships_users.role
                                                   and seg.memberships_menues.usuario = seg.memberships_users.usuario 
                                                   and seg.memberships_menues.usuario_app = seg.memberships_users.usuario_app');*/
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