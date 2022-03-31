<?php include("functions.php");


// Si l'utilisateur n'est pas connecté on lui demande de se connecter
if(!isset($_SESSION['user'])) {
	header('location:connection.php');
}

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
<body>
    <nav>
        <ul>
            <li>Accueil</li>
            <li>Profil</li>
            <li>Notifications</li>
            <li>Créer un post</li>
            <?php if(isset($_SESSION["user"])) { ?>
            <li><a href="?action=déconnexion">Se déconnecter</a></li>
            <?php } ?>
        </ul>
    </nav>
    <div>
        <input type="checkbox" name="my-checkbox" id="check_create_post">
        <label for="check_create_post">+</label>
    </div>
    <!-- Pour poster -->
    <form class="form-create-post" name="create-post" method="post">
        <input type="text" name="content">
        <input type="submit" value="Poster !">
    </form>
    <!-- requête pour créer un post -->
    <?php 
    // Pour une raison que je ne connais pas la requete sql ne lis pas les tableaux dans des tableaux, j'ai donc mis les données du session[user][] qui m'intéressait dans des variables
    $userId_int = intval($_SESSION['user']['id_user']);
    $username = $_SESSION['user']['username'];
    // var_dump($userId_int);
        if($_POST) {
            if ($_POST["content"]) {
                    // Gestion d'erreur pour les ""
                $_POST['content'] = addslashes($_POST['content']);
                // Requete pour créer le poste
                // $pdo->exec("INSERT INTO post(id_author, author_username, date, photo_link, content) VALUES ('$_SESSION[user][id_user]', '$_SESSION[user][username]',NOW(), 'aupif/url/photo.jpeg', '$_POST[content]')");
                $pdo->exec("INSERT INTO post(id_author, author_username, date, photo_link, content) VALUES ('$userId_int', '$username',NOW(), 'aupif/url/photo.jpeg', '$_POST[content]')");
            }
        }
    ?>
    <br><br>
    <section class="container-post">
        <!-- requête pour créer un post -->

        <!-- on affiche les posts -->
        <?php
            // Tres longue requete qui va chercher les posts de l'utilisateur, des comptes qu'il suit plus ceux de ses amis pour afficher les 10 premiers et les trier du plus récent au plus ancient
            // SELECT * FROM post WHERE id_author = 1 OR id_author IN (SELECT id_followed FROM `followed_list` WHERE id_user = 1) OR id_author IN (SELECT id_user FROM `friend_request` WHERE id_friend = 1) OR id_author IN (SELECT id_friend FROM `friend_request` WHERE id_user = 1) ORDER BY date DESC LIMIT 0,10
            $r2 = $pdo->query("SELECT * FROM post WHERE id_author = '$userId_int' OR id_author IN (SELECT id_followed FROM `followed_list` WHERE id_user = '$userId_int') OR id_author IN (SELECT id_user FROM `friend_request` WHERE id_friend = '$userId_int' AND accept=1) OR id_author IN (SELECT id_friend FROM `friend_request` WHERE id_user = '$userId_int' AND accept=1) ORDER BY date DESC LIMIT 0,10");
            while($allPost = $r2->fetch(PDO::FETCH_ASSOC)) {
                ?><div class="container_post">
                        <p><?php echo $allPost['author_username'];?></p>
                        <p><?php echo $allPost['date'];?></p>
                        <p><?php echo $allPost['content'];?></p>
                        $allPost["post_id"]
                        <!-- Rajout du bouton follow/befriend si l'auteur du poste n'est pas l'utilisateur -->
                        <?php if ($allPost['author_username']!= $_SESSION['user']['username']) { 
                            followBefriend2($_POST["follow_friend_request"], $allPost['id_author']);
                        } ?> 
                </div><?php
            } ?>
    </section>

</body>
</html>

