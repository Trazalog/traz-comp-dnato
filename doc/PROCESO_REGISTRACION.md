# Proceso de Registración de Usuario

Este documento describe el flujo completo del proceso de registración de usuarios en Trazalog Tools.

## Tabla de Contenidos

1. [Resumen Ejecutivo](#resumen-ejecutivo)
2. [Flujo General del Proceso](#flujo-general-del-proceso)
3. [Diagramas de Secuencia](#diagramas-de-secuencia)
4. [Detalles Técnicos](#detalles-técnicos)
5. [Validaciones](#validaciones)
6. [Base de Datos](#base-de-datos)

---

## Resumen Ejecutivo

El proceso de registración de usuarios en Trazalog Tools consta de **5 etapas principales**:

1. **Formulario de Registro Inicial**: Usuario completa datos básicos (nombre, email, razón social, teléfono, país)
2. **Validaciones**: Se validan duplicados, formato de teléfono y reCAPTCHA
3. **Creación de Usuario**: Se crea el usuario en la base de datos (sin password) y se genera token de activación
4. **Activación de Cuenta**: Usuario recibe email, establece su contraseña y activa la cuenta
5. **Formulario Adicional**: Usuario completa formulario dinámico con información adicional
6. **Finalización**: Usuario ve página de bienvenida con información de usuarios freemium

---

## Flujo General del Proceso

```mermaid
flowchart TD
    A[Usuario accede a /main/register] --> B[Mostrar formulario de registro]
    B --> C[Usuario completa formulario]
    C --> D{Validaciones}
    D -->|Error| B
    D -->|OK| E[Crear usuario en BD]
    E --> F[Generar token de activación]
    F --> G[Enviar email con link]
    G --> H[Usuario hace clic en link]
    H --> I[Validar token]
    I --> J[Mostrar formulario de password]
    J --> K[Usuario establece password]
    K --> L[Actualizar usuario con password]
    L --> M[Crear sesión]
    M --> N[Mostrar formulario dinámico]
    N --> O[Usuario completa formulario]
    O --> P[Guardar respuestas]
    P --> Q[Mostrar página de bienvenida]
```

---

## Diagramas de Secuencia

### 1. Proceso de Registro Inicial

```mermaid
sequenceDiagram
    participant U as Usuario
    participant B as Navegador
    participant C as Main Controller
    participant M as User Model
    participant DB as PostgreSQL
    participant E as Email Service

    U->>B: Accede a /main/register
    B->>C: GET /main/register
    C->>M: obtenerPaisesRegistracion()
    M->>DB: SELECT países desde REST_CORE
    DB-->>M: Lista de países
    M-->>C: $data['paises']
    C->>B: Mostrar formulario register.php
    B-->>U: Formulario de registro

    U->>B: Completa y envía formulario
    B->>C: POST /main/register (datos del formulario)
    
    C->>C: Validar formulario (CodeIgniter)
    alt Validación falla
        C->>B: Mostrar errores
        B-->>U: Formulario con errores
    else Validación OK
        C->>M: isDuplicate(email)
        M->>DB: SELECT FROM seg.users WHERE email = ?
        DB-->>M: Resultado
        M-->>C: true/false
        
        alt Email duplicado
            C->>B: Mensaje error: "Email ya existe"
            B-->>U: Error
        else Email no duplicado
            C->>M: existeRazonSocial(razon_social, pais_id)
            M->>DB: SELECT descripción país desde core.tablas
            DB-->>M: Descripción país
            M->>DB: SELECT FROM core.empresas WHERE UPPER(descripcion) = ? AND UPPER(pais) = ?
            DB-->>M: Resultado
            M-->>C: true/false
            
            alt Razón social duplicada
                C->>B: Mensaje error: "Razón social ya existe"
                B-->>U: Error
            else Razón social OK
                C->>M: validarTelefonoPorPais(telefono, pais_id)
                M-->>C: true/false
                
                alt Teléfono inválido
                    C->>B: Mensaje error: "Teléfono inválido"
                    B-->>U: Error
                else Teléfono válido
                    alt reCAPTCHA habilitado
                        C->>E: Verificar reCAPTCHA con Google
                        E-->>C: Resultado
                        alt reCAPTCHA inválido
                            C->>B: Mensaje error: "reCAPTCHA inválido"
                            B-->>U: Error
                        end
                    end
                    
                    C->>C: procesarRegistro($clean)
                    C->>M: insertUser($clean)
                    M->>DB: INSERT INTO seg.users (first_name, last_name, email, role, status, banned_users, reg_pais_id, reg_razon_social, telefono)
                    DB-->>M: user_id
                    M-->>C: user_id
                    
                    C->>M: insertToken(user_id)
                    M->>DB: INSERT INTO seg.tokens (token, user_id, created)
                    DB-->>M: token
                    M-->>C: token
                    
                    C->>C: Generar URL: /main/complete/token/{token}
                    C->>E: Enviar email de activación
                    E-->>C: Email enviado
                    
                    C->>B: Redirigir a /main/register con mensaje de éxito
                    B-->>U: "Registro exitoso! Revise su email"
                end
            end
        end
    end
```

### 2. Proceso de Activación de Cuenta

```mermaid
sequenceDiagram
    participant U as Usuario
    participant B as Navegador
    participant E as Email
    participant C as Main Controller
    participant M as User Model
    participant DB as PostgreSQL
    participant S as Sesión

    U->>E: Recibe email de activación
    E->>U: Link: /main/complete/token/{token}
    U->>B: Hace clic en link
    B->>C: GET /main/complete/token/{token}
    
    C->>C: Decodificar token (base64)
    C->>M: isTokenValid(token)
    M->>DB: SELECT FROM seg.tokens WHERE token = ? AND user_id = ?
    DB-->>M: Token info
    
    alt Token inválido o expirado
        M-->>C: false
        C->>B: Redirigir a /main/login con error
        B-->>U: "Token inválido o expirado"
    else Token válido
        M->>DB: SELECT FROM seg.users WHERE id = ?
        DB-->>M: User info
        M-->>C: User info
        
        C->>B: Mostrar formulario complete_password.php
        B-->>U: Formulario para establecer password
        
        U->>B: Completa password y confirma
        B->>C: POST /main/complete (password, passconf)
        
        C->>C: Validar password (min_length[5], matches)
        alt Validación falla
            C->>B: Mostrar errores
            B-->>U: Formulario con errores
        else Validación OK
            C->>C: Crear hash de password (PBKDF2)
            C->>M: updateUserInfo(password_hash)
            M->>DB: UPDATE seg.users SET password = ? WHERE id = ?
            DB-->>M: Usuario actualizado
            M-->>C: User info
            
            C->>S: Crear sesión de usuario
            S-->>C: Sesión creada
            
            C->>B: Redirigir a /register/register_success
            B-->>U: Página de formulario adicional
        end
    end
```

### 3. Proceso de Formulario Dinámico

```mermaid
sequenceDiagram
    participant U as Usuario
    participant B as Navegador
    participant C as Register Controller
    participant F as Forms Model
    participant DB as PostgreSQL
    participant S as Sesión

    U->>B: Accede a /register/register_success
    B->>C: GET /register/register_success
    
    C->>F: generarInstancia(FORMULARIO_REGISTRO_ID)
    F->>DB: INSERT INTO frm.instancias_formularios (form_id, fecha_creacion)
    DB-->>F: info_id
    F-->>C: info_id
    
    C->>S: Guardar temp_info_id en sesión
    C->>F: Cargar estructura del formulario
    F->>DB: SELECT items, valores desde frm.items, core.tablas
    DB-->>F: Estructura del formulario
    F-->>C: HTML del formulario
    
    C->>B: Mostrar formulario_page.php con formulario dinámico
    B-->>U: Formulario dinámico (radios, checkboxes, textarea)
    
    U->>B: Completa formulario y hace clic en "Guardar"
    B->>C: POST /register/guardarFormularioRegistro (AJAX)
    
    C->>S: Obtener user_id y temp_info_id de sesión
    C->>F: actualizar(info_id, form_data)
    F->>DB: INSERT/UPDATE en frm.respuestas_formularios
    DB-->>F: Resultado
    F-->>C: Éxito
    
    C->>DB: UPDATE seg.users SET reg_info_id = ? WHERE id = ?
    DB-->>C: Usuario actualizado
    
    C->>S: Limpiar temp_info_id
    C->>B: JSON: {success: true}
    B->>B: Redirigir a /register/registro_completo
    B->>C: GET /register/registro_completo
    C->>B: Mostrar bienvenida_page.php
    B-->>U: Página de bienvenida con usuarios freemium
```

### 4. Flujo Completo Integrado

```mermaid
sequenceDiagram
    participant U as Usuario
    participant B as Navegador
    participant MC as Main Controller
    participant RC as Register Controller
    participant UM as User Model
    participant FM as Forms Model
    participant DB as PostgreSQL
    participant E as Email Service
    participant S as Sesión

    Note over U,S: ETAPA 1: Registro Inicial
    U->>B: Accede a /main/register
    B->>MC: GET /main/register
    MC->>B: Formulario de registro
    B-->>U: Formulario
    
    U->>B: Envía datos
    B->>MC: POST /main/register
    MC->>UM: Validaciones (email, razón social, teléfono)
    UM->>DB: Consultas de validación
    DB-->>UM: Resultados
    UM-->>MC: Validaciones OK
    
    MC->>UM: insertUser()
    UM->>DB: INSERT INTO seg.users
    DB-->>UM: user_id
    UM-->>MC: user_id
    
    MC->>UM: insertToken()
    UM->>DB: INSERT INTO seg.tokens
    DB-->>UM: token
    UM-->>MC: token
    
    MC->>E: Enviar email de activación
    E-->>U: Email con link
    
    Note over U,S: ETAPA 2: Activación
    U->>B: Clic en link del email
    B->>MC: GET /main/complete/token/{token}
    MC->>UM: isTokenValid()
    UM->>DB: Validar token
    DB-->>UM: Token válido
    UM-->>MC: User info
    MC->>B: Formulario de password
    B-->>U: Formulario
    
    U->>B: Establece password
    B->>MC: POST /main/complete
    MC->>UM: updateUserInfo(password_hash)
    UM->>DB: UPDATE seg.users SET password
    DB-->>UM: Usuario actualizado
    UM-->>MC: User info
    
    MC->>S: Crear sesión
    MC->>B: Redirigir a /register/register_success
    
    Note over U,S: ETAPA 3: Formulario Dinámico
    B->>RC: GET /register/register_success
    RC->>FM: generarInstancia()
    FM->>DB: INSERT INTO frm.instancias_formularios
    DB-->>FM: info_id
    FM-->>RC: info_id
    
    RC->>S: Guardar temp_info_id
    RC->>FM: Cargar estructura formulario
    FM->>DB: SELECT items, valores
    DB-->>FM: Estructura
    FM-->>RC: HTML formulario
    RC->>B: Mostrar formulario dinámico
    B-->>U: Formulario
    
    U->>B: Completa y guarda
    B->>RC: POST /register/guardarFormularioRegistro (AJAX)
    RC->>FM: actualizar(info_id, datos)
    FM->>DB: INSERT/UPDATE frm.respuestas_formularios
    DB-->>FM: Éxito
    FM-->>RC: Éxito
    
    RC->>DB: UPDATE seg.users SET reg_info_id
    DB-->>RC: OK
    RC->>S: Limpiar temp_info_id
    RC->>B: JSON {success: true}
    B->>B: Redirigir a /register/registro_completo
    
    Note over U,S: ETAPA 4: Finalización
    B->>RC: GET /register/registro_completo
    RC->>B: Mostrar bienvenida_page.php
    B-->>U: Página de bienvenida
```

---

## Detalles Técnicos

### Controladores Involucrados

#### Main Controller (`application/controllers/Main.php`)

- **`register()`**: Maneja el formulario de registro inicial
  - Valida datos del formulario
  - Verifica duplicados (email, razón social)
  - Valida formato de teléfono según país
  - Verifica reCAPTCHA (si está habilitado)
  - Llama a `procesarRegistro()`

- **`procesarRegistro($clean)`**: Procesa el registro
  - Inserta usuario en `seg.users` (sin password)
  - Genera token de activación
  - Envía email con link de activación

- **`complete()`**: Maneja la activación de cuenta
  - Valida token
  - Muestra formulario para establecer password
  - Actualiza usuario con password hasheado
  - Crea sesión
  - Redirige a formulario adicional

#### Register Controller (`application/controllers/Register.php`)

- **`register_success()`**: Muestra formulario dinámico adicional
  - Crea instancia del formulario
  - Carga estructura del formulario
  - Muestra formulario al usuario

- **`guardarFormularioRegistro()`**: Guarda respuestas del formulario
  - Actualiza instancia del formulario
  - Guarda respuestas en `frm.respuestas_formularios`
  - Actualiza `reg_info_id` en `seg.users`

- **`registro_completo()`**: Muestra página de bienvenida
  - Muestra información de usuarios freemium
  - Finaliza el proceso de registro

### Modelos Involucrados

#### User Model (`application/models/User_model.php`)

- **`insertUser($d)`**: Inserta usuario en `seg.users`
  - Campos: `first_name`, `last_name`, `email`, `role`, `status`, `banned_users`, `reg_pais_id`, `reg_razon_social`, `telefono`
  - **NO incluye password** (se establece después)

- **`insertToken($user_id)`**: Genera token de activación
  - Crea token aleatorio (30 caracteres)
  - Inserta en `seg.tokens`
  - Retorna `token + user_id`

- **`isTokenValid($token)`**: Valida token
  - Verifica que el token exista
  - Verifica que no haya expirado (debe ser del día actual)
  - Retorna información del usuario si es válido

- **`isDuplicate($email)`**: Verifica email duplicado
  - Consulta `seg.users` por email

- **`existeRazonSocial($razon_social, $pais_id)`**: Verifica razón social duplicada
  - Obtiene descripción del país desde `core.tablas`
  - Busca en `core.empresas` por razón social y país (case-insensitive)

- **`validarTelefonoPorPais($telefono, $pais_id)`**: Valida formato de teléfono
  - Usa patrones regex según el país

- **`updateUserInfo($cleanPost)`**: Actualiza información del usuario
  - Actualiza password hasheado
  - Actualiza otros campos si se proporcionan

#### Forms Model (`modules/traz-comp-formularios/application/models/Forms.php`)

- **`generarInstancia($form_id)`**: Crea nueva instancia del formulario
  - Inserta en `frm.instancias_formularios`
  - Retorna `info_id`

- **`actualizar($info_id, $form_data)`**: Guarda respuestas del formulario
  - Inserta/actualiza en `frm.respuestas_formularios`
  - Maneja arrays de checkboxes (implode con `_`)

### Tablas de Base de Datos

#### `seg.users`
- Almacena información básica del usuario
- Campos relevantes: `id`, `first_name`, `last_name`, `email`, `password`, `role`, `status`, `banned_users`, `reg_pais_id`, `reg_razon_social`, `telefono`, `reg_info_id`

#### `seg.tokens`
- Almacena tokens de activación
- Campos: `token`, `user_id`, `created`
- Los tokens expiran después de 1 día

#### `frm.instancias_formularios`
- Almacena instancias de formularios dinámicos
- Campos: `info_id`, `form_id`, `fecha_creacion`

#### `frm.respuestas_formularios`
- Almacena respuestas de los formularios
- Campos: `info_id`, `item_id`, `valor`

#### `core.empresas`
- Almacena información de empresas
- Se usa para validar razón social duplicada

#### `core.tablas`
- Almacena tablas de referencia (países, estados, etc.)
- Se usa para obtener descripción de países

---

## Validaciones

### Validaciones del Formulario de Registro

1. **Validación de Campos Requeridos** (CodeIgniter Form Validation)
   - `firstname`: Requerido
   - `lastname`: Requerido
   - `email`: Requerido, formato válido
   - `reg_razon_social`: Requerido
   - `telefono`: Requerido
   - `reg_pais_id`: Requerido

2. **Validación de Email Duplicado**
   - Consulta directa a `seg.users`
   - Si existe, muestra error: "El email que intenta registrar ya existe..."

3. **Validación de Razón Social Duplicada**
   - Obtiene descripción del país desde `core.tablas`
   - Busca en `core.empresas` por razón social y país (case-insensitive)
   - Si existe, muestra error: "La Razón Social ingresada ya existe en el sistema para el país solicitado"

4. **Validación de Teléfono**
   - Valida formato según país usando patrones regex
   - Países soportados: AR, BR, CL, UY, PE, EC, MX, BO
   - Si es inválido, muestra error: "El formato del teléfono no es válido para el país seleccionado"

5. **Validación reCAPTCHA** (si está habilitado)
   - Verifica con Google reCAPTCHA API
   - Si es inválido, muestra error: "Error en la validación reCAPTCHA"

### Validaciones del Formulario de Password

1. **Validación de Password** (CodeIgniter Form Validation)
   - `password`: Requerido, mínimo 5 caracteres
   - `passconf`: Requerido, debe coincidir con `password`

2. **Validación de Token**
   - Token debe existir en `seg.tokens`
   - Token debe ser del día actual (no expirado)
   - Si es inválido, redirige a login con error

---

## Base de Datos

### Inserciones Realizadas

1. **`seg.users`** (durante `insertUser()`)
   ```sql
   INSERT INTO seg.users (
       first_name, last_name, email, role, status, banned_users,
       reg_pais_id, reg_razon_social, telefono
   ) VALUES (...)
   ```

2. **`seg.tokens`** (durante `insertToken()`)
   ```sql
   INSERT INTO seg.tokens (token, user_id, created)
   VALUES (?, ?, CURRENT_DATE)
   ```

3. **`frm.instancias_formularios`** (durante `generarInstancia()`)
   ```sql
   INSERT INTO frm.instancias_formularios (form_id, fecha_creacion)
   VALUES (?, CURRENT_TIMESTAMP)
   ```

4. **`frm.respuestas_formularios`** (durante `actualizar()`)
   ```sql
   INSERT INTO frm.respuestas_formularios (info_id, item_id, valor)
   VALUES (?, ?, ?)
   ON CONFLICT DO UPDATE ...
   ```

### Actualizaciones Realizadas

1. **`seg.users`** (durante `updateUserInfo()`)
   ```sql
   UPDATE seg.users
   SET password = ?
   WHERE id = ?
   ```

2. **`seg.users`** (durante `guardarFormularioRegistro()`)
   ```sql
   UPDATE seg.users
   SET reg_info_id = ?
   WHERE id = ?
   ```

---

## Notas Importantes

1. **Password**: El password NO se establece durante el registro inicial. Se establece durante la activación de cuenta.

2. **Hash de Password**: Se usa PBKDF2 con SHA256, 1000 iteraciones, formato: `sha256:1000:salt:hash` (compatible con PHP Password library).

3. **Token de Activación**: Los tokens expiran después de 1 día. El formato es `token(30 chars) + user_id`.

4. **Formulario Dinámico**: El formulario adicional se genera dinámicamente desde la base de datos usando el módulo `traz-comp-formularios`.

5. **Sesión**: La sesión se crea después de establecer el password, no durante el registro inicial.

6. **Empresa**: La razón social se guarda en `reg_razon_social` del usuario, pero NO se crea automáticamente una empresa en `core.empresas` durante el registro. Solo se valida que no exista duplicada.

---

## Flujo de Datos

```
Usuario → Formulario → Validaciones → BD (seg.users sin password)
  ↓
Email con token → Usuario hace clic → Validar token → Formulario password
  ↓
Establecer password → BD (seg.users con password) → Sesión → Formulario dinámico
  ↓
Completar formulario → BD (frm.respuestas_formularios) → Página de bienvenida
```

---

## Referencias

- Controlador: `application/controllers/Main.php` (métodos `register()`, `procesarRegistro()`, `complete()`)
- Controlador: `application/controllers/Register.php` (métodos `register_success()`, `guardarFormularioRegistro()`, `registro_completo()`)
- Modelo: `application/models/User_model.php`
- Modelo: `modules/traz-comp-formularios/application/models/Forms.php`
- Vista: `application/views/register.php`
- Vista: `application/views/complete_password.php`
- Vista: `application/views/formulario_page.php`
- Vista: `application/views/bienvenida_page.php`

