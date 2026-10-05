<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}

class Tablas extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function obtenerTabla($tabla)
    {
        $url = REST_CORE . "/tablas/$tabla";
        $response = $this->rest->callAPI("GET", $url);
        $data = json_decode($response["data"], true);

        return $data['tablas']['tabla'];

    }

    public function obtener($id = false)
    {
        if ($id) {
            $this->db->where('tabla', $id);
        }
        $this->db->where('eliminado',false);
        return $this->db->get('alm.utl_tablas')->result();
    }
}
