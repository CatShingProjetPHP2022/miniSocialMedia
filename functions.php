<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
//relier notre site à la database
$pdo = new PDO('mysql:host=localhost;dbname=projet-php', 'root', 'root', array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING, PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));
// $pdo->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, false);
// On ouvre une session pour y stocker les infos utilisateurs
session_start();

// Pour se déconnecter
if(isset($_GET['action']) && $_GET['action']="deconnexion") {
    session_destroy();
    header("location:connection.php");
}

//je declare une variable qui me permettra d'afficher des messages pour l'utilisateur:
$content ='';
// $userId_int = intval($_SESSION['user']['id_user']);
// $updateSession = $pdo->query("SELECT list_of_friends,followed_list FROM user WHERE id_user='$userId_int' ")->fetchAll(PDO::FETCH_ASSOC);
// $_SESSION['list_of_friends'] = explode(" ",$updateSession[0]['list_of_friends']);
// $_SESSION['followed_list'] = explode(" ",$updateSession[0]['followed_list']);

// $updateFriends = $pdo->query("SELECT list_of_friends FROM user WHERE id_user='$userId_int' ");
// if ($updateFriends) {
//     while ($test = $updateFriends->fetch(PDO::FETCH_ASSOC)) {
//         $_SESSION['list_of_friends'] = explode(" ",$test['list_of_friends']);
//     }
// }

