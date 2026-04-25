# 📋 Guía de Despliegue - API Crear Usuario

## 🎯 Objetivo
Desplegar y probar la nueva API `POST /usuario` que centraliza la creación de usuarios en WSO2, reemplazando las llamadas directas a base de datos desde PHP.

---

## 📦 Artefactos Necesarios

### 1. **Base de Datos (PostgreSQL)**
- ✅ `sp_insert_usuario_hash.sql` - Stored Procedure para hash de passwords
  - Ubicación: `_backend/api/dataservices/sp_insert_usuario_hash.sql`
  - Función: `seg.insert_usuario_con_hash()`
  - Requisito: Extensión `pgcrypto`

### 2. **WSO2 Data Service**
- ✅ `COREDataService.dbs` - Data Service con queries
  - Ubicación: `_backend/api/dataservices/COREDataService.dbs`
  - Queries:
    - `setUsuario` → Llama al stored procedure
    - `checkUsuarioDuplicado` → Valida emails duplicados
    - `setUserBusiness` → Inserta en `seg.users_business`
  - Resources:
    - `POST /COREDataService/usuario`
    - `GET /COREDataService/usuario/duplicado/{email}`
    - `POST /COREDataService/users_business`

### 3. **WSO2 API**
- ✅ `toolsCOREAPI.xml` - API de orquestación
  - Ubicación: `_backend/api/toolsCOREAPI.xml`
  - Resource: `POST /usuario`
  - Flujo completo: validación → PostgreSQL → users_business → Asset Planner → BPM

---

## 🔄 Orden de Despliegue y Prueba

### **FASE 1: Base de Datos (PostgreSQL)**

#### Paso 1.1: Habilitar extensión pgcrypto
```sql
-- Conectar a la base de datos PostgreSQL
psql -U postgres -d tu_base_de_datos

-- Ejecutar:
CREATE EXTENSION IF NOT EXISTS pgcrypto;

-- Verificar:
SELECT * FROM pg_extension WHERE extname = 'pgcrypto';
```

#### Paso 1.2: Crear Stored Procedure
```bash
# Desde el servidor donde está PostgreSQL
psql -U postgres -d tu_base_de_datos -f _backend/api/dataservices/sp_insert_usuario_hash.sql
```

**Validación:**
```sql
-- Verificar que la función existe
SELECT proname, prosrc 
FROM pg_proc 
WHERE proname = 'insert_usuario_con_hash';

-- Probar la función manualmente (opcional)
SELECT seg.insert_usuario_con_hash(
    'Test',           -- first_name
    'User',           -- last_name
    'test@test.com',  -- email
    'password123',    -- password_plain
    'subscriber',     -- role
    'active',         -- status
    '',               -- banned_users
    '',               -- telefono
    '',               -- dni
    'testuser',       -- usernick
    NULL,             -- depo_id
    NULL,             -- image_name
    NULL              -- image
);
```

**Resultado esperado:** Debe retornar un `id` (INTEGER) del usuario creado.

---

### **FASE 2: WSO2 Data Service**

#### Paso 2.1: Desplegar COREDataService.dbs
```bash
# Copiar el archivo al servidor WSO2
scp _backend/api/dataservices/COREDataService.dbs usuario@wso2-server:/ruta/wso2/repository/deployment/server/dataservices/

# O usar la consola de WSO2:
# 1. Login a WSO2 Management Console (https://wso2-server:9443/console)
# 2. Services → Data Services → Create
# 3. Upload COREDataService.dbs
```

#### Paso 2.2: Verificar Data Service
- Acceder a: `https://wso2-server:9443/services/COREDataService?wsdl`
- Verificar que aparezcan los nuevos operations:
  - `setUsuario`
  - `checkUsuarioDuplicado`
  - `setUserBusiness`

#### Paso 2.3: Probar Queries Individualmente

**A) Probar checkUsuarioDuplicado:**
```bash
curl -X GET "https://wso2-server:9443/services/COREDataService/usuario/duplicado/test@test.com" \
  -H "Authorization: Basic base64(user:pass)"
```

