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
// Identificador del AS. Debe ser una URI; debe coincidir con [[apim.jwt.issuer]] name
// en deployment.toml del APIM y con el KM "Dnato" registrado en APIM Admin.
// Para ngrok: exportar DNATO_ISSUER=https://<dominio-ngrok>/oauth antes de iniciar Apache.
$config['jwt_issuer']      = getenv('DNATO_ISSUER') ?: 'http://localhost/oauth';
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
