<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Controlador para la funcionalidad de Carga Masiva
 * Permite cargar archivos Excel y procesarlos mediante stored procedures
 * 
 * @author Trazalog Tools Team
 * @version 1.0
 * @since 2024-01-XX
 */
class Bulkload extends CI_Controller {

    /**
     * Constructor del controlador
     * Inicializa librerías y helpers necesarios
     */
    public function __construct() {
        parent::__construct();
        
        // Cargar librerías necesarias
        $this->load->library('session');
        $this->load->library('form_validation');
        $this->load->library('upload');
        $this->load->helper('url');
        $this->load->helper('file');
        
        // Verificar que el usuario esté autenticado
        if (!$this->session->userdata('email')) {
            redirect('main/login');
        }
        
        // Cargar modelos necesarios
        $this->load->model('User_model', 'user_model', TRUE);
        $this->load->model('Bulkloads', 'bulkloads', TRUE);
        $this->load->model('Bulkload_model');
        
        // Cargar librerías necesarias para las vistas
        $this->load->library('Userlevel', 'userlevel');
        
        // Configurar logging
        log_message('info', 'Bulkload controller initialized for user: ' . $this->session->userdata('email'));
    }

    /**
     * Página principal de carga masiva
     * Muestra el formulario y lista de entidades disponibles
     */
    public function index() {
        try {
            log_message('info', '=== INICIANDO Bulkload index ===');
            log_message('info', 'Usuario actual: ' . $this->session->userdata('email'));
            log_message('info', 'Empresa ID: ' . $this->session->userdata('empr_id'));
            
            log_message('info', 'Obteniendo entidades de negocio desde WSO2...');
            // Obtener entidades de negocio desde WSO2
            try {
                $entidades = $this->bulkloads->obtenerEntidadesNegocio();
                
                if ($entidades === false) {
                    log_message('warning', 'WSO2 no disponible - usando datos de prueba');
                    // Datos de prueba para testing
                    $entidades = array(
                        array(
                            'nombre' => 'Artículos',
                            'stored_procedure' => 'sta.sp_carga_masiva_articulos',
                            'template' => 'Template de prueba para artículos'
                        ),
                        array(
                            'nombre' => 'Herramientas',
                            'stored_procedure' => 'sta.sp_carga_masiva_herramientas',
                            'template' => 'Template de prueba para herramientas'
                        )
                    );
                    $this->session->set_flashdata('warning_message', 'Usando datos de prueba. WSO2 no disponible.');
                    log_message('info', 'Datos de prueba configurados: ' . count($entidades) . ' entidades');
                } else {
                    log_message('info', 'Entidades obtenidas desde WSO2: ' . count($entidades) . ' entidades');
                }
            } catch (Exception $e) {
                log_message('error', 'Error al obtener entidades desde WSO2: ' . $e->getMessage());
                log_message('warning', 'Usando datos de prueba debido a error en WSO2');
                // Datos de prueba para testing
                $entidades = array(
                    array(
                        'nombre' => 'Artículos',
                        'stored_procedure' => 'sta.sp_carga_masiva_articulos',
                        'template' => 'Template de prueba para artículos'
                    ),
                    array(
                        'nombre' => 'Herramientas',
                        'stored_procedure' => 'sta.sp_carga_masiva_herramientas',
                        'template' => 'Template de prueba para herramientas'
                    )
                );
                $this->session->set_flashdata('warning_message', 'Usando datos de prueba. Error en WSO2: ' . $e->getMessage());
                log_message('info', 'Datos de prueba configurados: ' . count($entidades) . ' entidades');
            }
            
            log_message('info', 'Preparando datos para la vista...');
            // Preparar datos para la vista (igual que Main)
            $data = $this->session->userdata();
            $data['title'] = 'Carga Masiva de Datos';
            $data['entidades'] = $entidades;
            $data['user_model'] = $this->user_model;
            $data['userlevel'] = $this->userlevel;
            
            // Agregar datos necesarios para el navbar
            log_message('info', 'Cargando datos adicionales para navbar...');
            $this->load->model('Roles');
            $data['usersList'] = $this->user_model->getListUserData();
            $data['emp_connect'] = $this->user_model->gestMembershipsUserInfo($data['email'], 1);
            $data['groups'] = $this->Roles->getBpmGroups();
            
            log_message('info', 'Datos de navbar cargados:');
            log_message('debug', 'usersList count: ' . (is_array($data['usersList']) ? count($data['usersList']) : 'no es array'));
            log_message('debug', 'emp_connect count: ' . (is_array($data['emp_connect']) ? count($data['emp_connect']) : 'no es array'));
            log_message('debug', 'groups count: ' . (is_array($data['groups']) ? count($data['groups']) : 'no es array'));
            
            log_message('info', 'Datos preparados exitosamente');
            log_message('debug', 'Estructura de datos: ' . json_encode(array_keys($data)));
            log_message('debug', 'Entidades finales: ' . json_encode($entidades));
            
            log_message('info', 'Cargando vistas...');
            // Cargar vistas
            $this->load->view('header', $data);
            $this->load->view('navbar', $data);
            $this->load->view('bulkload/index', $data);
            $this->load->view('footer');
            
            log_message('info', '=== FINALIZANDO Bulkload index exitosamente ===');
            
        } catch (Exception $e) {
            log_message('error', 'Exception en bulkload index: ' . $e->getMessage());
            log_message('error', 'Archivo: ' . $e->getFile() . ' Línea: ' . $e->getLine());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            $this->session->set_flashdata('error_message', 'Error interno del sistema. Contacte al administrador.');
            redirect('main/dashboard');
        }
    }

