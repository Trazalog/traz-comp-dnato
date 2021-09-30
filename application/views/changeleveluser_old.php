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
            <div class="col-md-8 col-lg-offset-0">
                <h4>Roles en el Sistema
                    <button type="button" class="btn pull-right btn-primary" data-toggle="modal" onclick="modalRol();" >Agregar Rol</button>
                </h4>
                <!-- ______ TABLA TEMPORAL ______ -->
                    
                    <table id="tbl_temporal" class="table table-striped">
                            <thead >					
                                    <th class="hidden">email</th>					
                                    <th>Empresa</th>					
                                    <th>Rol</th>					
                                    <th>Acción</th>
                            </thead>
                            <tbody >
                                <?php foreach($mem_user as $members){ ?>
                                    <tr>
                                        <td class="hidden"><?php echo $members->email ?></td>
                                        <td><?php echo $members->group ?></td>
                                        <td><?php echo $members->role ?></td>
                                        <td><i class='fa fa-trash' aria-hidden='true'></i></td>
                                        
                                    </tr>
                                <?php } ?>
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
				    <h4><i class="icon fa fa-ban"></i> Error!</h4>
					Revise que todos los campos esten completos
				</div>

				<form class="form-horizontal" id="frm-NoConsumible">
                
					<div class="form-group">
                        <label >email: </label>
                        <input id="emailuser" name="emailuser" type="text" value="<?php echo $user->email; ?>" />
                    </div>                    
					<div class="form-group">
						<label class=" control-label" for="codigo">Empresa:</label>
						<select class="form-control " name="groups" id="groups" >
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
						<select class="form-control " name="roles" id="roles" >
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
                <button type="button" class="btn btn-primary" onclick="agregarRoles(<?php echo $user->id; ?>);">Agregar</button>
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

//<--_______ PAGE ______-->
                                    
 //<--_______ MODAL _____-->
 function modalRol(){

$("#modalRolEmpresa").modal('show');
$("#groups").val('-1');
$("#roles").val('-1');  
document.getElementById('errorModal').style.display = 'none';
}

    function agregarRoles(userId){

        var groupId = $("#groups option:selected").val();
        var roleId = $("#roles option:selected").val();
        var email = $("#emailuser").val();
        console.log('0 Grupo: '+groupId+' Rol: '+roleId+' User: '+userId+' Email: '+email);

        if((groupId !== '-1') && (roleId !== '-1')){
            
            document.getElementById('errorModal').style.display = 'none';
        
            
            var icon = "<i class='fa fa-trash' aria-hidden='true'></i>";
            var goup_nombre = $("#groups option:selected").text();
            var role_nombre = $("#roles option:selected").text();

            console.log('1 Grupo: '+groupId+' Rol: '+roleId+' User: '+userId+' Email: '+email);
            var rowCount = $("#tbl_temporal > tbody > tr").length;
            var rows = $("#tbl_temporal > tbody > tr");
            console.log(rowCount);
            console.log(rows);


            var row =   '<tr>'+ 
                        '<td class="hidden">' + email + '</td>'+
                        '<td>' + icon + '</td>'+
                        '<td>'+ goup_nombre  +'</td>'+
                        '<td>'+ role_nombre  +'</td>'+
            '</tr>';
            $("#tbl_temporal tbody").append(row);
        }else{
            console.log('2 Grupo: '+groupId+' Rol: '+roleId+' User: '+userId+' Email: '+email);
            document.getElementById('errorModal').style.display = 'block';
        
        }
    }

</script>
