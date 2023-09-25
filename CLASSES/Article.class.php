<?php
require_once("./settings.php");
require_once("Producer.class.php");

class Article
{

    public $id;
    public $name;
    public $type;
    public $description;

    private $main_info = array();
    private $secondary_info = array();

    private $Producer;


    function __construct()
    {
        global $dbconnect;

        $count_args = func_num_args();
        $args = func_get_args();

        // Перегружаем конструктор по количеству подданых параметров

        // Поиск артикула по id
        if ($count_args == 1) {
            $this->id = (int)$args[0];

            $query = queryGetArticleById($this->id);
            $result = pg_query($dbconnect, $query) or die('Ошибка запроса: ' . pg_last_error());
            $article = pg_fetch_assoc($result);

            $this->name = $article['article_name'];
            $this->type = $article['type'];
            if (isset($article['description']))
                $this->description = $article['description'];
            else
                $this->description = "";

            $this->setInfo();

            $this->Producer = new Producer((int)$article['producer_id']);
        } else {
            die('Неверные аргументы в конструкторе Group');
        }
    }

    private function setInfo()
    {
        global $dbconnect;
        $query = querySelectArticleInfo($this->id);
        $result = pg_query($dbconnect, $query) or die('Ошибка запроса: ' . pg_last_error());

        $main_info_by_catalogues = array();
        $secondary_info_by_catalogues = array();
        while ($row = pg_fetch_assoc($result)) {
            $json_info = $this->parseArticleInfoJSONtoARRAY($row['json']);
            array_push($main_info_by_catalogues, array(
                "catalogue_name" => $row['catalogue_name'],
                "json" => $json_info[0]
            ));
            array_push($secondary_info_by_catalogues, array(
                "catalogue_name" => $row['catalogue_name'],
                "json" => $json_info[1]
            ));
        }

        $this->main_info = $main_info_by_catalogues;
        $this->secondary_info = $secondary_info_by_catalogues;
    }

    public function getAllCharacteristics()
    {
        $array_characteristics_original = array();
        $array_characteristics_alt = array();
        $count_producers = 0;
        foreach ($this->main_info as $index => $info_by_catalogue) {
            foreach ($info_by_catalogue['json'] as $key => $characteristic) {
                if (!in_array($key, $array_characteristics_original)) {
                    array_push($array_characteristics_original, $key);
                    array_push($array_characteristics_alt, getCharacteristicNameAlternative($key));
                }
            }
            $count_producers += 1;
        }

        $array_characteristics_by_catalogues = array_fill(0, count($array_characteristics_original), []);
        foreach ($this->main_info as $index => $info_by_catalogue) {
            foreach ($info_by_catalogue['json'] as $key => $characteristic) {
                $line_index = array_search($key, $array_characteristics_original);
                array_push($array_characteristics_by_catalogues[$line_index], $characteristic);
            }
            foreach ($array_characteristics_by_catalogues as $key => $line) {
                if (count($line) < $index + 1) {
                    array_push($array_characteristics_by_catalogues[$key], "-");
                }
            }
        }

        $return_array = array();
        foreach ($array_characteristics_by_catalogues as $index => $line_characteristic) {
            $return_array = array_merge($return_array, array($array_characteristics_alt[$index] => $line_characteristic));
        }

        return $return_array;
    }

