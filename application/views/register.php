<style>
    /* Timestamp: <?php echo time(); ?> - Color: #A6A6A6 DarkGray */
    * {
        margin: 0 !important;
        padding: 0 !important;
        box-sizing: border-box !important;
    }
    
    html, body {
        margin: 0 !important;
        padding: 0 !important;
        overflow-x: hidden !important;
        font-family: Arial, sans-serif !important;
        width: 100% !important;
        height: 100% !important;
        background: #696969 !important;
        background-color: #696969 !important;
        opacity: 1 !important;
    }
    
    .container, .row, .col, [class*="col-"] {
        margin: 0 !important;
        padding: 0 !important;
        width: 100% !important;
        max-width: none !important;
    }
    
    .register-container {
        background: #696969 !important;
        background-color: #696969 !important;
        background-image: none !important;
        height: 100vh;
        padding: 0;
        width: 100vw;
        margin: 0;
        position: fixed;
        top: 0;
        left: 0;
        display: flex;
        justify-content: flex-start;
        z-index: 1000;
        opacity: 1 !important;
    }
    
    .register-left {
        width: 40%;
        min-width: 500px;
        padding: 30px 50px 30px 50px;
        background: #696969 !important;
        background-color: #696969 !important;
        text-align: left;
        box-sizing: border-box;
        flex-shrink: 0;
    }
    
    .register-right {
        width: 60%;
        background: #2F2F2F url('<?php echo base_url(); ?>public/img/toolsregister.png') !important;
        background-color: #2F2F2F !important;
        background-size: cover !important;
        background-position: center !important;
        background-repeat: no-repeat !important;
        height: 100vh;
        box-sizing: border-box;
        flex: 1;
    }
    
    .logo-container {
        text-align: left;
        margin-bottom: 30px !important;
        margin-left: 20px !important;
        margin-top: 20px !important;
    }
    
    .logo-container img {
        max-width: 200px;
    }
    
    .register-title {
        font-family: 'Ubuntu', sans-serif;
        color: #ecf0f1;
        font-size: 2.2em;
        font-weight: bold;
        text-align: left;
        margin-bottom: 15px !important;
        margin-top: 10px !important;
        margin-left: 20px !important;
    }
    
    .register-subtitle {
        color: #bdc3c7;
        font-size: 1em;
        text-align: left;
        line-height: 1.3;
        margin-bottom: 40px !important;
        margin-top: 10px !important;
        margin-left: 20px !important;
    }
    
    .form-group {
        margin-bottom: 12px !important;
        width: 100%;
        margin-left: 20px !important;
    }
    
    .form-control {
        background-color: #ecf0f1;
        border: none !important;
        border-width: 0 !important;
        border-style: none !important;
        border-radius: 5px;
        padding: 12px !important;
        font-size: 16px;
        width: 100%;
        max-width: 400px;
        margin-top: 3px !important;
        outline: none !important;
        box-shadow: none !important;
    }
    
    .form-control:focus {
        border: none !important;
        border-width: 0 !important;
        border-style: none !important;
        border-color: transparent !important;
        box-shadow: none !important;
        outline: none !important;
    }
    
    .btn-register {
        background-color: #e74c3c;
        border: none;
        color: white;
        padding: 15px 30px !important;
        font-size: 18px;
        font-weight: bold;
        border-radius: 5px;
        width: 100%;
        max-width: 400px;
        margin-top: 15px !important;
        margin-bottom: 10px !important;
        margin-left: 20px !important;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }
    
    .btn-register:hover {
        background-color: #c0392b;
    }
    
    .warning-text {
        color: #f39c12 !important;
        font-size: 0.8em !important;
        font-style: italic !important;
        margin-top: 3px !important;
        margin-left: 20px !important;
        margin-right: 0 !important;
        padding-left: 0 !important;
        text-align: left !important;
    }
    
    .terms-text {
        color: #bdc3c7;
        font-size: 0.85em;
        text-align: left;
        margin-top: 10px !important;
        margin-bottom: 8px !important;
        margin-left: 20px !important;
        line-height: 1.3;
    }
    
    .login-link {
        color: #3498db;
        text-decoration: none;
    }
    
    .login-link:hover {
        text-decoration: underline;
    }
    
    .form-control:focus {
        outline: none;
    }
    
    .error-message {
        color: #e74c3c !important;
        font-size: 0.8em !important;
        margin-top: 3px !important;
        margin-left: 20px !important;
        display: none !important;
        background: rgba(231, 76, 60, 0.1) !important;
        padding: 5px 10px !important;
        border-radius: 3px !important;
        border-left: 3px solid #e74c3c !important;
        max-width: 400px !important;
    }
    
    .server-error-message {
        color: #e74c3c !important;
        font-size: 0.9em !important;
        margin: 0 0 20px 20px !important;
        max-width: 400px !important;
        padding: 12px !important;
        border-radius: 4px !important;
        background: rgba(231, 76, 60, 0.1) !important;
        border-left: 3px solid #e74c3c !important;
    }
    
    .form-container {
        transition: opacity 0.3s ease;
    }
    
    .form-container.hidden {
        display: none !important;
    }
