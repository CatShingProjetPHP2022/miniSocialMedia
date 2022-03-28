<!-- connexion à la bdd  -->
<?php include("functions.php");

session_start();
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
   function followBefriend($input, $target) {
        
        if ($input) {
            global $userId_int;
            global $pdo;
            switch($input) {
                case "follow":
                    $pdo->exec("INSERT INTO followed_list (id_user, id_followed) VALUES ('$userId_int', '$target')");
                    $pdo->exec("UPDATE user SET followed_list=CONCAT(followed_list,' $target')");
                    break
                case "befriend":
                    $pdo->exec("INSERT INTO friends_list (id_friend_1st, id_friend_2nd, accept  ) VALUES ('$userId_int', '$target', FALSE)");
                    break
                case "unfollow":
                    $pdo->exec("INSERT INTO followed_list (id_user, id_followed) VALUES ('$userId_int', '$target')");
                    $pdo->exec("UPDATE user SET followed_list=CONCAT(followed_list,' $target')");
                    break
                case "unfriend"
                    $pdo->exec("DELETE FROM friends_list WHERE (id_friend_1st ='$userId_int' AND id_friend_2nd ='$target) OR  (id_friend_1st ='$target AND id_friend_2nd ='$userId_int')");
            }
        }
    }
?>



