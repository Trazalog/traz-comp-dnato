<div class="col-lg-4 col-lg-offset-4">
    <style>
    body {
      background-repeat: no-repeat;
      background-attachment: fixed;
      background-size: cover;
    </style>
    <br>
    <br>
    <img 
    src="<?= base_url($logoEmpresa); ?>" 
    alt="Trazalog Tools"
    class="brand-image"
    style="width: 360px; height: auto !important;"
>
    <br>

    <h2>Bienvenido!</h2>
    <h5>Ingrese por favor.</h5>
    <?php $fattr = array('class' => 'form-signin');
         echo form_open(base_url().'main/login/', $fattr); ?>


    <?php
      /* El selector de empresa se eliminó de esta pantalla.
         Antes se llenaba con Roles::getBpmGroups(), es decir listaba todas las
         empresas del sistema a cualquiera que abriera el login sin sesión.
         Ahora la empresa se resuelve del lado del servidor a partir de las
         membresías del usuario ya autenticado; si tiene más de una, el login
         continúa en main/seleccionar_empresa. */
    ?>

    <div class="form-group">
      <?php echo form_input(array(
          'name'=>'email', 
          'id'=> 'email', 
          'placeholder'=>'Correo electrónico', 
          'class'=>'form-control', 
          'value'=> set_value('email'))); ?>
      <?php echo form_error('email') ?>
    </div>
    <div class="form-group">
      <?php echo form_password(array(
          'name'=>'password',
          'id'=> 'password',
          'placeholder'=>'Contraseña',
          'class'=>'form-control',
          'value'=> set_value('password'))); ?>
      <?php echo form_error('password') ?>
    </div>
    <?php if($recaptcha == 'yes'){ ?>
    <div style="text-align:center;" class="form-group">
        <div style="display: inline-block;"><?php echo $this->recaptcha->render(); ?></div>
    </div>
    <?php
    }
    echo form_submit(array('value'=>'Ingresar', 'class'=>'btn btn-lg btn-primary btn-block')); ?>
    <?php echo form_close(); ?>
    <br>
    <p>No esta registrado? <a href="<?php echo base_url();?>main/register">Registrese por favor</a></p>
    <p>Olvido su contraseña? <a href="<?php echo base_url();?>main/forgot">Recupere contraseña</a></p>
</div>