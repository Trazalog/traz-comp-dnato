#!/bin/bash

# Script para desplegar COREToolsApplication CAR - Fase 0.3
# Ejecutar con: bash development/scripts/deploy_car_fase_0_3.sh

set -e

echo "=========================================="
echo "Despliegue CAR - Fase 0.3"
echo "=========================================="
echo ""

# Configuración
WSO2MI_HOME="/home/rodolfo/dev/wso2mi-4.3.0"
CAR_SOURCE="/mnt/win/dev/git/traz-comp-dnato/development/COREToolsApplication_1.0.0.car"
CAR_DEST="$WSO2MI_HOME/repository/deployment/server/carbonapps/COREToolsApplication_1.0.0.car"
DROPINS_DIR="$WSO2MI_HOME/dropins"

echo "Paso 1: Eliminando driver PostgreSQL de dropins/..."
rm -f "$DROPINS_DIR/postgresql_jdbc_1.0.0.jar" 2>/dev/null || true
rm -f "$DROPINS_DIR/postgresql-jdbc.jar" 2>/dev/null || true

if [ -f "$DROPINS_DIR/postgresql_jdbc_1.0.0.jar" ] || [ -f "$DROPINS_DIR/postgresql-jdbc.jar" ]; then
    echo "⚠️  ADVERTENCIA: Algunos drivers PostgreSQL todavía están en dropins/"
    ls -la "$DROPINS_DIR/postgresql*.jar" 2>/dev/null || true
else
    echo "✓ Drivers PostgreSQL eliminados de dropins/"
fi

echo ""
echo "Paso 2: Eliminando CAR existente (si existe)..."
if [ -f "$CAR_DEST" ]; then
    rm -f "$CAR_DEST"
    echo "✓ CAR existente eliminado"
    echo "  Esperando 10 segundos para undeploy..."
    sleep 10
else
    echo "✓ No hay CAR existente"
fi

echo ""
echo "Paso 3: Copiando CAR al directorio de despliegue..."
if [ ! -f "$CAR_SOURCE" ]; then
    echo "❌ ERROR: No se encuentra el CAR en: $CAR_SOURCE"
    exit 1
fi

cp "$CAR_SOURCE" "$CAR_DEST"
echo "✓ CAR copiado a: $CAR_DEST"

echo ""
echo "Paso 4: Esperando 20 segundos para que WSO2 MI detecte y despliegue el CAR..."
sleep 20

echo ""
echo "Paso 5: Verificando logs de despliegue..."
echo "----------------------------------------"
tail -200 "$WSO2MI_HOME/repository/logs/wso2carbon.log" | grep -iE "coredataservice|toolsdatasource|coretoolsapplication|deploy.*success|deploy.*error" | tail -30 || echo "No se encontraron logs relevantes"

echo ""
echo "Paso 6: Verificando errores de PostgreSQL..."
echo "----------------------------------------"
tail -300 "$WSO2MI_HOME/repository/logs/wso2carbon.log" | grep -iE "error.*coredataservice|error.*toolsdatasource|error.*postgres|connection.*failed|socket.*timeout" | tail -15 || echo "✓ No se encontraron errores de PostgreSQL"

echo ""
echo "Paso 7: Verificando errores OSGi del driver..."
echo "----------------------------------------"
tail -300 "$WSO2MI_HOME/repository/logs/wso2carbon.log" | grep -iE "javax.transaction.xa|postgresql.*jdbc42.*error" | tail -5 || echo "✓ No se encontraron errores OSGi del driver"

echo ""
echo "Paso 8: Verificando que el DataService está accesible..."
echo "----------------------------------------"
curl -s http://localhost:8290/services/COREDataService?wsdl 2>&1 | head -20 || echo "⚠️  El DataService no está accesible"

echo ""
echo "=========================================="
echo "Despliegue completado"
echo "=========================================="
echo ""
echo "Verifica los logs arriba para confirmar el despliegue exitoso."
echo "Si hay errores, revisa: $WSO2MI_HOME/repository/logs/wso2carbon.log"



