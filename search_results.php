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
    <title>Résultats de recherche</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php getNav(); ?>
    <div>
        <?php
        // affiche les résultats
        global $pdo;
        if(!empty(trim($_GET["searchRequest"]))) {
            $input = strtolower(trim($_GET["searchRequest"]));
            $search = $pdo->query("SELECT * FROM user WHERE username LIKE LOWER('%$input%') ");;
            while($allResults = $search->fetch(PDO::FETCH_ASSOC)) {
                echo "<div class='search-result'>";
                    echo "<img src=".$allResults["photo_link"]." alt=''>";
                    echo "<p>".$allResults["username"]."</p>";
                    if ($allResults["id_user"]== $userId_int) {
                        echo "<a href='profile.php'>Accéder à mon profil</a>"
                    } else {
                        echo "<a href='profile.php?profil=".$allResults["id_user"]."'>Accéder au profil</a>";
                    }
                echo "</div>";
            }
        }
        ?>
    </div>
</body>
</html>