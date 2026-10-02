<?php
/**
 * Confirmación "correo de recuperación enviado".
 *
 * Mismo layout split-screen que login/forgot/reset_password. El controller lo
 * renderiza self-contained (header + esta vista, sin container.php ni footer.php).
 *
 * Variables esperadas (opcionales):
 *   $logoEmpresa logo del sitio (core.tablas / configuraciones_ui)
 *   $copyright   'true' si va la línea de copyright
 */
defined('BASEPATH') OR exit('No direct script access allowed');

$imagenLogin = defined('LOGIN_IMG_BACKGROUND') ? LOGIN_IMG_BACKGROUND : 'public/img/toolslogin.jpg';
$logoSitio   = isset($logoEmpresa) ? $logoEmpresa : (defined('LOGIN_IMG_LOGO') ? LOGIN_IMG_LOGO : 'public/img/logotzl.png');
?>
<style>
    html, body { margin: 0; padding: 0; height: 100%; overflow-x: hidden; background: #ffffff; }

    .tz-login { display: -webkit-box; display: -ms-flexbox; display: flex; min-height: 100vh; width: 100%; }

    .tz-login__form {
        -webkit-box-flex: 0; -ms-flex: 0 0 46%; flex: 0 0 46%; max-width: 46%;
        background: #ffffff;
        display: -webkit-box; display: -ms-flexbox; display: flex;
        -webkit-box-orient: vertical; -webkit-box-direction: normal; -ms-flex-direction: column; flex-direction: column;
        overflow-y: auto; padding: 48px 8% 32px 8%;
    }
    .tz-login__inner { width: 100%; max-width: 440px; margin: auto; }
    .tz-login__logo { max-width: 250px; height: auto; margin-bottom: 44px; }

    .tz-login__icon {
        width: 74px; height: 74px; border-radius: 50%;
        background: #eafaf1; color: #27ae60;
        display: -webkit-box; display: -ms-flexbox; display: flex;
        -webkit-box-align: center; -ms-flex-align: center; align-items: center;
        -webkit-box-pack: center; -ms-flex-pack: center; justify-content: center;
        font-size: 34px; margin-bottom: 24px;
    }

    .tz-login__title { font-size: 32px; font-weight: 700; color: #1b2733; margin: 0 0 10px 0; letter-spacing: -0.5px; }
    .tz-login__subtitle { font-size: 17px; color: #6b7a8c; margin: 0 0 32px 0; line-height: 1.5; }

    .tz-btn {
        display: block; width: 100%; height: 56px; border: 0; border-radius: 9px;
        background: #3498db; color: #ffffff; font-size: 17.5px; font-weight: 600; cursor: pointer;
        text-align: center; line-height: 56px; text-decoration: none;
        -webkit-transition: background 0.15s ease, box-shadow 0.15s ease, -webkit-transform 0.15s ease;
                transition: background 0.15s ease, box-shadow 0.15s ease, transform 0.15s ease;
    }
    .tz-btn:hover { background: #2980b9; box-shadow: 0 8px 20px rgba(41, 128, 185, 0.28); -webkit-transform: translateY(-1px); transform: translateY(-1px); color: #fff; text-decoration: none; }

    .tz-login__foot { margin-top: 40px; font-size: 12px; color: #9aa8b8; text-align: center; }
    .tz-login__foot a { color: #9aa8b8; }
    .tz-login__version { white-space: nowrap; font-variant-numeric: tabular-nums; }

    .tz-login__visual {
        -webkit-box-flex: 1; -ms-flex: 1 1 54%; flex: 1 1 54%;
        position: relative; background-color: #24303d;
        background-position: center; background-size: cover; background-repeat: no-repeat;
    }
    .tz-login__visual:after {
        content: ""; position: absolute; left: 0; right: 0; bottom: 0; top: 0;
        background: -webkit-linear-gradient(top, rgba(15,25,35,0) 45%, rgba(15,25,35,0.55) 100%);
        background: linear-gradient(to bottom, rgba(15,25,35,0) 45%, rgba(15,25,35,0.55) 100%);
    }

    @media (max-width: 991px) {
        .tz-login__visual { display: none; }
        .tz-login__form { -ms-flex: 0 0 100%; flex: 0 0 100%; max-width: 100%; padding: 40px 24px; }
    }
</style>

<div class="tz-login">

    <div class="tz-login__form">
        <div class="tz-login__inner">

            <img src="<?php echo base_url($logoSitio); ?>" alt="Trazalog Tools" class="tz-login__logo">

            <div class="tz-login__icon"><i class="fa fa-envelope-o" aria-hidden="true"></i></div>

            <h1 class="tz-login__title">Correo enviado</h1>
            <p class="tz-login__subtitle">
                Revisá tu bandeja de entrada y seguí el enlace que te enviamos para restablecer tu contraseña.
                Si no lo ves, mirá en la carpeta de spam.
            </p>

            <a href="<?php echo site_url(); ?>main/login" class="tz-btn">Volver a iniciar sesión</a>

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
</body>
</html>
