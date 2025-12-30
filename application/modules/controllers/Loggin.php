<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Loggin extends CI_Controller {
    function __construct(){

      parent::__construct();
   }
   function index()
   {
      $this->load->view('loggin');
   }

   public function loggin()
   {
       echo json_encode(['status' => true]);
   }
}
?>