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
//   array(0 => "HIFI-FILTER", 1 => FALSE),
//   array(0 => "MANN&HUMMEL", 1 => FALSE),
//   array(0 => "FLEETGUARD", 1 => FALSE),
//   array(0 => "SF-FILTER", 1 => FALSE),
//   array(0 => "BALDWIN", 1 => FALSE),
//   array(0 => "FIL-FILTER", 1 => FALSE),
//   array(0 => "LEFONG", 1 => FALSE)
// ];

$ARRAY_CATALOGUES = [
  array(0 => "DONALDSON", 1 => TRUE)
];

$ARRAY_NAME_CATALOGUES = ["DONALDSON"];

$ARRAY_SPLIT_CHARS = ['.', '-', ' '];

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
