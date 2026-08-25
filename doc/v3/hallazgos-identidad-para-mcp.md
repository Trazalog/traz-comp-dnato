# Hallazgos de identidad en Dnato — para el equipo/agente que trabaja el MCP

## Objetivo

Es la lista de defectos y deriva de documentación que aparecieron al leer el código de identidad de Dnato (OAuth 2.1 + PKCE, emisión de JWT, JWKS, discovery). Está escrito para **quien construyó y opera la cadena MCP del lado `traz-tools`** (APIM, MI, fachada `toolsMCPAPI`): son cosas que hoy no se ven porque el flujo todavía no está corriendo en producción, pero que van a morder cuando lo esté. Se lee **una vez que la cadena MCP funcione end-to-end**, antes de dar de alta al primer cliente.

**No cubre** cómo funciona el flujo OAuth (eso está en `doc/identity/oauth-login-flow.md` y `doc/identity/token-issuance.md`, con las salvedades de la sección 6 de este documento), ni el proceso de registración (ver `doc/PROCESO_REGISTRACION.md`), ni nada del lado WSO2.

---

## Metadata

| | |
|---|---|
| **Relevado por** | Claude Code, sesión "DNATO Mejoras" |
| **Fecha** | 2026-08-21 |
| **Repo / rama** | `traz-comp-dnato`, `develop-v3` |
| **Método** | Lectura de código. **No se ejecutó nada, no se corrigió nada.** |
| **Alcance** | `Oauth.php`, `Oauthlogin.php`, `JwtIssuer.php`, `OauthCode_model.php`, `Cli.php`, `config/jwt.php`, `config/oauth_clients.php`, `config/routes.php` |
| **Fuentes cruzadas** | `traz-tools`: `doc/v3/STATE.md` (2026-08-20), `doc/adr/ADR-009`, `doc/identity/dnato-jwt-prereqs.md`, `doc/identity/oauth-discovery-flow.md`, relevamiento DocTest F1 |

> ⚠️ **Ninguno de estos hallazgos fue corregido.** Este documento sólo los registra. Cada uno indica si la corrección es una decisión técnica menor, funcional, o de arquitectura (requiere ADR).

---

## 1. Cómo usar este documento

1. Leé la tabla resumen (§2) para saber qué te toca.
2. Los hallazgos **A** y **B** bloquean el uso real del login OAuth; conviene resolverlos antes de que el primer cliente pruebe el connector.
3. Los hallazgos **C** a **E** son deuda que se manifiesta con uso sostenido (concurrencia, tokens que vencen, tokens de prueba que no representan la realidad).
4. La sección 6 (deriva de documentación) importa si vas a escribir cualquier cosa apoyándote en los docs de identidad: hoy hay tres afirmaciones falsas en ellos.
5. La sección 7 lista lo que **no** se revisó, para que nadie lea este documento como "auditoría completa de Dnato".

---

## 2. Resumen

| # | Hallazgo | Clase | Evidencia | Corrección |
|---|---|---|---|---|
| **A** | `Oauth::authorize()` no valida `redirect_uri` contra la whitelist | 🔴 seguridad | `application/controllers/Oauth.php:44-93` | Técnica; **pendiente de tu confirmación de si necesita ADR** |
| **B** | Sintaxis PHP 7+ vigente en el camino OAuth, sobre un runtime PHP 5.6 | 🔴 rompe el flujo | `Oauthlogin.php:262,295,360` · `Cli.php:63,70,80,95` | Técnica menor; hay una rama con la mitad hecha |
| **C** | `OauthCode_model::consume()` no es atómico (TOCTOU) | 🟡 | `application/models/OauthCode_model.php:50-74` | Técnica menor |
| **D** | No se emite `refresh_token`; el TTL se subió a 24 h como paliativo | 🟡 funcional | `application/config/jwt.php:39-42` | Funcional — decide el PM |
| **E** | `Cli::issue_test_token` emite tokens sin `empr_id_mysql` | 🟡 | `application/controllers/Cli.php:104` | Técnica menor |

