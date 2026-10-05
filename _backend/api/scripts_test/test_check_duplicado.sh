#!/bin/bash

# Script para probar validación de duplicados
# Uso: ./test_check_duplicado.sh [WSO2_URL] [EMAIL]

WSO2_URL="${1:-https://localhost:8243}"
EMAIL="${2:-test@example.com}"

GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m'

echo -e "${YELLOW}=== Test Validación Duplicado ===${NC}\n"
echo "Email a verificar: $EMAIL"
echo ""

RESPONSE=$(curl -s -w "\n%{http_code}" -X GET "${WSO2_URL}/services/COREDataService/usuario/duplicado/${EMAIL}" \
  -H "Accept: application/json")

HTTP_CODE=$(echo "$RESPONSE" | tail -n1)
BODY=$(echo "$RESPONSE" | sed '$d')

echo -e "${YELLOW}HTTP Status:${NC} $HTTP_CODE"
echo -e "${YELLOW}Response:${NC}"
echo "$BODY" | jq '.' 2>/dev/null || echo "$BODY"
echo ""

EXISTE=$(echo "$BODY" | jq -r '.respuesta.existe' 2>/dev/null)

if [ "$EXISTE" = "true" ]; then
    echo -e "${RED}✗ El usuario YA EXISTE${NC}"
elif [ "$EXISTE" = "false" ]; then
    echo -e "${GREEN}✓ El usuario NO existe (puede crearse)${NC}"
else
    echo -e "${YELLOW}? No se pudo determinar${NC}"
fi

