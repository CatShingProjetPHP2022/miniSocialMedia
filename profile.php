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
    $profile = $u->fetchAll(PDO::FETCH_ASSOC|PDO::FETCH_UNIQUE);
    $name = $profile[$userProfileId]["username"];
    $pageTitle = "Profil de ".$name;
    $photo = $profile[$userProfileId]["photo_link"];
} else {
    $userProfileId = $userId_int;
    $name = $username;
    $pageTitle = "Mon profil";
    $photo = $_SESSION['user']['photo_link'];
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?></title>
    <link rel="stylesheet" href="css/reset.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/stylefinal.css">
</head>
<body>
    <?php getNav(); 
    // echo "<br>";
    // echo "Abonnements".var_dump($_SESSION['followed_list']);
    // echo "<br>";
    // echo "Amis".var_dump($_SESSION['list_of_friends']);
    // echo "<br>";
    ?>
    <section class="profile-wrapper">
    <div class="profile-container">
        <img src="<?php echo $photo; ?>" alt="">
        <p><?php echo $name; ?></p>
        <?php if (intval($userProfileId) != $userId_int) {
        ?><form action="" method="post">
            <select name="follow_friend_request" class="btn-follow-befriend">
                <?php followBefriend($_POST["follow_friend_request"], $userProfileId);?>
            </select>
            <input type="submit" value="Soumettre">
        </form> <?php } ?>
    <?php if ($userProfileId == $userId_int) { ?>
        <form method="POST" enctype="multipart/form-data">
            <input type="file" name="change_photo" class="btn-follow-befriend">
            <input type="submit" name=upload value="Changer votre photo" style="float:left">
        </form> 
    <?php }
        if (isset($_POST["upload"]) && $_POST["upload"]=="Changer votre photo") {
            $nameChgForm ="change_photo";
            $pictureAdress = newUploadPicture($nameChgForm);
            $changePicture = $pdo->exec("UPDATE user SET photo_link = '$pictureAdress' WHERE id_user='$userId_int' ");
        }
    ?>
    </div>
    </section>
    <!-- <div>
        <?php
            // echo "test";
            // echo var_dump($_FILES["change_photo"]);
            // echo var_dump($pictureAdress);
            // echo $_SESSION['message'];
        ?>
    </div> -->
    <div class="main">
        <section class="container-allPost">
            <?php
                if ($userProfileId == $userId_int) {
                    createPost("INSERT INTO post(id_author, author_username, date, photo_link, content) VALUES (:id_user, :username, NOW(), :photo_link_post, :content)");
                }
                showPosts("SELECT * FROM post WHERE id_author = '$userProfileId' ORDER BY date DESC LIMIT 0,10");
            ?>
        </section>        
    </div>

</body>
</html>