<?php defined('BASEPATH') OR exit('No direct script access allowed');

class ApplicationVersion
{
    const MAJOR = 1;
    const MINOR = 5;
    const PATCH = 4;

    /**
     * Devuelve la versión desde git o, si git no está disponible (ej. Apache como daemon), la constante.
     */
    public static function getVerision() {
        if (php_sapi_name() !== 'cli') {
            return self::MAJOR . '.' . self::MINOR . '.' . self::PATCH;
        }
        $hash = @exec("git rev-list --tags --max-count=1 2>/dev/null");
        if ($hash === false || $hash === '' || $hash === null) {
            return self::MAJOR . '.' . self::MINOR . '.' . self::PATCH;
        }
        $tag = @exec("git describe --tags $hash 2>/dev/null");
        return ($tag !== false && $tag !== '') ? $tag : (self::MAJOR . '.' . self::MINOR . '.' . self::PATCH);
    }

    public static function getLastVersions() {
        if (php_sapi_name() !== 'cli') {
            return json_encode([self::MAJOR . '.' . self::MINOR . '.' . self::PATCH]);
        }
        try {
            $gitOutput = @shell_exec('git log --tags --simplify-by-decoration --pretty="format:%ci %d" 2>/dev/null');
            
            if (empty($gitOutput)) {
                return json_encode([self::MAJOR . '.' . self::MINOR . '.' . self::PATCH]);
            }
            
            $tagsArray = explode(PHP_EOL, $gitOutput);
            $tagsArray = array_filter($tagsArray, function($item) {
                return !empty(trim($item));
            });
            
            if (empty($tagsArray)) {
                return json_encode([self::MAJOR . '.' . self::MINOR . '.' . self::PATCH]);
            }
            
            return json_encode(array_values($tagsArray));
        } catch (Exception $e) {
            return json_encode([self::MAJOR . '.' . self::MINOR . '.' . self::PATCH]);
        }
    }
}