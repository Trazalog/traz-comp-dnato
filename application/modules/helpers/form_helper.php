<?php defined('BASEPATH') or exit('No direct script access allowed');

if (!function_exists('form')) {
    function form($data, $modal = false)
    {
        $html = "<form class='frm' id='frm-$data->id' data-ninfoid='$data->id' data-form='" . (isset($data->form_id) ? $data->form_id : 'frm-default') . "' data-info='" . (isset($data->info_id) ? $data->info_id : null) . "' data-valido='false'>";
        $html .= "<fieldset>";
        if (!$data->items) {
            return 'Formulario No encontrado.';
        }

        foreach ($data->items as $key => $e) {

            switch ($e->tipo_dato) {

                case 'titulo1':
                    $html .= "<div class='".($e->columna ? $e->columna : 'col-md-12')."'><div class=<h1>$e->label</h1></div>";
                    break;
                case 'titulo2':
                    $html .= "<div class='".($e->columna ? $e->columna : 'col-md-12')."'><h2>$e->label</h2></div>";
                    break;
                case 'titulo3':
                    $html .= "<div class='".($e->columna ? $e->columna : 'col-md-12')."'><h3>$e->label</h3></div>";
                    break;

                case 'comentario':
                    $html .= "<p class='text-info'>$e->label</p>";
                    break;

                case 'input':
                    $html .= input($e);
                    break;

                case 'select':
                    $html .= select($e);
                    break;

                case 'service':
                        $html .= select($e);
                        break;

                case 'date':
                    $html .= datepicker($e);
                    break;

                case 'check':
                    $html .= check($e);
                    break;

                case 'radio':
                    $html .= radio($e);
                    break;

                case 'file':
                    $html .= archivo($e);
                    break;

                case 'textarea':
                    $html .= textarea($e);
                    break;
                
                case 'image':
                    $html .= image($e);
                    break;

                case 'number':
                        $html .= number($e);
                        break;

                case 'btnAgregar':
                    $html .= btnAgregar($e);
                    break;
                default:
                    $html .= "<hr>";
                    break;
            }
        }

        return $html . '<div style="float:right" class="col-md-12"><button type="button" class="btn btn-primary pull-right frm-save ' . ($modal ? 'hidden' : null) . '" onclick="frmGuardar(this)">Guardar</button></div></form></fieldset>';
    }
}
function input($e)
{
    return
        "<div class='".($e->columna ? $e->columna : 'col-md-12')."'>
            <div class='form-group'>
                <label for=''>$e->label" . ($e->requerido ? "<strong class='text-danger'> *</strong>" : null) . ":</label>
                <input class='form-control' value='" . (isset($e->valor) ? $e->valor : null) . "' type='text' placeholder='Ingrese $e->label...' id='$e->name'  name='$e->name' " . ($e->requerido ? req() : null) . "/>
            </div>
        </div>";
}

function select($e)
{
    $val = '<option value=""> -Seleccionar- </option>';
    foreach ($e->values as $o) {
        $val .= "<option value='$o->value' " . ((isset($e->valor) && $e->valor == $o->value) ? 'selected' : null) . ">$o->label</option>";
    }

    return
        "<div class='".($e->columna ? $e->columna : 'col-md-12')."'>
            <div class='form-group'>
                <label for=''>$e->label" . ($e->requerido ? "<strong class='text-danger'> *</strong>" : null) . ":</label>
                <select style='width: 100%;' class='form-control frm-select' name='$e->name' ". ($e->requerido ?  'data-bv-notempty data-bv-notempty-message="Campo Obligatorio *"' : null).">$val</select>
            </div>
        </div>";
}

function datepicker($e)
{
    return
        "<div class='".($e->columna ? $e->columna : 'col-md-12')."'>
            <div class='form-group'>
                <label for=''>$e->label" . ($e->requerido ? "<strong class='text-danger'> *</strong>" : null) . ":</label>
                <input class='form-control datepicker' value='" . (isset($e->valor) ? $e->valor : null) . "' type='date' placeholder='dd/mm/aaaa' id='$e->name'  name='$e->name' " . ($e->requerido ? req() : null) . " data-bv-date-format='DD/MM/YYYY' data-bv-date-message='Formato de Fecha Inválido'/>
            </div>
        </div>";

}

function check($e)
{
    $html = "";
    foreach ($e->values as $key => $o) {
        $html .= "<div class='checkbox'>
                                <label>
                                    <input type='checkbox' name='$e->name[]' class='flat-red i-check' value='$o->value' " . ($key == 0 && $e->requerido ? null : null) . ((isset($e->valor) && strpos("_" . $e->valor, $o->value) > 0 ? ' checked' : null)) . ">
                                    $o->label
                                </label>
                            </div>";
    }
    // $html .= "<input class='hidden' type='checkbox' name='$e->name[]' value=' ' checked>";
    return
        "<div class='".($e->columna ? $e->columna : 'col-md-12')."'>
            <div class='form-group'>
                <label>$e->label" . ($e->requerido ? "<strong class='text-danger'> *</strong>" : null) . ":</label><div style='margin-left: 10%;'> $html</div>
            </div>
        </div>";

}

function radio($e)
{
    $html = '';
    foreach ($e->values as $key => $o) {
        $html .= "<div class='radio'>
                        <label>
                            <input type='radio' name='$o->tabla' class='flat-red i-check' value='$o->value' " . ($key == 0 && $e->requerido ? null : null) . " " . ((isset($e->valor) && $e->valor == $o->value) ? 'checked' : null) . ">
                            $o->label
                        </label>
                    </div>";
    }
    return
        "<div class='".($e->columna ? $e->columna : 'col-md-12')."'>
            <div class='form-group'>
                <label>$e->label" . ($e->requerido ? "<strong class='text-danger'> *</strong>" : null) . ":</label><div style='margin-left: 10%;'> $html</div>
            </div>
        </div>";
}

