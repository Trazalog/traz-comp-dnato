-- sta.alm_lotes definition

-- Drop table

-- DROP TABLE sta.alm_lotes;

CREATE TABLE sta.alm_lotes (
	cod_articulo varchar NULL,
	numero_proveedor varchar NULL,
	numero_deposito varchar NULL,
	cantidad float8 NULL,
	procesado bool DEFAULT false NULL,
	fec_proceso date NULL,
	p_pesos float8 NULL,
	p_dolar float8 NULL
);


-- sta.articulos definition

-- Drop table

-- DROP TABLE sta.articulos;

CREATE TABLE sta.articulos (
	unidad_medida varchar NULL,
	codigo varchar NULL,
	descripcion varchar NULL,
	tipo varchar NULL,
	procesado bool DEFAULT false NULL,
	fec_proceso date NULL,
	punto_pedido int4 NULL
);


-- sta.articulos_etapas definition

-- Drop table

-- DROP TABLE sta.articulos_etapas;

CREATE TABLE sta.articulos_etapas (
	"Etapa" varchar NULL,
	"Entrada" varchar NULL,
	"Producto" varchar NULL,
	"Salida" varchar NULL,
	procesado bool DEFAULT false NULL,
	fec_proceso date NULL
);


-- sta.herramientas definition

-- Drop table

-- DROP TABLE sta.herramientas;

CREATE TABLE sta.herramientas (
	codigo varchar NULL,
	marca varchar NULL,
	modelo varchar NULL,
	tipo varchar NULL,
	descripcion varchar NULL,
	panol varchar NULL,
	procesado bool DEFAULT false NULL,
	fec_proceso timestamp NULL
);


-- sta.entidades_negocio definition

-- Drop table

-- DROP TABLE sta.entidades_negocio;

-- sta.entidades_negocio definition

-- Drop table

