#!/usr/bin/env bash
#
# Repack ToolsAPIProject_1.0.0.car: descomprime el CAR, copia artefactos desde
# ToolsAPIProject_1.0.0/ (fuente en repo) y vuelve a generar el .car y opcionalmente
# lo copia al despliegue de WSO2 Micro Integrator.
#
# Uso:
#   ./repack-toolsapi-car.sh
#   WSO2_MI_HOME=/ruta/a/wso2mi-4.5.0 ./repack-toolsapi-car.sh
#   SYNC_MODE=all ./repack-toolsapi-car.sh   # rsync completo ToolsAPIProject_1.0.0 -> CAR
#
set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
DEV_ROOT="${SCRIPT_DIR}"
CAR_NAME="ToolsAPIProject_1.0.0.car"
CAR_PATH="${DEV_ROOT}/${CAR_NAME}"
SRC_TREE="${DEV_ROOT}/ToolsAPIProject_1.0.0"

# Destino por defecto: carbonapps del MI del usuario (ajustar con WSO2_MI_HOME)
WSO2_MI_HOME="${WSO2_MI_HOME:-${HOME}/.wso2-mi/micro-integrator/wso2mi-4.5.0}"
CARBONAPPS_DIR="${WSO2_MI_HOME}/repository/deployment/server/carbonapps"

# Lista por defecto: rutas relativas a ToolsAPIProject_1.0.0/
# Incluye APIs, Data Services, Secuencias y Templates que se editan desde el repo.
# Mantener ordenado y SIN duplicados. Si algún archivo adicional se empieza a editar
# desde el repo, sumarlo acá para que el repack lo lleve al CAR.
DEFAULT_SYNC_FILES=(
  # APIs
  "toolsCOREAPI_1.0.0/toolsCOREAPI-1.0.0.xml"
  "toolsbpmAPI_1.0.0/toolsbpmAPI-1.0.0.xml"
  # Data Services
  "COREDataService_1.0.0/COREDataService-1.0.0.dbs"
  # Sequences
  "toolsBpmActorMembership_1.0.0/toolsBpmActorMembership-1.0.0.xml"
  "toolsBpmActorGrupo_1.0.0/toolsBpmActorGrupo-1.0.0.xml"
  "toolsCreateRole_1.0.0/toolsCreateRole-1.0.0.xml"
  # Templates
  "bpmAPICallTemplate_1.0.0/bpmAPICallTemplate-1.0.0.xml"
)

SYNC_MODE="${SYNC_MODE:-files}" # files | all

log() { printf '%s\n' "$*"; }

die() { log "ERROR: $*" >&2; exit 1; }

[[ -f "${CAR_PATH}" ]] || die "No existe el CAR: ${CAR_PATH}"
[[ -d "${SRC_TREE}" ]] || die "No existe el árbol fuente: ${SRC_TREE}"
command -v zip >/dev/null 2>&1 || die "Instalá el paquete zip (zip/unzip)"
command -v unzip >/dev/null 2>&1 || die "Instalá unzip"

TMP="$(mktemp -d "${TMPDIR:-/tmp}/repack-toolsapi-XXXXXX")"
cleanup() { rm -rf "${TMP}"; }
trap cleanup EXIT

UNPACK="${TMP}/unpack"
NEW_CAR="${TMP}/${CAR_NAME}"

mkdir -p "${UNPACK}"
log "Descomprimiendo ${CAR_PATH} -> ${UNPACK}"
unzip -q -o "${CAR_PATH}" -d "${UNPACK}"

if [[ "${SYNC_MODE}" == "all" ]]; then
  log "SYNC_MODE=all: rsync desde ${SRC_TREE}/"
  rsync -a --delete "${SRC_TREE}/" "${UNPACK}/"
else
  log "Sincronizando artefactos (modo files):"
  for rel in "${DEFAULT_SYNC_FILES[@]}"; do
    src="${SRC_TREE}/${rel}"
    dst="${UNPACK}/${rel}"
    [[ -f "${src}" ]] || die "Falta archivo fuente: ${src}"
    mkdir -p "$(dirname "${dst}")"
    cp -a "${src}" "${dst}"
    log "  OK ${rel}"
  done
fi

log "Generando ${NEW_CAR}"
(
  cd "${UNPACK}"
  rm -f "${NEW_CAR}"
  zip -r -q "${NEW_CAR}" .
)

BACKUP="${DEV_ROOT}/${CAR_NAME}.bak.$(date +%Y%m%d%H%M%S)"
cp -a "${CAR_PATH}" "${BACKUP}"
log "Backup del CAR anterior: ${BACKUP}"

# cp -a desde tmp a algunos FS (p. ej. montaje Windows) puede fallar; -f basta para el binario .car
cp -f "${NEW_CAR}" "${CAR_PATH}"
log "Actualizado en repo: ${CAR_PATH}"

if [[ -d "${CARBONAPPS_DIR}" ]]; then
  log "Copiando a ${CARBONAPPS_DIR}/"
  cp -f "${NEW_CAR}" "${CARBONAPPS_DIR}/${CAR_NAME}"
  log "Despliegue listo. WSO2 MI debería redesplegar el carbon app automáticamente."
else
  log "AVISO: No existe ${CARBONAPPS_DIR} — no se copió al MI. Definí WSO2_MI_HOME o creá el directorio."
fi

log "Hecho."
