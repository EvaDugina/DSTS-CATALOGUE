<?php
require_once("./settings.php");

class Producer
{

    public $id;
    public $name;
    public $dsts_name;


    function __construct()
    {
        global $dbconnect;

        $count_args = func_num_args();
        $args = func_get_args();

        // Перегружаем конструктор по количеству подданых параметров

        if ($count_args == 1) {
            $this->id = (int)$args[0];

            $query = querySelectProducerById($this->id);
            $result = pg_query($dbconnect, $query) or die('Ошибка запроса: ' . pg_last_error());
            $producer = pg_fetch_assoc($result);

            $this->name = $producer['producer_name'];
            $this->dsts_name = $producer['producer_name_dsts'];
        } else {
            die('Неверные аргументы в конструкторе Group');
        }
    }

    public function getProducerNameVariations()
    {
        global $dbconnect;
        $query = querySelectProducerNameVariations($this->id);
        $result = pg_query($dbconnect, $query);

        $names_by_catalogues = array();
        while ($row = pg_fetch_assoc($result))
            array_push($names_by_catalogues, array(
                [0] => $row['producer_name'],
                [1] => $row['catalogue_name']
            ));

        return $names_by_catalogues;
    }

    public function setSimmilarProducer($main_producer_id)
    {
        global $dbconnect;

        $query = queryInsertProducerComparison($main_producer_id, $this->id);
        pg_query($dbconnect, $query) or die('Ошибка запроса: ' . pg_last_error());

        // // $query = queryDeleteProducer($this->id);
        // // pg_query($dbconnect, $query) or die('Ошибка запроса: ' . pg_last_error());

        // $newProducer = new Producer($new_producer_id);

        // $this->id = $newProducer->id;
        // $this->name = $newProducer->name;
        // $this->dsts_name = $newProducer->dsts_name;
    }

    public function setProducerDSTSName($new_producer_name_dsts)
    {
        global $dbconnect;

        $query = queryUpdateProducerNameDSTS($this->id, $new_producer_name_dsts);
        pg_query($dbconnect, $query) or die('Ошибка запроса: ' . pg_last_error());
    }

    function getProducerNameByDSTSCatalogue()
    {
        global $dbconnect;
        $query = queryGetProducerNameByDSTSCatalogue($this->id);
        $result = pg_query($dbconnect, $query);
        $row = pg_fetch_assoc($result);
        if ($row)
            return $row['producer_name'];
        else {
            return false;
        }
    }

    function getMainProducerName()
    {
        global $dbconnect;
        $query = querySelectProducerComparison($this->id);
        $result = pg_query($dbconnect, $query);
        if (pg_num_rows($result) < 1) {
            $main_producer_id = $this->id;
        } else {
            $main_producer_id = pg_fetch_assoc($result)['main_producer_id'];
        }

        $query = querySelectProducerById($main_producer_id);
        $result = pg_query($dbconnect, $query);
        $producer = pg_fetch_assoc($result);
        if ($producer)
            return $producer['producer_name'];
        else {
            return false;
        }
    }
}





function getProducerIdByName($producer_name)
{
    global $dbconnect;

    $query = querySelectProducerByName($producer_name);
    $result = pg_query($dbconnect, $query) or die('Ошибка запроса: ' . pg_last_error());
    return pg_fetch_assoc($result)['id'];
}

function getProducersNames()
{
    global $dbconnect;

    $query = queryGetAllProducersNames();
    $result = pg_query($dbconnect, $query) or die('Ошибка запроса: ' . pg_last_error());

    $producer_names = array();
    while ($row = pg_fetch_assoc($result)) {
        array_push($producer_names, $row['producer_name']);
    }

    return $producer_names;
}


function getProducersNamesDSTS()
{
    global $dbconnect;

    $query = queryGetAllProducersNamesDSTS();
    $result = pg_query($dbconnect, $query) or die('Ошибка запроса: ' . pg_last_error());

    $producer_names = array();
    while ($row = pg_fetch_assoc($result)) {
        array_push($producer_names, $row['producer_name']);
    }

    return $producer_names;
}


function getProducerNameByCatalogue($producer_id, $catalogue_name)
{
    global $dbconnect;
    $query = queryGetProducerNameByCatalogue($producer_id, $catalogue_name);
    $result = pg_query($dbconnect, $query);
    if (pg_num_rows($result) < 2) {
        $row = pg_fetch_assoc($result);
        if ($row)
            return $row['producer_name'];
        else {
            return false;
        }
    } else {
        while ($row = pg_fetch_assoc($result)) {
            if ($row['original_producer_name'] != $row['producer_name'])
                return $row['producer_name'];
        }
        return false;
    }
}


