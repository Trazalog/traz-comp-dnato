ERROR - 2020-07-27 16:23:13 --> Severity: error --> Exception: syntax error, unexpected '$res' (T_VARIABLE) D:\xampp\htdocs\login\application\models\Roles.php 14
DEBUG - 2020-07-27 16:23:41 --> UTF-8 Support Enabled
DEBUG - 2020-07-27 16:23:41 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-27 16:23:42 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-27 16:23:42 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
ERROR - 2020-07-27 16:24:06 --> Severity: Warning --> pg_query(): Query failed: ERROR:  relation &quot;seg.roles&quot; does not exist
LINE 2: FROM &quot;seg&quot;.&quot;roles&quot;
             ^ D:\xampp\htdocs\login\system\database\drivers\postgre\postgre_driver.php 242
ERROR - 2020-07-27 16:24:06 --> Query error: ERROR:  relation "seg.roles" does not exist
LINE 2: FROM "seg"."roles"
             ^ - Invalid query: SELECT *
FROM "seg"."roles"
WHERE "eliminado" = FALSE
ERROR - 2020-07-27 17:23:38 --> Severity: Error --> Uncaught Error: Call to a member function result_array() on boolean in D:\xampp\htdocs\login\application\models\Roles.php:14
Stack trace:
#0 D:\xampp\htdocs\login\application\controllers\Main.php(213): Roles->obtener()
#1 D:\xampp\htdocs\login\system\core\CodeIgniter.php(532): Main->changelevel()
#2 D:\xampp\htdocs\login\index.php(315): require_once('D:\\xampp\\htdocs...')
#3 {main}
  thrown D:\xampp\htdocs\login\application\models\Roles.php 14
DEBUG - 2020-07-27 17:23:50 --> UTF-8 Support Enabled
DEBUG - 2020-07-27 17:23:50 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-27 17:23:51 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-27 17:23:51 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
ERROR - 2020-07-27 17:24:14 --> Severity: Warning --> pg_query(): Query failed: ERROR:  operator does not exist: smallint = boolean
LINE 3: WHERE &quot;eliminado&quot; = FALSE
                          ^
HINT:  No operator matches the given name and argument types. You might need to add explicit type casts. D:\xampp\htdocs\login\system\database\drivers\postgre\postgre_driver.php 242
ERROR - 2020-07-27 17:24:14 --> Query error: ERROR:  operator does not exist: smallint = boolean
LINE 3: WHERE "eliminado" = FALSE
                          ^
HINT:  No operator matches the given name and argument types. You might need to add explicit type casts. - Invalid query: SELECT *
FROM "seg"."roles"
WHERE "eliminado" = FALSE
ERROR - 2020-07-27 17:25:03 --> Severity: Error --> Uncaught Error: Call to a member function result_array() on boolean in D:\xampp\htdocs\login\application\models\Roles.php:14
Stack trace:
#0 D:\xampp\htdocs\login\application\controllers\Main.php(213): Roles->obtener()
#1 D:\xampp\htdocs\login\system\core\CodeIgniter.php(532): Main->changelevel()
#2 D:\xampp\htdocs\login\index.php(315): require_once('D:\\xampp\\htdocs...')
#3 {main}
  thrown D:\xampp\htdocs\login\application\models\Roles.php 14
