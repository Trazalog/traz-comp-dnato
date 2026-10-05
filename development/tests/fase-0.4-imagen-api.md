# Fase 0.4: Actualizar API para Enviar Imagen a PostgreSQL

## Objetivo
Modificar la API `toolsCOREApi.xml` para que extraiga y envíe los parámetros de imagen (`image_name` e `image`) al DataService.

## Fecha de Inicio
2026-02-15

## Estado
⚠️ **PROBLEMA DETECTADO** - Cambios implementados, pero hay un error al procesar la respuesta del DataService

---

## Cambios Implementados

### Verificación: Propiedades de Imagen Ya Extraídas

**Archivo**: `development/toolsCOREApi.xml` (líneas 239-240)

✅ **Ya están presentes**:
```xml
<property name="usr_image_name" expression="json-eval($.usuario.image_name)"/>
<property name="usr_image" expression="json-eval($.usuario.image)"/>
```

### Cambio: Actualizar payloadFactory para Incluir Imagen

**Archivo**: `development/toolsCOREApi.xml` (línea 280-293)

**Cambio realizado**:
- Agregados `image_name` e `image` al formato JSON del payloadFactory
- Agregados argumentos `$11` y `$12` para `usr_image_name` e `usr_image`

**Código antes**:
```xml
<payloadFactory media-type="json" description="crear usuario">
    <format>{     "_post_usuario":{        "first_name":"$1",        "last_name":"$2",        "email":"$3",        "password_plain":"$4",        "role":"$5",        "status":"$6",        "banned_users":"$7",        "telefono":"$8",        "dni":"$9",        "usernick":"$10"     }  }</format>
    <args>
        <arg evaluator="xml" expression="get-property('usr_firstname')"/>
        <arg evaluator="xml" expression="get-property('usr_lastname')"/>
        <arg evaluator="xml" expression="get-property('usr_email')"/>
        <arg evaluator="xml" expression="get-property('usr_password')"/>
        <arg evaluator="xml" expression="get-property('usr_role')"/>
        <arg evaluator="xml" expression="get-property('usr_status')"/>
        <arg evaluator="xml" expression="get-property('usr_banned_users')"/>
        <arg evaluator="xml" expression="get-property('usr_telefono')"/>
        <arg evaluator="xml" expression="get-property('usr_dni')"/>
        <arg evaluator="xml" expression="get-property('usr_nick')"/>
    </args>
</payloadFactory>
```

**Código después**:
```xml
<payloadFactory media-type="json" description="crear usuario">
    <format>{     "_post_usuario":{        "first_name":"$1",        "last_name":"$2",        "email":"$3",        "password_plain":"$4",        "role":"$5",        "status":"$6",        "banned_users":"$7",        "telefono":"$8",        "dni":"$9",        "usernick":"$10",        "image_name":"$11",        "image":"$12"     }  }</format>
    <args>
        <arg evaluator="xml" expression="get-property('usr_firstname')"/>
        <arg evaluator="xml" expression="get-property('usr_lastname')"/>
        <arg evaluator="xml" expression="get-property('usr_email')"/>
        <arg evaluator="xml" expression="get-property('usr_password')"/>
        <arg evaluator="xml" expression="get-property('usr_role')"/>
        <arg evaluator="xml" expression="get-property('usr_status')"/>
        <arg evaluator="xml" expression="get-property('usr_banned_users')"/>
        <arg evaluator="xml" expression="get-property('usr_telefono')"/>
        <arg evaluator="xml" expression="get-property('usr_dni')"/>
        <arg evaluator="xml" expression="get-property('usr_nick')"/>
        <arg evaluator="xml" expression="get-property('usr_image_name')"/>  <!-- NUEVO -->
        <arg evaluator="xml" expression="get-property('usr_image')"/>         <!-- NUEVO -->
    </args>
</payloadFactory>
```

### Artefactos Afectados
- **toolsCOREApi** v1.0.0
  - Resource: `/usuario` (POST)
  - Endpoint: `http://localhost:8290/tools/core/usuario`

---

## Pruebas a Ejecutar

### Prueba 0.4.1: Prueba Completa de la API con Imagen

#### Criterio de Aprobación
- El request debe ser exitoso (HTTP 200)
- La respuesta debe contener `respuesta.resultado = "ok"`
- La respuesta debe contener `respuesta.usr_id`
- El usuario debe crearse en PostgreSQL con imagen
- El usuario debe crearse en BPM
- El usuario debe crearse en AssetPlanner

#### Datos de la Prueba
```json
{
  "usuario": {
    "usernick": "test_api_img_TIMESTAMP",
    "email": "test_api_TIMESTAMP@test.com",
    "firstname": "Test",
    "lastname": "API",
    "password": "password123",
    "role": "2",
    "status": "approved",
    "banned_users": "unban",
    "telefono": "1234567890",
    "dni": "12345678",
    "business": "Empresa Test",
    "image_name": "foto.jpg",
    "image": "iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg=="
  },
  "bpmSession": null
}
```

**Endpoint probado:**
```
POST http://localhost:8290/tools/core/usuario
```

#### Resultado Esperado
1. HTTP Status: 200 OK
2. Respuesta JSON con estructura:
   ```json
   {
     "respuesta": {
       "resultado": "ok",
       "usr_id": "123"
     }
   }
   ```
3. Usuario creado en PostgreSQL con `image_name` e `image` guardados
4. Usuario creado en BPM
5. Usuario creado en AssetPlanner

#### Resultado Obtenido
*Pendiente de ejecución*

---

