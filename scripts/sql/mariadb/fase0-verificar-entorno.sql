-- =============================================================================
-- Carga Masiva multi-motor — FASE 0: verificación del entorno MariaDB
-- =============================================================================
--
-- OBJETIVO
--   Antes de escribir una línea de integración, confirmar contra el servidor
--   real qué hay y qué falta. El plan asume que los procedimientos de carga
--   masiva de Equipos y Sectores YA EXISTEN en `assetv2`; esto lo verifica, y
--   además documenta su firma y su formato de salida, que es lo que hay que
--   respetar del lado de PHP.
--
--   Es de SOLO LECTURA: no modifica nada.
--
-- DÓNDE SE EJECUTA
--   Contra la MariaDB de AssetPlanner, con la VPN levantada:
--
--     mysql -h 10.142.0.13 -P 3306 -u rootremote -p assetv2 \
--           < scripts/sql/mariadb/fase0-verificar-entorno.sql
--
--   (la contraseña es la del datasource AssetPlannerDataSource de WSO2)
--
-- QUÉ MIRAR EN EL RESULTADO
--   1. Versión — el diseño apunta a 10.1.44. Si es 10.2+, se pueden usar cosas
--      que el plan descartó; si es anterior, hay que revisar.
--   2. secure_file_priv — si devuelve una ruta, LOAD DATA INFILE sólo puede
--      leer de ahí, y el CSV de staging tendría que escribirse en ese lugar.
--      Si está vacío, no hay restricción. Es el riesgo #4 del plan.
--   3. Los procedimientos: si no aparecen, el trabajo cambia de "integrar" a
--      "crear", que es bastante más.
--   4. La firma de cada uno: cuántos parámetros, de qué tipo, y si hay un OUT.
--   5. Si existe un dispatcher `ejecutar_carga_masiva` equivalente al de
--      PostgreSQL, o si hay que llamar a cada SP por nombre.
-- =============================================================================

SELECT '=== 1. Versión del servidor ===' AS seccion;
SELECT VERSION() AS version_mariadb, @@version_comment AS comentario;

SELECT '=== 2. Permisos de archivo (LOAD DATA INFILE) ===' AS seccion;
SHOW VARIABLES LIKE 'secure_file_priv';
SHOW VARIABLES LIKE 'local_infile';
SELECT CURRENT_USER() AS usuario_conectado, DATABASE() AS base_actual;

SELECT '=== 3. Procedimientos de carga masiva existentes ===' AS seccion;
SELECT ROUTINE_NAME, ROUTINE_TYPE, CREATED, LAST_ALTERED, DEFINER
  FROM information_schema.ROUTINES
 WHERE ROUTINE_SCHEMA = 'assetv2'
   AND (ROUTINE_NAME LIKE '%bulkload%' OR ROUTINE_NAME LIKE '%carga_masiva%')
 ORDER BY ROUTINE_NAME;

SELECT '=== 4. Firma de cada procedimiento ===' AS seccion;
SELECT SPECIFIC_NAME AS procedimiento, ORDINAL_POSITION AS pos, PARAMETER_MODE AS modo,
       PARAMETER_NAME AS parametro, DTD_IDENTIFIER AS tipo
  FROM information_schema.PARAMETERS
 WHERE SPECIFIC_SCHEMA = 'assetv2'
   AND (SPECIFIC_NAME LIKE '%bulkload%' OR SPECIFIC_NAME LIKE '%carga_masiva%')
 ORDER BY SPECIFIC_NAME, ORDINAL_POSITION;

SELECT '=== 5. Tabla destino de Equipos ===' AS seccion;
SELECT COLUMN_NAME, DATA_TYPE, IS_NULLABLE, COLUMN_KEY, COLUMN_DEFAULT
  FROM information_schema.COLUMNS
 WHERE TABLE_SCHEMA = 'assetv2' AND TABLE_NAME = 'equipos'
 ORDER BY ORDINAL_POSITION;

SELECT '=== 6. Cuántos equipos hay hoy, por empresa (línea base) ===' AS seccion;
SELECT id_empresa, COUNT(*) AS equipos
  FROM assetv2.equipos
 GROUP BY id_empresa
 ORDER BY equipos DESC
 LIMIT 10;
