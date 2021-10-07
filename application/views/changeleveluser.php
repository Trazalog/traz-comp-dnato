<!-- ________________ FORMULARIO _____________ -->
<div class="col-lg-12 col-lg-offset-0">
	<h2>Cambio de Rol</h2>
	<h5>Hola <span><?php echo $first_name; ?></span>. Aquí puede realizar cambios de roles de usuario para el usuario. </h5>
    <hr> 
<!-- /.box-header -->
<div class="box-body">
        <div class="row">
            <div class="col-md-4 col-lg-offset-0">
                <h4>Edición de Roles</h4>
                <div class="form-group">
                    <label>Email: </label>  <?php echo ' '.$user->email; ?><br>
                    <label>Nombre: </label> <?php echo $user->first_name. ' '. $user->last_name; ?>
                </div>
                <!-- /.form-group -->
                <div class="form-group">
                    <label for="level" name="level">Perfil:</label>
                    <select class="form-control selec_habilitar" name="level" id="level">
                            <option value="-1" >-Seleccione Rol-</option>
                            <?php
                                foreach($dd_list as $indice => $op)
                                { 
                                    if($user->role == $indice){
                                        echo '<option selected="selected" value="'.$indice.'">'.$op.'</option>';    
                                    }else{
                                        echo '<option value="'.$indice.'">'.$op.'</option>';
                                    }
                                }
                            ?>
                    </select>
                </div>
                <!-- /.form-group -->
            </div>
            <!-- /.col -->
            <div class="col-md-7 col-lg-offset-1">
                <h4>Roles en el Sistema
                    <button type="button" class="btn pull-right btn-primary" data-toggle="modal" onclick="modalRol();" >Agregar Rol</button>
                </h4>
                <!-- ______ TABLA TEMPORAL ______ -->
                    
                    <table id="tbl_temporal" class="table table-striped">
                            <thead >					
                                    <th class="hidden">email</th>					
                                    <th class="hidden">group_id</th>					
                                    <th>Empresa</th>					
                                    <th>Rol</th>					
                                    <th>Acción</th>
                            </thead>
                            <tbody >
                                <?php  
                                    foreach($mem_user as $members){ 
                                        foreach($groups as $group){
                                            list($id_group, $group_name) = explode ("-",$group->name);
                                            if($members->group === $group_name){

                                                if($members->email !== null){    
                                                     
                                                    echo '<tr>';
                                                    echo '<td class="hidden">'. $members->email. '</td>';
                                                    echo '<td class="hidden">'.$group->id.'-'.$group->name. '</td>';
                                                    echo '<td>'. $group->displayName. '</td>';
                                                    echo '<td>'. $members->role. '</td>';
                                                    echo '<td><i class="fa fa-trash text-red" aria-hidden="true" style="cursor: pointer;" onclick="EliminarRolUsuario()"></i></td>';
                                                    echo '</tr>';
                                                }
                                                break;
                                            }
                                        }
                                    } ?>
                            </tbody>
                    </table>											

                <!--_______ FIN TABLA TEMPORAL ______-->
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->
    </div>
    <!-- /.box-body -->	
    <br>
    <div class="modal-footer">
        <a href= <?php echo site_url().'main/users' ?>><button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button></a>
        <button type="button" class="btn btn-primary" onclick="guardarRolesUsuario();">Guardar</button>
    </div>

</div>

<!--_______ MODAL ______-->
<div class="modal fade" id="mdlRolEmpresa">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Nuevo Rol Para el Usuario: <strong> <?php echo $user->first_name. ' '. $user->last_name; ?></strong></h4>
            </div>
            
            <div class="modal-body">

				<div class="alert alert-danger alert-dismissable" id="errorModal" style="display: none">
					Revise que todos los campos esten completos
				</div>
				
                <div class="alert alert-danger alert-dismissable" id="errorModalAsig" style="display: none">
                    Revise. El rol seleccionado ya ha sido asignado
				</div>

				<form class="form-horizontal" id="formRolEmprresa">
                
					<div class="form-group">
                        <label >email: </label>
                        <input id="emailuser" name="emailuser" type="text" value="<?php echo $user->email; ?>" />
                    </div>                    
					<div class="form-group">
						<label class=" control-label" for="codigo">Empresa:</label>
						<select class="form-control " name="groups" id="groups" >
						<option value="-1" disabled selected>-Seleccione Grupos BPM-</option>
						<?php
                            if(empty($mem_user->emp_core)){
                                foreach($groups as $group){
    
                                    list($id_group, $group_name) = explode ("-",$group->name);
                                    if($members->group === $group_name){
                                        echo '<option value="'.$group->id.'-'.$group->name.'">'.$group->displayName.'</option>';
                                    }
                                }
                            }else{
                                foreach($mem_user as $members){ 
                                    foreach($groups as $group){
    
                                        list($id_group, $group_name) = explode ("-",$group->name);
                                        if($members->group === $group_name){
                                            echo '<option value="'.$group->id.'-'.$group->name.'">'.$group->displayName.'</option>';
                                        }
                                    }
                                }
                            }
						?>
					</select>
					</div>                    

					<div class="form-group">
						<label class=" control-label" for="fecha_vencimiento">Rol:</label>
						<select class="form-control " name="roles" id="roles" >
                                <option value="-1" disabled selected >-Seleccione Roles BPM-</option>
                                <?php
                                        foreach($roles as $rol){
                                            echo '<option value="'.$rol->id.'-'.$rol->name.'">'.$rol->displayName.'</option>';
                                        }
                                ?>
                            </select>
					</div>
					</form>
				</div> <!-- /.modal-body -->

            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="agregarRoles('<?php echo $user->id; ?>');">Agregar</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Finalizar</button>
            </div>
        </div>
        <!-- /.modal-content -->
      </div>
      <!-- /.modal-dialog -->
    </div>
    <!-- /.modal -->
 <!--_______ MODAL ______-->


 <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>


