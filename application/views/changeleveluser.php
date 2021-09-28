<!-- ________________ FORMULARIO _____________ -->
<div class="col-lg-12 col-lg-offset-0">
	<h2>Cambio de Rol</h2>
	<h5>Hola <span><?php echo $first_name; ?></span>. Aquí puede realizar cambios de roles de usuario para el usuario. </h5>
    <hr>     

    <!-- /.box-header -->
    <div class="box-body">
        <div class="row">
            <div class="col-md-4">
                <h4>Edición de Roles</h4>
                <div class="form-group">
                    <label>Nro. Documento: </label><?php echo ' '.$user->dni; ?><br>
                    <label>Email: </label> <?php echo ' '.$user->email; ?><br>
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
            <div class="col-md-8 col-lg-offset-0">
                <h4>Roles en el Sistema
                    <button type="button" class="btn pull-right btn-primary" data-toggle="modal" data-target="#modalRolEmpresa">Agregar</button>
                </h4>
                <!-- ______ TABLA TEMPORAL ______ -->
                    
                    <table id="tbl_temporal" class="table table-striped">
                            <thead >
                                    <th class="hidden">IdRolEmpresa</th>					
                                    <th>Acción</th>
                                    <th>Empresa</th>					
                                    <th>Rol</th>					
                            </thead>
                            <tbody >
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
        <button type="button" class="btn btn-primary">Guardar</button>
    </div>

</div>


 <!--_______ MODAL ______-->
<div class="modal fade" id="modalRolEmpresa">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Nuevo Rol Para el Usuario: <strong> <?php echo $user->first_name. ' '. $user->last_name; ?></strong></h4>
            </div>
            
            <div class="modal-body">

				<div class="alert alert-danger alert-dismissable" id="error" style="display: none">
				    <h4><i class="icon fa fa-ban"></i> Error!</h4>
					Revise que todos los campos esten completos
				</div>

				<form class="form-horizontal" id="frm-NoConsumible">
                
					<div class="form-group">
						<label class=" control-label" for="codigo">Empresa:</label>
						<select class="form-control bpm_deshab" name="groups" id="groups">
						<option value="-1" disabled selected>-Seleccione Grupos BPM-</option>
						<?php
								foreach($groups as $gr)
								{
									echo '<option value="'.$gr->id.'-'.$gr->name.'">'.$gr->displayName.'</option>';
								}
						?>
					</select>
					</div>                    

					<div class="form-group">
						<label class=" control-label" for="fecha_vencimiento">Rol:</label>
						<select class="form-control bpm_deshab" name="roles" id="roles">
                                <option value="-1" disabled selected >-Seleccione Roles BPM-</option>
                                <?php
                                        foreach($roles as $rol)
                                        {
                                            echo '<option value="'.$rol->id.'-'.$rol->name.'">'.$rol->displayName.'</option>';
                                        }
                                ?>
                            </select>
					</div>
					</form>
				</div> <!-- /.modal-body -->

            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="agregar()">Agregar</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Finalizar</button>
            </div>
        </div>
        <!-- /.modal-content -->
      </div>
      <!-- /.modal-dialog -->
    </div>
    <!-- /.modal -->
 <!--_______ MODAL ______-->

<script>

function agregar(){

    var group_id = $("#groups option:selected").val();
    var role_id = $("#roles option:selected").val();

    if ((group_id != -1) || (role_id != -1)) {
    debugger;
            var user_id = $("#email").val();
            var icon = "<i class='fa fa-trash' aria-hidden='true'></i>";
            var goup_nombre = $("#groups option:selected").text();
            var role_nombre = $("#roles option:selected").text();

            var row = '<tr>'+ 
                    '<td class="hidden">' + user_id + '</td>'+
                    '<td>' + icon + '</td>'+
                    '<td>'+ goup_nombre  +'</td>'+
                    '<td>'+ role_nombre  +'</td>'+
            '</tr>';

            var membership = {};
            membership.email = user_id;
            var grupo = group_id.split("-");
            var name = grupo[2];
            membership.group = name;
            membership.role = role_nombre;

            var membershipBPM = {};
            membershipBPM.group_id = grupo[0];
            var rolarray = role_id.split("-");
            membershipBPM.role_id = rolarray[0];

            $.ajax({

                    type: 'POST',
                    data:{ membership, membershipBPM },
                    url: '<?php echo base_url() ?>/main/guardarMembership',
                    success: function(result) {

                        $("#tbl_temporal tbody").append(row);
                    },
                    error: function(result){
                        alert('Error al guardar membresia...');
                    },
                    complete: function(){

                    }
            });


    }else{
            alert("Seleccione un Grupo y un rol antes de agregar por favor...");
            return;
    }
}


</script>
