<?php
require_once("utilities.php");

$au = new auth_ssh();

checkAuLoggedIN($au);

if (isset($_POST['flag']))
    $flag = $_POST['flag'];
else
    exit;

$SERVER_HOST = "localhost";
$SERVER_PORT = 8083;
$PATH_TO_SCRAPPER = "DSTS-SCRAPPER-MODULE";

if ($flag == "GetServerParameters") {
    echo json_encode(["host" => $SERVER_HOST, "port" => $SERVER_PORT]);
    exit;
}


// https://docker-php.readthedocs.io/en/latest/cookbook/container-run/

if ($flag == "StartDaemon") {
    exit;
}

if ($flag == "StopDaemon") {
    exit;
}

exit;
