<?php
/**
 * Login web — Paso 2: selección de empresa.
 *
 * Se muestra sólo cuando el usuario ya validó sus credenciales y pertenece a
 * más de una empresa. Cada empresa es un botón: una tarjeta con su logo y su
 * nombre. Si la empresa no tiene logo cargado, imageAdmin() devuelve un
 * placeholder genérico, así que la tarjeta nunca queda vacía.
 *
 * Variables esperadas:
 *   $empresas    array de objetos con empr_id, descripcion, image, imagepath
 *   $csrf_token  token de un solo uso generado en el Paso 1
 *   $logoEmpresa logo del sitio (core.tablas / configuraciones_ui)
 */
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div class="col-lg-8 col-lg-offset-2 col-md-10 col-md-offset-1">

  <style>
    .sel-empresa-head {
      text-align: center;
      margin-bottom: 28px;
    }
    .sel-empresa-head img {
      width: 260px;
      height: auto !important;
      margin: 24px 0 18px 0;
    }
    .sel-empresa-head h2 {
      margin-bottom: 6px;
      font-weight: 600;
    }
    .sel-empresa-head p {
      color: #7f8c8d;
      font-size: 1.05em;
      margin: 0;
    }

    .sel-empresa-grid {
      display: -webkit-box;
      display: -ms-flexbox;
      display: flex;
      -ms-flex-wrap: wrap;
          flex-wrap: wrap;
      -webkit-box-pack: center;
      -ms-flex-pack: center;
          justify-content: center;
      margin: 0 -10px;
    }

    .sel-empresa-item {
      width: 220px;
      margin: 10px;
    }

    /* El botón es la tarjeta entera: un <button> real, para que funcione con
       teclado y lectores de pantalla, no un div con onclick. */
    .sel-empresa-card {
      display: block;
      width: 100%;
      padding: 22px 16px 18px 16px;
      background: #ffffff;
      border: 2px solid #e4e8ec;
      border-radius: 10px;
      cursor: pointer;
      text-align: center;
      -webkit-transition: all 0.18s ease;
              transition: all 0.18s ease;
      box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06);
    }
    .sel-empresa-card:hover,
    .sel-empresa-card:focus {
      border-color: #3498db;
      box-shadow: 0 10px 24px rgba(52, 152, 219, 0.22);
      -webkit-transform: translateY(-4px);
              transform: translateY(-4px);
      outline: none;
    }
    .sel-empresa-card:active {
      -webkit-transform: translateY(-1px);
              transform: translateY(-1px);
    }

    .sel-empresa-logo {
      height: 88px;
      line-height: 88px;
      margin-bottom: 14px;
    }
    .sel-empresa-logo img {
      max-height: 88px;
      max-width: 100%;
      width: auto;
      vertical-align: middle;
    }

    .sel-empresa-nombre {
      font-size: 1.02em;
      font-weight: 600;
      color: #2c3e50;
      line-height: 1.3;
      word-wrap: break-word;
    }

    .sel-empresa-pie {
      text-align: center;
      margin-top: 30px;
    }

    @media (max-width: 480px) {
      .sel-empresa-item { width: 100%; }
    }
  </style>

  <div class="sel-empresa-head">
    <img src="<?php echo base_url($logoEmpresa); ?>" alt="Trazalog Tools">
    <h2>¿Con qué empresa querés ingresar?</h2>
    <p>Tu usuario pertenece a más de una. Hacé clic en una para continuar.</p>
  </div>

  <div class="sel-empresa-grid">
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
      <div class="sel-empresa-item">
        <?php
          /* Un formulario por tarjeta, con el empr_id en un hidden.
             No se usa <button name="empr_id" value="..."> porque las versiones
             viejas de IE envían el contenido del botón en lugar de su value. */
          echo form_open(base_url() . 'main/seleccionar_empresa');
        ?>
          <input type="hidden" name="login_csrf" value="<?php echo html_escape($csrf_token); ?>">
          <input type="hidden" name="empr_id" value="<?php echo (int) $empresa->empr_id; ?>">
          <button type="submit"
                  class="sel-empresa-card"
                  title="Ingresar a <?php echo html_escape($nombre); ?>">
            <span class="sel-empresa-logo">
              <img src="<?php echo $logo; ?>" alt="<?php echo html_escape($nombre); ?>">
            </span>
            <span class="sel-empresa-nombre"><?php echo html_escape($nombre); ?></span>
          </button>
        <?php echo form_close(); ?>
      </div>
    <?php endforeach; ?>
  </div>

  <div class="sel-empresa-pie">
    <a href="<?php echo base_url(); ?>main/logout">Ingresar con otro usuario</a>
  </div>

</div>
