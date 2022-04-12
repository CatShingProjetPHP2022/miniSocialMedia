<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
//relier notre site à la database
$pdo = new PDO('mysql:host=localhost;dbname=projet-php', 'root', 'root', array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING, PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));
$pdo->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
// On ouvre une session pour y stocker les infos utilisateurs
session_start();

// Pour se déconnecter
if(isset($_GET['action']) && $_GET['action']="deconnexion") {
    session_destroy();
    header("location:connection.php");
}

//je declare une variable qui me permettra d'afficher des messages pour l'utilisateur:
$content ='';


function getNav() {
    ?>
        <header>
            <img src="<?php echo $_SESSION['user']['photo_link']?>" alt="profile_picture" class="pp_preview">
            <form class ="search-bar" name="search" method="get" action="">
                <input type="text" name="searchRequest" value="<?php if (isset($_GET["searchRequest"])) {echo $_GET["searchRequest"];} ?>" placeholder="">
                <input type="submit" name="confirmSearch" value="Rechercher">
            </form>
            <nav>
                <a href="index.php"><img src="images/house.svg" alt="home"></a>
                <a href="profile.php"><img src="images/message.svg" alt="message"></a>
                <a href="messages.php"><img src="" alt=""></a>
                <?php if(isset($_SESSION["user"])) { ?>
                <a href="?action=déconnexion"><img src="" alt="">Se déconnecter</a>
                <?php } ?>
            </nav>
        </header>
        <a href=""></a>
    <?php
        // affiche les résultats
        global $pdo;
        if(isset($_GET["confirmSearch"]) && !empty(trim($_GET["searchRequest"]))) {
            header("location:search_results.php?searchRequest=".trim($_GET["searchRequest"]));
            // ."&confirmSearch=Rechercher"
        }
}

