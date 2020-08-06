<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

DEBUG - 2020-07-29 10:28:00 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 10:28:01 --> Global POST, GET and COOKIE data sanitized
ERROR - 2020-07-29 10:28:23 --> Severity: Warning --> pg_connect(): Unable to connect to PostgreSQL server: could not connect to server: Connection timed out (0x0000274C/10060)
	Is the server running on host &quot;10.142.0.7&quot; and accepting
	TCP/IP connections on port 5432? D:\xampp\htdocs\login\system\database\drivers\postgre\postgre_driver.php 154
ERROR - 2020-07-29 10:28:23 --> Unable to connect to the database
DEBUG - 2020-07-29 10:28:23 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
ERROR - 2020-07-29 10:28:27 --> Severity: Error --> Maximum execution time of 30 seconds exceeded D:\xampp\htdocs\login\application\libraries\Userlevel.php 10
DEBUG - 2020-07-29 10:28:44 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 10:28:44 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 10:28:47 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 10:28:47 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-29 10:28:47 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 10:28:47 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 10:28:49 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 10:28:49 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-29 10:28:49 --> #Main/login | {"__ci_last_regenerate":1596029305}
DEBUG - 2020-07-29 10:28:49 --> cURL Class Initialized
DEBUG - 2020-07-29 10:28:49 --> #Main/login | Carga Login |false| []
DEBUG - 2020-07-29 10:28:50 --> Total execution time: 2.1311
DEBUG - 2020-07-29 10:29:10 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 10:29:11 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 10:29:12 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 10:29:12 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-29 10:29:12 --> #Main/login | {"__ci_last_regenerate":1596029305}
DEBUG - 2020-07-29 10:29:12 --> cURL Class Initialized
ERROR - 2020-07-29 10:29:12 --> Severity: Warning --> pg_query(): Query failed: ERROR:  relation &quot;users&quot; does not exist
LINE 1: UPDATE &quot;users&quot; SET &quot;last_login&quot; = '2020-07-29 10:29:12 AM'
               ^ D:\xampp\htdocs\login\system\database\drivers\postgre\postgre_driver.php 242
ERROR - 2020-07-29 10:29:12 --> Query error: ERROR:  relation "users" does not exist
LINE 1: UPDATE "users" SET "last_login" = '2020-07-29 10:29:12 AM'
               ^ - Invalid query: UPDATE "users" SET "last_login" = '2020-07-29 10:29:12 AM'
WHERE "id" = '1'
DEBUG - 2020-07-29 10:29:12 --> #Main/login | userInfo: {"id":"1","email":"admin@gmail.com","first_name":"Lola","last_name":"Meraz","role":"1","last_login":"2020-07-17 03:40:22 PM","status":"approved","banned_users":"unban","passmd5":null,"telefono":null,"dni":null}
DEBUG - 2020-07-29 10:29:12 --> #Main/checkLoginUser/
DEBUG - 2020-07-29 10:29:39 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 10:29:39 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 10:29:43 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 10:29:43 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-29 10:29:44 --> Total execution time: 5.1963
DEBUG - 2020-07-29 10:30:27 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 10:30:27 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 10:30:28 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 10:30:28 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-29 10:30:29 --> Total execution time: 2.4171
DEBUG - 2020-07-29 10:30:31 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 10:30:31 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 10:30:33 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 10:30:33 --> Total execution time: 2.3776
DEBUG - 2020-07-29 10:30:54 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 10:30:54 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 10:30:56 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 10:30:56 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-29 10:30:56 --> Total execution time: 2.4321
DEBUG - 2020-07-29 10:44:56 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 10:44:56 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 10:44:57 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 10:44:57 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
ERROR - 2020-07-29 10:44:58 --> #TRAZA|USER_MODEL|associateBPMrol($data_rol) >> ERROR -> ""
DEBUG - 2020-07-29 10:44:58 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 10:44:58 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 10:45:00 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 10:45:00 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-29 10:45:01 --> Total execution time: 2.5171
DEBUG - 2020-07-29 10:45:23 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 10:45:23 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 10:45:24 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 10:45:24 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-29 10:45:25 --> Total execution time: 1.9841
DEBUG - 2020-07-29 10:46:09 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 10:46:09 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 10:46:11 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 10:46:11 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
ERROR - 2020-07-29 10:46:33 --> #TRAZA|USER_MODEL|associateBPMrol($data_rol) >> ERROR -> ""
DEBUG - 2020-07-29 10:55:30 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 10:55:30 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 10:55:31 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 10:55:31 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-29 10:55:32 --> Total execution time: 2.6582
DEBUG - 2020-07-29 11:00:26 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 11:00:26 --> Global POST, GET and COOKIE data sanitized
ERROR - 2020-07-29 11:00:47 --> Severity: Warning --> pg_connect(): Unable to connect to PostgreSQL server: could not connect to server: Connection timed out (0x0000274C/10060)
	Is the server running on host &quot;10.142.0.7&quot; and accepting
	TCP/IP connections on port 5432? D:\xampp\htdocs\login\system\database\drivers\postgre\postgre_driver.php 154
ERROR - 2020-07-29 11:00:47 --> Unable to connect to the database
DEBUG - 2020-07-29 11:00:47 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 11:00:47 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
ERROR - 2020-07-29 11:00:59 --> Severity: Warning --> pg_escape_literal() expects parameter 1 to be resource, boolean given D:\xampp\htdocs\login\system\database\drivers\postgre\postgre_driver.php 326
ERROR - 2020-07-29 11:01:17 --> Severity: Warning --> pg_connect(): Unable to connect to PostgreSQL server: could not connect to server: Software caused connection abort (0x00002745/10053)
	Is the server running on host &quot;10.142.0.7&quot; and accepting
	TCP/IP connections on port 5432? D:\xampp\htdocs\login\system\database\drivers\postgre\postgre_driver.php 154
ERROR - 2020-07-29 11:01:17 --> Unable to connect to the database
ERROR - 2020-07-29 11:01:17 --> Severity: Warning --> pg_last_error() expects parameter 1 to be resource, boolean given D:\xampp\htdocs\login\system\database\drivers\postgre\postgre_driver.php 480
ERROR - 2020-07-29 11:01:17 --> Query error:  - Invalid query: SELECT *
FROM "seg"."users"
WHERE "email" =
 LIMIT 1
ERROR - 2020-07-29 11:01:17 --> Severity: Warning --> pg_affected_rows() expects parameter 1 to be resource, boolean given D:\xampp\htdocs\login\system\database\drivers\postgre\postgre_driver.php 345
ERROR - 2020-07-29 11:01:56 --> Severity: Warning --> pg_escape_literal() expects parameter 1 to be resource, boolean given D:\xampp\htdocs\login\system\database\drivers\postgre\postgre_driver.php 326
ERROR - 2020-07-29 11:01:56 --> Severity: Warning --> pg_escape_literal() expects parameter 1 to be resource, boolean given D:\xampp\htdocs\login\system\database\drivers\postgre\postgre_driver.php 326
ERROR - 2020-07-29 11:01:56 --> Severity: Warning --> pg_escape_literal() expects parameter 1 to be resource, boolean given D:\xampp\htdocs\login\system\database\drivers\postgre\postgre_driver.php 326
ERROR - 2020-07-29 11:01:56 --> Severity: Warning --> pg_escape_literal() expects parameter 1 to be resource, boolean given D:\xampp\htdocs\login\system\database\drivers\postgre\postgre_driver.php 326
ERROR - 2020-07-29 11:01:56 --> Severity: Warning --> pg_escape_literal() expects parameter 1 to be resource, boolean given D:\xampp\htdocs\login\system\database\drivers\postgre\postgre_driver.php 326
ERROR - 2020-07-29 11:01:56 --> Severity: Warning --> pg_escape_literal() expects parameter 1 to be resource, boolean given D:\xampp\htdocs\login\system\database\drivers\postgre\postgre_driver.php 326
ERROR - 2020-07-29 11:01:56 --> Severity: Warning --> pg_escape_literal() expects parameter 1 to be resource, boolean given D:\xampp\htdocs\login\system\database\drivers\postgre\postgre_driver.php 326
ERROR - 2020-07-29 11:01:56 --> Severity: Warning --> pg_escape_literal() expects parameter 1 to be resource, boolean given D:\xampp\htdocs\login\system\database\drivers\postgre\postgre_driver.php 326
ERROR - 2020-07-29 11:01:56 --> Severity: Warning --> pg_escape_literal() expects parameter 1 to be resource, boolean given D:\xampp\htdocs\login\system\database\drivers\postgre\postgre_driver.php 326
ERROR - 2020-07-29 11:01:56 --> Severity: Warning --> pg_last_error() expects parameter 1 to be resource, boolean given D:\xampp\htdocs\login\system\database\drivers\postgre\postgre_driver.php 480
DEBUG - 2020-07-29 11:03:38 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 11:03:38 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 11:03:41 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 11:03:41 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-29 11:03:53 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 11:03:53 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 11:03:55 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 11:03:55 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-29 11:03:57 --> Total execution time: 3.8502
DEBUG - 2020-07-29 11:04:39 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 11:04:39 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 11:04:42 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 11:04:42 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-29 11:29:00 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 11:29:00 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 11:29:04 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 11:29:04 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-29 11:29:05 --> Total execution time: 4.8663
DEBUG - 2020-07-29 11:29:38 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 11:29:38 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 11:29:54 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 11:29:54 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
ERROR - 2020-07-29 11:30:01 --> Severity: Warning --> pg_query(): Query failed: ERROR:  relation &quot;seg.id&quot; does not exist
LINE 1: SELECT CURRVAL('seg.id') AS ins_id
                       ^ D:\xampp\htdocs\login\system\database\drivers\postgre\postgre_driver.php 242
ERROR - 2020-07-29 11:30:01 --> Query error: ERROR:  relation "seg.id" does not exist
LINE 1: SELECT CURRVAL('seg.id') AS ins_id
                       ^ - Invalid query: SELECT CURRVAL('seg.id') AS ins_id
ERROR - 2020-07-29 11:30:01 --> Severity: error --> Exception: Call to a member function row() on boolean D:\xampp\htdocs\login\system\database\drivers\postgre\postgre_driver.php 390
DEBUG - 2020-07-29 11:30:10 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 11:30:10 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 11:30:12 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 11:30:12 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-29 11:30:12 --> Total execution time: 2.3951
DEBUG - 2020-07-29 11:31:16 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 11:31:16 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 11:31:19 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 11:31:19 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-29 11:31:19 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 11:31:19 --> Global POST, GET and COOKIE data sanitized
ERROR - 2020-07-29 11:31:19 --> 404 Page Not Found: Login/index
DEBUG - 2020-07-29 11:31:32 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 11:31:32 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 11:31:33 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 11:31:33 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-29 11:31:33 --> #Main/login | {"__ci_last_regenerate":1596033093}
DEBUG - 2020-07-29 11:31:33 --> cURL Class Initialized
DEBUG - 2020-07-29 11:31:33 --> #Main/login | Carga Login |false| []
DEBUG - 2020-07-29 11:31:34 --> Total execution time: 1.8351
DEBUG - 2020-07-29 11:31:50 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 11:31:50 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 11:31:51 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 11:31:51 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-29 11:31:51 --> #Main/login | {"__ci_last_regenerate":1596033093}
DEBUG - 2020-07-29 11:31:51 --> cURL Class Initialized
DEBUG - 2020-07-29 11:31:51 --> #Main/login | Carga Login |false| {"email":"hugoDS","password":"123456"}
DEBUG - 2020-07-29 11:31:51 --> Total execution time: 1.4351
DEBUG - 2020-07-29 11:32:06 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 11:32:06 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 11:32:10 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 11:32:10 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-29 11:32:10 --> #Main/login | {"__ci_last_regenerate":1596033093}
DEBUG - 2020-07-29 11:32:10 --> cURL Class Initialized
DEBUG - 2020-07-29 11:32:10 --> #Main/login | userInfo: false
ERROR - 2020-07-29 11:32:10 --> #Main/login | Wrong password or email.
DEBUG - 2020-07-29 11:32:11 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 11:32:11 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 11:32:14 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 11:32:14 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-29 11:32:14 --> #Main/login | {"__ci_last_regenerate":1596033093}
DEBUG - 2020-07-29 11:32:14 --> cURL Class Initialized
DEBUG - 2020-07-29 11:32:14 --> #Main/login | Carga Login |false| []
DEBUG - 2020-07-29 11:32:15 --> Total execution time: 3.7352
DEBUG - 2020-07-29 11:32:46 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 11:32:47 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 11:32:48 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 11:32:48 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-29 11:32:48 --> #Main/login | {"__ci_last_regenerate":1596033093}
DEBUG - 2020-07-29 11:32:48 --> cURL Class Initialized
ERROR - 2020-07-29 11:32:48 --> Severity: Warning --> pg_query(): Query failed: ERROR:  relation &quot;users&quot; does not exist
LINE 1: UPDATE &quot;users&quot; SET &quot;last_login&quot; = '2020-07-29 11:32:48 AM'
               ^ D:\xampp\htdocs\login\system\database\drivers\postgre\postgre_driver.php 242
ERROR - 2020-07-29 11:32:48 --> Query error: ERROR:  relation "users" does not exist
LINE 1: UPDATE "users" SET "last_login" = '2020-07-29 11:32:48 AM'
               ^ - Invalid query: UPDATE "users" SET "last_login" = '2020-07-29 11:32:48 AM'
