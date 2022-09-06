<div class="container">
        <h2><?php echo $title; ?></h2>
        <table id="usersList" class="table table-bordered table-hover">
            <thead>  
                <tr>
                    <th>id</th>
                    <th class="hidden">Empresa</th>
                    <th>Nombre</th>
                    <th>Usuario</th>
                    <th>Ultimo login</th>
                    <th>Nivel de Usuario</th>
                    <th>Estado</th>
                    <th >Acciones</th>
                </tr>
            </thead>  
            <tbody>
            <?php                    
                    foreach($emp_connect as $emp_con){
                        foreach($usersList as $row){
                            
                            if($row->busines == $emp_con->group){  //Filtra Empresa del conectado
                                if(($email != $row->email) && ($usernick != $row->usernick)){   // No                            
                                                                
                                    echo "<tr id='".$row->id."|".$row->busines."|".$row->first_name."|".$row->last_name."|".$row->email."|".$row->last_login."|".$row->nombre."|".$row->status."'>";
                                    echo '<td>'.$row->id.'</td>';
                                    echo '<td class="hidden">'.$row->busines.'</td>';
                                    echo '<td>'.$row->first_name.' '.$row->last_name.'</td>';
                                    echo '<td>'.$row->email.'</td>';
                                    echo '<td>'.$row->last_login.'</td>';
                                    echo '<td>'.$row->nombre.'</td>';
                                    echo '<td>'.$row->status.'</td>';
                                    //echo '<td><a href="'.site_url().'main/changelevel"><button type="button" class="btn btn-primary">Rol</button></a></td>';
                                    echo '<td>';
                                    /*echo '<a href="'.site_url().'main/changeleveluser/'.$row->id.'"><button type="button" class="btn btn-primary">Rol</button></a>';
                                    echo str_repeat ("&nbsp;",1); 
                                    echo '<a href="'.site_url().'main/deleteuser/'.$row->id.'/'.$row->busines.'"><button type="button" class="btn btn-danger">Borrar</button></a>';*/
                                    echo '<a href="'.site_url().'main/changeleveluser/'.$row->id.'"><i class="fa fa-fw fa-address-card-o text-light-blue" style="cursor: pointer; margin-left: 4px;" title="Asignar Rol"  id="btnEditUser"></i></a>';
                                    echo '<a href="'.site_url().'main/deleteuser/'.$row->id.'/'.$row->busines.'"><i class="fa fa-fw fa-trash-o text-light-blue" style="cursor: pointer; margin-left: 4px;" title="Eliminar Usuario"  id="btnDeleteUser"></i></a>';                                    
                                    echo '</td></tr>';
                                }
                            }
                        }
                    }
    
                ?>
            </tbody>
        </table>
    </div>

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
   
    $('#usersList').DataTable();
});

function Edituser(eval){

    console.log("edit");
    var data= $(eval).closest('tr').attr('id');
    console.log("Data"+data);
    var dataMenu = data.split("|");
    console.log("Id: "+dataMenu[0]+" busines: "+dataMenu[1]);
}

function deleteUser(eval){

    console.log("Delete");
    var data= $(eval).closest('tr').attr('id');
    console.log("Data"+data);
    var dataMenu = data.split("|");
    console.log("Id: "+dataMenu[0]+" busines: "+dataMenu[1]);
    $('#modalConfirm').modal('show');
    $('h4.modal-title').text('Deseas eliminar este usuario?');    

    $("#modal-btn-si").on("click", function(){
        
        console.log("Confirm");
        $.ajax({
            type: "POST",
            url:'<?php echo base_url() ?>main/deleteuser',
            data: {
                id: dataMenu[0],
                busines: dataMenu[1]
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

</script>