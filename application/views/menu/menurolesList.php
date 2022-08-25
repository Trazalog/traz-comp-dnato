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
                            <th>Grupo</th>
                            <th>Módulo</th>                            
                            <th>Opciones</th>                            
                            <th>Roles</th>
                            <th>Estado</th>
                        </thead>                            
                        <tbody>
                            <?php

                                foreach($emp_connect as $emp_con){
                                    foreach($mnroles as $mnrole){
                                        if($emp_con->group == $mnrole->group){
                                            echo "<tr id='".$mnrole->group."|".$mnrole->modulo."|".$mnrole->opcion."|".$mnrole->role."'>";                                            
                                            echo '<td>';                                                
                                                echo '<i class="fa fa-fw fa-eye text-light-blue" style="cursor: pointer; margin-left: 4px;" title="Ver" id="btnGetMenu" onclick="getMenuRole(this)"></i>';
                                                echo '<i class="fa fa-pencil-square-o  text-light-blue" style="cursor: pointer; margin-left: 4px;" title="Editar" id="btnEditMenu" onclick="editMenuRole(this)"></i>';
                                                if(!$mnrole->eliminado){
                                                
                                                    echo '<i class="fa fa-fw fa-toggle-on text-light-blue" style="cursor: pointer; margin-left: 4px;" title="Inactivar"  id="btnDeleteMenuRole" onclick="deleteMenuRole(this)"></i>';
                                                }else{
                                                  
                                                    echo '<i class="fa fa-fw fa-toggle-off text-light-blue" style="cursor: pointer; margin-left: 4px;" title="Activar"  id="btnActiveMenuRole" onclick="activeMenuRole(this)"></i>';
                                                }
                                            echo '</td>';
                                            foreach($groups as $group){
                                                if(strpos($group->name,'-') !== false){ /*SI*/
                                                    list($group_id, $group_name) = explode ("-",$group->name);
                                                    if($group_name == $mnrole->group){
                                                        echo '<td>'.$group->displayName.'</td>';
                                                    }
                                                }else{ /*NO*/
                                                    if($group->name == $mnrole->group){
                                                        echo '<td>'.$group->displayName.'</td>';
                                                    }
                                                }
                                            }                                            
                                            echo '<td>'.$mnrole->modulo.'</td>';
                                            echo '<td>'.$mnrole->opcion.'</td>';                                            
                                            echo '<td>'.$mnrole->role.'</td>';
                                            echo '<td>';
                                            if(!$mnrole->eliminado){
                                                
                                                echo '<span data-toggle="tooltip" title="" class="badge badge-success">Activo</span>';
                                            }else{
                                        
                                                echo '<span data-toggle="tooltip" title="" class="badge badge-danger" style="background-color: red;">Inactivo</span>';
                                            }
                                            echo '</td>';
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
                <h4 class="modal-title"><strong>Opción de Menú</strong> </h4>
                <h5>Hola <?php use function PHPSTORM_META\type; echo $first_name; ?>,</h5>
                <h5>Por favor ingrese la información requerida a continuación.</h5>       
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
                                    echo '<option value="-1" selected >-Seleccione un Empresa-</option>';
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
                                <option value="-1" selected>-Seleccione una opción-</option>                            
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-xs-12 col-md-12">
                        <div class="form-group">
                        <label>Módulo: (*)</label>
                            <select class="form-control " name="modulo" id="modulo",  required="true" onchange="cargarOpcion();" >
                            <?php
                                echo '<option value="-1" selected >-Seleccione un modulo-</option>';
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
                            <label>Opción: (*)</label>
                            <select class="form-control" name="opcion_padre" id="opcion_padre" >
                                <option value="-1" selected>-Seleccione una opción-</option>                            
                            </select>
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
                <?php echo form_submit(array('value'=>'Guardar', 'id'=>'btnSaveMenuRole', 'class'=>'btn btn-primary')); ?>
                <?php echo form_close(); ?>
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

    $('#groups, #roles, #modulo, #opcion_padre').change(function () {        
        var buttonDisabled =  $('#groups').val() == -1 || $('#roles').val() == -1 || $('#modulo').val() == -1 || $('#opcion_padre').val() == -1;
        $('#btnSaveMenuRole').attr("disabled", buttonDisabled);
    });
    
});

$('#btnAgreMenuRol').click(function cargarModal() {

    console.log("Modal");
    $('#modalMenuRole').modal('show');

    $('h4.modal-title').text('Agregar Menú Role');
    $('#operacion').val('insert');

    clearForm(1);
    accionBtn(1); 
    
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
    /*sgroup = 777;*/

    $('#roles')[0].options.length = 0;
    var select = document.getElementsByName(domElement)[0];

    Object.keys(json).forEach(function(elm) {

        if(elm-1  == -1){
            
            option = '<option value="-1" selected >-Seleccione un rol-</option>';
            $('#roles').append(option);
        }       
        srole= json[elm]['name'].split("-");

        if(sgroup == srole[0]){
            option = '<option value="'+json[elm]['id']+'-'+json[elm]['name']+'">'+json[elm]['displayName']+'</option>';
            /*console.log("OPTION: "+option);*/
            $('#roles').append(option);
        }

    })
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
            option = '<option value="-1" selected >-Seleccione una opción-</option>';
            $('#opcion_padre').append(option);
            /* option = '<option value="null">-Es padre-</option>';*/
            /*$('#opcion_padre').append(option);*/
        }       
        if(modulo == json[elm]['modulo']){
            /*console.log(" Modulo: "+json[elm]['modulo']+" Opcion: "+json[elm]['opcion']);*/
            if(json[elm]['opcion'] == null){
                option = '<option value="null">Es padre</option>';
                /*console.log("OPTION: "+option);*/
            }else{
                option = '<option value="'+json[elm]['opcion']+'">'+json[elm]['texto']+'  ['+json[elm]['opcion']+']</option>';
                /*console.log("OPTION: "+option);*/
            }            
            $('#opcion_padre').append(option);
        }       
    });
}

