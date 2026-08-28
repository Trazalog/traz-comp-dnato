<style>
body {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    min-height: 100vh;
    margin: 0;
    font-family: 'Ubuntu', sans-serif;
}

.form-container {
    display: flex;
    min-height: 100vh;
    align-items: center;
    justify-content: center;
    padding: 20px;
}

.form-left {
    background: rgba(255, 255, 255, 0.95);
    padding: 40px;
    border-radius: 10px;
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
    width: 100%;
    max-width: 600px;
    margin-right: 20px;
}

.form-right {
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

.form-title {
    font-family: 'Ubuntu', sans-serif;
    color: #2c3e50;
    font-size: 2.2em;
    font-weight: bold;
    text-align: center;
    margin-bottom: 15px;
}

.form-subtitle {
    color: #7f8c8d;
    font-size: 1em;
    text-align: center;
    line-height: 1.3;
    margin-bottom: 30px;
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

@media (max-width: 768px) {
    .form-container {
        flex-direction: column;
    }

    .form-left, .form-right {
        margin-right: 0;
        margin-bottom: 20px;
    }
}
</style>

<div class="form-container">
    <div class="form-left">
        <div class="logo-container">
            <img src="<?php echo base_url() . (defined('REGISTER_IMG_LOGO') ? REGISTER_IMG_LOGO : 'public/img/toolsgrey.png'); ?>" alt="Trazalog Tools">
        </div>

        <h1 class="form-title">Información Adicional de Registro</h1>
        <p class="form-subtitle">Por favor completa la siguiente información para finalizar tu registro:</p>

        <div class="panel panel-default">
            <div class="panel-body">
                <?php echo getForm($info_id); ?>
            </div>
        </div>
    </div>

    <div class="form-right">
        <div class="image-container">
            <img src="<?php echo base_url() . (defined('REGISTER_IMG_FORMULARIO') ? REGISTER_IMG_FORMULARIO : 'public/img/toolsform.jpg'); ?>" alt="Formulario de Registro">
        </div>
    </div>
</div>

<script type="text/javascript">
(function () {
    var GUARDAR_URL = <?php echo json_encode(base_url('register/guardarFormularioRegistro')); ?>;
    var CREAR_EMPRESA_URL = <?php echo json_encode(base_url('register/crearEmpresa')); ?>;

    function frmGuardar(button) {
        var $btn = jQuery(button);
        var $form = $btn.closest('form');

        if ($form.length === 0) {
            alert('No se pudo encontrar el formulario.');
            return false;
        }

        var info_id = $form.attr('data-info')
            || $form.attr('data-ninfoid')
            || $form.find('input[name="info_id"]').val();

        if (!info_id || info_id === 'null') {
            alert('No se pudo obtener el identificador del formulario.');
            return false;
        }

        var formData = new FormData($form[0]);
        formData.append('info_id', info_id);

        $btn.prop('disabled', true);

        jQuery.ajax({
            type: 'POST',
            url: GUARDAR_URL,
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json'
        }).done(function (response) {
            if (response && response.success) {
                window.location.href = response.redirect || CREAR_EMPRESA_URL;
            } else {
                var msg = response && response.message ? response.message : 'Error desconocido';
                alert('Error al guardar: ' + msg);
                $btn.prop('disabled', false);
            }
        }).fail(function (xhr) {
            console.error('Error AJAX al guardar formulario:', xhr.status, xhr.responseText);
            alert('Error al guardar el formulario. Revisá la consola para más detalles.');
            $btn.prop('disabled', false);
        });

        return false;
    }

    window.frmGuardar = frmGuardar;

    jQuery(function ($) {
        $('.frm-save').each(function () {
            var $btn = $(this);
            $btn.removeAttr('onclick');
            $btn.off('click.frmGuardar').on('click.frmGuardar', function (e) {
                e.preventDefault();
                e.stopPropagation();
                return window.frmGuardar(this);
            });
        });
    });
})();
</script>
