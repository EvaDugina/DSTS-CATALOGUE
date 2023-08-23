<?php
//session_start();
include_once('auth_ssh.class.php');
$au = new auth_ssh();
if($au->isAdmin()) 
    header('Location:mainpage_admin.php');
else if($au->loggedIn())
    header('Location:mainpage_user.php');
else 
    header('Location:login.php');
?>
