<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
$route['default_controller'] = 'main';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

// Alias de compatibilidad: redirigir rutas de la versión SEPARATED al controlador unificado
$route['bulkload_separated'] = 'bulkload';
$route['bulkload_separated/(:any)'] = 'bulkload/$1';

// OAuth 2.1 endpoints — E9-IDENT-03
$route['oauth/authorize']                                    = 'oauth/authorize';
$route['oauth/token']                                        = 'oauth/token';
$route['oauth/.well-known/jwks.json']                        = 'oauth/jwks';
$route['oauth/.well-known/oauth-authorization-server']       = 'oauth/authorization_server_metadata';
$route['oauth/.well-known/openid-configuration']             = 'oauth/authorization_server_metadata';
$route['oauth/register']                                     = 'oauth/register_client';

// OAuth 2.1 login screen — E9-IDENT-04
// Un usuario con varias empresas elige en el paso 2 (oauth/login/empresa).
// Revisa la nota sobre TAD-IDENT-02 en la cabecera de Oauthlogin.php.
$route['oauth/login']              = 'oauthlogin/index';
$route['oauth/login/credentials']  = 'oauthlogin/credentials';
$route['oauth/login/empresa']      = 'oauthlogin/empresa';
$route['oauth/resume']             = 'oauth/resume_after_login';
