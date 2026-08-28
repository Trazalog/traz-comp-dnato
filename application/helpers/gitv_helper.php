<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Versión de la aplicación.
 *
 * Resuelve el tag de git en este orden, quedándose con lo primero que sirva:
 *
 *   1. Archivo VERSION en la raíz del proyecto. Es lo que hay que generar en el
 *      deploy de producción (`git describe --tags > VERSION`), porque ahí puede
 *      no existir el directorio .git.
 *   2. Lectura directa de .git en PHP puro — sin exec(). Da el tag exacto cuando
 *      HEAD está parado sobre uno, que es el caso de un despliegue.
 *   3. `git describe` por exec(), sólo para enriquecer en desarrollo (agrega la
 *      cantidad de commits desde el tag). Cacheado, ver CACHE_TTL.
 *   4. Las constantes MAJOR/MINOR/PATCH, como último recurso.
 *
 * Por qué no se usa exec() como camino principal: cuando Apache corre como otro
 * usuario, git rechaza el repositorio por "dubious ownership" y devuelve vacío;
 * además exec puede estar deshabilitado y cuesta un fork por request. La versión
 * anterior de este helper cortaba en la primera línea con
 * `php_sapi_name() !== 'cli'`, así que por web SIEMPRE devolvía la constante y
 * nunca el tag.
 */
class ApplicationVersion
{
    const MAJOR = 1;
    const MINOR = 5;
    const PATCH = 4;

    /** Segundos que se cachea el resultado de `git describe`. */
    const CACHE_TTL = 300;

    /** @var string|null memoria por request */
    private static $cache = null;

    /**
     * Versión a mostrar en pantalla.
     *
     * @return string p. ej. "v1.3.1.9", "v1.3.1.8-15-g69c027d" o "dev-69c027d"
     */
    public static function getVersion()
    {
        if (self::$cache !== null) {
            return self::$cache;
        }

        $version = self::desdeArchivoVersion();
        if ($version === '') {
            $version = self::desdeGit();
        }
        if ($version === '') {
            $version = self::MAJOR . '.' . self::MINOR . '.' . self::PATCH;
        }

        self::$cache = $version;
        return $version;
    }

    /**
     * Alias histórico (con el error de tipeo original). Lo usa footer.php.
     * Se mantiene para no romper las pantallas que ya lo llaman.
     *
     * @return string
     */
    public static function getVerision()
    {
        return self::getVersion();
    }

    // ------------------------------------------------------------------
    // Fuentes
    // ------------------------------------------------------------------

    /**
     * Archivo VERSION en la raíz. Una sola línea con el tag.
     *
     * @return string cadena vacía si no existe o está vacío
     */
    private static function desdeArchivoVersion()
    {
        $ruta = FCPATH . 'VERSION';
        if (!is_file($ruta) || !is_readable($ruta)) {
            return '';
        }
        $contenido = trim((string) @file_get_contents($ruta));
        return self::esVersionValida($contenido) ? $contenido : '';
    }

    /**
     * Lee .git. Primero PHP puro (tag exacto sobre HEAD); si HEAD no está
     * taggeado, intenta enriquecer con `git describe`.
     *
     * @return string cadena vacía si no hay repositorio legible
     */
    private static function desdeGit()
    {
        $gitDir = self::ubicarGitDir();
        if ($gitDir === '') {
            return '';
        }

        $sha = self::shaDeHead($gitDir);
        if ($sha === '') {
            return '';
        }

        // Tag exacto sobre HEAD: es el caso de un deploy sobre un tag.
        $tag = self::tagDeSha($gitDir, $sha);
        if ($tag !== '') {
            return $tag;
        }

        // HEAD entre tags (desarrollo): git describe agrega los commits de más.
        $describe = self::describeCacheado();
        if ($describe !== '') {
            return $describe;
        }

        return 'dev-' . substr($sha, 0, 7);
    }

    // ------------------------------------------------------------------
    // Lectura de .git en PHP puro
    // ------------------------------------------------------------------

