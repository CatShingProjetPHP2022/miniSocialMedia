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
    <title>Messagerie</title>
    <link rel="stylesheet" href="css/reset.css">
    
    <link rel="stylesheet" href="css/stylefinal.css">
</head>
<body>
<?php getNav(); ?>

<!-- la Voila ta barre de recherche d'amis mrc mgl -->


<section class ="messagerie">
    
    <div class="containerMessagerie">
        
            
        
           
            <!-- <form class ="search-bar" name="search" method="get">
                <input type="text" name="searchFriends" value="<?php // if (isset($_GET["searchFriends"])) {echo $_GET["searchFriends"];} ?>" placeholder="Rechercher des amis">
                <input type="submit" name="confirmSearchFriends" value="GO">
            </form> -->
            <?php 
            // if(isset($_GET["confirmSearchFriends"]) && !empty(trim($_GET["searchFriends"]))) {
            //         $input2 = strtolower(trim($_GET["searchFriends"]));
            //         $search2 = $pdo->query("SELECT * FROM user WHERE username LIKE LOWER('%$input2%') AND id_user IN (SELECT id_user FROM friend_request WHERE id_friend='$userId_int' AND accept = 1) OR id_user IN (SELECT id_friend FROM friend_request WHERE id_user='$userId_int' AND accept=1)");
            //         while($allResFriends = $search2->fetch(PDO::FETCH_ASSOC)) {
            //             echo "<div class='search-result'>";
            //             echo "<img src=".$allResFriends["photo_link"]." alt=''>";
            //             echo "<p>".$allResFriends["username"]."</p>";
            //             // modifier le href qui dirige vers la conversation
            //             echo "<a href='?FRIENDS=".$allResFriends["id_user"]."'>Parler à cet ami</a>";
            //             echo "</div>";
            //         }
            // }
            // ?>
        <div class = "champsFriendslist">
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
            // echo var_dump($_GET['FRIENDS']);
                if ($_GET['FRIENDS']) {

                    $friendsSelect = $pdo -> query("SELECT id_user FROM user WHERE username = '$_GET[FRIENDS]' ");
                    $idFriends = $friendsSelect -> fetch();
                    $msgOfFriends = $pdo -> query("SELECT content FROM message WHERE id_recipient '$idFriends[0]'=  AND id_sender ='$userId_int' ");
                    while($msgFriends = $msgOfFriends -> fetch(PDO::FETCH_ASSOC)){
                        echo '<p>'.$msgFriends['content'].'</p>';
                        echo '<br>';
                    }
                }
                if ($_GET['FRIENDS']!="") {
                    $isSeeing = $pdo -> exec("UPDATE message SET is_read = 'READ' WHERE id_sender = '$idFriends[0]' ");
                }
                
            ?>
            </div>
            <div class = "champMsgReceive">
            <?php
                if ($_GET['FRIENDS']) {

                    $msgOfSelf = $pdo -> query("SELECT content FROM message WHERE id_recipient = '$userId_int' AND id_sender = '$idFriends[0]' ");
                    while($msgSelf = $msgOfSelf -> fetch(PDO::FETCH_ASSOC)){
                        echo '<p>'.$msgSelf['content'].'</p>';
                        echo '<br>';
                    }
                }   
            ?>
            </div>
        </div>
    </div>

    <div class = "champEnvoi">
        <form  method="post" class="form-add-message">
            
            <input type="text" name="message" placeholder="        Envoyer un message"></input>
            <input type="submit" name="envoyer_message" value=">">
        </form>

    <?php
        $friendsLast = $_GET['FRIENDS'];
        if ($_POST['envoyer_message'] && $_POST['message']!="") {
            $_POST['message'] = addslashes($_POST['message']);
            $pdo -> exec("INSERT INTO message (id_sender,id_recipient,dateEmission,content,is_read) VALUES ('$userId_int','$idFriends[0]',NOW(),'$_POST[message]','NOREAD')");
            $_POST['message']="";
            // header("location:messages.php?FRIENDS='$friendsLast' ");
            }
    ?>
    </div>
</section>    
</body>
</html>