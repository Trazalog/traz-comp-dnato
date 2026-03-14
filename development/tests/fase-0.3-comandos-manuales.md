# Comandos Manuales para Ejecutar - Fase 0.3

## ⚠️ Problema Técnico

El shell no está disponible en este momento (`spawn /usr/bin/bash ENOENT`), por lo que no puedo ejecutar comandos directamente.

## 🚀 Comandos que Debes Ejecutar Manualmente

### Paso 1: Eliminar Driver PostgreSQL de `dropins/`

```bash
rm /home/rodolfo/dev/wso2mi-4.3.0/dropins/postgresql_jdbc_1.0.0.jar
```

**Verificar**:
```bash
ls -la /home/rodolfo/dev/wso2mi-4.3.0/dropins/postgresql*.jar
# Debería mostrar: "No such file or directory"
```

### Paso 2: Copiar CAR al Directorio de Despliegue

```bash
cp /mnt/win/dev/git/traz-comp-dnato/development/COREToolsApplication_1.0.0.car \
   /home/rodolfo/dev/wso2mi-4.3.0/repository/deployment/server/carbonapps/
```

**Verificar**:
```bash
ls -lh /home/rodolfo/dev/wso2mi-4.3.0/repository/deployment/server/carbonapps/COREToolsApplication_1.0.0.car
```

### Paso 3: Esperar y Verificar Despliegue

```bash
# Esperar 20 segundos
sleep 20

# Verificar logs
tail -200 /home/rodolfo/dev/wso2mi-4.3.0/repository/logs/wso2carbon.log | grep -iE "coredataservice|toolsdatasource|coretoolsapplication|deploy.*success|deploy.*error" | tail -30
```

### Paso 4: Verificar DataService

```bash
curl -s http://localhost:8290/services/COREDataService?wsdl 2>&1 | head -20
```

## 📋 O Usar el Script Automatizado

**Alternativa más fácil**: Ejecutar el script que creé:

```bash
cd /mnt/win/dev/git/traz-comp-dnato
bash development/scripts/deploy_car_fase_0_3.sh
```

El script hace todo automáticamente y muestra los resultados.

## 📊 Después de Ejecutar

Una vez que ejecutes los comandos (o el script), comparte:
1. La salida de los comandos
2. Los logs de despliegue
3. El resultado de la verificación del DataService

Con esa información podré:
- ✅ Analizar si el despliegue fue exitoso
- ✅ Identificar cualquier error restante
- ✅ Continuar con las pruebas de Fase 0.3



