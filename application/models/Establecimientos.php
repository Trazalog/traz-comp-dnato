<?php if (!defined('BASEPATH')) { exit('No direct script access allowed'); }

/**
 * Wrapper de las operaciones de Establecimientos / Depósitos / Encargados
 * expuestas directamente por el DataService WSO2 COREDataService.
 *
 * Endpoints consumidos (ver development/ToolsAPIProject_1.0.0/COREDataService_1.0.0/COREDataService-1.0.0.dbs):
 *  - POST /establecimiento            -> setEstablecimiento (devuelve {"respuesta":{"esta_id":...}})
 *  - POST /deposito/establecimiento   -> setDepositoPorEstablecimiento (devuelve {"respuesta":{"depo_id":...}})
 *  - POST /deposito/encargado         -> setEncargadoDeposito (sin body)
 *  - DELETE /deposito                 -> deleteDeposito (rollback parcial)
 *  - DELETE /establecimiento          -> deleteEstablecimiento (soft delete, rollback parcial)
 *  - DELETE /deposito/encargado       -> deleteDepositoEncargados (rollback parcial)
 */
class Establecimientos extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('REST');
    }

    /**
     * Inserta un establecimiento y devuelve el esta_id recién generado.
     *
     * @param array $data keys: nombre, calle, altura, pais, estado, localidad, empr_id
     * @return array{ok:bool, esta_id:?string, code:int, body:string, message:string}
     */
    public function crearEstablecimiento(array $data)
    {
        $payload = array(
            '_post_establecimiento' => array(
                'nombre'     => isset($data['nombre']) ? (string) $data['nombre'] : '',
                'calle'      => isset($data['calle']) ? (string) $data['calle'] : '',
                'altura'     => isset($data['altura']) ? (string) $data['altura'] : '',
                'pais'       => isset($data['pais']) ? (string) $data['pais'] : '',
                'estado'     => isset($data['estado']) ? (string) $data['estado'] : '',
                'localidad'  => isset($data['localidad']) ? (string) $data['localidad'] : '',
                'empr_id'    => isset($data['empr_id']) ? (string) $data['empr_id'] : '',
            )
        );

        $url = COREDataService_URL . '/establecimiento';
        log_message('INFO', '#TRAZA|ESTABLECIMIENTOS|crearEstablecimiento() >> POST ' . $url . ' payload=' . json_encode($payload));
        $res = $this->rest->callAPI('POST', $url, $payload);
        return $this->procesarRespuestaSimple($res, 'esta_id', 'Error creando establecimiento');
    }

    /**
     * Inserta un depósito y devuelve el depo_id recién generado.
     *
     * @param array $data keys: descripcion, nombre, empr_id, esta_id
     * @return array{ok:bool, depo_id:?string, code:int, body:string, message:string}
     */
    public function crearDeposito(array $data)
    {
        $payload = array(
            '_post_deposito_establecimiento' => array(
                'descripcion' => isset($data['descripcion']) ? (string) $data['descripcion'] : '',
                'nombre'      => isset($data['nombre']) ? (string) $data['nombre'] : '',
                'empr_id'     => isset($data['empr_id']) ? (string) $data['empr_id'] : '',
                'esta_id'     => isset($data['esta_id']) ? (string) $data['esta_id'] : '',
            )
        );

        $url = COREDataService_URL . '/deposito/establecimiento';
        log_message('INFO', '#TRAZA|ESTABLECIMIENTOS|crearDeposito() >> POST ' . $url . ' payload=' . json_encode($payload));
        $res = $this->rest->callAPI('POST', $url, $payload);
        return $this->procesarRespuestaSimple($res, 'depo_id', 'Error creando depósito');
    }

    /**
     * Asigna (inserta en core.encargados_depositos) un user_id como encargado del depósito.
     *
     * @param string|int $depoId
     * @param string|int $userId
     * @return array{ok:bool, code:int, body:string, message:string}
     */
    public function asignarEncargadoDeposito($depoId, $userId)
    {
        $depoId = trim((string) $depoId);
        $userId = trim((string) $userId);
        if ($depoId === '' || $userId === '') {
            return array(
                'ok'      => false,
                'code'    => 0,
                'body'    => '',
                'message' => 'asignarEncargadoDeposito: depo_id o user_id vacío (depo=' . $depoId . ', user=' . $userId . ')',
            );
        }

        $payload = array(
            '_post_deposito_encargado' => array(
                'depo_id' => $depoId,
                'user_id' => $userId,
            )
        );
        $url = COREDataService_URL . '/deposito/encargado';
        log_message('INFO', '#TRAZA|ESTABLECIMIENTOS|asignarEncargadoDeposito() >> POST ' . $url . ' payload=' . json_encode($payload));
        $res = $this->rest->callAPI('POST', $url, $payload);

        $code = isset($res['code']) ? (int) $res['code'] : 0;
        $body = isset($res['data']) ? (string) $res['data'] : '';
        $ok = !empty($res['status']) && $code >= 200 && $code < 300;
        return array(
            'ok'      => $ok,
            'code'    => $code,
            'body'    => $body,
            'message' => $ok ? '' : ('Error asignando encargado (HTTP ' . $code . '): ' . substr($body, 0, 300)),
        );
    }

    /**
     * Rollback: borra el registro de establecimiento (soft delete: set eliminado=true).
     *
     * @param string|int $estaId
     * @return bool
     */
    public function eliminarEstablecimiento($estaId)
    {
        $estaId = trim((string) $estaId);
        if ($estaId === '') {
            return false;
        }
        $payload = array(
            '_delete_establecimiento' => array('esta_id' => $estaId)
        );
        $url = COREDataService_URL . '/establecimiento';
        log_message('INFO', '#TRAZA|ESTABLECIMIENTOS|eliminarEstablecimiento() >> DELETE ' . $url . ' esta_id=' . $estaId);
        $res = $this->rest->callAPI('DELETE', $url, $payload);
        return !empty($res['status']) && (isset($res['code']) ? $res['code'] : 0) >= 200 && (isset($res['code']) ? $res['code'] : 0) < 300;
    }

    /**
     * Rollback: borra físicamente el depósito y, si existen, sus encargados.
     *
     * @param string|int $depoId
     * @return bool
     */
    public function eliminarDeposito($depoId)
    {
        $depoId = trim((string) $depoId);
        if ($depoId === '') {
            return false;
        }

        // Primero encargados (FK) y luego el depósito.
        $urlEnc = COREDataService_URL . '/deposito/encargado';
        $this->rest->callAPI('DELETE', $urlEnc, array(
            '_delete_deposito_encargado' => array('depo_id' => $depoId)
        ));

        $url = COREDataService_URL . '/deposito';
        log_message('INFO', '#TRAZA|ESTABLECIMIENTOS|eliminarDeposito() >> DELETE ' . $url . ' depo_id=' . $depoId);
        $res = $this->rest->callAPI('DELETE', $url, array(
            '_delete_deposito' => array('depo_id' => $depoId)
        ));
        return !empty($res['status']) && (isset($res['code']) ? $res['code'] : 0) >= 200 && (isset($res['code']) ? $res['code'] : 0) < 300;
    }

    /**
     * Normaliza respuestas tipo {"respuesta":{"<idField>":"..."}} validando HTTP y extrayendo el id.
     *
     * @param array|false $res      retorno de REST::callAPI
     * @param string      $idField  "esta_id" | "depo_id"
     * @param string      $errLabel etiqueta para mensajes de error
     * @return array{ok:bool, code:int, body:string, message:string}&array<string, mixed>
     */
    private function procesarRespuestaSimple($res, $idField, $errLabel)
    {
        $code = is_array($res) && isset($res['code']) ? (int) $res['code'] : 0;
        $body = is_array($res) && isset($res['data']) ? (string) $res['data'] : '';
        $out = array(
            'ok'    => false,
            $idField => null,
            'code'  => $code,
            'body'  => $body,
            'message' => '',
        );

        if (!is_array($res) || empty($res['status']) || $code < 200 || $code >= 300) {
            $out['message'] = $errLabel . ' (HTTP ' . $code . '): ' . substr($body, 0, 300);
            return $out;
        }

        $decoded = json_decode($body);
        if ($decoded && isset($decoded->respuesta->{$idField})) {
            $id = trim((string) $decoded->respuesta->{$idField});
            if ($id !== '') {
                $out['ok'] = true;
                $out[$idField] = $id;
                return $out;
            }
        }

        $out['message'] = $errLabel . ': respuesta sin ' . $idField . ' (body=' . substr($body, 0, 300) . ')';
        return $out;
    }
}
