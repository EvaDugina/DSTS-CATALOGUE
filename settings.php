<?php
$DB_CONNECTION_STRING = "host=localhost port=4445 dbname=dsts user=zamay86 password=matan42";
session_start();

// подключение к БД
$dbconnect = pg_connect($DB_CONNECTION_STRING);
if (!$dbconnect) {
	echo "Ошибка подключения к БД";
	http_response_code(500);
	exit;
}
