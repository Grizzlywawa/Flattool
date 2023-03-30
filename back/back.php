<?php

//permet d'autoriser l'usage des variables de session
session_start();

//on teste si la variable de session $_SESSION['id_compte'] existe
if (isset($_SESSION['id_compte'])) {



    // on connecte le fichier de fonctions
    require_once("../outils/fonctions.php");
    //on établit une connexion avec la base de données
    $connexion = connexion();



    if (isset($_GET['action'])) {

        //on switche sur la valeur contenue dans $_GET['action"]
        switch ($_GET['action']) {
            case "logout":
                //detruit toutes les variables de session qui ont été enregistrées
                session_destroy();
                //on redirige vers la page d'accueil du site
                header("Location:../index.php");

            case "messagerie":

                include("messagerie.php");

                break;

            case "compte":

                include("compte.php");

                break;

            case "page":
                include("page.php");

                break;
        }
    }
    //permet d'associer front.php avec front.html
    include("back.html");

    //on referme la connexion avec BDD
    @mysqli_close($connexion);
} else {
    header("Location:../log/login.php");
}
?>