-- Script para corregir el stored procedure ejecutar_carga_masiva
-- Este script corrige el problema de búsqueda de stored procedures con esquema incluido

-- DROP FUNCTION sta.ejecutar_carga_masiva(varchar, text, int4);

CREATE OR REPLACE FUNCTION sta.ejecutar_carga_masiva(p_stored_procedure character varying, p_url_archivo text, p_empr_id integer)
 RETURNS text
 LANGUAGE plpgsql
 SECURITY DEFINER
AS $function$
DECLARE
    v_sql TEXT;
    v_result TEXT;
    v_success BOOLEAN := FALSE;
    v_message TEXT := '';
    v_log_messages TEXT := '';
    v_procedure_name TEXT;
BEGIN
    
    -- Limpiar logs anteriores de esta sesión
    DROP TABLE if exists temp_log_messages;
    
    -- Insertar log inicial
    PERFORM sta.insert_log_message('INFO', 'Iniciando carga masiva: ' || p_stored_procedure || ' con archivo: ' || p_url_archivo || ' para empresa: ' || p_empr_id, 'ejecutar_carga_masiva');
    
    -- Limpiar el nombre del stored procedure (remover esquema si ya está incluido)
    IF p_stored_procedure LIKE 'sta.%' THEN
        v_procedure_name := split_part(p_stored_procedure, '.', 2);
    ELSE
        v_procedure_name := p_stored_procedure;
    END IF;
    
    -- Validar que el stored procedure existe
    IF NOT EXISTS (
        SELECT 1 FROM information_schema.routines 
        WHERE routine_schema = 'sta' 
        AND routine_name = v_procedure_name
        AND routine_type = 'FUNCTION'
    ) THEN
        v_message := 'Stored procedure no encontrado: ' || v_procedure_name;
        
        PERFORM sta.insert_log_message('ERROR', v_message, 'ejecutar_carga_masiva');
        
        -- Recopilar mensajes de log
        SELECT string_agg(level || ': ' || message, ' | ' ORDER BY timestamp) 
        INTO v_log_messages
        FROM temp_log_messages;
        
        RETURN 'ERROR: ' || v_message || ' | LOGS: ' || COALESCE(v_log_messages, '');
    END IF;
    
    -- Construir la llamada dinámica al stored procedure
    v_sql := 'SELECT sta.' || v_procedure_name || '($1, $2)';
    
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
    SELECT string_agg(level || ': ' || message, ' | ' ORDER BY timestamp) 
    INTO v_log_messages
    FROM temp_log_messages;
    
    -- Retornar resultado con logs
    IF v_success THEN
        RETURN 'SUCCESS: ' || v_message || ' | RESULT: ' || COALESCE(v_result, '') || ' | LOGS: ' || COALESCE(v_log_messages, '');
    ELSE
        RETURN 'ERROR: ' || v_message || ' | LOGS: ' || COALESCE(v_log_messages, '');
    END IF;
END;
$function$
;