<script>

    function modalRol(){
        $("#mdlRolEmpresa").modal('show');
        $("#groups").val('-1');
        $("#roles").val('-1');  
        document.getElementById('errorModal').style.display = 'none';
        document.getElementById('errorModalAsig').style.display = 'none';
    }

    function agregarRoles(userId){

        var email = $("#emailuser").val();
        var groupId = $("#groups option:selected").val();
        var roleId = $("#roles option:selected").val();
        
        if((groupId !== '-1') && (roleId !== '-1')){
            
            /*console.log('1 Grupo: '+groupId+' Rol: '+roleId+' User: '+userId+' Email: '+email); */
            document.getElementById('errorModal').style.display = 'none';
                     
            /** Extrae la tabla HTML */
            var table = new Array();
            $('#tbl_temporal tr').each(function(row, tr) {

                var email = $(tr).find('td:eq(0)').text();
                var group = $(tr).find('td:eq(1)').text();
                var role  = $(tr).find('td:eq(2)').text();
                if(email !== '' && group !== '' && role !== '')
                    table[row] = {
                        "email": $(tr).find('td:eq(0)').text(),
                        "group_id": $(tr).find('td:eq(1)').text(),
                        "group": $(tr).find('td:eq(2)').text(),
                        "role": $(tr).find('td:eq(3)').text()
                    }
            });  
            var data = table.filter(Boolean);
            var dataRole = JSON.stringify(data);
            console.log(data);
            
            var icon = '<i class="fa fa-trash text-red" aria-hidden="true" style="cursor: pointer;" onclick="EliminarRolUsuario()"></i>';
            var group_nombre = $("#groups option:selected").text();
            var role_nombre = $("#roles option:selected").text();
            var row =   '<tr>'+ 
                        '<td class="hidden" >' + email + '</td>'+
                        '<td class="hidden" >' + groupId + '</td>'+
                        '<td>'+ group_nombre  +'</td>'+
                        '<td>'+ role_nombre  +'</td>'+
                        '<td>' + icon + '</td>'+
            '</tr>';  
           /**console.log(row);*/

            /** Revisa que no esten repetidos los roles */
            var sw = true;
            for (let i = 0; i < data.length; i++) {
                 /**console.log(data[i].group +' '+data[i].role); */
                 /**console.log(data[i].group === group_nombre && data[i].role === role_nombre);*/
                if(data[i].group === group_nombre && data[i].role === role_nombre ){
                    sw = false;
                    break;
                }
            }

            if(sw){
                document.getElementById('errorModalAsig').style.display = 'none';
                $("#tbl_temporal tbody").append(row);
            }else{
                document.getElementById('errorModalAsig').style.display = 'block';
            }
            
        }else{  
            document.getElementById('errorModal').style.display = 'block';
            return;
        }

    }

    

    function EliminarRolUsuario(email,group,role){
        console.log(email+' '+group+' '+role);    
    }

    function guardarRolesUsuario(){

        var email = $("#emailuser").val();
        var level = $("#level option:selected").val();
        
        var table = new Array();
        $('#tbl_temporal tr').each(function(row, tr) {

            var email = $(tr).find('td:eq(0)').text();
            var group = $(tr).find('td:eq(1)').text();
            var role  = $(tr).find('td:eq(2)').text();
            if(email !== '' && group !== '' && role !== '')
                table[row] = {
                    "email": $(tr).find('td:eq(0)').text(),
                    "group": $(tr).find('td:eq(1)').text(),
                    "role": $(tr).find('td:eq(2)').text()
                }
        });  
        var data = table.filter(Boolean);
        var dataRole = JSON.stringify(data);
        console.log(dataRole);

        $.ajax({
			type: "POST",
			/*url:'<?php echo base_url() ?>/main/changeLevelRolUser',*/
			data: {
				dataRole: dataRole,
				email: email,
				level: level
			},
			success: function(rsp) {
				alert("Guarado correctamente.");
			},
			error: function() {
				alert("Se produjo un error al guardar rol/nivel del usuario.");
			},
			complete: function() {

			}
		});

    }

</script>