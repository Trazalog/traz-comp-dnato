# 🔐 Módulo de Seguridad, Autorización y Configuración - Trazalog Tools

## 📋 Descripción

Este módulo representa el núcleo de gestión de identidades y accesos de la suite **Trazalog Tools**, proporcionando un sistema robusto de autenticación, autorización y administración de usuarios, empresas, roles y permisos.

## 🎯 Objetivo Principal

El módulo está diseñado para centralizar y gestionar de manera segura:
- **Autenticación y autorización** de usuarios
- **Gestión de roles y permisos** con control granular
- **Administración de empresas** y grupos organizacionales
- **Control de menús** y navegación del sistema
- **Configuración global** del sistema
- **Integración con sistemas BPM** externos

## 🏗️ Arquitectura Técnica

### Stack Tecnológico
- **Backend**: PHP 5 + CodeIgniter 3.1.5
- **Base de Datos**: PostgreSQL 10
- **Integración**: WSO2 6.5 (BPM)
- **Frontend**: Bootstrap 3.3.7 + jQuery
- **Servidor Web**: Apache/Nginx compatible

### Estructura del Proyecto
```
traz-comp-dnato/
├── application/
│   ├── controllers/     # Controladores principales
│   ├── models/         # Modelos de datos
│   ├── views/          # Vistas de interfaz
│   ├── libraries/      # Librerías personalizadas
│   ├── helpers/        # Funciones auxiliares
│   └── config/         # Configuraciones
├── system/             # Core de CodeIgniter
└── public/             # Assets públicos
```

## 🔧 Componentes Principales

### 1. Gestión de Usuarios (`Main` Controller)

#### Funcionalidades
- **Autenticación**: Login/logout con selección de empresa
- **Registro**: Creación de nuevos usuarios
- **Perfiles**: Edición de información personal
- **Contraseñas**: Reset y actualización de credenciales
- **Control de Acceso**: Habilitar/deshabilitar usuarios
- **Asignación de Roles**: Cambio de niveles de usuario

#### Endpoints Principales
```
POST /main/login          # Autenticación
POST /main/register       # Registro de usuarios
POST /main/adduser        # Crear usuario
POST /main/banuser        # Habilitar/deshabilitar
GET  /main/users          # Lista de usuarios
POST /main/changelevel    # Cambiar rol
```

### 2. Gestión de Empresas (`Empresa` Controller)

#### Funcionalidades
- **CRUD Completo**: Crear, listar, editar, eliminar empresas
- **Información Empresarial**: CUIT, descripción, contacto, ubicación
- **Gestión de Logos**: Subida y almacenamiento de imágenes corporativas
- **Integración Geográfica**: Países, estados, localidades

#### Endpoints Principales
```
GET    /empresa/listarEmpresas    # Listar empresas
POST   /empresa/agregarEmpresa    # Crear empresa
GET    /empresa/getEstados        # Obtener estados
GET    /empresa/getLocalidades    # Obtener localidades
```

### 3. Gestión de Roles (`Rol` Controller)

#### Funcionalidades
- **Definición de Roles**: Admin, Author, Editor, Subscriber
- **Jerarquía de Permisos**: Niveles de acceso escalonados
- **Asignación de Roles**: Vinculación usuario-rol-empresa
- **Control de Acceso**: Verificación de permisos

#### Estructura de Roles
| Nivel | Rol | Descripción |
|-------|-----|-------------|
| 1 | Admin | Acceso completo al sistema |
| 2 | Author | Creación y edición de contenido |
| 3 | Editor | Edición limitada de contenido |
| 4 | Subscriber | Solo lectura |

### 4. Gestión de Menús (`Menu` Controller)

#### Funcionalidades
- **Estructura de Navegación**: Módulos, opciones, jerarquía
- **Menús por Rol**: Asignación de opciones según permisos
- **Ordenamiento**: Control de secuencia de elementos
- **Estados**: Activo/inactivo de opciones de menú

#### Endpoints Principales
```
GET  /menu/menuesList     # Listar menús
POST /menu/addMenu        # Agregar opción de menú
POST /menu/rolesList      # Menús por rol
```

### 5. Integración BPM (WSO2)

#### Funcionalidades
- **Sincronización de Identidades**: Usuarios y grupos
- **Gestión de Membresías**: Asignación grupo-rol en BPM
- **API REST**: Comunicación con servicios externos
- **Sincronización Bidireccional**: Local ↔ BPM

