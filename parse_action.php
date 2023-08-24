<?php
require_once("settings.php");
require_once("utilities.php");

$au = new auth_ssh();
checkAuLoggedIN($au);
checkAuIsAdmin($au);

if(isset($_POST['catalog_packages_json']))
    $catalog_packages_json = $_POST['catalog_packages_json'];
else 
    exit;

$catalog_packages = json_decode($catalog_packages_json);
// print_r($catalog_packages);

$catalogue_name = $catalog_packages[0]->selected_catalogue;
array_shift($catalog_packages);

// Кросс-референс DONALDSON (P550777; P 550777;): 
// AC DELCO (PF2161; PF940; 06940; 6940; P940;), 
// ADVANCE MIXER (A0777;), 
// AG CHEM EQUIPMENT (607071;), 
// AGCO (607071; 74322701;), 
// ALCO (SP1011;), 
// ALLIS-CHALMERS (73124135;)

// print_r($catalog_packages);

if ($catalogue_name == "ВСЕ") {
    $result_parse = "";
    foreach($ARRAY_CATALOGUES as $catalogue) {
        if($catalogue[1]) {
            $result_parse .= getCrossRefByCatalogue($catalog_packages, $catalogue[0]);
            $result_parse .= "\t\t\t\t\t";
        }
    }
} else {
    $result_parse = getCrossRefByCatalogue($catalog_packages, $catalogue_name);
}


echo $result_parse;
exit;


function getCrossRefByCatalogue($catalog_packages, $catalogue_name) {

    $cross_ref = "";

    $last_producer_name = "";

    $cross_ref .= "Кросс-референс " . $catalogue_name;
    foreach ($catalog_packages[0] as $analog) {
        $cross_ref .= " (" . articleNameVariations($analog->article_name) . "): ";
    }

    foreach ($catalog_packages as $key => $catalogue) {

        if($key == 0)
            continue;

        foreach ($catalogue as $analog) {

            // echo $catalogue_name;
            // print_r($analog);
            // echo "\n\n\n";
    
            if($analog->catalogue_name != $catalogue_name) 
                continue;
    
            if($key == 1) {
                $last_producer_name = $analog->producer_name_dsts;
                $cross_ref .= $analog->producer_name_dsts;
                $cross_ref .= " (";
            } else if($analog->producer_name_dsts == $last_producer_name) {
                $cross_ref .= " ";
            } else {
                $last_producer_name = $analog->producer_name_dsts;
                $cross_ref .= "), " . $analog->producer_name_dsts . " (";
            }
            $cross_ref .= articleNameVariations($analog->article_name);
            
            if($key == count($catalog_packages)-1) {
                $cross_ref .= ")";
            }
    
        }
    }

    return $cross_ref;
}


function articleNameVariations($article_name) {
    global $ARRAY_SPLIT_CHARS;

    $arrayChars = $ARRAY_SPLIT_CHARS;

    // echo $article_name . " | ";
    
    $article_name_splitted = getSplitArticleName($article_name, $arrayChars);

    // echo $article_name_splitted . " | ";

    if (!$article_name_splitted) {    
        return $article_name . ";";
    } else {
        $articleNameVariations = $article_name . "; ";
        $articleNameVariations .= concatArrayByChar($article_name_splitted, '') . ";";
    }

        // array_push($arrayChars, '');
        // foreach ($arrayChars as $key => $char) {
        //     $articleNameVariations .= concatArrayByChar($article_name_splitted, $char) . ";";
        //     if($key < count($arrayChars)-1)
        //         $articleNameVariations .= " ";
        // }
    return $articleNameVariations;


}

?>