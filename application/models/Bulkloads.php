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
    /**
     * Punto de entrada de la carga masiva: decide contra qué motor corre la
     * entidad y despacha. El controlador y la vista no cambian ni se enteran.
     *
     * @param  string $csv_filepath     ruta del CSV ya generado en staging
     * @param  string $stored_procedure nombre del SP tal como está en el catálogo
     * @param  int    $empr_id          empresa del usuario
     * @return array|false              mismo formato para los dos motores
     */
    public function enviarADataservice($csv_filepath, $stored_procedure, $empr_id) {
        $motor = $this->resolverMotorBd($stored_procedure);
        log_message('info', '=== CARGA MASIVA === motor resuelto: ' . $motor . ' para ' . $stored_procedure);

        if ($motor === 'mariadb') {
            return $this->ejecutarCargaMariaDB($csv_filepath, $stored_procedure, $empr_id);
        }
        return $this->ejecutarCargaPostgreSQL($csv_filepath, $stored_procedure, $empr_id);
    }

    /**
     * Resuelve el motor de una entidad leyendo sta.entidades_negocio, que vive
     * siempre en PostgreSQL aunque la carga corra en otro motor.
     *
     * Se consulta la tabla directamente y no el catálogo que ya trajo el
     * controlador vía WSO2, a propósito: así el despacho funciona aunque el
     * COREDataService todavía no haya sido redesplegado con la columna nueva.
     *
     * Si la entidad no está en el catálogo, se cae a la convención de nombres
     * que el propio catálogo respeta: los procedimientos de PostgreSQL llevan
     * el prefijo 'sta.' y los de AssetPlanner no.
     *
     * @param  string $stored_procedure
     * @return string 'postgresql' | 'mariadb'
     */
    private function resolverMotorBd($stored_procedure) {
        $sp = trim((string) $stored_procedure);

        try {
            $this->load->database();
            $q = $this->db->query(
                'SELECT motor_bd FROM sta.entidades_negocio WHERE stored_procedure = ? LIMIT 1',
                array($sp)
            );
            if ($q && $q->num_rows() > 0) {
                $motor = strtolower(trim((string) $q->row()->motor_bd));
                if ($motor === 'mariadb' || $motor === 'postgresql') {
                    return $motor;
                }
            }
            log_message('info', '#BULKLOAD|resolverMotorBd >> sin motor_bd en el catálogo para ' . $sp . ', se deduce por el nombre');
        } catch (Exception $e) {
            // La columna puede no existir todavía en un ambiente sin migrar.
            log_message('error', '#BULKLOAD|resolverMotorBd >> no pude leer el catálogo: ' . $e->getMessage());
        }

        return (strpos($sp, 'sta.') === 0) ? 'postgresql' : 'mariadb';
    }

    /**
     * Carga masiva contra PostgreSQL. Es el camino histórico, sin cambios:
     * el dispatcher sta.ejecutar_carga_masiva recibe el CSV y se encarga del
     * COPY y de invocar al procedimiento de la entidad.
     */
    private function ejecutarCargaPostgreSQL($csv_filepath, $stored_procedure, $empr_id) {
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
           
            /* harkode para usar en desarrollo - descomentar  $csv_filepath para probar y chekear que este el archivo en el server */
            //$csv_filepath= '/home/soportetrazalog24/CargaMasivaHerramientas.csv';


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
                return array(
                    'success' => false,
                    'output' => 'ERROR: ' . $error['message'],
                    'raw_response' => array('output' => $error['message'])
                );
            }
            log_message('info', 'Consulta ejecutada exitosamente');
            
            // Obtener el resultado
            log_message('info', 'Obteniendo resultado de la consulta...');
            $result = $query->row();
            
            if (!$result) {
                log_message('error', 'No se obtuvo resultado del stored procedure');
                log_message('debug', 'Número de filas afectadas: ' . $this->db->affected_rows());
                return array(
                    'success' => false,
                    'output' => 'ERROR: No se obtuvo resultado del stored procedure',
                    'raw_response' => array('output' => 'No result')
                );
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

                // Obtener los SOTR_IDs desde el procedure
                preg_match('/SOTR_IDs:\s*([0-9,\s]+)/', $output, $matches);

                if (!empty($matches[1])) {
                    $sotr_ids = array_map('trim', explode(',', $matches[1]));

                    foreach ($sotr_ids as $sotr_id) {
                        // Obtener los datos del solicitante transporte
                        $url = REST_RESI . "/getSolicitanteTransporte/" . $sotr_id;
                        $response = $this->rest->callAPI("GET", $url);
                        $data = json_decode($response["data"], true);

                        if (!empty($data["transportistas"]["transportista"][0])) {
                            $sol = $data["transportistas"]["transportista"][0];
                            $razon_social = $sol["razon_social"];
                            $cuit = $sol["cuit"];

                            log_message('debug', "Creando empresa para solicitante_transportista SOTR_ID {$sotr_id} - Razon Social: {$razon_social}, CUIT: {$cuit}");

                            $empresa = [
                                'nombre'      => $razon_social,
                                'cuit'        => $cuit,
                                'descripcion' => $razon_social,
                                'telefono'    => '',
                                'email'       => '',
                                'pais_id'     => '',
                                'prov_id'     => '',
                                'loca_id'     => '',
                                'imagepath'   => '',
                                'image'       => ''
                            ];

                            $post = ['empresa' => $empresa];
                            $headers = ['Content-Type: application/json'];

                            // Llamado a la API para crear la empresa en Bonita y core.empresas
                            $url = API_CORE . "/empresa";
                            $result = $this->rest->callAPI("POST", $url, $post, $headers);
                            $result_decoded = json_decode($result["data"], true);

                            log_message('debug', "Empresa creada para SOTR_ID {$sotr_id}");
                            log_message('debug', "Respuesta API generador/empresa: " . json_encode($result_decoded));

                            // Verificar si la API devolvió un empr_id
                            if (!empty($result_decoded["respuesta"]["empr_id"])) {
                                $empr_id = $result_decoded["respuesta"]["empr_id"];

                                // Actualizar la tabla solicitantes_transportista con el nuevo empr_id
                                $this->db->set('empr_id', $empr_id);
                                $this->db->where('sotr_id', $sotr_id);
                                $this->db->update('log.solicitantes_transporte');

                                if ($this->db->affected_rows() > 0) {
                                    log_message('debug', "Actualizado solicitantes_transporte.empr_id = {$empr_id} para SOTR_ID {$sotr_id}");
                                } else {
                                    log_message('error', "No se pudo actualizar empr_id para SOTR_ID {$sotr_id}");
                                }
                            } else {
                                log_message('error', "No se recibió empr_id válido para SOTR_ID {$sotr_id}");
                            }
                        } else {
                            log_message('warning', "No se encontró transportista para SOTR_ID {$sotr_id}");
                        }
                    }

                    log_message('debug', 'Procesamiento de transportistas completado');
                } else {
                    $sotr_ids = [];
                }
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

    // =======================================================================
    // MariaDB (AssetPlanner)
    // =======================================================================

    /**
     * Carga masiva contra MariaDB (base assetv2 de AssetPlanner).
     *
     * EL PATRÓN ES DISTINTO AL DE POSTGRESQL, y no es un detalle: allá el
     * dispatcher recibe la ruta del CSV y hace el COPY por su cuenta. Acá los
     * procedimientos NO reciben archivo — verificado sobre el servidor:
     * bulkload_equipos tiene un solo parámetro, (IN p_id_empresa INT). Leen de
     * una tabla de staging (sta_equipos, sta_articulos), procesan lo que esté
     * con procesado = 0 y lo marcan al terminar.
     *
     * Entonces acá hay dos pasos donde en PostgreSQL hay uno:
     *   1. volcar el CSV en la tabla de staging que corresponde a la entidad;
     *   2. CALL al procedimiento con el id de empresa.
     *
     * El CSV se inserta desde PHP en vez de con LOAD DATA INFILE porque el
     * archivo vive en el servidor de Dnato y MariaDB corre en otro host: un
     * LOAD DATA server-side no lo vería. Para los volúmenes de una carga
     * masiva de catálogo (decenas o cientos de filas) el costo es irrelevante.
     *
     * @param  string $csv_filepath
     * @param  string $stored_procedure  p. ej. 'bulkload_equipos'
     * @param  int    $empr_id           id de empresa EN ASSETPLANNER
     * @return array|false
     */
    private function ejecutarCargaMariaDB($csv_filepath, $stored_procedure, $empr_id) {
        $logs = array();

        try {
            if (!file_exists($csv_filepath)) {
                return $this->respuestaCarga(false, 'ERROR: Archivo CSV no encontrado: ' . $csv_filepath);
            }

            $sp = trim((string) $stored_procedure);
            if (preg_match('/^[a-zA-Z0-9_]+$/', $sp) !== 1) {
                return $this->respuestaCarga(false, 'ERROR: Nombre de procedimiento inválido: ' . $sp);
            }

            // El id de empresa de Dnato NO sirve para AssetPlanner: son bases
            // distintas con numeraciones propias. Se traduce igual que en la
            // cadena de identidad del MCP, por core.empresas.empr_id_mysql.
            //
            // Se resuelve ANTES de tocar el staging: si no se puede, no tiene
            // sentido haber escrito filas que después habría que limpiar.
            $empr_id_asset = $this->resolverEmprIdAssetPlanner($empr_id, $logs);
            if ($empr_id_asset === false) {
                return $this->respuestaCarga(false, 'ERROR: ' . end($logs), $logs);
            }

            $tabla_staging = $this->tablaStagingDe($sp);
            $logs[] = 'Motor MariaDB | procedimiento: ' . $sp . ' | staging: ' . $tabla_staging;

            $mdb = $this->load->database('assetplanner', TRUE);
            if (!$mdb) {
                return $this->respuestaCarga(false, 'ERROR: No se pudo conectar a AssetPlanner (MariaDB)');
            }

            // La tabla de staging tiene que existir: si no, la entidad está mal
            // dada de alta en el catálogo y conviene decirlo claro.
            $existe = $mdb->query(
                'SELECT COUNT(*) AS n FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ?',
                array($tabla_staging)
            );
            if (!$existe || (int) $existe->row()->n === 0) {
                return $this->respuestaCarga(false, 'ERROR: No existe la tabla de staging ' . $tabla_staging . ' en AssetPlanner');
            }

            // Columnas reales de la tabla, para mapear el CSV por NOMBRE de
            // encabezado y no por posición: así un cambio de orden en la
            // plantilla no rompe la carga.
            $cols_tabla = array();
            $qc = $mdb->query(
                'SELECT COLUMN_NAME FROM information_schema.COLUMNS
                  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ?
                    AND COLUMN_NAME NOT IN (\'procesado\', \'fec_proceso\')',
                array($tabla_staging)
            );
            foreach ($qc->result() as $r) {
                $cols_tabla[strtolower($r->COLUMN_NAME)] = $r->COLUMN_NAME;
            }

            $filas = $this->leerCsvParaStaging($csv_filepath, $cols_tabla, $logs);
            if ($filas === false) {
                return $this->respuestaCarga(false, 'ERROR: ' . end($logs), $logs);
            }
            if (count($filas['datos']) === 0) {
                return $this->respuestaCarga(false, 'ERROR: El archivo no tiene filas de datos', $logs);
            }

            // Si quedaron filas sin procesar de una corrida anterior, el
            // procedimiento las tomaría como propias de esta carga y las
            // cargaría en ESTA empresa. Pero borrarlas sin más es destructivo:
            // podrían ser de otro usuario cargando al mismo tiempo, o de una
            // corrida que quedó a medias y que alguien quiere recuperar.
            //
            // Se aborta y se avisa. Limpiar es una decisión de quien administra,
            // no algo que esta función deba tomar por su cuenta.
            $pendientes = $mdb->query('SELECT COUNT(*) AS n FROM ' . $tabla_staging . ' WHERE procesado = 0');
            $cuantas = ($pendientes && $pendientes->num_rows() > 0) ? (int) $pendientes->row()->n : 0;
            if ($cuantas > 0) {
                $mdb->close();
                $logs[] = 'La tabla ' . $tabla_staging . ' tiene ' . $cuantas . ' fila(s) sin procesar de una carga '
                        . 'anterior. Si se continuara, el procedimiento las cargaría como si fueran de esta empresa. '
                        . 'Revisar y limpiar esas filas antes de reintentar.';
                log_message('ERROR', '#BULKLOAD|ejecutarCargaMariaDB >> ' . $tabla_staging . ' con ' . $cuantas . ' pendientes; se aborta');
                return $this->respuestaCarga(false, 'ERROR: ' . end($logs), $logs);
            }

            $insertadas = $this->insertarEnStaging($mdb, $tabla_staging, $filas, $logs);
            if ($insertadas === false) {
                return $this->respuestaCarga(false, 'ERROR: ' . end($logs), $logs);
            }
            $logs[] = 'Filas cargadas en staging: ' . $insertadas;

            $salida = $this->llamarProcedimientoMariaDB($mdb, $sp, $empr_id_asset, $logs);
            $mdb->close();

            if ($salida === false) {
                return $this->respuestaCarga(false, 'ERROR: ' . end($logs), $logs);
            }

            // Los procedimientos de AssetPlanner no devuelven SUCCESS:/ERROR:
            // como los de PostgreSQL: emiten una serie de mensajes con prefijo
            // propio (BULKEQ:, etc.). Se traduce al contrato que espera la
            // vista, que es la misma para los dos motores.
            $hubo_error = false;
            foreach ($salida as $linea) {
                if (stripos($linea, 'ERROR') !== false) {
                    $hubo_error = true;
                    break;
                }
            }
            $logs = array_merge($logs, $salida);

            if ($hubo_error) {
                return $this->respuestaCarga(false, 'ERROR: ' . implode(' | ', $salida), $logs);
            }
            return $this->respuestaCarga(true, 'SUCCESS: Carga masiva ejecutada en AssetPlanner (' . $insertadas . ' filas)', $logs);

        } catch (Exception $e) {
            log_message('error', '#BULKLOAD|ejecutarCargaMariaDB >> ' . $e->getMessage());
            return $this->respuestaCarga(false, 'ERROR: ' . $e->getMessage(), $logs);
        }
    }

    /**
     * Tabla de staging que le corresponde a un procedimiento, por convención:
     * bulkload_equipos -> sta_equipos, bulkload_articulos -> sta_articulos.
     *
     * @param  string $stored_procedure
     * @return string
     */
    private function tablaStagingDe($stored_procedure) {
        $base = preg_replace('/^bulkload_/', '', strtolower(trim((string) $stored_procedure)));
        return 'sta_' . $base;
    }

    /**
     * Lee el CSV y arma las filas a insertar, quedándose sólo con las columnas
     * que existen en la tabla de staging. El mapeo es por nombre de encabezado.
     *
     * @param  string $csv_filepath
     * @param  array  $cols_tabla   columna_en_minuscula => ColumnaReal
     * @param  array  $logs         se completa por referencia
     * @return array|false          array('columnas'=>..., 'datos'=>...)
     */
    private function leerCsvParaStaging($csv_filepath, $cols_tabla, &$logs) {
        $fh = fopen($csv_filepath, 'r');
        if (!$fh) {
            $logs[] = 'No se pudo abrir el CSV: ' . $csv_filepath;
            return false;
        }

        $delim = $this->detectarDelimitador($csv_filepath);
        $encabezado = fgetcsv($fh, 0, $delim);
        if (!$encabezado) {
            fclose($fh);
            $logs[] = 'El CSV no tiene encabezado';
            return false;
        }

        // Posición en el CSV -> nombre real de columna en la tabla
        $mapa = array();
        foreach ($encabezado as $i => $nombre) {
            $limpio = strtolower(trim(str_replace("\xEF\xBB\xBF", '', (string) $nombre)));
            if (isset($cols_tabla[$limpio])) {
                $mapa[$i] = $cols_tabla[$limpio];
            }
        }
        if (empty($mapa)) {
            fclose($fh);
            $logs[] = 'Ningún encabezado del CSV coincide con las columnas de la tabla de staging. Encabezados: ' . implode(', ', $encabezado);
            return false;
        }
        $logs[] = 'Columnas mapeadas: ' . implode(', ', array_values($mapa));

        $datos = array();
        while (($fila = fgetcsv($fh, 0, $delim)) !== FALSE) {
            if (count($fila) === 1 && trim((string) $fila[0]) === '') {
                continue; // línea vacía
            }
            $registro = array();
            foreach ($mapa as $i => $col) {
                $registro[$col] = isset($fila[$i]) ? trim((string) $fila[$i]) : '';
            }
            $datos[] = $registro;
        }
        fclose($fh);

        return array('columnas' => array_values($mapa), 'datos' => $datos);
    }

    /**
     * El CSV que genera el sistema puede venir con coma o con punto y coma
     * según cómo se haya exportado el Excel.
     *
     * @param  string $csv_filepath
     * @return string
     */
    private function detectarDelimitador($csv_filepath) {
        $fh = fopen($csv_filepath, 'r');
        if (!$fh) {
            return ',';
        }
        $primera = fgets($fh);
        fclose($fh);
        return (substr_count((string) $primera, ';') > substr_count((string) $primera, ',')) ? ';' : ',';
    }

    /**
     * Inserta las filas en la tabla de staging, en lotes.
     *
     * @param  object $mdb    conexión a AssetPlanner
     * @param  string $tabla
     * @param  array  $filas
     * @param  array  $logs
     * @return int|false      cantidad insertada
     */
    private function insertarEnStaging($mdb, $tabla, $filas, &$logs) {
        $columnas = $filas['columnas'];
        $total    = 0;
        $lote     = array();

        foreach ($filas['datos'] as $registro) {
            $lote[] = $registro;
            if (count($lote) >= 200) {
                if (!$mdb->insert_batch($tabla, $lote)) {
                    $e = $mdb->error();
                    $logs[] = 'Error insertando en ' . $tabla . ': ' . $e['message'];
                    return false;
                }
                $total += count($lote);
                $lote = array();
            }
        }
        if (!empty($lote)) {
            if (!$mdb->insert_batch($tabla, $lote)) {
                $e = $mdb->error();
                $logs[] = 'Error insertando en ' . $tabla . ': ' . $e['message'];
                return false;
            }
            $total += count($lote);
        }
        return $total;
    }

    /**
     * Ejecuta CALL sp(empr_id) y junta la salida.
     *
     * Los procedimientos de AssetPlanner van informando su avance con SELECTs
     * sueltos, así que devuelven VARIOS resultsets. Hay que recorrerlos todos:
     * quedarse con el primero pierde justamente el mensaje final, que es el
     * que dice si terminó bien o falló.
     *
     * @param  object $mdb
     * @param  string $sp
     * @param  int    $empr_id
     * @param  array  $logs
     * @return array|false  líneas devueltas por el procedimiento
     */
    private function llamarProcedimientoMariaDB($mdb, $sp, $empr_id, &$logs) {
        $conn = $mdb->conn_id;
        if (!($conn instanceof mysqli)) {
            $logs[] = 'La conexión a AssetPlanner no es mysqli; no puedo leer múltiples resultados';
            return false;
        }

        $sql = 'CALL ' . $sp . '(' . intval($empr_id) . ')';
        $logs[] = 'Ejecutando: ' . $sql;

        $salida = array();
        if (!$conn->multi_query($sql)) {
            $logs[] = 'Falló el CALL: (' . $conn->errno . ') ' . $conn->error;
            return false;
        }

        do {
            $res = $conn->store_result();
            if ($res) {
                while ($fila = $res->fetch_row()) {
                    foreach ($fila as $valor) {
                        $valor = trim((string) $valor);
                        if ($valor !== '') {
                            $salida[] = $valor;
                        }
                    }
                }
                $res->free();
            }
        } while ($conn->more_results() && $conn->next_result());

        if ($conn->errno) {
            $logs[] = 'Error durante la ejecución: (' . $conn->errno . ') ' . $conn->error;
            return false;
        }
        return $salida;
    }

    /**
     * Formato de respuesta único para los dos motores: lo que espera
     * application/views/bulkload/resultado.php.
     *
     * @param  bool   $exito
     * @param  string $output
     * @param  array  $logs
     * @return array
     */
    private function respuestaCarga($exito, $output, $logs = array()) {
        if (!empty($logs)) {
            log_message('debug', '#BULKLOAD >> ' . implode(' || ', $logs));
        }
        return array(
            'success'      => (bool) $exito,
            'output'       => $output,
            'raw_response' => array('output' => $output, 'logs' => $logs)
        );
    }

    /**
     * Traduce el id de empresa de Dnato al id que esa misma empresa tiene en
     * AssetPlanner (base assetv2).
     *
     * POR QUÉ HACE FALTA: son dos bases con numeraciones independientes. En la
     * base de desarrollo, de las 4 empresas vinculadas 3 tienen ids distintos
     * (187->17, 188->18, 190->20). Pasarle a MariaDB el empr_id de Dnato
     * cargaría los equipos EN LA EMPRESA DE OTRO CLIENTE, sin ningún error
     * visible. Es el mismo problema que se corrigió en las tools alm_* del MCP.
     *
     * El vínculo es core.empresas.empr_id_mysql, exactamente el que alimenta el
     * claim empr_id_mysql del JWT (ver Empresas::getEmpresaById, usado por
     * Oauth::_issueCode y Oauthlogin::_resolveCompany). Acá se consulta la
     * tabla directamente en lugar de ir por el DataService, porque el resource
     * GET /empresa/{empr_id} no está en todas las copias desplegadas del CAR.
     *
     * SI NO HAY VÍNCULO, CORTA. Es deliberado: sin el id correcto la única
     * alternativa sería adivinar, y adivinar acá significa escribir en los
     * datos de otra empresa. Mejor no cargar nada y decir por qué.
     *
     * @param  int   $empr_id  id de empresa en Dnato (PostgreSQL)
     * @param  array $logs
     * @return int|false       id en AssetPlanner, o false si no se puede resolver
     */
    private function resolverEmprIdAssetPlanner($empr_id, &$logs) {
        $empr_id = (int) $empr_id;
        if ($empr_id <= 0) {
            $logs[] = 'Id de empresa inválido: ' . $empr_id;
            return false;
        }

        try {
            $this->load->database();
            $q = $this->db->query(
                'SELECT empr_id_mysql, nombre FROM core.empresas WHERE empr_id = ? AND eliminado = false LIMIT 1',
                array($empr_id)
            );

            if (!$q || $q->num_rows() === 0) {
                $logs[] = 'La empresa ' . $empr_id . ' no existe en core.empresas';
                return false;
            }

            $fila = $q->row();
            if ($fila->empr_id_mysql === null || trim((string) $fila->empr_id_mysql) === '') {
                $logs[] = 'La empresa "' . $fila->nombre . '" (empr_id ' . $empr_id . ') no tiene definido su '
                        . 'equivalente en AssetPlanner (core.empresas.empr_id_mysql). Sin ese dato la carga se '
                        . 'haría sobre otra empresa, así que se cancela. Un administrador debe completar el vínculo.';
                log_message('ERROR', '#BULKLOAD|resolverEmprIdAssetPlanner >> empr_id_mysql vacío para empr_id=' . $empr_id);
                return false;
            }

            $asset_id = (int) $fila->empr_id_mysql;
            if ($asset_id <= 0) {
                $logs[] = 'El vínculo con AssetPlanner de la empresa ' . $empr_id . ' es inválido: ' . $fila->empr_id_mysql;
                return false;
            }

            $logs[] = 'Empresa: Dnato ' . $empr_id . ' -> AssetPlanner ' . $asset_id . ' ("' . $fila->nombre . '")';
            return $asset_id;

        } catch (Exception $e) {
            $logs[] = 'No pude resolver la empresa en AssetPlanner: ' . $e->getMessage();
            log_message('ERROR', '#BULKLOAD|resolverEmprIdAssetPlanner >> ' . $e->getMessage());
            return false;
        }
    }
}
