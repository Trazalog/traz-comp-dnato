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
                        <button class="btn btn-block btn-primary" style="width: 100px; margin-top: 10px;" id="btnAgreMenuRol">Agregar</button>             
                    <?php 
                        }
                    ?>
                    <!--
                    <div class="row">
                        <div class="col-xs-12 col-md-4">
                            <div class="form-group">
                                <select class="form-control" name="groups" id="groups" onchange="CargarRolesEmpresas();">
                                <?php
                                    /*foreach($emp_connect as $emp_con){    ///Emrpesas del Usuario conectado
                                        foreach($groups as $group){
                                            list($id_group, $group_name) = explode ("-",$group->name);
                                            if($id_group && $group_name){ 
                                                if($emp_con->group === $group_name){
                                                    echo '<option value="'.$group->id.'-'.$group->name.'">'.$group->displayName.'</option>';
                                                    break;
                                                }
                                            }
                                        }
                                    }*/
                                ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-xs-12 col-md-4">
                            <div class="form-group">
                                <select class="form-control" name="roles" id="roles">
                                <?php
                                    
                                ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-xs-12 col-md-4">
                        <div class="form-group">
                                <select class="form-control" name="cmbopcion" id="cmbopcion">
                                <?php
                                    
                                ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    -->

                </div><!-- /.box-header -->                
                <hr>
                <div class="box-body">
                    <table id="menuesroles" class="table table-bordered table-hover">
                        <thead>
                            <th>Acciones</th>
                            <th>Usuario</th>
                            <th>Opciones</th>
                            <th>Perfil</th>
                            <th>Módulo</th>
                            <th>Grupo</th>
                            <th>Roles</th>
                            <th>Estado</th>
                        </thead>                            
                        <tbody>
                            <?php

                                foreach($emp_connect as $emp_con){
                                    foreach($mnroles as $mnrole){
                                        if($emp_con->group == $mnrole->group){
                                            echo "<tr id='".$mnrole->email."|".$mnrole->group."|".$mnrole->role."|".$mnrole->modulo."|".$mnrole->opcion."|".$mnrole->rolemm."'>";                                            
                                            echo '<td>                                                
                                                <i class="fa fa-fw fa-eye text-light-blue" style="cursor: pointer; margin-left: 4px;" title="Ver" id="btnGetMenu" onclick="getMenuRole(this)"></i>
                                                <i class="fa fa-pencil-square-o  text-light-blue" style="cursor: pointer; margin-left: 4px;" title="Editar" id="btnEditMenu" onclick="editMenuRole(this)"></i>                                                
                                                <i class="fa fa-fw fa-toggle-on text-light-blue" style="cursor: pointer; margin-left: 4px;" title="Eliminar"  id="btnDeleteMenu" onclick="deleteMenuRole(this)"></i>
                                            </td>';
                                            echo '<td>'.$mnrole->email.'</td>';
                                            echo '<td>'.$mnrole->opcion.'</td>';
                                            echo '<td>';
                                                if($mnrole->role == 2){
                                                    echo '<span data-toggle="tooltip" title="" class="badge bg-red">Autor</span>';
                                                }else{
                                                    echo '<span data-toggle="tooltip" title="" class="badge bg-danger">Admin</span>';
                                                }
                                            '</td>';
                                            echo '<td>'.$mnrole->modulo.'</td>';
                                            echo '<td>'.$mnrole->group.'</td>';
                                            echo '<td>'.$mnrole->rolemm.'</td>';
                                            echo '<td>Estado</td>';
                                            echo '</tr>';
                                        }
                                    }
                                }
    
                            ?>
                        </tbody>
                    </table>
                </div><!-- /.box-body -->
            </div><!-- /.box -->
        </div><!-- /.col -->
    </div><!-- /.row -->
</section><!-- /.content -->