function getMainProducerName($producer_id)
{
    global $dbconnect;
    $query = querySelectProducerComparison($producer_id);
    $result = pg_query($dbconnect, $query);
    if (pg_num_rows($result) < 1) {
        $main_producer_id = $producer_id;
    } else {
        $main_producer_id = pg_fetch_assoc($result)['main_producer_id'];
    }

    $query = querySelectProducerById($main_producer_id);
    $result = pg_query($dbconnect, $query);
    $producer = pg_fetch_assoc($result);
    if ($producer)
        return array("id" => $producer['id'], "producer_name" => $producer['producer_name']);
    else {
        return false;
    }
}

function getProducerNameByDSTSCatalogue($producer_id)
{
    global $dbconnect;
    $query = queryGetProducerNameByDSTSCatalogue($producer_id);
    $result = pg_query($dbconnect, $query);
    $row = pg_fetch_assoc($result);
    if ($row)
        return $row['producer_name'];
    else {
        return false;
    }
}



// ФУНКЦИИ ЗАПРОСОВ К БД
// ФУНКЦИИ ЗАПРОСОВ К БД
// ФУНКЦИИ ЗАПРОСОВ К БД


function querySelectProducerById($producer_id)
{
    return "SELECT producers.id, producers.producer_name, producers_dsts_names.producer_name AS producer_name_dsts 
            FROM producers
            LEFT JOIN producers_dsts_names ON producers_dsts_names.producer_id = producers.id
            WHERE producers.id = $producer_id;";
}
function querySelectProducerNameVariations($producer_id)
{
    return "SELECT * FROM producers_name_variations WHERE producer_id = $producer_id;";
}

function querySelectProducerByName($producer_name)
{
    return "SELECT * FROM producers WHERE producer_name = '$producer_name';";
}
function querySelectProducerNameVariation($producer_name, $catalogue_name)
{
    return "SELECT * FROM producers_name_variations WHERE producer_name = '$producer_name' AND catalogue_name = '$catalogue_name';";
}
function queryCheckProducerNameVariation($producer_id, $producer_name, $catalogue_name)
{
    return "SELECT * FROM producers_name_variations WHERE producer_id = $producer_id AND producer_name = '$producer_name' AND catalogue_name = '$catalogue_name';";
}
function querySelectProducerNameVarations($producer_id)
{
    return "SELECT * FROM producers_name_variations WHERE producer_id = $producer_id;";
}

function queryGetProducerNameByCatalogue($producer_id, $catalogue_name)
{
    return "SELECT producers_name_variations.producer_name, producers.producer_name AS original_producer_name
            FROM producers_name_variations
            LEFT JOIN producers ON producers.id = producers_name_variations.producer_id
            WHERE producer_id = $producer_id AND catalogue_name = '$catalogue_name';";
}

function queryGetProducerNameByDSTSCatalogue($producer_id)
{
    return "SELECT producer_name FROM producers_dsts_names
            WHERE producer_id = $producer_id;";
}

function queryGetAllProducersNames()
{
    return "SELECT DISTINCT producer_name FROM producers
            ORDER BY producer_name;";
}

function queryGetAllProducersNamesDSTS()
{
    return "SELECT producer_name FROM producers_dsts_names;";
}

function querySelectProducerComparison($producer_id)
{
    return "SELECT main_producer_id FROM producers_comparison
            WHERE secondary_producer_id = $producer_id;";
}





function queryUpdateProducerFromNameVariations($last_producer_id, $new_producer_id)
{
    return "UPDATE producers_name_variations SET producer_id = $new_producer_id WHERE producer_id = $last_producer_id;
            UPDATE articles SET producer_id = $new_producer_id WHERE producer_id = $last_producer_id";
}

function queryUpdateProducerNameDSTS($producer_id, $new_producer_name)
{
    return "INSERT INTO producers_dsts_names (producer_id, producer_name)
            VALUES ($producer_id, '$new_producer_name')
            ON CONFLICT (producer_id) DO 
            UPDATE SET producer_name = '$new_producer_name';";
}




function queryInsertProducer($producer_name)
{
    return "INSERT INTO public.producers(producer_name) VALUES ('$producer_name') RETURNING id;";
}
function queryInsertProducerNameVariation($producer_id, $producer_name, $catalogue_name)
{
    return "INSERT INTO public.producers_name_variations(producer_id, producer_name, catalogue_name) VALUES ($producer_id, '$producer_name', '$catalogue_name') 
        RETURNING id;";
}

function queryInsertProducerComparison($main_producer_id, $secondary_producer_id)
{
    return "INSERT INTO producers_comparison (main_producer_id, secondary_producer_id)
            VALUES ($main_producer_id, $secondary_producer_id)
            ON CONFLICT (secondary_producer_id) DO 
            UPDATE SET main_producer_id = $main_producer_id;";
}




function queryDeleteProducer($producer_id)
{
    return "DELETE FROM producers WHERE id = $producer_id;";
}
