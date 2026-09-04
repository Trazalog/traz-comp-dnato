-- =============================================================================
-- DESPLIEGUE — Carga Masiva multi-motor · lado PostgreSQL
-- =============================================================================
--
-- OBJETIVO
--   Dejar la base PostgreSQL de Dnato de un ambiente lista para que la Carga
--   Masiva pueda despachar entidades contra MariaDB (AssetPlanner) además de
--   contra PostgreSQL. Es el script que se corre al desplegar en DEMO y en
--   PRODUCCIÓN.
--
--   NO cubre el lado de MariaDB: ver la sección "QUÉ FALTA DESPUÉS DE ESTO" al
--   final del archivo. Correr sólo este script NO alcanza para que una carga de
--   equipos funcione — pero sí hace que falle con un mensaje claro en vez de
--   fallar de forma confusa.
--
-- DÓNDE SE EJECUTA
--   Desde una terminal con acceso de red a la base (VPN levantada si hace
--   falta), contra la base PostgreSQL que contiene los esquemas `core` y `sta`
--   del ambiente que se está desplegando:
--
--     psql -h <host> -p <puerto> -U <usuario> -d <base> \
--          -f scripts/sql/despliegue/2026-09-carga-masiva-multimotor.sql
--
--   Si en vez de psql se usa un cliente gráfico (DBeaver, pgAdmin, DataGrip),
--   hay que OMITIR la línea "\set ON_ERROR_STOP on": es un meta-comando de psql
--   y esos clientes la rechazan. El script es igual de seguro sin ella, porque
--   todo va dentro de una transacción y un fallo la aborta entera.
--   Un ambiente por vez. NO correrlo contra dos bases en paralelo.
--
-- SEGURIDAD DE EJECUCIÓN
--   - Es IDEMPOTENTE: se puede correr más de una vez sin efecto adicional.
--   - Va todo dentro de UNA transacción. Si cualquier chequeo falla, no queda
--     nada aplicado a medias.
--   - Aborta con mensaje explícito si encuentra el ambiente en un estado que
--     haría inconsistente la migración (ver los tres pre-chequeos).
--
-- ORDEN RESPECTO DEL DEPLOY DE CÓDIGO
--   Se puede correr ANTES de desplegar el código: la columna `motor_bd` nace
--   con default 'postgresql', así que el código viejo sigue funcionando igual.
--   Correrlo antes es lo recomendado.
--
-- HISTORIAL / RELACIÓN CON OTROS SCRIPTS
--   Este script consolida y reemplaza, a los efectos del despliegue:
--     - traz-comp-dnato: scripts/sql/sta/2026-08-agregar-motor-bd.sql
--       (ya aplicado en DESARROLLO el 2026-08; se conserva como registro)
--     - traz-tools:      scripts/sql/2026-08-core-empresas-empr-id-mysql.sql
--       (misma migración, idempotente: si ya se corrió, acá es no-op)
--   Correr este script en un ambiente donde ya se corrió alguno de esos dos NO
--   rompe nada.
--
-- =============================================================================

\set ON_ERROR_STOP on

BEGIN;


-- -----------------------------------------------------------------------------
-- PRE-CHEQUEOS — si algo de esto falla, la transacción entera se revierte
-- -----------------------------------------------------------------------------
DO $precheck$
DECLARE
    v_dups   integer;
BEGIN
    -- 1) Que existan las tablas que vamos a tocar.
    IF to_regclass('sta.entidades_negocio') IS NULL THEN
        RAISE EXCEPTION
            'No existe sta.entidades_negocio en esta base. ¿Es la base correcta del ambiente?';
    END IF;

    IF to_regclass('core.empresas') IS NULL THEN
        RAISE EXCEPTION
            'No existe core.empresas en esta base. ¿Es la base correcta del ambiente?';
    END IF;

    -- 2) Duplicados de empr_id_mysql: harían fallar el índice único de más
    --    abajo. Se chequea ANTES para abortar con un mensaje entendible en vez
    --    de con el error crudo del índice.
    IF EXISTS (SELECT 1
                 FROM information_schema.columns
                WHERE table_schema = 'core'
                  AND table_name   = 'empresas'
                  AND column_name  = 'empr_id_mysql') THEN

        SELECT count(*) INTO v_dups
          FROM (SELECT empr_id_mysql
                  FROM core.empresas
                 WHERE empr_id_mysql IS NOT NULL
                 GROUP BY empr_id_mysql
                HAVING count(*) > 1) d;

        IF v_dups > 0 THEN
            RAISE EXCEPTION
                'Hay % valor(es) de empr_id_mysql repetidos en core.empresas. Un id de AssetPlanner no puede apuntar a dos empresas de Tools: hay que resolver el duplicado a mano antes de desplegar. Para verlos: SELECT empr_id_mysql, count(*) FROM core.empresas WHERE empr_id_mysql IS NOT NULL GROUP BY 1 HAVING count(*) > 1;',
                v_dups;
        END IF;
    END IF;

    RAISE NOTICE 'Pre-chequeos OK.';