---

## 3. Hallazgo A — `redirect_uri` sin validar contra la whitelist

**Clase: 🔴 seguridad.**

### Qué pasa

`Oauth::authorize()` valida el `client_id` contra `ALLOWED_CLIENT_ID`, el `response_type`, el `code_challenge_method` y que `code_challenge` y `redirect_uri` no vengan vacíos. **No compara nunca el `redirect_uri` contra la lista blanca de `application/config/oauth_clients.php`.** Después, `_issueCode()` (`Oauth.php:340-372`) redirige el authorization code a esa URI, sea cual sea.

La validación **sí existe en el otro camino**: `Oauthlogin::_resolvePendingParams()` compara contra `$clients[$clientId]['redirect_uris']` con `in_array(..., true)` (`Oauthlogin.php:207-211`). O sea: no falta la regla, falta aplicarla en `Oauth.php`.

### Por qué importa para la cadena MCP

`POST /oauth/register` (RFC 7591, `Oauth.php:306-333`) devuelve el `client_id` fijo a cualquiera que lo pida, sin persistir nada y sin validar los `redirect_uris` que recibe — es una decisión consciente y documentada para la fase 1 (`traz-tools/doc/identity/oauth-discovery-flow.md` §6.3). Combinada con la falta de whitelist, un tercero puede completar el flujo entero y recibir el code en una URI propia.

**PKCE no mitiga este caso.** PKCE protege contra la intercepción de un code ajeno; acá el atacante genera su propio `code_verifier`/`code_challenge`, así que el intercambio en `/oauth/token` le funciona.

El requisito es explícito en OAuth 2.1: el AS debe comparar el `redirect_uri` contra los valores registrados, con comparación exacta de string.

### Qué haría falta

Aplicar en `Oauth::authorize()` la misma verificación que ya hace `Oauthlogin::_resolvePendingParams()`, y devolver el error **sin redirigir** (un `redirect_uri` no válido nunca debe usarse para transportar el error).

### Decisión pendiente

Para mí es una decisión **técnica menor** —es cerrar en un controller un chequeo que ya existe en el otro, sin cambiar el mecanismo de identidad—, pero como toca la superficie de seguridad del AS, **queda a confirmación del PM** si prefiere que pase por un ADR antes de tocarse. No se implementó nada.

---

## 4. Hallazgo B — sintaxis PHP 7+ en el camino OAuth, con runtime PHP 5.6

**Clase: 🔴 — rompe el flujo por completo, no lo degrada.**

### Qué pasa

`CLAUDE.md` y el CONTEXT-PACK de este repo son explícitos: PHP 5.6 estricto, sin `??`, sin `random_bytes()`. Hoy, en `develop-v3`, sobreviven estos usos:

| Archivo:línea | Construcción | Efecto en PHP 5.6 |
|---|---|---|
| `Oauthlogin.php:262` | `bin2hex(random_bytes(32))` | Fatal error: función indefinida |
| `Oauthlogin.php:295` | `$result['code'] ?? 'N/A'` | **Parse error** |
| `Oauthlogin.php:360` | `$tabla[0]['valor'] ?? ''` | **Parse error** |
| `Cli.php:63, 70, 80, 95` | `??` (4 usos) | **Parse error** |

`??` es un error de **parseo**, no de ejecución: en PHP 5.6 el archivo entero no compila. `Oauthlogin.php` es el controller del login OAuth, así que **todo el login OAuth queda muerto**, no sólo las ramas que tocan esas líneas.

`random_bytes()` merece una nota aparte: el repo ya tiene la solución escrita. `application/libraries/Password.php:50` implementa `random_bytes_compat()` con cascada `random_bytes` → `openssl_random_pseudo_bytes` → `mcrypt_create_iv` → `/dev/urandom`. Y el propio `Oauth.php:349` usa `openssl_random_pseudo_bytes(32)` para lo mismo. La línea 262 de `Oauthlogin.php` es la excepción, no la regla.

