# 🔍 Análisis del Error NullPointerException

## Error Persistente
```
DS Code: DATABASE_ERROR
Error in 'SQLQuery.processPreNormalQuery': null
Nested Exception: java.lang.NullPointerException
```

## Estado Actual

### ✅ Lo que funciona:
- Stored procedure funciona perfectamente cuando se llama directamente
- Parámetros llegan correctamente al Data Service
- API está desplegado y funcionando

### ❌ Lo que NO funciona:
- Data Service falla con NullPointerException al procesar la query
- El error ocurre en `processPreNormalQuery`, ANTES de ejecutar la query

## Posibles Causas

1. **Problema con la sintaxis del SQL en WSO2**
   - Tal vez WSO2 no puede procesar stored procedures con muchos parámetros
   - Problema con cómo se formatea el SQL

2. **Problema con el Data Source**
   - Tal vez hay un problema de conexión o configuración
   - El Data Source puede no estar configurado correctamente

3. **Problema con el stored procedure en WSO2**
   - WSO2 puede tener problemas llamando stored procedures de PostgreSQL
   - Puede necesitar permisos especiales

4. **Data Service no redesplegado**
   - Los cambios pueden no haberse aplicado
   - Necesita reinicio completo de WSO2

## Próximos Pasos Sugeridos

1. **Verificar logs detallados de WSO2**
   ```bash
   tail -f /ruta/wso2/repository/logs/wso2carbon.log | grep -i "setUsuario\|insert_usuario"
   ```

2. **Probar con query INSERT directo** (sin stored procedure)
   - Para verificar si el problema es el stored procedure o algo más

3. **Verificar configuración del Data Source**
   - Confirmar que ToolsDataSource está correctamente configurado
   - Verificar conectividad desde WSO2 a PostgreSQL

4. **Probar stored procedure desde WSO2 directamente**
   - Usar la consola de WSO2 para probar el query manualmente