    /**
     * @return string ruta al directorio .git, o cadena vacía
     */
    private static function ubicarGitDir()
    {
        $base = rtrim(FCPATH, DIRECTORY_SEPARATOR);
        for ($i = 0; $i < 4; $i++) {
            $candidato = $base . DIRECTORY_SEPARATOR . '.git';
            if (is_dir($candidato) && is_readable($candidato)) {
                return $candidato;
            }
            $padre = dirname($base);
            if ($padre === $base) {
                break;
            }
            $base = $padre;
        }
        return '';
    }

    /**
     * @param  string $gitDir
     * @return string SHA de HEAD, o cadena vacía
     */
    private static function shaDeHead($gitDir)
    {
        $head = @file_get_contents($gitDir . '/HEAD');
        if ($head === false) {
            return '';
        }
        $head = trim($head);

        // HEAD desacoplado: el SHA está directo en el archivo.
        if (strpos($head, 'ref:') !== 0) {
            return self::esSha($head) ? $head : '';
        }

        $ref     = trim(substr($head, 4));
        $archivo = $gitDir . '/' . $ref;
        if (is_file($archivo)) {
            $sha = trim((string) @file_get_contents($archivo));
            if (self::esSha($sha)) {
                return $sha;
            }
        }

        // La rama puede estar empaquetada en packed-refs.
        foreach (self::lineasPackedRefs($gitDir) as $linea) {
            $partes = explode(' ', $linea, 2);
            if (count($partes) === 2 && trim($partes[1]) === $ref && self::esSha($partes[0])) {
                return $partes[0];
            }
        }
        return '';
    }

    /**
     * Busca un tag que apunte exactamente a ese SHA.
     *
     * @param  string $gitDir
     * @param  string $sha
     * @return string nombre del tag, o cadena vacía
     */
    private static function tagDeSha($gitDir, $sha)
    {
        // 1. Tags sueltos en refs/tags
        $dirTags = $gitDir . '/refs/tags';
        if (is_dir($dirTags)) {
            $nombres = @scandir($dirTags);
            if (is_array($nombres)) {
                foreach ($nombres as $nombre) {
                    if ($nombre === '.' || $nombre === '..') {
                        continue;
                    }
                    $archivo = $dirTags . '/' . $nombre;
                    if (!is_file($archivo)) {
                        continue;
                    }
                    if (trim((string) @file_get_contents($archivo)) === $sha) {
                        return $nombre;
                    }
                }
            }
        }

        // 2. Tags empaquetados. Una línea '^<sha>' inmediatamente posterior
        //    indica el commit al que apunta un tag anotado.
        $lineas   = self::lineasPackedRefs($gitDir);
        $cantidad = count($lineas);
        for ($i = 0; $i < $cantidad; $i++) {
            $partes = explode(' ', $lineas[$i], 2);
            if (count($partes) !== 2) {
                continue;
            }
            $refSha = $partes[0];
            $ref    = trim($partes[1]);
            if (strpos($ref, 'refs/tags/') !== 0) {
                continue;
            }
            $nombre = substr($ref, strlen('refs/tags/'));

            // Tag anotado: la línea siguiente trae el commit real.
            if ($i + 1 < $cantidad && isset($lineas[$i + 1][0]) && $lineas[$i + 1][0] === '^') {
                $shaCommit = trim(substr($lineas[$i + 1], 1));
                if ($shaCommit === $sha) {
                    return $nombre;
                }
                continue;
            }
            if ($refSha === $sha) {
                return $nombre;
            }
        }
        return '';
    }

