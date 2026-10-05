-- Script SQL para verificar que el password se hasheó correctamente en MD5
-- Ejecutar en MySQL AssetPlanner

-- Reemplazar 'test_md5_1769719591' con el nick del usuario de prueba creado
SET @test_nick = 'test_md5_1769719591';
SET @expected_md5 = '482c811da5d5b4bc6d497ffa98491e38';  -- MD5 de "password123"

-- Verificar usuario creado
SELECT 
    usrNick,
    usrPassword,
    LENGTH(usrPassword) as pass_length,
    CASE 
        WHEN LENGTH(usrPassword) = 32 THEN '✓ Longitud correcta (32 caracteres)'
        ELSE '✗ Longitud incorrecta'
    END as longitud_check,
    CASE 
        WHEN usrPassword REGEXP '^[0-9a-f]{32}$' THEN '✓ Formato hexadecimal válido'
        ELSE '✗ Formato hexadecimal inválido'
    END as formato_check,
    CASE 
        WHEN usrPassword = @expected_md5 THEN '✓ MD5 coincide con esperado'
        ELSE CONCAT('✗ MD5 no coincide. Esperado: ', @expected_md5, ', Obtenido: ', usrPassword)
    END as md5_check
FROM sisusers 
WHERE usrNick = @test_nick;

-- Verificar que el password original funciona (comparar con MD5 del password ingresado)
-- En AssetPlanner, cuando un usuario hace login, el sistema hashea el password ingresado
-- y lo compara con usrPassword. Si coinciden, el login es exitoso.

-- Para probar manualmente:
-- 1. Intentar login en AssetPlanner con:
--    - Username: test_md5_1769719591
--    - Password: password123
-- 2. Si el login funciona, significa que el MD5 está correcto