<!-- Modal -->
<div id="modalMenuRole" class="modal" role="dialog">
    <div class="modal-dialog">

        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><strong>Opcion de Menu</strong> </h4>
                <h5>Hola <?php use function PHPSTORM_META\type; echo $first_name; ?>,</h5>
                <h5>Por favor ingrese la informacion requerida a continuacion.</h5>       
                <?php 
                    $fattr = array('class' => 'form-signin', 'enctype'=>'multipart/form-data'  );
                    echo form_open('/menu/addMenuRoles', $fattr);
                ?>
            </div>
            <div class="modal-body">
                
                <div class="row">
                    <div class="col-xs-12 col-md-12">
                        <div class="form-group">
                            <label>Empresa: (*)</label>
                            <select class="form-control" name="groups" id="groups" onchange="CargarRolesEmpresas();">
                                <?php
                                    echo '<option value="-1" disabled selected >-Seleccione un Empresa-</option>';
                                    foreach($emp_connect as $emp_con){    ///Emrpesas del Usuario conectado
                                        foreach($groups as $group){
                                            list($id_group, $group_name) = explode ("-",$group->name);
                                            if($id_group && $group_name){ 
                                                if($emp_con->group === $group_name){
                                                    echo '<option value="'.$group->id.'-'.$group->name.'">'.$group->displayName.'</option>';
                                                    break;
                                                }
                                            }
                                        }
                                    }
                                ?>
                                </select>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-xs-12 col-md-12">
                        <div class="form-group">
                            <label>Rol: (*)</label>                          
                            <select class="form-control" name="roles" id="roles" >
                                <option value="-1" disabled selected>-Seleccione una opcion-</option>                            
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-xs-12 col-md-12">
                        <div class="form-group">
                        <label>Modulo: (*)</label>
                            <select class="form-control " name="modulo" id="modulo",  required="true" onchange="cargarOpcion();" >
                            <?php
                                echo '<option value="-1" disabled selected >-Seleccione un modulo-</option>';
                                foreach($modulos as $modulo){    ///Emrpesas del Usuario conectado
                                    echo '<option value="'.$modulo->modulo.'">'.$modulo->modulo.'</option>';
                                }    
                            ?>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xs-12 col-md-12">
                        <div class="form-group">
                            <label>Opcion: (*)</label>
                            <select class="form-control" name="opcion_padre" id="opcion_padre" >
                                <option value="-1" disabled selected>-Seleccione una opcion-</option>                            
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xs-12 col-md-12">
                        <div class="form-group">
                            <?php echo form_input(array('name'=>'email', 'type' => 'hidden', 'id'=> 'email', 'placeholder'=>'Email', 'class'=>'form-control', 'value' => set_value('email'))); ?>
                            <?php echo form_error('email');?>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-xs-12 col-md-12">
                        <div class="form-group">
                            <?php echo form_input(array('name'=>'operacion', 'type' => 'hidden', 'id'=> 'operacion', 'placeholder'=>'Operacion', 'class'=>'form-control', 'value' => set_value('operacion'))); ?>
                            <?php echo form_error('operacion');?>
                        </div>
                    </div>
                </div>

            </div><!-- /.modal-body -->
            <div class="modal-footer">
                <button type="button" class="btn btn-default" id="CancelMenuRole" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnSaveMenuRole" data-dismiss="modal">Guardar</button>
                <button type="button" class="btn btn-primary" id="btnCerrarMenuRole" data-dismiss="modal" >Cerrar</button>
            </div>
        </div>
    </div>
</div>
<!-- Modal -->


<!-- Modal Confirm-->
<div class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" id="modalRoleConfirm">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Deseas Eliminar este registro?</h4>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" id="modal-role-btn-si">Confirmar</button>
        <button type="button" class="btn btn-primary" id="modal-role-btn-no">Cancelar</button>
      </div>
    </div>
  </div>
</div>
<!-- Modal Confirm-->


<script>

$(document).ready(function () {
  
    $('#menuesroles').DataTable();

    $('#opcion_padre').change(function () {

        var buttonDisabled =  $('#groups').val() == -1 || $('#roles').val() == -1 || $('#modulo').val() == -1 || $('#opcion_padre').val() == -1;
        $('#btnSaveMenuRole').attr("disabled", buttonDisabled);
    });
    
});

$('#btnAgreMenuRol').click(function cargarModal() {

    console.log("Modal");
    $('#modalMenuRole').modal('show');
    accionBtn(1); 
    clearForm(1);
    $('h4.modal-title').text('Agregar Menu Role');
    $('#operacion').val('insert');

});

function CargarRolesEmpresas(){

    var group =$('#groups').val();
    /*console.log("group: "+group);*/
    var role = <?php echo json_encode($roles) ?>;
    /*console.log("role: "+JSON.stringify(role));*/

    sgroup = group.split("-");
    /*console.log("empId: "+sgroup[1]);*/
    addOptionsEmp("roles", role,sgroup[1]);
}

/* Rutina para agregar opciones a un <select>*/
function addOptionsEmp(domElement, json, sgroup) {

    var option = '';
    var srole ='';
    sgroup = 777;

    $('#roles')[0].options.length = 0;
    var select = document.getElementsByName(domElement)[0];

    Object.keys(json).forEach(function(elm) {

        if(elm-1  == -1){
            
            option = '<option value="-1" disabled selected >-Seleccione un rol-</option>';
            $('#roles').append(option);
            /*
            option = '<option value="null">-Es padre-</option>';
            $('#opcion_padre').append(option);
            */

        }       
        srole= json[elm]['name'].split("-");

        if(sgroup == srole[0]){
            option = '<option value="'+json[elm]['id']+'-'+json[elm]['name']+'">'+json[elm]['displayName']+'</option>';
            console.log("OPTION: "+option);
            $('#roles').append(option);
        }

    })
}

