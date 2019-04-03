<?php
require('partials/_toolsConnexion.php');

$res = new stdClass();

$res->userName = $_SESSION['user']['firstname'];
echo json_encode($res);
