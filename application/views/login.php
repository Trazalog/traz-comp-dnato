<div class="col-lg-4 col-lg-offset-4">
    <h2>Bienvenido!</h2>
    <h5>Ingrese por favor.</h5>
    <?php $fattr = array('class' => 'form-signin');
         echo form_open(base_url().'main/login/', $fattr); ?>


    <div class="form-group">
      <?php
          // groups de BPM
          $opciones= array('' => 'Seleccione Empresa...');
          foreach ($empresas as $value) {

            // $nom = explode("-", $value->name);
            // $empr_id = $nom[0];
            // $key = $empr_id;
            $key = $value->name;
            $opciones[$key] = $value->displayName;
          }
          $empr_id = 'empr_id';
          echo form_dropdown($empr_id, $opciones, set_value($empr_id),'class = "form-control" id="empr_id"');
          //echo form_dropdown('name', 'opciones', 'opcion seleccionada', 'atributos del select(id,onChange, etc')
      ?>
    </div>

    <div class="form-group">
      <?php echo form_input(array(
          'name'=>'email', 
          'id'=> 'email', 
          'placeholder'=>'Email', 
          'class'=>'form-control', 
          'value'=> set_value('email'))); ?>
      <?php echo form_error('email') ?>
    </div>
    <div class="form-group">
      <?php echo form_password(array(
          'name'=>'password',
          'id'=> 'password',
          'placeholder'=>'Password',
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
    <!-- <p>No esta registrado? <a href="<?php //echo base_url();?>main/register">Registrese por favor</a></p>
    <p>Olvido su contraseña? <a href="<?php //echo base_url();?>main/forgot">Recupere contraseña</a></p> -->
</div>