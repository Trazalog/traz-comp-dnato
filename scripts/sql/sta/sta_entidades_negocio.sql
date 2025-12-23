-- DROP TABLE sta.entidades_negocio;

CREATE TABLE sta.entidades_negocio (
	nombre varchar(255) NOT NULL,
	stored_procedure varchar(255) NOT NULL,
	"template" text NOT NULL,
	fec_alta timestamp DEFAULT CURRENT_TIMESTAMP NULL,
	usuario varchar DEFAULT CURRENT_USER NOT NULL,
	CONSTRAINT entidades_negocio_pkey PRIMARY KEY (nombre)
);
CREATE INDEX idx_entidades_negocio_nombre ON sta.entidades_negocio USING btree (nombre);