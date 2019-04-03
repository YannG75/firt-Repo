<?php
session_start();

try{
    $db = new PDO('mysql:host=localhost;dbname=chat;charset=utf8mb4', 'root', '', array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
}
catch (Exception $exception)
{
    die( 'Erreur : ' . $exception->getMessage() );
}

if (isset($_SESSION['user']['id']) && $_SESSION['user']['id'] == false){
    $res->userConnect = "userConnect";
    header('location: index.php');
}


/*if (isset($_SESSION['user']['id']) == false AND $_GET['index.html']){
    header('Location: index.php');
}*/