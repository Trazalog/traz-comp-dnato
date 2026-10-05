# Fase Roles 1 – Validar respuesta HTTP en guardarMembershipBPM

## Fecha
2026-03-14

## Objetivo
Validar la respuesta HTTP de la API BPM en `Roles->guardarMembershipBPM()` antes de decodificar. Si la API falla (status false o HTTP >= 300), loguear el error y retornar `null` en lugar de provocar errores PHP al acceder a propiedades de objetos de error.

---

## Contexto en el plan

| Fase | Descripción | Estado |
|------|-------------|--------|
| **Roles 1** | Validar respuesta HTTP en guardarMembershipBPM | En pruebas |
| Roles 2 | Validar errores en Main->guardarMembership | Pendiente |
| Roles 3 | Validar usuario en getInfoBPM | Pendiente |
| Roles 4 | Externalizar sesiones BPM | Pendiente |
| Roles 5 | Corregir changeLevelRolUserObject y guardarRolesUsuario | Pendiente |

---

## Cambios implementados (Fase Roles 1)

### Archivo: `application/models/Roles.php`

**Método `guardarMembershipBPM()`**:

1. Validación de `$info_bpm`: si es `null` o no tiene `id`, retorna `null` y loguea error.
2. Guardar resultado de `callAPI()` en variable `$result` antes de decodificar.
3. Validar `$result['status']` y `$result['code']`: si falla (status false o code >= 300), loguear error y retornar `null`.
4. Solo decodificar JSON cuando la respuesta sea exitosa.

---

## Pruebas a ejecutar

### Requisitos previos

- Usuario admin logueado en la aplicación.
- Al menos un usuario de prueba en `seg.users` con `usernick` existente en Bonita BPM.
- WSO2 MI con toolsbpmAPI desplegado y Bonita accesible.
- Grupos y roles válidos en BPM (visibles en la pantalla Cambio de Rol).
- Empresa de Prueba: usar un grupo/empresa que exista en BPM.

### Prueba 1.1: Asignar rol a usuario existente en BPM (éxito)

**Pasos**:

1. Ir a **Usuarios** → seleccionar un usuario que exista en BPM.
2. Clic en **Cambiar Niveles** (o equivalente para abrir la pantalla de cambio de rol).
3. Seleccionar **Perfil** en el dropdown.
4. Clic en **Agregar Rol**.
5. Elegir un **Grupo** (Empresa de Prueba) y un **Rol**.
6. Clic en **Guardar**.

**Resultado esperado**:

- Mensaje "Guardado. Los cambios y asignaciones fueron guardados correctamente."
- No debe aparecer error PHP en pantalla ni en logs.

### Prueba 1.2: Verificar en PostgreSQL

**Consulta** (reemplazar `EMAIL_USUARIO` por el email del usuario de la prueba 1.1):

```sql
SELECT * FROM seg.memberships_users WHERE email = 'EMAIL_USUARIO';
```

**Resultado esperado**:

- Al menos una fila con `email`, `group`, `role` correspondientes al rol asignado.

### Prueba 1.3: Simular fallo (usuario inexistente en BPM)

**Opción A – Usuario sin usernick en BPM**:

- Si existe un usuario en `seg.users` que **no** esté creado en Bonita BPM, intentar asignarle un rol.
- O crear temporalmente un usuario en `seg.users` sin crearlo en BPM.

**Opción B – Sesión BPM inválida**:

- Temporalmente poner una sesión inválida en `Roles.php` (o en config cuando se externalice) para que la API retorne error.

**Resultado esperado**:

- No debe aparecer error PHP fatal ("Trying to get property of non-object", etc.).
- Debe mostrarse mensaje de error al usuario o el flujo debe manejar el fallo sin crashear.
- En `application/logs/` debe aparecer un log de ERROR con el detalle del fallo de la API.

---

## Checklist de cierre Fase Roles 1

- [ ] 1.1 – Asignar rol a usuario existente: éxito sin errores PHP
- [ ] 1.2 – Verificación en PostgreSQL (seg.memberships_users)
- [ ] 1.3 – Fallo manejado correctamente (sin PHP error, log de ERROR presente)

---

## Referencias

- Plan: `.cursor/plans/` (corrección roles y documentación)
- Modelo Roles: `application/models/Roles.php`
- Vista cambio de rol: `application/views/changeleveluser.php`
