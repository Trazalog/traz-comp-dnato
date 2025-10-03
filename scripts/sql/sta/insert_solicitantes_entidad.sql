-- =====================================================
-- INSERTAR SOLICITANTES TRANSPORTE EN ENTIDADES DE NEGOCIO
-- =====================================================

-- Insertar la nueva entidad de negocio para solicitantes de transporte
INSERT INTO sta.entidades_negocio (nombre, stored_procedure, template) 
VALUES (
    'Solicitantes de Transporte',
    'sta.bulkload_solicitantes_transporte',
    'https://docs.google.com/spreadsheets/d/ejemplo_solicitantes_transporte/edit?usp=sharing'
) ON CONFLICT (nombre) DO UPDATE SET
    stored_procedure = EXCLUDED.stored_procedure,
    template = EXCLUDED.template;

-- Verificar inserción
SELECT 'Entidad Solicitantes de Transporte agregada correctamente' as status
WHERE EXISTS (
    SELECT 1 FROM sta.entidades_negocio 
    WHERE nombre = 'Solicitantes de Transporte'
);
