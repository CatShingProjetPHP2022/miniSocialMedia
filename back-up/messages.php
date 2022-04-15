<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages</title>
    <link rel="stylesheet" href="css/Message.css">
</head>
<body>
<section class ="messagerie">
    <?php
    session_start(); //lancement session commune 
    $_SESSION['selectCount']=1; //recupere le id user de l'utilisateur
    $var2 = new PDO('mysql:host=localhost;dbname=projet_php','root','',array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING,PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8')); // connection a la base
    $intelUser = $var2 -> query("SELECT * FROM user WHERE id_user = '$_SESSION[selectCount]'" ); //on recupere l'ensemble des données utilisateur
    $orderIntelUser = $intelUser -> fetch();
    $listFriendsUser=[];
    $listFriendsUser = explode(" ",$orderIntelUser['list_of_friends']);
    ?>

    <div class = "champsFriends">
        <h2>Catshing</h2>
        
    <?php
    

    for ($i=0; $i < count($listFriendsUser); $i++) { 
        $tableFriendsUser = $var2 -> query("SELECT username FROM user WHERE id_user = '$listFriendsUser[$i]'");
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

                $friendsSelect = $var2 -> query("SELECT id_user FROM user WHERE username = '$_GET[FRIENDS]'");
                $idFriends = $friendsSelect -> fetch();
                $msgOfFriends = $var2 -> query("SELECT content FROM message WHERE id_recipient = $idFriends[0]");
                while($msgFriends = $msgOfFriends -> fetch(PDO::FETCH_ASSOC)){
                    echo $msgFriends['content'];
                    echo '<br>';
                }
            }
        ?>
        </div>
        <div class = "champMsgReceive">
        <?php
            if ($_GET['FRIENDS']) {

                $msgOfSelf = $var2 -> query("SELECT content FROM message WHERE id_recipient = $_SESSION[selectCount]");
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
        if ($_POST['envoyer_message']) {
            $_POST['message'] = addslashes($_POST['message']);
            
            if ($_POST['message']!="") {
                $var2 -> exec("INSERT INTO message (id_sender,id_recipient,dateEmission,content) VALUES ('$_SESSION[selectCount]','$idFriends[0]',NOW(),'$_POST[message]')");
                //  $var1 -> exec("INSERT INTO 'message' (id_sender,id_recepient,content) VALUES ('$_SESSION[selectCount]','$idFriends[0]','$_POST[message]')");
                echo "$_SESSION[selectCount] ,$idFriends[0] ,$_POST[message]";
            }
            
        $_POST['message']="";
        }

    ?>
    </div>
</section>    
</body>
</html>
