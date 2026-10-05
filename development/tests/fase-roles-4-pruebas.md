# Fase Roles 4 – Externalizar sesiones BPM

## Fecha
2026-03-14

## Objetivo
Mover los tokens/sesiones BPM hardcodeados en `Roles.php` a `application/config/constants.php`, de modo que se puedan actualizar sin modificar el código cuando expire la sesión.

---

## Contexto en el plan

| Fase | Descripción | Estado |
|------|-------------|--------|
| Roles 1–3 | Validaciones y manejo de errores | Cerradas |
| **Roles 4** | Externalizar sesiones BPM | En pruebas |
| Roles 5 | Corregir changeLevelRolUserObject y guardarRolesUsuario | Pendiente |

---

## Cambios implementados (Fase Roles 4)

### Archivo: `application/config/constants.php`

Añadidas constantes:

- `$bpm_roles_session_base`: string base de la sesión (X-Bonita-API-Token=...;JSESSIONID=...;bonita.tenant=1;)
- `BPM_ROLES_SESSION`: formato para payload (guardarMembershipBPM, deleteMembershipBPM)
- `BPM_ROLES_SESSION_URL`: formato URL-encoded para getInfoBPM

### Archivo: `application/models/Roles.php`

- Reemplazados los literales por `BPM_ROLES_SESSION` y `BPM_ROLES_SESSION_URL`.
- Fallback al valor anterior si las constantes no están definidas (compatibilidad).

### Cómo actualizar la sesión

1. Hacer login en Bonita (o usar la API de login).
2. Extraer de la respuesta/cookies: `X-Bonita-API-Token` y `JSESSIONID`.
3. Editar `constants.php` y actualizar `$bpm_roles_session_base` con el formato: `X-Bonita-API-Token=VALOR;JSESSIONID=VALOR;bonita.tenant=1;`

---

## Pruebas a ejecutar

### Prueba 4.1: Sesión válida configurada

**Pasos**:

1. Verificar que `$bpm_roles_session_base` en constants.php tiene una sesión válida (obtenida de Bonita).
2. Asignar un rol a un usuario desde la pantalla Cambio de Rol.
3. Guardar.

**Resultado esperado**:

- Asignación correcta.
- Rol visible en la tabla y en BPM.

### Prueba 4.2: Sesión inválida o vacía

**Pasos**:

1. Temporalmente poner una sesión inválida en `$bpm_roles_session_base` (ej. token expirado).
2. Intentar asignar un rol.

**Resultado esperado**:

- No debe haber errores PHP por hardcodeo.
- Debe mostrarse mensaje de error (ej. fallo en BPM).
- En logs: ERROR con detalle del fallo de la API.

---

## Checklist de cierre Fase Roles 4

- [ ] 4.1 – Sesión válida: asignación funciona
- [ ] 4.2 – Sesión inválida: error manejado sin crash

---

## Referencias

- Plan: corrección roles y documentación
- Config: `application/config/constants.php`
- Modelo: `application/models/Roles.php`
