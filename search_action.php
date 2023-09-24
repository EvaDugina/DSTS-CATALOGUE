<?php
require_once("settings.php");
require_once("utilities.php");

$au = new auth_ssh();
checkAuLoggedIN($au);


if (isset($_POST['getProducersNameDsts'])) {
    $array_producer_names = getProducersNames();
    echo json_encode($array_producer_names);
    exit;
}

if (isset($_POST['article_name']) && isset($_POST['search_type'])) {
    $article_name = $_POST['article_name'];
    $search_type = $_POST['search_type'];
} else
    exit;

$producer_id = null;
if (isset($_POST['producer_name'])) {
    $producer_name = $_POST['producer_name'];
    $producer_name_splitted = explode("+", $producer_name);
    if (count($producer_name_splitted) > 1) {
        $producer_name = "";
        foreach ($producer_name_splitted as $key => $name_part) {
            if ($key != 0)
                $producer_name .= " ";
            $producer_name .= $name_part;
        }
    }
    $producer_id = getProducerIdByName($producer_name);
}


$return_values = array();
$arrays_articles = array();

// if ($producer_name == "")
//     $found_articles = getArticle($article_name, $search_type);
// else
$found_articles = getArticleWithProducerId($article_name, $search_type, $producer_id);

if (count($found_articles) == 0) {
    $return_value = array(
        "error" => "article_id"
    );
    $return_values = array_merge($return_values, $return_value);
    echo json_encode($return_values);
    exit;
} else if (count($found_articles) > 1) {
    $return_value = array(
        "error" => "articles"
    );
    foreach ($found_articles as $article) {
        $Article = new Article($article['article_id']);

        $article_array = getArticleArray($Article->name, $Article->getProducer()->id, $Article->id, $Article->hasInfo(), $Article->type, $Article->getDescription());
        $article_array[0]["status"] = 0;

        $arrays_articles = array_merge($arrays_articles, $article_array);
    }
    $return_values = array_merge($return_value, $arrays_articles);
    echo json_encode($return_values);
    exit;
}

$found_article = $found_articles[0];
$Article = new Article($found_article['article_id']);


// ПОИСК АНАЛОГОВ В БД:
$query = queryGetGroupComparison($Article->id);
$result = pg_query($dbconnect, $query);
$group = pg_fetch_assoc($result);
if ($group) {
    $group_id = $group['group_id'];
} else {
    $return_value = array(
        "error" => "group_id"
    );
    $return_values = array_merge($return_values, $return_value);
    $main_article_array = getArticleArray($Article->name, $Article->getProducer()->id, $Article->id, $Article->hasInfo(), $Article->type, $Article->getDescription());
    $main_article_array[0]["status"] = 0;
    if (count($main_article_array) > 0)
        $return_values = array_merge($return_values, $main_article_array);
    echo json_encode($return_values);
    exit;
}

$query = queryGetAnalogArticlesId($group_id);
$result = pg_query($dbconnect, $query);


while ($row = pg_fetch_assoc($result)) {
    $analogArticle = new Article($row['article_id']);
    $article_array = getArticleArray($analogArticle->name, $analogArticle->getProducer()->id, $analogArticle->id, $analogArticle->hasInfo(), $Article->type, $analogArticle->getDescription());
    if (in_array($analogArticle->getProducer()->getMainProducerName(), $ARRAY_NAME_CATALOGUES)) {
        $article_array[0]["status"] = 1;
        if (count($article_array) > 0)
            $return_values = array_merge($article_array, $return_values);
    }
    if ($analogArticle->id != $Article->id) {
        $article_array[0]["status"] = 2;
        if (count($article_array) > 0)
            $return_values = array_merge($return_values, $article_array);

        // $arrays_articles = array_merge($article_array, $arrays_articles);

    }
}


$main_article_array = getArticleArray($Article->name, $Article->getProducer()->id, $Article->id, $Article->hasInfo(), $Article->type, $Article->getDescription());
$main_article_array[0]["status"] = 0;
if (count($main_article_array) > 0)
    $return_values = array_merge($main_article_array, $return_values);


echo json_encode($return_values);
exit;





function getArticleArray($article_name, $producer_id, $article_id, $hasInfo, $type, $description)
{
    global $ARRAY_CATALOGUES;

    $article_array = array();

    foreach ($ARRAY_CATALOGUES as $catalogue) {

        if ($catalogue[1] == FALSE)
            continue;

        $catalogue_name = $catalogue[0];

        $main_producer = getMainProducerName($producer_id);
        $main_producer_name = $main_producer['producer_name'];
        $main_producer_id = $main_producer['id'];

        $producer_name_by_catalogue = getProducerNameByCatalogue($producer_id, $catalogue_name);
        if ($producer_name_by_catalogue == false) {
            continue;
        }

        if ($main_producer_id == false) {
            $producer_dsts_name = getProducerNameByDSTSCatalogue($producer_id);
            if ($producer_dsts_name == false) {
                $producer_dsts_name = "";
            }
        } else {
            $producer_dsts_name = getProducerNameByDSTSCatalogue($main_producer_id);
        }

        if ($type == 1)
            $description = "(УСТАРЕВШИЙ)";

        $article_array_by_catalogue = array(
            "article_id" => $article_id,
            "article_name" => $article_name,
            "catalogue_name" => $catalogue_name,
            "producer_id" => $producer_id,
            "producer_name_dsts" => $producer_dsts_name,
            "producer_name" => $main_producer_name,
            "producer_name_by_catalogue" => $producer_name_by_catalogue,
            "description" => $description,
            "hasInfo" => $hasInfo
        );

        array_push($article_array, $article_array_by_catalogue);
    }

    return $article_array;
}
