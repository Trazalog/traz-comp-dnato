# Bonita BPM 7.11: Obtener actores de un proceso y asignar roles mediante REST API

Este documento describe la secuencia de llamadas a la API REST de Bonita BPM 7.11 para:
1. Obtener los actores de un proceso
2. Consultar los roles/miembros actuales de cada actor
3. Asignar nuevos roles a cada actor

**Referencias:** Especificación OpenAPI (`doc/bonita/bonita-openapi-0.0.1.yaml`), documentación oficial de Bonita.

---

## 1. Conceptos clave

### Actores (Actors)
- Los **actores** son placeholders definidos en el proceso que indican quién puede ejecutar tareas o iniciar instancias.
- Cada actor pertenece a un proceso (tiene `process_id`).
- Los actores se definen en el diseño del proceso (por ejemplo en Bonita Studio).

### Actor Members (ActorMember)
- Un **ActorMember** es la asociación entre la organización y un actor.
- Tipos de miembro (`member_type`): `USER`, `ROLE`, `GROUP`, `roleAndGroup` (membership = rol en un grupo).
- Para asignar un actor a usuarios se usan: rol, grupo, usuario concreto o membership (rol+grupo).

### Roles
- Los **roles** se gestionan en la API Identity (`/API/identity/role`).
- Un membership asocia usuario + grupo + rol.

---

## 2. Autenticación (obligatoria)

Todas las llamadas requieren autenticación. Para métodos POST, PUT y DELETE también se necesita el header `X-Bonita-API-Token`.

### 2.1 Login

```http
POST /bonita/loginservice
Content-Type: application/x-www-form-urlencoded

username=install&password=install&redirect=false&redirectURL=
```

**Respuesta:** La respuesta incluye:
- Cookie `JSESSIONID` (debe enviarse en todas las peticiones siguientes)
- Cookie `X-Bonita-API-Token` (valor para el header CSRF)

### 2.2 Headers para operaciones de escritura

Para **POST**, **PUT** y **DELETE** incluir:

```http
X-Bonita-API-Token: <valor de la cookie X-Bonita-API-Token>
```

---

## 3. Secuencia de llamadas

### Paso 1: Obtener el ID del proceso (si no se conoce)

```http
GET /bonita/API/bpm/process?p=0&c=10&f=name=<nombre_proceso>
```

O por ID si ya se conoce:

```http
GET /bonita/API/bpm/process/{processId}
```

**Respuesta:** Objeto con `id` (processId), `name`, `version`, etc.

---

### Paso 2: Obtener los actores del proceso

```http
GET /bonita/API/bpm/actor?p=0&c=100&f=process_id=<processId>
```

**Parámetros:**
- `p`: índice de página (0, 1, 2...)
- `c`: cantidad de resultados por página
- `f=process_id=<processId>`: filtro obligatorio por ID de proceso

**Respuesta:** Array de actores, por ejemplo:

```json
[
  {
    "id": "1",
    "process_id": "4717422838168315799",
    "description": null,
    "name": "employee",
    "displayName": "Employee actor"
  },
  {
    "id": "2",
    "process_id": "4717422838168315799",
    "description": null,
    "name": "manager",
    "displayName": "Manager actor"
  }
]
```

---

### Paso 3: Obtener los miembros actuales de cada actor

Para cada actor obtenido en el paso 2:

```http
GET /bonita/API/bpm/actorMemberEntry?p=0&c=100&f=actor_id=<actorId>
```

**Bonita 7.6:** usar `/API/bpm/actorMember` en lugar de `actorMemberEntry`.

**Parámetros:**
- `f=actor_id=<actorId>`: filtro obligatorio por ID de actor
- Opcionales: `f=member_type=user|role|group|roleAndGroup`, `f=role_id=`, `f=group_id=`, `f=user_id=`

**Respuesta:** Array de ActorMembers, por ejemplo:

```json
[
  {
    "id": "206",
    "actor_id": "2",
    "role_id": "4",
    "group_id": "8",
    "user_id": "-1"
  }
]
```

**Interpretación:**
- `role_id` > 0: el actor está mapeado a un rol
- `group_id` > 0: mapeado a un grupo
- `user_id` > 0: mapeado a un usuario concreto
- `-1` indica que ese campo no aplica para ese tipo de miembro

---

### Paso 4: Obtener los roles disponibles (Identity API)

Para asignar un rol nuevo, primero hay que conocer su ID:

```http
GET /bonita/API/identity/role?p=0&c=100
```

O buscar por nombre:

```http
GET /bonita/API/identity/role?p=0&c=100&s=<nombre_rol>
```

**Respuesta:** Array de roles con `id`, `name`, etc.

---

### Paso 5: Asignar un nuevo rol a un actor