WHERE "id" = '3'
DEBUG - 2020-07-29 11:32:48 --> #Main/login | userInfo: {"id":"3","email":"hgallardo@trazalog.com","first_name":"hugo","last_name":"gallardo","role":"1","last_login":null,"status":"approved","banned_users":null,"passmd5":null,"telefono":"2345111111","dni":"2222222222","usernick":"hugoDS"}
ERROR - 2020-07-29 11:32:48 --> Something Error!
DEBUG - 2020-07-29 11:32:49 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 11:32:49 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 11:32:50 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 11:32:50 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-29 11:32:50 --> #Main/login | {"__ci_last_regenerate":1596033093}
DEBUG - 2020-07-29 11:32:50 --> cURL Class Initialized
DEBUG - 2020-07-29 11:32:50 --> #Main/login | Carga Login |false| []
DEBUG - 2020-07-29 11:32:50 --> Total execution time: 1.4651
DEBUG - 2020-07-29 11:33:32 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 11:33:32 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 11:33:43 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 11:33:43 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-29 11:33:43 --> #Main/login | {"__ci_last_regenerate":1596033093}
DEBUG - 2020-07-29 11:33:43 --> cURL Class Initialized
ERROR - 2020-07-29 11:33:44 --> Severity: Warning --> pg_query(): Query failed: ERROR:  relation &quot;users&quot; does not exist
LINE 1: UPDATE &quot;users&quot; SET &quot;last_login&quot; = '2020-07-29 11:33:43 AM'
               ^ D:\xampp\htdocs\login\system\database\drivers\postgre\postgre_driver.php 242
ERROR - 2020-07-29 11:33:44 --> Query error: ERROR:  relation "users" does not exist
LINE 1: UPDATE "users" SET "last_login" = '2020-07-29 11:33:43 AM'
               ^ - Invalid query: UPDATE "users" SET "last_login" = '2020-07-29 11:33:43 AM'
WHERE "id" = '3'
DEBUG - 2020-07-29 11:33:44 --> #Main/login | userInfo: {"id":"3","email":"hgallardo@trazalog.com","first_name":"hugo","last_name":"gallardo","role":"1","last_login":null,"status":"approved","banned_users":null,"passmd5":null,"telefono":"2345111111","dni":"2222222222","usernick":"hugoDS"}
ERROR - 2020-07-29 11:33:44 --> Something Error!
DEBUG - 2020-07-29 11:33:44 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 11:33:44 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 11:33:47 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 11:33:47 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-29 11:33:47 --> #Main/login | {"__ci_last_regenerate":1596033093}
DEBUG - 2020-07-29 11:33:47 --> cURL Class Initialized
DEBUG - 2020-07-29 11:33:47 --> #Main/login | Carga Login |false| []
DEBUG - 2020-07-29 11:33:47 --> Total execution time: 3.4742
DEBUG - 2020-07-29 11:34:15 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 11:34:15 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 11:34:17 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 11:34:17 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-29 11:34:17 --> #Main/login | {"__ci_last_regenerate":1596033093}
DEBUG - 2020-07-29 11:34:17 --> cURL Class Initialized
ERROR - 2020-07-29 11:34:18 --> Severity: Warning --> pg_query(): Query failed: ERROR:  relation &quot;users&quot; does not exist
LINE 1: UPDATE &quot;users&quot; SET &quot;last_login&quot; = '2020-07-29 11:34:17 AM'
               ^ D:\xampp\htdocs\login\system\database\drivers\postgre\postgre_driver.php 242
ERROR - 2020-07-29 11:34:18 --> Query error: ERROR:  relation "users" does not exist
LINE 1: UPDATE "users" SET "last_login" = '2020-07-29 11:34:17 AM'
               ^ - Invalid query: UPDATE "users" SET "last_login" = '2020-07-29 11:34:17 AM'
WHERE "id" = '3'
DEBUG - 2020-07-29 11:34:18 --> #Main/login | userInfo: {"id":"3","email":"hgallardo@trazalog.com","first_name":"hugo","last_name":"gallardo","role":"1","last_login":null,"status":"approved","banned_users":null,"passmd5":null,"telefono":"2345111111","dni":"2222222222","usernick":"hugoDS"}
ERROR - 2020-07-29 11:34:18 --> Something Error!
DEBUG - 2020-07-29 11:34:18 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 11:34:18 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 11:34:20 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 11:34:20 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-29 11:34:20 --> #Main/login | {"__ci_last_regenerate":1596033093}
DEBUG - 2020-07-29 11:34:20 --> cURL Class Initialized
DEBUG - 2020-07-29 11:34:20 --> #Main/login | Carga Login |false| []
DEBUG - 2020-07-29 11:34:21 --> Total execution time: 2.4141
DEBUG - 2020-07-29 11:34:51 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 11:34:51 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 11:34:52 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 11:34:52 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-29 11:34:52 --> #Main/login | {"__ci_last_regenerate":1596033093}
DEBUG - 2020-07-29 11:34:52 --> cURL Class Initialized
ERROR - 2020-07-29 11:34:53 --> Severity: Warning --> pg_query(): Query failed: ERROR:  relation &quot;users&quot; does not exist
LINE 1: UPDATE &quot;users&quot; SET &quot;last_login&quot; = '2020-07-29 11:34:53 AM'
               ^ D:\xampp\htdocs\login\system\database\drivers\postgre\postgre_driver.php 242
ERROR - 2020-07-29 11:34:53 --> Query error: ERROR:  relation "users" does not exist
LINE 1: UPDATE "users" SET "last_login" = '2020-07-29 11:34:53 AM'
               ^ - Invalid query: UPDATE "users" SET "last_login" = '2020-07-29 11:34:53 AM'
WHERE "id" = '1'
DEBUG - 2020-07-29 11:34:53 --> #Main/login | userInfo: {"id":"1","email":"admin@gmail.com","first_name":"Lola","last_name":"Meraz","role":"1","last_login":"2020-07-17 03:40:22 PM","status":"approved","banned_users":"unban","passmd5":null,"telefono":null,"dni":null,"usernick":null}
DEBUG - 2020-07-29 11:34:53 --> #Main/checkLoginUser/
DEBUG - 2020-07-29 11:35:16 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 11:35:16 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 11:35:17 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 11:35:17 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
ERROR - 2020-07-29 11:35:17 --> Severity: Warning --> pg_query(): Query failed: ERROR:  relation &quot;users&quot; does not exist
LINE 2: FROM &quot;users&quot;
             ^ D:\xampp\htdocs\login\system\database\drivers\postgre\postgre_driver.php 242
ERROR - 2020-07-29 11:35:17 --> Query error: ERROR:  relation "users" does not exist
LINE 2: FROM "users"
             ^ - Invalid query: SELECT *
FROM "users"
WHERE "id" = '1'
 LIMIT 1
ERROR - 2020-07-29 11:35:17 --> Severity: Warning --> pg_affected_rows() expects parameter 1 to be resource, boolean given D:\xampp\htdocs\login\system\database\drivers\postgre\postgre_driver.php 345
DEBUG - 2020-07-29 11:35:17 --> Total execution time: 1.8221
DEBUG - 2020-07-29 11:35:30 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 11:35:30 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 11:35:32 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 11:35:32 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-29 11:35:33 --> Total execution time: 2.6942
DEBUG - 2020-07-29 11:35:41 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 11:35:41 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 11:35:43 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 11:35:43 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-29 11:35:44 --> Total execution time: 3.1992
DEBUG - 2020-07-29 11:35:45 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 11:35:45 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 11:35:46 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 11:35:46 --> Total execution time: 1.3800
DEBUG - 2020-07-29 11:36:04 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 11:36:04 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 11:36:06 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 11:36:06 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
ERROR - 2020-07-29 11:36:07 --> Severity: Warning --> pg_query(): Query failed: ERROR:  relation &quot;users&quot; does not exist
LINE 2: FROM &quot;users&quot;
             ^ D:\xampp\htdocs\login\system\database\drivers\postgre\postgre_driver.php 242
ERROR - 2020-07-29 11:36:07 --> Query error: ERROR:  relation "users" does not exist
LINE 2: FROM "users"
             ^ - Invalid query: SELECT *
FROM "users"
WHERE "id" = '1'
 LIMIT 1
ERROR - 2020-07-29 11:36:07 --> Severity: Warning --> pg_affected_rows() expects parameter 1 to be resource, boolean given D:\xampp\htdocs\login\system\database\drivers\postgre\postgre_driver.php 345
DEBUG - 2020-07-29 11:36:07 --> Total execution time: 2.9112
DEBUG - 2020-07-29 11:37:07 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 11:37:07 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 11:37:08 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 11:37:08 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-29 11:37:09 --> Total execution time: 1.7471
DEBUG - 2020-07-29 11:37:17 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 11:37:17 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 11:37:18 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 11:37:18 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-29 11:37:19 --> Total execution time: 2.0121
DEBUG - 2020-07-29 11:38:51 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 11:38:51 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 11:38:52 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 11:38:52 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
ERROR - 2020-07-29 11:38:53 --> #TRAZA|USER_MODEL|associateBPMrol($data_rol) >> ERROR -> ""
DEBUG - 2020-07-29 11:38:53 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 11:38:53 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 11:38:55 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 11:38:55 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-29 11:38:55 --> Total execution time: 2.3001
DEBUG - 2020-07-29 11:59:10 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 11:59:10 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 11:59:12 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 11:59:12 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-29 11:59:13 --> Total execution time: 2.6942
DEBUG - 2020-07-29 12:00:45 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 12:00:45 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 12:00:46 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 12:00:46 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
ERROR - 2020-07-29 12:01:05 --> Severity: Warning --> pg_query(): Query failed: ERROR:  relation &quot;seg.id&quot; does not exist
LINE 1: SELECT CURRVAL('seg.id') AS ins_id
                       ^ D:\xampp\htdocs\login\system\database\drivers\postgre\postgre_driver.php 242
ERROR - 2020-07-29 12:01:05 --> Query error: ERROR:  relation "seg.id" does not exist
LINE 1: SELECT CURRVAL('seg.id') AS ins_id
                       ^ - Invalid query: SELECT CURRVAL('seg.id') AS ins_id
ERROR - 2020-07-29 12:02:40 --> Severity: Error --> Uncaught Error: Call to a member function row() on boolean in D:\xampp\htdocs\login\system\database\drivers\postgre\postgre_driver.php:390
Stack trace:
#0 D:\xampp\htdocs\login\application\models\User_model.php(221): CI_DB_postgre_driver->insert_id('seg.id')
#1 D:\xampp\htdocs\login\application\controllers\Main.php(433): User_model->addUser(Array)
#2 D:\xampp\htdocs\login\system\core\CodeIgniter.php(532): Main->adduser()
#3 D:\xampp\htdocs\login\index.php(315): require_once('D:\\xampp\\htdocs...')
#4 {main}
  thrown D:\xampp\htdocs\login\system\database\drivers\postgre\postgre_driver.php 390
DEBUG - 2020-07-29 12:02:44 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 12:02:44 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 12:02:46 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 12:02:46 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-29 12:02:50 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 12:02:50 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 12:02:51 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 12:02:51 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-29 12:02:51 --> Total execution time: 1.5631
DEBUG - 2020-07-29 12:03:33 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 12:03:33 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 12:03:34 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 12:03:34 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
ERROR - 2020-07-29 12:03:34 --> Severity: Warning --> pg_query(): Query failed: ERROR:  relation &quot;seg.id&quot; does not exist
LINE 1: SELECT CURRVAL('seg.id') AS ins_id
                       ^ D:\xampp\htdocs\login\system\database\drivers\postgre\postgre_driver.php 242
ERROR - 2020-07-29 12:03:34 --> Query error: ERROR:  relation "seg.id" does not exist
LINE 1: SELECT CURRVAL('seg.id') AS ins_id
                       ^ - Invalid query: SELECT CURRVAL('seg.id') AS ins_id
ERROR - 2020-07-29 12:03:35 --> Severity: error --> Exception: Call to a member function row() on boolean D:\xampp\htdocs\login\system\database\drivers\postgre\postgre_driver.php 390
DEBUG - 2020-07-29 12:04:16 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 12:04:16 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 12:04:17 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 12:04:17 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-29 12:04:18 --> Total execution time: 2.3121
DEBUG - 2020-07-29 12:04:56 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 12:04:56 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 12:04:57 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 12:04:57 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
ERROR - 2020-07-29 12:04:57 --> Severity: Warning --> pg_query(): Query failed: ERROR:  relation &quot;seg.id&quot; does not exist
LINE 1: SELECT CURRVAL('seg.id') AS ins_id
                       ^ D:\xampp\htdocs\login\system\database\drivers\postgre\postgre_driver.php 242
ERROR - 2020-07-29 12:04:57 --> Query error: ERROR:  relation "seg.id" does not exist
LINE 1: SELECT CURRVAL('seg.id') AS ins_id
                       ^ - Invalid query: SELECT CURRVAL('seg.id') AS ins_id
