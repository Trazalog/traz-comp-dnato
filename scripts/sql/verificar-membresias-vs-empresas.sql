-- =============================================================================
-- Control previo al despliegue del login con selección de empresa
-- =============================================================================
--
-- OBJETIVO
--   El login nuevo resuelve las empresas de un usuario uniendo
--   seg.memberships_users con core.empresas: el "group" de la membresía tiene
--   que coincidir con el `nombre` O la `descripcion` de una empresa. Una
--   membresía que no coincida con ninguna NO se le puede ofrecer al usuario
--   (sin empr_id no hay sesión).
--
--   Este script mide cuántos casos así existen HOY, antes de desplegar.
--   Es de SOLO LECTURA: no modifica nada.
--
--   EL MATCH VA CONTRA nombre O descripcion, y no es redundante: verificado
--   contra la base de desarrollo el 2026-08-27, cada empresa usa una u otra
--   según cómo fue creada. Sobre 58 grupos distintos, mirando sólo
--   `descripcion` quedaban 9 usuarios sin poder entrar; sólo `nombre`, 40;
--   mirando las dos, 7 (todos de cuentas de prueba).
--
-- DÓNDE SE EJECUTA
--   Contra la base PostgreSQL de Dnato del ambiente que se vaya a desplegar
--   (primero demo, después producción). Desde una terminal con acceso a esa
--   base, por ejemplo:
--
--     psql -h <host> -U <usuario> -d <base> -f verificar-membresias-vs-empresas.sql
--
--   O pegando cada consulta en el cliente SQL que se use habitualmente.
--
-- CÓMO SE LEE EL RESULTADO
--   Consulta 1: si devuelve 0 filas, no hay nada que corregir y el login nuevo
--               le va a mostrar a cada usuario todas sus empresas.
--               Si devuelve filas, esas membresías quedarían invisibles.
--   Consulta 2: usuarios que se quedarían SIN NINGUNA empresa, o sea que no
--               podrían entrar. Es la más importante: idealmente 0 filas.
--   Consulta 3: cuántos usuarios van a ver la pantalla nueva de selección.
-- =============================================================================


-- -----------------------------------------------------------------------------
-- 1. Membresías huérfanas: el group no coincide con ninguna empresa
-- -----------------------------------------------------------------------------
SELECT mu.email,
       mu."group" AS grupo_sin_empresa
  FROM seg.memberships_users mu
 WHERE NOT EXISTS (
           SELECT 1
             FROM core.empresas e
            WHERE (   (TRIM(e.nombre)      <> '' AND UPPER(TRIM(e.nombre))      = UPPER(TRIM(mu."group")))
                           OR (TRIM(e.descripcion) <> '' AND UPPER(TRIM(e.descripcion)) = UPPER(TRIM(mu."group"))) )
              AND e.eliminado = false
       )
 GROUP BY mu.email, mu."group"
 ORDER BY mu."group", mu.email;


-- -----------------------------------------------------------------------------
-- 2. 🔴 Usuarios que quedarían SIN NINGUNA empresa resoluble
--    (tienen membresías, pero ninguna matchea → no podrían iniciar sesión)
-- -----------------------------------------------------------------------------
SELECT u.email,
       COUNT(DISTINCT mu."group") AS membresias_totales
  FROM seg.users u
  INNER JOIN seg.memberships_users mu
          ON LOWER(TRIM(mu.email)) = LOWER(TRIM(u.email))
 WHERE NOT EXISTS (
           SELECT 1
             FROM seg.memberships_users mu2
             INNER JOIN core.empresas e
                     ON (   (TRIM(e.nombre)      <> '' AND UPPER(TRIM(e.nombre))      = UPPER(TRIM(mu2."group")))
                             OR (TRIM(e.descripcion) <> '' AND UPPER(TRIM(e.descripcion)) = UPPER(TRIM(mu2."group"))) )
            WHERE LOWER(TRIM(mu2.email)) = LOWER(TRIM(u.email))
              AND e.eliminado = false
       )
 GROUP BY u.email
 ORDER BY u.email;


-- -----------------------------------------------------------------------------
-- 3. Distribución de usuarios por cantidad de empresas resolubles
--    Cuántos van directo al sistema (1) y cuántos van a ver la pantalla nueva (2+)
-- -----------------------------------------------------------------------------
SELECT cantidad_empresas,
       COUNT(*) AS usuarios
  FROM (
        SELECT LOWER(TRIM(mu.email))        AS email,
               COUNT(DISTINCT e.empr_id)    AS cantidad_empresas
          FROM seg.memberships_users mu
          INNER JOIN core.empresas e
                  ON (   (TRIM(e.nombre)      <> '' AND UPPER(TRIM(e.nombre))      = UPPER(TRIM(mu."group")))
                           OR (TRIM(e.descripcion) <> '' AND UPPER(TRIM(e.descripcion)) = UPPER(TRIM(mu."group"))) )
         WHERE e.eliminado = false
         GROUP BY LOWER(TRIM(mu.email))
       ) t
 GROUP BY cantidad_empresas
 ORDER BY cantidad_empresas;


-- -----------------------------------------------------------------------------
-- 4. Empresas sin logo cargado
--    No es un problema: la pantalla usa un placeholder genérico. Sirve para
--    saber cuántas tarjetas van a salir con el logo por defecto.
-- -----------------------------------------------------------------------------
SELECT COUNT(*) FILTER (WHERE image IS NULL OR imagepath IS NULL OR imagepath = '') AS sin_logo,
       COUNT(*)                                                                     AS total
  FROM core.empresas
 WHERE eliminado = false;
