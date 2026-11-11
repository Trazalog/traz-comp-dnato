<?php
/*
 * Prueba de /bulkload_api/cargar (AGENTE-CO-09) — NO toca ninguna base compartida.
 *
 * DÓNDE: terminal local. CÓMO se armó la corrida del 2026-11-11 (18 de 18):
 *  1. Copia de este repo en una carpeta temporal, con:
 *     - application/models/Bulkloads.php reemplazado por un falso que guarda en
 *       PRUEBA_LOG lo que recibe (procedimiento, empresa, archivo) y devuelve
 *       success=false si el CSV contiene "FALLA";
 *     - config/database.php 'default' apuntando a un PostgreSQL descartable con
 *       sta.entidades_negocio (Articulos -> sta.bulkload_articulos,
 *       Mantenimiento Equipos -> bulkload_equipos; Herramientas sin cargar).
 *  2. Un par de claves RSA de prueba en keys/priv.pem y keys/pub.pem.
 *  3. Servidor: JWT_PRIVATE_KEY_PATH=... JWT_PUBLIC_KEY_PATH=... DNATO_ISSUER=http://prueba/oauth \
 *       PRUEBA_LOG=.../cargas.jsonl php -S 127.0.0.1:18770 router.php   (PHP 7.3 de lampp)
 *  4. php scripts/test/bulkload_api_prueba.php  (desde la carpeta de la copia)
 */
// Prueba de /bulkload_api/cargar contra la copia aislada (php 7.3 de lampp).
require __DIR__ . '/app/vendor/autoload.php';
use Firebase\JWT\JWT;

$priv = file_get_contents(__DIR__ . '/keys/priv.pem');
$otra = openssl_pkey_new(array('private_key_bits' => 2048));
openssl_pkey_export($otra, $privOtra);

function token($claims, $key) {
    $base = array('iss' => 'http://prueba/oauth', 'aud' => 'trazalog-mcp', 'iat' => time(), 'exp' => time() + 600,
                  'email' => 'admin@empresa', 'empr_id' => '42', 'role' => '1');
    return JWT::encode(array_merge($base, $claims), $key, 'RS256');
}
function pedir($token, $cuerpo, $metodo = 'POST') {
    $ch = curl_init('http://127.0.0.1:18770/bulkload_api/cargar');
    $h = array('Content-Type: application/json');
    if ($token !== null) { $h[] = 'Authorization: Bearer ' . $token; }
    curl_setopt_array($ch, array(CURLOPT_CUSTOMREQUEST => $metodo, CURLOPT_RETURNTRANSFER => true, CURLOPT_HTTPHEADER => $h,
        CURLOPT_POSTFIELDS => is_string($cuerpo) ? $cuerpo : json_encode($cuerpo), CURLOPT_TIMEOUT => 10));
    $r = curl_exec($ch); $c = curl_getinfo($ch, CURLINFO_HTTP_CODE); curl_close($ch);
    return array($c, json_decode($r, true), $r);
}
$log = __DIR__ . '/cargas.jsonl'; @unlink($log);
$csv = "codigo,descripcion,unidad_medida,tipo,punto_pedido\nA1,Guante,Par,Insumo,10\nA2,Casco,Unidad,EPP,5\n";
$ok = token(array(), $priv);
$casos = array(
    array('sin token', null, array('entidad' => 'Articulos', 'csv' => $csv), 401),
    array('firmado con otra clave', token(array(), $privOtra), array('entidad' => 'Articulos', 'csv' => $csv), 401),
    array('vencido', token(array('exp' => time() - 3600), $priv), array('entidad' => 'Articulos', 'csv' => $csv), 401),
    array('otra audiencia', token(array('aud' => 'otra'), $priv), array('entidad' => 'Articulos', 'csv' => $csv), 401),
    array('token de maquina', token(array('token_tipo' => 'maquina', 'role' => ''), $priv), array('entidad' => 'Articulos', 'csv' => $csv), 403),
    array('no es administrador', token(array('role' => '4'), $priv), array('entidad' => 'Articulos', 'csv' => $csv), 403),
    array('manda empr_id', $ok, array('entidad' => 'Articulos', 'csv' => $csv, 'empr_id' => 99), 400),
    array('manda procedimiento', $ok, array('entidad' => 'Articulos', 'csv' => $csv, 'stored_procedure' => 'sta.otra'), 400),
    array('entidad fuera de la lista', $ok, array('entidad' => 'Solicitantes de Transporte', 'csv' => $csv), 400),
    array('entidad no configurada', $ok, array('entidad' => 'Herramientas', 'csv' => $csv), 400),
    array('solo encabezado', $ok, array('entidad' => 'Articulos', 'csv' => "codigo,descripcion\n"), 400),
    array('no es JSON', $ok, 'hola', 400),
    array('GET', $ok, '', 405, 'GET'),
    array('carga PostgreSQL', $ok, array('entidad' => 'Articulos', 'csv' => $csv), 200),
    array('carga MariaDB', $ok, array('entidad' => 'Mantenimiento Equipos', 'csv' => "codigo,descripcion\nP1,Pala\n"), 200),
    array('la carga falla', $ok, array('entidad' => 'Articulos', 'csv' => "codigo,descripcion\nFALLA,x\n"), 200),
);
$fallas = 0;
foreach ($casos as $c) {
    list($codigo, $json, $crudo) = pedir($c[1], $c[2], isset($c[4]) ? $c[4] : 'POST');
    $bien = $codigo === $c[3];
    $fallas += $bien ? 0 : 1;
    printf("%s %-28s %d %s\n", $bien ? 'OK ' : 'MAL', $c[0], $codigo, substr($crudo, 0, 110));
}
// Lo que recibió la carga
$reg = array_map(function ($l) { return json_decode($l, true); }, array_filter(explode("\n", (string) @file_get_contents($log))));
$reg = array_values($reg);
$esperado = count($reg) === 3 && $reg[0]['sp'] === 'sta.bulkload_articulos' && $reg[0]['empr_id'] === 42 && $reg[0]['csv_existe']
    && $reg[0]['en_stage'] && $reg[0]['csv'] === $csv && $reg[1]['sp'] === 'bulkload_equipos';
$fallas += $esperado ? 0 : 1;
echo ($esperado ? 'OK ' : 'MAL') . " la carga recibio procedimiento del catalogo, empresa del token y el archivo en bulkload_stage_files\n";
$quedan = glob(__DIR__ . '/app/bulkload_stage_files/bulkload_kuntur_*');
$fallas += count($quedan) === 0 ? 0 : 1;
echo (count($quedan) === 0 ? 'OK ' : 'MAL') . " no quedan archivos en el servidor\n";
echo "\n" . ($fallas === 0 ? "TODO BIEN" : "FALLAS: $fallas") . "\n";
exit($fallas === 0 ? 0 : 1);
