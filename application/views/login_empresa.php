<?php
/**
 * Login web — Paso 2: selección de empresa.
 *
 * Se muestra sólo cuando el usuario ya validó sus credenciales y pertenece a
 * más de una empresa. Cada empresa es un botón: una tarjeta con su logo y su
 * nombre. Si la empresa no tiene logo cargado, imageAdmin() devuelve un
 * placeholder genérico, así que la tarjeta nunca queda vacía.
 *
 * Comparte el lenguaje visual del login (misma imagen de fondo, misma
 * tipografía y mismos colores) para que las dos pantallas se lean como una
 * sola secuencia de entrada.
 *
 * Variables esperadas:
 *   $empresas    array de objetos con empr_id, descripcion, nombre, image, imagepath
 *   $csrf_token  token generado en el Paso 1
 *   $logoEmpresa logo del sitio (core.tablas / configuraciones_ui)
 */
defined('BASEPATH') OR exit('No direct script access allowed');

$flash        = $this->session->flashdata();
$mensajeTexto = '';
if (!empty($flash['danger_message'])) {
    $mensajeTexto = $flash['danger_message'];
} elseif (!empty($flash['flash_message'])) {
    $mensajeTexto = $flash['flash_message'];
}

$imagenFondo = defined('LOGIN_IMG_BACKGROUND') ? LOGIN_IMG_BACKGROUND : 'public/img/toolsregister.png';
?>
<style>
    html, body {
        margin: 0;
        padding: 0;
        height: 100%;
        overflow-x: hidden;
    }

    .tz-sel {
        min-height: 100vh;
        width: 100%;
        background-color: #24303d;
        background-image:
            -webkit-linear-gradient(rgba(15,25,35,0.82), rgba(15,25,35,0.88)),
            url('<?php echo base_url($imagenFondo); ?>');
        background-image:
            linear-gradient(rgba(15,25,35,0.82), rgba(15,25,35,0.88)),
            url('<?php echo base_url($imagenFondo); ?>');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        display: -webkit-box;
        display: -ms-flexbox;
        display: flex;
        -webkit-box-align: center;
        -ms-flex-align: center;
                align-items: center;
        -webkit-box-pack: center;
        -ms-flex-pack: center;
                justify-content: center;
        padding: 40px 20px;
    }

    .tz-sel__card {
        width: 100%;
        max-width: 780px;
        background: #ffffff;
        border-radius: 16px;
        box-shadow: 0 24px 60px rgba(0, 0, 0, 0.35);
        padding: 44px 40px 36px 40px;
    }

    .tz-sel__head {
        text-align: center;
        margin-bottom: 34px;
    }
    .tz-sel__logo {
        max-width: 180px;
        height: auto;
        margin-bottom: 26px;
    }
    .tz-sel__title {
        font-size: 26px;
        font-weight: 700;
        color: #1b2733;
        margin: 0 0 8px 0;
        letter-spacing: -0.3px;
    }
    .tz-sel__subtitle {
        font-size: 15px;
        color: #6b7a8c;
        margin: 0;
    }

    .tz-sel__alert {
        background: #fef5e7;
        color: #8a6116;
        border-left: 4px solid #f39c12;
        border-radius: 8px;
        padding: 13px 16px;
        margin-bottom: 24px;
        font-size: 14px;
    }

    .tz-sel__grid {
        display: -webkit-box;
        display: -ms-flexbox;
        display: flex;
        -ms-flex-wrap: wrap;
            flex-wrap: wrap;
        -webkit-box-pack: center;
        -ms-flex-pack: center;
                justify-content: center;
        margin: 0 -9px;
    }

    .tz-sel__item {
        width: 210px;
        margin: 9px;
    }
    .tz-sel__item form { margin: 0; }

    /* El botón ES la tarjeta: un <button> real, para que funcione con teclado
       y lectores de pantalla, no un div con onclick. */
    .tz-sel__card-btn {
        display: block;
        width: 100%;
        padding: 24px 16px 20px 16px;
        background: #ffffff;
        border: 2px solid #e4e9ef;
        border-radius: 12px;
        cursor: pointer;
        text-align: center;
        -webkit-transition: all 0.18s ease;
                transition: all 0.18s ease;
    }
    .tz-sel__card-btn:hover,
    .tz-sel__card-btn:focus {
        border-color: #3498db;
        background: #fbfdff;
        box-shadow: 0 12px 26px rgba(52, 152, 219, 0.22);
        -webkit-transform: translateY(-4px);
                transform: translateY(-4px);
        outline: none;
    }
    .tz-sel__card-btn:active {
        -webkit-transform: translateY(-1px);
                transform: translateY(-1px);
    }

    .tz-sel__logo-box {
        height: 84px;
        line-height: 84px;
        margin-bottom: 14px;
    }
    .tz-sel__logo-box img {
        max-height: 84px;
        max-width: 100%;
        width: auto;
        vertical-align: middle;
    }

    .tz-sel__nombre {
        display: block;
        font-size: 15px;
        font-weight: 600;
        color: #2c3e50;
        line-height: 1.3;
        word-wrap: break-word;
    }

    .tz-sel__foot {
        text-align: center;
        margin-top: 30px;
        font-size: 14px;
    }
    .tz-sel__foot a { color: #3498db; text-decoration: none; }
    .tz-sel__foot a:hover { text-decoration: underline; }

    @media (max-width: 560px) {
        .tz-sel__card { padding: 32px 20px 26px 20px; }
        .tz-sel__item { width: 100%; }
    }
</style>

<div class="tz-sel">
    <div class="tz-sel__card">

        <div class="tz-sel__head">
            <img src="<?php echo base_url($logoEmpresa); ?>" alt="Trazalog Tools" class="tz-sel__logo">
            <h1 class="tz-sel__title">¿Con qué empresa querés ingresar?</h1>
            <p class="tz-sel__subtitle">Tu usuario pertenece a más de una. Hacé clic en una para continuar.</p>
        </div>

        <?php if ($mensajeTexto !== ''): ?>
            <div class="tz-sel__alert"><?php echo $mensajeTexto; ?></div>
        <?php endif; ?>

        <div class="tz-sel__grid">
            <?php foreach ($empresas as $empresa): ?>
                <?php
                  $nombre = isset($empresa->descripcion) && trim((string) $empresa->descripcion) !== ''
                      ? $empresa->descripcion
                      : $empresa->nombre;
                  $logo = imageAdmin(
                      isset($empresa->image) ? $empresa->image : null,
                      isset($empresa->imagepath) ? $empresa->imagepath : null
                  );
                ?>
                <div class="tz-sel__item">
                    <?php
                      /* Un formulario por tarjeta, con el empr_id en un hidden.
                         No se usa <button name="empr_id" value="..."> porque las
                         versiones viejas de IE envían el contenido del botón en
                         lugar de su value. */
                      echo form_open(base_url() . 'main/seleccionar_empresa');
                    ?>
                        <input type="hidden" name="login_csrf" value="<?php echo html_escape($csrf_token); ?>">
                        <input type="hidden" name="empr_id" value="<?php echo (int) $empresa->empr_id; ?>">
                        <button type="submit"
                                class="tz-sel__card-btn"
                                title="Ingresar a <?php echo html_escape($nombre); ?>">
                            <span class="tz-sel__logo-box">
                                <img src="<?php echo $logo; ?>" alt="<?php echo html_escape($nombre); ?>">
                            </span>
                            <span class="tz-sel__nombre"><?php echo html_escape($nombre); ?></span>
                        </button>
                    <?php echo form_close(); ?>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="tz-sel__foot">
            <a href="<?php echo base_url(); ?>main/logout">Ingresar con otro usuario</a>
        </div>

    </div>
</div>
