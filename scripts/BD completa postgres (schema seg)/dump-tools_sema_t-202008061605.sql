PGDMP                         x            tools_sema_t    11.7    11.2 '               0    0    ENCODING    ENCODING        SET client_encoding = 'UTF8';
                       false                       0    0 
   STDSTRINGS 
   STDSTRINGS     (   SET standard_conforming_strings = 'on';
                       false                       0    0 
   SEARCHPATH 
   SEARCHPATH     8   SELECT pg_catalog.set_config('search_path', '', false);
                       false                       1262    19047    tools_sema_t    DATABASE     ~   CREATE DATABASE tools_sema_t WITH TEMPLATE = template0 ENCODING = 'UTF8' LC_COLLATE = 'en_US.UTF-8' LC_CTYPE = 'en_US.UTF-8';
    DROP DATABASE tools_sema_t;
             postgres    false            
            2615    32369    seg    SCHEMA        CREATE SCHEMA seg;
    DROP SCHEMA seg;
             postgres    false            C           1259    32675    memberships_menues    TABLE     D  CREATE TABLE seg.memberships_menues (
    modulo character varying NOT NULL,
    opcion character varying NOT NULL,
    "group" character varying,
    role character varying,
    fec_alta date DEFAULT now() NOT NULL,
    usuario character varying DEFAULT CURRENT_USER NOT NULL,
    usuario_app character varying NOT NULL
);
 #   DROP TABLE seg.memberships_menues;
       seg         postgres    false    10            B           1259    32642    memberships_users    TABLE     :  CREATE TABLE seg.memberships_users (
    "group" character varying NOT NULL,
    role character varying NOT NULL,
    fec_alta character varying DEFAULT now() NOT NULL,
    usuario character varying DEFAULT CURRENT_USER NOT NULL,
    usuario_app character varying NOT NULL,
    email character varying NOT NULL
);
 "   DROP TABLE seg.memberships_users;
       seg         postgres    false    10            D           1259    32688    menues    TABLE     �  CREATE TABLE seg.menues (
    modulo character varying(4) NOT NULL,
    opcion character varying NOT NULL,
    texto character varying NOT NULL,
    url character varying,
    javascript character varying,
    orden integer DEFAULT 0,
    url_icono character varying,
    texto_onmouseover character varying,
    eliminado smallint DEFAULT 0,
    fec_alta character varying DEFAULT now() NOT NULL,
    usuario character varying DEFAULT CURRENT_USER NOT NULL,
    usuario_app character varying NOT NULL,
    opcion_padre character varying,
    CONSTRAINT menues_check CHECK ((((modulo)::text = 'PRD'::text) OR ((modulo)::text = 'CORE'::text) OR ((modulo)::text = 'ALM'::text) OR ((modulo)::text = 'MAN'::text) OR ((modulo)::text = 'TAR'::text) OR ((modulo)::text = 'PAN'::text) OR ((modulo)::text = 'LOG'::text) OR ((modulo)::text = 'SEG'::text) OR ((modulo)::text = 'TRZ'::text) OR ((modulo)::text = 'PRO'::text) OR ((modulo)::text = 'FIS'::text)))
);
    DROP TABLE seg.menues;
       seg         postgres    false    10            E           1259    32758    roles    TABLE     �   CREATE TABLE seg.roles (
    rol_id integer NOT NULL,
    nombre character varying,
    descripcion character varying,
    fec_alta date,
    eliminado smallint DEFAULT 0 NOT NULL
);
    DROP TABLE seg.roles;
       seg         postgres    false    10            ;           1259    32370    settings    TABLE     �   CREATE TABLE seg.settings (
    id integer NOT NULL,
    site_title character varying(50) NOT NULL,
    timezone character varying(100) NOT NULL,
    recaptcha character varying(5) NOT NULL,
    theme character varying(100) NOT NULL
);
    DROP TABLE seg.settings;
       seg         postgres    false    10            <           1259    32373    settings_id_seq    SEQUENCE     �   CREATE SEQUENCE seg.settings_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;
 #   DROP SEQUENCE seg.settings_id_seq;
       seg       postgres    false    10    315                       0    0    settings_id_seq    SEQUENCE OWNED BY     =   ALTER SEQUENCE seg.settings_id_seq OWNED BY seg.settings.id;
            seg       postgres    false    316            =           1259    32375    tokens    TABLE     �   CREATE TABLE seg.tokens (
    id integer NOT NULL,
    token character varying(255) NOT NULL,
    user_id bigint NOT NULL,
    created date NOT NULL
);
    DROP TABLE seg.tokens;
       seg         postgres    false    10            >           1259    32378    tokens_id_seq    SEQUENCE     �   CREATE SEQUENCE seg.tokens_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;
 !   DROP SEQUENCE seg.tokens_id_seq;
       seg       postgres    false    317    10                       0    0    tokens_id_seq    SEQUENCE OWNED BY     9   ALTER SEQUENCE seg.tokens_id_seq OWNED BY seg.tokens.id;
            seg       postgres    false    318            ?           1259    32380    users    TABLE     �  CREATE TABLE seg.users (
    id integer NOT NULL,
    email character varying(100),
    first_name character varying(100),
    last_name character varying(100),
    role character varying(10),
    password text,
    last_login character varying(100),
    status character varying(100),
    banned_users character varying(100),
    passmd5 text,
    telefono character varying,
    dni character varying,
    usernick character varying
);
    DROP TABLE seg.users;
       seg         postgres    false    10            @           1259    32386    users_id_seq    SEQUENCE     �   CREATE SEQUENCE seg.users_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;
     DROP SEQUENCE seg.users_id_seq;
       seg       postgres    false    10    319                       0    0    users_id_seq    SEQUENCE OWNED BY     7   ALTER SEQUENCE seg.users_id_seq OWNED BY seg.users.id;
            seg       postgres    false    320            s           2604    32388    settings id    DEFAULT     d   ALTER TABLE ONLY seg.settings ALTER COLUMN id SET DEFAULT nextval('seg.settings_id_seq'::regclass);
 7   ALTER TABLE seg.settings ALTER COLUMN id DROP DEFAULT;
       seg       postgres    false    316    315            t           2604    32389 	   tokens id    DEFAULT     `   ALTER TABLE ONLY seg.tokens ALTER COLUMN id SET DEFAULT nextval('seg.tokens_id_seq'::regclass);
 5   ALTER TABLE seg.tokens ALTER COLUMN id DROP DEFAULT;
       seg       postgres    false    318    317            u           2604    32390    users id    DEFAULT     ^   ALTER TABLE ONLY seg.users ALTER COLUMN id SET DEFAULT nextval('seg.users_id_seq'::regclass);
 4   ALTER TABLE seg.users ALTER COLUMN id DROP DEFAULT;
       seg       postgres    false    320    319                      0    32675    memberships_menues 
   TABLE DATA               h   COPY seg.memberships_menues (modulo, opcion, "group", role, fec_alta, usuario, usuario_app) FROM stdin;
    seg       postgres    false    323                      0    32642    memberships_users 
   TABLE DATA               ^   COPY seg.memberships_users ("group", role, fec_alta, usuario, usuario_app, email) FROM stdin;
    seg       postgres    false    322                      0    32688    menues 
   TABLE DATA               �   COPY seg.menues (modulo, opcion, texto, url, javascript, orden, url_icono, texto_onmouseover, eliminado, fec_alta, usuario, usuario_app, opcion_padre) FROM stdin;
    seg       postgres    false    324                      0    32758    roles 
   TABLE DATA               N   COPY seg.roles (rol_id, nombre, descripcion, fec_alta, eliminado) FROM stdin;
    seg       postgres    false    325                      0    32370    settings 
   TABLE DATA               K   COPY seg.settings (id, site_title, timezone, recaptcha, theme) FROM stdin;
    seg       postgres    false    315                      0    32375    tokens 
   TABLE DATA               :   COPY seg.tokens (id, token, user_id, created) FROM stdin;
    seg       postgres    false    317            
          0    32380    users 
   TABLE DATA               �   COPY seg.users (id, email, first_name, last_name, role, password, last_login, status, banned_users, passmd5, telefono, dni, usernick) FROM stdin;
    seg       postgres    false    319                       0    0    settings_id_seq    SEQUENCE SET     ;   SELECT pg_catalog.setval('seg.settings_id_seq', 1, false);
            seg       postgres    false    316                       0    0    tokens_id_seq    SEQUENCE SET     9   SELECT pg_catalog.setval('seg.tokens_id_seq', 1, false);
            seg       postgres    false    318                       0    0    users_id_seq    SEQUENCE SET     8   SELECT pg_catalog.setval('seg.users_id_seq', 16, true);
            seg       postgres    false    320            �           2606    32778     memberships_users memberships_pk 
   CONSTRAINT     m   ALTER TABLE ONLY seg.memberships_users
    ADD CONSTRAINT memberships_pk PRIMARY KEY ("group", role, email);
 G   ALTER TABLE ONLY seg.memberships_users DROP CONSTRAINT memberships_pk;
       seg         postgres    false    322    322    322            �           2606    32700    menues menues_pk 
   CONSTRAINT     W   ALTER TABLE ONLY seg.menues
    ADD CONSTRAINT menues_pk PRIMARY KEY (modulo, opcion);
 7   ALTER TABLE ONLY seg.menues DROP CONSTRAINT menues_pk;
       seg         postgres    false    324    324            �           2606    32392    tokens tokens_pk 
   CONSTRAINT     K   ALTER TABLE ONLY seg.tokens
    ADD CONSTRAINT tokens_pk PRIMARY KEY (id);
 7   ALTER TABLE ONLY seg.tokens DROP CONSTRAINT tokens_pk;
       seg         postgres    false    317            �           2606    32394    users users_pk 
   CONSTRAINT     I   ALTER TABLE ONLY seg.users
    ADD CONSTRAINT users_pk PRIMARY KEY (id);
 5   ALTER TABLE ONLY seg.users DROP CONSTRAINT users_pk;
       seg         postgres    false    319            �           2606    32767    users users_un 
   CONSTRAINT     G   ALTER TABLE ONLY seg.users
    ADD CONSTRAINT users_un UNIQUE (email);
 5   ALTER TABLE ONLY seg.users DROP CONSTRAINT users_un;
       seg         postgres    false    319            �           2606    32706 6   memberships_menues memberships_menues_modulo_opcion_fk    FK CONSTRAINT     �   ALTER TABLE ONLY seg.memberships_menues
    ADD CONSTRAINT memberships_menues_modulo_opcion_fk FOREIGN KEY (modulo, opcion) REFERENCES seg.menues(modulo, opcion);
 ]   ALTER TABLE ONLY seg.memberships_menues DROP CONSTRAINT memberships_menues_modulo_opcion_fk;
       seg       postgres    false    324    323    323    324    3977            �           2606    32784 &   memberships_users memberships_users_fk    FK CONSTRAINT     �   ALTER TABLE ONLY seg.memberships_users
    ADD CONSTRAINT memberships_users_fk FOREIGN KEY (email) REFERENCES seg.users(email);
 M   ALTER TABLE ONLY seg.memberships_users DROP CONSTRAINT memberships_users_fk;
       seg       postgres    false    322    3973    319            �           2606    32701    menues menues_opcion_padre_fk    FK CONSTRAINT     �   ALTER TABLE ONLY seg.menues
    ADD CONSTRAINT menues_opcion_padre_fk FOREIGN KEY (modulo, opcion_padre) REFERENCES seg.menues(modulo, opcion);
 D   ALTER TABLE ONLY seg.menues DROP CONSTRAINT menues_opcion_padre_fk;
       seg       postgres    false    324    324    324    3977    324               W   x�u�1� @ѹ�Y�م�4�)z��p�y?��DYj�,�a(��+�,�:k�a��/-�hՎ�@w����I�_�>_ �           x�m�Kn� ��5>�ʈ��mV�T��v3ƈ a� /�k��X�3������
��d��Ӗ�-g�1%JP��;ٷT�-�b�ɨ��ɂ���x,	��%:��V����-'��$��At�,�G���_�M�q1������1���)������|'Nu�b�[�	58��	?���:d��Nёp1�p���iK���_�����{�?� �M|}�c(0{s�I̩�Bс�N�����1�U�4M��         �  x��T�R�0<?�"?��Z�[G/V��x��i�<&����̐����ɣ�.�K��5T��FE����S��C�*w��i�S[X}�MA,���o9�<�>��	K��/��l�gg��	�AE��YÆri�t���nX�(�v7Vݪ�=����B�^�C��J�J%�%X�ڵ�i�ĺ�6P�+V��?sY�q�M�2��s/a��ojka��՞���V�U�V��k��]-����`C'�%�\&��d�H��*�Q���Ri�@��9�(�=TN�Q4*O�(K����+L�0��֌��'����w��$��=���J���cBo�(���n�<?hu��J��dq]c}�.�+#Ѱ�w<|ŷQ���k��Ǥ�Ȏ�d����3�>�
�j���1�N�������a���Ch�8P�'��iE�x�y�         {   x�3�tL������4202�50�5��4�2�t,-��/0�tM�,�"a�\�T�\����)i�Y�Z\!P$�@b�`	CTs�W�`N�`GT	����Ģ�|׊�Ԣ�|N���P>F��� �1         j   x�=ʱ
�@����a���͵t�*H��7$�KD������=�ۖ�h�d��	���E	?��k'Mji����(t�0��G��l��8(x�h����v ��s9�/�U(�            x������ � �      
   �  x����r�0���S��$���W	1�z��0�l� #��~�.�y���L��:=ҌF��|�7g��q�є�-EF�S`��6 �"�X�t!��ͤt����A[���a���ܸ�4���{��Ĝo`UZ�/S��XR���D��'�!���{��(�
