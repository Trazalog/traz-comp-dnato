# Configuración del par de claves RS256 para JWT

**Ticket**: E9-IDENT-03  
**Algoritmo**: RS256 (RSASSA-PKCS1-v1_5 con SHA-256)  
**Responsabilidad**: La clave **privada** vive solo en Dnato. La clave **pública** se exporta a WSO2 (E9-IDENT-05).

---

## Generación del par de claves

```bash
# 1. Crear directorio (excluido de git via .gitignore)
mkdir -p application/config/keys

# 2. Generar clave privada RSA 2048-bit
openssl genrsa -out application/config/keys/jwt_private.pem 2048

# 3. Extraer clave pública
openssl rsa \
    -in  application/config/keys/jwt_private.pem \
    -pubout \
    -out application/config/keys/jwt_public.pem

# 4. Verificar
openssl rsa -in application/config/keys/jwt_private.pem -check
```

El directorio `application/config/keys/` está en `.gitignore`. **NUNCA commitear las claves privadas.**

---

## Configuración en el servidor

Establecer la variable de entorno apuntando a la clave privada (en `.env`, en el virtualhost Apache, o en el entorno Docker):

```
JWT_PRIVATE_KEY_PATH=/var/www/dnato/application/config/keys/jwt_private.pem
JWT_PUBLIC_KEY_PATH=/var/www/dnato/application/config/keys/jwt_public.pem
```

El archivo `application/config/jwt.php` usa estas variables con fallback al path local de desarrollo.

---

## Compartir la clave pública con WSO2

### Opción A — Endpoint JWKS (recomendado)

El endpoint `GET /oauth/.well-known/jwks.json` ya está implementado y devuelve la clave pública en formato JWK estándar. En WSO2 APIM configurar la URL del JWKS:

```
https://dnato.trazalog.com/oauth/.well-known/jwks.json
```

### Opción B — Archivo .pem manual

Copiar `application/config/keys/jwt_public.pem` al servidor WSO2 y configurar el JWT validator en `api.yaml`:

```yaml
x-wso2-auth-header: Authorization
securityDefinitions:
  jwt:
    type: oauth2
    authorizationUrl: https://dnato.trazalog.com/oauth/authorize
    flow: accessCode
x-wso2-jwt-config:
  issuer: trazalog-dnato
  audience: trazalog-mcp
  certificate: /path/to/jwt_public.pem
```

---

## Rotación de claves (procedimiento manual — MVP)

1. **Generar nuevo par** en el servidor Dnato (pasos de arriba con nombres `jwt_private_v2.pem` / `jwt_public_v2.pem`).
2. **Actualizar `jwt_kid`** en `application/config/jwt.php` (ej. `dnato-rs256-v2`).
3. **Actualizar `JWT_PRIVATE_KEY_PATH`** en las variables de entorno para que apunte a la nueva clave.
4. **Exportar `jwt_public_v2.pem`** a WSO2 (agregarlo al JWKS o reemplazar el .pem).
5. **Mantener la clave anterior activa 1 TTL** (1 hora) para que tokens en tránsito sigan siendo válidos.
6. **Después del TTL**: remover la clave anterior de WSO2 y eliminar `jwt_private_v1.pem` del servidor.

> Para automatizar la rotación en el futuro, implementar JWKS dinámico con `kid` múltiples y configurar WSO2 para re-fetch periódico.

---

## Verificación rápida con curl

```bash
# 1. Emitir token de prueba via CLI
TOKEN=$(php index.php cli issue_test_token admin@empresa.com 1)

# 2. Verificar estructura (header.payload.signature)
echo $TOKEN | cut -d. -f2 | base64 -d 2>/dev/null | python3 -m json.tool

# 3. Verificar firma con la clave pública
# (requiere jwt-cli: https://github.com/mike-engel/jwt-cli)
jwt decode $TOKEN --secret @application/config/keys/jwt_public.pem
```
