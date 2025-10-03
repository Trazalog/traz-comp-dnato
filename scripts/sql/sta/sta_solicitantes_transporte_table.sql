-- =====================================================
-- TABLA STA.SOLICITANTES_TRANSPORTE
-- =====================================================
-- Tabla temporal para carga masiva de solicitantes de transporte

-- DROP TABLE IF EXISTS sta.solicitantes_transporte;

CREATE TABLE IF NOT EXISTS sta.solicitantes_transporte (
    id SERIAL PRIMARY KEY,
    evaluador VARCHAR(255),
    razon_social VARCHAR(500) NOT NULL,
    num_registro VARCHAR(50),
    anio INTEGER,
    expediente_num VARCHAR(100),
    cuit VARCHAR(20) NOT NULL,
    domicilio VARCHAR(500) NOT NULL,
    actividad VARCHAR(500),
    tipo_res_1 VARCHAR(10),
    tipo_res_2 VARCHAR(10),
    tipo_res_3 VARCHAR(10),
    tipo_res_4 VARCHAR(10),
    tipo_res_5 VARCHAR(10),
    procesado BOOLEAN DEFAULT FALSE,
    fec_proceso TIMESTAMP DEFAULT NULL,
    error_mensaje TEXT
);

-- Índices para mejorar performance
CREATE INDEX IF NOT EXISTS idx_solicitantes_transporte_procesado ON sta.solicitantes_transporte(procesado);
CREATE INDEX IF NOT EXISTS idx_solicitantes_transporte_cuit ON sta.solicitantes_transporte(cuit);

-- Comentarios en la tabla
COMMENT ON TABLE sta.solicitantes_transporte IS 'Tabla temporal para carga masiva de solicitantes de transporte';
COMMENT ON COLUMN sta.solicitantes_transporte.evaluador IS 'Nombre del evaluador';
COMMENT ON COLUMN sta.solicitantes_transporte.razon_social IS 'Razón social de la empresa';
COMMENT ON COLUMN sta.solicitantes_transporte.num_registro IS 'Número de registro';
COMMENT ON COLUMN sta.solicitantes_transporte.anio IS 'Año del expediente';
COMMENT ON COLUMN sta.solicitantes_transporte.expediente_num IS 'Número de expediente';
COMMENT ON COLUMN sta.solicitantes_transporte.cuit IS 'CUIT de la empresa';
COMMENT ON COLUMN sta.solicitantes_transporte.domicilio IS 'Domicilio de la empresa';
COMMENT ON COLUMN sta.solicitantes_transporte.actividad IS 'Actividad principal';
COMMENT ON COLUMN sta.solicitantes_transporte.tipo_res_1 IS 'Tipo de residuo 1 (x = marcado)';
COMMENT ON COLUMN sta.solicitantes_transporte.tipo_res_2 IS 'Tipo de residuo 2 (x = marcado)';
COMMENT ON COLUMN sta.solicitantes_transporte.tipo_res_3 IS 'Tipo de residuo 3 (x = marcado)';
COMMENT ON COLUMN sta.solicitantes_transporte.tipo_res_4 IS 'Tipo de residuo 4 (x = marcado)';
COMMENT ON COLUMN sta.solicitantes_transporte.tipo_res_5 IS 'Tipo de residuo 5 (x = marcado)';
COMMENT ON COLUMN sta.solicitantes_transporte.procesado IS 'Indica si el registro fue procesado';
COMMENT ON COLUMN sta.solicitantes_transporte.fec_proceso IS 'Fecha y hora del procesamiento';
COMMENT ON COLUMN sta.solicitantes_transporte.error_mensaje IS 'Mensaje de error si el procesamiento falló';
