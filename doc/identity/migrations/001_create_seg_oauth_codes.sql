-- Migración: E9-IDENT-03
-- Tabla: seg.oauth_codes
-- Propósito: almacenar authorization codes de un solo uso para el flujo OAuth 2.1 + PKCE.
--            Cada code expira a los 60 segundos y solo puede usarse una vez.
--
-- Ejecutar como superusuario o con permisos en el schema seg.

CREATE TABLE IF NOT EXISTS seg.oauth_codes (
    id_code        VARCHAR(64)  PRIMARY KEY,     -- hex aleatorio (bin2hex(random_bytes(32)))
    email          VARCHAR(255) NOT NULL,
    empr_id        INTEGER      NOT NULL,
    code_challenge VARCHAR(128) NOT NULL,         -- base64url(SHA256(code_verifier))
    redirect_uri   TEXT         NOT NULL,
    useridbpm      VARCHAR(64)  NOT NULL DEFAULT '', -- ID del usuario en Bonita
    groupbpm       VARCHAR(128) NOT NULL DEFAULT '', -- nombre del grupo Bonita (empresa sin prefijo)
    created_at     TIMESTAMP    NOT NULL DEFAULT NOW(),
    used_at        TIMESTAMP    NULL
);

-- Índice para limpiar codes viejos eficientemente
CREATE INDEX IF NOT EXISTS idx_oauth_codes_created_at ON seg.oauth_codes (created_at);

-- Comentarios de columnas
COMMENT ON TABLE  seg.oauth_codes            IS 'Authorization codes OAuth 2.1 PKCE — uso único, TTL 60s';
COMMENT ON COLUMN seg.oauth_codes.id_code    IS 'Código opaco de autorización (hex 32 bytes)';
COMMENT ON COLUMN seg.oauth_codes.empr_id    IS 'ID de empresa resuelta durante el login, viajará como claim del JWT';
COMMENT ON COLUMN seg.oauth_codes.code_challenge IS 'PKCE S256: base64url(SHA256(code_verifier))';
COMMENT ON COLUMN seg.oauth_codes.used_at    IS 'NULL si no fue canjeado; timestamp del canje para auditoría';
