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
