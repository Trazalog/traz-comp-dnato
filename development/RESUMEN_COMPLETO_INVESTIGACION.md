# Resumen Completo de Investigación - Fase 0.1

## Problema
El password NO se está hasheando en MD5 antes de insertarse en MySQL AssetPlanner.

## Soluciones Intentadas

### 1. MD5() en SQL del DataService
- **Resultado**: ❌ NO FUNCIONA
- **Razón**: WSO2 DataService escapa los parámetros como literales, no ejecuta funciones SQL

### 2. Stored Procedure en MySQL
- **Resultado**: ✅ Funciona directamente, ❌ NO funciona desde WSO2
- **Stored Procedure creado**: `sp_insert_user_asset` - FUNCIONA cuando se llama directamente
- **Sintaxis probada**:
  - `CALL sp_insert_user_asset(:nick, ...)` - NO funciona
  - `{call sp_insert_user_asset(?, ?, ?, ?, ?)}` - NO funciona
- **Razón**: WSO2 DataService no ejecuta stored procedures correctamente

### 3. Script JavaScript en el API
- **Resultado**: ❌ NO SE EJECUTA
- **Código**: Correcto, en el lugar correcto (línea 393-408)
- **API**: Desplegado correctamente
- **Logs**: No aparecen logs del script, no hay errores
- **Razón**: Desconocida - el script no se ejecuta sin errores visibles

### 4. Class Mediator
- **Resultado**: ❌ ClassNotFoundException
- **JAR creado**: HashMD5Mediator.jar
- **Ubicaciones probadas**:
  - `/repository/components/lib/` - No existe
  - `/dropins/` - ClassNotFoundException
- **Razón**: El class mediator no se carga correctamente

## Conclusión

**Ninguna de las soluciones probadas funciona**. El problema fundamental es que:
1. WSO2 DataService no ejecuta funciones SQL ni stored procedures en parámetros
2. El script JavaScript no se ejecuta (sin errores visibles)
3. El class mediator no se carga correctamente

## Próximos Pasos Sugeridos

1. **Investigar por qué el script JavaScript no se ejecuta**:
   - Verificar si WSO2 MI requiere configuración especial para scripts
   - Revisar si hay algún problema con la sintaxis del script
   - Verificar si el script se ejecuta pero falla silenciosamente

2. **Alternativa: Hashear en el cliente (PHP)**:
   - Hashear el password en MD5 en el código PHP antes de enviarlo al API
   - Esta sería la solución más simple y garantizada

3. **Alternativa: Usar un proxy/endpoint intermedio**:
   - Crear un endpoint que hashee el password antes de llamar al DataService