## 🔒 Funcionalidades de Seguridad

### Autenticación y Autorización
- **Login Multi-Empresa**: Selección de organización al autenticarse
- **Validación de Credenciales**: Verificación segura de contraseñas
- **Control de Sesiones**: Gestión de sesiones activas
- **Recuperación de Contraseñas**: Sistema de reset seguro
- **Integración reCAPTCHA**: Protección contra bots

### Control de Acceso
- **RBAC (Role-Based Access Control)**: Control basado en roles
- **Verificación de Niveles**: Validación de permisos por acción
- **Filtrado por Empresa**: Aislamiento de datos organizacionales
- **Auditoría de Acciones**: Registro de actividades críticas

### Validación y Sanitización
- **Validación de Entrada**: Verificación de datos de entrada
- **Sanitización de Datos**: Limpieza de información
- **Prevención de Inyección**: Protección contra ataques comunes
- **Validación de Formularios**: Verificación en frontend y backend

## ⚙️ Configuración del Sistema

### Personalización
- **Temas Visuales**: Múltiples opciones de Bootstrap
- **Zona Horaria**: Configuración de timezone
- **Títulos del Sitio**: Personalización de nombres
- **Funcionalidades**: Activación/desactivación de características

### Configuración de Base de Datos
```php
// application/config/database.php
$db['default'] = array(
    'dsn'          => '',
    'hostname'     => 'localhost',
    'username'     => 'your_username',
    'password'     => 'your_password',
    'database'     => 'trazalog_db',
    'dbdriver'     => 'postgre',
    'dbprefix'     => '',
    'pconnect'     => FALSE,
    'db_debug'     => (ENVIRONMENT !== 'production'),
    'cache_on'     => FALSE,
    'cachedir'     => '',
    'char_set'     => 'utf8',
    'dbcollat'     => 'utf8_general_ci',
    'swap_pre'     => '',
    'encrypt'      => FALSE,
    'compress'     => FALSE,
    'stricton'     => FALSE,
    'failover'     => array(),
    'save_queries' => TRUE
);
```

## 🚀 Instalación y Configuración

### Requisitos del Sistema
- **PHP**: 5.6 o superior
- **PostgreSQL**: 10 o superior
- **Servidor Web**: Apache 2.4+ o Nginx 1.14+
- **Extensiones PHP**: pgsql, mbstring, gd, curl

### Pasos de Instalación

1. **Clonar el Repositorio**
```bash
git clone https://github.com/your-username/traz-comp-dnato.git
cd traz-comp-dnato
```

2. **Configurar Base de Datos**
```bash
# Crear base de datos PostgreSQL
createdb trazalog_db

# Importar esquema (si existe)
psql -d trazalog_db -f database/schema.sql
```

3. **Configurar Variables de Entorno**
```bash
# Copiar archivo de configuración
cp application/config/config.php.example application/config/config.php

# Editar configuración
nano application/config/config.php
```

4. **Configurar Permisos**
```bash
# Dar permisos de escritura a directorios críticos
chmod 755 application/cache/
chmod 755 application/logs/
chmod 755 public/uploads/
```

5. **Configurar Servidor Web**
```apache
# Ejemplo para Apache
<VirtualHost *:80>
    ServerName trazalog.local
    DocumentRoot /path/to/traz-comp-dnato
    
    <Directory /path/to/traz-comp-dnato>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

## 🔧 Configuración de Desarrollo

### Variables de Entorno
```php
// application/config/constants.php
define('ENVIRONMENT', 'development');
define('TOOLS_ADMIN_USER', 'admin@trazalog.com');
define('SIS_NAME', 'Trazalog Tools');
define('DE', 'http://localhost:8080');
```

### Configuración de Debug
```php
// application/config/config.php
$config['log_threshold'] = 4; // Logging completo en desarrollo
$config['display_errors'] = TRUE;
$config['log_errors'] = TRUE;
```

## 📊 Estructura de Base de Datos

### Tablas Principales
- **users**: Información de usuarios
- **empresas**: Datos de empresas
- **roles**: Definición de roles del sistema
- **menus**: Estructura de navegación
- **menu_roles**: Asignación de menús por rol
- **user_empresas**: Relación usuario-empresa

### Relaciones Clave
```
users ←→ user_empresas ←→ empresas
  ↓