    /**
     * @param  string $gitDir
     * @return array líneas útiles de packed-refs (sin comentarios ni vacías)
     */
    private static function lineasPackedRefs($gitDir)
    {
        $archivo = $gitDir . '/packed-refs';
        if (!is_file($archivo) || !is_readable($archivo)) {
            return array();
        }
        $crudo = @file($archivo, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if (!is_array($crudo)) {
            return array();
        }
        $out = array();
        foreach ($crudo as $linea) {
            $linea = trim($linea);
            if ($linea === '' || $linea[0] === '#') {
                continue;
            }
            $out[] = $linea;
        }
        return $out;
    }

    // ------------------------------------------------------------------
    // git describe (sólo enriquece; puede no estar disponible)
    // ------------------------------------------------------------------

    /**
     * `git describe --tags` con caché en disco, para no pagar un fork por request.
     *
     * @return string cadena vacía si no se pudo obtener
     */
    private static function describeCacheado()
    {
        $cache = rtrim(sys_get_temp_dir(), DIRECTORY_SEPARATOR)
               . DIRECTORY_SEPARATOR . 'dnato_version_' . md5(FCPATH) . '.txt';

        if (is_file($cache) && (time() - (int) @filemtime($cache)) < self::CACHE_TTL) {
            $guardado = trim((string) @file_get_contents($cache));
            if (self::esVersionValida($guardado)) {
                return $guardado;
            }
        }

        if (!function_exists('exec')) {
            return '';
        }
        $deshabilitadas = array_map('trim', explode(',', (string) ini_get('disable_functions')));
        if (in_array('exec', $deshabilitadas, true)) {
            return '';
        }

        $salida = @exec('cd ' . escapeshellarg(FCPATH) . ' && git describe --tags 2>/dev/null');
        $salida = is_string($salida) ? trim($salida) : '';
        if (!self::esVersionValida($salida)) {
            return '';
        }

        @file_put_contents($cache, $salida);
        return $salida;
    }

    // ------------------------------------------------------------------
    // Utilidades
    // ------------------------------------------------------------------

    /**
     * Evita que llegue a pantalla cualquier cosa: sólo caracteres de versión.
     *
     * @param  string $valor
     * @return bool
     */
    private static function esVersionValida($valor)
    {
        return is_string($valor)
            && $valor !== ''
            && strlen($valor) <= 60
            && preg_match('/^[A-Za-z0-9._\-+]+$/', $valor) === 1;
    }

    /**
     * @param  string $valor
     * @return bool
     */
    private static function esSha($valor)
    {
        return is_string($valor) && preg_match('/^[0-9a-f]{40}$/i', $valor) === 1;
    }

    /**
     * Listado de tags para el modal de versiones. Se mantiene el contrato
     * anterior (JSON), pero ahora también funciona fuera de CLI.
     *
     * @return string JSON
     */
    public static function getLastVersions()
    {
        $gitDir = self::ubicarGitDir();
        if ($gitDir === '') {
            return json_encode(array(self::getVersion()));
        }

        $tags = array();

        $dirTags = $gitDir . '/refs/tags';
        if (is_dir($dirTags)) {
            $nombres = @scandir($dirTags);
            if (is_array($nombres)) {
                foreach ($nombres as $nombre) {
                    if ($nombre !== '.' && $nombre !== '..' && is_file($dirTags . '/' . $nombre)) {
                        $tags[] = $nombre;
                    }
                }
            }
        }

        foreach (self::lineasPackedRefs($gitDir) as $linea) {
            $partes = explode(' ', $linea, 2);
            if (count($partes) === 2 && strpos(trim($partes[1]), 'refs/tags/') === 0) {
                $tags[] = substr(trim($partes[1]), strlen('refs/tags/'));
            }
        }

        $tags = array_values(array_unique($tags));
        if (empty($tags)) {
            return json_encode(array(self::getVersion()));
        }

        usort($tags, array(__CLASS__, 'compararVersiones'));
        return json_encode(array_slice($tags, 0, 20));
    }

    /**
     * Orden natural descendente de versiones. Método propio en vez de closure
     * por compatibilidad con el estilo del resto del repo.
     *
     * @param  string $a
     * @param  string $b
     * @return int
     */
    public static function compararVersiones($a, $b)
    {
        return version_compare(ltrim($b, 'vV'), ltrim($a, 'vV'));
    }
}
