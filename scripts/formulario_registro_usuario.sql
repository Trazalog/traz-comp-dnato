-- Script para crear el formulario de registro de usuario
-- Formulario con 4 preguntas para recopilar información del usuario

-- 1. Crear el formulario principal
INSERT INTO frm.formularios (nombre, empr_id, fec_alta, usuario) 
VALUES ('Formulario Registro Usuario', 1, NOW(), 'sistema');

-- Obtener el form_id generado (asumimos que será el siguiente disponible)
-- En un entorno real, deberías usar una función o variable para obtener el ID

-- 2. Crear los valores para la pregunta 1: ¿Cómo te enteraste de Trazalog?
INSERT INTO core.tablas (tabla, tabl_id, descripcion, valor, eliminado, fec_alta, usuario) VALUES 
('1-como_enteraste', 1, 'Internet', 'internet', false, NOW(), 'sistema'),
('1-como_enteraste', 2, 'Referencia de otro usuario', 'referencia', false, NOW(), 'sistema'),
('1-como_enteraste', 3, 'Publicidad', 'publicidad', false, NOW(), 'sistema');

-- 3. Crear los valores para la pregunta 2: ¿A que se dedica tu empresa?
INSERT INTO core.tablas (tabla, tabl_id, descripcion, valor, eliminado, fec_alta, usuario) VALUES 
('2-actividad_empresa', 1, 'Industria', 'industria', false, NOW(), 'sistema'),
('2-actividad_empresa', 2, 'Minería', 'mineria', false, NOW(), 'sistema'),
('2-actividad_empresa', 3, 'Agricultura', 'agricultura', false, NOW(), 'sistema'),
('2-actividad_empresa', 4, 'Ganadería', 'ganaderia', false, NOW(), 'sistema'),
('2-actividad_empresa', 5, 'Reciclado', 'reciclado', false, NOW(), 'sistema'),
('2-actividad_empresa', 6, 'Tecnología', 'tecnologia', false, NOW(), 'sistema'),
('2-actividad_empresa', 7, 'Militar', 'militar', false, NOW(), 'sistema');

-- 4. Crear los valores para la pregunta 3: ¿Cuántos empleados tiene tu empresa?
INSERT INTO core.tablas (tabla, tabl_id, descripcion, valor, eliminado, fec_alta, usuario) VALUES 
('3-cantidad_empleados', 1, '1 a 5', '1-5', false, NOW(), 'sistema'),
('3-cantidad_empleados', 2, '5 a 20', '5-20', false, NOW(), 'sistema'),
('3-cantidad_empleados', 3, '20 a 40', '20-40', false, NOW(), 'sistema'),
('3-cantidad_empleados', 4, 'Más de 40', 'mas-40', false, NOW(), 'sistema');

-- 5. Crear los items del formulario
-- Asumiendo que el form_id será el último insertado + 1, o puedes usar una variable
-- Para este ejemplo, asumimos que el form_id es 1 (ajusta según tu caso)

INSERT INTO frm.items (form_id, name, label, tipo_dato, requerido, orden, columna, valo_id, fec_alta) VALUES 
-- Pregunta 1: ¿Cómo te enteraste de Trazalog? (Radio buttons)
(1, 'como_enteraste', '¿Cómo te enteraste de Trazalog?', 'radio', true, 1, 'col-md-12', 'como_enteraste', NOW()),

-- Pregunta 2: ¿A que se dedica tu empresa? (Checkboxes múltiples)
(1, 'actividad_empresa', '¿A qué se dedica tu empresa?', 'check', true, 2, 'col-md-12', 'actividad_empresa', NOW()),

-- Pregunta 3: ¿Cuántos empleados tiene tu empresa? (Radio buttons)
(1, 'cantidad_empleados', '¿Cuántos empleados tiene tu empresa?', 'radio', true, 3, 'col-md-12', 'cantidad_empleados', NOW()),

-- Pregunta 4: ¿Cuáles son los principales problemas que enfrentas? (Textarea)
(1, 'problemas_principales', '¿Cuáles son los principales problemas que hoy enfrentas?', 'textarea', false, 4, 'col-md-12', NULL, NOW());

-- 6. Agregar columna reg_info_id a la tabla seg.users
ALTER TABLE seg.users ADD COLUMN reg_info_id INTEGER;

-- Agregar comentario a la columna
COMMENT ON COLUMN seg.users.reg_info_id IS 'ID de la instancia del formulario de registro completado por el usuario';

-- Crear índice para mejorar performance
CREATE INDEX idx_users_reg_info_id ON seg.users(reg_info_id);

-- Agregar foreign key constraint (opcional, descomenta si quieres mantener integridad referencial)
-- ALTER TABLE seg.users ADD CONSTRAINT fk_users_reg_info 
-- FOREIGN KEY (reg_info_id) REFERENCES frm.instancias_formularios(info_id);
