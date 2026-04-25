#!/bin/bash

# Script de diagnóstico de conectividad con WSO2
WSO2_URL="https://10.142.0.13:8280"
RESULTS_FILE="diagnostico_$(date +%Y%m%d_%H%M%S).txt"

echo "=========================================="
echo "DIAGNÓSTICO DE CONECTIVIDAD WSO2"
echo "Servidor: $WSO2_URL"
echo "Fecha: $(date)"
echo "=========================================="
echo ""

{
    echo "=== RESULTADOS DE DIAGNÓSTICO ==="
    echo "Servidor: $WSO2_URL"
    echo "Fecha: $(date)"
    echo ""
    
    # Test 1: Ping básico
    echo "TEST 1: Ping al servidor"
    echo "----------------------------------------"
    ping -c 3 10.142.0.13 2>&1 || echo "Ping falló o no disponible"
    echo ""
    
    # Test 2: Verificar puerto
    echo "TEST 2: Verificar puerto 8280"
    echo "----------------------------------------"
    timeout 5 bash -c "echo > /dev/tcp/10.142.0.13/8280" 2>&1 && echo "Puerto 8280: ABIERTO" || echo "Puerto 8280: CERRADO o no accesible"
    echo ""
    
    # Test 3: curl con verbose
    echo "TEST 3: curl verbose (GET /)"
    echo "----------------------------------------"
    curl -k -v -X GET "${WSO2_URL}/" 2>&1 | head -30
    echo ""
    
    # Test 4: curl a API específica
    echo "TEST 4: curl a toolsCOREAPI"
    echo "----------------------------------------"
    curl -k -v -X GET "${WSO2_URL}/toolsCOREAPI/1.0.0/usuario" -H "Accept: application/json" 2>&1 | head -40
    echo ""
    
    # Test 5: curl a Data Service
    echo "TEST 5: curl a COREDataService"
    echo "----------------------------------------"
    curl -k -v -X GET "${WSO2_URL}/services/COREDataService" 2>&1 | head -40
    echo ""
    
    # Test 6: Información del sistema
    echo "TEST 6: Información del sistema"
    echo "----------------------------------------"
    echo "OS: $(uname -a)"
    echo "curl version: $(curl --version | head -1)"
    echo "OpenSSL version: $(openssl version 2>/dev/null || echo 'No disponible')"
    echo ""
    
} | tee "$RESULTS_FILE"

echo ""
echo "Resultados guardados en: $RESULTS_FILE"

