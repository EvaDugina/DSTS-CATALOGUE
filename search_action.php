<?php
require_once("settings.php");
require_once("utilities.php");

$au = new auth_ssh();
checkAuLoggedIN($au);

if(isset($_POST['article_name']))
    $article_name = $_POST['article_name'];
else 
    exit;

$return_values = array();

$article = getArticleId($article_name);
if($article == false) {
    $return_value = array(
        "error" => "article_id"
    );
    array_push($return_values, $return_value);
    echo json_encode($return_values);
    exit;
} 

$article_id = $article['id'];

$return_value = array(
    "article_name" => $article['article_name'],
    "producer_name" => $article['producer_name']
);

array_push($return_values, $return_value);

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
    $return_value = array(
        "article_name" => $row['article_name'],
        "producer_name" => $row['producer_name']
    );
    
    array_push($return_values, $return_value);
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


function getArticleId($article_name) {
    global $dbconnect;
    global $ARRAY_SPLIT_CHARS;

    $arrayChars = $ARRAY_SPLIT_CHARS;

    $article_name_splitted = getSplitArticleName($article_name, $arrayChars);

    if (!$article_name_splitted) {
        $query = queryGetArticleId($article_name); 
        $result = pg_query($dbconnect, $query);
        $row = pg_fetch_assoc($result); 
        if($row)
            return $row; 
        else 
            return false;
    }
    else {
        $query = queryGetArticleId(concatArrayByChar($article_name_splitted, '')); 
        array_push($arrayChars, '');
        foreach ($arrayChars as $char) {
            $query = queryGetArticleId(concatArrayByChar($article_name_splitted, $char)); 
            $result = pg_query($dbconnect, $query);
            $row = pg_fetch_assoc($result); 
            if ($row) {
                return $row;
            } 
        }
    }

    return false;

}




function checkCorrectSymbolsInArticleName() {

}




// ФУНКЦИИ ЗАПРОСОВ К БД
// ФУНКЦИИ ЗАПРОСОВ К БД
// ФУНКЦИИ ЗАПРОСОВ К БД

function queryGetArticleId($article_name) {
    return "SELECT articles.*, producers.producer_name FROM articles 
            INNER JOIN producers ON producers.id = articles.producer_id
            WHERE article_name = '$article_name';";
}

function queryGetAnalogArticlesId($group_id) {
    //TODO: УТОЧНИТЬ, КАКОЕ ИМЕННО НАЗВАНИЕ ПРОДЮСЕРА ВЫВОДИТЬ!
    return "SELECT ac.article_id, articles.article_name, producers.producer_name FROM articles_comparison as ac
            INNER JOIN articles ON articles.id = ac.article_id
            INNER JOIN producers ON producers.id = articles.producer_id
            WHERE group_id = $group_id
            ORDER BY producers.producer_name";
}

// ФУНКЦИЯ ДЛЯ ТЕСТА КОРЯВОЙ БД
function queryGetAnalogArticlesIdTEST($article_id) {
    return "SELECT ac.second_article_id, articles.article_name, producers.producer_name FROM articles_comparison as ac
            INNER JOIN articles ON articles.id = ac.second_article_id
            INNER JOIN producers ON producers.id = articles.producer_id
            WHERE ac.first_article_id = $article_id
            ORDER BY producers.producer_name";
}

function queryGetGroupComparison($article_id) {
    return "SELECT group_id FROM articles_comparison WHERE article_id = $article_id";
}
    
?>
