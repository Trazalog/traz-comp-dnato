<?php
// Test directo de SMTP sin CodeIgniter
require_once 'application/config/constants.php';

echo "Probando conexión SMTP directa...\n";

$host = 'mauriper.ferozo.com';
$port = 465;
$username = 'register@trazalog.com';
$password = 'Xdv*mq35wW';

echo "Conectando a $host:$port...\n";

$context = stream_context_create([
    'ssl' => [
        'verify_peer' => false,
        'verify_peer_name' => false,
        'allow_self_signed' => true
    ]
]);

$socket = stream_socket_client("ssl://$host:$port", $errno, $errstr, 30, STREAM_CLIENT_CONNECT, $context);

if (!$socket) {
    echo "Error de conexión: $errstr ($errno)\n";
    exit(1);
}

echo "Conexión establecida!\n";

// Leer respuesta inicial
$response = fgets($socket);
echo "Servidor: $response";

// Enviar EHLO
fwrite($socket, "EHLO localhost\r\n");
$response = fgets($socket);
echo "EHLO: $response";

// Enviar AUTH LOGIN
fwrite($socket, "AUTH LOGIN\r\n");
$response = fgets($socket);
echo "AUTH: $response";

// Enviar usuario (base64)
$user_b64 = base64_encode($username);
fwrite($socket, "$user_b64\r\n");
$response = fgets($socket);
echo "Usuario: $response";

// Enviar contraseña (base64)
$pass_b64 = base64_encode($password);
fwrite($socket, "$pass_b64\r\n");
$response = fgets($socket);
echo "Contraseña: $response";

// Enviar MAIL FROM
fwrite($socket, "MAIL FROM: <register@trazalog.com>\r\n");
$response = fgets($socket);
echo "MAIL FROM: $response";

// Enviar RCPT TO
fwrite($socket, "RCPT TO: <rodolfo@rtools.ca>\r\n");
$response = fgets($socket);
echo "RCPT TO: $response";

// Enviar DATA
fwrite($socket, "DATA\r\n");
$response = fgets($socket);
echo "DATA: $response";

// Enviar mensaje
$message = "From: register@trazalog.com\r\n";
$message .= "To: rodolfo@rtools.ca\r\n";
$message .= "Subject: Test SMTP Directo\r\n";
$message .= "\r\n";
$message .= "Este es un test de email SMTP directo.\r\n";
$message .= ".\r\n";

fwrite($socket, $message);
$response = fgets($socket);
echo "Mensaje: $response";

// Enviar QUIT
fwrite($socket, "QUIT\r\n");
$response = fgets($socket);
echo "QUIT: $response";

fclose($socket);
echo "Test completado!\n";
?>
