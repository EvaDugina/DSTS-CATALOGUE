<?php
require_once("./settings.php");
require_once("Producer.class.php");

class Article
{

    public $id;
    public $name;

    private $info = array();

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

        $info_by_catalogues = array();

        while ($row = pg_fetch_assoc($result)) {
            $json_info = $this->parseArticleInfoJSONtoARRAY($row['json']);
            array_push($info_by_catalogues, array(
                "catalogue_name" => $row['catalogue_name'],
                "json" => $json_info
            ));
        }

        $this->info = $info_by_catalogues;
    }


    public function getInfo()
    {
        return $this->info;
    }

    public function hasInfo()
    {
        return count($this->info) > 0;
    }

    public function getProducer()
    {
        return $this->Producer;
    }


    public function getImageUrl()
    {
        foreach ($this->info as $info) {
            if ($info['catalogue_name'] == "DONALDSON" && $info['json']->imageUrl) {
                return $info['json']->imageUrl;
            }
        }
        return "";
    }

    private function parseArticleInfoJSONtoARRAY($article_info_json)
    {
        $article_info = json_decode($article_info_json);
        return $article_info;
    }
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
    return "SELECT * FROM articles WHERE id = $article_id;";
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
