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

        $url = rtrim((string) REST_CORE, '/') . '/establecimiento';
        log_message('INFO', '#TRAZA|ESTABLECIMIENTOS|crearEstablecimiento() >> POST ' . $url . ' payload=' . json_encode($payload));
        $res = $this->rest->callAPI('POST', $url, $payload);
        $out = $this->procesarRespuestaSimple($res, 'esta_id', 'Error creando establecimiento');
        if (!$out['ok'] && $this->puedeIntentarResolverId($res)) {
            $estaId = $this->resolverEstaIdPorConsulta(
                isset($data['empr_id']) ? (string) $data['empr_id'] : '',
                isset($data['nombre']) ? (string) $data['nombre'] : ''
            );
            if ($estaId !== '') {
                log_message('WARNING', '#TRAZA|ESTABLECIMIENTOS|crearEstablecimiento() >> Respuesta sin esta_id; recuperado por GET /establecimientos: esta_id=' . $estaId);
                $out['ok'] = true;
                $out['esta_id'] = $estaId;
                $out['message'] = '';
            }
        }
        return $out;
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

        $url = rtrim((string) REST_CORE, '/') . '/deposito/establecimiento';
        log_message('INFO', '#TRAZA|ESTABLECIMIENTOS|crearDeposito() >> POST ' . $url . ' payload=' . json_encode($payload));
        $res = $this->rest->callAPI('POST', $url, $payload);
        $out = $this->procesarRespuestaSimple($res, 'depo_id', 'Error creando depósito');
        if (!$out['ok'] && $this->puedeIntentarResolverId($res)) {
            $depoId = $this->resolverDepoIdPorConsulta(
                isset($data['esta_id']) ? (string) $data['esta_id'] : '',
                isset($data['empr_id']) ? (string) $data['empr_id'] : '',
                isset($data['nombre']) ? (string) $data['nombre'] : ''
            );
            if ($depoId !== '') {
                log_message('WARNING', '#TRAZA|ESTABLECIMIENTOS|crearDeposito() >> Respuesta sin depo_id; recuperado por GET /depositos/establecimiento: depo_id=' . $depoId);
                $out['ok'] = true;
                $out['depo_id'] = $depoId;
                $out['message'] = '';
            }
        }
        return $out;
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
        $url = rtrim((string) REST_CORE, '/') . '/deposito/encargado';
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
        $url = rtrim((string) REST_CORE, '/') . '/establecimiento';
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
        $urlEnc = rtrim((string) REST_CORE, '/') . '/deposito/encargado';
        $this->rest->callAPI('DELETE', $urlEnc, array(
            '_delete_deposito_encargado' => array('depo_id' => $depoId)
        ));

        $url = rtrim((string) REST_CORE, '/') . '/deposito';
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

    /**
     * Cuando el DataService responde 2xx pero body vacío/sin GeneratedKeys,
     * intentamos resolver id por endpoint de lectura.
     *
     * @param array|false $res
     * @return bool
     */
    private function puedeIntentarResolverId($res)
    {
        if (!is_array($res) || empty($res['status'])) {
            return false;
        }
        $code = isset($res['code']) ? (int) $res['code'] : 0;
        if ($code < 200 || $code >= 300) {
            return false;
        }
        $body = isset($res['data']) ? trim((string) $res['data']) : '';
        return ($body === '' || $body === 'false' || $body === 'null');
    }

    /**
     * Busca el establecimiento recién creado por empr_id + nombre.
     *
     * @param string $emprId
     * @param string $nombre
     * @return string esta_id o ''
     */
    private function resolverEstaIdPorConsulta($emprId, $nombre)
    {
        $emprId = trim((string) $emprId);
        if ($emprId === '') {
            return '';
        }
        $url = rtrim((string) REST_CORE, '/') . '/establecimientos/' . rawurlencode($emprId);
        $res = $this->rest->callAPI('GET', $url);
        if (!is_array($res) || empty($res['status'])) {
            return '';
        }
        $decoded = json_decode(isset($res['data']) ? (string) $res['data'] : '');
        if (!$decoded || !isset($decoded->establecimientos->establecimiento)) {
            return '';
        }
        $items = $this->normalizarLista($decoded->establecimientos->establecimiento);
        $targetNombre = trim((string) $nombre);
        $best = '';
        foreach ($items as $it) {
            if (!is_object($it) || !isset($it->esta_id)) {
                continue;
            }
            if ($targetNombre !== '' && isset($it->nombre) && trim((string) $it->nombre) !== $targetNombre) {
                continue;
            }
            $cand = trim((string) $it->esta_id);
            if ($cand !== '' && ((int) $cand >= (int) $best)) {
                $best = $cand;
            }
        }
        return $best;
    }

    /**
     * Busca el depósito recién creado por esta_id + empr_id + nombre.
     *
     * @param string $estaId
     * @param string $emprId
     * @param string $nombre
     * @return string depo_id o ''
     */
    private function resolverDepoIdPorConsulta($estaId, $emprId, $nombre)
    {
        $estaId = trim((string) $estaId);
        $emprId = trim((string) $emprId);
        if ($estaId === '' || $emprId === '') {
            return '';
        }
        $url = rtrim((string) REST_CORE, '/') . '/depositos/establecimiento/' . rawurlencode($estaId) . '/empresa/' . rawurlencode($emprId);
        $res = $this->rest->callAPI('GET', $url);
        if (!is_array($res) || empty($res['status'])) {
            return '';
        }
        $decoded = json_decode(isset($res['data']) ? (string) $res['data'] : '');
        if (!$decoded || !isset($decoded->depositos->deposito)) {
            return '';
        }
        $items = $this->normalizarLista($decoded->depositos->deposito);
        $targetNombre = trim((string) $nombre);
        $best = '';
        foreach ($items as $it) {
            if (!is_object($it) || !isset($it->depo_id)) {
                continue;
            }
            if ($targetNombre !== '' && isset($it->nombre) && trim((string) $it->nombre) !== $targetNombre) {
                continue;
            }
            $cand = trim((string) $it->depo_id);
            if ($cand !== '' && ((int) $cand >= (int) $best)) {
                $best = $cand;
            }
        }
        return $best;
    }

    /**
     * Convierte objeto|array en lista homogénea.
     *
     * @param mixed $node
     * @return array
     */
    private function normalizarLista($node)
    {
        if (is_array($node)) {
            return $node;
        }
        if (is_object($node)) {
            return array($node);
        }
        return array();
    }

}
