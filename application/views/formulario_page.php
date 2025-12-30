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
            <img src="<?php echo base_url(); ?>public/img/toolsgrey.png" alt="Trazalog Tools">
        </div>
        
        <h1 class="form-title">Información Adicional de Registro</h1>
        <p class="form-subtitle">Por favor completa la siguiente información para finalizar tu registro:</p>
        
        <!-- Script debe estar ANTES del formulario para que frmGuardar esté disponible -->
        <script type="text/javascript">
        console.log('=== DEBUG INICIO: Definiendo frmGuardar ===');
        console.log('DEBUG: typeof jQuery:', typeof jQuery);
        console.log('DEBUG: typeof $:', typeof $);
        console.log('DEBUG: typeof frmGuardar ANTES:', typeof frmGuardar);
        console.log('DEBUG: typeof window.frmGuardar ANTES:', typeof window.frmGuardar);
        
        // Guardar referencia a función existente si existe
        var frmGuardarOriginal = window.frmGuardar;
        if (frmGuardarOriginal) {
            console.log('DEBUG: frmGuardar ya existía, guardando referencia');
        }
        
        // Definir frmGuardar globalmente de forma simple (sin IIFE para que esté disponible inmediatamente)
        function frmGuardar(button) {
            console.log('=== DEBUG: frmGuardar EJECUTADO ===');
            console.log('DEBUG: button recibido:', button);
            console.log('DEBUG: typeof jQuery:', typeof jQuery);
            console.log('DEBUG: typeof $:', typeof $);
            
            if (typeof jQuery === 'undefined' && typeof $ === 'undefined') {
                alert('ERROR: jQuery no está disponible');
                console.error('ERROR: jQuery no disponible');
                return false;
            }
            
            var $ = jQuery || window.$;
            var form = $(button).closest('form');
            console.log('DEBUG: form encontrado:', form.length > 0);
            console.log('DEBUG: form ID:', form.attr('id'));
            console.log('DEBUG: form data-info:', form.attr('data-info'));
            console.log('DEBUG: form data-ninfoid:', form.attr('data-ninfoid'));
            
            if (form.length === 0) {
                alert('ERROR: No se pudo encontrar el formulario');
                console.error('ERROR: Form no encontrado');
                return false;
            }
            
            var formData = new FormData(form[0]);
            var info_id = form.attr('data-info') || form.attr('data-ninfoid') || form.find('input[name="info_id"]').val();
            console.log('DEBUG: info_id obtenido:', info_id);
            
            if (!info_id || info_id === 'null' || info_id === '' || info_id === null) {
                alert('Error: No se pudo obtener el ID del formulario. info_id=' + info_id);
                console.error('ERROR: info_id inválido:', info_id);
                return false;
            }
            
            formData.append('info_id', info_id);
            var url = '<?php echo base_url(); ?>register/guardarFormularioRegistro';
            console.log('DEBUG: URL AJAX:', url);
            console.log('DEBUG: Enviando AJAX...');
            
            $.ajax({
                type: 'POST',
                url: url,
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(response) {
                    console.log('DEBUG: Respuesta AJAX exitosa:', response);
                    if (response && response.success) {
                        // Redirigir a crearEmpresa si está en la respuesta, sino usar la URL por defecto
                        if (response.redirect) {
                            window.location.href = response.redirect;
                        } else {
                            window.location.href = '<?php echo base_url(); ?>register/crearEmpresa';
                        }
                    } else {
                        var errorMsg = response && response.message ? response.message : 'Error desconocido';
                        alert('Error: ' + errorMsg);
                        console.error('ERROR en respuesta:', response);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('ERROR AJAX completo:', {
                        status: status,
                        error: error,
                        responseText: xhr.responseText,
                        statusCode: xhr.status
                    });
                    alert('Error al guardar el formulario. Ver consola para detalles.');
                }
            });
            
            return false;
        }
        
        // Asegurar que también esté en window
        window.frmGuardar = frmGuardar;
        
        console.log('DEBUG: typeof frmGuardar DESPUÉS:', typeof frmGuardar);
        console.log('DEBUG: typeof window.frmGuardar DESPUÉS:', typeof window.frmGuardar);
        console.log('DEBUG: frmGuardar === window.frmGuardar:', frmGuardar === window.frmGuardar);
        console.log('=== DEBUG FIN: frmGuardar definida ===');
        </script>
        
        <!-- Formulario dinámico usando el módulo traz-comp-formularios -->
        <div class="panel panel-default">
            <div class="panel-body">
                <?php echo nuevoForm($form_id); ?>
            </div>
        </div>
        
        <!-- Script INLINE inmediatamente después del formulario para interceptar onclick -->
        <script type="text/javascript">
        console.log('=== DEBUG INLINE: Script inmediatamente después del formulario ===');
        console.log('DEBUG INLINE: typeof frmGuardar:', typeof frmGuardar);
        console.log('DEBUG INLINE: typeof window.frmGuardar:', typeof window.frmGuardar);
        
        // Ejecutar inmediatamente (sin esperar a document.ready)
        (function() {
            console.log('DEBUG INLINE: Ejecutando inmediatamente...');
            var botones = document.querySelectorAll('.frm-save');
            console.log('DEBUG INLINE: Botones encontrados (querySelectorAll):', botones.length);
            
            for (var i = 0; i < botones.length; i++) {
                var btn = botones[i];
                console.log('DEBUG INLINE: Botón ' + i + ':', {
                    onclick: btn.getAttribute('onclick'),
                    class: btn.className
                });
                
                // Guardar onclick original
                var onclickOriginal = btn.getAttribute('onclick');
                console.log('DEBUG INLINE: onclick original:', onclickOriginal);
                
                // Remover onclick y agregar event listener
                btn.removeAttribute('onclick');
                btn.addEventListener('click', function(e) {
                    console.log('=== DEBUG INLINE: Click capturado (addEventListener) ===');
                    e.preventDefault();
                    e.stopPropagation();
                    
                    console.log('DEBUG INLINE: typeof frmGuardar en click:', typeof frmGuardar);
                    console.log('DEBUG INLINE: typeof window.frmGuardar en click:', typeof window.frmGuardar);
                    
                    if (typeof window.frmGuardar === 'function') {
                        console.log('DEBUG INLINE: Llamando window.frmGuardar');
                        window.frmGuardar(this);
                    } else if (typeof frmGuardar === 'function') {
                        console.log('DEBUG INLINE: Llamando frmGuardar');
                        frmGuardar(this);
                    } else {
                        console.error('ERROR INLINE: frmGuardar no está disponible!');
                        alert('Error: frmGuardar no está disponible. Ver consola para detalles.');
                    }
                    return false;
                }, false);
                
                console.log('DEBUG INLINE: Event listener agregado al botón ' + i);
            }
            
            console.log('=== DEBUG INLINE: Procesamiento completado ===');
        })();
        </script>
    </div>
    
    <div class="form-right">
        <div class="image-container">
            <img src="<?php echo base_url(); ?>public/img/toolsform.png" alt="Formulario de Registro">
        </div>
    </div>
</div>

<!-- Script adicional DESPUÉS del formulario para reemplazar onclick con event listeners -->
<script type="text/javascript">
console.log('=== DEBUG POST-FORM: Script después del formulario ===');
console.log('DEBUG POST-FORM: typeof jQuery:', typeof jQuery);
console.log('DEBUG POST-FORM: typeof $:', typeof $);
console.log('DEBUG POST-FORM: typeof frmGuardar:', typeof frmGuardar);
console.log('DEBUG POST-FORM: typeof window.frmGuardar:', typeof window.frmGuardar);

// Verificar si hay scripts que sobrescriban frmGuardar
if (typeof frmGuardar !== 'function' || typeof window.frmGuardar !== 'function') {
    console.error('ERROR POST-FORM: frmGuardar no está definida después del formulario!');
    console.error('ERROR POST-FORM: Re-definiendo frmGuardar...');
    
    window.frmGuardar = function(button) {
        console.log('=== DEBUG POST-FORM: frmGuardar EJECUTADO (re-definición) ===');
        var $ = jQuery || window.$;
        var form = $(button).closest('form');
        var formData = new FormData(form[0]);
        var info_id = form.attr('data-info') || form.attr('data-ninfoid') || form.find('input[name="info_id"]').val();
        
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
                if (response && response.success) {
                    if (response.redirect) {
                        window.location.href = response.redirect;
                    } else {
                        window.location.href = '<?php echo base_url(); ?>register/crearEmpresa';
                    }
                } else {
                    alert('Error: ' + (response && response.message ? response.message : 'Error desconocido'));
                }
            },
            error: function(xhr, status, error) {
                console.error('Error AJAX:', status, error);
                alert('Error al guardar el formulario');
            }
        });
        
        return false;
    };
    frmGuardar = window.frmGuardar;
    console.log('DEBUG POST-FORM: frmGuardar re-definida');
}

