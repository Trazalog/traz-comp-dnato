-- =====================================================
-- BULKLOAD ARTICULOS ETAPAS CON LOGGING TEMPORAL
-- =====================================================

-- DROP FUNCTION sta.bulkload_articulos_etapas(varchar, int4);

CREATE OR REPLACE FUNCTION sta.bulkload_articulos_etapas(p_archivo character varying, p_empr_id integer)
 RETURNS TEXT
 LANGUAGE plpgsql
AS $function$
	DECLARE 
	v_etapa_aux varchar;
	v_entrada_aux varchar;
 	v_salida_aux varchar;
 	v_producto_aux varchar;
 	v_etap_id int4;
	v_registros_procesados INTEGER := 0;
	v_registros_exitosos INTEGER := 0;
	v_registros_fallidos INTEGER := 0;
	v_errores TEXT := '';
	cur_artietapcsv CURSOR FOR SELECT
						ltrim(rtrim(upper(ae."Etapa")))
						, ltrim(rtrim(upper(ae."Entrada"))) 
						, ltrim(rtrim(upper(ae."Producto"))) 
						, ltrim(rtrim(upper(ae."Salida"))) 
						FROM sta.articulos_etapas ae 
						WHERE ae.procesado = FALSE;

 	BEGIN
		/**
		 * Carga de un archivo csv
		 * 	con columnas "Etapa","Entrada", "Producto","Salida"
		 * los datos en prd.etapas_materiales (entrada), prd.etapas_productos y prd.etapas_salidas
		 * la primer fila de csv debe tener estos nombres no importa el orden
		 */
		
		-- La tabla temporal se crea automáticamente en sta.insert_log_message
		
		PERFORM sta.insert_log_message('INFO', 'Cargando archivo ' || p_archivo || ' con empresa ' || p_empr_id, 'bulkload_articulos_etapas');

		BEGIN

			PERFORM sta.insert_log_message('INFO', 'antes Archivo cargado', 'bulkload_articulos_etapas');
		   EXECUTE 
			FORMAT('COPY sta.articulos_etapas ("Etapa","Entrada","Producto","Salida") FROM %s WITH CSV HEADER'
		    ,p_archivo);
	       
		    PERFORM sta.insert_log_message('INFO', 'Archivo cargado', 'bulkload_articulos_etapas');
       
		    PERFORM sta.insert_log_message('INFO', 'Eliminando registros invalidos ' || p_empr_id, 'bulkload_articulos_etapas');

			DELETE FROM sta.articulos_etapas ar 
			WHERE ar."Etapa" IS  NULL
			OR (ar."Entrada" = '0' AND ar."Producto" = '0'AND ar."Salida" ='0')
			OR (ar."Entrada" IS NULL AND ar."Producto" IS NULL AND ar."Salida" IS NULL);

			PERFORM sta.insert_log_message('INFO', 'Insertando registros', 'bulkload_articulos_etapas');
			open cur_artietapcsv;
			loop
				fetch cur_artietapcsv into v_etapa_aux,v_entrada_aux,v_producto_aux,v_salida_aux;
				exit when NOT FOUND;
	    		
				PERFORM sta.insert_log_message('INFO', 'Obtengo etapa ' || v_etapa_aux, 'bulkload_articulos_etapas');
	
				SELECT etap_id 
				INTO STRICT v_etap_id
				FROM prd.etapas e 
				WHERE UPPER(E.nombre )= v_etapa_aux
				AND e.empr_id = p_empr_id;
			
				PERFORM sta.insert_log_message('INFO', 'Procesando registro ' || v_etapa_aux || '(' || v_etap_id || ')-' || v_entrada_aux || '-' || v_producto_aux || '-' || v_salida_aux, 'bulkload_articulos_etapas');      		


				IF v_entrada_aux IS NOT NULL AND v_entrada_aux != '' AND v_entrada_aux !='0' THEN 
				
					PERFORM sta.insert_log_message('INFO', 'Insertando en etapas_materiales ' || v_etapa_aux || '(' || v_etap_id || ')-' || v_entrada_aux, 'bulkload_articulos_etapas');      		
					BEGIN
						
						INSERT INTO prd.etapas_materiales
						(arti_id 
						,etap_id )
						SELECT a.arti_id
						,v_etap_id
						FROM alm.alm_articulos a 
						WHERE a.barcode = v_entrada_aux
						AND a.empr_id =p_empr_id;
					EXCEPTION
						WHEN unique_violation THEN
							PERFORM sta.insert_log_message('INFO', 'Registro existente, obviando', 'bulkload_articulos_etapas');      		
					END;
				
					END IF;
			
				IF v_producto_aux IS NOT NULL AND v_producto_aux != '' AND v_producto_aux !='0' THEN 
				
					BEGIN 
						PERFORM sta.insert_log_message('INFO', 'Insertando en etapas_productos ' || v_etapa_aux || '(' || v_etap_id || ')-' || v_producto_aux, 'bulkload_articulos_etapas');      		
						INSERT INTO prd.etapas_productos
						(arti_id 
						,etap_id )
						SELECT a.arti_id
						,v_etap_id
						FROM alm.alm_articulos a 
						WHERE a.barcode = v_producto_aux
						AND a.empr_id =p_empr_id;

					EXCEPTION
						WHEN unique_violation THEN
							PERFORM sta.insert_log_message('INFO', 'Registro existente, obviando', 'bulkload_articulos_etapas');      		
					END;

				END IF;
			
				IF v_salida_aux IS NOT NULL AND v_salida_aux != '' AND v_salida_aux !='0' THEN 

					BEGIN 
						PERFORM sta.insert_log_message('INFO', 'Insertando en etapas_salidas ' || v_etapa_aux || '(' || v_etap_id || ')-' || v_salida_aux, 'bulkload_articulos_etapas');      		
						INSERT INTO prd.etapas_salidas 
						(arti_id 
						,etap_id )
						SELECT a.arti_id
						,v_etap_id
						FROM alm.alm_articulos a 
						WHERE a.barcode = v_salida_aux
						AND a.empr_id =p_empr_id;
					EXCEPTION
						WHEN unique_violation THEN
							PERFORM sta.insert_log_message('INFO', 'Registro existente, obviando', 'bulkload_articulos_etapas');      		
					END;

				END IF;
		
	
			end loop;
			CLOSE cur_artietapcsv;
                    PERFORM sta.insert_log_message('INFO', 'Registros cargados, marcando batch como procesado', 'bulkload_articulos_etapas');

			UPDATE
				sta.articulos_etapas 
			SET
				procesado = TRUE
				, fec_proceso = now()
			WHERE
				procesado = FALSE;

                    PERFORM sta.insert_log_message('SUCCESS', 'Carga bulk terminada exitosamente', 'bulkload_articulos_etapas');
			
		EXCEPTION	
			when others then
				/* Borro el actual CSV con error*/
				DELETE FROM sta.articulos_etapas ar 
				WHERE ar.procesado = FALSE;
                            PERFORM sta.insert_log_message('ERROR', 'Error general: ' || SQLERRM, 'bulkload_articulos_etapas');
				raise EXCEPTION 'BULKARET: error al cargar batch de articulos %: %', sqlstate,sqlerrm;

		END;
		
		-- Retornar solo el resultado del procesamiento (sin logs)
		-- Los logs los maneja el procedimiento padre
		RETURN 'Procesados: ' || v_registros_procesados || 
			' | Exitosos: ' || v_registros_exitosos || ' | Fallidos: ' || v_registros_fallidos || 
			' | Errores: ' || COALESCE(v_errores, 'Ninguno');
	END;
$function$
;
