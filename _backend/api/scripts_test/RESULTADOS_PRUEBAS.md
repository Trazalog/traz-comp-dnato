# 📊 Resultados de Pruebas - API Crear Usuario

**Fecha:** 2025-11-17  
**Servidor WSO2:** http://10.142.0.13:8280  
**Protocolo:** HTTP (no HTTPS)

---

## ✅ Tests Exitosos

### TEST 1: Data Service - Check Usuario Duplicado
- **URL:** `http://10.142.0.13:8280/services/COREDataService/usuario/duplicado/{email}`
- **Método:** GET
- **Estado:** ✅ **FUNCIONA CORRECTAMENTE**
- **HTTP Status:** 200
- **Response Ejemplo:**
  ```json
  {
    "respuesta": {
      "existe": "false"
    }
  }
  ```
- **Observaciones:** 
  - El Data Service está desplegado y funcionando
  - La query `checkUsuarioDuplicado` funciona correctamente
  - Retorna formato JSON esperado

---

## ❌ Tests Fallidos

### TEST 2: API toolsCOREAPI - Crear Usuario
- **URL:** `http://10.142.0.13:8280/toolsCOREAPI/1.0.0/usuario`
- **Método:** POST
- **Estado:** ❌ **API NO ENCONTRADO**
- **HTTP Status:** 404
- **Rutas Probadas:**
  - `toolsCOREAPI/1.0.0/usuario` → 404
  - `toolsCOREAPI/usuario` → 404
  - `api/toolsCOREAPI/1.0.0/usuario` → 404

**Posibles Causas:**
1. El API `toolsCOREAPI.xml` no está desplegado en WSO2
2. La ruta del API es diferente a la esperada
3. El API necesita configuración adicional (contexto, versionado)
4. El API está desplegado pero con otro nombre

---

## 🔍 Diagnóstico de Conectividad

### Conectividad de Red
- **Ping:** ❌ Falló (100% packet loss) - Normal si el servidor bloquea ICMP
- **Puerto 8280:** ✅ **ABIERTO y ACCESIBLE**
- **Protocolo:** HTTP (no HTTPS en puerto 8280)

### Errores Detectados
- **Error SSL inicial:** "wrong version number" 
  - **Causa:** Intento de usar HTTPS en puerto HTTP
  - **Solución:** Usar HTTP en lugar de HTTPS

---

## 📋 Resumen de Funcionalidades

| Componente | Estado | HTTP Code | Notas |
|------------|--------|-----------|-------|
| Data Service - checkUsuarioDuplicado | ✅ Funciona | 200 | Query funciona correctamente |
| Data Service - setUsuario | ⚠️ No probado | - | Requiere API para probar |
| Data Service - setUserBusiness | ⚠️ No probado | - | Requiere API para probar |
| API toolsCOREAPI - POST /usuario | ❌ No encontrado | 404 | API no desplegado o ruta incorrecta |

---

## 🎯 Próximos Pasos

### 1. Verificar Despliegue del API
```bash
# Verificar en WSO2 Management Console
# https://10.142.0.13:9443/console
# Services → List → Buscar "toolsCOREAPI"
```

### 2. Verificar Ruta Correcta del API
- Revisar configuración de WSO2
- Verificar contexto y versionado del API
- Consultar logs de WSO2: `/repository/logs/wso2carbon.log`

### 3. Desplegar API si no está desplegado
```bash
# Copiar toolsCOREAPI.xml a:
# /ruta/wso2/repository/deployment/synapse-configs/default/api/
```

### 4. Obtener BPM Session Válido
- El API requiere un `bpmSession` válido
- Obtener token de sesión de Bonita BPM antes de probar creación de usuarios

### 5. Probar con Data Service Directo
Mientras tanto, se puede probar el stored procedure directamente:
```sql
-- En PostgreSQL
SELECT seg.insert_usuario_con_hash(
    'Test', 'User', 'test@example.com', 'password123',
    'subscriber', 'active', '', '', '', 'testuser',
    NULL, NULL, NULL
);
```

---

## 📝 Comandos de Prueba Ejecutados

### Test Data Service (Exitoso)
```bash
curl -s -X GET "http://10.142.0.13:8280/services/COREDataService/usuario/duplicado/test@example.com" \
  -H "Accept: application/json"
```

### Test API (Fallido - 404)
```bash
curl -s -X POST "http://10.142.0.13:8280/toolsCOREAPI/1.0.0/usuario" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"bpmSession":"test","usuario":{...}}'
```

---

## ⚠️ Observaciones Importantes

1. **Protocolo:** El servidor usa HTTP en puerto 8280, no HTTPS
2. **Data Service:** Funciona correctamente y está desplegado
3. **API:** No se encontró en las rutas probadas - requiere verificación de despliegue
4. **BPM Session:** Se necesita un token válido para pruebas completas de creación de usuarios

---

## 📞 Información de Contacto

Para resolver el problema del API:
1. Verificar logs de WSO2
2. Consultar administrador de WSO2 sobre la ruta correcta
3. Verificar que el API esté desplegado correctamente

---

**Generado:** 2025-11-17 20:18:00

