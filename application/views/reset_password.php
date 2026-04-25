<div class="col-lg-4 col-lg-offset-4">
    <h2>Restablecer tu contraseña</h2>
    <h5>Hola <span><?php echo $firstName; ?></span>,<br>Ingresá tu nueva contraseña dos veces para restablecerla.</h5>
<?php 
    $fattr = array('class' => 'form-signin');
    echo form_open(site_url().'main/reset_password/token/'.$token, $fattr); ?>
    <div class="form-group">
      <?php echo form_password(array('name'=>'password', 'id'=> 'password', 'placeholder'=>'Contraseña', 'class'=>'form-control', 'value' => set_value('password'))); ?>
      <?php echo form_error('password') ?>
    </div>
    <div class="form-group">
      <?php echo form_password(array('name'=>'passconf', 'id'=> 'passconf', 'placeholder'=>'Confirmar contraseña', 'class'=>'form-control', 'value'=> set_value('passconf'))); ?>
      <?php echo form_error('passconf') ?>
    </div>
    <?php echo form_submit(array('value'=>'Restablecer contraseña', 'class'=>'btn btn-lg btn-primary btn-block')); ?>
    <?php echo form_close(); ?>
</div>
