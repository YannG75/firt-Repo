<?php
require('partials/_toolsConnexion.php');

header("Access-Control-Allow-Origin: *");
$response = new \stdClass();

if (isset($_SESSION['user'])) {
    $response->firstname = $_SESSION['user']['firstname'];
    $response->name = $_SESSION['user']['name'];
    $response->email = $_SESSION['user']['email'];
    $response->password = $_SESSION['user']['password'];
    $response->pseudo = $_SESSION['user']['pseudo'];
    $response->color = $_SESSION['user']['color'];

    echo json_encode($response);
}