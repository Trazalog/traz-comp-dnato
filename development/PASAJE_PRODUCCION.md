# Pasaje a Producción — Proyecto Registración + Empresa-Asset

Checklist consolidado del trabajo realizado el 2026-04-25.
Ejecutar en este orden.

---

## 0) Pre-requisitos del servidor

### PostgreSQL: extensión `pgcrypto`
Necesaria para PBKDF2 en el SP de creación de usuarios.

```bash
# Como postgres
psql -d <DB_TOOLS> -c "CREATE EXTENSION IF NOT EXISTS pgcrypto;"
```

Si la extensión no está disponible (típico en PG11 RHEL7 EOL):
- Instalar `postgresql11-contrib` desde repo PGDG (o RPM manual `--nodeps --nosignature` matching el patch del server).
- Reiniciar la sesión de psql tras instalar.

### PHP
La aplicación corre en PHP 5.x → **NO usar `??`** (null coalescing). Ya se reemplazaron las ocurrencias en `application/controllers/Main.php` por `isset(...) ? ... : ...`.

---

## 1) Cambios de Base de Datos

### 1.1 PostgreSQL — DDL

Orden de ejecución:

1. **`scripts/modificar_tabla_usuarios.sql`**
   Agrega `seg.users.reg_info_id` + comment + índice.

2. **`development/sp_insert_usuario_con_hash_con_imagen.sql`**
   - Crea/actualiza función `xor_bytea(BYTEA, BYTEA)` (helper para PBKDF2).
   - Reemplaza `seg.insert_usuario_con_hash(...)` con versión PBKDF2-SHA256 compatible con `application/libraries/Password.php` (formato `sha256:1000:saltB64:hashB64`).
   - GRANT EXECUTE a PUBLIC.
   - **Requiere `pgcrypto`** instalado (paso 0).

### 1.2 PostgreSQL — Datos iniciales (formulario de registro dinámico)

> El formulario se creó en TEST con `form_id=72`. La constante PHP `FORMULARIO_REGISTRO_ID = 72` debe coincidir con el `form_id` real en PROD.

**Si en PROD se carga desde cero:**

1. **`scripts/crear_formulario_registro.sql`**
   - Inserta 'Formulario Registro Usuario' en `frm.formularios` (script trae `empr_id=1` hardcoded; **en runtime la app usa `REGISTER_TEMP_EMPR_ID = 9000`** vía session, así que el `empr_id` del registro en `frm.formularios` no es bloqueante para el flujo público).
   - Inserta filas en `core.tablas` (ej: `1-como_enteraste`).
   - Inserta items en `frm.items`.
   - **Capturar el `form_id` realmente generado** y reflejarlo en `application/config/constants.php` (`FORMULARIO_REGISTRO_ID`).

2. **NO ejecutar** `scripts/formulario_registro_usuario.sql` (duplicado y vuelve a hacer ALTER de `reg_info_id`).

**Si en PROD el form ya está creado pero con id distinto a 72:**

Opción A (preferida): actualizar la constante PHP en `constants.php` al `form_id` real de PROD.

Opción B: re-mapear filas con los scripts de patch:
- **`scripts/corregir_formulario_registro.sql`** (re-inserta items con `form_id=72`).
- **`scripts/corregir_valo_id.sql`** (alinea `valo_id` con entradas de `core.tablas`).

### 1.3 MariaDB (AssetPlanner)

**`development/sql/asset-empresa-trigger.sql`**

- Crea SP `sp_create_empresa_groups_and_actions(p_empr_id)`:
  inserta 5 grupos default en `sisgroups` + acciones en `sisgroupsactions`.
- Crea trigger `trg_empresas_after_insert` sobre `empresas`:
  llama al SP automáticamente al alta de empresa.

Validar:
```sql
SHOW TRIGGERS LIKE 'empresas';
SHOW PROCEDURE STATUS WHERE Name='sp_create_empresa_groups_and_actions';
```

---

## 2) Aplicación PHP

### 2.1 `application/config/constants.php`

Verificar / setear en PROD:

```php
define('FORMULARIO_REGISTRO_ID', 72);     // Ajustar al form_id real en PROD
define('REGISTER_TEMP_EMPR_ID', 9000);    // empresa "buffer" usada antes de crear la real

// WSO2 / BPM (igual que TEST, ajustar host)
define('WSO2_API_URL', 'https://<host-wso2-prod>:8243');
define('WSO2_DATASERVICES_URL', 'https://<host-wso2-prod>:8253/services');
// y demás ya presentes
```

