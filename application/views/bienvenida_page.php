<style>
body {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    min-height: 100vh;
    margin: 0;
    font-family: 'Ubuntu', sans-serif;
}

.welcome-container {
    display: flex;
    min-height: 100vh;
    align-items: center;
    justify-content: center;
    padding: 20px;
}

.welcome-left {
    background: rgba(255, 255, 255, 0.95);
    padding: 40px;
    border-radius: 10px;
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
    width: 100%;
    max-width: 600px;
    margin-right: 20px;
}

.welcome-right {
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

.welcome-title {
    font-family: 'Ubuntu', sans-serif;
    color: #2c3e50;
    font-size: 2.2em;
    font-weight: bold;
    text-align: center;
    margin-bottom: 15px;
}

.welcome-subtitle {
    color: #7f8c8d;
    font-size: 1.1em;
    text-align: center;
    line-height: 1.4;
    margin-bottom: 30px;
}

.welcome-content {
    color: #2c3e50;
    line-height: 1.6;
    margin-bottom: 30px;
}

.welcome-content h3 {
    color: #3498db;
    margin-bottom: 20px;
    font-size: 1.3em;
}

.welcome-content ul {
    list-style: none;
    padding: 0;
}

.welcome-content li {
    padding: 8px 0;
    padding-left: 25px;
    position: relative;
}

.welcome-content li:before {
    content: "✓";
    color: #27ae60;
    font-weight: bold;
    position: absolute;
    left: 0;
}

.btn-login {
    background: linear-gradient(45deg, #27ae60, #2ecc71);
    color: white;
    border: none;
    padding: 15px 30px;
    border-radius: 8px;
    font-size: 16px;
    font-weight: bold;
    text-decoration: none;
    display: inline-block;
    transition: all 0.3s ease;
    margin-top: 20px;
}

.btn-login:hover {
    background: linear-gradient(45deg, #2ecc71, #27ae60);
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
    color: white;
    text-decoration: none;
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
    .welcome-container {
        flex-direction: column;
    }
    
    .welcome-left, .welcome-right {
        margin-right: 0;
        margin-bottom: 20px;
    }
}
</style>

<div class="welcome-container">
    <div class="welcome-left">
        <div class="logo-container">
            <img src="<?php echo base_url(); ?>public/img/toolsgrey.png" alt="Trazalog Tools">
        </div>
        
        <h1 class="welcome-title">¡Registro Completado Exitosamente!</h1>
        <h2 class="welcome-subtitle">¡Bienvenido a Trazalog Tools!</h2>
        
        <div class="welcome-content">
            <p>Tu registro ha sido completado exitosamente. Ahora puedes acceder a todas las funcionalidades de la plataforma gratuitamente.</p>
            
            <h3>¿Qué sigue ahora?</h3>
            <ul>
                <li>Inicia sesión con tus credenciales</li>
                <li>Explora las funcionalidades disponibles</li>
                <li>Configura tu perfil de usuario</li>
                <li>Comienza a usar la plataforma</li>
            </ul>
            
            <p>Se generaron los siguientes usuarios para que puedas utilizar Trazalog Tools:</p>
            
            <?php echo FREEMIUM_USERS; ?>
        </div>
        
        <div style="text-align: center;">
            <a href="<?php echo base_url(); ?>main/login" class="btn-login">Ir a Iniciar Sesión</a>
        </div>
    </div>
    
    <div class="welcome-right">
        <div class="image-container">
            <img src="<?php echo base_url(); ?>public/img/toolsbienvenida.png" alt="Bienvenida a Trazalog Tools">
        </div>
    </div>
</div>

