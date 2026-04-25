<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
 * Este archivo sobrescribe las líneas de la librería form_validation de CodeIgniter 3
 * con traducciones al español. CI carga primero system/language/english/form_validation_lang.php
 * y después include() este archivo (mismo nombre, sufijo "english"), por lo que las claves
 * aquí definidas prevalecen en el array_merge final dentro de CI_Lang::load().
 *
 * Si se decide mover $config['language'] a 'spanish', existe también
 * application/language/spanish/form_validation_lang.php con los mismos textos.
 */

$lang['form_validation_required']               = 'El campo {field} es obligatorio.';
$lang['form_validation_isset']                  = 'El campo {field} debe tener un valor.';
$lang['form_validation_valid_email']            = 'El campo {field} debe contener una dirección de correo válida.';
$lang['form_validation_valid_emails']           = 'El campo {field} debe contener sólo direcciones de correo válidas.';
$lang['form_validation_valid_url']              = 'El campo {field} debe contener una URL válida.';
$lang['form_validation_valid_ip']               = 'El campo {field} debe contener una dirección IP válida.';
$lang['form_validation_min_length']             = 'El campo {field} debe tener al menos {param} caracteres.';
$lang['form_validation_max_length']             = 'El campo {field} no puede superar los {param} caracteres.';
$lang['form_validation_exact_length']           = 'El campo {field} debe tener exactamente {param} caracteres.';
$lang['form_validation_alpha']                  = 'El campo {field} sólo puede contener letras.';
$lang['form_validation_alpha_numeric']          = 'El campo {field} sólo puede contener caracteres alfanuméricos.';
$lang['form_validation_alpha_numeric_spaces']   = 'El campo {field} sólo puede contener caracteres alfanuméricos y espacios.';
$lang['form_validation_alpha_dash']             = 'El campo {field} sólo puede contener caracteres alfanuméricos, guiones bajos y guiones.';
$lang['form_validation_numeric']                = 'El campo {field} sólo puede contener números.';
$lang['form_validation_is_numeric']             = 'El campo {field} sólo puede contener caracteres numéricos.';
$lang['form_validation_integer']                = 'El campo {field} debe contener un número entero.';
$lang['form_validation_regex_match']            = 'El campo {field} tiene un formato inválido.';
$lang['form_validation_matches']                = 'El campo {field} no coincide con el campo {param}.';
$lang['form_validation_differs']                = 'El campo {field} debe ser distinto del campo {param}.';
$lang['form_validation_is_unique']              = 'El campo {field} debe tener un valor único.';
$lang['form_validation_is_natural']             = 'El campo {field} sólo puede contener dígitos.';
$lang['form_validation_is_natural_no_zero']     = 'El campo {field} sólo puede contener dígitos y ser mayor a cero.';
$lang['form_validation_decimal']                = 'El campo {field} debe contener un número decimal.';
$lang['form_validation_less_than']              = 'El campo {field} debe contener un número menor a {param}.';
$lang['form_validation_less_than_equal_to']     = 'El campo {field} debe contener un número menor o igual a {param}.';
$lang['form_validation_greater_than']           = 'El campo {field} debe contener un número mayor a {param}.';
$lang['form_validation_greater_than_equal_to']  = 'El campo {field} debe contener un número mayor o igual a {param}.';
$lang['form_validation_error_message_not_set']  = 'No se pudo obtener un mensaje de error para el campo {field}.';
$lang['form_validation_in_list']                = 'El campo {field} debe ser uno de los siguientes: {param}.';
