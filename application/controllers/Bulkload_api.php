<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Bulkload_api — la misma Carga Masiva de la pantalla, sin sesión de pantalla.
 *
 * Para qué: Kuntur, el agente de puesta en marcha, le arma al administrador el
 * archivo de la plantilla a partir de su planilla. Cuando el administrador toca
 * "Cargar", el motor de los agentes llama acá con EL TOKEN DE ESE
 * ADMINISTRADOR (ADR-A12). No es una carga distinta: después de validar, llama
 * a Bulkloads::enviarADataservice() tal como lo hace Bulkload::procesarCarga(),
 * así que vale todo lo de la pantalla (motor PostgreSQL o MariaDB, validaciones,
 * todo o nada). Bulkload.php y Bulkloads.php no se modifican.
 *
 *   POST /bulkload_api/cargar
 *   Authorization: Bearer <JWT emitido por este Dnato>
 *   Content-Type: application/json
 *   {"entidad": "Articulos", "csv": "codigo,descripcion,...\n..."}
 *
 * Respuestas (JSON):
 *   200 {"success": true|false, "output": "..."}  la carga corrió; success dice si grabó
 *   400 parámetros inválidos      401 sin token o token inválido
 *   403 token de máquina o usuario que no es administrador
 *   405 método distinto de POST   500 error interno
 *
 * Reglas:
 *   - La empresa sale SOLO del token (claim empr_id); el cuerpo no la trae.
 *   - Token de máquina, nunca (ADR-A10): un agente solo graba con el token de
 *     una persona que confirmó.
 *   - Solo administradores de Dnato (role = 1), igual que el acceso a Kuntur.
 *   - Solo las entidades de ENTIDADES_KUNTUR, y el procedimiento sale del
 *     catálogo sta.entidades_negocio: el pedido nunca nombra un procedimiento.
 *
 * AGENTE-CO-09 — traz-tools/doc/agente/analisis-impacto-kuntur-configuracion.md §3.2
 */
class Bulkload_api extends CI_Controller {

    /** Rol de administrador de Dnato (seg.roles). Mismo valor que usa el agente. */
    const ROL_ADMIN = '1';

    /** Lo que Kuntur puede cargar: nombres de sta.entidades_negocio. */
    private static $ENTIDADES_KUNTUR = array(
        'Articulos',
        'Herramientas',
        'Mantenimiento Equipos',
        'Mantenimiento Articulos',
    );

    /** Límites del archivo: una puesta en marcha, no una migración. */
    const MAX_BYTES = 2097152;   // 2 MB
    const MAX_FILAS = 5000;

    public function __construct() {
        parent::__construct();
        $this->load->library('JwtVerifier', NULL, 'jwtverifier');
        $this->load->model('Bulkloads', 'bulkloads', TRUE);
    }

