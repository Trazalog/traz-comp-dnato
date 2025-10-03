-- =====================================================
-- BULKLOAD HERRAMIENTAS CON LOGGING TEMPORAL
-- =====================================================

-- DROP FUNCTION sta.bulkload_herramientas(varchar, int4);

CREATE OR REPLACE FUNCTION sta.bulkload_herramientas(p_archivo character varying, p_empr_id integer)
 RETURNS TEXT
 LANGUAGE plpgsql
AS $function$
	DECLARE 
		v_codigo_aux varchar;
		v_marca_aux varchar;
		v_modelo_aux varchar;
		v_tipo_aux varchar;
		v_descripcion_aux varchar;
		v_panol_aux int;
		v_registros_procesados INTEGER := 0;
		v_registros_exitosos INTEGER := 0;
		v_registros_fallidos INTEGER := 0;
		v_errores TEXT := '';

		cur_herrcsv CURSOR FOR SELECT
							ltrim(rtrim(upper(he.codigo)))
							, sta.calcula_marca_herramienta(ltrim(rtrim(he.marca)),p_empr_id)
							, ltrim(rtrim(he.modelo))
							, ltrim(rtrim(he.tipo))
							, ltrim(rtrim(he.descripcion))
							, sta.calcula_panol(ltrim(rtrim(he.panol)),p_empr_id)					
							FROM sta.herramientas he
							WHERE he.procesado = FALSE;
 	BEGIN
		/**
		 * Carga de un archivo csv
		 * con columnas codigo, marca ,modelo ,tipo ,descripcion, panol 
		 * los datos en pan.herramienas
		 * la primer fila de csv debe tener estos nombres no importa el orden
		 */
		
		-- La tabla temporal se crea automáticamente en sta.insert_log_message
		
        PERFORM sta.insert_log_message('INFO', 'Cargando archivo ' || p_archivo || ' con empresa ' || p_empr_id, 'bulkload_herramientas');

		BEGIN
	       

	        EXECUTE 
			FORMAT('COPY sta.herramientas (codigo, marca ,modelo ,tipo ,descripcion, panol ) FROM %s WITH CSV HEADER'
		    ,p_archivo);

                PERFORM sta.insert_log_message('INFO', 'Archivo cargado', 'bulkload_herramientas');


		    PERFORM sta.insert_log_message('INFO', 'Eliminando registros sin codigo o descripcion con empresa ' || p_empr_id, 'bulkload_herramientas');

			DELETE FROM sta.herramientas he 
			WHERE he.codigo IS  NULL
			OR he.descripcion IS NULL;

                    PERFORM sta.insert_log_message('INFO', 'Insertando registros', 'bulkload_herramientas');

			open cur_herrcsv;
			loop
				fetch cur_herrcsv into v_codigo_aux , v_marca_aux ,v_modelo_aux ,v_tipo_aux ,v_descripcion_aux ,v_panol_aux ;
				exit when NOT FOUND;
	    		
                            PERFORM sta.insert_log_message('INFO', 'Procesando registro ' || v_codigo_aux || '-' || v_marca_aux || '-' || v_modelo_aux || '-' || v_tipo_aux || '-' || v_descripcion_aux || '-' || v_panol_aux, 'bulkload_herramientas');
      		
				insert into pan.herramientas
						(codigo,
						marca,
						modelo,
						tipo,
						descripcion,
						pano_id,
						empr_id,
						usuario_app)
				values(v_codigo_aux 
						,v_marca_aux 
						,v_modelo_aux 
						,v_tipo_aux 
						,v_descripcion_aux 
						,v_panol_aux 
						,p_empr_id
						,'sta.bulkload_herramientas');

			end loop;
			CLOSE cur_herrcsv;
                    PERFORM sta.insert_log_message('INFO', 'Registros cargados, marcando batch como procesado', 'bulkload_herramientas');

			UPDATE
				sta.herramientas
			SET
				procesado = TRUE
				, fec_proceso = now()
			WHERE
				procesado = FALSE;

                    PERFORM sta.insert_log_message('SUCCESS', 'Carga bulk terminada exitosamente', 'bulkload_herramientas');
			
		EXCEPTION	
			when others then
				/* Borro el actual CSV con error*/
				DELETE FROM sta.herramientas he 
				WHERE he.procesado = FALSE;
                            PERFORM sta.insert_log_message('ERROR', 'Error general: ' || SQLERRM, 'bulkload_herramientas');
				raise EXCEPTION 'BULHERR: error al cargar batch de articulos %: %', sqlstate,sqlerrm;

		END;
		
		-- Retornar solo el resultado del procesamiento (sin logs)
		-- Los logs los maneja el procedimiento padre
		RETURN 'Procesados: ' || v_registros_procesados || 
			' | Exitosos: ' || v_registros_exitosos || ' | Fallidos: ' || v_registros_fallidos || 
			' | Errores: ' || COALESCE(v_errores, 'Ninguno');
	END;
$function$
;
