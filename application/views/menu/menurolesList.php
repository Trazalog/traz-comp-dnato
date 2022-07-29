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
                    
                    <div class="row">
                        <div class="col-xs-12 col-md-4">
                            <div class="form-group">
                                <select class="form-control" name="groups" id="groups" onchange="CargarRolesEmpresas();">
                                <?php
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
                        </thead>                            
                        <tbody>
                            <?php

                                foreach($emp_connect as $emp_con){
                                    foreach($mnroles as $mnrole){
                                        if($emp_con->group == $mnrole->group){
                                            echo "<tr id='".$mnrole->email."|".$mnrole->group."|".$mnrole->role."|".$mnrole->modulo."|".$mnrole->opcion."|".$mnrole->rolemm."'>";                                            
                                            echo '<td>
                                                <i class="fa fa-fw fa-times-circle text-light-blue" style="cursor: pointer; margin-left: 4px;" title="Eliminar"  id="btnDeleteMenu" onclick="deleteMenuRole(this)"></i>
                                                <i class="fa fa-fw fa-pencil text-light-blue" style="cursor: pointer; margin-left: 4px;" title="Editar" id="btnEditMenu" onclick="editMenuRole(this)"></i>
                                                <i class="fa fa-fw fa-search text-light-blue" style="cursor: pointer; margin-left: 4px;" title="Ver" id="btnGetMenu" onclick="getMenuRole(this)"></i>
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
                    echo form_open('/menu/addMenu', $fattr);
                ?>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-xs-12 col-md-6">
                        <div class="form-group">
                            <?php echo form_input(array('name'=>'group', 'readonly'=>'true', 'id'=> 'group', 'placeholder'=>'Empresa', 'class'=>'form-control', 'value' => set_value('group'))); ?>
                            <?php echo form_error('group');?>
                        </div>
                    </div>

                    <div class="col-xs-12 col-md-6">
                        <div class="form-group">
                            <?php echo form_input(array('name'=>'role', 'readonly'=>'true', 'id'=> 'role', 'placeholder'=>'Rol', 'class'=>'form-control', 'value' => set_value('role'))); ?>
                            <?php echo form_error('role');?>
                        </div>
                    </div>
                </div>
                
            
                <div class="row">
                    <div class="col-xs-12 col-md-12">
                        <div class="form-group">
                            <?php echo form_input(array('name'=>'modulo', 'id'=> 'modulo', 'placeholder'=>'Modulo', 'class'=>'form-control', 'value' => set_value('modulo'))); ?>
                            <?php echo form_error('modulo');?>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xs-12 col-md-12">
                        <div class="form-group">
                            <?php echo form_input(array('name'=>'opcion', 'id'=> 'opcion', 'placeholder'=>'Opcion', 'class'=>'form-control', 'value' => set_value('opcion'))); ?>
                            <?php echo form_error('opcion');?>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xs-12 col-md-12">
                        <div class="form-group">
                            <?php echo form_input(array('name'=>'email', 'readonly'=>'true', 'id'=> 'email', 'placeholder'=>'Email', 'class'=>'form-control', 'value' => set_value('email'))); ?>
                            <?php echo form_error('email');?>
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
});

function CargarRolesEmpresas(){

    var group =$('#groups').val();

    var role = <?php echo json_encode($roles) ?>;

    sgroup = group.split("-");

    addOptions("roles", role,sgroup[1]);
}

/* Rutina para agregar opciones a un <select>*/
function addOptions(domElement, json, sgroup) {

    var option = '';
    var srole ='';
    sgroup = 777;

    var select = document.getElementsByName(domElement)[0];

    Object.keys(json).forEach(function(elm) {
    
        srole= json[elm]['name'].split("-");

        if(sgroup == srole[0]){
            option = '<option value="'+json[elm]['id']+'-'+json[elm]['name']+'">'+json[elm]['displayName']+'</option>';
            console.log("OPTION: "+option);
            $('#roles').append(option);
        }

    })
}

function editMenuRole(eval){

    console.log("Edit");
    var data= $(eval).closest('tr').attr('id');
    console.log("Data: "+data);
    var dataMenuRole = data.split("|");
    console.log("email: "+dataMenuRole[0]+" group: "+dataMenuRole[1]+" role: "+dataMenuRole[2]+" modulo: "+dataMenuRole[3]+" opcion: "+dataMenuRole[4]+" rolemm: "+dataMenuRole[5]);
    $('#modalMenuRole').modal('show');
    $('h4.modal-title').text('Actualizar Opciones de Menu');

    accionBtn(1);

    $('#email').val(dataMenuRole[0]);
    $('#group').val(dataMenuRole[1]);
    if(dataMenuRole[2] == 2)
        $('#role').val("Autor");
    else
        $('#role').val("Admin");
}

function getMenuRole(eval){

    console.log("Get");
    var data= $(eval).closest('tr').attr('id');
    console.log("Data: "+data);
    var dataMenuRole = data.split("|");
    console.log("email: "+dataMenuRole[0]+" group: "+dataMenuRole[1]+" role: "+dataMenuRole[2]+" modulo: "+dataMenuRole[3]+" opcion: "+dataMenuRole[4]+" rolemm: "+dataMenuRole[5]);
    $('#modalMenuRole').modal('show');
    $('h4.modal-title').text('Visualizar Opciones de Menu');


    accionBtn(2);
    
    $('#email').val(dataMenuRole[0]);
    $('#group').val(dataMenuRole[1]);
    if(dataMenuRole[2] == 2)
        $('#role').val("Autor");
    else
        $('#role').val("Admin");
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
    $('#CancelMenuRole').show();
}

}

</script>