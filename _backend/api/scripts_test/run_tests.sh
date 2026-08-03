#!/bin/bash

# Script maestro para ejecutar todas las pruebas
# Servidor WSO2: https://10.142.0.13:8280

WSO2_URL="https://10.142.0.13:8280"
BPM_SESSION="${1:-test_session_token}"
RESULTS_FILE="test_results_$(date +%Y%m%d_%H%M%S).txt"

echo "=========================================="
echo "TEST SUITE - API Crear Usuario"
echo "Servidor: $WSO2_URL"
echo "Fecha: $(date)"
echo "=========================================="
echo ""

# Función para ejecutar test y guardar resultado
run_test() {
    local test_name=$1
    local script=$2
    local args=$3
    
    echo "----------------------------------------"
    echo "Ejecutando: $test_name"
    echo "----------------------------------------"
    
    if [ -f "$script" ]; then
        bash "$script" $WSO2_URL $args 2>&1 | tee -a "$RESULTS_FILE"
        local exit_code=${PIPESTATUS[0]}
        echo ""
        echo "Exit code: $exit_code"
        echo ""
        return $exit_code
    else
        echo "ERROR: Script no encontrado: $script"
        echo "ERROR: Script no encontrado: $script" >> "$RESULTS_FILE"
        return 1
    fi
}

# Iniciar archivo de resultados
echo "=== RESULTADOS DE PRUEBAS ===" > "$RESULTS_FILE"
echo "Servidor: $WSO2_URL" >> "$RESULTS_FILE"
echo "Fecha: $(date)" >> "$RESULTS_FILE"
echo "" >> "$RESULTS_FILE"

# Test 1: Verificar duplicado (sin crear usuario primero)
echo "TEST 1: Verificar duplicado (email inexistente)"
run_test "Check Duplicado" "test_check_duplicado.sh" "test_nonexistent@example.com"
TEST1_RESULT=$?

# Test 2: Crear usuario
echo "TEST 2: Crear usuario nuevo"
run_test "Create Usuario" "test_create_usuario.sh" "$BPM_SESSION"
TEST2_RESULT=$?

# Extraer email del test 2 si fue exitoso
if [ $TEST2_RESULT -eq 0 ]; then
    # Intentar extraer email del output
    CREATED_EMAIL=$(grep -oP 'Email: \K[^\s]+' "$RESULTS_FILE" | tail -1)
    if [ -n "$CREATED_EMAIL" ]; then
        echo "TEST 3: Verificar duplicado (email creado)"
        run_test "Check Duplicado (creado)" "test_check_duplicado.sh" "$CREATED_EMAIL"
        TEST3_RESULT=$?
    fi
fi

# Test 4: Intentar crear usuario duplicado
if [ -n "$CREATED_EMAIL" ]; then
    echo "TEST 4: Intentar crear usuario duplicado"
    RANDOM_ID=$(date +%s)_dup
    bash test_create_usuario.sh $WSO2_URL "$BPM_SESSION" "$CREATED_EMAIL" 2>&1 | tee -a "$RESULTS_FILE"
    TEST4_RESULT=$?
fi

# Resumen
echo ""
echo "=========================================="
echo "RESUMEN DE PRUEBAS"
echo "=========================================="
echo "Test 1 (Check duplicado): $([ $TEST1_RESULT -eq 0 ] && echo 'PASS' || echo 'FAIL')"
echo "Test 2 (Crear usuario): $([ $TEST2_RESULT -eq 0 ] && echo 'PASS' || echo 'FAIL')"
[ -n "$TEST3_RESULT" ] && echo "Test 3 (Check duplicado creado): $([ $TEST3_RESULT -eq 0 ] && echo 'PASS' || echo 'FAIL')"
[ -n "$TEST4_RESULT" ] && echo "Test 4 (Duplicado rechazado): $([ $TEST4_RESULT -ne 0 ] && echo 'PASS' || echo 'FAIL')"
echo ""
echo "Resultados completos guardados en: $RESULTS_FILE"
echo "=========================================="

