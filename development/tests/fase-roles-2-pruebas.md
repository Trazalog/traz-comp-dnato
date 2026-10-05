# Fase Roles 2 – Validar errores en Main->guardarMembership

## Fecha
2026-03-14

## Objetivo
Validar que `Main->guardarMembership()` compruebe el resultado de `user_model->guardarMembership()` y de `Roles->guardarMembershipBPM()`. Si falla alguno, debe retornar error, hacer rollback cuando falle BPM, y devolver JSON estructurado si la petición es AJAX.

---

## Contexto en el plan

| Fase | Descripción | Estado |
|------|-------------|--------|
| Roles 1 | Validar respuesta HTTP en guardarMembershipBPM | Cerrada |
| **Roles 2** | Validar errores en Main->guardarMembership | En pruebas |
| Roles 3 | Validar usuario en getInfoBPM | Pendiente |
| Roles 4 | Externalizar sesiones BPM | Pendiente |
| Roles 5 | Corregir changeLevelRolUserObject y guardarRolesUsuario | Pendiente |

---

## Cambios implementados (Fase Roles 2)

### Archivo: `application/controllers/Main.php`

**Método `guardarMembership()`**:

1. Validar `$resp` de `user_model->guardarMembership()`: si falla, llamar a `_guardarMembershipError()` y retornar `false`.
2. Validar `$infoUser` de `getUserInfoByEmail()`: si es null o no tiene `usernick`, llamar a `_guardarMembershipError()` y retornar `false`.
3. Validar respuesta de `Roles->guardarMembershipBPM()`: si retorna `null` o no tiene `payload->user_id`, hacer rollback con `borrarMembership()` y retornar `false`.
4. En éxito, llamar a `_guardarMembershipSuccess()`.

**Nuevos métodos privados**:

- `_guardarMembershipError($msg)`: loguea, setea flashdata y, si es AJAX, envía JSON `{success: false, message: ...}` con HTTP 400.
- `_guardarMembershipSuccess()`: setea flashdata y, si es AJAX, envía JSON `{success: true, message: ...}` con HTTP 200.

**Nota**: El flujo principal de la pantalla Cambio de Rol usa `changeLevelRolUser` vía `guardarRolesUsuario`. El método `guardarMembership` se usa desde `changelevel.php` (modal associaterol) donde el AJAX está comentado. Las correcciones aplican cuando se reactive o se llame desde otro punto.

---

## Pruebas a ejecutar

### Prueba 2.1: Llamar guardarMembership con datos válidos (éxito)

**Pasos** (requiere descomentar el AJAX en changelevel.php o llamar vía curl/Postman):

1. Loguearse como admin.
2. Enviar POST a `main/guardarMembership` con:
   - `membership`: `{email: "usuario_existente@test.com", group: "NombreGrupo", role: "NombreRol", usuario_app: "admin_nick"}`
   - `membershipBPM`: `{group_id: "ID_GRUPO_BPM", role_id: "ID_ROL_BPM"}`

**Resultado esperado**:

- Si es AJAX: HTTP 200, JSON `{success: true, message: "Rol asignado correctamente."}`.
- Si no es AJAX: redirect/vista con flashdata de éxito.

### Prueba 2.2: Llamar con email inexistente

**Pasos**:

1. Enviar POST con `membership.email` de un usuario que no exista en `seg.users`.

**Resultado esperado**:

- No debe retornar siempre éxito.
- Debe retornar error (HTTP 400 si AJAX, o flash_message con mensaje de error).
- En logs: mensaje de ERROR con "Usuario no encontrado".

### Prueba 2.3: Simular fallo en BPM (rollback)

**Pasos**:

1. Usar datos válidos para BD pero que provoquen fallo en BPM (ej. group_id o role_id inválidos, o usuario sin crear en BPM).
2. Verificar que no se inserta en `seg.memberships_users` o que se revierte.

**Resultado esperado**:

- Mensaje de error indicando fallo en BPM.
- No debe quedar registro huérfano en `seg.memberships_users` (rollback correcto).

---

## Checklist de cierre Fase Roles 2

- [ ] 2.1 – guardarMembership con datos válidos: éxito
- [ ] 2.2 – Email inexistente: retorna error (no siempre true)
- [ ] 2.3 – Fallo BPM: rollback y mensaje de error

---

## Referencias

- Plan: corrección roles y documentación
- Controlador: `application/controllers/Main.php`
- Vista changelevel: `application/views/changelevel.php`