DEBUG - 2020-07-27 17:25:13 --> UTF-8 Support Enabled
DEBUG - 2020-07-27 17:25:13 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-27 17:25:14 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-27 17:25:14 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-27 17:26:05 --> UTF-8 Support Enabled
DEBUG - 2020-07-27 17:26:05 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-27 17:26:06 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-27 17:26:06 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-27 17:26:45 --> Total execution time: 39.9153
DEBUG - 2020-07-27 17:26:47 --> UTF-8 Support Enabled
DEBUG - 2020-07-27 17:26:47 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-27 17:26:48 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-27 17:27:15 --> Total execution time: 27.4769
DEBUG - 2020-07-27 17:32:21 --> UTF-8 Support Enabled
DEBUG - 2020-07-27 17:32:21 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-27 17:32:23 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-27 17:32:23 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-27 17:32:58 --> UTF-8 Support Enabled
DEBUG - 2020-07-27 17:32:58 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-27 17:32:59 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-27 17:32:59 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-27 17:33:00 --> Total execution time: 1.8471
DEBUG - 2020-07-27 17:33:03 --> UTF-8 Support Enabled
DEBUG - 2020-07-27 17:33:03 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-27 17:33:04 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-27 17:33:04 --> Total execution time: 1.2901
DEBUG - 2020-07-27 17:34:25 --> UTF-8 Support Enabled
DEBUG - 2020-07-27 17:34:25 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-27 17:34:26 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-27 17:34:26 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-27 17:34:27 --> Total execution time: 1.8391
DEBUG - 2020-07-27 17:34:30 --> UTF-8 Support Enabled
DEBUG - 2020-07-27 17:34:30 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-27 17:34:31 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-27 17:34:31 --> Total execution time: 1.3371
DEBUG - 2020-07-27 17:34:38 --> UTF-8 Support Enabled
DEBUG - 2020-07-27 17:34:38 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-27 17:34:39 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-27 17:34:39 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-27 17:35:55 --> UTF-8 Support Enabled
DEBUG - 2020-07-27 17:35:55 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-27 17:35:56 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-27 17:35:56 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-27 17:36:11 --> UTF-8 Support Enabled
DEBUG - 2020-07-27 17:36:11 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-27 17:36:12 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-27 17:36:12 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-27 17:37:56 --> UTF-8 Support Enabled
DEBUG - 2020-07-27 17:37:56 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-27 17:37:57 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-27 17:37:57 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-27 17:38:34 --> Total execution time: 37.8302
DEBUG - 2020-07-27 17:38:36 --> UTF-8 Support Enabled
DEBUG - 2020-07-27 17:38:36 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-27 17:38:37 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-27 17:38:53 --> Total execution time: 17.0069
DEBUG - 2020-07-27 17:42:45 --> UTF-8 Support Enabled
DEBUG - 2020-07-27 17:42:45 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-27 17:42:46 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-27 17:42:46 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-27 17:43:28 --> Total execution time: 42.5914
DEBUG - 2020-07-27 17:43:31 --> UTF-8 Support Enabled
DEBUG - 2020-07-27 17:43:31 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-27 17:43:32 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-27 17:44:26 --> Total execution time: 54.8318
DEBUG - 2020-07-27 17:47:47 --> UTF-8 Support Enabled
DEBUG - 2020-07-27 17:47:47 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-27 17:47:48 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
ERROR - 2020-07-27 17:47:54 --> Severity: Warning --> pg_query(): Query failed: ERROR:  null value in column &quot;rol_id&quot; violates not-null constraint
DETAIL:  Failing row contains (null, nom, adsfad, null, 0). D:\xampp\htdocs\login\system\database\drivers\postgre\postgre_driver.php 242
ERROR - 2020-07-27 17:47:54 --> Query error: ERROR:  null value in column "rol_id" violates not-null constraint
DETAIL:  Failing row contains (null, nom, adsfad, null, 0). - Invalid query: INSERT INTO "seg"."roles" ("nombre", "descripcion") VALUES ('nom', 'adsfad')
DEBUG - 2020-07-27 17:47:56 --> Total execution time: 9.7106
DEBUG - 2020-07-27 18:01:51 --> UTF-8 Support Enabled
DEBUG - 2020-07-27 18:01:51 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-27 18:01:52 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-27 18:01:52 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-27 18:01:52 --> Total execution time: 1.5411
DEBUG - 2020-07-27 18:02:13 --> UTF-8 Support Enabled
DEBUG - 2020-07-27 18:02:13 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-27 18:02:15 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-27 18:02:15 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-27 18:02:15 --> Total execution time: 1.6671
DEBUG - 2020-07-27 18:02:28 --> UTF-8 Support Enabled
DEBUG - 2020-07-27 18:02:28 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-27 18:02:29 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-27 18:02:29 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-27 18:02:31 --> Total execution time: 2.4821
DEBUG - 2020-07-27 18:02:51 --> UTF-8 Support Enabled
DEBUG - 2020-07-27 18:02:51 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-27 18:02:52 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-27 18:02:52 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-27 18:03:03 --> UTF-8 Support Enabled
DEBUG - 2020-07-27 18:03:03 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-27 18:03:05 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-27 18:03:05 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-27 18:03:05 --> Total execution time: 1.9911
DEBUG - 2020-07-27 18:03:07 --> UTF-8 Support Enabled
DEBUG - 2020-07-27 18:03:07 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-27 18:03:08 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-27 18:03:08 --> Total execution time: 1.5611
DEBUG - 2020-07-27 18:03:17 --> UTF-8 Support Enabled
DEBUG - 2020-07-27 18:03:17 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-27 18:03:18 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-27 18:03:19 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-27 21:03:19 --> Total execution time: 1.7951
DEBUG - 2020-07-27 18:03:46 --> UTF-8 Support Enabled
DEBUG - 2020-07-27 18:03:46 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-27 18:03:47 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-27 18:03:47 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-27 18:03:48 --> UTF-8 Support Enabled
DEBUG - 2020-07-27 18:03:48 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-27 18:03:49 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-27 18:03:49 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-27 21:03:50 --> Total execution time: 1.6741
DEBUG - 2020-07-27 18:04:21 --> UTF-8 Support Enabled
DEBUG - 2020-07-27 18:04:21 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-27 18:04:23 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-27 18:04:23 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-27 18:04:23 --> UTF-8 Support Enabled
DEBUG - 2020-07-27 18:04:23 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-27 18:04:25 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-27 18:04:25 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-27 21:04:25 --> Total execution time: 1.6551
DEBUG - 2020-07-27 18:06:12 --> UTF-8 Support Enabled
DEBUG - 2020-07-27 18:06:12 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-27 18:06:13 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-27 18:06:13 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-27 21:06:14 --> Total execution time: 1.6541
DEBUG - 2020-07-27 18:06:45 --> UTF-8 Support Enabled
DEBUG - 2020-07-27 18:06:45 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-27 18:06:47 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-27 18:06:47 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-27 18:06:47 --> Total execution time: 1.9411
DEBUG - 2020-07-27 18:06:49 --> UTF-8 Support Enabled
DEBUG - 2020-07-27 18:06:49 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-27 18:06:50 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-27 18:06:50 --> Total execution time: 1.3091
DEBUG - 2020-07-27 18:07:46 --> UTF-8 Support Enabled
DEBUG - 2020-07-27 18:07:46 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-27 18:07:48 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-27 18:07:48 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
ERROR - 2020-07-27 18:07:48 --> Severity: Warning --> pg_query(): Query failed: ERROR:  relation &quot;users&quot; does not exist
LINE 2: FROM &quot;users&quot;
             ^ D:\xampp\htdocs\login\system\database\drivers\postgre\postgre_driver.php 242
