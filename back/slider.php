<?php
//on teste si la variable de session S_SESSION['id_compte'] existe
if (isset($_SESSION['id_compte'])) {
    $titre = "Gestion du slider";
    $form = "form_slider.html";
    //action par défaut du formulaire
    $action_form = "inserer_slider";
    //pour cocher par défaut visible à oui
    $check[1] = "checked";
    //on définit les variables par défaut pour le redimensionnement des images
    $quality = 80;
    $largeur_b = 1600;
    $largeur_m = 800;
    $largeur_s = 60;

    if (isset($_GET['cas'])) {
        //on switche sur la valeur contenue dans $_GET['action']
        switch ($_GET['cas']) {
            case "inserer_slider":

                if (empty($_POST['titre_slider'])) {
                    $confirmation = "<p class=\"pas_ok\">Le titre est obligatoire</p>";
                    $color_champ['titre_slider'] = "color_champ";
                } elseif (empty($_POST['contenu_slider'])) {
                    $confirmation = "<p class=\"pas_ok\">Le contenu est obligatoire</p>";
                    $color_champ['contenu_slider'] = "color_champ";
                } elseif (empty($_FILES['img_slider']['name'])) {
                    $confirmation = "<p class=\"pas_ok\">L'image est obligatoire</p>";
                    $color_champ['img_slider'] = "color_champ";
                } elseif (empty($_POST['alt_slider'])) {
                    $confirmation = "<p class=\"pas_ok\">Le contenu alt est obligatoire</p>";
                    $color_champ['alt_slider'] = "color_champ";
                } else {
                    //on calcule le rang à attribuer à la nouvelle page
                    $requete0 = "SELECT COUNT(*) AS rang FROM slider";
                    $resultat0 = mysqli_query($connexion, $requete0);
                    $ligne0 = mysqli_fetch_object($resultat0);
                    $new_rang = $ligne0->rang + 1;

                    //on enregistre le compte dans la table comptes
                    $requete = "INSERT INTO slider SET titre_slider='" . security($_POST['titre_slider']) . "',
                                                contenu_slider='" . security($_POST['contenu_slider']) . "',
                                                alt_slider='" . security($_POST['alt_slider']) . "',
                                                visible='" . $_POST['visible'] . "',
                                                rang='" . $new_rang . "'";

                    $resultat = mysqli_query($connexion, $requete);
                    //on récupere le dernier id_compte qui vient d'être créé
                    $dernier_id_cree = mysqli_insert_id($connexion);

                    //si le champ parcourir est utilisé (pas vide)
                    if (!empty($_FILES['img_slider']['name'])) {
                        $tab_img = pathinfo($_FILES['img_slider']['name']);
                        $extension = $tab_img['extension'];
                        //on teste si l'esxtension est aurorisé
                        if ($extension == "png" or $extension == "gif" or $extension == "jpg" or $extension == "webp") {
                            //si le fichier est bien uploadé du local vers le distant
                            if (is_uploaded_file($_FILES['img_slider']['tmp_name'])) {
                                //on détermine les chemins des 3 images à générer
                                $chemin_b = "../medias/slider" . $dernier_id_cree . "_b." . $extension;
                                $chemin_m = "../medias/slider" . $dernier_id_cree . "_m." . $extension;
                                $chemin_s = "../medias/slider" . $dernier_id_cree . "_s." . $extension;

                                if (copy($_FILES['img_slider']['tmp_name'], $chemin_b)) {
                                    //on prend les mesures du fichier image
                                    $size = GetImageSize($chemin_b);
                                    $largeur = $size[0];
                                    $hauteur = $size[1];
                                    $rapport = $largeur / $hauteur;

                                    //si la largeur de l'image uploadée est inférieure à 1600 (voir ligne 9)
                                    if ($largeur < $largeur_b) {
                                        $largeur_b = $largeur;
                                        $hauteur_b = $hauteur;
                                    } else {
                                        $hauteur_b = $largeur_b / $rapport;
                                    }
                                    //on créé une copie en redimensionnant et en appliquant un taux de compression
                                    redimage($chemin_b, $chemin_b, $largeur_b, $hauteur_b, $quality);

                                    //si la largeur de l'image uploadée est inférieure à 800 (voir ligne 10)
                                    if ($largeur < $largeur_m) {
                                        $largeur_m = $largeur;
                                        $hauteur_m = $hauteur;
                                    } else {
                                        $hauteur_m = $largeur_m / $rapport;
                                    }
                                    redimage($chemin_b, $chemin_m, $largeur_m, $hauteur_m, $quality);

                                    //on cree la miniature 
                                    $hauteur_s = $largeur_s / $rapport;
                                    redimage($chemin_b, $chemin_s, $largeur_s, $hauteur_s, $quality);

                                    //on met à jour le champ img_compte de la table comptes 
                                    $requete2 = "UPDATE slider SET img_slider='" . $chemin_s . "' WHERE id_slider='" . $dernier_id_cree . "'";
                                    $resultat2 = mysqli_query($connexion, $requete2);
                                    $confirmation = "<p class=\"ok\">La nouvelle illustation a bien été enregistrée</p>";
                                }

                            }
                        } else {
                            $confirmation = "<p class=\"pas_ok\">Cette extension n'est pas autorisée</p>";
                        }
                    } else {
                        //on confirme l'enregistrement
                        $confirmation = "<p class=\"ok\">La nouvelle illustation a bien été enregistrée</p>";
                    }

                    //on vide les champs du formulaire
                    foreach ($_POST as $cle => $valeur) {
                        //unset supprime une variable
                        unset($_POST[$cle]);
                    }
                }


                break;

            case "avertir_slider":

                if (isset($_GET['id_slider'])) {
                    $confirmation = "<p>Voulez-vous supprimer l'illustration n°" . $_GET['id_slider'] . "</p>";
                    $confirmation .= "<a href=\"back.php?action=slider&cas=supprimer_slider&id_slider=" . $_GET['id_slider'] . "\">OUI</a>&nbsp;&nbsp;&nbsp;";
                    $confirmation .= "<a href=\"back.php?action=slider\">NON</a>";
                }

                break;

            case "supprimer_slider":

                if (isset($_GET['id_slider'])) {
                    //on vérifie si il y a une image associée au compte
                    $requete = "SELECT * FROM slider WHERE id_slider='" . $_GET['id_slider'] . "'";
                    $resultat = mysqli_query($connexion, $requete);
                    $ligne = mysqli_fetch_object($resultat);
                    //si il y a une image
                    if (!empty($ligne->img_slider)) {
                        $chemin_b = str_replace("_s", "_b", $ligne->img_slider);
                        $chemin_m = str_replace("_s", "_m", $ligne->img_slider);
                        $chemin_s = $ligne->img_compte;
                        //on supprime les fichiers image (le @ désactive les warning)
                        @unlink($chemin_b);
                        @unlink($chemin_m);
                        @unlink($chemin_s);
                    }
                    $requete2 = "DELETE FROM slider WHERE id_slider='" . $_GET['id_slider'] . "'";
                    $resultat2 = mysqli_query($connexion, $requete2);
                    $confirmation = "<p class=\"ok\">L'illustration a bien été supprimée</p>";

                    //on réordonne les pages
                    $requete3 = "SELECT * FROM slider ORDER BY rang";
                    $resultat3 = mysqli_query($connexion, $requete3);
                    $i = 1;
                    while ($ligne3 = mysqli_fetch_object($resultat3)) {
                        $requete4 = "UPDATE slider SET rang='" . $i . "' WHERE id_slider='" . $ligne3->id_slider . "'";
                        $resultat4 = mysqli_query($connexion, $requete4);
                        $i++;
                    }
                }

                break;

            case "changer_etat":

                if (isset($_GET['id_slider'])) {
                    if (isset($_GET['etat'])) {
                        $requete = "UPDATE slider SET visible='" . $_GET['etat'] . "' WHERE id_slider='" . $_GET['id_slider'] . "'";
                        $resultat = mysqli_query($connexion, $requete);
                    }
                }

                break;

            case "recharger_slider":

                if (isset($_GET['id_slider'])) {
                    $action_form = "modifier_slider&id_slider=" . $_GET['id_slider'];
                    //on recharge les champs du formulaire
                    $requete = "SELECT * FROM slider WHERE id_slider='" . $_GET['id_slider'] . "'";
                    $resultat = mysqli_query($connexion, $requete);
                    $ligne = mysqli_fetch_object($resultat);
                    //on réattribue à chaque champ du formulaire la valeur récupérée dans la base
                    $_POST['titre_slider'] = $ligne->titre_slider;
                    $_POST['contenu_slider'] = $ligne->contenu_slider;
                    $check[$ligne->visible] = "checked";
                    $_POST['alt_slider'] = $ligne->alt_slider;

                    if (!empty($ligne->img_slider)) {
                        $miniature = "<div><img src=\"" . $ligne->img_slider . "\" alt=\"\" /></div>";
                    }
                }

                break;

            case "modifier_slider":

                if (isset($_GET['id_slider'])) {
                    if (empty($_POST['titre_slider'])) {
                        $confirmation = "<p class=\"pas_ok\">Le titre est obligatoire</p>";
                        $color_champ['titre_slider'] = "color_champ";
                    } elseif (empty($_POST['contenu_slider'])) {
                        $confirmation = "<p class=\"pas_ok\">Le contenu est obligatoire</p>";
                        $color_champ['contenu_slider'] = "color_champ";
                    } elseif (empty($_POST['alt_slider'])) {
                        $confirmation = "<p class=\"pas_ok\">Le contenu alt est obligatoire</p>";
                        $color_champ['alt_slider'] = "color_champ";
                    } else {
                        $requete = "UPDATE slider SET titre_slider='" . security($_POST['titre_slider']) . "',
                                                contenu_slider='" . security($_POST['contenu_slider']) . "',
                                                alt_slider='" . security($_POST['alt_slider']) . "',
                                                visible='" . $_POST['visible'] . "' 
                                                WHERE id_slider='" . $_GET['id_slider'] . "'";
                        //echo $requete;
                        $resultat = mysqli_query($connexion, $requete);

                        //si le champ parcourir est utilisé (pas vide)
                        if (!empty($_FILES['img_slider']['name'])) {
                            $tab_img = pathinfo($_FILES['img_slider']['name']);
                            $extension = $tab_img['extension'];
                            //on teste si l'esxtension est aurorisé
                            if ($extension == "png" or $extension == "gif" or $extension == "jpg" or $extension == "webp") {
                                //si le fichier est bien uploadé du local vers le distant
                                if (is_uploaded_file($_FILES['img_slider']['tmp_name'])) {
                                    //on détermine les chemins des 3 images à générer
                                    $chemin_b = "../medias/slider" . $_GET['id_slider'] . "_b." . $extension;
                                    $chemin_m = "../medias/slider" . $_GET['id_slider'] . "_m." . $extension;
                                    $chemin_s = "../medias/slider" . $_GET['id_slider'] . "_s." . $extension;

                                    if (copy($_FILES['img_slider']['tmp_name'], $chemin_b)) {
                                        //on prend les mesures du fichier image
                                        $size = GetImageSize($chemin_b);
                                        $largeur = $size[0];
                                        $hauteur = $size[1];
                                        $rapport = $largeur / $hauteur;

                                        //si la largeur de l'image uploadée est inférieure à 1600 (voir ligne 9)
                                        if ($largeur < $largeur_b) {
                                            $largeur_b = $largeur;
                                            $hauteur_b = $hauteur;
                                        } else {
                                            $hauteur_b = $largeur_b / $rapport;
                                        }
                                        //on créé une copie en redimensionnant et en appliquant un taux de compression
                                        redimage($chemin_b, $chemin_b, $largeur_b, $hauteur_b, $quality);

                                        //si la largeur de l'image uploadée est inférieure à 800 (voir ligne 10)
                                        if ($largeur < $largeur_m) {
                                            $largeur_m = $largeur;
                                            $hauteur_m = $hauteur;
                                        } else {
                                            $hauteur_m = $largeur_m / $rapport;
                                        }
                                        redimage($chemin_b, $chemin_m, $largeur_m, $hauteur_m, $quality);

                                        //on cree la miniature 
                                        $hauteur_s = $largeur_s / $rapport;
                                        redimage($chemin_b, $chemin_s, $largeur_s, $hauteur_s, $quality);

                                        //on met à jour le champ img_compte de la table comptes 
                                        $requete2 = "UPDATE slider SET img_slider='" . $chemin_s . "' WHERE id_slider='" . $_GET['id_slider'] . "'";
                                        $resultat2 = mysqli_query($connexion, $requete2);
                                        $confirmation = "<p class=\"ok\">L'illustration a bien été modifiée</p>";
                                    }

                                }
                            } else {
                                $confirmation = "<p class=\"pas_ok\">Cette extension n'est pas autorisée</p>";
                            }
                        } else {
                            //on confirme l'enregistrement
                            $confirmation = "<p class=\"ok\">L'illustration a bien été modifiée</p>";
                        }
                    }

                    //on vide les champs du formulaire
                    foreach ($_POST as $cle => $valeur) {
                        //unset supprime une variable
                        unset($_POST[$cle]);
                    }
                }
                break;

            case "trier_slider":

                if (isset($_GET['id_slider'])) {
                    switch ($_GET['sens']) {
                        case "up":
                            if (isset($_GET['rang']) && $_GET['rang'] > 1) {
                                //on change le rang de la ligne a qui on veut prendre la place
                                $rang = $_GET['rang'] - 1;
                                $requete = "UPDATE slider SET rang='" . $_GET['rang'] . "' WHERE rang='" . $rang . "'";
                                $resultat = mysqli_query($connexion, $requete);

                                //on change le rang de la rubrique (id_rubrique) concernée
                                $requete2 = "UPDATE slider SET rang='" . $rang . "' WHERE id_slider='" . $_GET['id_slider'] . "'";
                                //echo $requete;
                                $resultat2 = mysqli_query($connexion, $requete2);
                            }
                            break;

                        case "down":
                            //on calcule le nb de lignes 
                            $requete = "SELECT count(*) AS nb_slider FROM slider";
                            $resultat = mysqli_query($connexion, $requete);
                            $ligne = mysqli_fetch_object($resultat);

                            if (isset($_GET['rang']) && $_GET['rang'] < $ligne->nb_slider) {
                                //on change le rang de la ligne a qui on veut prendre la place
                                $rang = $_GET['rang'] + 1;
                                $requete = "UPDATE slider SET rang='" . $_GET['rang'] . "' WHERE rang='" . $rang . "'";
                                $resultat = mysqli_query($connexion, $requete);

                                //on change le rang du slider (id_slider) concernée
                                $requete2 = "UPDATE slider SET rang='" . $rang . "' WHERE id_slider='" . $_GET['id_slider'] . "'";
                                //echo $requete;
                                $resultat2 = mysqli_query($connexion, $requete2);
                            }
                            break;
                    }
                }

                break;
        }
    }

    //======================================================================================================
    //tableau d'affichage des pages
    //on selectionne tous les pages triés par date de création et le compte correspondant
    $requete = "SELECT * FROM slider ORDER BY rang ASC";
    $resultat = mysqli_query($connexion, $requete);
    //tant que $resultat contient des lignes (uplets)
    $content = "";
    $i = 0;
    while ($ligne = mysqli_fetch_object($resultat)) {
        $content .= "<details class=\"tab_results\">";
        $content .= "<summary>";

        //pour le tri
        $content .= "<div class=\"actions\">";
        $content .= "<a href=\"back.php?action=slider&cas=trier_slider&sens=up&id_slider=" . $ligne->id_slider . "&rang=" . $ligne->rang . "\"><span class=\"dashicons dashicons-arrow-up\"></span></a>";
        $content .= "<a href=\"back.php?action=slider&cas=trier_slider&sens=down&id_slider=" . $ligne->id_slider . "&rang=" . $ligne->rang . "\"><span class=\"dashicons dashicons-arrow-down\"></span></a>";
        $content .= "&nbsp;&nbsp;";
        $content .= "<div>" . $ligne->id_slider . " - " . $ligne->titre_slider . "</div>";
        $content .= "</div>";
        //fin tri

        $content .= "<div><img src=\"" . $ligne->img_slider . "\" alt=\"\" /></div>";
        if ($ligne->visible == 1) {
            $content .= "<div class=\"actions\"><a href=\"back.php?action=slider&cas=changer_etat&etat=0&id_slider=" . $ligne->id_slider . "\"><span class=\"dashicons dashicons-visibility\"></span></a>";
        } else {
            $content .= "<div class=\"actions\"><a href=\"back.php?action=slider&cas=changer_etat&etat=1&id_slider=" . $ligne->id_slider . "\"><span class=\"dashicons dashicons-hidden\"></span></a>";
        }
        $content .= "<a href=\"back.php?action=slider&cas=recharger_slider&id_slider=" . $ligne->id_slider . "#form_back\"><span class=\"dashicons dashicons-admin-customizer\"></span></a>";
        $content .= "<a href=\"back.php?action=slider&cas=avertir_slider&id_slider=" . $ligne->id_slider . "\"><span class=\"dashicons dashicons-no\"></span></a></div>";
        $content .= "</summary>";

        $content .= "<div class=\"all\">Attribut alt : " . $ligne->alt_slider . "<br><br>" . $ligne->contenu_slider . "</div>";

        $content .= "</details>";
    }

} else {
    //l'utilisateur n'est pas autorisé
    header("Location:../log/login.php");
}

?>