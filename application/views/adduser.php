<div class="col-lg-4 col-lg-offset-4">
    <h2>Hola <?php echo $first_name; ?>,</h2>
    <h5>Por favor ingrese la informacion requerida a continuacion.</h5>     
    <?php 
        $fattr = array('class' => 'form-signin');
        echo form_open('/main/adduser', $fattr);
    ?>
    <div class="form-group">
      <?php echo form_input(array('name'=>'firstname', 'id'=> 'firstname', 'placeholder'=>'Nombre', 'class'=>'form-control', 'value' => set_value('firstname'))); ?>
      <?php echo form_error('firstname');?>
    </div>
    <div class="form-group">
      <?php echo form_input(array('name'=>'lastname', 'id'=> 'lastname', 'placeholder'=>'Apellido', 'class'=>'form-control', 'value'=> set_value('lastname'))); ?>
      <?php echo form_error('lastname');?>
    </div>
    <div class="form-group">
      <?php echo form_input(array('name'=>'email', 'id'=> 'email', 'placeholder'=>'Email', 'class'=>'form-control', 'value'=> set_value('email'))); ?>
      <?php echo form_error('email');?>
    </div>
     <div class="form-group">
      <?php echo form_input(array('name'=>'usernick', 'id'=> 'usernick', 'placeholder'=>'Usernick', 'class'=>'form-control', 'value'=> set_value('usernick'))); ?>
      <?php echo form_error('usernick');?>
    </div>

    <!-- FLEIVA -->
     <div class="form-group">
      <?php echo form_input(array('name'=>'telefono', 'id'=> 'telefono', 'placeholder'=>'Teléfono', 'class'=>'form-control', 'value'=> set_value('telefono'))); ?>
      <?php echo form_error('telefono');?>
    </div>
       <div class="form-group">
      <?php echo form_input(array('name'=>'dni', 'id'=> 'dni', 'placeholder'=>'D.N.I', 'class'=>'form-control', 'value'=> set_value('dni'))); ?>
      <?php echo form_error('dni');?>
    </div>
    <!-- FLEIVA -->

    <div class="form-group">
    <?php
    // rol de Dnato
        $dd_list = array(
                  '1'   => 'Admin',
                  '2'   => 'Author',
                  '3'   => 'Editor',
                  '4'   => 'Subscriber',
                );
        $dd_name = "role";
        echo form_dropdown($dd_name, $dd_list, set_value($dd_name),'class = "form-control" id="role"');
    ?>
    </div>    
    <div class="form-group">
      <?php echo form_password(array('name'=>'password', 'id'=> 'password', 'placeholder'=>'Password', 'class'=>'form-control', 'value' => set_value('password'))); ?>
      <?php echo form_error('password') ?>
    </div>
    <div class="form-group">
      <?php echo form_password(array('name'=>'passconf', 'id'=> 'passconf', 'placeholder'=>'Confirme Password', 'class'=>'form-control', 'value'=> set_value('passconf'))); ?>
      <?php echo form_error('passconf') ?>
    </div>
    <?php echo form_submit(array('value'=>'Guardar', 'class'=>'btn btn-lg btn-primary btn-block')); ?>
    <?php echo form_close(); ?>
</div>