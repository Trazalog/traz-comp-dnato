#!/usr/bin/env python3
"""
Script completo de prueba para Fase 0.1: Hashear Password MD5 en DataService para AssetPlanner
Valida que el password se hashee correctamente en MD5 antes de insertarlo en MySQL AssetPlanner
"""

import requests
import hashlib
import time
import sys
from datetime import datetime

# Colores para output
GREEN = '\033[0;32m'
RED = '\033[0;31m'
YELLOW = '\033[1;33m'
BLUE = '\033[0;34m'
NC = '\033[0m'  # No Color

# Configuración
DATASERVICE_URL = "http://10.142.0.13:8280/services/COREDataService/assetuser/add"
MYSQL_CONFIG = {
    'host': '10.142.0.13',
    'port': 3306,
    'user': 'rootremote',
    'password': '!Password00',
    'database': 'assetv2'
}

def print_header(text):
    print(f"\n{'='*60}")
    print(f"{text}")
    print(f"{'='*60}\n")

def print_section(text):
    print(f"\n{'-'*60}")
    print(f"{text}")
    print(f"{'-'*60}\n")

def calculate_md5(text):
    """Calcula el MD5 de un texto"""
    return hashlib.md5(text.encode('utf-8')).hexdigest()

def test_0_1_1():
    """Prueba 0.1.1: Prueba Directa del DataService"""
    print_section("Prueba 0.1.1: Prueba Directa del DataService")
    
    timestamp = int(time.time())
    test_nick = f"test_md5_{timestamp}"
    test_password = "password123"
    expected_md5 = "482c811da5d5b4bc6d497ffa98491e38"
    
    print(f"Creando usuario: {test_nick}")
    print(f"Password original: {test_password}")
    print(f"MD5 esperado: {expected_md5}")
    
    # Verificar MD5 localmente
    calculated_md5 = calculate_md5(test_password)
    if calculated_md5 == expected_md5:
        print(f"{GREEN}✓ MD5 calculado correctamente: {calculated_md5}{NC}")
    else:
        print(f"{RED}✗ MD5 incorrecto. Esperado: {expected_md5}, Obtenido: {calculated_md5}{NC}")
        return False, None, None
    
    # Llamar al endpoint
    print(f"\nLlamando al endpoint: {DATASERVICE_URL}")
    payload = {
        "_post_assetuser_add": {
            "nick": test_nick,
            "name": "Test",
            "lastName": "MD5",
            "pass": test_password,
            "image": ""
        }
    }
    
    try:
        response = requests.post(DATASERVICE_URL, json=payload, timeout=10)
        print(f"HTTP Status Code: {response.status_code}")
        print(f"Response Body: {response.text[:200]}")
        
        if response.status_code in [200, 202]:
            print(f"{GREEN}✓ Request exitoso (HTTP {response.status_code}){NC}")
            return True, test_nick, test_password
        else:
            print(f"{RED}✗ Request falló (HTTP {response.status_code}){NC}")
            return False, None, None
    except Exception as e:
        print(f"{RED}✗ Error al llamar al endpoint: {e}{NC}")
        return False, None, None

