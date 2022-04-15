<?php
//cacher les erreurs
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(0);
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
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

function getNav() {
    global $userId_int, $pdo;
    ?>
        <header class="overal_header">
            <div class="header-container">
            <div class="logo-container">
                <span><a href="index.php">CATSHING</a></span>
            </div>
           
        <form class ="search-bar" name="search" method="get">
                <input type="text" name="searchRequest" value="<?php if (isset($_GET["searchRequest"])) {echo $_GET["searchRequest"];} ?>" placeholder="Rechercher">
                <input type="submit" name="confirmSearch" value="GO" class="search-button">
            </form>
            <nav class="icons">
                <a href="index.php"><img src="images/assets/house.svg" alt="home"></a>
                <a href="messages.php"><img src="images/assets/message.svg" alt="message"></a>
                <?php $unredMessages = $pdo->query("SELECT COUNT(*) FROM `message` WHERE id_recipient='$userId_int' AND is_read='NOREAD'")->fetchColumn();
                //montre le nbr de messages non lus
                if ($unredMessages > 0) {
                    echo "<div class='RENTRER NOM DE CLASSE'>".$unredMessages."</div>";
                }
                if(isset($_SESSION["user"])) { ?>
                <a href="?action=déconnexion">Se déconnecter</a>
                <?php } /*ELSE RAJOUTER UNE OPTION QUI AFFICHERAIT "SE CONNECTER" - non il y a pas besoin,si on est pas connecter on est automatiquement redirigé vers la page de connexion, on peut pas rentrer sans se connecter */ ?>
                <div class="pp_container">
                    <a href="profile.php"><img src="<?php echo $_SESSION['user']['photo_link']?>" alt="profile_picture" class="pp_preview"></a>
                </div>
            </nav>
            </div>
        </header>
        <a href=""></a>
    <?php
        // affiche les résultats
        global $pdo;
        if(isset($_GET["confirmSearch"]) && !empty(trim($_GET["searchRequest"]))) {
            header("location:search_results.php?searchRequest=".trim($_GET["searchRequest"]));
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
        echo '<option value="alreadyAsked">Annuler la demande d\'ami</option>';
        // echo '<option value="alreadyAsked">Vous avez déjà envoyé une demande d\'ami</option>';
    }
    $alreadyAsked2 = $pdo->query("SELECT  COUNT(*) FROM friend_request WHERE id_user ='$target' AND id_friend='$userId_int' AND accept=FALSE")->fetchColumn();
    if ($alreadyAsked2 > 0) {
        echo '<option value="alreadyAsked2">On vous a déjà envoyé une demande d\'ami</option>';
    }
    if ($friend == 0) {
        if ( $alreadyAsked == 0  && $alreadyAsked2 == 0) {
            echo '<option value="befriend">Demander en ami</option>';
        }
        // $followed = 0;
        // foreach($listFollowed as $id_followed) {
        //     if ($target == $id_followed) {
        //         // Je suis déjà cette personne
        //         $followed += 1;
        //         echo '<option value="unfollow">Se désabonner</option>';
        //     }
        // }
        // if ($followed == 0) {
        //     echo '<option value="follow">S\'abonner</option>';
        // }
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
            case "alreadyAsked":
                $req = "DELETE FROM friend_request WHERE id_user=:id_user and id_friend=:id_target";
                $sth = $pdo->prepare($req);
                $sth->bindValue(':id_user', $userId_int, PDO::PARAM_INT);
                $sth->bindValue(':id_target', $target, PDO::PARAM_INT);
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
        echo $pictureName;
        $adressPicture = "./images/".$pictureName;
        echo $adressPicture;
        move_uploaded_file($_FILES["$file"]["tmp_name"], $adressPicture);
        // Renvoie le lien de l'adresse
        return $adressPicture;
    } 
}