</style>

    <?php 
// Capturar mensajes flash para mostrarlos dentro del marco del formulario
$arr = $this->session->flashdata();
$server_message_type = '';
$server_message_text = '';
if(!empty($arr['flash_message'])){
    $server_message_type = 'warning';
    $server_message_text = $arr['flash_message'];
}else if (!empty($arr['success_message'])){
    $server_message_type = 'success';
    $server_message_text = $arr['success_message'];
}else if (!empty($arr['danger_message'])){
    $server_message_type = 'danger';
    $server_message_text = $arr['danger_message'];
}
?>

<div class="register-container">
    <div class="register-left">
        <div class="logo-container">
            <img src="<?php echo base_url(); ?>public/img/toolsgrey.png" alt="Trazalog Tools">
        </div>
        
        <h1 class="register-title">Regístrese Gratis</h1>
        <p class="register-subtitle">Podés utilizar todas nuestras funcionalidades y generar gratuitamente hasta 5 usuarios</p>
    <?php if (!empty($server_message_text)): ?>
    <div id="server-message" class="server-error-message" style="<?php echo ($server_message_type==='success'?'background:#27ae60 !important; color:#fff !important;':'').($server_message_type==='warning'?'background:#f39c12 !important; color:#fff !important;':'').($server_message_type==='danger'?'background:#e74c3c !important; color:#fff !important;':''); ?>">
        <?php echo $server_message_text; ?>
    </div>
    <?php endif; ?>
        
    <div class="form-container" <?php echo ($server_message_type==='success') ? 'style="display: none;"' : ''; ?>>
        <?php echo form_open('main/register'); ?>
        
        <div class="form-group">
            <select name="reg_pais_id" id="reg_pais_id" class="form-control" required>
                <option value="">Seleccione un país *</option>
                <?php if(isset($paises) && is_array($paises)): ?>
                    <?php foreach($paises as $pais): ?>
                        <option value="<?php echo $pais['tabl_id']; ?>" <?php echo (isset($form_data['reg_pais_id']) && $form_data['reg_pais_id'] == $pais['tabl_id']) ? 'selected' : ''; ?>>
                            <?php echo get_flag_by_description($pais['descripcion']) . ' ' . $pais['descripcion']; ?>
                        </option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
        </div>
        
        <div class="form-group">
            <input type="text" name="reg_razon_social" id="reg_razon_social" class="form-control" placeholder="Nombre o Razón Social de la empresa *" value="<?php echo isset($form_data['reg_razon_social']) ? htmlspecialchars($form_data['reg_razon_social']) : ''; ?>" required>
        </div>
        
    <div class="form-group">
            <input type="text" name="firstname" id="firstname" class="form-control" placeholder="Nombre *" value="<?php echo isset($form_data['firstname']) ? htmlspecialchars($form_data['firstname']) : ''; ?>" required>
    </div>
        
    <div class="form-group">
            <input type="text" name="lastname" id="lastname" class="form-control" placeholder="Apellido *" value="<?php echo isset($form_data['lastname']) ? htmlspecialchars($form_data['lastname']) : ''; ?>" required>
    </div>
        
    <div class="form-group">
            <input type="email" name="email" id="email" class="form-control" placeholder="Email *" value="<?php echo isset($form_data['email']) ? htmlspecialchars($form_data['email']) : ''; ?>" required>
            <div class="warning-text">Atención! Utilizaremos este dominio para generarte usuarios para tu empresa</div>
    </div>
        
    <div class="form-group">
        <input type="tel" name="telefono" id="telefono" class="form-control" placeholder="Teléfono *" value="<?php echo isset($form_data['telefono']) ? htmlspecialchars($form_data['telefono']) : ''; ?>" required>
    </div>
        
        <button type="submit" class="btn-register">Utilizar Trazalog Tools Gratis</button>
        
        <div class="terms-text">
            Al registrarme, acepto los Términos y Condiciones y las Políticas de Privacidad de Trazalog.
        </div>
        
        <div class="terms-text">
            ¿Ya es usuario? <a href="<?php echo base_url(); ?>main/login" class="login-link">Acceda aquí</a>
        </div>
        
        <?php echo form_close(); ?>
    </div>
    </div>
    
    <div class="register-right">
        <!-- Imagen de fondo -->
    </div>