ERROR - 2020-07-27 18:07:48 --> Query error: ERROR:  relation "users" does not exist
LINE 2: FROM "users"
             ^ - Invalid query: SELECT *
FROM "users"
WHERE "id" = '1'
 LIMIT 1
ERROR - 2020-07-27 18:07:48 --> Severity: Warning --> pg_affected_rows() expects parameter 1 to be resource, boolean given D:\xampp\htdocs\login\system\database\drivers\postgre\postgre_driver.php 345
DEBUG - 2020-07-27 18:07:49 --> Total execution time: 2.0931
DEBUG - 2020-07-27 18:07:57 --> UTF-8 Support Enabled
DEBUG - 2020-07-27 18:07:57 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-27 18:07:58 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-27 18:07:58 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-27 18:07:58 --> #Main/index | {"__ci_last_regenerate":1595884052,"id":"1","email":"admin@gmail.com","first_name":"Lola","last_name":"Meraz","role":"1","last_login":"2020-07-17 03:40:22 PM","status":"approved","banned_users":"unban","passmd5":null}
DEBUG - 2020-07-27 18:07:58 --> #Main/index | Error de Redireccionamiento
DEBUG - 2020-07-27 18:07:58 --> Total execution time: 1.1271
DEBUG - 2020-07-27 18:08:01 --> UTF-8 Support Enabled
DEBUG - 2020-07-27 18:08:02 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-27 18:08:03 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-27 18:08:03 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
ERROR - 2020-07-27 18:08:03 --> Severity: Warning --> pg_query(): Query failed: ERROR:  relation &quot;users&quot; does not exist
LINE 2: FROM &quot;users&quot;
             ^ D:\xampp\htdocs\login\system\database\drivers\postgre\postgre_driver.php 242