// $updateFollows = $pdo->query("SELECT followed_list FROM user WHERE id_user='$userId_int' ");
// if ($updateFollows) {
//     while ($test2 = $updateFollows->fetch(PDO::FETCH_ASSOC)) {
//         $_SESSION['followed_list'] = explode(" ",$test2['followed_list']);
//     }
// }

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
                <a href="profile.php">Mon profil</a>
                <a href="messages.php"><img src="images/message.svg" alt="message"><img src="" alt=""></a>
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
    // bouton avec les différentes options
    global $userId_int, $pdo;
    $friend = 0;
    $listFriend = $_SESSION['list_of_friends'];
    $listFollowed = $_SESSION['followed_list'];
    foreach($listFriend as $id_friend) {
        if ($target == $id_friend) {
                echo '<option value="unfriend">Ne plus être ami avec cette personne</option>';
            $friend += 1;
        }          
    }
    $alreadyAsked = $pdo->query("SELECT COUNT(*) FROM friend_request WHERE id_user ='$userId_int' AND id_friend='$target' AND accept=FALSE")->fetchColumn();
    if ($alreadyAsked > 0) {
        echo '<option value="alreadyAsked">Vous avez déjà envoyé une demande d\'ami</option>';
    }
    $alreadyAsked2 = $pdo->query("SELECT  COUNT(*) FROM friend_request WHERE id_user ='$target' AND id_friend='$userId_int' AND accept=FALSE")->fetchColumn();
    if ($alreadyAsked2 > 0) {
        echo '<option value="alreadyAsked">On vous a déjà envoyé une demande d\'ami</option>';
    }
    if ($friend == 0) {
        if ( $alreadyAsked == 0  && $alreadyAsked2 == 0) {
            echo '<option value="befriend">Demander en ami</option>';
        }
        $followed = 0;
        foreach($listFollowed as $id_followed) {
            if ($target == $id_followed) {
                // Je suis déjà cette personne
                $followed += 1;
                echo '<option value="unfollow">Se désabonner</option>';
            }
        }
        if ($followed == 0) {
            echo '<option value="follow">S\'abonner</option>';
        }
    }
    //variables pour requetes sql
    $doubleSpace = " ".$target." ";
    $spaceLeft = " ".$target;
    $doubleSpace2 = " ".$userId_int." ";
    $spaceLeft2 = " ".$userId_int;
    if (isset($input)) {
        switch($input) {
            case "follow":
                $req = "INSERT INTO followed_list (id_user, id_followed, unfollow) VALUES (:id_user, :id_target, 0);
                UPDATE user SET followed_list=CONCAT(followed_list, ' ', :id_target) WHERE id_user=:id_user ";
                $sth = $pdo->prepare($req);
                $sth->bindValue(':id_user', $userId_int, PDO::PARAM_INT);
                $sth->bindValue(':id_target', $target, PDO::PARAM_INT);
                $sth->execute();
                break;
            case "befriend":
                $req = "INSERT INTO friend_request (id_user, id_friend, accept, date) VALUES (:id_user, :id_target, FALSE, NOW() );";
                $sth = $pdo->prepare($req);
                $sth->bindValue(':id_user', $userId_int, PDO::PARAM_INT);
                $sth->bindValue(':id_target', $target, PDO::PARAM_INT);
                $sth->execute();
                break;
            case "unfollow":
                $req = "DELETE FROM followed_list WHERE id_user=:id_user and id_followed=:id_target;
                UPDATE user SET followed_list=REPLACE(followed_list, :doubleSpace, ' ') WHERE id_user=:id_user;
                UPDATE user SET followed_list=INSERT(followed_list, LOCATE(:doubleSpace, followed_list), CHAR_LENGTH(:spaceLeft), '') WHERE id_user=:id_user;
                UPDATE user SET followed_list =REVERSE(INSERT(REVERSE(followed_list), LOCATE(REVERSE(:spaceLeft), REVERSE(followed_list)), CHAR_LENGTH(:spaceLeft), '')) WHERE id_user=:id_user";
                $sth = $pdo->prepare($req);
                $sth->bindValue(':id_user', $userId_int, PDO::PARAM_INT);
                $sth->bindValue(':id_target', $target, PDO::PARAM_INT);
                $sth->bindValue(':doubleSpace', $doubleSpace, PDO::PARAM_STR);
                $sth->bindValue(':spaceLeft', $spaceLeft, PDO::PARAM_STR);
                $sth->execute();
                break;
            case "unfriend":
                $req = "DELETE FROM friend_request WHERE id_user=:id_user and id_friend=:id_target;
                DELETE FROM friend_request WHERE id_friend=:id_user and id_user=:id_target;
                UPDATE user SET list_of_friends=REPLACE(list_of_friends, :doubleSpace, ' ') WHERE id_user=:id_user;
                UPDATE user SET list_of_friends=INSERT(list_of_friends, LOCATE(:doubleSpace, list_of_friends), CHAR_LENGTH(:spaceLeft), '') WHERE id_user=:id_user;
                UPDATE user SET list_of_friends =REVERSE(INSERT(REVERSE(list_of_friends), LOCATE(REVERSE(:spaceLeft), REVERSE(list_of_friends)), CHAR_LENGTH(:spaceLeft), '')) WHERE id_user=:id_user;

                UPDATE user SET list_of_friends=REPLACE(list_of_friends, :doubleSpace2, ' ') WHERE id_user=:id_target;
                UPDATE user SET list_of_friends=INSERT(list_of_friends, LOCATE(:doubleSpace2, list_of_friends), CHAR_LENGTH(:spaceLeft2), '') WHERE id_user=:id_target;
                UPDATE user SET list_of_friends =REVERSE(INSERT(REVERSE(list_of_friends), LOCATE(REVERSE(:spaceLeft2), REVERSE(list_of_friends)), CHAR_LENGTH(:spaceLeft2), '')) WHERE id_user=:id_target";
                $sth = $pdo->prepare($req);
                $sth->bindValue(':id_user', $userId_int, PDO::PARAM_INT);
                $sth->bindValue(':id_target', $target, PDO::PARAM_INT);
                $sth->bindValue(':doubleSpace', $doubleSpace, PDO::PARAM_STR);
                $sth->bindValue(':spaceLeft', $spaceLeft, PDO::PARAM_STR);
                $sth->bindValue(':doubleSpace2', $doubleSpace2, PDO::PARAM_STR);
                $sth->bindValue(':spaceLeft2', $spaceLeft2, PDO::PARAM_STR);
                $sth->execute();
                break;
        }
    }
    // on actualise la liste d'amis et les abonnements dans le session
    $sth=$pdo->prepare("SELECT list_of_friends, followed_list FROM user where id_user='$userId_int' ");
    $sth->execute();
    $updateSession = $sth->fetchAll(PDO::FETCH_ASSOC);
    $_SESSION['list_of_friends'] = explode(" ",$updateSession[0]['list_of_friends']);
    $_SESSION['followed_list'] = explode(" ",$updateSession[0]['followed_list']);
    if ($_POST["follow_friend_request"]) {
        header("location:profile.php?profil=".$target);
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
        // Renvoie le lien de l'adresse
        return $adressPicture;
    } 
}


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
                if (isset($_POST["content-post"]) && trim($_POST["content-post"]) != "") {
                    // Gestion d'erreur pour les ""
                    $content= addslashes($_POST['content-post']);
                    // Lien pour rajouter notre photo si il y en a une à rajouter
                    if (isset($_POST["photo_link_post"])) {
                        $photo_link2="photo_link_post";
                        $_POST["photo_link_post"] = uploadPicture($photo_link2);
                        if (!file_exists($_POST["photo_link_post"])) {
                            $post= $pdo->prepare($createPostSQL);
                            $post->bindValue(':photo_link_post', $_POST["photo_link_post"]);
                            $post->bindValue(':content', $content);
                            $post->execute();
                        }
                    } else {
                        // Lien par défaut qui mène nulle part
                        $_POST["photo_link_post"] = "aupif/url/photo.jpeg";
                    }
                    // Requete pour créer le poste
                    echo var_dump($_FILES["$photo_link2"]);
                }
            }
}

