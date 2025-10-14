<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Register extends CI_Controller {

	function __construct(){
		parent::__construct();
		$this->load->model('User_model', 'user_model', TRUE);
	}

	public function register_success()
	{
		$data['title'] = "Registro Exitoso";
		$this->load->view('header', $data);
		$this->load->view('container');
		$this->load->view('register_success');
		$this->load->view('footer');
	}
}


