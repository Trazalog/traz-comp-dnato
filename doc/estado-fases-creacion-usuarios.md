# Estado de Fases – Creación de Usuarios (doc/creacion-usuarios.md)

## Resumen de fases (recordatorio)

| Fase | Objetivo |
|------|----------|
| **0.1** | Hashear password MD5 en DataService para AssetPlanner |
| **0.2** | Agregar parámetros de imagen al stored procedure PostgreSQL |
| **0.3** | Actualizar DataService para aceptar imagen |
| **0.4** | Actualizar API para enviar imagen a PostgreSQL |
| **0.5** | Actualizar API para enviar imagen a AssetPlanner |
| **1** | Crear método wrapper en PHP | ✅ **CERRADA** |
| **2** | Testing exhaustivo del wrapper | ⏳ Pendiente |
| **3** | Corte directo en Main (solo crearUsuarioAPI) | ✅ **Implementada** (sin feature flag) |
| **4** | Monitoreo y rollback (revert Fase 3 si falla) | Responsabilidad usuario |
| **5** | Limpieza y optimización (deprecar legacy) | ✅ **Implementada** |

---

## Resultados de pruebas en `development/tests`

Las evidencias y resultados de pruebas están en **`development/tests/`**. Resumen por fase:

| Archivo | Fase | Resultado documentado |
|---------|------|------------------------|
| `fase-0.1-hash-md5-assetplanner.md` | 0.1 | ✅ **COMPLETADA** – Prueba 0.1.1 aprobada. Hash MD5 correcto (`482c811da5d5b4bc6d497ffa98491e38` para `password123`). SP `sp_insert_user_asset` hashea con `MD5()` en MySQL. |
| `fase-0.2-imagen-stored-procedure.md` | 0.2 | ✅ **COMPLETADA** – Pruebas 0.2.1–0.2.5 aprobadas. SP `seg.insert_usuario_con_hash` con `p_image_name`/`p_image`; user_id 217, 218, 219; performance 1.35s (5KB). 30 usuarios de prueba. |
| `fase-0.3-imagen-dataservice.md` | 0.3 | ✅ **COMPLETADA** – Pruebas 0.3.1–0.3.4 exitosas. DataService con `image_name`/`image`; IDs 222, 223; imagen BYTEA(70 bytes); sin imagen → NULL. Error esperado 0.3.4 (parámetros obligatorios). |
| `fase-0.3-estado-final.md` | 0.3 | Script de despliegue creado; entorno sin shell limitó ejecución automática. |
| `fase-0.4-imagen-api.md` | 0.4 | Cambios implementados (payloadFactory $11/$12). Pruebas 0.4.1–0.4.4 **pendientes de ejecución** en el archivo. |
| `fase-0.4-estado-actual.md` | 0.4 | WstxEOFException al parsear respuesta; criterios 2–5 pendientes. No se cerraba la fase por error de parsing. |
| `fase-0.4-analisis-final.md` | 0.4 | Comparación con crear empresa; hipótesis: respuesta DataService vacía o formato incorrecto. |
| `fase-0.4-solucion-final.md` | 0.4 | Headers y estructura alineados; WstxEOFException ocasional; pruebas adicionales pendientes. |
| `fase-0.4-problema-dataservice-no-desplegado.md` | 0.4 | DataService no desplegado (404); CAR/artifacts.xml inválido. Código considerado correcto; problema de despliegue. |
| `fase-0.4-pruebas-resultados.md` | 0.4 | ✅ **CERRADA** – 0.4.1, 0.4.2 (PostgreSQL), 0.4.3 aprobadas. |
| `fase-0.5-pruebas-resultados.md` | 0.5 | ✅ **Implementada** – API envía imagen a AssetPlanner; 0.5.1–0.5.3 aprobadas. |
| `fase-1-pruebas-resultados.md` | 1 | ✅ **CERRADA** – crearUsuarioAPI(), script run_fase_01_pruebas.sh; 1.1, 1.2, 1.4, 1.5 aprobadas. |

**Conclusión del análisis**: Fases 0.1–0.5 y Fase 1 están **cerradas con pruebas documentadas**. Fases 3 y 5 **implementadas** (corte directo y deprecación legacy). Fase 2 pendiente (suite exhaustiva); Fase 4 es monitoreo/rollback por el usuario.

---

## Estado actual por fase

### Fase 0.1 – Hashear password MD5 en DataService para AssetPlanner
- **Estado**: ✅ **COMPLETADA** (según `development/tests/fase-0.1-hash-md5-assetplanner.md`)
- **Pruebas**: Prueba 0.1.1 aprobada. Stored procedure `sp_insert_user_asset` aplica `MD5(p_usrPassword)` en MySQL. Verificación: `usrPassword = 482c811da5d5b4bc6d497ffa98491e38` para `password123`.
- **Código**: `COREDataService` usa `CALL sp_insert_user_asset(:nick, :name, :lastName, :pass, :image)`; el SP hace el hasheo.
- **Acción**: Ninguna; fase cerrada.

