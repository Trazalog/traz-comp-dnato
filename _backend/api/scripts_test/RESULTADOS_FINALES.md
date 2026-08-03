# 📊 Resultados Finales de Pruebas - API Crear Usuario

**Fecha:** 2025-11-17  
**Servidor WSO2:** http://10.142.0.13:8280  
**URL Correcta:** `http://10.142.0.13:8280/tools/core/usuario`

---

## ✅ Tests Exitosos

### TEST 1: Data Service - Check Usuario Duplicado
- **URL:** `http://10.142.0.13:8280/services/COREDataService/usuario/duplicado/{email}`
- **Método:** GET
- **Estado:** ✅ **FUNCIONA CORRECTAMENTE**
- **HTTP Status:** 200
- **Response:**
  ```json
  {
    "respuesta": {
      "existe": "false"
    }
  }
  ```

---

## ⚠️ Tests con Errores (pero API encontrado)

### TEST 2: API toolsCOREAPI - Crear Usuario
- **URL:** `http://10.142.0.13:8280/tools/core/usuario` ✅ **URL CORRECTA**
- **Método:** POST
- **Estado:** ⚠️ **API ENCONTRADO PERO ERROR DE BASE DE DATOS**
- **HTTP Status:** 404 (pero con respuesta JSON de error)
- **Error Detectado:**
  ```json
  {
    "respuesta": {
      "codigo": "1000",
      "error": "POST /usuario con problemas",
      "detalle": "DATABASE_ERROR - NullPointerException"
    }
  }
  ```

**Análisis del Error:**
- El API está desplegado y funcionando (ya no retorna 404 de "no encontrado")
- El flujo llega hasta el Data Service
- El error ocurre en `SQLQuery.processPreNormalQuery`
- **Causa probable:** El stored procedure `seg.insert_usuario_con_hash()` no está creado en PostgreSQL o hay un problema con los parámetros

---

## 🔍 Diagnóstico Detallado

### Error Específico:
```
DS Code: DATABASE_ERROR
Nested Exception: java.lang.NullPointerException
Current Request Name: _post_usuario
Current Params: {
  image=, 
  role=, 
  usernick=, 
  last_name=, 
  banned_users=, 
  image_name=, 
  password_plain=, 
  telefono=, 
  depo_id=, 
  first_name=, 
  email=, 
  dni=, 
  status=
}
```

**Posibles Causas:**
1. ❌ Stored procedure `seg.insert_usuario_con_hash()` no existe en PostgreSQL
2. ❌ Extensión `pgcrypto` no está habilitada
3. ❌ Problema con parámetros NULL o vacíos
4. ❌ Error de conexión a la base de datos desde WSO2

---

## 📋 Resumen de Estado

| Componente | Estado | HTTP Code | Notas |
|------------|--------|-----------|-------|
| Data Service - checkUsuarioDuplicado | ✅ Funciona | 200 | Query funciona correctamente |
| API toolsCOREAPI - POST /usuario | ⚠️ Error DB | 404* | API encontrado, error en stored procedure |
| Stored Procedure | ❌ No verificado | - | Requiere verificación en PostgreSQL |

*Nota: El HTTP 404 en este caso es la respuesta de error del API, no significa que el API no esté desplegado.

---

## 🎯 Próximos Pasos

### 1. Verificar Stored Procedure en PostgreSQL
```sql
-- Conectar a PostgreSQL
psql -U postgres -d tu_base_de_datos

-- Verificar que existe
SELECT proname, prosrc 
FROM pg_proc 
WHERE proname = 'insert_usuario_con_hash';

-- Si no existe, crearlo:
\i _backend/api/dataservices/sp_insert_usuario_hash.sql

-- Verificar extensión pgcrypto
SELECT * FROM pg_extension WHERE extname = 'pgcrypto';
-- Si no existe:
CREATE EXTENSION IF NOT EXISTS pgcrypto;
```

### 2. Probar Stored Procedure Directamente
```sql
SELECT seg.insert_usuario_con_hash(
    'Test',           -- first_name
    'User',           -- last_name
    'test@test.com',  -- email
    'password123',    -- password_plain
    'subscriber',     -- role
    'active',         -- status
    '',               -- banned_users
    '+5491112345678', -- telefono
    '12345678',       -- dni
    'testuser',       -- usernick
    NULL,             -- depo_id
    NULL,             -- image_name
    NULL              -- image
);
```

### 3. Verificar Configuración de Data Source en WSO2
- Verificar que `ToolsDataSource` esté configurado correctamente
- Verificar conectividad desde WSO2 a PostgreSQL
- Revisar logs de WSO2: `/repository/logs/wso2carbon.log`

### 4. Obtener BPM Session Válido
- El API requiere un `bpmSession` válido para crear usuarios en BPM
- Obtener token de sesión de Bonita BPM

---

## 📝 Comandos de Prueba Actualizados

### Test Data Service (Funciona)
```bash
curl -s -X GET "http://10.142.0.13:8280/services/COREDataService/usuario/duplicado/test@example.com" \
  -H "Accept: application/json"
```

### Test API (URL Correcta)
```bash
curl -s -X POST "http://10.142.0.13:8280/tools/core/usuario" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "bpmSession": "test_session",
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

## ✅ Logros

1. ✅ **URL Correcta Identificada:** `/tools/core/usuario`
2. ✅ **API Desplegado:** El API está funcionando y procesando requests
3. ✅ **Data Service Funcional:** La query de duplicados funciona
4. ✅ **Flujo Completo:** El request llega hasta el Data Service

## ❌ Pendientes

1. ❌ **Stored Procedure:** Verificar creación en PostgreSQL
2. ❌ **Extensión pgcrypto:** Verificar que esté habilitada
3. ❌ **BPM Session:** Obtener token válido para pruebas completas
4. ❌ **Prueba End-to-End:** Completar flujo completo de creación

---

**Generado:** 2025-11-17 20:34:00

