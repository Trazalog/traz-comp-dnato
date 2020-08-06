<div class="col-lg-4 col-lg-offset-4">
    <h2>Editar perfil</h2>
    <h5>Hola <span><?php echo $first_name; ?></span>.</h5>     
<?php 
    $fattr = array('class' => 'form-signin');
    echo form_open(site_url().'main/changeuser/', $fattr); ?>
    
    <div class="form-group">
      <?php echo form_input(array('name'=>'firstname', 'id'=> 'firstname', 'placeholder'=>'Nombre', 'class'=>'form-control', 'value' => set_value('firstname', $groups->first_name))); ?>
      <?php echo form_error('firstname');?>
    </div>
    <div class="form-group">
      <?php echo form_input(array('name'=>'lastname', 'id'=> 'lastname', 'placeholder'=>'Apellido', 'class'=>'form-control', 'value'=> set_value('lastname', $groups->last_name))); ?>
      <?php echo form_error('lastname');?>
    </div>
    <div class="form-group">
      <?php echo form_input(array('name'=>'email', 'id'=> 'email', 'placeholder'=>'Email', 'class'=>'form-control', 'value'=> set_value('email', $groups->email))); ?>
    </div>
    <div class="form-group">
      <?php echo form_password(array('name'=>'password', 'id'=> 'password', 'placeholder'=>'Contraseña', 'class'=>'form-control', 'value' => set_value('password'))); ?>
      <?php echo form_error('password') ?>
    </div>
    <div class="form-group">
      <?php echo form_password(array('name'=>'passconf', 'id'=> 'passconf', 'placeholder'=>'Confirmar Contraseña', 'class'=>'form-control', 'value'=> set_value('passconf'))); ?>
      <?php echo form_error('passconf') ?>
    </div>
    <?php echo form_submit(array('value'=>'Cambiar', 'class'=>'btn btn-lg btn-primary btn-block')); ?>
    <?php echo form_close(); ?>
</div>