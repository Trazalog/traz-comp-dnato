# Problema Detectado - Fase 0.4: DataService No Desplegado

## Problema Principal

El DataService `COREDataService` no está desplegado, por lo que la API no puede llamarlo y falla con error 404.

## Análisis

1. **Fase 0.3 se dio por cerrada** con pruebas exitosas del DataService
2. **Las pruebas de Fase 0.3** se ejecutaron directamente al DataService: `POST http://localhost:8290/services/COREDataService/usuario`
3. **Estado actual**: El DataService no está desplegado (404 Not Found)
4. **Causa**: El CAR no se está desplegando correctamente por error en `artifacts.xml`

## Error en Logs

```
[2026-02-15 23:07:24,135] ERROR - artifacts.xml is invalid. No Artifact found with the type - carbon/application
[2026-02-15 22:56:57,304] INFO - main sequence executed for call to non-existent = /services/COREDataService/usuario
```

## Solución Temporal

Desplegar el DataService manualmente:
```bash
cp /mnt/win/dev/git/traz-comp-dnato/development/COREDataService.xml \
   /home/rodolfo/dev/wso2mi-4.3.0/repository/deployment/server/dataservices/
```

## Próximos Pasos

1. Verificar que el DataService se despliegue manualmente
2. Probar la API end-to-end
3. Corregir el CAR para que incluya el DataService correctamente
4. Ejecutar todas las pruebas de Fase 0.4

