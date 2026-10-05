-- Script para corregir el formulario de registro de usuario
-- Actualizar los items para usar el form_id correcto (72)

-- 1. Eliminar los items incorrectos con form_id = 1
DELETE FROM frm.items WHERE form_id = 1 AND name IN ('como_enteraste', 'actividad_empresa', 'cantidad_empleados', 'problemas_principales');

-- 2. Insertar los items correctos con form_id = 72
INSERT INTO frm.items (form_id, name, label, tipo_dato, requerido, orden, columna, valo_id, fec_alta) VALUES 
-- Pregunta 1: ¿Cómo te enteraste de Trazalog? (Radio buttons)
(72, 'como_enteraste', '¿Cómo te enteraste de Trazalog?', 'radio', true, 1, 'col-md-12', 'como_enteraste', NOW()),

-- Pregunta 2: ¿A que se dedica tu empresa? (Checkboxes múltiples)
(72, 'actividad_empresa', '¿A qué se dedica tu empresa?', 'check', true, 2, 'col-md-12', 'actividad_empresa', NOW()),

-- Pregunta 3: ¿Cuántos empleados tiene tu empresa? (Radio buttons)
(72, 'cantidad_empleados', '¿Cuántos empleados tiene tu empresa?', 'radio', true, 3, 'col-md-12', 'cantidad_empleados', NOW()),

-- Pregunta 4: ¿Cuáles son los principales problemas que enfrentas? (Textarea)
(72, 'problemas_principales', '¿Cuáles son los principales problemas que hoy enfrentas?', 'textarea', false, 4, 'col-md-12', NULL, NOW());

-- 3. Verificar que se insertaron correctamente
SELECT 'Items del formulario de registro insertados correctamente' as resultado;
SELECT form_id, name, label FROM frm.items WHERE form_id = 72 ORDER BY orden;


