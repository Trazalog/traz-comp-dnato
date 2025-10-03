<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Modelo para la funcionalidad de Carga Masiva
 * Maneja operaciones de base de datos relacionadas con carga masiva
 * 
 * @author Trazalog Tools Team
 * @version 1.0
 * @since 2024-01-XX
 */
class Bulkload_model extends CI_Model {

    /**
     * Constructor del modelo
     */
    public function __construct() {
        parent::__construct();
        log_message('info', 'Bulkload model initialized');
    }

    /**
     * Obtiene información de empresa por usuario
     * 
     * @param string $email Email del usuario
     * @return array|false Información de empresa o false si hay error
     */
    public function obtenerEmpresaPorUsuario($email) {
        try {
            log_message('info', 'Getting company info for user: ' . $email);
            
            // Consulta para obtener información de empresa del usuario
            $this->db->select('u.busines, e.empr_id, e.nombre as empresa_nombre');
            $this->db->from('users u');
            $this->db->join('empresas e', 'e.nombre = u.busines', 'left');
            $this->db->where('u.email', $email);
            $this->db->limit(1);
            
            $query = $this->db->get();
            
            if ($query->num_rows() > 0) {
                $result = $query->row_array();
                log_message('debug', 'Company info retrieved: ' . json_encode($result));
                return $result;
            } else {
                log_message('warning', 'No company info found for user: ' . $email);
                return false;
            }
            
        } catch (Exception $e) {
            log_message('error', 'Exception in obtenerEmpresaPorUsuario: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Registra el log de carga masiva
     * 
     * @param array $data Datos del log
     * @return bool True si se registró correctamente
     */
    public function registrarLogCarga($data) {
        try {
            log_message('info', 'Registering bulkload log');
            
            $log_data = array(
                'usuario_email' => $data['usuario_email'],
                'entidad_negocio' => $data['entidad_negocio'],
                'archivo_original' => $data['archivo_original'],
                'archivo_procesado' => $data['archivo_procesado'],
                'stored_procedure' => $data['stored_procedure'],
                'empr_id' => $data['empr_id'],
                'estado' => $data['estado'],
                'mensaje' => $data['mensaje'],
                'fecha_procesamiento' => date('Y-m-d H:i:s'),
                'ip_address' => $this->input->ip_address()
            );
            
            $result = $this->db->insert('bulkload_logs', $log_data);
            
            if ($result) {
                log_message('info', 'Bulkload log registered successfully');
                return true;
            } else {
                log_message('error', 'Failed to register bulkload log');
                return false;
            }
            
        } catch (Exception $e) {
            log_message('error', 'Exception in registrarLogCarga: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtiene el historial de cargas masivas para un usuario
     * 
     * @param string $email Email del usuario
     * @param int $limit Límite de registros a retornar
     * @return array|false Array de logs o false si hay error
     */
    public function obtenerHistorialCargas($email, $limit = 50) {
        try {
            log_message('info', 'Getting bulkload history for user: ' . $email);
            
            $this->db->select('*');
            $this->db->from('bulkload_logs');
            $this->db->where('usuario_email', $email);
            $this->db->order_by('fecha_procesamiento', 'DESC');
            $this->db->limit($limit);
            
            $query = $this->db->get();
            
            if ($query->num_rows() > 0) {
                $result = $query->result_array();
                log_message('debug', 'Retrieved ' . count($result) . ' bulkload history records');
                return $result;
            } else {
                log_message('info', 'No bulkload history found for user: ' . $email);
                return array();
            }
            
        } catch (Exception $e) {
            log_message('error', 'Exception in obtenerHistorialCargas: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Verifica si un usuario tiene permisos para realizar carga masiva
     * 
     * @param string $email Email del usuario
     * @param string $entidad_negocio Entidad de negocio a cargar
     * @return bool True si tiene permisos
     */
    public function verificarPermisosCarga($email, $entidad_negocio) {
        try {
            log_message('info', 'Checking bulkload permissions for user: ' . $email . ' and entity: ' . $entidad_negocio);
            
            // Obtener información del usuario
            $this->db->select('u.role, u.busines');
            $this->db->from('users u');
            $this->db->where('u.email', $email);
            $this->db->limit(1);
            
            $query = $this->db->get();
            
            if ($query->num_rows() > 0) {
                $user = $query->row_array();
                
                // Verificar si es administrador (role = 1)
                if ($user['role'] == 1) {
                    log_message('debug', 'User has admin role, bulkload allowed');
                    return true;
                }
                
                // Verificar permisos específicos por empresa y entidad
                $this->db->select('*');
                $this->db->from('bulkload_permisos');
                $this->db->where('empresa', $user['busines']);
                $this->db->where('entidad_negocio', $entidad_negocio);
                $this->db->where('usuario_email', $email);
                $this->db->where('activo', 1);
                $this->db->limit(1);
                
                $permiso_query = $this->db->get();
                
                if ($permiso_query->num_rows() > 0) {
                    log_message('debug', 'User has specific bulkload permissions');
                    return true;
                }
                
                log_message('warning', 'User does not have bulkload permissions');
                return false;
            } else {
                log_message('warning', 'User not found: ' . $email);
                return false;
            }
            
        } catch (Exception $e) {
            log_message('error', 'Exception in verificarPermisosCarga: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtiene estadísticas de carga masiva para un usuario
     * 
     * @param string $email Email del usuario
     * @return array|false Estadísticas o false si hay error
     */
    public function obtenerEstadisticasCarga($email) {
        try {
            log_message('info', 'Getting bulkload statistics for user: ' . $email);
            
            $this->db->select('
                COUNT(*) as total_cargas,
                SUM(CASE WHEN estado = "exitoso" THEN 1 ELSE 0 END) as cargas_exitosas,
                SUM(CASE WHEN estado = "error" THEN 1 ELSE 0 END) as cargas_fallidas,
                MAX(fecha_procesamiento) as ultima_carga
            ');
            $this->db->from('bulkload_logs');
            $this->db->where('usuario_email', $email);
            
            $query = $this->db->get();
            
            if ($query->num_rows() > 0) {
                $result = $query->row_array();
                log_message('debug', 'Bulkload statistics retrieved: ' . json_encode($result));
                return $result;
            } else {
                log_message('info', 'No bulkload statistics found for user: ' . $email);
                return array(
                    'total_cargas' => 0,
                    'cargas_exitosas' => 0,
                    'cargas_fallidas' => 0,
                    'ultima_carga' => null
                );
            }
            
        } catch (Exception $e) {
            log_message('error', 'Exception in obtenerEstadisticasCarga: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Limpia logs antiguos de carga masiva
     * 
     * @param int $dias_antiguedad Días de antigüedad para limpiar
     * @return int|false Número de registros eliminados o false si hay error
     */
    public function limpiarLogsAntiguos($dias_antiguedad = 90) {
        try {
            log_message('info', 'Cleaning old bulkload logs older than ' . $dias_antiguedad . ' days');
            
            $fecha_limite = date('Y-m-d H:i:s', strtotime('-' . $dias_antiguedad . ' days'));
            
            $this->db->where('fecha_procesamiento <', $fecha_limite);
            $this->db->delete('bulkload_logs');
            
            $affected_rows = $this->db->affected_rows();
            
            log_message('info', 'Cleaned ' . $affected_rows . ' old bulkload log records');
            return $affected_rows;
            
        } catch (Exception $e) {
            log_message('error', 'Exception in limpiarLogsAntiguos: ' . $e->getMessage());
            return false;
        }
    }
}






