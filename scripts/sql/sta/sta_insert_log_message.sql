-- DROP FUNCTION sta.insert_log_message(varchar, text, varchar);

CREATE OR REPLACE FUNCTION sta.insert_log_message(p_level character varying, p_message text, p_procedure_name character varying)
 RETURNS void
 LANGUAGE plpgsql
 SECURITY DEFINER
AS $function$
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
    INSERT INTO sta.temp_log_messages (level, message, procedure_name) 
    VALUES (p_level, p_message, p_procedure_name);
    
    -- También mostrar en consola (equivalente a RAISE INFO)
    RAISE INFO '%: %', p_procedure_name, p_message;
END;
$function$
;