### Estado: hay media corrección sin landear

La rama **`fix/e9-ident-cli-php56-compat-empr-id-lookup`** (commit `58177e2`, también en `origin`) corrige `Cli.php` y agrega la resolución de `empr_id` vía `core.empresas`. **No está mergeada a `develop-v3` y no toca `Oauthlogin.php`.** Recuperarla cubre 4 de los 7 usos.

Esto ya se había detectado desde el otro lado: `traz-tools/doc/v3/STATE.md`, decisión del 2026-08-09, lista entre los bugs encontrados en `demo.cloudtrazalog.com` la "sintaxis `??` (PHP7+) en `Cli.php` corriendo sobre PHP 5.6", y los marca como "candidatos a PR en `traz-comp-dnato`". El PR nunca se abrió.

### Trampa de verificación

**`php -l` no sirve como red de seguridad acá.** El intérprete de la máquina de desarrollo es PHP 8.3, así que valida sin quejarse todo lo que rompe en el server. Hasta que exista un linter fijado a 5.6, la verificación tiene que ser por lectura o corriendo `php -l` **en el server de destino**.

---

## 5. Hallazgos C, D y E

### C — `OauthCode_model::consume()` no es atómico

`consume()` (`OauthCode_model.php:50-74`) hace `SELECT ... WHERE used_at IS NULL`, evalúa la expiración en PHP, y recién después ejecuta el `UPDATE used_at`. Entre el `SELECT` y el `UPDATE` hay una ventana en la que dos `POST /oauth/token` concurrentes con el mismo code pasan los dos.

El docblock del método dice *"lo marca como usado en un solo paso para prevenir race conditions"* — describe una garantía que el código no da.

El uso de un solo `UPDATE ... WHERE id_code = ? AND used_at IS NULL` y decidir por filas afectadas resuelve el caso. Mitigantes actuales: TTL de 60 s y el binding de `redirect_uri`. **Clase 🟡, técnica menor.**

### D — no hay `refresh_token`

`application/config/jwt.php:39-42` documenta la situación con honestidad: el TTL está en **86400 s (24 h)** y el comentario explica por qué —`/oauth/token` no emite `refresh_token`, así que con un TTL corto el cliente (Claude) queda trabado al vencer: no renueva ni re-dispara el login.

Dos consecuencias para la cadena MCP: (1) el usuario tiene que rehacer el alta del connector cada 24 h; (2) un token de 24 h amplía la ventana de un token filtrado. Además, `doc/identity/token-issuance.md` sigue diciendo 3600 s.

Implementar `refresh_token` cambia el contrato del AS: **es funcional/arquitectura, lo decide el PM**, y probablemente amerite ADR porque toca cómo se sostiene la sesión de un agente.

### E — `Cli::issue_test_token` emite tokens incompletos

`Cli.php:104` llama `$this->jwtissuer->issue($userArray, $empr_id, $groupBpm)` sin el cuarto argumento, `$empr_id_mysql`. `JwtIssuer::issue()` lo default-ea a `null` y emite el claim como cadena vacía.

Los dos caminos reales sí lo resuelven (`Oauth::_issueCode()` y `Oauthlogin::_resolveCompany()`, ambos vía `Empresas::getEmpresaById()`). O sea: **el token de prueba del CLI no representa lo que emite el flujo real**, justo en el claim que las tools `man_*` usan como id de empresa en `assetv2`. Una verificación hecha con el CLI puede dar verde sobre un token que en producción llevaría otro contenido. **Clase 🟡, técnica menor.**

> Relacionado, y bloqueante para PROD según `traz-tools/doc/v3/STATE.md`: la columna `core.empresas.empr_id_mysql` **no tiene migración SQL formal en ningún repo**. Si el CLI se usa para probar en un ambiente donde la columna no existe, el síntoma va a ser confuso.