function editMenuRole(eval){

    console.log("Edit");
    var data= $(eval).closest('tr').attr('id');
    /*console.log("Data: "+data);*/
    var dataMenuRole = data.split("|");
    /*console.log(" group: "+dataMenuRole[1]+" role: "+dataMenuRole[2]+" modulo: "+dataMenuRole[3]+" opcion: "+dataMenuRole[4]+" rolemm: "+dataMenuRole[5]);*/
    $('#modalMenuRole').modal('show');
    $('h4.modal-title').text('Actualizar Opciones de Menú Roles');
    $('#operacion').val('update');

    accionBtn(1);

   /** cargar info select */     
   cargarInfoSelect(dataMenuRole);

   $('#groups').attr("style", "pointer-events: none;");
   $('#roles').attr("style", "pointer-events: none;");

}

function getMenuRole(eval){

    console.log("GetMenuRole");
    var data= $(eval).closest('tr').attr('id');
    /*console.log("Data: "+data);*/
    var dataMenuRole = data.split("|");
    /*console.log("group: "+dataMenuRole[0]+" modulo: "+dataMenuRole[1]+" opcion: "+dataMenuRole[2]+" role: "+dataMenuRole[3]);*/
    $('#modalMenuRole').modal('show');
    $('h4.modal-title').text('Visualizar Opciones de Menú Roles');

    accionBtn(2);   
    $('#operacion').val('View');

    cargarInfoSelect(dataMenuRole);

    $('#groups').attr("style", "pointer-events: none;");
    $('#roles').attr("style", "pointer-events: none;");
    $('#modulo').attr("style", "pointer-events: none;");
    $('#opcion_padre').attr("style", "pointer-events: none;");

}

