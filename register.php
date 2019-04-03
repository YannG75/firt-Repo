<?php
require ('partials/_toolsConnexion.php');

//initialisation du conteneur desp otentiels messages
$messages = [];
//si le formulaire a été soumis
if(isset($_POST['register'])){

    //si firstname est vide, j'ajoute un message à mon tableau
    //idem pour les autres champs vides
    if(empty($_POST['firstname'])){
        $messages['firstname'] = 'le prénom est obligatoire';
    }
    if(empty($_POST['name'])){
        $messages['name'] = 'le nom de famille est obligatoire';
    }
    if(empty($_POST['emailRegister'])){
        $messages['email'] = 'l-email est obligatoire';
    }
    if(empty($_POST['passwordRegister'])){
        $messages['password'] = 'le mot de passe est obligatoire';
    }
    if(empty($_POST['pseudo'])){
        $messages['pseudo'] = 'le pseudo est obligatoire';
    }
    if(empty($_POST['color'])){
        $messages['color'] = 'la couleur est obligatoire';
    }

    //ici on check si ladresse email existe déjà en base de données
    $query = $db->prepare('SELECT email FROM chat_user WHERE email = ?');
    $query->execute(
        [
            $_POST['emailRegister']
        ]
    );
    $emailExist = $query->fetch();

    //si l'email est déjà dans la base de données, on prévient l'utilisateur qu'il ne peut pas l'utiliser
    if($emailExist != false){
        $messages['emailExist'] = "l'adresse email est déjà utilisée";
    }

    //si et seulement si aucun message n'a été mis dans le tableau, alors faire l'insertion en DB
    if(empty($messages)){
        $query = $db->prepare('INSERT INTO chat_user (firstname, name, email, password, pseudo, color) VALUES (?, ? , ? , ? , ?, ?)');
        $result = $query->execute(
            [
                $_POST['firstname'],
                $_POST['name'],
                $_POST['emailRegister'],
                md5($_POST['passwordRegister']),
                $_POST['pseudo'],
                $_POST['color']
            ]
        );
        $lastUserId = $db -> lastInsertId();

        $insertChannelGlobal = $db->prepare('INSERT INTO channel_user (id_user, id_channel) VALUES (?, ?)');
        $ChannelGlobal = $insertChannelGlobal->execute(
            [
             $lastUserId,
             1000,
            ]
        );
            

        header('Location: index.php');
        //si l'ensertion s'est bien passée, prévenir l'utilisateur
        if($result == true){
            echo 'enregistrement ok !';
        }
        else{
            echo 'enregistrement pas ok...';
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php  require ('partials/header.php'); ?>
</head>
<body>
<section>
    <form id="register" method="post">
        <input placeholder="Prénom..." id="firstname" name="firstname" type="text">
        <input placeholder="Nom..." id="name" name="name" type="text">
        <input placeholder="Pseudo..." id="pseudo" name="pseudo" type="text">
        <input placeholder="Email..." id="emailRegister" name="emailRegister" type="email">
        <input placeholder="Mot de passe..." id="passwordRegister" name="passwordRegister" type="password">
        <input type="color" id="color" name="color" placeholder="Couleur..." style="max-width: 100px; border-radius: 30px; padding-right:10px" >
        <p style="color: white;">Vous avez déjà un compte ? <a href="index.php" style="color: purple;"> Connectez-vous !</a></p>
        <button name="register" id="register" type="submit">Inscription</button>
    </form>
</section>
<?php
foreach($messages as $message){
    echo $message . '<br>';
}
?>
</body>
</html>