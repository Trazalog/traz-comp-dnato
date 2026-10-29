-- Migración: DNATO-TOKEN (ADR-A10, análisis aprobado por el PM el 2026-10-29)
-- Tabla: seg.oauth_clients
-- Propósito: clientes de máquina para grant_type=client_credentials. Uno por
--            empresa con los agentes de IA habilitados. El token que se emite
--            lleva el empr_id DE ESTA FILA, nunca uno que venga en el pedido.
--
-- El secreto NO se guarda: solo su hash (password_hash, bcrypt). Se muestra
-- una única vez al darlo de alta con:
--   php index.php cli oauth_cliente_alta <empr_id> "<nombre>"
--
-- Idempotente. Ejecutar como superusuario o con permisos en el schema seg.
-- Rollback: DROP TABLE IF EXISTS seg.oauth_clients;

CREATE TABLE IF NOT EXISTS seg.oauth_clients (
    client_id       VARCHAR(64)  PRIMARY KEY,           -- 'maq-' + hex aleatorio
    secret_hash     VARCHAR(255) NOT NULL,              -- password_hash() del secreto
    empr_id         INTEGER      NOT NULL,              -- core.empresas.empr_id
    nombre          VARCHAR(255) NOT NULL DEFAULT '',
    activo          BOOLEAN      NOT NULL DEFAULT TRUE,  -- FALSE revoca en el acto
    fec_alta        TIMESTAMP    NOT NULL DEFAULT NOW(),
    fec_ultimo_uso  TIMESTAMP    NULL
);

CREATE INDEX IF NOT EXISTS idx_oauth_clients_empr ON seg.oauth_clients (empr_id) WHERE activo;

COMMENT ON TABLE  seg.oauth_clients                IS 'Clientes de maquina (client_credentials) de los agentes de IA. Uno por empresa. ADR-A10';
COMMENT ON COLUMN seg.oauth_clients.secret_hash    IS 'Hash bcrypt del secreto. El secreto en claro no se guarda en ningun lado de Dnato';
COMMENT ON COLUMN seg.oauth_clients.empr_id        IS 'Empresa del token. Sale de aca, nunca del pedido';
COMMENT ON COLUMN seg.oauth_clients.fec_ultimo_uso IS 'Ultima emision de token: sirve para detectar uso raro';
