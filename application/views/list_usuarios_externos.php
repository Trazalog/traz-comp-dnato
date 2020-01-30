

        <div class="container">
        <h2>Usuarios Externos</h2>
        <table class="table table-hover table-bordered table-striped">
          <tr>
              <th>
                  Nombre / Razon S.
              </th>
              <th>
                  CUIT
              </th>
              <th>
                  Usernick
              </th>
                <th>
                  Email
              </th>
              <th>
                  Teléfono
              </th>
              <th>
                  Status
              </th>
              <th colspan="2">
                  Edit
              </th>
          </tr>
                <?php
                    foreach($groups as $row)
                    { 
                    
                    if($row->role != USUARIO_EXTERNO) continue;

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
                    echo '<td>'.$row->nombre_razon.'</td>';
                    echo '<td>'.$row->cuit_empresa.'</td>';
                    echo '<td>'.$row->usernick.'</td>';
                    echo '<td>'.$row->email.'</td>';
                    echo '<td>'.$row->telefono.'</td>';
                   
                    echo '<td>'.$row->status.'</td>';
                    echo '<td><a href="'.site_url().'main/changelevel"><button type="button" class="btn btn-primary">Role</button></a></td>';
                    echo '<td><a href="'.site_url().'main/deleteuser/'.$row->id.'"><button type="button" class="btn btn-danger">Delete</button></a></td>';
                    echo '</tr>';
                    }
                ?>
        </table>
    </div>