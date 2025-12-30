<?php
// Archivo de prueba para verificar que el módulo de formularios funciona
defined('BASEPATH') OR exit('No direct script access allowed');

class Test_formularios extends CI_Controller {
    
    public function index() {
        echo "<h1>Prueba del Módulo de Formularios</h1>";
        
        try {
            // Cargar el modelo directamente
            require_once(APPPATH . 'modules/traz-comp-formularios/application/models/Forms.php');
            $this->Forms = new Forms();
            
            // Cargar el helper directamente
            require_once(APPPATH . 'modules/traz-comp-formularios/application/helpers/form_helper.php');
            
            echo "<p>✅ Modelo Forms cargado correctamente</p>";
            echo "<p>✅ Helper form_helper cargado correctamente</p>";
            
            // Probar obtener plantilla del formulario
            $formulario = $this->Forms->obtenerPlantilla(1);
            
            if ($formulario) {
                echo "<p>✅ Formulario obtenido correctamente</p>";
                echo "<p>Nombre del formulario: " . $formulario->nombre . "</p>";
                echo "<p>Número de items: " . count($formulario->items) . "</p>";
                
                // Mostrar los items
                echo "<h3>Items del formulario:</h3>";
                echo "<ul>";
                foreach ($formulario->items as $item) {
                    echo "<li>" . $item->label . " (" . $item->tipo_dato . ")</li>";
                }
                echo "</ul>";
                
            } else {
                echo "<p>❌ Error al obtener el formulario</p>";
            }
            
        } catch (Exception $e) {
            echo "<p>❌ Error: " . $e->getMessage() . "</p>";
        }
    }
}
