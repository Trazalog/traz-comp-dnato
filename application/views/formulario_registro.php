<div class="row">
    <div class="col-md-8 col-md-offset-2">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Información Adicional de Registro</h3>
            </div>
            <div class="box-body">
                <p class="text-muted">
                    Por favor completa la siguiente información para finalizar tu registro:
                </p>
                
                <!-- Formulario dinámico usando el módulo traz-comp-formularios -->
                <div class="panel panel-default">
                    <div class="panel-body">
                        <?php echo nuevoForm($form_id); ?>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
</div>

<script>
function frmGuardar(button) {
    console.log('frmGuardar llamado');
    
    var form = $(button).closest('form');
    var formData = new FormData(form[0]);
    var info_id = form.attr('data-info');
    
    if (!info_id) {
        alert('Error: No se pudo obtener el ID del formulario');
        return false;
    }
    
    formData.append('info_id', info_id);
    
    $.ajax({
        type: 'POST',
        url: '<?php echo base_url(); ?>register/guardarFormularioRegistro',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                alert('¡Formulario guardado correctamente!');
                window.location.href = '<?php echo base_url(); ?>register/registro_completo';
            } else {
                alert('Error: ' + response.message);
            }
        },
        error: function() {
            alert('Error al guardar el formulario');
        }
    });
}

$(document).ready(function() {
    $('.frm-save').removeAttr('onclick').on('click', function(e) {
        e.preventDefault();
        frmGuardar(this);
    });
});
</script>