ERROR - 2020-07-29 12:04:58 --> Severity: error --> Exception: Call to a member function row() on boolean D:\xampp\htdocs\login\system\database\drivers\postgre\postgre_driver.php 390
DEBUG - 2020-07-29 12:05:18 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 12:05:18 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 12:05:19 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 12:05:19 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-29 12:05:19 --> Total execution time: 1.4681
DEBUG - 2020-07-29 12:05:49 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 12:05:49 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 12:05:51 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 12:05:51 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
ERROR - 2020-07-29 12:06:07 --> Severity: Warning --> pg_query(): Query failed: ERROR:  relation &quot;seg.id&quot; does not exist
LINE 1: SELECT CURRVAL('seg.id') AS ins_id
                       ^ D:\xampp\htdocs\login\system\database\drivers\postgre\postgre_driver.php 242
ERROR - 2020-07-29 12:06:07 --> Query error: ERROR:  relation "seg.id" does not exist
LINE 1: SELECT CURRVAL('seg.id') AS ins_id
                       ^ - Invalid query: SELECT CURRVAL('seg.id') AS ins_id
ERROR - 2020-07-29 12:33:54 --> Severity: Error --> Uncaught Error: Call to a member function row() on boolean in D:\xampp\htdocs\login\system\database\drivers\postgre\postgre_driver.php:390
Stack trace:
#0 D:\xampp\htdocs\login\application\models\User_model.php(221): CI_DB_postgre_driver->insert_id('seg.id')
#1 D:\xampp\htdocs\login\application\controllers\Main.php(433): User_model->addUser(Array)
#2 D:\xampp\htdocs\login\system\core\CodeIgniter.php(532): Main->adduser()
#3 D:\xampp\htdocs\login\index.php(315): require_once('D:\\xampp\\htdocs...')
#4 {main}
  thrown D:\xampp\htdocs\login\system\database\drivers\postgre\postgre_driver.php 390
DEBUG - 2020-07-29 12:53:42 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 12:53:42 --> Global POST, GET and COOKIE data sanitized
ERROR - 2020-07-29 12:53:42 --> PDO: Invalid or non-existent subdriver
ERROR - 2020-07-29 12:54:23 --> Severity: Error --> Uncaught PDOException: invalid data source name in D:\xampp\htdocs\login\system\database\drivers\pdo\pdo_driver.php:136
Stack trace:
#0 D:\xampp\htdocs\login\system\database\drivers\pdo\pdo_driver.php(136): PDO->__construct('', 'postgres', '!Password00', Array)
#1 D:\xampp\htdocs\login\system\database\DB_driver.php(401): CI_DB_pdo_driver->db_connect(false)
#2 D:\xampp\htdocs\login\system\database\DB.php(216): CI_DB_driver->initialize()
#3 D:\xampp\htdocs\login\system\core\Loader.php(393): DB(Array, NULL)
#4 D:\xampp\htdocs\login\system\core\Loader.php(1350): CI_Loader->database()
#5 D:\xampp\htdocs\login\system\core\Loader.php(157): CI_Loader->_ci_autoloader()
#6 D:\xampp\htdocs\login\system\core\Controller.php(79): CI_Loader->initialize()
#7 D:\xampp\htdocs\login\application\controllers\Main.php(10): CI_Controller->__construct()
#8 D:\xampp\htdocs\login\system\core\CodeIgniter.php(518): Main->__construct()
#9 D:\xampp\htdocs\login\index.php(315): require_once('D:\\xampp\\htdocs...')
#10 {main}
  thrown D:\xampp\htdocs\login\system\database\drivers\pdo\pdo_driver.php 136
DEBUG - 2020-07-29 12:54:26 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 12:54:26 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 12:54:27 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 12:54:27 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-29 12:54:27 --> Total execution time: 1.5781
DEBUG - 2020-07-29 12:55:38 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 12:55:38 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 12:55:39 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 12:55:39 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-29 12:55:55 --> Total execution time: 16.3739
DEBUG - 2020-07-29 12:56:18 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 12:56:18 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 12:56:19 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 12:56:19 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
ERROR - 2020-07-29 12:56:42 --> Severity: Warning --> pg_query(): Query failed: ERROR:  relation &quot;user_id_seq&quot; does not exist
LINE 1: SELECT CURRVAL('user_id_seq') AS ins_id
                       ^ D:\xampp\htdocs\login\system\database\drivers\postgre\postgre_driver.php 242
ERROR - 2020-07-29 12:56:42 --> Query error: ERROR:  relation "user_id_seq" does not exist
LINE 1: SELECT CURRVAL('user_id_seq') AS ins_id
                       ^ - Invalid query: SELECT CURRVAL('user_id_seq') AS ins_id
ERROR - 2020-07-29 13:01:41 --> Severity: Error --> Uncaught Error: Call to a member function row() on boolean in D:\xampp\htdocs\login\system\database\drivers\postgre\postgre_driver.php:390
Stack trace:
#0 D:\xampp\htdocs\login\application\models\User_model.php(221): CI_DB_postgre_driver->insert_id('user_id_seq')
#1 D:\xampp\htdocs\login\application\controllers\Main.php(433): User_model->addUser(Array)
#2 D:\xampp\htdocs\login\system\core\CodeIgniter.php(532): Main->adduser()
#3 D:\xampp\htdocs\login\index.php(315): require_once('D:\\xampp\\htdocs...')
#4 {main}
  thrown D:\xampp\htdocs\login\system\database\drivers\postgre\postgre_driver.php 390
DEBUG - 2020-07-29 13:01:58 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 13:01:58 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 13:01:59 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 13:01:59 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-29 13:02:00 --> Total execution time: 1.5111
DEBUG - 2020-07-29 13:02:31 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 13:02:31 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 13:02:32 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 13:02:32 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
ERROR - 2020-07-29 13:02:45 --> Severity: Warning --> pg_query(): Query failed: ERROR:  relation &quot;user_id_seq&quot; does not exist
LINE 1: SELECT CURRVAL('user_id_seq') AS ins_id
                       ^ D:\xampp\htdocs\login\system\database\drivers\postgre\postgre_driver.php 242
ERROR - 2020-07-29 13:02:45 --> Query error: ERROR:  relation "user_id_seq" does not exist
LINE 1: SELECT CURRVAL('user_id_seq') AS ins_id
                       ^ - Invalid query: SELECT CURRVAL('user_id_seq') AS ins_id
ERROR - 2020-07-29 13:50:04 --> Severity: Error --> Uncaught Error: Call to a member function row() on boolean in D:\xampp\htdocs\login\system\database\drivers\postgre\postgre_driver.php:390
Stack trace:
#0 D:\xampp\htdocs\login\application\models\User_model.php(221): CI_DB_postgre_driver->insert_id('user_id_seq')
#1 D:\xampp\htdocs\login\application\controllers\Main.php(433): User_model->addUser(Array)
#2 D:\xampp\htdocs\login\system\core\CodeIgniter.php(532): Main->adduser()
#3 D:\xampp\htdocs\login\index.php(315): require_once('D:\\xampp\\htdocs...')
#4 {main}
  thrown D:\xampp\htdocs\login\system\database\drivers\postgre\postgre_driver.php 390
DEBUG - 2020-07-29 13:50:23 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 13:50:23 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 13:50:24 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 13:50:24 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-29 13:50:24 --> Total execution time: 1.5631
DEBUG - 2020-07-29 13:50:53 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 13:50:53 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 13:50:54 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 13:50:54 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-29 13:50:54 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 13:50:54 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 13:50:56 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 13:50:56 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-29 13:50:56 --> Total execution time: 1.4871
DEBUG - 2020-07-29 13:52:19 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 13:52:19 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 13:52:20 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 13:52:20 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-29 13:57:48 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 13:57:48 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 13:57:50 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 13:57:50 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-29 13:57:50 --> Total execution time: 1.4971
DEBUG - 2020-07-29 14:01:57 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 14:01:57 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 14:01:58 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 14:01:58 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-29 14:03:03 --> #TRAZA|USER_MODEL|associateBPMrol($data_rol): $data_rol >> {"user_id":"212","group":"grupo test","role":"rol test","usuario_app":"hugoDS"}
ERROR - 2020-07-29 14:17:07 --> #TRAZA|USER_MODEL|associateBPMrol($data_rol) >> ERROR -> ""
DEBUG - 2020-07-29 14:17:36 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 14:17:36 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 14:17:37 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 14:17:37 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-29 14:17:38 --> Total execution time: 1.4821
DEBUG - 2020-07-29 14:18:16 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 14:18:16 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 14:18:17 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 14:18:17 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-29 14:19:29 --> #TRAZA|USER_MODEL|ASOCIATEBPMROL($data_rol): $data_rol >> {"user_id":"212","group":"grupo test","role":"rol test","usuario_app":"hugoDS"}
DEBUG - 2020-07-29 14:20:05 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 14:20:05 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 14:20:06 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 14:20:06 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-29 14:21:55 --> Total execution time: 110.9363
DEBUG - 2020-07-29 14:22:27 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 14:22:27 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 14:22:28 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 14:22:28 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-29 14:22:28 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 14:22:29 --> Global POST, GET and COOKIE data sanitized
ERROR - 2020-07-29 14:22:29 --> 404 Page Not Found: Login/index
DEBUG - 2020-07-29 14:25:31 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 14:25:31 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 14:25:32 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 14:25:32 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-29 14:25:32 --> #Main/login | {"__ci_last_regenerate":1596043532}
DEBUG - 2020-07-29 14:25:32 --> cURL Class Initialized
DEBUG - 2020-07-29 14:25:32 --> #Main/login | Carga Login |false| []
DEBUG - 2020-07-29 14:25:32 --> Total execution time: 1.2881
DEBUG - 2020-07-29 14:41:31 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 14:41:31 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 14:41:32 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 14:41:32 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-29 14:41:32 --> #Main/login | {"__ci_last_regenerate":1596044492}
DEBUG - 2020-07-29 14:41:32 --> cURL Class Initialized
DEBUG - 2020-07-29 14:41:32 --> #Main/login | Carga Login |false| {"email":"hugoEDS","password":"111111"}
DEBUG - 2020-07-29 14:41:32 --> Total execution time: 1.3761
DEBUG - 2020-07-29 14:41:40 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 14:41:40 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 14:41:42 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 14:41:42 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-29 14:41:42 --> #Main/login | {"__ci_last_regenerate":1596044492}
DEBUG - 2020-07-29 14:41:42 --> cURL Class Initialized
ERROR - 2020-07-29 14:41:42 --> Severity: Warning --> pg_query(): Query failed: ERROR:  relation &quot;users&quot; does not exist
LINE 1: UPDATE &quot;users&quot; SET &quot;last_login&quot; = '2020-07-29 02:41:42 PM'
               ^ D:\xampp\htdocs\login\system\database\drivers\postgre\postgre_driver.php 242
ERROR - 2020-07-29 14:41:42 --> Query error: ERROR:  relation "users" does not exist
LINE 1: UPDATE "users" SET "last_login" = '2020-07-29 02:41:42 PM'
               ^ - Invalid query: UPDATE "users" SET "last_login" = '2020-07-29 02:41:42 PM'
WHERE "id" = '1'
DEBUG - 2020-07-29 14:41:42 --> #Main/login | userInfo: {"id":"1","email":"admin@gmail.com","first_name":"Lola","last_name":"Meraz","role":"1","last_login":"2020-07-17 03:40:22 PM","status":"approved","banned_users":"unban","passmd5":null,"telefono":null,"dni":null,"usernick":null}
DEBUG - 2020-07-29 14:41:42 --> #Main/checkLoginUser/
DEBUG - 2020-07-29 14:42:11 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 14:42:11 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 14:42:12 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 14:42:12 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-29 14:42:12 --> Total execution time: 1.6631
DEBUG - 2020-07-29 14:42:19 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 14:42:19 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 14:42:20 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 14:42:20 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-29 14:42:20 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 14:42:20 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 14:42:21 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 14:42:21 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-29 14:42:21 --> #Main/login | {"__ci_last_regenerate":1596044541}
DEBUG - 2020-07-29 14:42:21 --> cURL Class Initialized
DEBUG - 2020-07-29 14:42:21 --> #Main/login | Carga Login |false| []
DEBUG - 2020-07-29 14:42:21 --> Total execution time: 1.3301
DEBUG - 2020-07-29 14:42:32 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 14:42:32 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 14:42:33 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 14:42:33 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-29 14:42:33 --> #Main/login | {"__ci_last_regenerate":1596044541}
DEBUG - 2020-07-29 14:42:33 --> cURL Class Initialized
ERROR - 2020-07-29 14:42:33 --> Severity: Warning --> pg_query(): Query failed: ERROR:  relation &quot;users&quot; does not exist
LINE 1: UPDATE &quot;users&quot; SET &quot;last_login&quot; = '2020-07-29 02:42:33 PM'
               ^ D:\xampp\htdocs\login\system\database\drivers\postgre\postgre_driver.php 242
ERROR - 2020-07-29 14:42:33 --> Query error: ERROR:  relation "users" does not exist
LINE 1: UPDATE "users" SET "last_login" = '2020-07-29 02:42:33 PM'
               ^ - Invalid query: UPDATE "users" SET "last_login" = '2020-07-29 02:42:33 PM'
