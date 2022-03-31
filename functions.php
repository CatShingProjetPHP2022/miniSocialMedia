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
        $friend = 0;
        foreach($_SESSION['list_of_friends'] as $id_friend) {
            if ($target == $id_friend) {
                $friend += 1;
                echo '<option value="unfriend">Ne plus être ami avec cette personne</option>';
                break;
            } else {
                echo '<option value="befriend">Demander en ami</option>';
                break;
            }
        }
        if ($friend == 0) {
            foreach($_SESSION['followed_list'] as $id_followed) {
                if ($target == $id_followed) {
                    echo '<option value="unfollow">Se désabonner</option>';
                    break;
                } else {
                    echo '<option value="follow">S\'abonner</option>';
                    break;
                }
            }
        }
    if ($input) {
        global $userId_int;
        global $pdo;
        switch($input) {
            case "follow":
                $pdo->exec("INSERT INTO followed_list (id_user, id_followed, unfollow) VALUES ('$userId_int', '$target', 0);
                            UPDATE user SET followed_list=CONCAT(followed_list,' $target') WHERE id_user='$userId_int' ");
                // Si ça existe déjà juste on
                break;
            case "befriend":
                $pdo->exec("INSERT INTO friend_request (id_friend_1st, id_friend_2nd, accept, date ) VALUES ('$userId_int', '$target', FALSE, NOW())");
                break;
            case "unfollow":
                // Detruit la ligne correspondante dans la table followed list puis enlève l'ID de la personne dans la followed_list de user
                $pdo->exec("DELETE FROM followed_list WHERE id_user= '$userId_int' and id_followed='$target';
                            UPDATE user SET followed_list=REPLACE(followed_list,' '$target' ', ' ') WHERE id_user=$userId_int;
                            UPDATE user SET followed_list =INSERT(followed_list, LOCATE(' \'$target\' ', followed_list), CHAR_LENGTH(' \'$target\'), '') WHERE id_user=$userId_int;
                            UPDATE user SET followed_list =REVERSE(INSERT(REVERSE(followed_list), LOCATE(REVERSE('\'$target\' '), REVERSE(followed_list)), CHAR_LENGTH('\'$target\' '), '')) WHERE id_user='$userId_int';");
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
        $adressPicture = "./images/".$pictureName;
        move_uploaded_file($_FILES["$file"]["tmp_name"], $adressPicture);
        return $adressPicture;
    } 
}




?>