### Prueba 0.4.2: Verificar Imagen en PostgreSQL

#### Criterio de Aprobación
- El usuario debe existir en la tabla `seg.users`
- `image_name` debe ser igual al valor enviado
- `image` debe contener datos (BYTEA)
- `LENGTH(image)` debe ser > 0

#### Datos de la Prueba
```sql
SELECT id, email, usernick, image_name, 
       CASE WHEN image IS NULL THEN 'NULL' ELSE CONCAT('BYTEA(', LENGTH(image), ' bytes)') END as image_status
FROM seg.users 
WHERE email = 'test_api_TIMESTAMP@test.com';
```

#### Resultado Esperado
```
 id  | email                    | usernick        | image_name | image_status
-----+--------------------------+-----------------+------------+-------------
 123 | test_api_TIMESTAMP@test.com | test_api_img_TIMESTAMP | foto.jpg   | BYTEA(70 bytes)
```

**Validaciones**:
1. ✅ `image_name` = 'foto.jpg'
2. ✅ `image_status` contiene 'BYTEA' y tamaño > 0

#### Resultado Obtenido
*Pendiente de ejecución*

---

### Prueba 0.4.3: Prueba sin Imagen

#### Criterio de Aprobación
- El request debe ser exitoso
- El usuario debe crearse correctamente
- `image_name` e `image` pueden ser strings vacíos
- En la base de datos, estos campos deben ser NULL

#### Datos de la Prueba
```json
{
  "usuario": {
    "usernick": "test_api_noimg_TIMESTAMP",
    "email": "test_api_noimg_TIMESTAMP@test.com",
    "firstname": "Test",
    "lastname": "SinImagen",
    "password": "password123",
    "role": "2",
    "status": "approved",
    "banned_users": "unban",
    "telefono": "1234567890",
    "dni": "12345678",
    "business": "Empresa Test",
    "image_name": "",
    "image": ""
  },
  "bpmSession": null
}
```

#### Resultado Esperado
1. HTTP Status: 200 OK
2. Usuario creado exitosamente
3. En base de datos: `image_name` e `image` = NULL

#### Resultado Obtenido
*Pendiente de ejecución*

---

### Prueba 0.4.4: Prueba de Validación de Parámetros

#### Criterio de Aprobación
- Si no se envían `image_name` e `image`, la API debe manejarlo correctamente
- Debe crear el usuario con `image_name` e `image` = NULL
- O debe retornar un error apropiado si los parámetros son requeridos

#### Datos de la Prueba
```json
{
  "usuario": {
    "usernick": "test_api_sinparams_TIMESTAMP",
    "email": "test_api_sinparams_TIMESTAMP@test.com",
    "firstname": "Test",
    "lastname": "SinParams",
    "password": "password123",
    "role": "2",
    "status": "approved",
    "banned_users": "unban",
    "telefono": "1234567890",
    "dni": "12345678",
    "business": "Empresa Test"
    // image_name e image NO se envían
  },
  "bpmSession": null
}
```

#### Resultado Esperado
- Opción A: Usuario creado con `image_name` e `image` = NULL (comportamiento esperado si la API maneja NULL)
- Opción B: Error indicando que faltan parámetros (si el DataService los requiere)

#### Resultado Obtenido
*Pendiente de ejecución*

---

## Criterios de Éxito Fase 0.4

- [ ] ✅ API extrae correctamente parámetros de imagen
- [ ] ✅ Imagen se envía correctamente al DataService
- [ ] ✅ Imagen se guarda en PostgreSQL
- [ ] ✅ Funciona con imagen (string base64)
- [ ] ✅ Funciona sin imagen (strings vacíos o NULL)
- [ ] ✅ Respuesta JSON correcta con `respuesta.resultado = "ok"`
- [ ] ✅ Todos los casos de prueba pasan (100%)
- [ ] ✅ No hay regresiones en funcionalidad existente

## Rollback Plan Fase 0.4

Si las pruebas fallan:
1. Revertir cambios en `toolsCOREApi.xml`
2. Restaurar payloadFactory sin parámetros de imagen
3. Verificar que funcionalidad básica sigue funcionando
4. Investigar y corregir

## Notas Técnicas

### Compatibilidad
- El DataService ya acepta `image_name` e `image` (Fase 0.3 completada)
- La API ahora pasa estos parámetros al DataService
- Si los parámetros no se envían, WSO2 puede pasar NULL o strings vacíos

### Validación
- WSO2 API no valida automáticamente si los parámetros están presentes
- El DataService requiere que los parámetros estén presentes (aunque estén vacíos)
- Strings vacíos se convierten a NULL en el stored procedure

## Próximos Pasos

Una vez que la Fase 0.4 esté completada y probada:
1. **Fase 0.5**: Actualizar API para enviar imagen a AssetPlanner

## Ejecución de pruebas (cierre de fase)

- **Script**: `development/scripts/run_fase_04_pruebas.sh` — ejecutar en el host donde corre WSO2 MI.
- **Resultados y checklist**: `development/tests/fase-0.4-pruebas-resultados.md`.

## Fase Considerada "Terminada" Cuando:

1. ✅ **API modificada**: El payloadFactory incluye `image_name` e `image`
2. ✅ **Pruebas exitosas**: Todas las pruebas (0.4.1 a 0.4.4) pasan al 100%
3. ✅ **Verificación técnica**: Los datos se pasan correctamente al DataService
4. ✅ **Respuesta JSON correcta**: La estructura de respuesta es la esperada
5. ✅ **Sin regresiones**: La funcionalidad existente sigue funcionando

