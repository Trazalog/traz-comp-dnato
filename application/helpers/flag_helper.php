<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Flag Helper
 * Funciones para mostrar banderas de países
 */

/**
 * Obtiene el emoji de bandera para un país según su código
 * @param string $codigo_pais Código del país (AR, PE, EC, etc.)
 * @return string Emoji de la bandera o código del país si no se encuentra
 */
function get_flag_emoji($codigo_pais) {
    $banderas = array(
        'AR' => '🇦🇷', // Argentina
        'PE' => '🇵🇪', // Perú
        'EC' => '🇪🇨', // Ecuador
        'DE' => '🇩🇪', // Alemania
        'MX' => '🇲🇽', // México
        'UY' => '🇺🇾', // Uruguay
        'BO' => '🇧🇴', // Bolivia
        'BR' => '🇧🇷', // Brasil
        'CL' => '🇨🇱', // Chile
        'CO' => '🇨🇴', // Colombia
        'PY' => '🇵🇾', // Paraguay
        'VE' => '🇻🇪', // Venezuela
        'US' => '🇺🇸', // Estados Unidos
        'CA' => '🇨🇦', // Canadá
        'ES' => '🇪🇸', // España
        'FR' => '🇫🇷', // Francia
        'IT' => '🇮🇹', // Italia
        'GB' => '🇬🇧', // Reino Unido
        'AU' => '🇦🇺', // Australia
        'JP' => '🇯🇵', // Japón
        'CN' => '🇨🇳', // China
        'IN' => '🇮🇳', // India
        'RU' => '🇷🇺', // Rusia
    );
    
    return isset($banderas[$codigo_pais]) ? $banderas[$codigo_pais] : '🏳️';
}

/**
 * Obtiene el emoji de bandera para un país según su descripción
 * @param string $descripcion Descripción del país (Argentina, Perú, etc.)
 * @return string Emoji de la bandera o código del país si no se encuentra
 */
function get_flag_by_description($descripcion) {
    $paises_codigos = array(
        'Argentina' => 'AR',
        'Peru' => 'PE',
        'Perú' => 'PE',
        'Ecuador' => 'EC',
        'Alemania' => 'DE',
        'Mexico' => 'MX',
        'México' => 'MX',
        'Uruguay' => 'UY',
        'Bolivia' => 'BO',
        'Brasil' => 'BR',
        'Chile' => 'CL',
        'Colombia' => 'CO',
        'Paraguay' => 'PY',
        'Venezuela' => 'VE',
        'Estados Unidos' => 'US',
        'Canada' => 'CA',
        'Canadá' => 'CA',
        'España' => 'ES',
        'Francia' => 'FR',
        'Italia' => 'IT',
        'Reino Unido' => 'GB',
        'Australia' => 'AU',
        'Japon' => 'JP',
        'Japón' => 'JP',
        'China' => 'CN',
        'India' => 'IN',
        'Rusia' => 'RU',
    );
    
    $codigo = isset($paises_codigos[$descripcion]) ? $paises_codigos[$descripcion] : null;
    return $codigo ? get_flag_emoji($codigo) : '🏳️';
}