</div>

<script>
console.log('Script iniciando...');

document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM cargado');
    
    var telefonoInput = document.getElementById('telefono');
    var paisSelect = document.getElementById('reg_pais_id');
    var form = document.querySelector('form');
    
    console.log('Elementos encontrados:', {
        telefono: telefonoInput,
        pais: paisSelect,
        form: form
    });
    
    if (telefonoInput) {
        telefonoInput.addEventListener('keypress', function(e) {
            var ch = String.fromCharCode(e.which || e.keyCode);
            if (!/[\+0-9 \-\(\)]/.test(ch)) {
                e.preventDefault();
            }
        });
        console.log('Restricción de caracteres agregada');
        
        telefonoInput.addEventListener('input', function() {
            var telefono = telefonoInput.value.trim();
            var paisId = paisSelect.value;
            
            if (telefono && paisId) {
                var patrones = {
                    'paises_registracionAR': /^\+?54\s?9?\d{4}\s?\d{6}$/,
                    'paises_registracionPE': /^\+?51\s?9?\d{8}$/,
                    'paises_registracionEC': /^\+?593\s?9?\d{8}$/,
                    'paises_registracionDE': /^\+?49\s?\d{10,11}$/,
                    'paises_registracionMX': /^\+?52\s?9?\d{10}$/,
                    'paises_registracionUY': /^\+?598\s?9?\d{7}$/,
                    'paises_registracionBO': /^\+?591\s?9?\d{8}$/
                };
                
                var patron = patrones[paisId];
                if (patron && !patron.test(telefono)) {
                    telefonoInput.style.border = '1px solid #e74c3c';
                } else {
                    telefonoInput.style.border = 'none';
                }
            }
        });
        console.log('Validación en tiempo real agregada');
    }
    
    if (form) {
        form.addEventListener('submit', function(e) {
            console.log('Formulario enviado');
            
            var error = '';
            
            if (!paisSelect.value) {
                error = 'Seleccione un país';
            } else if (!document.getElementById('reg_razon_social').value.trim()) {
                error = 'Ingrese la razón social de la empresa';
            } else if (!document.getElementById('firstname').value.trim()) {
                error = 'Ingrese su nombre';
            } else if (!document.getElementById('lastname').value.trim()) {
                error = 'Ingrese su apellido';
            } else if (!document.getElementById('email').value.trim()) {
                error = 'Ingrese su email';
            } else if (!telefonoInput.value.trim()) {
                error = 'Ingrese su teléfono';
            } else {
                var telefono = telefonoInput.value.trim();
                var paisId = paisSelect.value;
                
                var patrones = {
                    'paises_registracionAR': /^\+?54\s?9?\d{4}\s?\d{6}$/,
                    'paises_registracionPE': /^\+?51\s?9?\d{8}$/,
                    'paises_registracionEC': /^\+?593\s?9?\d{8}$/,
                    'paises_registracionDE': /^\+?49\s?\d{10,11}$/,
                    'paises_registracionMX': /^\+?52\s?9?\d{10}$/,
                    'paises_registracionUY': /^\+?598\s?9?\d{7}$/,
                    'paises_registracionBO': /^\+?591\s?9?\d{8}$/
                };
                
                var patron = patrones[paisId];
                if (patron && !patron.test(telefono)) {
                    error = 'Formato de teléfono inválido para el país seleccionado';
                }
            }
            
            if (error) {
                e.preventDefault();
                alert(error);
                console.log('Validación falló:', error);
            } else {
                console.log('Validación exitosa');
            }
        });
        console.log('Validación de formulario agregada');
    }
    
    console.log('Todas las validaciones configuradas');
});
</script>