WHERE "id" = '1'
DEBUG - 2020-07-29 14:42:33 --> #Main/login | userInfo: {"id":"1","email":"admin@gmail.com","first_name":"Lola","last_name":"Meraz","role":"1","last_login":"2020-07-17 03:40:22 PM","status":"approved","banned_users":"unban","passmd5":null,"telefono":null,"dni":null,"usernick":null}
DEBUG - 2020-07-29 14:42:33 --> #Main/checkLoginUser/
DEBUG - 2020-07-29 14:42:47 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 14:42:47 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 14:42:48 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 14:42:48 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-29 14:42:49 --> Total execution time: 1.6851
DEBUG - 2020-07-29 14:43:00 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 14:43:00 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 14:43:01 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 14:43:01 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-29 14:43:02 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 14:43:02 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 14:43:03 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 14:43:03 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-29 14:43:03 --> #Main/login | {"__ci_last_regenerate":1596044583}
DEBUG - 2020-07-29 14:43:03 --> cURL Class Initialized
DEBUG - 2020-07-29 14:43:03 --> #Main/login | Carga Login |false| []
DEBUG - 2020-07-29 14:43:03 --> Total execution time: 1.3261
DEBUG - 2020-07-29 14:43:13 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 14:43:13 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 14:43:14 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 14:43:14 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-29 14:43:14 --> #Main/login | {"__ci_last_regenerate":1596044583}
DEBUG - 2020-07-29 14:43:14 --> cURL Class Initialized
DEBUG - 2020-07-29 14:43:14 --> #Main/login | Carga Login |false| {"email":"hugoEDS","password":"111111"}
DEBUG - 2020-07-29 14:43:14 --> Total execution time: 1.3361
DEBUG - 2020-07-29 14:43:43 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 14:43:43 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 14:43:45 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 14:43:45 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-29 14:43:45 --> #Main/login | {"__ci_last_regenerate":1596044583}
DEBUG - 2020-07-29 14:43:45 --> cURL Class Initialized
ERROR - 2020-07-29 14:43:45 --> Severity: Warning --> pg_query(): Query failed: ERROR:  relation &quot;users&quot; does not exist
LINE 1: UPDATE &quot;users&quot; SET &quot;last_login&quot; = '2020-07-29 02:43:45 PM'
               ^ D:\xampp\htdocs\login\system\database\drivers\postgre\postgre_driver.php 242
ERROR - 2020-07-29 14:43:45 --> Query error: ERROR:  relation "users" does not exist
LINE 1: UPDATE "users" SET "last_login" = '2020-07-29 02:43:45 PM'
               ^ - Invalid query: UPDATE "users" SET "last_login" = '2020-07-29 02:43:45 PM'
WHERE "id" = '13'
DEBUG - 2020-07-29 14:43:45 --> #Main/login | userInfo: {"id":"13","email":"hugoeduardogallardo@gmail.com","first_name":"hugo","last_name":"gallardo","role":"1","last_login":null,"status":"approved","banned_users":null,"passmd5":null,"telefono":"2345111111","dni":"2222222222","usernick":"hugoEDS"}
ERROR - 2020-07-29 14:43:45 --> Something Error!
DEBUG - 2020-07-29 14:43:45 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 14:43:45 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 14:43:46 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 14:43:46 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-29 14:43:46 --> #Main/login | {"__ci_last_regenerate":1596044583}
DEBUG - 2020-07-29 14:43:46 --> cURL Class Initialized
DEBUG - 2020-07-29 14:43:46 --> #Main/login | Carga Login |false| []
DEBUG - 2020-07-29 14:43:47 --> Total execution time: 1.3281
DEBUG - 2020-07-29 14:44:02 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 14:44:02 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 14:44:03 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 14:44:03 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-29 14:44:03 --> #Main/login | {"__ci_last_regenerate":1596044583}
DEBUG - 2020-07-29 14:44:03 --> cURL Class Initialized
DEBUG - 2020-07-29 14:44:03 --> #Main/login | userInfo: false
ERROR - 2020-07-29 14:44:03 --> #Main/login | Wrong password or email.
DEBUG - 2020-07-29 14:44:04 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 14:44:04 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 14:44:05 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 14:44:05 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-29 14:44:05 --> #Main/login | {"__ci_last_regenerate":1596044583}
DEBUG - 2020-07-29 14:44:05 --> cURL Class Initialized
DEBUG - 2020-07-29 14:44:05 --> #Main/login | Carga Login |false| []
DEBUG - 2020-07-29 14:44:05 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 14:44:05 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 14:44:05 --> Total execution time: 1.3741
DEBUG - 2020-07-29 14:44:06 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 14:44:06 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-29 14:44:06 --> #Main/login | {"__ci_last_regenerate":1596044583}
DEBUG - 2020-07-29 14:44:06 --> cURL Class Initialized
DEBUG - 2020-07-29 14:44:07 --> #Main/login | userInfo: false
ERROR - 2020-07-29 14:44:07 --> #Main/login | Wrong password or email.
DEBUG - 2020-07-29 14:44:07 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 14:44:07 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 14:44:08 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 14:44:08 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-29 14:44:08 --> #Main/login | {"__ci_last_regenerate":1596044583}
DEBUG - 2020-07-29 14:44:08 --> cURL Class Initialized
DEBUG - 2020-07-29 14:44:08 --> #Main/login | Carga Login |false| []
DEBUG - 2020-07-29 14:44:08 --> Total execution time: 1.3321
DEBUG - 2020-07-29 14:44:19 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 14:44:19 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 14:44:20 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 14:44:20 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-29 14:44:20 --> #Main/login | {"__ci_last_regenerate":1596044583}
DEBUG - 2020-07-29 14:44:20 --> cURL Class Initialized
DEBUG - 2020-07-29 14:44:20 --> #Main/login | userInfo: false
ERROR - 2020-07-29 14:44:20 --> #Main/login | Wrong password or email.
DEBUG - 2020-07-29 14:44:20 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 14:44:20 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 14:44:21 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 14:44:21 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-29 14:44:21 --> #Main/login | {"__ci_last_regenerate":1596044583}
DEBUG - 2020-07-29 14:44:21 --> cURL Class Initialized
DEBUG - 2020-07-29 14:44:21 --> #Main/login | Carga Login |false| []
DEBUG - 2020-07-29 14:44:22 --> Total execution time: 1.3321
DEBUG - 2020-07-29 14:45:01 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 14:45:01 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 14:45:03 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 14:45:03 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-29 14:45:03 --> #Main/login | {"__ci_last_regenerate":1596044583}
DEBUG - 2020-07-29 14:45:03 --> cURL Class Initialized
ERROR - 2020-07-29 14:45:03 --> Severity: Warning --> pg_query(): Query failed: ERROR:  relation &quot;users&quot; does not exist
LINE 1: UPDATE &quot;users&quot; SET &quot;last_login&quot; = '2020-07-29 02:45:03 PM'
               ^ D:\xampp\htdocs\login\system\database\drivers\postgre\postgre_driver.php 242
ERROR - 2020-07-29 14:45:03 --> Query error: ERROR:  relation "users" does not exist
LINE 1: UPDATE "users" SET "last_login" = '2020-07-29 02:45:03 PM'
               ^ - Invalid query: UPDATE "users" SET "last_login" = '2020-07-29 02:45:03 PM'
WHERE "id" = '1'
DEBUG - 2020-07-29 14:45:03 --> #Main/login | userInfo: {"id":"1","email":"admin@gmail.com","first_name":"Lola","last_name":"Meraz","role":"1","last_login":"2020-07-17 03:40:22 PM","status":"approved","banned_users":"unban","passmd5":null,"telefono":null,"dni":null,"usernick":null}
DEBUG - 2020-07-29 14:45:03 --> #Main/checkLoginUser/
DEBUG - 2020-07-29 14:45:17 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 14:45:17 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 14:45:18 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 14:45:18 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-29 14:45:18 --> Total execution time: 1.7421
DEBUG - 2020-07-29 14:45:32 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 14:45:32 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 14:45:33 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 14:45:33 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-29 14:45:34 --> Total execution time: 1.4941
DEBUG - 2020-07-29 14:46:08 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 14:46:08 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 14:46:10 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 14:46:10 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-29 14:46:10 --> #TRAZA|USER_MODEL|ASOCIATEBPMROL($data_rol): $data_rol >> {"user_id":"212","group":"grupo test","role":"rol test","usuario_app":"hugoDS"}
ERROR - 2020-07-29 14:46:10 --> Severity: Warning --> pg_query(): Query failed: ERROR:  duplicate key value violates unique constraint &quot;memberships_pk&quot;
DETAIL:  Key (&quot;group&quot;, role, user_id)=(grupo test, rol test, 212) already exists. D:\xampp\htdocs\login\system\database\drivers\postgre\postgre_driver.php 242
ERROR - 2020-07-29 14:46:10 --> Query error: ERROR:  duplicate key value violates unique constraint "memberships_pk"
DETAIL:  Key ("group", role, user_id)=(grupo test, rol test, 212) already exists. - Invalid query: INSERT INTO "seg"."memberships_users" ("user_id", "group", "role", "usuario_app") VALUES ('212', 'grupo test', 'rol test', 'hugoDS')
ERROR - 2020-07-29 14:46:10 --> #TRAZA|USER_MODEL|ASOCIATEBPMROL($data_rol) >> ERROR -> "ERROR:  duplicate key value violates unique constraint \"memberships_pk\"\nDETAIL:  Key (\"group\", role, user_id)=(grupo test, rol test, 212) already exists."
DEBUG - 2020-07-29 14:46:11 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 14:46:11 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 14:46:12 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 14:46:12 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-29 14:46:12 --> Total execution time: 1.6601
DEBUG - 2020-07-29 14:46:30 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 14:46:30 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 14:46:31 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 14:46:31 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-29 14:46:32 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 14:46:32 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 14:46:34 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 14:46:34 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-29 14:46:34 --> #Main/login | {"__ci_last_regenerate":1596044794}
DEBUG - 2020-07-29 14:46:34 --> cURL Class Initialized
DEBUG - 2020-07-29 14:46:34 --> #Main/login | Carga Login |false| []
DEBUG - 2020-07-29 14:46:34 --> Total execution time: 2.0071
DEBUG - 2020-07-29 14:46:49 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 14:46:49 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 14:46:50 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 14:46:50 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-29 14:46:50 --> #Main/login | {"__ci_last_regenerate":1596044794}
DEBUG - 2020-07-29 14:46:50 --> cURL Class Initialized
ERROR - 2020-07-29 14:46:51 --> Severity: Warning --> pg_query(): Query failed: ERROR:  relation &quot;users&quot; does not exist
LINE 1: UPDATE &quot;users&quot; SET &quot;last_login&quot; = '2020-07-29 02:46:50 PM'
               ^ D:\xampp\htdocs\login\system\database\drivers\postgre\postgre_driver.php 242
ERROR - 2020-07-29 14:46:51 --> Query error: ERROR:  relation "users" does not exist
LINE 1: UPDATE "users" SET "last_login" = '2020-07-29 02:46:50 PM'
               ^ - Invalid query: UPDATE "users" SET "last_login" = '2020-07-29 02:46:50 PM'
WHERE "id" = '14'
DEBUG - 2020-07-29 14:46:51 --> #Main/login | userInfo: {"id":"14","email":"hgallardo@trazalog.com","first_name":"hugo","last_name":"gallardo","role":"1","last_login":null,"status":"approved","banned_users":null,"passmd5":null,"telefono":"2345111111","dni":"2222222222","usernick":"hugoEDS"}
ERROR - 2020-07-29 14:46:51 --> Something Error!
DEBUG - 2020-07-29 14:46:51 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 14:46:51 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 14:46:52 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 14:46:52 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-29 14:46:52 --> #Main/login | {"__ci_last_regenerate":1596044794}
DEBUG - 2020-07-29 14:46:52 --> cURL Class Initialized
DEBUG - 2020-07-29 14:46:52 --> #Main/login | Carga Login |false| []
DEBUG - 2020-07-29 14:46:52 --> Total execution time: 1.3351
DEBUG - 2020-07-29 14:51:15 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 14:51:15 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 14:51:16 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 14:51:17 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-29 14:51:37 --> #Main/login | {"__ci_last_regenerate":1596044794}
DEBUG - 2020-07-29 14:51:39 --> cURL Class Initialized
ERROR - 2020-07-29 14:52:24 --> Severity: Warning --> pg_query(): Query failed: ERROR:  relation &quot;users&quot; does not exist
LINE 1: UPDATE &quot;users&quot; SET &quot;last_login&quot; = '2020-07-29 02:52:24 PM'
               ^ D:\xampp\htdocs\login\system\database\drivers\postgre\postgre_driver.php 242
ERROR - 2020-07-29 14:52:24 --> Query error: ERROR:  relation "users" does not exist
LINE 1: UPDATE "users" SET "last_login" = '2020-07-29 02:52:24 PM'
               ^ - Invalid query: UPDATE "users" SET "last_login" = '2020-07-29 02:52:24 PM'
