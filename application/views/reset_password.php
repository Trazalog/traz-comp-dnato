<?php
/**
 * Restablecer contraseña (ingresar nueva clave recuperada).
 *
 * Mismo layout split-screen que el login (views/login.php) y recuperar (views/forgot.php):
 * formulario a la izquierda, imagen a sangre a la derecha. El controller lo renderiza
 * self-contained (header + esta vista, sin container.php ni footer.php).
 *
 * Variables esperadas:
 *   $firstName   nombre del usuario (para el saludo)
 *   $token       token de reseteo (va en la URL del form)
 *   $logoEmpresa logo del sitio (core.tablas / configuraciones_ui)
 *   $copyright   'true' si va la línea de copyright
 */
defined('BASEPATH') OR exit('No direct script access allowed');

$flash        = $this->session->flashdata();
$mensajeTipo  = '';
$mensajeTexto = '';
if (!empty($flash['danger_message'])) {
    $mensajeTipo  = 'danger';
    $mensajeTexto = $flash['danger_message'];
} elseif (!empty($flash['flash_message'])) {
    $mensajeTipo  = 'warning';
    $mensajeTexto = $flash['flash_message'];
} elseif (!empty($flash['success_message'])) {
    $mensajeTipo  = 'success';
    $mensajeTexto = $flash['success_message'];
}

