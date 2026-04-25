#!/bin/bash

# Script para probar diferentes escenarios
# Uso: ./test_scenarios.sh [WSO2_URL] [BPM_SESSION]

WSO2_URL="${1:-https://localhost:8243}"
BPM_SESSION="${2:-your_bpm_session_token_here}"

GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

echo -e "${BLUE}=== Test de Escenarios - API Usuario ===${NC}\n"

# Función helper para crear usuario
create_user() {
    local email=$1
    local username=$2
    local firstname=$3
    local lastname=$4
    local role=$5
    local business=$6
    
    PAYLOAD=$(cat <<EOF
{
  "bpmSession": "$BPM_SESSION",
  "usuario": {
    "firstname": "$firstname",
    "lastname": "$lastname",
    "email": "$email",
    "password": "Test123456",
    "role": "$role",
    "status": "active",
    "banned_users": "",
    "telefono": "+5491112345678",
    "dni": "12345678",
    "usernick": "$username",
    "depo_id": null,
    "image_name": null,
    "image": null,
    "business": "$business"
  }
}
EOF
)
    
    curl -s -w "\n%{http_code}" -X POST "${WSO2_URL}/tools/core/usuario" \
      -H "Content-Type: application/json" \
      -H "Accept: application/json" \
      -d "$PAYLOAD"
}

# Escenario 1: Usuario nuevo (debe funcionar)
echo -e "${YELLOW}Escenario 1: Usuario nuevo${NC}"
RANDOM_ID=$(date +%s)_scenario1
RESPONSE=$(create_user \
  "test${RANDOM_ID}@example.com" \
  "testuser${RANDOM_ID}" \
  "Test" \
  "User" \
  "subscriber" \
  "empresa_test")
HTTP_CODE=$(echo "$RESPONSE" | tail -n1)
if [ "$HTTP_CODE" -eq 200 ] || [ "$HTTP_CODE" -eq 201 ]; then
    echo -e "${GREEN}✓ Usuario creado exitosamente${NC}"
else
    echo -e "${RED}✗ Error: HTTP $HTTP_CODE${NC}"
fi
echo ""

# Escenario 2: Usuario duplicado (debe fallar)
echo -e "${YELLOW}Escenario 2: Usuario duplicado${NC}"
DUPLICATE_EMAIL="test${RANDOM_ID}@example.com"  # Reusar el email anterior
RESPONSE=$(create_user \
  "$DUPLICATE_EMAIL" \
  "testuser_dup" \
  "Test" \
  "Duplicate" \
  "subscriber" \
  "empresa_test")
HTTP_CODE=$(echo "$RESPONSE" | tail -n1)
BODY=$(echo "$RESPONSE" | sed '$d')
if [ "$HTTP_CODE" -eq 400 ] || [ "$HTTP_CODE" -eq 409 ]; then
    echo -e "${GREEN}✓ Correctamente rechazado (duplicado)${NC}"
elif echo "$BODY" | grep -q "duplicado\|ya existe"; then
    echo -e "${GREEN}✓ Correctamente rechazado (mensaje de duplicado)${NC}"
else
    echo -e "${RED}✗ No se detectó el duplicado (HTTP $HTTP_CODE)${NC}"
fi
echo ""

# Escenario 3: Usuario Admin
echo -e "${YELLOW}Escenario 3: Usuario con rol Admin${NC}"
RANDOM_ID=$(date +%s)_admin
RESPONSE=$(create_user \
  "admin${RANDOM_ID}@example.com" \
  "admin${RANDOM_ID}" \
  "Admin" \
  "User" \
  "admin" \
  "empresa_test")
HTTP_CODE=$(echo "$RESPONSE" | tail -n1)
if [ "$HTTP_CODE" -eq 200 ] || [ "$HTTP_CODE" -eq 201 ]; then
    echo -e "${GREEN}✓ Admin creado exitosamente${NC}"
else
    echo -e "${RED}✗ Error: HTTP $HTTP_CODE${NC}"
fi
echo ""

# Escenario 4: Email inválido
echo -e "${YELLOW}Escenario 4: Email inválido${NC}"
RESPONSE=$(create_user \
  "email_invalido" \
  "testuser_inv" \
  "Test" \
  "Invalid" \
  "subscriber" \
  "empresa_test")
HTTP_CODE=$(echo "$RESPONSE" | tail -n1)
if [ "$HTTP_CODE" -ge 400 ]; then
    echo -e "${GREEN}✓ Correctamente rechazado (email inválido)${NC}"
else
    echo -e "${YELLOW}? No se validó el email (HTTP $HTTP_CODE)${NC}"
fi
echo ""

# Escenario 5: Campos requeridos faltantes
echo -e "${YELLOW}Escenario 5: Payload incompleto${NC}"
INCOMPLETE_PAYLOAD='{"bpmSession":"'$BPM_SESSION'","usuario":{"email":"incomplete@test.com"}}'
RESPONSE=$(curl -s -w "\n%{http_code}" -X POST "${WSO2_URL}/tools/core/usuario" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d "$INCOMPLETE_PAYLOAD")
HTTP_CODE=$(echo "$RESPONSE" | tail -n1)
if [ "$HTTP_CODE" -ge 400 ]; then
    echo -e "${GREEN}✓ Correctamente rechazado (payload incompleto)${NC}"
else
    echo -e "${YELLOW}? No se validó el payload (HTTP $HTTP_CODE)${NC}"
fi
echo ""

echo -e "${BLUE}=== Fin de Escenarios ===${NC}"

