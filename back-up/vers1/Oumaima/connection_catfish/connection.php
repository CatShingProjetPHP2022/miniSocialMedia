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

    // Si les caractères utilisés dasn le champs username sont valides:
    if (!preg_match('#^[a-zA-Z0-9.-_]+$#', $_POST['username'])) {
        $erreur .= '<p>Format d\'username invalide </p>';
        //"".=" Utile pour ajouter les nouvelles erreurs dans la variable erreur mélange de concaténation et affectation de valeur
    };
    // on commence par # et ^pour le formatage puis dasn crochet on met caratères qu'on doit utiliser - souvent utilisé pour les adresses mail
    // Permet de verifier si carcatères matchent avec caractère entré par linternaute

    //je gère les pb d'apostrophes pr chq champs grace a une boucle
    foreach ($_POST as $indice => $valeur) {
        $_POST[$indice] = addslashes($valeur);
    }

    //je hash le mot de passe :
    $_POST['password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Si serveur est vide (empty()) :
    if (empty($erreur)) {
        $var->exec("INSERT INTO user (username, password,photo_link, email) VALUES ('$_POST[username]', '$_POST[password]', '$_POST[photo_link]', '$_POST[email]')");
        $content .= '<p>Connexion validée</p>';
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
    <link rel="stylesheet" href="style_connection.css">
    <title>Connexion</title>
</head>

<body>
<section>
    <form method="post" class="sign_in_form">

            <div class="username">
                <label for="username" placeholder="@example"><b>Nom d'utilisateur</b></label>
                <input type="text" name="username" required>
            </div>


            <div class="password">
                <label for="password" placeholder="Mot de passe"><b>Mot de passe</b></label>
                <input type="password" name="password" required>
            </div>
            <div>
                <button type="submit">SE CONNECTER</button>
            </div>

        </section>

        <section>
            <div class="">
                <p>Ou connectez vous avec</p>
                <img src="" alt="">
                <img src="" alt="">
                <img src="" alt="">
            </div>
        </section>
        <div class="sign_me_in">
                <span class="not_already_in">Pas encore inscrit ?</span>
                <div class="button_container">
                    <button type="submit"><a href="catfish_signup.php" style="text-decoration:none; color:black">SE CONNECTER</a></button>
                </div>
            </div>
    </form>
</body>

</html>