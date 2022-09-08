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
      <?php echo form_input(array('name'=>'nombre', 'id'=> 'nombre', 'placeholder'=>'Nombre (*)', 'class'=>'form-control', 'value' => set_value('nombre'))); ?>
      <?php echo form_error('nombre');?>
    </div>
    <div class="form-group">
      <?php echo form_input(array('name'=>'cuit', 'id'=> 'cuit', 'placeholder'=>'CUIT (*)', 'class'=>'form-control', 'value'=> set_value('cuit'))); ?>
      <?php echo form_error('cuit');?>
    </div>
    <div class="form-group">
      <?php echo form_input(array('name'=>'descripcion', 'id'=> 'descripcion', 'placeholder'=>'Descripción (*)', 'class'=>'form-control', 'value'=> set_value('descripciondescripcion'))); ?>
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
            <option value="" disabled selected>-Seleccione País-</option>	
            <?php
                foreach ($listarPaises as $pais) {
                echo '<option  value="'.$pais->tabl_id.'">'.$pais->valor.'</option>';
                }
            ?>
        </select>
    </div>
    <div class="form-group">
        <select onchange="seleccionEstado()" class="form-control select select-hidden-accesible habilitar" name="prov_id" id="prov_id" style='width: 100%;'>
            <option value="" disabled selected>-Seleccione Estado/Provincia-</option>
        </select>
    </div>
    <div class="form-group">
        <select class="form-control select select-hidden-accesible habilitar" name="loca_id" id="loca_id" style='width: 100%;'>
            <option value="" disabled selected>-Seleccione Localidad-</option>
        </select>
    </div>
    <h5><strong>Logo de la empresa</strong></h5>
    <div class="form-group">
      <?php echo form_input(array('name'=>'image', 'accept' => 'image/*', 'id'=> 'image', 'type' => 'file', 'placeholder'=>'Foto Perfil', 'class'=>'form-control', 'value'=> set_value('image'))); ?>
      <?php echo form_error('image');?>
    </div>
    
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
                $('#prov_id').empty();
                $('#loca_id').empty();
                if (rsp != null) {
                    /* habilitarEdicion(); */
                    var datos = "<option value='' disabled selected>-Seleccione Estado/Provincia-</option>";
                    for (let i = 0; i < rsp.length; i++) {
                        var datito = encodeURIComponent(rsp[i].tabl_id);
                        datos += "<option value=" + datito + ">" + rsp[i].valor + "</option>";
                    }
                    $('#prov_id').html(datos);
                    var datos = "<option value='' disabled selected>-Seleccione Localidad-</option>";
                    $('#loca_id').html(datos);
                } else {
                    var provincia = "<option value='' disabled selected>-Seleccione Estado/Provincia-</option>";
                    $('#prov_id').html(provincia);
                    var localidad = "<option value='' disabled selected>-Seleccione Localidad-</option>";
                    $('#loca_id').html(localidad);
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
        var id_pais = $("#pais_id option:selected").text();
        var id_estado = $("#prov_id option:selected").text();
        /* wo(); */
        $.ajax({
            type: 'GET',
            dataType: "json",
            data: {id_pais, id_estado},
            url:'<?php echo base_url() ?>Empresa/getLocalidades',
            success: function(rsp) {
                $('#loca_id').empty();
                if (rsp != null) {
                    /* habilitarEdicion(); */
                    var datos = "<option value='' disabled selected>-Seleccione Localidad-</option>";
                    for (let i = 0; i < rsp.length; i++) {
                        var valor = encodeURIComponent(rsp[i].tabl_id);
                        datos += "<option value=" + valor + ">" + rsp[i].valor + "</option>";
                    }
                    $('#loca_id').html(datos);
                } else {
                    var datos = "<option value='' disabled selected>-Seleccione Localidad-</option>";
                    $('#loca_id').html(datos); 
                    alertify.error("El Estado no contiene localidades");
                }
            },
            error: function(data) {
                alert('Error');
            }
        });
    }
</script>