WHERE "id" = '14'
DEBUG - 2020-07-29 14:52:26 --> #Main/login | userInfo: {"id":"14","email":"hgallardo@trazalog.com","first_name":"hugo","last_name":"gallardo","role":"1","last_login":null,"status":"approved","banned_users":null,"passmd5":null,"telefono":"2345111111","dni":"2222222222","usernick":"hugoEDS"}
DEBUG - 2020-07-29 14:53:52 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 14:53:52 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 14:53:53 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 14:53:53 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-29 14:54:05 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 14:54:05 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 14:54:06 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 14:54:06 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-29 14:54:06 --> #Main/login | {"__ci_last_regenerate":1596045233}
DEBUG - 2020-07-29 14:54:06 --> cURL Class Initialized
DEBUG - 2020-07-29 14:54:06 --> #Main/login | Carga Login |false| []
DEBUG - 2020-07-29 14:54:06 --> Total execution time: 1.3381
DEBUG - 2020-07-29 14:54:25 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 14:54:25 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 14:54:26 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 14:54:26 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-29 14:54:32 --> #Main/login | {"__ci_last_regenerate":1596045233}
DEBUG - 2020-07-29 14:54:45 --> cURL Class Initialized
DEBUG - 2020-07-29 15:00:19 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 15:00:19 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 15:00:20 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 15:00:20 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-29 15:00:37 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 15:00:37 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 15:00:38 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 15:00:38 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-29 15:00:38 --> #Main/login | {"__ci_last_regenerate":1596045620}
DEBUG - 2020-07-29 15:00:38 --> cURL Class Initialized
DEBUG - 2020-07-29 15:00:38 --> #Main/login | Carga Login |false| []
DEBUG - 2020-07-29 15:00:39 --> Total execution time: 1.3191
DEBUG - 2020-07-29 15:01:21 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 15:01:21 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 15:01:23 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 15:01:23 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-29 15:01:28 --> #Main/login | {"__ci_last_regenerate":1596045620}
DEBUG - 2020-07-29 15:01:32 --> cURL Class Initialized
ERROR - 2020-07-29 15:02:19 --> Severity: Warning --> pg_query(): Query failed: ERROR:  relation &quot;users&quot; does not exist
LINE 1: UPDATE &quot;users&quot; SET &quot;last_login&quot; = '2020-07-29 03:02:19 PM'
               ^ D:\xampp\htdocs\login\system\database\drivers\postgre\postgre_driver.php 242
ERROR - 2020-07-29 15:02:19 --> Query error: ERROR:  relation "users" does not exist
LINE 1: UPDATE "users" SET "last_login" = '2020-07-29 03:02:19 PM'
               ^ - Invalid query: UPDATE "users" SET "last_login" = '2020-07-29 03:02:19 PM'
WHERE "id" = '14'
DEBUG - 2020-07-29 15:02:22 --> #Main/login | userInfo: {"id":"14","email":"hgallardo@trazalog.com","first_name":"hugo","last_name":"gallardo","role":"1","last_login":null,"status":"approved","banned_users":null,"passmd5":null,"telefono":"2345111111","dni":"2222222222","usernick":"hugoEDS"}
ERROR - 2020-07-29 15:02:24 --> Something Error!
DEBUG - 2020-07-29 15:02:25 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 15:02:25 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 15:02:26 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 15:02:26 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-29 15:02:28 --> #Main/login | {"__ci_last_regenerate":1596045620}
DEBUG - 2020-07-29 15:02:29 --> cURL Class Initialized
DEBUG - 2020-07-29 15:02:30 --> #Main/login | Carga Login |false| []
DEBUG - 2020-07-29 15:02:30 --> Total execution time: 5.4833
DEBUG - 2020-07-29 15:02:53 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 15:02:53 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 15:02:54 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 15:02:54 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-29 15:02:59 --> #Main/login | {"__ci_last_regenerate":1596045620}
DEBUG - 2020-07-29 15:03:00 --> cURL Class Initialized
ERROR - 2020-07-29 15:04:04 --> Severity: Warning --> pg_query(): Query failed: ERROR:  relation &quot;users&quot; does not exist
LINE 1: UPDATE &quot;users&quot; SET &quot;last_login&quot; = '2020-07-29 03:04:04 PM'
               ^ D:\xampp\htdocs\login\system\database\drivers\postgre\postgre_driver.php 242
ERROR - 2020-07-29 15:04:04 --> Query error: ERROR:  relation "users" does not exist
LINE 1: UPDATE "users" SET "last_login" = '2020-07-29 03:04:04 PM'
               ^ - Invalid query: UPDATE "users" SET "last_login" = '2020-07-29 03:04:04 PM'
WHERE "id" = '14'
DEBUG - 2020-07-29 15:04:39 --> #Main/login | userInfo: {"id":"14","email":"hgallardo@trazalog.com","first_name":"hugo","last_name":"gallardo","role":"1","last_login":null,"status":"approved","banned_users":null,"passmd5":null,"telefono":"2345111111","dni":"2222222222","usernick":"hugoEDS"}
DEBUG - 2020-07-29 15:07:26 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 15:07:26 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 15:07:27 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 15:07:27 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-29 15:07:27 --> #Main/login | {"__ci_last_regenerate":1596046047}
DEBUG - 2020-07-29 15:07:27 --> cURL Class Initialized
DEBUG - 2020-07-29 15:07:27 --> #Main/login | Carga Login |false| []
DEBUG - 2020-07-29 15:07:27 --> Total execution time: 1.2731
DEBUG - 2020-07-29 15:07:57 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 15:07:57 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 15:07:58 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 15:07:58 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-29 15:07:58 --> #Main/login | {"__ci_last_regenerate":1596046047}
DEBUG - 2020-07-29 15:07:58 --> cURL Class Initialized
ERROR - 2020-07-29 15:07:58 --> Severity: Warning --> pg_query(): Query failed: ERROR:  relation &quot;users&quot; does not exist
LINE 1: UPDATE &quot;users&quot; SET &quot;last_login&quot; = '2020-07-29 03:07:58 PM'
               ^ D:\xampp\htdocs\login\system\database\drivers\postgre\postgre_driver.php 242
ERROR - 2020-07-29 15:07:58 --> Query error: ERROR:  relation "users" does not exist
LINE 1: UPDATE "users" SET "last_login" = '2020-07-29 03:07:58 PM'
               ^ - Invalid query: UPDATE "users" SET "last_login" = '2020-07-29 03:07:58 PM'
WHERE "id" = '14'
DEBUG - 2020-07-29 15:07:58 --> #Main/login | userInfo: {"id":"14","email":"hgallardo@trazalog.com","first_name":"hugo","last_name":"gallardo","role":"1","last_login":null,"status":"approved","banned_users":"unband","passmd5":null,"telefono":"2345111111","dni":"2222222222","usernick":"hugoEDS"}
ERROR - 2020-07-29 15:07:58 --> Something Error!
DEBUG - 2020-07-29 15:07:59 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 15:07:59 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 15:08:00 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 15:08:00 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-29 15:08:00 --> #Main/login | {"__ci_last_regenerate":1596046047}
DEBUG - 2020-07-29 15:08:00 --> cURL Class Initialized
DEBUG - 2020-07-29 15:08:00 --> #Main/login | Carga Login |false| []
DEBUG - 2020-07-29 15:08:00 --> Total execution time: 1.3631
DEBUG - 2020-07-29 15:08:36 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 15:08:36 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 15:08:37 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 15:08:37 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-29 15:08:41 --> #Main/login | {"__ci_last_regenerate":1596046047}
DEBUG - 2020-07-29 15:08:42 --> cURL Class Initialized
ERROR - 2020-07-29 15:09:18 --> Severity: Warning --> pg_query(): Query failed: ERROR:  relation &quot;users&quot; does not exist
LINE 1: UPDATE &quot;users&quot; SET &quot;last_login&quot; = '2020-07-29 03:09:18 PM'
               ^ D:\xampp\htdocs\login\system\database\drivers\postgre\postgre_driver.php 242
ERROR - 2020-07-29 15:09:18 --> Query error: ERROR:  relation "users" does not exist
LINE 1: UPDATE "users" SET "last_login" = '2020-07-29 03:09:18 PM'
               ^ - Invalid query: UPDATE "users" SET "last_login" = '2020-07-29 03:09:18 PM'
WHERE "id" = '14'
DEBUG - 2020-07-29 15:09:27 --> #Main/login | userInfo: {"id":"14","email":"hgallardo@trazalog.com","first_name":"hugo","last_name":"gallardo","role":"1","last_login":null,"status":"approved","banned_users":"unband","passmd5":null,"telefono":"2345111111","dni":"2222222222","usernick":"hugoEDS"}
DEBUG - 2020-07-29 15:12:17 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 15:12:17 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 15:12:18 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 15:12:18 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-29 15:12:33 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 15:12:33 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 15:12:34 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 15:12:34 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-29 15:12:34 --> #Main/login | {"__ci_last_regenerate":1596046354}
DEBUG - 2020-07-29 15:12:34 --> cURL Class Initialized
DEBUG - 2020-07-29 15:12:34 --> #Main/login | Carga Login |false| []
DEBUG - 2020-07-29 15:12:34 --> Total execution time: 1.3611
DEBUG - 2020-07-29 15:13:23 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 15:13:23 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 15:13:24 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 15:13:24 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-29 15:13:27 --> #Main/login | {"__ci_last_regenerate":1596046354}
DEBUG - 2020-07-29 15:13:29 --> cURL Class Initialized
ERROR - 2020-07-29 15:13:45 --> Severity: Warning --> pg_query(): Query failed: ERROR:  relation &quot;users&quot; does not exist
LINE 1: UPDATE &quot;users&quot; SET &quot;last_login&quot; = '2020-07-29 03:13:45 PM'
               ^ D:\xampp\htdocs\login\system\database\drivers\postgre\postgre_driver.php 242
ERROR - 2020-07-29 15:13:45 --> Query error: ERROR:  relation "users" does not exist
LINE 1: UPDATE "users" SET "last_login" = '2020-07-29 03:13:45 PM'
               ^ - Invalid query: UPDATE "users" SET "last_login" = '2020-07-29 03:13:45 PM'
WHERE "id" = '14'
DEBUG - 2020-07-29 15:13:50 --> #Main/login | userInfo: {"id":"14","email":"hgallardo@trazalog.com","first_name":"hugo","last_name":"gallardo","role":"1","last_login":null,"status":"approved","banned_users":"unban","passmd5":null,"telefono":"2345111111","dni":"2222222222","usernick":"hugoEDS"}
DEBUG - 2020-07-29 15:14:07 --> #Main/checkLoginUser/
DEBUG - 2020-07-29 15:15:19 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 15:15:19 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 15:15:20 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 15:15:20 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
ERROR - 2020-07-29 15:15:20 --> Severity: Warning --> pg_query(): Query failed: ERROR:  relation &quot;users&quot; does not exist
LINE 2: FROM &quot;users&quot;
             ^ D:\xampp\htdocs\login\system\database\drivers\postgre\postgre_driver.php 242
ERROR - 2020-07-29 15:15:20 --> Query error: ERROR:  relation "users" does not exist
LINE 2: FROM "users"
             ^ - Invalid query: SELECT *
FROM "users"
WHERE "id" = '14'
 LIMIT 1
ERROR - 2020-07-29 15:15:20 --> Severity: Warning --> pg_affected_rows() expects parameter 1 to be resource, boolean given D:\xampp\htdocs\login\system\database\drivers\postgre\postgre_driver.php 345
DEBUG - 2020-07-29 15:15:21 --> Total execution time: 1.6521
DEBUG - 2020-07-29 15:15:50 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 15:15:50 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 15:15:51 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 15:15:51 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-29 15:15:51 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 15:15:51 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 15:15:52 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 15:15:52 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-29 15:15:52 --> #Main/login | {"__ci_last_regenerate":1596046552}
DEBUG - 2020-07-29 15:15:52 --> cURL Class Initialized
DEBUG - 2020-07-29 15:15:52 --> #Main/login | Carga Login |false| []
DEBUG - 2020-07-29 15:15:52 --> Total execution time: 1.3691
DEBUG - 2020-07-29 15:16:09 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 15:16:09 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 15:16:10 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 15:16:10 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-29 15:16:10 --> #Main/login | {"__ci_last_regenerate":1596046552}
DEBUG - 2020-07-29 15:16:10 --> cURL Class Initialized
ERROR - 2020-07-29 15:16:11 --> Severity: Warning --> pg_query(): Query failed: ERROR:  relation &quot;users&quot; does not exist
LINE 1: UPDATE &quot;users&quot; SET &quot;last_login&quot; = '2020-07-29 03:16:10 PM'
               ^ D:\xampp\htdocs\login\system\database\drivers\postgre\postgre_driver.php 242
ERROR - 2020-07-29 15:16:11 --> Query error: ERROR:  relation "users" does not exist
LINE 1: UPDATE "users" SET "last_login" = '2020-07-29 03:16:10 PM'
               ^ - Invalid query: UPDATE "users" SET "last_login" = '2020-07-29 03:16:10 PM'
