# Solución Final para Fase 0.1

## Problema Raíz Identificado
WSO2 DataService NO ejecuta funciones SQL en parámetros. El parámetro `:pass` se escapa como literal.

## Solución: Usar Stored Procedure con Sintaxis JDBC Correcta

El stored procedure `sp_insert_user_asset` funciona correctamente cuando se llama directamente desde MySQL.

### Implementación Final

1. **Stored Procedure en MySQL** (YA CREADO Y FUNCIONANDO):
   ```sql
   CALL sp_insert_user_asset('test', 'Test', 'User', 'password123', '');
   -- Resultado: usrPassword = '482c811da5d5b4bc6d497ffa98491e38' ✓
   ```

2. **DataService debe usar sintaxis JDBC para stored procedures**:
   ```xml
   <query id="setUserAsset" useConfig="AssetPlannerDataSource">
      <sql>{call sp_insert_user_asset(?, ?, ?, ?, ?)}</sql>
      <param name="nick" sqlType="STRING" type="IN"/>
      <param name="name" sqlType="STRING" type="IN"/>
      <param name="lastName" sqlType="STRING" type="IN"/>
      <param name="pass" sqlType="STRING" type="IN"/>
      <param name="image" sqlType="STRING" type="IN"/>
   </query>
   ```

### Alternativa: Hashear en API con Class Mediator

Si el script JavaScript no funciona, crear un class mediator en Java que haga el hash.

