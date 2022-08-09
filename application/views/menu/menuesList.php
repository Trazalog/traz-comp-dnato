<input type="hidden" id="permission" value="<?php echo $permission;?>">
<div class="row">
    <div class="col-xs-12">
        <div class="alert alert-danger alert-dismissable" id="error1" style="display: none">
            <h4><i class="icon fa fa-ban"></i> ALERTA!</h4>
            Este equipo! SI tiene datos tecnicos cargados
        </div>
    </div>
</div>
<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title"><?php echo $title; ?></h3>
                    <?php
                        if ($role == 1) {
                    ?>
                        <button class="btn btn-block btn-primary" style="width: 100px; margin-top: 10px;" id="btnAgreMenu">Agregar</button>             
                    <?php 
                        }
                    ?>
                </div><!-- /.box-header -->
                <hr>
                <div class="box-body">
                    <table id="menues" class="table table-bordered table-hover">
                        <thead>
                            
                            <th>Acciones</th>
                            <th>Módulo</th>
                            <th>Opción</th>
                            <th>Nombre</th>
                            <th>URL</th>
                            <th>Opción Padre</th>
                            <th>Nro. Orden</th>
                            <th>Texto MouseOver</th>
                            <th class="hidden">Javascript</th>
                            <th class="hidden">Url Icono</th>
                            <th>Estado</th>
                            <th class="hidden">Fecha Alta</th>
                            <th class="hidden">Usuario</th>
                            <th class="hidden">Usuario App</th>

                        </thead>                            
                        <tbody>
                            <?php
                                foreach($menues as $menu){
                                    echo "<tr id='".$menu->modulo."|".$menu->opcion."|".$menu->texto."|".$menu->url."|".$menu->opcion_padre."|".$menu->orden."|".$menu->texto_onmouseover."|".$menu->javascript."|".$menu->url_icono."|".$menu->eliminado."|".$menu->fec_alta."'>";                                            
                                    echo '<td>';                                    
                                    echo '<i class="fa fa-fw fa-eye text-light-blue" style="cursor: pointer; margin-left: 4px;" title="Ver" id="btnGetMenu" onclick="getMenu(this)"></i>';
                                    echo '<i class="fa fa-pencil-square-o text-light-blue" style="cursor: pointer; margin-left: 4px;" title="Editar" id="btnEditMenu" onclick="editMenu(this)"></i>';                                    
                                        if(!$menu->eliminado)
                                           echo '<i class="fa fa-fw fa-toggle-on text-light-blue" style="cursor: pointer; margin-left: 4px;" title="Inactivar"  id="btnDeleteMenu" onclick="deleteMenu(this)"></i>';
                                        else
                                           echo '<i class="fa fa-fw fa-toggle-off text-light-blue" style="cursor: pointer; margin-left: 4px;" title="Activar"  id="btnActiveMenu" onclick="activeMenu(this)"></i>';
                                    echo '</td>';
                                    echo '<td>'.$menu->modulo.'</td>';
                                    echo '<td>'.$menu->opcion.'</td>';
                                    echo '<td>'.$menu->texto.'</td>';
                                    echo '<td>'.$menu->url.'</td>';
                                    echo '<td>'.$menu->opcion_padre.'</td>';
                                    echo '<td>'.$menu->orden.'</td>';
                                    echo '<td>'.$menu->texto_onmouseover.'</td>';
                                    echo '<td class="hidden">'.$menu->javascript.'</td>';
                                    echo '<td class="hidden">'.$menu->url_icono.'</td>';
                                    echo '<td>';
                                        if(!$menu->eliminado){
                                            echo '<span data-toggle="tooltip" title="" class="badge badge-success">Activo</span>';
                                        }else{
                                            echo '<span data-toggle="tooltip" title="" class="badge badge-danger" style="background-color: red;">Inactivo</span>';
                                        }
                                    echo '</td>';
                                    echo '<td class="hidden">'.$menu->fec_alta.'</td>';
                                    echo '<td class="hidden">'.$menu->usuario.'</td>';
                                    echo '<td class="hidden">'.$menu->usuario_app.'</td>';
                                    echo '</tr>';
                                }
                            ?>
                        </tbody>
                    </table>
                </div><!-- /.box-body -->
            </div><!-- /.box -->
        </div><!-- /.col -->
    </div><!-- /.row -->
