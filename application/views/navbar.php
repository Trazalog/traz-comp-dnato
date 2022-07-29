<link href='<?php  echo base_url();?>assets/fullcalendar/lib/main.min.css' rel='stylesheet' />

        
        <style>
          .navbar-nav>.user-menu .user-image {
            float: left;
            width: 25px;
            height: 25px;
            border-radius: 50%;
            margin-right: 10px;
            margin-top: -2px;
          }

        </style>
        
        <?php
        //check user level
	    $dataLevel = $this->userlevel->checkLevel($role);
      $result = $this->user_model->getAllSettings();
	    $site_title = $result->site_title;
	    //check user level
        ?>
        <nav class="navbar navbar-inverse">
            <div class="container">
              <div class="container-fluid">
                <!-- Brand and toggle get grouped for better mobile display -->
                <div class="navbar-header">
                  <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                  </button>
                  <!-- <a class="navbar-brand" href="<?php //echo site_url();?>main/users"><?php //echo $site_title; ?></a>  -->
                </div>
            
                <!-- Collect the nav links, forms, and other content for toggling -->
                <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                  <ul class="nav navbar-nav">
                    <!-- <li><a href="<?php //echo site_url();?>main/"><i class="fa fa-tachometer" aria-hidden="true"></i> Dashboard</a></li> -->
                    <li><a href="<?php echo DE;?>"><i class="fa fa-tachometer" aria-hidden="true"></i> <?php echo SIS_NAME; ?></a></li>

                    <?php
                        if($dataLevel == 'is_admin'){ //Check user level if is Admin
                            echo'
                            <li class="dropdown">
                              <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><i class="fa fa-users" aria-hidden="true"></i> Gestion de Usuarios <span class="caret"></span></a>
                              <ul class="dropdown-menu">
                                <li><a href="'.site_url().'main/users">Lista de Usuarios</a></li>

                                <li><a href="'.site_url().'main/adduser">Agregar Usuario</a></li>
                                
                                <li><a href="'.site_url().'main/banuser">Habilitar/Deshabilitar Usuario</a></li>
                              
                                </ul>
                            </li>
                            <li class="dropdown">
                              <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><i class="fa fa-users" aria-hidden="true"></i> Gestion de Menues <span class="caret"></span></a>
                              <ul class="dropdown-menu">
                                <li><a href="'.site_url().'menu/menuesList">Alta de Menues</a></li>
                              </ul>
                            </li>
                            <li><a href="'.site_url().'main/settings"><i class="glyphicon glyphicon-cog" aria-hidden="true"></i> Configuracion</a></li>';
                        }
                    ?>
                  </ul>
                  <ul class="nav navbar-nav navbar-right">
                    <li class="dropdown user user-menu">
                      
                      <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                        <?php
                          foreach($usersList as $user){
                            if(($email == $user->email) && ($usernick == $user->usernick)){                             
                              echo '<img src="'.imageAdmin($user->image, $user->image_name).'" class="user-image" alt="User Image"/>';
                              break;
                            }
                          }
                        ?>
                        
                        <!--<img src="<?php /*echo site_url()*/?>/public/img/user2-160x160.jpg" class="user-image" alt="User Image">-->
                        <span class="hidden-xs"><?php echo $first_name. ' '.$last_name; ?></span>
                      </a>
                        

                      <ul class="dropdown-menu">
                        <li><a href="<?php echo site_url();?>main/profile"><?php echo $email; ?></a></li>
                        <li><a href="<?php echo site_url();?>main/changeuser">Editar Perfil</a></li>
                        <li role="separator" class="divider"></li>

                        <?php
                          if(isset($groupsBpm)){
                            foreach($groupsBpm as $group){
                              list($id_group, $group_name) = explode ("-",$group->name);
                              if($groupBpm == $group_name){
                                echo "  "."<li><a><i class='fa fa-check'></i>  ".$group->displayName."</a></li>";
                                echo "<li role='separator' class='divider'></li>";
                              }
                              
                            }
                          }else{
                            foreach($groups as $group){
                              list($id_group, $group_name) = explode ("-",$group->name);
                              if($groupBpm == $group_name){
                                echo "  "."<li><a><i class='fa fa-check'></i>  ".$group->displayName."</a></li>";
                                echo "<li role='separator' class='divider'></li>";
                              }
                              
                            }
                          }
                            
                        ?>
                                                
                        <li><a href="<?php echo base_url().'main/logout' ?>">Salir</a></li>
                      </ul>
                    </li>
                  </ul>
                </div><!-- /.navbar-collapse -->
              </div><!-- /.container-fluid -->
            </div>
        </nav>
