<style>
body {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    min-height: 100vh;
    margin: 0;
    font-family: 'Ubuntu', sans-serif;
}

.empresa-container {
    display: flex;
    min-height: 100vh;
    align-items: center;
    justify-content: center;
    padding: 20px;
}

.empresa-left {
    background: rgba(255, 255, 255, 0.95);
    padding: 40px;
    border-radius: 10px;
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
    width: 100%;
    max-width: 600px;
    margin-right: 20px;
}

.empresa-right {
    background: rgba(255, 255, 255, 0.1);
    padding: 40px;
    border-radius: 10px;
    text-align: center;
    width: 100%;
    max-width: 400px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
}

.logo-container {
    text-align: center;
    margin-bottom: 30px;
}

.logo-container img {
    max-width: 200px;
    filter: grayscale(100%);
    opacity: 0.7;
}

.empresa-title {
    font-family: 'Ubuntu', sans-serif;
    color: #2c3e50;
    font-size: 2.2em;
    font-weight: bold;
    text-align: center;
    margin-bottom: 15px;
}

.empresa-subtitle {
    color: #7f8c8d;
    font-size: 1em;
    text-align: center;
    line-height: 1.3;
    margin-bottom: 30px;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    color: #2c3e50;
    font-weight: 600;
    font-size: 14px;
}

.form-control {
    background-color: #f8f9fa;
    border: 2px solid #ecf0f1;
    border-radius: 8px;
    padding: 15px;
    font-size: 16px;
    width: 100%;
    transition: all 0.3s ease;
    box-sizing: border-box;
}

.form-control:focus {
    border-color: #3498db;
    box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
    outline: none;
}

.form-control:disabled,
.form-control[readonly] {
    background-color: #e9ecef;
    cursor: not-allowed;
    opacity: 0.7;
}

select.form-control {
    cursor: pointer;
}

