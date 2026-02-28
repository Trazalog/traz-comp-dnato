-- Stored Procedure: seg.insert_usuario_con_hash
-- Fase 0.2: Agregar parámetros de imagen
-- Fecha: 2026-01-30

CREATE OR REPLACE FUNCTION seg.insert_usuario_con_hash(
    p_first_name VARCHAR,
    p_last_name VARCHAR,
    p_email VARCHAR,
    p_password_plain VARCHAR,
    p_role VARCHAR,
    p_status VARCHAR,
    p_banned_users VARCHAR,
    p_telefono VARCHAR,
    p_dni VARCHAR,
    p_usernick VARCHAR,
    p_image_name VARCHAR,  -- NUEVO PARÁMETRO
    p_image TEXT           -- NUEVO PARÁMETRO (se convierte a bytea)
) RETURNS INTEGER AS $$
DECLARE
    v_user_id INTEGER;
    v_password_hash TEXT;
    v_image_bytea BYTEA;
BEGIN
    -- Hashear password con bcrypt
    v_password_hash := crypt(p_password_plain, gen_salt('bf'));
    
    -- Convertir imagen de TEXT (base64) a BYTEA
    -- Si p_image es NULL o vacío, dejar v_image_bytea como NULL
    IF p_image IS NULL OR p_image = '' THEN
        v_image_bytea := NULL;
    ELSE
        -- Decodificar base64 a bytea
        v_image_bytea := decode(p_image, 'base64');
    END IF;
    
    -- Insertar usuario con imagen
    INSERT INTO seg.users (
        first_name, last_name, email, password, role, status, 
        banned_users, telefono, dni, usernick, image_name, image, depo_id
    ) VALUES (
        p_first_name, p_last_name, p_email, v_password_hash, p_role, p_status,
        p_banned_users, p_telefono, p_dni, p_usernick, 
        p_image_name, v_image_bytea, NULL  -- depo_id siempre NULL en creación
    ) RETURNING id INTO v_user_id;
    
    RETURN v_user_id;
END;
$$ LANGUAGE plpgsql;

-- Comentario de la función
COMMENT ON FUNCTION seg.insert_usuario_con_hash IS 'Crea un usuario en seg.users con password hasheado en bcrypt. Incluye soporte para imagen (image_name e image). Fase 0.2 - 2026-01-30';

