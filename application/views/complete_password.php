<style>
body {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    min-height: 100vh;
    margin: 0;
    font-family: 'Ubuntu', sans-serif;
}

.password-container {
    display: flex;
    min-height: 100vh;
    align-items: center;
    justify-content: center;
    padding: 20px;
}

.password-left {
    background: rgba(255, 255, 255, 0.95);
    padding: 40px;
    border-radius: 10px;
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
    width: 100%;
    max-width: 450px;
    margin-right: 20px;
}

.password-right {
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

.password-title {
    font-family: 'Ubuntu', sans-serif;
    color: #2c3e50;
    font-size: 2.2em;
    font-weight: bold;
    text-align: center;
    margin-bottom: 15px;
}

.password-subtitle {
    color: #7f8c8d;
    font-size: 1em;
    text-align: center;
    line-height: 1.3;
    margin-bottom: 30px;
}

.form-group {
    margin-bottom: 20px;
}

.form-control {
    background-color: #f8f9fa;
    border: 2px solid #ecf0f1;
    border-radius: 8px;
    padding: 15px;
    font-size: 16px;
    width: 100%;
    transition: all 0.3s ease;
}

.form-control:focus {
    border-color: #3498db;
    box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
    outline: none;
}

.btn-complete {
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
}

.btn-complete:hover {
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

@media (max-width: 768px) {
    .password-container {
        flex-direction: column;
    }
    
    .password-left, .password-right {
        margin-right: 0;
        margin-bottom: 20px;
    }
}
</style>

<div class="password-container">
    <div class="password-left">
        <div class="logo-container">
            <img src="<?php echo base_url(); ?>public/img/toolsgrey.png" alt="Trazalog Tools">
        </div>
        
        <h1 class="password-title">Establecer Contraseña</h1>
        <p class="password-subtitle">Hola <strong><?php echo $firstName; ?></strong>. Tu usuario es <strong><?php echo $email; ?></strong>.<br>Por favor elige tu contraseña para comenzar a usar el sistema.</p>
        
        <?php 
        $fattr = array('class' => 'form-signin');
        echo form_open(site_url().'main/complete/token/'.$token, $fattr); 
        ?>
        
        <div class="form-group">
            <?php echo form_password(array(
                'name'=>'password', 
                'id'=> 'password', 
                'placeholder'=>'Contraseña', 
                'class'=>'form-control', 
                'value' => set_value('password')
            )); ?>
            <?php echo form_error('password') ?>
        </div>
        
        <div class="form-group">
            <?php echo form_password(array(
                'name'=>'passconf', 
                'id'=> 'passconf', 
                'placeholder'=>'Confirmar Contraseña', 
                'class'=>'form-control', 
                'value'=> set_value('passconf')
            )); ?>
            <?php echo form_error('passconf') ?>
        </div>
        
        <?php echo form_hidden('user_id', $user_id);?>
        <?php echo form_submit(array('value'=>'Completar Registro', 'class'=>'btn-complete')); ?>
        <?php echo form_close(); ?>
    </div>
    
    <div class="password-right">
        <div class="image-container">
            <img src="<?php echo base_url(); ?>public/img/toolschangepass.png" alt="Configurar Contraseña">
        </div>
    </div>
</div>

