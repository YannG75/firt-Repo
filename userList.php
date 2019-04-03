<?php
require ('partials/_toolsConnexion.php');

$data = file_get_contents('php://input');
$idChannel = json_decode($data);

$res = new stdClass();

$users = $db->prepare(
    'SELECT cu.pseudo, cu.is_connected 
              FROM chat_user cu'
);

$query_users = $db->prepare('
        SELECT cu.pseudo, cu.is_connected 
         FROM chat_user cu  JOIN channel_user cus
         ON  cu.id = cus.id_user
      WHERE  cus.id_channel = ?
         ');

$query_users->execute(
    array(
        $idChannel,
    ));
$users = $query_users->fetchAll();

var_dump($users);
$array = [];
for ($i = 0; $i < sizeof($users); $i++) {

    $array[$i] = [
        "pseudo" => $users[$i]['pseudo'],
    ];
    if ($users[$i]['is_connected'] == "1"){
        $array[$i]['is_connected'] ="green";
    }
    else{
        $array[$i]['is_connected'] ="red";
    }

}

$res = new stdClass();
$res->arrayAllChatUsers = $array;
echo json_encode($res);



die();