    /**
     * Obtiene las entidades de negocio desde WSO2
     * 
     * @return array|false Array de entidades o false si hay error
     */

    /**
     * Procesa la carga masiva del archivo
     * Valida, convierte y envía el archivo al dataservice
     */
    public function procesarCarga() {
        try {
            log_message('info', '=== INICIANDO procesarCarga ===');
            log_message('info', 'Usuario: ' . $this->session->userdata('email'));
            log_message('info', 'Empresa ID: ' . $this->session->userdata('empr_id'));
            log_message('info', 'Iniciando proceso de carga masiva...');
            
            log_message('debug', 'Datos POST recibidos: ' . json_encode($this->input->post()));
            log_message('debug', 'Datos FILES recibidos: ' . json_encode($_FILES));
            log_message('debug', 'Cantidad de archivos: ' . count($_FILES));
            
            // Validar entrada
            log_message('info', 'Configurando reglas de validación...');
            $this->form_validation->set_rules('entidad_negocio', 'Entidad de Negocio', 'required');
            
            log_message('info', 'Ejecutando validación del formulario...');
            if (!$this->form_validation->run()) {
                log_message('warning', 'Validación falló: ' . validation_errors());
                $this->session->set_flashdata('error_message', validation_errors());
                $this->session->set_userdata('selected_entidad', $this->input->post('entidad_negocio'));
                redirect('bulkload');
                return;
            }
            log_message('info', 'Validación del formulario exitosa');
            
            // Obtener datos del formulario
            $entidad_negocio = $this->input->post('entidad_negocio');
            $stored_procedure = $this->input->post('stored_procedure');
            
            log_message('info', 'Datos del formulario extraídos:');
            log_message('debug', 'Entidad de negocio: ' . $entidad_negocio);
            log_message('debug', 'Stored procedure: ' . $stored_procedure);
            log_message('debug', 'Tipo de entidad_negocio: ' . gettype($entidad_negocio));
            log_message('debug', 'Tipo de stored_procedure: ' . gettype($stored_procedure));
            
            // Validar archivo
            if (!isset($_FILES['archivo_excel']) || $_FILES['archivo_excel']['error'] !== UPLOAD_ERR_OK) {
                $this->session->set_flashdata('error_message', 'Error al cargar el archivo. Verifique que el archivo sea válido.');
                $this->session->set_userdata('selected_entidad', $entidad_negocio);
                redirect('bulkload');
                return;
            }
            
            $archivo = $_FILES['archivo_excel'];
            
            // Validar extensión del archivo
            $extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
            $extensiones_validas = array('xlsx', 'xls');
            
            if (!in_array($extension, $extensiones_validas)) {
                $this->session->set_flashdata('error_message', 'Formato de archivo no válido. Solo se permiten archivos Excel (.xlsx, .xls)');
                $this->session->set_userdata('selected_entidad', $entidad_negocio);
                redirect('bulkload');
                return;
            }
            
            log_message('info', 'File validation passed. File: ' . $archivo['name'] . ', Size: ' . $archivo['size']);
            
            // Crear directorio de staging si no existe
            $staging_dir = FCPATH . 'bulkload_stage_files';
            if (!is_dir($staging_dir)) {
                mkdir($staging_dir, 0755, true);
                log_message('info', 'Created staging directory: ' . $staging_dir);
            }
            
            // Generar nombre único para el archivo
            $timestamp = date('Y-m-d_H-i-s');
#            $filename = 'bulkload_' . $timestamp . '_' . uniqid() . '.' . $extension;
            $filename = 'bulkload_solicitante.' . $extension;
            $filepath = $staging_dir . '/' . $filename;
            
            log_message('debug', 'Generated filename: ' . $filename);
            log_message('debug', 'Full filepath: ' . $filepath);
            
            // Mover archivo al directorio de staging
            if (!move_uploaded_file($archivo['tmp_name'], $filepath)) {
                log_message('error', 'Failed to move uploaded file to staging directory');
                $this->session->set_flashdata('error_message', 'Error al procesar el archivo. Intente nuevamente.');
                redirect('bulkload');
                return;
            }
            
            log_message('info', 'File moved to staging: ' . $filepath);
            
            // Convertir Excel a CSV
            log_message('info', 'Iniciando conversión de Excel a CSV...');
            try {
                $csv_filepath = $this->convertirExcelACsv($filepath, $extension);
                
                if ($csv_filepath === false) {
                    log_message('error', 'La conversión devolvió false');
                    $this->session->set_flashdata('error_message', 'Error al convertir el archivo Excel a CSV. Verifique que el archivo sea válido.');
                    redirect('bulkload');
                    return;
                }
                
                log_message('info', 'Excel converted to CSV: ' . $csv_filepath);
            } catch (Exception $e) {
                log_message('error', 'Error al convertir Excel a CSV: ' . $e->getMessage());
                $this->session->set_flashdata('error_message', 'Error al convertir el archivo: ' . $e->getMessage());
                redirect('bulkload');
                return;
            }
            
            // Obtener empr_id de la sesión
            log_message('info', 'Obteniendo ID de empresa de la sesión...');
            try {
                $empr_id = $this->bulkloads->obtenerEmpresaId();
                
                if ($empr_id === false) {
                    log_message('error', 'No se pudo obtener empr_id de la sesión');
                    $this->session->set_flashdata('error_message', 'Error al obtener información de la empresa. Verifique su sesión.');
                    redirect('bulkload');
                    return;
                }
                
                log_message('info', 'Empresa ID obtenido exitosamente: ' . $empr_id);
                log_message('debug', 'Tipo de empr_id: ' . gettype($empr_id));
            } catch (Exception $e) {
                log_message('error', 'Error al obtener empr_id: ' . $e->getMessage());
                $this->session->set_flashdata('error_message', 'Error al obtener información de la empresa: ' . $e->getMessage());
                redirect('bulkload');
                return;
            }
            
            // Enviar archivo al dataservice
            log_message('info', 'Enviando archivo al dataservice...');
            try {
                $resultado = $this->bulkloads->enviarADataservice($csv_filepath, $stored_procedure, $empr_id);
                
                if ($resultado === false) {
                    log_message('error', 'El dataservice devolvió false');
                    $this->session->set_flashdata('error_message', 'Error al procesar el archivo en el sistema. Intente nuevamente.');
                    redirect('bulkload');
                    return;
                }
                
                log_message('info', 'Archivo procesado por el dataservice');
                log_message('debug', 'Resultado del dataservice: ' . json_encode($resultado));
            } catch (Exception $e) {
                log_message('error', 'Error al procesar archivo en dataservice: ' . $e->getMessage());
                $this->session->set_flashdata('error_message', 'Error al procesar el archivo: ' . $e->getMessage());
                redirect('bulkload');
                return;
            }
            
            // Procesar resultado
            if ($resultado['success']) {
                log_message('info', 'Bulkload completed successfully: ' . $resultado['output']);
            } else {
                log_message('error', 'Bulkload failed: ' . $resultado['output']);
            }
            
            // Limpiar archivos temporales
            $this->limpiarArchivosTemporales($filepath, $csv_filepath);
            
            // Limpiar entidad seleccionada de la sesión
            $this->session->unset_userdata('selected_entidad');
            
            // Redirigir a página de resultado
            $this->mostrarResultado($resultado);
            
            } catch (Exception $e) {
                log_message('error', 'Exception en procesarCarga: ' . $e->getMessage());
                log_message('error', 'Archivo: ' . $e->getFile() . ' Línea: ' . $e->getLine());
                log_message('error', 'Stack trace: ' . $e->getTraceAsString());
                $this->session->set_flashdata('error_message', 'Error interno del sistema: ' . $e->getMessage());
                $this->session->set_userdata('selected_entidad', $this->input->post('entidad_negocio'));
                redirect('bulkload');
            }
    }

