-- =====================================================
-- FUNCIONES AUXILIARES PARA BULKLOAD SOLICITANTES TRANSPORTE
-- =====================================================

-- Función para determinar rubro basado en la actividad
CREATE OR REPLACE FUNCTION sta.determinar_rubro_por_actividad(p_actividad varchar)
RETURNS varchar
LANGUAGE plpgsql
AS $function$
BEGIN
    -- Mapeo de actividades a rubros
    -- Estos valores deben existir en core.tablas con tabl_tipo = 'RUBRO'
    
    IF p_actividad IS NULL OR TRIM(p_actividad) = '' THEN
        RETURN 'RUBRO_GENERAL'; -- Rubro por defecto
    END IF;
    
    p_actividad := UPPER(TRIM(p_actividad));
    
    -- Mapeo de actividades específicas
    IF p_actividad LIKE '%MINERA%' OR p_actividad LIKE '%EXPLOTACION%' OR p_actividad LIKE '%PROSPECCION%' THEN
        RETURN 'RUBRO_MINERO';
    ELSIF p_actividad LIKE '%ALIMENTARIA%' OR p_actividad LIKE '%ALIMENTACION%' OR p_actividad LIKE '%DULCES%' OR p_actividad LIKE '%CONSERVAS%' THEN
        RETURN 'RUBRO_ALIMENTARIO';
    ELSIF p_actividad LIKE '%CONSTRUCCION%' OR p_actividad LIKE '%CONSTRUCTORA%' THEN
        RETURN 'RUBRO_CONSTRUCCION';
    ELSIF p_actividad LIKE '%COMERCIAL%' OR p_actividad LIKE '%CENTRO DE COMPRAS%' THEN
        RETURN 'RUBRO_COMERCIAL';
    ELSIF p_actividad LIKE '%INDUSTRIAL%' OR p_actividad LIKE '%FABRICA%' OR p_actividad LIKE '%PLANTA%' THEN
        RETURN 'RUBRO_INDUSTRIAL';
    ELSIF p_actividad LIKE '%HOTELERIA%' OR p_actividad LIKE '%HOTEL%' THEN
        RETURN 'RUBRO_HOTELERO';
    ELSIF p_actividad LIKE '%AGRICOLA%' OR p_actividad LIKE '%AGRO%' OR p_actividad LIKE '%EMPAQUE%' THEN
        RETURN 'RUBRO_AGRICOLA';
    ELSE
        RETURN 'RUBRO_GENERAL'; -- Rubro por defecto
    END IF;
END;
$function$
;

-- Función para determinar tipo de solicitante basado en tipos de residuo
CREATE OR REPLACE FUNCTION sta.determinar_tipo_solicitante(
    p_tipo_res_1 varchar, 
    p_tipo_res_2 varchar, 
    p_tipo_res_3 varchar, 
    p_tipo_res_4 varchar, 
    p_tipo_res_5 varchar
)
RETURNS varchar
LANGUAGE plpgsql
AS $function$
DECLARE
    v_tipos_activos INTEGER := 0;
BEGIN
    -- Contar cuántos tipos de residuo están marcados (x)
    v_tipos_activos := 0;
    
    IF p_tipo_res_1 = 'x' OR p_tipo_res_1 = 'X' THEN
        v_tipos_activos := v_tipos_activos + 1;
    END IF;
    
    IF p_tipo_res_2 = 'x' OR p_tipo_res_2 = 'X' THEN
        v_tipos_activos := v_tipos_activos + 1;
    END IF;
    
    IF p_tipo_res_3 = 'x' OR p_tipo_res_3 = 'X' THEN
        v_tipos_activos := v_tipos_activos + 1;
    END IF;
    
    IF p_tipo_res_4 = 'x' OR p_tipo_res_4 = 'X' THEN
        v_tipos_activos := v_tipos_activos + 1;
    END IF;
    
    IF p_tipo_res_5 = 'x' OR p_tipo_res_5 = 'X' THEN
        v_tipos_activos := v_tipos_activos + 1;
    END IF;
    
    -- Determinar tipo basado en cantidad de residuos
    -- Estos valores deben existir en core.tablas con tabl_tipo = 'TIPO_SOLICITANTE'
    
    IF v_tipos_activos = 0 THEN
        RETURN 'TIPO_SOLICITANTE_GENERAL';
    ELSIF v_tipos_activos = 1 THEN
        RETURN 'TIPO_SOLICITANTE_ESPECIFICO';
    ELSIF v_tipos_activos <= 3 THEN
        RETURN 'TIPO_SOLICITANTE_MULTIPLE';
    ELSE
        RETURN 'TIPO_SOLICITANTE_COMPLEJO';
    END IF;
END;
$function$
;

-- Función para obtener departamento por domicilio
CREATE OR REPLACE FUNCTION sta.obtener_departamento_por_domicilio(p_domicilio varchar)
RETURNS int
LANGUAGE plpgsql
AS $function$
BEGIN
    -- Mapeo básico de departamentos de San Juan por palabras clave en el domicilio
    -- Estos valores deben existir en core.departamentos
    
    IF p_domicilio IS NULL OR TRIM(p_domicilio) = '' THEN
        RETURN NULL;
    END IF;
    
    p_domicilio := UPPER(TRIM(p_domicilio));
    
    -- Mapeo de departamentos
    IF p_domicilio LIKE '%CAPITAL%' OR p_domicilio LIKE '%SAN JUAN%' THEN
        RETURN 1; -- Capital (asumiendo que el ID 1 es Capital)
    ELSIF p_domicilio LIKE '%RAWSON%' THEN
        RETURN 2; -- Rawson
    ELSIF p_domicilio LIKE '%RIVADAVIA%' THEN
        RETURN 3; -- Rivadavia
    ELSIF p_domicilio LIKE '%POCITO%' THEN
        RETURN 4; -- Pocito
    ELSIF p_domicilio LIKE '%CHIMBAS%' THEN
        RETURN 5; -- Chimbas
    ELSIF p_domicilio LIKE '%SARMIENTO%' THEN
        RETURN 6; -- Sarmiento
    ELSE
        RETURN 1; -- Capital por defecto
    END IF;
END;
$function$
;

-- Función para obtener provincia San Juan
CREATE OR REPLACE FUNCTION sta.obtener_provincia_san_juan()
RETURNS int
LANGUAGE plpgsql
AS $function$
BEGIN
    -- Retorna el ID de la provincia de San Juan
    -- Este valor debe existir en alm.alm_proveedores
    RETURN 1; -- Asumiendo que el ID 1 corresponde a San Juan
END;
$function$
;

-- Comentarios en las funciones
COMMENT ON FUNCTION sta.determinar_rubro_por_actividad(varchar) IS 'Determina el rubro basado en la actividad de la empresa';
COMMENT ON FUNCTION sta.determinar_tipo_solicitante(varchar, varchar, varchar, varchar, varchar) IS 'Determina el tipo de solicitante basado en los tipos de residuo marcados';
COMMENT ON FUNCTION sta.obtener_departamento_por_domicilio(varchar) IS 'Obtiene el departamento basado en el domicilio';
COMMENT ON FUNCTION sta.obtener_provincia_san_juan() IS 'Retorna el ID de la provincia de San Juan';
