<?php
require_once("settings.php");
require_once("utilities.php");

$au = new auth_ssh();
checkAuLoggedIN($au);
checkAuIsAdmin($au);

if(isset($_POST['analogs_json']))
    $analogs_js_json = $_POST['analogs_json'];
else 
    exit;

$analogs = json_decode($analogs_js_json);
// print_r($analogs);

// Кросс-референс DONALDSON (P550777; P 550777;): 
// AC DELCO (PF2161; PF940; 06940; 6940; P940;), 
// ADVANCE MIXER (A0777;), 
// AG CHEM EQUIPMENT (607071;), 
// AGCO (607071; 74322701;), 
// ALCO (SP1011;), 
// ALLIS-CHALMERS (73124135;)

$article = $analogs[0];

$result_parse = "Кросс-референс " . $article->producer_name;
$result_parse .= " (" . articleNameVariations($article->article_name) . "): ";

array_shift($analogs);

$last_producer_name = "";
foreach ($analogs as $key => $analog) {
    if($key == 0) {
        $last_producer_name = $analog->producer_name;
        $result_parse .= $analog->producer_name;
        $result_parse .= " (";
    } 
    
    if($analog->producer_name == $last_producer_name) {
        $result_parse .= " ";
    } else {
        $last_producer_name = $analog->producer_name;
        $result_parse .= "), " . $analog->producer_name . " (";
    }
    $result_parse .= articleNameVariations($analog->article_name);
    
    if($key == count($analogs)-1) {
        $result_parse .= ")";
    }
}


echo $result_parse;
exit;



function articleNameVariations($article_name) {
    global $ARRAY_SPLIT_CHARS;

    $arrayChars = $ARRAY_SPLIT_CHARS;

    // echo $article_name . " | ";
    
    $article_name_splitted = getSplitArticleName($article_name, $arrayChars);

    // echo $article_name_splitted . " | ";

    if (!$article_name_splitted) {    
        return $article_name . ";";
    } else {
        $articleNameVariations = "";
        array_push($arrayChars, '');
        foreach ($arrayChars as $key => $char) {
            $articleNameVariations .= concatArrayByChar($article_name_splitted, $char) . ";";
            if($key < count($arrayChars)-1)
                $articleNameVariations .= " ";
        }
        return $articleNameVariations;
    }


}

?>