function comments($idPost) {
    echo "<div class='container-all-comments'>";
        $uniqueNameForm = "content-comment-post".$idPost;
        ?>
        <div class="espace_commentaire">
            <p>Commentaire
        </div>
        <form class="form-add-comment" name="comment" method="post">
            <input type="text" name="<?php echo $uniqueNameForm; ?>">
            <input type="submit" value=">">
        </form>
        <?php
        // Ecrire un commentaire
        // wsh t'en es ou ?
        if(isset($_POST["$uniqueNameForm"])) {
            global $pdo, $username, $userId_int;
            $comment= addslashes($_POST["$uniqueNameForm"]);
            $pdo->exec("INSERT INTO comment(id_post, id_user, author_username, date, content) VALUES ('$idPost', '$userId_int', '$username', NOW(), '$comment')");
        }
        global $pdo, $username;
        $c = $pdo->query("SELECT * FROM comment WHERE id_post = '$idPost' ORDER BY date");
        // Montrer les commentaires
        $com=0;
        while($allComments = $c->fetch(PDO::FETCH_ASSOC)) {
            $com+=1;
            ?>
             
            <div class="container_comment_single">
                <p><?php if ($allComments['author_username']==$username) { echo "Moi";} else { echo $allComments['author_username'];}?></p>
                <p><?php echo $allComments['content'];?></p>
                <p><?php echo $allComments['date'];?></p>
            </div>
            <?php
        }
        ?>
        <div class="espace_commentaire">
            <p>nombres de commentaire : <?php echo($com)?>
        </div>
        <?php
    echo "</div>";
}

function createPost($createPostSQL) {
    global $pdo, $username, $userId_int;
        ?>
        <!-- Pour poster -->
        
        <!-- requête pour créer un post -->
        <?php 
        // var_dump($userId_int);
            if (isset($_POST["content-post"]) && trim($_POST["content-post"]) != "") {
                // Gestion d'erreur pour les ""
                $content= addslashes($_POST['content-post']);
                // Lien pour rajouter une photo si il y en a une à rajouter
                if (isset($_POST["upload_post_with_picture"]) && $_POST["upload_post_with_picture"] == "Poster !") {
                    $photo_link2="photo_link_post";
                    $_POST["photo_link_post"] = "aupif/url/photo.jpeg";
                    if (isset($_FILES["photo_link_post"])) {
                        $testImg = newUploadPicture($photo_link2);
                        $_POST["photo_link_post"] = $testImg;
                    } 
                    if (!isset($testImg)) {
                    //  Lien par défaut qui mène nulle part
                        $_POST["photo_link_post"] = "aupif/url/photo.jpeg";
                    }
                // Requete pour créer le poste
                $post= $pdo->prepare($createPostSQL);
                $post->bindValue(':id_user', $userId_int);
                $post->bindValue(':username', $username);
                $post->bindValue(':photo_link_post', $_POST["photo_link_post"]);
                $post->bindValue(':content', $content);
                $post->execute();
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
            <div class=intelPoster> 
                <a href="profile.php?profil=<?php echo $allPost['id_author'] ?>"><?php if ($allPost['author_username']==$username) { echo "Moi";} else { echo $allPost['author_username'];}?></a>
                <img class="profil-picture" src="<?php echo $profilePicture;?>" alt="image de profil">
            </div>       
                <?php if($allPost['photo_link'] != "aupif/url/photo.jpeg"){ echo "<img class='image-post' src=".$allPost['photo_link']." alt='image du post'>"; } ?>
            <div class="containerMsg">
                <p><?php echo $allPost['content'];?></p>
                <p class="datePost"><?php echo $allPost['date'];?></p>
            </div> 
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

function newUploadPicture($picture) {
    $message = ''; 

  if (isset($_FILES[$picture]) && $_FILES[$picture]['error'] === UPLOAD_ERR_OK)
  {
    // get details of the uploaded file
    $fileTmpPath = $_FILES[$picture]['tmp_name'];
    $fileName = $_FILES[$picture]['name'];
    $fileSize = $_FILES[$picture]['size'];
    $fileType = $_FILES[$picture]['type'];
    $fileNameCmps = explode(".", $fileName);
    $fileExtension = strtolower(end($fileNameCmps));
 
    // sanitize file-name
    $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
 
    // check if file has one of the following extensions
    $allowedfileExtensions = array('jpg', 'gif', 'png', 'zip', 'txt', 'xls', 'doc');
 
    if (in_array($fileExtension, $allowedfileExtensions))
    {
      // directory in which the uploaded file will be moved
      $uploadFileDir = './images/';
      $dest_path = $uploadFileDir . $newFileName;
 
      if(move_uploaded_file($fileTmpPath, $dest_path)) 
      {
        $message ='File is successfully uploaded.';
      }
      else
      {
        $message = 'There was some error moving the file to upload directory. Please make sure the upload directory is writable by web server.';
      }
    }
    else
    {
      $message = 'Upload failed. Allowed file types: ' . implode(',', $allowedfileExtensions);
    }
  }
  else
  {
    $message = 'There is some error in the file upload. Please check the following error.<br>';
    $message .= 'Error:' . $_FILES[$picture]['error'];
  }
  $_SESSION['message'] = $message;
  if (isset($dest_path)) {
    return $dest_path;
  }
}
?>