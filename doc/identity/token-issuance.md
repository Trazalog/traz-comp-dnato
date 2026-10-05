# Emisión de Tokens JWT en Dnato

**Ticket**: E9-IDENT-03  
**Decisión arquitectónica**: TAD-IDENT-P01 (Sección 6.8, TRAZALOG_v3_MCP_ARCHITECTURE.md)  
**Resumen**: Dnato es el Authorization Server. Emite JWTs firmados con RS256 conteniendo el claim `empr_id`. WSO2 MCP Gateway valida el token en cada request y extrae/inyecta `empr_id` downstream.

---

## 1. Estructura del JWT

### Header
```json
{
  "alg": "RS256",
  "typ": "JWT",
  "kid": "dnato-rs256-v1"
}
```

### Payload (claims)
| Claim       | Tipo    | Descripción                                                            |
|-------------|---------|------------------------------------------------------------------------|
| `sub`       | string  | `usernick` del usuario (coincide con `userName` en Bonita)             |
| `email`     | string  | Email del usuario en `seg.users`                                       |
| `empr_id`   | integer | ID de empresa resuelta en el login — **el claim de tenant crítico**    |
| `role`      | string  | Rol numérico del usuario en el sistema                                 |
| `userIdBpm` | string  | ID del usuario en Bonita (para filtrar tareas)                         |
| `groupBpm`  | string  | Nombre del grupo Bonita sin prefijo numérico (ej. `"EmpresaABC"`)      |
| `iss`       | string  | `"trazalog-dnato"` — identifica al emisor                              |
| `aud`       | string  | `"trazalog-mcp"` — audiencia del token                                 |
| `iat`       | integer | Unix timestamp de emisión                                              |
| `exp`       | integer | Unix timestamp de expiración (`iat + 3600`)                            |

### Ejemplo de payload decodificado
```json
{
  "iss": "trazalog-dnato",
  "aud": "trazalog-mcp",
  "iat": 1748440000,
  "exp": 1748443600,
  "sub": "jperez",
  "email": "jperez@empresaabc.com",
  "empr_id": 7,
  "role": "2",
  "userIdBpm": "42",
  "groupBpm": "EmpresaABC"
}
```

---

## 2. Flujo OAuth 2.1 Authorization Code + PKCE

### 2.1 Diagrama

```
Cliente MCP               Dnato (AuthServer)            seg.oauth_codes
     |                          |                              |
     |-- GET /oauth/authorize -->|                              |
     |   client_id, redirect_uri|                              |
     |   response_type=code     |                              |
     |   code_challenge (S256)  |                              |
     |   state                  |                              |
     |                          |-- chequea sesión PHP ------->|
     |                          |   (si no hay sesión:         |
     |                          |    redirige a /main/login)   |
     |                          |-- genera code aleatorio ---->|
     |                          |-- almacena code + email + -->|
     |                          |   empr_id + code_challenge   |
     |<-- redirect redirect_uri?code=...&state=... ------------|
     |
     |-- POST /oauth/token ----->|
     |   code, code_verifier     |                              |
     |   client_id, redirect_uri |                              |
     |                           |-- consume(code) ----------->|
     |                           |<-- row (email, empr_id...) -|
     |                           |-- verifica PKCE S256         |
     |                           |-- getUserInfo(email)         |
     |                           |-- JwtIssuer::issue()         |
     |<-- { access_token: JWT } -|
```

### 2.2 Generación del code_verifier y code_challenge (lado cliente)

```python
import os, hashlib, base64

# Generar verifier (mínimo 43 caracteres, máximo 128)
code_verifier = base64.urlsafe_b64encode(os.urandom(32)).rstrip(b'=').decode()

# Calcular challenge S256
digest = hashlib.sha256(code_verifier.encode()).digest()
code_challenge = base64.urlsafe_b64encode(digest).rstrip(b'=').decode()
```

### 2.3 Ejemplo curl paso a paso

```bash
# Variables
CLIENT_ID="trazalog-mcp-connector"
REDIRECT_URI="https://mcp-client.example.com/callback"
CODE_VERIFIER="$(openssl rand -base64 32 | tr -d '=+/' | head -c 43)"
CODE_CHALLENGE="$(echo -n "$CODE_VERIFIER" | sha256sum | xxd -r -p | base64 | tr -d '=' | tr '+/' '-_')"
STATE="$(openssl rand -hex 8)"

# 1. Iniciar flujo (en browser o webview)
open "https://dnato.trazalog.com/oauth/authorize?\
client_id=$CLIENT_ID\
&redirect_uri=$REDIRECT_URI\
&response_type=code\
&code_challenge=$CODE_CHALLENGE\
&code_challenge_method=S256\
&state=$STATE"

# 2. Después de login el browser redirige a:
#    $REDIRECT_URI?code=<CODE>&state=<STATE>
# Extraer CODE de la URL.

# 3. Intercambiar code por JWT
curl -s -X POST https://dnato.trazalog.com/oauth/token \
  -d "grant_type=authorization_code" \
  -d "code=$CODE" \
  -d "code_verifier=$CODE_VERIFIER" \
  -d "client_id=$CLIENT_ID" \
  -d "redirect_uri=$REDIRECT_URI"
# Respuesta: { "access_token": "<JWT>", "token_type": "Bearer", "expires_in": 3600 }
```

---

## 3. Emitir token de prueba desde CLI

Para debugging y emergencias (solo accesible al admin en el servidor):

```bash
# Usuario con un solo membership (autoselección P02)
php index.php cli issue_test_token admin@empresa.com

# Usuario con múltiples memberships (requiere empr_id explícito)
php index.php cli issue_test_token admin@empresa.com 7
```

Si el usuario tiene más de una empresa y no se pasa `empr_id`, el script lista las opciones disponibles con sus IDs.

**IMPORTANTE**: Este endpoint está protegido contra acceso HTTP (`$this->input->is_cli_request()`). Solo puede ejecutarse desde la línea de comandos en el servidor.

---

## 4. Endpoint JWKS

```bash
curl https://dnato.trazalog.com/oauth/.well-known/jwks.json
```

Respuesta:
```json
{
  "keys": [{
    "kty": "RSA",
    "use": "sig",
    "alg": "RS256",
    "kid": "dnato-rs256-v1",
    "n": "<modulus base64url>",
    "e": "AQAB"
  }]
}
```

WSO2 puede consumir este endpoint para validar firmas automáticamente sin gestión manual de certificados.

---

## 5. Rotación de claves de firma

Ver `doc/identity/jwt-keys-setup.md` — sección "Rotación de claves (procedimiento manual — MVP)".
