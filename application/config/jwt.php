<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| JWT — Configuración del firmador RS256
|--------------------------------------------------------------------------
|
| JWT_PRIVATE_KEY_PATH: ruta absoluta a la clave privada PEM.
|   Se carga desde variable de entorno para no embeber la clave en código.
|   Ejemplo en .env o en la config de Apache/Nginx:
|     JWT_PRIVATE_KEY_PATH=/var/www/dnato/application/config/keys/jwt_private.pem
|
| JWT_PUBLIC_KEY_PATH: ruta a la clave pública. Se usa en el endpoint JWKS
|   y para export a WSO2 (E9-IDENT-05).
|
*/

$private_key_path = getenv('JWT_PRIVATE_KEY_PATH');
$public_key_path  = getenv('JWT_PUBLIC_KEY_PATH');

// Fallback al path local durante desarrollo (excluido de git via .gitignore)
if (!$private_key_path) {
    $private_key_path = APPPATH . 'config/keys/jwt_private.pem';
}
if (!$public_key_path) {
    $public_key_path = APPPATH . 'config/keys/jwt_public.pem';
}

$config['jwt_private_key'] = is_file($private_key_path) ? file_get_contents($private_key_path) : null;
$config['jwt_public_key']  = is_file($public_key_path)  ? file_get_contents($public_key_path)  : null;

$config['jwt_algorithm']   = 'RS256';

/*
| Identificador del Authorization Server: el claim "iss" del JWT. Debe ser una
| URI y coincidir EXACTAMENTE con [[apim.jwt.issuer]] name del deployment.toml
| del APIM; si no, el APIM rechaza los tokens.
|
| Se resuelve en tres pasos, del más específico al más general:
|   1. DNATO_ISSUER del entorno — los ambientes que ya la definen no cambian.
|   2. La constante DNATO_OAUTH_ISSUER de constants.php (config por ambiente).
|   3. Derivado de base_url, que a su vez se autodetecta del request.
|
| El paso 3 hace que ngrok y cualquier dominio funcionen sin configurar nada.
| El fallback anterior era 'http://localhost/oauth' fijo, que sólo era correcto
| en una máquina de desarrollo servida desde la raíz.
*/
$jwt_issuer = getenv('DNATO_ISSUER');

if (!$jwt_issuer && defined('DNATO_OAUTH_ISSUER') && DNATO_OAUTH_ISSUER !== '') {
    $jwt_issuer = DNATO_OAUTH_ISSUER;
}

if (!$jwt_issuer) {
    $base_detectada = '';
    if (function_exists('get_instance')) {
        $CI_tmp = get_instance();
        if ($CI_tmp !== null) {
            $base_detectada = rtrim((string) $CI_tmp->config->item('base_url'), '/');
        }
    }
    $jwt_issuer = ($base_detectada !== '') ? $base_detectada . '/oauth' : 'http://localhost/oauth';
}

$config['jwt_issuer'] = $jwt_issuer;
$config['jwt_audience']    = 'trazalog-mcp';
$config['jwt_ttl']         = 86400; // segundos — 24h en DEV para no expirar durante demos.
                                    // El endpoint /oauth/token NO emite refresh_token todavía,
                                    // así que un TTL corto deja al cliente (Claude) trabado al vencer:
                                    // no renueva ni re-dispara login. TODO: implementar refresh_token.

// key_id para el JWKS endpoint (permite rotar claves sin romper validadores)
$config['jwt_kid'] = 'dnato-rs256-v1';

// consumer_key del APIM Application subscrita a las MCP APIs.
// APIM usa este valor (claim "azp") para validar la subscription del token.
// Obtener desde: APIM DevPortal → Aplicaciones → TrazalogDnatoMCP → Keys → Consumer Key.
// Configurable por entorno via variable de entorno JWT_AZP.
$config['jwt_azp'] = getenv('JWT_AZP') ?: 'z_CtMHRzWPSgY8aXWYxFuzsOli4a';
