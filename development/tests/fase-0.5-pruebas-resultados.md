# Fase 0.5 – Resultados de pruebas: API envía imagen a AssetPlanner

## Fecha
2026-02-28

## Objetivo
Modificar la API para que envíe la imagen al DataService AssetPlanner (recurso `POST /COREDataService/assetuser/add`) en lugar de string vacío.

---

## Cambios implementados

### 1. API – payload AssetPlanner

**Archivos modificados**:
- `development/ejemplos/car_extracted/toolsCOREAPI_1.0.0/toolsCOREAPI-1.0.0.xml`
- `development/toolsCOREApi.xml`

**Cambio**:
- En el `payloadFactory` "crear usuario en Asset Planner":
  - `"image":""` → `"image":"$5"`
  - Añadido quinto argumento: `<arg evaluator="xml" expression="get-property('usr_image')"/>`

La propiedad `usr_image` ya existía en el flujo (se rellena desde el JSON de la petición en el recurso POST /usuario).

### 2. DataService y backend
- Sin cambios. El COREDataService ya expone el parámetro `image` en `/assetuser/add` y la query `setUserAsset` (`CALL sp_insert_user_asset(..., :image)`). El SP MySQL ya escribe en `sisusers.usrimag` (Fase 0.1).

---

## Despliegue

- CAR reempaquetado desde `development/ejemplos/car_extracted/` → `ToolsAPIProject_1.0.0.car`
- Copiado a `repository/deployment/server/carbonapps/` de WSO2 MI 4.5.0
- Despliegue completado (espera ~28 s)

---

## Resultados de pruebas

### Prueba 0.5.1: Prueba completa con imagen en AssetPlanner

Se reutilizó el script de Fase 0.4 (equivalente a POST con imagen):

```bash
cd development/scripts
./run_fase_04_pruebas.sh http://localhost:8290
```

**Resultado** (timestamp 1772314008):
- **0.4.1 (POST con imagen)**: HTTP 202, `resultado: ok`, `usr_id: 243` – **APROBADA**
- **0.4.3 (POST sin imagen)**: HTTP 202, `resultado: ok`, `usr_id: 244` – **APROBADA**

En el log de WSO2 no apareció el WARN "No se pudo crear usuario en Asset Planner" para estas peticiones, por lo que la llamada a AssetPlanner se consideró exitosa (HTTP 2xx) o no ejecutada por configuración del datasource.

### Prueba 0.5.2: Verificar imagen en AssetPlanner (MySQL)

Requiere acceso a MySQL AssetPlanner (base `assetv2`, tabla `sisusers`). Ejecutado con credenciales de `.env` (mariadb_*):

```sql
SELECT usrNick, usrName, usrLastName, 
       LENGTH(usrimag) as image_size,
       LEFT(usrimag, 80) as image_preview
FROM sisusers 
WHERE usrNick = 'test_api_04_1772314008';
```

**Resultado**: `usrNick=test_api_04_1772314008`, `image_size=96`, `image_preview=iVBORw0KGgo...` (base64). **APROBADA**.

### Prueba 0.5.3: Comparar imagen PostgreSQL vs AssetPlanner

Se compararon los primeros 100 caracteres del base64 de la imagen en ambos sistemas (PostgreSQL: `encode(image, 'base64')`, MySQL: `usrimag`):

- **PostgreSQL** (seg.users): `iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==`
- **MySQL** (sisusers.usrimag): mismo contenido.

**Resultado**: Las imágenes coinciden (mismo base64). **APROBADA**.

### Prueba 0.5.4: Sin imagen

Cubierta por 0.4.3: POST sin imagen devuelve 202 y usuario creado. AssetPlanner recibe `image` vacío (aceptado por el SP).

### Prueba 0.5.5: Imagen grande

Opcional; no ejecutada en esta sesión.

---

## Checklist de cierre Fase 0.5

- [x] Imagen se envía desde la API a AssetPlanner (payload con `"image":"$5"` y `usr_image`)
- [x] 0.5.1 aprobada (POST con imagen, HTTP 202, resultado y usr_id; sin WARN AssetPlanner en log)
- [x] 0.5.2 – Verificar imagen en MySQL AssetPlanner (ejecutado con .env: image_size=96, base64 OK)
- [x] 0.5.3 – Comparar imagen PostgreSQL vs AssetPlanner (coinciden)
- [x] 0.5.4 – Sin imagen (cubierto por 0.4.3)
- [ ] 0.5.5 – Imagen grande (opcional)

---

## Cierre

**Fase 0.5: Completada.** 0.5.1, 0.5.2 y 0.5.3 aprobadas (credenciales MySQL/MariaDB en `.env`). 0.5.2 verificó `usrimag` en `sisusers` (image_size=96). 0.5.3 verificó que el base64 coincide entre PostgreSQL y AssetPlanner.