ERROR - 2020-07-27 18:08:03 --> Query error: ERROR:  relation "users" does not exist
LINE 2: FROM "users"
             ^ - Invalid query: SELECT *
FROM "users"
WHERE "id" = '1'
 LIMIT 1
ERROR - 2020-07-27 18:08:03 --> Severity: Warning --> pg_affected_rows() expects parameter 1 to be resource, boolean given D:\xampp\htdocs\login\system\database\drivers\postgre\postgre_driver.php 345
DEBUG - 2020-07-27 18:08:04 --> Total execution time: 2.3161
DEBUG - 2020-07-27 18:09:46 --> UTF-8 Support Enabled
DEBUG - 2020-07-27 18:09:46 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-27 18:09:47 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-27 18:09:47 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
ERROR - 2020-07-27 18:09:47 --> Severity: Warning --> pg_query(): Query failed: ERROR:  relation &quot;users&quot; does not exist
LINE 2: FROM &quot;users&quot;
             ^ D:\xampp\htdocs\login\system\database\drivers\postgre\postgre_driver.php 242
ERROR - 2020-07-27 18:09:47 --> Query error: ERROR:  relation "users" does not exist
LINE 2: FROM "users"
             ^ - Invalid query: SELECT *
FROM "users"
WHERE "id" = '1'
 LIMIT 1
ERROR - 2020-07-27 18:09:47 --> Severity: Warning --> pg_affected_rows() expects parameter 1 to be resource, boolean given D:\xampp\htdocs\login\system\database\drivers\postgre\postgre_driver.php 345
DEBUG - 2020-07-27 18:09:48 --> Total execution time: 1.6961
DEBUG - 2020-07-27 18:09:53 --> UTF-8 Support Enabled
DEBUG - 2020-07-27 18:09:53 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-27 18:09:54 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-27 18:09:54 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-27 18:09:54 --> #Main/login | {"__ci_last_regenerate":1595884052,"id":"1","email":"admin@gmail.com","first_name":"Lola","last_name":"Meraz","role":"1","last_login":"2020-07-17 03:40:22 PM","status":"approved","banned_users":"unban","passmd5":null}
DEBUG - 2020-07-27 18:09:54 --> #Main/login Sesion Existente
DEBUG - 2020-07-27 18:13:08 --> UTF-8 Support Enabled
DEBUG - 2020-07-27 18:13:08 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-27 18:13:10 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-27 18:13:10 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
ERROR - 2020-07-27 18:13:10 --> Severity: Warning --> pg_query(): Query failed: ERROR:  relation &quot;users&quot; does not exist
LINE 2: FROM &quot;users&quot;
             ^ D:\xampp\htdocs\login\system\database\drivers\postgre\postgre_driver.php 242
