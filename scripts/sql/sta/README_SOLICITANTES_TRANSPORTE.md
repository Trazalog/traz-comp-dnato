# 🚛 Solicitantes de Transporte - Carga Masiva

## 📋 Descripción

Esta funcionalidad permite realizar cargas masivas de solicitantes de transporte desde archivos CSV hacia el sistema Trazalog Tools, específicamente en la tabla `log.solicitantes_transporte`.

## 🏗️ Arquitectura

### Tablas Involucradas
- **`sta.solicitantes_transporte`**: Tabla temporal para la carga masiva
- **`log.solicitantes_transporte`**: Tabla final donde se almacenan los datos procesados

### Stored Procedures
- **`sta.bulkload_solicitantes_transporte`**: Procedimiento principal de carga masiva
- **`sta.determinar_rubro_por_actividad`**: Determina el rubro basado en la actividad
- **`sta.determinar_tipo_solicitante`**: Determina el tipo basado en residuos
- **`sta.obtener_departamento_por_domicilio`**: Obtiene departamento por domicilio
- **`sta.obtener_provincia_san_juan`**: Retorna ID de provincia San Juan

## 📊 Estructura del CSV

El archivo CSV debe tener las siguientes columnas (en cualquier orden):

```csv
EVALUADOR,GRANDES GENERADOR DE RSU,Nº DE REGISTRO,AÑO,EXPEDIENTE Nº,Nº DE CUIT,DOMICILIO,ACTIVIDAD,tipo res 1,tipo res 2,tipo res 3,tipo res 4,tipo res 5
```

### Campos Obligatorios
- **GRANDES GENERADOR DE RSU** → `razon_social`
- **Nº DE CUIT** → `cuit`
- **DOMICILIO** → `domicilio`

### Campos Opcionales
- **EVALUADOR** → `evaluador`
- **Nº DE REGISTRO** → `num_registro`
- **AÑO** → `anio`
- **EXPEDIENTE Nº** → `expediente_num`
- **ACTIVIDAD** → `actividad`
- **tipo res 1-5** → `tipo_res_1` a `tipo_res_5` (marcar con 'x')

## 🔧 Instalación

### 1. Ejecutar Scripts en Orden
```bash
# 1. Crear tabla temporal
psql -h 10.142.0.13 -U postgres -d tools_prod_t -f sta_solicitantes_transporte_table.sql

# 2. Crear funciones auxiliares
psql -h 10.142.0.13 -U postgres -d tools_prod_t -f sta_solicitantes_aux_functions.sql

# 3. Crear stored procedure principal
psql -h 10.142.0.13 -U postgres -d tools_prod_t -f bulkload_solicitantes_transporte.sql

# 4. Agregar a entidades de negocio
psql -h 10.142.0.13 -U postgres -d tools_prod_t -f insert_solicitantes_entidad.sql
```

### 2. O Ejecutar Todo Junto
```bash
psql -h 10.142.0.13 -U postgres -d tools_prod_t -f install_solicitantes_transporte.sql
```

## 🧪 Pruebas

### Ejecutar Test
```bash
psql -h 10.142.0.13 -U postgres -d tools_prod_t -f test_solicitantes_transporte.sql
```

## 📋 Mapeo de Datos

### Rubros por Actividad
- **MINERA/EXPLOTACION/PROSPECCION** → `RUBRO_MINERO`
- **ALIMENTARIA/ALIMENTACION/DULCES/CONSERVAS** → `RUBRO_ALIMENTARIO`
- **CONSTRUCCION/CONSTRUCTORA** → `RUBRO_CONSTRUCCION`
- **COMERCIAL/CENTRO DE COMPRAS** → `RUBRO_COMERCIAL`
- **INDUSTRIAL/FABRICA/PLANTA** → `RUBRO_INDUSTRIAL`
- **HOTELERIA/HOTEL** → `RUBRO_HOTELERO`
- **AGRICOLA/AGRO/EMPAQUE** → `RUBRO_AGRICOLA`
- **Otros** → `RUBRO_GENERAL`

### Tipos de Solicitante por Residuos
- **0 residuos marcados** → `TIPO_SOLICITANTE_GENERAL`
- **1 residuo marcado** → `TIPO_SOLICITANTE_ESPECIFICO`
- **2-3 residuos marcados** → `TIPO_SOLICITANTE_MULTIPLE`
- **4-5 residuos marcados** → `TIPO_SOLICITANTE_COMPLEJO`

### Departamentos por Domicilio
- **CAPITAL/SAN JUAN** → ID 1
- **RAWSON** → ID 2
- **RIVADAVIA** → ID 3
- **POCITO** → ID 4
- **CHIMBAS** → ID 5
- **SARMIENTO** → ID 6
- **Otros** → ID 1 (Capital por defecto)

## ⚠️ Consideraciones Importantes

1. **Foreign Keys**: Asegúrate de que existan los registros en las tablas referenciadas:
   - `core.tablas` (para rubr_id y tist_id)
   - `core.departamentos` (para depa_id)
   - `alm.alm_proveedores` (para prov_id)
   - `core.empresas` (para empr_id)

2. **Valores por Defecto**:
   - `zona_id`: NULL
   - `info_id`: NULL
   - `user_id`: 'BULKLOAD'
   - `eliminado`: 0

3. **Logging**: Todos los pasos se registran en `temp_log_messages`

## 🔍 Monitoreo

### Verificar Procesamiento
```sql
-- Ver registros procesados
SELECT 
    COUNT(*) as total,
    COUNT(CASE WHEN procesado = TRUE THEN 1 END) as procesados,
    COUNT(CASE WHEN procesado = FALSE THEN 1 END) as pendientes
FROM sta.solicitantes_transporte;

-- Ver registros en tabla final
SELECT COUNT(*) FROM log.solicitantes_transporte WHERE usuario_app = 'BULKLOAD';
```

### Ver Logs
```sql
-- Ver logs de procesamiento
SELECT * FROM temp_log_messages 
WHERE procedure_name = 'bulkload_solicitantes_transporte'
ORDER BY timestamp;
```
