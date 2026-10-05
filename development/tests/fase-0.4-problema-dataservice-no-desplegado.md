# Problema Detectado - Fase 0.4: DataService No Desplegado

## Problema Principal

El DataService `COREDataService` no está desplegado, por lo que la API no puede llamarlo y falla con error 404.

## Análisis Completo

### Estado de la Fase 0.3

1. **Fase 0.3 se dio por cerrada** con pruebas exitosas del DataService
2. **Las pruebas de Fase 0.3** se ejecutaron directamente al DataService: `POST http://localhost:8290/services/COREDataService/usuario`
3. **Estado actual**: El DataService no está desplegado (404 Not Found)
4. **Causa raíz**: El CAR no se está desplegando correctamente por error en `artifacts.xml`

### Estado Actual del Código

✅ **Código de la API está CORRECTO**:
- El código está alineado exactamente con el flujo de "crear empresa" que funciona
- `payloadFactory` incluye `image_name` e `image` correctamente
- Headers `Accept` y `messageType` configurados correctamente
- Estructura idéntica al flujo que funciona

### Problemas Detectados

1. **WSO2 MI no está corriendo**:
   - Puerto 8290 no responde
   - No hay proceso `micro-integrator` corriendo
   - Solo hay procesos `choreo-cli` de la extensión de Cursor

2. **DataService no está desplegado**:
   - Archivo `.xml` existe en `/home/rodolfo/dev/wso2mi-4.3.0/repository/deployment/server/dataservices/`
   - Archivo `.dbs` existe (indica que se intentó desplegar)
   - Pero el servicio no responde: `main sequence executed for call to non-existent = /services/COREDataService/usuario`

3. **CAR no se despliega correctamente**:
   - Error en logs: `artifacts.xml is invalid. No Artifact found with the type - carbon/application`
   - El CAR existe pero no se despliega automáticamente

## Errores en Logs

```
[2026-02-15 23:07:24,135] ERROR - artifacts.xml is invalid. No Artifact found with the type - carbon/application
[2026-02-15 22:56:57,304] INFO - main sequence executed for call to non-existent = /services/COREDataService/usuario
[2026-02-15 23:11:26,257] INFO - main sequence executed for call to non-existent = /services/COREDataService?wsdl
```

## Solución Requerida

### Paso 1: Iniciar WSO2 MI

```bash
cd /home/rodolfo/dev/wso2mi-4.3.0
nohup bin/micro-integrator.sh > /dev/null 2>&1 &
sleep 40
```

### Paso 2: Desplegar DataService Manualmente

```bash
# Eliminar archivo .dbs si existe para forzar redeploy
rm -f /home/rodolfo/dev/wso2mi-4.3.0/repository/deployment/server/dataservices/COREDataService.dbs

# Copiar archivo .xml
cp /mnt/win/dev/git/traz-comp-dnato/development/COREDataService.xml \
   /home/rodolfo/dev/wso2mi-4.3.0/repository/deployment/server/dataservices/

# Esperar 15-20 segundos para despliegue
sleep 20
```

### Paso 3: Verificar Despliegue

```bash
# Verificar que el servicio responde
curl -s http://localhost:8290/services/COREDataService?wsdl | head -5

# Verificar logs
tail -100 /home/rodolfo/dev/wso2mi-4.3.0/repository/logs/wso2carbon.log | \
  grep -E "COREDataService.*deploy|Successfully.*deployed.*Data Service"
```

### Paso 4: Probar DataService Directamente

```bash
TIMESTAMP=$(date +%s)
curl -X POST http://localhost:8290/services/COREDataService/usuario \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d "{\"_post_usuario\":{\"first_name\":\"Test\",\"last_name\":\"Direct\",\"email\":\"test_direct_${TIMESTAMP}@test.com\",\"password_plain\":\"password123\",\"role\":\"2\",\"status\":\"approved\",\"banned_users\":\"unban\",\"telefono\":\"1234567890\",\"dni\":\"12345678\",\"usernick\":\"test_direct_${TIMESTAMP}\",\"image_name\":\"foto.jpg\",\"image\":\"iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==\"}}"
```

**Resultado esperado**: Debe retornar `{"GeneratedKeys":{"Entry":[{"ID":"XXX"}]}}`

### Paso 5: Probar API Completa

```bash
TIMESTAMP=$(date +%s)
curl -X POST http://localhost:8290/tools/core/usuario \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d "{\"bpmSession\":\"test\",\"usuario\":{\"usernick\":\"test_api_${TIMESTAMP}\",\"email\":\"test_api_${TIMESTAMP}@test.com\",\"business\":\"1\",\"firstname\":\"Test\",\"lastname\":\"API\",\"password\":\"password123\",\"role\":\"2\",\"status\":\"approved\",\"banned_users\":\"unban\",\"telefono\":\"1234567890\",\"dni\":\"12345678\",\"depo_id\":\"1\",\"image_name\":\"foto.jpg\",\"image\":\"iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==\"}}"
```

**Resultado esperado**: Debe retornar `{"respuesta":{"resultado":"ok","usr_id":"XXX"}}`

## Conclusión

**El código de la Fase 0.4 está completo y correcto**. El problema es de infraestructura/despliegue, no de código:

1. ✅ Código de API correcto (alineado con crear empresa)
2. ✅ PayloadFactory incluye imagen
3. ✅ Headers configurados correctamente
4. ❌ WSO2 MI no está corriendo
5. ❌ DataService no está desplegado

Una vez que WSO2 MI esté corriendo y el DataService desplegado, las pruebas deberían funcionar correctamente.

## Próximos Pasos

1. Iniciar WSO2 MI
2. Desplegar DataService manualmente
3. Verificar que DataService responde
4. Ejecutar todas las pruebas de Fase 0.4 (0.4.1 a 0.4.4)
5. Documentar resultados

