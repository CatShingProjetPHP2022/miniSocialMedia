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
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/reset.css">
</head>
<body>
    <?php getNav();
    echo "<br>";
    echo "Abonnements".var_dump($_SESSION['followed_list']);
    echo "<br>";
    echo "Amis".var_dump($_SESSION['list_of_friends']);
    echo "<br>";
    // echo var_dump($updateSession);
    ?>
    <div class="main">
        <div class="container-notifications">
                <?php 
                    $n = $pdo->query("SELECT * FROM friend_request WHERE accept = 0 AND id_friend='$userId_int' ORDER BY date DESC LIMIT 0,10 ");
                    while($allNotif = $n->fetch(PDO::FETCH_ASSOC)) {
                        $nameReq = $pdo->query("SELECT username FROM user WHERE id_user = '$allNotif[id_user]' ")->fetchColumn();
                        echo '<div class="single-notification">';
                            echo "<p>"; echo $nameReq."</p>";
                            echo "<p>"; echo $allNotif['date']."</p>";
                            $idNotif = "befriend".$allNotif['id_friend_request'];
                            echo "<form method=post>";
                                echo '<input type="submit" name="'; echo $idNotif.'" value="Accepter la demande d\'ami">';
                                if ($_POST) {
                                    if ($_POST["$idNotif"]) {
                                        friendshipAccept($allNotif['id_user'], $_POST["$idNotif"]);
                                    }
                                }
                            echo "</form>";
                        echo "</div>";
                    } 
                ?>
        </div>
        <br><br>
        <section class="container-allPost">
            <?php
            createPost("INSERT INTO post(id_author, author_username, date, photo_link, content) VALUES ('$userId_int', '$username',NOW(), :photo_link_post, :content)");
            // Tres longue requete qui va chercher les posts de l'utilisateur, des comptes qu'il suit plus ceux de ses amis pour afficher les 10 premiers et les trier du plus récent au plus ancient
            showPosts("SELECT * FROM post WHERE id_author = '$userId_int' OR id_author IN (SELECT id_followed FROM followed_list WHERE id_user = '$userId_int') OR id_author IN (SELECT id_user FROM friend_request WHERE id_friend = '$userId_int' AND accept=1) OR id_author IN (SELECT id_friend FROM friend_request WHERE id_user = '$userId_int' AND accept=1) ORDER BY date DESC LIMIT 0,30");
            ?>
        </section>        
    </div>

</body>
</html>