# Fase 0.1: Hashear Password MD5 para AssetPlanner

## Objetivo
Implementar el hash MD5 del password en el stored procedure de MySQL para AssetPlanner, asegurando que el password se almacene correctamente hasheado en la tabla `sisusers`.

## Fecha de Finalización
2026-01-30

## Estado
✅ **COMPLETADA**

---

## Prueba 0.1.1: Prueba Directa del DataService con Password MD5

### Criterio de Aprobación
- El DataService debe crear un usuario en AssetPlanner (tabla `sisusers`)
- El password almacenado debe estar hasheado en MD5
- El hash MD5 debe coincidir con el hash esperado para el password en texto plano

### Artefactos Afectados
- **COREDataService** v1.0.0
  - Query: `setUserAsset`
  - Resource: `/assetuser/add` (POST)
- **AssetPlannerDataSource** v1.0.0
  - Driver: `com.mysql.cj.jdbc.Driver`
  - URL: `jdbc:mysql://10.142.0.13:3306/assetv2`
- **Stored Procedure MySQL**: `sp_insert_user_asset`
  - Base de datos: `assetv2`
  - Tabla: `sisusers`

### Datos de la Prueba
```json
{
  "_post_assetuser_add": {
    "nick": "test_ok_1769787682",
    "name": "Test",
    "lastName": "OK",
    "pass": "password123",
    "image": ""
  }
}
```

**Endpoint probado:**
```
POST http://localhost:8290/services/COREDataService/assetuser/add
```

**Timestamp de la prueba:** 2026-01-30 12:41:18

### Resultado Esperado
1. Usuario creado en la tabla `sisusers` con `usrNick = 'test_ok_1769787682'`
2. Password almacenado: `482c811da5d5b4bc6d497ffa98491e38` (MD5 de 'password123')
3. Longitud del password: 32 caracteres (típico de MD5)
4. Verificación: `usrPassword = MD5('password123')` debe ser verdadero

### Resultado Obtenido
```sql
SELECT usrNick, usrPassword, LENGTH(usrPassword) as pass_length, 
       CASE WHEN usrPassword = MD5('password123') 
            THEN '✓✓✓ HASH CORRECTO - STORED PROCEDURE FUNCIONA ✓✓✓' 
            ELSE CONCAT('✗ Hash incorrecto. Password recibido: ', usrPassword) 
       END as verificacion 
FROM sisusers 
WHERE usrNick = 'test_ok_1769787682';
```

**Resultado:**
```
usrNick: test_ok_1769787682
usrPassword: 482c811da5d5b4bc6d497ffa98491e38
pass_length: 32
verificacion: ✓✓✓ HASH CORRECTO - STORED PROCEDURE FUNCIONA ✓✓✓
```

**Verificación del hash:**
```bash
echo -n "password123" | md5sum
# Resultado: 482c811da5d5b4bc6d497ffa98491e38  ✓ COINCIDE
```

### Conclusión
✅ **PRUEBA APROBADA**

El stored procedure `sp_insert_user_asset` está funcionando correctamente:
- Recibe el password en texto plano desde WSO2 DataService
- Aplica el hash MD5 internamente usando la función `MD5()` de MySQL
- Almacena el password hasheado en la tabla `sisusers`
- El hash generado coincide exactamente con el hash MD5 esperado

---

## Configuración Técnica Implementada

### Drivers JDBC Instalados
- **PostgreSQL**: `postgresql-jdbc.jar` (ubicado en `/home/rodolfo/dev/wso2mi-4.3.0/lib/` y `/dropins/`)
- **MySQL/MariaDB**: `mysql-connector-java-8.0.17.jar` (ubicado en `/home/rodolfo/dev/wso2mi-4.3.0/lib/` y `/dropins/`)

### Stored Procedure MySQL
```sql
DELIMITER //
CREATE PROCEDURE sp_insert_user_asset(
    IN p_usrNick VARCHAR(255),
    IN p_usrName VARCHAR(255),
    IN p_usrLastName VARCHAR(255),
    IN p_usrPassword VARCHAR(255),
    IN p_usrimag TEXT
)
BEGIN
    INSERT INTO sisusers(usrNick, usrName, usrLastName, usrPassword, usrimag)
    VALUES (p_usrNick, p_usrName, p_usrLastName, MD5(p_usrPassword), p_usrimag);
END //
DELIMITER ;
```

### Query en COREDataService
```xml
<query id="setUserAsset" useConfig="AssetPlannerDataSource">
    <sql>CALL sp_insert_user_asset(:nick, :name, :lastName, :pass, :image)</sql>
    <param name="nick" sqlType="STRING"/>
    <param name="name" sqlType="STRING"/>
    <param name="lastName" sqlType="STRING"/>
    <param name="pass" sqlType="STRING"/>
    <param name="image" sqlType="STRING"/>
</query>
```

### Resource en COREDataService
```xml
<resource method="POST" path="/assetuser/add">
    <description>Agrega un usuario en MariaDB</description>
    <call-query href="setUserAsset">
        <with-param name="nick" query-param="nick"/>
        <with-param name="name" query-param="name"/>
        <with-param name="lastName" query-param="lastName"/>
        <with-param name="pass" query-param="pass"/>
        <with-param name="image" query-param="image"/>
    </call-query>
</resource>
```

---

## Notas Técnicas

### Problemas Resueltos
1. **ClassNotFoundException para drivers JDBC**: Resuelto instalando los drivers en `/lib/` y `/dropins/` y reiniciando WSO2 MI
2. **Driver incorrecto para MariaDB**: Corregido cambiando de `org.mariadb.jdbc.Driver` a `com.mysql.cj.jdbc.Driver` (usando MySQL connector compatible con MariaDB)
3. **URL de conexión**: Cambiada de `jdbc:mariadb://` a `jdbc:mysql://` para compatibilidad con el driver MySQL
4. **Hash MD5 en stored procedure**: Implementado usando la función nativa `MD5()` de MySQL dentro del stored procedure

### Decisiones de Diseño
- El hash MD5 se realiza en el stored procedure, no en el API ni en el DataService
- Esto asegura que el hash siempre se aplica, independientemente de cómo se llame al stored procedure
- El password se envía en texto plano desde WSO2, pero se hashea antes de almacenarse

---

## Fase Considerada "Terminada" Porque:

1. ✅ **Funcionalidad implementada**: El hash MD5 se aplica correctamente al crear usuarios en AssetPlanner
2. ✅ **Prueba exitosa**: La prueba 0.1.1 demuestra que el password se almacena correctamente hasheado
3. ✅ **Verificación técnica**: El hash generado coincide exactamente con el hash MD5 esperado
4. ✅ **Artefactos desplegados**: Todos los componentes (CAR, DataService, Datasources) están desplegados y funcionando
5. ✅ **Drivers instalados**: Los drivers JDBC necesarios están instalados y funcionando

La fase 0.1 está completa y lista para continuar con las siguientes fases de la migración.






