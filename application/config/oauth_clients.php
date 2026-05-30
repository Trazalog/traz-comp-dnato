<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| OAuth 2.1 Client Registry — E9-IDENT-04
|--------------------------------------------------------------------------
| Cada entrada define un cliente OAuth autorizado.
|
| display_name  — texto que aparece en la pantalla de login:
|                 "Claude solicita acceder a Trazalog"
| redirect_uris — lista blanca de redirect_uri permitidos.
|                 El redirect_uri del connector Claude.ai se obtiene en:
|                 Settings → Integrations → Custom connectors → <connector> → Redirect URI
|
| Para registrar un nuevo cliente: agregar entrada aquí y reiniciar Apache/PHP-FPM.
*/

$config['oauth_clients'] = [
    'trazalog-mcp-connector' => [
        'display_name'  => 'Claude',
        'redirect_uris' => [
            'https://claude.ai/api/mcp/auth_callback',
        ],
    ],
];
