<?php
require_once("settings.php");
require_once("utilities.php");

$au = new auth_ssh();
checkAuLoggedIN($au);
checkAuIsAdmin($au);

if (isset($_POST['producer_id']) && isset($_POST['new_producer_name_dsts']) && isset($_POST['new_producer_name'])) {
    $producer_id = $_POST['producer_id'];
    $new_producer_name_dsts = $_POST['new_producer_name_dsts'];
    $new_producer_name = $_POST['new_producer_name'];
} else if (isset($_POST['editCharacteristics'])) {
    editCharacteristics(json_decode($_POST['characteristics']));
    exit;
} else {
    echo "ERROR!";
    exit;
}

$response = array();

$Producer = new Producer($producer_id);

if ($new_producer_name_dsts != "") {
    $Producer->setProducerDSTSName($new_producer_name_dsts);
    $response['setProducerDSTSName'] = true;
}


if ($new_producer_name != "") {
    $new_producer_id = getProducerIdByName($new_producer_name);
    $Producer->setSimmilarProducer($new_producer_id);
    $response['setSimmilarProducer'] = true;
}

echo json_encode($response);
exit;


function editCharacteristics($characteristics)
{
    global $dbconnect;

    $query = "";
    foreach ($characteristics as $characteristic) {
        $query .= queryUpdateCharacteristic($characteristic->realName, $characteristic->newName);
    }
    pg_query($dbconnect, $query);
}

function queryUpdateCharacteristic($realCharacteristic, $newCharacteristic)
{
    return "UPDATE characteristics_comparison SET characteristic_alt = \$antihype1\$$newCharacteristic\$antihype1\$ WHERE characteristic_alt = '$realCharacteristic';";
}
