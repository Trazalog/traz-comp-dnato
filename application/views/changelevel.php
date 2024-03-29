<!-- ________________ FORMULARIO _____________ -->
<div class="col-lg-4 col-lg-offset-4">
	<h2>Cambio de Rol</h2>
	<h5>Hola <span><?php echo $first_name; ?></span>, <br>Por favor cambie el rol de usuario.</h5>     
	<?php //$fattr = array('class' => 'form-signin');
		//echo form_open(site_url().'main/changelevel/', $fattr); ?>		
		<!--  sistema -->
		
    <div class="form-group">
		<h3>Gestión de Indentidades</h3>
				<label for="email" name="email">Usuarios:</label>
        <select class="form-control selec_habilitar" name="email" id="email">
					<option value="-1" disabled selected >-Seleccione Usuario-</option>
					<?php
            foreach($users as $row)
            { 
              echo '<option value="'.$row->email.'">'.$row->email.'</option>';
            }
          ?>
        </select>
		</div>
		<div class="form-group">
				<label for="level" name="level">Perfil:</label>
        <select class="form-control selec_habilitar" name="level" id="level">
					<option value="-1" disabled selected >-Seleccione Rol-</option>
					<?php
            foreach($dd_list as $indice => $op)
            { 
              echo '<option value="'.$indice.'">'.$op.'</option>';
            }
          ?>
        </select>
		</div>
		<div class="col-md-12 col-sm-12 col-xs-12">				
			<div class="form-group">
				<button class="btn btn-primary pull-right" onclick="guardar()" style="margin-top: 10px;">Guardar</button>
			</div>
		</div>	
		
		<?php //echo form_close(); ?>	
		<!--  / sistema -->

		<!--_________________SEPARADOR_________________-->
			<div class="col-md-12 col-sm-12 col-xs-12"><hr></div>
		<!--_________________SEPARADOR_________________-->	
		<div class="col-md-12 col-sm-12 col-xs-12"></div>				
		<!--  BPM -->								
			<div class="form-group">
			<h3>Sistema</h3>
					<label for="groups" name="groups">Grupo:</label>
					<select class="form-control bpm_deshab" name="groups" id="groups" disabled>
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
					<label for="roles" name="roles">Rol:</label>
					<select class="form-control bpm_deshab" name="roles" id="roles" disabled = "disabled">
						<option value="-1" disabled selected >-Seleccione Roles BPM-</option>
						<?php
								foreach($roles as $rol)
								{
									echo '<option value="'.$rol->id.'-'.$rol->name.'">'.$rol->displayName.'</option>';
								}
						?>
					</select>
			</div>
	
		<!--  / BPM -->
		<div class="col-md-12 col-sm-12 col-xs-12">						
			<div class="form-group">
				<button class="btn btn-primary pull-right"  id="btn_agregar" onclick="agregar()"disabled style="margin-top: 10px;">Agregar</button>
			</div>
		</div>	
</div>
<!-- _________________________________________ -->

<!--_________________SEPARADOR_________________-->
<div class="col-md-12 col-sm-12 col-xs-12"><hr></div>
<!--_________________SEPARADOR_________________-->

<!-- ______ TABLA TEMPORAL ______ -->
<div class="col-md-12 col-sm-12 col-xs-12">
	
	<table id="tbl_temporal" class="table table-bordered table-striped">
			<thead class="thead-dark" bgcolor="#eeeeee">
					<th class="hidden">id de usuario</th>					
					<th>Borrar</th>
					<th>Grupo</th>					
					<th>Rol</th>					
			</thead>
			<tbody >
			</tbody>
	</table>											

</div>		

<!--_______ FIN TABLA TEMPORAL ______-->


<div class="col-md-12 col-sm-12 col-xs-12">
		
</div>
    

</div> 

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>

<script>
	
	function guardar(){
	
		var fila = $("#tbl_temporal tbody tr");
		var membership = [];

		fila.each(function(i,e) {
			
			var user_id = $(this).find("td").eq(0).html();
			var group_id = $(this).find("td").eq(4).html();
			var role_id	= $(this).find("td").eq(5).html();
			tmp = {};		
			tmp.coen_id = user_id;
			tmp.group = group_id;
			tmp.role  = role_id;
			membership.push(tmp);
		});

		var email = $('#email').val();
		var level = $('#level').val();
		console.log(email+' '+level);
		$.ajax({
				type: 'POST',
				data:{email:email,
							level:level},
				url:'<?php echo base_url() ?>/main/cambiarNivelUsr',
				
				success: function($result){

							$(".selec_habilitar").attr('disabled', 'disabled');
							$(".bpm_deshab").attr("disabled", false);
							$("#btn_agregar").removeAttr('disabled'); 
				},
				error:function($result){

				}
				
		});		
	}


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

				console.log(membershipBPM);
				console.log(membership);

				/*
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
				});*/

		
		}else{
				alert("Seleccione un Grupo y un rol antes de agregar por favor...");
				return;
		}
	}

	$(document).on("click",".fa-trash",function() {
			var row = $(this).parents("tr");
			var membership = [];
			var user_id = $(this).parents("tr").find("td").eq(0).html();
			var group_id = $(this).parents("tr").find("td").eq(2).html();
			var role_id	= $(this).parents("tr").find("td").eq(3).html();
			tmp = {};
			tmp.email = user_id;
			tmp.group = group_id;
			tmp.role  = role_id;
			membership.push(tmp);

			$.ajax({
				type: 'POST',
				data:{ membership },
				url: '<?php echo base_url() ?>/main/borrarMembership',
				success: function(result) {
					row.remove();
				},
				error: function(result){
						
				},
				complete: function(){
									
				}
			});	
	});

</script>

