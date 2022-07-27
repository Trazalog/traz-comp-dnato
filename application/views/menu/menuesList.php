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
                                        if(!$menu->eliminado)
                                           echo '<i class="fa fa-fw fa-times-circle text-light-blue" style="cursor: pointer; margin-left: 4px;" title="Eliminar"  id="btnDeleteMenu" onclick="deleteMenu(this)"></i>';
                                        else
                                           echo '<i class="fa fa-fw fa-check-circle text-light-blue" style="cursor: pointer; margin-left: 4px;" title="Eliminar"  id="btnActiveMenu" onclick="activeMenu(this)"></i>';
                                        
                                    echo'<i class="fa fa-fw fa-pencil text-light-blue" style="cursor: pointer; margin-left: 4px;" title="Editar" id="btnEditMenu" onclick="editMenu(this)"></i>
                                         <i class="fa fa-fw fa-search text-light-blue" style="cursor: pointer; margin-left: 4px;" title="Ver" id="btnGetMenu" onclick="getMenu(this)"></i>
                                    </td>';
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
                                            echo '<span data-toggle="tooltip" title="" class="badge bg-red">Activo</span>';
                                        }else{
                                            echo '<span data-toggle="tooltip" title="" class="badge bg-danger">Inactivo</span>';
                                        }
                                    '</td>';
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
                <h4 class="modal-title"><strong>Menu</strong> </h4>
                <h5>Hola <?php use function PHPSTORM_META\type; echo $first_name; ?>,</h5>
                <h5>Por favor ingrese la informacion requerida a continuacion.</h5>       
                <?php 
                    $fattr = array('class' => 'form-signin', 'enctype'=>'multipart/form-data'  );
                    echo form_open('/menu/addMenu', $fattr);
                ?>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-xs-12 col-md-6">
                        <div class="form-group">
                            <?php echo form_input(array('name'=>'modulo', 'id'=> 'modulo', 'placeholder'=>'Modulo', 'class'=>'form-control', 'value' => set_value('modulo'))); ?>
                            <?php echo form_error('modulo');?>
                        </div>
                    </div>

                    <div class="col-xs-12 col-md-6">
                        <div class="form-group">
                            <?php echo form_input(array('name'=>'opcion', 'id'=> 'opcion', 'placeholder'=>'Opcion', 'class'=>'form-control', 'value' => set_value('opcion'))); ?>
                            <?php echo form_error('opcion');?>
                        </div>
                    </div>
                </div>
                
            
                <div class="row">
                    <div class="col-xs-12 col-md-12">
                        <div class="form-group">
                            <?php echo form_input(array('name'=>'texto', 'id'=> 'texto', 'placeholder'=>'Nombre', 'class'=>'form-control', 'value' => set_value('texto'))); ?>
                            <?php echo form_error('texto');?>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xs-12 col-md-6">
                        <div class="form-group">
                            <?php echo form_input(array('name'=>'opcion_padre', 'id'=> 'opcion_padre', 'placeholder'=>'Opcion Padre', 'class'=>'form-control', 'value' => set_value('opcion_padre'))); ?>
                            <?php echo form_error('opcion_padre');?>
                        </div>
                    </div>
                    <div class="col-xs-12 col-md-6">
                        <div class="form-group">
                            <?php echo form_input(array('name'=>'orden', 'id'=> 'orden', 'placeholder'=>'Numero de Orden', 'class'=>'form-control', 'value' => set_value('orden'))); ?>
                            <?php echo form_error('orden');?>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xs-12 col-md-12">
                        <div class="form-group">
                            <?php echo form_input(array('name'=>'url', 'id'=> 'url', 'placeholder'=>'URL', 'class'=>'form-control', 'value' => set_value('url'))); ?>
                            <?php echo form_error('url');?>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xs-12 col-md-12">
                        <div class="form-group">
                            <?php echo form_input(array('name'=>'url_icono', 'id'=> 'url_icono', 'placeholder'=>'Icono', 'class'=>'form-control', 'value' => set_value('url_icono'))); ?>
                            <?php echo form_error('url_icono');?>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xs-12 col-md-12">
                        <div class="form-group">
                            <?php echo form_input(array('name'=>'texto_onmouseover', 'id'=> 'texto_onmouseover', 'placeholder'=>'Texto Hover', 'class'=>'form-control', 'value' => set_value('texto_onmouseover'))); ?>
                            <?php echo form_error('texto_onmouseover');?>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-xs-12 col-md-6">
                    <div class="form-group">
                            <?php echo form_input(array('name'=>'eliminado', 'id'=> 'eliminado', 'placeholder'=>'Eliminado', 'class'=>'form-control', 'value' => set_value('eliminado'))); ?>
                            <?php echo form_error('eliminado');?>
                        </div>
                    </div>
                    <div class="col-xs-12 col-md-6">
                        <div class="form-group">
                            <?php echo form_input(array('name'=>'fec_alta', 'id'=> 'fec_alta', 'placeholder'=>'Fecha Alta', 'class'=>'form-control', 'value' => set_value('fec_alta'))); ?>
                            <?php echo form_error('url_icono');?>
                        </div>
                    </div>
                </div>

            </div><!-- /.modal-body -->
            <div class="modal-footer">
                <button type="button" class="btn btn-default" id="CancelMenu" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnSaveMenu" data-dismiss="modal">Guardar</button>
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
});

$('#btnAgreMenu').click(function cargarModal() {

    console.log("Modal");
    $('#modalMenu').modal('show');
    accionBtn(1); 
    $('h4.modal-title').text('Agregar Menu');

});

$('#btnSaveMenu').click(function cargarModal() {
    
    console.log("Guardar Menu");
    $('#modalMenu').modal('show');
    
});

function activeMenu(eval){

    console.log("Active");
    var data= $(eval).closest('tr').attr('id');
    console.log("Data: "+data);
    var dataMenu = data.split("|");
    console.log("Modulo: "+dataMenu[0]+" Opcion: "+dataMenu[1]);
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
    console.log("Data: "+data);
    var dataMenu = data.split("|");
    console.log("Modulo: "+dataMenu[0]+" Opcion: "+dataMenu[1]);
    $('#modalConfirm').modal('show');
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
    console.log("Data: "+data);
    var dataMenu = data.split("|");
    $('h4.modal-title').text('Actualizar Menu');

    accionBtn(1); 

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
    $('#fec_alta').val(dataMenu[10]);
    
}

function getMenu(eval){

    console.log("Get");
    var data= $(eval).closest('tr').attr('id');
    console.log("Data: "+data);
    var dataMenu = data.split("|");
    $('h4.modal-title').text('Visualizar Menu');
    
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
    $('#fec_alta').val(dataMenu[10]);
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


</script>

