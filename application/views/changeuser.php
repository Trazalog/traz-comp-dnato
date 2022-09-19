<div class="container"> 
  <div class="col-md-12">  
    <div class="col-md-4">      
        <br>
        <div class="portlet light profile-sidebar-portlet bordered">
            <div class="profile-userpic" style="width: 50%;  display: block;  margin-left: auto;  margin-right: auto; ">
            <?php

use function PHPSTORM_META\type;

              foreach($usersList as $user){
                if(($email == $user->email) && ($usernick == $user->usernick)){                             
                  echo '<img src="'.imageAdmin($user->image, $user->image_name).'" class="img-responsive" style="border: 1px solid #000;" alt="User Image"/>';
                  break;
                }
              }
            ?>                                    
            </div>
        </div>
    </div>
    
    <div class="col-md-8"> 
        <div class="portlet light bordered">
            <div class="portlet-title tabbable-line">
                <div class="caption caption-md">
                  <h2>Editar perfil</h2>
                  <h5>Hola <span><?php echo $$groups->first_name; ?></span>.</h5>
                </div>
            </div>
            <div class="portlet-body">              
                <div>                
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs" role="tablist">
                        <li role="presentation" class="active"><a href="#home" aria-controls="home" role="tab" data-toggle="tab">Info General</a></li>
                        <li role="presentation"><a href="#profile" aria-controls="profile" role="tab" data-toggle="tab">Contraseña</a></li>                        
                    </ul>
                
                    <!-- Tab panes -->
                    <div class="tab-content">
                        <div role="tabpanel" class="tab-pane active" id="home">
                          <br>
                          <?php 
                              $fattr = array('class' => 'form-signin-user', 'enctype'=>'multipart/form-data');
                              /*$fattr = array('class' => 'form-signin', 'enctype'=>'multipart/form-data'  );*/
                              echo form_open(site_url().'main/updateuser/', $fattr); 
                          ?>
                          <div class="form-group">
                            <?php echo form_input(array('name'=>'image', 'accept' => 'image/*', 'id'=> 'image', 'type' => 'file', 'placeholder'=>'Foto Perfil', 'class'=>'form-control', 'value'=> set_value('image'))); ?>
                            <?php echo form_error('image');?>
                          </div>
                          
                          <div class="form-group">
                            <?php echo form_input(array('name'=>'firstnameuser', 'id'=> 'firstnameuser', 'placeholder'=>'Nombre', 'class'=>'form-control', 'value' => set_value('firstnameuser', $groups->first_name))); ?>
                            <?php echo form_error('firstnameuser');?>
                          </div>
                          <div class="form-group">
                            <?php echo form_input(array('name'=>'lastnameuser', 'id'=> 'lastnameuser', 'placeholder'=>'Apellido', 'class'=>'form-control', 'value'=> set_value('lastnameuser', $groups->last_name))); ?>
                            <?php echo form_error('lastnameuser');?>
                          </div>
                          <div class="form-group">
                            <?php echo form_input(array('name'=>'emailuser', 'id'=> 'emailuser', 'placeholder'=>'Email', 'readOnly' => 'true', 'class'=>'form-control', 'value'=> set_value('emailuser', $groups->email))); ?>
                          </div>
                          <?php echo form_submit(array('value'=>'Actualizar', 'class'=>'btn btn-lg btn-primary btn-block')); ?>
                          <?php echo form_close(); ?>
                        </div>
                        <div role="tabpanel" class="tab-pane" id="profile">
                          <br>
                          <?php 
                              $fattr = array('class' => 'form-signin', 'enctype'=>'multipart/form-data');
                              /*$fattr = array('class' => 'form-signin', 'enctype'=>'multipart/form-data'  );*/
                              echo form_open(site_url().'main/changeuser/', $fattr); 
                          ?>
                          <div class="form-group">
                            <?php echo form_input(array('name'=>'firstname', 'type' => 'hidden', 'id'=> 'firstname', 'placeholder'=>'Nombre', 'class'=>'form-control', 'value' => set_value('firstname', $groups->first_name))); ?>
                            <?php echo form_error('firstname');?>
                          </div>
                          <div class="form-group">
                            <?php echo form_input(array('name'=>'lastname', 'type' =>'hidden', 'id'=> 'lastname', 'placeholder'=>'Apellido', 'class'=>'form-control', 'value'=> set_value('lastname', $groups->last_name))); ?>
                            <?php echo form_error('lastname');?>
                          </div>
                          <div class="form-group">
                            <?php echo form_input(array('name'=>'email', 'type' =>'hidden', 'id'=> 'email', 'readOnly' => 'true', 'placeholder'=>'Email', 'class'=>'form-control', 'value'=> set_value('email', $groups->email))); ?>
                          </div>
                          <div class="form-group">
                            <?php echo form_password(array('name'=>'password', 'id'=> 'password', 'placeholder'=>'Contraseña', 'class'=>'form-control', 'value' => set_value('password'))); ?>
                            <?php echo form_error('password') ?>
                          </div>
                          <div class="form-group">
                            <?php echo form_password(array('name'=>'passconf', 'id'=> 'passconf', 'placeholder'=>'Confirmar Contraseña', 'class'=>'form-control', 'value'=> set_value('passconf'))); ?>
                            <?php echo form_error('passconf') ?>
                            <?php echo form_submit(array('value'=>'Cambiar', 'id'=> 'btnGuardar', 'class'=>'btn btn-lg btn-primary btn-block')); ?>
                            <?php echo form_close(); ?>
                          </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4 col-lg-offset-4">
      <?php // echo form_submit(array('value'=>'Cambiar', 'id'=> 'btnGuardar', 'class'=>'btn btn-lg btn-primary btn-block')); ?>
      <?php //echo form_close(); ?>
    </div>
</div>
</div>