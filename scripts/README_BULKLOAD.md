# 🔄 Funcionalidad de Carga Masiva - Trazalog Tools

## 📋 Descripción

Este módulo permite realizar cargas masivas de datos desde archivos Excel hacia el sistema Trazalog Tools, utilizando stored procedures dinámicos y la integración con WSO2 DataServices.

## 🏗️ Arquitectura

### Componentes PHP
- **Controller**: `application/controllers/Bulkload.php`
- **Model**: `application/models/Bulkload_model.php`
- **View**: `application/views/bulkload/index.php`

### Integración WSO2
- **DataService**: `COREDataService.dbs`
- **Endpoint**: `/carga_masiva/archivo` (POST)
- **Endpoint**: `/entidades_negocio` (GET)

### Base de Datos
- **Tabla**: `sta.entidades_negocio`
- **Logs**: `bulkload_logs` (opcional)

## 🚀 Instalación

### 1. Archivos PHP
Los archivos PHP ya están creados en el proyecto:
- ✅ `application/controllers/Bulkload.php`
- ✅ `application/models/Bulkload_model.php`
- ✅ `application/views/bulkload/index.php`

### 2. Configuración
Las constantes ya están agregadas en `application/config/constants.php`:
- ✅ `COREDataService_URL`
- ✅ `BULKLOAD_STAGING_DIR`
- ✅ `BULKLOAD_MAX_FILE_SIZE`
- ✅ `BULKLOAD_ALLOWED_EXTENSIONS`
- ✅ `BULKLOAD_TIMEOUT`

### 3. Directorios
- ✅ `bulkload_stage_files/` (creado con permisos 755)
- ✅ `.htaccess` (protección del directorio)

## 🔧 Configuración WSO2

### 1. Desplegar DataService
1. Copiar `scripts/wso2/COREDataService.dbs` al servidor WSO2
2. Configurar el datasource `AssetPlannerDataSource`
3. Desplegar el servicio

### 2. Scripts de Base de Datos
Ejecutar los siguientes scripts en PostgreSQL en el orden indicado:

1. **`scripts/sta/sta_tables.sql`**: Estructura de tablas
2. **`scripts/sta/sta_entidades_negocio.sql`**: Tabla de entidades de negocio
3. **`scripts/sta/bulkload_articulos.sql`**: Stored procedure para artículos
4. **`scripts/sta/bulkload_herramientas.sql`**: Stored procedure para herramientas
5. **`scripts/sta/bulkload_alm_lotes.sql`**: Stored procedure para lotes
6. **`scripts/sta/bulkload_articulos_etapas.sql`**: Stored procedure para etapas

### 3. Configurar Datasource
```xml
<config id="AssetPlannerDataSource">
   <property name="org.wso2.ws.dataservice.driver">org.postgresql.Driver</property>
   <property name="org.wso2.ws.dataservice.protocol">jdbc:postgresql://localhost:5432/trazalog_db</property>
   <property name="org.wso2.ws.dataservice.user">your_username</property>
   <property name="org.wso2.ws.dataservice.password">your_password</property>
</config>
```

### 3. Verificar Endpoints
- **GET**: `http://your-wso2-server:8280/services/COREDataService/entidades_negocio`
- **POST**: `http://your-wso2-server:8280/services/COREDataService/carga_masiva/archivo`

## 📊 Base de Datos

### 1. Tabla de Entidades
```sql
CREATE TABLE sta.entidades_negocio (
    id SERIAL PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,
    stored_procedure VARCHAR(255) NOT NULL,
    template TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### 2. Datos de Ejemplo
```sql
INSERT INTO sta.entidades_negocio (nombre, stored_procedure, template) VALUES
('Artículos', 'sta.sp_carga_masiva_articulos', 'Plantilla para carga masiva de artículos. Incluir columnas: código, descripción, precio, categoría'),
('Clientes', 'sta.sp_carga_masiva_clientes', 'Plantilla para carga masiva de clientes. Incluir columnas: CUIT, razón social, dirección, teléfono'),
('Proveedores', 'sta.sp_carga_masiva_proveedores', 'Plantilla para carga masiva de proveedores. Incluir columnas: CUIT, razón social, dirección, teléfono');
```

### 3. Menú (Opcional)
```sql
-- Ejecutar scripts/menu/insert_bulkload_menu.sql
-- Esto agrega la opción "Carga Masiva" al menú "Operaciones"
```

## 🧪 Testing

### 1. Verificar Funcionalidad
1. Acceder a `/bulkload` en el navegador
2. Verificar que se muestre el formulario
3. Seleccionar una entidad de negocio
4. Descargar template
5. Subir archivo Excel
6. Verificar procesamiento

### 2. Verificar Logs
```bash
# Ver logs de CodeIgniter
tail -f application/logs/log-YYYY-MM-DD.php

# Ver logs de WSO2
tail -f /path/to/wso2/logs/wso2carbon.log
```

### 3. Verificar Archivos
```bash
# Ver archivos temporales
ls -la bulkload_stage_files/

# Verificar permisos
ls -la bulkload_stage_files/.htaccess
```

## 🔒 Seguridad

### 1. Validaciones Implementadas
- ✅ Verificación de autenticación
- ✅ Validación de tipos de archivo
- ✅ Control de acceso por roles
- ✅ Sanitización de entrada
- ✅ Protección del directorio de archivos

### 2. Logs de Seguridad
- ✅ Registro de todas las operaciones
- ✅ Captura de IP del usuario
- ✅ Timestamp de operaciones
- ✅ Estado de éxito/fallo

## 🚨 Troubleshooting

### Error: "No se pueden obtener entidades de negocio"
- Verificar conexión a WSO2
- Verificar que el DataService esté desplegado
- Verificar configuración de `COREDataService_URL`

### Error: "No se puede convertir Excel a CSV"
- Verificar que el archivo sea válido
- Verificar permisos del directorio `bulkload_stage_files`
- Verificar que PhpSpreadsheet esté instalado (opcional)

### Error: "No se puede enviar al dataservice"
- Verificar que el stored procedure exista
- Verificar que `empr_id` esté en la sesión
- Verificar logs de WSO2

## 📈 Monitoreo

### 1. Métricas de Uso
- Total de cargas realizadas
- Cargas exitosas vs fallidas
- Tiempo promedio de procesamiento
- Usuarios más activos

### 2. Logs de Auditoría
- Usuario que realizó la carga
- Entidad de negocio procesada
- Archivo original y procesado
- Resultado del procesamiento
- Timestamp de la operación

## 🔄 Mantenimiento

### 1. Limpieza Automática
```php
// Ejecutar periódicamente para limpiar logs antiguos
$this->Bulkload_model->limpiarLogsAntiguos(90); // 90 días
```

### 2. Backup de Configuración
- Respaldar `sta.entidades_negocio`
- Respaldar configuración de WSO2
- Respaldar archivos de constantes

## 📞 Soporte

### Equipo de Desarrollo
- **Backend**: [@dev1](mailto:dev1@trazalog.com)
- **WSO2**: [@wso2-admin](mailto:wso2-admin@trazalog.com)
- **DBA**: [@dba](mailto:dba@trazalog.com)

### Documentación Adicional
- [README Principal](../README.md)
- [Documentación de WSO2](https://docs.wso2.com/)
- [CodeIgniter User Guide](https://codeigniter.com/user_guide/)

---

**Última actualización**: Enero 2024  
**Versión**: 1.0.0  
**Estado**: ✅ Implementado y listo para testing






