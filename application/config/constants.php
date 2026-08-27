<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Display Debug backtrace
|--------------------------------------------------------------------------
|
| If set to TRUE, a backtrace will be displayed along with php errors. If
| error_reporting is disabled, the backtrace will not display, regardless
| of this setting
|
*/
defined('SHOW_DEBUG_BACKTRACE') OR define('SHOW_DEBUG_BACKTRACE', TRUE);

/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
*/
defined('FILE_READ_MODE')  OR define('FILE_READ_MODE', 0644);
defined('FILE_WRITE_MODE') OR define('FILE_WRITE_MODE', 0666);
defined('DIR_READ_MODE')   OR define('DIR_READ_MODE', 0755);
defined('DIR_WRITE_MODE')  OR define('DIR_WRITE_MODE', 0755);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/
defined('FOPEN_READ')                           OR define('FOPEN_READ', 'rb');
defined('FOPEN_READ_WRITE')                     OR define('FOPEN_READ_WRITE', 'r+b');
defined('FOPEN_WRITE_CREATE_DESTRUCTIVE')       OR define('FOPEN_WRITE_CREATE_DESTRUCTIVE', 'wb'); // truncates existing file data, use with care
defined('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE')  OR define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE', 'w+b'); // truncates existing file data, use with care
defined('FOPEN_WRITE_CREATE')                   OR define('FOPEN_WRITE_CREATE', 'ab');
defined('FOPEN_READ_WRITE_CREATE')              OR define('FOPEN_READ_WRITE_CREATE', 'a+b');
defined('FOPEN_WRITE_CREATE_STRICT')            OR define('FOPEN_WRITE_CREATE_STRICT', 'xb');
defined('FOPEN_READ_WRITE_CREATE_STRICT')       OR define('FOPEN_READ_WRITE_CREATE_STRICT', 'x+b');

/*
|--------------------------------------------------------------------------
| Exit Status Codes
|--------------------------------------------------------------------------
|
| Used to indicate the conditions under which the script is exit()ing.
| While there is no universal standard for error codes, there are some
| broad conventions.  Three such conventions are mentioned below, for
| those who wish to make use of them.  The CodeIgniter defaults were
| chosen for the least overlap with these conventions, while still
| leaving room for others to be defined in future versions and user
| applications.
|
| The three main conventions used for determining exit status codes
| are as follows:
|
|    Standard C/C++ Library (stdlibc):
|       http://www.gnu.org/software/libc/manual/html_node/Exit-Status.html
|       (This link also contains other GNU-specific conventions)
|    BSD sysexits.h:
|       http://www.gsp.com/cgi-bin/man.cgi?section=3&topic=sysexits
|    Bash scripting:
|       http://tldp.org/LDP/abs/html/exitcodes.html
|
*/
defined('EXIT_SUCCESS')        OR define('EXIT_SUCCESS', 0); // no errors
defined('EXIT_ERROR')          OR define('EXIT_ERROR', 1); // generic error
defined('EXIT_CONFIG')         OR define('EXIT_CONFIG', 3); // configuration error
defined('EXIT_UNKNOWN_FILE')   OR define('EXIT_UNKNOWN_FILE', 4); // file not found
defined('EXIT_UNKNOWN_CLASS')  OR define('EXIT_UNKNOWN_CLASS', 5); // unknown class
defined('EXIT_UNKNOWN_METHOD') OR define('EXIT_UNKNOWN_METHOD', 6); // unknown class member
defined('EXIT_USER_INPUT')     OR define('EXIT_USER_INPUT', 7); // invalid user input
defined('EXIT_DATABASE')       OR define('EXIT_DATABASE', 8); // database error
defined('EXIT__AUTO_MIN')      OR define('EXIT__AUTO_MIN', 9); // lowest automatically-assigned error code
defined('EXIT__AUTO_MAX')      OR define('EXIT__AUTO_MAX', 125); // highest automatically-assigned error code

define('BONITA_URL', 'http://10.142.0.13:8080/bonita/');

/*
|--------------------------------------------------------------------------
| WSO2 Micro Integrator - URL base
|--------------------------------------------------------------------------
| Cada ambiente tiene su propio constants.php: en este (desa) usamos nuestro
| WSO2 local. En otros ambientes, en su constants ponen su URL (ej. 10.142.0.13:8280).
*/
$wso2_base = 'http://localhost:8290';
define('REST_BPM', $wso2_base.'/tools/bpm');
define('API_CORE', $wso2_base . '/tools/core');
define('BPM_ADMIN_USER', 'admin');
define('BPM_ADMIN_PASS', '123traza');
define('FRM', 'traz-comp-formularios/');
define('FORMULARIO_REGISTRO_ID', 72);
define('REGISTER_TEMP_EMPR_ID', 9000);
#define('TOOLS_ADMIN_USER','admin@gmail.com');
define('TOOLS_ADMIN_USER','ramon@gmail.com');
define('BPM_USER_PASS', 'bpm');