</section><!-- /.content -->

<!-- Modal Agregar -->
<div id="modalMenu" class="modal" role="dialog">
    <div class="modal-dialog">

        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><strong>Menú</strong> </h4>
                <h5>Hola <?php use function PHPSTORM_META\type; echo $first_name; ?>,</h5>
                <h5>Por favor ingrese la información requerida a continuación.</h5>       
                <?php 
                    $fattr = array('class' => 'form-signin', 'enctype'=>'multipart/form-data'  );
                    echo form_open('/menu/addMenu', $fattr);
                ?>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-xs-12 col-md-6">
                        <div class="form-group">
                            <label for="modulo">Módulo: (*)</label>
                            <select class="form-control " name="modulo" id="modulo",  required="true" onchange="cargarOpcion();" >
                            <?php
                                echo '<option value="-1" disabled selected >-Seleccione un módulo-</option>';
                                foreach($modulos as $modulo){    ///Emrpesas del Usuario conectado
                                    echo '<option value="'.$modulo->modulo.'">'.$modulo->modulo.'</option>';
                                }    
                            ?>
                            </select>
                        </div>
                    </div>

                    <div class="col-xs-12 col-md-6">
                        <div class="form-group">
                            <label for="opcion">Opción: (*)</label>
                            <?php echo form_input(array('name'=>'opcion', 'id'=> 'opcion', 'placeholder'=>'Opcion', 'class'=>'form-control', 'data-placement'=>'top',  'title'=> 'Nombre de referencia, sin espacios, utilizando guiones bajos para separar las palabras.', 'value' => set_value('opcion'))); ?>
                            <?php echo form_error('opcion');?>
                        </div>
                    </div>
                </div>
                
            
                <div class="row">
                    <div class="col-xs-12 col-md-12">
                        <div class="form-group">
                            <label for="texto">Nombre: (*)</label>
                            <?php echo form_input(array('name'=>'texto', 'id'=> 'texto', 'placeholder'=>'Nombre', 'class'=>'form-control', 'value' => set_value('texto'))); ?>
                            <?php echo form_error('texto');?>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xs-12 col-md-8">
                        <div class="form-group">
                            <label for="opcion_padre">Opción Padre: (*)</label>                            
                            <select class="form-control" name="opcion_padre" id="opcion_padre" data-placement="top" title= "Indica el orden en que se visualiza el menú. Si es padre estará como principal, los sucesores hijos o nietos, se iran anidando como submenus.">
                                <option value="-1">-Seleccione una opción-</option>                            
                            </select>
                        </div>
                    </div>
                    <div class="col-xs-12 col-md-4">
                        <div class="form-group">
                            <label for="orden">Numero Orden: (*)</label>
                            <?php echo form_input(array('name'=>'orden', 'id'=> 'orden', 'placeholder'=>'Numero de Orden', 'class'=>'form-control', 'value' => set_value('orden'))); ?>
                            <?php echo form_error('orden');?>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xs-12 col-md-12">
                        <div class="form-group">
                            <label for="orden">URL:</label>
                            <?php echo form_input(array('name'=>'url', 'id'=> 'url', 'placeholder'=>'URL', 'class'=>'form-control', 'data-placement'=>'top',  'title'=> 'Corresponde a la dirección de la ruta del menú. Escriba respetando el formato: módulo/nombre/dash', 'value' => set_value('url'))); ?>
                            <?php echo form_error('url');?>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xs-12 col-md-12">
                        <div class="form-group">
                            <label for="orden">Icono:</label>
                            <?php echo form_input(array('name'=>'url_icono', 'id'=> 'url_icono', 'placeholder'=>'Icono', 'class'=>'form-control', 'value' => set_value('url_icono'))); ?>
                            <?php echo form_error('url_icono');?>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xs-12 col-md-12">
                        <div class="form-group">
                            <label for="orden">Texto Hover:</label>
                            <?php echo form_input(array('name'=>'texto_onmouseover', 'id'=> 'texto_onmouseover', 'placeholder'=>'Texto Hover', 'class'=>'form-control', 'data-placement'=>'top',  'title'=> 'Este texto se mostrará cada vez que pase el cursor de su ratón por encima del menú.', 'value' => set_value('texto_onmouseover'))); ?>
                            <?php echo form_error('texto_onmouseover');?>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-xs-12 col-md-6 hidden">
                    <div class="form-group">
                    <?php

                        $dd_list = array(
                                   '0'   => 'Activo',
                                   '1'   => 'Inactivo',
                                 );
                        $dd_name = "eliminado";
                        echo form_dropdown($dd_name, $dd_list, set_value($dd_name),'class = "form-control" id="eliminado" ');
                        ?>
                        </div>
                    </div>
                    <div class="col-xs-12 col-md-6">
                        <div class="form-group">
                            <?php echo form_input(array('name'=>'operacion', 'type' => 'hidden', 'id'=> 'operacion', 'placeholder'=>'Operacion', 'class'=>'form-control', 'value' => set_value('operacion'))); ?>
                            <?php echo form_error('operacion');?>
                        </div>
                    </div>
                </div>

            </div><!-- /.modal-body -->
            <div class="modal-footer">
                <button type="button" class="btn btn-default" id="CancelMenu" data-dismiss="modal">Cancelar</button>
                <?php echo form_submit(array('value'=>'Guardar', 'id'=>'btnSaveMenu', 'class'=>'btn btn-primary')); ?>
                <?php echo form_close(); ?>
                <button type="button" class="btn btn-primary" id="btnCerrarMenu" data-dismiss="modal" >Cerrar</button>
            </div>
        </div>
    </div>
