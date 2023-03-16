<?php

// on connecte le fichier de fonctions
require_once("../outils/fonctions.php");
//on établit une connexion avec la base de données
$connexion = connexion();



if (isset($_GET['action'])) {

    //on switche sur la valeur contenue dans $_GET['action"]
    switch ($_GET['action']) {
       case "messagerie":

       break; 
    }
}




//permet d'associer front.php avec front.html
include("back.html");

//on referme la connexion avec BDD
mysqli_close($connexion);
?>