/*
|--------------------------------------------------------------------------
| Sesión BPM para asignación de roles (tools/bpm)
|--------------------------------------------------------------------------
| Usado por Roles->getInfoBPM, guardarMembershipBPM, deleteMembershipBPM.
| Obtener sesión: login a Bonita, extraer X-Bonita-API-Token y JSESSIONID.
| Formato base: X-Bonita-API-Token=xxx;JSESSIONID=xxx;bonita.tenant=1;
| Actualizar cuando expire la sesión.
*/
$bpm_roles_session_base = 'X-Bonita-API-Token=658fcd51-ef8b-48c3-9606-1d89a88cf3e5;JSESSIONID=BCDEA4A05749709F4DFBDCBB58A527E8;bonita.tenant=1;';
define('BPM_ROLES_SESSION', '"' . $bpm_roles_session_base . '"');
define('BPM_ROLES_SESSION_URL', rawurlencode($bpm_roles_session_base));

#SISTEMA A ENLAZAR
define('USUARIO_EXTERNO', 8);
define('DE', 'http://traz-comp.local/traz-tools/');
define('DS', 'http://traz-comp.local/traz-comp-dnato/main/login');
define('DNATO', 'http://traz-comp.local/traz-comp-dnato/main/users');
define('SIS_NAME', 'TOOLS');

/*
|--------------------------------------------------------------------------
| Dominios de webmail publicos
|--------------------------------------------------------------------------
| Si el email con el que se registra el usuario pertenece a alguno de estos
| dominios, durante "Completar Datos de Empresa" se le pedira un dominio
| corporativo adicional para generar los usuarios por defecto de la empresa.
| Si el email NO pertenece a un webmail, se reutiliza directamente el dominio
| del email para generar esos usuarios.
*/
define('WEBMAIL_DOMAINS', array(
    'gmail.com', 'googlemail.com',
    'hotmail.com', 'hotmail.es', 'hotmail.com.ar', 'hotmail.co.uk',
    'outlook.com', 'outlook.es', 'outlook.com.ar',
    'live.com', 'live.com.ar', 'live.com.mx', 'msn.com',
    'yahoo.com', 'yahoo.es', 'yahoo.com.ar', 'yahoo.com.mx', 'ymail.com', 'rocketmail.com',
    'aol.com',
    'icloud.com', 'me.com', 'mac.com',
    'protonmail.com', 'proton.me', 'pm.me',
    'zoho.com',
    'gmx.com', 'gmx.net', 'gmx.us', 'gmx.es',
    'yandex.com', 'yandex.ru',
    'mail.com', 'mail.ru',
    'fastmail.com',
    'tutanota.com', 'tuta.io', 'tutamail.com',
    'hey.com',
));

// Fallback para ambientes PHP legacy donde define(..., array(...)) no sea soportado.
define('WEBMAIL_DOMAINS_CSV', 'gmail.com,googlemail.com,hotmail.com,hotmail.es,hotmail.com.ar,hotmail.co.uk,outlook.com,outlook.es,outlook.com.ar,live.com,live.com.ar,live.com.mx,msn.com,yahoo.com,yahoo.es,yahoo.com.ar,yahoo.com.mx,ymail.com,rocketmail.com,aol.com,icloud.com,me.com,mac.com,protonmail.com,proton.me,pm.me,zoho.com,gmx.com,gmx.net,gmx.us,gmx.es,yandex.com,yandex.ru,mail.com,mail.ru,fastmail.com,tutanota.com,tuta.io,tutamail.com,hey.com');

/*
|--------------------------------------------------------------------------
| Variables HOST y REST
|--------------------------------------------------------------------------
|
| Variables Locales (HOST usa mismo puerto WSO2 que API_CORE)
|
*/
define('HOST', $wso2_base);
define('REST_CORE', HOST.'/services/COREDataService');
define('API_URL', HOST.'/tools/log');
define('REST_RESI', HOST.'/services/semaresiduosDS');

#ERRORES DE BONITA
define('ASP_100', 'Fallo Conexión BPM');
define('ASP_101', 'Error al Inciar Proceso');
define('ASP_102', 'Error al Tomar Tarea');
define('ASP_103', 'Error al Soltar Tarea');
define('ASP_104', 'Error al Cerrar Tarea');
define('ASP_105', 'Error al Obtener Vista Global');
define('ASP_106', 'Error al Obtener Usuarios');
define('ASP_107', 'Error al Asignar Usuario');
define('ASP_108', 'Error al Guardar Comentarios');
define('ASP_109', 'Error de Loggin');
define('ASP_110', 'Error al Obtener Detalle Tarea');
define('ASP_111', 'Error al Obtener Bandeja de Tareas');
define('ASP_112', 'Error al Obtener Comentarios');
define('ASP_113', 'Usuario No Encontrado');
define('ASP_114', 'Error al Actualizar Variable');
define('ASP_115', 'Error al Leer Variable');

