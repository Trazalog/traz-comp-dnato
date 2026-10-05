#!/bin/bash

# Script para crear múltiples usuarios de prueba
# Uso: ./test_create_usuario_multiple.sh [WSO2_URL] [BPM_SESSION] [CANTIDAD]

WSO2_URL="${1:-https://localhost:8243}"
BPM_SESSION="${2:-your_bpm_session_token_here}"
CANTIDAD="${3:-5}"

GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m'

echo -e "${YELLOW}=== Creando $CANTIDAD usuarios de prueba ===${NC}\n"

SUCCESS=0
FAILED=0

for i in $(seq 1 $CANTIDAD); do
    RANDOM_ID=$(date +%s)_$i
    FIRST_NAME="Test${RANDOM_ID}"
    LAST_NAME="User${RANDOM_ID}"
    EMAIL="test${RANDOM_ID}@example.com"
    USERNAME="testuser${RANDOM_ID}"
    PASSWORD="Test123456"
    BUSINESS="empresa_test_${RANDOM_ID}"
    
    PAYLOAD=$(cat <<EOF
{
  "bpmSession": "$BPM_SESSION",
  "usuario": {
    "firstname": "$FIRST_NAME",
    "lastname": "$LAST_NAME",
    "email": "$EMAIL",
    "password": "$PASSWORD",
    "role": "subscriber",
    "status": "active",
    "banned_users": "",
    "telefono": "+5491112345678",
    "dni": "12345678",
    "usernick": "$USERNAME",
    "depo_id": null,
    "image_name": null,
    "image": null,
    "business": "$BUSINESS"
  }
}
EOF
)
    
    echo -n "Usuario $i/$CANTIDAD ($EMAIL)... "
    
    HTTP_CODE=$(curl -s -o /dev/null -w "%{http_code}" -X POST "${WSO2_URL}/tools/core/usuario" \
      -H "Content-Type: application/json" \
      -H "Accept: application/json" \
      -d "$PAYLOAD")
    
    if [ "$HTTP_CODE" -eq 200 ] || [ "$HTTP_CODE" -eq 201 ]; then
        echo -e "${GREEN}✓${NC}"
        SUCCESS=$((SUCCESS + 1))
    else
        echo -e "${RED}✗ (HTTP $HTTP_CODE)${NC}"
        FAILED=$((FAILED + 1))
    fi
    
    sleep 1
done

echo ""
echo -e "${YELLOW}=== Resumen ===${NC}"
echo -e "${GREEN}Exitosos: $SUCCESS${NC}"
echo -e "${RED}Fallidos: $FAILED${NC}"

