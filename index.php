<?php

require('partials/_toolsConnexion.php');

if (isset($_GET['disconnect'])) {
    if (isset($_SESSION['user'])) {
        $query = $db->prepare('UPDATE chat_user SET is_connected = ? WHERE id = ?');
        $result = $query->execute(
            [
                0,
                $_SESSION['user']['id']
            ]
        );
    }
        unset($_SESSION['user']);

}

if (isset($_POST['connexion'])) {
    $queryUser = $db->prepare('SELECT * FROM chat_user WHERE email = ? AND password = ?');
    $queryUser->execute(array($_POST['email'], md5($_POST['password'])));

    $userExist = $queryUser->rowCount();

    if ($userExist == 1) {
        if (isset($_SESSION['user'])) {
            $query = $db->prepare('UPDATE chat_user SET is_connected = ? WHERE id = ?');
            $result = $query->execute(
                [
                    0,
                    $_SESSION['user']['id']
                ]
            );
            unset($_SESSION['user']);
        }


        $resultUser = $queryUser->fetch();
        $_SESSION['user'] = $resultUser;
        $email = $_POST['email'];
        $password = $_POST['password'];


        $query = $db->prepare('UPDATE chat_user SET is_connected = ? WHERE id = ?');
        $result = $query->execute(
            [
                1,
                $_SESSION['user']['id']
            ]
        );


        header('Location: chat.html');


        exit;
    }


} else {
    $email = NULL;
    $password = NULL;
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php require('partials/header.php'); ?>
</head>
<body>
<section id="sectionForm">
    <form id="connexionForm" method="post">
        <h1>Connectez-vous</h1>
        <input placeholder="Email" id="email" name="email" type="email" value="fffffff@ffff.ff">
        <?php if (isset($_POST['email']) && empty($_POST['email'])): ?>
            <p>l'email est obligatoire</p>
        <?php endif; ?>
        <input placeholder="Mot de passe" id="password" name="password" type="password" value="123456789">
        <?php if (isset($_POST['password']) && empty($_POST['password'])): ?>
            <p>le mot de passe est obligatoire</p>
        <?php endif; ?>
        <span id="alert"></span>
        <p style="color: white;">Vous n'avez pas de compte ? <a href="register.php" style="color: purple;">
                Inscrivez-vous !</a></p>
        <div>
            <button name="connexion" type="submit" id="connexion">Connexion</button>
        </div>
    </form>
</section>
</body>
</html>
