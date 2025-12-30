# 📊 Resumen Final - Estado del Proyecto

**Fecha:** 2025-11-17  
**Servidor WSO2:** http://10.142.0.13:8280  
**Base de Datos:** 10.142.0.13:5432/tools_prod_t (usuario: postgres)

---

## ✅ Lo que FUNCIONA

### 1. Stored Procedure PostgreSQL
- ✅ **Creado y funcionando** en `tools_prod_t`
- ✅ **Extensión pgcrypto** habilitada
- ✅ **Prueba directa exitosa:** Retornó ID 162
- ✅ **Hash generado:** Formato `sha256:1000:salt:hash`

### 2. Data Service - Check Duplicado
- ✅ **URL:** `http://10.142.0.13:8280/services/COREDataService/usuario/duplicado/{email}`
- ✅ **HTTP Status:** 200
- ✅ **Funciona correctamente**

### 3. API Desplegado
- ✅ **URL Correcta:** `http://10.142.0.13:8280/tools/core/usuario`
- ✅ **API responde** (no es 404 de "no encontrado")
- ✅ **Flujo llega hasta Data Service**

---

## ⚠️ Problema Actual

### Error: Parámetros Vacíos en Data Service

**Síntoma:**
```
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

**Causa Probable:**
- El `payloadFactory` no está extrayendo correctamente los valores del JSON original
- El payload puede perderse después de la validación de duplicados
- Problema con cómo WSO2 procesa los parámetros null/vacíos

---

## 🔧 Correcciones Realizadas

1. ✅ **Stored Procedure:**
   - Corregido tipos de datos para `hmac()`
   - Eliminado XOR problemático
   - Implementación simplificada pero funcional

2. ✅ **URL del API:**
   - Corregida de `/toolsCOREAPI/1.0.0/usuario` a `/tools/core/usuario`

3. ✅ **Scripts de Prueba:**
   - Actualizados con URL correcta
   - Scripts funcionales para testing

---

## 🎯 Próximos Pasos para Resolver

### 1. Verificar Payload en WSO2
- Revisar logs de WSO2 para ver el payload exacto que llega al Data Service
- Verificar que el `payloadFactory` esté generando el JSON correcto

### 2. Probar con Valores No-Null
- Intentar con todos los campos llenos (sin null)
- Verificar si el problema es con valores null/vacíos

### 3. Revisar Configuración del Data Service
- Verificar que el Data Service esté configurado para recibir JSON
- Revisar el mapeo de parámetros en el resource

### 4. Comparar con Resource de Empresa
- El resource `/empresa` funciona correctamente
- Comparar el formato del payload entre ambos

---

## 📝 Comandos Útiles

### Verificar Stored Procedure
```bash
PGPASSWORD='!Password00' psql -h 10.142.0.13 -p 5432 -U postgres -d tools_prod_t \
  -c "SELECT seg.insert_usuario_con_hash('Test', 'User', 'test@test.com', 'password123', 'subscriber', 'active', '', '+5491112345678', '12345678', 'testuser', NULL, NULL, NULL) as id;"
```

### Probar Data Service Directo
```bash
curl -X POST "http://10.142.0.13:8280/services/COREDataService/usuario" \
  -H "Content-Type: application/json" \
  -d '{
    "_post_usuario": {
      "first_name": "Test",
      "last_name": "User",
      "email": "test@test.com",
      "password_plain": "password123",
      "role": "subscriber",
      "status": "active",
      "banned_users": "",
      "telefono": "+5491112345678",
      "dni": "12345678",
      "usernick": "testuser",
      "depo_id": null,
      "image_name": null,
      "image": null
    }
  }'
```

---

## 📊 Estado de Componentes

| Componente | Estado | Notas |
|------------|--------|-------|
| Stored Procedure | ✅ Funciona | Probado directamente |
| Extensión pgcrypto | ✅ Habilitada | Verificado |
| Data Service - checkUsuarioDuplicado | ✅ Funciona | HTTP 200 |
| Data Service - setUsuario | ⚠️ Error | Parámetros vacíos |
| API toolsCOREAPI | ⚠️ Parcial | Llega hasta Data Service |
| BPM Session | ❌ Pendiente | Requerido para prueba completa |

---

**Última actualización:** 2025-11-17 20:45:00

