-- =====================================================
-- BULKLOAD ARTICULOS CON LOGGING TEMPORAL - CORREGIDO
-- =====================================================

-- DROP FUNCTION sta.bulkload_articulos(varchar, int4);

CREATE OR REPLACE FUNCTION sta.bulkload_articulos(p_archivo character varying, p_empr_id integer)
 RETURNS TEXT
 LANGUAGE plpgsql
AS $function$
	DECLARE 
	v_codigo_aux varchar;
	v_descripcion_aux varchar;
 	v_umedida_aux varchar;
 	v_tipo_aux varchar;
 	v_punto_pedido_aux int;
	v_registros_procesados INTEGER := 0;
	v_registros_exitosos INTEGER := 0;
	v_registros_fallidos INTEGER := 0;
	v_errores TEXT := '';
	cur_articsv CURSOR FOR SELECT
						ltrim(rtrim(upper(ar.codigo)))
						, ltrim(rtrim(ar.descripcion))
						, sta.calcula_unidad_medida(ltrim(rtrim(ar.unidad_medida)),p_empr_id)
						, sta.calcula_tipo_articulo(ltrim(rtrim(ar.tipo)),p_empr_id)
						, ar.punto_pedido
						FROM sta.articulos ar
						WHERE ar.procesado = FALSE;
 	BEGIN
		/**
		 * Carga de un archivo csv
		 * 	con columnas "Código","Descripción", "U. Medida"
		 * los datos en alm_articulos
		 * la primer fila de csv debe tener estos nombres no importa el orden
		 */
		
		-- La tabla temporal se crea automáticamente en sta.insert_log_message
		
		PERFORM sta.insert_log_message('INFO', 'Cargando archivo ' || p_archivo || ' con empresa ' || p_empr_id, 'bulkload_articulos');

		begin
			PERFORM sta.insert_log_message('INFO', 'antes de Archivo cargado', 'bulkload_articulos');

		   EXECUTE 
			FORMAT('COPY sta.articulos (codigo,descripcion,unidad_medida,tipo, punto_pedido) FROM %L WITH CSV HEADER'
		    ,p_archivo);
	       
			PERFORM sta.insert_log_message('INFO', 'Archivo cargado', 'bulkload_articulos');
       
			PERFORM sta.insert_log_message('INFO', 'Eliminando registros sin codigo o descripcion con empresa ' || p_empr_id, 'bulkload_articulos');

			DELETE FROM sta.articulos ar 
			WHERE ar.codigo IS  NULL
			OR ar.descripcion IS NULL;

			PERFORM sta.insert_log_message('INFO', 'Insertando registros', 'bulkload_articulos');
			
			open cur_articsv;
			loop
				fetch cur_articsv into v_codigo_aux,v_descripcion_aux,v_umedida_aux,v_tipo_aux, v_punto_pedido_aux;
				exit when NOT FOUND;
	    		
	    		v_registros_procesados := v_registros_procesados + 1;
	    		
	    		begin
	    			INSERT INTO alm.alm_articulos 
	    			(empr_id, codigo, descripcion, unidad_medida, tipo, punto_pedido, activo, fec_alta, usuario_alta)
	    			VALUES 
	    			(p_empr_id, v_codigo_aux, v_descripcion_aux, v_umedida_aux, v_tipo_aux, v_punto_pedido_aux, true, now(), 'BULKLOAD');
	    			
	    			v_registros_exitosos := v_registros_exitosos + 1;
	    			
	    		exception
	    			when others then
	    				v_registros_fallidos := v_registros_fallidos + 1;
	    				v_errores := v_errores || 'Error en codigo ' || v_codigo_aux || ': ' || SQLERRM || '; ';
	    				
	    				PERFORM sta.insert_log_message('ERROR', 'Error insertando articulo ' || v_codigo_aux || ': ' || SQLERRM, 'bulkload_articulos');
	    		end;
	    		
	    		UPDATE sta.articulos 
	    		SET procesado = TRUE
	    		WHERE codigo = v_codigo_aux;
	    		
			end loop;
			close cur_articsv;
			
			PERFORM sta.insert_log_message('SUCCESS', 'Procesamiento completado. Procesados: ' || v_registros_procesados || 
				', Exitosos: ' || v_registros_exitosos || ', Fallidos: ' || v_registros_fallidos, 'bulkload_articulos');
			
		exception
			when others then
				PERFORM sta.insert_log_message('ERROR', 'Error general: ' || SQLERRM, 'bulkload_articulos');
				RAISE;
		end;
		
		-- Retornar solo el resultado del procesamiento (sin logs)
		-- Los logs los maneja el procedimiento padre
		RETURN 'Procesados: ' || v_registros_procesados || 
			' | Exitosos: ' || v_registros_exitosos || ' | Fallidos: ' || v_registros_fallidos || 
			' | Errores: ' || COALESCE(v_errores, 'Ninguno');
		
	END;
$function$
;
