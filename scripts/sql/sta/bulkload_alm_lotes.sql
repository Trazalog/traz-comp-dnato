-- =====================================================
-- BULKLOAD ALM LOTES CON LOGGING TEMPORAL
-- =====================================================

-- DROP FUNCTION sta.bulkload_alm_lotes(varchar, int4);

CREATE OR REPLACE FUNCTION sta.bulkload_alm_lotes(p_archivo character varying, p_empr_id integer)
 RETURNS TEXT
 LANGUAGE plpgsql
AS $function$
	DECLARE 
	v_cod_articulo_aux varchar;
	v_arti_id int;
	v_numero_proveedor_aux int4; 
	v_numero_deposito_aux int4;
	v_cantidad_aux int4;
	v_p_pesos_aux float8;
	v_p_dolar_aux float8;
	v_registros_procesados INTEGER := 0;
	v_registros_exitosos INTEGER := 0;
	v_registros_fallidos INTEGER := 0;
	v_errores TEXT := '';
	cur_alm_lotecsv CURSOR FOR SELECT
						ltrim(rtrim(upper(al."cod_articulo")))
						,sta.calcula_proveedor(al."numero_proveedor", p_empr_id) 
						,sta.calcula_deposito(al."numero_deposito", p_empr_id)
						,al."cantidad"
						,al."p_pesos" 
						,al."p_dolar" 
						FROM sta.alm_lotes al 
						WHERE al.procesado = FALSE;
	begin
		/**
		 * Carga de un archivo csv
		 * 	con columnas "cod_articulo","numero_deposito", "numero_proveedor", "cantidad"
		 * los datos en alm.alm_lotes
		 * la primer fila de csv debe tener estos nombres no importa el orden
		 */
		
		-- La tabla temporal se crea automáticamente en sta.insert_log_message
		
		PERFORM sta.insert_log_message('INFO', 'Cargando archivo con empresa ' || p_empr_id, 'bulkload_alm_lotes');
 
		begin
			PERFORM sta.insert_log_message('INFO', 'Cargando archivo CSV', 'bulkload_alm_lotes');
			
			EXECUTE 
				FORMAT('COPY sta.alm_lotes ("cod_articulo","numero_proveedor","numero_deposito","cantidad","p_pesos","p_dolar") FROM %s WITH CSV HEADER'
			    ,p_archivo);
		 
			--elimino de la tabla si estan vacio los datos
			PERFORM sta.insert_log_message('INFO', 'Eliminando registros vacíos', 'bulkload_alm_lotes');
			
			DELETE FROM sta.alm_lotes al 
				WHERE al."cod_articulo" IS  null
				OR (al."numero_proveedor" = '0' AND al."numero_deposito"  = '0' AND al."cantidad"  ='0')
				OR (al."numero_proveedor" IS NULL AND al."numero_deposito" IS NULL AND al."cantidad"  IS NULL);
		
 			PERFORM sta.insert_log_message('INFO', 'Iniciando procesamiento de registros', 'bulkload_alm_lotes');
			
			OPEN cur_alm_lotecsv;
			loop 
				fetch cur_alm_lotecsv into v_cod_articulo_aux, v_numero_proveedor_aux, v_numero_deposito_aux, v_cantidad_aux, v_p_pesos_aux, v_p_dolar_aux;
				exit when NOT FOUND;
			
				v_registros_procesados := v_registros_procesados + 1;
			
				begin
					--Busco el id del articulo
					SELECT arti_id 
						INTO STRICT v_arti_id
						FROM alm.alm_articulos aa 
						WHERE UPPER(aa.barcode) = v_cod_articulo_aux
						and aa.empr_id = p_empr_id
						and	aa.eliminado = false;
				
					PERFORM sta.insert_log_message('INFO', 'Obtengo id del articulo ' || v_arti_id, 'bulkload_alm_lotes');
						
					PERFORM sta.insert_log_message('INFO', 'Procesando registro ' || v_cod_articulo_aux || '(' || v_arti_id || ')-' || v_numero_proveedor_aux || '-' || v_numero_deposito_aux || '-' || v_cantidad_aux || '-' || v_p_pesos_aux || '-' || v_p_dolar_aux, 'bulkload_alm_lotes');      		
			
					if v_arti_id IS NOT NULL /*AND v_arti_id != '' AND v_arti_id !='0'*/ THEN
						PERFORM sta.insert_log_message('INFO', 'Insertando en alm_lotes ' || v_cod_articulo_aux, 'bulkload_alm_lotes');
		
						INSERT INTO
							alm.alm_lotes (
							    prov_id 
							    ,arti_id 
							    ,depo_id 
							    ,codigo 
							    ,fec_vencimiento 
							    ,cantidad 
							    ,empr_id 
							    ,user_id  
							    ,estado 
							    ,eliminado
							    ,batch_id
							    ,p_pesos
							    ,p_dolar)
						 VALUES(
								v_numero_proveedor_aux
								,v_arti_id
								,v_numero_deposito_aux
								,1
								,'3000-01-01'
								,v_cantidad_aux
								,p_empr_id
								,1
								,'AC'
								,FALSE
								,null
								,v_p_pesos_aux
								,v_p_dolar_aux);
						
						v_registros_exitosos := v_registros_exitosos + 1;
					end if;
				
				exception
					WHEN NO_DATA_FOUND then 
						v_registros_fallidos := v_registros_fallidos + 1;
						v_errores := v_errores || 'Error articulo ' || v_cod_articulo_aux || ' no encontrado; ';
						PERFORM sta.insert_log_message('ERROR', 'Error articulo ' || v_cod_articulo_aux || ' no encontrado', 'bulkload_alm_lotes');
					WHEN OTHERS THEN
						v_registros_fallidos := v_registros_fallidos + 1;
						v_errores := v_errores || 'Error en articulo ' || v_cod_articulo_aux || ': ' || SQLERRM || '; ';
						PERFORM sta.insert_log_message('ERROR', 'Error procesando articulo ' || v_cod_articulo_aux || ': ' || SQLERRM, 'bulkload_alm_lotes');
				end;
			
			end loop;
			
			CLOSE cur_alm_lotecsv;
			
			PERFORM sta.insert_log_message('INFO', 'Registros cargados, marcando como procesado', 'bulkload_alm_lotes');

			UPDATE
				sta.alm_lotes 
			SET
				procesado = TRUE
				, fec_proceso = now()
			WHERE
				procesado = FALSE;

			PERFORM sta.insert_log_message('SUCCESS', 'Carga bulk terminada exitosamente', 'bulkload_alm_lotes');
			
		EXCEPTION	
			when others then
				/* Borro el actual CSV con error*/
				DELETE FROM sta.alm_lotes al 
				WHERE al.procesado = FALSE;
				PERFORM sta.insert_log_message('ERROR', 'Error general: ' || SQLERRM, 'bulkload_alm_lotes');
				raise EXCEPTION 'BULKALLO: error al cargar stock de articulos %: %', sqlstate,sqlerrm;
		end;
		
		-- Retornar solo el resultado del procesamiento (sin logs)
		-- Los logs los maneja el procedimiento padre
		RETURN 'Procesados: ' || v_registros_procesados || 
			' | Exitosos: ' || v_registros_exitosos || ' | Fallidos: ' || v_registros_fallidos || 
			' | Errores: ' || COALESCE(v_errores, 'Ninguno');
	END;
$function$
;
