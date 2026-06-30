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
