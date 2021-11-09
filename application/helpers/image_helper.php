<?php defined('BASEPATH') or exit('No direct script access allowed');

if (!function_exists('form')) {


    function image($e){

        $image = '';
        if(isset($e->valor4_base64)){
        
            $rec = stream_get_contents($e->valor4_base64);
            $ext = obtenerExtension($e->valor);
            $image = "$ext$rec";
        }else{
            $image = DNATO."public/img/icon-user-default.png";
        }
        return $image;
    }

    //Funcion para obtener la extension del archivo codificado
    function obtenerExtension($archivo){
        
        $ext = explode('.',$archivo);
            switch(strtolower($ext[1])){
                case 'jpg': $ext = 'data:image/jpg;base64,';break;
                case 'png': $ext = 'data:image/png;base64,';break;
                case 'jpeg': $ext = 'data:image/jpeg;base64,';break;
                case 'pjpeg': $ext = 'data:image/pjpeg;base64,';break;
                case 'wbmp': $ext = 'data:image/vnd.wap.wbmp;base64,';break;
                case 'webp': $ext = 'data:image/webp;base64,';break;
                case 'pdf': $ext = 'data:application/pdf;base64,';break;
                case 'doc': $ext = 'data:application/msword;base64,';break;
                case 'xls': $ext = 'data:application/vnd.ms-excel;base64,';break;
                case 'docx': $ext = 'data:application/vnd.openxmlformats-officedocument.wordprocessingml.document;base64,';break;
                case 'txt': $ext = 'data:text/plain;base64,';break;
                case 'csv': $ext = 'data:text/csv;base64,';break;
                default: $ext = "";
            }
        return $ext;
    }
}


?>