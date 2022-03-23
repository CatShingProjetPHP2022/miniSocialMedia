<?php 

//je me connecte à la base de données 
$var = new PDO('mysql:host=localhost;dbname=projet-php','root','');

//verifier qu'on est connectés
var_dump($var);

//j'ouvre une session pr stocker par la suite les informations:
    session_start();

//je declare une variable qui me permettra d'afficher des messages pour l'utilisateur:
$content ='';

?>