WHERE "id" = '14'
DEBUG - 2020-07-29 15:16:11 --> #Main/login | userInfo: {"id":"14","email":"hgallardo@trazalog.com","first_name":"hugo","last_name":"gallardo","role":"1","last_login":null,"status":"approved","banned_users":"unban","passmd5":null,"telefono":"2345111111","dni":"2222222222","usernick":"hugoEDS"}
DEBUG - 2020-07-29 15:16:11 --> #Main/checkLoginUser/
DEBUG - 2020-07-29 15:24:32 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 15:24:32 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 15:24:33 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 15:24:33 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-29 15:24:34 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 15:24:34 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 15:24:35 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 15:24:35 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-29 15:24:35 --> #Main/login | {"__ci_last_regenerate":1596047075}
DEBUG - 2020-07-29 15:24:35 --> cURL Class Initialized
DEBUG - 2020-07-29 15:24:35 --> #Main/login | Carga Login |false| []
DEBUG - 2020-07-29 15:24:35 --> Total execution time: 1.2931
DEBUG - 2020-07-29 15:25:01 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 15:25:01 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 15:25:02 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 15:25:02 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-29 15:25:02 --> #Main/login | {"__ci_last_regenerate":1596047075}
DEBUG - 2020-07-29 15:25:02 --> cURL Class Initialized
ERROR - 2020-07-29 15:25:02 --> Severity: Warning --> pg_query(): Query failed: ERROR:  relation &quot;users&quot; does not exist
LINE 1: UPDATE &quot;users&quot; SET &quot;last_login&quot; = '2020-07-29 03:25:02 PM'
               ^ D:\xampp\htdocs\login\system\database\drivers\postgre\postgre_driver.php 242
ERROR - 2020-07-29 15:25:02 --> Query error: ERROR:  relation "users" does not exist
LINE 1: UPDATE "users" SET "last_login" = '2020-07-29 03:25:02 PM'
               ^ - Invalid query: UPDATE "users" SET "last_login" = '2020-07-29 03:25:02 PM'
WHERE "id" = '14'
DEBUG - 2020-07-29 15:25:02 --> #Main/login | userInfo: {"id":"14","email":"hgallardo@trazalog.com","first_name":"hugo","last_name":"gallardo","role":"2","last_login":null,"status":"approved","banned_users":"unban","passmd5":null,"telefono":"2345111111","dni":"2222222222","usernick":"hugoEDS"}
DEBUG - 2020-07-29 15:25:02 --> #Main/checkLoginUser/
DEBUG - 2020-07-29 15:25:14 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 15:25:14 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 15:25:15 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 15:25:15 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
ERROR - 2020-07-29 15:25:15 --> Severity: Warning --> pg_query(): Query failed: ERROR:  relation &quot;users&quot; does not exist
LINE 2: FROM &quot;users&quot;
             ^ D:\xampp\htdocs\login\system\database\drivers\postgre\postgre_driver.php 242
ERROR - 2020-07-29 15:25:15 --> Query error: ERROR:  relation "users" does not exist
LINE 2: FROM "users"
             ^ - Invalid query: SELECT *
FROM "users"
WHERE "id" = '14'
 LIMIT 1
ERROR - 2020-07-29 15:25:15 --> Severity: Warning --> pg_affected_rows() expects parameter 1 to be resource, boolean given D:\xampp\htdocs\login\system\database\drivers\postgre\postgre_driver.php 345
DEBUG - 2020-07-29 15:25:16 --> Total execution time: 1.7421
DEBUG - 2020-07-29 15:25:45 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 15:25:45 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 15:25:46 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 15:25:46 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-29 15:25:46 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 15:25:46 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 15:25:47 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 15:25:47 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-29 15:25:47 --> #Main/login | {"__ci_last_regenerate":1596047147}
DEBUG - 2020-07-29 15:25:47 --> cURL Class Initialized
DEBUG - 2020-07-29 15:25:47 --> #Main/login | Carga Login |false| []
DEBUG - 2020-07-29 15:25:47 --> Total execution time: 1.3791
DEBUG - 2020-07-29 15:28:44 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 15:28:44 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 15:28:46 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 15:28:46 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-29 15:28:46 --> #Main/login | {"__ci_last_regenerate":1596047147}
DEBUG - 2020-07-29 15:28:46 --> cURL Class Initialized
ERROR - 2020-07-29 15:28:46 --> Severity: Warning --> pg_query(): Query failed: ERROR:  relation &quot;users&quot; does not exist
LINE 1: UPDATE &quot;users&quot; SET &quot;last_login&quot; = '2020-07-29 03:28:46 PM'
               ^ D:\xampp\htdocs\login\system\database\drivers\postgre\postgre_driver.php 242
ERROR - 2020-07-29 15:28:46 --> Query error: ERROR:  relation "users" does not exist
LINE 1: UPDATE "users" SET "last_login" = '2020-07-29 03:28:46 PM'
               ^ - Invalid query: UPDATE "users" SET "last_login" = '2020-07-29 03:28:46 PM'
WHERE "id" = '14'
DEBUG - 2020-07-29 15:28:46 --> #Main/login | userInfo: {"id":"14","email":"hgallardo@trazalog.com","first_name":"hugo","last_name":"gallardo","role":"2","last_login":null,"status":"approved","banned_users":"unban","passmd5":null,"telefono":"2345111111","dni":"2222222222","usernick":"hugoEDS"}
DEBUG - 2020-07-29 15:28:46 --> #Main/checkLoginUser/
DEBUG - 2020-07-29 15:29:08 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 15:29:08 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 15:29:09 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 15:29:09 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
ERROR - 2020-07-29 15:29:09 --> Severity: Warning --> pg_query(): Query failed: ERROR:  relation &quot;users&quot; does not exist
LINE 2: FROM &quot;users&quot;
             ^ D:\xampp\htdocs\login\system\database\drivers\postgre\postgre_driver.php 242
ERROR - 2020-07-29 15:29:09 --> Query error: ERROR:  relation "users" does not exist
LINE 2: FROM "users"
             ^ - Invalid query: SELECT *
FROM "users"
WHERE "id" = '14'
 LIMIT 1
ERROR - 2020-07-29 15:29:09 --> Severity: Warning --> pg_affected_rows() expects parameter 1 to be resource, boolean given D:\xampp\htdocs\login\system\database\drivers\postgre\postgre_driver.php 345
DEBUG - 2020-07-29 15:29:10 --> Total execution time: 1.6331
DEBUG - 2020-07-29 15:29:37 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 15:29:37 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 15:29:38 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 15:29:38 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-29 15:29:38 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 15:29:38 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 15:29:39 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 15:29:39 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-29 15:29:39 --> #Main/login | {"__ci_last_regenerate":1596047379}
DEBUG - 2020-07-29 15:29:39 --> cURL Class Initialized
DEBUG - 2020-07-29 15:29:39 --> #Main/login | Carga Login |false| []
DEBUG - 2020-07-29 15:29:39 --> Total execution time: 1.3551
DEBUG - 2020-07-29 15:29:47 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 15:29:48 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 15:29:49 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 15:29:49 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-29 15:29:49 --> #Main/login | {"__ci_last_regenerate":1596047379}
DEBUG - 2020-07-29 15:29:49 --> cURL Class Initialized
ERROR - 2020-07-29 15:29:49 --> Severity: Warning --> pg_query(): Query failed: ERROR:  relation &quot;users&quot; does not exist
LINE 1: UPDATE &quot;users&quot; SET &quot;last_login&quot; = '2020-07-29 03:29:49 PM'
               ^ D:\xampp\htdocs\login\system\database\drivers\postgre\postgre_driver.php 242
ERROR - 2020-07-29 15:29:49 --> Query error: ERROR:  relation "users" does not exist
LINE 1: UPDATE "users" SET "last_login" = '2020-07-29 03:29:49 PM'
               ^ - Invalid query: UPDATE "users" SET "last_login" = '2020-07-29 03:29:49 PM'
WHERE "id" = '1'
DEBUG - 2020-07-29 15:29:49 --> #Main/login | userInfo: {"id":"1","email":"admin@gmail.com","first_name":"Lola","last_name":"Meraz","role":"1","last_login":"2020-07-17 03:40:22 PM","status":"approved","banned_users":"unban","passmd5":null,"telefono":null,"dni":null,"usernick":null}
DEBUG - 2020-07-29 15:29:49 --> #Main/checkLoginUser/
DEBUG - 2020-07-29 15:30:04 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 15:30:04 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 15:30:05 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 15:30:05 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-29 15:30:06 --> Total execution time: 1.9901
DEBUG - 2020-07-29 15:34:22 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 15:34:22 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 15:34:23 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 15:34:23 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-29 15:34:24 --> Total execution time: 1.4981
DEBUG - 2020-07-29 16:08:31 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 16:08:31 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 16:08:32 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 16:08:32 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-29 16:08:32 --> Total execution time: 1.6421
DEBUG - 2020-07-29 16:08:38 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 16:08:38 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 16:08:40 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 16:08:40 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-29 16:08:40 --> Total execution time: 1.8131
DEBUG - 2020-07-29 16:08:41 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 16:08:42 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 16:08:43 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 16:08:44 --> Total execution time: 2.6746
DEBUG - 2020-07-29 16:12:18 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 16:12:18 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 16:12:19 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 16:12:19 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-29 16:12:19 --> Total execution time: 1.7191
DEBUG - 2020-07-29 16:12:28 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 16:12:28 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 16:12:29 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 16:12:29 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-29 16:12:30 --> Total execution time: 1.9301
DEBUG - 2020-07-29 16:12:31 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 16:12:31 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 16:12:32 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 16:12:33 --> Total execution time: 1.4072
DEBUG - 2020-07-29 16:12:59 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 16:12:59 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 16:13:00 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 16:13:00 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-29 16:13:01 --> Total execution time: 1.8111
DEBUG - 2020-07-29 16:14:21 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 16:14:21 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 16:14:23 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 16:14:23 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-29 16:14:24 --> Total execution time: 2.5321
DEBUG - 2020-07-29 16:14:28 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 16:14:28 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 16:14:29 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 16:14:29 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-29 16:14:30 --> Total execution time: 1.6811
DEBUG - 2020-07-29 16:14:48 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 16:14:48 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 16:14:49 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 16:14:49 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-29 16:14:50 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 16:14:50 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 16:14:51 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 16:14:51 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-29 16:14:51 --> #Main/login | {"__ci_last_regenerate":1596050091}
DEBUG - 2020-07-29 16:14:51 --> cURL Class Initialized
DEBUG - 2020-07-29 16:14:51 --> #Main/login | Carga Login |false| []
DEBUG - 2020-07-29 16:14:51 --> Total execution time: 1.5271
DEBUG - 2020-07-29 16:14:58 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 16:14:58 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 16:14:59 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 16:14:59 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-29 16:14:59 --> #Main/login | {"__ci_last_regenerate":1596050091}
DEBUG - 2020-07-29 16:14:59 --> cURL Class Initialized
ERROR - 2020-07-29 16:14:59 --> Severity: Warning --> pg_query(): Query failed: ERROR:  relation &quot;users&quot; does not exist
LINE 1: UPDATE &quot;users&quot; SET &quot;last_login&quot; = '2020-07-29 04:14:59 PM'
               ^ D:\xampp\htdocs\login\system\database\drivers\postgre\postgre_driver.php 242
ERROR - 2020-07-29 16:14:59 --> Query error: ERROR:  relation "users" does not exist
LINE 1: UPDATE "users" SET "last_login" = '2020-07-29 04:14:59 PM'
               ^ - Invalid query: UPDATE "users" SET "last_login" = '2020-07-29 04:14:59 PM'
WHERE "id" = '14'
DEBUG - 2020-07-29 16:14:59 --> #Main/login | userInfo: {"id":"14","email":"hgallardo@trazalog.com","first_name":"hugo","last_name":"gallardo","role":"2","last_login":null,"status":"approved","banned_users":"","passmd5":null,"telefono":"2345111111","dni":"2222222222","usernick":"hugoEDS"}
ERROR - 2020-07-29 16:14:59 --> Something Error!
DEBUG - 2020-07-29 16:15:00 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 16:15:00 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 16:15:01 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 16:15:01 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-29 16:15:01 --> #Main/login | {"__ci_last_regenerate":1596050091}
DEBUG - 2020-07-29 16:15:01 --> cURL Class Initialized
DEBUG - 2020-07-29 16:15:01 --> #Main/login | Carga Login |false| []
DEBUG - 2020-07-29 16:15:01 --> Total execution time: 1.3531
DEBUG - 2020-07-29 16:15:07 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 16:15:07 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 16:15:08 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 16:15:08 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-29 16:15:08 --> #Main/login | {"__ci_last_regenerate":1596050091}
DEBUG - 2020-07-29 16:15:08 --> cURL Class Initialized
ERROR - 2020-07-29 16:15:09 --> Severity: Warning --> pg_query(): Query failed: ERROR:  relation &quot;users&quot; does not exist
LINE 1: UPDATE &quot;users&quot; SET &quot;last_login&quot; = '2020-07-29 04:15:09 PM'
               ^ D:\xampp\htdocs\login\system\database\drivers\postgre\postgre_driver.php 242
ERROR - 2020-07-29 16:15:09 --> Query error: ERROR:  relation "users" does not exist
LINE 1: UPDATE "users" SET "last_login" = '2020-07-29 04:15:09 PM'
               ^ - Invalid query: UPDATE "users" SET "last_login" = '2020-07-29 04:15:09 PM'
