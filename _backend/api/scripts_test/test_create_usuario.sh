#!/bin/bash

# Script de prueba para crear usuario con datos aleatorios
# Uso: ./test_create_usuario.sh [WSO2_URL] [BPM_SESSION]

# Configuración
WSO2_URL="${1:-https://localhost:8243}"
BPM_SESSION="${2:-your_bpm_session_token_here}"

# Colores para output
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo -e "${YELLOW}=== Test API Crear Usuario ===${NC}\n"

# Generar datos aleatorios
RANDOM_ID=$(date +%s)
FIRST_NAME="Test${RANDOM_ID}"
LAST_NAME="User${RANDOM_ID}"
EMAIL="test${RANDOM_ID}@example.com"
USERNAME="testuser${RANDOM_ID}"
PASSWORD="Test123456"
BUSINESS="empresa_test_${RANDOM_ID}"

echo -e "${GREEN}Datos generados:${NC}"
echo "  Email: $EMAIL"
echo "  Username: $USERNAME"
echo "  Password: $PASSWORD"
echo "  Business: $BUSINESS"
echo ""

# Payload JSON
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

echo -e "${YELLOW}Ejecutando POST /usuario...${NC}\n"

# Ejecutar curl
RESPONSE=$(curl -s -w "\n%{http_code}" -X POST "${WSO2_URL}/tools/core/usuario" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d "$PAYLOAD")

# Separar respuesta y código HTTP
HTTP_CODE=$(echo "$RESPONSE" | tail -n1)
BODY=$(echo "$RESPONSE" | sed '$d')

echo -e "${YELLOW}HTTP Status:${NC} $HTTP_CODE"
echo -e "${YELLOW}Response:${NC}"
echo "$BODY" | jq '.' 2>/dev/null || echo "$BODY"
echo ""

# Validar respuesta
if [ "$HTTP_CODE" -eq 200 ] || [ "$HTTP_CODE" -eq 201 ]; then
    echo -e "${GREEN}✓ Usuario creado exitosamente${NC}"
    
    # Extraer user_id si está disponible
    USER_ID=$(echo "$BODY" | jq -r '.respuesta.usr_id' 2>/dev/null)
    if [ "$USER_ID" != "null" ] && [ -n "$USER_ID" ]; then
        echo -e "${GREEN}  User ID: $USER_ID${NC}"
    fi
else
    echo -e "${RED}✗ Error al crear usuario${NC}"
    exit 1
fi

echo ""
echo -e "${YELLOW}=== Datos para verificación ===${NC}"
echo "Email: $EMAIL"
echo "Username: $USERNAME"
echo "Password: $PASSWORD"
echo "Business: $BUSINESS"