    public function getMainInfo()
    {
        return $this->main_info;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function getGroup()
    {
        global $dbconnect;

        $query = queryGetGroupComparison($this->id);
        $result = pg_query($dbconnect, $query);
        $group_id = pg_fetch_assoc($result)['group_id'];
        return (int)$group_id;
    }

    public function getLinkToCataloguePage()
    {
        return "https://shop.donaldson.com/store/ru-ru/product/" . $this->name . "/" . $this->secondary_info[0]['json']['productId'];
    }

    public function hasInfo()
    {
        return count($this->main_info) > 0;
    }

    public function getProducer()
    {
        return $this->Producer;
    }


    public function getImageUrl()
    {
        foreach ($this->secondary_info as $info) {
            if ($info['catalogue_name'] == "DONALDSON" && $info['json']['imageUrl']) {
                return $info['json']['imageUrl'];
            }
        }
        return "";
    }

    private function parseArticleInfoJSONtoARRAY($article_info_json)
    {
        $main_info = array();
        $secondary_info = array();

        $main_info_flag = true;
        $article_info = json_decode($article_info_json);
        foreach ($article_info as $key => $line) {
            if ($key == "productId") {
                $main_info_flag = false;
            }

            if ($main_info_flag) {
                $main_info = array_merge($main_info, array($key => $line));
            } else {
                $secondary_info = array_merge($secondary_info, array($key => $line));
            }
        }

        return array($main_info, $secondary_info);
    }
}


function getArticleAnalogs($Article, $group_id)
{
    global $dbconnect;
    global $ARRAY_NAME_CATALOGUES;

    $return_values = array();

    $query = queryGetAnalogArticlesId($group_id);
    $result = pg_query($dbconnect, $query);

    while ($row = pg_fetch_assoc($result)) {
        $analogArticle = new Article($row['article_id']);
        $article_array = getArticleArray($analogArticle->name, $analogArticle->getProducer()->id, $analogArticle->id, $analogArticle->hasInfo(), $Article->type, $analogArticle->getDescription());
        // if (in_array($analogArticle->getProducer()->getMainProducerName(), $ARRAY_NAME_CATALOGUES)) {
        //     $article_array[0]["status"] = 1;
        //     if (count($article_array) > 0)
        //         $return_values = array_merge($article_array, $return_values);
        // }
        if ($analogArticle->id != $Article->id) {
            $article_array[0]["status"] = 2;
            if (count($article_array) > 0)
                $return_values = array_merge($return_values, $article_array);

            // $arrays_articles = array_merge($article_array, $arrays_articles);

        }
    }

    return $return_values;
}


function getCharacteristicNameAlternative($characteristicName)
{
    global $dbconnect;
    $query = querySelectCharacteristicNameAlternative($characteristicName);
    $result = pg_query($dbconnect, $query) or die('Ошибка запроса: ' . pg_last_error());
    $characteristic = pg_fetch_assoc($result);
    return $characteristic['characteristic_alt'];
}


function getArticle($article_name, $search_type)
{
    global $dbconnect;
    global $ARRAY_SPLIT_CHARS;

    $arrayChars = $ARRAY_SPLIT_CHARS;

    $article_name_splitted = getSplitArticleName($article_name, $arrayChars);

    $found_articles = array();

    if ($article_name_splitted) {
        $article_name = concatArrayByChar($article_name_splitted, '');
    }

    if ($search_type == "soft") {
        $query = queryGetArticleSoft($article_name);
    } else {
        $query = queryGetArticleStrict($article_name);
    }
    $result = pg_query($dbconnect, $query);
    while ($row = pg_fetch_assoc($result))
        array_push($found_articles, $row);

    return $found_articles;
}

function getArticleWithProducerId($article_name, $search_type, $producer_id = null)
{
    global $dbconnect;
    global $ARRAY_SPLIT_CHARS;

    $arrayChars = $ARRAY_SPLIT_CHARS;

    $article_name_splitted = getSplitArticleName($article_name, $arrayChars);

    $found_articles = array();

    if ($article_name_splitted) {
        $article_name = concatArrayByChar($article_name_splitted, '');
    }

    if ($producer_id != null) {
        $query = queryGetArticleWithProducerIdSoft($article_name, $producer_id);
    } else {
        if ($search_type == "soft") {
            $query = queryGetArticleSoft($article_name);
        } else {
            $query = queryGetArticleStrict($article_name);
        }
    }
    $result = pg_query($dbconnect, $query);
    while ($row = pg_fetch_assoc($result))
        array_push($found_articles, $row);

    return $found_articles;
}


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



// ФУНКЦИИ ЗАПРОСОВ К БД
// ФУНКЦИИ ЗАПРОСОВ К БД
// ФУНКЦИИ ЗАПРОСОВ К БД


function queryGetArticleSoft($article_name)
{
    return "SELECT articles.id AS article_id FROM articles
            WHERE article_name LIKE '%' || '$article_name' || '%';";
}
function queryGetArticleStrict($article_name)
{
    return "SELECT articles.id AS article_id FROM articles
            WHERE article_name = '$article_name';";
}


function queryGetArticleWithProducerIdSoft($article_name, $producer_id)
{
    return "SELECT articles.id AS article_id FROM articles
            WHERE article_name LIKE '%' || '$article_name' || '%' AND producer_id = $producer_id;";
}




function queryGetAnalogArticlesId($group_id)
{
    return "SELECT ac.article_id, articles.article_name, articles.producer_id, producers.producer_name FROM articles_comparison as ac
            INNER JOIN articles ON articles.id = ac.article_id
            LEFT JOIN producers ON producers.id = articles.producer_id
            WHERE group_id = $group_id
            ORDER BY producers.producer_name";
}

function queryGetGroupComparison($article_id)
{
    return "SELECT group_id FROM articles_comparison WHERE article_id = $article_id";
}





function queryGetArticleById($article_id)
{
    return "SELECT articles.*, articles_details.type as description FROM articles 
            LEFT JOIN articles_details ON articles_details.article_id = articles.id 
            WHERE articles.id = $article_id;";
}
function querySelectArticleByNameAndProducerId($article_name, $producer_id)
{
    return "SELECT * FROM articles WHERE article_name = '$article_name' AND producer_id = $producer_id;";
}

function querySelectGroupArticleAnalogs($article_id)
{
    return "SELECT group_id FROM articles_comparison WHERE article_id = $article_id;";
}
function querySelectArticleAnalogsByGroup($group_id)
{
    return "SELECT article_id FROM articles_comparison WHERE group_id = $group_id;";
}
function queryCheckArticleAnalog($group_id, $analog_article_id, $catalogue_name)
{
    return "SELECT * FROM articles_comparison WHERE group_id = $group_id AND article_id = $analog_article_id AND catalogue_name = '$catalogue_name'";
}
function querySelectArticleInfoWithCatalogueName($article_id, $catalogue_name)
{
    return "SELECT * FROM articles_details WHERE article_id = $article_id AND catalogue_name = '$catalogue_name'";
}
function querySelectArticleInfo($article_id)
{
    return "SELECT * FROM articles_details WHERE article_id = $article_id";
}
function querySelectMaxGroupNumber()
{
    return "SELECT MAX(group_id) AS max_group_id FROM articles_comparison;";
}

function querySelectCharacteristicNameAlternative($characteristic_original)
{
    return "SELECT characteristic_alt FROM characteristics_comparison WHERE characteristic_original = '$characteristic_original';";
}




function queryInsertArticle($article_name, $producer_id)
{
    return "INSERT INTO public.articles(article_name, producer_id) VALUES ('$article_name', $producer_id) RETURNING id;";
}

function queryInsertArticlesComparison($group_id, $article_id, $catalogue_name)
{
    return "INSERT INTO public.articles_comparison(group_id, article_id, catalogue_name) VALUES ($group_id, $article_id, '$catalogue_name');";
}
function queryInsertArticleInfo($article_id, $catalogue_name, $json)
{
    return "INSERT INTO public.articles_details(article_id, catalogue_name, json) VALUES ($article_id, '$catalogue_name', \$antihype1\${$json}\$antihype1\$);";
}