### 2.2 Compatibilidad PHP 5.x

Ya aplicado en `application/controllers/Main.php` (todos los `??` → `isset(...) ? ... : ...`). Si se hace merge desde otra rama, **revisar nuevamente** que no se reintroduzcan operadores `??`.

```bash
grep -n '??' application/controllers/Main.php
```

---

## 3) WSO2 — ToolsAPIProject_1.0.0

Re-empaquetar y desplegar el CApp con los siguientes 4 artefactos modificados:

| Artefacto | Cambio |
|---|---|
| `bpmAPICallTemplate_1.0.0/bpmAPICallTemplate-1.0.0.xml` | Manejo idempotente de "already exists" 4xx en BPM |
| `toolsbpmAPI_1.0.0/toolsbpmAPI-1.0.0.xml` | Paginación `/roles`, `/role/porNombre`, `/actor/membership`, `/actor/rol`, `/actor/grupo` |
| `COREDataService_1.0.0/COREDataService-1.0.0.dbs` | Queries `setUsuarioRegistro`, `insertTokenRegistro`, `checkUsuarioDuplicado`, `getUsernickByEmail`, `insertMembership`, `deleteMembership`, `setEmpresaAsset`, `deleteEmpresaAsset` + recursos `/usuario/registro`, `/token`, `/usuario/duplicado`, `/usuario/usernick`, `/membership` (POST/DELETE/POST delete), `/empresa/asset` |
| `toolsCOREAPI_1.0.0/toolsCOREAPI-1.0.0.xml` | `POST /empresa` (con asset+actor-maps), `POST /usuario/registro`, `POST /usuario/bpm-asset`, `POST /rol/asignar`, `POST /rol/desasignar` |

**Validación post-deploy:**

```bash
# DataService responde:
curl -k https://<host>:8253/services/COREDataService?wsdl | head

# Endpoints clave:
curl -k -X POST https://<host>:8253/services/COREDataService/usuario/duplicado \
  -H 'Content-Type: application/json' -d '{"email":"prueba@x.com"}'
```

---

## 4) Configuración de Email (Postfix + stunnel SMTPS 465)

> Producción debe replicar el mismo esquema que ya quedó funcional en TEST:
> Postfix → stunnel local (127.0.0.1:10025) → `mauriper.ferozo.com:465` (SSL implícito).
> No se usa 587/STARTTLS.

### 4.1 stunnel cliente SMTPS

`/etc/stunnel/smtps.conf`:
```ini
foreground = no
debug = 5
output = /var/log/stunnel-smtps.log

[smtps-relay]
client = yes
accept = 127.0.0.1:10025
connect = mauriper.ferozo.com:465
sslVersion = TLSv1.2
verify = 0
```

Service unit `/etc/systemd/system/stunnel-smtps.service`:
```ini
[Unit]
Description=stunnel SMTPS client for Postfix relay
After=network.target

[Service]
Type=forking
ExecStart=/usr/bin/stunnel /etc/stunnel/smtps.conf
ExecStop=/bin/kill -TERM $MAINPID
Restart=on-failure

[Install]
WantedBy=multi-user.target
```

```bash
sudo systemctl daemon-reload
sudo systemctl enable --now stunnel-smtps
ss -tlnp | grep 10025      # debe escuchar
```

### 4.2 Postfix `/etc/postfix/main.cf`

Agregar / verificar:

```ini
myhostname = <hostname-prod>
myorigin = trazalog.com
inet_interfaces = localhost
inet_protocols = all
mydestination = $myhostname, localhost.$mydomain, localhost

# Relay vía stunnel local (que ya hace SSL a Ferozo:465)
relayhost = [127.0.0.1]:10025

# Auth SASL en plano (la cifra el stunnel)
smtp_sasl_auth_enable = yes
smtp_sasl_password_maps = hash:/etc/postfix/sasl_passwd
smtp_sasl_security_options = noanonymous
smtp_sasl_mechanism_filter = plain, login
smtp_tls_security_level = none

# Reescritura de remitente para que el From visible no diga "root"
smtp_generic_maps = hash:/etc/postfix/generic
sender_canonical_maps = hash:/etc/postfix/sender_canonical
```

### 4.3 Credenciales SMTP (`/etc/postfix/sasl_passwd`)

```
[127.0.0.1]:10025 infra@trazalog.com:jp@qW@2LCLRbJzs
```

```bash
sudo chmod 600 /etc/postfix/sasl_passwd
sudo postmap /etc/postfix/sasl_passwd
```

### 4.4 Reescritura del From (que NO aparezca "root")