END
$precheck$;


-- -----------------------------------------------------------------------------
-- 1) sta.entidades_negocio.motor_bd
--    Le dice al despachador de PHP contra qué base corre cada entidad. El
--    catálogo sigue viviendo SIEMPRE en PostgreSQL, aunque la carga real de
--    algunas entidades ocurra en MariaDB.
--
--    Nota de producción: en PostgreSQL 10 un ADD COLUMN con NOT NULL DEFAULT
--    reescribe la tabla (el camino rápido llegó en la 11). Acá es irrelevante
--    porque la tabla tiene un puñado de filas, pero conviene saberlo.
-- -----------------------------------------------------------------------------
ALTER TABLE sta.entidades_negocio
  ADD COLUMN IF NOT EXISTS motor_bd varchar(20) NOT NULL DEFAULT 'postgresql';

-- Sólo dos valores válidos. Se recrea para que el script sea repetible.
ALTER TABLE sta.entidades_negocio
  DROP CONSTRAINT IF EXISTS chk_entidades_negocio_motor_bd;

ALTER TABLE sta.entidades_negocio
  ADD CONSTRAINT chk_entidades_negocio_motor_bd
  CHECK (motor_bd IN ('postgresql', 'mariadb'));

COMMENT ON COLUMN sta.entidades_negocio.motor_bd IS
  'Motor donde corre el stored procedure de esta entidad: postgresql (por defecto) o mariadb (AssetPlanner). El catálogo siempre vive en PostgreSQL.';

-- Las entidades de AssetPlanner se marcan por NOMBRE explícito, no por el
-- prefijo del procedimiento: la convención "sin prefijo sta. = MariaDB" existe
-- y el código la usa como último recurso, pero no se apoya la migración en
-- ella. Si un ambiente no tiene alguna de estas entidades, el UPDATE
-- simplemente no la encuentra y no pasa nada.
UPDATE sta.entidades_negocio
   SET motor_bd = 'mariadb'
 WHERE nombre IN ('Mantenimiento Equipos', 'Mantenimiento Articulos')
   AND motor_bd <> 'mariadb';


-- -----------------------------------------------------------------------------
-- POST-CHEQUEO de coherencia entre motor y nombre del procedimiento
-- -----------------------------------------------------------------------------
DO $coherencia$
DECLARE
    v_lista text;
BEGIN
    -- FATAL: marcada como mariadb pero el SP tiene prefijo de PostgreSQL.
    -- Sería una entidad imposible de ejecutar: el despachador la mandaría a
    -- MariaDB a buscar un procedimiento que se llama "sta.algo".
    SELECT string_agg(nombre || ' -> ' || stored_procedure, '; ')
      INTO v_lista
      FROM sta.entidades_negocio
     WHERE motor_bd = 'mariadb'
       AND stored_procedure LIKE 'sta.%';

    IF v_lista IS NOT NULL THEN
        RAISE EXCEPTION
            'Entidad marcada como mariadb pero con procedimiento de PostgreSQL: %. Revisar el catálogo antes de desplegar.',
            v_lista;
    END IF;

    -- AVISO (no aborta): sin prefijo sta. y marcada postgresql. Es el síntoma
    -- exacto del bug que este cambio vino a arreglar — la entidad se despacha a
    -- PostgreSQL y devuelve "Stored procedure no encontrado". Puede ser
    -- legítimo si el ambiente tiene procedimientos de PostgreSQL sin prefijo,
    -- por eso avisa en vez de cortar.
    SELECT string_agg(nombre || ' -> ' || stored_procedure, '; ')
      INTO v_lista
      FROM sta.entidades_negocio
     WHERE motor_bd = 'postgresql'
       AND stored_procedure NOT LIKE 'sta.%';

    IF v_lista IS NOT NULL THEN
        RAISE NOTICE
            'REVISAR: entidad(es) sin prefijo sta. que quedaron como postgresql: %. Si en realidad corren en AssetPlanner, hay que marcarlas como mariadb a mano.',
            v_lista;
    END IF;
