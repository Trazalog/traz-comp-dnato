-- Stored Procedure: seg.insert_usuario_con_hash
-- PBKDF2-SHA256 compatible con application/libraries/Password.php
-- Formato: sha256:1000:salt_base64:hash_base64
-- REQUIERE: CREATE EXTENSION IF NOT EXISTS pgcrypto;

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
    p_image_name VARCHAR DEFAULT NULL,
    p_image TEXT DEFAULT NULL
) RETURNS INTEGER AS $$
DECLARE
    v_user_id INTEGER;
    v_password_hash TEXT;
    v_salt_base64 TEXT;
    v_salt_bytes BYTEA;
    v_key_length INTEGER := 24;
    v_iterations INTEGER := 1000;
    v_algorithm TEXT := 'sha256';
    v_hash_length INTEGER := 32; -- SHA-256 = 32 bytes
    v_block_count INTEGER;
    v_output BYTEA := ''::BYTEA;
    v_last BYTEA;
    v_xorsum BYTEA;
    v_image_bytea BYTEA;
    i INTEGER;
    j INTEGER;
    b INTEGER;
BEGIN
    IF NOT EXISTS (SELECT 1 FROM pg_extension WHERE extname = 'pgcrypto') THEN
        RAISE EXCEPTION 'Extension pgcrypto is required. Run: CREATE EXTENSION IF NOT EXISTS pgcrypto;';
    END IF;

    -- Generar salt: 24 bytes random, codificado en base64
    -- PHP pasa el salt como STRING base64 al HMAC, no como bytes raw
    v_salt_bytes := gen_random_bytes(24);
    v_salt_base64 := encode(v_salt_bytes, 'base64');

    -- PBKDF2 según RFC 2898 (réplica exacta de Password.php)
    v_block_count := ceil(v_key_length::FLOAT / v_hash_length);

    FOR b IN 1..v_block_count LOOP
        -- $last = $salt . pack("N", $i)  =>  salt_base64_string || 4-byte big-endian block index
        -- PHP hace hash_hmac($algo, $salt . pack("N",$i), $password, true)
        -- donde $salt es el string base64 (no los bytes), y $password es el texto plano
        v_last := hmac(
            convert_to(v_salt_base64, 'UTF8') || int4send(b),
            convert_to(p_password_plain, 'UTF8'),
            'sha256'
        );
        v_xorsum := v_last;

        FOR j IN 1..(v_iterations - 1) LOOP
            -- $last = hash_hmac($algo, $last, $password, true)
            v_last := hmac(v_last, convert_to(p_password_plain, 'UTF8'), 'sha256');
            -- $xorsum ^= $last  (XOR byte a byte)
            v_xorsum := xor_bytea(v_xorsum, v_last);
        END LOOP;

        v_output := v_output || v_xorsum;
    END LOOP;

    -- Tomar los primeros key_length bytes y codificar base64
    v_password_hash := v_algorithm || ':' || v_iterations || ':' || v_salt_base64 || ':' ||
                       encode(substring(v_output FROM 1 FOR v_key_length), 'base64');

    -- Imagen: convertir TEXT base64 a BYTEA
    IF p_image IS NOT NULL AND p_image <> '' THEN
        v_image_bytea := decode(p_image, 'base64');
    ELSE
        v_image_bytea := NULL;
    END IF;

    INSERT INTO seg.users (
        first_name, last_name, email, password, role, status,
        banned_users, telefono, dni, usernick, image_name, image, depo_id
    ) VALUES (
        p_first_name, p_last_name, p_email, v_password_hash, p_role, p_status,
        p_banned_users, p_telefono, p_dni, p_usernick,
        p_image_name, v_image_bytea, NULL
    ) RETURNING id INTO v_user_id;

    RETURN v_user_id;
END;
$$ LANGUAGE plpgsql;

-- Función auxiliar XOR de dos BYTEA (necesaria para PBKDF2)
CREATE OR REPLACE FUNCTION xor_bytea(a BYTEA, b BYTEA) RETURNS BYTEA AS $$
DECLARE
    result BYTEA;
    i INTEGER;
BEGIN
    IF length(a) <> length(b) THEN
        RAISE EXCEPTION 'xor_bytea: longitudes distintas (% vs %)', length(a), length(b);
    END IF;
    result := a;
    FOR i IN 0..(length(a) - 1) LOOP
        result := set_byte(result, i, get_byte(a, i) # get_byte(b, i));
    END LOOP;
    RETURN result;
END;
$$ LANGUAGE plpgsql IMMUTABLE;

GRANT EXECUTE ON FUNCTION seg.insert_usuario_con_hash TO PUBLIC;
GRANT EXECUTE ON FUNCTION xor_bytea TO PUBLIC;

COMMENT ON FUNCTION seg.insert_usuario_con_hash IS
'Inserta usuario en seg.users con hash PBKDF2-SHA256 idéntico al de application/libraries/Password.php. Formato: sha256:1000:salt:hash.';
