# CONTEXT-PACK — traz-comp-dnato

> `Versión: 1.0 | Sincronizado con: TRAZALOG_v3_MCP_ARCHITECTURE.md / último ADR: ADR-009 | Última actualización: 2026-07-14`

> **Este archivo es un RESUMEN OPERATIVO, NO la fuente de verdad.** Leelo primero, pero ante cualquier ambigüedad, contradicción, o tema que no cubra, andá a la fuente canónica ANTES de decidir. Si ni la fuente canónica lo cubre, PARÁ — es una decisión de arquitectura o de negocio, no la tomes solo (ver reglas de escalamiento en CLAUDE.md).
>
> **Nota:** el estado vivo del proyecto (`STATE.md`) vive en el repo `traz-tools`, no acá — es la fuente única para evitar desincronización entre los dos repos. Si necesitás saber en qué sprint/tarea estás, andá a `traz-tools/doc/v3/STATE.md`.

## Jerarquía de fuentes (en este orden)

1. **Negocio y objetivos** → `doc/v3/investigacion-sector-minero-trazalog-v3-2.md` + la estrategia de pricing (`TRAZALOG_v3_PRICING_STRATEGY` — verificar path real en `doc/v3/` antes de citarla en un PR) (viven en `traz-tools`, referenciados acá)
2. **Arquitectura** → `TRAZALOG_v3_MCP_ARCHITECTURE.md` + `doc/adr/ADR-*.md` (viven en `traz-tools`)
3. **Proceso** → `TRAZALOG_v3_CICD_STRATEGY.md` sección 5-bis (vive en `traz-tools`)
4. **Entorno y git de ESTE repo** → `CLAUDE.md` (acá mismo)

**Chequeo de staleness (hacer antes de cualquier tarea):** si tenés `traz-tools` clonado también, corré `ls ../traz-tools/doc/adr/` y compará contra el "último ADR" del encabezado. Si no tenés acceso a `traz-tools` en esta sesión, asumí que este resumen podría estar desactualizado y preguntá a Rodolfo si hay ADRs posteriores a ADR-009.

---

## 1. Qué es este proyecto y su objetivo de negocio

Dnato es el módulo de **identidad y autenticación** de Trazalog — resuelve login, sesión, multi-empresa (multi-tenancy) y, desde v3, emite los tokens JWT que consumen las tools MCP. Trazalog vende gestión de operaciones a proveedores de servicios mineros de Cuyo (ventana pre-2027). Dnato es la puerta de entrada: sin un login y una emisión de JWT confiables, ninguna tool MCP puede garantizar aislamiento entre empresas — que es el argumento de venta central del producto ("tus datos están criptográficamente aislados de otros clientes").

## 2. Decisiones de arquitectura vigentes (las que afectan a Dnato)

| ADR | Decisión (1 línea) |
|---|---|
| ADR-008 | El APIM (en `traz-tools`) valida el JWT de Dnato como Key Manager federado — Dnato expone JWKS, el APIM ya no hace passthrough al MI |
| **ADR-009** | **VIGENTE.** El `empr_id` se propaga vía backend JWT (`X-JWT-Assertion`), generado por el APIM a partir del JWT de Dnato — Dnato sigue siendo el emisor original, este ADR afecta la capa WSO2, no el código de Dnato en sí |

## 3. Mecanismo de identidad vigente — el rol de Dnato

```
Usuario → login en Dnato (OauthLogin.php, 2 pasos: credenciales → selección de empresa si N>1)
        → Dnato emite JWT (JwtIssuer.php) con claim empr_id, firmado RS256
        → Dnato expone JWKS en /oauth/.well-known/jwks.json (clave pública)
        → Dnato expone discovery RFC 8414 en /.well-known/oauth-authorization-server
        → El JWT viaja a Claude.ai, que lo usa contra el APIM (en traz-tools)
```

**Regla de oro:** Dnato es la ÚNICA fuente de verdad de qué empresa (`empr_id`) corresponde a qué usuario. Nunca aceptar `empr_id` como parámetro de entrada en ningún endpoint — siempre se resuelve server-side desde la sesión/membership del usuario autenticado.

