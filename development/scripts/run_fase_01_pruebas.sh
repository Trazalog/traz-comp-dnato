#!/usr/bin/env bash
# Pruebas Fase 1 - Wrapper API crearUsuarioAPI (llamada directa a API)
# Ejecutar en el host donde corre WSO2 MI. Usa .env del repo para PostgreSQL/MySQL.
# Uso: ./run_fase_01_pruebas.sh [BASE_URL]
# Ejemplo: ./run_fase_01_pruebas.sh http://localhost:8290

set -e
BASE_URL="${1:-http://localhost:8290}"
SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
REPO_ROOT="$(cd "$SCRIPT_DIR/../.." && pwd)"
TIMESTAMP=$(date +%s)
RESULT_DIR="$SCRIPT_DIR/../tests"
mkdir -p "$RESULT_DIR"

# Cargar .env si existe (sin source para evitar aliases/funciones del shell)
if [ -f "$REPO_ROOT/.env" ]; then
  while IFS= read -r line || [ -n "$line" ]; do
    [[ "$line" =~ ^#.*$ ]] && continue
    [[ "$line" =~ ^[[:space:]]*$ ]] && continue
    if [[ "$line" =~ ^([A-Za-z_][A-Za-z0-9_]*)=(.*)$ ]]; then
      export "${BASH_REMATCH[1]}=${BASH_REMATCH[2]}"
    fi
  done < "$REPO_ROOT/.env"
fi

echo "=== Pruebas Fase 1 - API crearUsuarioAPI - $BASE_URL ==="
echo "Timestamp: $TIMESTAMP"
echo ""

USERNICK="test_f1_${TIMESTAMP}"
EMAIL="test_f1_${TIMESTAMP}@test.com"

# --- Prueba 1.1: Crear usuario (éxito) ---
echo "--- Prueba 1.1: POST /tools/core/usuario (éxito) ---"
RESP=$(curl -s -w "\nHTTP_CODE:%{http_code}" -m 45 -X POST "${BASE_URL}/tools/core/usuario" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d "{
  \"usuario\": {
    \"usernick\": \"${USERNICK}\",
    \"email\": \"${EMAIL}\",
    \"business\": \"1\",
    \"firstname\": \"Test\",
    \"lastname\": \"Fase01\",
    \"password\": \"password123\",
    \"role\": \"2\",
    \"status\": \"approved\",
    \"banned_users\": \"unban\",
    \"telefono\": \"1234567890\",
    \"dni\": \"12345678\",
    \"image_name\": \"\",
    \"image\": \"\"
  },
  \"bpmSession\": null
}" 2>&1) || true
BODY=$(echo "$RESP" | sed '/^HTTP_CODE:/d' | tr -d '\n')
CODE=$(echo "$RESP" | grep "HTTP_CODE:" | sed 's/HTTP_CODE://')
echo "HTTP_CODE: $CODE"
echo "BODY: ${BODY:0:400}..."
if [ "$CODE" = "200" ] || [ "$CODE" = "202" ]; then
  if echo "$BODY" | grep -q "resultado" && echo "$BODY" | grep -q "usr_id"; then
    echo "1.1: APROBADA"
    USR_ID=$(echo "$BODY" | grep -o '"usr_id":"[^"]*"' | head -1 | cut -d'"' -f4)
    echo "usr_id obtenido: $USR_ID"
  else
    echo "1.1: FALLO (falta resultado o usr_id)"
  fi
else
  echo "1.1: FALLO (HTTP $CODE)"
fi
echo ""

# --- Prueba 1.2: Verificar en PostgreSQL ---
echo "--- Prueba 1.2: Verificar en PostgreSQL (seg.users, seg.users_business) ---"
if [ -n "$postgres_password" ]; then
  PG_OUT=$(PGPASSWORD="$postgres_password" psql -h "${postgres_host:-10.142.0.13}" -p "${postgres_port:-5432}" -U "${postgres_user:-postgres}" -d "${postgres_database:-tools_prod_t}" -t -A -c "SELECT id, email, usernick FROM seg.users WHERE email = '$EMAIL';" 2>/dev/null) || true
  if [ -n "$PG_OUT" ]; then
    echo "1.2: APROBADA - Usuario en PostgreSQL: $PG_OUT"
  else
    echo "1.2: FALLO o sin acceso a PostgreSQL"
  fi
else
  echo "1.2: OMITIDA (falta postgres_password en .env)"
fi
echo ""

# --- Prueba 1.4: Verificar en MySQL AssetPlanner ---
echo "--- Prueba 1.4: Verificar en MySQL AssetPlanner (sisusers) ---"
if [ -n "$mariadb_password" ]; then
  MYSQL_OUT=$(mysql -h "${mariadb_host:-10.142.0.13}" -P "${mariadb_port:-3306}" -u "${mariadb_user:-rootremote}" -p"${mariadb_password}" "${mariadb_database:-assetv2}" -N -e "SELECT usrNick, usrName FROM sisusers WHERE usrNick = '$USERNICK';" 2>/dev/null) || true
  if [ -n "$MYSQL_OUT" ]; then
    echo "1.4: APROBADA - Usuario en AssetPlanner: $MYSQL_OUT"
  else
    echo "1.4: FALLO o sin acceso a MySQL"
  fi
else
  echo "1.4: OMITIDA (falta mariadb_password en .env)"
fi
echo ""

# --- Prueba 1.5: Email duplicado (debe fallar) ---
echo "--- Prueba 1.5: POST con email duplicado (debe retornar error) ---"
RESP2=$(curl -s -w "\nHTTP_CODE:%{http_code}" -m 45 -X POST "${BASE_URL}/tools/core/usuario" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d "{
  \"usuario\": {
    \"usernick\": \"${USERNICK}_dup\",
    \"email\": \"${EMAIL}\",
    \"business\": \"1\",
    \"firstname\": \"Test\",
    \"lastname\": \"Dup\",
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
CODE2=$(echo "$RESP2" | grep "HTTP_CODE:" | sed 's/HTTP_CODE://')
BODY2=$(echo "$RESP2" | sed '/^HTTP_CODE:/d' | tr -d '\n')
echo "HTTP_CODE: $CODE2"
if [ "$CODE2" != "200" ] && [ "$CODE2" != "202" ]; then
  if echo "$BODY2" | grep -qi "duplicado\|ya existe\|error"; then
    echo "1.5: APROBADA (error esperado por email duplicado)"
  else
    echo "1.5: APROBADA (HTTP $CODE2, no 200/202)"
  fi
else
  echo "1.5: FALLO (se creó usuario duplicado, no debería)"
fi
echo ""

echo "=== Resumen Fase 1 ==="
echo "1.1 (API éxito), 1.2 (PostgreSQL), 1.4 (MySQL), 1.5 (duplicado) - revisar arriba"
