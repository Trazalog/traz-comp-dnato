    <div class="container">
        <h2>Usuarios</h2>
        <table class="table table-hover table-bordered table-striped">
          <tr>
              <th>Nombre</th>
              <th>Usuario</th>
              <th>Ultimo login</th>
              <th>Nivel de Usuario</th>
              <th>Estado</th>
              <th colspan="2">Editar</th>
          </tr>
                <?php
                    foreach($groups as $row)
                    { 

                    //if($row->role == USUARIO_EXTERNO) continue;
                    if($row->role == 1){
                        $rolename = "Admin";
                    }elseif($row->role == 2){
                        $rolename = "Author";
                    }elseif($row->role == 3){
                        $rolename = "Editor";
                    }elseif($row->role == 4){
                        $rolename = "Subscriber";
                    }
                    
                    echo '<tr>';
                    echo '<td>'.$row->first_name.' '.$row->last_name.'</td>';
                    echo '<td>'.$row->email.'</td>';
                    echo '<td>'.$row->last_login.'</td>';
                    echo '<td>'.$rolename.'</td>';
                    echo '<td>'.$row->status.'</td>';
                    //echo '<td><a href="'.site_url().'main/changelevel"><button type="button" class="btn btn-primary">Rol</button></a></td>';
                    echo '<td><a href="'.site_url().'main/changeleveluser/'.$row->id.'"><button type="button" class="btn btn-primary">Rol</button></a></td>';
                    echo '<td><a href="'.site_url().'main/deleteuser/'.$row->id.'"><button type="button" class="btn btn-danger">Borrar</button></a></td>';
                    echo '</tr>';
                    }
                ?>
        </table>
    </div>