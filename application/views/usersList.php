<div class="container">
        <h2><?php echo $title; ?></h2>
        <table class="table table-hover table-bordered table-striped">
          <tr>
              <th>id</th>
              <th>Empresa</th>
              <th>Nombre</th>
              <th>Usuario</th>
              <th>Ultimo login</th>
              <th>Nivel de Usuario</th>
              <th>Estado</th>
              <th colspan="2">Editar</th>
          </tr>
                <?php
                    
                    foreach($emp_connect as $emp_con){
                        foreach($usersList as $row){
                            
                            if($row->busines == $emp_con->group){  //Filtra Empresa del conectado
                                if(($email != $row->email) && ($usernick != $row->usernick)){   // No                            
                                                                
                                    echo '<tr>';
                                    echo '<td>'.$row->id.'</td>';
                                    echo '<td>'.$row->busines.'</td>';
                                    echo '<td>'.$row->first_name.' '.$row->last_name.'</td>';
                                    echo '<td>'.$row->email.'</td>';
                                    echo '<td>'.$row->last_login.'</td>';
                                    echo '<td>'.$row->nombre.'</td>';
                                    echo '<td>'.$row->status.'</td>';
                                    //echo '<td><a href="'.site_url().'main/changelevel"><button type="button" class="btn btn-primary">Rol</button></a></td>';
                                    echo '<td><a href="'.site_url().'main/changeleveluser/'.$row->id.'"><button type="button" class="btn btn-primary">Rol</button></a></td>';
                                    echo '<td><a href="'.site_url().'main/deleteuser/'.$row->id.'"><button type="button" class="btn btn-danger">Borrar</button></a></td>';
                                    echo '</td>';
                                }
                            }
                        }
                    }
    
                ?>
        </table>
    </div>