# 🔍 Estado Actual - Error "A result was returned when none was expected"

## Error Persistente
```
DS Code: DATABASE_ERROR
Error in 'SQLQuery.processPreNormalQuery': A result was returned when none was expected.
Nested Exception: org.postgresql.util.PSQLException: A result was returned when none was expected.
```

## Análisis del Problema

### ✅ Lo que funciona:
- Stored procedure funciona perfectamente cuando se llama directamente desde PostgreSQL
- Parámetros llegan correctamente al Data Service
- Data Service está desplegado y funcionando

### ❌ Lo que NO funciona:
- WSO2 Data Services está usando `executeUpdate()` en lugar de `executeQuery()`
- Esto causa el error cuando el stored procedure retorna un valor

## Intentos Realizados

1. ✅ Eliminado parámetro `image` (BINARY) - Simplificado
2. ✅ Eliminado parámetro `image_name` - Simplificado
3. ✅ Eliminado parámetro `depo_id` - Pasando NULL directamente
4. ✅ Cambiado de `returnGeneratedKeys="true"` a result mapping normal
5. ✅ Probado con `outputType="json"`
6. ✅ Probado con `element` format
7. ❌ Todos los intentos resultan en el mismo error

## Posibles Soluciones

### Opción 1: Cambiar el stored procedure
Modificar el stored procedure para que no retorne un valor directamente, sino que use `RETURNING id` en el INSERT:
```sql
-- En lugar de RETURN v_user_id;
-- Usar RETURNING id en el INSERT
```

**Problema**: Requiere modificar el stored procedure y puede afectar otros usos.

### Opción 2: Usar un wrapper query
Crear un query que envuelva el stored procedure de manera diferente:
```sql
SELECT * FROM (SELECT seg.insert_usuario_con_hash(...) as id) AS result;
```

**Problema**: Puede no resolver el problema si WSO2 sigue detectando como UPDATE.

### Opción 3: Usar DO block
Usar un bloque DO de PostgreSQL para llamar al stored procedure:
```sql
DO $$
DECLARE
    v_id INTEGER;
BEGIN
    v_id := seg.insert_usuario_con_hash(...);
    -- Pero esto no retorna el valor...
END $$;
```

**Problema**: No retorna el valor directamente.

### Opción 4: Cambiar a INSERT directo
En lugar de usar stored procedure, hacer el INSERT directamente en el Data Service y manejar el hash en el lado de la aplicación o en otro lugar.

**Problema**: Perdemos la centralización del hash de password.

### Opción 5: Verificar configuración de WSO2
Puede haber una configuración en WSO2 que fuerza el uso de `executeUpdate()` para ciertos tipos de queries.

## Recomendación

**Opción más viable**: Cambiar el stored procedure para usar `RETURNING id` en el INSERT en lugar de `RETURN v_user_id;`. Esto debería hacer que WSO2 lo trate como un INSERT normal con `returnGeneratedKeys="true"`.

## Próximos Pasos

1. Modificar el stored procedure `seg.insert_usuario_con_hash` para usar `RETURNING id`
2. Actualizar el query en el Data Service para usar `returnGeneratedKeys="true"`
3. Probar nuevamente

