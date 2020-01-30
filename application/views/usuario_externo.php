<div class="col-lg-4 col-lg-offset-4">
    <h2>Hello <?php echo $first_name; ?>,</h2>
    <h5>Please enter the required information below.</h5>     
    <?php 
        $fattr = array('class' => 'form-signin');
        echo form_open('/main/adduserexterno', $fattr);
    ?>
    <div class="form-group">
      <?php echo form_input(array('name'=>'nombre_razon', 'id'=> 'nombre_razon', 'placeholder'=>'Nombre / Razón Social', 'class'=>'form-control', 'value' => set_value('nombre_razon'))); ?>
      <?php echo form_error('nombre_razon');?>
    </div>
    <div class="form-group">
      <?php echo form_input(array('name'=>'email', 'id'=> 'email', 'placeholder'=>'Email', 'class'=>'form-control', 'value'=> set_value('email'))); ?>
      <?php echo form_error('email');?>
    </div>
    <!-- FLEIVA -->    
     <div class="form-group">
      <?php echo form_input(array('name'=>'telefono', 'id'=> 'telefono', 'placeholder'=>'Teléfono', 'class'=>'form-control', 'value'=> set_value('telefono'))); ?>
      <?php echo form_error('telefono');?>
    </div>
     <div class="form-group">
      <?php echo form_input(array('name'=>'cuit_empresa', 'id'=> 'cuit_empresa', 'placeholder'=>'CUIT / Empresa', 'class'=>'form-control', 'value'=> set_value('cuit_empresa'))); ?>
      <?php echo form_error('cuit_empresa');?>
    </div>
    <div class="form-group">
      <?php echo form_input(array('name'=>'usernick', 'id'=> 'usernick', 'placeholder'=>'Usernick', 'class'=>'form-control', 'value'=> set_value('usernick'))); ?>
      <?php echo form_error('usernick');?>
    </div>
    <!-- FLEIVA -->

    <div class="form-group">
      <?php echo form_password(array('name'=>'password', 'id'=> 'password', 'placeholder'=>'Password', 'class'=>'form-control', 'value' => set_value('password'))); ?>
      <?php echo form_error('password') ?>
    </div>
    <div class="form-group">
      <?php echo form_password(array('name'=>'passconf', 'id'=> 'passconf', 'placeholder'=>'Confirm Password', 'class'=>'form-control', 'value'=> set_value('passconf'))); ?>
      <?php echo form_error('passconf') ?>
    </div>
    <?php echo form_submit(array('value'=>'Add', 'class'=>'btn btn-lg btn-primary btn-block')); ?>
    <?php echo form_close(); ?>
</div>