-- =====================================================
-- BULKLOAD LOOKUP FUNCTIONS CON LOGGING TEMPORAL
-- =====================================================

-- DROP FUNCTION sta.calcula_deposito(varchar, int4);

CREATE OR REPLACE FUNCTION sta.calcula_deposito(p_codigo character varying, p_empr_id integer)
 RETURNS character varying
 LANGUAGE plpgsql
AS $function$
	DECLARE 
		v_id_depo varchar;
	BEGIN
		/** Calcula de acuerdo al codigo el id 
		 *  de deposito
		 *  @author rruiz
		 */
		
		-- La tabla temporal se crea automáticamente en sta.insert_log_message
		
		PERFORM sta.insert_log_message('INFO', 'calculando deposito ' || p_codigo, 'calcula_deposito');
	
		SELECT ad.depo_id 
		INTO STRICT v_id_depo
		FROM alm.alm_depositos ad  
		WHERE ad.empr_id= p_empr_id
		AND upper(ad.descripcion) = upper(p_codigo);
	
		RETURN v_id_depo;
	EXCEPTION
		WHEN NO_DATA_FOUND THEN
			PERFORM sta.insert_log_message('WARNING', 'El deposito no se puede calcular ' || p_codigo, 'calcula_deposito');
			RAISE;
       
	END;
$function$
;

-- DROP FUNCTION sta.calcula_marca_herramienta(varchar, int4);

CREATE OR REPLACE FUNCTION sta.calcula_marca_herramienta(p_codigo character varying, p_empr_id integer)
 RETURNS character varying
 LANGUAGE plpgsql
AS $function$
	DECLARE 
		v_tipo varchar;
	BEGIN
		/** Calcula de acuerdo al codigo el id 
		 *  de marca de herramienta
		 *  @author rruiz
		 */
		
		-- La tabla temporal se crea automáticamente en sta.insert_log_message
		
		SELECT t.tabl_id
		INTO STRICT v_tipo
		FROM core.tablas t 
		WHERE t.tabla = p_empr_id||'-marcas_herramientas'
		AND upper(t.valor) = upper(p_codigo)
		AND t.activo = true;
	
		RETURN v_tipo;
	EXCEPTION
		WHEN NO_DATA_FOUND THEN
			PERFORM sta.insert_log_message('WARNING', 'La marca de herramienta no se puede calcular ' || p_codigo, 'calcula_marca_herramienta');
			RAISE;
       
	END;
$function$
;

-- DROP FUNCTION sta.calcula_tipo_herramienta(varchar, int4);

CREATE OR REPLACE FUNCTION sta.calcula_tipo_herramienta(p_codigo character varying, p_empr_id integer)
 RETURNS character varying
 LANGUAGE plpgsql
AS $function$
	DECLARE 
		v_tipo varchar;
	BEGIN
		/** Calcula de acuerdo al codigo el id 
		 *  de tipo de herramienta
		 *  @author rruiz
		 */
		
		-- La tabla temporal se crea automáticamente en sta.insert_log_message
		
		SELECT t.tabl_id
		INTO STRICT v_tipo
		FROM core.tablas t 
		WHERE t.tabla = p_empr_id||'-tipos_herramientas'
		AND upper(t.valor) = upper(p_codigo)
		AND t.activo = true;
	
		RETURN v_tipo;
	EXCEPTION
		WHEN NO_DATA_FOUND THEN
			PERFORM sta.insert_log_message('WARNING', 'El tipo de herramienta no se puede calcular ' || p_codigo, 'calcula_tipo_herramienta');
			RAISE;
       
	END;
$function$
;

-- DROP FUNCTION sta.calcula_estado_herramienta(varchar, int4);

CREATE OR REPLACE FUNCTION sta.calcula_estado_herramienta(p_codigo character varying, p_empr_id integer)
 RETURNS character varying
 LANGUAGE plpgsql
AS $function$
	DECLARE 
		v_tipo varchar;
	BEGIN
		/** Calcula de acuerdo al codigo el id 
		 *  de estado de herramienta
		 *  @author rruiz
		 */
		
		-- La tabla temporal se crea automáticamente en sta.insert_log_message
		
		SELECT t.tabl_id
		INTO STRICT v_tipo
		FROM core.tablas t 
		WHERE t.tabla = p_empr_id||'-estados_herramientas'
		AND upper(t.valor) = upper(p_codigo)
		AND t.activo = true;
	
		RETURN v_tipo;
	EXCEPTION
		WHEN NO_DATA_FOUND THEN
			PERFORM sta.insert_log_message('WARNING', 'El estado de herramienta no se puede calcular ' || p_codigo, 'calcula_estado_herramienta');
			RAISE;
       
	END;
