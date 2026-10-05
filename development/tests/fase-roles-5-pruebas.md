# Fase Roles 5 – Corregir changeLevelRolUserObject y guardarRolesUsuario

## Fecha
2026-03-14

## Objetivo
Corregir los bugs en `changeLevelRolUserObject` (uso de índice en bucle, sobrescritura de variable, return prematuro) y en `guardarRolesUsuario` (solo enviaba el último rol). Unificar el flujo para que siempre use arrays y `changeLevelRolUserObject`.

---

## Contexto en el plan

| Fase | Descripción | Estado |
|------|-------------|--------|
| Roles 1–4 | Validaciones, errores, getInfoBPM, sesiones | Cerradas |
| **Roles 5** | changeLevelRolUserObject y guardarRolesUsuario | En pruebas |

---

## Cambios implementados (Fase Roles 5)

### Archivo: `application/controllers/Main.php`

**Método `changeLevelRolUserObject()`**:

1. Usar variable `$guardado` para el resultado de `guardarMembership`, no sobrescribir `$dataRole`.
2. Pasar `$dataRoleBpm[$i]` a `guardarMembershipBPM` (con fallback a `$dataRoleBpm` si es objeto único).
3. Mover `return true` fuera del bucle; retornar éxito solo tras procesar todos los roles.
4. Si falla BPM: rollback de los memberships guardados en esa iteración.
5. Añadir `usuario_app` correctamente en cada elemento del array (`$dataRole[$i]['usuario_app']`).
6. Validar `$infoUser` antes de usar.
7. Respuesta AJAX: HTTP 400 en error, 200 en éxito, con JSON `{success, message}`.

**Nuevo método**: `_changeLevelRolUserObjectResponse($success, $message)` para enviar respuesta JSON en peticiones AJAX.

### Archivo: `application/views/changeleveluser.php`

**Función `guardarRolesUsuario()`**:

1. Cambiar `table` y `tableBpm` de objetos a arrays (`[]`).
2. En el bucle: `table.push(...)` y `tableBpm.push(...)` para cada fila.
3. Llamar a `changeLevelRolUserObject` en lugar de `changeLevelRolUser`.
4. Validación: si la tabla está vacía, mostrar alerta y no enviar.

---

## Pruebas a ejecutar

### Prueba 5.1: Asignar 1 rol

**Pasos**:

1. Ir a Cambio de Rol de un usuario.
2. Seleccionar Perfil.
3. Agregar 1 rol (Grupo + Rol) desde el modal.
4. Clic en Guardar.

**Resultado esperado**:

- Mensaje "Guardado. Los cambios y asignaciones fueron guardados correctamente."
- Verificar en PostgreSQL: `SELECT * FROM seg.memberships_users WHERE email = 'EMAIL';` — debe haber 1 fila.
- Rol visible en BPM.

### Prueba 5.2: Asignar 3 roles distintos

**Pasos**:

1. Ir a Cambio de Rol de un usuario (sin roles previos o limpiar la tabla).
2. Agregar 3 roles distintos (3 grupos/roles diferentes) desde el modal.
3. La tabla debe mostrar 3 filas.
4. Clic en Guardar.

**Resultado esperado**:

- Mensaje de éxito.
- En PostgreSQL: 3 filas en `seg.memberships_users` para ese email.
- Los 3 roles asignados en BPM.

### Prueba 5.3: Eliminar un rol

**Pasos**:

1. Con un usuario que tenga al menos 1 rol asignado.
2. Clic en el icono de eliminar (trash) de una fila.
3. Verificar que la fila desaparece y que se llama a `borrarMembership`.

**Resultado esperado**:

- La fila se elimina de la tabla.
- En PostgreSQL: el registro correspondiente ya no existe en `seg.memberships_users`.
- En BPM: el membership se eliminó.

### Prueba 5.4: Error en AJAX (HTTP 400)

**Pasos**:

1. Simular fallo (ej. usuario sin usernick en BPM o sesión BPM inválida).
2. Intentar guardar roles.

**Resultado esperado**:

- Se muestra el mensaje de error (alert o div `errorRol`).
- La petición AJAX recibe HTTP 400 (callback `error` se ejecuta).

---

## Checklist de cierre Fase Roles 5

- [ ] 5.1 – Asignar 1 rol: éxito en BD y BPM
- [ ] 5.2 – Asignar 3 roles: los 3 guardados
- [ ] 5.3 – Eliminar rol: se borra en BD y BPM
- [ ] 5.4 – Error: HTTP 400 y mensaje al usuario

---

## Referencias

- Plan: corrección roles y documentación
- Controlador: `application/controllers/Main.php`
- Vista: `application/views/changeleveluser.php`
