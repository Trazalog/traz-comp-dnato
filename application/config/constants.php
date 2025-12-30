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
define('REST_BPM', 'http://10.142.0.13:8280/tools/bpm');
define('API_CORE', 'http://10.142.0.13:8280/tools/core');
define('BPM_ADMIN_USER', 'admin');
define('BPM_ADMIN_PASS', '123traza');
define('FRM', 'traz-comp-formularios/');
define('FORMULARIO_REGISTRO_ID', 72);
define('TOOLS_ADMIN_USER','admin@gmail.com');
define('BPM_USER_PASS', 'bpm');

#SISTEMA A ENLAZAR
define('USUARIO_EXTERNO', 8);
define('DE', 'http://traz-comp.local/');
define('DS', 'http://traz-comp.local/main/login');
define('DNATO', 'http://traz-comp.local/traz-comp-dnato/');
define('SIS_NAME', 'TOOLS');

/*
|--------------------------------------------------------------------------
| Variables HOST y REST
|--------------------------------------------------------------------------
|
| Variables Locales
|
*/
define('HOST', 'http://10.142.0.13:8280');
define('REST_CORE', HOST.'/services/COREDataService');

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
define('COREDataService_URL', 'http://10.142.0.13:8280/services/COREDataService');

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

define('REGISTRACION_USUARIOS_DEFAULT', array(
    'usuario' => array(
        'Solicitante de Almacén',
        'Solicitante de Mantenimiento'
    ),
    'almacen' => array(
        'Responsable de Almacén'
    ),
    'panol' => array(
        'Responsable de Pañol'
    ),
    'produccion' => array(
        'Responsable de Producción'
    ),
    'mantenimiento' => array(
        'Supervisor de Mantenimiento',
        'Planificador de Mantenimiento'
    )
));

define('BPM_SESSION_FALLBACK', '"X-Bonita-API-Token=658fcd51-ef8b-48c3-9606-1d89a88cf3e5;JSESSIONID=BCDEA4A05749709F4DFBDCBB58A527E8;bonita.tenant=1;"');

// Usuarios freemium para mostrar en página de bienvenida
define('FREEMIUM_USERS', '
<div style="background-color: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #3498db;">
    <h4 style="color: #2c3e50; margin-top: 0;">Usuarios Generados Automáticamente</h4>
    <p style="margin-bottom: 15px; color: #7f8c8d;">Se han creado los siguientes usuarios para que puedas utilizar Trazalog Tools:</p>
    
    <div style="background-color: white; padding: 15px; border-radius: 5px; border: 1px solid #ecf0f1;">
        <div style="display: flex; justify-content: space-between; align-items: center; padding: 10px 0; border-bottom: 1px solid #ecf0f1;">
            <div>
                <strong style="color: #2c3e50;">admin@tudominio.com</strong>
                <br><small style="color: #7f8c8d;">Administrador del sistema</small>
            </div>
            <span style="background-color: #27ae60; color: white; padding: 4px 8px; border-radius: 3px; font-size: 12px;">ACTIVO</span>
        </div>
        
        <div style="display: flex; justify-content: space-between; align-items: center; padding: 10px 0; border-bottom: 1px solid #ecf0f1;">
            <div>
                <strong style="color: #2c3e50;">operador@tudominio.com</strong>
                <br><small style="color: #7f8c8d;">Operador de planta</small>
            </div>
            <span style="background-color: #27ae60; color: white; padding: 4px 8px; border-radius: 3px; font-size: 12px;">ACTIVO</span>
        </div>
        
        <div style="display: flex; justify-content: space-between; align-items: center; padding: 10px 0;">
            <div>
                <strong style="color: #2c3e50;">supervisor@tudominio.com</strong>
                <br><small style="color: #7f8c8d;">Supervisor de producción</small>
            </div>
            <span style="background-color: #27ae60; color: white; padding: 4px 8px; border-radius: 3px; font-size: 12px;">ACTIVO</span>
        </div>
    </div>
    
    <div style="margin-top: 15px; padding: 10px; background-color: #e8f4fd; border-radius: 5px;">
        <p style="margin: 0; color: #2980b9; font-size: 14px;">
            <strong>Nota:</strong> Todos los usuarios tienen la contraseña inicial "123456". 
            Te recomendamos cambiar las contraseñas después del primer inicio de sesión.
        </p>
    </div>
</div>
');

/*
||--------------------------------------------------------------------------
|| Formularios Dinámicos Configuration
||--------------------------------------------------------------------------
||
|| Configuración para el módulo de formularios dinámicos
||
*/