ERROR - 2020-07-27 18:13:10 --> Query error: ERROR:  relation "users" does not exist
LINE 2: FROM "users"
             ^ - Invalid query: SELECT *
FROM "users"
WHERE "id" = '1'
 LIMIT 1
ERROR - 2020-07-27 18:13:10 --> Severity: Warning --> pg_affected_rows() expects parameter 1 to be resource, boolean given D:\xampp\htdocs\login\system\database\drivers\postgre\postgre_driver.php 345
DEBUG - 2020-07-27 18:13:10 --> Total execution time: 1.7411
DEBUG - 2020-07-27 18:13:25 --> UTF-8 Support Enabled
DEBUG - 2020-07-27 18:13:25 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-27 18:13:26 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-27 18:13:26 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-27 18:13:27 --> Total execution time: 1.7461
DEBUG - 2020-07-27 18:35:36 --> UTF-8 Support Enabled
DEBUG - 2020-07-27 18:35:36 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-27 18:35:38 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-27 18:35:38 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
ERROR - 2020-07-27 18:35:38 --> Severity: Warning --> pg_query(): Query failed: ERROR:  relation &quot;users&quot; does not exist
LINE 2: FROM &quot;users&quot;
             ^ D:\xampp\htdocs\login\system\database\drivers\postgre\postgre_driver.php 242
ERROR - 2020-07-27 18:35:38 --> Query error: ERROR:  relation "users" does not exist
LINE 2: FROM "users"
             ^ - Invalid query: SELECT *
FROM "users"
WHERE "id" = '1'
 LIMIT 1
ERROR - 2020-07-27 18:35:38 --> Severity: Warning --> pg_affected_rows() expects parameter 1 to be resource, boolean given D:\xampp\htdocs\login\system\database\drivers\postgre\postgre_driver.php 345
DEBUG - 2020-07-27 18:35:39 --> Total execution time: 2.3151
DEBUG - 2020-07-27 18:35:56 --> UTF-8 Support Enabled
DEBUG - 2020-07-27 18:35:56 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-27 18:35:57 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-27 18:35:57 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-27 18:35:58 --> Total execution time: 2.3121
DEBUG - 2020-07-27 18:37:20 --> UTF-8 Support Enabled
DEBUG - 2020-07-27 18:37:20 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-27 18:37:22 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-27 18:37:22 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-27 18:37:23 --> Total execution time: 2.3671
DEBUG - 2020-07-27 18:38:06 --> UTF-8 Support Enabled
DEBUG - 2020-07-27 18:38:06 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-27 18:38:07 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-27 18:38:07 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
ERROR - 2020-07-27 18:38:07 --> Severity: Warning --> pg_query(): Query failed: ERROR:  relation &quot;users&quot; does not exist
LINE 2: FROM &quot;users&quot;
             ^ D:\xampp\htdocs\login\system\database\drivers\postgre\postgre_driver.php 242
ERROR - 2020-07-27 18:38:07 --> Query error: ERROR:  relation "users" does not exist
LINE 2: FROM "users"
             ^ - Invalid query: SELECT *
FROM "users"
WHERE "id" = '1'
 LIMIT 1
ERROR - 2020-07-27 18:38:07 --> Severity: Warning --> pg_affected_rows() expects parameter 1 to be resource, boolean given D:\xampp\htdocs\login\system\database\drivers\postgre\postgre_driver.php 345
DEBUG - 2020-07-27 18:38:07 --> Total execution time: 1.9401
DEBUG - 2020-07-27 18:38:37 --> UTF-8 Support Enabled
DEBUG - 2020-07-27 18:38:37 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-27 18:38:38 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-27 18:38:38 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
ERROR - 2020-07-27 18:38:38 --> Severity: Warning --> pg_query(): Query failed: ERROR:  relation &quot;users&quot; does not exist
LINE 2: FROM &quot;users&quot;
             ^ D:\xampp\htdocs\login\system\database\drivers\postgre\postgre_driver.php 242
