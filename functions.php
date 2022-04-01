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

//je declare une variable qui me permettra d'afficher des messages pour l'utilisateur:
$content ='';


function followBefriend ($input, $target) {
    // global $_SESSION['followed_list'];
    // global $_SESSION['list_of_friends'];
    // bouton avec les différentes options
        $friend;
        $listFriend = $_SESSION['list_of_friends'];
        $listFollowed = $_SESSION['followed_list'];
        foreach($listFriend as $id_friend) {
            if ($target == $id_friend) {
                echo '<option value="unfriend">Ne plus être ami avec cette personne</option>';
                $friend = true;
            } 
        }
        if ($friend != true) {
            $followed;
            foreach($listFollowed as $id_followed) {
                if ($target == $id_followed) {
                    $followed = true;
                    echo '<option value="unfollow">Se désabonner</option>';
                    echo '<option value="befriend">Demander en ami</option>';
                    ?> <option value=""> <?php echo var_dump($target) ;?> </option> <?php
                    ?> <option value=""> <?php echo var_dump($input) ;?> </option> <?php
                }
            }
            if ($followed != true) {
                echo '<option value="follow">S\'abonner</option>';
                echo '<option value="befriend">Demander en ami</option>';
            }
        }
    if (isset($input)) {
        global $userId_int;
        global $pdo;
        switch($input) {
            case "follow":
                $pdo->exec("INSERT INTO followed_list (id_user, id_followed, unfollow) VALUES ('$userId_int', '$target', 0);
                            UPDATE user SET followed_list=CONCAT(followed_list,' $target') WHERE id_user='$userId_int' ");
                break;
            case "befriend":
                $pdo->exec("INSERT INTO friend_request (id_friend_1st, id_friend_2nd, accept, date ) VALUES ('$userId_int', '$target', 0, NOW());
                            UPDATE user SET list_of_friends=CONCAT(list_of_friends,' $target') WHERE id_user='$userId_int' ");
                break;
            case "unfollow":
                //DELETE FROM followed_list WHERE id_user= '$userId_int' and id_followed='$target';
                $pdo->exec("UPDATE user SET followed_list=REPLACE(followed_list,' $target ', ' ') WHERE id_user='$userId_int';
                            UPDATE user SET followed_list =INSERT(followed_list, LOCATE(' $target ', followed_list), CHAR_LENGTH(' $target'), '') WHERE id_user='$userId_int';
                            UPDATE user SET followed_list =REVERSE(INSERT(REVERSE(followed_list), LOCATE(REVERSE('$target '), REVERSE(followed_list)), CHAR_LENGTH('$target '), '')) WHERE id_user='$userId_int';");
                break;
            case "unfriend":
                $pdo->exec("DELETE FROM friend_request WHERE id_friend_1st= '$userId_int' and id_friend_2nd='$target';
                            DELETE FROM friend_request WHERE id_friend_2nd= '$userId_int' and id_friend_1st='$target';
                            UPDATE user SET list_of_friends=REPLACE(list_of_friends,' '$target' ', ' ') WHERE id_user='$userId_int';
                            UPDATE user SET list_of_friends =INSERT(list_of_friends, LOCATE(' \'$target\' ', list_of_friends), CHAR_LENGTH(' \'$target\'), '') WHERE id_user='$userId_int';
                            UPDATE user SET list_of_friends =REVERSE(INSERT(REVERSE(list_of_friends), LOCATE(REVERSE('\'$target\' '), REVERSE(list_of_friends)), CHAR_LENGTH('\'$target\' '), '')) WHERE id_user='$userId_int';");
                break;
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