</div>
<!-- Modal Agregar -->


<!-- Modal Confirm-->
<div class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" id="modalConfirm">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel"></h4>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" id="modal-btn-si">Confirmar</button>
        <button type="button" class="btn btn-primary" id="modal-btn-no">Cancelar</button>
      </div>
    </div>
  </div>
</div>
<!-- Modal Confirm-->

<script>

$(document).ready(function () {
    $('#menues').DataTable();

    $('#btnSaveMenu').attr("disabled", true);

    $('#opcion_padre, #opcion, #texto, #orden, #url, #modulo').keyup(function () {

        var buttonDisabled =  $('#opcion_padre').val() == -1 || $('#modulo').val() == null || $('#opcion').val().length == 0 || $('#texto').val().length == 0 || $('#url').val().length == 0 || $('#orden').val().length == 0;
        $('#btnSaveMenu').attr("disabled", buttonDisabled);
    });
});

$('#btnAgreMenu').click(function cargarModal() {

    console.log("Modal");
    $('#modalMenu').modal('show');
    accionBtn(1); 
    clearMenu(1);
    $('h4.modal-title').text('Agregar Menú');
    $('#operacion').val('insert');

});


$('#btnSaveMenu').click(function cargarModal() {
    
    console.log("Guardar Menu");

    var modulo = $("#modulo").val();
    var opcion = $('#opcion').val();
    var texto = $('#texto').val();
    var opion_padre = $('#opcion_padre').val();
    var orden = $('#orden').val();
    var url = $('#url').val();
    var url_icono =  $('#url_icono').val();
    var texto_on =  $('#texto_onmouseover').val();
    var eliminado = $('#eliminado').val();
    var operacion =  $('#operacion').val();

    
        /*$('#btnSaveMenu').prop('disabled', false);*/
        
        /*$.ajax({
            type: "POST",
            url:'<?php echo base_url() ?>/menu/addMenu',
            data: {
                modulo: modulo,
                opcion: opcion,
                texto : texto,
                opcion_padre : opcion_padre,
                orden: orden,
                url: url,
                url_icono: url_icono,
                texto_on: texto_on,
                eliminado: eliminado,
                operacion: operacion
            },
            success: function(rsp) {
                console.log(rsp);
                
            },
            error: function(rsp) {
                console.log(rsp);
            },
            complete: function() {

            }
        });*/

    /*}*/    
});