    public function cargar() {
        if (strtoupper($this->input->method()) !== 'POST') {
            return $this->_responder(405, array('error' => 'metodo_no_permitido'));
        }

        // 1. Quién llama
        $token = $this->jwtverifier->tokenDelPedido();
        if ($token === '') {
            return $this->_responder(401, array('error' => 'sin_token'));
        }
        try {
            $claims = $this->jwtverifier->verificar($token);
        } catch (Exception $e) {
            log_message('ERROR', '#TRAZA | DNATO | Bulkload_api | cargar() >> token rechazado: ' . $e->getMessage());
            return $this->_responder(401, array('error' => 'token_invalido'));
        }

        $usuario = isset($claims['email']) && $claims['email'] !== '' ? (string) $claims['email']
                 : (isset($claims['sub']) ? (string) $claims['sub'] : '');
        if (isset($claims['token_tipo']) && $claims['token_tipo'] === 'maquina') {
            log_message('ERROR', '#TRAZA | DNATO | Bulkload_api | cargar() >> token de maquina rechazado: ' . $usuario);
            return $this->_responder(403, array('error' => 'forbidden', 'message' => 'Un token de máquina no puede cargar datos'));
        }
        $rol = isset($claims['role']) ? trim((string) $claims['role']) : '';
        if ($rol !== self::ROL_ADMIN) {
            log_message('ERROR', '#TRAZA | DNATO | Bulkload_api | cargar() >> no es administrador: ' . $usuario);
            return $this->_responder(403, array('error' => 'forbidden', 'message' => 'Solo un administrador puede hacer cargas masivas'));
        }
        $empr_id = isset($claims['empr_id']) ? (int) $claims['empr_id'] : 0;
        if ($empr_id <= 0) {
            return $this->_responder(403, array('error' => 'forbidden', 'message' => 'El token no tiene empresa'));
        }

        // 2. Qué pide
        $cuerpo = json_decode((string) $this->input->raw_input_stream, true);
        if (!is_array($cuerpo)) {
            return $this->_invalido('El cuerpo tiene que ser JSON');
        }
        if (array_key_exists('empr_id', $cuerpo) || array_key_exists('stored_procedure', $cuerpo)) {
            return $this->_invalido('La empresa y el procedimiento no se mandan: salen del token y del catálogo');
        }
        $entidad = isset($cuerpo['entidad']) ? trim((string) $cuerpo['entidad']) : '';
        $csv     = isset($cuerpo['csv']) ? (string) $cuerpo['csv'] : '';
        if (!in_array($entidad, self::$ENTIDADES_KUNTUR, true)) {
            return $this->_invalido('entidad tiene que ser una de: ' . implode(', ', self::$ENTIDADES_KUNTUR));
        }
        if (strlen($csv) === 0 || strlen($csv) > self::MAX_BYTES) {
            return $this->_invalido('csv vacío o de más de ' . self::MAX_BYTES . ' bytes');
        }
        if (!mb_check_encoding($csv, 'UTF-8')) {
            return $this->_invalido('csv tiene que estar en UTF-8');
        }
        $lineas = preg_split('/\r\n|\n|\r/', trim($csv));
        $lineas = array_values(array_filter($lineas, function ($l) { return trim($l) !== ''; }));
        if (count($lineas) <= 1) {
            return $this->_invalido('El archivo no tiene filas de datos, solo encabezado');
        }
        if (count($lineas) - 1 > self::MAX_FILAS) {
            return $this->_invalido('Más de ' . self::MAX_FILAS . ' filas');
        }

        $sp = $this->_procedimientoDe($entidad);
        if ($sp === false) {
            return $this->_responder(400, array('error' => 'parametro_invalido',
                'message' => 'La entidad ' . $entidad . ' no está configurada en la Carga Masiva de este ambiente'));
        }

        // 3. El archivo, donde lo deja la pantalla
        $dir = FCPATH . 'bulkload_stage_files';
        if (!is_dir($dir) && !mkdir($dir, 0755, true)) {
            log_message('ERROR', '#TRAZA | DNATO | Bulkload_api | cargar() >> no se pudo crear ' . $dir);
            return $this->_responder(500, array('error' => 'error_interno'));
        }
        $aleatorio = bin2hex(openssl_random_pseudo_bytes(8));
        $ruta = $dir . '/bulkload_kuntur_' . date('Y-m-d_H-i-s') . '_' . $aleatorio . '.csv';
        if (file_put_contents($ruta, implode("\n", $lineas) . "\n") === false) {
            log_message('ERROR', '#TRAZA | DNATO | Bulkload_api | cargar() >> no se pudo escribir ' . $ruta);
            return $this->_responder(500, array('error' => 'error_interno'));
        }
        @chmod($ruta, 0644); // la base lo lee con COPY

        log_message('INFO', '#TRAZA | DNATO | Bulkload_api | cargar() >> ' . $entidad . ' (' . $sp . ') empr_id=' . $empr_id
            . ' filas=' . (count($lineas) - 1) . ' por ' . $usuario);

        // 4. La misma carga que la pantalla
        try {
            $resultado = $this->bulkloads->enviarADataservice($ruta, $sp, $empr_id);
        } catch (Exception $e) {
            @unlink($ruta);
            log_message('ERROR', '#TRAZA | DNATO | Bulkload_api | cargar() >> ' . $e->getMessage());
            return $this->_responder(500, array('error' => 'error_interno'));
        }
        @unlink($ruta);

        if ($resultado === false || !is_array($resultado)) {
            return $this->_responder(500, array('error' => 'error_interno', 'message' => 'La carga no devolvió resultado'));
        }
        return $this->_responder(200, array(
            'success' => !empty($resultado['success']),
            'output'  => isset($resultado['output']) ? (string) $resultado['output'] : '',
            'entidad' => $entidad,
            'filas'   => count($lineas) - 1,
        ));
    }

    /**
     * Procedimiento de la entidad según el catálogo de la Carga Masiva.
     * Misma tabla que lee la pantalla (sta.entidades_negocio).
     *
     * @return string|false
     */
    private function _procedimientoDe($entidad) {
        try {
            $this->load->database();
            $q = $this->db->query('SELECT stored_procedure FROM sta.entidades_negocio WHERE nombre = ? LIMIT 1', array($entidad));
            if ($q && $q->num_rows() > 0) {
                $sp = trim((string) $q->row()->stored_procedure);
                return $sp !== '' ? $sp : false;
            }
        } catch (Exception $e) {
            log_message('ERROR', '#TRAZA | DNATO | Bulkload_api | _procedimientoDe() >> ' . $e->getMessage());
        }
        return false;
    }

    private function _invalido($mensaje) {
        return $this->_responder(400, array('error' => 'parametro_invalido', 'message' => $mensaje));
    }

    private function _responder($status, $cuerpo) {
        $this->output
            ->set_status_header($status)
            ->set_content_type('application/json', 'utf-8')
            ->set_header('Cache-Control: no-store')
            ->set_output(json_encode($cuerpo));
    }
}
