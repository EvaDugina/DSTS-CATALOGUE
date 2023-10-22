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

        $articles = getArticleArray($Article->name, $Article->getProducer()->id, $Article->id, $Article->hasInfo(), $Article->type, $Article->getDescription());
        $article_array = [];
        if (in_array($Article->getProducer()->name, getCataloguesName())) {
            foreach ($articles as $article) {
                if ($article['catalogue_name'] == $Article->getProducer()->name)
                    $article_array = [0 => $article];
            }
        } else {
            $article_array = [0 => $articles[0]];
        }

        $article_array[0]['status'] = 0;

        $arrays_articles = array_merge($arrays_articles, $article_array);
    }
    $return_values = array_merge($return_value, $arrays_articles);
    echo json_encode($return_values);
    exit;
}

$found_article = $found_articles[0];
$Article = new Article($found_article['article_id']);


// ПОИСК АНАЛОГОВ В БД:
$group_ids = $Article->getGroups();
if (!$group_ids) {
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

$return_values = getMainArticleAnalogs($Article, $group_ids);

// Добавление себя в начало
$main_article_array = getArticleArray($Article->name, $Article->getProducer()->id, $Article->id, $Article->hasInfo(), $Article->type, $Article->getDescription());
$main_article_array[0]["status"] = 0;
if (count($main_article_array) > 0)
    $return_values = array_merge($main_article_array, $return_values);


echo json_encode($return_values);
exit;
