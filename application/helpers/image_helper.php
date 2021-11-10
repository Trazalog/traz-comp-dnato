<?php defined('BASEPATH') or exit('No direct script access allowed');

if (!function_exists('form')) {


    function image($img64,$imgName){

        $image = '';
        //log_message('DEBUG','#TRAZA|MAIN|image($img64,$imgName)  img64 >> '.$img64);
        //log_message('DEBUG','#TRAZA|MAIN|image($img64,$imgName)  imgName >> '.$imgName);
        if(isset($img64) && isset($imgName)){
           
            $rec = pg_unescape_bytea($img64);
            $ext = obtenerExtension($imgName);
            //$image = "background-image: url($ext$rec);";
            $image = "$ext$rec";
        //}else{
            //$image = site_url(). "public/img/icon-user-default.png";
        }
        //log_message('DEBUG','#TRAZA|MAIN|image($img64,$imgName) image  >> '.$image);

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