    /**
     * Convierte archivo Excel a CSV
     * 
     * @param string $filepath Ruta del archivo Excel
     * @param string $extension Extensión del archivo
     * @return string|false Ruta del archivo CSV o false si hay error
     */
    private function convertirExcelACsv($filepath, $extension) {
        try {
            log_message('info', '=== INICIANDO convertirExcelACsv ===');
            log_message('info', 'Converting Excel to CSV: ' . $filepath);
            log_message('debug', 'File extension: ' . $extension);
            
            // Cargar SimpleXLSX manualmente para PHP 5.6 compatibility
            if (!class_exists('SimpleXLSX')) {
                require_once APPPATH . 'third_party/simplexlsx/SimpleXLSX.php';
            }
            
            $simplexlsx_available = class_exists('SimpleXLSX');
            log_message('debug', 'SimpleXLSX available: ' . ($simplexlsx_available ? 'YES' : 'NO'));
            
            if ($simplexlsx_available) {
                log_message('debug', 'Using SimpleXLSX for conversion');
                return $this->convertirConSimpleXLSX($filepath, $extension);
            } else {
                log_message('warning', 'SimpleXLSX not available, using manual conversion');
                return $this->convertirManual($filepath, $extension);
            }
            
        } catch (Exception $e) {
            log_message('error', 'Exception en convertirExcelACsv: ' . $e->getMessage());
            log_message('error', 'Archivo: ' . $e->getFile() . ' Línea: ' . $e->getLine());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            throw $e; // Propagar la excepción
        }
    }

