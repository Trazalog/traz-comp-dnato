<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<!-- Contenido principal -->
<div class="container">
    <div class="row">
        <div class="col-lg-12">
            <h2><?php echo $title; ?></h2>
            <hr>
            
            <!-- Mensajes de estado -->
            <?php if($this->session->flashdata('success_message')): ?>
                <div class="alert alert-success alert-dismissible" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <?php echo $this->session->flashdata('success_message'); ?>
                </div>
            <?php endif; ?>
            
            <?php if($this->session->flashdata('error_message')): ?>
                <div class="alert alert-danger alert-dismissible" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <?php echo $this->session->flashdata('error_message'); ?>
                </div>
            <?php endif; ?>
            
            <!-- Formulario de carga masiva -->
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Formulario de Carga Masiva</h3>
                </div>
                <div class="panel-body">
                    <?php echo form_open_multipart('bulkload/procesarCarga', array('id' => 'bulkloadForm', 'class' => 'form-horizontal')); ?>
                        
                        <!-- Selección de entidad de negocio -->
                        <div class="form-group">
                            <label for="entidad_negocio" class="col-sm-3 control-label">
                                Seleccione Entidad de Negocio a Cargar <span class="text-danger">*</span>
                            </label>
                            <div class="col-sm-9">
                                <select class="form-control" name="entidad_negocio" id="entidad_negocio" required>
                                    <option value="">- Seleccione una entidad -</option>
                                    <?php if(isset($entidades) && is_array($entidades)): ?>
                                        <?php foreach($entidades as $entidad): ?>
                                            <option value="<?php echo htmlspecialchars($entidad['nombre']); ?>" 
                                                    data-stored-procedure="<?php echo htmlspecialchars($entidad['stored_procedure']); ?>"
                                                    data-template="<?php echo htmlspecialchars($entidad['template'], ENT_QUOTES, 'UTF-8'); ?>"
                                                    <?php echo ($this->session->userdata('selected_entidad') == $entidad['nombre']) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($entidad['nombre']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                                <span class="help-block">Seleccione la entidad de negocio que desea cargar masivamente</span>
                            </div>
                        </div>
                        
                        <!-- Campos ocultos -->
                        <input type="hidden" name="stored_procedure" id="stored_procedure" value="">
                        
                        <!-- Sección de template (inicialmente oculta) -->
                        <div id="templateSection" style="display: none;">
                            <div class="form-group">
                                <label class="col-sm-3 control-label">Descargue Template</label>
                                <div class="col-sm-9">
                    <a href="#" id="downloadTemplate" class="btn btn-info" target="_blank">
                        <i class="fa fa-download"></i> Descargar Template
                    </a>
                                    <p class="help-block">
                                        Abra el template de Excel y complételo como se indica, 
                                        luego selecciónelo debajo y presione "Cargar"
                                    </p>
                                </div>
                            </div>
                            
                            <!-- Selección de archivo -->
                            <div class="form-group">
                                <label for="archivo_excel" class="col-sm-3 control-label">
                                    Seleccione archivo <span class="text-danger">*</span>
                                </label>
                                <div class="col-sm-9">
                                    <input type="file" class="form-control" name="archivo_excel" id="archivo_excel" 
                                           accept=".xlsx,.xls" required>
                                    <span class="help-block">
                                        Solo se permiten archivos Excel (.xlsx, .xls) con encoding UTF-8
                                    </span>
                                </div>
                            </div>
                            
                            <!-- Botón de carga -->
                            <div class="form-group">
                                <div class="col-sm-offset-3 col-sm-9">
                                    <button type="submit" class="btn btn-primary" id="btnCargar">
                                        <i class="fa fa-upload"></i> Cargar
                                    </button>
                                    <button type="reset" class="btn btn-default">
                                        <i class="fa fa-refresh"></i> Limpiar
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                    <?php echo form_close(); ?>
                </div>
            </div>
            
            <!-- Información adicional -->
            <div class="panel panel-info">
                <div class="panel-heading">
                    <h3 class="panel-title">Información Importante</h3>
                </div>
                <div class="panel-body">
                    <ul>
                        <li><strong>Formato de archivo:</strong> Solo se aceptan archivos Excel (.xlsx, .xls)</li>
                        <li><strong>Encoding:</strong> El archivo debe estar en UTF-8</li>
                        <li><strong>Tamaño máximo:</strong> 10 MB</li>
                        <li><strong>Proceso:</strong> El archivo se procesa en el servidor y se envía al sistema central</li>
                        <li><strong>Resultado:</strong> Se mostrará el estado del procesamiento en pantalla</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript para funcionalidad -->
<script>
$(document).ready(function() {
    $('#entidad_negocio').change(function() {
        var valor = $(this).val();
        var selectedOption = $(this).find('option:selected');
        var storedProcedure = selectedOption.data('stored-procedure');
        var template = selectedOption.data('template');
        
        if (valor !== '') {
            $('#templateSection').show();
            $('#stored_procedure').val(storedProcedure);
            $('#btnCargar').prop('disabled', false);
            
            var downloadBtn = $('#downloadTemplate');
            downloadBtn.attr('href', template);
            
            if (template.includes('docs.google.com') || template.includes('drive.google.com')) {
                downloadBtn.attr('target', '_blank');
                downloadBtn.html('<i class="fa fa-download"></i> Descargar Template');
            } else {
                downloadBtn.removeAttr('target');
                downloadBtn.html('<i class="fa fa-download"></i> Descargar Template');
            }
        } else {
            $('#templateSection').hide();
            $('#stored_procedure').val('');
            $('#btnCargar').prop('disabled', true);
        }
    });
    
    // Ejecutar al cargar si hay entidad preseleccionada
    $('#entidad_negocio').trigger('change');
});
</script>
