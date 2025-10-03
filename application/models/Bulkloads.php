<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Modelo para operaciones de carga masiva
 * 
 * Maneja todas las operaciones relacionadas con:
 * - Obtención de entidades de negocio desde WSO2 DataService
 * - Envío de archivos al DataService para procesamiento
 * - Obtención de información de empresa desde sesión
 */
class Bulkloads extends CI_Model {
    
    public function __construct() {
        parent::__construct();
        $this->load->library('curl');
        log_message('info', 'Bulkloads model initialized');
    }

    /**
     * Obtiene las entidades de negocio desde WSO2 DataService
     * 
     * @return array|false Array de entidades o false si hay error
     */
    public function obtenerEntidadesNegocio() {
        try {
            log_message('info', '=== INICIANDO obtenerEntidadesNegocio ===');
            
            // Construir URL del DataService
            $url = COREDataService_URL . '/entidades_negocio';
            log_message('debug', 'WSO2 DataService URL: ' . $url);
            
            // Usar cURL nativo para mejor control
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, BULKLOAD_TIMEOUT);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Accept: application/json',
                'Content-Type: application/json'
            ));
            
            // Ejecutar petición
            $response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curl_error = curl_error($ch);
            $curl_errno = curl_errno($ch);
            curl_close($ch);
            
            log_message('debug', 'HTTP Response Code: ' . $http_code);
            log_message('debug', 'Response: ' . $response);
            
            if ($curl_errno !== 0) {
                log_message('error', 'cURL Error: ' . $curl_error);
                log_message('error', 'cURL Error Code: ' . $curl_errno);
                return false;
            }
            
            if ($http_code !== 200) {
                log_message('error', 'HTTP Error: ' . $http_code);
                log_message('error', 'HTTP Response Body: ' . $response);
                return false;
            }
            
            // Parsear respuesta JSON
            $data = json_decode($response, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                log_message('error', 'JSON Parse Error: ' . json_last_error_msg());
                // Intentar conversión XML a JSON como fallback
                $data = $this->convertirXmlAJson($response);
                if ($data === false) {
                    return false;
                }
            }
            
            // Verificar estructura de respuesta
            if (!isset($data['response']['entidades'])) {
                log_message('error', 'Invalid response structure from WSO2');
                return false;
            }
            
            $entidades = $data['response']['entidades'];
            log_message('info', 'Entidades obtenidas: ' . count($entidades));
            log_message('debug', 'Entidades: ' . json_encode($entidades));
            
            return $entidades;
            
        } catch (Exception $e) {
            log_message('error', 'Exception in obtenerEntidadesNegocio: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Envía el archivo CSV al DataService de WSO2 para procesamiento
     * 
     * @param string $csv_filepath Ruta del archivo CSV
     * @param string $stored_procedure Nombre del stored procedure
     * @param string $empr_id ID de la empresa
     * @return array|false Resultado del procesamiento o false si hay error
     */
    public function enviarADataservice($csv_filepath, $stored_procedure, $empr_id) {
        try {
            log_message('info', '=== INICIANDO enviarADataservice ===');
            log_message('debug', 'CSV file: ' . $csv_filepath);
            log_message('debug', 'Stored procedure: ' . $stored_procedure);
            log_message('debug', 'Empresa ID: ' . $empr_id);
            
            // Construir URL del DataService
            $url = COREDataService_URL . '/carga_masiva/archivo';
            log_message('debug', 'WSO2 DataService URL: ' . $url);
            
            // Preparar payload con formato correcto para WSO2
            $payload = array(
                '_post_carga_masiva_archivo' => array(
                    'url_archivo' => $csv_filepath,
                    'stored_procedure' => $stored_procedure,
                    'empr_id' => strval($empr_id) // WSO2 espera string pero se castea a integer en la query
                )
            );
            
            $json_payload = json_encode($payload);
            log_message('debug', 'Payload: ' . $json_payload);
            
            // Usar cURL nativo para mejor control
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, BULKLOAD_TIMEOUT);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $json_payload);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Accept: application/json',
                'Content-Type: application/json',
                'Content-Length: ' . strlen($json_payload)
            ));
            
            // Ejecutar petición
            $response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curl_error = curl_error($ch);
            $curl_errno = curl_errno($ch);
            curl_close($ch);
            
            log_message('debug', 'HTTP Response Code: ' . $http_code);
            log_message('debug', 'Response: ' . $response);
            
            if ($curl_errno !== 0) {
                log_message('error', 'cURL Error: ' . $curl_error);
                log_message('error', 'cURL Error Code: ' . $curl_errno);
                return false;
            }
            
            if ($http_code !== 200) {
                log_message('error', 'HTTP Error: ' . $http_code);
                log_message('error', 'HTTP Response Body: ' . $response);
                return false;
            }
            
            // Parsear respuesta JSON
            $data = json_decode($response, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                log_message('error', 'JSON Parse Error: ' . json_last_error_msg());
                // Intentar conversión XML a JSON como fallback
                $data = $this->convertirXmlAJson($response);
                if ($data === false) {
                    return false;
                }
            }
            
            // Verificar estructura de respuesta
            if (!isset($data['response']['resultado'])) {
                log_message('error', 'Invalid response structure from WSO2');
                return false;
            }
            
            $resultado = $data['response']['resultado'][0]; // Primer elemento del array
            log_message('info', 'Procesamiento completado');
            log_message('debug', 'Resultado: ' . json_encode($resultado));
            
            // La nueva estructura retorna un string con toda la información
            $output = isset($resultado['output']) ? $resultado['output'] : '';
            log_message('debug', 'Output completo: ' . $output);
            
            return array(
                'success' => strpos($output, 'SUCCESS:') === 0,
                'output' => $output,
                'raw_response' => $resultado
            );
            
        } catch (Exception $e) {
            log_message('error', 'Exception in enviarADataservice: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtiene el ID de empresa de la sesión del usuario
     * 
     * @return string|false ID de empresa o false si hay error
     */
    public function obtenerEmpresaId() {
        try {
            log_message('info', '=== INICIANDO obtenerEmpresaId ===');
            
            // Obtener empr_id directamente de la sesión (se guarda durante el login)
            $empr_id = $this->session->userdata('empr_id');
            
            if (empty($empr_id)) {
                log_message('error', 'No empr_id found in session');
                log_message('debug', 'Available session data: ' . json_encode($this->session->userdata()));
                return false;
            }
            
            log_message('debug', 'empr_id found in session: ' . $empr_id);
            return $empr_id;
            
        } catch (Exception $e) {
            log_message('error', 'Exception in obtenerEmpresaId: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Convierte respuesta XML a JSON (fallback)
     * 
     * @param string $xml_response Respuesta XML del DataService
     * @return array|false Array JSON o false si hay error
     */
    private function convertirXmlAJson($xml_response) {
        try {
            log_message('info', 'Converting XML response to JSON');
            
            $xml = simplexml_load_string($xml_response);
            if ($xml === false) {
                log_message('error', 'Failed to parse XML response');
                return false;
            }
            
            // Convertir XML a array
            $json = json_encode($xml);
            $data = json_decode($json, true);
            
            log_message('debug', 'XML converted to JSON: ' . json_encode($data));
            return $data;
            
        } catch (Exception $e) {
            log_message('error', 'Exception in convertirXmlAJson: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtiene el resultado de la carga masiva desde la tabla temporal (GET)
     * 
     * @return array|false Resultado del procesamiento o false si hay error
     */
    public function obtenerResultadoCargaMasiva() {
        try {
            log_message('info', '=== INICIANDO obtenerResultadoCargaMasiva (GET) ===');
            
            // Construir URL del DataService
            $url = COREDataService_URL . '/carga_masiva/resultado';
            log_message('debug', 'WSO2 DataService URL: ' . $url);
            
            // Usar cURL nativo para mejor control
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, BULKLOAD_TIMEOUT);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Accept: application/json',
                'Content-Type: application/json'
            ));
            
            // Ejecutar petición
            $response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curl_error = curl_error($ch);
            $curl_errno = curl_errno($ch);
            curl_close($ch);
            
            log_message('debug', 'HTTP Response Code: ' . $http_code);
            log_message('debug', 'Response: ' . $response);
            
            if ($curl_errno !== 0) {
                log_message('error', 'cURL Error: ' . $curl_error);
                log_message('error', 'cURL Error Code: ' . $curl_errno);
                return false;
            }
            
            if ($http_code !== 200) {
                log_message('error', 'HTTP Error: ' . $http_code);
                log_message('error', 'HTTP Response Body: ' . $response);
                return false;
            }
            
            // Parsear respuesta JSON
            $data = json_decode($response, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                log_message('error', 'JSON Parse Error: ' . json_last_error_msg());
                // Intentar conversión XML a JSON como fallback
                $data = $this->convertirXmlAJson($response);
                if ($data === false) {
                    return false;
                }
            }
            
            // Verificar estructura de respuesta
            if (!isset($data['response']['resultado'])) {
                log_message('error', 'Invalid response structure from WSO2');
                return false;
            }
            
            $resultado = $data['response']['resultado'][0]; // Primer elemento del array
            log_message('info', 'Resultado obtenido exitosamente');
            log_message('debug', 'Resultado: ' . json_encode($resultado));
            
            // Extraer información del resultado
            $output = isset($resultado['output']) ? $resultado['output'] : '';
            $total_messages = isset($resultado['total_messages']) ? $resultado['total_messages'] : 0;
            $last_message_time = isset($resultado['last_message_time']) ? $resultado['last_message_time'] : '';
            
            log_message('debug', 'Output completo: ' . $output);
            log_message('debug', 'Total mensajes: ' . $total_messages);
            log_message('debug', 'Último mensaje: ' . $last_message_time);
            
            return array(
                'success' => strpos($output, 'SUCCESS:') === 0,
                'output' => $output,
                'total_messages' => $total_messages,
                'last_message_time' => $last_message_time,
                'raw_response' => $resultado
            );
            
        } catch (Exception $e) {
            log_message('error', 'Exception in obtenerResultadoCargaMasiva: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Limpia los logs temporales (opcional)
     * 
     * @return array|false Resultado de la limpieza o false si hay error
     */
    public function limpiarLogsTemporales() {
        try {
            log_message('info', '=== INICIANDO limpiarLogsTemporales ===');
            
            // Construir URL del DataService
            $url = COREDataService_URL . '/carga_masiva/limpiar';
            log_message('debug', 'WSO2 DataService URL: ' . $url);
            
            // Usar cURL nativo para mejor control
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, BULKLOAD_TIMEOUT);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Accept: application/json',
                'Content-Type: application/json'
            ));
            
            // Ejecutar petición
            $response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curl_error = curl_error($ch);
            $curl_errno = curl_errno($ch);
            curl_close($ch);
            
            log_message('debug', 'HTTP Response Code: ' . $http_code);
            log_message('debug', 'Response: ' . $response);
            
            if ($curl_errno !== 0) {
                log_message('error', 'cURL Error: ' . $curl_error);
                log_message('error', 'cURL Error Code: ' . $curl_errno);
                return false;
            }
            
            if ($http_code !== 200) {
                log_message('error', 'HTTP Error: ' . $http_code);
                log_message('error', 'HTTP Response Body: ' . $response);
                return false;
            }
            
            // Parsear respuesta JSON
            $data = json_decode($response, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                log_message('error', 'JSON Parse Error: ' . json_last_error_msg());
                return false;
            }
            
            log_message('info', 'Logs temporales limpiados exitosamente');
            log_message('debug', 'Response: ' . json_encode($data));
            
            return array(
                'success' => true,
                'status' => $data['response']['status'],
                'message' => 'Logs temporales limpiados correctamente'
            );
            
        } catch (Exception $e) {
            log_message('error', 'Exception in limpiarLogsTemporales: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtiene información de empresa por usuario (método legacy - no usar)
     * 
     * @deprecated Usar obtenerEmpresaId() en su lugar
     * @param string $email Email del usuario
     * @return array|false Información de empresa o false si hay error
     */
    public function obtenerEmpresaPorUsuario($email) {
        try {
            log_message('info', 'Getting company info for user: ' . $email);
            
            // Este método ya no se usa, se reemplazó por obtenerEmpresaId()
            log_message('warning', 'obtenerEmpresaPorUsuario is deprecated, use obtenerEmpresaId() instead');
            return false;
            
        } catch (Exception $e) {
            log_message('error', 'Exception in obtenerEmpresaPorUsuario: ' . $e->getMessage());
            return false;
        }
    }
}
