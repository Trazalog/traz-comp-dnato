<?php
// Script standalone para probar SMTP SSL (465) con AUTH LOGIN
// No depende de CodeIgniter

error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = 'mauriper.ferozo.com';
$port = 465; // SSL
$username = 'register@trazalog.com';
$password = 'Xdv*mq35wW';
$to = 'rodolfo@rtools.ca';

function read_line($socket)
{
    $line = '';
    while ($s = fgets($socket, 515)) {
        $line .= $s;
        // Respuestas SMTP terminan cuando el código no va seguido de '-'
        if (preg_match('/^\d{3} /', $s)) break;
        if (!preg_match('/^\d{3}-/', $s)) break;
    }
    return $line;
}

function expect_ok($resp, $expectedPrefix)
{
    if (strpos($resp, $expectedPrefix) !== 0) {
        throw new RuntimeException("Respuesta inesperada: $resp");
    }
}

echo "Conectando a ssl://$host:$port...\n";
$context = stream_context_create([
    'ssl' => [
        // Dejar verificación activada; si falla, sabremos que el cert no es válido para el host
        'verify_peer' => true,
        'verify_peer_name' => true,
        'allow_self_signed' => false,
        'SNI_enabled' => true,
        'peer_name' => $host,
    ]
]);

$socket = @stream_socket_client("ssl://$host:$port", $errno, $errstr, 30, STREAM_CLIENT_CONNECT, $context);
if (!$socket) {
    echo "ERROR de conexión TLS: $errstr ($errno)\n";
    echo "Sugerencia: probar 587/TLS o usar el CN del certificado del proveedor (p.ej. smtp.ferozo.com).\n";
    exit(1);
}

stream_set_timeout($socket, 30);

$resp = read_line($socket); echo "S: $resp";
expect_ok($resp, '220');

fwrite($socket, "EHLO localhost\r\n");
$resp = read_line($socket); echo "S: $resp";
expect_ok($resp, '250');

fwrite($socket, "AUTH LOGIN\r\n");
$resp = read_line($socket); echo "S: $resp";
expect_ok($resp, '334');

fwrite($socket, base64_encode($username) . "\r\n");
$resp = read_line($socket); echo "S: $resp";
expect_ok($resp, '334');

fwrite($socket, base64_encode($password) . "\r\n");
$resp = read_line($socket); echo "S: $resp";
expect_ok($resp, '235');

fwrite($socket, "MAIL FROM:<$username>\r\n");
$resp = read_line($socket); echo "S: $resp";
expect_ok($resp, '250');

fwrite($socket, "RCPT TO:<$to>\r\n");
$resp = read_line($socket); echo "S: $resp";
expect_ok($resp, '250');

fwrite($socket, "DATA\r\n");
$resp = read_line($socket); echo "S: $resp";
expect_ok($resp, '354');

$message = '';
$message .= "From: $username\r\n";
$message .= "To: $to\r\n";
$message .= "Subject: Prueba SMTP directa (SSL 465)\r\n";
$message .= "MIME-Version: 1.0\r\n";
$message .= "Content-Type: text/plain; charset=UTF-8\r\n";
$message .= "\r\n";
$message .= "Este es un test de SMTP directo contra $host:$port.\r\n";
$message .= ".\r\n"; // fin DATA

fwrite($socket, $message);
$resp = read_line($socket); echo "S: $resp";
expect_ok($resp, '250');

fwrite($socket, "QUIT\r\n");
$resp = read_line($socket); echo "S: $resp";

fclose($socket);
echo "OK: correo enviado (si el servidor lo aceptó para entrega).\n";


