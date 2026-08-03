# Pasos Inmediatos para Reintentar Despliegue - Fase 0.3

## ⚠️ Estado Actual

- ✅ WSO2 MI está corriendo (reiniciado a las 18:02:04)
- ✅ Driver PostgreSQL en `lib/` (correcto)
- ⚠️ Driver `postgresql_jdbc_1.0.0.jar` todavía en `dropins/` (puede causar conflicto)
- ⚠️ CAR no está desplegado

## 🚀 Pasos a Ejecutar (En Orden)

### Paso 1: Eliminar Driver Restante de `dropins/`

```bash
rm /home/rodolfo/dev/wso2mi-4.3.0/dropins/postgresql_jdbc_1.0.0.jar
```

**Verificar**:
```bash
ls -la /home/rodolfo/dev/wso2mi-4.3.0/dropins/postgresql*.jar
# Debería mostrar: "No such file or directory"
```

### Paso 2: Copiar CAR al Directorio de Despliegue

```bash
cp /mnt/win/dev/git/traz-comp-dnato/development/COREToolsApplication_1.0.0.car \
   /home/rodolfo/dev/wso2mi-4.3.0/repository/deployment/server/carbonapps/
```

**Verificar**:
```bash
ls -la /home/rodolfo/dev/wso2mi-4.3.0/repository/deployment/server/carbonapps/COREToolsApplication_1.0.0.car
# Debería mostrar el archivo
```

### Paso 3: Esperar Despliegue (15-20 segundos)

```bash
sleep 20
```

### Paso 4: Verificar Logs de Despliegue

```bash
tail -150 /home/rodolfo/dev/wso2mi-4.3.0/repository/logs/wso2carbon.log | grep -i "coredataservice\|toolsdatasource\|coretoolsapplication\|deploy\|error" | tail -30
```

**Buscar**:
- ✅ `Successfully deployed Carbon Application : COREToolsApplication_1.0.0`
- ✅ `Successfully deployed Data Service : COREDataService`
- ❌ NO debería haber: `ERROR.*COREDataService`
- ❌ NO debería haber: `CONNECTION_UNAVAILABLE_ERROR`
- ❌ NO debería haber: `SocketTimeoutException`

### Paso 5: Verificar que el DataService Está Accesible

```bash
curl -s http://localhost:8290/services/COREDataService?wsdl 2>&1 | head -20
```

**Resultado esperado**: Debería mostrar XML del WSDL, no un error 404.

## 🔍 Verificaciones Adicionales

### Verificar Errores de PostgreSQL

```bash
tail -200 /home/rodolfo/dev/wso2mi-4.3.0/repository/logs/wso2carbon.log | grep -i "error.*postgres\|connection.*failed\|socket.*timeout" | tail -10
```

**Si hay errores de conexión**: Verificar conectividad PostgreSQL:
```bash
PGPASSWORD='!Password00' psql -h 10.142.0.13 -p 5432 -U postgres -d tools_prod_t -c "SELECT 1;"
```

### Verificar Errores OSGi del Driver

```bash
tail -200 /home/rodolfo/dev/wso2mi-4.3.0/repository/logs/wso2carbon.log | grep -i "javax.transaction.xa\|postgresql.*jdbc42.*error" | tail -5
```

**Resultado esperado**: No debería haber errores (si el driver fue eliminado de `dropins/`).

## 📊 Resultados Esperados

### ✅ Despliegue Exitoso

Si el despliegue es exitoso, deberías ver en los logs:
- `Successfully deployed Carbon Application : COREToolsApplication_1.0.0`
- `Successfully deployed Data Service : COREDataService`
- El DataService responde en `http://localhost:8290/services/COREDataService?wsdl`

### ❌ Despliegue Fallido

Si el despliegue falla, revisar:
1. **Error de conexión PostgreSQL**: Verificar VPN y estado del servidor
2. **Error "already exists"**: Eliminar CAR existente y redeployar
3. **Error OSGi**: Eliminar driver de `dropins/` y reiniciar WSO2 MI

## 📝 Notas

- El driver `postgresql_jdbc_1.0.0.jar` en `dropins/` puede causar conflicto OSGi
- Si el despliegue falla por conexión PostgreSQL, es responsabilidad del usuario verificar conectividad (según premisas de trabajo autónomo)
- Después de un despliegue exitoso, se pueden ejecutar las pruebas de Fase 0.3



