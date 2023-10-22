<?php
require_once("settings.php");
require_once("utilities.php");
// require_once("C:/xampp/htdocs/dsts/python_module");

$au = new auth_ssh();
checkAuLoggedIN($au);
checkAuIsAdmin($au);

// $path_scrapper = 'C:/xampp/htdocs/dsts/python_module/JSONScrapper.exe';
// $path_parser = 'C:/xampp/htdocs/dsts/python_module/JSONParser.exe';
$path_scrapper = 'C:/Users/vania/Documents/PyCharmProjects/DSTSWebScrapper/JSONSScrapper.py';
$path_parser = 'C:/Users/vania/Documents/PyCharmProjects/DSTSWebScrapper/JSONParser.py';

if (isset($_POST['code_stop'])) {
    $result = shell_exec("kill $(ps aux | grep '[p]ython $path_scrapper' | awk '{print $2}')");
    $result = shell_exec("kill $(ps aux | grep '[p]ython $path_parser' | awk '{print $2}')");
}

if (isset($_POST['article_name']) && isset($_POST['catalogue_name'])) {
    $article_name = $_POST['article_name'];
    $catalogue_name = $_POST['catalogue_name'];
} else {
    echo "ERROR!";
    exit;
}

$line = escapeshellcmd($catalogue_name) . " " . escapeshellcmd($article_name);
$path = "SEARCH_REQUESTS.txt";
file_put_contents($path, $line);

set_time_limit(7200);

// https://scriptcoding.ru/wscript-shell-run/?ysclid=lm87h0cour390551456
// https://www.script-coding.com/WSH/WshShell.html
// $WshShell = new COM('WScript.Shell');
// $oExec = $WshShell->Run("py $path_scrapper");

$command = shell_exec("py $path_scrapper");
$command = shell_exec("py $path_parser");

set_time_limit(120);

$path = "SEARCH_REQUESTS.txt";
file_put_contents($path, "");

$number = getCountFiles("LOGS");
$path_output = "LOGS/" . $number . "_output.txt";
echo file_get_contents($path_output);


// echo json_encode($response);
exit;



function getCountFiles($path)
{

    $files = glob($path . "/*");

    if ($files) {
        return count($files) - 1;
    }

    return 0;
}
