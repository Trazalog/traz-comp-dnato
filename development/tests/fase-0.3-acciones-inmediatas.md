# Acciones Inmediatas para Resolver Problema PostgreSQL - Fase 0.3

## 🚨 Problema Identificado

El DataService `COREDataService` no se despliega porque:
1. **PostgreSQL no está accesible** durante el despliegue (`SocketTimeoutException: Connect timed out`)
2. **Driver PostgreSQL tiene conflicto OSGi** (está en `lib/` y `dropins/`, causando error `javax.transaction.xa`)

## ✅ Acciones Inmediatas Requeridas

### Acción 1: Verificar Conectividad a PostgreSQL (CRÍTICO)

**Responsabilidad**: Usuario (según premisas de trabajo autónomo)

**Comando para verificar**:
```bash
PGPASSWORD='!Password00' psql -h 10.142.0.13 -p 5432 -U postgres -d tools_prod_t -c "SELECT 1;"
```

**Si falla**:
- Verificar que la VPN esté conectada
- Verificar que el servidor PostgreSQL esté corriendo
- Verificar que no haya firewall bloqueando el puerto 5432

**Si funciona**: Continuar con Acción 2

### Acción 2: Resolver Conflicto del Driver PostgreSQL

**Problema**: El driver PostgreSQL está en `lib/` Y en `dropins/`, causando conflicto OSGi.

**Solución**: Eliminar drivers PostgreSQL de `dropins/` (mantener solo en `lib/`)

**Comandos**:
```bash
# Verificar drivers actuales
ls -la /home/rodolfo/dev/wso2mi-4.3.0/lib/postgresql*.jar
ls -la /home/rodolfo/dev/wso2mi-4.3.0/dropins/postgresql*.jar

# Eliminar drivers de dropins/ (mantener solo en lib/)
rm /home/rodolfo/dev/wso2mi-4.3.0/dropins/postgresql*.jar

# Verificar que solo queda en lib/
ls -la /home/rodolfo/dev/wso2mi-4.3.0/lib/postgresql*.jar
```

**Razón**: Los drivers en `lib/` se cargan directamente en el classpath del sistema, evitando problemas OSGi. Los drivers en `dropins/` intentan cargarse como bundles OSGi y requieren dependencias adicionales como `javax.transaction.xa`.

### Acción 3: Reiniciar WSO2 MI

**Después de eliminar drivers de `dropins/`**, reiniciar WSO2 MI completamente:

```bash
# Detener WSO2 MI
pkill -f "micro-integrator"

# Esperar 5 segundos
sleep 5

# Iniciar WSO2 MI
cd /home/rodolfo/dev/wso2mi-4.3.0
nohup bin/micro-integrator.sh > /dev/null 2>&1 &

# Esperar 30-40 segundos para que inicie completamente
sleep 40

# Verificar que está corriendo
ps aux | grep -i "micro.*integrator" | grep java
netstat -tlnp | grep 8290
```

### Acción 4: Reintentar Despliegue del CAR

**Después de que WSO2 MI esté corriendo y PostgreSQL esté accesible**:

```bash
# Copiar CAR al directorio de despliegue
cp /mnt/win/dev/git/traz-comp-dnato/development/car_build/COREToolsApplication_1.0.0.car \
   /home/rodolfo/dev/wso2mi-4.3.0/repository/deployment/server/carbonapps/

# Esperar 10-15 segundos para que se despliegue
sleep 15

# Verificar logs de despliegue
tail -100 /home/rodolfo/dev/wso2mi-4.3.0/repository/logs/wso2carbon.log | grep -i "coredataservice\|toolsdatasource\|deploy\|error"
```

**Verificar que**:
- No haya errores de conexión a PostgreSQL
- El DataService se despliega correctamente
- No haya errores OSGi del driver PostgreSQL

## 📊 Verificación Final

Después de todas las acciones, verificar:

1. **DataService accesible**:
   ```bash
   curl -s http://localhost:8290/services/COREDataService?wsdl 2>&1 | head -20
   ```

2. **Sin errores en logs**:
   ```bash
   tail -200 /home/rodolfo/dev/wso2mi-4.3.0/repository/logs/wso2carbon.log | grep -i "error.*postgres\|error.*dataservice\|error.*coredataservice"
   ```

3. **Driver OSGi resuelto**:
   ```bash
   tail -200 /home/rodolfo/dev/wso2mi-4.3.0/repository/logs/wso2carbon.log | grep -i "javax.transaction.xa\|postgresql.*jdbc42"
   ```
   **Resultado esperado**: No debería haber errores de `javax.transaction.xa`

## 📝 Notas

- **Conectividad PostgreSQL**: Es responsabilidad del usuario según las premisas de trabajo autónomo (`doc/creacion-usuarios.md`)
- **Driver OSGi**: El problema se resuelve eliminando drivers de `dropins/` y manteniendo solo en `lib/`
- **Reinicio completo**: Es necesario después de modificar drivers para que WSO2 MI cargue los cambios

## 🔗 Referencias

- Hipótesis: `development/tests/fase-0.3-hipotesis-postgresql.md`
- Soluciones detalladas: `development/tests/fase-0.3-soluciones-postgresql.md`
- Premisas de trabajo: `doc/creacion-usuarios.md`