function cargarOpcion(){
    
    console.log('cargarOpcion');
    var modulo =$('#modulo').val();
    console.log("modulo: "+modulo);
    var op_padres = <?php echo json_encode($op_padres) ?>;
    console.log("op_padres: "+op_padres);

    addOptions("opcion_padre", op_padres, modulo);
}

function addOptions(domElement, json ,modulo){
    
   console.log("addOptions");
   var opction= '';
   $('#opcion_padre')[0].options.length = 0;
   var select =  document.getElementsByName(domElement)[0];
   

    Object.keys(json).forEach(function(elm) {
        console.log("Elm: "+elm); 
        if(elm-1  == -1){
            
            option = '<option value="-1" disabled selected >-Seleccione una opcion-</option>';
            $('#opcion_padre').append(option);
            /*
            option = '<option value="null">-Es padre-</option>';
            $('#opcion_padre').append(option);
            */

        }       
        if(modulo == json[elm]['modulo']){
            console.log(" Modulo: "+json[elm]['modulo']+" Opcion: "+json[elm]['opcion']);
            if(json[elm]['opcion'] == null){
                option = '<option value="null">Es padre</option>';
                console.log("OPTION: "+option);
            }else{
                option = '<option value="'+json[elm]['opcion']+'">'+json[elm]['texto']+'  ['+json[elm]['opcion']+']</option>';
                console.log("OPTION: "+option);
            }            
            $('#opcion_padre').append(option);
        }       
    });
}

function editMenuRole(eval){

    console.log("Edit");
    var data= $(eval).closest('tr').attr('id');
    console.log("Data: "+data);
    var dataMenuRole = data.split("|");
    console.log("email: "+dataMenuRole[0]+" group: "+dataMenuRole[1]+" role: "+dataMenuRole[2]+" modulo: "+dataMenuRole[3]+" opcion: "+dataMenuRole[4]+" rolemm: "+dataMenuRole[5]);
    $('#modalMenuRole').modal('show');
    $('h4.modal-title').text('Actualizar Opciones de Menu Roles');
    $('#operacion').val('update');

    accionBtn(1);

    $('#modulo').val(dataMenuRole[3]);    
    $('#email').val(dataMenuRole[0]);
}

function getMenuRole(eval){

    console.log("Get");
    var data= $(eval).closest('tr').attr('id');
    console.log("Data: "+data);
    var dataMenuRole = data.split("|");
    console.log("email: "+dataMenuRole[0]+" group: "+dataMenuRole[1]+" role: "+dataMenuRole[2]+" modulo: "+dataMenuRole[3]+" opcion: "+dataMenuRole[4]+" rolemm: "+dataMenuRole[5]);
    $('#modalMenuRole').modal('show');
    $('h4.modal-title').text('Visualizar Opciones de Menu Roles');

    accionBtn(2);
    
    $('#modulo').val(dataMenuRole[3]);    
    $('#email').val(dataMenuRole[0]);
}

function deleteMenuRole(eval){

    console.log("Delete");
    var data= $(eval).closest('tr').attr('id');
    console.log("Data: "+data);
    var dataMenuRole = data.split("|");
    console.log("email: "+dataMenuRole[0]+" group: "+dataMenuRole[1]+" role: "+dataMenuRole[2]+" modulo: "+dataMenuRole[3]+" opcion: "+dataMenuRole[4]+" rolemm: "+dataMenuRole[5]);
    $('#modalRoleConfirm').modal('show');

    $("#modal-role-btn-si").on("click", function(){
        console.log("Confirm");
        $.ajax({
            type: "POST",
            url:'<?php echo base_url() ?>/menu/deleteMenuRole',
            data: {
                email: dataMenuRole[0],
                group:  dataMenuRole[1],
                role:   dataMenuRole[2],
                modulo: dataMenuRole[3],
                opcion: dataMenuRole[4],
                rolemm: dataMenuRole[5],
                
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
        $("#modalRoleConfirm").modal('hide');
    });

    $("#modal-role-btn-no").on("click", function(){
        console.log("Cancel");
        $("#modalRoleConfirm").modal('hide');
    });

}

function accionBtn(btn){

    if(btn == 2 ){
        $('#btnCerrarMenuRole').show();
        $('#btnSaveMenuRole').hide();
        $('#CancelMenuRole').hide();
    }else{
        $('#btnCerrarMenuRole').hide();
        $('#btnSaveMenuRole').show();
        $('#btnSaveMenuRole').attr("disabled", true);
        $('#CancelMenuRole').show();
    }

}

function clearForm(btn){

    if(btn == 1){

        $('#groups').val(-1);
        $('#roles').val(-1);
        $('#modulo').val(-1);
        $('#opcion_padre').val(-1);

    }
}


</script>