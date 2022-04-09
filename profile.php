<?php include("functions.php");


// Si l'utilisateur n'est pas connecté on lui demande de se connecter
if(!isset($_SESSION['user'])) {
	header('location:connection.php');
}

// Pour une raison que je ne connais pas la requete sql ne lis pas les tableaux associatifs, j'ai donc mis les données du session qui m'intéressait dans des variables
$userId_int = intval($_SESSION['user']['id_user']);
$username = $_SESSION['user']['username'];

if(isset($_GET['profil']) && is_int(intval($_GET["profil"]))) {
    $userProfileId = intval($_GET["profil"]);
    $u = $pdo->query("SELECT * FROM user WHERE id_user = '$userProfileId' ");
    $profile = $u->fetch();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/reset.css">
</head>
<body>
    <?php getNav(); ?>
    <div class="profile-container">
        <img src="<?php echo $profile["photo_link"]; ?>" alt="">
        <p><?php echo $profile["username"]; ?></p>
        <form action="" method="post">
            <select name="follow_friend_request" class="btn-follow-befriend">
                <?php followBefriend($_POST["follow_friend_request"], $userProfileId); ?>
            </select>
            <input type="submit" value="Soumettre">
        </form>
    </div>
    <div class="main">
        <br><br>
        <section class="container-allPost">
            <?php
            // Tres longue requete qui va chercher les posts de l'utilisateur, des comptes qu'il suit plus ceux de ses amis pour afficher les 10 premiers et les trier du plus récent au plus ancient
            showPosts("SELECT * FROM post WHERE id_author = '$userProfileId' ORDER BY date DESC LIMIT 0,10");
            ?>
        </section>        
    </div>

</body>
</html>