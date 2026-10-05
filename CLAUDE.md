# CLAUDE.md — traz-comp-dnato

## Restricción crítica de entorno

**Dnato corre en PHP 5.6 (XAMPP/LAMPP). Esta decisión es firme y no se cambia.**

### Sintaxis PHP PROHIBIDA en este repo

| Construcción | Disponible desde | Alternativa PHP 5.6 |
|---|---|---|
| `private string $x;` (typed properties) | PHP 7.4 | `private $x;` |
| `function foo(int $a, string $b)` (scalar hints) | PHP 7.0 | `function foo($a, $b)` |
| `function foo(): string` (return types) | PHP 7.0 | `function foo()` |
| `$a ?? $b` (null coalescing) | PHP 7.0 | `isset($a) ? $a : $b` |
| `?string` (nullable types) | PHP 7.1 | sin tipo |
| `: void` (void return) | PHP 7.1 | sin return type |
| `[$a, $b] = array` (short destructuring) | PHP 7.1 | `list($a, $b) = array` |
| `random_bytes()` | PHP 7.0 | `openssl_random_pseudo_bytes()` |
| arrow functions `fn() =>` | PHP 7.4 | closures tradicionales |

### Dependencias composer

El `composer.json` tiene `"platform": {"php": "5.6.40"}` fijado para que composer
nunca resuelva paquetes incompatibles, incluso si el sistema donde corre tiene PHP 8.

**NO instalar paquetes que exijan PHP >= 7.0.** Verificar siempre con:
```bash
composer update <paquete> --dry-run
```
antes de agregar dependencias nuevas.

### Paquetes actuales

| Paquete | Versión | Motivo del pin |
|---|---|---|
| `firebase/php-jwt` | `^5.5` (v5.5.1) | v6+ exige PHP >=8.0; v5.5.1 soporta RS256 con API idéntica |

`phpunit` fue eliminado de require-dev: la suite original (v9.x) exige PHP >=7.3
y no puede correr en PHP 5.6. Testing manual hasta que se adopte una suite compatible.

### Virtualhost DEV

`http://traz-comp.local/traz-comp-dnato/`

---

## 🔒 Metodología de Git — OBLIGATORIA

### Nunca commitear directo a ramas de integración
PROHIBIDO hacer commit o push directo a: develop-v3, develop, master, main.
Estas ramas están protegidas y todo cambio DEBE entrar por Pull Request.

### Flujo obligatorio para cualquier cambio
1. Sincronizar: `git checkout develop-v3 && git pull origin develop-v3`
2. Crear rama de trabajo con nombre descriptivo:
   `git checkout -b <tipo>/<issue-id>-<desc-corta>`
   (tipos: feat, fix, docs, chore, refactor)
   ejemplo: feature/E9-IDENT-12-refresh-tokens
3. Hacer TODOS los commits en esa rama (nunca en la de integración)
4. Formato de commit: `tipo(scope): descripción [ID-ISSUE]`
5. Push de la rama de trabajo: `git push origin <nombre-rama>`
6. Abrir PR: `gh pr create --base develop-v3 --head <nombre-rama>`
7. NO mergear el PR sin confirmación explícita del PM
8. NUNCA `git push` directo a develop-v3, develop, master ni main

### Antes de crear un PR
- Verificar que el build pasa (si aplica: `./mvnw clean install`)
- Verificar que no quedan marcadores de conflicto:
  `grep -rn "^<<<<<<<\|^=======\|^>>>>>>>" .`
- Verificar que no se commitean secretos (claves privadas, tokens, .env)

### Si un push a rama de integración es rechazado (protected branch)
NO intentar forzar ni desactivar la protección. Es el comportamiento correcto.
Mover los commits a una rama feature y abrir PR.

---

## 🧭 Metodología v2 — Contexto y estado antes de cualquier tarea

> Ver detalle completo en `traz-tools/doc/v3/TRAZALOG_v3_CICD_STRATEGY.md` sección 5-bis. Este bloque resume las obligaciones operativas para vos (Claude Code) en ESTE repo (traz-comp-dnato).

### Antes de empezar CUALQUIER tarea

