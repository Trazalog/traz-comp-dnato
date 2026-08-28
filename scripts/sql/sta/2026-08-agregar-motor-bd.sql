-- =============================================================================
-- Carga Masiva multi-motor — migración del catálogo de entidades
-- =============================================================================
--
-- QUÉ HACE
--   Agrega la columna `motor_bd` a sta.entidades_negocio, que es lo que le
--   permite al despachador de PHP saber contra qué base corre cada entidad.
--   El catálogo sigue viviendo SIEMPRE en PostgreSQL, aunque la carga real de
--   algunas entidades ocurra en MariaDB.
--
-- POR QUÉ HACE FALTA
--   El catálogo ya tiene cargadas 'Mantenimiento Equipos' y
--   'Mantenimiento Articulos', cuyos procedimientos viven en MariaDB (assetv2)
--   y se distinguen porque NO llevan el prefijo 'sta.'. Sin esta columna, el
--   sistema las manda igual al dispatcher de PostgreSQL, que busca
--   sta.bulkload_equipos, no lo encuentra y devuelve
--   "Stored procedure no encontrado".
--
-- DÓNDE SE EJECUTA
--   Contra la base PostgreSQL de Dnato del ambiente, con la VPN levantada:
--     psql -h 10.142.0.13 -U postgres -d tools_prod_t \
--          -f scripts/sql/sta/2026-08-agregar-motor-bd.sql
--
--   Es idempotente: se puede correr más de una vez sin romper nada.
-- =============================================================================

BEGIN;

ALTER TABLE sta.entidades_negocio
  ADD COLUMN IF NOT EXISTS motor_bd varchar(20) NOT NULL DEFAULT 'postgresql';

-- Sólo dos valores válidos. Se recrea para que la migración sea repetible.
ALTER TABLE sta.entidades_negocio
  DROP CONSTRAINT IF EXISTS chk_entidades_negocio_motor_bd;

ALTER TABLE sta.entidades_negocio
  ADD CONSTRAINT chk_entidades_negocio_motor_bd
  CHECK (motor_bd IN ('postgresql', 'mariadb'));

COMMENT ON COLUMN sta.entidades_negocio.motor_bd IS
  'Motor donde corre el stored procedure de esta entidad: postgresql (por defecto) o mariadb (AssetPlanner, base assetv2). El catálogo siempre vive en PostgreSQL.';

-- Las entidades cuyo procedimiento NO lleva el prefijo 'sta.' son las de
-- AssetPlanner. Se marcan por nombre explícito y no por el prefijo, para que
-- la migración no dependa de una convención de nombres.
UPDATE sta.entidades_negocio
   SET motor_bd = 'mariadb'
 WHERE nombre IN ('Mantenimiento Equipos', 'Mantenimiento Articulos');

COMMIT;

-- Control: así debería quedar
SELECT nombre, stored_procedure, motor_bd
  FROM sta.entidades_negocio
 ORDER BY motor_bd, nombre;