function cargarInfoSelect(dataMenuRole){

    console.log('cargarInfoSelect');
    $("#groups option").each(function(){
        
        var group_id = $(this).val();
        /*console.log('Group: '+ group_id);    */    

        if(group_id.split("-")){
            /*console.log('Explode');  */
            var group = group_id.split("-");            
            /*console.log('0:'+group[0]+' 1: '+group[1]+' 2: '+group[2]); */
            if(group[2] == dataMenuRole[0]){
                $('#groups').val(group_id); 
            }
        }else{
            /*console.log('NoExplode');*/
            if(group_id == dataMenuRole[0]){
                $('#groups').val(group_id); 
            }
        }   
        CargarRolesEmpresas();
    });

    $("#roles option").each(function(){

        var role_id = $(this).val();
        /*console.log('Roles: '+ role_id);    */ 
        
        if(role_id.split("-")){
            var roles = role_id.split("-");
            /*console.log('0: '+roles[0]+' 1: '+roles[1]+' 2: '+roles[2]); */
            if(roles[2] == dataMenuRole[3]){
                $('#roles').val(role_id); 
            }
        }else{
            if(role_id == dataMenuRole[3]){
                $('#roles').val(role_id); 
            }
        }
    });
    
    $("#modulo option").each(function(){

        var modulo_id = $(this).val();
        /*console.log('Modulo: '+ modulo_id);     */
        
        if(modulo_id.split("-")){
            var modulo = modulo_id.split("-");
            /*console.log('0: '+modulo[0]+' 1: '+modulo[1]+' 2: '+modulo[2]); */
            if(modulo[0] == dataMenuRole[1]){
                $('#modulo').val(modulo_id); 
            }
        }else{
            if(modulo_id == dataMenuRole[1]){
                $('#modulo').val(modulo_id); 
            }
        }
        cargarOpcion();
    });

    $("#opcion_padre option").each(function(){

        var op_padre_id = $(this).val();
        /*console.log('Opcion Padre: '+ op_padre_id);   */  

        if(op_padre_id.split("-")){
            var op_padre = op_padre_id.split("-");
            /*console.log('0: '+op_padre[0]+' 1: '+op_padre[1]+' 2: '+op_padre[2]); */
            if(op_padre[0] == dataMenuRole[2]){
                $('#opcion_padre').val(op_padre_id); 
            }
        }else{
            if(op_padre_id == dataMenuRole[2]){
                $('#opcion_padre').val(op_padre_id); 
            }
        }
    });

    /*console.log("group: "+$('#groups').val()+" modulo: "+$('#modulo').val()+" opcion: "+$('#opcion_padre').val()+" role: "+$('#roles').val()+' Op: '+$('#operacion').val());*/
}

function activeMenuRole(eval){

    console.log("Active");
    var data= $(eval).closest('tr').attr('id');
    /*console.log("Data: "+data);*/
    var dataMenuRole = data.split("|");
    /*console.log("group: "+dataMenuRole[0]+" modulo: "+dataMenuRole[1]+" opcion: "+dataMenuRole[2]+" role: "+dataMenuRole[3]);*/
    $('#modalRoleConfirm').modal('show');

    $("#modal-role-btn-si").on("click", function(){
        console.log("Confirm");
        $.ajax({
            type: "POST",
            url:'<?php echo base_url() ?>/menu/activeMenuRole',
            data: {               
                group:  dataMenuRole[0],
                modulo: dataMenuRole[1],
                opcion: dataMenuRole[2],
                role: dataMenuRole[3],
                
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

function deleteMenuRole(eval){

    console.log("Delete");
    var data= $(eval).closest('tr').attr('id');
   /* console.log("Data: "+data);*/
    var dataMenuRole = data.split("|");
    /*console.log("group: "+dataMenuRole[0]+" modulo: "+dataMenuRole[1]+" opcion: "+dataMenuRole[2]+" role: "+dataMenuRole[3]);*/
    $('#modalRoleConfirm').modal('show');
    
    $("#modal-role-btn-si").on("click", function(){
        console.log("Confirm");
        $.ajax({
            type: "POST",
            url:'<?php echo base_url() ?>/menu/deleteMenuRole',
            data: {               
                group:  dataMenuRole[0],
                modulo: dataMenuRole[1],
                opcion: dataMenuRole[2],
                role: dataMenuRole[3],
                
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

    /*console.log("group: "+$('#groups').val()+" modulo: "+$('#modulo').val()+" opcion: "+$('#opcion_padre').val()+" role: "+$('#roles').val()+' Op: '+$('#operacion').val());*/

    if(btn == 2 ){

        $('#btnCerrarMenuRole').show();
        $('#btnSaveMenuRole').hide();
        $('#CancelMenuRole').hide();

    }else{
        $('#btnCerrarMenuRole').hide();
        $('#btnSaveMenuRole').show();
        if($('#operacion').val() == 'insert'){
            $('#btnSaveMenuRole').attr("disabled", true);        
        }
        if($('#operacion').val() == 'update'){
            $('#btnSaveMenuRole').attr("disabled", false);
        }               
        $('#CancelMenuRole').show();
        
        $('#groups').attr("style", "pointer-events: auto;");
        $('#roles').attr("style", "pointer-events: auto;");
        $('#modulo').attr("style", "pointer-events: auto;");
        $('#opcion_padre').attr("style", "pointer-events: auto;");
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