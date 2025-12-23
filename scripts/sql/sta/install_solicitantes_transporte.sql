-- =====================================================
-- INSTALACIÓN COMPLETA SOLICITANTES TRANSPORTE
-- =====================================================
-- Script para instalar la funcionalidad completa de solicitantes de transporte

-- 1. Crear tabla temporal
\echo 'Creando tabla sta.solicitantes_transporte...'
\i sta_solicitantes_transporte_table.sql

-- 2. Crear funciones auxiliares
\echo 'Creando funciones auxiliares...'
\i sta_solicitantes_aux_functions.sql

-- 3. Crear stored procedure principal
\echo 'Creando stored procedure bulkload_solicitantes_transporte...'
\i bulkload_solicitantes_transporte.sql

-- 4. Verificar instalación
\echo 'Verificando instalación...'

-- Verificar tabla
SELECT 'Tabla sta.solicitantes_transporte creada correctamente' as status
WHERE EXISTS (
    SELECT 1 FROM information_schema.tables 
    WHERE table_schema = 'sta' AND table_name = 'solicitantes_transporte'
);

-- Verificar funciones
SELECT 'Funciones auxiliares creadas correctamente' as status
WHERE EXISTS (
    SELECT 1 FROM information_schema.routines 
    WHERE routine_schema = 'sta' AND routine_name = 'determinar_rubro_por_actividad'
) AND EXISTS (
    SELECT 1 FROM information_schema.routines 
    WHERE routine_schema = 'sta' AND routine_name = 'determinar_tipo_solicitante'
) AND EXISTS (
    SELECT 1 FROM information_schema.routines 
    WHERE routine_schema = 'sta' AND routine_name = 'obtener_departamento_por_domicilio'
) AND EXISTS (
    SELECT 1 FROM information_schema.routines 
    WHERE routine_schema = 'sta' AND routine_name = 'obtener_provincia_san_juan'
);

-- Verificar stored procedure
SELECT 'Stored procedure bulkload_solicitantes_transporte creado correctamente' as status
WHERE EXISTS (
    SELECT 1 FROM information_schema.routines 
    WHERE routine_schema = 'sta' AND routine_name = 'bulkload_solicitantes_transporte'
);

\echo 'Instalación completada!'