/*
|--------------------------------------------------------------------------
| WSO2 DataService URLs
|--------------------------------------------------------------------------
|
| URLs para los servicios de datos de WSO2
|
*/
define('COREDataService_URL', $wso2_base . '/services/COREDataService');

/*
|--------------------------------------------------------------------------
| Bulkload Configuration
|--------------------------------------------------------------------------
|
| Configuración para la funcionalidad de carga masiva
|
*/
define('BULKLOAD_STAGING_DIR', FCPATH . 'bulkload_stage_files');
define('BULKLOAD_MAX_FILE_SIZE', 10 * 1024 * 1024); // 10 MB
define('BULKLOAD_ALLOWED_EXTENSIONS', 'xlsx,xls');
define('BULKLOAD_TIMEOUT', 60); // segundos

/*
|--------------------------------------------------------------------------
| Registro de Usuarios Configuration
|--------------------------------------------------------------------------
|
| Configuración para la funcionalidad de registro de usuarios
|
*/
define('REST_CORE_PAISES', REST_CORE . '/tablas/paises_registracion');

// Campos adicionales para usuarios
define('CAMPOS_USUARIO_ADICIONALES', array(
    'reg_pais_id',
    'reg_razon_social', 
    'telefono'
));

define('REGISTRACION_PASSWORD_DEFAULT', '12345');

/*
|--------------------------------------------------------------------------
| Imágenes del flujo de registro y login (configurables)
|--------------------------------------------------------------------------
*/
define('REGISTER_IMG_LOGO', 'public/img/toolsgrey.png');
define('REGISTER_IMG_BACKGROUND', 'public/img/toolsregister.png');
define('REGISTER_IMG_COMPLETE_PASSWORD', 'public/img/toolschangepass.png');
define('REGISTER_IMG_FORMULARIO', 'public/img/toolsform.png');
define('REGISTER_IMG_CREAR_EMPRESA', 'public/img/toolscreaempr.png');
define('REGISTER_IMG_BIENVENIDA', 'public/img/toolsbienvenida.png');
define('REGISTER_IMG_EMAIL_LOGO', 'public/img/logotzl.png');
define('LOGIN_IMG_LOGO', 'public/img/logotzl.png');

/*
| Imagen del panel derecho del login (split-screen). Se muestra a sangre, con
| background-size: cover. Por defecto es la misma del registro, para que las
| dos pantallas de entrada al sistema hablen el mismo idioma visual.
*/
define('LOGIN_IMG_BACKGROUND', 'public/img/toolsregister.png');

/*
| Banner de autoregistro (freemium) en la pantalla de login.
|
| TRUE  → se muestra el banner "Crear cuenta gratis", que lleva a main/register.
| FALSE → el login no ofrece ninguna vía de alta; el registro sigue accesible
|         por URL directa, esto sólo controla si se promociona en el login.
|
| Ponerlo en FALSE cuando el alta freemium se cierre o se pase a alta asistida.
*/
define('LOGIN_MOSTRAR_REGISTRO', TRUE);

/*
 * Configuracion unica (JSON) para usuarios por defecto de registracion.
 * Se usa JSON para compatibilidad total con PHP 5.6+ y superiores.
 */
define('REGISTRACION_USUARIOS_DEFAULT_JSON', '{'
    . '"usuario":["Solicitante de Almacén","Solicitante de Mantenimiento"],'
    . '"almacen":["Responsable de Almacén"],'
    . '"panol":["Responsable de Pañol"],'
    . '"produccion":["Responsable de Producción"],'
    . '"mantenimiento":["Supervisor de Mantenimiento","Planificador de Mantenimiento"]'
. '}');

define('BPM_SESSION_FALLBACK', '"X-Bonita-API-Token=658fcd51-ef8b-48c3-9606-1d89a88cf3e5;JSESSIONID=BCDEA4A05749709F4DFBDCBB58A527E8;bonita.tenant=1;"');

/*
 * Defaults para el alta automática de Establecimiento + Depósito que se crea al dar de alta una empresa
 * (Register::postProcesarEmpresa -> Establecimientos::crearDefaultsEmpresa).
 * ENCARGADO_ALIAS debe coincidir con una clave de REGISTRACION_USUARIOS_DEFAULT_JSON para que el usuario exista.
 */
define('REGISTRACION_ESTABLECIMIENTO_DEFAULT_NOMBRE', 'Establecimiento Principal');
define('REGISTRACION_DEPOSITO_DEFAULT_NOMBRE', 'Deposito 1');
define('REGISTRACION_DEPOSITO_DEFAULT_DESCRIPCION', 'Depósito 1');
define('REGISTRACION_DEPOSITO_DEFAULT_ENCARGADO_ALIAS', 'almacen');

// La página de bienvenida (register/registro_completo) arma el listado desde REGISTRACION_USUARIOS_DEFAULT_JSON + dominio corporativo.

/*
||--------------------------------------------------------------------------
|| Formularios Dinámicos Configuration
||--------------------------------------------------------------------------
||
|| Configuración para el módulo de formularios dinámicos
||
*/

