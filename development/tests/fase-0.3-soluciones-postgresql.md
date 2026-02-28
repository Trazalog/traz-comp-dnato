# Soluciones para Problemas de PostgreSQL en WSO2 MI - Fase 0.3

## 📋 Resumen del Problema

El DataService `COREDataService` no se despliega porque:
1. **WSO2 valida la conexión a PostgreSQL durante el despliegue** y falla con `SocketTimeoutException: Connect timed out`
2. **El driver PostgreSQL tiene problemas de resolución OSGi**: `Unresolved requirement: Import-Package: javax.transaction.xa`

## 🔧 Soluciones Propuestas

### Solución 1: Verificar y Asegurar Conectividad a PostgreSQL

**Problema**: PostgreSQL no está accesible desde WSO2 durante el despliegue.

**Pasos**:

1. **Verificar que PostgreSQL esté corriendo y accesible**:
   ```bash
   # Desde el servidor WSO2, probar conexión
   PGPASSWORD='!Password00' psql -h 10.142.0.13 -p 5432 -U postgres -d tools_prod_t -c "SELECT 1;"
   ```

2. **Verificar VPN y conectividad de red**:
   ```bash
   # Verificar que el servidor PostgreSQL es alcanzable
   ping 10.142.0.13
   telnet 10.142.0.13 5432
   ```

3. **Si PostgreSQL no está accesible**:
   - Verificar que la VPN esté conectada
   - Verificar que el servidor PostgreSQL esté corriendo
   - Verificar que no haya firewall bloqueando el puerto 5432
   - **Nota**: Según las premisas de trabajo autónomo, el usuario debe asegurar la conectividad

### Solución 2: Resolver Problema del Driver PostgreSQL OSGi

**Problema**: El driver PostgreSQL no se resuelve correctamente en OSGi porque falta `javax.transaction.xa`.

**Opciones**:

#### Opción A: Usar Driver en `lib/` en lugar de `dropins/` (RECOMENDADO)

Los drivers JDBC en WSO2 MI funcionan mejor cuando están en `lib/` en lugar de `dropins/` porque:
- `lib/` carga los JARs directamente en el classpath del sistema
- `dropins/` intenta cargarlos como bundles OSGi, lo que requiere dependencias adicionales

**Pasos**:

1. **Verificar ubicación actual del driver**:
   ```bash
   ls -la /home/rodolfo/dev/wso2mi-4.3.0/lib/postgresql*.jar
   ls -la /home/rodolfo/dev/wso2mi-4.3.0/dropins/postgresql*.jar
   ```

2. **Asegurar que el driver esté SOLO en `lib/`**:
   ```bash
   # Si está en dropins, moverlo a lib (o eliminarlo de dropins)
   # El driver ya está en lib/, verificar que no haya conflicto
   ```

3. **Reiniciar WSO2 MI completamente** para que cargue el driver desde `lib/`:
   ```bash
   # Detener WSO2 MI
   pkill -f "micro-integrator"
   
   # Iniciar WSO2 MI
   cd /home/rodolfo/dev/wso2mi-4.3.0
   nohup bin/micro-integrator.sh > /dev/null 2>&1 &
   
   # Esperar 30-40 segundos para que inicie completamente
   ```

#### Opción B: Usar Driver PostgreSQL OSGi Bundle

Si el driver en `lib/` no funciona, usar un driver PostgreSQL que sea un bundle OSGi válido.

**Pasos**:

1. **Descargar driver PostgreSQL OSGi bundle** (si está disponible)
2. **Reemplazar el driver actual** en `lib/` o `dropins/`
3. **Reiniciar WSO2 MI**

#### Opción C: Agregar Dependencia `javax.transaction.xa`

Si es necesario mantener el driver en `dropins/`, agregar el paquete `javax.transaction.xa` al entorno OSGi.

**Nota**: Esta opción es más compleja y requiere modificar la configuración OSGi de WSO2.

