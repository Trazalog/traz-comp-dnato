<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}

class Roles extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }
    
    public function obtener($id = false)
    {
        $this->db->where('eliminado', false);
        $res = $this->db->get('roles')->result();
        
        $list = [];
        foreach ($res as $o) {
            $list[$o->rol_id] = $o->nombre;
        }

        return $list;
    }

    public function guardar($data)
    {
        return $this->db->insert('roles', $data);
    }
    
}