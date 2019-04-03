<?php
require('partials/_toolsConnexion.php');

if (isset($_SESSION['user']['last_channel_id'])) {



    $query_last_channel_id = $db->query('SELECT id FROM channel ORDER BY id DESC LIMIT 1');
    $last_channel_id = $query_last_channel_id->fetchColumn();
    $res = new stdClass();

    if ($_SESSION['user']['last_channel_id'] = !$last_channel_id) {
        $query_number_of_channel_to_display = $db->prepare('

            SELECT COUNT(*) 
            FROM channel ch
            
            JOIN channel_user cus
                     ON  ch.id = cus.id_channel
                    JOIN  chat_user cu
                    ON  cu.id = cus.id_user
                    WHERE id > ?  AND chu = ?
            ');

        $query_number_of_channel_to_display->execute(
            array(
                $_SESSION['user']['last_channel_id'],
                $_SESSION['user']['id']
            ));
        $number_of_channel_to_display = $query_number_of_channel_to_display->fetchColumn();


        $query_new_channels = $db->prepare('
         SELECT ch.title as channel_title, ch.description as channel_description, ch.id as channel_id
         FROM chat_user cu  JOIN channel_user cus
         ON  cu.id = cus.id_user
        JOIN  channel ch 
        ON  ch.id = cus.id_channel
      WHERE cu.id = ?
      ORDER by id DESC LIMIT ?
         ');

        $query_new_channels->execute(
            $array(
                $_SESSION['user']['id'],
                $number_of_channel_to_display
            )
        );

        $new_channels = $query_new_channels->fetchAll();

        $array = [];

        for ($i = 0; $i < $number_of_channel_to_display; $i++) {

            $array[$i] = [
                "channel_title" => $allChannel[$i]['channel_title'],
                "channel_description" => $allChannel[$i]['channel_description'],
                "channel_id" => $allChannel[$i]['channel_id'],
            ];

        }

        $res->arrayAllUserChannel = $array;


        $_SESSION['user']['last_channel_id'] = $last_channel_id;


    } else {
        $res->msg = "pas de nouveaux channels";
    }
    echo json_encode($res);

}