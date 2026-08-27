<?php
/**
 * tratar-imagen.php — aplica el tratamiento visual de las imágenes del flujo
 * de entrada (duotono petróleo/arena + contraste plano + grano).
 *
 * Es la versión automatizada de lo que en Photopea serían tres capas de ajuste.
 * La ventaja de hacerlo por script: las seis imágenes salen con EXACTAMENTE los
 * mismos valores, que es lo que hace que parezcan una sola sesión de fotos.
 *
 * USO (desde una terminal, parado en la raíz del repo):
 *
 *   php scripts/tratar-imagen.php <entrada> [salida]
 *
 * Ejemplos:
 *   php scripts/tratar-imagen.php foto.png
 *       → genera foto-tratada.png al lado del original
 *
 *   php scripts/tratar-imagen.php foto.webp public/img/toolslogin.png
 *       → escribe directamente el archivo final
 *
 *   for f in ~/fotos/*.png; do php scripts/tratar-imagen.php "$f"; done
 *       → trata una carpeta entera
 *
 * OPCIONES (variables de entorno, todas opcionales):
 *   FUERZA=70     intensidad del duotono, 0-100. Por debajo de 60 casi no se
 *                 nota; por encima de 85 empieza a verse como afiche. 70 es el
 *                 valor del documento y el que hay que usar en las seis.
 *   GRANO=6       cantidad de grano, 0-30. 0 lo desactiva.
 *   CONTRASTE=12  cuánto se aplana el contraste, 0-40.
 *
 * Ejemplo con opciones:
 *   FUERZA=80 GRANO=8 php scripts/tratar-imagen.php foto.png prueba-80.png
 *
 * No modifica el archivo de entrada nunca. Requiere PHP con la extensión GD.
 *
 * Ver doc/branding/imagenes-registracion.md §2 (el estilo) y §4 (el tratamiento).
 */

// --- Paleta del duotono (doc/branding/imagenes-registracion.md §4.1) --------
$SOMBRA = array(0x1b, 0x2a, 0x38); // #1b2a38 azul petróleo
$MEDIO  = array(0x6b, 0x7a, 0x80); // #6b7a80 gris azulado
$LUZ    = array(0xe9, 0xdf, 0xcd); // #e9dfcd arena cálido

// --- Parámetros ------------------------------------------------------------
$fuerza    = _leerEntorno('FUERZA',    70, 0, 100) / 100.0;
$grano     = _leerEntorno('GRANO',      6, 0,  30);
$contraste = _leerEntorno('CONTRASTE', 12, 0,  40) / 100.0;

// --- Argumentos ------------------------------------------------------------
if (!extension_loaded('gd')) {
    _salir('Falta la extensión GD de PHP. En Debian/Ubuntu: sudo apt install php-gd');
}
if ($argc < 2) {
    _salir("Uso: php scripts/tratar-imagen.php <entrada> [salida]\n"
         . "Ver el encabezado de este archivo para las opciones.");
}

$entrada = $argv[1];
if (!is_file($entrada) || !is_readable($entrada)) {
    _salir('No puedo leer el archivo: ' . $entrada);
}

if ($argc >= 3) {
    $salida = $argv[2];
} else {
    $dir    = dirname($entrada);
    $base   = pathinfo($entrada, PATHINFO_FILENAME);
    $salida = $dir . DIRECTORY_SEPARATOR . $base . '-tratada.png';
}

// --- Cargar ----------------------------------------------------------------
$img = _cargar($entrada);
if ($img === false) {
    _salir('Formato no reconocido o archivo dañado: ' . $entrada);
}

$ancho = imagesx($img);
$alto  = imagesy($img);
echo 'Entrada : ' . $entrada . ' (' . $ancho . 'x' . $alto . ")\n";
echo 'Ajustes : duotono ' . round($fuerza * 100) . '% · grano ' . $grano
   . ' · contraste -' . round($contraste * 100) . "%\n";

// --- Tabla de consulta: luminancia (0-255) -> color del degradado -----------
// Precalcularla evita interpolar en cada uno del millón de píxeles.
$lut = array();
for ($l = 0; $l < 256; $l++) {
    $t = $l / 255.0;
    if ($t <= 0.5) {
        $k = $t / 0.5;
        $lut[$l] = array(
            (int) round($SOMBRA[0] + ($MEDIO[0] - $SOMBRA[0]) * $k),
            (int) round($SOMBRA[1] + ($MEDIO[1] - $SOMBRA[1]) * $k),
            (int) round($SOMBRA[2] + ($MEDIO[2] - $SOMBRA[2]) * $k),
        );
    } else {
        $k = ($t - 0.5) / 0.5;
        $lut[$l] = array(
            (int) round($MEDIO[0] + ($LUZ[0] - $MEDIO[0]) * $k),
            (int) round($MEDIO[1] + ($LUZ[1] - $MEDIO[1]) * $k),
            (int) round($MEDIO[2] + ($LUZ[2] - $MEDIO[2]) * $k),
        );
    }
}