def test_0_1_2(test_nick, test_password):
    """Prueba 0.1.2: Verificar Login en AssetPlanner"""
    print_section("Prueba 0.1.2: Verificar Login en AssetPlanner")
    
    try:
        import pymysql
    except ImportError:
        print(f"{YELLOW}⚠ PyMySQL no está instalado. Instalando...{NC}")
        import subprocess
        subprocess.check_call([sys.executable, "-m", "pip", "install", "pymysql", "-q"])
        import pymysql
    
    try:
        # Conectar a MySQL
        connection = pymysql.connect(
            host=MYSQL_CONFIG['host'],
            port=MYSQL_CONFIG['port'],
            user=MYSQL_CONFIG['user'],
            password=MYSQL_CONFIG['password'],
            database=MYSQL_CONFIG['database'],
            cursorclass=pymysql.cursors.DictCursor
        )
        
        with connection.cursor() as cursor:
            # Verificar usuario creado
            sql = "SELECT usrNick, usrPassword, LENGTH(usrPassword) as pass_length FROM sisusers WHERE usrNick = %s"
            cursor.execute(sql, (test_nick,))
            result = cursor.fetchone()
            
            if not result:
                print(f"{RED}✗ Usuario {test_nick} no encontrado en la base de datos{NC}")
                return False
            
            print(f"{GREEN}✓ Usuario encontrado en la base de datos{NC}")
            print(f"  - usrNick: {result['usrNick']}")
            print(f"  - usrPassword: {result['usrPassword']}")
            print(f"  - Longitud: {result['pass_length']} caracteres")
            
            # Verificar longitud
            if result['pass_length'] == 32:
                print(f"{GREEN}✓ Longitud correcta (32 caracteres){NC}")
            else:
                print(f"{RED}✗ Longitud incorrecta. Esperado: 32, Obtenido: {result['pass_length']}{NC}")
                return False
            
            # Verificar formato hexadecimal
            import re
            if re.match(r'^[0-9a-f]{32}$', result['usrPassword']):
                print(f"{GREEN}✓ Formato hexadecimal válido{NC}")
            else:
                print(f"{RED}✗ Formato hexadecimal inválido{NC}")
                return False
            
            # Verificar que coincide con MD5 esperado
            expected_md5 = calculate_md5(test_password)
            if result['usrPassword'] == expected_md5:
                print(f"{GREEN}✓ MD5 coincide con esperado: {expected_md5}{NC}")
            else:
                print(f"{RED}✗ MD5 no coincide. Esperado: {expected_md5}, Obtenido: {result['usrPassword']}{NC}")
                return False
            
            # Verificar que el login funcionaría (comparar MD5 del password con el guardado)
            print(f"\n{YELLOW}Nota: Para probar login manualmente:{NC}")
            print(f"  1. Ir a AssetPlanner")
            print(f"  2. Username: {test_nick}")
            print(f"  3. Password: {test_password}")
            print(f"  4. El sistema debería hashear el password y compararlo con {result['usrPassword']}")
            print(f"  5. Si coinciden, el login será exitoso")
            
        connection.close()
        return True
        
    except ImportError:
        print(f"{RED}✗ No se pudo instalar PyMySQL. Instala manualmente: pip install pymysql{NC}")
        return False
    except Exception as e:
        print(f"{RED}✗ Error al conectar a MySQL: {e}{NC}")
        return False

def test_0_1_3():
    """Prueba 0.1.3: Prueba con Diferentes Passwords"""
    print_section("Prueba 0.1.3: Prueba con Diferentes Passwords")
    
    test_passwords = [
        ("abc", "900150983cd24fb0d6963f7d28e17f72"),
        ("password_muy_largo_123456789", "a1b2c3d4e5f6g7h8i9j0k1l2m3n4o5p6"),
        ("P@ssw0rd!123", "dummy"),  # Calcularemos el MD5
    ]
    
    # Calcular MD5 para el último
    test_passwords[2] = ("P@ssw0rd!123", calculate_md5("P@ssw0rd!123"))
    
    results = []
    timestamp = int(time.time())
    
    for i, (password, expected_md5) in enumerate(test_passwords):
        test_nick = f"test_md5_variants_{timestamp}_{i}"
        calculated_md5 = calculate_md5(password)
        
        print(f"\nTest {i+1}: {test_nick}")
        print(f"  Password: {password}")
        print(f"  MD5 esperado: {expected_md5}")
        print(f"  MD5 calculado: {calculated_md5}")
        
        if calculated_md5 == expected_md5:
            print(f"  {GREEN}✓ MD5 correcto{NC}")
        else:
            print(f"  {RED}✗ MD5 incorrecto{NC}")
            results.append(False)
            continue
        
        # Crear usuario
        payload = {
            "_post_assetuser_add": {
                "nick": test_nick,
                "name": "Test",
                "lastName": f"Variant{i}",
                "pass": password,
                "image": ""
            }
        }
        
        try:
            response = requests.post(DATASERVICE_URL, json=payload, timeout=10)
            if response.status_code in [200, 202]:
                print(f"  {GREEN}✓ Usuario creado exitosamente{NC}")
                results.append(True)
            else:
                print(f"  {RED}✗ Error al crear usuario (HTTP {response.status_code}){NC}")
                results.append(False)
        except Exception as e:
            print(f"  {RED}✗ Error: {e}{NC}")
            results.append(False)
    
    if all(results):
        print(f"\n{GREEN}✓ Todas las pruebas de passwords variados pasaron{NC}")
        return True
    else:
        print(f"\n{RED}✗ Algunas pruebas de passwords variados fallaron{NC}")
        return False

