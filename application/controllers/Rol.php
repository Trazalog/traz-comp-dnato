<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Rol extends CI_Controller {
    function __construct(){

      parent::__construct();
      $this->load->model('Roles'); 
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
?>