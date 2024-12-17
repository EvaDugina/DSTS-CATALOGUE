<?php
require_once("utilities.php");

$au = new auth_ssh();

checkAuLoggedIN($au);

if (isset($_POST['flag']))
    $flag = $_POST['flag'];
else
    exit;

$scrapper_parameters = json_decode(file_get_contents("./scrapper_config.json"));
$SCRAPPER_HOST = $scrapper_parameters->host;
$SCRAPPER_PORT = $scrapper_parameters->port;

if ($flag == "GetServerParameters") {
    echo json_encode(["host" => $SCRAPPER_HOST, "port" => $SCRAPPER_PORT]);
    exit;
}

if ($flag == "StartDaemon") {
    exit;
}

if ($flag == "StopDaemon") {
    exit;
}

exit;
