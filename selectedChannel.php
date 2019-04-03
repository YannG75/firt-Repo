<?php
require('partials/_toolsConnexion.php');

$res = new stdClass();

$data = file_get_contents('php://input');
$idChannel = json_decode($data);


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
        $idChannel,
    ));
$secure_channelId = $query_secure_channelId->fetchColumn();


//var_dump($secure_channelId);
$array = [];
if (!empty($secure_channelId)  ){

    $query_all_channel_message = $db->prepare('
       SELECT m.content, cu.pseudo, cu.id as user_id, cu.color, m.time 
       FROM  message m
       
       JOIN chat_user cu
       ON m.user_id = cu.id

       WHERE m.channel_id = ?
          ORDER by m.time ASC 
         ');

        $query_all_channel_message->bindValue(1,intval($secure_channelId), PDO::PARAM_INT);

    $query_all_channel_message->execute();

    $arrayAllMessage = $query_all_channel_message -> fetchAll();
   // var_dump($arrayAllMessage);

    for ($i = 0; $i < sizeof($arrayAllMessage); $i++) {

        $array[$i] = [
            "message_content" => $arrayAllMessage[$i]['content'],
            "user_color" => $arrayAllMessage[$i]['color'],
            "user_pseudo" => $arrayAllMessage[$i]['pseudo'],
            "message_time" => $arrayAllMessage[$i]['time'],
        ];
        if ($arrayAllMessage[$i]['user_id'] == $_SESSION['user']['id']){
            $array[$i]['positionMessage'] ="right";
        }
        else{
            $array[$i]['positionMessage'] ="left";
        }

    }

}else{
    $res->msh = "pas de channel associé";
}
$res = new stdClass();
$res->arrayAllMessage = $array;
echo json_encode($res);


