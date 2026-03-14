# Fase 0.4 – Despliegue con estructura CAR correcta

## Estructura del CAR de ejemplo (ToolsAPIProject_1.0.0.car)

Se tomó como referencia el CAR generado desde el proyecto WSO2 en `/mnt/win/dev/git/traz-tools/_backend/api/ToolsAPIProject`, copiado en `development/ejemplos/ToolsAPIProject_1.0.0.car`.

### Estructura interna

- **Raíz del CAR** (sin carpeta META-INF):
  - `artifacts.xml` – Lista el artefacto raíz tipo `carbon/application` y sus dependencias.
  - `metadata.xml` – Misma estructura que `artifacts.xml` (dependencias).
  - `descriptor.xml` – Proyecto Carbon (`id`, `versionedDeployment`, etc.).
  - Carpetas por artefacto: `NombreArtifact_version/` con:
    - `artifact.xml` – Tipo (`synapse/api`, `service/dataservice`, `datasource/datasource`, `synapse/sequence`) y `<file>NombreArchivo-version.ext</file>`.
    - Archivo del artefacto (ej. `toolsCOREAPI-1.0.0.xml`, `COREDataService-1.0.0.dbs`).

### Tipos de artefacto usados

| Tipo en artifact.xml   | Uso                         |
|------------------------|-----------------------------|
| `carbon/application`   | Aplicación Carbon (raíz)    |
| `synapse/api`          | API REST                    |
| `service/dataservice`  | DataService (.dbs)          |
| `datasource/datasource`| DataSource                  |
| `synapse/sequence`     | Secuencia (ej. toolsFault)  |

---

## CARs generados para esta fase

### 1. COREToolsDataSources_1.0.0.car

- **Origen**: `development/datasources_car_build/`
- **Contenido**: ToolsDataSource (PostgreSQL), AssetPlannerDataSource (MySQL).
- **Desplegar primero** para que los DataServices tengan datasources.

### 2. COREToolsFase04_1.0.0.car (modificaciones fase 0.4)

- **Origen**: `development/car_minimal_fase04/`
- **Contenido**:
  - **toolsCOREAPI** – API con soporte de imagen (crear usuario).
  - **COREDataService** – DataService con parámetros de imagen.
  - **toolsFault** – Secuencia de fault.
- **No incluye datasources**; usa los desplegados por COREToolsDataSources.

### 3. ToolsAPIProject_1.0.0-fase04.car (CAR completo con cambios)

- **Origen**: `development/car_from_example_build/` (ejemplo con reemplazos).
- **Contenido**: Mismo que el CAR de ejemplo, pero con:
  - `toolsCOREAPI_1.0.0/toolsCOREAPI-1.0.0.xml` ← `development/toolsCOREApi.xml`
  - `COREDataService_1.0.0/COREDataService-1.0.0.dbs` ← `development/COREDataService.xml`
- **Uso**: Cuando se despliegue el proyecto completo (p. ej. sin COREToolsDataSources previo), usar este CAR. En ese caso no debe haber otro CAR con ToolsDataSource ya desplegado.

---

## Ubicaciones de WSO2 Micro Integrator

| Versión | Ruta |
|--------|------|
| **4.3.0** (doc actual) | `/home/rodolfo/dev/wso2mi-4.3.0` |
| **4.4.0** (plugin)     | `/home/rodolfo/.wso2-mi/micro-integrator/wso2mi-4.4.0` |
| **4.5.0** (plugin)     | `/home/rodolfo/.wso2-mi/micro-integrator/wso2mi-4.5.0` |

- **Inicio**: `bin/micro-integrator.sh`
- **CARs**: `repository/deployment/server/carbonapps/`
- **Logs**: `repository/logs/wso2carbon.log`

Para probar en 4.4 o 4.5, copiar los mismos CARs en la carpeta `carbonapps` de esa instalación.

---

## Orden de despliegue (recomendado)

1. **Quitar** artefactos manuales que puedan duplicar nombre:
   - `repository/deployment/server/dataservices/COREDataService.dbs` (y .xml si existe).
   - `repository/deployment/server/synapse-configs/default/api/toolsCOREApi.xml` (si existe).
2. **Dejar solo** en `carbonapps/`:
   - `COREToolsDataSources_1.0.0.car`
   - `COREToolsFase04_1.0.0.car`
3. **Reiniciar** WSO2 MI (opcional pero recomendado para un estado limpio) o esperar el ciclo de despliegue (~30 s).
4. **Comprobar** en `wso2carbon.log`:
   - `Successfully Deployed Carbon Application : COREToolsDataSources_1.0.0`
   - `Successfully Deployed Carbon Application : COREToolsFase04_1.0.0`

---

## Prueba del endpoint

```bash
# Sustituir host/puerto si no es local (ej. 10.142.0.13:8290)
curl -X POST http://localhost:8290/tools/core/usuario \
  -H "Content-Type: application/json" \
  -d '{
    "usuario": {
      "usernick": "test_fase04_1",
      "email": "test_fase04_1@test.com",
      "business": "1",
      "firstname": "Test",
      "lastname": "Fase04",
      "password": "password123",
      "role": "2",
      "status": "approved",
      "banned_users": "unban",
      "telefono": "1234567890",
      "dni": "12345678",
      "image_name": "foto.jpg",
      "image": "iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg=="
    },
    "bpmSession": null
  }'
```

Requiere VPN y bases de datos (PostgreSQL, MySQL AssetPlanner) accesibles según `doc/creacion-usuarios.md`.

---

## Resumen

- Se analizó la estructura del CAR de ejemplo y se replicó en:
  - CAR mínimo **COREToolsFase04** (solo API + COREDataService + toolsFault).
  - CAR de datasources **COREToolsDataSources**.
- Se desplegaron correctamente en WSO2 MI 4.3.0 tras eliminar artefactos manuales duplicados.
- Rutas de MI 4.4.0 y 4.5.0 documentadas para pruebas en versiones nuevas.
