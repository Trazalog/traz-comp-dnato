<?php defined('BASEPATH') or exit('No direct script access allowed');

class Test extends CI_Controller
{
    public function __construct()
    {

        parent::__construct();

    }

    public function index()
    {
        //   $config = array(
        //       'protocol' => 'smtp',
        //       'smtp_host' => 'smtp.gmail.com',
        //       'smtp_port' => 587,
        //       'smtp_user' => 'soportetrazalog24@gmail.com',
        //       'smtp_pass' => '123trazalog24',
        //      # 'smtp_timeout' => 90,
        //       'newline' => "\r\n",
        //       'crlf' => "\r\n",
        //       'mailtype' => 'html',
        //       'charset' => 'utf-8',
        //   );

        $config = array(
            'protocol' => 'smtp',
            'smtp_host' => 'ssl://smtp.gmail.com',
            'smtp_auth' => true,
            'smtp_port' => '465',
            'smtp_user' => 'soportetrazalog24@gmail.com',
            'smtp_pass' => '123trazalog24',
            'mailtype' => 'html',
            'newline' => "\r\n",
            'crlf' => "\r\n",
            'charset' => ' utf-8',
        );

        $this->load->library('email', $config);
        $this->load->library('sendmail');

        $message = 'Mail Test'; #$this->sendmail->sendRegister($this->input->post('lastname'), $this->input->post('email'), $link, $sTl);
        $to_email = 'fleiva@trazalog.com'; #$this->input->post('email');
        $this->email->from($this->config->item('register'), 'Set Password Fernando Leiva'); //from sender, title email
        $this->email->to($to_email);
        $this->email->subject('Set Password Login');
        $this->email->message($message);
      

        //Sending mail
        if ($this->email->send()) {
            echo 'Mail Enviado';

        } else {
            $this->session->set_flashdata('flash_message', 'There was a problem sending an email.');
            echo $this->email->print_debugger();

            echo 'Me Fayol';
            exit;
        }

    }

}