function activeMenu(eval){

    console.log("Active");
    var data= $(eval).closest('tr').attr('id');
    /*console.log("Data: "+data);*/
    var dataMenu = data.split("|");
    /*console.log("Modulo: "+dataMenu[0]+" Opcion: "+dataMenu[1]);*/
    $('#modalConfirm').modal('show');
    $('h4.modal-title').text('Deseas activar este registro?');
    

    $("#modal-btn-si").on("click", function(){
        console.log("Confirm");
        $.ajax({
            type: "POST",
            url:'<?php echo base_url() ?>/menu/activeMenu',
            data: {
                modulo: dataMenu[0],
                opcion: dataMenu[1]
            },
            success: function(rsp) {
                console.log(rsp);
                window.location.reload();
            },
            error: function(rsp) {
                console.log(rsp);
            },
            complete: function() {

            }
        });
        $("#modalConfirm").modal('hide');
    });

    $("#modal-btn-no").on("click", function(){
        console.log("Cancel");
        $("#modalConfirm").modal('hide');
    });
}

function deleteMenu(eval){

    console.log("Delete");
    var data= $(eval).closest('tr').attr('id');
    /*console.log("Data: "+data);*/
    var dataMenu = data.split("|");
    /*console.log("Modulo: "+dataMenu[0]+" Opcion: "+dataMenu[1]);
    $('#modalConfirm').modal('show');*/
    $('h4.modal-title').text('Deseas desactivar este registro?');

    $("#modal-btn-si").on("click", function(){
        console.log("Confirm");
        $.ajax({
            type: "POST",
            url:'<?php echo base_url() ?>/menu/deleteMenu',
            data: {
                modulo: dataMenu[0],
                opcion: dataMenu[1]
            },
            success: function(rsp) {
                console.log(rsp);
                window.location.reload();
            },
            error: function(rsp) {
                console.log(rsp);
            },
            complete: function() {

            }
        });
        $("#modalConfirm").modal('hide');
    });

    $("#modal-btn-no").on("click", function(){
        console.log("Cancel");
        $("#modalConfirm").modal('hide');
    });
}

function validar(e) {
 if (e.target.value.trim() == "")
  alert("debe ingresar un valor en el campo");
 else
  alert("ingreso "+e.target.value.trim()+", es correcto!");
}

function editMenu(eval){

    console.log("Edit");
    var data= $(eval).closest('tr').attr('id');
    /*console.log("Data: "+data);*/
    var dataMenu = data.split("|");
    $('h4.modal-title').text('Actualizar Menú');

    accionBtn(1); 
    clearMenu(2);

    $('#modalMenu').modal('show');
    $('#modulo').val(dataMenu[0]);
    $('#modulo').prop('readonly', true);
    $('#opcion').val(dataMenu[1]);
    $('#opcion').prop('readonly', true);
    $('#texto').val(dataMenu[2]);
    $('#opcion_padre').val(dataMenu[4]);
    $('#opcion_padre').prop('readonly', true);
    $('#orden').val(dataMenu[5]);
    $('#url').val(dataMenu[3]);
    $('#url_icono').val(dataMenu[8]);
    $('#texto_onmouseover').val(dataMenu[6]);
    $('#eliminado').val(dataMenu[9]);
    $('#operacion').val('update');
    
}