$function$
;

-- DROP FUNCTION sta.calcula_ubicacion_herramienta(varchar, int4);

CREATE OR REPLACE FUNCTION sta.calcula_ubicacion_herramienta(p_codigo character varying, p_empr_id integer)
 RETURNS character varying
 LANGUAGE plpgsql
AS $function$
	DECLARE 
		v_tipo varchar;
	BEGIN
		/** Calcula de acuerdo al codigo el id 
		 *  de ubicacion de herramienta
		 *  @author rruiz
		 */
		
		-- La tabla temporal se crea automáticamente en sta.insert_log_message
		
		SELECT t.tabl_id
		INTO STRICT v_tipo
		FROM core.tablas t 
		WHERE t.tabla = p_empr_id||'-ubicaciones_herramientas'
		AND upper(t.valor) = upper(p_codigo)
		AND t.activo = true;
	
		RETURN v_tipo;
	EXCEPTION
		WHEN NO_DATA_FOUND THEN
			PERFORM sta.insert_log_message('WARNING', 'La ubicacion de herramienta no se puede calcular ' || p_codigo, 'calcula_ubicacion_herramienta');
			RAISE;
       
	END;
$function$
;

-- DROP FUNCTION sta.calcula_proveedor(varchar, int4);

CREATE OR REPLACE FUNCTION sta.calcula_proveedor(p_codigo character varying, p_empr_id integer)
 RETURNS character varying
 LANGUAGE plpgsql
AS $function$
	DECLARE 
		v_tipo varchar;
	BEGIN
		/** Calcula de acuerdo al codigo el id 
		 *  de proveedor
		 *  @author rruiz
		 */
		
		-- La tabla temporal se crea automáticamente en sta.insert_log_message
		
		SELECT t.tabl_id
		INTO STRICT v_tipo
		FROM core.tablas t 
		WHERE t.tabla = p_empr_id||'-proveedores'
		AND upper(t.valor) = upper(p_codigo)
		AND t.activo = true;
	
		RETURN v_tipo;
	EXCEPTION
		WHEN NO_DATA_FOUND THEN
			PERFORM sta.insert_log_message('WARNING', 'El proveedor no se puede calcular ' || p_codigo, 'calcula_proveedor');
			RAISE;
       
	END;
$function$
;

-- DROP FUNCTION sta.calcula_unidad_medida(varchar, int4);

CREATE OR REPLACE FUNCTION sta.calcula_unidad_medida(p_codigo character varying, p_empr_id integer)
 RETURNS character varying
 LANGUAGE plpgsql
AS $function$
	DECLARE 
		v_tipo varchar;
	BEGIN
		/** Calcula de acuerdo al codigo el id 
		 *  de unidad de medida
		 *  @author rruiz
		 */
		
		-- La tabla temporal se crea automáticamente en sta.insert_log_message
		
		SELECT t.tabl_id
		INTO STRICT v_tipo
		FROM core.tablas t 
		WHERE t.tabla = p_empr_id||'-unidades_medida'
		AND upper(t.valor) = upper(p_codigo)
		AND t.activo = true;
	
		RETURN v_tipo;
	EXCEPTION
		WHEN NO_DATA_FOUND THEN
			PERFORM sta.insert_log_message('WARNING', 'La unidad de medida no se puede calcular ' || p_codigo, 'calcula_unidad_medida');
			RAISE;
       
	END;
$function$
;

-- DROP FUNCTION sta.calcula_categoria_articulo(varchar, int4);

CREATE OR REPLACE FUNCTION sta.calcula_categoria_articulo(p_codigo character varying, p_empr_id integer)
 RETURNS character varying
 LANGUAGE plpgsql
AS $function$
	DECLARE 
		v_tipo varchar;
	BEGIN
		/** Calcula de acuerdo al codigo el id 
		 *  de categoria de articulo
		 *  @author rruiz
		 */
		
		-- La tabla temporal se crea automáticamente en sta.insert_log_message
		
		SELECT t.tabl_id
		INTO STRICT v_tipo
		FROM core.tablas t 
		WHERE t.tabla = p_empr_id||'-categorias_articulos'
		AND upper(t.valor) = upper(p_codigo)
		AND t.activo = true;
	
		RETURN v_tipo;
	EXCEPTION
		WHEN NO_DATA_FOUND THEN
			PERFORM sta.insert_log_message('WARNING', 'La categoria de articulo no se puede calcular ' || p_codigo, 'calcula_categoria_articulo');
			RAISE;
       
	END;
$function$
;
