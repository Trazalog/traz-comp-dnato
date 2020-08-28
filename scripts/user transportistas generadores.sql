ALTER TABLE log.solicitantes_transporte ADD user_id varchar NULL;
ALTER TABLE log.solicitantes_transporte ADD CONSTRAINT solicitantes_transporte_users_fk FOREIGN KEY (user_id) REFERENCES seg.users(email);


ALTER TABLE log.transportistas ADD user_id varchar NULL;
ALTER TABLE log.transportistas ADD CONSTRAINT transportistas_users_fk FOREIGN KEY (user_id) REFERENCES seg.users(email);