function getMenu(eval){

    console.log("Get");
    var data= $(eval).closest('tr').attr('id');
    /*console.log("Data: "+data);*/
    var dataMenu = data.split("|");
    $('h4.modal-title').text('Visualizar Menú');
    
    accionBtn(2);   

    $('#modalMenu').modal('show');
    
    $('#modulo').val(dataMenu[0]);    
    $('#opcion').val(dataMenu[1]);
    $('#texto').val(dataMenu[2]);
    $('#opcion_padre').val(dataMenu[4]);
    $('#orden').val(dataMenu[5]);
    $('#url').val(dataMenu[3]);
    $('#url_icono').val(dataMenu[8]);
    $('#texto_onmouseover').val(dataMenu[6]);
    $('#eliminado').val(dataMenu[9]);

    $('#modulo').attr("style", "pointer-events: none;");
    $('#opcion').prop('readonly', true);
    $('#texto').prop('readonly', true);
    $('#opcion_padre').attr("style", "pointer-events: none;");
    $('#orden').prop('readonly', true);
    $('#url').prop('readonly', true);
    $('#url_icono').prop('readonly', true);
    $('#texto_onmouseover').prop('readonly', true);
    $('#eliminado').attr("style", "pointer-events: none;");
   
}

function accionBtn(btn){

    if(btn == 2 ){
        $('#btnCerrarMenu').show();
        $('#btnSaveMenu').hide();
        $('#CancelMenu').hide();
    }else{
        $('#btnCerrarMenu').hide();
        $('#btnSaveMenu').show();
        $('#CancelMenu').show();
    }
    
}

function clearMenu(operacion){

    if(operacion == 1) {

        $('#modulo').val('');
        $('#opcion').val('');
        $('#texto').val('');
        $('#opcion_padre').val('');
        $('#orden').val('');
        $('#url').val('');
        $('#url_icono').val('');
        $('#texto_onmouseover').val('');
        $('#eliminado').val('');
        $('#operacion').val('');

        $('#modulo').prop('readonly', false);
        $('#opcion').prop('readonly', false);
        $('#texto').prop('readonly', false);
        $('#opcion_padre').prop('readonly', false);
        $('#orden').prop('readonly', false);
        $('#url').prop('readonly', false);
        $('#url_icono').prop('readonly', false);
        $('#texto_onmouseover').prop('readonly', false);
        $('#eliminado').prop('readonly', false);

        $('#modulo').val('-1');
        $('#opcion_padre').val('-1');
        $('#eliminado').val('0');
    }else{

        $('#modulo').attr("style", "pointer-events: none;");
        $('#opcion').prop('readonly', true);
        $('#opcion_padre').attr("style", "pointer-events: none;");
        $('#eliminado').attr("style", "pointer-events: none;");

        $('#opcion').prop('readonly', false);
        $('#texto').prop('readonly', false);
        $('#orden').prop('readonly', false);
        $('#url').prop('readonly', false);
        $('#url_icono').prop('readonly', false);
        $('#texto_onmouseover').prop('readonly', false);


    }

}

function cargarOpcion(){
    
    console.log('cargarOpcion');
    var modulo =$('#modulo').val();
    /*console.log("modulo: "+modulo);*/
    var op_padres = <?php echo json_encode($op_padres) ?>;
    /*console.log("op_padres: "+op_padres);*/

    addOptions("opcion_padre", op_padres, modulo);
}

function addOptions(domElement, json ,modulo){
    
   console.log("addOptions");
   var opction= '';
   $('#opcion_padre')[0].options.length = 0;
   var select =  document.getElementsByName(domElement)[0];
   

    Object.keys(json).forEach(function(elm) {
        /*console.log("Elm: "+elm); */
        if(elm-1  == -1){
            
            option = '<option value="-1" disabled selected >-Seleccione una opción-</option>';
            $('#opcion_padre').append(option);
            /*
            option = '<option value="null">-Es padre-</option>';
            $('#opcion_padre').append(option);
            */

        }       
        if(modulo == json[elm]['modulo']){
            /*console.log(" Modulo: "+json[elm]['modulo']+" Opcion: "+json[elm]['opcion']);*/
            if(json[elm]['opcion'] == null){
                option = '<option value="null">Es padre</option>';
                /*console.log("OPTION: "+option);*/
            }else{
                option = '<option value="'+json[elm]['opcion']+'">'+json[elm]['texto']+'  ['+json[elm]['opcion']+']</option>';
               /* console.log("OPTION: "+option);*/
            }            
            $('#opcion_padre').append(option);
        }       
    });
}


</script>

