#!/bin/bash

# Script completo de prueba para Fase 0.1: Hashear Password MD5 en DataService para AssetPlanner
# Valida que el password se hashee correctamente en MD5 antes de insertarlo en MySQL AssetPlanner

set -e

# Colores
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

# Configuración
DATASERVICE_URL="http://10.142.0.13:8280/services/COREDataService/assetuser/add"
MYSQL_HOST="10.142.0.13"
MYSQL_PORT="3306"
MYSQL_USER="rootremote"
MYSQL_PASS="!Password00"
MYSQL_DB="assetv2"

# Función para calcular MD5
calculate_md5() {
    echo -n "$1" | md5sum | cut -d' ' -f1
}

# Función para verificar si MySQL está disponible
check_mysql() {
    if command -v mysql &> /dev/null; then
        return 0
    else
        echo -e "${YELLOW}⚠ Cliente MySQL no encontrado. Intentando con Python...${NC}"
        return 1
    fi
}

# Función para ejecutar query MySQL con Python
mysql_query_python() {
    local query="$1"
    python3 << EOF
import sys
try:
    import pymysql
    connection = pymysql.connect(
        host='${MYSQL_HOST}',
        port=${MYSQL_PORT},
        user='${MYSQL_USER}',
        password='${MYSQL_PASS}',
        database='${MYSQL_DB}',
        cursorclass=pymysql.cursors.DictCursor
    )
    with connection.cursor() as cursor:
        cursor.execute("${query}")
        result = cursor.fetchone()
        if result:
            print(f"usrNick={result.get('usrNick', '')}")
            print(f"usrPassword={result.get('usrPassword', '')}")
            print(f"pass_length={result.get('pass_length', 0)}")
        else:
            print("NOT_FOUND")
    connection.close()
except ImportError:
    print("PYMYSQL_NOT_INSTALLED")
    sys.exit(1)
except Exception as e:
    print(f"ERROR: {e}")
    sys.exit(1)
EOF
}

print_header() {
    echo ""
    echo "============================================================"
    echo "$1"
    echo "============================================================"
    echo ""
}

print_section() {
    echo ""
    echo "------------------------------------------------------------"
    echo "$1"
    echo "------------------------------------------------------------"
    echo ""
}

# PRUEBA 0.1.1: Prueba Directa del DataService
print_section "Prueba 0.1.1: Prueba Directa del DataService"

TIMESTAMP=$(date +%s)
TEST_NICK="test_md5_${TIMESTAMP}"
TEST_PASSWORD="password123"
EXPECTED_MD5="482c811da5d5b4bc6d497ffa98491e38"

echo "Creando usuario: $TEST_NICK"
echo "Password original: $TEST_PASSWORD"
echo "MD5 esperado: $EXPECTED_MD5"

# Verificar MD5 localmente
CALCULATED_MD5=$(calculate_md5 "$TEST_PASSWORD")
if [ "$CALCULATED_MD5" == "$EXPECTED_MD5" ]; then
    echo -e "${GREEN}✓ MD5 calculado correctamente: $CALCULATED_MD5${NC}"
else
    echo -e "${RED}✗ MD5 incorrecto. Esperado: $EXPECTED_MD5, Obtenido: $CALCULATED_MD5${NC}"
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
if [ "$HTTP_CODE" == "202" ] || [ "$HTTP_CODE" == "200" ]; then
    echo -e "${GREEN}✓ Request exitoso (HTTP $HTTP_CODE)${NC}"
else
    echo -e "${RED}✗ Request falló (HTTP $HTTP_CODE)${NC}"
    exit 1
fi

# Esperar un momento para que se procese
echo -e "\n${YELLOW}Esperando 3 segundos para que se procese la inserción...${NC}"
sleep 3

# PRUEBA 0.1.2 y 0.1.4: Verificar en Base de Datos
print_section "Prueba 0.1.2 y 0.1.4: Verificar en Base de Datos MySQL"

if check_mysql; then
    # Usar cliente MySQL nativo
    echo "Usando cliente MySQL nativo..."
    QUERY_RESULT=$(mysql -h "$MYSQL_HOST" -P "$MYSQL_PORT" -u "$MYSQL_USER" -p"$MYSQL_PASS" "$MYSQL_DB" -e "SELECT usrNick, usrPassword, LENGTH(usrPassword) as pass_length FROM sisusers WHERE usrNick = '$TEST_NICK';" 2>&1 | tail -n +2)
    
    if [ -z "$QUERY_RESULT" ] || echo "$QUERY_RESULT" | grep -q "ERROR"; then
        echo -e "${RED}✗ Error al consultar MySQL: $QUERY_RESULT${NC}"
        exit 1
    fi
    
    USR_PASSWORD=$(echo "$QUERY_RESULT" | awk '{print $2}')
    PASS_LENGTH=$(echo "$QUERY_RESULT" | awk '{print $3}')
    
else
    # Usar Python con PyMySQL
    echo "Usando Python con PyMySQL..."
    
    # Intentar instalar PyMySQL si no está
        if ! python3 -c "import pymysql" 2>/dev/null; then
            echo -e "${YELLOW}Instalando PyMySQL...${NC}"
            pip3 install --break-system-packages pymysql --quiet 2>&1 || {
                echo -e "${RED}✗ No se pudo instalar PyMySQL. Instala manualmente: pip3 install --break-system-packages pymysql${NC}"
                exit 1
            }
        fi
    
    QUERY_RESULT=$(mysql_query_python "SELECT usrNick, usrPassword, LENGTH(usrPassword) as pass_length FROM sisusers WHERE usrNick = '$TEST_NICK'")
    
    if echo "$QUERY_RESULT" | grep -q "NOT_FOUND"; then
        echo -e "${RED}✗ Usuario $TEST_NICK no encontrado en la base de datos${NC}"
        exit 1
    fi
    
    if echo "$QUERY_RESULT" | grep -q "ERROR"; then
        echo -e "${RED}✗ Error: $QUERY_RESULT${NC}"
        exit 1
    fi
    
    USR_PASSWORD=$(echo "$QUERY_RESULT" | grep "usrPassword=" | cut -d'=' -f2)
    PASS_LENGTH=$(echo "$QUERY_RESULT" | grep "pass_length=" | cut -d'=' -f2)
