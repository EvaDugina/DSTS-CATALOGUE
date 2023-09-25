<?php
require_once("settings.php");
require_once("utilities.php");

$au = new auth_ssh();
checkAuLoggedIN($au);
checkAuIsAdmin($au);

$article = null;
if (isset($_POST['article']) && /*isset($_POST['analogs']) &&*/ isset($_POST['selected_catalogues'])) {
    $article = json_decode($_POST['article']);
    // $analogs = json_decode($_POST['analogs']);
    $selected_catalogues = json_decode($_POST['selected_catalogues']);
} else
    exit;

// print_r($analogs);

$Article = new Article($article->article_id);
$group_id = $Article->getGroup();

$analogs = getArticleAnalogs($Article, $group_id);

$result_parse = "";
foreach ($selected_catalogues as $catalogue_name) {
    $result_parse .= getCrossRefByCatalogue($article, $analogs, $catalogue_name);
    $result_parse .= "\t\t\t\t\t";
}




echo $result_parse;
exit;


function getCrossRefByCatalogue($Article, $analogs, $catalogue_name)
{

    $cross_ref = "";

    $last_producer_name = "";

    $cross_ref .= "Кросс-референс " . $catalogue_name;
    $cross_ref .= " (" . articleNameVariations($Article->article_name) . "): ";

    foreach ($analogs as $key => $analog) {

        if ($analog['catalogue_name'] != $catalogue_name)
            continue;

        if ($analog['producer_name_dsts'] != "")
            $producer_name = $analog['producer_name_dsts'];
        else
            $producer_name = $analog['producer_name_by_catalogue'];

        if ($key == 0) {
            $last_producer_name = $producer_name;
            $cross_ref .= $producer_name;
            $cross_ref .= " (";
        } else if ($producer_name == $last_producer_name) {
            $cross_ref .= " ";
        } else {
            $last_producer_name = $producer_name;
            $cross_ref .= "), " . $producer_name . " (";
        }
        $cross_ref .= articleNameVariations($analog['article_name']);

        if ($key == count($analogs) - 1) {
            $cross_ref .= ")";
        }
    }

    return $cross_ref;
}


function articleNameVariations($article_name)
{
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
