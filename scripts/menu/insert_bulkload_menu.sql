-- Script para insertar la opción de menú de Carga Masiva
-- Ejecutar después de crear las tablas necesarias

-- Insertar opción de menú para Carga Masiva
INSERT INTO seg.menues (
    modulo, 
    opcion, 
    texto, 
    url, 
    opcion_padre, 
    orden, 
    texto_onmouseover, 
    javascript, 
    url_icono, 
    eliminado, 
    fec_alta, 
    usuario, 
    usuario_app
) VALUES (
    'Operaciones', 
    'Carga Masiva', 
    'Carga Masiva de Datos', 
    'bulkload', 
    'Operaciones', 
    10, 
    'Cargar datos masivamente desde archivos Excel', 
    '', 
    'fa fa-upload', 
    0, 
    CURRENT_TIMESTAMP, 
    'admin', 
    'admin'
);

-- Asignar menú a roles administrativos (role = 1)
INSERT INTO seg.memberships_menues (
    group, 
    modulo, 
    opcion, 
    role, 
    eliminado
) VALUES (
    'admin', 
    'Operaciones', 
    'Carga Masiva', 
    1, 
    0
);

-- Si existe la tabla de permisos específicos, también insertar ahí
-- INSERT INTO bulkload_permisos (empresa, entidad_negocio, usuario_email, activo) 
-- VALUES ('admin', 'Todas', 'admin@gmail.com', 1);

-- Verificar la inserción
SELECT 
    m.modulo,
    m.opcion,
    m.texto,
    m.url,
    m.orden,
    m.eliminado,
    mm.group,
    mm.role
FROM seg.menues m
LEFT JOIN seg.memberships_menues mm ON m.modulo = mm.modulo AND m.opcion = mm.opcion
WHERE m.opcion = 'Carga Masiva'
ORDER BY m.modulo, m.opcion;






