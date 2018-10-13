<?php
session_start();
if(isset($_SESSION['user_id'])){
    $_SESSION=array();
    session_destroy();
    setcookie(session_name(),'',time()-3600);
    header('Location: index.php');
    exit();
}else{
    header('Location: index.php');
    exit();
}