�1�Ll@��\X�݂���s�R@����<�e�D�X�1�j-���?'m��N�eEE��RgÓ0�`�WlA�LW4c�)�s�1_ӖW���,� f��+2������(�'�a���?Z}䓙�x3�82/HD�'øI<�Y�KST�w��	!�:��,�{d6=L�j����H?lx>��"q5�ڱE��n��~\�?�p�f��g��,!�V�U�q{��ߋ4����1E�y0.��/xR��0�qX4�C��]߆!���7��3��4�t�q%ۥ�l3����a��W�����vc�B-��hͻ���i��D�CS5���c��A�^�$I��y�      '               0    0    ENCODING    ENCODING        SET client_encoding = 'UTF8';
                       false                       0    0 
   STDSTRINGS 
   STDSTRINGS     (   SET standard_conforming_strings = 'on';
                       false                       0    0 
   SEARCHPATH 
   SEARCHPATH     8   SELECT pg_catalog.set_config('search_path', '', false);
                       false                       1262    19047    tools_sema_t    DATABASE     ~   CREATE DATABASE tools_sema_t WITH TEMPLATE = template0 ENCODING = 'UTF8' LC_COLLATE = 'en_US.UTF-8' LC_CTYPE = 'en_US.UTF-8';
    DROP DATABASE tools_sema_t;
             postgres    false            
            2615    32369    seg    SCHEMA        CREATE SCHEMA seg;
    DROP SCHEMA seg;
             postgres    false            C           1259    32675    memberships_menues    TABLE     D  CREATE TABLE seg.memberships_menues (
    modulo character varying NOT NULL,
    opcion character varying NOT NULL,
    "group" character varying,
    role character varying,
    fec_alta date DEFAULT now() NOT NULL,
    usuario character varying DEFAULT CURRENT_USER NOT NULL,
    usuario_app character varying NOT NULL
);
 #   DROP TABLE seg.memberships_menues;
       seg         postgres    false    10            B           1259    32642    memberships_users    TABLE     :  CREATE TABLE seg.memberships_users (
    "group" character varying NOT NULL,
    role character varying NOT NULL,
    fec_alta character varying DEFAULT now() NOT NULL,
    usuario character varying DEFAULT CURRENT_USER NOT NULL,
    usuario_app character varying NOT NULL,
    email character varying NOT NULL
);
 "   DROP TABLE seg.memberships_users;
       seg         postgres    false    10            D           1259    32688    menues    TABLE     �  CREATE TABLE seg.menues (
    modulo character varying(4) NOT NULL,
    opcion character varying NOT NULL,
    texto character varying NOT NULL,
    url character varying,
    javascript character varying,
    orden integer DEFAULT 0,
    url_icono character varying,
    texto_onmouseover character varying,
    eliminado smallint DEFAULT 0,
    fec_alta character varying DEFAULT now() NOT NULL,
    usuario character varying DEFAULT CURRENT_USER NOT NULL,
    usuario_app character varying NOT NULL,
    opcion_padre character varying,
    CONSTRAINT menues_check CHECK ((((modulo)::text = 'PRD'::text) OR ((modulo)::text = 'CORE'::text) OR ((modulo)::text = 'ALM'::text) OR ((modulo)::text = 'MAN'::text) OR ((modulo)::text = 'TAR'::text) OR ((modulo)::text = 'PAN'::text) OR ((modulo)::text = 'LOG'::text) OR ((modulo)::text = 'SEG'::text) OR ((modulo)::text = 'TRZ'::text) OR ((modulo)::text = 'PRO'::text) OR ((modulo)::text = 'FIS'::text)))
);
    DROP TABLE seg.menues;
       seg         postgres    false    10            E           1259    32758    roles    TABLE     �   CREATE TABLE seg.roles (
    rol_id integer NOT NULL,
    nombre character varying,
    descripcion character varying,
    fec_alta date,
    eliminado smallint DEFAULT 0 NOT NULL
);
    DROP TABLE seg.roles;
       seg         postgres    false    10            ;           1259    32370    settings    TABLE     �   CREATE TABLE seg.settings (
    id integer NOT NULL,
    site_title character varying(50) NOT NULL,
    timezone character varying(100) NOT NULL,
    recaptcha character varying(5) NOT NULL,
    theme character varying(100) NOT NULL
);
    DROP TABLE seg.settings;
       seg         postgres    false    10            <           1259    32373    settings_id_seq    SEQUENCE     �   CREATE SEQUENCE seg.settings_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;
 #   DROP SEQUENCE seg.settings_id_seq;
       seg       postgres    false    10    315                       0    0    settings_id_seq    SEQUENCE OWNED BY     =   ALTER SEQUENCE seg.settings_id_seq OWNED BY seg.settings.id;
            seg       postgres    false    316            =           1259    32375    tokens    TABLE     �   CREATE TABLE seg.tokens (
    id integer NOT NULL,
    token character varying(255) NOT NULL,
    user_id bigint NOT NULL,
    created date NOT NULL
);
    DROP TABLE seg.tokens;
       seg         postgres    false    10            >           1259    32378    tokens_id_seq    SEQUENCE     �   CREATE SEQUENCE seg.tokens_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;
 !   DROP SEQUENCE seg.tokens_id_seq;
       seg       postgres    false    317    10                       0    0    tokens_id_seq    SEQUENCE OWNED BY     9   ALTER SEQUENCE seg.tokens_id_seq OWNED BY seg.tokens.id;
            seg       postgres    false    318            ?           1259    32380    users    TABLE     �  CREATE TABLE seg.users (
    id integer NOT NULL,
    email character varying(100),
    first_name character varying(100),
    last_name character varying(100),
    role character varying(10),
    password text,
    last_login character varying(100),
    status character varying(100),
    banned_users character varying(100),
    passmd5 text,
    telefono character varying,
    dni character varying,
    usernick character varying
);
    DROP TABLE seg.users;
       seg         postgres    false    10            @           1259    32386    users_id_seq    SEQUENCE     �   CREATE SEQUENCE seg.users_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;
     DROP SEQUENCE seg.users_id_seq;
       seg       postgres    false    10    319                       0    0    users_id_seq    SEQUENCE OWNED BY     7   ALTER SEQUENCE seg.users_id_seq OWNED BY seg.users.id;
            seg       postgres    false    320            s           2604    32388    settings id    DEFAULT     d   ALTER TABLE ONLY seg.settings ALTER COLUMN id SET DEFAULT nextval('seg.settings_id_seq'::regclass);
 7   ALTER TABLE seg.settings ALTER COLUMN id DROP DEFAULT;
       seg       postgres    false    316    315            t           2604    32389 	   tokens id    DEFAULT     `   ALTER TABLE ONLY seg.tokens ALTER COLUMN id SET DEFAULT nextval('seg.tokens_id_seq'::regclass);
 5   ALTER TABLE seg.tokens ALTER COLUMN id DROP DEFAULT;
       seg       postgres    false    318    317            u           2604    32390    users id    DEFAULT     ^   ALTER TABLE ONLY seg.users ALTER COLUMN id SET DEFAULT nextval('seg.users_id_seq'::regclass);
 4   ALTER TABLE seg.users ALTER COLUMN id DROP DEFAULT;
       seg       postgres    false    320    319                      0    32675    memberships_menues 
   TABLE DATA               h   COPY seg.memberships_menues (modulo, opcion, "group", role, fec_alta, usuario, usuario_app) FROM stdin;
    seg       postgres    false    323   �                 0    32642    memberships_users 
   TABLE DATA               ^   COPY seg.memberships_users ("group", role, fec_alta, usuario, usuario_app, email) FROM stdin;
    seg       postgres    false    322   W                 0    32688    menues 
   TABLE DATA               �   COPY seg.menues (modulo, opcion, texto, url, javascript, orden, url_icono, texto_onmouseover, eliminado, fec_alta, usuario, usuario_app, opcion_padre) FROM stdin;
    seg       postgres    false    324   k                  0    32758    roles 
   TABLE DATA               N   COPY seg.roles (rol_id, nombre, descripcion, fec_alta, eliminado) FROM stdin;
    seg       postgres    false    325   &                 0    32370    settings 
   TABLE DATA               K   COPY seg.settings (id, site_title, timezone, recaptcha, theme) FROM stdin;
    seg       postgres    false    315   �                 0    32375    tokens 
   TABLE DATA               :   COPY seg.tokens (id, token, user_id, created) FROM stdin;
    seg       postgres    false    317   +       
          0    32380    users 
   TABLE DATA               �   COPY seg.users (id, email, first_name, last_name, role, password, last_login, status, banned_users, passmd5, telefono, dni, usernick) FROM stdin;
    seg       postgres    false    319   H                  0    0    settings_id_seq    SEQUENCE SET     ;   SELECT pg_catalog.setval('seg.settings_id_seq', 1, false);
            seg       postgres    false    316                       0    0    tokens_id_seq    SEQUENCE SET     9   SELECT pg_catalog.setval('seg.tokens_id_seq', 1, false);
            seg       postgres    false    318                       0    0    users_id_seq    SEQUENCE SET     8   SELECT pg_catalog.setval('seg.users_id_seq', 16, true);
            seg       postgres    false    320            �           2606    32778     memberships_users memberships_pk 
   CONSTRAINT     m   ALTER TABLE ONLY seg.memberships_users
    ADD CONSTRAINT memberships_pk PRIMARY KEY ("group", role, email);
 G   ALTER TABLE ONLY seg.memberships_users DROP CONSTRAINT memberships_pk;
       seg         postgres    false    322    322    322            �           2606    32700    menues menues_pk 
   CONSTRAINT     W   ALTER TABLE ONLY seg.menues
    ADD CONSTRAINT menues_pk PRIMARY KEY (modulo, opcion);
 7   ALTER TABLE ONLY seg.menues DROP CONSTRAINT menues_pk;
       seg         postgres    false    324    324            �           2606    32392    tokens tokens_pk 
   CONSTRAINT     K   ALTER TABLE ONLY seg.tokens
    ADD CONSTRAINT tokens_pk PRIMARY KEY (id);
 7   ALTER TABLE ONLY seg.tokens DROP CONSTRAINT tokens_pk;
       seg         postgres    false    317            �           2606    32394    users users_pk 
   CONSTRAINT     I   ALTER TABLE ONLY seg.users
    ADD CONSTRAINT users_pk PRIMARY KEY (id);
 5   ALTER TABLE ONLY seg.users DROP CONSTRAINT users_pk;
       seg         postgres    false    319            �           2606    32767    users users_un 
   CONSTRAINT     G   ALTER TABLE ONLY seg.users
    ADD CONSTRAINT users_un UNIQUE (email);
 5   ALTER TABLE ONLY seg.users DROP CONSTRAINT users_un;
       seg         postgres    false    319            �           2606    32706 6   memberships_menues memberships_menues_modulo_opcion_fk    FK CONSTRAINT     �   ALTER TABLE ONLY seg.memberships_menues
    ADD CONSTRAINT memberships_menues_modulo_opcion_fk FOREIGN KEY (modulo, opcion) REFERENCES seg.menues(modulo, opcion);
 ]   ALTER TABLE ONLY seg.memberships_menues DROP CONSTRAINT memberships_menues_modulo_opcion_fk;
       seg       postgres    false    324    323    323    324    3977            �           2606    32784 &   memberships_users memberships_users_fk    FK CONSTRAINT     �   ALTER TABLE ONLY seg.memberships_users
    ADD CONSTRAINT memberships_users_fk FOREIGN KEY (email) REFERENCES seg.users(email);
 M   ALTER TABLE ONLY seg.memberships_users DROP CONSTRAINT memberships_users_fk;
       seg       postgres    false    322    3973    319            �           2606    32701    menues menues_opcion_padre_fk    FK CONSTRAINT     �   ALTER TABLE ONLY seg.menues
    ADD CONSTRAINT menues_opcion_padre_fk FOREIGN KEY (modulo, opcion_padre) REFERENCES seg.menues(modulo, opcion);
 D   ALTER TABLE ONLY seg.menues DROP CONSTRAINT menues_opcion_padre_fk;
       seg       postgres    false    324    324    324    3977    324           