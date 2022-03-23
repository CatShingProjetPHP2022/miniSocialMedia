<?php include("functions.php");

session_start();
$_SESSION["user"] = 3;
$_SESSION["username"] = "Florent";
?>

<!DOCTYPE html> 
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<nav>
    <ul>
        <li>Accueil</li>
        <li>Profil</li>
        <li>Notifications</li>
        <li>Créer un post</li>
        <li>Se déconnecter</li>
    </ul>
</nav>
<br><br>
<div>
  <input type="checkbox" name="my-checkbox" id="check_create_post">
  <label for="check_create_post">+</label>
</div>
<!-- Pour poster -->
<form class="form-create-post" name="create-post" method="post">
    <input type="text" name="content">
    <input type="submit" value="Poster !">
</form>
<br><br>
<!-- REFRESH -->
<body>
    <section class="container-post">
        <!-- requête pour créer un post -->
        <?php 
            if($_POST) {
                if ($_POST["content"]) {
                    $_POST['content'] = addslashes($_POST['content']);
                    // Gestion d'erreur pour les ""
                    $pdo->exec("INSERT INTO post(id_author, author_username, date, photo_link, content) VALUES ('$_SESSION[user]', '$_SESSION[username]',
                    NOW(), 'aupif/url/photo.jpeg', '$_POST[content]')");
                    ?>
                        <div>
                                <p><?php echo $_SESSION['username'];?></p>
                                <p><?php echo date("Y")."-". date("F")."-". date("j")." ". date("G");?></p>
                                <p><?php echo $_POST['content'];?></p>
                        </div>
                    <?php

                }
            }
        ?>
        <!-- on affiche les posts -->
        <?php
            $r = $pdo->query("SELECT * FROM post WHERE id_author = '$_SESSION[user]' OR id_author = (SELECT id_followed FROM `followed_list` WHERE id_user = '$_SESSION[user]') OR id_author = (SELECT id_friend_1st FROM `friends_list` WHERE id_friend_2nd = '$_SESSION[user]') OR id_author = (SELECT id_friend_2nd FROM `friends_list` WHERE id_friend_1st = '$_SESSION[user]') ORDER BY date DESC LIMIT 0,10");
            // Tres longue requete qui va chercher les posts de l'utilisateur, des comptes qu'il suit plus ceux de ses amis pour afficher les 10 premiers et les trier du plus récent au plus ancient
            while($post = $r->fetch(PDO::FETCH_ASSOC)) {
                ?><div>
                        <p><?php echo $post['author_username'];?></p>
                        <p><?php echo $post['date'];?></p>
                        <p><?php echo $post['content'];?></p>
                </div><?php
            }
        ?>
    </section>
</body>
</html>