### Solución 3: Deshabilitar Validación de Conexión durante Despliegue (NO RECOMENDADO)

**Advertencia**: Esta solución puede causar problemas si el datasource no está configurado correctamente.

**Pasos**:

1. **Modificar configuración de WSO2** para deshabilitar validación de conexión
2. **Documentar el cambio** para futuras referencias

**Nota**: Esta solución no se recomienda porque puede ocultar problemas de configuración reales.

## 📝 Plan de Acción Recomendado

### Paso 1: Verificar Conectividad (PRIORITARIO) ⚠️
**Estado**: Pendiente - Requiere acción del usuario

1. ⏳ Verificar que PostgreSQL esté accesible desde el servidor WSO2
   - **Comando**: `PGPASSWORD='!Password00' psql -h 10.142.0.13 -p 5432 -U postgres -d tools_prod_t -c "SELECT 1;"`
   - **Resultado esperado**: Debe retornar `1` si PostgreSQL está accesible
   - **Si falla**: El usuario debe verificar VPN y estado del servidor PostgreSQL (según premisas de trabajo autónomo)

2. ⏳ Si no está accesible, informar al usuario (según premisas de trabajo autónomo)

### Paso 2: Resolver Driver OSGi
**Estado**: En progreso

1. ✅ Verificar ubicación actual del driver PostgreSQL
   - **Driver encontrado en**: 
     - `/home/rodolfo/dev/wso2mi-4.3.0/lib/postgresql-jdbc.jar` ✓
     - `/home/rodolfo/dev/wso2mi-4.3.0/dropins/postgresql-jdbc.jar` ⚠️ (puede causar conflicto)
     - `/home/rodolfo/dev/wso2mi-4.3.0/dropins/postgresql_jdbc_1.0.0.jar` ⚠️ (puede causar conflicto)

2. ⏳ **ACCIÓN REQUERIDA**: Eliminar drivers de `dropins/` para evitar conflictos OSGi
   ```bash
   # Eliminar drivers PostgreSQL de dropins/ (mantener solo en lib/)
   rm /home/rodolfo/dev/wso2mi-4.3.0/dropins/postgresql*.jar
   ```

3. ⏳ Reiniciar WSO2 MI completamente después de eliminar drivers de `dropins/`

### Paso 3: Reintentar Despliegue
**Estado**: Pendiente (después de Pasos 1 y 2)

1. ⏳ Desplegar el CAR `COREToolsApplication_1.0.0.car`
2. ⏳ Verificar en los logs que el DataService se despliega correctamente
3. ⏳ Verificar que no haya errores de conexión

### Paso 4: Ejecutar Pruebas de Fase 0.3
**Estado**: Pendiente (después de Paso 3)

1. ⏳ Prueba 0.3.1: Prueba directa del DataService con imagen
2. ⏳ Prueba 0.3.2: Verificar en base de datos
3. ⏳ Prueba 0.3.3: Prueba sin imagen
4. ⏳ Prueba 0.3.4: Prueba de validación de parámetros

## 🔍 Verificaciones Post-Solución

Después de aplicar las soluciones, verificar:

1. **Logs de WSO2 MI**:
   ```bash
   tail -f /home/rodolfo/dev/wso2mi-4.3.0/repository/logs/wso2carbon.log | grep -i "postgres\|dataservice\|coredataservice"
   ```

2. **Estado del DataService**:
   ```bash
   curl -s http://localhost:8290/services/COREDataService?wsdl 2>&1 | head -20
   ```

3. **Estado del datasource**:
   - Verificar en los logs que el datasource `ToolsDataSource` se despliega correctamente
   - Verificar que no haya errores de conexión

## 📚 Referencias

- Hipótesis documentada: `development/tests/fase-0.3-hipotesis-postgresql.md`
- Premisas de trabajo autónomo: `doc/creacion-usuarios.md` (sección "Premisas para Trabajo Autónomo")
- Credenciales: `development/credentials.txt`

