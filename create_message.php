<?php
require('partials/_toolsConnexion.php');

setlocale(LC_TIME, "fr_FR");
$res = new stdClass();

$data = file_get_contents('php://input');
$json = json_decode($data);


$valueMessage = $json->{'valueMessage'};
$channelId = $json->{'channelId'};

//var_dump($channelId);

if (!empty($valueMessage)) {


    $micro_date = microtime();
    $date_array = explode(" ", $micro_date);
    $date = date("Y-m-d H:i:s", $date_array[1]);
    $filterTime = substr($date_array[0], -5, 5);
    $timeToInsert = str_replace($filterTime, "", $date_array[0]);


    $filterTimeTwo = substr($timeToInsert, -5, 1);

    ///////  insers la date au bon format dans    $timeToInsertTwo   pour l'utliser lors des insertions
    $timeToInsertTwo = str_replace($filterTimeTwo, "", $timeToInsert);

    $userID = $_SESSION['user']['id'];


    ///// si le channel n'est pas le channel principal ( channel_id = 1000)

    if (intval($channelId) != 1000) {

        ////////////// securise le channel id recu en verifiant si il est bien associé au user connecté
        $query_secure_channelId = $db->prepare('
        SELECT cus.id_channel
         FROM chat_user cu  JOIN channel_user cus
         ON  cu.id = cus.id_user
        JOIN  channel ch 
        ON  ch.id = cus.id_channel
      WHERE cu.id = ? and  cus.id_channel = ?
         ');

        $query_secure_channelId->execute(
            array(
                $_SESSION['user']['id'],
                $channelId,
            ));
        $secure_channelId = $query_secure_channelId->fetchColumn();


        /////////// insere le content, user id ET  le channel id

        if (!empty($secure_channelId)) {

            $insert_message = $db->prepare('INSERT INTO message (content, user_id, time, channel_id ) VALUES (?, ?, ?, ?)');
            $insert_message->execute(
                array(
                    $valueMessage,
                    $userID,
                    $date . $timeToInsertTwo,
                    intval($channelId)
                ));
        }               //////////////////    insers content, user id MAIS PAS  channel id puisque il est par defaut a 1000 en bd
    } else {
        $insert_message = $db->prepare('INSERT INTO message (content, user_id, time) VALUES (?, ?, ?)');
        $insert_message->execute(
            array(
                $valueMessage,
                $userID,
                $date . $timeToInsertTwo,
            ));
    }

//////// recupere  le message insérer pour etre sur  de l'insertion de celui ci
    $query_message = $db->prepare('
        SELECT m.content, c.id as user_id, c.pseudo, c.color, m.time
         FROM message m  JOIN chat_user c
         ON  m.user_id = c.id  
         WHERE m.time = ?
         ');


    $query_message->execute(array($date . $timeToInsertTwo));
    $message = $query_message->fetchAll();

//////////////////     si le user id correspond au user id du user connecté  le mettre a droite sinon a gauche
    if ($message[0]['user_id'] == $_SESSION['user']['id']) {
        $res->positionMessage = "right";
    } else {
        $res->positionMessage = "left";
    }

    $res->messageContent = $message[0]['content'];
    $res->userColor = $message[0]["color"];
    $res->userPseudo = $message[0]["pseudo"];
    $res->messageTime = $message[0]['time'];

} else {
    $res->msg = "champ vide";
}

$query_last_date = $db->query('SELECT time FROM message ORDER BY time DESC LIMIT 1');

$last_date = $query_last_date->fetchColumn();

$_SESSION['user']['last_time'] = $last_date;


echo json_encode($res);









