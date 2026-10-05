-- Script para modificar la tabla seg.users y agregar columna reg_info_id
-- Ejecutar paso a paso

-- 1. Agregar columna reg_info_id a la tabla seg.users
ALTER TABLE seg.users ADD COLUMN reg_info_id INTEGER;

-- 2. Agregar comentario a la columna
COMMENT ON COLUMN seg.users.reg_info_id IS 'ID de la instancia del formulario de registro completado por el usuario';

-- 3. Crear índice para mejorar performance
CREATE INDEX idx_users_reg_info_id ON seg.users(reg_info_id);
