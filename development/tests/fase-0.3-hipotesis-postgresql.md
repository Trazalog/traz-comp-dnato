# Hipótesis: Problema de Validación de Conexión PostgreSQL durante Despliegue del DataService

## 📋 Resumen Ejecutivo

**Hipótesis Principal**: El DataService `COREDataService` no se despliega porque WSO2 Micro Integrator valida la conexión a PostgreSQL durante el despliegue, y esta conexión falla con timeout, causando que el DataService se marque como "not valid" y el CAR revierta todos los artefactos desplegados.

## 🔍 Evidencia en los Logs

### Error Principal Identificado

```
[2026-02-15 14:09:46,484] ERROR {org.wso2.micro.integrator.dataservices.core.DBDeployer} 
- The COREDataService-1.0.0.dbs service, which is not valid, caused {1} 
DS Fault Message: El intento de conexión falló.
DS Code: CONNECTION_UNAVAILABLE_ERROR
```

### Stack Trace del Error

El error ocurre en la siguiente secuencia:

1. **DBDeployer intenta desplegar el DataService**:
   ```
   at org.wso2.micro.integrator.dataservices.core.DBDeployer.deploy(DBDeployer.java:209)
   at org.wso2.micro.integrator.dataservices.core.DBDeployer.createDBService(DBDeployer.java:819)
   ```

2. **WSO2 intenta crear la configuración del datasource**:
   ```
   at org.wso2.micro.integrator.dataservices.core.description.config.SQLCarbonDataSourceConfig.<init>(SQLCarbonDataSourceConfig.java:64)
   at org.wso2.micro.integrator.dataservices.core.description.config.SQLConfig.initSQLDataSource(SQLConfig.java:151)
   ```

3. **WSO2 intenta crear una conexión de prueba**:
   ```
   at org.wso2.micro.integrator.dataservices.core.description.config.SQLConfig.createConnection(SQLConfig.java:187)
   at org.apache.tomcat.jdbc.pool.ConnectionPool.createConnection(ConnectionPool.java:769)
   ```

4. **La conexión falla con timeout**:
   ```
   Caused by: java.net.SocketTimeoutException: Connect timed out
   at java.base/sun.nio.ch.NioSocketImpl.timedFinishConnect(NioSocketImpl.java:546)
   ```

5. **El CAR revierte todos los artefactos**:
   ```
   [2026-02-15 14:09:46,486] ERROR {org.wso2.micro.integrator.initializer.deployment.application.deployer.CappDeployer} 
   - Error occurred while deploying the Carbon application: COREToolsApplication_1.0.0.car. 
   Reverting successfully deployed artifacts in the CApp.
   ```

### Problemas Adicionales Identificados

#### 1. Error de Módulo OSGi
```
[2026-02-15 14:09:32,813] ERROR {Events.Framework} 
- FrameworkEvent ERROR org.osgi.framework.BundleException: 
Could not resolve module: org.postgresql.jdbc42 [172]
Unresolved requirement: Import-Package: javax.transaction.xa
```

**Análisis**: El driver PostgreSQL requiere el paquete `javax.transaction.xa` que no está disponible en el classpath de OSGi. Esto puede causar que el driver no funcione correctamente.

#### 2. Drivers PostgreSQL Encontrados
- `/home/rodolfo/dev/wso2mi-4.3.0/lib/postgresql-jdbc.jar` ✓
- `/home/rodolfo/dev/wso2mi-4.3.0/dropins/postgresql-jdbc.jar` ✓
- `/home/rodolfo/dev/wso2mi-4.3.0/dropins/postgresql_jdbc_1.0.0.jar` ✓

**Análisis**: Los drivers están presentes, pero el módulo OSGi no se resuelve correctamente.

#### 3. Datasource No Desplegado Manualmente
El directorio `/home/rodolfo/dev/wso2mi-4.3.0/repository/deployment/server/datasources` no existe, lo que significa que los datasources solo se despliegan desde el CAR.

## 🎯 Hipótesis Detallada

### Flujo del Problema

1. **WSO2 inicia el despliegue del CAR** `COREToolsApplication_1.0.0.car`
2. **Se despliegan los datasources** (`ToolsDataSource`, `AssetPlannerDataSource`) desde el CAR
3. **Se intenta desplegar el DataService** `COREDataService`
4. **El DBDeployer valida la conexión** a PostgreSQL durante el despliegue (comportamiento estándar de WSO2)
5. **La conexión falla** con `SocketTimeoutException: Connect timed out` porque:
   - PostgreSQL no está accesible desde WSO2 (VPN caída, firewall, servidor caído)
   - El driver PostgreSQL no se resuelve correctamente en OSGi (falta `javax.transaction.xa`)
   - El datasource no está completamente inicializado cuando el DataService intenta validarlo
