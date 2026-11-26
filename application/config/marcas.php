<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Marcas por dominio (REQ-019, ADR-015) — aplicación de ingreso
|--------------------------------------------------------------------------
|
| Lista CERRADA de dominios que muestran una marca distinta de Trazalog.
| Cualquier dominio que no figure acá muestra Trazalog, exactamente como hoy.
| Los 'hosts' tienen que ser los MISMOS que en traz-tools
| (application/config/marcas.php): Tools y el ingreso se sirven juntos.
|
| La marca por defecto (Trazalog) NO se define acá: sus valores siguen
| saliendo de core.tablas y de constants.php como hasta ahora.
|
| Claves (todas optativas; la que falte usa el valor de Trazalog):
|   hosts          dominios que muestran esta marca
|   nombre         nombre corto en textos ("pirkai.net")
|   producto       nombre del producto en textos ("Trazalog Tools")
|   titulo_login   <title> del ingreso
|   css            hoja de estilos propia (se carga en header.php)
|   favicon        ícono de la pestaña
|   logo_login     logo del ingreso y del recupero de clave (fondo blanco)
|   url_sitio      link del pie de página
|   razon_social   titular de los derechos en el texto legal
|   constantes     reemplazo de constantes de imagen (REGISTER_IMG_*, LOGIN_IMG_*)
|
| Los mails (bienvenida, activación, recupero) NO cambian de marca: quedan
| fuera del alcance de REQ-019.
|
| ⚠ Valores de pirkai PROVISORIOS: logo de texto y colores elegidos sin
|   definición del dueño del producto. Reemplazar los archivos de
|   public/marcas/pirkai/ manteniendo los nombres.
*/

$config['marcas'] = array(

    'pirkai' => array(
        'hosts' => array(
            'app.pirkai.net',
            'demo.pirkai.net',
            'pirkai.local',
        ),
        'nombre'       => 'pirkai.net',
        'producto'     => 'pirkai.net',
        'titulo_login' => 'pirkai.net',
        'css'          => 'public/marcas/pirkai/marca.css',
        'favicon'      => 'public/marcas/pirkai/favicon.svg',
        'logo_login'   => 'public/marcas/pirkai/logo-impresos.svg',
        'url_sitio'    => 'https://pirkai.net/',
        'razon_social' => 'pirkai.net',
        'constantes'   => array(
            'LOGIN_IMG_LOGO'    => 'public/marcas/pirkai/logo-impresos.svg',
            'REGISTER_IMG_LOGO' => 'public/marcas/pirkai/logo.svg',
        ),
    ),

);
