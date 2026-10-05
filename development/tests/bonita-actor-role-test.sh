#!/bin/bash
# Prueba de API REST Bonita: agregar y quitar rol a actor
# Valida contra instancia de desarrollo (Bonita 7.6)
# Usa credenciales de .env

set -e
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(cd "$SCRIPT_DIR/../.." && pwd)"
ENV_FILE="$PROJECT_ROOT/.env"
COOKIES_FILE="$SCRIPT_DIR/.bonita-cookies-$$"
LOG_FILE="$SCRIPT_DIR/bonita-actor-role-test.log"

# Cargar .env
if [ -f "$ENV_FILE" ]; then
  export $(grep -v '^#' "$ENV_FILE" | xargs)
fi

BONITA_URL="${BONITA_URL:-http://10.142.0.13:8080/bonita}"
BONITA_USER="${BONITA_ADMIN:-admin}"
BONITA_PASS="${BONITA_PASSWORD:-123traza}"

log() { echo "[$(date +%H:%M:%S)] $*" | tee -a "$LOG_FILE"; }
cleanup() { rm -f "$COOKIES_FILE"; }
trap cleanup EXIT

log "=== Prueba Bonita Actor/Role API ==="
log "URL: $BONITA_URL"
log "User: $BONITA_USER"

# 1. Login
log "1. Login..."
LOGIN_RESP=$(curl -s -c "$COOKIES_FILE" -w "\n%{http_code}" -X POST "$BONITA_URL/loginservice" \
  -H "Content-Type: application/x-www-form-urlencoded" \
  -d "username=$BONITA_USER&password=$BONITA_PASS&redirect=false&redirectURL=")
HTTP_CODE=$(echo "$LOGIN_RESP" | tail -n1)
BODY=$(echo "$LOGIN_RESP" | sed '$d')

if [ "$HTTP_CODE" != "204" ] && [ "$HTTP_CODE" != "200" ]; then
  log "ERROR: Login falló (HTTP $HTTP_CODE). Respuesta: $BODY"
  exit 1
fi

# Extraer X-Bonita-API-Token de cookies
API_TOKEN=$(grep -oP 'X-Bonita-API-Token\t\K[^\t]+' "$COOKIES_FILE" 2>/dev/null || true)
if [ -z "$API_TOKEN" ]; then
  API_TOKEN=$(grep 'X-Bonita-API-Token' "$COOKIES_FILE" | awk '{print $NF}')
fi
log "Login OK. Token: ${API_TOKEN:0:20}..."

# 2. Listar procesos
log "2. Listando procesos..."
PROCESSES=$(curl -s -b "$COOKIES_FILE" "$BONITA_URL/API/bpm/process?p=0&c=10")
if echo "$PROCESSES" | grep -q '"id"'; then
  PROCESS_ID=$(echo "$PROCESSES" | grep -oP '"id"\s*:\s*"\K[^"]+' | head -1)
  PROCESS_NAME=$(echo "$PROCESSES" | grep -oP '"name"\s*:\s*"\K[^"]+' | head -1)
  log "Proceso: $PROCESS_NAME (id=$PROCESS_ID)"
else
  log "ERROR: No se encontraron procesos. Respuesta: $PROCESSES"
  exit 1
fi

# 3. Listar actores del proceso
log "3. Listando actores del proceso..."
ACTORS=$(curl -s -b "$COOKIES_FILE" "$BONITA_URL/API/bpm/actor?p=0&c=100&f=process_id%3D$PROCESS_ID")
if echo "$ACTORS" | grep -q '"id"'; then
  ACTOR_ID=$(echo "$ACTORS" | grep -oP '"id"\s*:\s*"\K[^"]+' | head -1)
  ACTOR_NAME=$(echo "$ACTORS" | grep -oP '"name"\s*:\s*"\K[^"]+' | head -1)
  log "Actor: $ACTOR_NAME (id=$ACTOR_ID)"
else
  log "ERROR: No se encontraron actores. Respuesta: $ACTORS"
  exit 1
fi

# 4. Listar roles
log "4. Listando roles..."
ROLES=$(curl -s -b "$COOKIES_FILE" "$BONITA_URL/API/identity/role?p=0&c=100")
if echo "$ROLES" | grep -q '"id"'; then
  ROLE_ID=$(echo "$ROLES" | grep -oP '"id"\s*:\s*"\K[^"]+' | head -1)
  ROLE_NAME=$(echo "$ROLES" | grep -oP '"name"\s*:\s*"\K[^"]+' | head -1)
  log "Rol: $ROLE_NAME (id=$ROLE_ID)"
else
  log "ERROR: No se encontraron roles. Respuesta: $ROLES"
  exit 1
fi

# 5. Listar miembros actuales del actor
log "5. Miembros actuales del actor $ACTOR_ID..."
MEMBERS_BEFORE=$(curl -s -b "$COOKIES_FILE" "$BONITA_URL/API/bpm/actorMemberEntry?p=0&c=100&f=actor_id%3D$ACTOR_ID")
log "Antes: $MEMBERS_BEFORE"

