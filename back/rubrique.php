<?php
//on teste si la variable de session $_SESSION['id_compte'] existe
if (isset($_SESSION['id_compte'])) {
    //affiche un titre de la session
    $titre = "Gestion des Rubriques";
    $form = "form_rubrique.html";
    //action par défault du formulaire
    $action_form = "inserer_rubrique";
    //pour cocher par défault visible à oui
    $check1[1] = "checked"; //visible
    $check2[0] = "checked"; //slider


    if (isset($_GET['cas'])) {

        //on switche sur la valeur contenue dans $_GET['action"]
        switch ($_GET['cas']) {

            case "inserer_rubrique":

                if (empty($_POST['nom_rubrique'])) {
                    $confirmation = "<p class=\"pas_ok\">Le nom de la rubrique est obligatoire</p>";
                    $color_champ['nom_rubrique'] = "color_champ";
                } elseif (empty($_POST['titre_rubrique'])) {
                    $confirmation = "<p class=\"pas_ok\">Le titre est obligatoire</p>";
                    $color_champ['titre_rubrique'] = "color_champ";
                } else {

                    $requete0 ="SELECT COUNT(*) AS rang FROM rubriques";
                    $resultat0 = mysqli_query($connexion, $requete0);
                    $ligne0 = mysqli_fetch_object($resultat0);
                    $new_rang = $ligne0->rang + 1;

                    $requete = "INSERT INTO rubriques SET
                    id_compte='" . $_SESSION['id_compte'] . "',
                        nom_rubrique='" . security($_POST['nom_rubrique']) . "',
                        titre_rubrique='" . security($_POST['titre_rubrique']) . "',
                        lien_rubrique='" . security($_POST['lien_rubrique']) . "',
                        visible='" . $_POST['visible'] . "',
                        slider='" . $_POST['slider'] . "',
                        rang='".$new_rang."',
                        date_rubrique=NOW()";
                    $resultat = mysqli_query($connexion, $requete);
                    $confirmation = "<p class=\"ok\">La rubrique a bien été créée.</p>";
                    //on vide les champs du formulaire
                    foreach ($_POST as $cle => $valeur) {
                        //unset supprime une variable
                        unset($_POST[$cle]);
                    }

                }

                break;

            case "avertir_rubrique":

                if (isset($_GET['id_rubrique'])) {
                    $confirmation = "<p>Voulez-vous supprimer la rubrique n°" . $_GET['id_rubrique'] . "</p>";
                    $confirmation .= "<a href=\"back.php?action=rubrique&cas=supprimer_rubrique&id_rubrique=" . $_GET['id_rubrique'] . "\">Oui</a>&nbsp;&nbsp;&nbsp;";
                    $confirmation .= "<a href=\"back.php?action=rubrique\">Non</a>";
                }

                break;

            case "supprimer_rubrique":

                if (isset($_GET['id_rubrique'])) {
                    //on vérifie s'il y a des pages accosiées à cette rubrique
                    $requete = "SELECT * FROM pages WHERE id_rubrique='" . $_GET['id_rubrique'] . "'";
                    $resultat = mysqli_query($connexion, $requete);
                    //on calcule le nombre de lignes qu'il y a dans $resultat
                    $nb = mysqli_num_rows($resultat);
                    //s'il y a des rubriques
                    if ($nb > 0) {
                        $confirmation = "<p class=\"pas_ok\">Des pages sont encore associées à cette rubrique</p>";
                    } else {
                        $requete2 = "DELETE FROM rubriques WHERE id_rubrique='" . $_GET['id_rubrique'] . "'";
                        $resultat2 = mysqli_query($connexion, $requete2);
                        $confirmation = "<p class=\"ok\">La rubrique a bien été supprimée</p>";

                    }
                    $requete2 = "SELECT * FROM rubriques ORDER BY rang";
                    $resultat2 = mysqli_query($connexion, $requete2);
                    $i = 1;
                    while ($ligne2 = mysqli_fetch_object($resultat2)) {
                        $requete3 = "UPDATE rubriques SET rang='" . $i . "'WHERE id_rubrique='" . $ligne2->id_rubrique . "'";
                        $resultat3 = mysqli_query($connexion, $requete3);
                        $i++;
                    }
                }



                break;


            case "recharger_rubrique":
                if (isset($_GET['id_rubrique'])) {
                    $action_form = "modifier_rubrique&id_rubrique=" . $_GET['id_rubrique'];

                    //on recharge les champs du formulaire
                    $requete = "SELECT * FROM rubriques WHERE id_rubrique='" . $_GET['id_rubrique'] . "'";
                    $resultat = mysqli_query($connexion, $requete);
                    $ligne = mysqli_fetch_object($resultat);
                    // on réattribbue à chaque champ du formulaire la valeur récupérée dans la BDD
                    $_POST['nom_rubrique'] = $ligne->nom_rubrique;
                    $_POST['titre_rubrique'] = $ligne->titre_rubrique;
                    $_POST['lien_rubrique'] = $ligne->lien_rubrique;
                    $check1[$ligne->visible] = "checked";
                    $check2[$ligne->slider] = "checked";
                }


                break;

            case "modifier_rubrique":
                if (isset($_GET['id_rubrique'])) {
                    //on met à jour la table 
                    if (empty($_POST['nom_rubrique'])) {
                        $confirmation = "<p class=\"pas_ok\">Le nom de la rubrique est obligatoire</p>";
                        $color_champ['nom_rubrique'] = "color_champ";
                    } elseif (empty($_POST['titre_rubrique'])) {
                        $confirmation = "<p class=\"pas_ok\">Le titre de la rubrique est obligatoire</p>";
                        $color_champ['titre_rubrique'] = "color_champ";
                    } else {
                        $requete = "UPDATE rubriques SET id_compte='" . $_SESSION['id_compte'] . "',
                        nom_rubrique='" . security($_POST['nom_rubrique']) . "',
                        titre_rubrique='" . security($_POST['titre_rubrique']) . "',
                        lien_rubrique='" . security($_POST['lien_rubrique']) . "',
                        visible='" . $_POST['visible'] . "',
                        slider='" . $_POST['slider'] . "',
                        date_rubrique=NOW() WHERE id_rubrique='" . $_GET['id_rubrique'] . "'";
                        $resultat = mysqli_query($connexion, $requete);
                        $confirmation = "<p class=\"ok\">La rubrique a bien été modifiée</p>";

                        //on vide les champs du formulaire
                        foreach ($_POST as $cle => $valeur) {
                            //unset supprime une variabl
                            unset($_POST[$cle]);
                        }
                    }

                }

                break;

            case "modifier_etat":

                if (isset($_GET['id_rubrique'])) {
                    if (isset($_GET['etat'])) {
                        $requete = "UPDATE rubriques SET visible='" . $_GET['etat'] . "' WHERE id_rubrique='" . $_GET['id_rubrique'] . "'";
                        $resultat = mysqli_query($connexion, $requete);
                    }
                }
                break;

            case "trier_rubrique":
                if (isset($_GET['id_rubrique'])) {
                    switch ($_GET['sens']) {
                        case "up":
                            if(isset($_GET['rang']) && $_GET['rang']>1)
                                {
                                //on change le rang de la ligne a qui on veut prendre la place
                                $rang=$_GET['rang']-1;
                                $requete="UPDATE rubriques SET rang='" . $_GET['rang'] . "' WHERE rang='" . $rang . "'";
                                $resultat=mysqli_query($connexion,$requete);
        
                                //on change le rang de la rubrique (id_rubrique) concernée
                                $requete2="UPDATE rubriques SET rang='" . $rang . "' WHERE id_rubrique='" . $_GET['id_rubrique'] . "'";
                                //echo $requete;
                                $resultat2=mysqli_query($connexion,$requete2);
                                }
                            break;
                        case "down":
                            //on calcule le nombre de lignes
                            $requete="SELECT COUNT(*) AS nb_rubrique FROM rubriques";
                            $resultat=mysqli_query($connexion, $requete);
                            $ligne=mysqli_fetch_object($resultat);
                            
                            if(isset($_GET['rang']) && $_GET['rang']<$ligne->nb_rubrique)
                            {
                            //on change le rang de la ligne a qui on veut prendre la place
                            $rang=$_GET['rang']+1;
                            $requete="UPDATE rubriques SET rang='" . $_GET['rang'] . "' WHERE rang='" . $rang . "'";
                            $resultat=mysqli_query($connexion,$requete);
    
                            //on change le rang de la rubrique (id_rubrique) concernée
                            $requete2="UPDATE rubriques SET rang='" . $rang . "' WHERE id_rubrique='" . $_GET['id_rubrique'] . "'";
                            //echo $requete;
                            $resultat2=mysqli_query($connexion,$requete2);
                            }
                            break;
                    }
                }

                break;


        }
    }


    $requete = "SELECT r.*, c.* FROM rubriques AS r
                INNER JOIN comptes AS c
                ON r.id_compte = c.id_compte
                ORDER BY r.rang ASC";

    $resultat = mysqli_query($connexion, $requete);
    //tant que $resultat contient des lignes (uplets)
    $content = "";
    while ($ligne = mysqli_fetch_object($resultat)) {

        $content .= "<details>";
        $content .= "<summary>";
        $content .= "<div class=\"actions\">";
        $content .= "<a href=\"back.php?action=rubrique&cas=trier_rubrique&sens=up&id_rubrique=" . $ligne->id_rubrique . "&rang=" . $ligne->rang . "\"><i class=\"fa-solid fa-arrow-up\"></i></a>";
        $content .= "<a href=\"back.php?action=rubrique&cas=trier_rubrique&sens=down&id_rubrique=" . $ligne->id_rubrique . "&rang=" . $ligne->rang . "\"><i class=\"fa-solid fa-arrow-down\"></i></a>";
        $content .= "&nbsp;&nbsp;</div>";
        $content .= "<div id=\"info_rubrique\">";
        $content .= "<div id=\"id_rubrique\">" . $ligne->id_rubrique . " " . $ligne->nom_rubrique . "</div>";
        $content .= "<div id=\"nom_rubrique\">" . $ligne->titre_rubrique . "</div>";
        $content .= "</div><div id=\"change_rubrique\">";
        //Pour changer l'état de la rubrique en visible ou non en cliquant sur l'état de la rubrique
        if ($ligne->visible == 1) {
            $content .= "<div><a href=\"back.php?action=rubrique&cas=modifier_etat&etat=0&id_rubrique=" . $ligne->id_rubrique . "\"><i style=\"color: goldenrod;\" class=\"fa-regular fa-eye\"></i></a></div>";
        } else {
            $content .= "<div><a href=\"back.php?action=rubrique&cas=modifier_etat&etat=1&id_rubrique=" . $ligne->id_rubrique . "\"><i style=\"color: goldenrod;\" class=\"fa-regular fa-eye-slash\"></i></a></div>";
        }
        $content .= "<div><a href=\"back.php?action=rubrique&cas=recharger_rubrique&id_rubrique=" . $ligne->id_rubrique . "\"  ><i style=\"color: goldenrod;\" class=\"fa-solid fa-paintbrush\"></i></a></div>";
        $content .= "<div><a href=\"back.php?action=rubrique&cas=avertir_rubrique&id_rubrique=" . $ligne->id_rubrique . "\"><i style=\"color: goldenrod;\" class=\"fa-solid fa-x\"></i></a></div>";
        $content .= "</div>";
        $content .= "</summary>";
        $content .= "<div class=\"contenu_rubrique\">";
        $content .= "<div id=\"date\">Créer le " . $ligne->date_rubrique . "</div>";
        $content .= "<div id\"compte\">Créer par " . $ligne->prenom_compte . " " . $ligne->nom_compte . "</div>";
        $content .= "<div id=\"lien_rubrique\">" . $ligne->lien_rubrique . "</div>";
        $content .= "</div>";
        $content .= "</details>";
    }

} else {
    //l'utilisateur n'est pas autorisé
    header("Location:../log/login.php");
}
?>