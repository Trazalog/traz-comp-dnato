<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

DEBUG - 2020-09-18 10:03:04 --> UTF-8 Support Enabled
DEBUG - 2020-09-18 10:03:04 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-09-18 10:03:06 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-09-18 10:03:06 --> Config file loaded: D:\xampp\htdocs\traz-comp-dnato\application\config/email.php
DEBUG - 2020-09-18 10:03:06 --> UTF-8 Support Enabled
DEBUG - 2020-09-18 10:03:06 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-09-18 10:03:08 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-09-18 10:03:08 --> Config file loaded: D:\xampp\htdocs\traz-comp-dnato\application\config/email.php
DEBUG - 2020-09-18 10:03:08 --> #Main/login | {"__ci_last_regenerate":1600434188}
DEBUG - 2020-09-18 10:03:08 --> cURL Class Initialized
DEBUG - 2020-09-18 10:03:08 --> #Main/login | Carga Login |false| []
DEBUG - 2020-09-18 10:03:08 --> Total execution time: 1.9271
DEBUG - 2020-09-18 10:03:17 --> UTF-8 Support Enabled
DEBUG - 2020-09-18 10:03:17 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-09-18 10:03:18 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-09-18 10:03:18 --> Config file loaded: D:\xampp\htdocs\traz-comp-dnato\application\config/email.php
DEBUG - 2020-09-18 10:03:18 --> #Main/login | {"__ci_last_regenerate":1600434188}
DEBUG - 2020-09-18 10:03:18 --> cURL Class Initialized
ERROR - 2020-09-18 10:03:19 --> Severity: Warning --> pg_query(): Query failed: ERROR:  relation &quot;users&quot; does not exist
LINE 1: UPDATE &quot;users&quot; SET &quot;last_login&quot; = '2020-09-18 10:03:18 AM'
               ^ D:\xampp\htdocs\traz-comp-dnato\system\database\drivers\postgre\postgre_driver.php 242
ERROR - 2020-09-18 10:03:19 --> Query error: ERROR:  relation "users" does not exist
LINE 1: UPDATE "users" SET "last_login" = '2020-09-18 10:03:18 AM'
               ^ - Invalid query: UPDATE "users" SET "last_login" = '2020-09-18 10:03:18 AM'
WHERE "id" = '32'
DEBUG - 2020-09-18 10:03:19 --> #Main/login | userInfo: {"id":"32","email":"pepe@almacen.com","first_name":"Domingo D.","last_name":"Ramos","role":"1","last_login":null,"status":"approved","banned_users":"unban","passmd5":null,"telefono":"4242424","dni":"21212121","usernick":"almacentools","depo_id":null}
DEBUG - 2020-09-18 10:03:19 --> #TRAZA | #REST | #CURL | #URL >> http://10.142.0.7:8080/bonita/loginservice
DEBUG - 2020-09-18 10:03:19 --> #TRAZA | #REST | #CURL | #HEADER SALIDA >> GET /bonita/loginservice?username=admin&password=123traza&redirect=false HTTP/1.1
Host: 10.142.0.7:8080
Accept: */*


DEBUG - 2020-09-18 10:03:19 --> #TRAZA | #REST | #CURL | #HTTP_CODE >> 200
DEBUG - 2020-09-18 10:03:19 --> #TRAZA | #REST | #CURL | #HEADER RESPUESTA>> HTTP/1.1 200 
Set-Cookie: bonita.tenant=1
Set-Cookie: JSESSIONID=E8228E234039CB91C4E0AE6F493F582A; Path=/bonita; HttpOnly
Set-Cookie: X-Bonita-API-Token=54aab642-9e24-4cbc-bfe2-050b38186dab; Path=/bonita
Content-Length: 0
Date: Fri, 18 Sep 2020 13:03:19 GMT


DEBUG - 2020-09-18 10:03:19 --> #TRAZA | #REST | #CURL | #BODY >> ""
DEBUG - 2020-09-18 10:03:19 --> #TRAZA | #REST | #CURL | #URL >> http://10.142.0.7:8080/bonita/API/identity/user?p=0&c=50
DEBUG - 2020-09-18 10:03:20 --> #TRAZA | #REST | #CURL | #HEADER SALIDA >> GET /bonita/API/identity/user?p=0&c=50 HTTP/1.1
Host: 10.142.0.7:8080
Accept: */*
Cookie: bonita_tenant=1;JSESSIONID=E8228E234039CB91C4E0AE6F493F582A;X-Bonita-API-Token=54aab642-9e24-4cbc-bfe2-050b38186dab
X-Bonita-API-Token: 54aab642-9e24-4cbc-bfe2-050b38186dab
Content-Type: application/json


