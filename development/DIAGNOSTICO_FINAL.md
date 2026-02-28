# Diagnóstico Final Fase 0.1

## Hallazgos

### ✅ Lo que SÍ funciona:
1. **Stored Procedure en MySQL**: `sp_insert_user_asset` hashea correctamente cuando se llama directamente
2. **API se despliega**: toolsCOREApi se actualiza correctamente
3. **DataService se despliega**: COREDataService se actualiza correctamente
4. **Apache Commons Codec disponible**: `/home/rodolfo/dev/wso2mi-4.3.0/wso2/lib/commons-codec-1.15.jar`
5. **Librerías JavaScript disponibles**: `js-22.3.4.jar` y `js-scriptengine-22.3.4.jar`

### ❌ Lo que NO funciona:
1. **MD5() en SQL**: WSO2 DataService no ejecuta funciones SQL en parámetros
2. **Stored Procedure desde WSO2**: No se ejecuta aunque funciona directamente
3. **Script JavaScript**: No se ejecuta (no hay logs, no hay errores)

## Conclusión

El problema es que **WSO2 DataService prepara los statements SQL y escapa los parámetros**, por lo que:
- Las funciones SQL no se ejecutan
- Los stored procedures no se llaman correctamente

## Solución Recomendada

**Hashear el password en el API ANTES de enviarlo al DataService**, usando un **class mediator** en lugar de script mediator, ya que el script JavaScript no se está ejecutando.

### Implementación con Class Mediator:

1. Compilar el class mediator (HashMD5Mediator.java)
2. Agregarlo a WSO2 MI
3. Usarlo en el API en lugar del script JavaScript

