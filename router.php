<?php
// Router para servidor embebido PHP: reescribe peticiones a index.php (CodeIgniter)
$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
if ($uri !== '/' && file_exists(__DIR__ . $uri)) {
    return false; // servir estático
}
$_SERVER['CI_ENV'] = 'development';
require_once __DIR__ . '/index.php';
