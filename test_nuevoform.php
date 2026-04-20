<?php
// Script de prueba directo para simular el controlador Register
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "=== PRUEBA DIRECTA DEL CONTROLADOR REGISTER ===\n";

// Simular el entorno de CodeIgniter
define('BASEPATH', TRUE);
define('FCPATH', __DIR__ . '/');
define('APPPATH', __DIR__ . '/application/');

// Cargar constants
require_once(APPPATH . 'config/constants.php');
echo "FRM: " . FRM . "\n";
echo "FORMULARIO_REGISTRO_ID: " . FORMULARIO_REGISTRO_ID . "\n";

// Cargar el helper
$helper_path = APPPATH . 'modules/traz-comp-formularios/helpers/form_helper.php';
echo "Cargando helper: " . $helper_path . "\n";
require_once($helper_path);

// Verificar si la función nuevoForm existe
if (function_exists('nuevoForm')) {
    echo "Función nuevoForm() disponible\n";
    
    // Intentar llamar a la función directamente
    echo "Intentando llamar a nuevoForm(" . FORMULARIO_REGISTRO_ID . ")...\n";
    try {
        $resultado = nuevoForm(FORMULARIO_REGISTRO_ID);
        echo "Resultado: " . $resultado . "\n";
    } catch (Exception $e) {
        echo "ERROR llamando a nuevoForm(): " . $e->getMessage() . "\n";
        echo "Trace: " . $e->getTraceAsString() . "\n";
    }
} else {
    echo "ERROR: Función nuevoForm() NO disponible\n";
}

echo "\n=== FIN DE PRUEBA ===\n";
?>


