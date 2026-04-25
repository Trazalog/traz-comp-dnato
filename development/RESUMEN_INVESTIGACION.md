# Investigación Fase 0.1 - Problema con Hash MD5

## Problema Identificado
El password NO se está hasheando en MD5 antes de insertarse en MySQL AssetPlanner.

## Intentos Realizados

### 1. MD5() en SQL del DataService
- ❌ **No funciona**: WSO2 DataService no ejecuta funciones SQL en parámetros
- El parámetro `:pass` se escapa como literal, no como parte de una expresión SQL

### 2. Stored Procedure en MySQL
- ✅ **Funciona directamente**: `sp_insert_user_asset` hashea correctamente cuando se llama directamente
- ❌ **No funciona desde WSO2**: WSO2 DataService no ejecuta el stored procedure correctamente
- Probado con: `CALL sp_insert_user_asset(...)` y `{call sp_insert_user_asset(?, ?, ?, ?, ?)}`

### 3. Script JavaScript en el API
- ✅ **Código correcto**: El script está en el lugar correcto (línea 393-408)
- ✅ **API desplegado**: El API se actualizó correctamente
- ❌ **Script NO se ejecuta**: No se ven logs del script en wso2carbon.log
- ❌ **No hay errores**: No aparecen errores relacionados con el script

## Evidencia

1. **Stored Procedure funciona**:
   ```sql
   CALL sp_insert_user_asset('test_direct_sp', 'Test', 'Direct', 'password123', '');
   -- Resultado: usrPassword = '482c811da5d5b4bc6d497ffa98491e38' (32 caracteres) ✓
   ```

2. **Desde WSO2 DataService NO funciona**:
   - Usuarios creados tienen: `usrPassword = 'password123'` (11 caracteres) ✗

3. **Script JavaScript no se ejecuta**:
   - No se ven logs de "ANTES_SCRIPT" ni "DESPUES_SCRIPT"
   - No se ven logs de "password_original" ni "password_md5"
   - No hay errores relacionados con el script

## Posibles Causas

1. **WSO2 MI no soporta script mediator con JavaScript** (o requiere configuración especial)
2. **El script se ejecuta pero falla silenciosamente** (error no visible en logs)
3. **Problema con el scope de las propiedades** (la propiedad no se establece correctamente)
4. **El script mediator requiere una sintaxis diferente** en WSO2 MI

## Próximos Pasos Sugeridos

1. Verificar si WSO2 MI soporta script mediator con JavaScript
2. Probar con class mediator en lugar de script mediator
3. Verificar si hay alguna configuración necesaria para habilitar scripts
4. Revisar documentación específica de WSO2 MI sobre script mediator
