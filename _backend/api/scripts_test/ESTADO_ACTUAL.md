# 📊 Estado Actual - API Crear Usuario

**Fecha:** 2025-11-17  
**Última Prueba:** Después de redesplegar API

---

## ✅ Progreso Logrado

### 1. Parámetros Llegan Correctamente
Los parámetros ahora SÍ están llegando al Data Service:
```
Current Params: {
  image=null, 
  role=subscriber, 
  usernick=testuser1763437130, 
  last_name=User1763437130, 
  banned_users=, 
  image_name=null, 
  password_plain=Test123456, 
  telefono=+5491112345678, 
  depo_id=null, 
  first_name=Test1763437130, 
  email=test1763437130@example.com, 
  dni=12345678, 
  status=active
}
```

### 2. Correcciones Realizadas
- ✅ **API:** Propiedades guardadas antes del `call` para verificar duplicados
- ✅ **API:** `payloadFactory` usa `get-property()` en lugar de `json-eval()`
- ✅ **Data Service:** Manejo mejorado de valores NULL (agregado `OR :param='null'`)
- ✅ **Data Service:** Parámetros opcionales marcados con `optional="true"`

---

## ⚠️ Problema Actual

### Error: NullPointerException en Data Service

**Error:**
```
DS Code: DATABASE_ERROR
Nested Exception: java.lang.NullPointerException
Current Request Name: _post_usuario
```

**Causa Probable:**
1. El Data Service necesita ser **redesplegado** para aplicar los cambios
2. El parámetro `image` (tipo BINARY) está recibiendo la cadena "null" en lugar de NULL real
3. WSO2 Data Services puede tener problemas procesando BINARY cuando viene como string "null"

---

## 🔧 Próximos Pasos

### 1. Redesplegar Data Service
```bash
# Copiar COREDataService.dbs actualizado a WSO2
# Ruta: /ruta/wso2/repository/deployment/server/dataservices/
```

### 2. Verificar Logs de WSO2
Revisar logs para ver el error exacto:
```bash
tail -f /ruta/wso2/repository/logs/wso2carbon.log
```

### 3. Alternativa: Enviar valores vacíos en lugar de null
Si el problema persiste, modificar el API para enviar strings vacíos en lugar de null:
```json
{
  "image_name": "",
  "image": "",
  "depo_id": ""
}
```

---

## 📝 Cambios Realizados

### API (toolsCOREAPI.xml)
- Guardar propiedades antes del `call` (líneas 231-241)
- Usar `get-property()` en `payloadFactory` (líneas 278-290)

### Data Service (COREDataService.dbs)
- Manejo de NULL mejorado en SQL (línea 506)
- Parámetros opcionales marcados (líneas 520-522)

---

**Última actualización:** 2025-11-17 21:00:00