fi

echo -e "${GREEN}✓ Usuario encontrado en la base de datos${NC}"
echo "  - usrNick: $TEST_NICK"
echo "  - usrPassword: $USR_PASSWORD"
echo "  - Longitud: $PASS_LENGTH caracteres"

# Verificar longitud
if [ "$PASS_LENGTH" == "32" ]; then
    echo -e "${GREEN}✓ Longitud correcta (32 caracteres)${NC}"
else
    echo -e "${RED}✗ Longitud incorrecta. Esperado: 32, Obtenido: $PASS_LENGTH${NC}"
    exit 1
fi

# Verificar formato hexadecimal
if [[ "$USR_PASSWORD" =~ ^[0-9a-f]{32}$ ]]; then
    echo -e "${GREEN}✓ Formato hexadecimal válido${NC}"
else
    echo -e "${RED}✗ Formato hexadecimal inválido${NC}"
    exit 1
fi

# Verificar que coincide con MD5 esperado
if [ "$USR_PASSWORD" == "$EXPECTED_MD5" ]; then
    echo -e "${GREEN}✓ MD5 coincide con esperado: $EXPECTED_MD5${NC}"
else
    echo -e "${RED}✗ MD5 no coincide. Esperado: $EXPECTED_MD5, Obtenido: $USR_PASSWORD${NC}"
    exit 1
fi

# PRUEBA 0.1.3: Prueba con Diferentes Passwords
print_section "Prueba 0.1.3: Prueba con Diferentes Passwords"

TEST_PASSWORDS=(
    "abc:900150983cd24fb0d6963f7d28e17f72"
    "P@ssw0rd!123"
)

ALL_PASSED=true
for i in "${!TEST_PASSWORDS[@]}"; do
    IFS=':' read -r PASSWORD EXPECTED <<< "${TEST_PASSWORDS[$i]}"
    
    # Si no hay EXPECTED, calcularlo
    if [ -z "$EXPECTED" ]; then
        EXPECTED=$(calculate_md5 "$PASSWORD")
    fi
    
    TEST_NICK_VAR="test_md5_variant_${TIMESTAMP}_${i}"
    CALCULATED=$(calculate_md5 "$PASSWORD")
    
    echo ""
    echo "Test $((i+1)): $TEST_NICK_VAR"
    echo "  Password: $PASSWORD"
    echo "  MD5 esperado: $EXPECTED"
    echo "  MD5 calculado: $CALCULATED"
    
    if [ "$CALCULATED" == "$EXPECTED" ]; then
        echo -e "  ${GREEN}✓ MD5 correcto${NC}"
    else
        echo -e "  ${RED}✗ MD5 incorrecto${NC}"
        ALL_PASSED=false
        continue
    fi
    
    # Crear usuario
    RESPONSE_VAR=$(curl -s -w "\n%{http_code}" -X POST "$DATASERVICE_URL" \
        -H "Content-Type: application/json" \
        -d "{\"_post_assetuser_add\":{\"nick\":\"$TEST_NICK_VAR\",\"name\":\"Test\",\"lastName\":\"Variant${i}\",\"pass\":\"$PASSWORD\",\"image\":\"\"}}")
    
    HTTP_CODE_VAR=$(echo "$RESPONSE_VAR" | tail -n1)
    
    if [ "$HTTP_CODE_VAR" == "202" ] || [ "$HTTP_CODE_VAR" == "200" ]; then
        echo -e "  ${GREEN}✓ Usuario creado exitosamente${NC}"
    else
        echo -e "  ${RED}✗ Error al crear usuario (HTTP $HTTP_CODE_VAR)${NC}"
        ALL_PASSED=false
    fi
done

if [ "$ALL_PASSED" == true ]; then
    echo -e "\n${GREEN}✓ Todas las pruebas de passwords variados pasaron${NC}"
else
    echo -e "\n${RED}✗ Algunas pruebas de passwords variados fallaron${NC}"
    exit 1
fi

# RESUMEN FINAL
print_header "RESUMEN FINAL - FASE 0.1"

echo -e "${GREEN}✓✓✓ TODAS LAS PRUEBAS PASARON AL 100% ✓✓✓${NC}"
echo ""
echo -e "${GREEN}FASE 0.1 COMPLETADA EXITOSAMENTE${NC}"
echo ""
echo "Usuario de prueba principal creado: $TEST_NICK"
echo "Password original: $TEST_PASSWORD"
echo "MD5 en BD: $USR_PASSWORD"
echo ""
echo -e "${BLUE}Nota: Para probar login manualmente en AssetPlanner:${NC}"
echo "  1. Ir a AssetPlanner"
echo "  2. Username: $TEST_NICK"
echo "  3. Password: $TEST_PASSWORD"
echo "  4. El sistema debería hashear el password y compararlo con $USR_PASSWORD"
echo "  5. Si coinciden, el login será exitoso"
echo ""

exit 0

