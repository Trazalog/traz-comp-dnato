<?php
/**
 * Login web — Paso 1: credenciales.
 *
 * Layout split-screen: formulario a la izquierda, imagen a sangre a la derecha
 * con el banner de autoregistro encima. En pantallas chicas la imagen se oculta
 * y queda sólo el formulario.
 *
 * El selector de empresa ya no está acá: antes se llenaba con
 * Roles::getBpmGroups(), es decir listaba todas las empresas del sistema a
 * cualquiera que abriera el login sin sesión. Ahora la empresa se resuelve del
 * lado del servidor a partir de las membresías del usuario ya autenticado y,
 * si tiene más de una, el login sigue en main/seleccionar_empresa.
 *
 * Variables esperadas:
 *   $logoEmpresa       logo del sitio (core.tablas / configuraciones_ui)
 *   $copyright         'true' si va la línea de copyright
 *   $recaptcha         'yes' si hay que renderizar el widget
 *   $mostrar_registro  bool — banner freemium (constante LOGIN_MOSTRAR_REGISTRO)
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

$imagenLogin = defined('LOGIN_IMG_BACKGROUND') ? LOGIN_IMG_BACKGROUND : 'public/img/toolsregister.png';
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

    /* ---------- Panel del formulario ---------- */
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
        /* Sin justify-content: con overflow, el contenido que desborda se
           corta y no se puede alcanzar. El centrado lo hace margin:auto
           en .tz-login__inner, que sí convive con el scroll. */
        overflow-y: auto;
        padding: 48px 8% 32px 8%;
    }

    .tz-login__inner {
        width: 100%;
        max-width: 440px;
        margin: auto;
    }

    .tz-login__logo {
        max-width: 250px;
        height: auto;
        margin-bottom: 44px;
    }

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
    }

    /* ---------- Mensajes ---------- */
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

    /* ---------- Campos ---------- */
    .tz-field { margin-bottom: 22px; }

    .tz-field__label {
        display: block;
        font-size: 14.5px;
        font-weight: 600;
        color: #44546a;
        margin-bottom: 8px;
    }

    .tz-field__box {
        position: relative;
    }

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
        padding: 0 46px 0 46px;
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
    .tz-field__input::-ms-clear { display: none; }

    .tz-field__toggle {
        position: absolute;
        right: 6px;
        top: 50%;
        margin-top: -16px;
        width: 34px;
        height: 32px;
        border: 0;
        background: transparent;
        color: #a3b1c2;
        cursor: pointer;
        font-size: 15px;
        padding: 0;
    }
    .tz-field__toggle:hover { color: #3498db; }

    .tz-field .error, .tz-field__error {
        display: block;
        color: #c0392b;
        font-size: 12.5px;
        margin-top: 6px;
    }

    /* ---------- Botón ---------- */
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

    .tz-login__links {
        margin-top: 24px;
        font-size: 15.5px;
        text-align: center;
    }
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

    .tz-recaptcha { margin-bottom: 20px; text-align: center; }
    .tz-recaptcha > div { display: inline-block; }

    /* ---------- Panel visual ---------- */
    .tz-login__visual {
        -webkit-box-flex: 1;
        -ms-flex: 1 1 54%;
                flex: 1 1 54%;
        position: relative;
        background-color: #24303d;
        background-position: center;
        background-size: cover;
        background-repeat: no-repeat;
        display: -webkit-box;
        display: -ms-flexbox;
        display: flex;
        -webkit-box-align: end;
        -ms-flex-align: end;
                align-items: flex-end;
        padding: 44px;
    }
    /* Velo oscuro: da contraste al banner sin apagar la foto. */
    .tz-login__visual:after {
        content: "";
        position: absolute;
        left: 0; right: 0; bottom: 0; top: 0;
        background: -webkit-linear-gradient(top, rgba(15,25,35,0) 35%, rgba(15,25,35,0.78) 100%);
        background: linear-gradient(to bottom, rgba(15,25,35,0) 35%, rgba(15,25,35,0.78) 100%);
    }

    /* ---------- Banner freemium ---------- */
    .tz-promo {
        position: relative;
        z-index: 2;
        max-width: 430px;
        background: rgba(255, 255, 255, 0.97);
        border-radius: 14px;
        padding: 26px 28px;
        box-shadow: 0 18px 45px rgba(0, 0, 0, 0.3);
    }
    .tz-promo__kicker {
        display: inline-block;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 1.1px;
        text-transform: uppercase;
        color: #1d6f42;
        background: #eafaf1;
        border-radius: 20px;
        padding: 5px 12px;
        margin-bottom: 14px;
    }
    .tz-promo__title {
        font-size: 21px;
        font-weight: 700;
        color: #1b2733;
        margin: 0 0 10px 0;
        line-height: 1.3;
    }
    .tz-promo__text {
        font-size: 14.5px;
        color: #55677c;
        line-height: 1.55;
        margin: 0 0 18px 0;
    }
    .tz-promo__cta {
        display: inline-block;
        background: #27ae60;
        color: #ffffff !important;
        font-size: 15px;
        font-weight: 600;
        text-decoration: none !important;
        padding: 12px 22px;
        border-radius: 9px;
        -webkit-transition: background 0.15s ease, box-shadow 0.15s ease;
                transition: background 0.15s ease, box-shadow 0.15s ease;
    }
    .tz-promo__cta:hover {
        background: #219150;
        box-shadow: 0 8px 20px rgba(39, 174, 96, 0.3);
    }
    .tz-promo__nota {
        font-size: 12.5px;
        color: #8595a7;
        margin: 12px 0 0 0;
    }

    /* ---------- Responsive ---------- */
    @media (max-width: 991px) {
        .tz-login__visual { display: none; }
        .tz-login__form {
            -ms-flex: 0 0 100%;
                flex: 0 0 100%;
            max-width: 100%;
            padding: 40px 24px;
        }
        /* Sin panel visual, el banner acompaña al formulario. */
        .tz-promo--mobile { display: block; margin-top: 28px; max-width: 100%; box-shadow: none; border: 1.5px solid #e1e8ef; }
    }
    @media (min-width: 992px) {
        .tz-promo--mobile { display: none; }
    }
</style>

<div class="tz-login">

    <div class="tz-login__form">
        <div class="tz-login__inner">

            <img src="<?php echo base_url($logoEmpresa); ?>" alt="Trazalog Tools" class="tz-login__logo">

            <h1 class="tz-login__title">Bienvenido</h1>
            <p class="tz-login__subtitle">Ingresá a tu cuenta para continuar.</p>

            <?php if ($mensajeTexto !== ''): ?>
                <div class="tz-alert tz-alert--<?php echo $mensajeTipo; ?>">
                    <?php echo $mensajeTexto; ?>
                </div>
            <?php endif; ?>

            <?php echo form_open(base_url() . 'main/login/'); ?>

                <div class="tz-field">
                    <label class="tz-field__label" for="email">Correo electrónico</label>
                    <div class="tz-field__box">
                        <i class="fa fa-envelope-o tz-field__icon" aria-hidden="true"></i>
                        <input type="email"
                               name="email"
                               id="email"
                               class="tz-field__input"
                               placeholder="tu.correo@empresa.com"
                               autocomplete="username"
                               autofocus
                               value="<?php echo set_value('email'); ?>">
                    </div>
                    <?php echo form_error('email'); ?>
                </div>

                <div class="tz-field">
                    <label class="tz-field__label" for="password">Contraseña</label>
                    <div class="tz-field__box">
                        <i class="fa fa-lock tz-field__icon" aria-hidden="true"></i>
                        <input type="password"
                               name="password"
                               id="password"
                               class="tz-field__input"
                               placeholder="Tu contraseña"
                               autocomplete="current-password">
                        <button type="button"
                                class="tz-field__toggle"
                                id="tzVerPass"
                                title="Mostrar u ocultar la contraseña"
                                aria-label="Mostrar u ocultar la contraseña">
                            <i class="fa fa-eye" aria-hidden="true"></i>
                        </button>
                    </div>
                    <?php echo form_error('password'); ?>
                </div>

                <?php if ($recaptcha == 'yes'): ?>
                    <div class="tz-recaptcha"><?php echo $this->recaptcha->render(); ?></div>
                <?php endif; ?>

                <button type="submit" class="tz-btn">Ingresar</button>

            <?php echo form_close(); ?>

            <div class="tz-login__links">
                <a href="<?php echo base_url(); ?>main/forgot">¿Olvidaste tu contraseña?</a>
            </div>

            <?php if (!empty($mostrar_registro)): ?>
                <!-- Copia del banner para pantallas donde el panel visual no se muestra -->
                <div class="tz-promo tz-promo--mobile">
                    <span class="tz-promo__kicker">Plan freemium</span>
                    <h2 class="tz-promo__title">Empezá gratis, con todo incluido</h2>
                    <p class="tz-promo__text">
                        Creá la cuenta de tu empresa <strong>con 5 usuarios sin costo</strong>
                        y acceso a todas las funcionalidades de Trazalog.
                    </p>
                    <a href="<?php echo base_url(); ?>main/register" class="tz-promo__cta">Crear cuenta gratis</a>
                    <p class="tz-promo__nota">No pedimos tarjeta de crédito.</p>
                </div>
            <?php endif; ?>

            <div class="tz-login__foot">
                <?php if (isset($copyright) && $copyright == 'true'): ?>
                    Copyright &middot; <a href="http://trazalog.com/" target="_blank" rel="noopener">TRAZALOG</a>
                    &middot;
                <?php endif; ?>
                <span class="tz-login__version"><?php echo html_escape(ApplicationVersion::getVersion()); ?></span>
            </div>

        </div>
    </div>

    <div class="tz-login__visual" style="background-image: url('<?php echo base_url($imagenLogin); ?>');">
        <?php if (!empty($mostrar_registro)): ?>
            <div class="tz-promo">
                <span class="tz-promo__kicker">Plan freemium</span>
                <h2 class="tz-promo__title">Empezá gratis, con todo incluido</h2>
                <p class="tz-promo__text">
                    Creá la cuenta de tu empresa <strong>con 5 usuarios sin costo</strong>
                    y acceso a todas las funcionalidades de Trazalog. Dejá de seguir tu operación
                    con planillas.
                </p>
                <a href="<?php echo base_url(); ?>main/register" class="tz-promo__cta">Crear cuenta gratis</a>
                <p class="tz-promo__nota">No pedimos tarjeta de crédito.</p>
            </div>
        <?php endif; ?>
    </div>

</div>

<script type="text/javascript">
(function () {
    var boton = document.getElementById('tzVerPass');
    var campo = document.getElementById('password');
    if (!boton || !campo) { return; }
    boton.onclick = function () {
        var oculto = campo.type === 'password';
        campo.type = oculto ? 'text' : 'password';
        var icono = boton.getElementsByTagName('i')[0];
        if (icono) {
            icono.className = oculto ? 'fa fa-eye-slash' : 'fa fa-eye';
        }
        campo.focus();
    };
})();
</script>
</body>
</html>