1. Leé `doc/v3/CONTEXT-PACK.md` completo (de este repo).
2. **El estado vivo del proyecto (STATE.md) vive en el OTRO repo** (`traz-tools/doc/v3/STATE.md`) — es único para evitar desincronización entre los dos repos. Si tenés acceso a ese repo en tu sesión, leelo. Si no, preguntale a Rodolfo el estado actual antes de asumir nada.
3. **Chequeo de staleness:** si tenés `traz-tools` accesible, corré `ls ../traz-tools/doc/adr/` (o el path que corresponda) y comparalo contra el "último ADR" declarado en el encabezado del CONTEXT-PACK de este repo. Si no tenés acceso, asumí que el CONTEXT-PACK podría estar desactualizado y preguntá antes de decisiones sensibles.

### Jerarquía de fuentes — el CONTEXT-PACK es un resumen, no la verdad

El CONTEXT-PACK.md de este repo es un resumen operativo. La fuente canónica de arquitectura es `TRAZALOG_v3_MCP_ARCHITECTURE.md` + los ADR individuales (ambos viven en `traz-tools/doc/`, aunque el código de identidad esté acá). Ante cualquier ambigüedad:

1. Primero, si tenés acceso, leé la sección correspondiente del documento canónico en `traz-tools`.
2. Si no tenés acceso o el documento tampoco lo cubre, **PARÁ**. No improvises decisiones de arquitectura. Reportalo como "requiere decisión de arquitectura".

**Vos NO tomás decisiones de arquitectura ni de negocio.** Las toman Rodolfo y Claude Web en un workshop previo (clase 🔴). Tu trabajo es implementar decisiones ya tomadas.

### ⚠️ Restricción de entorno — la más importante de este repo

Este repo corre en **PHP 5.6 estricto**. Antes de escribir código, releé la sección de restricciones del CONTEXT-PACK.md (típos hints, `??`, `random_bytes()`, etc. están PROHIBIDOS). Antes de instalar cualquier dependencia nueva, verificá en Packagist que soporta PHP 5.6 — `composer.json` tiene `platform.php = "5.6.40"` fijado a propósito. Esta restricción ya rompió el ambiente dos veces en el Sprint 2; no la repitas.

### Al terminar CUALQUIER tarea (parte del Definition of Done)

1. Si tenés acceso a `traz-tools`, actualizá `traz-tools/doc/v3/STATE.md` con el resultado de tu tarea. Si no tenés acceso, reportale a Rodolfo el resultado en detalle para que él o Claude Web lo actualicen.
2. **Si tu tarea implica un cambio de mecanismo que afecta la arquitectura** (ej. cómo se emite o valida el JWT): eso requiere un ADR nuevo en `traz-tools/doc/adr/` — reportalo como pendiente de ese ADR, no lo des por cerrado sin esa formalización.
3. Si tu tarea modificó algo que el CONTEXT-PACK.md de este repo describe, actualizalo en el mismo PR.
4. Abrí el PR con este formato obligatorio de descripción:

```markdown
## Qué cambia
[1-2 líneas, en términos funcionales]

## Por qué
[referencia a la tarea/issue/decisión que lo origina]

## Cómo lo verifiqué
[php -l ejecutado / tests / curls, con resultado]

Closes #NNN
```

### Reglas de escalamiento — cuándo parar y preguntar

| Tipo de duda | Qué hacés |
|---|---|
| Técnica menor (dos formas válidas de implementar lo mismo, ambas PHP 5.6 compatibles) | Decidís vos, documentás la elección en la descripción del PR |
| Funcional o de negocio (variantes con impacto distinto para el usuario, ej. cómo se resuelve una membresía multi-empresa) | PARÁS y preguntás a Rodolfo con las opciones + tu recomendación. No avanzás sin respuesta |
| Arquitectura (contradice o no está cubierto por el CONTEXT-PACK ni por el documento canónico de `traz-tools`) | PARÁS, marcás la tarea como "requiere decisión de arquitectura" |

**Regla de oro: ante la duda de si algo es menor o funcional, preguntá.**

### Clasificación de riesgo de tu tarea

- Docs/scripts/tests/configs sin efecto en runtime ni datos → 🟢
- Código de producción (endpoints OAuth, emisión de JWT, modelos de usuario) → 🟡, ciclo estándar de 4 pasos
- Cambios al mecanismo de identidad, seguridad, migraciones de BD → 🔴, requiere workshop previo con Rodolfo — no implementes sin esa decisión documentada en un ADR
