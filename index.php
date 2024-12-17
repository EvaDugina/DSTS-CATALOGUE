<?php
//session_start();
include_once('auth_ssh.class.php');
$au = new auth_ssh();
if ($au->loggedIn())
    header('Location:search.php');
else
    header('Location:login.php');
