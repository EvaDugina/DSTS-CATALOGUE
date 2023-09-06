<?php
require_once("settings.php");
require_once("utilities.php");

$au = new auth_ssh();
checkAuLoggedIN($au);
checkAuIsAdmin($au);

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

$path_scrapper = 'C:/Users/vania/Documents/PyCharmProjects/DSTSWebScrapper/JSONSScrapper.py';
$command = shell_exec("py $path_scrapper");

$path_parser = 'C:/Users/vania/Documents/PyCharmProjects/DSTSWebScrapper/JSONParser.py';
$command = shell_exec("py $path_parser");

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
