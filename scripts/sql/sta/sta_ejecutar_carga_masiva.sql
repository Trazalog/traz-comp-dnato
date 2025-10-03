-- =====================================================
-- PROCEDIMIENTO PADRE PARA CARGA MASIVA DINÁMICA - CORREGIDO
-- =====================================================

-- Función wrapper que ejecuta stored procedures de carga masiva dinámicamente
-- y recopila todos los logs en una tabla temporal
CREATE OR REPLACE FUNCTION sta.ejecutar_carga_masiva(
    p_stored_procedure VARCHAR(100),
    p_url_archivo TEXT,
    p_empr_id INTEGER
)
RETURNS TEXT
LANGUAGE plpgsql
SECURITY DEFINER
AS $$
DECLARE
    v_sql TEXT;
    v_result TEXT;
    v_success BOOLEAN := FALSE;
    v_message TEXT := '';
    v_log_messages TEXT := '';
BEGIN
    -- Crear tabla temporal con nombre fijo (se reutiliza en toda la sesión)
    CREATE TEMP TABLE IF NOT EXISTS temp_log_messages (
        id SERIAL PRIMARY KEY,
        timestamp TIMESTAMP DEFAULT NOW(),
        level VARCHAR(10),
        message TEXT,
        procedure_name VARCHAR(100)
    );
    
    -- Limpiar logs anteriores de esta sesión
    DELETE FROM temp_log_messages;
    
    -- Insertar log inicial
    PERFORM sta.insert_log_message('INFO', 'Iniciando carga masiva: ' || p_stored_procedure || ' con archivo: ' || p_url_archivo || ' para empresa: ' || p_empr_id, 'ejecutar_carga_masiva');
    
    -- Validar que el stored procedure existe
    IF NOT EXISTS (
        SELECT 1 FROM information_schema.routines 
        WHERE routine_schema = 'sta' 
        AND routine_name = p_stored_procedure
        AND routine_type = 'FUNCTION'
    ) THEN
        v_message := 'Stored procedure no encontrado: ' || p_stored_procedure;
        
        PERFORM sta.insert_log_message('ERROR', v_message, 'ejecutar_carga_masiva');
        
        -- Recopilar mensajes de log
        SELECT string_agg(level || ': ' || message, ' | ') 
        INTO v_log_messages
        FROM temp_log_messages 
        ORDER BY timestamp;
        
        RETURN 'ERROR: ' || v_message || ' | LOGS: ' || COALESCE(v_log_messages, '');
    END IF;
    
    -- Construir la llamada dinámica al stored procedure
    v_sql := 'SELECT sta.' || p_stored_procedure || '($1, $2)';
    
    BEGIN
        -- Ejecutar el stored procedure dinámicamente
        EXECUTE v_sql INTO v_result USING p_url_archivo, p_empr_id;
        
        -- Si llegamos aquí, la ejecución fue exitosa
        v_success := TRUE;
        v_message := 'Carga masiva ejecutada exitosamente';
        
        PERFORM sta.insert_log_message('SUCCESS', v_message, 'ejecutar_carga_masiva');
        
    EXCEPTION
        WHEN OTHERS THEN
            v_success := FALSE;
            v_message := 'Error ejecutando stored procedure: ' || SQLERRM;
            
            PERFORM sta.insert_log_message('ERROR', 'Error en carga masiva: ' || v_message, 'ejecutar_carga_masiva');
    END;
    
    -- Recopilar todos los mensajes de log
    SELECT string_agg(level || ': ' || message, ' | ') 
    INTO v_log_messages
    FROM temp_log_messages 
    ORDER BY timestamp;
    
    -- Retornar resultado con logs
    IF v_success THEN
        RETURN 'SUCCESS: ' || v_message || ' | RESULT: ' || COALESCE(v_result, '') || ' | LOGS: ' || COALESCE(v_log_messages, '');
    ELSE
        RETURN 'ERROR: ' || v_message || ' | LOGS: ' || COALESCE(v_log_messages, '');
    END IF;
END;
$$;

-- Comentarios y documentación
COMMENT ON FUNCTION sta.ejecutar_carga_masiva(VARCHAR, TEXT, INTEGER) IS 
'Función wrapper que ejecuta stored procedures de carga masiva dinámicamente y recopila todos los logs';

-- =====================================================
-- FUNCIÓN AUXILIAR PARA LOGGING - SIMPLIFICADA
-- =====================================================

-- Función auxiliar para insertar logs en la tabla temporal y mostrar en consola
CREATE OR REPLACE FUNCTION sta.insert_log_message(
    p_level VARCHAR(10),
    p_message TEXT,
    p_procedure_name VARCHAR(100)
)
RETURNS VOID
LANGUAGE plpgsql
SECURITY DEFINER
AS $$
BEGIN
    -- Crear tabla temporal si no existe (mismo nombre en toda la sesión)
    CREATE TEMP TABLE IF NOT EXISTS temp_log_messages (
        id SERIAL PRIMARY KEY,
        timestamp TIMESTAMP DEFAULT NOW(),
        level VARCHAR(10),
        message TEXT,
        procedure_name VARCHAR(100)
    );
    
    -- Insertar el mensaje de log
    INSERT INTO temp_log_messages (level, message, procedure_name) 
    VALUES (p_level, p_message, p_procedure_name);
    
    -- También mostrar en consola (equivalente a RAISE INFO)
    RAISE INFO '%: %', p_procedure_name, p_message;
END;
$$;

COMMENT ON FUNCTION sta.insert_log_message(VARCHAR, TEXT, VARCHAR) IS 
'Función auxiliar para insertar mensajes de log en la tabla temporal de la sesión';