.btn-guardar {
    background: linear-gradient(45deg, #3498db, #2980b9);
    color: white;
    border: none;
    padding: 15px 30px;
    border-radius: 8px;
    font-size: 16px;
    font-weight: bold;
    width: 100%;
    cursor: pointer;
    transition: all 0.3s ease;
    margin-top: 10px;
}

.btn-guardar:hover {
    background: linear-gradient(45deg, #2980b9, #1f5f8b);
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
}

.error {
    color: #e74c3c;
    font-size: 14px;
    margin-top: 5px;
}

.image-container {
    text-align: center;
}

.image-container img {
    max-width: 100%;
    height: auto;
    border-radius: 10px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
}

.readonly-field {
    background-color: #e9ecef !important;
    cursor: not-allowed;
    opacity: 0.8;
}

@media (max-width: 768px) {
    .empresa-container {
        flex-direction: column;
    }
    
    .empresa-left, .empresa-right {
        margin-right: 0;
        margin-bottom: 20px;
    }
}
</style>

<div class="empresa-container">
    <div class="empresa-left">
        <div class="logo-container">
            <img src="<?php echo base_url(); ?>public/img/toolsgrey.png" alt="Trazalog Tools">
        </div>
        
        <h1 class="empresa-title">Completar Datos de Empresa</h1>
        <p class="empresa-subtitle">Por favor completa la información faltante de tu empresa para finalizar el registro.</p>
        
        <?php if ($this->session->flashdata('flash_message')): ?>
            <div class="alert alert-danger" style="background-color: #f8d7da; color: #721c24; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #f5c6cb;">
                <?php echo $this->session->flashdata('flash_message'); ?>
            </div>
        <?php endif; ?>
        
        <?php 
        $fattr = array('class' => 'form-signin', 'enctype'=>'multipart/form-data');
        echo form_open(site_url().'register/guardarEmpresa', $fattr); 
        ?>
        
        <!-- Campos Read-Only -->
        <div class="form-group">
            <label>Nombre de la Empresa</label>
            <?php echo form_input(array(
                'name'=>'nombre_display', 
                'id'=> 'nombre_display', 
                'value' => $user_data->reg_razon_social,
                'class'=>'form-control readonly-field', 
                'readonly' => 'readonly'
            )); ?>
        </div>
        
        <div class="form-group">
            <label>Descripción</label>
            <?php echo form_input(array(
                'name'=>'descripcion_display', 
                'id'=> 'descripcion_display', 
                'value' => $user_data->reg_razon_social,
                'class'=>'form-control readonly-field', 
                'readonly' => 'readonly'
            )); ?>
        </div>
        
        <div class="form-group">
            <label>Teléfono</label>
            <?php echo form_input(array(
                'name'=>'telefono_display', 
                'id'=> 'telefono_display', 
                'value' => $user_data->telefono,
                'class'=>'form-control readonly-field', 
                'readonly' => 'readonly'
            )); ?>
        </div>
        
        <div class="form-group">
            <label>Email</label>
            <?php echo form_input(array(
                'name'=>'email_display', 
                'id'=> 'email_display', 
                'value' => $user_data->email,
                'class'=>'form-control readonly-field', 
                'readonly' => 'readonly'
            )); ?>
        </div>
        
        <div class="form-group">
            <label>País</label>
            <?php echo form_input(array(
                'name'=>'pais_display', 
                'id'=> 'pais_display', 
                'value' => $pais_nombre,
                'class'=>'form-control readonly-field', 
                'readonly' => 'readonly'
            )); ?>
        </div>
        
        <!-- Campos Editables -->
        <div class="form-group">
            <label>Identificador Tributario <strong class="text-danger">*</strong></label>
            <?php echo form_input(array(
                'name'=>'cuit', 
                'id'=> 'cuit', 
                'placeholder'=>'Ingrese el Identificador Tributario', 
                'class'=>'form-control', 
                'value' => set_value('cuit')
            )); ?>
            <?php echo form_error('cuit'); ?>
        </div>
        
        <div class="form-group">
            <label>Provincia/Estado <strong class="text-danger">*</strong></label>
            <select onchange="seleccionEstado()" class="form-control" name="prov_id" id="prov_id" required>
                <option value="" disabled selected>-Seleccione Provincia/Estado-</option>
            </select>
            <?php echo form_error('prov_id'); ?>
        </div>
        
        <div class="form-group">
            <label>Localidad <strong class="text-danger">*</strong></label>
            <select class="form-control" name="loca_id" id="loca_id" required>
                <option value="" disabled selected>-Seleccione Localidad-</option>
            </select>
            <?php echo form_error('loca_id'); ?>
        </div>
        
        <div class="form-group">
            <label>Logo de la Empresa (Opcional)</label>
            <?php echo form_input(array(
                'name'=>'image', 
                'accept' => 'image/*', 
                'id'=> 'image', 
                'type' => 'file', 
                'class'=>'form-control'
            )); ?>
            <small class="text-muted">Formatos aceptados: JPG, PNG, GIF. Tamaño máximo: 5MB</small>
        </div>
        
        <?php echo form_submit(array('value'=>'Guardar y Continuar', 'class'=>'btn-guardar')); ?>
        <?php echo form_close(); ?>
    </div>
    
    <div class="empresa-right">
        <div class="image-container">
            <img src="<?php echo base_url(); ?>public/img/toolscreaempr.png" alt="Crear Empresa">
        </div>
    </div>
</div>

<script>
    // Cargar estados al cargar la página si hay un país seleccionado
    $(document).ready(function() {
        var pais_id = '<?php echo $pais_id; ?>';
        if (pais_id) {
            // Obtener el nombre del país para enviarlo al API
            var pais_nombre = '<?php echo addslashes($pais_nombre); ?>';
            if (pais_nombre) {
                seleccionPais(pais_nombre);
            }
        }
    });
    
    /* Carga Estados dependiendo del pais seleccionado */
    function seleccionPais(paisNombre) {
        if (!paisNombre) {
            paisNombre = $("#pais_display").val();
        }
        
        $.ajax({
            type: 'GET',
            dataType: "json",
            data: {id_pais: paisNombre},
            url:'<?php echo base_url() ?>Register/getEstados',
            success: function(rsp) {
                $('#prov_id').empty();
                $('#loca_id').empty();
                if (rsp != null && rsp.length > 0) {
                    var datos = "<option value='' disabled selected>-Seleccione Provincia/Estado-</option>";
                    for (let i = 0; i < rsp.length; i++) {
                        var datito = encodeURIComponent(rsp[i].tabl_id);
                        datos += "<option value=" + datito + ">" + rsp[i].valor + "</option>";
                    }
                    $('#prov_id').html(datos);
                    var datos = "<option value='' disabled selected>-Seleccione Localidad-</option>";
                    $('#loca_id').html(datos);
                } else {
                    var provincia = "<option value='' disabled selected>-Seleccione Provincia/Estado-</option>";
                    $('#prov_id').html(provincia);
                    var localidad = "<option value='' disabled selected>-Seleccione Localidad-</option>";
                    $('#loca_id').html(localidad);
                    alert('El País no contiene estados');
                }
            },
            error: function(data) {
                console.error('Error al cargar estados:', data);
                alert('Error al cargar las provincias/estados');
            }
        });
    }

    /* Carga Localidades dependiendo del estado seleccionado */
    function seleccionEstado() {
        var pais_nombre = '<?php echo addslashes($pais_nombre); ?>';
        var id_estado = $("#prov_id option:selected").text();
        
        if (!id_estado || id_estado === '-Seleccione Provincia/Estado-') {
            $('#loca_id').empty();
            $('#loca_id').html("<option value='' disabled selected>-Seleccione Localidad-</option>");
            return;
        }
        
        $.ajax({
            type: 'GET',
            dataType: "json",
            data: {id_pais: pais_nombre, id_estado: id_estado},
            url:'<?php echo base_url() ?>Register/getLocalidades',
            success: function(rsp) {
                $('#loca_id').empty();
                if (rsp != null && rsp.length > 0) {
                    var datos = "<option value='' disabled selected>-Seleccione Localidad-</option>";
                    for (let i = 0; i < rsp.length; i++) {
                        var valor = encodeURIComponent(rsp[i].tabl_id);
                        datos += "<option value=" + valor + ">" + rsp[i].valor + "</option>";
                    }
                    $('#loca_id').html(datos);
                } else {
                    var datos = "<option value='' disabled selected>-Seleccione Localidad-</option>";
                    $('#loca_id').html(datos); 
                    alert('El Estado no contiene localidades');
                }
            },
            error: function(data) {
                console.error('Error al cargar localidades:', data);
                alert('Error al cargar las localidades');
            }
        });
    }
</script>