def test_0_1_4(test_nick, test_password):
    """Prueba 0.1.4: Verificar Consistencia"""
    print_section("Prueba 0.1.4: Verificar Consistencia del Hash MD5")
    
    try:
        import pymysql
    except ImportError:
        print(f"{YELLOW}⚠ PyMySQL no está instalado{NC}")
        return False
    
    try:
        connection = pymysql.connect(
            host=MYSQL_CONFIG['host'],
            port=MYSQL_CONFIG['port'],
            user=MYSQL_CONFIG['user'],
            password=MYSQL_CONFIG['password'],
            database=MYSQL_CONFIG['database'],
            cursorclass=pymysql.cursors.DictCursor
        )
        
        with connection.cursor() as cursor:
            sql = "SELECT usrPassword FROM sisusers WHERE usrNick = %s"
            cursor.execute(sql, (test_nick,))
            result = cursor.execute(sql, (test_nick,))
            row = cursor.fetchone()
            
            if not row:
                print(f"{RED}✗ Usuario no encontrado{NC}")
                return False
            
            stored_hash = row['usrPassword']
            calculated_hash = calculate_md5(test_password)
            
            print(f"Password original: {test_password}")
            print(f"Hash almacenado en BD: {stored_hash}")
            print(f"Hash calculado localmente: {calculated_hash}")
            
            if stored_hash == calculated_hash:
                print(f"{GREEN}✓ Los hashes coinciden perfectamente{NC}")
                print(f"{GREEN}✓ Consistencia verificada{NC}")
                connection.close()
                return True
            else:
                print(f"{RED}✗ Los hashes NO coinciden{NC}")
                connection.close()
                return False
                
    except Exception as e:
        print(f"{RED}✗ Error: {e}{NC}")
        return False

def main():
    print_header("FASE 0.1: Prueba Completa de Hash MD5 en AssetPlanner")
    print(f"Fecha: {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}")
    
    all_passed = True
    
    # Prueba 0.1.1
    success, test_nick, test_password = test_0_1_1()
    if not success:
        print(f"\n{RED}✗ Fase 0.1.1 falló. No se puede continuar.{NC}")
        return False
    all_passed = all_passed and success
    
    # Esperar un momento para que se procese
    print(f"\n{YELLOW}Esperando 2 segundos para que se procese la inserción...{NC}")
    time.sleep(2)
    
    # Prueba 0.1.2
    success = test_0_1_2(test_nick, test_password)
    all_passed = all_passed and success
    
    # Prueba 0.1.3
    success = test_0_1_3()
    all_passed = all_passed and success
    
    # Prueba 0.1.4
    success = test_0_1_4(test_nick, test_password)
    all_passed = all_passed and success
    
    # Resumen final
    print_header("RESUMEN FINAL - FASE 0.1")
    if all_passed:
        print(f"{GREEN}✓✓✓ TODAS LAS PRUEBAS PASARON AL 100% ✓✓✓{NC}")
        print(f"\n{GREEN}FASE 0.1 COMPLETADA EXITOSAMENTE{NC}")
        print(f"\nUsuario de prueba creado: {test_nick}")
        print(f"Password original: {test_password}")
        print(f"MD5 en BD: {calculate_md5(test_password)}")
        return True
    else:
        print(f"{RED}✗✗✗ ALGUNAS PRUEBAS FALLARON ✗✗✗{NC}")
        print(f"\n{RED}FASE 0.1 NO COMPLETADA{NC}")
        return False

if __name__ == "__main__":
    success = main()
    sys.exit(0 if success else 1)






