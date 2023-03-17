<?php
//permet d'utiliser l'usage des varaibles de session
session_start();

//si on est connecté au back, on place un btn sur le front pour revenir sur le back
if (isset($_SESSION['id_compte'])) {
    $retour="<div><a href=\"../back/back.php\">RETOUR</a></div>";
}

// on connecte le fichier de fonctions
require_once("../outils/fonctions.php");
$content = "home.php";
$contact="form_contact.php";
//on établit une connexion avec la base de données
$connexion = connexion();



if (isset($_GET['action'])) {

    $message = array();
    $color_champ = array();
    //on switche sur la valeur contenue dans $_GET['action"]
    switch ($_GET['action']) {
        case "contact":
            if (empty($_POST['nom_contact'])) {
                $message['nom_contact'] = "<label class=\"pas_ok\">Mets ton nom, connard !</label>";
                $color_champ['nom_contact'] = "color_champ";
            } elseif (empty($_POST['email_contact'])) {
                $message['email_contact'] = "<label class=\"pas_ok\">Mets ton mail, connard !</label>";
                $color_champ['email_contact'] = "color_champ";
            } elseif (empty($_POST['message_contact'])) {
                $message['message_contact'] = "<label class=\"pas_ok\">Mets ton message, connard !</label>";
                $color_champ['message_contact'] = "color_champ";
            } else {
                //on enregistre les champs dabns la table contacts$
                $requete = "INSERT INTO contacts
            SET nom_contact='" . $_POST['nom_contact'] . "',
            prenom_contact='" . $_POST['prenom_contact'] . "',
            email_contact='" . $_POST['email_contact'] . "',
            message_contact='" . $_POST['message_contact'] . "',
            date_contact=NOW()";

                $result = mysqli_query($connexion, $requete);
                $contact="merci.php";
            }
            break;


        case "page":
            //si on reçoit le paramètre page via la methode GET (lien url)
            if (isset($_GET['page'])) {
                $content = $_GET['page'] . ".php";
                //ex: formules.php
            }
            break;

    }
}




//permet d'associer front.php avec front.html
include("front.html");

//on referme la connexion avec BDD
mysqli_close($connexion);
?>