# Troubleshooting: Error al crear usuario (adduser → API)

Cuando el formulario **Alta de usuario** muestra "Error al crear usuario" o "Error del servicio (HTTP 404)", la petición llega a la aplicación PHP y esta llama a la API WSO2. El fallo puede estar en: red/conectividad, API WSO2, DataService, PostgreSQL o AssetPlanner.

---

## 0) Dónde está el log de la aplicación (CodeIgniter)

**Ruta del archivo de log**: dentro del proyecto, en **`application/logs/log-YYYY-MM-DD.php`** (por ejemplo `application/logs/log-2026-02-28.php` para el 28/02/2026).

- Abre ese archivo (mismo día en que ocurrió el error) y busca líneas que contengan: `crearUsuarioAPI`, `adduser`, `TRAZA`, `User_model`, `MAIN`.
- Si no ves nada: comprueba que el usuario con el que corre Apache (p. ej. `daemon`) pueda escribir en `application/logs/` y que en `application/config/config.php` tengas `$config['log_threshold'] = 2` (o 1 como mínimo para que se registren ERROR).

---

## 1) Revisar el mensaje en pantalla (después del cambio de manejo de errores)

- **Producción**: Se muestra el mensaje de error que devuelve la API (ej. "Error general crear usuario").
- **Desarrollo** (`ENVIRONMENT === 'development'`): Se muestra ese mensaje más un **Detalle** con el cuerpo de respuesta o el detalle que envíe la API.

Para ver el detalle en desarrollo, en `index.php` debe estar definido `ENVIRONMENT` como `development` (o definir `$_SERVER['CI_ENV'] = 'development'` en el vhost/setenv si usas Apache).

---

## 2) Log de la aplicación PHP (CodeIgniter)

Los errores de `crearUsuarioAPI()` se registran con `log_message('ERROR', ...)`.

- **Ruta del log**: `application/logs/log-YYYY-MM-DD.php` (por defecto; si `config['log_path']` está vacío, es la carpeta `application/logs/` del proyecto).
- **Qué buscar**: líneas con `#TRAZA | User_model | crearUsuarioAPI()` o `#TRAZA | MAIN | adduser()`.
- **Permisos**: el usuario con el que corre Apache (ej. `daemon`) debe poder escribir en `application/logs/`. Si no escribe, revisar permisos o `log_threshold` en `application/config/config.php` (1 = solo ERROR, 2 = DEBUG, etc.).

Ejemplo de líneas útiles:

```
ERROR - #TRAZA | User_model | crearUsuarioAPI() >> Error del servicio (HTTP 404) | detalle: {"respuesta":{"error":"..."}}
ERROR - #TRAZA | MAIN | adduser() >> crearUsuarioAPI falló: Error general crear usuario | ...
```

---

## 3) Log de WSO2 MI (recomendado cuando falla la API)

La API de creación de usuario corre en **WSO2 Micro Integrator**. Cualquier fallo dentro de la API (secuencia, llamada a DataService, a AssetPlanner, etc.) suele quedar registrado en el log de WSO2.

- **Ruta típica del log** (en tu instalación local):
  - Linux (instalación típica): `$MI_HOME/repository/logs/wso2carbon.log`
  - Ejemplo con MI en home: `~/.wso2-mi/micro-integrator/wso2mi-4.5.0/repository/logs/wso2carbon.log`

- **Qué buscar**:
  - Errores en la **secuencia** que atiende `POST /tools/core/usuario` (excepciones, fault).
  - Errores de **conexión** a PostgreSQL o al DataService (timeout, connection refused, auth).
  - Errores de **AssetPlanner** (MySQL/MariaDB): "No se pudo crear usuario en Asset Planner", o respuestas no 2xx.
  - Mensajes de **rollback** (`_delete_usuario`, etc.) que indican que se dio marcha atrás tras un fallo en un paso posterior (ej. BPM o AssetPlanner).

Ejemplo de búsqueda en la misma máquina donde corre WSO2:

```bash
# Ver últimas líneas del log (ajusta MI_HOME si es distinto)
tail -200 ~/.wso2-mi/micro-integrator/wso2mi-4.5.0/repository/logs/wso2carbon.log

# Buscar errores o menciones a usuario
grep -i "usuario\|error\|exception\|fault\|rollback" ~/.wso2-mi/micro-integrator/wso2mi-4.5.0/repository/logs/wso2carbon.log | tail -100
```

Con eso puedes deducir si el fallo fue: timeout a BD, error en el DataService, error en AssetPlanner, o fault por BPM, etc.

---

## 4) Causa típica: función PostgreSQL no existe o firma distinta

Si en el log aparece algo como:

```text
ERROR: function seg.insert_usuario_con_hash(character varying, ..., unknown, unknown, unknown) does not exist
  Hint: No function matches the given name and argument types.
```

**Causa**: En el PostgreSQL al que se conecta WSO2 (datasource del DataService) **no existe** la función `seg.insert_usuario_con_hash` con 12 parámetros (incluidos `image_name` e `image`), o existe una versión antigua con solo 10 parámetros.

**Solución**: Ejecutar en ese PostgreSQL (el que usa WSO2, p. ej. `10.142.0.13`, base `tools_prod_t`, schema `seg`) el script:

```bash
# Con las credenciales correctas del datasource Tools (PostgreSQL)
psql -h 10.142.0.13 -p 5432 -U postgres -d tools_prod_t -f /mnt/win/dev/git/traz-comp-dnato/development/sp_insert_usuario_con_hash_con_imagen.sql
```

Ese script define `seg.insert_usuario_con_hash` con 12 parámetros (incluidos `p_image_name` y `p_image`). Mientras esa función no exista o no coincida la firma, el DataService seguirá devolviendo 404 con "POST /usuario con problemas".

---

## 5) Resumen de causas habituales

| Síntoma / Log | Causa probable |
|----------------|----------------|
| PHP log: "function seg.insert_usuario_con_hash(...) does not exist" | **Falta crear/actualizar el SP en PostgreSQL** que usa WSO2. Ejecutar `development/sp_insert_usuario_con_hash_con_imagen.sql` en esa BD. |
| PHP log: "No se pudo conectar" o timeout | WSO2 no accesible (puerto 8290 en desa), firewall, o URL incorrecta en `API_CORE`. |
| PHP log: HTTP 404 y body con "Error general crear usuario" / "POST /usuario con problemas" | La API respondió con fault; revisar **wso2carbon.log** y/o comprobar que el SP existe en PostgreSQL (ver arriba). |
| WSO2 log: error de conexión a PostgreSQL/MySQL | Revisar conectividad y credenciales de los datasources en WSO2. |
| WSO2 log: rollback / _delete_usuario | La API creó el usuario en PostgreSQL pero falló un paso posterior (AssetPlanner o BPM) y ejecutó rollback. |

---

## Referencias

- Constante de URL API: `application/config/constants.php` (`API_CORE`, puerto **8290** en desarrollo, **8280** en producción).
- Si en el log ves `#CURL | #URL >> http://10.142.0.13:8280/...` y tu WSO2 local usa el puerto **8290**, la app está en modo producción: define `ENVIRONMENT=development` (p. ej. en `index.php` o con `SetEnv CI_ENV development` en el VirtualHost de Apache) para que use 8290.
- Controlador: `application/controllers/Main.php` → `adduser()`.
- Modelo: `application/models/User_model.php` → `crearUsuarioAPI()`.