**Respuesta esperada:**
```json
{
  "respuesta": {
    "existe": "false"
  }
}
```

**B) Probar setUsuario:**
```bash
curl -X POST "https://wso2-server:9443/services/COREDataService/usuario" \
  -H "Content-Type: application/json" \
  -H "Authorization: Basic base64(user:pass)" \
  -d '{
    "_post_usuario": {
      "first_name": "Test",
      "last_name": "User",
      "email": "test@test.com",
      "password_plain": "password123",
      "role": "subscriber",
      "status": "active",
      "banned_users": "",
      "telefono": "",
      "dni": "",
      "usernick": "testuser",
      "depo_id": null,
      "image_name": null,
      "image": null
    }
  }'
```

**Respuesta esperada:**
```json
{
  "GeneratedKeys": {
    "Entry": [
      {
        "ID": 123
      }
    ]
  }
}
```

**C) Probar setUserBusiness:**
```bash
curl -X POST "https://wso2-server:9443/services/COREDataService/users_business" \
  -H "Content-Type: application/json" \
  -H "Authorization: Basic base64(user:pass)" \
  -d '{
    "_post_users_business": {
      "email": "test@test.com",
      "busines": "empresa_test"
    }
  }'
```

---

### **FASE 3: WSO2 API (toolsCOREAPI)**

#### Paso 3.1: Desplegar toolsCOREAPI.xml
```bash
# Copiar el archivo al servidor WSO2
scp _backend/api/toolsCOREAPI.xml usuario@wso2-server:/ruta/wso2/repository/deployment/synapse-configs/default/api/

# O usar la consola de WSO2:
# 1. Login a WSO2 API Manager (https://wso2-server:9443/publisher)
# 2. Create API → Import → Upload toolsCOREAPI.xml
```

#### Paso 3.2: Verificar API
- Acceder a: `https://wso2-server:8243/toolsCOREAPI/1.0.0/usuario` (o la URL configurada)
- Verificar que el resource `POST /usuario` esté disponible

#### Paso 3.3: Probar API Completa

**Preparar datos de prueba:**
```json
{
  "bpmSession": "session_token_aqui",
  "usuario": {
    "firstname": "Test",
    "lastname": "User",
    "email": "test@test.com",
    "password": "password123",
    "role": "subscriber",
    "status": "active",
    "banned_users": "",
    "telefono": "",
    "dni": "",
    "usernick": "testuser",
    "depo_id": null,
    "image_name": null,
    "image": null,
    "business": "empresa_test"
  }
}
```

**Ejecutar prueba:**
```bash
curl -X POST "https://wso2-server:8243/toolsCOREAPI/1.0.0/usuario" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer token_aqui" \
  -d @payload_test.json
```

**Respuesta esperada (éxito):**
```json
{
  "respuesta": {
    "resultado": "ok",
    "usr_id": "123",
    "bpmSession": "session_token_aqui"
  }
}
```

**Respuesta esperada (error - duplicado):**
```json
{
  "error": {
    "mensaje": "El usuario ya existe",
    "tipo": "Usuario duplicado"
  }
}
```

---

### **FASE 4: Validación End-to-End**

#### Paso 4.1: Verificar en Base de Datos
```sql
-- Verificar usuario creado
SELECT id, email, first_name, last_name, role, status, password
FROM seg.users
WHERE email = 'test@test.com';

-- Verificar formato del password hash
-- Debe ser: sha256:1000:salt:hash
SELECT 
  email,
  substring(password, 1, 12) as hash_format,
  length(password) as hash_length
FROM seg.users
WHERE email = 'test@test.com';

-- Verificar users_business
SELECT * FROM seg.users_business WHERE email = 'test@test.com';
```

#### Paso 4.2: Validar Compatibilidad con PHP
```php
// Probar que el hash generado por PostgreSQL es compatible con PHP
$hash_from_db = "sha256:1000:..."; // Hash generado por PostgreSQL
$password = "password123";

// Debe retornar true
$result = $this->password->check_password($password, $hash_from_db);
```

