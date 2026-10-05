-- Script para corregir los valo_id en los items del formulario
-- Los valo_id deben coincidir con los nombres de las tablas en core.tablas

-- Actualizar el item de "como_enteraste" para usar la tabla correcta
UPDATE frm.items 
SET valo_id = '1-como_enteraste' 
WHERE form_id = 72 AND name = 'como_enteraste';

-- Actualizar el item de "actividad_empresa" para usar la tabla correcta
UPDATE frm.items 
SET valo_id = '2-actividad_empresa' 
WHERE form_id = 72 AND name = 'actividad_empresa';

-- Actualizar el item de "cantidad_empleados" para usar la tabla correcta
UPDATE frm.items 
SET valo_id = '3-cantidad_empleados' 
WHERE form_id = 72 AND name = 'cantidad_empleados';

-- Verificar los cambios
SELECT name, valo_id, tipo_dato FROM frm.items WHERE form_id = 72 ORDER BY orden;


