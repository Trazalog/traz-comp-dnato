# OAuth 2.1 PKCE Login Flow — E9-IDENT-04

## Resumen

Trazalog Dnato actúa como Authorization Server para el conector MCP de Claude.
Este documento describe el flujo completo desde que Claude inicia la autenticación
hasta que el JWT queda disponible para llamadas a herramientas.

---

## Diagrama de secuencia

```
Claude.ai          Dnato (OauthLogin)        Dnato (Oauth)       WSO2 MI         Bonita BPM
    |                      |                      |                  |                |
    |-- GET /oauth/authorize?client_id=...------->|                  |                |
    |   code_challenge, redirect_uri, state       |                  |                |
    |                      |                      |                  |                |
    |                      | valida params         |                  |                |
    |                      | guarda oauth_pending  |                  |                |
    |<-- 302 /oauth/login ------------------------|                  |                |
    |                      |                      |                  |                |
    |-- GET /oauth/login ------------------------>|                  |                |
    |<-- 200 formulario email/password ----------|                  |                |
    |                      |                      |                  |                |
    |-- POST /oauth/login/credentials ----------->|                  |                |
    |   email, password, oauth_csrf               |                  |                |
    |                      |                      |                  |                |
    |                      | checkLogin()          |                  |                |
    |                      | BPM::getUser() -------------------------------->         |
    |                      |<--------------------------------------- userIdBpm        |
    |                      |                      |                  |                |
    |                      | GET /memberships/xUserid/{id}/session/dd                 |
    |                      |--------------------------------->        |                |
    |                      |<-- [{name, displayName}, ...] ---|      |                |
    |                      |                      |                  |                |
    |   [si N=1 membresía] |                      |                  |                |
    |                      | OauthCode_model::store(code, ...)        |                |
    |<-- 302 redirect_uri?code=...&state=... -----|                  |                |
    |                      |                      |                  |                |
    |   [si N>1 membresías]|                      |                  |                |
    |<-- 302 /oauth/login/select-company ---------|                  |                |
    |                      |                      |                  |                |
    |-- GET /oauth/login/select-company ---------->|                  |                |
    |<-- 200 dropdown empresas ------------------|                  |                |
    |                      |                      |                  |                |
    |-- POST /oauth/login/select-company -------->|                  |                |
    |   empr_id, oauth_csrf                        |                  |                |
    |                      |                      |                  |                |
    |                      | chekEmpresa() valida membresía          |                |
    |                      | OauthCode_model::store(code, ...)        |                |
    |<-- 302 redirect_uri?code=...&state=... -----|                  |                |
    |                      |                      |                  |                |
    |-- POST /oauth/token ------------------------------------------------->          |
    |   code, code_verifier, client_id, redirect_uri                                  |
    |                      |                      |                  |                |
    |                      |                      | PKCE verify      |                |
    |                      |                      | JwtIssuer::issue()|               |
    |<-- {"access_token": "<JWT>", ...} ----------|                  |                |
```

---

## Endpoints OauthLogin

| Método | Ruta                          | Descripción                                              |
|--------|-------------------------------|----------------------------------------------------------|
| GET    | /oauth/login                  | Formulario de credenciales (Paso 1)                      |
| POST   | /oauth/login/credentials      | Valida email/password, consulta membresías               |
| GET    | /oauth/login/select-company   | Dropdown de empresa cuando el usuario tiene más de una   |
| POST   | /oauth/login/select-company   | Confirma empresa, emite el authorization code            |

### Parámetros OAuth esperados en GET /oauth/login

| Parámetro              | Requerido | Descripción                              |
|------------------------|-----------|------------------------------------------|
| client_id              | sí        | Debe estar en oauth_clients.php          |
| redirect_uri           | sí        | Debe estar en la whitelist del cliente   |
| response_type          | sí        | Siempre `code`                           |
| code_challenge         | sí        | SHA256(code_verifier) en base64url       |
| code_challenge_method  | sí        | Siempre `S256`                           |
| state                  | opcional  | Valor opaco del cliente (anti-CSRF)      |

---

## Comportamiento multi-empresa (Decisión P02 — Sección 6.8)

| Membresías | Comportamiento                                                            |
|------------|---------------------------------------------------------------------------|
| 0          | Error explícito: "El usuario no tiene empresa asignada en el sistema."    |
| 1          | Autoselección silenciosa — sin dropdown, el code se emite directamente    |
| > 1        | Redirección a Paso 2: dropdown para que el usuario elija                  |

La empresa seleccionada queda registrada en `seg.oauth_codes` (columna `empr_id`)
y se incluye como claim en el JWT emitido por `/oauth/token`.

---

## Consulta de membresías

Se usa el proxy WSO2 MI (decisión P03) en lugar de BPM::getMemeberships() directo:

```
GET REST_BPM/memberships/xUserid/{userIdBpm}/session/dd
```

