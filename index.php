<?php include("functions.php");

// Si l'utilisateur n'est pas connecté on lui demande de se connecter
if(!isset($_SESSION['user'])) {
	header('location:connection.php');
}

// Pour une raison que je ne connais pas la requete sql ne lis pas les tableaux associatifs, j'ai donc mis les données du session qui m'intéressait dans des variables
$userId_int = intval($_SESSION['user']['id_user']);
$username = $_SESSION['user']['username'];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil</title>
    <link rel="stylesheet" href="css/reset.css">
    <!-- <link rel="stylesheet" href="css/style.css"> -->
    <link rel="stylesheet" href="css/stylefinal.css">
</head>
<body>
    <?php getNav();
    // echo "<br>";
    // echo "Abonnements".var_dump($_SESSION['followed_list']);
    // echo "<br>";
    // echo "Amis".var_dump($_SESSION['list_of_friends']);
    // echo "<br>";
    // echo var_dump($updateSession);
    ?>
     <div class="add_new_post" id="add_new_post">
            <p>NEW POST</p>
    </div>
    <form class="form-create-post postOff" name="create-post" method="post" enctype="multipart/form-data" id="form_post">
            <div class="containerCreatePost" id="createPost">
                <input placeholder="tappez votre post "type="text" name="content-post" required >
                <input type="file" name="photo_link_post">
                <!-- On peut poster sans mettre d'image, c'est optionnel -->
                <input type="submit" name="upload_post_with_picture" value="Poster !" id="app-posting">
            </div>
        </form>
    <script>
        let newPost = document.getElementById("add_new_post");
        let postInterface = document.getElementById("form_post");
        let formPost = document.getElementById("add-posting");
        newPost.addEventListener("click",()=>{
            postInterface.classList.remove("postOff");
        })
        app-posting.addEventListener("click",()=>{
            
            postInterface.classList.add("postOff");
        })
    

    </script>
    <div class="main">
        <div class="container-friends">
                <?php 
                    $n = $pdo->query("SELECT * FROM friend_request WHERE accept = 0 AND id_friend='$userId_int' ORDER BY date DESC LIMIT 0,10 ");
                    while($allNotif = $n->fetch(PDO::FETCH_ASSOC)) {
                        $nameReq = $pdo->query("SELECT username, photo_link FROM user WHERE id_user = '$allNotif[id_user]' ")->fetchAll(PDO::FETCH_ASSOC);
                        echo '<div class="single-friends">';
                            echo "<a href='profile.php?profil=".$allNotif["id_user"]."'>".$nameReq[0]["username"]."</a>";
                            echo "<img class='profil-picture' src='";echo $nameReq[0]["photo_link"]."' alt='photo de profil'>";
                            // echo "<p>"; echo "Envoyé le ".$allNotif['date']."</p>";
                            $idNotif = "befriend".$allNotif['id_friend_request'];
                            echo "<form method=post>";
                                echo '<input type="submit" name="'; echo $idNotif.'" value="Accepter la demande d\'ami">';
                                if ($_POST) {
                                    if ($_POST["$idNotif"]) {
                                        friendshipAccept($allNotif['id_user'], $_POST["$idNotif"]);
                                    }
                                }
                            echo "</form>";
                            echo "<p>"; echo "Envoyé le ".$allNotif['date']."</p>";
                        echo "</div>";
                    }
                    $f = $pdo->query("SELECT * FROM user where id_user IN (SELECT id_user FROM friend_request WHERE id_friend = '$userId_int' AND accept = 1) OR id_user IN (SELECT id_friend FROM friend_request WHERE id_user='$userId_int' AND accept = 1)");
                    while($allFriends = $f->fetch(PDO::FETCH_ASSOC)) {
                        echo '<div class="single-friends">';
                            echo "<a href='profile.php?profil=".$allFriends["id_user"]."'>".$allFriends["username"]."</a>";
                            echo "<img class='profil-picture' src='";echo $allFriends["photo_link"]."' alt='photo de profil'>";
                        echo "</div>";
                    }
                ?>
        </div>
        <br><br>
        <section class="container-allPost">
            <?php
            createPost("INSERT INTO post(id_author, author_username, date, photo_link, content) VALUES (:id_user, :username, NOW(), :photo_link_post, :content)");
            // Tres longue requete qui va chercher les posts de l'utilisateur, des comptes qu'il suit plus ceux de ses amis pour afficher les 10 premiers et les trier du plus récent au plus ancient
            showPosts("SELECT * FROM post WHERE id_author = '$userId_int' OR id_author IN (SELECT id_followed FROM followed_list WHERE id_user = '$userId_int') OR id_author IN (SELECT id_user FROM friend_request WHERE id_friend = '$userId_int' AND accept=1) OR id_author IN (SELECT id_friend FROM friend_request WHERE id_user = '$userId_int' AND accept=1) ORDER BY date DESC LIMIT 0,30");
            ?>
        </section>        
    </div>
</body>
</html>