<?php
//connexion à la base de données
require('partials/_toolsConnexion.php');


header("Access-Control-Allow-Origin: *");
$data = file_get_contents('php://input');
$response = new \stdClass();

$id = $_SESSION['user']['id'];
$name = $_POST['name'];
$firstname =  $_POST['firstname'];
$nickname = $_POST['nickname'];
$email = $_POST['email'];
$pass = md5($_POST['password']);
$color = $_POST['color'];

if (isset($name) && !empty($name) && isset($firstname) && !empty($firstname) && isset($nickname) && !empty($nickname) && isset($email) && !empty($email) && isset($pass)&& !empty($pass) ) {
    $queryEdit = $db->prepare('UPDATE chat_user SET name = ?, firstname = ?, pseudo = ?, password = ?, email = ?, color = ? WHERE id=' . $id);
    $resultEdit = $queryEdit->execute([
        ucfirst($name),
        ucfirst($firstname),
        ucfirst($nickname),
        $pass,
        $email,
        $color
    ]);
    $_SESSION['user']['firstname'] = $firstname;
    $_SESSION['user']['name'] = $name;
    $_SESSION['user']['password'] = $pass;
    $_SESSION['user']['email'] = $email;
    $_SESSION['user']['pseudo'] = $nickname;

    $response->type = 1;
    $response->msg = 'Update with success';
    echo json_encode($response);
}
else{
    $response->type = 0;
    $response->msg = 'Update failed';
    echo json_encode($response);
}
