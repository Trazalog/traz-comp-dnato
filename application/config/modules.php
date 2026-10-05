<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Enable/Disable Modules
|--------------------------------------------------------------------------
|
| If you would like to disable modules, you can do so by setting the
| 'modules_locations' array to an empty array.
|
| You can also disable specific modules by setting the 'modules_disabled'
| array to the module names you want to disable.
|
*/

$config['modules_locations'] = array(
    APPPATH.'modules/' => '../modules/',
);

$config['modules_autoload'] = array(
    'traz-comp-formularios'
);