## 4. Restricciones duras — ⚠️ LA MÁS IMPORTANTE DE ESTE REPO

- **PHP 5.6 ESTRICTO.** Este repo corre en PHP 5.6 (XAMPP/LAMPP). NO está permitido usar:
  - Typed properties (`private string $x`)
  - Scalar type hints (`string $x`, `int $x`) ni return types (`: string`, `: bool`, `: void`)
  - Null coalescing (`??`) → usar `isset($x) ? $x : $default`
  - Short array destructuring (`[$a, $b] = ...`) → usar `list($a, $b) = ...`
  - `random_bytes()` → usar `openssl_random_pseudo_bytes()`
  - Arrow functions, cualquier sintaxis de PHP 7+/8
- **`composer.json` tiene `platform.php = "5.6.40"` fijado.** No agregar dependencias que exijan PHP ≥ 7 sin verificar antes contra ese pin. Antes de instalar cualquier paquete nuevo, confirmar en Packagist que la versión soporta PHP 5.6.
- **Antes de cualquier commit, correr `php -l` sobre los archivos tocados** si hay dudas de compatibilidad.
- Esta restricción ya rompió el ambiente DOS VECES en el Sprint 2 (firebase/php-jwt v7 + sintaxis moderna en JwtIssuer/Oauth/OauthLogin). No repetir.

## 5. Regla de oro de repos

| Si el trabajo es… | Va en el repo… |
|---|---|
| Login, tokens, JWT, OAuth, usuarios, roles, sesión, membresías | **Este repo (traz-comp-dnato)** |
| WSO2 (APIM, MI), APIs, DataServices, sequences, Virtual MCP Servers | **traz-tools** |

## 6. Si tu tarea toca X → leé también Y

| Tu tarea toca… | Leé también… |
|---|---|
| Emisión de JWT / claims | `doc/identity/token-issuance.md`, `JwtIssuer.php` |
| Flujo de login OAuth / PKCE | `doc/identity/oauth-login-flow.md`, `OauthLogin.php` |
| Login web (usuario en el navegador) | `Main::login()` + `Main::seleccionar_empresa()`. Pide sólo email y contraseña; la empresa se resuelve server-side desde `seg.memberships_users` × `core.empresas` y, si hay más de una, se elige en `views/login_empresa.php`. **No comparte código con el login OAuth**, que sigue rechazando usuarios multi-empresa (TAD-IDENT-02). Las dos vistas usan layout a sangre: no cargan `container.php` ni `footer.php`. El banner freemium se enciende y apaga con `LOGIN_MOSTRAR_REGISTRO` en `constants.php` |
| Discovery OAuth (RFC 8414) | `application/controllers/Oauth.php` (método `jwks()` y endpoint `.well-known`) |
| Cómo el APIM consume estos tokens | `traz-tools/doc/adr/ADR-008-*.md`, `ADR-009-*.md` (repo distinto) |
| Modelo de negocio / tiers | `TRAZALOG_v3_PRICING_STRATEGY` en `traz-tools/doc/v3/` (verificar path; repo distinto) |

## 7. Dónde está cada cosa (mapa de directorios clave)

```
application/controllers/Oauth.php       → endpoints /oauth/authorize, /oauth/token, /oauth/.well-known/jwks.json
application/controllers/OauthLogin.php  → pantalla de login (2 pasos: credenciales, selección de empresa)
application/libraries/JwtIssuer.php     → emisión de JWT RS256 con claim empr_id
application/models/OauthCode_model.php  → gestión de authorization codes (PKCE)
application/models/User_model.php       → resolución de empr_id por usuario/grupo
doc/identity/migrations/                → migraciones SQL (seg.oauth_codes, etc.)
doc/identity/                           → docs de OAuth, JWT, discovery
```

---

## Mantenimiento de este archivo

**Todo PR que modifique el mecanismo de emisión de JWT o el flujo OAuth debe actualizar este CONTEXT-PACK en el mismo PR** — y si la decisión de fondo es un cambio de arquitectura, corresponde primero un ADR en `traz-tools/doc/adr/` (aunque el código viva acá).
