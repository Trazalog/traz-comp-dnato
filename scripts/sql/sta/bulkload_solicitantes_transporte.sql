-- =====================================================
-- BULKLOAD SOLICITANTES TRANSPORTE CON LOGGING TEMPORAL
-- =====================================================

-- DROP FUNCTION sta.bulkload_solicitantes_transporte(varchar, int4);

CREATE OR REPLACE FUNCTION sta.bulkload_solicitantes_transporte(p_archivo character varying, p_empr_id integer)
 RETURNS TEXT
 LANGUAGE plpgsql
AS $function$
	DECLARE 
	v_evaluador_aux varchar;
	v_razon_social_aux varchar;
	v_num_registro_aux varchar;
	v_anio_aux int;
	v_expediente_num_aux varchar;
	v_cuit_aux varchar;
	v_domicilio_aux varchar;
	v_actividad_aux varchar;
	v_tipo_res_1_aux varchar;
	v_tipo_res_2_aux varchar;
	v_tipo_res_3_aux varchar;
	v_tipo_res_4_aux varchar;
	v_tipo_res_5_aux varchar;
	v_rubr_id_aux varchar;
	v_tist_id_aux varchar;
	v_zona_id_aux int;
	v_depa_id_aux int;
	v_prov_id_aux int;
	v_user_id_aux varchar;
	v_info_id_aux int;
	v_registros_procesados INTEGER := 0;
	v_registros_exitosos INTEGER := 0;
	v_registros_fallidos INTEGER := 0;
	v_errores TEXT := '';
	cur_solicitantes_csv CURSOR FOR SELECT
						ltrim(rtrim(st.evaluador))
						, ltrim(rtrim(st.razon_social))
						, ltrim(rtrim(st.num_registro))
						, st.anio
						, ltrim(rtrim(st.expediente_num))
						, ltrim(rtrim(st.cuit))
						, ltrim(rtrim(st.domicilio))
						, ltrim(rtrim(st.actividad))
						, ltrim(rtrim(st.tipo_res_1))
						, ltrim(rtrim(st.tipo_res_2))
						, ltrim(rtrim(st.tipo_res_3))
						, ltrim(rtrim(st.tipo_res_4))
						, ltrim(rtrim(st.tipo_res_5))
						FROM sta.solicitantes_transporte st
						WHERE st.procesado = FALSE;
 	BEGIN
		
		/* 
		 * Carga de un archivo csv con solicitantes de transporte
		 * Columnas: EVALUADOR,GRANDES GENERADOR DE RSU,Nº DE REGISTRO,AÑO,EXPEDIENTE Nº,Nº DE CUIT,DOMICILIO,ACTIVIDAD,tipo res 1,tipo res 2,tipo res 3,tipo res 4,tipo res 5
		 * Los datos se insertan en log.solicitantes_transporte
		 * La primer fila del csv debe tener estos nombres no importa el orden
		 */
		
		-- La tabla temporal se crea automáticamente en sta.insert_log_message
		
		PERFORM sta.insert_log_message('INFO', 'Cargando archivo de solicitantes transporte ' || p_archivo || ' con empresa ' || p_empr_id, 'bulkload_solicitantes_transporte');

		begin
			PERFORM sta.insert_log_message('INFO', 'Iniciando carga de archivo CSV', 'bulkload_solicitantes_transporte');

		   EXECUTE 
			FORMAT('COPY sta.solicitantes_transporte (evaluador,razon_social,num_registro,anio,expediente_num,cuit,domicilio,actividad,tipo_res_1,tipo_res_2,tipo_res_3,tipo_res_4,tipo_res_5) FROM %L WITH CSV HEADER'
		    ,p_archivo);
	       
			PERFORM sta.insert_log_message('INFO', 'Archivo CSV cargado exitosamente', 'bulkload_solicitantes_transporte');
       
			PERFORM sta.insert_log_message('INFO', 'Eliminando registros sin datos obligatorios', 'bulkload_solicitantes_transporte');

			-- Eliminar registros sin datos obligatorios
			DELETE FROM sta.solicitantes_transporte st 
			WHERE st.razon_social IS NULL OR TRIM(st.razon_social) = ''
			OR st.cuit IS NULL OR TRIM(st.cuit) = ''
			OR st.domicilio IS NULL OR TRIM(st.domicilio) = '';

			PERFORM sta.insert_log_message('INFO', 'Iniciando procesamiento de registros', 'bulkload_solicitantes_transporte');
			
			open cur_solicitantes_csv;
			loop
				fetch cur_solicitantes_csv into v_evaluador_aux,v_razon_social_aux,v_num_registro_aux,v_anio_aux,v_expediente_num_aux,v_cuit_aux,v_domicilio_aux,v_actividad_aux,v_tipo_res_1_aux,v_tipo_res_2_aux,v_tipo_res_3_aux,v_tipo_res_4_aux,v_tipo_res_5_aux;
				exit when NOT FOUND;
	    		
	    		v_registros_procesados := v_registros_procesados + 1;
	    		
	    		begin
	    			-- Determinar rubro basado en la actividad
	    			v_rubr_id_aux := sta.determinar_rubro_por_actividad(v_actividad_aux);
	    			
	    			-- Determinar tipo de solicitante basado en los tipos de residuo
	    			v_tist_id_aux := sta.determinar_tipo_solicitante(v_tipo_res_1_aux, v_tipo_res_2_aux, v_tipo_res_3_aux, v_tipo_res_4_aux, v_tipo_res_5_aux);
	    			
	    			-- Obtener zona por defecto (puede ser NULL)
	    			v_zona_id_aux := NULL;
	    			
	    			-- Obtener departamento por domicilio (puede ser NULL)
	    			v_depa_id_aux := sta.obtener_departamento_por_domicilio(v_domicilio_aux);
	    			
	    			-- Obtener provincia (San Juan por defecto)
	    			v_prov_id_aux := sta.obtener_provincia_san_juan();
	    			
	    			-- Usuario por defecto
	    			v_user_id_aux := 'BULKLOAD';
	    			
	    			-- Info ID por defecto
	    			v_info_id_aux := NULL;
	    			
	    			-- Insertar en la tabla final
	    			INSERT INTO log.solicitantes_transporte 
	    			(razon_social, cuit, domicilio, num_registro, usuario_app, zona_id, rubr_id, tist_id, 
	    			 eliminado, depa_id, prov_id, user_id, empr_id, info_id)
	    			VALUES 
	    			(v_razon_social_aux, v_cuit_aux, v_domicilio_aux, v_num_registro_aux, 'BULKLOAD', 
	    			 v_zona_id_aux, v_rubr_id_aux, v_tist_id_aux, 0, v_depa_id_aux, v_prov_id_aux, 
	    			 v_user_id_aux, p_empr_id, v_info_id_aux);
	    			
	    			v_registros_exitosos := v_registros_exitosos + 1;
	    			
	    		exception
	    			when others then
	    				v_registros_fallidos := v_registros_fallidos + 1;
	    				v_errores := v_errores || 'Error en CUIT ' || v_cuit_aux || ': ' || SQLERRM || '; ';
	    				
	    				PERFORM sta.insert_log_message('ERROR', 'Error insertando solicitante ' || v_cuit_aux || ': ' || SQLERRM, 'bulkload_solicitantes_transporte');
	    		end;
	    		
	    		-- Marcar como procesado
	    		UPDATE sta.solicitantes_transporte 
	    		SET procesado = TRUE, fec_proceso = NOW()
	    		WHERE cuit = v_cuit_aux AND razon_social = v_razon_social_aux;
	    		
			end loop;
			close cur_solicitantes_csv;
			
			PERFORM sta.insert_log_message('SUCCESS', 'Procesamiento completado. Procesados: ' || v_registros_procesados || 
				', Exitosos: ' || v_registros_exitosos || ', Fallidos: ' || v_registros_fallidos, 'bulkload_solicitantes_transporte');
			
		exception
			when others then
				PERFORM sta.insert_log_message('ERROR', 'Error general: ' || SQLERRM, 'bulkload_solicitantes_transporte');
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
