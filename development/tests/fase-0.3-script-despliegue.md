# Script de Despliegue Automático - Fase 0.3

## 📋 Script Creado

He creado un script que automatiza todos los pasos de despliegue:

**Ubicación**: `development/scripts/deploy_car_fase_0_3.sh`

## 🚀 Cómo Ejecutarlo

```bash
cd /mnt/win/dev/git/traz-comp-dnato
bash development/scripts/deploy_car_fase_0_3.sh
```

O si prefieres hacerlo ejecutable:

```bash
chmod +x development/scripts/deploy_car_fase_0_3.sh
./development/scripts/deploy_car_fase_0_3.sh
```

## ✅ Lo que Hace el Script

1. **Elimina drivers PostgreSQL de `dropins/`**
   - Elimina `postgresql_jdbc_1.0.0.jar`
   - Elimina `postgresql-jdbc.jar` (si existe)

2. **Elimina CAR existente** (si existe)
   - Espera 10 segundos para undeploy

3. **Copia el CAR al directorio de despliegue**
   - Desde: `/mnt/win/dev/git/traz-comp-dnato/development/COREToolsApplication_1.0.0.car`
   - Hacia: `/home/rodolfo/dev/wso2mi-4.3.0/repository/deployment/server/carbonapps/`

4. **Espera 20 segundos** para que WSO2 MI detecte y despliegue

5. **Verifica logs de despliegue**
   - Busca mensajes de éxito/error
   - Busca errores de PostgreSQL
   - Busca errores OSGi del driver

6. **Verifica que el DataService está accesible**
   - Intenta acceder a `http://localhost:8290/services/COREDataService?wsdl`

## 📊 Resultados Esperados

### ✅ Despliegue Exitoso

Deberías ver en la salida:
- `✓ Drivers PostgreSQL eliminados de dropins/`
- `✓ CAR copiado a: ...`
- `Successfully Deployed Carbon Application : COREDataService`
- El DataService responde con XML del WSDL

### ❌ Despliegue Fallido

Si falla, el script mostrará:
- Errores específicos en los logs
- Razón del fallo (conexión PostgreSQL, OSGi, etc.)

## 🔧 Solución de Problemas

### Si el script falla por permisos:

```bash
# Ejecutar con sudo (si es necesario)
sudo bash development/scripts/deploy_car_fase_0_3.sh
```

### Si PostgreSQL no está accesible:

El script mostrará errores de conexión. Verifica:
```bash
PGPASSWORD='!Password00' psql -h 10.142.0.13 -p 5432 -U postgres -d tools_prod_t -c "SELECT 1;"
```

### Si el CAR ya existe:

El script lo elimina automáticamente antes de copiar el nuevo.

## 📝 Notas

- El script espera que WSO2 MI esté corriendo
- Si WSO2 MI no está corriendo, el despliegue fallará
- El script muestra todos los logs relevantes al final



