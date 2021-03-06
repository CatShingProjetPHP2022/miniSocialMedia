<?php
include('init.php');

//si le formulaire a été posté, alors là on balance la fonctionnalité :
if ($_POST) {
    //je vérifie si je récupère bien les valeurs des champs:
    //print_r($_POST);

    //je définie une variable pour afficher les erreurs:
    if ($_POST) {
        // Je vérifie si je récupère bien les valeurs des champs
        // print_r($_POST);
        // Je défini une variable pour afficher les erreurs
        $erreur = '';

        // Si le prénom n'est pas trop court ou n'est pas trop long:
        if (strlen($_POST['username']) < 3 || strlen($_POST['username']) > 20) {
            $erreur = 'Taille d\'username incorrect';
        }
        //si les caractères utilisés dns le champs sont valides:

    }
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
    $r = $var->query("SELECT * FROM user WHERE email='$_POST[email]'");

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

    // Si serveur est vide (empty()) :
    if (empty($erreur)) {
        $var->exec("INSERT INTO user (username, password,photo_link, email) VALUES ('$_POST[username]', '$_POST[password]', '$_POST[photo_link]', '$_POST[email]')");
        $content .= '<p>Inscription validée</p>';
    }// AJOUTER  DANS LA LIGNE 53 photo_link
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
    <link rel="stylesheet" href="style_signup.css">
    <title>Inscription</title>
</head>

<body>

    <section class="signup_form_wrapper">
        <form method="post" class="signup_form">

            <div class="username">
                <label for="username" placeholder="@example"><b>Nom d'utilisateur</b></label>
                <input type="text" name="username" required>
            </div>


            <div class="password">
                <label for="password" placeholder="Mot de passe"><b>Mot de passe</b></label>
                <input type="password" name="password" required>
            </div>

            <div class="profile_picture">
                <label for="photo_link"><b>Photo de profil</b></label>
                <p>Selectionnez une photo</p>
                <input type="file" name="photo_link" required>
            </div>

            <div class="email">
                <label for="email" placeholder="example@email.com"><b>Email</b></label>
                <input type="email" name="email" required>
            </div>

            <div class="conditions">
                <input type="checkbox" checked required  value="ok" name="conditions" id="signup_checkbox">
                <label for="conditions">J'accepte les conditions générales d'utilisation</label>
            </div>

            <button type="submit">S'INSCRIRE</button>

            <hr>
            <div class="already_in_container">
                <span class="already_in">Déja inscrit ?</span>
                <div class="button_container">
                    <button type="submit"><a href="connection.php" style="text-decoration:none; color:black">SE CONNECTER</a></button>
                </div>
            </div>

    </section>
    </form>
</body>

</html>