WHERE "id" = '14'
DEBUG - 2020-07-29 16:15:09 --> #Main/login | userInfo: {"id":"14","email":"hgallardo@trazalog.com","first_name":"hugo","last_name":"gallardo","role":"2","last_login":null,"status":"approved","banned_users":"","passmd5":null,"telefono":"2345111111","dni":"2222222222","usernick":"hugoEDS"}
ERROR - 2020-07-29 16:15:09 --> Something Error!
DEBUG - 2020-07-29 16:15:09 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 16:15:09 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 16:15:10 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 16:15:10 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-29 16:15:10 --> #Main/login | {"__ci_last_regenerate":1596050091}
DEBUG - 2020-07-29 16:15:10 --> cURL Class Initialized
DEBUG - 2020-07-29 16:15:10 --> #Main/login | Carga Login |false| []
DEBUG - 2020-07-29 16:15:11 --> Total execution time: 1.3601
DEBUG - 2020-07-29 16:15:36 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 16:15:36 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 16:15:37 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 16:15:37 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-29 16:15:37 --> #Main/login | {"__ci_last_regenerate":1596050091}
DEBUG - 2020-07-29 16:15:37 --> cURL Class Initialized
ERROR - 2020-07-29 16:15:37 --> Severity: Warning --> pg_query(): Query failed: ERROR:  relation &quot;users&quot; does not exist
LINE 1: UPDATE &quot;users&quot; SET &quot;last_login&quot; = '2020-07-29 04:15:37 PM'
               ^ D:\xampp\htdocs\login\system\database\drivers\postgre\postgre_driver.php 242
ERROR - 2020-07-29 16:15:37 --> Query error: ERROR:  relation "users" does not exist
LINE 1: UPDATE "users" SET "last_login" = '2020-07-29 04:15:37 PM'
               ^ - Invalid query: UPDATE "users" SET "last_login" = '2020-07-29 04:15:37 PM'
WHERE "id" = '14'
DEBUG - 2020-07-29 16:15:37 --> #Main/login | userInfo: {"id":"14","email":"hgallardo@trazalog.com","first_name":"hugo","last_name":"gallardo","role":"2","last_login":null,"status":"approved","banned_users":"","passmd5":null,"telefono":"2345111111","dni":"2222222222","usernick":"hugoEDS"}
ERROR - 2020-07-29 16:15:37 --> Something Error!
DEBUG - 2020-07-29 16:15:37 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 16:15:37 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 16:15:39 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 16:15:39 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-29 16:15:39 --> #Main/login | {"__ci_last_regenerate":1596050091}
DEBUG - 2020-07-29 16:15:39 --> cURL Class Initialized
DEBUG - 2020-07-29 16:15:39 --> #Main/login | Carga Login |false| []
DEBUG - 2020-07-29 16:15:39 --> Total execution time: 1.3101
DEBUG - 2020-07-29 16:15:58 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 16:15:58 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 16:16:00 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 16:16:00 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-29 16:16:00 --> #Main/login | {"__ci_last_regenerate":1596050091}
DEBUG - 2020-07-29 16:16:00 --> cURL Class Initialized
ERROR - 2020-07-29 16:16:00 --> Severity: Warning --> pg_query(): Query failed: ERROR:  relation &quot;users&quot; does not exist
LINE 1: UPDATE &quot;users&quot; SET &quot;last_login&quot; = '2020-07-29 04:16:00 PM'
               ^ D:\xampp\htdocs\login\system\database\drivers\postgre\postgre_driver.php 242
ERROR - 2020-07-29 16:16:00 --> Query error: ERROR:  relation "users" does not exist
LINE 1: UPDATE "users" SET "last_login" = '2020-07-29 04:16:00 PM'
               ^ - Invalid query: UPDATE "users" SET "last_login" = '2020-07-29 04:16:00 PM'
