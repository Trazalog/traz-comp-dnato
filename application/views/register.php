<div class="col-lg-4 col-lg-offset-4">
    <h2>Hola!</h2>
    <h5>Por favor ingrese la informacion requerida abajo.</h5>
    <?php 
        $fattr = array('class' => 'form-signin');
        echo form_open('/main/register', $fattr);
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
    <?php if($recaptcha == 'yes'){ ?>
    <div style="text-align:center;" class="form-group">
        <div style="display: inline-block;"><?php echo $this->recaptcha->render(); ?></div>
    </div>
    <?php
    }
    echo form_submit(array('value'=>'Sign up', 'class'=>'btn btn-lg btn-primary btn-block')); ?>
    <?php echo form_close(); ?>
    <br>
    <p>Registrado? <a href="<?php echo site_url();?>main/login">Ingrese...</a></p>
</div>