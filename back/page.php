<?php
//on teste si la variable de session $_SESSION['id_compte'] existe
if (isset($_SESSION['id_compte'])) {
    //affiche un titre de la session
    $titre = "Gestion des Pages";
    $form = "form_page.html";
    //action par défault du formulaire
    $action_form = "inserer_page";


    if (isset($_GET['cas'])) {

        //on switche sur la valeur contenue dans $_GET['action"]
        switch ($_GET['cas']) {

            case "inserer_compte":
                if (empty($_POST['nom_compte'])) {
                    $confirmation = "<p class=\"pas_ok\">Le nom du compte est obligatoire</p>";
                } elseif (empty($_POST['email_compte'])) {
                    $confirmation = "<p class=\"pas_ok\">Le champ email est obligatoire</p>";

                } elseif (empty($_POST['login_compte'])) {
                    $confirmation = "<p class=\"pas_ok\">Le champ login est obligatoire</p>";

                } elseif (empty($_POST['pass_compte'])) {
                    $confirmation = "<p class=\"pas_ok\">Le champ mot de passe est obligatoire</p>";

                } else {
                    $requete = "INSERT INTO comptes SET
                        nom_compte='" . security($_POST['nom_compte']) . "',
                        prenom_compte='" . security($_POST['prenom_compte']) . "',
                        email_compte='" . security($_POST['email_compte']) . "',
                        login_compte='" . security($_POST['login_compte']) . "',
                        pass_compte=SHA1('" . ($_POST['pass_compte']) . "')";
                    $resultat = mysqli_query($connexion, $requete);

                    $confirmation = "<p class=\"ok\">Le compte a bien été enregistré.</p>";
                    foreach ($_POST as $cle => $valeur) {
                        unset($_POST[$cle]);
                    }

                }

                break;

            case "avertir_compte":

                if (isset($_GET['id_compte'])) {
                    $confirmation = "<p>Voulez-vous supprimer l'utilisateur n°" . $_GET['id_compte'] . "</p>";
                    $confirmation .= "<a href=\"back.php?action=compte&cas=supprimer_compte&id_compte=" . $_GET['id_compte'] . "\">Oui</a>&nbsp;&nbsp;&nbsp;";
                    $confirmation .= "<a href=\"back.php?action=compte\">Non</a>";
                }

                break;

            case "supprimer_compte":

                if (isset($_GET['id_compte'])) {
                    //on vérifie que ce n'est pas le dernier compte autorisé
                    $requete = "SELECT COUNT(*) AS nb_compte FROM comptes";
                    $resultat = mysqli_query($connexion, $requete);
                    $ligne = mysqli_fetch_object($resultat);
                    //si c'est le dernier compte de la table
                    if ($ligne->nb_compte == 1) {
                        $confirmation = "<p class=\"pas_ok\">Le compte ne peut pas être supprimé car dernier compte autorisé</p>";
                    } else {
                        $requete2 = "DELETE FROM comptes WHERE id_compte='" . $_GET['id_compte'] . "'";
                        $resultat2 = mysqli_query($connexion, $requete2);
                        $confirmation = "<p class=\"ok\">Le compte a bien été supprimé</p>";
                    }



                }

                break;
            case "recharger_compte":
                $action_form = "modifier_compte&id_compte=" . $_GET['id_compte'];
                if (isset($_GET['id_compte'])) {
                    //on recharge les champs du formulaire
                    $requete = "SELECT * FROM comptes WHERE id_compte='" . $_GET['id_compte'] . "'";
                    $resultat = mysqli_query($connexion, $requete);
                    $ligne = mysqli_fetch_object($resultat);
                    // on réattribbue à chaque champ du formulaire la valeur récupérée dans la BDD
                    $_POST['nom_compte'] = $ligne->nom_compte;
                    $_POST['prenom_compte'] = $ligne->prenom_compte;
                    $_POST['email_compte'] = $ligne->email_compte;
                    $_POST['login_compte'] = $ligne->login_compte;
                }

                break;
            case "modifier_compte":
                if (isset($_GET['id_compte'])) {
                    //on met à jour la table 
                    if (empty($_POST['nom_compte'])) {
                        $confirmation = "<p class=\"pas_ok\">Le nom du compte est obligatoire</p>";
                    } elseif (empty($_POST['email_compte'])) {
                        $confirmation = "<p class=\"pas_ok\">Le champ email est obligatoire</p>";

                    } elseif (empty($_POST['login_compte'])) {
                        $confirmation = "<p class=\"pas_ok\">Le champ login est obligatoire</p>";

                    } else {
                        $requete = "UPDATE comptes SET nom_compte='" . security($_POST['nom_compte']) . "',
                        prenom_compte='" . security($_POST['prenom_compte']) . "',
                        email_compte='" . security($_POST['email_compte']) . "',
                        login_compte='" . security($_POST['login_compte']) . "'";


                        //CAS 1 : le mot de passe a été ressaisi
                        if (!empty($_POST['pass_compte'])) {

                            $requete .= ",pass_compte=SHA1('" . $_POST['pass_compte'] . "')";

                        }
                        $requete .= " WHERE id_compte='" . $_GET['id_compte'] . "'";
                        $resultat = mysqli_query($connexion, $requete);
                    }

                    //on notifie la mise à jour
                    $confirmation = '<p class=\"ok\"> Le compte a bien été modifié</p>';

                    //on vide les champs du formulaire
                    foreach ($_POST as $cle => $valeur) {
                        //unset supprime une variabl
                        unset($_POST[$cle]);
                    }
                }

                break;
        }
    }


    $requete = "SELECT * FROM comptes ORDER BY id_compte ASC";
    $resultat = mysqli_query($connexion, $requete);
    //tant que $resultat contient des lignes (uplets)
    $content = "";
    while ($ligne = mysqli_fetch_object($resultat)) {

        $content .= "<details>";
        $content .= "<summary>";
        $content .= "<div>" . $ligne->id_compte . "</div>";
        $content .= "<div>" . $ligne->login_compte . " " . "</div>";
        $content .= "<div>" . $ligne->email_compte . "</div>";
        $content .= "<div><a href=\"back.php?action=compte&cas=recharger_compte&id_compte=" . $ligne->id_compte . "\"  >Modifier</a></div>";
        $content .= "<div><a href=\"back.php?action=compte&cas=avertir_compte&id_compte=" . $ligne->id_compte . "\">Supprimer</a></div>";
        $content .= "</summary>";
        $content .= "<div id=\"nom_compte\">" . $ligne->nom_compte . " " . $ligne->prenom_compte . "</div>";

        $content .= "</details>";
    }

} else {
    //l'utilisateur n'est pas autorisé
    header("Location:../log/login.php");
}
?>