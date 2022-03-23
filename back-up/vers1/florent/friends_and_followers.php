<!-- connexion à la bdd  -->
<?php include("functions.php");

session_start();
$_SESSION["user"] = 3;
?>

<!-- test bouton -->
<form method="post">
    <select name="request" id="" class="btn-follow-befriend">
        <option value="follow">S'abonner</option>
        <option value="befriend">Demander en ami</option>
    </select>
    <input type="submit" value="Soumettre">
</form>

<!-- il faudra sûrement le mettre dans une fonctions rangé dans functions.php qu'on va call -->
<?php
    if ($_POST) {
        if ($_POST["request"]=="follow") {
            $pdo->exec("INSERT INTO followed_list (id_user, id_followed) VALUES ('$_SESSION[user]', 2)");
            // pour s'abonner il faut aller sur le compte de la personne auquel on veut s'abonner, il y a donc une requete pour
            // pour cet utilisateur sur lequel on a clique, on recup les infos de cette requette à la place du 2
        } else if ($_POST["request"]=="befriend") {
            $pdo->exec("INSERT INTO friends_list (id_friend_1st, id_friend_2nd, accept  ) VALUES ('$_SESSION[user]', 2, FALSE)");
        }
    }
?>