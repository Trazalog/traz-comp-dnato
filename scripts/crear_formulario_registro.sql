-- Script para crear el formulario de registro de usuario
-- Ejecutar después de modificar la tabla seg.usuarios

-- 1. Crear el formulario principal
INSERT INTO frm.formularios (nombre, empr_id, fec_alta, usuario) 
VALUES ('Formulario Registro Usuario', 9000, NOW(), 'sistema');

-- 2. Crear los valores para la pregunta 1: ¿Cómo te enteraste de Trazalog?
INSERT INTO core.tablas (empr_id, tabla,  descripcion, valor, eliminado, fec_alta, usuario) VALUES 
(9000, 'como_enteraste', 'Internet', 'internet', false, NOW(), 'sistema'),
(9000, 'como_enteraste', 'Referencia de otro usuario', 'referencia', false, NOW(), 'sistema'),
(9000, 'como_enteraste',  'Publicidad', 'publicidad', false, NOW(), 'sistema');

-- 3. Crear los valores para la pregunta 2: ¿A qué se dedica tu empresa?
INSERT INTO core.tablas (empr_id, tabla,  descripcion, valor, eliminado, fec_alta, usuario) VALUES 
(9000, 'actividad_empresa', 'Industria', 'industria', false, NOW(), 'sistema'),
(9000, 'actividad_empresa', 'Minería', 'mineria', false, NOW(), 'sistema'),
(9000, 'actividad_empresa', 'Agricultura', 'agricultura', false, NOW(), 'sistema'),
(9000, 'actividad_empresa', 'Ganadería', 'ganaderia', false, NOW(), 'sistema'),
(9000, 'actividad_empresa', 'Reciclado', 'reciclado', false, NOW(), 'sistema'),
(9000, 'actividad_empresa', 'Tecnología', 'tecnologia', false, NOW(), 'sistema'),
(9000, 'actividad_empresa', 'Militar', 'militar', false, NOW(), 'sistema');

-- 4. Crear los valores para la pregunta 3: ¿Cuántos empleados tiene tu empresa?
INSERT INTO core.tablas (empr_id, tabla,  descripcion, valor, eliminado, fec_alta, usuario) VALUES 
(9000, 'cantidad_empleados', '1 a 5', '1-5', false, NOW(), 'sistema'),
(9000, 'cantidad_empleados', '5 a 20', '5-20', false, NOW(), 'sistema'),
(9000, 'cantidad_empleados', '20 a 40', '20-40', false, NOW(), 'sistema'),
(9000, 'cantidad_empleados', 'Más de 40', 'mas-40', false, NOW(), 'sistema');

-- 5. Crear los items del formulario (asumiendo que el form_id será 1)
INSERT INTO frm.items (form_id, name, label, tipo_dato, requerido, orden, columna, valo_id, fec_alta) VALUES
(24, 'como_enteraste', '¿Cómo te enteraste de Trazalog?', 'radio', true, 1, 'col-md-12', 'como_enteraste', NOW()),
(24, 'actividad_empresa', '¿A qué se dedica tu empresa?', 'check', true, 2, 'col-md-12', 'actividad_empresa', NOW()),
(24, 'cantidad_empleados', '¿Cuántos empleados tiene tu empresa?', 'radio', true, 3, 'col-md-12', 'cantidad_empleados', NOW()),
(24, 'problemas_principales', '¿Cuáles son los principales problemas que hoy enfrentas?', 'textarea', false, 4, 'col-md-12', NULL, NOW());
