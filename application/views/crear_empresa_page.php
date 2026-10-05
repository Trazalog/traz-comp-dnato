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
            <img src="<?php echo base_url() . REGISTER_IMG_LOGO; ?>" alt="Trazalog Tools">
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
            <label>Correo electrónico</label>
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
        <?php
            $is_webmail   = !empty($is_webmail);
            $email_domain = isset($email_domain) ? $email_domain : '';
        ?>
        <?php if ($is_webmail): ?>
        <div class="form-group">
            <label>Dominio de la Empresa <strong class="text-danger">*</strong></label>
            <?php echo form_input(array(
                'name'        => 'company_domain',
                'id'          => 'company_domain',
                'placeholder' => 'Ej: rtools.ca',
                'class'       => 'form-control',
                'value'       => set_value('company_domain'),
                'required'    => 'required',
                'pattern'     => '^@?[A-Za-z0-9-]+(\.[A-Za-z0-9-]+)+$'
            )); ?>
            <small class="text-muted">
                Detectamos que te registraste con un email de webmail publico (<strong><?php echo htmlspecialchars($email_domain); ?></strong>).
                Ingresá el dominio corporativo de tu empresa; lo vamos a usar para crear los usuarios por defecto
                (ej: <em>usuario@tuempresa.com</em>).
            </small>
            <?php echo form_error('company_domain'); ?>
        </div>
        <?php else: ?>
        <div class="form-group">
            <label>Dominio de la Empresa</label>
            <?php echo form_input(array(
                'name'     => 'company_domain_display',
                'id'       => 'company_domain_display',
                'value'    => $email_domain,
                'class'    => 'form-control readonly-field',
                'readonly' => 'readonly'
            )); ?>
            <small class="text-muted">Este dominio se utilizará para crear los usuarios por defecto de la empresa.</small>
        </div>
        <?php endif; ?>

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
            <img src="<?php echo base_url() . REGISTER_IMG_CREAR_EMPRESA; ?>" alt="Crear Empresa">
        </div>
    </div>
</div>

<script>
    var URL_ESTADOS = <?php echo json_encode(site_url('register/getEstados')); ?>;
    var URL_LOCALIDADES = <?php echo json_encode(site_url('register/getLocalidades')); ?>;
    var PAIS_NOMBRE_REG = <?php echo json_encode($pais_nombre); ?>;
    var PAIS_ID_REG = <?php echo json_encode($pais_id); ?>;

    console.log('[crearEmpresa][v3-kickoff] script loaded', {
        URL_ESTADOS: URL_ESTADOS,
        URL_LOCALIDADES: URL_LOCALIDADES,
        PAIS_NOMBRE_REG: PAIS_NOMBRE_REG,
        PAIS_ID_REG: PAIS_ID_REG,
        jQuery: typeof jQuery,
        $: typeof $
    });

    function coerceListaTablas(rsp) {
        if (rsp == null) {
            return [];
        }
        if (Array.isArray(rsp)) {
            return rsp;
        }
        return [rsp];
    }

    function seleccionPais(paisNombre) {
        if (!paisNombre) {
            paisNombre = '';
        }
        console.log('[crearEmpresa] seleccionPais() → GET', URL_ESTADOS);

        $.ajax({
            type: 'GET',
            dataType: 'json',
            data: { id_pais: paisNombre },
            url: URL_ESTADOS,
            success: function(rsp) {
                console.log('[crearEmpresa] getEstados success. raw rsp:', rsp);
                var lista = coerceListaTablas(rsp);
                console.log('[crearEmpresa] getEstados lista normalizada length=' + lista.length, lista);
                $('#prov_id').empty();
                $('#loca_id').empty();
                if (lista.length > 0) {
                    var datos = "<option value='' disabled selected>-Seleccione Provincia/Estado-</option>";
                    for (var i = 0; i < lista.length; i++) {
                        var datito = encodeURIComponent(lista[i].tabl_id);
                        datos += "<option value=" + datito + ">" + lista[i].valor + "</option>";
                    }
                    $('#prov_id').html(datos);
                    $('#loca_id').html("<option value='' disabled selected>-Seleccione Localidad-</option>");
                } else {
                    $('#prov_id').html("<option value='' disabled selected>-Seleccione Provincia/Estado-</option>");
                    $('#loca_id').html("<option value='' disabled selected>-Seleccione Localidad-</option>");
                    console.warn('[crearEmpresa] getEstados devolvió lista vacía');
                }
            },
            error: function(xhr) {
                console.error('[crearEmpresa] Error al cargar estados:', xhr.status, xhr.responseText);
            }
        });
    }

    function seleccionEstado() {
        var id_estado = $('#prov_id option:selected').text();
        console.log('[crearEmpresa] seleccionEstado() → id_estado=', id_estado);

        if (!id_estado || id_estado === '-Seleccione Provincia/Estado-') {
            $('#loca_id').empty();
            $('#loca_id').html("<option value='' disabled selected>-Seleccione Localidad-</option>");
            return;
        }

        $.ajax({
            type: 'GET',
            dataType: 'json',
            data: { id_estado: id_estado },
            url: URL_LOCALIDADES,
            success: function(rsp) {
                console.log('[crearEmpresa] getLocalidades success. raw rsp:', rsp);
                var lista = coerceListaTablas(rsp);
                $('#loca_id').empty();
                if (lista.length > 0) {
                    var datos = "<option value='' disabled selected>-Seleccione Localidad-</option>";
                    for (var i = 0; i < lista.length; i++) {
                        var valor = encodeURIComponent(lista[i].tabl_id);
                        datos += "<option value=" + valor + ">" + lista[i].valor + "</option>";
                    }
                    $('#loca_id').html(datos);
                } else {
                    $('#loca_id').html("<option value='' disabled selected>-Seleccione Localidad-</option>");
                    console.warn('[crearEmpresa] getLocalidades devolvió lista vacía');
                }
            },
            error: function(xhr) {
                console.error('[crearEmpresa] Error al cargar localidades:', xhr.status, xhr.responseText);
            }
        });
    }

    /*
     * Los elementos del combo ya existen en el DOM al ejecutarse este script (está al final del view),
     * por lo que disparamos la carga de estados sin depender de $(document).ready.
     * Nota: NO usar comentarios de linea (//) en <script> inline en este proyecto; el hook 'compress'
     * colapsa los saltos de linea del HTML y dejaria comentado todo lo que viene despues.
     */
    (function kickoffSeleccionPais() {
        if (typeof jQuery === 'undefined') {
            console.error('[crearEmpresa] kickoff: jQuery NO está definido, no se puede cargar estados');
            return;
        }
        console.log('[crearEmpresa] kickoff → llamando seleccionPais() (sin ready)');
        try {
            seleccionPais();
        } catch (e) {
            console.error('[crearEmpresa] kickoff: error al llamar seleccionPais()', e);
        }
    })();
</script>

