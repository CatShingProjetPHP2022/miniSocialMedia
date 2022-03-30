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
        global $userId_int;
        global $pdo;
        if ($input=="follow") {
            $pdo->exec("INSERT INTO followed_list (id_user, id_followed) VALUES ('$userId_int', '$target')");
            $pdo->exec("UPDATE user SET followed_list=CONCAT(followed_list,' $target') WHERE id_user='$userId_int' ");
            // pour s'abonner il faut aller sur le compte de la personne auquel on veut s'abonner, il y a donc une requete pour
            // pour cet utilisateur sur lequel on a clique, on recup les infos de cette requette à la place du 2
        } else if ($input=="befriend") {
            $pdo->exec("INSERT INTO friend_request (id_user, id_friend, accept, date  ) VALUES ('$userId_int', '$target', 0, NOW())");
        }
    }

}


//je declare une variable qui me permettra d'afficher des messages pour l'utilisateur:
$content ='';


function followBefriend2 ($input, $target) {
    // global $_SESSION['followed_list'];
    // global $_SESSION['list_of_friends'];
    // bouton avec les différentes options
    echo '<form method="post"> <select name="follow_friend_request" id="" class="btn-follow-befriend">';
        foreach($_SESSION['followed_list'] as $id_followed) {
            if ($target == $id_followed) {
                echo '<option value="unfollow">Se désabonner</option>';
            } else {
                echo '<option value="follow">S\'abonner</option>';
            }
        }
        foreach($_SESSION['list_of_friends'] as $id_friend) {
            if ($target == $id_friend) {
                echo '<option value="unfriend">Ne plus être ami avec cette personne</option>';
            } else {
                echo '<option value="befriend">Demander en ami</option>';
            }
        }
    echo '</select> <input type="submit" value="Soumettre"> </form>';
    if ($input) {
        global $userId_int;
        global $pdo;
        switch($input) {
            case "follow":
                $pdo->exec("INSERT INTO followed_list (id_user, id_followed, unfollow) VALUES ('$userId_int', '$target', 0)");
                $pdo->exec("UPDATE user SET followed_list=CONCAT(followed_list,' $target') WHERE id_user='$userId_int' ");
                break;
            case "befriend":
                $pdo->exec("INSERT INTO friend_request (id_friend_1st, id_friend_2nd, accept, date ) VALUES ('$userId_int', '$target', FALSE, NOW())");
                break;
            case "unfollow":
                $pdo->exec("UPDATE followed_list  SET unfollow=1");
                break;
            case "unfriend":
                $pdo->exec("UPDATE friend_request  SET accept=0");
        }
    }
}


// Ajouter une photo de profil
// Si notre image à un nom et pas d'erreur de chargement
function uploadPicture ($file) {
    if(isset($_FILES["$file"]["name"]) && ($_FILES["$file"]["error"] == 0)) {
        // stockage de l'image
        $pictureName = preg_replace("/\s+/", "", (time().basename($_FILES["$file"]["name"])));
        $parent = dirname(__DIR__);
        $adressPicture = "./images/".$pictureName;
        move_uploaded_file($_FILES["$file"]["tmp_name"], $adressPicture);
        return $adressPicture;
    } 
}




?>