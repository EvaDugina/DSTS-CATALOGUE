<?php
require_once("settings.php");
require_once("utilities.php");

$au = new auth_ssh();
checkAuLoggedIN($au);

if(isset($_POST['article_name']))
    $article_name = $_POST['article_name'];
else 
    exit;

// if(isset($_POST['catalogue_name']))
//     $catalogue_name = $_POST['catalogue_name'];
// else 
//     exit;

$return_values = array();

$article = getArticle($article_name);
if($article == false) {
    $return_value = array(
        "error" => "article_id"
    );
    array_push($return_values, $return_value);
    echo json_encode($return_values);
    exit;
} 


$producer_id = $article['producer_id'];
$article_array = getArticleArray($article['article_name'], $producer_id, $article['producer_name']);
if(count($article_array) > 0) 
    array_push($return_values, $article_array);


$article_id = $article['id'];

// ПОИСК АНАЛОГОВ В БД:
// $query = queryGetGroupComparison($article_id);
// $result = pg_query($dbconnect, $query);
// $group_id = pg_fetch_assoc($result)['group_id'];

// $query = queryGetAnalogArticlesId($group_id);
// $result = pg_query($dbconnect, $query);

// КОРЯВАЯ ХУЙНЯ ДЛЯ ТЕСТА:
$query = queryGetAnalogArticlesIdTEST($article_id);
$result = pg_query($dbconnect, $query);


while ($row = pg_fetch_assoc($result)) {
    $producer_id = $row['producer_id'];

    $article_array = getArticleArray($row['article_name'], $producer_id, $row['producer_name']);
    if(count($article_array) > 0)
        array_push($return_values, $article_array);

}


// $return_value = array(
//     "article_name" => "P550667",
//     "producer_name" => "DONALDSON"
// );
// array_push($return_values, $return_value);

// $return_value = array(
//     "article_name" => "P550667",
//     "producer_name" => "FIL-FILTER"
// );
// array_push($return_values, $return_value);


echo json_encode($return_values);
exit;


function getArticle($article_name) {
    global $dbconnect;
    global $ARRAY_SPLIT_CHARS;

    $arrayChars = $ARRAY_SPLIT_CHARS;

    $article_name_splitted = getSplitArticleName($article_name, $arrayChars);

    if (!$article_name_splitted) {
        $query = queryGetArticle($article_name); 
        $result = pg_query($dbconnect, $query);
        $row = pg_fetch_assoc($result); 
        if($row)
            return $row; 
        else 
            return false;
    }
    else {
        $query = queryGetArticle(concatArrayByChar($article_name_splitted, '')); 
        array_push($arrayChars, '');
        foreach ($arrayChars as $char) {
            $query = queryGetArticle(concatArrayByChar($article_name_splitted, $char)); 
            $result = pg_query($dbconnect, $query);
            $row = pg_fetch_assoc($result); 
            if ($row) {
                return $row;
            } 
        }
    }

    return false;
}




function getProducerNameByCatalogue($producer_id, $catalogue_name) {
    global $dbconnect;
    $query = queryGetProducerNameByCatalogue($producer_id, $catalogue_name); 
    $result = pg_query($dbconnect, $query);
    $row = pg_fetch_assoc($result); 
    if($row)
        return $row['producer_name']; 
    else {
        return false;
    }
}

// function getProducerNameByDSTSCatalogue($producer_id) {
//     global $dbconnect;
//     $query = queryGetProducerNameByDSTSCatalogue($producer_id); 
//     $result = pg_query($dbconnect, $query);
//     $row = pg_fetch_assoc($result);
//     if($row)
//         return $row['producer_name']; 
//     else {
//         return false;
//     }
// }


function getArticleArray($article_name, $producer_id, $producer_dsts_name) {
    global $ARRAY_CATALOGUES;

    $article_array = array();

    foreach ($ARRAY_CATALOGUES as $catalogue) {

        if($catalogue[1] == FALSE)
            continue;

        $catalogue_name = $catalogue[0];

        $producer_name_by_catalogue = getProducerNameByCatalogue($producer_id, $catalogue_name);
        if($producer_name_by_catalogue == false) {
            continue;
        }
        
        $article_array_by_catalogue = array(
            "article_name" => $article_name,
            "producer_name_dsts" => $producer_dsts_name,
            "catalogue_name" => $catalogue_name,
            "producer_name_by_catalogue" => $producer_name_by_catalogue
        );
        
        array_push($article_array, $article_array_by_catalogue);
    }

    return $article_array;
}




// ФУНКЦИИ ЗАПРОСОВ К БД
// ФУНКЦИИ ЗАПРОСОВ К БД
// ФУНКЦИИ ЗАПРОСОВ К БД

function queryGetArticle($article_name) {
    return "SELECT articles.*, producers.producer_name FROM articles 
            LEFT JOIN producers ON producers.id = articles.producer_id
            WHERE article_name = '$article_name';";
}

function queryGetAnalogArticlesId($group_id) {
    return "SELECT ac.article_id, articles.article_name, articles.producer_id, producers.producer_name FROM articles_comparison as ac
            INNER JOIN articles ON articles.id = ac.article_id
            LEFT JOIN producers ON producers.id = articles.producer_id
            WHERE group_id = $group_id
            ORDER BY producers.producer_name";
}

// ФУНКЦИЯ ДЛЯ ТЕСТА КОРЯВОЙ БД
function queryGetAnalogArticlesIdTEST($article_id) {
    return "SELECT ac.second_article_id, articles.article_name, articles.producer_id, producers.producer_name FROM articles_comparison as ac
            INNER JOIN articles ON articles.id = ac.second_article_id
            LEFT JOIN producers ON producers.id = articles.producer_id
            WHERE ac.first_article_id = $article_id";
}

function queryGetGroupComparison($article_id) {
    return "SELECT group_id FROM articles_comparison WHERE article_id = $article_id";
}

function queryGetProducerNameByCatalogue($producer_id, $catalogue_name) {
    return "SELECT producer_name FROM producer_name_variations
            WHERE producer_id = $producer_id AND catalogue_name = '$catalogue_name'";
}

// function queryGetProducerNameByDSTSCatalogue($producer_id) {
//     return "SELECT producer_name FROM producers_dsts_name
//             WHERE producer_id = $producer_id;";
// }

    
?>