**`/etc/postfix/sender_canonical`** (envelope-from):
```
root            register@trazalog.com
trazalog        register@trazalog.com
@vm-demo-trazalog.localdomain  register@trazalog.com
```

**`/etc/postfix/generic`** (header `From:` saliente al relay):
```
root@vm-demo-trazalog.localdomain         Trazalog Tools <register@trazalog.com>
trazalog@vm-demo-trazalog.localdomain     Trazalog Tools <register@trazalog.com>
register@vm-demo-trazalog.localdomain     Trazalog Tools <register@trazalog.com>
```

> Reemplazar `vm-demo-trazalog.localdomain` por el hostname FQDN real de PROD (`hostname -f`).

```bash
sudo postmap /etc/postfix/sender_canonical
sudo postmap /etc/postfix/generic
sudo systemctl reload postfix
```

### 4.5 Hardening del envío desde la app PHP

`application/config/email.php` — forzar el sender en sendmail para que el envelope ya salga correcto:

```php
$config = array(
    'protocol' => 'sendmail',
    'mailpath' => '/usr/sbin/sendmail -t -i -f register@trazalog.com',
    'mailtype' => 'html',
    'charset'  => 'utf-8',
    'newline'  => "\r\n",
    'crlf'     => "\r\n",
);
```

Esto garantiza que cualquier envío desde CI use `register@trazalog.com` como envelope-from además del header `From:` que ya pone `Main.php` (`Trazalog Tools <register@trazalog.com>`).

### 4.6 Buzón local `trazalog`

Limpiar de TEST/instalación previa:
```bash
sudo cp /var/mail/trazalog /var/mail/trazalog.bak.$(date +%F-%H%M) 2>/dev/null
sudo truncate -s 0 /var/mail/trazalog
```

`/etc/aliases` — redirigir notificaciones del sistema:
```
root:     ruiz.rodolfo@gmail.com
trazalog: ruiz.rodolfo@gmail.com
```

```bash
sudo newaliases
```

### 4.7 Validación email

```bash
# Prueba de envío básica
echo -e "Subject: Prueba PROD\n\nHola desde PROD" | sendmail -v ruiz.rodolfo@gmail.com

# Logs
sudo tail -f /var/log/maillog            # debe verse status=sent vía relay=127.0.0.1[127.0.0.1]:10025
sudo tail -f /var/log/stunnel-smtps.log  # SSL handshake OK con 200.58.111.54:465

# Cola debe estar vacía
mailq
```

Verificar en bandeja del destinatario que el remitente sea **"Trazalog Tools <register@trazalog.com>"** y NO "root".

---

## 5) Otros detalles realizados hoy

- **Compatibilidad PHP 5.x** en `application/controllers/Main.php`: reemplazo de operador `??` por `isset(...) ? ... : ...` (causaba HTTP 500 *Parsing Error*).
- **`COREDataService`**: se renombró la query `setTokenRegistro` → `insertTokenRegistro` para alinear con baseline del commit `a6398ce`. El recurso `POST /token` ya apunta al nuevo nombre.
- **Comparación integral vs commit `a6398ce`**: los 4 artefactos WSO2 quedaron sin operaciones faltantes ni queries faltantes. Templates/Sequences (`bpmAPICallTemplate`, `toolsBpmActorGrupo`, `toolsBpmActorMembership`, `toolsCreateRole`, `toolsFault`, `toolsLogAPI`, `toolsMANAPI`) están alineados.

---

## 6) Smoke test post-deploy

1. **Healthcheck DS**: `GET /services/COREDataService/healthcheck`.
2. **Formulario público de registro** abre y muestra los items dinámicos del `FORMULARIO_REGISTRO_ID`.
3. **Alta de usuario por registro**:
   - Inserta en `seg.users` con `reg_info_id`.
   - Inserta token en `seg.tokens`.
   - Llega email "Trazalog Tools" con link de activación.
4. **Click activación** → set password (PBKDF2 vía `seg.insert_usuario_con_hash`).
5. **Alta de empresa**:
   - Inserta `core.empresas` (PG).
   - Trigger MariaDB ejecuta `sp_create_empresa_groups_and_actions`.
   - WSO2 `POST /empresa` resuelve asset + actor-maps en BPM.
6. **Alta rol/grupo a actor**: `POST /actor/rol`, `POST /actor/grupo`, `POST /rol/asignar`, `POST /rol/desasignar` responden `200 ok`.

---

**Mantener este documento como checklist de release. Versionar en git junto con los artefactos.**
