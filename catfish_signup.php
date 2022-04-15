<?php
include('functions.php');

//si le formulaire a été posté, alors là on balance la fonctionnalité :
if ($_POST) {
    //je vérifie si je récupère bien les valeurs des champs:
    //print_r($_POST);

    //je définie une variable pour afficher les erreurs:
        // Je vérifie si je récupère bien les valeurs des champs
        // print_r($_POST);
        // Je défini une variable pour afficher les erreurs
        $erreur = '';
        
        // Si le prénom n'est pas trop court ou n'est pas trop long:
        if (strlen($_POST['username']) < 3 || strlen($_POST['username']) > 20) {
            $erreur = 'Taille d\'username incorrect';
        }
        //si les caractères utilisés dns le champs sont valides:
    // Si le prénom n'est pas trop court ou n'est pas trop long:
    if (strlen($_POST['username']) < 3 || strlen($_POST['username']) > 20) {
        $erreur = '<p>Taille d\'username invalide</p>';
    }

    // Si les caractères utilisés dasn le champs prénom sont valides:
    if (!preg_match('#^[a-zA-Z0-9.-_]+$#', $_POST['username'])) {
        $erreur .= '<p>Format d\'username invalide </p>';
        //"".=" Utile pour ajouter les nouvelles erreurs dans la variable erreur mélange de concaténation et affectation de valeur
    };
    // on commence par # et ^pour le formatage puis dasn crochet on met caratères qu'on doit utiliser - souvent utilisé pour les adresses mail
    // Permet de verifier si carcatères matchent avec caractère entré par linternaute

    //je verifie si l'email n'est pas deja présent dans la base de données
    $r = $pdo->query("SELECT * FROM user WHERE email='$_POST[email]'");

    //si y'a un ou plusieurs resultats 
    if ($r->rowCount() >= 1) {
        $erreur .= '<p>Email déjà utilisé</p>';
    }
    //je gère les pb d'apostrophes pr chq champs grace a une boucle
    foreach ($_POST as $indice => $valeur) {
        $_POST[$indice] = addslashes($valeur);
    }

    //je hash le mot de passe :
    $_POST['password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);

    //J'upload la photo, si et seulement si il n'y a pas d'autre erreur pour éviter d'uploader 2 fois la photo alors qu'on a foiré l'inscription
    if (empty($erreur)) {
        $photo_link="photo_link";
        $_POST["photo_link"] = uploadPicture($photo_link);
        if (!file_exists($_POST["photo_link"])) {
            $erreur .= '<p>'.$_SESSION['message'].'</p>';
        }        
    }

    // Si serveur est vide (empty()) :
    if (empty($erreur)) {
        $pdo->exec("INSERT INTO user (username, password, photo_link, email, list_of_friends, followed_list) VALUES ('$_POST[username]', '$_POST[password]', '$_POST[photo_link]', '$_POST[email]', '', '')");
        $content .= '<p>Inscription validée</p>';
    }
    //j'ajoute le contenu de $erreur à l'interieur de $content
    $content .= $erreur;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/stylefinal.css">
    <link rel="stylesheet" href="css/reset.css">
    <title>Inscription</title>
</head>

<body class="wrapper">
  
<div class="signup_wrapper" id="container">
	
	<div class="form-container sign-up-container">
		<form method="post" class="signup_form" enctype="multipart/form-data">
			<h1 class="connection">Inscription</h1>
            <input type="text" name="username" placeholder="Identifiant" class="open_folder user_info_fields"/>
            <input type="email" name="email" placeholder="Email" class="input user_info_fields"/>
			<input type="password" name="password" placeholder="Mot de passe" class="input user_info_fields"/>
            <label for="photo_link" style="margin-block:15px; background-color: #ff806a;">
               Choisir une photo
            <input type="file" id="photo_link" name="photo_link" accept="image/png, image/jpg, image/jpeg" class="upload_photo"/>
           </label>
			<!-- <button>S'inscrire</button> -->
            
            <!-- <a href="signup_catfish.php" class="already_in">Déjà Inscrit ?</a> -->
            <button type="submit">S'INSCRIRE</button>
            <!-- <button style="margin-bottom:15px;">Connexion</button> -->
            <button><a href="connection.php" style="text-decoration:none; color:white; font-weight: 600">SE CONNECTER</a></button>
            <div class="social-container">
				<a href="#" class="social" class="link">
                    <img src="images/assets/icons8-twitter.svg" alt="twitter">
                </a>
				<a href="#" class="social"class="link">
                <img src="images/assets/icons8-google.svg" alt="google">
                </a>
				<a href="#" class="social" class="link">
                <img src="images/assets/icons8-facebook.svg" alt="fb">
                </a>
			</div>
            <?php echo $content ?>
		</form>
	</div>
	<div class="img-container_signup">
        <img src="images/assets/AdobeStock_338147710.jpeg" alt="cat_image">
	</div>
</div>

</body>

</html>