# Probar path alternativo (Bonita 7.6 puede usar actorMember)
ACTOR_MEMBER_PATH="actorMemberEntry"
MEMBERS_ALT=$(curl -s -b "$COOKIES_FILE" "$BONITA_URL/API/bpm/actorMember?p=0&c=100&f=actor_id%3D$ACTOR_ID" 2>/dev/null || echo "[]")
if echo "$MEMBERS_ALT" | grep -q '"id"'; then
  ACTOR_MEMBER_PATH="actorMember"
  log "Usando path: actorMember (7.6)"
  MEMBERS_BEFORE="$MEMBERS_ALT"
else
  log "Usando path: actorMemberEntry (7.11)"
fi

# 6. Agregar rol al actor (POST)
log "6. Agregando rol $ROLE_ID al actor $ACTOR_ID..."
ADD_RESP=$(curl -s -b "$COOKIES_FILE" -w "\n%{http_code}" -X POST "$BONITA_URL/API/bpm/$ACTOR_MEMBER_PATH" \
  -H "Content-Type: application/json" \
  -H "X-Bonita-API-Token: $API_TOKEN" \
  -d "{\"actor_id\":\"$ACTOR_ID\",\"role_id\":\"$ROLE_ID\",\"group_id\":\"-1\",\"user_id\":\"-1\"}")
ADD_HTTP=$(echo "$ADD_RESP" | tail -n1)
ADD_BODY=$(echo "$ADD_RESP" | sed '$d')

if [ "$ADD_HTTP" = "200" ] || [ "$ADD_HTTP" = "201" ]; then
  NEW_MEMBER_ID=$(echo "$ADD_BODY" | grep -oP '"id"\s*:\s*"?\K[^",]+' | head -1)
  log "OK: Rol agregado. ActorMember id=$NEW_MEMBER_ID"
else
  log "ERROR: POST falló (HTTP $ADD_HTTP). Respuesta: $ADD_BODY"
  log "Intentando con Content-Type alternativo..."
  ADD_RESP2=$(curl -s -b "$COOKIES_FILE" -w "\n%{http_code}" -X POST "$BONITA_URL/API/bpm/$ACTOR_MEMBER_PATH" \
    -H "Content-Type: application/json" \
    -H "X-Bonita-API-Token: $API_TOKEN" \
    -d '{"actor_id":"'"$ACTOR_ID"'","role_id":"'"$ROLE_ID"'","group_id":"-1","user_id":"-1"}')
  ADD_HTTP2=$(echo "$ADD_RESP2" | tail -n1)
  ADD_BODY2=$(echo "$ADD_RESP2" | sed '$d')
  if [ "$ADD_HTTP2" = "200" ] || [ "$ADD_HTTP2" = "201" ]; then
    NEW_MEMBER_ID=$(echo "$ADD_BODY2" | grep -oP '"id"\s*:\s*"?\K[^",]+' | head -1)
    log "OK (intento 2): Rol agregado. ActorMember id=$NEW_MEMBER_ID"
  else
    log "ERROR: POST falló en ambos intentos. Última respuesta: $ADD_BODY2"
    exit 1
  fi
fi

# 7. Verificar que el miembro existe
log "7. Verificando miembros después de agregar..."
MEMBERS_AFTER=$(curl -s -b "$COOKIES_FILE" "$BONITA_URL/API/bpm/$ACTOR_MEMBER_PATH?p=0&c=100&f=actor_id%3D$ACTOR_ID")
log "Después: $MEMBERS_AFTER"

# 8. Quitar el rol (DELETE)
log "8. Quitando rol (DELETE ActorMember id=$NEW_MEMBER_ID)..."
DEL_RESP=$(curl -s -b "$COOKIES_FILE" -w "\n%{http_code}" -X DELETE \
  -H "X-Bonita-API-Token: $API_TOKEN" \
  "$BONITA_URL/API/bpm/$ACTOR_MEMBER_PATH/$NEW_MEMBER_ID")
DEL_HTTP=$(echo "$DEL_RESP" | tail -n1)
DEL_BODY=$(echo "$DEL_RESP" | sed '$d')

if [ "$DEL_HTTP" = "200" ] || [ "$DEL_HTTP" = "204" ]; then
  log "OK: Rol quitado correctamente"
else
  log "ERROR: DELETE falló (HTTP $DEL_HTTP). Respuesta: $DEL_BODY"
  exit 1
fi

# 9. Verificar que se quitó
log "9. Verificando miembros después de quitar..."
MEMBERS_FINAL=$(curl -s -b "$COOKIES_FILE" "$BONITA_URL/API/bpm/$ACTOR_MEMBER_PATH?p=0&c=100&f=actor_id%3D$ACTOR_ID")
log "Final: $MEMBERS_FINAL"

log "=== Prueba completada exitosamente ==="
log "Log guardado en: $LOG_FILE"
