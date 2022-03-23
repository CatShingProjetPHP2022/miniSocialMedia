<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
//relier notre site à la database
$pdo = new PDO('mysql:host=localhost;dbname=projet-php', 'root', 'root', array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING, PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));

// On ouvre une session pour y stocker les infos utilisateurs
session_start();

// Pour se déconnecter
if(isset($_GET['action']) && $_GET['action']="deconnexion") {
    session_destroy();
    header("location:connection.php");
}

// Demande d'ami et abonnement
function followBefriend($input, $target) {
    if ($input) {
        $userId_int = intval($_SESSION['user']['id_user']);
        global $pdo;
        if ($input=="follow") {
            $pdo->exec("INSERT INTO followed_list (id_user, id_followed) VALUES ('$userId_int', '$target')");
            // pour s'abonner il faut aller sur le compte de la personne auquel on veut s'abonner, il y a donc une requete pour
            // pour cet utilisateur sur lequel on a clique, on recup les infos de cette requette à la place du 2
        } else if ($input=="befriend") {
            $pdo->exec("INSERT INTO friends_list (id_friend_1st, id_friend_2nd, accept  ) VALUES ('$userId_int', '$target', FALSE)");
        }
    }

}


//je declare une variable qui me permettra d'afficher des messages pour l'utilisateur:
$content ='';
?>