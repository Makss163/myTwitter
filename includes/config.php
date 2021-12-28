<?php
//константы
define('SITE_NAME', 'Twitter'); // имя сайта
define('HOST', 'http://' . $_SERVER['HTTP_HOST']); // домен
define('DB_HOST', 'localhost'); // директория БД
define('DB_NAME', 'mytwitter'); // имя БД
define('DB_USER', 'root'); // имя пользователя для входа в БД
define('DB_PASS', ''); // пароль для входа в БД
session_start();