DEBUG - 2020-09-18 10:03:20 --> #TRAZA | #REST | #CURL | #HTTP_CODE >> 200
DEBUG - 2020-09-18 10:03:20 --> #TRAZA | #REST | #CURL | #HEADER RESPUESTA>> HTTP/1.1 200 
Cache-Control: no-store, no-cache, must-revalidate, proxy-revalidate
Content-Range: 0-50/92
Content-Type: application/json;charset=UTF-8
Transfer-Encoding: chunked
Date: Fri, 18 Sep 2020 13:03:19 GMT


DEBUG - 2020-09-18 10:03:20 --> #TRAZA | #REST | #CURL | #BODY >> "[{\"firstname\":\"Almacen\",\"icon\":\"icons\/default\/icon_user.png\",\"creation_date\":\"2019-09-09 20:23:03.464\",\"userName\":\"almacen1\",\"title\":\"\",\"created_by_user_id\":\"-1\",\"enabled\":\"true\",\"lastname\":\"\",\"last_connection\":\"2020-05-08 00:51:26.973\",\"password\":\"\",\"manager_id\":\"0\",\"id\":\"5\",\"job_title\":\"\",\"last_update_date\":\"2019-11-20 16:17:09.001\"},{\"firstname\":\"Generador \",\"icon\":\"icons\/default\/icon_user.png\",\"creation_date\":\"2020-03-18 21:18:23.383\",\"userName\":\"generador1\",\"title\":\"\",\"created_by_user_id\":\"7\",\"enabled\":\"true\",\"lastname\":\"1\",\"last_connection\":\"2020-08-07 13:55:34.444\",\"password\":\"\",\"manager_id\":\"0\",\"id\":\"401\",\"job_title\":\"\",\"last_update_date\":\"2020-03-18 21:18:23.383\"},{\"firstname\":\"Transportista\",\"icon\":\"icons\/default\/icon_user.png\",\"creation_date\":\"2020-03-18 21:18:55.078\",\"userName\":\"transportista1\",\"title\":\"\",\"created_by_user_id\":\"7\",\"enabled\":\"true\",\"lastname\":\"1\",\"last_connection\":\"2020-08-25 04:26:11.534\",\"password\":\"\",\"manager_id\":\"0\",\"id\":\"402\",\"job_title\":\"\",\"last_update_date\":\"2020-03-18 21:18:55.078\"},{\"firstname\":\"Jose \",\"icon\":\"icons\/default\/icon_user.png\",\"creation_date\":\"2020-02-12 16:45:09.410\",\"userName\":\"jose.aballay\",\"title\":\"\",\"created_by_user_id\":\"7\",\"enabled\":\"true\",\"lastname\":\"Aballay\",\"last_connection\":\"\",\"password\":\"\",\"manager_id\":\"0\",\"id\":\"220\",\"job_title\":\"\",\"last_update_date\":\"2020-02-12 16:45:09.410\"},{\"firstname\":\"Admin\",\"icon\":\"icons\/default\/icon_user.png\",\"creation_date\":\"2019-09-09 22:26:58.889\",\"userName\":\"admin\",\"title\":\"\",\"created_by_user_id\":\"2\",\"enabled\":\"true\",\"lastname\":\"Admin\",\"last_connection\":\"2020-09-18 13:03:19.318\",\"password\":\"\",\"manager_id\":\"0\",\"id\":\"7\",\"job_title\":\"\",\"last_update_date\":\"2019-09-10 00:21:50.023\"},{\"firstname\":\"Duilio \",\"icon\":\"icons\/default\/icon_user.png\",\"creation_date\":\"2020-02-12 16:45:09.314\",\"userName\":\"duilio.alcaraz\",\"title\":\"\",\"created_by_user_id\":\"7\",\"enabled\":\"true\",\"lastname\":\"Alcaraz\",\"last_connection\":\"\",\"password\":\"\",\"manager_id\":\"0\",\"id\":\"212\",\"job_title\":\"\",\"last_update_date\":\"2020-02-12 16:45:09.314\"},{\"firstname\":\"Miguel\",\"icon\":\"icons\/default\/icon_user.png\",\"creation_date\":\"2020-02-12 16:45:09.308\",\"userName\":\"miguel.allendez\",\"title\":\"\",\"created_by_user_id\":\"7\",\"enabled\":\"true\",\"lastname\":\"Allendez\",\"last_connection\":\"\",\"password\":\"\",\"manager_id\":\"0\",\"id\":\"211\",\"job_title\":\"\",\"last_update_date\":\"2020-02-12 16:45:09.308\"},{\"firstname\":\"Javier \",\"icon\":\"icons\/default\/icon_user.png\",\"creation_date\":\"2020-02-12 16:45:09.201\",\"userName\":\"javier.arias\",\"title\":\"\",\"created_by_user_id\":\"7\",\"enabled\":\"true\",\"lastname\":\"Arias\",\"last_connection\":\"\",\"password\":\"\",\"manager_id\":\"0\",\"id\":\"202\",\"job_title\":\"\",\"last_update_date\":\"2020-02-12 16:45:09.201\"},{\"firstname\":\"Rafael \",\"icon\":\"icons\/default\/icon_user.png\",\"creation_date\":\"2020-02-12 16:45:09.392\",\"userName\":\"rafael.arias\",\"title\":\"\",\"created_by_user_id\":\"7\",\"enabled\":\"true\",\"lastname\":\"Arias\",\"last_connection\":\"\",\"password\":\"\",\"manager_id\":\"0\",\"id\":\"219\",\"job_title\":\"\",\"last_update_date\":\"2020-02-12 16:45:09.392\"},{\"firstname\":\"Paula \",\"icon\":\"icons\/default\/icon_user.png\",\"creation_date\":\"2020-02-12 16:45:09.542\",\"userName\":\"paula.azcurra\",\"title\":\"\",\"created_by_user_id\":\"7\",\"enabled\":\"true\",\"lastname\":\"Azcurra\",\"last_connection\":\"\",\"password\":\"\",\"manager_id\":\"0\",\"id\":\"232\",\"job_title\":\"\",\"last_update_date\":\"2020-02-12 16:45:09.542\"},{\"firstname\":\"M. Gabriela \",\"icon\":\"icons\/default\/icon_user.png\",\"creation_date\":\"2020-02-12 16:45:09.125\",\"userName\":\"gabriela.balderramo\",\"title\":\"\",\"created_by_user_id\":\"7\",\"enabled\":\"true\",\"lastname\":\"Balderramo\",\"last_connection\":\"\",\"password\":\"\",\"manager_id\":\"0\",\"id\":\"201\",\"job_title\":\"\",\"last_update_date\":\"2020-02-12 16:45:09.125\"},{\"firstname\":\"Emilio \",\"icon\":\"icons\/default\/icon_user.png\",\"creation_date\":\"2020-02-12 16:45:09.280\",\"userName\":\"emilio.balderramo\",\"title\":\"\",\"created_by_user_id\":\"7\",\"enabled\":\"true\",\"lastname\":\"Balderramo\",\"last_connection\":\"\",\"password\":\"\",\"manager_id\":\"0\",\"id\":\"209\",\"job_title\":\"\",\"last_update_date\":\"2020-02-12 16:45:09.280\"},{\"firstname\":\"J. Manuel \",\"icon\":\"icons\/default\/icon_user.png\",\"creation_date\":\"2020-02-12 16:45:09.579\",\"userName\":\"manuel.balderramo\",\"title\":\"\",\"created_by_user_id\":\"7\",\"enabled\":\"true\",\"lastname\":\"Balderramo\",\"last_connection\":\"\",\"password\":\"\",\"manager_id\":\"0\",\"id\":\"236\",\"job_title\":\"\",\"last_update_date\":\"2020-02-12 16:45:09.579\"},{\"firstname\":\"Pablo \",\"icon\":\"icons\/default\/icon_user.png\",\"creation_date\":\"2020-02-12 16:45:09.596\",\"userName\":\"pablo.balderramo\",\"title\":\"\",\"created_by_user_id\":\"7\",\"enabled\":\"true\",\"lastname\":\"Balderramo\",\"last_connection\":\"\",\"password\":\"\",\"manager_id\":\"0\",\"id\":\"238\",\"job_title\":\"\",\"last_update_date\":\"2020-02-12 16:45:09.596\"},{\"firstname\":\"Mart\u00edn\",\"icon\":\"icons\/default\/icon_user.png\",\"creation_date\":\"2020-02-12 16:45:09.604\",\"userName\":\"martin.balderramo\",\"title\":\"\",\"created_by_user_id\":\"7\",\"enabled\":\"true\",\"lastname\":\"Balderramo\",\"last_connection\":\"\",\"password\":\"\",\"manager_id\":\"0\",\"id\":\"239\",\"job_title\":\"\",\"last_update_date\":\"2020-02-12 16:45:09.604\"},{\"firstname\":\"Maria Eugenia\",\"icon\":\"icons\/default\/icon_user.png\",\"creation_date\":\"2020-02-12 16:45:09.609\",\"userName\":\"eugenia.balderramo\",\"title\":\"\",\"created_by_user_id\":\"7\",\"enabled\":\"true\",\"lastname\":\"Balderramo\",\"last_connection\":\"\",\"password\":\"\",\"manager_id\":\"0\",\"id\":\"240\",\"job_title\":\"\",\"last_update_date\":\"2020-02-12 16:45:09.609\"},{\"firstname\":\"Operario\",\"icon\":\"icons\/default\/icon_user.png\",\"creation_date\":\"2020-06-30 14:18:53.465\",\"userName\":\"bascula\",\"title\":\"\",\"created_by_user_id\":\"7\",\"enabled\":\"true\",\"lastname\":\"Bascula\",\"last_connection\":\"2020-08-20 14:57:19.906\",\"password\":\"\",\"manager_id\":\"0\",\"id\":\"501\",\"job_title\":\"\",\"last_update_date\":\"2020-08-07 20:15:26.728\"},{\"firstname\":\"Lia \",\"icon\":\"icons\/default\/icon_user.png\",\"creation_date\":\"2019-12-04 16:31:08.061\",\"userName\":\"lia.busatto\",\"title\":\"\",\"created_by_user_id\":\"7\",\"enabled\":\"true\",\"lastname\":\"Busatto\",\"last_connection\":\"\",\"password\":\"\",\"manager_id\":\"0\",\"id\":\"102\",\"job_title\":\"\",\"last_update_date\":\"2019-12-04 16:34:30.471\"},{\"firstname\":\"Ema Amado\",\"icon\":\"icons\/default\/icon_user.png\",\"creation_date\":\"2020-08-20 14:33:56.733\",\"userName\":\"emamadobustos\",\"title\":\"Sr\",\"created_by_user_id\":\"7\",\"enabled\":\"true\",\"lastname\":\"Bustos\",\"last_connection\":\"2020-09-17 20:33:09.749\",\"password\":\"\",\"manager_id\":\"0\",\"id\":\"617\",\"job_title\":\"Human resources benefits\",\"last_update_date\":\"2020-08-24 21:39:01.245\"},{\"firstname\":\"Cindy\",\"icon\":\"icons\/default\/icon_user.png\",\"creation_date\":\"2020-07-31 19:25:43.528\",\"userName\":\"cindynero\",\"title\":\"Sr\",\"created_by_user_id\":\"7\",\"enabled\":\"false\",\"lastname\":\"Cindy\",\"last_connection\":\"\",\"password\":\"\",\"manager_id\":\"0\",\"id\":\"505\",\"job_title\":\"Human resources benefits\",\"last_update_date\":\"2020-07-31 19:25:43.528\"},{\"firstname\":\"Nicolas \",\"icon\":\"icons\/default\/icon_user.png\",\"creation_date\":\"2020-02-12 16:45:09.381\",\"userName\":\"nicolas.delgado\",\"title\":\"\",\"created_by_user_id\":\"7\",\"enabled\":\"true\",\"lastname\":\"Delgado\",\"last_connection\":\"\",\"password\":\"\",\"manager_id\":\"0\",\"id\":\"218\",\"job_title\":\"\",\"last_update_date\":\"2020-02-12 16:45:09.381\"},{\"firstname\":\"Olga D\u00edsima\",\"icon\":\"icons\/default\/icon_user.png\",\"creation_date\":\"2020-08-05 21:00:02.324\",\"userName\":\"olga\",\"title\":\"Sr\",\"created_by_user_id\":\"7\",\"enabled\":\"false\",\"lastname\":\"Del Hoyo\",\"last_connection\":\"\",\"password\":\"\",\"manager_id\":\"0\",\"id\":\"602\",\"job_title\":\"Human resources benefits\",\"last_update_date\":\"2020-08-05 21:00:02.324\"},{\"firstname\":\"Florencia\",\"icon\":\"icons\/default\/icon_user.png\",\"creation_date\":\"2020-08-05 21:26:26.238\",\"userName\":\"flordelav\",\"title\":\"Sre\",\"created_by_user_id\":\"7\",\"enabled\":\"false\",\"lastname\":\"DellaVerga\",\"last_connection\":\"\",\"password\":\"\",\"manager_id\":\"0\",\"id\":\"603\",\"job_title\":\"Amigue con manije\",\"last_update_date\":\"2020-08-05 21:26:26.238\"},{\"firstname\":\"Florencia\",\"icon\":\"icons\/default\/icon_user.png\",\"creation_date\":\"2020-08-05 21:29:16.503\",\"userName\":\"flordelav2\",\"title\":\"Sre\",\"created_by_user_id\":\"7\",\"enabled\":\"false\",\"lastname\":\"DellaVerga\",\"last_connection\":\"\",\"password\":\"\",\"manager_id\":\"0\",\"id\":\"604\",\"job_title\":\"Amigue con manije\",\"last_update_date\":\"2020-08-05 21:29:16.503\"},{\"firstname\":\"Florencia\",\"icon\":\"icons\/default\/icon_user.png\",\"creation_date\":\"2020-08-05 21:31:22.458\",\"userName\":\"flordelav3\",\"title\":\"Sre\",\"created_by_user_id\":\"7\",\"enabled\":\"true\",\"lastname\":\"DellaVerga\",\"last_connection\":\"\",\"password\":\"\",\"manager_id\":\"0\",\"id\":\"605\",\"job_title\":\"Amigue con manije\",\"last_update_date\":\"2020-08-05 21:31:22.547\"},{\"firstname\":\"Almacen\",\"icon\":\"icons\/default\/icon_user.png\",\"creation_date\":\"2019-09-19 00:22:39.157\",\"userName\":\"almacen1_pe\",\"title\":\"\",\"created_by_user_id\":\"7\",\"enabled\":\"true\",\"lastname\":\"demo.pe\",\"last_connection\":\"2019-09-19 00:26:15.650\",\"password\":\"\",\"manager_id\":\"0\",\"id\":\"17\",\"job_title\":\"\",\"last_update_date\":\"2019-09-19 00:22:39.157\"},{\"firstname\":\"Mantenedor1\",\"icon\":\"icons\/default\/icon_user.png\",\"creation_date\":\"2019-09-19 00:30:19.808\",\"userName\":\"mantenedor1_pe\",\"title\":\"\",\"created_by_user_id\":\"7\",\"enabled\":\"true\",\"lastname\":\"demo.pe\",\"last_connection\":\"2019-09-20 17:39:40.507\",\"password\":\"\",\"manager_id\":\"0\",\"id\":\"18\",\"job_title\":\"\",\"last_update_date\":\"2019-09-20 16:36:04.280\"},{\"firstname\":\"Planificador\",\"icon\":\"icons\/default\/icon_user.png\",\"creation_date\":\"2019-09-19 00:31:18.968\",\"userName\":\"planificador1_pe\",\"title\":\"\",\"created_by_user_id\":\"7\",\"enabled\":\"true\",\"lastname\":\"demo.pe\",\"last_connection\":\"2019-10-16 12:00:26.373\",\"password\":\"\",\"manager_id\":\"0\",\"id\":\"19\",\"job_title\":\"\",\"last_update_date\":\"2019-09-19 00:31:18.968\"},{\"firstname\":\"Solicitante\",\"icon\":\"icons\/default\/icon_user.png\",\"creation_date\":\"2019-09-19 00:31:46.497\",\"userName\":\"solicitante1_pe\",\"title\":\"\",\"created_by_user_id\":\"7\",\"enabled\":\"true\",\"lastname\":\"demo.pe\",\"last_connection\":\"2019-09-20 15:46:24.442\",\"password\":\"\",\"manager_id\":\"0\",\"id\":\"20\",\"job_title\":\"\",\"last_update_date\":\"2019-09-19 00:31:46.497\"},{\"firstname\":\"Supervisor\",\"icon\":\"icons\/default\/icon_user.png\",\"creation_date\":\"2019-09-19 00:33:06.317\",\"userName\":\"supervisor1_pe\",\"title\":\"\",\"created_by_user_id\":\"7\",\"enabled\":\"true\",\"lastname\":\"demo.pe\",\"last_connection\":\"2019-11-01 16:01:10.976\",\"password\":\"\",\"manager_id\":\"0\",\"id\":\"21\",\"job_title\":\"\",\"last_update_date\":\"2019-09-19 00:33:06.317\"},{\"firstname\":\"Operario\",\"icon\":\"icons\/default\/icon_user.png\",\"creation_date\":\"2020-08-13 18:49:49.071\",\"userName\":\"operarioDF-Escombrera\",\"title\":\"Sr\",\"created_by_user_id\":\"7\",\"enabled\":\"true\",\"lastname\":\"Depo DF - Escombrera\",\"last_connection\":\"\",\"password\":\"\",\"manager_id\":\"0\",\"id\":\"610\",\"job_title\":\"Human resources benefits\",\"last_update_date\":\"2020-08-14 19:52:03.846\"},{\"firstname\":\"Operario\",\"icon\":\"icons\/default\/icon_user.png\",\"creation_date\":\"2020-08-13 19:07:54.281\",\"userName\":\"operarioDF-RellenoSanitario\",\"title\":\"Sr\",\"created_by_user_id\":\"7\",\"enabled\":\"true\",\"lastname\":\"Depo DF - Relleno Sanitario\",\"last_connection\":\"\",\"password\":\"\",\"manager_id\":\"0\",\"id\":\"612\",\"job_title\":\"Human resources benefits\",\"last_update_date\":\"2020-08-14 19:53:29.213\"},{\"firstname\":\"Operario\",\"icon\":\"icons\/default\/icon_user.png\",\"creation_date\":\"2020-08-13 18:01:50.617\",\"userName\":\"operarioPRI-Chatarra\",\"title\":\"Sr\",\"created_by_user_id\":\"7\",\"enabled\":\"true\",\"lastname\":\"Depo PRI - Chatarra\",\"last_connection\":\"\",\"password\":\"\",\"manager_id\":\"0\",\"id\":\"609\",\"job_title\":\"Human resources benefits\",\"last_update_date\":\"2020-08-14 19:54:04.258\"},{\"firstname\":\"Operario\",\"icon\":\"icons\/default\/icon_user.png\",\"creation_date\":\"2020-08-13 17:53:19.565\",\"userName\":\"operarioPRI-RSU\",\"title\":\"Sr\",\"created_by_user_id\":\"7\",\"enabled\":\"true\",\"lastname\":\"Depo PRI - RSU\",\"last_connection\":\"2020-09-17 20:24:55.653\",\"password\":\"\",\"manager_id\":\"0\",\"id\":\"608\",\"job_title\":\"Human resources benefits\",\"last_update_date\":\"2020-08-14 19:54:43.645\"},{\"firstname\":\"Operario\",\"icon\":\"icons\/default\/icon_user.png\",\"creation_date\":\"2020-08-14 18:01:08.459\",\"userName\":\"operarioPRO-Industriales\",\"title\":\"\",\"created_by_user_id\":\"7\",\"enabled\":\"true\",\"lastname\":\"Depo PRO - Industriales\",\"last_connection\":\"\",\"password\":\"\",\"manager_id\":\"0\",\"id\":\"613\",\"job_title\":\"\",\"last_update_date\":\"2020-08-14 19:55:25.111\"},{\"firstname\":\"Operario\",\"icon\":\"icons\/default\/icon_user.png\",\"creation_date\":\"2020-08-13 18:53:15.219\",\"userName\":\"operarioPRO-Ramas\",\"title\":\"Sr\",\"created_by_user_id\":\"7\",\"enabled\":\"true\",\"lastname\":\"Depo PRO - Ramas\",\"last_connection\":\"\",\"password\":\"\",\"manager_id\":\"0\",\"id\":\"611\",\"job_title\":\"Human resources benefits\",\"last_update_date\":\"2020-08-14 19:56:03.416\"},{\"firstname\":\"Operario\",\"icon\":\"icons\/default\/icon_user.png\",\"creation_date\":\"2020-06-30 14:20:44.370\",\"userName\":\"descarga\",\"title\":\"\",\"created_by_user_id\":\"7\",\"enabled\":\"true\",\"lastname\":\"Descarga\",\"last_connection\":\"2020-06-30 14:21:24.945\",\"password\":\"\",\"manager_id\":\"0\",\"id\":\"502\",\"job_title\":\"\",\"last_update_date\":\"2020-06-30 14:20:44.370\"},{\"firstname\":\"Jose \",\"icon\":\"icons\/default\/icon_user.png\",\"creation_date\":\"2020-02-12 16:45:09.415\",\"userName\":\"jose.espin \",\"title\":\"\",\"created_by_user_id\":\"7\",\"enabled\":\"true\",\"lastname\":\"Espin \",\"last_connection\":\"\",\"password\":\"\",\"manager_id\":\"0\",\"id\":\"221\",\"job_title\":\"\",\"last_update_date\":\"2020-02-12 16:45:09.415\"},{\"firstname\":\"admin\",\"icon\":\"icons\/default\/icon_user.png\",\"creation_date\":\"2020-02-14 22:51:18.994\",\"userName\":\"adminfin\",\"title\":\"\",\"created_by_user_id\":\"7\",\"enabled\":\"true\",\"lastname\":\"fin\",\"last_connection\":\"2020-05-05 18:34:47.393\",\"password\":\"\",\"manager_id\":\"0\",\"id\":\"301\",\"job_title\":\"\",\"last_update_date\":\"2020-02-14 22:51:18.994\"},{\"firstname\":\"Elver\",\"icon\":\"icons\/default\/icon_user.png\",\"creation_date\":\"2020-08-22 03:59:08.199\",\"userName\":\"elvergadura\",\"title\":\"Sr\",\"created_by_user_id\":\"7\",\"enabled\":\"true\",\"lastname\":\"Gadura\",\"last_connection\":\"2020-09-17 20:26:08.096\",\"password\":\"\",\"manager_id\":\"0\",\"id\":\"618\",\"job_title\":\"Human resources benefits\",\"last_update_date\":\"2020-09-07 17:11:54.314\"},{\"firstname\":\"hugo\",\"icon\":\"icons\/default\/icon_user.png\",\"creation_date\":\"2020-08-13 16:27:57.681\",\"userName\":\"hugues\",\"title\":\"Sr\",\"created_by_user_id\":\"7\",\"enabled\":\"true\",\"lastname\":\"gallardo\",\"last_connection\":\"\",\"password\":\"\",\"manager_id\":\"0\",\"id\":\"606\",\"job_title\":\"Human resources benefits\",\"last_update_date\":\"2020-08-13 16:27:57.800\"},{\"firstname\":\"Liliana\",\"icon\":\"icons\/default\/icon_user.png\",\"creation_date\":\"2020-02-12 16:45:09.229\",\"userName\":\"liliana.gomez\",\"title\":\"\",\"created_by_user_id\":\"7\",\"enabled\":\"true\",\"lastname\":\"Gomez\",\"last_connection\":\"\",\"password\":\"\",\"manager_id\":\"0\",\"id\":\"205\",\"job_title\":\"\",\"last_update_date\":\"2020-02-12 16:45:09.229\"},{\"firstname\":\"Ariel \",\"icon\":\"icons\/default\/icon_user.png\",\"creation_date\":\"2020-02-12 16:45:09.338\",\"userName\":\"ariel.gomez\",\"title\":\"\",\"created_by_user_id\":\"7\",\"enabled\":\"true\",\"lastname\":\"Gomez\",\"last_connection\":\"\",\"password\":\"\",\"manager_id\":\"0\",\"id\":\"214\",\"job_title\":\"\",\"last_update_date\":\"2020-02-12 16:45:09.338\"},{\"firstname\":\"Victor \",\"icon\":\"icons\/default\/icon_user.png\",\"creation_date\":\"2020-02-12 16:45:09.430\",\"userName\":\"victor.gonzalez\",\"title\":\"\",\"created_by_user_id\":\"7\",\"enabled\":\"true\",\"lastname\":\"Gonzalez\",\"last_connection\":\"\",\"password\":\"\",\"manager_id\":\"0\",\"id\":\"222\",\"job_title\":\"\",\"last_update_date\":\"2020-02-12 16:45:09.430\"},{\"firstname\":\"Gustavo \",\"icon\":\"icons\/default\/icon_user.png\",\"creation_date\":\"2020-02-12 16:45:09.441\",\"userName\":\"gustavo.gonzalez\",\"title\":\"\",\"created_by_user_id\":\"7\",\"enabled\":\"true\",\"lastname\":\"Gonzalez\",\"last_connection\":\"\",\"password\":\"\",\"manager_id\":\"0\",\"id\":\"224\",\"job_title\":\"\",\"last_update_date\":\"2020-02-12 16:45:09.441\"},{\"firstname\":\"IT\",\"icon\":\"icons\/default\/icon_user.png\",\"creation_date\":\"2019-09-09 20:24:09.289\",\"userName\":\"IT\",\"title\":\"\",\"created_by_user_id\":\"-1\",\"enabled\":\"true\",\"lastname\":\"IT\",\"last_connection\":\"\",\"password\":\"\",\"manager_id\":\"0\",\"id\":\"6\",\"job_title\":\"\",\"last_update_date\":\"2019-09-09 20:24:09.289\"},{\"firstname\":\"Juan Carlos \",\"icon\":\"icons\/default\/icon_user.png\",\"creation_date\":\"2020-02-12 16:45:09.563\",\"userName\":\"juan.jofre\",\"title\":\"\",\"created_by_user_id\":\"7\",\"enabled\":\"true\",\"lastname\":\"Jofre\",\"last_connection\":\"\",\"password\":\"\",\"manager_id\":\"0\",\"id\":\"234\",\"job_title\":\"\",\"last_update_date\":\"2020-02-12 16:45:09.563\"},{\"firstname\":\"Ruben \",\"icon\":\"icons\/default\/icon_user.png\",\"creation_date\":\"2020-08-18 19:03:25.504\",\"userName\":\"rubenJuarez\",\"title\":\"Sr\",\"created_by_user_id\":\"7\",\"enabled\":\"true\",\"lastname\":\"Juarez\",\"last_connection\":\"2020-09-17 17:44:34.478\",\"password\":\"\",\"manager_id\":\"0\",\"id\":\"614\",\"job_title\":\"Human resources benefits\",\"last_update_date\":\"2020-08-20 14:09:40.667\"},{\"firstname\":\"Eduardo \",\"icon\":\"icons\/default\/icon_user.png\",\"creation_date\":\"2020-02-12 16:45:09.330\",\"userName\":\"eduardo.llanos\",\"title\":\"\",\"created_by_user_id\":\"7\",\"enabled\":\"true\",\"lastname\":\"Llanos\",\"last_connection\":\"\",\"password\":\"\",\"manager_id\":\"0\",\"id\":\"213\",\"job_title\":\"\",\"last_update_date\":\"2020-02-12 16:45:09.330\"},{\"firstname\":\"Leonardo \",\"icon\":\"icons\/default\/icon_user.png\",\"creation_date\":\"2020-02-12 16:45:09.365\",\"userName\":\"leonardo.maffezzini\",\"title\":\"\",\"created_by_user_id\":\"7\",\"enabled\":\"true\",\"lastname\":\"Maffezzini\",\"last_connection\":\"\",\"password\":\"\",\"manager_id\":\"0\",\"id\":\"217\",\"job_title\":\"\",\"last_update_date\":\"2020-02-12 16:45:09.365\"}]"
DEBUG - 2020-09-18 10:03:20 --> #TRAZA | #BPM >> Usuario No Encontrado
ERROR - 2020-09-18 10:03:20 --> #TRAZA|MAIN|LOGIN|NO HAY USUARIO EN BPM CON EL NICK >> almacentools
DEBUG - 2020-09-18 10:03:20 --> #Main/checkLoginUser/
DEBUG - 2020-09-18 12:45:18 --> UTF-8 Support Enabled
DEBUG - 2020-09-18 12:45:18 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-09-18 12:45:20 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-09-18 12:45:20 --> Config file loaded: D:\xampp\htdocs\traz-comp-dnato\application\config/email.php
DEBUG - 2020-09-18 12:45:20 --> #Main/login | {"__ci_last_regenerate":1600443918,"id":"32","email":"pepe@almacen.com","first_name":"Domingo D.","last_name":"Ramos","role":"1","last_login":null,"status":"approved","banned_users":"unban","passmd5":null,"telefono":"4242424","dni":"21212121","usernick":"almacentools","depo_id":null}
DEBUG - 2020-09-18 12:45:20 --> #Main/login Sesion Existente
DEBUG - 2020-09-18 12:45:47 --> UTF-8 Support Enabled
DEBUG - 2020-09-18 12:45:47 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-09-18 12:45:48 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-09-18 12:45:48 --> Config file loaded: D:\xampp\htdocs\traz-comp-dnato\application\config/email.php
DEBUG - 2020-09-18 12:45:48 --> #Main/login | {"__ci_last_regenerate":1600443918,"id":"32","email":"pepe@almacen.com","first_name":"Domingo D.","last_name":"Ramos","role":"1","last_login":null,"status":"approved","banned_users":"unban","passmd5":null,"telefono":"4242424","dni":"21212121","usernick":"almacentools","depo_id":null}
DEBUG - 2020-09-18 12:45:48 --> #Main/login Sesion Existente
DEBUG - 2020-09-18 20:24:25 --> UTF-8 Support Enabled
DEBUG - 2020-09-18 20:24:25 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-09-18 20:24:27 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-09-18 20:24:27 --> Config file loaded: D:\xampp\htdocs\traz-comp-dnato\application\config/email.php
DEBUG - 2020-09-18 20:24:28 --> Total execution time: 3.1512
DEBUG - 2020-09-18 20:25:27 --> UTF-8 Support Enabled
DEBUG - 2020-09-18 20:25:27 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-09-18 20:25:28 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-09-18 20:25:28 --> Config file loaded: D:\xampp\htdocs\traz-comp-dnato\application\config/email.php
DEBUG - 2020-09-18 20:25:29 --> Total execution time: 1.9871
DEBUG - 2020-09-18 20:26:05 --> UTF-8 Support Enabled
DEBUG - 2020-09-18 20:26:05 --> Global POST, GET and COOKIE data sanitized
DEBUG - 2020-09-18 20:26:06 --> Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.
DEBUG - 2020-09-18 20:26:06 --> Config file loaded: D:\xampp\htdocs\traz-comp-dnato\application\config/email.php
DEBUG - 2020-09-18 23:26:07 --> Total execution time: 1.9531