function archivo($e)
{

    $file = null;

    if (isset($e->valor)) {
        // $url = base_url(files . $e->valor);
        $ext = obtenerExtension($e->valor);
        $rec = stream_get_contents($e->valor4_base64);
        $url = $ext.$rec;
        $file = " download='$e->valor' href='$url' ";
    } else {
        $file = "style='display: none;'";
    }

    return
        "<div class='".($e->columna ? $e->columna : 'col-md-12')."'>
            <div class='form-group'>
                <label>$e->label" . ($e->requerido ? "<strong class='text-danger'> *</strong>" : null) . ":</label>
                <input class='form-control' id='$e->name' type='file' name='". ($e->multiple ? '-file-'.$e->name.'[]' : '-file-'.$e->name) . "' " . ($e->requerido ? req() : null). ">
                <p class='help-block show-file'><a $file class='help-button col-sm-4 download' title='Descargar' download>
                    <iclass='fa fa-download'></i> Ver Adjunto</a>
                </p>
            </div>
        </div>";
}

function textarea($e)
{
    return
        "<div class='".($e->columna ? $e->columna : 'col-md-12')."'>
            <div class='form-group'>
                <label>$e->label" . ($e->requerido ? "<strong class='text-danger'> *</strong>" : null) . ":</label>
                <textarea class='form-control' rows='3' placeholder='Ingrese Texto...' id='$e->name' type='file' name='$e->name' " . ($e->requerido ? req() : null)
        . ">" . (isset($e->valor) ? $e->valor : null) . "</textarea>
        </div>
    </div>";
}

function req()
{
    return
        ' data-bv-notempty
          data-bv-notempty-message="Campo Obligatorio *" ';
}

function hreq()
{
    echo '<strong class="text-danger">*</strong>';
}

function imagePerfil($img64,$imgName){

    $image = '';

    if(isset($img64) && isset($imgName)){
       
        $rec = pg_unescape_bytea($img64);
        $ext = obtenerExtension($imgName);
        $image = "$ext$rec";
    }else{
        $image = "data:image/png;base64,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";
    }

    return $image;
}

function image($e){
    $style = '';
    $indice = rand(500, 100000);

    if(isset($e->valor4_base64)){
    
        $rec = stream_get_contents($e->valor4_base64);
        $ext = obtenerExtension($e->valor);
        $style = "background-image: url($ext$rec);";
    }else{
        $style = "background-image: url(lib/imageForms/camera_2.png);";
    }
    
    return
    "<div class='".($e->columna ? $e->columna : 'col-md-12')."'>
        <label for='$e->label'>$e->label" . ($e->requerido ? "<strong class='text-danger'> *</strong>" : null) . ":</label>
        <div class='form-group imgConte centrar'>
        <label for='$indice'>
            <div class='imgEdit'>
                <input class='form-control' value='" . (isset($e->valor) ? $e->valor : null) . "' type='file' id='$indice'  name='". ($e->multiple ? '-file-'.$e->name.'[]' : '-file-'.$e->name) . "' " . ($e->requerido ? req() : null) . " onchange='previewFile(this)' accept='image/*' capture/>   
            </div>
        <div class='imgPreview'>
            <div id='vistaPrevia_$indice' style='$style'></div>
        </div>
        </label>
    </div>
    </div>";
}

function number($e){
    return
        "<div class='".($e->columna ? $e->columna : 'col-md-12')."'>
            <div class='form-group'>
                <label for=''>$e->label" . ($e->requerido ? "<strong class='text-danger'> *</strong>" : null) . ":</label>
                <input class='form-control' value='" . (isset($e->valor) ? $e->valor : null) . "' type='number' placeholder='Ingrese $e->label...' id='$e->name'  name='$e->name' " . ($e->requerido ? req() : null) . "/>
            </div>
        </div>";
}

function btnAgregar($e){
    return
        "<div class='".($e->columna ? $e->columna : 'col-md-12')."'>
            <div class='form-group'>
                <button type='button' class='btn btn-primary' onclick='agregar$e->name()'><i class='fa fa-plus'></i> $e->label</button>
            </div>
        </div>";
}

function nuevoForm($form_id)
{
    if ($form_id) {
        $ci = &get_instance();
        $ci->load->model(FRM . 'Forms');
        $res = $ci->Forms->generarInstancia($form_id);
        $res = getForm($res['info_id']);
        return $res;
    }
}
/**
	* Obtiene el la instacia del formulario dinamico
	* @param integer $info_id
	* @return array con instancia formulario dibujado
*/
function getForm($info_id){
    log_message('DEBUG',"#TRAZA | #TRAZ-COMP-FORMULARIOS | HELPER | getForm() -> info_id : ". $info_id);
    if ($info_id) {
        $ci = &get_instance();
        $ci->load->model(FRM . 'Forms');
        $res = $ci->Forms->obtener($info_id);
        $res = form($res);
        return $res;
    }
}

function getFormXEmpresa($nombre, $emprId){
        $ci = &get_instance();
        $ci->load->model(FRM . 'Forms');
        $res = $ci->Forms->obtenerXEmpresa($nombre, $emprId);
        return form($res);
}

//Funcion para obtener la extension del archivo codificado
function obtenerExtension($archivo){
    $ext = explode('.',$archivo);
        switch(strtolower(array_pop($ext))){
            case 'jpg': $ext = 'data:image/jpg;base64,';break;
            case 'png': $ext = 'data:image/png;base64,';break;
            case 'jpeg': $ext = 'data:image/jpeg;base64,';break;
            case 'jfif': $ext = 'data:image/jpeg;base64,';break;
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