WHERE "id" = '1'
DEBUG - 2020-07-29 16:16:00 --> #Main/login | userInfo: {"id":"1","email":"admin@gmail.com","first_name":"Lola","last_name":"Meraz","role":"1","last_login":"2020-07-17 03:40:22 PM","status":"approved","banned_users":"unban","passmd5":null,"telefono":null,"dni":null,"usernick":null}
DEBUG - 2020-07-29 16:16:00 --> #Main/checkLoginUser/
DEBUG - 2020-07-29 16:16:14 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 16:16:14 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 16:16:15 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 16:16:15 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-29 16:16:15 --> Total execution time: 1.6641
DEBUG - 2020-07-29 16:29:53 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 16:29:53 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 16:29:54 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 16:29:54 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-29 16:29:55 --> Total execution time: 1.5351
DEBUG - 2020-07-29 16:53:32 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 16:53:32 --> Global POST, GET and COOKIE data sanitized
ERROR - 2020-07-29 16:53:32 --> Severity: error --> Exception: syntax error, unexpected ';', expecting identifier (T_STRING) or variable (T_VARIABLE) or '{' or '$' D:\xampp\htdocs\login\application\controllers\Main.php 540
DEBUG - 2020-07-29 16:54:08 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 16:54:08 --> Global POST, GET and COOKIE data sanitized
ERROR - 2020-07-29 16:54:08 --> Severity: error --> Exception: syntax error, unexpected ';', expecting identifier (T_STRING) or variable (T_VARIABLE) or '{' or '$' D:\xampp\htdocs\login\application\controllers\Main.php 540
DEBUG - 2020-07-29 16:54:30 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 16:54:30 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 16:54:31 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 16:54:31 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-29 16:54:32 --> Total execution time: 1.9181
DEBUG - 2020-07-29 16:55:16 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 16:55:16 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 16:55:17 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 16:55:17 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-29 16:55:17 --> Total execution time: 1.5061
DEBUG - 2020-07-29 17:13:16 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 17:13:16 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 17:13:17 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 17:13:18 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-29 17:13:23 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 17:13:23 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 17:13:25 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 17:13:25 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
ERROR - 2020-07-29 17:13:39 --> Severity: Notice --> Undefined variable: title D:\xampp\htdocs\login\application\views\header.php 16
ERROR - 2020-07-29 17:13:39 --> Severity: Notice --> Undefined variable: etapas D:\xampp\htdocs\login\application\views\membership.php 16
ERROR - 2020-07-29 17:13:39 --> Severity: Warning --> Invalid argument supplied for foreach() D:\xampp\htdocs\login\application\views\membership.php 16
ERROR - 2020-07-29 17:13:39 --> Severity: Notice --> Undefined variable: materiales D:\xampp\htdocs\login\application\views\membership.php 32
ERROR - 2020-07-29 17:13:39 --> Severity: Warning --> Invalid argument supplied for foreach() D:\xampp\htdocs\login\application\views\membership.php 32
ERROR - 2020-07-29 17:13:39 --> Severity: Notice --> Undefined variable: recaptcha D:\xampp\htdocs\login\application\views\login.php 24
DEBUG - 2020-07-29 17:13:39 --> Total execution time: 16.0899
DEBUG - 2020-07-29 17:17:29 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 17:17:29 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 17:17:30 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 17:17:30 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
ERROR - 2020-07-29 17:19:16 --> Severity: Notice --> Undefined variable: title D:\xampp\htdocs\login\application\views\header.php 16
ERROR - 2020-07-29 17:19:16 --> Severity: Notice --> Undefined variable: etapas D:\xampp\htdocs\login\application\views\membership.php 16
ERROR - 2020-07-29 17:19:16 --> Severity: Warning --> Invalid argument supplied for foreach() D:\xampp\htdocs\login\application\views\membership.php 16
ERROR - 2020-07-29 17:19:16 --> Severity: Notice --> Undefined variable: materiales D:\xampp\htdocs\login\application\views\membership.php 32
ERROR - 2020-07-29 17:19:16 --> Severity: Warning --> Invalid argument supplied for foreach() D:\xampp\htdocs\login\application\views\membership.php 32
ERROR - 2020-07-29 17:19:16 --> Severity: Notice --> Undefined variable: recaptcha D:\xampp\htdocs\login\application\views\login.php 24
DEBUG - 2020-07-29 17:19:16 --> Total execution time: 106.9341
DEBUG - 2020-07-29 17:21:27 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 17:21:27 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 17:21:29 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 17:21:29 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
ERROR - 2020-07-29 17:21:32 --> Severity: Notice --> Undefined variable: title D:\xampp\htdocs\login\application\views\header.php 16
ERROR - 2020-07-29 17:21:32 --> Severity: Notice --> Undefined variable: etapas D:\xampp\htdocs\login\application\views\membership.php 16
ERROR - 2020-07-29 17:21:32 --> Severity: Warning --> Invalid argument supplied for foreach() D:\xampp\htdocs\login\application\views\membership.php 16
ERROR - 2020-07-29 17:21:33 --> Severity: Notice --> Undefined variable: materiales D:\xampp\htdocs\login\application\views\membership.php 32
ERROR - 2020-07-29 17:21:33 --> Severity: Warning --> Invalid argument supplied for foreach() D:\xampp\htdocs\login\application\views\membership.php 32
DEBUG - 2020-07-29 17:21:33 --> Total execution time: 5.2973
DEBUG - 2020-07-29 17:24:22 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 17:24:22 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 17:24:23 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 17:24:23 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
ERROR - 2020-07-29 17:24:31 --> Severity: Notice --> Undefined variable: title D:\xampp\htdocs\login\application\views\header.php 16
ERROR - 2020-07-29 17:24:31 --> Severity: Notice --> Undefined variable: etapas D:\xampp\htdocs\login\application\views\membership.php 16
ERROR - 2020-07-29 17:24:31 --> Severity: Warning --> Invalid argument supplied for foreach() D:\xampp\htdocs\login\application\views\membership.php 16
ERROR - 2020-07-29 17:24:31 --> Severity: Notice --> Undefined variable: materiales D:\xampp\htdocs\login\application\views\membership.php 32
ERROR - 2020-07-29 17:24:31 --> Severity: Warning --> Invalid argument supplied for foreach() D:\xampp\htdocs\login\application\views\membership.php 32
DEBUG - 2020-07-29 17:24:31 --> Total execution time: 9.4765
DEBUG - 2020-07-29 17:26:42 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 17:26:42 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 17:26:43 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 17:26:43 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-29 17:29:08 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 17:29:08 --> Global POST, GET and COOKIE data sanitized
ERROR - 2020-07-29 17:29:08 --> 404 Page Not Found: Main/associateBPMrol
DEBUG - 2020-07-29 17:29:32 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 17:29:32 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 17:29:34 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 17:29:34 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
ERROR - 2020-07-29 17:29:43 --> Severity: Notice --> Undefined variable: title D:\xampp\htdocs\login\application\views\header.php 16
ERROR - 2020-07-29 17:29:43 --> Severity: Notice --> Undefined variable: etapas D:\xampp\htdocs\login\application\views\membership.php 16
ERROR - 2020-07-29 17:29:43 --> Severity: Warning --> Invalid argument supplied for foreach() D:\xampp\htdocs\login\application\views\membership.php 16
ERROR - 2020-07-29 17:29:43 --> Severity: Notice --> Undefined variable: materiales D:\xampp\htdocs\login\application\views\membership.php 32
ERROR - 2020-07-29 17:29:43 --> Severity: Warning --> Invalid argument supplied for foreach() D:\xampp\htdocs\login\application\views\membership.php 32
DEBUG - 2020-07-29 17:29:43 --> Total execution time: 11.0366
DEBUG - 2020-07-29 17:46:53 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 17:46:53 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 17:46:54 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 17:46:54 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
DEBUG - 2020-07-29 17:48:44 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 17:48:44 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 17:48:45 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 17:48:45 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
ERROR - 2020-07-29 17:49:14 --> Severity: Notice --> Undefined variable: title D:\xampp\htdocs\login\application\views\header.php 16
ERROR - 2020-07-29 17:49:14 --> Severity: Notice --> Undefined variable: etapas D:\xampp\htdocs\login\application\views\membership.php 16
ERROR - 2020-07-29 17:49:14 --> Severity: Warning --> Invalid argument supplied for foreach() D:\xampp\htdocs\login\application\views\membership.php 16
ERROR - 2020-07-29 17:49:14 --> Severity: Notice --> Undefined variable: materiales D:\xampp\htdocs\login\application\views\membership.php 32
ERROR - 2020-07-29 17:49:14 --> Severity: Warning --> Invalid argument supplied for foreach() D:\xampp\htdocs\login\application\views\membership.php 32
DEBUG - 2020-07-29 17:49:15 --> Total execution time: 31.0378
DEBUG - 2020-07-29 17:51:00 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 17:51:00 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 17:51:01 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 17:51:01 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
ERROR - 2020-07-29 17:51:07 --> Severity: Notice --> Undefined variable: title D:\xampp\htdocs\login\application\views\header.php 16
ERROR - 2020-07-29 17:51:07 --> Severity: Notice --> Undefined variable: description D:\xampp\htdocs\login\application\views\membership.php 18
ERROR - 2020-07-29 17:51:07 --> Severity: Notice --> Undefined variable: description D:\xampp\htdocs\login\application\views\membership.php 18
ERROR - 2020-07-29 17:51:07 --> Severity: Notice --> Undefined variable: materiales D:\xampp\htdocs\login\application\views\membership.php 32
ERROR - 2020-07-29 17:51:07 --> Severity: Warning --> Invalid argument supplied for foreach() D:\xampp\htdocs\login\application\views\membership.php 32
DEBUG - 2020-07-29 17:51:09 --> Total execution time: 8.5125
DEBUG - 2020-07-29 17:52:15 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 17:52:15 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 17:52:16 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 17:52:16 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
ERROR - 2020-07-29 17:52:29 --> Severity: Notice --> Undefined variable: title D:\xampp\htdocs\login\application\views\header.php 16
ERROR - 2020-07-29 17:53:18 --> Severity: Notice --> Undefined variable: description D:\xampp\htdocs\login\application\views\membership.php 18
ERROR - 2020-07-29 17:53:19 --> Severity: Notice --> Undefined variable: description D:\xampp\htdocs\login\application\views\membership.php 18
ERROR - 2020-07-29 17:53:19 --> Severity: Notice --> Undefined variable: materiales D:\xampp\htdocs\login\application\views\membership.php 32
ERROR - 2020-07-29 17:53:19 --> Severity: Warning --> Invalid argument supplied for foreach() D:\xampp\htdocs\login\application\views\membership.php 32
DEBUG - 2020-07-29 17:53:20 --> Total execution time: 65.3137
DEBUG - 2020-07-29 17:53:26 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 17:53:27 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 17:53:28 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 17:53:28 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
ERROR - 2020-07-29 17:53:32 --> Severity: Notice --> Undefined variable: title D:\xampp\htdocs\login\application\views\header.php 16
ERROR - 2020-07-29 17:53:35 --> Severity: Notice --> Undefined variable: materiales D:\xampp\htdocs\login\application\views\membership.php 32
ERROR - 2020-07-29 17:53:35 --> Severity: Warning --> Invalid argument supplied for foreach() D:\xampp\htdocs\login\application\views\membership.php 32
DEBUG - 2020-07-29 17:53:37 --> Total execution time: 10.2216
DEBUG - 2020-07-29 17:57:01 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 17:57:01 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 17:57:02 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 17:57:02 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
ERROR - 2020-07-29 17:57:06 --> Severity: Notice --> Undefined variable: title D:\xampp\htdocs\login\application\views\header.php 16
ERROR - 2020-07-29 17:57:08 --> Severity: Notice --> Undefined variable: materiales D:\xampp\htdocs\login\application\views\membership.php 32
ERROR - 2020-07-29 17:57:08 --> Severity: Warning --> Invalid argument supplied for foreach() D:\xampp\htdocs\login\application\views\membership.php 32
DEBUG - 2020-07-29 17:57:09 --> Total execution time: 7.9915
DEBUG - 2020-07-29 18:05:57 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 18:05:57 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 18:05:58 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 18:05:58 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
ERROR - 2020-07-29 18:06:10 --> Severity: Notice --> Undefined variable: title D:\xampp\htdocs\login\application\views\header.php 16
ERROR - 2020-07-29 18:06:57 --> Severity: Parsing Error --> syntax error, unexpected '$value' (T_VARIABLE), expecting ',' or ';' D:\xampp\htdocs\login\application\views\membership.php 35
DEBUG - 2020-07-29 18:07:13 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 18:07:13 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 18:07:14 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 18:07:14 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
ERROR - 2020-07-29 18:07:20 --> Severity: Notice --> Undefined variable: title D:\xampp\htdocs\login\application\views\header.php 16
DEBUG - 2020-07-29 18:07:27 --> Total execution time: 14.2158
DEBUG - 2020-07-29 18:08:02 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 18:08:02 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 18:08:03 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 18:08:03 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
ERROR - 2020-07-29 18:08:06 --> Severity: Notice --> Undefined variable: title D:\xampp\htdocs\login\application\views\header.php 16
DEBUG - 2020-07-29 18:08:10 --> Total execution time: 7.7934
DEBUG - 2020-07-29 18:12:16 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 18:12:16 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 18:12:18 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 18:12:18 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
ERROR - 2020-07-29 18:12:18 --> Severity: Notice --> Undefined variable: title D:\xampp\htdocs\login\application\views\header.php 16
DEBUG - 2020-07-29 18:12:18 --> Total execution time: 2.4641
DEBUG - 2020-07-29 18:13:19 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 18:13:19 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 18:13:20 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 18:13:20 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
ERROR - 2020-07-29 18:13:20 --> Severity: Notice --> Undefined variable: title D:\xampp\htdocs\login\application\views\header.php 16
DEBUG - 2020-07-29 18:13:20 --> Total execution time: 1.5901
DEBUG - 2020-07-29 18:14:25 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 18:14:25 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 18:14:27 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 18:14:27 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
ERROR - 2020-07-29 18:14:27 --> Severity: Notice --> Undefined variable: title D:\xampp\htdocs\login\application\views\header.php 16
DEBUG - 2020-07-29 18:14:27 --> Total execution time: 1.5471
DEBUG - 2020-07-29 18:16:08 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 18:16:08 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 18:16:10 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 18:16:10 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
ERROR - 2020-07-29 18:16:10 --> Severity: Notice --> Undefined variable: title D:\xampp\htdocs\login\application\views\header.php 16
DEBUG - 2020-07-29 18:16:10 --> Total execution time: 1.7681
DEBUG - 2020-07-29 18:17:07 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 18:17:07 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 18:17:08 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 18:17:08 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
ERROR - 2020-07-29 18:17:08 --> Severity: Notice --> Undefined variable: title D:\xampp\htdocs\login\application\views\header.php 16
DEBUG - 2020-07-29 18:17:08 --> Total execution time: 1.5081
DEBUG - 2020-07-29 18:30:33 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 18:30:33 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 18:30:34 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 18:30:34 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
ERROR - 2020-07-29 18:30:34 --> Severity: Notice --> Undefined variable: title D:\xampp\htdocs\login\application\views\header.php 16
DEBUG - 2020-07-29 18:30:34 --> Total execution time: 1.5051
DEBUG - 2020-07-29 18:31:02 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 18:31:02 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 18:31:04 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 18:31:04 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
ERROR - 2020-07-29 18:31:04 --> Severity: Notice --> Undefined variable: title D:\xampp\htdocs\login\application\views\header.php 16
DEBUG - 2020-07-29 18:31:04 --> Total execution time: 1.4971
DEBUG - 2020-07-29 18:40:34 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 18:40:34 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 18:40:35 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 18:40:35 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
ERROR - 2020-07-29 18:40:35 --> Severity: Notice --> Undefined variable: title D:\xampp\htdocs\login\application\views\header.php 16
DEBUG - 2020-07-29 18:40:35 --> Total execution time: 1.5311
DEBUG - 2020-07-29 18:44:59 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 18:44:59 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 18:45:00 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 18:45:00 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
ERROR - 2020-07-29 18:45:00 --> Severity: Notice --> Undefined variable: title D:\xampp\htdocs\login\application\views\header.php 16
DEBUG - 2020-07-29 18:45:00 --> Total execution time: 1.4941
DEBUG - 2020-07-29 18:51:04 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 18:51:04 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 18:51:05 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 18:51:05 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
ERROR - 2020-07-29 18:51:05 --> Severity: Notice --> Undefined variable: title D:\xampp\htdocs\login\application\views\header.php 16
DEBUG - 2020-07-29 18:51:06 --> Total execution time: 1.5401
DEBUG - 2020-07-29 18:55:31 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 18:55:31 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 18:55:33 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 18:55:33 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
ERROR - 2020-07-29 18:55:33 --> Severity: Notice --> Undefined variable: title D:\xampp\htdocs\login\application\views\header.php 16
DEBUG - 2020-07-29 18:55:33 --> Total execution time: 1.5081
DEBUG - 2020-07-29 18:57:00 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 18:57:00 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 18:57:01 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 18:57:01 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
ERROR - 2020-07-29 18:57:02 --> Severity: Notice --> Undefined variable: title D:\xampp\htdocs\login\application\views\header.php 16
DEBUG - 2020-07-29 18:57:02 --> Total execution time: 1.5291
DEBUG - 2020-07-29 18:58:56 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 18:58:56 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 18:58:57 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 18:58:57 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
ERROR - 2020-07-29 18:58:58 --> Severity: Notice --> Undefined variable: title D:\xampp\htdocs\login\application\views\header.php 16
DEBUG - 2020-07-29 18:58:58 --> Total execution time: 2.0211
DEBUG - 2020-07-29 19:02:12 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 19:02:12 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 19:02:13 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 19:02:13 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
ERROR - 2020-07-29 19:02:14 --> Severity: Notice --> Undefined variable: title D:\xampp\htdocs\login\application\views\header.php 16
DEBUG - 2020-07-29 19:02:14 --> Total execution time: 1.4961
DEBUG - 2020-07-29 19:06:29 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 19:06:29 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 19:06:30 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 19:06:30 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
ERROR - 2020-07-29 19:06:30 --> Severity: Notice --> Undefined variable: title D:\xampp\htdocs\login\application\views\header.php 16
DEBUG - 2020-07-29 19:06:31 --> Total execution time: 1.4991
DEBUG - 2020-07-29 19:09:18 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 19:09:18 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 19:09:19 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 19:09:19 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
ERROR - 2020-07-29 19:09:19 --> Severity: Notice --> Undefined variable: title D:\xampp\htdocs\login\application\views\header.php 16
DEBUG - 2020-07-29 19:09:20 --> Total execution time: 1.5041
DEBUG - 2020-07-29 19:13:13 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 19:13:13 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 19:13:14 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 19:13:14 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
ERROR - 2020-07-29 19:13:15 --> Severity: Notice --> Undefined variable: title D:\xampp\htdocs\login\application\views\header.php 16
DEBUG - 2020-07-29 19:13:15 --> Total execution time: 1.9831
DEBUG - 2020-07-29 19:15:27 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 19:15:27 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 19:15:28 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 19:15:28 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
ERROR - 2020-07-29 19:15:28 --> Severity: Notice --> Undefined variable: title D:\xampp\htdocs\login\application\views\header.php 16
DEBUG - 2020-07-29 19:15:28 --> Total execution time: 1.5141
DEBUG - 2020-07-29 19:18:06 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 19:18:06 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 19:18:07 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 19:18:07 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
ERROR - 2020-07-29 19:18:07 --> Severity: Notice --> Undefined variable: title D:\xampp\htdocs\login\application\views\header.php 16
DEBUG - 2020-07-29 19:18:07 --> Total execution time: 1.5011
DEBUG - 2020-07-29 19:19:02 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 19:19:02 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 19:19:04 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 19:19:04 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
ERROR - 2020-07-29 19:19:04 --> Severity: Notice --> Undefined variable: title D:\xampp\htdocs\login\application\views\header.php 16
DEBUG - 2020-07-29 19:19:04 --> Total execution time: 1.5901
DEBUG - 2020-07-29 19:19:31 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 19:19:31 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 19:19:32 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 19:19:32 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
ERROR - 2020-07-29 19:19:32 --> Severity: Notice --> Undefined variable: title D:\xampp\htdocs\login\application\views\header.php 16
DEBUG - 2020-07-29 19:19:32 --> Total execution time: 1.5171
DEBUG - 2020-07-29 19:20:03 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 19:20:03 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 19:20:04 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 19:20:04 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
ERROR - 2020-07-29 19:20:04 --> Severity: Notice --> Undefined variable: title D:\xampp\htdocs\login\application\views\header.php 16
DEBUG - 2020-07-29 19:20:04 --> Total execution time: 1.5011
DEBUG - 2020-07-29 19:20:39 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 19:20:39 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 19:20:40 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 19:20:40 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
ERROR - 2020-07-29 19:20:40 --> Severity: Notice --> Undefined variable: title D:\xampp\htdocs\login\application\views\header.php 16
DEBUG - 2020-07-29 19:20:40 --> Total execution time: 1.4931
DEBUG - 2020-07-29 19:22:15 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 19:22:15 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 19:22:17 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 19:22:17 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
ERROR - 2020-07-29 19:22:17 --> Severity: Notice --> Undefined variable: title D:\xampp\htdocs\login\application\views\header.php 16
DEBUG - 2020-07-29 19:22:17 --> Total execution time: 1.5011
DEBUG - 2020-07-29 19:22:57 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 19:22:57 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 19:22:58 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 19:22:58 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
ERROR - 2020-07-29 19:22:58 --> Severity: Notice --> Undefined variable: title D:\xampp\htdocs\login\application\views\header.php 16
DEBUG - 2020-07-29 19:22:59 --> Total execution time: 1.4971
DEBUG - 2020-07-29 19:23:08 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 19:23:08 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 19:23:09 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 19:23:09 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
ERROR - 2020-07-29 19:23:10 --> Severity: Notice --> Undefined variable: title D:\xampp\htdocs\login\application\views\header.php 16
DEBUG - 2020-07-29 19:23:10 --> Total execution time: 1.5011
DEBUG - 2020-07-29 19:23:56 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 19:23:56 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 19:23:57 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 19:23:57 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
ERROR - 2020-07-29 19:23:57 --> Severity: Notice --> Undefined variable: title D:\xampp\htdocs\login\application\views\header.php 16
DEBUG - 2020-07-29 19:23:57 --> Total execution time: 1.4991
DEBUG - 2020-07-29 19:25:30 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 19:25:30 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 19:25:31 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 19:25:31 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
ERROR - 2020-07-29 19:25:31 --> Severity: Notice --> Undefined variable: title D:\xampp\htdocs\login\application\views\header.php 16
DEBUG - 2020-07-29 19:25:31 --> Total execution time: 1.5341
DEBUG - 2020-07-29 19:26:29 --> UTF-8 Support Enabled
DEBUG - 2020-07-29 19:26:29 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-29 19:26:30 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-29 19:26:30 --> Config file loaded: D:\xampp\htdocs\login\application\config/email.php
ERROR - 2020-07-29 19:26:30 --> Severity: Notice --> Undefined variable: title D:\xampp\htdocs\login\application\views\header.php 16
DEBUG - 2020-07-29 19:26:30 --> Total execution time: 1.5341