    /**
     * Conversión usando SimpleXLSX (recomendado para PHP 7.3)
     */
    private function convertirConSimpleXLSX($filepath, $extension) {
        try {
            log_message('info', '=== INICIANDO convertirConSimpleXLSX ===');
            log_message('debug', 'File path: ' . $filepath);
            log_message('debug', 'Extension: ' . $extension);
            
            // Solo soporta XLSX
            if ($extension !== 'xlsx') {
                log_message('error', 'SimpleXLSX only supports XLSX files');
                return false;
            }
            
            // Cargar archivo Excel
            $xlsx = SimpleXLSX::parse($filepath);
            if (!$xlsx) {
                log_message('error', 'Failed to parse XLSX file: ' . SimpleXLSX::parseError());
                return false;
            }
            
            log_message('debug', 'XLSX file parsed successfully');
            
            // Generar ruta del CSV
            $csv_filepath = str_replace('.xlsx', '.csv', $filepath);
            log_message('debug', 'CSV output path: ' . $csv_filepath);
            
            // Crear archivo CSV
            $csv_handle = fopen($csv_filepath, 'w');
            if (!$csv_handle) {
                log_message('error', 'Cannot create CSV file');
                return false;
            }
            
            // Obtener datos de la primera hoja
            $rows = $xlsx->rows();
            log_message('debug', 'Number of rows: ' . count($rows));
            
            // Escribir CSV
            foreach ($rows as $row) {
                $csvRow = [];
                foreach ($row as $cell) {
                    // Escapar comillas y envolver en comillas
                    $value = str_replace('"', '""', $cell);
                    $csvRow[] = '"' . $value . '"';
                }
                fwrite($csv_handle, implode(',', $csvRow) . "\n");
            }
            
            fclose($csv_handle);
            
            log_message('info', 'Excel converted to CSV using SimpleXLSX: ' . $csv_filepath);
            return $csv_filepath;
            
        } catch (Exception $e) {
            log_message('error', 'Exception en convertirConSimpleXLSX: ' . $e->getMessage());
            log_message('error', 'Archivo: ' . $e->getFile() . ' Línea: ' . $e->getLine());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            throw $e; // Propagar la excepción
        }
    }