---

## 6. Deriva de documentación — tres afirmaciones hoy falsas

Si vas a escribir algo apoyándote en los docs de identidad, tené en cuenta que estas tres cosas ya no son ciertas:

| Documento | Dice | El código hace |
|---|---|---|
| `doc/identity/oauth-login-flow.md` | Login OAuth de **2 pasos** con dropdown de selección de empresa (decisión P02), con endpoints `GET/POST /oauth/login/select-company` | **Un solo paso.** `Oauthlogin` implementa TAD-IDENT-02 estricto: 0 empresas → error, 1 → sigue, **>1 → error "múltiples empresas no soportado"** (`Oauthlogin.php:133-144`). Las rutas `select-company` no existen en `routes.php` |
| `doc/identity/token-issuance.md` | TTL 3600 s; tabla de claims sin `azp` ni `empr_id_mysql` | TTL **86400 s**; `JwtIssuer.php:48-61` emite además `azp` y `empr_id_mysql` |
| `traz-tools/doc/identity/dnato-jwt-prereqs.md` | `iss = "trazalog-dnato"` (string opaco); claim `azp` "no presente", diferido a monetización | `jwt.php:37` toma `iss` de `DNATO_ISSUER` (una **URL**, default `http://localhost/oauth`); `jwt.php:51` define `jwt_azp` y `JwtIssuer` lo emite |

Hay una cuarta, ya anotada en el propio `doc/v3/CONTEXT-PACK.md`: la sección 2 declara ADR-009 como último ADR, y en `traz-tools/doc/adr/` existen **ADR-011, ADR-012 y ADR-013**.

**Nota funcional que sale de la primera fila:** el login OAuth y el login web se comportan distinto ante un usuario multi-empresa. El OAuth lo rechaza; el login web (`Main::login()`) le ofrece un combo para elegir. La duda ya está levantada en el relevamiento DocTest (caso DNATO-UC-009) y **espera decisión del PM**.

---

## 7. Qué NO se revisó

Para que este documento no se lea como una auditoría completa:

- **No se revisó** el ABM de usuarios, empresas, menús ni carga masiva. Los 8 hallazgos de esas pantallas están en `traz-tools/doctest/catalogo/dnato/RESUMEN-RELEVAMIENTO-DNATO.md` §4 (entre ellos: `edituser()`/`deleteuser()` no verifican que el usuario objetivo pertenezca a la empresa del administrador, y el combo de empresas del login lista todas las empresas del sistema sin sesión).
- **No se revisó** nada del lado WSO2: ni la validación del `X-JWT-Assertion`, ni la configuración del Key Manager, ni las sequences del MI.
- **No se ejecutó** ningún flujo. Todo sale de leer código; nada está verificado contra un ambiente corriendo.
- **No se revisó** el proceso de registración desde la óptica de seguridad — está documentado funcionalmente en `doc/PROCESO_REGISTRACION.md`, que incluye su propia sección de riesgos.

---

## 8. Referencias

- `doc/v3/CONTEXT-PACK.md` — resumen operativo de este repo (jerarquía de fuentes, restricción PHP 5.6)
- `doc/identity/token-issuance.md`, `doc/identity/oauth-login-flow.md`, `doc/identity/jwt-keys-setup.md` — con las salvedades de §6
- `doc/PROCESO_REGISTRACION.md` — proceso de registración y sus dependencias en `traz-tools`
- `traz-tools/doc/v3/STATE.md` — estado vivo del proyecto (fuente única)
- `traz-tools/doc/adr/ADR-009-backend-jwt-assertion.md` — cómo el APIM propaga `empr_id` al MI
- `traz-tools/doc/identity/dnato-jwt-prereqs.md`, `oauth-discovery-flow.md`
- `traz-tools/doctest/catalogo/dnato/RESUMEN-RELEVAMIENTO-DNATO.md` — 21 casos funcionales + 8 hallazgos de ABM
