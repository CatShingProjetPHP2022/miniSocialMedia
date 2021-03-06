<?php
include('functions.php');

if (isset($_SESSION["user"])) {
    header('location:index.php');
}

// Si le form est posté :
    if($_POST) {
        // Je vérifie si je récupère bien les infos :
        // var_dump($_POST);
    
        // Je récupère les infos correspondants à l'email dans la table :
        $r = $pdo->query("SELECT * FROM user WHERE email = '$_POST[email]'");
    
        // Si le nombre de résultat est plus grand ou égal à 1, alors le compte existe :
        if($r->rowCount() >= 1) {
            // Je stock toutes les infos sous forme d'array :
            $user= $r->fetch(PDO::FETCH_ASSOC);
            // Si le mot de passe posté correspond à celui présent dans $user:
            if(password_verify($_POST['password'], $user['password'])) {
                // Je test si le mdp fonctionne :
                $content .= '<p>email + MDP : OK</p>';
                // J'enregistre les infos dans la session :
                $_SESSION['user']['id_user'] = $user['id_user'];
                $_SESSION['user']['username'] = $user['username'];
                $_SESSION['user']['photo_link'] = $user['photo_link'];
                $_SESSION['user']['email'] = $user['email'];
                $_SESSION['list_of_friends'] = explode(" ",$user['list_of_friends']);
                $_SESSION['followed_list'] = explode(" ",$user['followed_list']);
                 // Pour une raison que je ne connais pas la requete sql ne lis pas les tableaux associatifs, j'ai donc mis les données du session qui m'intéressait dans des variables
                $userId_int = intval($_SESSION['user']['id_user']);
                $username = $_SESSION['user']['username'];
                // Je redirige l'utilisateur vers la page d'accueil :
                header('location:index.php');
            } else {
                // Le mot de passe est incorrect :
                $content .= '<p>Mot de passe incorrect.</p>';
            }
        } else {
            $content .= '<p>Compte inexistant</p>';
        }
    }
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/reset.css">
    
    <link rel="stylesheet" href="css/stylefinal.css">
    <title>Connexion</title>
</head>

<body class="wrapper">
<div class="container" id="container">
	
	<div class="form-container sign-in-container">
		<form method="post" class="connection_form">
			<h1 class="connection">Connexion</h1>
			<div class="social-container">
            <a href="#" class="social" class="link">
                    <img src="images/assets/icons8-twitter.svg" alt="twitter">
                </a>
				<a href="signin_google.php" class="social" class="link">
                <img src="images/assets/icons8-google.svg" alt="google">
                </a>
				<a href="#" class="social" class="link">
                <img src="images/assets/icons8-facebook.svg" alt="fb">
                </a>
			</div>
			<input type="email" name="email" placeholder="Email" />
			<input type="password" name="password" placeholder="Mot de passe" />
			<button type="submit" class="connection">Connexion</button>
		
            <span class="not_already_in">Pas encore Inscrit ?</span>
            <button style="margin-bottom:15px;" class="signup_button"><a href="catfish_signup.php" style="text-decoration:none;color:white">Inscrivez-vous</a></button>
        </form>
	</div>
	<div class="img-container">
        <img src="images/assets/AdobeStock_401755189.jpeg" alt="">
	</div>
</div>
    
</body>

</html>