# Implementación con Stored Procedure - Fase 0.1

## Cambios Realizados

### 1. API (toolsCOREApi.xml)
- ✅ Removido class mediator HashMD5Mediator
- ✅ El API ahora envía el password en texto plano al DataService
- ✅ El password se hasheará en el stored procedure

### 2. DataService (COREDataService.xml)
- ✅ Query `setUserAsset` actualizado para usar stored procedure:
  ```xml
  <sql>{call sp_insert_user_asset(?, ?, ?, ?, ?)}</sql>
  <param name="nick" sqlType="STRING" paramType="SCALAR" type="IN"/>
  <param name="name" sqlType="STRING" paramType="SCALAR" type="IN"/>
  <param name="lastName" sqlType="STRING" paramType="SCALAR" type="IN"/>
  <param name="pass" sqlType="STRING" paramType="SCALAR" type="IN"/>
  <param name="image" sqlType="STRING" paramType="SCALAR" type="IN"/>
  ```

### 3. Stored Procedure en MySQL
- ✅ Creado: `sp_insert_user_asset`
- ✅ Funciona correctamente cuando se llama directamente
- ✅ Hashea el password en MD5 antes de insertar

## Estado Actual

- Archivos actualizados y desplegados
- Stored procedure creado y funcionando
- Pendiente: Verificar que WSO2 DataService ejecute el stored procedure correctamente

## Próxima Prueba

Ejecutar creación de usuario y verificar que el password esté hasheado en MD5 (32 caracteres hexadecimales).

