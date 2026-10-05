# Reintento de Despliegue - Fase 0.3

## Estado Actual

**Fecha/Hora**: 2026-02-15 18:02+

### Verificaciones Realizadas

1. **WSO2 MI Estado**:
   - ✅ WSO2 MI reiniciado a las 18:02:04
   - ✅ Puerto 8290 escuchando
   - ✅ Servidor iniciado correctamente

2. **Drivers PostgreSQL**:
   - ✅ Driver en `lib/`: `postgresql-jdbc.jar` (correcto)
   - ⚠️ Driver en `dropins/`: `postgresql_jdbc_1.0.0.jar` (aún existe - puede causar conflicto)

3. **CAR Estado**:
   - ✅ CAR existe en: `/mnt/win/dev/git/traz-comp-dnato/development/COREToolsApplication_1.0.0.car`
   - ⚠️ CAR NO está en directorio de despliegue: `/home/rodolfo/dev/wso2mi-4.3.0/repository/deployment/server/carbonapps/`

## Acciones a Ejecutar

### Paso 1: Eliminar Driver Restante de `dropins/`

**Nota**: El driver `postgresql_jdbc_1.0.0.jar` todavía está en `dropins/`. Esto puede causar conflicto OSGi.

**Comando manual requerido**:
```bash
rm /home/rodolfo/dev/wso2mi-4.3.0/dropins/postgresql_jdbc_1.0.0.jar
```

### Paso 2: Copiar CAR al Directorio de Despliegue

**Comando**:
```bash
cp /mnt/win/dev/git/traz-comp-dnato/development/COREToolsApplication_1.0.0.car \
   /home/rodolfo/dev/wso2mi-4.3.0/repository/deployment/server/carbonapps/
```

### Paso 3: Esperar y Verificar Despliegue

**Esperar 15-20 segundos** para que WSO2 MI detecte y despliegue el CAR.

**Verificar logs**:
```bash
tail -100 /home/rodolfo/dev/wso2mi-4.3.0/repository/logs/wso2carbon.log | grep -i "coredataservice\|toolsdatasource\|coretoolsapplication\|deploy\|error"
```

## Verificaciones Post-Despliegue

### 1. Verificar que el DataService se Desplegó

```bash
curl -s http://localhost:8290/services/COREDataService?wsdl 2>&1 | head -20
```

**Resultado esperado**: Debería mostrar el WSDL del DataService, no un error 404.

### 2. Verificar Logs de Errores

```bash
tail -200 /home/rodolfo/dev/wso2mi-4.3.0/repository/logs/wso2carbon.log | grep -i "error.*coredataservice\|error.*toolsdatasource\|error.*postgres\|connection.*failed"
```

**Resultado esperado**: No debería haber errores de conexión a PostgreSQL o del DataService.

### 3. Verificar Errores OSGi del Driver

```bash
tail -200 /home/rodolfo/dev/wso2mi-4.0/repository/logs/wso2carbon.log | grep -i "javax.transaction.xa\|postgresql.*jdbc42.*error"
```

**Resultado esperado**: No debería haber errores de `javax.transaction.xa` (si el driver fue eliminado de `dropins/`).

## Posibles Problemas

### Problema 1: PostgreSQL No Accesible

**Síntoma**: Error `SocketTimeoutException: Connect timed out` en logs

**Solución**: Verificar conectividad PostgreSQL:
```bash
PGPASSWORD='!Password00' psql -h 10.142.0.13 -p 5432 -U postgres -d tools_prod_t -c "SELECT 1;"
```

### Problema 2: CAR Ya Existe

**Síntoma**: Error `Carbon Application : COREToolsApplication_1.0.0 already exists`

**Solución**: Eliminar CAR existente y redeployar:
```bash
rm /home/rodolfo/dev/wso2mi-4.3.0/repository/deployment/server/carbonapps/COREToolsApplication_1.0.0.car
# Esperar 10 segundos para undeploy
sleep 10
# Copiar nuevo CAR
cp /mnt/win/dev/git/traz-comp-dnato/development/COREToolsApplication_1.0.0.car \
   /home/rodolfo/dev/wso2mi-4.3.0/repository/deployment/server/carbonapps/
```

### Problema 3: Driver OSGi Todavía Presente

**Síntoma**: Error `Unresolved requirement: Import-Package: javax.transaction.xa`

**Solución**: Eliminar manualmente el driver de `dropins/`:
```bash
rm /home/rodolfo/dev/wso2mi-4.3.0/dropins/postgresql_jdbc_1.0.0.jar
# Reiniciar WSO2 MI
pkill -f "micro-integrator"
sleep 5
cd /home/rodolfo/dev/wso2mi-4.3.0
nohup bin/micro-integrator.sh > /dev/null 2>&1 &
sleep 40
```

## Próximos Pasos

Después de un despliegue exitoso:

1. ✅ Ejecutar Prueba 0.3.1: Prueba directa del DataService con imagen
2. ✅ Ejecutar Prueba 0.3.2: Verificar en base de datos
3. ✅ Ejecutar Prueba 0.3.3: Prueba sin imagen
4. ✅ Ejecutar Prueba 0.3.4: Prueba de validación de parámetros
5. ✅ Documentar resultados en `development/tests/fase-0.3-imagen-dataservice.md`



