<?php
require_once("settings.php");
require_once("utilities.php");

$au = new auth_ssh();
checkAuLoggedIN($au);

if (isset($_POST['article_name']))
    $article_name = $_POST['article_name'];
else
    exit;

if (isset($_POST['search_type']))
    $search_type = $_POST['search_type'];
else
    exit;

$return_values = array();
$arrays_articles = array();

$found_articles = getArticle($article_name, $search_type);

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

        $article_array = getArticleArray($Article->name, $Article->getProducer()->id, $Article->id, $Article->hasInfo());
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
$group_id = pg_fetch_assoc($result)['group_id'];

$query = queryGetAnalogArticlesId($group_id);
$result = pg_query($dbconnect, $query);


while ($row = pg_fetch_assoc($result)) {
    $analogArticle = new Article($row['article_id']);
    $article_array = getArticleArray($analogArticle->name, $analogArticle->getProducer()->id, $analogArticle->id, $analogArticle->hasInfo());
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


$main_article_array = getArticleArray($Article->name, $Article->getProducer()->id, $Article->id, $Article->hasInfo());
$main_article_array[0]["status"] = 0;
if (count($main_article_array) > 0)
    $return_values = array_merge($main_article_array, $return_values);


echo json_encode($return_values);
exit;





function getArticleArray($article_name, $producer_id, $article_id, $hasInfo)
{
    global $ARRAY_CATALOGUES;

    $article_array = array();

    foreach ($ARRAY_CATALOGUES as $catalogue) {

        if ($catalogue[1] == FALSE)
            continue;

        $catalogue_name = $catalogue[0];

        $main_producer_name = getMainProducerName($producer_id);

        $producer_name_by_catalogue = getProducerNameByCatalogue($producer_id, $catalogue_name);
        if ($producer_name_by_catalogue == false) {
            continue;
        }

        $producer_dsts_name = getProducerNameByDSTSCatalogue($producer_id);
        if ($producer_name_by_catalogue == false) {
            $producer_dsts_name = "";
        }

        $article_array_by_catalogue = array(
            "article_id" => $article_id,
            "article_name" => $article_name,
            "catalogue_name" => $catalogue_name,
            "producer_id" => $producer_id,
            "producer_name_dsts" => $producer_dsts_name,
            "producer_name" => $main_producer_name,
            "producer_name_by_catalogue" => $producer_name_by_catalogue,
            "hasInfo" => $hasInfo
        );

        array_push($article_array, $article_array_by_catalogue);
    }

    return $article_array;
}
