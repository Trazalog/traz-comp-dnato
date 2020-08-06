<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

DEBUG - 2020-07-17 17:16:09 --> UTF-8 Support Enabled
DEBUG - 2020-07-17 17:16:09 --> No URI present. Default controller set.
DEBUG - 2020-07-17 17:16:09 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-17 17:16:15 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-17 17:16:15 --> Config file loaded: D:\xampp\htdocs\traz-comp-dnato\application\config/email.php
DEBUG - 2020-07-17 17:16:15 --> #Main/index | {"__ci_last_regenerate":1595016975}
DEBUG - 2020-07-17 17:16:15 --> #Main/index | No email
DEBUG - 2020-07-17 17:16:15 --> UTF-8 Support Enabled
DEBUG - 2020-07-17 17:16:15 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-17 17:16:21 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-17 17:16:21 --> Config file loaded: D:\xampp\htdocs\traz-comp-dnato\application\config/email.php
DEBUG - 2020-07-17 17:17:03 --> #Main/login | {"__ci_last_regenerate":1595016975}
DEBUG - 2020-07-17 17:17:12 --> cURL Class Initialized
DEBUG - 2020-07-17 17:17:39 --> #Main/login | Carga Login |false| []
ERROR - 2020-07-17 17:17:49 --> Severity: Warning --> pg_query(): Query failed: ERROR:  relation &quot;settings&quot; does not exist
LINE 2: FROM &quot;settings&quot;
             ^ D:\xampp\htdocs\traz-comp-dnato\system\database\drivers\postgre\postgre_driver.php 242
ERROR - 2020-07-17 17:17:49 --> Query error: ERROR:  relation "settings" does not exist
LINE 2: FROM "settings"
             ^ - Invalid query: SELECT *
FROM "settings"
ERROR - 2020-07-17 17:32:49 --> Severity: Error --> Uncaught Error: Call to a member function row() on boolean in D:\xampp\htdocs\traz-comp-dnato\application\models\User_model.php:307
Stack trace:
#0 D:\xampp\htdocs\traz-comp-dnato\application\views\header.php(4): User_model->getAllSettings()
#1 D:\xampp\htdocs\traz-comp-dnato\system\core\Loader.php(962): include('D:\\xampp\\htdocs...')
#2 D:\xampp\htdocs\traz-comp-dnato\system\core\Loader.php(489): CI_Loader->_ci_load(Array)
#3 D:\xampp\htdocs\traz-comp-dnato\application\controllers\Main.php(721): CI_Loader->view('header', Array)
#4 D:\xampp\htdocs\traz-comp-dnato\system\core\CodeIgniter.php(532): Main->login()
#5 D:\xampp\htdocs\traz-comp-dnato\index.php(315): require_once('D:\\xampp\\htdocs...')
#6 {main}
  thrown D:\xampp\htdocs\traz-comp-dnato\application\models\User_model.php 307
DEBUG - 2020-07-17 17:58:56 --> UTF-8 Support Enabled
DEBUG - 2020-07-17 17:58:56 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-17 17:58:57 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-17 17:58:57 --> Config file loaded: D:\xampp\htdocs\traz-comp-dnato\application\config/email.php
DEBUG - 2020-07-17 17:58:57 --> #Main/login | {"__ci_last_regenerate":1595019537}
DEBUG - 2020-07-17 17:58:57 --> cURL Class Initialized
DEBUG - 2020-07-17 17:58:57 --> #Main/login | Carga Login |false| []
ERROR - 2020-07-17 17:58:57 --> Severity: Warning --> pg_query(): Query failed: ERROR:  relation &quot;settings&quot; does not exist
LINE 2: FROM &quot;settings&quot;
             ^ D:\xampp\htdocs\traz-comp-dnato\system\database\drivers\postgre\postgre_driver.php 242
ERROR - 2020-07-17 17:58:57 --> Query error: ERROR:  relation "settings" does not exist
LINE 2: FROM "settings"
             ^ - Invalid query: SELECT *
FROM "settings"
ERROR - 2020-07-17 17:58:58 --> Severity: error --> Exception: Call to a member function row() on boolean D:\xampp\htdocs\traz-comp-dnato\application\models\User_model.php 307
DEBUG - 2020-07-17 17:59:37 --> UTF-8 Support Enabled
DEBUG - 2020-07-17 17:59:37 --> No URI present. Default controller set.
DEBUG - 2020-07-17 17:59:37 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-17 17:59:38 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-17 17:59:38 --> Config file loaded: D:\xampp\htdocs\traz-comp-dnato\application\config/email.php
DEBUG - 2020-07-17 17:59:38 --> #Main/index | {"__ci_last_regenerate":1595019537}
DEBUG - 2020-07-17 17:59:38 --> #Main/index | No email
DEBUG - 2020-07-17 17:59:39 --> UTF-8 Support Enabled
DEBUG - 2020-07-17 17:59:39 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-17 17:59:40 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-17 17:59:40 --> Config file loaded: D:\xampp\htdocs\traz-comp-dnato\application\config/email.php
DEBUG - 2020-07-17 17:59:40 --> #Main/login | {"__ci_last_regenerate":1595019537}
DEBUG - 2020-07-17 17:59:40 --> cURL Class Initialized
DEBUG - 2020-07-17 17:59:40 --> #Main/login | Carga Login |false| []
ERROR - 2020-07-17 17:59:40 --> Severity: Warning --> pg_query(): Query failed: ERROR:  relation &quot;settings&quot; does not exist
LINE 2: FROM &quot;settings&quot;
             ^ D:\xampp\htdocs\traz-comp-dnato\system\database\drivers\postgre\postgre_driver.php 242
