-- =====================================================
-- TEST SOLICITANTES TRANSPORTE
-- =====================================================
-- Script para probar la funcionalidad de solicitantes de transporte

-- Limpiar datos de prueba anteriores
DELETE FROM sta.solicitantes_transporte WHERE procesado = TRUE;
DELETE FROM log.solicitantes_transporte WHERE usuario_app = 'TEST';

-- Crear archivo CSV de prueba
\echo 'Creando archivo CSV de prueba...'

-- Crear datos de prueba directamente en la tabla
INSERT INTO sta.solicitantes_transporte (
    evaluador, razon_social, num_registro, anio, expediente_num, cuit, 
    domicilio, actividad, tipo_res_1, tipo_res_2, tipo_res_3, tipo_res_4, tipo_res_5
) VALUES 
('TEST_USER', 'EMPRESA TEST S.A.', '999', 2024, '1300-TEST/24', '30-12345678-9', 
 'Av. Test 123, Capital, San Juan', 'Actividad de Prueba', 'x', '', 'x', '', 'x'),
('TEST_USER', 'OTRA EMPRESA S.R.L.', '998', 2024, '1300-TEST2/24', '30-87654321-0', 
 'Calle Test 456, Rawson, San Juan', 'Construcción', '', 'x', '', 'x', '');

-- Probar el stored procedure
\echo 'Ejecutando stored procedure de prueba...'

SELECT sta.bulkload_solicitantes_transporte('test_file.csv', 1) as resultado;

-- Verificar resultados
\echo 'Verificando resultados...'

-- Mostrar registros procesados en sta
SELECT 
    'Registros en sta.solicitantes_transporte' as tabla,
    COUNT(*) as total,
    COUNT(CASE WHEN procesado = TRUE THEN 1 END) as procesados,
    COUNT(CASE WHEN procesado = FALSE THEN 1 END) as pendientes
FROM sta.solicitantes_transporte;

-- Mostrar registros insertados en log
SELECT 
    'Registros en log.solicitantes_transporte' as tabla,
    COUNT(*) as total
FROM log.solicitantes_transporte 
WHERE usuario_app = 'BULKLOAD';

-- Mostrar algunos registros de ejemplo
SELECT 
    razon_social,
    cuit,
    domicilio,
    rubr_id,
    tist_id,
    depa_id,
    prov_id
FROM log.solicitantes_transporte 
WHERE usuario_app = 'BULKLOAD'
LIMIT 5;

\echo 'Test completado!'
