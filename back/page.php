<?php
//on teste si la variable de session $_SESSION['id_compte'] existe
if (isset($_SESSION['id_compte'])) {
    //affiche un titre de la session
    $titre = "Gestion des Pages";
    $form = "form_page.html";

    $quality = 80;
    $largeur_b = 1600;
    $largeur_m = 800;
    $largeur_s = 60;
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
                    //on récupère le dernier id_compte qui vient d'être créé
                    $dernier_id_cree = mysqli_insert_id($connexion);
                    //============================================================
                    //si le champ à parcourir est utilisé(pas vide)
                    if (!empty($_FILES['img_page']['name'])) {
                        $tab_img = pathinfo($_FILES['img_page']['name']);
                        $extension = $tab_img['extension'];
                        echo $extension;
                        //on teste si l'extension du fichier est autorisée
                        if ($extension == "png" || $extension == "jpg" || $extension == "gif" || $extension == "webp") {
                            //si le fichier est bien uploadé du local vers le distant
                            if (is_uploaded_file($_FILES['img_page']['tmp_name'])) {
                                //on détermine les chemins des 3 images à générer
                                $chemin_b = "../medias/media" . $dernier_id_cree . "_b." . $extension;
                                $chemin_m = "../medias/media" . $dernier_id_cree . "_m." . $extension;
                                $chemin_s = "../medias/media" . $dernier_id_cree . "_s." . $extension;
                                if (copy($_FILES['img_page']['tmp_name'], $chemin_b)) {
                                    //on prend les mesures du fichier image
                                    $size = GetImageSize($chemin_b);
                                    $largeur = $size[0];
                                    $hauteur = $size[1];
                                    $rapport = $largeur / $hauteur;

                                    // si la largeur de l'image uploadé est inférieure à 1600px (voir ligne 9)
                                    if ($largeur < $largeur_b) {
                                        $largeur_b = $largeur;
                                        $hauteur_b = $hauteur;
                                    } else {
                                        $hauteur_b = $largeur_b / $rapport;
                                    }
                                    // on créé une copie en redimensionnant et en appliquant un taux de compression
                                    redimage($chemin_b, $chemin_b, $largeur_b, $hauteur_b, $quality);
                                    // si la largeur de l'image uploadé est inférieure à 800px (voir ligne 10)
                                    if ($largeur < $largeur_m) {
                                        $largeur_m = $largeur;
                                        $hauteur_m = $hauteur;
                                    } else {
                                        $hauteur_m = $largeur_m / $rapport;
                                    }
                                    redimage($chemin_b, $chemin_m, $largeur_m, $hauteur_m, $quality);
                                    //on crée la miniature (_s)
                                    $hauteur_s = $largeur_s / $rapport;
                                    redimage($chemin_b, $chemin_s, $largeur_s, $hauteur_s, $quality);

                                    //redimage($image_source, $image_destination, $new_largeur, $new_hauteur, $quality);
                                    //on met à jour le champ img_compte de la table comptes
                                    $requete2 = "UPDATE pages SET img_page='" . $chemin_s . "' WHERE id_page='" . $dernier_id_cree . "'";
                                    $resultat2 = mysqli_query($connexion, $requete2);
                                    $confirmation = "<p class=\"ok\">La page a bien été enregistrée</p>";
                                }
                            }
                        } else {
                            $confirmation = "<p class=\"pas_ok\">Ce fichier n'est pas autorisé</p>";
                        }
                    } else {
                        //on confirme l'enregistrement
                        $confirmation = "<p class=\"ok\">La page a bien été enregistrée</p>";
                    }
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
                    //on vérifie s'il y a une image associée au compte
                    $requete = "SELECT * FROM pages WHERE id_page='" . $_GET['id_page'] . "'";
                    $resultat = mysqli_query($connexion, $requete);
                    $ligne = mysqli_fetch_object($resultat);
                    // s'il y a une image
                    if (!empty($ligne->img_page)) {
                        $chemin_b = str_replace("_s", "_b", $ligne->img_page);
                        $chemin_m = str_replace("_s", "_m", $ligne->img_page);
                        $chemin_s = $ligne->img_page;
                        //on supprime les fichiers images (le @ désactive les warnings)
                        @unlink($chemin_b);
                        @unlink($chemin_m);
                        @unlink($chemin_s);
                    }

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
                    //si le champ img_page n'est pas vide
                    if (!empty($ligne->img_page)) {
                        $miniature = "<div><img src='" . $ligne->img_page . "' alt''/><a href='back.php?action=page&cas=supprimer_img_page&id_page=" . $ligne->id_page . "'/>Supprimer</div>";
                    }
                }

                break;

            case "supprimer_img_page":
                if (isset($_GET['id_page'])) {
                    //on va chercher les éléments dans la table pages
                    $requete = "SELECT * FROM pages WHERE id_page='" . $_GET['id_page'] . "'";
                    $resultat = mysqli_query($connexion, $requete);
                    $ligne = mysqli_fetch_object($resultat);
                    $chemin_b = str_replace("_s", "_b", $ligne->img_page);
                    $chemin_m = str_replace("_s", "_m", $ligne->img_page);
                    @unlink($ligne->img_page);
                    @unlink($chemin_b);
                    @unlink($chemin_m);

                    $requete2 = "UPDATE pages SET img_page=NULL WHERE id_page='" . $_GET['id_page'] . "'";
                    $resultat2 = mysqli_query($connexion, $requete2);
                    $confirmation = "<p class=\"ok\">Le media a bien été supprimé</p>";
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

                    //si le champ à parcourir est utilisé(pas vide)
                    if (!empty($_FILES['img_page']['name'])) {
                        $tab_img = pathinfo($_FILES['img_page']['name']);
                        $extension = $tab_img['extension'];
                        //echo $extension;
                        //on teste si l'extension du fichier est autorisée
                        if ($extension == "png" || $extension == "jpg" || $extension == "gif" || $extension == "webp") {
                            //si le fichier est bien uploadé du local vers le distant
                            if (is_uploaded_file($_FILES['img_page']['tmp_name'])) {
                                //on détermine les chemins des 3 images à générer
                                $chemin_b = "../medias/media" . $_GET['id_page'] . "_b." . $extension;
                                $chemin_m = "../medias/media" . $_GET['id_page'] . "_m." . $extension;
                                $chemin_s = "../medias/media" . $_GET['id_page'] . "_s." . $extension;
                                if (copy($_FILES['img_page']['tmp_name'], $chemin_b)) {
                                    //on prend les mesures du fichier image
                                    $size = GetImageSize($chemin_b);
                                    $largeur = $size[0];
                                    $hauteur = $size[1];
                                    $rapport = $largeur / $hauteur;

                                    // si la largeur de l'image uploadé est inférieure à 1600px (voir ligne 9)
                                    if ($largeur < $largeur_b) {
                                        $largeur_b = $largeur;
                                        $hauteur_b = $hauteur;
                                    } else {
                                        $hauteur_b = $largeur_b / $rapport;
                                    }
                                    // on créé une copie en redimensionnant et en appliquant un taux de compression
                                    redimage($chemin_b, $chemin_b, $largeur_b, $hauteur_b, $quality);
                                    // si la largeur de l'image uploadé est inférieure à 800px (voir ligne 10)
                                    if ($largeur < $largeur_m) {
                                        $largeur_m = $largeur;
                                        $hauteur_m = $hauteur;
                                    } else {
                                        $hauteur_m = $largeur_m / $rapport;
                                    }
                                    redimage($chemin_b, $chemin_m, $largeur_m, $hauteur_m, $quality);
                                    //on crée la miniature (_s)
                                    $hauteur_s = $largeur_s / $rapport;
                                    redimage($chemin_b, $chemin_s, $largeur_s, $hauteur_s, $quality);

                                    //redimage($image_source, $image_destination, $new_largeur, $new_hauteur, $quality);
                                    //on met à jour le champ img_compte de la table comptes
                                    $requete2 = "UPDATE pages SET img_page='" . $chemin_s . "' WHERE id_page='" . $_GET['id_page'] . "'";
                                    $resultat2 = mysqli_query($connexion, $requete2);
                                    $confirmation = "<p class=\"ok\">La page a bien été modifiée</p>";
                                }
                            }
                        } else {
                            $confirmation = "<p class=\"pas_ok\">Ce fichier n'est pas autorisé</p>";
                        }
                    } else {
                        //on confirme l'enregistrement
                        $confirmation = "<p class=\"ok\">La page a bien été modifiée</p>";
                    }

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