    /**
     * Conversión manual (fallback)
     */
    private function convertirManual($filepath, $extension) {
        try {
            log_message('info', '=== INICIANDO convertirManual ===');
            log_message('info', 'Converting Excel to CSV manually for file type: ' . $extension);
            
            // Para archivos .xlsx, usar una conversión simple
            if ($extension === 'xlsx') {
                return $this->convertirXlsxManual($filepath);
            } elseif ($extension === 'xls') {
                return $this->convertirXlsManual($filepath);
            } else {
                log_message('error', 'Unsupported file type for manual conversion: ' . $extension);
                return false;
            }
        } catch (Exception $e) {
            log_message('error', 'Exception en convertirManual: ' . $e->getMessage());
            log_message('error', 'Archivo: ' . $e->getFile() . ' Línea: ' . $e->getLine());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            throw $e; // Propagar la excepción
        }
    }
    
    /**
     * Conversión manual simple de XLSX a CSV
     * Usa ZIP para extraer datos del archivo XLSX
     */
    private function convertirXlsxManual($filepath) {
        try {
            log_message('info', '=== INICIANDO convertirXlsxManual ===');
            
            // Un archivo XLSX es un ZIP que contiene XML
            $zip = new ZipArchive();
            if ($zip->open($filepath) !== TRUE) {
                log_message('error', 'Cannot open XLSX file as ZIP');
                return false;
            }
            
            // Buscar el archivo sharedStrings.xml
            $sharedStrings = '';
            for ($i = 0; $i < $zip->numFiles; $i++) {
                $filename = $zip->getNameIndex($i);
                if (strpos($filename, 'sharedStrings.xml') !== false) {
                    $sharedStrings = $zip->getFromIndex($i);
                    break;
                }
            }
            
            // Buscar el archivo sheet1.xml
            $worksheet = '';
            for ($i = 0; $i < $zip->numFiles; $i++) {
                $filename = $zip->getNameIndex($i);
                if (strpos($filename, 'sheet1.xml') !== false) {
                    $worksheet = $zip->getFromIndex($i);
                    break;
                }
            }
            
            $zip->close();
            
            if (empty($worksheet)) {
                log_message('error', 'Cannot find sheet1.xml in XLSX file');
                return false;
            }
            
            // Crear archivo CSV
            $csv_filepath = str_replace('.xlsx', '.csv', $filepath);
            $csv_handle = fopen($csv_filepath, 'w');
            
            if (!$csv_handle) {
                log_message('error', 'Cannot create CSV file');
                return false;
            }
            
            // Parsear el XML del worksheet
            $xml = simplexml_load_string($worksheet);
            if (!$xml) {
                log_message('error', 'Cannot parse worksheet XML');
                fclose($csv_handle);
                return false;
            }
            
            // Extraer datos de las celdas
            $rows = [];
            $maxCol = 0;
            
            foreach ($xml->sheetData->row as $row) {
                $rowData = [];
                $rowNum = (int)$row['r'];
                
                foreach ($row->c as $cell) {
                    $col = $this->getColumnIndex($cell['r']);
                    $value = '';
                    
                    if (isset($cell->v)) {
                        $value = (string)$cell->v;
                        
                        // Si es un string compartido, buscar en sharedStrings
                        if (isset($cell['t']) && $cell['t'] == 's' && !empty($sharedStrings)) {
                            $sharedXml = simplexml_load_string($sharedStrings);
                            if ($sharedXml && isset($sharedXml->si[(int)$value])) {
                                $value = (string)$sharedXml->si[(int)$value]->t;
                            }
                        }
                    }
                    
                    $rowData[$col] = $value;
                    $maxCol = max($maxCol, $col);
                }
                
                $rows[$rowNum] = $rowData;
            }
            
            // Escribir CSV
            for ($row = 1; $row <= count($rows); $row++) {
                $csvRow = [];
                for ($col = 0; $col <= $maxCol; $col++) {
                    $value = isset($rows[$row][$col]) ? $rows[$row][$col] : '';
                    $csvRow[] = '"' . str_replace('"', '""', $value) . '"';
                }
                fwrite($csv_handle, implode(',', $csvRow) . "\n");
            }
            
            fclose($csv_handle);
            
            log_message('info', 'XLSX converted to CSV manually: ' . $csv_filepath);
            return $csv_filepath;
            
        } catch (Exception $e) {
            log_message('error', 'Exception en convertirXlsxManual: ' . $e->getMessage());
            log_message('error', 'Archivo: ' . $e->getFile() . ' Línea: ' . $e->getLine());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            throw $e; // Propagar la excepción
        }
    }
    
