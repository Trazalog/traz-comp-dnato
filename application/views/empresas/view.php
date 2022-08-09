<style>

  
</style>


<div class="col-lg-4 col-lg-offset-4">
    <h2>Nueva Empresa </h2>
    <h5>Por favor ingrese la información requerida a continuación.</h5>     
    <?php 
        $fattr = array('class' => 'form-signin', 'enctype'=>'multipart/form-data'  );
        echo form_open('/empresa/agregarEmpresa', $fattr);
    ?>
    <div class="form-group">
      <?php echo form_input(array('name'=>'nombre', 'id'=> 'nombre', 'placeholder'=>'Nombre', 'class'=>'form-control', 'value' => set_value('nombre'))); ?>
      <?php echo form_error('nombre');?>
    </div>
    <div class="form-group">
      <?php echo form_input(array('name'=>'cuit', 'id'=> 'cuit', 'placeholder'=>'Cuit', 'class'=>'form-control', 'value'=> set_value('cuit'))); ?>
      <?php echo form_error('cuit');?>
    </div>
    <div class="form-group">
      <?php echo form_input(array('name'=>'descripcion', 'id'=> 'descripcion', 'placeholder'=>'Descripción', 'class'=>'form-control', 'value'=> set_value('descripciondescripcion'))); ?>
      <?php echo form_error('descripcion');?>
    </div>
    <div class="form-group">
      <?php echo form_input(array('name'=>'telefono', 'id'=> 'telefono', 'placeholder'=>'Teléfono', 'class'=>'form-control', 'value'=> set_value('telefono'))); ?>
      <?php echo form_error('telefono');?>
    </div>
    <div class="form-group">
      <?php echo form_input(array('name'=>'email', 'id'=> 'email', 'placeholder'=>'Email', 'class'=>'form-control', 'value'=> set_value('email'))); ?>
      <?php echo form_error('email');?>
    </div>

    <div class="form-group">
        <select onchange="seleccionPais()" class="form-control select select-hidden-accesible" name="pais_id" id="pais_id" style='width: 100%;'>
            <option value="" disabled selected>-Seleccione opción-</option>	
            <?php
                foreach ($listarPaises as $pais) {
                echo '<option  value="'.$pais->tabl_id.'">'.$pais->valor.'</option>';
                }
            ?>
        </select>
    </div>
    <div class="form-group">
        <select onchange="seleccionEstado()" class="form-control select select-hidden-accesible habilitar" name="estado" id="estado" style='width: 100%;'>
            <option value="" disabled selected>-Seleccione opción-</option>	
            <?php
                foreach ($tipos_clientes as $tipos) {
                echo '<option  value="'.$tipos->tabl_id.'">'.$tipos->valor.'</option>';
                }
            ?>
        </select>
    </div>
    <div class="form-group">
        <select class="form-control select select-hidden-accesible habilitar" name="localidad" id="localidad" style='width: 100%;'>
            <option value="" disabled selected>-Seleccione opción-</option>	
            <?php
                foreach ($tipos_clientes as $tipos) {
                echo '<option  value="'.$tipos->tabl_id.'">'.$tipos->valor.'</option>';
                }
            ?>
        </select>
    </div>
    <!-- <h5><strong>Logo de la empresa</strong></h5>
    <div class="form-group">
    </div> -->
    
    <?php echo form_submit(array('value'=>'Guardar', 'class'=>'btn btn-lg btn-primary btn-block')); ?>
    <?php echo form_close(); ?>
</div>
<script>
    /* carga Estados dependiendo del pais seleccionado*/
    function seleccionPais() {
        var id_pais = $("#pais_id option:selected").text();
        /* wo(); */
        $.ajax({
            type: 'GET',
            dataType: "json",
            data: {id_pais: id_pais},
            url:'<?php echo base_url() ?>Empresa/getEstados',
            success: function(rsp) {
                $('#estado').empty();
                $('#localidad').empty();
                if (rsp != null) {
                    /* habilitarEdicion(); */
                    var datos = "<option value='' disabled selected>-Seleccione opción-</option>";
                    $('#localidad').html(datos);
                    for (let i = 0; i < rsp.length; i++) {
                        var datito = encodeURIComponent(rsp[i].tabl_id);
                        datos += "<option value=" + datito + ">" + rsp[i].valor + "</option>";
                    }
                    $('#estado').html(datos);
                } else {
                    var datos = "<option value='' disabled selected>-Seleccione opción-</option>";
                    $('#estado').html(datos);
                    $('#localidad').html(datos);                   
                    alertify.error("El País no contiene estados");
                }    
                /* wc(); */
            },
            error: function(data) {
                alert('Error');
            }
        });
    }

    /* carga Localidades dependiendo del estado seleccionado*/
    function seleccionEstado() {
        var id_pais = $("#pais option:selected").text();
        var id_estado = $("#estado option:selected").text();
        wo();
        $.ajax({
            type: 'GET',
            dataType: "json",
            data: {id_pais, id_estado},
            url:'<?php echo base_url() ?>Empresa/getLocalidades',
            success: function(rsp) {
                $('#localidad').empty();
                if (rsp != null) {
                    habilitarEdicion();
                    var datos = "<option value='' disabled selected>-Seleccione opción-</option>";
                    for (let i = 0; i < rsp.length; i++) {
                        var valor = encodeURIComponent(rsp[i].tabl_id);
                        /* datos += "<option value='"+ rsp[i].tabl_id + "'>"+ rsp[i].valor + "</option>";*/
                        datos += "<option value=" + valor + ">" + rsp[i].valor + "</option>";
                    }
                    $('#localidad').html(datos);
                } else {
                    var datos = "<option value='' disabled selected>-Seleccione opción-</option>";
                    $('#localidad').html(datos); 
                    alertify.error("El Estado no contiene localidades");
                }
                wc();
            },
            error: function(data) {
                alert('Error');
            }
        });
    }
</script>