<?php
require('partials/_toolsConnexion.php');

header("Access-Control-Allow-Origin: *");

$queryUsers = $db->query('SELECT id, pseudo FROM chat_user');
$users= $queryUsers->fetchAll();

$array = [];
for ($i = 0; $i < sizeof($users); $i++) {

    $array[$i] = [
        "userId" => $users[$i]['id'],
        "pseudo" => $users[$i]['pseudo']
    ];

}

$res = new stdClass();
$res->arrayChatUsers = $array;

echo  json_encode($res);