# Prueba de API Actor/Role contra Bonita 7.6

**Fecha:** 2025-03-14  
**Objetivo:** Validar que la documentación basada en Bonita 7.11 funciona contra un servidor Bonita 7.6.

## Configuración

- **URL:** http://10.142.0.13:8080/bonita
- **Usuario:** admin (desde .env)
- **Script:** `development/tests/bonita-actor-role-test.sh`

## Resultado: ✅ Prueba exitosa

### Secuencia ejecutada

1. **Login** – OK (HTTP 204)
2. **Listar procesos** – Encontrado: "Inspecciones a Establecimientos" (id=7558850613353527519)
3. **Listar actores** – Encontrado: "Coordinador de Inspecciones" (id=107)
4. **Listar roles** – Encontrado: "Mantenedor" (id=1)
5. **Listar miembros del actor** – Actor tenía 1 membership previo (role 101 + group 101)
6. **POST agregar rol** – Agregado rol "Mantenedor" (id=1) al actor → ActorMember id=3601
7. **Verificación** – Actor tenía 2 miembros: el original + el nuevo
8. **DELETE quitar rol** – Eliminado ActorMember id=3601
9. **Verificación final** – Actor volvió a tener solo el miembro original

## Hallazgo principal: diferencia de paths entre 7.6 y 7.11

| Recurso        | Bonita 7.11 (OpenAPI) | Bonita 7.6 (validado) |
|----------------|----------------------|------------------------|
| Actor members  | `/API/bpm/actorMemberEntry` | `/API/bpm/actorMember` |

En Bonita 7.6 el path correcto es **`actorMember`**, no `actorMemberEntry`. El endpoint `actorMemberEntry` devuelve vacío en 7.6.

## Cuerpo del POST para agregar rol

```json
{
  "actor_id": "107",
  "role_id": "1",
  "group_id": "-1",
  "user_id": "-1"
}
```

- `group_id=-1` y `user_id=-1` indican mapeo solo por rol.
- Respuesta: objeto con `id` del nuevo ActorMember (ej. 3601).

## DELETE para quitar rol

```
DELETE /API/bpm/actorMember/{actorMemberId}
Header: X-Bonita-API-Token: <token>
```

Respuesta esperada: HTTP 200 o 204.

## Conclusión

La lógica documentada para Bonita 7.11 es compatible con 7.6, con esta diferencia:

- **7.6:** usar `/API/bpm/actorMember`
- **7.11:** usar `/API/bpm/actorMemberEntry` (según OpenAPI)

Se recomienda probar ambos paths si se desconoce la versión exacta del servidor.