    /**
     * Conversión manual simple de XLS a CSV
     * Para archivos .xls (formato binario) - implementación básica
     */
    private function convertirXlsManual($filepath) {
        try {
            log_message('info', '=== INICIANDO convertirXlsManual ===');
            log_message('warning', 'XLS manual conversion not implemented, using basic approach');
            
            // Para XLS, simplemente crear un CSV vacío con mensaje
            $csv_filepath = str_replace('.xls', '.csv', $filepath);
            $csv_handle = fopen($csv_filepath, 'w');
            
            if (!$csv_handle) {
                log_message('error', 'Cannot create CSV file');
                return false;
            }
            
            // Escribir mensaje de error
            fwrite($csv_handle, '"Error","XLS files require PhpSpreadsheet for proper conversion"\n');
            fwrite($csv_handle, '"Please","Use XLSX format instead"\n');
            
            fclose($csv_handle);
            
            log_message('warning', 'XLS file converted with error message: ' . $csv_filepath);
            return $csv_filepath;
            
        } catch (Exception $e) {
            log_message('error', 'Exception en convertirXlsManual: ' . $e->getMessage());
            log_message('error', 'Archivo: ' . $e->getFile() . ' Línea: ' . $e->getLine());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            throw $e; // Propagar la excepción
        }
    }
    
    /**
     * Convierte referencia de celda (ej: A1, B2) a índice numérico
     */
    private function getColumnIndex($cellRef) {
        $col = '';
        $row = '';
        
        for ($i = 0; $i < strlen($cellRef); $i++) {
            if (is_numeric($cellRef[$i])) {
                $row .= $cellRef[$i];
            } else {
                $col .= $cellRef[$i];
            }
        }
        
        $colIndex = 0;
        for ($i = 0; $i < strlen($col); $i++) {
            $colIndex = $colIndex * 26 + (ord($col[$i]) - ord('A') + 1);
        }
        
        return $colIndex - 1; // Convertir a índice base 0
    }

    /**
     * Obtiene el ID de empresa de la sesión
     * 
     * @return int|false ID de empresa o false si hay error
     */

    /**
     * Envía el archivo al dataservice de WSO2
     * 
     * @param string $csv_filepath Ruta del archivo CSV
     * @param string $stored_procedure Nombre del stored procedure
     * @param int $empr_id ID de empresa
     * @return array|false Resultado del procesamiento
     */

    /**
     * Limpia archivos temporales
     * 
     * @param string $excel_filepath Ruta del archivo Excel
     * @param string $csv_filepath Ruta del archivo CSV
     */
    private function limpiarArchivosTemporales($excel_filepath, $csv_filepath) {
        try {
            // Eliminar archivo Excel
            if (file_exists($excel_filepath)) {
                unlink($excel_filepath);
                log_message('info', 'Excel file cleaned: ' . $excel_filepath);
            }
            
            // Eliminar archivo CSV
            if (file_exists($csv_filepath)) {
                unlink($csv_filepath);
                log_message('info', 'CSV file cleaned: ' . $csv_filepath);
            }
            
        } catch (Exception $e) {
            log_message('error', 'Exception en limpiarArchivosTemporales: ' . $e->getMessage());
            log_message('error', 'Archivo: ' . $e->getFile() . ' Línea: ' . $e->getLine());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            throw $e; // Propagar la excepción
        }
    }