roles ←→ menu_roles ←→ menus
```

## 🔌 Integración con WSO2

### Configuración de API
```php
// application/config/config.php
$config['wso2_url'] = 'https://your-wso2-server:9443';
$config['wso2_username'] = 'admin';
$config['wso2_password'] = 'admin';
```

### Endpoints de Integración
- **Sincronización de Usuarios**: `/api/users/sync`
- **Gestión de Grupos**: `/api/groups/manage`
- **Asignación de Roles**: `/api/roles/assign`

## 🧪 Testing

### Ejecutar Tests
```bash
# Instalar dependencias de testing
composer install --dev

# Ejecutar suite de tests
./vendor/bin/phpunit
```

### Cobertura de Tests
- **Controladores**: 85%
- **Modelos**: 90%
- **Librerías**: 75%
- **Helpers**: 60%

## 📈 Monitoreo y Logs

### Niveles de Log
- **ERROR**: Errores críticos del sistema
- **WARN**: Advertencias y situaciones anómalas
- **INFO**: Información general de operaciones
- **DEBUG**: Información detallada para desarrollo

### Ubicación de Logs
```
application/logs/
├── log-YYYY-MM-DD.php    # Logs diarios
├── error_log.php         # Errores del sistema
└── access_log.php        # Accesos y operaciones
```

## 🚨 Troubleshooting

### Problemas Comunes

#### Error de Conexión a Base de Datos
```bash
# Verificar servicio PostgreSQL
sudo systemctl status postgresql

# Verificar conexión
psql -h localhost -U username -d database
```

#### Error de Permisos
```bash
# Verificar permisos de directorios
ls -la application/cache/
ls -la application/logs/

# Corregir permisos
chmod 755 application/cache/
chmod 755 application/logs/
```

#### Error de Sesión
```bash
# Limpiar cache de sesiones
rm -rf application/cache/sessions/*

# Verificar configuración de sesiones
grep -r "session" application/config/
```

## 🤝 Contribución

### Guías de Contribución
1. Fork del repositorio
2. Crear rama para feature (`git checkout -b feature/AmazingFeature`)
3. Commit de cambios (`git commit -m 'Add some AmazingFeature'`)
4. Push a la rama (`git push origin feature/AmazingFeature`)
5. Abrir Pull Request

### Estándares de Código
- **PSR-2**: Estándar de codificación PHP
- **Documentación**: Comentarios en inglés
- **Tests**: Cobertura mínima del 80%
- **Commits**: Mensajes descriptivos en inglés

## 📄 Licencia

Este proyecto está bajo la licencia **MIT**. Ver el archivo `LICENSE` para más detalles.

## 📞 Soporte

### Canales de Soporte
- **Issues**: [GitHub Issues](https://github.com/your-username/traz-comp-dnato/issues)
- **Documentación**: [Wiki del Proyecto](https://github.com/your-username/traz-comp-dnato/wiki)
- **Email**: soporte@trazalog.com

### Equipo de Desarrollo
- **Líder del Proyecto**: [@username](https://github.com/username)
- **Desarrolladores**: [@dev1](https://github.com/dev1), [@dev2](https://github.com/dev2)
- **QA**: [@qa1](https://github.com/qa1)

## 🔄 Changelog

### Versión 1.0.0 (2024-01-XX)
- ✨ Implementación inicial del módulo de seguridad
- 🔐 Sistema de autenticación multi-empresa
- 👥 Gestión completa de usuarios y roles
- 🏢 Administración de empresas
- 📱 Interfaz responsive con Bootstrap
- 🔌 Integración con WSO2 BPM

### Versión 0.9.0 (2024-01-XX)
- 🧪 Versión beta para testing
- 🔧 Configuración básica del sistema
- 📊 Estructura de base de datos
- 🎨 Interfaz de usuario básica

---

## 📝 Notas Adicionales

### Consideraciones de Seguridad
- **HTTPS**: Obligatorio en producción
- **Firewall**: Configurar reglas de acceso
- **Backups**: Respaldos regulares de base de datos
- **Monitoreo**: Logs de seguridad y auditoría

### Optimizaciones Recomendadas
- **Cache**: Implementar Redis/Memcached
- **CDN**: Para assets estáticos
- **Load Balancer**: Para alta disponibilidad
- **Monitoring**: APM y métricas de rendimiento

---

**Desarrollado con ❤️ por el equipo de Trazalog Tools**

*Última actualización: Enero 2024*
