# ✅ Checklist Rápido - Despliegue API Usuario

## 📦 Artefactos (3 archivos)

1. **`sp_insert_usuario_hash.sql`** → PostgreSQL Stored Procedure
2. **`COREDataService.dbs`** → WSO2 Data Service  
3. **`toolsCOREAPI.xml`** → WSO2 API

---

## 🔄 Orden de Despliegue

### **1️⃣ PostgreSQL (Base de Datos)**
```bash
# 1.1 Habilitar pgcrypto
psql -U postgres -d tu_db -c "CREATE EXTENSION IF NOT EXISTS pgcrypto;"

# 1.2 Crear stored procedure
psql -U postgres -d tu_db -f _backend/api/dataservices/sp_insert_usuario_hash.sql

# 1.3 Validar
psql -U postgres -d tu_db -c "SELECT proname FROM pg_proc WHERE proname = 'insert_usuario_con_hash';"
```

### **2️⃣ WSO2 Data Service**
```bash
# 2.1 Desplegar
# Copiar: COREDataService.dbs → /ruta/wso2/repository/deployment/server/dataservices/

# 2.2 Validar WSDL
curl https://wso2-server:9443/services/COREDataService?wsdl

# 2.3 Probar queries
curl -X GET "https://wso2-server:9443/services/COREDataService/usuario/duplicado/test@test.com"
```

### **3️⃣ WSO2 API**
```bash
# 3.1 Desplegar
# Copiar: toolsCOREAPI.xml → /ruta/wso2/repository/deployment/synapse-configs/default/api/

# 3.2 Probar API completa
curl -X POST "https://wso2-server:8243/toolsCOREAPI/1.0.0/usuario" \
  -H "Content-Type: application/json" \
  -d '{"bpmSession":"...","usuario":{...}}'
```

---

## ✅ Validaciones Críticas

- [ ] Stored procedure retorna `id` del usuario
- [ ] Hash tiene formato `sha256:1000:salt:hash`
- [ ] Data Service expone 3 queries correctamente
- [ ] API valida duplicados antes de crear
- [ ] API crea en PostgreSQL → users_business → Asset Planner → BPM
- [ ] Rollback funciona si falla BPM
- [ ] Hash es compatible con PHP `Password` library

---

**Ver guía completa:** `GUIA_DESPLIEGUE_API_USUARIO.md`

