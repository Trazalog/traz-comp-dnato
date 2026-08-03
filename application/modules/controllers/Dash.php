<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Dash extends CI_Controller {
    function __construct(){

      parent::__construct();
      $this->load->helper('menu_helper');
      $this->load->helper('file');
   }
   function index(){
     
      $data['menu'] = null;//menu(file_get_contents("C:/xampp/htdocs/bpm/menu.json"));

      $data['link'] = 'Form/obtenerTodos';
    
      $this->load->view('layout/Admin',$data);
   }
}
?>