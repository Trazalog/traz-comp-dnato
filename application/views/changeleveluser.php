    <!-- ________________ FORMULARIO _____________ -->
    <div class="col-lg-12 col-lg-offset-0">
        <h2>Cambio de Rol</h2>
        <h5>Hola <span><?php echo $first_name; ?></span>. Aquí puede realizar cambios de roles de usuario para el usuario. </h5>
        <hr> 
    <!-- /.box-header -->
    <div class="box-body">
        <div class="col-lg-12">
            <div class="alert alert-success alert-dismissable" id="saveRol"  style="display: none">
                <!--<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>-->
				Guardado. Los cambios y asignaciones fueron guardados correctamente.
			</div>
				
            <div class="alert alert-danger alert-dismissable" id="errorRol" style="display: none">
                <!--<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>-->
                Revise. Se produjo un error al guardar roles/niveles del usuario.
			</div>

            <div class="alert alert-success alert-dismissable" id="saveDeleteRol" style="display: none">
                <!--<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>-->
                Eliminado. Se han eliminado correctamente el rol del usuario.
			</div>
            <div class="alert alert-danger alert-dismissable" id="errorDeleteRol" style="display: none">
                <!--<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>-->
                Revise. Se produjo error al eliminar el rol del usuario.
			</div>
        </div>    
    
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
                                    <th class="hidden">role_id</th>					
                                    <th>Empresa</th>					
                                    <th>Rol</th>					
                                    <th>Acción</th>
                            </thead>
                            <tbody >
                                <?php  

                                    if($mem_user){
                                        foreach($mem_user as $membUser){

                                            foreach($groups as $group){
                                                list($id_group, $group_name) = explode ("-",$group->name);
                                                if($group_name == $membUser->group){ 
                                                    $nameGroup = $group->displayName; 
                                                    $idGroup = $group->id.'-'.$group->name;                                              
                                                    break;
                                                }                                            
                                            }

                                            foreach($roles as $rol){
                                                
                                                if(strpos($membUser->role,'-') !== false) { 
                                                    //Revisar esto no puedo hacer explode 
                                                    

                                                    // explodable 
                                                    $idRole = 'Si';
                                                    list($role_id, $role_name) = explode ("-",$rol->name);
                                                    if(($role_name == $membUser->role ) || (strpos($rol->name,$membUser->role ))){
                                                        $nameRole = $rol->displayName;
                                                        $idRole = $rol->id.'-'.$rol->name;
                                                        break;
                                                    }
                                                } else {
                                                    // not explodable
                                                    //$idRole = 'No';
                                                    if(strpos($rol->name,$membUser->role ) || ($rol->name == $membUser->role)){
                                                        $nameRole = $rol->displayName;
                                                        $idRole = $rol->id.'-'.$rol->name;
                                                        break;
                                                    }
                                                }

                                            }

                                            if($idGroup != '' && $nameGroup != ''  && $idRole != '' && $nameRole) {
                                                echo "<tr id='".$membUser->email."/".$idGroup."/".$idRole."'>";                                            
                                                echo "<td class='hidden'>".$membUser->email ."</td>";
                                                echo "<td class='hidden'>".$idGroup ."</td>";
                                                echo "<td class='hidden'>".$idRole ."</td>";
                                                echo "<td>".$nameGroup ."</td>";
                                                echo "<td>".$nameRole ."</td>";
                                                echo "<td><i class='fa fa-trash text-red' aria-hidden='true' style='cursor: pointer;' onclick='EliminarRolUsuario(this)'></i></td>";
                                                echo "</tr>";
                                            }
                                        }
                                    }
                                ?>
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
                <h4 class="modal-title">Nuevo Rol Para el Usuario: 
                    <strong> 
                        <?php echo $user->first_name. ' '. $user->last_name; ?>
                    </strong></h4>
            </div>
            
            <div class="modal-body">

				<div class="alert alert-danger alert-dismissable" id="errorModal" style="display: none">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
					Revise que todos los campos esten completos
				</div>
				
                <div class="alert alert-danger alert-dismissable" id="errorModalAsig" style="display: none">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    Revise. El rol seleccionado ya ha sido asignado
				</div>

				<form class="form-horizontal" id="formRolEmprresa">
                
					<div class="form-group hidden">
                        <label  >email: </label>
                        <input id="emailuser" name="emailuser" type="text" value="<?php echo $user->email; ?>" />
                    </div>    

					<div class="form-group">
						<label class=" control-label" for="codigo">Empresa:</label>
						<select class="form-control " name="groups" id="groups" onchange="CargarRolesEmpresas();" >
						    <option value="-1" disabled selected>-Seleccione Grupos BPM-</option>
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

                            echo '</select>';
                            echo '</div>';                    
                            
                            echo '<div class="form-group">';
                            echo '<label class=" control-label" for="roles">Rol:</label>';
                            echo '<select class="form-control " name="roles" id="roles" >';
                            echo '<option value="-1" disabled selected >-Seleccione Roles BPM-</option>';
                    
                    
                        ?>
                    </select>
					</div>
					</form>
				</div> <!-- /.modal-body -->

            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="agregarRoles('<?php echo $user->id; ?>');">Agregar</button>
                <button type="button" class="btn btn-success" data-dismiss="modal">Finalizar</button>
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

    function CargarRolesEmpresas(){

        var group =$('#groups').val();
        /*console.log("group: "+group);*/
        var role = <?php echo json_encode($roles) ?>;
        /*console.log("role: "+JSON.stringify(role));*/

        sgroup = group.split("-");
        /*console.log("empId: "+sgroup[1]);*/
        addOptions("roles", role,sgroup[1]);
    }

    /* Rutina para agregar opciones a un <select>*/
    function addOptions(domElement, json, sgroup) {

        var option = '';
        var srole ='';
        /*$("#roles").empty();*/
        /*console.log("DE: "+domElement+" JSON: "+JSON.stringify(json)+" SGROUP: "+sgroup);*/
        $('#roles')[0].options.length = 0;
        var select = document.getElementsByName(domElement)[0];
        
        /*console.log("select: "+select);*/
        Object.keys(json).forEach(function(elm) {
            /*console.log("Name: "+ json[elm]['name']+" id: "+ json[elm]['name']+" displayName: "+ json[elm]['displayName']);*/
            srole= json[elm]['name'].split("-");
            /*console.log("sgroup: "+sgroup+" srole[0]: "+srole[0]+" srole[1]: "+srole[1]);*/
            if(sgroup == srole[0]){
                option = '<option value="'+json[elm]['id']+'-'+json[elm]['name']+'">'+json[elm]['displayName']+'</option>';
                console.log("OPTION: "+option);
                $('#roles').append(option);
            }
        
        })
    }

    function url(){
        window.location.href="<?php echo site_url() ?>main/users";
    }


    function modalRol(){
        $("#mdlRolEmpresa").modal('show');
        $("#groups").val('-1');
        $("#roles").val('-1');  
        document.getElementById('errorModal').style.display = 'none';
        document.getElementById('errorModalAsig').style.display = 'none';

        document.getElementById('saveDeleteRol').style.display = 'none';
        document.getElementById('saveRol').style.display = 'none';
        document.getElementById('errorRol').style.display = 'none';
        document.getElementById('errorDeleteRol').style.display = 'none';
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
                        "role_id": $(tr).find('td:eq(2)').text(),
                        "group": $(tr).find('td:eq(3)').text(),
                        "role": $(tr).find('td:eq(4)').text()
                    }
            });  
            var data = table.filter(Boolean);
            var dataRole = JSON.stringify(data);
            /*console.log(data);*/
            
            
            var group_nombre = $("#groups option:selected").text();
            var role_nombre = $("#roles option:selected").text();
            var row =   "<tr id='"+email+'/'+groupId+'/'+roleId+"'>"+    
                        '<td class="hidden">' + email + '</td>'+
                        '<td class="hidden">' + groupId + '</td>'+
                        '<td class="hidden">' + roleId + '</td>'+
                        '<td>'+ group_nombre  +'</td>'+
                        '<td>'+ role_nombre  +'</td>'+
                        '<td><i class="fa fa-trash text-red" aria-hidden="true" style="cursor: pointer;" onclick="EliminarRolUsuario(this)"></td>'+
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
    

    function EliminarRolUsuario(eval){

        /*($(eval).closest('tr').attr('id'));*/ 
        var data= $(eval).closest('tr').attr('id');
        var dataRol = data.split("/");
        /*console.log(dataRol);*/

        var email = dataRol[0];
        var groupId = dataRol[1];
        var rolId = dataRol[2];
        /*console.log(email+' '+groupId+' '+rolId);*/

        var group = groupId.split("-");
        var role =  rolId.split("-");

        var grupo = group[0];
        var rol = role[0];

        /*
        console.log(group.length);
        console.log(role.length);
        */

        var nombGrupo = group[(group.length-1)];
        var nombRole  = caseRolUsuario(role);

        var dataRole = {};
        var dataRoleBpm = {};

        dataRole.email = email;
        dataRole.group = nombGrupo;
        dataRole.role = nombRole;

        dataRoleBpm.group_id = grupo;
        dataRoleBpm.role_id= rol;
        
        /*
        console.log(email);
        console.log(dataRoleBpm);
        console.log(dataRole);
        */
        
        $.ajax({
            type: "POST",
            url:'<?php echo base_url() ?>/main/deleteLevelRolUser',
            data: {
                email: email,
                dataRole: dataRole,
                dataRoleBpm: dataRoleBpm
            },
            success: function(rsp) {
                console.log(rsp);
                document.getElementById('saveDeleteRol').style.display = 'block';
                document.getElementById('saveRol').style.display = 'none';
                document.getElementById('errorRol').style.display = 'none';
                document.getElementById('errorDeleteRol').style.display = 'none';
                $(eval).closest('tr').remove();
            },
            error: function(rsp) {
                console.log(rsp);
                document.getElementById('errorDeleteRol').style.display = 'block';
                document.getElementById('saveRol').style.display = 'none';
                document.getElementById('errorRol').style.display = 'none';
                document.getElementById('saveDeleteRol').style.display = 'none';
            },
            complete: function() {

            }
        });
    }

    function caseRolUsuario(role){

        /*console.log(role);*/
        var lenrole = role.length;
        switch (lenrole) {
        case 2:
            /*
            console.log("2: "+role.length);
            console.log(role[0]+" "+role[1]);
            */
            return role[1];
        break;

        case 3:
            /*
            console.log("3: "+role.length);
            console.log(role[0]+" "+role[1]+" "+role[2]);
            */
            return role[2];
        break;
        
        case 4:
            /*
            console.log("4: "+role.length);
            console.log(role[0]+" "+role[1]+" "+role[2]+" "+role[3]);
            */
            return role[3];
        break;
        
        case 5:
            /*
            console.log("5: "+role.length);
            console.log(role[0]+" "+role[1]+" "+role[2]+" "+role[3]+" "+role[4]);
            */
            return role[4];
        break;
        
        default:
            /*
            console.log("D: "+role.length);
            console.log(role[0]);
            */
            return role[0];
        break;
            
        }
    }



    /** Esta funcion no se encuentra en funcionamiento */
    function guardarRolesUsuarioOLD(){ 

        var email = $("#emailuser").val();
        var level = $("#level option:selected").val();
                
        var table = {};
        var tableBpm = {};
        $('#tbl_temporal tr').each(function(row, tr) {

            var email = $(tr).find('td:eq(0)').text();
            var group = $(tr).find('td:eq(1)').text();
            var role  = $(tr).find('td:eq(2)').text();
            if(email !== '' && group !== '' && role !== ''){
                
                var idGroup = group.split("-");
                var idRole = role.split("-");
                
                var grupo = idGroup[0];
                var rol = idRole[0];

                var nombGrupo= idGroup[(idGroup.length-1)];
                var nombRole=  idRole[(idRole.length-1)];

                tableBpm.group_id = grupo;
                tableBpm.role_id= rol;

                table.email = email;
                table.group = nombGrupo.replace(/^\s*|\s*$/g,"");
                table.role = nombRole.replace(/^\s*|\s*$/g,"");
            
            }
        });         

        /*var dataBpm = tableBpm.filter(Boolean);
        var dataRoleBpm = JSON.stringify(dataBpm);
        var RoleBpm = JSON.parse(dataRoleBpm);
        var data = table.filter(Boolean);
        var dataRole = JSON.stringify(data);
        var Role = JSON.parse(dataRole);*/
        
        
        console.log(tableBpm);
        console.log(table);
        
        
        $.ajax({
            type: "POST",
            url:'<?php echo base_url() ?>/main/changeLevelRolUser',
            data: {
                dataRole: table,
                dataRoleBpm: tableBpm,
                email: email,
                level: level
            },
            success: function(rsp) {               
                    
                document.getElementById('saveRol').style.display = 'block';
                document.getElementById('errorDeleteRol').style.display = 'none';
                document.getElementById('errorRol').style.display = 'none';
                document.getElementById('saveDeleteRol').style.display = 'none';
            },
            error: function() {
                      
                document.getElementById('errorRol').style.display = 'block';
                document.getElementById('errorDeleteRol').style.display = 'none';
                document.getElementById('saveRol').style.display = 'none';
                document.getElementById('saveDeleteRol').style.display = 'none';
            },
            complete: function() {

            }
        });

    }

    /** Esta funcion funcionamiento guardar actual */
    function guardarRolesUsuario(){

        var email = $("#emailuser").val();
        var level = $("#level option:selected").val();
                
        var table = {};
        var tableBpm = {};
        $('#tbl_temporal tr').each(function(row, tr) {

            var email = $(tr).find('td:eq(0)').text();
            var group = $(tr).find('td:eq(1)').text();
            var role  = $(tr).find('td:eq(2)').text();
            if(email !== '' && group !== '' && role !== ''){
                
                var idGroup = group.split("-");
                var idRole = role.split("-");
                
                var grupo =  idGroup[0];    /** group; */   /** idGroup[(idGroup.length-2)]+'-'+idGroup[(idGroup.length-1)];*/
                var rol = idRole[0];        /** role; */    /** idRole[(idRole.length-2)]+'-'+idRole[(idRole.length-1)]; */

                var nombGrupo= idGroup[(idGroup.length-1)];
                var nombRole=  idRole[(idRole.length-1)];
                /**BPM */
                tableBpm.group_id = grupo;
                tableBpm.role_id= rol;

                /** Local */
                table.email = email;
                table.group = nombGrupo.replace(/^\s*|\s*$/g,"");
                table.role = nombRole.replace(/^\s*|\s*$/g,"");
            
            }
        });         

        /*var dataBpm = tableBpm.filter(Boolean);
        var dataRoleBpm = JSON.stringify(dataBpm);
        var RoleBpm = JSON.parse(dataRoleBpm);
        var data = table.filter(Boolean);
        var dataRole = JSON.stringify(data);
        var Role = JSON.parse(dataRole);*/
        
        
        console.log(tableBpm);
        console.log(table);
        
        
        $.ajax({
            type: "POST",
            url:'<?php echo base_url() ?>/main/changeLevelRolUser',
            data: {
                dataRole: table,
                dataRoleBpm: tableBpm,
                email: email,
                level: level
            },
            success: function(rsp) {               
                    
                document.getElementById('saveRol').style.display = 'block';
                document.getElementById('errorDeleteRol').style.display = 'none';
                document.getElementById('errorRol').style.display = 'none';
                document.getElementById('saveDeleteRol').style.display = 'none';
            },
            error: function() {
                      
                document.getElementById('errorRol').style.display = 'block';
                document.getElementById('errorDeleteRol').style.display = 'none';
                document.getElementById('saveRol').style.display = 'none';
                document.getElementById('saveDeleteRol').style.display = 'none';
            },
            complete: function() {

            }
        });

    }

    
    function guardarRolesUsuarioObject(){

        var email = $("#emailuser").val();
        var level = $("#level option:selected").val();
                
        var table = [];
        var tableBpm = [];
        $('#tbl_temporal tr').each(function(row, tr) {

            var email = $(tr).find('td:eq(0)').text();
            var group = $(tr).find('td:eq(1)').text();
            var role  = $(tr).find('td:eq(2)').text();
            if(email !== '' && group !== '' && role !== ''){

                var idGroup = group.split("-");
                var idRole = role.split("-");
                /*console.log(idRole);*/
                /*Posicion 0 va los idRoles y IdGrupos*/
                var grupo = idGroup[0];
                var rol = idRole[0];
                /** Posiciones de los Roles y Grupos en BPM */
                var nombGrupo= idGroup[(idRole.length-1)];
                var nombRole=  idRole[(idRole.length-2)] +'-' +idRole[(idRole.length-1)];        
                /**Table Seg */
                table[row] = {
                    "email": email,
                    "group": nombGrupo,
                    "role": nombRole
                },
                /** Table BPM */
                tableBpm[row]= {
                    "group_id" : grupo,
                    "role_id" :  rol
                }
            }
        });         
        
        var data = table.filter(Boolean);
        var dataRole = JSON.stringify(data);
        var roles = JSON.parse(dataRole);
        
        var dataBpm = tableBpm.filter(Boolean);
        var dataRoleBpm = JSON.stringify(dataBpm);
        var rolesBpm = JSON.parse(dataRoleBpm);
        /*
        console.log(rolesBpm);
        console.log(roles);
        */
        
        $.ajax({
			type: "POST",
			url:'<?php echo base_url() ?>/main/changeLevelRolUserObject',
			data: {
				dataRole: roles,
				dataRoleBpm: rolesBpm,
				email: email,
				level: level
			},
			success: function(rsp) {
				alert("Guardado correctamente.");
			},
			error: function() {
				alert("Se produjo un error al guardar rol/nivel del usuario.");
			},
			complete: function() {

			}
		});

    }

</script>