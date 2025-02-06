<?php
$DB_PARAMETERS = json_decode(file_get_contents("./db/db_config.json"));
$DB_CONNECTION_STRING = "host=$DB_PARAMETERS->host port=$DB_PARAMETERS->port dbname=$DB_PARAMETERS->dbname user=$DB_PARAMETERS->user password=$DB_PARAMETERS->password";
session_start();

// подключение к БД
$dbconnect = pg_connect($DB_CONNECTION_STRING);
if (!$dbconnect) {
	echo "Ошибка подключения к БД";
	http_response_code(500);
	exit;
}
