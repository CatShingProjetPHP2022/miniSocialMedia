<?php
include('functions.php');

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
    <link rel="stylesheet" href="css/style_connection.css">
    <title>Connexion</title>
</head>

<body>
<section>
    <form method="post" class="sign_in_form">

            <div class="username">
                <label for="email" placeholder="@example"><b>Votre email</b></label>
                <input type="text" name="email" required>
            </div>


            <div class="password">
                <label for="password" placeholder="Mot de passe"><b>Mot de passe</b></label>
                <input type="password" name="password" required>
            </div>
            <div>
                <button type="submit">SE CONNECTER</button>
            </div>
            <?php echo $content ?>
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
                    <button type="submit"><a href="catfish_signup.php" style="text-decoration:none; color:black">S'INSCRIRE</a></button>
                </div>
            </div>
    </form>
</body>

</html>