<?php

// on connecte le fichier de fonctions
require_once("../outils/fonctions.php");

//on établit une connexion avec la base de données
$connexion = connexion();

//si on reçoit le paramètre page via la methode GET (lien url)
if (isset($_GET['page'])) {
    $content = $_GET['page'] . ".php";
    //ex: formules.php
} else {
    $content = "home.php";
}


i




//permet d'associer front.php avec front.html
include("front.html");

//on referme la connexion avec BDD
mysqli_close($connexion);
?>