#### Paso 4.3: Verificar en BPM (Bonita)
- Acceder a Bonita BPM
- Verificar que el usuario fue creado con el `usernick` correcto

#### Paso 4.4: Verificar en Asset Planner (MariaDB)
```sql
-- Conectar a MariaDB de Asset Planner
SELECT * FROM sisusers WHERE nick = 'testuser';
```

---

## ✅ Checklist de Validación

### Base de Datos
- [ ] Extensión `pgcrypto` habilitada
- [ ] Stored procedure `seg.insert_usuario_con_hash()` creado
- [ ] Función retorna `id` del usuario creado
- [ ] Hash generado tiene formato `sha256:1000:salt:hash`
- [ ] Usuario insertado correctamente en `seg.users`
- [ ] Relación creada en `seg.users_business`

### WSO2 Data Service
- [ ] `COREDataService.dbs` desplegado
- [ ] Query `setUsuario` funciona
- [ ] Query `checkUsuarioDuplicado` funciona
- [ ] Query `setUserBusiness` funciona
- [ ] Resources REST disponibles

### WSO2 API
- [ ] `toolsCOREAPI.xml` desplegado
- [ ] Resource `POST /usuario` disponible
- [ ] Validación de duplicados funciona
- [ ] Creación en PostgreSQL funciona
- [ ] Creación en `users_business` funciona
- [ ] Creación en BPM funciona (o rollback si falla)
- [ ] Creación en Asset Planner funciona (o continúa si falla)

### Integración
- [ ] Hash compatible con PHP `Password` library
- [ ] Usuario puede hacer login con password original
- [ ] Rollback funciona si falla BPM
- [ ] Logs de WSO2 muestran flujo correcto

---

## 🐛 Troubleshooting

### Error: "Extension pgcrypto is required"
**Solución:** Ejecutar `CREATE EXTENSION IF NOT EXISTS pgcrypto;`

### Error: "Function seg.insert_usuario_con_hash does not exist"
**Solución:** Verificar que el stored procedure fue creado en el schema correcto

### Error: "Unable to connect to datasource"
**Solución:** Verificar configuración de `ToolsDataSource` en WSO2

### Error: "Usuario duplicado" cuando no debería serlo
**Solución:** Verificar query `checkUsuarioDuplicado` y formato del email

### Error: "POST /bpm/users con problemas"
**Solución:** 
- Verificar que `bpmSession` sea válido
- Verificar conectividad con Bonita BPM
- Verificar que el rollback elimine el usuario de PostgreSQL

### Hash no compatible con PHP
**Solución:** 
- Verificar formato del hash: debe ser `sha256:1000:salt:hash`
- Comparar con hash generado por PHP para el mismo password
- Ajustar stored procedure si es necesario

---

## 📝 Notas Importantes

1. **Password Hashing:** El stored procedure genera un hash PBKDF2 que debe ser compatible con la librería PHP `Password`. Si hay incompatibilidad, puede ser necesario ajustar el algoritmo.

2. **Rollback:** Si la creación en BPM falla, el API automáticamente elimina el usuario de PostgreSQL. Verificar que este rollback funcione correctamente.

3. **Asset Planner:** Si la creación en Asset Planner falla, el API continúa (no hace rollback). Esto es intencional según el diseño.

4. **BPM Session:** El API requiere un `bpmSession` válido. Asegurarse de obtenerlo antes de llamar al API.

5. **Logs:** Revisar logs de WSO2 en `/ruta/wso2/repository/logs/` para debugging.

---

## 🔄 Próximos Pasos (Después de Validar)

1. **Modificar PHP Controller:**
   - Actualizar `User_model->addUser()` para usar el nuevo API
   - Actualizar `User_model->isDuplicate()` para usar el nuevo API

2. **Testing:**
   - Probar creación desde la interfaz web
   - Probar casos de error (duplicados, BPM falla, etc.)
   - Validar que usuarios existentes pueden hacer login

3. **Documentación:**
   - Actualizar diagramas de secuencia
   - Documentar el nuevo flujo en README

---

**Última actualización:** 2025-01-XX
**Versión:** 1.0.0

