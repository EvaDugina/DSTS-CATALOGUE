<?php
require_once("settings.php");
include_once('auth_ssh.class.php');
require_once("CLASSES/Article.class.php");
require_once("CLASSES/Producer.class.php");


// защита от случайного перехода незарегистрированного пользователя
function checkAuLoggedIN($au)
{
  if (!$au->loggedIn()) {
    header('Location:login.php');
    exit;
  }
}

// защита от случайного перехода студента
function checkAuIsAdmin($au)
{
  if (!$au->isAdmin()) {
    $au->logout();
    header('Location:login.php');
  }
}

// $ARRAY_CATALOGUES = [
//   array(0 => "DONALDSON", 1 => TRUE),
//   array(0 => "HIFI", 1 => FALSE),
//   array(0 => "MANN&HUMMEL", 1 => FALSE),
//   array(0 => "FLEETGUARD", 1 => FALSE),
//   array(0 => "SFFILTER", 1 => FALSE),
//   array(0 => "BALDWIN", 1 => FALSE),
//   array(0 => "FILFILTER", 1 => FALSE),
//   array(0 => "LEFONG", 1 => FALSE)
// ];

$ARRAY_CATALOGUES = [
  array(
    "name" => "DONALDSON",
    "articleUrl" => "https://shop.donaldson.com/store/ru-ru/product/",
    "backgroundColor" => "primary",
    "frontColor" => "white",
    "iconPath" => "src/img/image_donaldson.jpg"
  ),
  array(
    "name" => "FILFILTER",
    "articleUrl" => "https://catalog.filfilter.com.tr/ru/product/",
    "backgroundColor" => "warning",
    "frontColor" => "black",
    "iconPath" => "src/img/icon_filfilter.png"
  )
];

$ARRAY_SPLIT_CHARS = ['.', '-', ' '];

$COUNT_LOADING_ELEMENTS = 20;





function getSplitArticleName($article_name, $arrayChars)
{
  foreach ($arrayChars as $char) {
    if (strpos($article_name, $char))
      return explode($char, $article_name);
  }
  return false;
}

function concatArrayByChar($arrayString, $char)
{
  $str = "";
  foreach ($arrayString as $key => $string) {
    $str .= $string;
    if ($key < count($arrayString) - 1)
      $str .= $char;
  }
  return $str;
}

function getArticleUrlByCatalogue($catalogue_name)
{
  global $ARRAY_CATALOGUES;

  foreach ($ARRAY_CATALOGUES as $catalogue) {
    if ($catalogue["name"] == $catalogue_name)
      return $catalogue["articleUrl"];
  }
  return "";
}

function getCataloguesName()
{
  global $ARRAY_CATALOGUES;

  $catalogue_names = [];
  foreach ($ARRAY_CATALOGUES as $catalogue) {
    array_push($catalogue_names, $catalogue["name"]);
  }

  return $catalogue_names;
}
