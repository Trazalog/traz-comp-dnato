<div class="col-lg-4 col-lg-offset-4">
    <h2>Habilitar/Deshabilitar Usuarios</h2>
    <h5>Hola <span><?php echo $first_name; ?></span>, <br>Por favor elija el usuario.</h5>     
    <?php $fattr = array('class' => 'form-signin');
         echo form_open(site_url().'main/banuser/', $fattr); ?>
    
    <div class="form-group">
        <select class="form-control" name="email" id="email">
            <?php
            foreach($usersList as $row){

              foreach($emp_connect as $emp){
                
                if($emp->group === $row->busines)
                  echo '<option value="'.$row->email.'">'.$row->email.'</option>';
              }
            }
            ?>
            </select>
    </div>

    <div class="form-group">
    <?php
        $dd_list = array(
                  'unban'   => 'Unban',
                  'ban'   => 'Ban',
                );
        $dd_name = "banuser";
        echo form_dropdown($dd_name, $dd_list, set_value($dd_name),'class = "form-control" id="banuser"');
    ?>
    </div>
    <?php echo form_submit(array('value'=>'Enviar', 'class'=>'btn btn-lg btn-primary btn-block')); ?>
    <a href="<?php echo site_url().'main/banuser/';?>"><button type="button" class="btn btn-default btn-lg btn-block">Cancelar</button></a>
    <?php echo form_close(); ?>
</div>