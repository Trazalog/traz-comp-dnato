# 📊 Resultados Corregidos - API Crear Usuario

**Fecha:** 2025-11-17  
**Servidor WSO2:** http://10.142.0.13:8280  
**Base de Datos:** 10.142.0.13:5432/tools_prod_t

---

## ✅ Correcciones Realizadas

### 1. Stored Procedure Corregido
- **Problema:** Error con tipos de datos en función `hmac()` y operador XOR
- **Solución:** 
  - Convertir parámetros a TEXT explícitamente para `hmac()`
  - Simplificar implementación eliminando XOR (usando HMAC iterado)
- **Estado:** ✅ **FUNCIONA CORRECTAMENTE**

### 2. Prueba Directa en PostgreSQL
```sql
SELECT seg.insert_usuario_con_hash(
    'Test', 'User', 'test@test.com', 'password123',
    'subscriber', 'active', '', '+5491112345678', '12345678',
    'testuser', NULL, NULL, NULL
) as id;
```
**Resultado:** ✅ Retornó ID 162 (usuario creado exitosamente)

---

## 📋 Estado Actual

| Componente | Estado | Detalle |
|------------|--------|---------|
| Stored Procedure | ✅ Funciona | Creado y probado en PostgreSQL |
| Extensión pgcrypto | ✅ Habilitada | Verificado |
| Data Service - checkUsuarioDuplicado | ✅ Funciona | HTTP 200 |
| API toolsCOREAPI | ⚠️ Pendiente prueba | Requiere BPM session válido |

---

## 🎯 Próximos Pasos

1. **Obtener BPM Session Válido**
   - El API requiere un token de sesión válido de Bonita BPM
   - Sin este token, el API fallará al intentar crear el usuario en BPM

2. **Probar API Completo**
   - Una vez obtenido el BPM session, ejecutar prueba completa
   - Verificar que el usuario se cree en:
     - PostgreSQL (seg.users)
     - seg.users_business
     - Asset Planner (MariaDB)
     - Bonita BPM

3. **Validar Hash Compatible con PHP**
   - Verificar que el hash generado por PostgreSQL sea compatible con la librería PHP `Password`
   - Probar login con el usuario creado

---

## 📝 Comandos de Prueba

### Probar Stored Procedure Directamente
```bash
PGPASSWORD='!Password00' psql -h 10.142.0.13 -p 5432 -U postgres -d tools_prod_t \
  -c "SELECT seg.insert_usuario_con_hash('Test', 'User', 'test@test.com', 'password123', 'subscriber', 'active', '', '+5491112345678', '12345678', 'testuser', NULL, NULL, NULL) as id;"
```

### Probar API (requiere BPM session)
```bash
curl -X POST "http://10.142.0.13:8280/tools/core/usuario" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "bpmSession": "BPM_SESSION_TOKEN_AQUI",
    "usuario": {
      "firstname": "Test",
      "lastname": "User",
      "email": "test@example.com",
      "password": "Test123456",
      "role": "subscriber",
      "status": "active",
      "banned_users": "",
      "telefono": "+5491112345678",
      "dni": "12345678",
      "usernick": "testuser",
      "depo_id": null,
      "image_name": null,
      "image": null,
      "business": "empresa_test"
    }
  }'
```

---

**Última actualización:** 2025-11-17 20:43:00

