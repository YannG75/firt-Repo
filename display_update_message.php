<?php
require('partials/_toolsConnexion.php');


$query_last_date = $db->query('SELECT time FROM message ORDER BY time DESC LIMIT 1');
$last_date= $query_last_date->fetchColumn();

$res = new stdClass();

if ($last_date != $_SESSION['user']['last_time']) {

    $query_number_of_message_to_display = $db->prepare('SELECT COUNT(*) FROM message WHERE time > ?');
    $query_number_of_message_to_display->execute(
        array(
            $_SESSION['user']['last_time']
        ));
    $number_of_message_to_display = $query_number_of_message_to_display->fetchColumn();

    $query_new_articles = $db->prepare('
        SELECT m.content, c.pseudo, c.color, m.time, c.id as user_id
         FROM message m  JOIN chat_user c
         ON  m.user_id = c.id  
         ORDER by time DESC  LIMIT ?
         ');
    $query_new_articles->bindValue(1,intval($number_of_message_to_display), PDO::PARAM_INT);
    $query_new_articles->execute();


    $new_articles =  $query_new_articles->fetchAll();

    $array = [];

    for ($i = 0; $i < $number_of_message_to_display; $i++) {

        $array[$i] = [
            "user_content" => $new_articles[$i]['content'],
            "user_color" => $new_articles[$i]['color'],
            "user_pseudo" => $new_articles[$i]['pseudo'],
            "messageTime" => $new_articles[$i]['time'],
        ];
        if ($new_articles[$i]['user_id'] == $_SESSION['user_id']){
            $array[$i]['positionMessage'] ="right";
        }
        else{
            $array[$i]['positionMessage'] ="left";
        }
    }

   $new_articles[$number_of_message_to_display-1]['time'];

    $_SESSION['user']['last_time'] = $last_date;

    $res->arrayAllNewMessage = $array;

}else {
    $res->msg = "pas de nouveaux message";

}
echo json_encode($res);







