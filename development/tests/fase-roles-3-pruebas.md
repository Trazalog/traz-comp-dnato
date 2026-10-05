# Fase Roles 3 – Validar usuario en getInfoBPM

## Fecha
2026-03-14

## Objetivo
Validar que `Roles->getInfoBPM()` compruebe la respuesta HTTP y la estructura del payload antes de acceder a `$aux->payload[0]`. Si el usuario no existe en BPM o la API falla, debe retornar `null` sin provocar errores PHP.

---

## Contexto en el plan

| Fase | Descripción | Estado |
|------|-------------|--------|
| Roles 1 | Validar respuesta HTTP en guardarMembershipBPM | Cerrada |
| Roles 2 | Validar errores en Main->guardarMembership | Cerrada |
| **Roles 3** | Validar usuario en getInfoBPM | En pruebas |
| Roles 4 | Externalizar sesiones BPM | Pendiente |
| Roles 5 | Corregir changeLevelRolUserObject y guardarRolesUsuario | Pendiente |

---

## Cambios implementados (Fase Roles 3)

### Archivo: `application/models/Roles.php`

**Método `getInfoBPM()`**:

1. Guardar resultado de `callAPI()` en `$result` antes de decodificar.
2. Validar `$result['status']` y `$result['code']`: si falla, loguear y retornar `null`.
3. Decodificar JSON y validar que `$aux` tenga `payload` como array no vacío.
4. Si no existe usuario: retornar `null` y loguear.

**Método `deleteMembershipBPM()`**:

- Añadida validación de `$info_bpm` antes de usar `$info_bpm->id`: si es `null`, retornar `null`.

---

## Pruebas a ejecutar

### Prueba 3.1: getInfoBPM con usernick existente

**Pasos**:

1. Usar un `usernick` que exista en Bonita BPM (ej. el de un usuario creado por la app).
2. Llamar indirectamente vía asignación de rol: ir a Cambio de Rol, asignar un rol a ese usuario y guardar.

**Resultado esperado**:

- La asignación se completa correctamente.
- En logs DEBUG: `getInfoBPM` con `$info_bpm` conteniendo objeto con `id`.

### Prueba 3.2: getInfoBPM con usernick inexistente

**Pasos**:

1. Crear o usar un usuario en `seg.users` cuyo `usernick` **no** exista en Bonita BPM.
2. Intentar asignarle un rol desde la pantalla Cambio de Rol.

**Resultado esperado**:

- No debe aparecer error PHP ("Trying to get property of non-object", "Undefined offset").
- Debe mostrarse mensaje de error al usuario (ej. "Fallo asignación de roles Bpm").
- En logs: mensaje de ERROR "Usuario no encontrado en BPM" o similar.

---

## Checklist de cierre Fase Roles 3

- [ ] 3.1 – getInfoBPM(usernick_existente): retorna objeto con id
- [ ] 3.2 – getInfoBPM(usernick_inexistente): retorna null sin error PHP

---

## Referencias

- Plan: corrección roles y documentación
- Modelo: `application/models/Roles.php`
