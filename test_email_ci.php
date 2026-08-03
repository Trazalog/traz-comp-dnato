<?php
// Prueba de envío de email usando la librería Email de CodeIgniter (config global)

define('ENVIRONMENT', 'development');
$_SERVER['CI_ENV'] = 'development';

require_once __DIR__ . '/index.php';

$CI =& get_instance();
$CI->load->library('email');

$to = 'rodolfo@rtools.ca';
$CI->email->from('register@trazalog.com', 'Trazalog Tools (Test)');
$CI->email->to($to);
$CI->email->subject('Prueba SMTP CI (ssl://mauriper.ferozo.com:465)');
$CI->email->message('Este es un correo de prueba enviado con la configuración global de CodeIgniter.');

if ($CI->email->send()) {
    echo "OK: Email enviado a $to\n";
} else {
    echo "ERROR al enviar email\n";
    echo $CI->email->print_debugger();
}


