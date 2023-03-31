<?php
//on teste si la variable de session $_SESSION['id_compte'] existe
if (isset($_SESSION['id_compte'])) {
    //affiche un titre de la session
    $titre = "Gestion des Pages";
    $form = "form_page.html";
    //action par défault du formulaire
    $action_form = "inserer_page";
    //pour cocher par défault visible à oui
    $check[1] = "checked";


    if (isset($_GET['cas'])) {

        //on switche sur la valeur contenue dans $_GET['action"]
        switch ($_GET['cas']) {

            case "inserer_page":

                if (empty($_POST['titre_page'])) {
                    $confirmation = "<p class=\"pas_ok\">Le titre de la page est obligatoire</p>";
                } elseif (empty($_POST['contenu_page'])) {
                    $confirmation = "<p class=\"pas_ok\">Le contenu de la page est obligatoire</p>";
                } else {
                    $requete = "INSERT INTO pages SET
                        titre_page='" . security($_POST['titre_page']) . "',
                        contenu_page='" . security($_POST['contenu_page']) . "',
                        visible='" . $_POST['visible'] . "',
                        date_page=NOW()";
                    $resultat = mysqli_query($connexion, $requete);

                    $confirmation = "<p class=\"ok\">La page a bien été créée.</p>";
                    foreach ($_POST as $cle => $valeur) {
                        unset($_POST[$cle]);
                    }

                }

                break;

            case "avertir_page":

                if (isset($_GET['id_page'])) {
                    $confirmation = "<p>Voulez-vous supprimer la page n°" . $_GET['id_page'] . "</p>";
                    $confirmation .= "<a href=\"back.php?action=page&cas=supprimer_page&id_page=" . $_GET['id_page'] . "\">Oui</a>&nbsp;&nbsp;&nbsp;";
                    $confirmation .= "<a href=\"back.php?action=page\">Non</a>";
                }

                break;

            case "supprimer_page":

                if (isset($_GET['id_page'])) {
                    //on vérifie que ce n'est pas le dernier compte autorisé
                    $requete = "SELECT COUNT(*) AS nb_page FROM pages";
                    $resultat = mysqli_query($connexion, $requete);
                    $ligne = mysqli_fetch_object($resultat);

                    $requete2 = "DELETE FROM pages WHERE id_page='" . $_GET['id_page'] . "'";
                    $resultat2 = mysqli_query($connexion, $requete2);
                    $confirmation = "<p class=\"ok\">La page a bien été supprimée</p>";
                }



                break;
            case "recharger_page":
                $action_form = "modifier_page&id_page=" . $_GET['id_page'];
                if (isset($_GET['id_page'])) {
                    //on recharge les champs du formulaire
                    $requete = "SELECT * FROM pages WHERE id_page='" . $_GET['id_page'] . "'";
                    $resultat = mysqli_query($connexion, $requete);
                    $ligne = mysqli_fetch_object($resultat);
                    // on réattribbue à chaque champ du formulaire la valeur récupérée dans la BDD
                    $_POST['titre_page'] = $ligne->titre_page;
                    $_POST['contenu_page'] = $ligne->contenu_page;
                    $_POST['visible'] = $ligne->visible;
                }

                break;
            case "modifier_page":
                if (isset($_GET['id_page'])) {
                    //on met à jour la table 
                    if (empty($_POST['titre_page'])) {
                        $confirmation = "<p class=\"pas_ok\">Le titre de la page est obligatoire</p>";
                    } elseif (empty($_POST['contenu_page'])) {
                        $confirmation = "<p class=\"pas_ok\">Le contenu de la page est obligatoire</p>";
                    } else {
                        $requete = "UPDATE pages SET titre_page='" . security($_POST['titre_page']) . "',
                        contenu_page='" . security($_POST['contenu_page']) . "',
                        date_page=NOW()";
                        $requete .= " WHERE id_page='" . $_GET['id_page'] . "'";
                        $resultat = mysqli_query($connexion, $requete);
                    }

                    //on notifie la mise à jour
                    $confirmation = '<p class=\"ok\"> La page a bien été modifiée</p>';

                    //on vide les champs du formulaire
                    foreach ($_POST as $cle => $valeur) {
                        //unset supprime une variabl
                        unset($_POST[$cle]);
                    }
                }

                break;

            case "modifier_etat":

                if (isset($_GET['id_page'])) {
                    if (isset($_GET['etat'])) {
                        $requete = "UPDATE pages SET visible='" . $_GET['etat'] . "' WHERE id_page='" . $_GET['id_page'] . "'";
                        $resultat = mysqli_query($connexion, $requete);
                    }
                }
            break;

            
        }
    }


    $requete = "SELECT * FROM pages ORDER BY id_page ASC";
    $resultat = mysqli_query($connexion, $requete);
    //tant que $resultat contient des lignes (uplets)
    $content = "";
    while ($ligne = mysqli_fetch_object($resultat)) {

        $content .= "<details>";
        $content .= "<summary>";
        $content .= "<div id=\"info_page\">";
        $content .= "<div id=\"id_page\">" . $ligne->id_page . "</div>";
        $content .= "<div id=\"titre_page\">" . $ligne->titre_page . " " . "</div>";
        $content .= "</div><div id=\"change_page\">";
        //Pour changer l'état de la page en visible ou non en cliquant sur l'état de la page
        if ($ligne->visible == 1) {
            $content .= "<div><a href=\"back.php?action=page&cas=modifier_etat&etat=0&id_page=" . $ligne->id_page . "\"><i style=\"color: goldenrod;\" class=\"fa-regular fa-eye\"></i></a></div>";
        } else {
            $content .= "<div><a href=\"back.php?action=page&cas=modifier_etat&etat=1&id_page=" . $ligne->id_page . "\"><i style=\"color: goldenrod;\" class=\"fa-regular fa-eye-slash\"></i></a></div>";
        }
        $content .= "<div><a href=\"back.php?action=page&cas=recharger_page&id_page=" . $ligne->id_page . "\"  ><i style=\"color: goldenrod;\" class=\"fa-solid fa-paintbrush\"></i></a></div>";
        $content .= "<div><a href=\"back.php?action=page&cas=avertir_page&id_page=" . $ligne->id_page . "\"><i style=\"color: goldenrod;\" class=\"fa-solid fa-x\"></i></a></div>";
        $content .= "</div>";
        $content .= "</summary>";
        $content .= "<div class=\"contenu_page\">";
        $content .= "<div id=\"date\">Créer le " . $ligne->date_page . "</div>";
        $content .= "<div id=\"contenu_page\">" . $ligne->contenu_page . "</div>";
        $content .= "</div>";
        $content .= "</details>";
    }

} else {
    //l'utilisateur n'est pas autorisé
    header("Location:../log/login.php");
}
?>