$(document).ready(function() {
    console.log('=== DEBUG POST-FORM: jQuery ready ===');
    console.log('DEBUG POST-FORM: Buscando botones .frm-save...');
    
    var botones = $('.frm-save');
    console.log('DEBUG POST-FORM: Botones encontrados:', botones.length);
    
    botones.each(function(index) {
        var $btn = $(this);
        console.log('DEBUG POST-FORM: Botón ' + index + ':', {
            onclick: $btn.attr('onclick'),
            class: $btn.attr('class'),
            id: $btn.attr('id')
        });
        
        // Guardar onclick original para debug
        var onclickOriginal = $btn.attr('onclick');
        console.log('DEBUG POST-FORM: onclick original:', onclickOriginal);
        
        // Remover el atributo onclick y agregar event listener
        $btn.removeAttr('onclick').on('click', function(e) {
            console.log('=== DEBUG POST-FORM: Click capturado por event listener ===');
            e.preventDefault();
            e.stopPropagation();
            
            console.log('DEBUG POST-FORM: typeof frmGuardar en click:', typeof frmGuardar);
            console.log('DEBUG POST-FORM: typeof window.frmGuardar en click:', typeof window.frmGuardar);
            
            if (typeof frmGuardar === 'function') {
                console.log('DEBUG POST-FORM: Llamando frmGuardar (sin window)');
                frmGuardar(this);
            } else if (typeof window.frmGuardar === 'function') {
                console.log('DEBUG POST-FORM: Llamando window.frmGuardar');
                window.frmGuardar(this);
            } else {
                console.error('ERROR POST-FORM: frmGuardar no está disponible en el click!');
                alert('Error: frmGuardar no está disponible. Ver consola para detalles.');
            }
            return false;
        });
        
        console.log('DEBUG POST-FORM: Event listener agregado al botón ' + index);
    });
    
    console.log('=== DEBUG POST-FORM: jQuery ready completado ===');
});

// Verificación final después de un pequeño delay
setTimeout(function() {
    console.log('=== DEBUG POST-FORM: Verificación final (después de 500ms) ===');
    console.log('DEBUG POST-FORM: typeof frmGuardar:', typeof frmGuardar);
    console.log('DEBUG POST-FORM: typeof window.frmGuardar:', typeof window.frmGuardar);
    console.log('DEBUG POST-FORM: Botones .frm-save:', $('.frm-save').length);
}, 500);
</script>