END
$coherencia$;


-- -----------------------------------------------------------------------------
-- 2) core.empresas.empr_id_mysql
--    Vínculo entre la empresa de Tools (PostgreSQL) y la misma empresa en la
--    base de AssetPlanner (MariaDB). Sin este valor cargado, la carga masiva
--    contra MariaDB ABORTA a propósito: usar el empr_id de Dnato escribiría en
--    los datos de otra empresa, porque las dos bases numeran distinto.
--
--    Esto crea la ESTRUCTURA. Cargar los VALORES es un trabajo de datos aparte,
--    y HOY ES MANUAL: verificado el 2026-09 que ningún código de traz-comp-dnato
--    ni de traz-tools invoca updateEmpresaAssetId, aunque el DataService y la
--    ruta PUT /empresa/asset-id de toolsCOREAPI existan. Por eso un ambiente
--    puede tener 0 empresas vinculadas aunque la registración haya corrido
--    muchas veces — ver "QUÉ FALTA DESPUÉS DE ESTO".
--
--    Espejo de traz-tools/scripts/sql/2026-08-core-empresas-empr-id-mysql.sql;
--    si ya se corrió aquel, esto es no-op.
-- -----------------------------------------------------------------------------
ALTER TABLE core.empresas
  ADD COLUMN IF NOT EXISTS empr_id_mysql integer;

COMMENT ON COLUMN core.empresas.empr_id_mysql IS
  'Id de la misma empresa en la base de AssetPlanner (MySQL/MariaDB). NULL = empresa sin contraparte en asset. HOY SE CARGA A MANO: existe el DataService updateEmpresaAssetId y la ruta PUT /empresa/asset-id en toolsCOREAPI, pero ningún código de traz-comp-dnato ni de traz-tools la invoca. Lectores: emisión de JWT (claim empr_id_mysql) y carga masiva contra MariaDB. Lookup inverso: COREDataService.getEmpresaByMysqlId.';

-- Unicidad parcial: un id de AssetPlanner no puede apuntar a dos empresas de
-- Tools. Parcial para permitir múltiples NULL (empresas sin vínculo).
--
-- Nota de producción: sin CONCURRENTLY, la creación toma un lock de escritura
-- sobre core.empresas. Es instantáneo con el volumen actual (decenas de filas).
-- CONCURRENTLY no se puede usar acá porque no corre dentro de una transacción.
CREATE UNIQUE INDEX IF NOT EXISTS empresas_empr_id_mysql_uk
  ON core.empresas (empr_id_mysql)
  WHERE empr_id_mysql IS NOT NULL;


COMMIT;


-- =============================================================================
-- VERIFICACIÓN POST-DESPLIEGUE
-- Se ejecutan solas al correr el script con psql -f. Leer las tres salidas.
-- =============================================================================

-- A) Catálogo: cada entidad con su motor. 'Mantenimiento Equipos' debe figurar
--    como mariadb.
SELECT nombre, stored_procedure, motor_bd
  FROM sta.entidades_negocio
 ORDER BY motor_bd, nombre;

-- B) Duplicados de empr_id_mysql: debe devolver 0 filas.
SELECT empr_id_mysql, count(*) AS veces
  FROM core.empresas
 WHERE empr_id_mysql IS NOT NULL
 GROUP BY empr_id_mysql
HAVING count(*) > 1;