// Curva de contraste: acerca los extremos al centro. Una foto real rara vez
// tiene negro puro ni blanco puro; los generadores sí los producen.
$curva = array();
$piso  = (int) round(255 * $contraste * 0.45);
$techo = 255 - (int) round(255 * $contraste * 0.35);
for ($v = 0; $v < 256; $v++) {
    $curva[$v] = (int) round($piso + ($techo - $piso) * ($v / 255.0));
}

// --- Procesar --------------------------------------------------------------
$destino = imagecreatetruecolor($ancho, $alto);
imagealphablending($destino, false);
imagesavealpha($destino, false);

$inicio = microtime(true);
for ($y = 0; $y < $alto; $y++) {
    for ($x = 0; $x < $ancho; $x++) {
        $rgb = imagecolorat($img, $x, $y);
        $r   = ($rgb >> 16) & 0xFF;
        $g   = ($rgb >> 8) & 0xFF;
        $b   = $rgb & 0xFF;

        // 1. Luminancia percibida (coeficientes ITU-R BT.601)
        $lum = (int) (0.299 * $r + 0.587 * $g + 0.114 * $b);
        if ($lum > 255) { $lum = 255; }

        // 2. Mapa de degradado, mezclado con el original. La mezcla parcial es
        //    lo que deja sobrevivir el naranja del EPP y mantiene la foto
        //    leyéndose como foto y no como afiche a dos tintas.
        $d  = $lut[$lum];
        $nr = (int) ($r * (1 - $fuerza) + $d[0] * $fuerza);
        $ng = (int) ($g * (1 - $fuerza) + $d[1] * $fuerza);
        $nb = (int) ($b * (1 - $fuerza) + $d[2] * $fuerza);

        // 3. Contraste plano
        $nr = $curva[$nr];
        $ng = $curva[$ng];
        $nb = $curva[$nb];

        // 4. Grano monocromático: el mismo desplazamiento en los tres canales,
        //    como el ruido de una película, no como ruido de color.
        if ($grano > 0) {
            $n   = mt_rand(-$grano, $grano);
            $nr += $n;
            $ng += $n;
            $nb += $n;
            if ($nr < 0) { $nr = 0; } elseif ($nr > 255) { $nr = 255; }
            if ($ng < 0) { $ng = 0; } elseif ($ng > 255) { $ng = 255; }
            if ($nb < 0) { $nb = 0; } elseif ($nb > 255) { $nb = 255; }
        }

        imagesetpixel($destino, $x, $y, ($nr << 16) | ($ng << 8) | $nb);
    }
}

// --- Guardar ---------------------------------------------------------------
$dirSalida = dirname($salida);
if (!is_dir($dirSalida)) {
    _salir('No existe el directorio de salida: ' . $dirSalida);
}
if (!imagepng($destino, $salida, 9)) {
    _salir('No pude escribir: ' . $salida);
}

imagedestroy($img);
imagedestroy($destino);

$segundos = round(microtime(true) - $inicio, 1);
$peso     = round(filesize($salida) / 1024);
echo 'Salida  : ' . $salida . ' (' . $peso . ' KB, ' . $segundos . " s)\n";
if ($peso > 400) {
    echo "Aviso   : pasa los 400 KB. Comprimila en squoosh.app antes de subirla.\n";
}
echo "Listo.\n";
exit(0);

// ---------------------------------------------------------------------------
// Auxiliares
// ---------------------------------------------------------------------------

/**
 * Abre PNG, JPEG, WebP o GIF según lo que sea el archivo realmente,
 * sin confiar en la extensión.
 *
 * @param  string $ruta
 * @return resource|false
 */
function _cargar($ruta)
{
    $info = @getimagesize($ruta);
    if ($info === false) {
        return false;
    }
    switch ($info[2]) {
        case IMAGETYPE_PNG:
            return @imagecreatefrompng($ruta);
        case IMAGETYPE_JPEG:
            return @imagecreatefromjpeg($ruta);
        case IMAGETYPE_GIF:
            return @imagecreatefromgif($ruta);
        default:
            if (defined('IMAGETYPE_WEBP') && $info[2] === IMAGETYPE_WEBP
                && function_exists('imagecreatefromwebp')) {
                return @imagecreatefromwebp($ruta);
            }
            return false;
    }
}

/**
 * @param  string $nombre
 * @param  int    $defecto
 * @param  int    $min
 * @param  int    $max
 * @return int
 */
function _leerEntorno($nombre, $defecto, $min, $max)
{
    $valor = getenv($nombre);
    if ($valor === false || $valor === '' || !is_numeric($valor)) {
        return $defecto;
    }
    $valor = (int) $valor;
    if ($valor < $min) { return $min; }
    if ($valor > $max) { return $max; }
    return $valor;
}

/**
 * @param string $mensaje
 */
function _salir($mensaje)
{
    fwrite(STDERR, $mensaje . "\n");
    exit(1);
}
