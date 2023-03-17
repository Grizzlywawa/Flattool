<?php
//on teste si la variable de session $_SESSION['id_compte'] existe
if (isset($_SESSION['id_compte'])) {
    //affiche un titre de la session
    $titre="Gestion de la Messagerie";


    // on connecte le fichier de fonctions
    require_once("../outils/fonctions.php");
    //on établit une connexion avec la base de données
    $connexion = connexion();

    if (isset($_GET['cas'])) {

        //on switche sur la valeur contenue dans $_GET['action"]
        switch ($_GET['cas']) {

            case "avertir_message":

            if(isset($_GET['id_contact'])){
                $confirmation="<p>Voulez-vous supprimer le message du contact n°" . $_GET['id_contact'] . "</p>";
                $confirmation.="<a href=\"back.php?action=messagerie&cas=supprimer_message&id_contact=". $_GET['id_contact'] ."\">Oui</a>&nbsp;&nbsp;&nbsp;";
                $confirmation.="<a href=\"back.php?action=messagerie\">Non</a>";
            }

                break;

            case "supprimer_message":

                if(isset($_GET['id_contact']))
                {
                $requete="DELETE FROM contacts WHERE id_contact='". $_GET['id_contact']. "'";
                $resultat=mysqli_query($connexion,$requete);
                $confirmation="<p>Le message a bien été supprimé</p>";
                }

                break;
        }
    }


    $requete = "SELECT * FROM contacts ORDER BY date_contact DESC";
    $resultat = mysqli_query($connexion, $requete);
    //tant que $resultat contient des lignes (uplets)
    $content="";
    while($ligne=mysqli_fetch_object($resultat)){
        $content.="<details>";
        $content.="<summary>";
        $content.="<div id=\"date\">". $ligne->date_contact ."</div>";
        $content.="<div id=\"nom\">". $ligne->nom_contact . " " . $ligne->prenom_contact ."</div>";
        $content.="<div id=\"mail\">". $ligne->email_contact ."</div>";
        $content.="<div><a href=\"back.php?action=messagerie&cas=avertir_message&id_contact=".$ligne->id_contact."\">Supprimer</a></i></div>";
        $content.="</summary>";

        $content.="<div id=\"message\">". $ligne->message_contact ."</div>";

        $content.="</details>";
    }
    //on referme la connexion avec BDD
    mysqli_close($connexion);
    }else {
        //l'utilisateur n'est pas autorisé
    header("Location:../log/login.php");
}
?>