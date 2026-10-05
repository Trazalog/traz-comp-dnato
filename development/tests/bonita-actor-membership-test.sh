#!/bin/bash
# Prueba API Bonita: ActorMember membership (rol + grupo)
# Equivalente semántico a POST /tools/bpm/actor/membership

set -e
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(cd "$SCRIPT_DIR/../.." && pwd)"
ENV_FILE="$PROJECT_ROOT/.env"
COOKIES_FILE="$SCRIPT_DIR/.bonita-cookies-memb-$$"
LOG_FILE="$SCRIPT_DIR/bonita-actor-membership-test.log"

if [ -f "$ENV_FILE" ]; then
  export $(grep -v '^#' "$ENV_FILE" | xargs)
fi

BONITA_URL="${BONITA_URL:-http://10.142.0.13:8080/bonita}"
BONITA_USER="${BONITA_ADMIN:-admin}"
BONITA_PASS="${BONITA_PASSWORD:-123traza}"

log() { echo "[$(date +%H:%M:%S)] $*" | tee -a "$LOG_FILE"; }
cleanup() { rm -f "$COOKIES_FILE"; }
trap cleanup EXIT

log "=== Prueba Bonita ActorMember (rol + grupo / membership) ==="
log "URL: $BONITA_URL"

LOGIN_RESP=$(curl -s -c "$COOKIES_FILE" -w "\n%{http_code}" -X POST "$BONITA_URL/loginservice" \
  -H "Content-Type: application/x-www-form-urlencoded" \
  -d "username=$BONITA_USER&password=$BONITA_PASS&redirect=false&redirectURL=")
HTTP_CODE=$(echo "$LOGIN_RESP" | tail -n1)
if [ "$HTTP_CODE" != "204" ] && [ "$HTTP_CODE" != "200" ]; then
  log "ERROR Login HTTP $HTTP_CODE"
  exit 1
fi
API_TOKEN=$(grep 'X-Bonita-API-Token' "$COOKIES_FILE" | awk '{print $NF}' | tail -1)
log "Login OK"

ACTOR_MEMBER_PATH="actorMember"

# Proceso + actor (primer proceso habilitado, primer actor)
PROC_JSON=$(curl -s -b "$COOKIES_FILE" "$BONITA_URL/API/bpm/process?p=0&c=1&f=activationState%3DENABLED")
PROCESS_ID=$(echo "$PROC_JSON" | python3 -c "import sys,json; a=json.load(sys.stdin); print(a[0]['id'] if isinstance(a,list) and a else '')" 2>/dev/null || echo "")
PROCESS_NAME=$(echo "$PROC_JSON" | python3 -c "import sys,json; a=json.load(sys.stdin); print(a[0].get('name','') if isinstance(a,list) and a else '')" 2>/dev/null || echo "")
log "Proceso: $PROCESS_NAME (id=$PROCESS_ID)"

ACTORS_JSON=$(curl -s -b "$COOKIES_FILE" "$BONITA_URL/API/bpm/actor?p=0&c=10&f=process_id%3D$PROCESS_ID")
ACTOR_ID=$(echo "$ACTORS_JSON" | python3 -c "import sys,json; a=json.load(sys.stdin); print(a[0]['id'] if isinstance(a,list) and a else '')" 2>/dev/null || echo "")
ACTOR_NAME=$(echo "$ACTORS_JSON" | python3 -c "import sys,json; a=json.load(sys.stdin); print(a[0].get('name','') if isinstance(a,list) and a else '')" 2>/dev/null || echo "")
log "Actor: $ACTOR_NAME (id=$ACTOR_ID)"

# Roles y grupos: pocas filas para mantener JSON pequeño
ROLES_JSON=$(curl -s -b "$COOKIES_FILE" "$BONITA_URL/API/identity/role?p=0&c=5&o=name%20ASC")
ROLE_ID=$(echo "$ROLES_JSON" | python3 -c "import sys,json; a=json.load(sys.stdin); print(a[0]['id'] if isinstance(a,list) and a else '')" 2>/dev/null || echo "")
ROLE_NAME=$(echo "$ROLES_JSON" | python3 -c "import sys,json; a=json.load(sys.stdin); print(a[0].get('name','') if isinstance(a,list) and a else '')" 2>/dev/null || echo "")
log "Rol elegido: $ROLE_NAME (id=$ROLE_ID)"

GROUPS_JSON=$(curl -s -b "$COOKIES_FILE" "$BONITA_URL/API/identity/group?p=0&c=5&o=name%20ASC")
GROUP_ID=$(echo "$GROUPS_JSON" | python3 -c "import sys,json; a=json.load(sys.stdin); print(a[0]['id'] if isinstance(a,list) and a else '')" 2>/dev/null || echo "")
GROUP_NAME=$(echo "$GROUPS_JSON" | python3 -c "import sys,json; a=json.load(sys.stdin); print(a[0].get('name','') if isinstance(a,list) and a else '')" 2>/dev/null || echo "")
log "Grupo elegido: $GROUP_NAME (id=$GROUP_ID)"

if [ -z "$ACTOR_ID" ] || [ -z "$ROLE_ID" ] || [ -z "$GROUP_ID" ]; then
  log "ERROR: faltan ids (actor=$ACTOR_ID role=$ROLE_ID group=$GROUP_ID)"
  exit 1
fi

log "POST membership: actor_id=$ACTOR_ID role_id=$ROLE_ID group_id=$GROUP_ID (path $ACTOR_MEMBER_PATH)"

ADD_RESP=$(curl -s -b "$COOKIES_FILE" -w "\n%{http_code}" -X POST "$BONITA_URL/API/bpm/$ACTOR_MEMBER_PATH" \
  -H "Content-Type: application/json" \
  -H "X-Bonita-API-Token: $API_TOKEN" \
  -d "{\"actor_id\":\"$ACTOR_ID\",\"role_id\":\"$ROLE_ID\",\"group_id\":\"$GROUP_ID\",\"user_id\":\"-1\"}")
ADD_HTTP=$(echo "$ADD_RESP" | tail -n1)
ADD_BODY=$(echo "$ADD_RESP" | sed '$d')

if [ "$ADD_HTTP" = "200" ] || [ "$ADD_HTTP" = "201" ]; then
  NEW_MEMBER_ID=$(echo "$ADD_BODY" | python3 -c "import sys,json; o=json.load(sys.stdin); print(o.get('id',''))" 2>/dev/null || echo "")
  log "OK: ActorMember (membership) creado id=$NEW_MEMBER_ID"
  log "DELETE limpieza..."
  DEL_RESP=$(curl -s -b "$COOKIES_FILE" -w "\n%{http_code}" -X DELETE "$BONITA_URL/API/bpm/$ACTOR_MEMBER_PATH/$NEW_MEMBER_ID" \
    -H "X-Bonita-API-Token: $API_TOKEN")
  DEL_HTTP=$(echo "$DEL_RESP" | tail -n1)
  log "DELETE HTTP $DEL_HTTP"
  log "=== Prueba OK (misma semántica que /tools/bpm/actor/membership) ==="
else
  log "POST HTTP $ADD_HTTP body: ${ADD_BODY:0:500}"
  exit 1
fi
