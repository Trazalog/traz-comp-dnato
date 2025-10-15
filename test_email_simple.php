<?php
// Test simple de email SMTP
$config = array(
    'protocol' => 'smtp',
    'smtp_host' => 'mauriper.ferozo.com',
    'smtp_port' => 465,
    'smtp_user' => 'register@trazalog.com',
    'smtp_pass' => 'Xdv*mq35wW',
    'smtp_crypto' => 'ssl',
    'mailtype' => 'html',
    'charset' => 'utf-8',
    'smtp_timeout' => 30,
    'newline' => "\r\n",
    'crlf' => "\r\n",
);

// Cargar CodeIgniter
require_once 'index.php';

$CI =& get_instance();
$CI->load->library('email', $config);

$CI->email->from('register@trazalog.com', 'Trazalog Tools');
$CI->email->to('rodolfo@rtools.ca');
$CI->email->subject('Test Email SMTP');
$CI->email->message('Este es un test de email SMTP directo.');

if ($CI->email->send()) {
    echo "Email enviado correctamente!\n";
} else {
    echo "Error al enviar email:\n";
    echo $CI->email->print_debugger();
}
?>
