<?php
require_once("settings.php");
include_once('auth_ssh.class.php');


// защита от случайного перехода незарегистрированного пользователя
function checkAuLoggedIN($au) {
  if (!$au->loggedIn()) {
    header('Location:login.php');
    exit;
  }
}

// защита от случайного перехода студента
function checkAuIsAdmin($au){
  if (!$au->isAdmin()){
    $au->logout();
    header('Location:login.php');
  }
}

$ARRAY_SPLIT_CHARS = ['.', '-', ' '];

function getSplitArticleName($article_name, $arrayChars) {
  foreach ($arrayChars as $char) {
      if(strpos($article_name, $char))
          return explode($char, $article_name);
  }
  return false;
}

function concatArrayByChar($arrayString, $char) {
  $str = "";
  foreach($arrayString as $key => $string) {
      $str .= $string;
      if($key < count($arrayString)-1)
          $str .= $char;
  }
  return $str;
}