`REST_BPM` se configura en `application/config/constants.php`:
- Desarrollo local: `http://localhost:8290/tools/bpm`
- Servidor compartido: `http://10.142.0.13:8280/tools/bpm`

Respuesta esperada:
```json
{
  "payload": [
    { "name": "123-GrupoEmpresa", "displayName": "Empresa XYZ" }
  ]
}
```

El campo `name` tiene formato `{empr_id}-{groupBpm}`. Se parsea con `explode('-', $name, 2)`.

---

## CSRF en formularios

La protección CSRF global de CodeIgniter está deshabilitada (para no afectar `/oauth/token`
que es llamado por clientes externos). OauthLogin implementa su propio token CSRF:

1. Al generar el formulario (GET): se crea `bin2hex(random_bytes(16))` → se guarda en
   `session['oauth_csrf']` y se inserta como `<input type="hidden" name="oauth_csrf">`.
2. En el POST: `hash_equals(session['oauth_csrf'], post['oauth_csrf'])` debe ser `true`.

---

## redirect_uri whitelist

Configurada en `application/config/oauth_clients.php`.

### Clientes registrados (MVP)

| client_id               | Nombre de pantalla | redirect_uri                              |
|-------------------------|--------------------|-------------------------------------------|
| trazalog-mcp-connector  | Claude             | https://claude.ai/api/mcp/auth_callback   |

### Cómo registrar un nuevo cliente

1. Agregar entrada en `application/config/oauth_clients.php`:
   ```php
   'mi-nuevo-cliente' => [
       'display_name'  => 'Mi App',
       'redirect_uris' => ['https://mi-app.example.com/oauth/callback'],
   ],
   ```
2. Agregar el mismo `client_id` como `ALLOWED_CLIENT_ID` en `application/controllers/Oauth.php`
   (actualmente soporta un único cliente; refactorizar si se necesitan múltiples).
3. Reiniciar PHP-FPM / Apache para limpiar opcache.

---

## Seguridad

- **PKCE S256**: El `code_challenge` se almacena en `seg.oauth_codes`. En `/oauth/token`,
  `hash('sha256', $code_verifier, true)` en base64url debe coincidir con el challenge guardado.
- **Code TTL**: Los codes expiran en 60 segundos (configurable en `OauthCode_model::CODE_TTL_SECONDS`).
- **Code single-use**: El campo `used_at` se marca al consumir; un code ya usado es rechazado.
- **redirect_uri binding**: El code se liga al redirect_uri usado en authorize; el token endpoint
  valida que el redirect_uri en el POST coincida.
- **chekEmpresa**: Al confirmar la empresa en Paso 2, se valida membresía en la BD local
  además de la lista en sesión (doble validación anti-tampering).

---

## Procedimiento de prueba end-to-end

```bash
# 1. Levantar ngrok apuntando al puerto de Dnato
ngrok http 80

# 2. Registrar connector en Claude.ai
#    Settings → Integrations → Custom connectors → Add
#    OAuth 2.0: Authorization URL = https://<ngrok>/oauth/authorize
#    Token URL = https://<ngrok>/oauth/token
#    Client ID = trazalog-mcp-connector
#    Scopes = (vacío o "openid")

# 3. Agregar la redirect_uri de Claude.ai en oauth_clients.php
#    (aparece en la pantalla de configuración del connector)

# 4. Conectar desde Claude.ai → sigue el flujo OAuth
#    Verificar en Dnato logs: application/logs/log-<fecha>.php

# 5. Casos a probar:
#    a. Usuario con 1 membresía → login fluye sin dropdown → JWT tiene empr_id correcto
#    b. Usuario con 2+ membresías → dropdown aparece → empr_id elegido queda en JWT
#    c. Usuario con 0 membresías → error claro en pantalla
#    d. Tool call de prueba → WSO2 valida JWT → inyecta empr_id en la petición a Bonita
```

---

## Archivos relacionados

| Archivo                                            | Descripción                                 |
|----------------------------------------------------|---------------------------------------------|
| `application/controllers/OauthLogin.php`           | Controller principal (E9-IDENT-04)          |
| `application/controllers/Oauth.php`                | Endpoints /authorize, /token, /jwks         |
| `application/config/oauth_clients.php`             | Whitelist de clientes y redirect_uri        |
| `application/views/oauth/login_step1.php`          | Vista Paso 1: credenciales                  |
| `application/views/oauth/login_step2.php`          | Vista Paso 2: selección de empresa          |
| `application/views/oauth/login_error.php`          | Vista de error                              |
| `application/models/OauthCode_model.php`           | Gestión de codes PKCE                       |
| `doc/identity/migrations/001_create_seg_oauth_codes.sql` | Migración BD (ejecutar antes de usar) |
| `doc/identity/token-issuance.md`                   | Documentación del JWT (E9-IDENT-03)         |