    /**
     * Muestra el resultado del procesamiento de carga masiva
     * 
     * @param array $resultado Resultado del procesamiento
     * @return void
     */
    private function mostrarResultado($resultado) {
        try {
            log_message('info', 'Mostrando resultado de carga masiva');
            
            // Preparar datos para la vista (igual que en index)
            $data = $this->session->userdata();
            $data['title'] = 'Resultado de Carga Masiva';
            $data['resultado'] = $resultado;
            $data['user_model'] = $this->user_model;
            $data['userlevel'] = $this->userlevel;
            
            // Agregar datos necesarios para el navbar
            log_message('info', 'Cargando datos adicionales para navbar en resultado...');
            $this->load->model('Roles');
            $data['usersList'] = $this->user_model->getListUserData();
            $data['emp_connect'] = $this->user_model->gestMembershipsUserInfo($data['email'], 1);
            $data['groups'] = $this->Roles->getBpmGroups();
            
            log_message('info', 'Datos de navbar cargados para resultado:');
            log_message('debug', 'usersList count: ' . (is_array($data['usersList']) ? count($data['usersList']) : 'no es array'));
            log_message('debug', 'emp_connect count: ' . (is_array($data['emp_connect']) ? count($data['emp_connect']) : 'no es array'));
            log_message('debug', 'groups count: ' . (is_array($data['groups']) ? count($data['groups']) : 'no es array'));
            
            // Cargar vistas con navbar
            $this->load->view('header', $data);
            $this->load->view('navbar', $data);
            $this->load->view('bulkload/resultado', $data);
            $this->load->view('footer');
            
        } catch (Exception $e) {
            log_message('error', 'Error al mostrar resultado: ' . $e->getMessage());
            $this->session->set_flashdata('error_message', 'Error al mostrar resultado: ' . $e->getMessage());
            redirect('bulkload');
        }
    }

    /**
     * Descarga el template de una entidad de negocio
     * 
     * @param int $id ID de la entidad de negocio
     */
    public function descargarTemplate($id) {
        try {
            log_message('info', 'Downloading template for entity ID: ' . $id);
            
            // Obtener entidades de negocio
            $entidades = $this->bulkloads->obtenerEntidadesNegocio();
            
            if ($entidades === false) {
                show_error('Error al obtener entidades de negocio', 500);
                return;
            }
            
            // Buscar la entidad específica por índice (ya que el frontend envía el índice)
            $entidad = null;
            if (isset($entidades[$id]) && is_array($entidades[$id])) {
                $entidad = $entidades[$id];
            }
            
            if ($entidad === null) {
                show_error('Entidad de negocio no encontrada', 404);
                return;
            }
            
            $template_url = $entidad['template'];
            log_message('debug', 'Template URL: ' . $template_url);
            
            // Verificar que la URL no esté vacía
            if (empty($template_url)) {
                show_error('No hay template disponible para esta entidad', 404);
                return;
            }
            
            // Descargar el archivo desde la URL
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $template_url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');
            
            $template_content = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curl_error = curl_error($ch);
            curl_close($ch);
            
            if ($curl_error) {
                log_message('error', 'cURL Error downloading template: ' . $curl_error);
                show_error('Error al descargar el template: ' . $curl_error, 500);
                return;
            }
            
            if ($http_code !== 200) {
                log_message('error', 'HTTP Error downloading template: ' . $http_code);
                show_error('Error al descargar el template. Código HTTP: ' . $http_code, 500);
                return;
            }
            
            if (empty($template_content)) {
                show_error('El template está vacío', 404);
                return;
            }
            
            // Determinar el tipo de archivo y extensión
            $file_extension = 'txt';
            $content_type = 'text/plain';
            
            // Verificar si es un archivo Excel
            if (strpos($template_url, '.xlsx') !== false || strpos($template_url, '.xls') !== false) {
                $file_extension = pathinfo(parse_url($template_url, PHP_URL_PATH), PATHINFO_EXTENSION);
                $content_type = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
            } elseif (strpos($template_url, '.csv') !== false) {
                $file_extension = 'csv';
                $content_type = 'text/csv';
            }
            
            // Generar nombre del archivo
            $filename = 'template_' . strtolower(str_replace(' ', '_', $entidad['nombre'])) . '.' . $file_extension;
            
            // Configurar headers para descarga
            header('Content-Type: ' . $content_type . '; charset=utf-8');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Content-Length: ' . strlen($template_content));
            
            echo $template_content;
            
            log_message('info', 'Template downloaded successfully: ' . $filename . ' from URL: ' . $template_url);
            
        } catch (Exception $e) {
            log_message('error', 'Exception en descargarTemplate: ' . $e->getMessage());
            log_message('error', 'Archivo: ' . $e->getFile() . ' Línea: ' . $e->getLine());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            show_error('Error interno del sistema: ' . $e->getMessage(), 500);
        }
    }
}