function followBefriend ($input, $target) {
    // global $_SESSION['followed_list'];
    // global $_SESSION['list_of_friends'];
    // bouton avec les différentes options
    global $userId_int, $pdo;
    $friend;
    $listFriend = $_SESSION['list_of_friends'];
    $listFollowed = $_SESSION['followed_list'];
    $alreadyAsked = $pdo->query("SELECT * FROM friend_request WHERE id_user ='$userId_int' AND id_friend='$target' AND accept=FALSE");
    $alreadyAsked2 = $pdo->query("SELECT * FROM friend_request WHERE id_user ='$target' AND id_friend='$userId_int' AND accept=FALSE");
    foreach($listFriend as $id_friend) {
        if ($target == $id_friend) {
                echo '<option value="unfriend">Ne plus être ami avec cette personne</option>';
            $friend = true;
        } else if ($alreadyAsked->rowCount() != 0) {
            echo '<option value="alreadyAsked">Vous avez déjà envoyé une demande d\'ami</option>';
        } else if ($alreadyAsked2->rowCount() != 0) {
            echo '<option value="alreadyAsked">On vous a déjà envoyé une demande d\'ami</option>';
        } else {
            echo '<option value="befriend">Demander en ami</option>';
        }          
    }
    if (!isset($friend)) {
        $followed;
        foreach($listFollowed as $id_followed) {
            if ($target == $id_followed) {
                // Je suis déjà cette personne
                $followed = true;
                echo '<option value="unfollow">Se désabonner</option>';

            } else {
                echo '<option value="follow">S\'abonner</option>';
            }
        }
    }
    if (isset($input)) {
        switch($input) {
            case "follow":
                $req = "INSERT INTO followed_list (id_user, id_followed, unfollow) VALUES (:id_user, :id_target, 0);
                UPDATE user SET followed_list=CONCAT(followed_list, ' ',:id_target) WHERE id_user=:id_user ";
                $follow = $pdo->prepare($req);
                $follow->bindValue(':id_user', $userId_int);
                $follow->bindValue(':id_target', $target);
                $follow->execute();
                break;
            case "befriend":
                $req = "INSERT INTO friend_request (id_user, id_friend, accept, date) VALUES (:id_user, :id_target, FALSE, NOW() )";
                $befriend = $pdo->prepare($req);
                $befriend->bindValue(':id_user', $userId_int);
                $befriend->bindValue(':id_target', $target);
                $befriend->execute();
                break;
            case "unfollow":
                //DELETE FROM followed_list WHERE id_user= '$userId_int' and id_followed='$target';
                // $pdo->exec("UPDATE user SET followed_list=REPLACE(followed_list,' $target ', ' ') WHERE id_user='$userId_int';
                //             UPDATE user SET followed_list =INSERT(followed_list, LOCATE(' $target ', followed_list), CHAR_LENGTH(' $target'), '') WHERE id_user='$userId_int';
                //             UPDATE user SET followed_list =REVERSE(INSERT(REVERSE(followed_list), LOCATE(REVERSE('$target '), REVERSE(followed_list)), CHAR_LENGTH('$target '), '')) WHERE id_user='$userId_int';");
                $req = "UPDATE user SET followed_list=REPLACE(followed_list, :doubleSpace, ' ') WHERE id_user=:id_user;
                UPDATE user SET followed_list=INSERT(followed_list, LOCATE(:doubleSpace, followed_list), CHAR_LENGTH(:spaceLeft), '') WHERE id_user=:id_user;
                UPDATE user SET followed_list =REVERSE(INSERT(REVERSE(followed_list), LOCATE(REVERSE(:spaceRight), REVERSE(followed_list)), CHAR_LENGTH(:spaceRIGHT), '')) WHERE id_user=:id_user";
                $unfollow = $pdo->prepare($req);
                $doubleSpace = " ".$target." ";
                $spaceLeft = " ".$target;
                $spaceRight = $target." ";
                $unfollow->bindValue(':doubleSpace', $doubleSpace);
                $unfollow->bindValue(':spaceLeft', $spaceLeft);
                $unfollow->bindValue(':spaceRight', $spaceRight);
                $unfollow->execute();
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

// function addComment($idPost) {
    ?>
    <!-- // <form class="form-add-comment" name="comment" method="post">
    //     <input type="text" name="content-comment">
    //     <input type="submit" value="Ecrire un commentaire">
    // </form> -->
    <?php
//     if($_POST["content-comment"]) {
//         global $pdo, $username, $userId_int;
//         $comment= addslashes($_POST["content-comment"]);
//         $pdo->exec("INSERT INTO comment(id_post, id_user, author_username, date, content) VALUES ('$idPost', '$userId_int', '$username', NOW(), '$comment')");
//     }
// }

function comments($idPost) {
    echo "<div class='container-all-comments'>";
        $uniqueNameForm = "content-comment-post".$idPost;
        ?>
        <form class="form-add-comment" name="comment" method="post">
            <input type="text" name="<?php echo $uniqueNameForm; ?>">
            <input type="submit" value="Ecrire un commentaire">
        </form>
        <?php
        // Ecrire un commentaire
        if(isset($_POST["$uniqueNameForm"])) {
            global $pdo, $username, $userId_int;
            $comment= addslashes($_POST["$uniqueNameForm"]);
            $pdo->exec("INSERT INTO comment(id_post, id_user, author_username, date, content) VALUES ('$idPost', '$userId_int', '$username', NOW(), '$comment')");
        }
        global $pdo, $username;
        $c = $pdo->query("SELECT * FROM comment WHERE id_post = '$idPost' ORDER BY date");
        // Montrer les commentaires
        while($allComments = $c->fetch(PDO::FETCH_ASSOC)) {
            ?><div class="container_comment_single">
                <p><?php if ($allComments['author_username']==$username) { echo "Moi";} else { echo $allComments['author_username'];}?></p>
                <p><?php echo $allComments['content'];?></p>
                <p><?php echo $allComments['date'];?></p>
            </div>
            <?php
        }
    echo "</div>";
}

function createPost($createPostSQL) {
    global $pdo, $username, $userId_int;
    ?>
        <!-- <div>
            <input type="checkbox" name="my-checkbox" id="check_create_post">
            <label for="check_create_post">+</label>
        </div> -->
        <!-- Pour poster -->
        <form class="form-create-post" name="create-post" method="post">
            <input type="text" name="content-post" required>
            <input type="file" name="photo_link_post">
            <input type="submit" value="Poster !">
        </form>
        <!-- requête pour créer un post -->
        <?php 
        // var_dump($userId_int);
            if($_POST) {
                if (isset($_POST["content-post"])) {
                    // Gestion d'erreur pour les ""
                    $content= addslashes($_POST['content-post']);
                    // Lien par défaut qui mène nulle part
                    $_POST["photo_link_post"] = "aupif/url/photo.jpeg";
                    // Lien pour rajouter notre photo si il y en a une à rajouter
                    if (!file_exists($_POST["photo_link_post"])) {
                        $photo_link2="photo_link_post";
                        $_POST["photo_link_post"] = uploadPicture($photo_link2);
                    }
                    // Requete pour créer le poste
                    $pdo->exec($createPostSQL);
                }
            }
}

function showPosts($showPostSQL) {
    global $pdo, $username;
    //on affiche les post
    $r2= $pdo->query($showPostSQL);
    while($allPost = $r2->fetch(PDO::FETCH_ASSOC)) {
        $profilePicture = $pdo->query("SELECT photo_link FROM user WHERE id_user = '$allPost[id_author]' ");
        $profilePicture = $profilePicture->fetch();
        ?><div class="container_post_single">
                <a href="profile.php?profil=<?php echo $allPost['id_author'] ?>"><?php if ($allPost['author_username']==$username) { echo "Moi";} else { echo $allPost['author_username'];}?></a>
                <img class="profil-picture" src="<?php echo $profilePicture[0];?>" alt="image de profil">
                <?php if($allPost['photo_link'] != "aupif/url/photo.jpeg"){ echo "<img class='image-post' src=".$allPost['photo_link']." alt='image du post'>"; } ?>
                <p><?php echo $allPost['content'];?></p>
                <p><?php echo $allPost['date'];?></p>
                <?php 
                comments($allPost['id_post']);
        echo "</div>";
    }
}

?>