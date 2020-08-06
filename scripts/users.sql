ALTER TABLE seg.users ADD telefono varchar NULL;
ALTER TABLE seg.users ADD dni varchar NULL;
ALTER TABLE seg.users ADD usernick varchar NOT NULL;
ALTER TABLE seg.users ADD CONSTRAINT users_un UNIQUE (email);

ALTER TABLE seg.memberships_users RENAME COLUMN user_id TO email;
ALTER TABLE seg.memberships_users ALTER COLUMN email TYPE varchar USING email::varchar;

ALTER TABLE seg.memberships_users DROP CONSTRAINT memberships_user_id_fk;
ALTER TABLE seg.memberships_users ADD CONSTRAINT memberships_users_fk FOREIGN KEY (email) REFERENCES seg.users(email);