ERROR - 2020-07-17 17:59:40 --> Query error: ERROR:  relation "settings" does not exist
LINE 2: FROM "settings"
             ^ - Invalid query: SELECT *
FROM "settings"
ERROR - 2020-07-17 17:59:41 --> Severity: error --> Exception: Call to a member function row() on boolean D:\xampp\htdocs\traz-comp-dnato\application\models\User_model.php 307
DEBUG - 2020-07-17 18:01:35 --> UTF-8 Support Enabled
DEBUG - 2020-07-17 18:01:35 --> No URI present. Default controller set.
DEBUG - 2020-07-17 18:01:35 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-17 18:01:40 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-17 18:01:40 --> Config file loaded: D:\xampp\htdocs\traz-comp-dnato\application\config/email.php
DEBUG - 2020-07-17 18:01:40 --> #Main/index | {"__ci_last_regenerate":1595019537}
DEBUG - 2020-07-17 18:01:40 --> #Main/index | No email
DEBUG - 2020-07-17 18:01:41 --> UTF-8 Support Enabled
DEBUG - 2020-07-17 18:01:41 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-17 18:01:57 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-17 18:02:11 --> Config file loaded: D:\xampp\htdocs\traz-comp-dnato\application\config/email.php
DEBUG - 2020-07-17 18:02:19 --> #Main/login | {"__ci_last_regenerate":1595019537}
DEBUG - 2020-07-17 18:02:36 --> cURL Class Initialized
DEBUG - 2020-07-17 18:02:58 --> #Main/login | Carga Login |false| []
ERROR - 2020-07-17 18:03:01 --> Severity: Warning --> pg_query(): Query failed: ERROR:  relation &quot;settings&quot; does not exist
LINE 2: FROM &quot;settings&quot;
             ^ D:\xampp\htdocs\traz-comp-dnato\system\database\drivers\postgre\postgre_driver.php 242
ERROR - 2020-07-17 18:03:01 --> Query error: ERROR:  relation "settings" does not exist
LINE 2: FROM "settings"
             ^ - Invalid query: SELECT *
FROM "settings"
ERROR - 2020-07-17 18:23:03 --> Severity: Error --> Uncaught Error: Call to a member function row() on boolean in D:\xampp\htdocs\traz-comp-dnato\application\models\User_model.php:307
Stack trace:
#0 D:\xampp\htdocs\traz-comp-dnato\application\views\header.php(4): User_model->getAllSettings()
#1 D:\xampp\htdocs\traz-comp-dnato\system\core\Loader.php(962): include('D:\\xampp\\htdocs...')
#2 D:\xampp\htdocs\traz-comp-dnato\system\core\Loader.php(489): CI_Loader->_ci_load(Array)
#3 D:\xampp\htdocs\traz-comp-dnato\application\controllers\Main.php(721): CI_Loader->view('header', Array)
#4 D:\xampp\htdocs\traz-comp-dnato\system\core\CodeIgniter.php(532): Main->login()
#5 D:\xampp\htdocs\traz-comp-dnato\index.php(315): require_once('D:\\xampp\\htdocs...')
#6 {main}
  thrown D:\xampp\htdocs\traz-comp-dnato\application\models\User_model.php 307
DEBUG - 2020-07-17 18:23:16 --> UTF-8 Support Enabled
DEBUG - 2020-07-17 18:23:16 --> No URI present. Default controller set.
DEBUG - 2020-07-17 18:23:16 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-17 18:23:32 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-17 18:23:46 --> Config file loaded: D:\xampp\htdocs\traz-comp-dnato\application\config/email.php
DEBUG - 2020-07-17 18:23:49 --> #Main/index | {"__ci_last_regenerate":1595021012}
DEBUG - 2020-07-17 18:23:49 --> #Main/index | No email
DEBUG - 2020-07-17 18:23:50 --> UTF-8 Support Enabled
DEBUG - 2020-07-17 18:23:50 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-07-17 18:24:04 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-07-17 18:24:26 --> Config file loaded: D:\xampp\htdocs\traz-comp-dnato\application\config/email.php
DEBUG - 2020-07-17 18:25:13 --> #Main/login | {"__ci_last_regenerate":1595021012}
DEBUG - 2020-07-17 18:25:15 --> cURL Class Initialized
DEBUG - 2020-07-17 18:25:25 --> #Main/login | Carga Login |false| []
ERROR - 2020-07-17 18:27:15 --> Severity: Error --> Uncaught Error: Call to undefined method CI_DB_postgre_driver::result() in D:\xampp\htdocs\traz-comp-dnato\application\models\User_model.php:307
Stack trace:
#0 D:\xampp\htdocs\traz-comp-dnato\application\views\header.php(4): User_model->getAllSettings()
#1 D:\xampp\htdocs\traz-comp-dnato\system\core\Loader.php(962): include('D:\\xampp\\htdocs...')
#2 D:\xampp\htdocs\traz-comp-dnato\system\core\Loader.php(489): CI_Loader->_ci_load(Array)
#3 D:\xampp\htdocs\traz-comp-dnato\application\controllers\Main.php(721): CI_Loader->view('header', Array)
#4 D:\xampp\htdocs\traz-comp-dnato\system\core\CodeIgniter.php(532): Main->login()
#5 D:\xampp\htdocs\traz-comp-dnato\index.php(315): require_once('D:\\xampp\\htdocs...')
#6 {main}
  thrown D:\xampp\htdocs\traz-comp-dnato\application\models\User_model.php 307
