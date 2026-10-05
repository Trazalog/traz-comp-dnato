#!/usr/bin/env python3
"""
Script simple para verificar MD5 en MySQL AssetPlanner
Usa solo librerías estándar de Python
"""

import subprocess
import sys
import hashlib
import time

def calculate_md5(text):
    return hashlib.md5(text.encode('utf-8')).hexdigest()

def mysql_query(query, host, port, user, password, database):
    """Ejecuta una query MySQL usando el cliente mysql si está disponible"""
    try:
        cmd = [
            'mysql',
            f'-h{host}',
            f'-P{port}',
            f'-u{user}',
            f'-p{password}',
            database,
            '-e', query,
            '-N'  # Sin headers
        ]
        result = subprocess.run(cmd, capture_output=True, text=True, timeout=10)
        if result.returncode == 0:
            return result.stdout.strip()
        else:
            return None
    except FileNotFoundError:
        return "MYSQL_NOT_FOUND"
    except Exception as e:
        return f"ERROR: {e}"

def main():
    # Configuración
    MYSQL_HOST = "10.142.0.13"
    MYSQL_PORT = "3306"
    MYSQL_USER = "rootremote"
    MYSQL_PASS = "!Password00"
    MYSQL_DB = "assetv2"
    
    # Buscar el último usuario test_md5 creado
    print("Buscando usuarios de prueba en la base de datos...")
    
    query = "SELECT usrNick, usrPassword, LENGTH(usrPassword) as pass_length FROM sisusers WHERE usrNick LIKE 'test_md5_%' ORDER BY usrNick DESC LIMIT 5;"
    result = mysql_query(query, MYSQL_HOST, MYSQL_PORT, MYSQL_USER, MYSQL_PASS, MYSQL_DB)
    
    if result == "MYSQL_NOT_FOUND":
        print("ERROR: Cliente MySQL no encontrado. Instala: sudo apt install mysql-client-core-8.0")
        print("\nAlternativa: Ejecuta manualmente en MySQL:")
        print(f"mysql -h {MYSQL_HOST} -P {MYSQL_PORT} -u {MYSQL_USER} -p'{MYSQL_PASS}' {MYSQL_DB}")
        print(f"-e \"SELECT usrNick, usrPassword, LENGTH(usrPassword) as pass_length FROM sisusers WHERE usrNick LIKE 'test_md5_%' ORDER BY usrNick DESC LIMIT 5;\"")
        sys.exit(1)
    
    if not result or result.startswith("ERROR"):
        print(f"ERROR: {result}")
        sys.exit(1)
    
    lines = [line for line in result.split('\n') if line.strip()]
    
    if not lines:
        print("No se encontraron usuarios de prueba en la base de datos.")
        sys.exit(1)
    
    print(f"\nEncontrados {len(lines)} usuario(s) de prueba:\n")
    
    all_passed = True
    for line in lines:
        parts = line.split('\t')
        if len(parts) < 3:
            continue
            
        usr_nick = parts[0]
        usr_password = parts[1]
        pass_length = int(parts[2])
        
        print(f"Usuario: {usr_nick}")
        print(f"  Password hash: {usr_password}")
        print(f"  Longitud: {pass_length} caracteres")
        
        # Verificar longitud
        if pass_length == 32:
            print("  ✓ Longitud correcta (32 caracteres)")
        else:
            print(f"  ✗ Longitud incorrecta. Esperado: 32, Obtenido: {pass_length}")
            all_passed = False
        
        # Verificar formato hexadecimal
        import re
        if re.match(r'^[0-9a-f]{32}$', usr_password):
            print("  ✓ Formato hexadecimal válido")
        else:
            print("  ✗ Formato hexadecimal inválido")
            all_passed = False
        
        # Para el primer usuario, verificar con password123
        if "test_md5_" in usr_nick and not usr_nick.endswith("_variant_"):
            expected_md5 = calculate_md5("password123")
            if usr_password == expected_md5:
                print(f"  ✓ MD5 coincide con password123: {expected_md5}")
            else:
                print(f"  ✗ MD5 no coincide. Esperado para 'password123': {expected_md5}")
                print(f"    Obtenido: {usr_password}")
                all_passed = False
        
        print()
    
    if all_passed:
        print("✓✓✓ TODAS LAS VALIDACIONES PASARON ✓✓✓")
        return 0
    else:
        print("✗✗✗ ALGUNAS VALIDACIONES FALLARON ✗✗✗")
        return 1

if __name__ == "__main__":
    sys.exit(main())