function showPosts($showPostSQL) {
    global $pdo, $username;
    //on affiche les post
    $r2= $pdo->query($showPostSQL);
    while($allPost = $r2->fetch(PDO::FETCH_ASSOC)) {
        $profilePicture = $pdo->query("SELECT photo_link FROM user WHERE id_user = '$allPost[id_author]' ")->fetchColumn();
        ?><div class="container_post_single">
                <a href="profile.php?profil=<?php echo $allPost['id_author'] ?>"><?php if ($allPost['author_username']==$username) { echo "Moi";} else { echo $allPost['author_username'];}?></a>
                <img class="profil-picture" src="<?php echo $profilePicture;?>" alt="image de profil">
                <?php if($allPost['photo_link'] != "aupif/url/photo.jpeg"){ echo "<img class='image-post' src=".$allPost['photo_link']." alt='image du post'>"; } ?>
                <p><?php echo $allPost['content'];?></p>
                <p><?php echo $allPost['date'];?></p>
                <?php 
                comments($allPost['id_post']);
        echo "</div>";
    }
}

function friendshipAccept($target, $refresh) {
    global $userId_int, $pdo;
    //variables pour requetes sql
    $doubleSpace = " ".$target." ";
    $spaceLeft = " ".$target;
    $doubleSpace2 = " ".$userId_int." ";
    $spaceLeft2 = " ".$userId_int;
    $fr = "UPDATE friend_request SET accept=1 WHERE id_user=:id_target AND id_friend=:id_user;
    UPDATE user SET list_of_friends=CONCAT(list_of_friends, ' ', :id_user) WHERE id_user=:id_target;
    UPDATE user SET list_of_friends=CONCAT(list_of_friends, ' ', :id_target) WHERE id_user=:id_user;
    
    DELETE FROM followed_list WHERE id_user=:id_user and id_followed=:id_target;
    UPDATE user SET followed_list=REPLACE(followed_list, :doubleSpace, ' ') WHERE id_user=:id_user;
    UPDATE user SET followed_list=INSERT(followed_list, LOCATE(:doubleSpace, followed_list), CHAR_LENGTH(:spaceLeft), '') WHERE id_user=:id_user;
    UPDATE user SET followed_list =REVERSE(INSERT(REVERSE(followed_list), LOCATE(REVERSE(:spaceLeft), REVERSE(followed_list)), CHAR_LENGTH(:spaceLeft), '')) WHERE id_user=:id_user;
    
    DELETE FROM followed_list WHERE id_user=:id_target and id_followed=:id_user;
    UPDATE user SET followed_list=REPLACE(followed_list, :doubleSpace2, ' ') WHERE id_user=:id_target;
    UPDATE user SET followed_list=INSERT(followed_list, LOCATE(:doubleSpace2, followed_list), CHAR_LENGTH(:spaceLeft2), '') WHERE id_user=:id_target;
    UPDATE user SET followed_list =REVERSE(INSERT(REVERSE(followed_list), LOCATE(REVERSE(:spaceLeft2), REVERSE(followed_list)), CHAR_LENGTH(:spaceLeft2), '')) WHERE id_user=:id_user";
    $sth = $pdo->prepare($fr);
    $sth->bindValue(':id_user', $userId_int, PDO::PARAM_INT);
    $sth->bindValue(':id_target', $target, PDO::PARAM_INT);
    $sth->bindValue(':doubleSpace', $doubleSpace, PDO::PARAM_STR);
    $sth->bindValue(':spaceLeft', $spaceLeft, PDO::PARAM_STR);
    $sth->bindValue(':doubleSpace2', $doubleSpace2, PDO::PARAM_STR);
    $sth->bindValue(':spaceLeft2', $spaceLeft2, PDO::PARAM_STR);
    $sth->execute();
    $sth = $pdo->prepare("SELECT list_of_friends, followed_list FROM user where id_user='$userId_int'");
    $sth->execute();
    $afterNotif = $sth->fetchAll(PDO::FETCH_ASSOC);
    $_SESSION['list_of_friends'] = explode(" ",$afterNotif[0]['list_of_friends']);
    $_SESSION['followed_list'] = explode(" ",$afterNotif[0]['followed_list']);
    if ($refresh) {
        header("location:index.php");
    }
}

?>