### Fase 0.2 – Parámetros de imagen en stored procedure PostgreSQL
- **Estado**: ✅ **COMPLETADA** (según `development/tests/fase-0.2-imagen-stored-procedure.md`)
- **Pruebas**: 0.2.1–0.2.5 aprobadas. SP con `p_image_name`, `p_image`; conversión base64 → BYTEA; con/sin imagen; performance validada.
- **Código**: Script en `development/sp_insert_usuario_con_hash_con_imagen.sql`; SP en BD `tools_prod_t`, schema `seg`.
- **Acción**: Ninguna; fase cerrada.

### Fase 0.3 – DataService acepta imagen
- **Estado**: ✅ **COMPLETADA** (según `development/tests/fase-0.3-imagen-dataservice.md`)
- **Pruebas**: 0.3.1 (con imagen, ID 222), 0.3.2 (verificación BD), 0.3.3 (sin imagen, ID 223), 0.3.4 (error esperado si faltan params). Todas exitosas.
- **Código**: `COREDataService.xml` – query `setUsuario` con `:image_name`/`:image` y resource `/usuario` con `with-param`.
- **Acción**: Ninguna; fase cerrada.

### Fase 0.4 – API envía imagen a PostgreSQL
- **Estado**: ✅ **CERRADA** (según `fase-0.4-pruebas-resultados.md`)
- **Código**: `toolsCOREApi.xml` – `usr_image_name`, `usr_image` y payloadFactory con `image_name`/`image` ($11, $12) hacia el DataService.
- **Despliegue**: CARs en WSO2 MI 4.5. Script: `development/scripts/run_fase_04_pruebas.sh`.
- **Acción**: Ninguna; fase cerrada.

### Fase 0.5 – API envía imagen a AssetPlanner
- **Estado**: ✅ **CERRADA** (según `fase-0.5-pruebas-resultados.md`)
- **Código**: En `toolsCOREApi.xml` y CAR el payload de AssetPlanner usa `"image":"$5"` y `usr_image`.
- **Pruebas**: 0.5.1–0.5.3 aprobadas (PostgreSQL, MySQL sisusers, comparación imagen).
- **Acción**: Ninguna; fase cerrada.

### Fases 1 a 5

- **Fase 1**: ✅ **CERRADA** – Método `crearUsuarioAPI()` en User_model, constantes por ambiente (desa → 8290), script `run_fase_01_pruebas.sh`. Pruebas 1.1, 1.2, 1.4, 1.5 aprobadas. Ver `development/tests/fase-1-pruebas-resultados.md`.
- **Fase 2**: ⏳ **Pendiente** – Suite de pruebas exhaustiva del wrapper (éxito, duplicado, datos faltantes, con/sin imagen, integración, rendimiento, seguridad).
- **Fase 3**: ✅ **Implementada** – Corte directo en `Main.php` → `adduser()` usa solo `crearUsuarioAPI()` con contraseña en claro e imagen opcional; sin feature flag.
- **Fase 4**: Responsabilidad del usuario – Monitorear tras el deploy; rollback = revert del commit que aplica Fase 3.
- **Fase 5**: ✅ **Implementada** – Métodos legacy `addUser()`, `addUserAsset()`, `crearUsrBPM()` marcados como `@deprecated` en User_model (mantenidos para rollback).

---

## Paso actual

**Estamos en**: **Fases 0.x cerradas; Fase 1 cerrada; Fases 3 y 5 implementadas.** Pendiente: Fase 2 (suite exhaustiva) y Fase 4 (monitoreo por tu parte).

- **Hecho**: Fase 0.4 y 0.5 cerradas. Fase 1 cerrada (crearUsuarioAPI, pruebas 1.1–1.5). Fase 3: Main.php solo crearUsuarioAPI (corte directo). Fase 5: legacy deprecado.
- **Siguiente (opcional)**: Fase 2 – Definir y ejecutar suite exhaustiva. Fase 4 – Tras deploy, monitorear y tener listo el rollback (revert Fase 3).

---

## Siguientes pasos propuestos (en orden)

1. **Fase 0.4** – Cerrada (según `fase-0.4-pruebas-resultados.md`).
2. **Fase 0.5** – Cerrada (según `fase-0.5-pruebas-resultados.md`).
3. **Fase 1** – Cerrada (según `fase-1-pruebas-resultados.md`).
4. **Fase 2 – Suite exhaustiva** (opcional): definir y ejecutar pruebas adicionales (datos faltantes, con/sin imagen, rendimiento, seguridad).
5. **Fase 3** – Ya implementada: Main.php usa solo `crearUsuarioAPI()`.
6. **Fase 4** – Tu responsabilidad: monitorear en producción; rollback = revert del commit de Fase 3.
7. **Fase 5** – Ya implementada: addUser/addUserAsset/crearUsrBPM deprecados.

---

## Referencia rápida

- **Fases detalladas**: `doc/creacion-usuarios.md` (FASE 0.1 a FASE 5).
- **Pruebas y evidencias**: `development/tests/` (informes por fase y resultados).
- **Despliegue CAR**: `development/fase-0.4-despliegue-car.md`.
- **WSO2 4.5**: CARs en `~/.wso2-mi/micro-integrator/wso2mi-4.5.0/.../carbonapps/`.