ERROR - 2020-07-27 18:38:38 --> Query error: ERROR:  relation "users" does not exist
LINE 2: FROM "users"
             ^ - Invalid query: SELECT *
FROM "users"
WHERE "id" = '1'
 LIMIT 1
ERROR - 2020-07-27 18:38:38 --> Severity: Warning --> pg_affected_rows() expects parameter 1 to be resource, boolean given D:\xampp\htdocs\login\system\database\drivers\postgre\postgre_driver.php 345
DEBUG - 2020-07-27 18:38:39 --> Total execution time: 1.7051
DEBUG - 2020-07-27 18:39:55 --> UTF-8 Support Enabled
DEBUG - 2020-07-27 18:39:55 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-27 18:39:56 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-27 18:39:56 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-27 18:39:57 --> Total execution time: 2.5921
DEBUG - 2020-07-27 18:40:29 --> UTF-8 Support Enabled
DEBUG - 2020-07-27 18:40:29 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-27 18:40:30 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-27 18:40:30 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-27 18:40:31 --> Total execution time: 2.0991
DEBUG - 2020-07-27 18:40:56 --> UTF-8 Support Enabled
DEBUG - 2020-07-27 18:40:56 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-27 18:40:57 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-27 18:40:57 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-27 18:40:58 --> Total execution time: 1.6881
DEBUG - 2020-07-27 19:59:04 --> UTF-8 Support Enabled
DEBUG - 2020-07-27 19:59:04 --> Global POST, GET and COOKIE data sanitized
ERROR - 2020-07-27 19:59:25 --> Severity: Warning --> pg_connect(): Unable to connect to PostgreSQL server: could not connect to server: Connection timed out (0x0000274C/10060)
	Is the server running on host &quot;10.142.0.7&quot; and accepting
	TCP/IP connections on port 5432? D:\xampp\htdocs\login\system\database\drivers\postgre\postgre_driver.php 154
ERROR - 2020-07-27 19:59:25 --> Unable to connect to the database
DEBUG - 2020-07-27 19:59:25 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-27 19:59:25 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
ERROR - 2020-07-27 19:59:46 --> Severity: Error --> Maximum execution time of 30 seconds exceeded D:\xampp\htdocs\login\system\core\Common.php 595
DEBUG - 2020-07-27 20:20:43 --> UTF-8 Support Enabled
DEBUG - 2020-07-27 20:20:43 --> Global POST, GET and COOKIE data sanitized
ERROR - 2020-07-27 20:21:05 --> Severity: Warning --> pg_connect(): Unable to connect to PostgreSQL server: could not connect to server: Connection timed out (0x0000274C/10060)
	Is the server running on host &quot;10.142.0.7&quot; and accepting
	TCP/IP connections on port 5432? D:\xampp\htdocs\login\system\database\drivers\postgre\postgre_driver.php 154
ERROR - 2020-07-27 20:21:05 --> Unable to connect to the database
DEBUG - 2020-07-27 20:21:05 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-27 20:21:05 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-27 20:21:05 --> #Main/index | {"__ci_last_regenerate":1595892065,"id":"1","email":"admin@gmail.com","first_name":"Lola","last_name":"Meraz","role":"1","last_login":"2020-07-17 03:40:22 PM","status":"approved","banned_users":"unban","passmd5":null}
DEBUG - 2020-07-27 20:21:05 --> #Main/index | Error de Redireccionamiento
DEBUG - 2020-07-27 20:21:06 --> Total execution time: 22.1983
DEBUG - 2020-07-27 20:21:21 --> UTF-8 Support Enabled
DEBUG - 2020-07-27 20:21:21 --> Global POST, GET and COOKIE data sanitized
ERROR - 2020-07-27 20:21:42 --> Severity: Warning --> pg_connect(): Unable to connect to PostgreSQL server: could not connect to server: Connection timed out (0x0000274C/10060)
	Is the server running on host &quot;10.142.0.7&quot; and accepting
	TCP/IP connections on port 5432? D:\xampp\htdocs\login\system\database\drivers\postgre\postgre_driver.php 154