-- C) Cuántas empresas tienen vínculo con AssetPlanner. Si vinculadas = 0,
--    NINGUNA empresa puede usar todavía la carga masiva contra MariaDB.
SELECT count(*) FILTER (WHERE empr_id_mysql IS NOT NULL) AS vinculadas,
       count(*)                                          AS total_activas
  FROM core.empresas
 WHERE eliminado = false;


-- =============================================================================
-- QUÉ FALTA DESPUÉS DE ESTO (fuera de este script)
-- =============================================================================
--
-- 1. CÓDIGO DESPLEGADO
--    El despacho multi-motor entró por el PR #34 de traz-comp-dnato. El tag
--    v2.5 es ANTERIOR. Si el ambiente corre v2.5 o algo previo, la carga sigue
--    devolviendo "Stored procedure no encontrado" aunque este script esté
--    aplicado.
--
-- 2. CONEXIÓN A LA MARIADB DEL AMBIENTE
--    application/config/database.php tiene el bloque $db['assetplanner'].
--    OJO: ese archivo SÍ está versionado en git (a diferencia de constants.php),
--    y en el repo apunta a la base de DESARROLLO. Hay que ajustar host, base,
--    usuario y contraseña a los del ambiente que se despliega, o la carga
--    escribe en la base equivocada.
--
-- 3. OBJETOS EN LA MARIADB DEL AMBIENTE
--    Tienen que existir la tabla de staging `sta_equipos` y el procedimiento
--    `bulkload_equipos(IN p_id_empresa INT)`. Además, las tablas maestras que
--    el procedimiento usa para traducir claves foráneas (sectores, tipos de
--    equipo, etc.) tienen que estar pobladas y ser coherentes.
--    ⚠️ Estos objetos NO están versionados en ningún repo: al 2026-09 sólo
--    existen instalados en la base de desarrollo.
--
-- 4. DATOS DE empr_id_mysql  ← es el que bloquea hoy en DEMO (0 de 26 empresas)
--    Este script crea la columna, no los valores. Y los valores NO se cargan
--    solos: no hay ningún llamador de updateEmpresaAssetId en traz-comp-dnato
--    ni en traz-tools, así que hay que poblarlos a mano hasta que ese hueco se
--    cierre. El emparejamiento se hace por nombre contra la tabla `empresas` de
--    la base de AssetPlanner (PK `id_empresa`, nombre en `descripcion`):
--
--      -- en PostgreSQL del ambiente
--      SELECT empr_id, nombre, descripcion FROM core.empresas
--       WHERE eliminado = false ORDER BY nombre;
--
--      -- en MariaDB del ambiente
--      SELECT id_empresa, descripcion FROM empresas ORDER BY descripcion;
--
--      -- y una vez emparejadas, por cada una:
--      UPDATE core.empresas SET empr_id_mysql = <id_empresa de AssetPlanner>
--       WHERE empr_id = <empr_id de Tools>;
--
--    El índice único parcial que crea este script impide asignar el mismo id de
--    AssetPlanner a dos empresas distintas.
--
--    Sin valores, la carga aborta con mensaje (no adivina un id, que sería
--    escribir en los datos de otra empresa).
--
-- 5. REDESPLIEGUE DEL CAR (opcional, no bloquea)
--    La query getEntidadesNegocio de COREDataService devuelve motor_bd desde el
--    PR #34. El modelo de PHP lee la tabla directamente, así que la carga
--    funciona igual sin redesplegar; el redespliegue sólo alinea lo que expone
--    el DataService.
--
-- =============================================================================
-- ROLLBACK (sólo si hay que volver atrás; deja el ambiente como antes)
-- =============================================================================
--
-- No hace falta revertir para volver al código viejo: con la columna presente
-- el código anterior al PR #34 funciona igual, la ignora. Revertir sólo si se
-- quiere borrar el rastro de la migración.
--
-- BEGIN;
--   ALTER TABLE sta.entidades_negocio
--     DROP CONSTRAINT IF EXISTS chk_entidades_negocio_motor_bd;
--   ALTER TABLE sta.entidades_negocio
--     DROP COLUMN IF EXISTS motor_bd;
-- COMMIT;
--
-- El índice y la columna empr_id_mysql NO se revierten: los usa la cadena de
-- identidad completa (ADR-009, emisión de JWT, registración freemium), no sólo
-- la carga masiva.
