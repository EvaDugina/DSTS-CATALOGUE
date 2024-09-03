<?php
require_once("utilities.php");

// $au = new auth_ssh();

// checkAuLoggedIN($au);

if (isset($_POST['flag']))
    $flag = $_POST['flag'];
else
    exit;



$SERVER_HOST = "localhost";
$SERVER_PORT = 8083;

if ($flag == "GetServerParameters") {
    echo json_encode(["host" => $SERVER_HOST, "port" => $SERVER_PORT]);
    exit;
}

if ($flag == "StartDaemon") {
    // $command = shell_exec("uvicorn server:app --reload --host $SERVER_HOST --port $SERVER_PORT");
    // exit;
}

if ($flag == "StopDaemon") {
    // $command = shell_exec("");
    // exit;
}

exit;
