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
            log_message('info', 'Bulkload index method called');
            
            // Obtener entidades de negocio desde WSO2
            $entidades = $this->bulkloads->obtenerEntidadesNegocio();
            
            if ($entidades === false) {
                log_message('warning', 'WSO2 not available, using test data');
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
            }
            
            // Preparar datos para la vista (igual que Main)
            $data = $this->session->userdata();
            $data['title'] = 'Carga Masiva de Datos';
            $data['entidades'] = $entidades;
            $data['user_model'] = $this->user_model;
            $data['userlevel'] = $this->userlevel;
            
            log_message('debug', 'Bulkload index data prepared: ' . json_encode($data));
            
            // Cargar vistas
            $this->load->view('header', $data);
            $this->load->view('navbar', $data);
            $this->load->view('bulkload/index', $data);
            $this->load->view('footer');
            
        } catch (Exception $e) {
            log_message('error', 'Error in bulkload index: ' . $e->getMessage());
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
            log_message('info', 'Bulkload process started');
            log_message('debug', 'POST data received: ' . json_encode($this->input->post()));
            log_message('debug', 'FILES data received: ' . json_encode($_FILES));
            
            // Validar entrada
            $this->form_validation->set_rules('entidad_negocio', 'Entidad de Negocio', 'required');
            
            if (!$this->form_validation->run()) {
                $this->session->set_flashdata('error_message', validation_errors());
                $this->session->set_userdata('selected_entidad', $this->input->post('entidad_negocio'));
                redirect('bulkload');
                return;
            }
            
            // Obtener datos del formulario
            $entidad_negocio = $this->input->post('entidad_negocio');
            $stored_procedure = $this->input->post('stored_procedure');
            
            log_message('debug', 'Processing bulkload for entity: ' . $entidad_negocio);
            log_message('debug', 'Stored procedure: ' . $stored_procedure);
            
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
            $filename = 'bulkload_rodo.' . $extension;
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
            $csv_filepath = $this->convertirExcelACsv($filepath, $extension);
            
            if ($csv_filepath === false) {
                $this->session->set_flashdata('error_message', 'Error al convertir el archivo Excel a CSV. Verifique que el archivo sea válido.');
                redirect('bulkload');
                return;
            }
            
            log_message('info', 'Excel converted to CSV: ' . $csv_filepath);
            
            // Obtener empr_id de la sesión
            $empr_id = $this->bulkloads->obtenerEmpresaId();
            
            if ($empr_id === false) {
                $this->session->set_flashdata('error_message', 'Error al obtener información de la empresa. Verifique su sesión.');
                redirect('bulkload');
                return;
            }
            
            log_message('debug', 'Company ID from session: ' . $empr_id);
            
            // Enviar archivo al dataservice
            $resultado = $this->bulkloads->enviarADataservice($csv_filepath, $stored_procedure, $empr_id);
            
            if ($resultado === false) {
                $this->session->set_flashdata('error_message', 'Error al procesar el archivo en el sistema. Intente nuevamente.');
                redirect('bulkload');
                return;
            }
            
            // Procesar resultado
            if ($resultado['success']) {
                $this->session->set_flashdata('success_message', 'Carga masiva realizada exitosamente. ' . $resultado['output']);
                log_message('info', 'Bulkload completed successfully: ' . $resultado['output']);
            } else {
                $this->session->set_flashdata('error_message', 'Error en la carga masiva: ' . $resultado['output']);
                log_message('error', 'Bulkload failed: ' . $resultado['output']);
            }
            
            // Limpiar archivos temporales
            $this->limpiarArchivosTemporales($filepath, $csv_filepath);
            
            // Limpiar entidad seleccionada de la sesión
            $this->session->unset_userdata('selected_entidad');
            
            redirect('bulkload');
            
            } catch (Exception $e) {
                log_message('error', 'Exception in procesarCarga: ' . $e->getMessage());
                $this->session->set_flashdata('error_message', 'Error interno del sistema. Contacte al administrador.');
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
            
            // Verificar si SimpleXLSX está disponible
            $simplexlsx_available = class_exists('Shuchkin\SimpleXLSX');
            log_message('debug', 'SimpleXLSX available: ' . ($simplexlsx_available ? 'YES' : 'NO'));
            
            if ($simplexlsx_available) {
                log_message('debug', 'Using SimpleXLSX for conversion');
                return $this->convertirConSimpleXLSX($filepath, $extension);
            } else {
                log_message('warning', 'SimpleXLSX not available, using manual conversion');
                return $this->convertirManual($filepath, $extension);
            }
            
        } catch (Exception $e) {
            log_message('error', 'Exception in convertirExcelACsv: ' . $e->getMessage());
            return false;
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
            $xlsx = \Shuchkin\SimpleXLSX::parse($filepath);
            if (!$xlsx) {
                log_message('error', 'Failed to parse XLSX file: ' . \Shuchkin\SimpleXLSX::parseError());
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
            log_message('error', 'Exception in convertirConSimpleXLSX: ' . $e->getMessage());
            return false;
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
            log_message('error', 'Exception in convertirManual: ' . $e->getMessage());
            return false;
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
            log_message('error', 'Exception in convertirXlsxManual: ' . $e->getMessage());
            return false;
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
            log_message('error', 'Exception in convertirXlsManual: ' . $e->getMessage());
            return false;
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
            log_message('error', 'Exception in limpiarArchivosTemporales: ' . $e->getMessage());
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
            
            log_message('debug', 'Template found for entity: ' . $entidad['nombre']);
            
            // Preparar respuesta para descarga
            $filename = 'template_' . strtolower(str_replace(' ', '_', $entidad['nombre'])) . '.txt';
            
            header('Content-Type: text/plain; charset=utf-8');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Content-Length: ' . strlen($entidad['template']));
            
            echo $entidad['template'];
            
            log_message('info', 'Template downloaded successfully: ' . $filename);
            
        } catch (Exception $e) {
            log_message('error', 'Exception in descargarTemplate: ' . $e->getMessage());
            show_error('Error interno del sistema', 500);
        }
    }
}
