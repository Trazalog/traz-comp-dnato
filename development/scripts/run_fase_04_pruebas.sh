#!/usr/bin/env bash
# Pruebas Fase 0.4 - API envía imagen a PostgreSQL
# Ejecutar en el host donde corre WSO2 MI (o donde se pueda alcanzar BASE_URL).
# Uso: ./run_fase_04_pruebas.sh [BASE_URL]
# Ejemplo: ./run_fase_04_pruebas.sh http://localhost:8290

set -e
BASE_URL="${1:-http://localhost:8290}"
TIMESTAMP=$(date +%s)
RESULT_DIR="$(dirname "$0")/../tests"
mkdir -p "$RESULT_DIR"

echo "=== Pruebas Fase 0.4 - $BASE_URL ==="
echo "Timestamp: $TIMESTAMP"
echo ""

# Prueba 0.4.1 - Con imagen
EMAIL_IMG="test_api_04_${TIMESTAMP}@test.com"
echo "--- Prueba 0.4.1: POST /tools/core/usuario CON imagen ---"
RESP_041=$(curl -s -w "\nHTTP_CODE:%{http_code}" -m 45 -X POST "${BASE_URL}/tools/core/usuario" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d "{
  \"usuario\": {
    \"usernick\": \"test_api_04_${TIMESTAMP}\",
    \"email\": \"${EMAIL_IMG}\",
    \"business\": \"1\",
    \"firstname\": \"Test\",
    \"lastname\": \"Fase04\",
    \"password\": \"password123\",
    \"role\": \"2\",
    \"status\": \"approved\",
    \"banned_users\": \"unban\",
    \"telefono\": \"1234567890\",
    \"dni\": \"12345678\",
    \"image_name\": \"foto.jpg\",
    \"image\": \"iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==\"
  },
  \"bpmSession\": null
}" 2>&1) || true
BODY_041=$(echo "$RESP_041" | sed '/^HTTP_CODE:/d' | tr -d '\n')
CODE_041=$(echo "$RESP_041" | grep "HTTP_CODE:" | sed 's/HTTP_CODE://')
echo "HTTP_CODE: $CODE_041"
echo "BODY: $BODY_041"
echo "EMAIL_USADO: $EMAIL_IMG"
if [ "$CODE_041" = "200" ] || [ "$CODE_041" = "202" ]; then
  if echo "$BODY_041" | grep -q "resultado"; then
    echo "0.4.1: APROBADA (HTTP $CODE_041 y respuesta con resultado)"
  else
    echo "0.4.1: FALLO o PENDIENTE (revisar BODY/CODE)"
  fi
else
  echo "0.4.1: FALLO o PENDIENTE (revisar BODY/CODE)"
fi
echo ""

# Pequeña pausa entre pruebas para evitar reutilización de conexión al mismo servidor
sleep 2

# Prueba 0.4.3 - Sin imagen
EMAIL_NOIMG="test_api_noimg_04_${TIMESTAMP}@test.com"
echo "--- Prueba 0.4.3: POST /tools/core/usuario SIN imagen ---"
RESP_043=$(curl -s -w "\nHTTP_CODE:%{http_code}" -m 45 -X POST "${BASE_URL}/tools/core/usuario" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d "{
  \"usuario\": {
    \"usernick\": \"test_api_noimg_04_${TIMESTAMP}\",
    \"email\": \"${EMAIL_NOIMG}\",
    \"business\": \"1\",
    \"firstname\": \"Test\",
    \"lastname\": \"SinImagen\",
    \"password\": \"password123\",
    \"role\": \"2\",
    \"status\": \"approved\",
    \"banned_users\": \"unban\",
    \"telefono\": \"\",
    \"dni\": \"\",
    \"image_name\": \"\",
    \"image\": \"\"
  },
  \"bpmSession\": null
}" 2>&1) || true
CODE_043=$(echo "$RESP_043" | grep "HTTP_CODE:" | sed 's/HTTP_CODE://')
BODY_043=$(echo "$RESP_043" | sed '/^HTTP_CODE:/d' | tr -d '\n')
echo "HTTP_CODE: $CODE_043"
echo "BODY: ${BODY_043:0:300}..."
if [ "$CODE_043" = "200" ] || [ "$CODE_043" = "202" ]; then
  if echo "$BODY_043" | grep -q "resultado"; then
    echo "0.4.3: APROBADA"
  else
    echo "0.4.3: FALLO o PENDIENTE"
  fi
else
  echo "0.4.3: FALLO o PENDIENTE"
fi
echo ""

echo "=== Resumen ==="
echo "0.4.1 (con imagen): CODE=$CODE_041"
echo "0.4.3 (sin imagen): CODE=$CODE_043"
echo "Para 0.4.2 (verificar en PostgreSQL) ejecutar manualmente:"
echo "  PGPASSWORD='...' psql -h 10.142.0.13 -p 5432 -U postgres -d tools_prod_t -c \"SELECT id, email, usernick, image_name, LENGTH(image) as image_size FROM seg.users WHERE email = '${EMAIL_IMG}';\""
echo ""
echo "Resultados guardables en: $RESULT_DIR/fase-0.4-pruebas-resultados.md"