$imagenLogin = defined('LOGIN_IMG_BACKGROUND') ? LOGIN_IMG_BACKGROUND : 'public/img/toolslogin.jpg';
$logoSitio   = isset($logoEmpresa) ? $logoEmpresa : (defined('LOGIN_IMG_LOGO') ? LOGIN_IMG_LOGO : 'public/img/logotzl.png');
?>
<style>
    html, body {
        margin: 0;
        padding: 0;
        height: 100%;
        overflow-x: hidden;
        background: #ffffff;
    }

    .tz-login {
        display: -webkit-box;
        display: -ms-flexbox;
        display: flex;
        min-height: 100vh;
        width: 100%;
    }

    .tz-login__form {
        -webkit-box-flex: 0;
        -ms-flex: 0 0 46%;
                flex: 0 0 46%;
        max-width: 46%;
        background: #ffffff;
        display: -webkit-box;
        display: -ms-flexbox;
        display: flex;
        -webkit-box-orient: vertical;
        -webkit-box-direction: normal;
        -ms-flex-direction: column;
                flex-direction: column;
        overflow-y: auto;
        padding: 48px 8% 32px 8%;
    }

    .tz-login__inner { width: 100%; max-width: 440px; margin: auto; }

    .tz-login__logo { max-width: 250px; height: auto; margin-bottom: 44px; }

    .tz-login__title {
        font-size: 36px;
        font-weight: 700;
        color: #1b2733;
        margin: 0 0 10px 0;
        letter-spacing: -0.5px;
    }

    .tz-login__subtitle {
        font-size: 17px;
        color: #6b7a8c;
        margin: 0 0 32px 0;
        line-height: 1.5;
    }
    .tz-login__subtitle strong { color: #1b2733; }

    .tz-alert {
        border-radius: 8px;
        padding: 13px 16px;
        margin-bottom: 22px;
        font-size: 14px;
        line-height: 1.45;
        border-left: 4px solid transparent;
    }
    .tz-alert--danger  { background: #fdecea; color: #922b21; border-left-color: #e74c3c; }
    .tz-alert--warning { background: #fef5e7; color: #8a6116; border-left-color: #f39c12; }
    .tz-alert--success { background: #eafaf1; color: #1d6f42; border-left-color: #27ae60; }

    .tz-field { margin-bottom: 22px; }

    .tz-field__label {
        display: block;
        font-size: 14.5px;
        font-weight: 600;
        color: #44546a;
        margin-bottom: 8px;
    }

    .tz-field__box { position: relative; }

    .tz-field__icon {
        position: absolute;
        left: 17px;
        top: 50%;
        margin-top: -9px;
        color: #a3b1c2;
        font-size: 17px;
        line-height: 1;
    }

    .tz-field__input {
        width: 100%;
        height: 54px;
        padding: 0 18px 0 46px;
        font-size: 16.5px;
        color: #1b2733;
        background: #f7f9fb;
        border: 1.5px solid #e1e8ef;
        border-radius: 9px;
        -webkit-transition: border-color 0.15s ease, background 0.15s ease, box-shadow 0.15s ease;
                transition: border-color 0.15s ease, background 0.15s ease, box-shadow 0.15s ease;
    }
    .tz-field__input:focus {
        outline: none;
        background: #ffffff;
        border-color: #3498db;
        box-shadow: 0 0 0 4px rgba(52, 152, 219, 0.13);
    }

    .tz-field .error, .tz-field__error {
        display: block;
        color: #c0392b;
        font-size: 12.5px;
        margin-top: 6px;
    }

    .tz-btn {
        display: block;
        width: 100%;
        height: 56px;
        border: 0;
        border-radius: 9px;
        background: #3498db;
        color: #ffffff;
        font-size: 17.5px;
        font-weight: 600;
        cursor: pointer;
        -webkit-transition: background 0.15s ease, box-shadow 0.15s ease, -webkit-transform 0.15s ease;
                transition: background 0.15s ease, box-shadow 0.15s ease, transform 0.15s ease;
    }
    .tz-btn:hover {
        background: #2980b9;
        box-shadow: 0 8px 20px rgba(41, 128, 185, 0.28);
        -webkit-transform: translateY(-1px);
                transform: translateY(-1px);
    }
    .tz-btn:active { -webkit-transform: translateY(0); transform: translateY(0); }

    .tz-login__links { margin-top: 24px; font-size: 15.5px; text-align: center; }
    .tz-login__links a { color: #3498db; text-decoration: none; }
    .tz-login__links a:hover { text-decoration: underline; }

    .tz-login__foot {
        margin-top: 40px;
        font-size: 12px;
        color: #9aa8b8;
        text-align: center;
    }
    .tz-login__foot a { color: #9aa8b8; }

    .tz-login__version {
        white-space: nowrap;
        font-variant-numeric: tabular-nums;
    }

    .tz-login__visual {
        -webkit-box-flex: 1;
        -ms-flex: 1 1 54%;
                flex: 1 1 54%;
        position: relative;
        background-color: #24303d;
        background-position: center;
        background-size: cover;
        background-repeat: no-repeat;
    }
    .tz-login__visual:after {
        content: "";
        position: absolute;
        left: 0; right: 0; bottom: 0; top: 0;
        background: -webkit-linear-gradient(top, rgba(15,25,35,0) 45%, rgba(15,25,35,0.55) 100%);
        background: linear-gradient(to bottom, rgba(15,25,35,0) 45%, rgba(15,25,35,0.55) 100%);
    }

    @media (max-width: 991px) {
        .tz-login__visual { display: none; }
        .tz-login__form {
            -ms-flex: 0 0 100%;
                flex: 0 0 100%;
            max-width: 100%;
            padding: 40px 24px;
        }
    }
</style>

<div class="tz-login">

    <div class="tz-login__form">
        <div class="tz-login__inner">

            <img src="<?php echo base_url($logoSitio); ?>" alt="Trazalog Tools" class="tz-login__logo">

            <h1 class="tz-login__title">Restablecer contraseña</h1>
            <p class="tz-login__subtitle">
                Hola <strong><?php echo html_escape($firstName); ?></strong>, ingresá tu nueva contraseña dos veces para restablecerla.
            </p>

            <?php if ($mensajeTexto !== ''): ?>
                <div class="tz-alert tz-alert--<?php echo $mensajeTipo; ?>">
                    <?php echo $mensajeTexto; ?>
                </div>
            <?php endif; ?>

            <?php echo form_open(site_url() . 'main/reset_password/token/' . $token); ?>

                <div class="tz-field">
                    <label class="tz-field__label" for="password">Nueva contraseña</label>
                    <div class="tz-field__box">
                        <i class="fa fa-lock tz-field__icon" aria-hidden="true"></i>
                        <input type="password"
                               name="password"
                               id="password"
                               class="tz-field__input js-password-strength"
                               placeholder="Mínimo 10 caracteres"
                               autocomplete="new-password"
                               minlength="10"
                               required
                               data-ps-confirm-target="passconf"
                               autofocus
                               value="<?php echo set_value('password'); ?>">
                    </div>
                    <?php echo form_error('password'); ?>
                </div>

                <div class="tz-field">
                    <label class="tz-field__label" for="passconf">Confirmar nueva contraseña</label>
                    <div class="tz-field__box">
                        <i class="fa fa-lock tz-field__icon" aria-hidden="true"></i>
                        <input type="password"
                               name="passconf"
                               id="passconf"
                               class="tz-field__input"
                               placeholder="Repetí la contraseña"
                               autocomplete="new-password"
                               minlength="10"
                               required
                               value="<?php echo set_value('passconf'); ?>">
                    </div>
                    <?php echo form_error('passconf'); ?>
                </div>

                <button type="submit" class="tz-btn">Restablecer contraseña</button>

            <?php echo form_close(); ?>

            <div class="tz-login__links">
                <a href="<?php echo site_url(); ?>main/login">Volver a iniciar sesión</a>
            </div>

            <div class="tz-login__foot">
                <?php if (isset($copyright) && $copyright == 'true'): ?>
                    Copyright &middot; <a href="http://trazalog.com/" target="_blank" rel="noopener">TRAZALOG</a>
                    &middot;
                <?php endif; ?>
                <span class="tz-login__version"><?php echo html_escape(ApplicationVersion::getVersion()); ?></span>
            </div>

        </div>
    </div>

    <div class="tz-login__visual" style="background-image: url('<?php echo base_url($imagenLogin); ?>');"></div>

</div>

<link rel="stylesheet" href="<?php echo base_url(); ?>public/css/password-strength.css">
<script src="<?php echo base_url(); ?>public/js/password-strength.js"></script>
</body>
</html>