ERROR - 2020-07-27 20:21:42 --> Unable to connect to the database
DEBUG - 2020-07-27 20:21:42 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-27 20:21:42 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-27 20:21:42 --> #Main/login | {"__ci_last_regenerate":1595892065,"id":"1","email":"admin@gmail.com","first_name":"Lola","last_name":"Meraz","role":"1","last_login":"2020-07-17 03:40:22 PM","status":"approved","banned_users":"unban","passmd5":null}
DEBUG - 2020-07-27 20:21:42 --> #Main/login Sesion Existente
DEBUG - 2020-07-27 20:22:24 --> UTF-8 Support Enabled
DEBUG - 2020-07-27 20:22:24 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-27 20:22:26 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-27 20:22:26 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-27 20:22:26 --> UTF-8 Support Enabled
DEBUG - 2020-07-27 20:22:26 --> Global POST, GET and COOKIE data sanitized
ERROR - 2020-07-27 20:22:26 --> 404 Page Not Found: Login/index
DEBUG - 2020-07-27 20:24:43 --> UTF-8 Support Enabled
DEBUG - 2020-07-27 20:24:44 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-27 20:24:45 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-27 20:24:45 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-27 20:24:45 --> #Main/login | {"__ci_last_regenerate":1595892285}
DEBUG - 2020-07-27 20:24:45 --> cURL Class Initialized
DEBUG - 2020-07-27 20:24:45 --> #Main/login | Carga Login |false| []
DEBUG - 2020-07-27 20:24:45 --> Total execution time: 1.7181
DEBUG - 2020-07-27 20:25:08 --> UTF-8 Support Enabled
DEBUG - 2020-07-27 20:25:08 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-27 20:25:09 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-27 20:25:09 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-27 20:25:09 --> #Main/login | {"__ci_last_regenerate":1595892285}
DEBUG - 2020-07-27 20:25:09 --> cURL Class Initialized
ERROR - 2020-07-27 20:25:10 --> Severity: Warning --> pg_query(): Query failed: ERROR:  relation &quot;users&quot; does not exist
LINE 1: UPDATE &quot;users&quot; SET &quot;last_login&quot; = '2020-07-27 08:25:10 PM'
               ^ D:\xampp\htdocs\login\system\database\drivers\postgre\postgre_driver.php 242
ERROR - 2020-07-27 20:25:10 --> Query error: ERROR:  relation "users" does not exist
LINE 1: UPDATE "users" SET "last_login" = '2020-07-27 08:25:10 PM'
               ^ - Invalid query: UPDATE "users" SET "last_login" = '2020-07-27 08:25:10 PM'
WHERE "id" = '1'
DEBUG - 2020-07-27 20:25:10 --> #Main/login | userInfo: {"id":"1","email":"admin@gmail.com","first_name":"Lola","last_name":"Meraz","role":"1","last_login":"2020-07-17 03:40:22 PM","status":"approved","banned_users":"unban","passmd5":null}
DEBUG - 2020-07-27 20:25:10 --> #Main/checkLoginUser/
DEBUG - 2020-07-27 20:47:17 --> UTF-8 Support Enabled
DEBUG - 2020-07-27 20:47:17 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-27 20:47:19 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-27 20:47:19 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-27 20:47:20 --> Total execution time: 2.1191
DEBUG - 2020-07-27 20:50:24 --> UTF-8 Support Enabled
DEBUG - 2020-07-27 20:50:24 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-27 20:50:25 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-27 20:50:25 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-27 20:50:26 --> Total execution time: 2.0481
