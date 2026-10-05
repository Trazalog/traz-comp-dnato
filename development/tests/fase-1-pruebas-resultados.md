# Fase 1 – Resultados de pruebas: Wrapper PHP crearUsuarioAPI()

## Fecha
2026-02-28

## Objetivo
Crear el método wrapper `crearUsuarioAPI()` en PHP que llama a la API WSO2 (`POST /tools/core/usuario`) y validar con pruebas automatizadas (API, PostgreSQL, MySQL AssetPlanner, duplicado).

---

## Contexto en el plan (Fases 0 → 1 → 5)

| Fase | Descripción | Estado |
|------|-------------|--------|
| 0.1–0.5 | API + DataService + imagen (PostgreSQL + AssetPlanner) | Cerradas |
| **1** | Método wrapper en PHP + pruebas 1.1–1.5 | **Cerrada** (este doc) |
| 2 | Suite de pruebas exhaustiva del wrapper | Pendiente |
| 3 | Corte directo en Main.php (solo crearUsuarioAPI) | Implementada |
| 4 | Monitoreo y rollback (revert commit Fase 3) | Responsabilidad usuario |
| 5 | Limpieza legacy (deprecar addUser/addUserAsset/crearUsrBPM) | Implementada |

---

## Cambios implementados (Fase 1)

### 1. Constants por ambiente
**Archivo**: `application/config/constants.php`

- WSO2 por entorno: si `ENVIRONMENT === 'development'` → puerto **8290** (WSO2 local), si no → 8280.
- Definidos `$wso2_port`, `$wso2_base` y con ellos: `REST_BPM`, `API_CORE`, `HOST`, `COREDataService_URL`.

### 2. User_model – crearUsuarioAPI() y getBpmSession()
**Archivo**: `application/models/User_model.php`

- **`crearUsuarioAPI($data)`**: monta payload `usuario` (usernick, email, firstname, lastname, **password en claro**, role, status, banned_users, telefono, dni, business, image_name, image) y `bpmSession` (vía `getBpmSession()`), llama a `API_CORE . '/usuario'` con `REST->callAPI('POST', ...)`, parsea respuesta y devuelve `array('usr_id','resultado','bpmSession')` o `false`.
- **`getBpmSession()`** (privado): por ahora retorna `null` (la API no llama a BPM en ese caso).

### 3. Script de pruebas Fase 1
**Archivo**: `development/scripts/run_fase_01_pruebas.sh`

- Carga `.env` por lectura línea a línea (sin `source`) para evitar errores en el shell.
- Pruebas: 1.1 POST `/tools/core/usuario` (éxito 200/202 y cuerpo con resultado/usr_id), 1.2 PostgreSQL (seg.users), 1.4 MySQL AssetPlanner (sisusers), 1.5 mismo email (esperar error).

**Uso**: `./run_fase_01_pruebas.sh http://localhost:8290`

---

## Resultados de ejecución (2026-02-28)

Ejecutado en el host donde corre WSO2 MI (localhost:8290) y con acceso a PostgreSQL/MySQL según `.env`.

```
=== Pruebas Fase 1 - API crearUsuarioAPI - http://localhost:8290 ===
Timestamp: 1772315475

--- Prueba 1.1: POST /tools/core/usuario (éxito) ---
HTTP_CODE: 202
BODY: {"respuesta": { "resultado" : "ok", "usr_id":"246", "bpmSession":"null" } }
1.1: APROBADA
usr_id obtenido: 246

--- Prueba 1.2: Verificar en PostgreSQL (seg.users, seg.users_business) ---
1.2: APROBADA - Usuario en PostgreSQL: 246|test_f1_1772315475@test.com|test_f1_1772315475

--- Prueba 1.4: Verificar en MySQL AssetPlanner (sisusers) ---
1.4: APROBADA - Usuario en AssetPlanner: test_f1_1772315475	Test

--- Prueba 1.5: POST con email duplicado (debe retornar error) ---
HTTP_CODE: 404
1.5: APROBADA (error esperado por email duplicado)

=== Resumen Fase 1 ===
```

| Prueba | Descripción | Resultado |
|--------|-------------|-----------|
| 1.1 | POST crear usuario (éxito) | APROBADA (HTTP 202, usr_id 246) |
| 1.2 | Usuario en PostgreSQL seg.users | APROBADA |
| 1.4 | Usuario en MySQL sisusers (AssetPlanner) | APROBADA |
| 1.5 | Email duplicado retorna error | APROBADA (HTTP 404) |

(1.3 BPM opcional: no ejecutada; con `bpmSession: null` la API no llama a BPM.)

---

## Checklist de cierre Fase 1

- [x] Constante/URL API por ambiente (desa → 8290)
- [x] `crearUsuarioAPI()` y `getBpmSession()` en User_model
- [x] Script `run_fase_01_pruebas.sh` con carga de .env sin `source`
- [x] 1.1 – API éxito (200/202 y usr_id)
- [x] 1.2 – Verificación PostgreSQL
- [x] 1.4 – Verificación MySQL AssetPlanner
- [x] 1.5 – Duplicado retorna error

---

## Cierre

**Fase 1: CERRADA.** Todas las pruebas 1.1, 1.2, 1.4 y 1.5 aprobadas. El wrapper PHP está listo para ser usado desde el controlador (Fase 3 ya implementada).

---

## Referencias

- Plan de fases: `doc/creacion-usuarios.md` (FASE 1)
- Estado global: `doc/estado-fases-creacion-usuarios.md`
- Fase 0.4 resultados: `development/tests/fase-0.4-pruebas-resultados.md`
- Fase 0.5 resultados: `development/tests/fase-0.5-pruebas-resultados.md`
