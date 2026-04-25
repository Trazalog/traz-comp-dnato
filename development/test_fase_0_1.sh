#!/bin/bash

# Script de Prueba para Fase 0.1: Hashear Password MD5 en DataService para AssetPlanner
# Este script valida que el password se hashee correctamente en MD5

set -e

# Colores para output
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Configuración
DATASERVICE_URL="http://10.142.0.13:8280/services/COREDataService/assetuser/add"
TIMESTAMP=$(date +%s)
TEST_NICK="test_md5_${TIMESTAMP}"
TEST_PASSWORD="password123"
EXPECTED_MD5="482c811da5d5b4bc6d497ffa98491e38"  # MD5 de "password123"

echo "=========================================="
echo "FASE 0.1: Prueba de Hash MD5 en AssetPlanner"
echo "=========================================="
echo ""

# Función para calcular MD5
calculate_md5() {
    echo -n "$1" | md5sum | cut -d' ' -f1
}

# Función para verificar MD5
verify_md5() {
    local calculated=$(calculate_md5 "$TEST_PASSWORD")
    if [ "$calculated" == "$EXPECTED_MD5" ]; then
        echo -e "${GREEN}✓ MD5 calculado correctamente: $calculated${NC}"
        return 0
    else
        echo -e "${RED}✗ MD5 incorrecto. Esperado: $EXPECTED_MD5, Obtenido: $calculated${NC}"
        return 1
    fi
}

# Prueba 0.1.1: Prueba Directa del DataService
echo "----------------------------------------"
echo "Prueba 0.1.1: Prueba Directa del DataService"
echo "----------------------------------------"
echo "Creando usuario: $TEST_NICK"
echo "Password original: $TEST_PASSWORD"
echo "MD5 esperado: $EXPECTED_MD5"
echo ""

# Verificar MD5 localmente primero
echo "Verificando cálculo MD5 local..."
verify_md5
if [ $? -ne 0 ]; then
    echo -e "${RED}ERROR: El cálculo MD5 local falló${NC}"
    exit 1
fi

# Llamar al endpoint
echo ""
echo "Llamando al endpoint: $DATASERVICE_URL"
RESPONSE=$(curl -s -w "\n%{http_code}" -X POST "$DATASERVICE_URL" \
    -H "Content-Type: application/json" \
    -d "{\"_post_assetuser_add\":{\"nick\":\"$TEST_NICK\",\"name\":\"Test\",\"lastName\":\"MD5\",\"pass\":\"$TEST_PASSWORD\",\"image\":\"\"}}")

HTTP_CODE=$(echo "$RESPONSE" | tail -n1)
BODY=$(echo "$RESPONSE" | sed '$d')

echo "HTTP Status Code: $HTTP_CODE"
echo "Response Body: $BODY"
echo ""

if [ "$HTTP_CODE" == "202" ] || [ "$HTTP_CODE" == "200" ]; then
    echo -e "${GREEN}✓ Request exitoso (HTTP $HTTP_CODE)${NC}"
else
    echo -e "${RED}✗ Request falló (HTTP $HTTP_CODE)${NC}"
    exit 1
fi

echo ""
echo "=========================================="
echo "NOTA: Para completar la validación, necesitas:"
echo "1. Conectarte a MySQL AssetPlanner"
echo "2. Ejecutar: SELECT usrNick, usrPassword, LENGTH(usrPassword) as pass_length"
echo "   FROM sisusers WHERE usrNick = '$TEST_NICK';"
echo "3. Verificar que:"
echo "   - usrPassword tiene 32 caracteres (MD5 hash)"
echo "   - usrPassword es hexadecimal (0-9, a-f)"
echo "   - usrPassword = '$EXPECTED_MD5'"
echo "4. Probar login en AssetPlanner con password original: $TEST_PASSWORD"
echo "=========================================="
echo ""

# Resumen
echo "----------------------------------------"
echo "RESUMEN DE PRUEBA 0.1.1"
echo "----------------------------------------"
echo -e "${GREEN}✓ Endpoint respondió correctamente${NC}"
echo -e "${GREEN}✓ MD5 calculado localmente: $EXPECTED_MD5${NC}"
echo -e "${YELLOW}⚠ Validación en base de datos requiere acceso a MySQL AssetPlanner${NC}"
echo ""
echo "Usuario de prueba creado: $TEST_NICK"
echo "Password original: $TEST_PASSWORD"
echo "MD5 esperado en BD: $EXPECTED_MD5"
echo ""






