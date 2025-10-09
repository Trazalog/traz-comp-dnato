<?php defined('BASEPATH') OR exit('No direct script access allowed');

class ApplicationVersion
{
    const MAJOR = 1;
    const MINOR = 5;
    const PATCH = 4;

    public static function getVerision() {

        $hash = exec("git rev-list --tags --max-count=1");
        return exec("git describe --tags $hash"); 
    }

    public static function getLastVersions() {
        try {
            $gitOutput = shell_exec('git log --tags --simplify-by-decoration --pretty="format:%ci %d"');
            
            if (empty($gitOutput)) {
                return json_encode(['']);
            }
            
            $tagsArray = explode(PHP_EOL, $gitOutput);
            $tagsArray = array_filter($tagsArray, function($item) {
                return !empty(trim($item));
            });
            
            if (empty($tagsArray)) {
                return json_encode(['']);
            }
            
            return json_encode(array_values($tagsArray));
        } catch (Exception $e) {
            return json_encode(['']);
        }
    }
}


?>