Según la convención REST de Bonita (crear recurso con POST), se usa:

```http
POST /bonita/API/bpm/actorMemberEntry
Content-Type: application/json
X-Bonita-API-Token: <token>

{
  "actor_id": "<actorId>",
  "role_id": "<roleId>",
  "group_id": "-1",
  "user_id": "-1"
}
```

**Bonita 7.6:** usar `/API/bpm/actorMember` en lugar de `actorMemberEntry`.

**Para mapear por rol únicamente:**
- `actor_id`: ID del actor
- `role_id`: ID del rol a asignar
- `group_id`: `-1`
- `user_id`: `-1`

**Para mapear por membership (rol + grupo):**
- `actor_id`: ID del actor
- `role_id`: ID del rol
- `group_id`: ID del grupo
- `user_id`: `-1`

**Para mapear a un usuario concreto:**
- `actor_id`: ID del actor
- `role_id`: `-1`
- `group_id`: `-1`
- `user_id`: ID del usuario

**Nota:** La especificación OpenAPI 0.0.1 no documenta explícitamente el POST para `actorMemberEntry`. Si este endpoint no funciona en tu instalación, puede ser necesario usar una [REST API Extension](https://documentation.bonitasoft.com/bonita/latest/api/rest-api-extensions) o la Java API (`ProcessRuntimeAPI`).

---

### Paso 6 (opcional): Eliminar una asignación de rol

Para quitar un ActorMember:

```http
DELETE /bonita/API/bpm/actorMemberEntry/<actorMemberId>
X-Bonita-API-Token: <token>
```

**Bonita 7.6:** usar `/API/bpm/actorMember/<id>` en lugar de `actorMemberEntry`.

---

## 4. Flujo completo resumido

```
1. POST /loginservice                    → Autenticación
2. GET  /API/bpm/process?f=name=...     → Obtener processId
3. GET  /API/bpm/actor?f=process_id=... → Listar actores del proceso
4. GET  /API/identity/role              → Listar roles disponibles
5. Para cada actor donde se quiera agregar un rol:
   POST /API/bpm/actorMemberEntry       → Crear ActorMember (actor_id, role_id, -1, -1)
6. GET  /API/bpm/actorMemberEntry?f=actor_id=... → Verificar asignaciones
```

---

## 5. Ejemplo con cURL

```bash
# 1. Login (guardar cookies)
curl -c cookies.txt -X POST "http://localhost:8080/bonita/loginservice" \
  -H "Content-Type: application/x-www-form-urlencoded" \
  -d "username=install&password=install&redirect=false"

# 2. Obtener actores del proceso (reemplazar PROCESS_ID)
curl -b cookies.txt "http://localhost:8080/bonita/API/bpm/actor?p=0&c=100&f=process_id%3DPROCESS_ID"

# 3. Obtener miembros de un actor (reemplazar ACTOR_ID)
curl -b cookies.txt "http://localhost:8080/bonita/API/bpm/actorMemberEntry?p=0&c=100&f=actor_id%3DACTOR_ID"

# 4. Asignar rol a actor (reemplazar TOKEN, ACTOR_ID, ROLE_ID)
curl -b cookies.txt -X POST "http://localhost:8080/bonita/API/bpm/actorMemberEntry" \
  -H "Content-Type: application/json" \
  -H "X-Bonita-API-Token: TOKEN" \
  -d '{"actor_id":"ACTOR_ID","role_id":"ROLE_ID","group_id":"-1","user_id":"-1"}'
```

---

## 6. Compatibilidad Bonita 7.6 vs 7.11

**Validado contra servidor Bonita 7.6** (2025-03-14). Diferencia importante:

| Recurso       | Bonita 7.11 (OpenAPI) | Bonita 7.6 (validado) |
|---------------|-----------------------|------------------------|
| Actor members | `/API/bpm/actorMemberEntry` | `/API/bpm/actorMember` |

En **7.6** usar `actorMember`; en **7.11** usar `actorMemberEntry`. Si no se conoce la versión, probar ambos paths.

**Prueba ejecutada:** `development/tests/bonita-actor-role-test.sh`  
**Resultado:** Ver `development/tests/bonita-actor-role-prueba-7.6.md`

---

## 7. Referencias

- [Bonita REST API Overview](https://documentation.bonitasoft.com/bonita/latest/api/rest-api-overview)
- [Bonita API Index](https://documentation.bonitasoft.com/bonita/latest/api/api-index)
- [Manage organization and actor mapping](https://documentation.bonitasoft.com/bonita/next/identity/approaches-to-managing-organizations-and-actor-mapping)
- OpenAPI local: `doc/bonita/bonita-openapi-0.0.1.yaml`
