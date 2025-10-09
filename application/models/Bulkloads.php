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
            log_message('info', 'Cargando librería REST...');
            
            // Cargar librería REST
            $this->load->library('REST');
            log_message('info', 'Librería REST cargada exitosamente');
            
            // Construir URL del DataService
            $url = REST_CORE . '/entidades_negocio';
            log_message('info', 'Construyendo URL del DataService');
            log_message('debug', 'REST_CORE constante: ' . REST_CORE);
            log_message('debug', 'URL completa: ' . $url);
            
            // Usar librería REST para hacer la llamada GET
            $headers = array();
            log_message('debug', 'Headers configurados: ' . json_encode($headers));
            log_message('info', 'Realizando llamada GET al DataService...');
            
            $result = $this->rest->callAPI('GET', $url, null, $headers);
            log_message('info', 'Llamada REST completada');
            log_message('debug', 'Resultado completo de REST: ' . json_encode($result));
            
            if (!$result['status']) {
                log_message('error', 'Error en llamada REST - status: false');
                log_message('error', 'Detalles del error: ' . json_encode($result));
                return false;
            }
            
            $response_code = $result['code'];
            $response_body = $result['data'];
            
            log_message('info', 'Respuesta HTTP recibida');
            log_message('debug', 'Código de respuesta HTTP: ' . $response_code);
            log_message('debug', 'Cuerpo de respuesta: ' . $response_body);
            
            if ($response_code !== 200) {
                log_message('error', 'Error HTTP - código: ' . $response_code);
                log_message('error', 'Cuerpo de respuesta de error: ' . $response_body);
                return false;
            }
            
            log_message('info', 'Iniciando parseo de respuesta JSON...');
            // Parsear respuesta JSON
            $data = json_decode($response_body, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                log_message('error', 'Error al parsear JSON: ' . json_last_error_msg());
                log_message('debug', 'Respuesta que causó el error: ' . $response_body);
                return false;
            }
            
            log_message('info', 'JSON parseado exitosamente');
            log_message('debug', 'Datos parseados: ' . json_encode($data));
            
            // Verificar estructura de respuesta
            if (!isset($data['response']['entidades'])) {
                log_message('error', 'Estructura de respuesta inválida - falta response.entidades');
                log_message('debug', 'Estructura recibida: ' . json_encode(array_keys($data)));
                return false;
            }
            
            $entidades = $data['response']['entidades'];
            log_message('info', 'Entidades extraídas exitosamente');
            log_message('info', 'Cantidad de entidades obtenidas: ' . count($entidades));
            log_message('debug', 'Lista completa de entidades: ' . json_encode($entidades));
            
            // Log detallado de cada entidad
            foreach ($entidades as $index => $entidad) {
                log_message('debug', "Entidad $index - Nombre: " . $entidad['nombre']);
                log_message('debug', "Entidad $index - Stored Procedure: " . $entidad['stored_procedure']);
                log_message('debug', "Entidad $index - Template: " . $entidad['template']);
            }
            
            log_message('info', '=== FINALIZANDO obtenerEntidadesNegocio exitosamente ===');
            return $entidades;
            
        } catch (Exception $e) {
            log_message('error', 'Exception en obtenerEntidadesNegocio: ' . $e->getMessage());
            log_message('error', 'Archivo: ' . $e->getFile() . ' Línea: ' . $e->getLine());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            throw $e; // Propagar la excepción
        }
    }

    /**
     * Ejecuta directamente el procedimiento de carga masiva en la base de datos
     * 
     * @param string $csv_filepath Ruta del archivo CSV
     * @param string $stored_procedure Nombre del stored procedure
     * @param string $empr_id ID de la empresa
     * @return array|false Resultado del procesamiento o false si hay error
     */
    public function enviarADataservice($csv_filepath, $stored_procedure, $empr_id) {
        try {
            log_message('info', '=== INICIANDO enviarADataservice (DIRECTO A BD) ===');
            log_message('info', 'Parámetros recibidos:');
            log_message('debug', 'CSV file path: ' . $csv_filepath);
            log_message('debug', 'Stored procedure: ' . $stored_procedure);
            log_message('debug', 'Empresa ID: ' . $empr_id);
            log_message('debug', 'Tipo de empr_id: ' . gettype($empr_id));
            
            // Verificar que el archivo existe
            if (!file_exists($csv_filepath)) {
                log_message('error', 'Archivo CSV no encontrado: ' . $csv_filepath);
                return false;
            }
            log_message('info', 'Archivo CSV verificado - existe');
            log_message('debug', 'Tamaño del archivo: ' . filesize($csv_filepath) . ' bytes');
            
            log_message('info', 'Cargando base de datos...');
            // Cargar la base de datos
            $this->load->database();
            log_message('info', 'Base de datos cargada exitosamente');
            
            // Preparar la consulta SQL para llamar al procedimiento
            $sql = "SELECT sta.ejecutar_carga_masiva(?, ?, ?) as resultado";
            $params = array(
                $stored_procedure,
                $csv_filepath,
                intval($empr_id)
            );
            
            log_message('info', 'Preparando consulta SQL...');
            log_message('debug', 'SQL Query: ' . $sql);
            log_message('debug', 'Parámetros preparados: ' . json_encode($params));
            log_message('debug', 'Parámetro 1 (stored_procedure): ' . $params[0]);
            log_message('debug', 'Parámetro 2 (csv_filepath): ' . $params[1]);
            log_message('debug', 'Parámetro 3 (empr_id): ' . $params[2] . ' (tipo: ' . gettype($params[2]) . ')');
            
            log_message('info', 'Ejecutando consulta en base de datos...');
            // Ejecutar la consulta
            $query = $this->db->query($sql, $params);
            
            if ($query === false) {
                $error = $this->db->error();
                log_message('error', 'Error en consulta a base de datos');
                log_message('error', 'Código de error: ' . $error['code']);
                log_message('error', 'Mensaje de error: ' . $error['message']);
                return false;
            }
            log_message('info', 'Consulta ejecutada exitosamente');
            
            // Obtener el resultado
            log_message('info', 'Obteniendo resultado de la consulta...');
            $result = $query->row();
            
            if (!$result) {
                log_message('error', 'No se obtuvo resultado del stored procedure');
                log_message('debug', 'Número de filas afectadas: ' . $this->db->affected_rows());
                return false;
            }
            log_message('info', 'Resultado obtenido exitosamente');
            
            $output = $result->resultado;
            log_message('info', 'Procesamiento de carga masiva completado');
            log_message('debug', 'Output completo del stored procedure: ' . $output);
            log_message('debug', 'Longitud del output: ' . strlen($output) . ' caracteres');
            
            // Analizar el resultado
            $is_success = strpos($output, 'SUCCESS:') === 0;
            log_message('info', 'Análisis del resultado: ' . ($is_success ? 'ÉXITO' : 'ERROR'));
            
            if ($is_success) {
                log_message('info', 'Carga masiva procesada exitosamente');
            } else {
                log_message('warning', 'Carga masiva completada con errores');
                log_message('debug', 'Contenido del error: ' . substr($output, 0, 200) . '...');
            }
            
            $response = array(
                'success' => $is_success,
                'output' => $output,
                'raw_response' => array('output' => $output)
            );
            
            log_message('debug', 'Respuesta final preparada: ' . json_encode($response));
            log_message('info', '=== FINALIZANDO enviarADataservice ===');
            
            return $response;
            
        } catch (Exception $e) {
            log_message('error', 'Exception en enviarADataservice: ' . $e->getMessage());
            log_message('error', 'Archivo: ' . $e->getFile() . ' Línea: ' . $e->getLine());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            throw $e; // Propagar la excepción
        }
    }

    /**
     * Envía el archivo CSV al DataService de WSO2 para procesamiento (MÉTODO ALTERNATIVO)
     * 
     * @param string $csv_filepath Ruta del archivo CSV
     * @param string $stored_procedure Nombre del stored procedure
     * @param string $empr_id ID de la empresa
     * @return array|false Resultado del procesamiento o false si hay error
     */
    public function enviarADataserviceWSO2($csv_filepath, $stored_procedure, $empr_id) {
        try {
            log_message('info', '=== INICIANDO enviarADataserviceWSO2 ===');
            log_message('debug', 'CSV file: ' . $csv_filepath);
            log_message('debug', 'Stored procedure: ' . $stored_procedure);
            log_message('debug', 'Empresa ID: ' . $empr_id);
            
            // Cargar librería REST
            $this->load->library('REST');
            
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
            
            log_message('debug', 'Payload: ' . json_encode($payload));
            
            // Usar librería REST para hacer la llamada POST
            $headers = array(
                'Content-Type: application/json'
            );
            
            $result = $this->rest->callAPI('POST', $url, $payload, $headers);
            
            if (!$result['status']) {
                log_message('error', 'Error en llamada REST: ' . json_encode($result));
                return false;
            }
            
            $response_code = $result['code'];
            $response_body = $result['data'];
            
            log_message('debug', 'HTTP Response Code: ' . $response_code);
            log_message('debug', 'Response: ' . $response_body);
            
            if ($response_code !== 200) {
                log_message('error', 'HTTP Error: ' . $response_code);
                log_message('error', 'HTTP Response Body: ' . $response_body);
                return false;
            }
            
            // Parsear respuesta JSON
            $data = json_decode($response_body, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                log_message('error', 'JSON Parse Error: ' . json_last_error_msg());
                return false;
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
            log_message('error', 'Exception en enviarADataserviceWSO2: ' . $e->getMessage());
            log_message('error', 'Archivo: ' . $e->getFile() . ' Línea: ' . $e->getLine());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            throw $e; // Propagar la excepción
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
            
            // Cargar librería REST
            $this->load->library('REST');
            
            // Construir URL del DataService
            $url = COREDataService_URL . '/carga_masiva/resultado';
            log_message('debug', 'WSO2 DataService URL: ' . $url);
            
            // Usar librería REST para hacer la llamada GET
            $headers = array(
                'Content-Type: application/json'
            );
            
            $result = $this->rest->callAPI('GET', $url, null, $headers);
            
            if (!$result['status']) {
                log_message('error', 'Error en llamada REST: ' . json_encode($result));
                return false;
            }
            
            $response_code = $result['code'];
            $response_body = $result['data'];
            
            log_message('debug', 'HTTP Response Code: ' . $response_code);
            log_message('debug', 'Response: ' . $response_body);
            
            if ($response_code !== 200) {
                log_message('error', 'HTTP Error: ' . $response_code);
                log_message('error', 'HTTP Response Body: ' . $response_body);
                return false;
            }
            
            // Parsear respuesta JSON
            $data = json_decode($response_body, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                log_message('error', 'JSON Parse Error: ' . json_last_error_msg());
                return false;
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
            log_message('error', 'Exception en obtenerResultadoCargaMasiva: ' . $e->getMessage());
            log_message('error', 'Archivo: ' . $e->getFile() . ' Línea: ' . $e->getLine());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            throw $e; // Propagar la excepción
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
            
            // Cargar librería REST
            $this->load->library('REST');
            
            // Construir URL del DataService
            $url = COREDataService_URL . '/carga_masiva/limpiar';
            log_message('debug', 'WSO2 DataService URL: ' . $url);
            
            // Usar librería REST para hacer la llamada POST
            $headers = array(
                'Content-Type: application/json'
            );
            
            $result = $this->rest->callAPI('POST', $url, null, $headers);
            
            if (!$result['status']) {
                log_message('error', 'Error en llamada REST: ' . json_encode($result));
                return false;
            }
            
            $response_code = $result['code'];
            $response_body = $result['data'];
            
            log_message('debug', 'HTTP Response Code: ' . $response_code);
            log_message('debug', 'Response: ' . $response_body);
            
            if ($response_code !== 200) {
                log_message('error', 'HTTP Error: ' . $response_code);
                log_message('error', 'HTTP Response Body: ' . $response_body);
                return false;
            }
            
            // Parsear respuesta JSON
            $data = json_decode($response_body, true);
            
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
            log_message('error', 'Exception en limpiarLogsTemporales: ' . $e->getMessage());
            log_message('error', 'Archivo: ' . $e->getFile() . ' Línea: ' . $e->getLine());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            throw $e; // Propagar la excepción
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
