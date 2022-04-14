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
    <title>Messages</title>
    <link rel="stylesheet" href="css/reset.css">
    <link rel="stylesheet" href="css/message.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php getNav(); ?>
<section class ="messagerie">

    <div class = "champsFriends">
        <h2>Catshing</h2>
        
    <?php
    

    for ($i=0; $i < count($_SESSION['list_of_friends']); $i++) { 
        $friend =$_SESSION['list_of_friends'][$i];
        $tableFriendsUser = $pdo->query("SELECT username FROM user WHERE id_user = '$friend' ");
        $friendsUser = $tableFriendsUser -> fetch();
        ?>
        <ul>
            <?php
            echo "<li><a href=?FRIENDS=$friendsUser[0]>$friendsUser[0]</a></li>";
            ?>
        </ul>
        <?php
    }
    ?>
    </div>


    <div class = "conversation">
        <div class = "champMsgSender">
        <?php
            if ($_GET['FRIENDS']) {

                $friendsSelect = $pdo -> query("SELECT id_user FROM user WHERE username = '$_GET[FRIENDS]'");
                $idFriends = $friendsSelect -> fetch();
                $msgOfFriends = $pdo -> query("SELECT content FROM message WHERE id_recipient = $idFriends[0] AND id_sender ='$userId_int' ");
                while($msgFriends = $msgOfFriends -> fetch(PDO::FETCH_ASSOC)){
                    echo $msgFriends['content'];
                    echo '<br>';
                }
            }
            if ($_GET['FRIENDS']!="") {
                $isSeeing = $pdo -> exec("UPDATE message SET is_read = 'READ' WHERE id_sender = $idFriends[0]");
            }
        ?>
        </div>
        <div class = "champMsgReceive">
        <?php
            if ($_GET['FRIENDS']) {

                $msgOfSelf = $pdo -> query("SELECT content FROM message WHERE id_recipient = $userId_int AND id_sender = $idFriends[0]");
                while($msgSelf = $msgOfSelf -> fetch(PDO::FETCH_ASSOC)){
                    echo $msgSelf['content'];
                    echo '<br>';
                }
            }   
        ?>
        </div>
    </div>


    <div class = "champEnvoi">
        <form  method="post">
            <label for="message">Message</label>
            <input type="text" name="message"></input>
            <input type="submit" name="envoyer_message">
        </form>

    <?php
        $friendsLast = $_GET['FRIENDS'];
        if ($_POST['envoyer_message'] && $_POST['message']!="") {
            $_POST['message'] = addslashes($_POST['message']);
            $pdo -> exec("INSERT INTO message (id_sender,id_recipient,dateEmission,content,is_read) VALUES ('$userId_int','$idFriends[0]',NOW(),'$_POST[message]','NOREAD')");
            $_POST['message']="";
            header("location:messages.php?FRIENDS=$friendsLast");
            }
    ?>
    </div>
</section>    
</body>
</html>




