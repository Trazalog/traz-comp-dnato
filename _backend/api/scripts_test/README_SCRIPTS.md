# 📝 Scripts de Prueba - API Crear Usuario

## 📦 Scripts Disponibles

### 1. `test_create_usuario.sh`
Crea un usuario con datos aleatorios.

**Uso:**
```bash
./test_create_usuario.sh [WSO2_URL] [BPM_SESSION]
```

**Ejemplo:**
```bash
./test_create_usuario.sh https://localhost:8243 "bpm_session_token_123"
```

**Características:**
- Genera datos aleatorios únicos (timestamp-based)
- Muestra respuesta completa
- Valida código HTTP
- Extrae y muestra user_id si está disponible

---

### 2. `test_create_usuario_multiple.sh`
Crea múltiples usuarios de prueba en batch.

**Uso:**
```bash
./test_create_usuario_multiple.sh [WSO2_URL] [BPM_SESSION] [CANTIDAD]
```

**Ejemplo:**
```bash
./test_create_usuario_multiple.sh https://localhost:8243 "bpm_session_token_123" 10
```

**Características:**
- Crea N usuarios en secuencia
- Muestra progreso y resumen
- Útil para pruebas de carga

---

### 3. `test_check_duplicado.sh`
Prueba la validación de usuarios duplicados.

**Uso:**
```bash
./test_check_duplicado.sh [WSO2_URL] [EMAIL]
```

**Ejemplo:**
```bash
./test_check_duplicado.sh https://localhost:8243 "test@example.com"
```

**Características:**
- Verifica si un email ya existe
- Útil para debugging de validaciones

---

### 4. `test_scenarios.sh`
Ejecuta múltiples escenarios de prueba.

**Uso:**
```bash
./test_scenarios.sh [WSO2_URL] [BPM_SESSION]
```

**Ejemplo:**
```bash
./test_scenarios.sh https://localhost:8243 "bpm_session_token_123"
```

**Escenarios incluidos:**
1. ✅ Usuario nuevo (debe funcionar)
2. ❌ Usuario duplicado (debe fallar)
3. 👤 Usuario Admin (debe funcionar)
4. 📧 Email inválido (debe fallar)
5. ⚠️ Payload incompleto (debe fallar)

---

## 🔧 Configuración

### Permisos de Ejecución
```bash
chmod +x test_create_usuario.sh
chmod +x test_create_usuario_multiple.sh
chmod +x test_check_duplicado.sh
chmod +x test_scenarios.sh
```

### Dependencias
- `curl` - Para hacer requests HTTP
- `jq` (opcional) - Para formatear JSON
  ```bash
  # Instalar jq
  sudo apt-get install jq  # Ubuntu/Debian
  brew install jq          # macOS
  ```

---

## 📋 Estructura del Payload

Todos los scripts usan esta estructura:

```json
{
  "bpmSession": "token_de_sesion_bpm",
  "usuario": {
    "firstname": "Nombre",
    "lastname": "Apellido",
    "email": "email@example.com",
    "password": "password123",
    "role": "subscriber",
    "status": "active",
    "banned_users": "",
    "telefono": "+5491112345678",
    "dni": "12345678",
    "usernick": "username",
    "depo_id": null,
    "image_name": null,
    "image": null,
    "business": "empresa_test"
  }
}
```

---

## 🎯 Ejemplos de Uso

### Prueba Básica
```bash
# Crear un usuario de prueba
./test_create_usuario.sh
```

### Prueba con URL Personalizada
```bash
./test_create_usuario.sh https://wso2-prod.example.com:8243 "bpm_token_abc123"
```

### Prueba de Duplicados
```bash
# 1. Crear usuario
./test_create_usuario.sh

# 2. Intentar crear el mismo usuario (debe fallar)
# Copiar el email del paso 1 y usarlo:
./test_check_duplicado.sh https://localhost:8243 "test1234567890@example.com"
```

### Prueba de Escenarios
```bash
# Ejecutar todos los escenarios
./test_scenarios.sh https://localhost:8243 "bpm_token"
```

---

## ✅ Validaciones Esperadas

### Respuesta Exitosa (HTTP 200/201)
```json
{
  "respuesta": {
    "resultado": "ok",
    "usr_id": "123",
    "bpmSession": "bpm_session_token"
  }
}
```

### Error - Usuario Duplicado (HTTP 400/409)
```json
{
  "error": {
    "mensaje": "El usuario ya existe",
    "tipo": "Usuario duplicado"
  }
}
```

---

## 🐛 Troubleshooting

### Error: "jq: command not found"
**Solución:** Instalar jq o remover las llamadas a `jq` en los scripts

### Error: "curl: (60) SSL certificate problem"
**Solución:** Agregar `-k` a los comandos curl para ignorar certificados SSL:
```bash
curl -k ...
```

### Error: "401 Unauthorized"
**Solución:** Verificar que el BPM_SESSION sea válido y no haya expirado

### Error: "Connection refused"
**Solución:** Verificar que WSO2 esté corriendo y la URL sea correcta

---

## 📊 Ejemplo de Output

```
=== Test API Crear Usuario ===

Datos generados:
  Email: test1699123456@example.com
  Username: testuser1699123456
  Password: Test123456
  Business: empresa_test_1699123456

Ejecutando POST /usuario...

HTTP Status: 200
Response:
{
  "respuesta": {
    "resultado": "ok",
    "usr_id": "456",
    "bpmSession": "bpm_session_token"
  }
}

✓ Usuario creado exitosamente
  User ID: 456

=== Datos para verificación ===
Email: test1699123456@example.com
Username: testuser1699123456
Password: Test123456
Business: empresa_test_1699123456
```

---

**Última actualización:** 2025-01-XX