6. **El DataService se marca como "not valid"** y el despliegue falla
7. **El CAR revierte todos los artefactos** desplegados (incluyendo el API que sí se había desplegado)
8. **Solo en despliegues posteriores** el API se despliega porque no depende de datasources

### Causas Probables (Orden de Probabilidad)

#### 1. **PostgreSQL No Accesible durante el Despliegue** (MÁS PROBABLE)
- **Evidencia**: `SocketTimeoutException: Connect timed out`
- **Causa**: La base de datos PostgreSQL en `10.142.0.13:5432` no está accesible desde el servidor WSO2 en el momento del despliegue
- **Posibles razones**:
  - VPN caída o no conectada
  - Firewall bloqueando el puerto 5432
  - Servidor PostgreSQL caído o pausado
  - Problemas de red temporal

#### 2. **Driver PostgreSQL No Funcional en OSGi** (PROBABLE)
- **Evidencia**: `Could not resolve module: org.postgresql.jdbc42 [172] Unresolved requirement: Import-Package: javax.transaction.xa`
- **Causa**: El driver PostgreSQL requiere el paquete `javax.transaction.xa` que no está disponible en el entorno OSGi de WSO2
- **Impacto**: Aunque el driver esté presente, no puede funcionar correctamente sin esta dependencia

#### 3. **Orden de Despliegue Incorrecto** (MENOS PROBABLE)
- **Evidencia**: No se encontraron logs de despliegue exitoso del datasource antes del error del DataService
- **Causa**: El DataService intenta validar la conexión antes de que el datasource esté completamente inicializado
- **Nota**: El orden en `artifacts.xml` parece correcto (datasources antes del DataService)

## 📚 Referencias a Documentación

Según la documentación de WSO2 y los patrones comunes:

1. **WSO2 DBDeployer valida conexiones durante el despliegue**: El `DBDeployer` intenta crear una conexión de prueba a cada datasource referenciado en el DataService durante el despliegue. Si esta conexión falla, el DataService se marca como "not valid" y no se despliega.

2. **Comportamiento de reversión del CAR**: Cuando un artefacto falla durante el despliegue de un CAR, WSO2 revierte todos los artefactos desplegados en ese intento para mantener la consistencia.

3. **Dependencias OSGi**: Los drivers JDBC en WSO2 deben ser bundles OSGi válidos. Si falta una dependencia como `javax.transaction.xa`, el módulo no se resuelve correctamente.

## ✅ Pruebas para Validar la Hipótesis

### Prueba 1: Verificar Conectividad a PostgreSQL
```bash
PGPASSWORD='!Password00' psql -h 10.142.0.13 -p 5432 -U postgres -d tools_prod_t -c "SELECT 1;"
```
**Resultado Esperado**: Si PostgreSQL está accesible, debería retornar `1`. Si no, confirmaría la causa #1.

### Prueba 2: Verificar Driver OSGi
Verificar si el driver PostgreSQL necesita dependencias adicionales o una versión específica compatible con OSGi.

### Prueba 3: Verificar Orden de Despliegue
Revisar los logs para confirmar que el datasource se despliega antes del DataService.

### Prueba 4: Desplegar Datasource Manualmente
Desplegar el datasource `ToolsDataSource` manualmente antes de desplegar el CAR para verificar si el problema es de timing.

## 🔧 Soluciones Propuestas

### Solución 1: Asegurar Conectividad a PostgreSQL
1. Verificar que la VPN esté conectada
2. Verificar que el servidor PostgreSQL esté corriendo
3. Verificar que no haya firewall bloqueando el puerto 5432
4. Probar la conexión manualmente desde el servidor WSO2

### Solución 2: Corregir Driver PostgreSQL OSGi
1. Verificar si el driver PostgreSQL necesita ser un bundle OSGi válido
2. Agregar la dependencia `javax.transaction.xa` si es necesario
3. Considerar usar una versión del driver PostgreSQL compatible con OSGi

### Solución 3: Deshabilitar Validación de Conexión (NO RECOMENDADO)
1. Modificar la configuración de WSO2 para deshabilitar la validación de conexión durante el despliegue
2. **Nota**: Esto puede causar problemas si el datasource no está configurado correctamente

## 📊 Conclusión

La hipótesis más probable es que **PostgreSQL no está accesible desde WSO2 durante el despliegue**, causando que la validación de conexión falle y el DataService no se despliegue. El error de OSGi con el driver PostgreSQL también puede estar contribuyendo al problema.

**Próximo Paso**: Verificar la conectividad a PostgreSQL y resolver el problema del driver OSGi antes de reintentar el despliegue.



