<?php
require('partials/_toolsConnexion.php');

header("Access-Control-Allow-Origin: *");
$data = file_get_contents('php://input');

$title = $_POST['title'];
$description = $_POST['description'];
$users = $_POST['users'];
$response = new \stdClass();

if ($title AND $users){
$channel = $db->prepare('INSERT INTO channel (title, description) VALUES (?, ?)');
$channel->execute(
    [
        $title,
        $description
    ]
);
$last_insert = $db->lastInsertId();
foreach ($users as $user) {
$userChannel = $db->prepare('INSERT INTO channel_user (id_user,id_channel ) VALUES (?, ?)');
$userChannel->execute(
    [
        $user,
        $last_insert
    ]
);
}

$response->type = 1;
    $response->msg = 'Cest du tout propre';
}

else {
    $response->type = 0;
    $response->msg = 'ça marche pas niggae';
}
echo json_encode($response);