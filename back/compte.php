<?php
//on teste si la variable de session $_SESSION['id_compte'] existe
if (isset($_SESSION['id_compte'])) {
    //affiche un titre de la session
    $titre = "Gestion des Comptes";
    $form = "form_compte.html";
    //on définit les variables par défault pour le redimensionnement des images
    $quality = 80;
    $largeur_b = 1600;
    $largeur_m = 800;
    $largeur_s = 60;


    //action par défault du formulaire
    $action_form = "inserer_compte";


    if (isset($_GET['cas'])) {

        //on switche sur la valeur contenue dans $_GET['action"]
        switch ($_GET['cas']) {

            case "inserer_compte":

                // pour maintenir la selection de la liste déroulante
                if (!empty($_POST['statut_compte'])) {
                    $select[$_POST['statut_compte']] = "selected";
                }
                if (empty($_POST['nom_compte'])) {
                    $confirmation = "<p class=\"pas_ok\">Le nom du compte est obligatoire</p>";
                } elseif (empty($_POST['email_compte'])) {
                    $confirmation = "<p class=\"pas_ok\">Le champ email est obligatoire</p>";
                } elseif (empty($_POST['statut_compte'])) {
                    $confirmation = "<p class=\"pas_ok\">Le statut est obligatoire</p>";

                } elseif (empty($_POST['login_compte'])) {
                    $confirmation = "<p class=\"pas_ok\">Le champ login est obligatoire</p>";

                } elseif (empty($_POST['pass_compte'])) {
                    $confirmation = "<p class=\"pas_ok\">Le champ mot de passe est obligatoire</p>";

                } else {

                    // on enregistre le ccompte dans la table compte
                    $requete = "INSERT INTO comptes SET
                        nom_compte='" . security($_POST['nom_compte']) . "',
                        prenom_compte='" . security($_POST['prenom_compte']) . "',
                        email_compte='" . $_POST['email_compte'] . "',
                        statut_compte='" . security($_POST['statut_compte']) . "',
                        login_compte='" . security($_POST['login_compte']) . "',
                        pass_compte=SHA1('" . $_POST['pass_compte'] . "')";
                    $resultat = mysqli_query($connexion, $requete);

                    //on récupère le dernier id_compte qui vient d'être créé
                    $dernier_id_cree = mysqli_insert_id($connexion);
                    //============================================================
                    if ($dernier_id_cree == 0) {
                        $confirmation = "<p class=\"pas_ok\">Ce mail est déjà lié à un compte !</p>";

                    } else {
                        //si le champ à parcourir est utilisé(pas vide)
                        if (!empty($_FILES['img_compte']['name'])) {
                            $tab_img = pathinfo($_FILES['img_compte']['name']);
                            $extension = $tab_img['extension'];
                            echo $extension;
                            //on teste si l'extension du fichier est autorisée
                            if ($extension == "png" || $extension == "jpg" || $extension == "gif" || $extension == "webp") {
                                //si le fichier est bien uploadé du local vers le distant
                                if (is_uploaded_file($_FILES['img_compte']['tmp_name'])) {
                                    //on détermine les chemins des 3 images à générer
                                    $chemin_b = "../medias/avatar" . $dernier_id_cree . "_b." . $extension;
                                    $chemin_m = "../medias/avatar" . $dernier_id_cree . "_m." . $extension;
                                    $chemin_s = "../medias/avatar" . $dernier_id_cree . "_s." . $extension;
                                    if (copy($_FILES['img_compte']['tmp_name'], $chemin_b)) {
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
                                        $requete2 = "UPDATE comptes SET img_compte='" . $chemin_s . "' WHERE id_compte='" . $dernier_id_cree . "'";
                                        $resultat2 = mysqli_query($connexion, $requete2);
                                        $confirmation = "<p class=\"ok\">Le compte a bien été enregistré</p>";
                                    }
                                }
                            } else {
                                $confirmation = "<p class=\"pas_ok\">Ce fichier n'est pas autorisé</p>";
                            }
                        } else {
                            //on confirme l'enregistrement
                            $confirmation = "<p class=\"ok\">Le compte a bien été enregistré</p>";
                        }


                        $confirmation = "<p class=\"ok\">Le compte a bien été enregistré.</p>";
                    }
                    //============================================================


                    //on vide les champs du formulaire
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
                        //on vérifie s'il y a une image associée au compte
                        $requete = "SELECT * FROM comptes WHERE id_compte='" . $_GET['id_compte'] . "'";
                        $resultat = mysqli_query($connexion, $requete);
                        $ligne = mysqli_fetch_object($resultat);
                        // s'il y a une image
                        if (!empty($ligne->img_compte)) {
                            $chemin_b = str_replace("_s", "_b", $ligne->img_compte);
                            $chemin_m = str_replace("_s", "_m", $ligne->img_compte);
                            $chemin_s = $ligne->img_compte;
                            //on supprime les fichiers images (le @ désactive les warnings)
                            @unlink($chemin_b);
                            @unlink($chemin_m);
                            @unlink($chemin_s);
                        }

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
                    //si le champ img_compte n'est pas vide
                    if (!empty($ligne->img_compte)) {
                        $miniature = "<div><img src='" . $ligne->img_compte . "' alt=''/><a href='back.php?action=compte&cas=supprimer_img_compte&id_compte=" . $ligne->id_compte . "' />Supprimer</div>";
                    }
                    //pour maintenr la selection de la liste déroulante
                    if (isset($ligne->statut_compte)) {
                        $select[$ligne->statut_compte] = "selected";
                    }
                }

                break;

            case "supprimer_img_compte":
                if (isset($_GET['id_compte'])) {
                    //on va chercher les éléments dans la table comptes
                    $requete = "SELECT * FROM comptes WHERE id_compte='" . $_GET['id_compte'] . "'";
                    $resultat = mysqli_query($connexion, $requete);
                    $ligne = mysqli_fetch_object($resultat);
                    $chemin_b = str_replace("_s", "_b", $ligne->img_compte);
                    $chemin_m = str_replace("_s", "_m", $ligne->img_compte);
                    @unlink($ligne->img_compte);
                    @unlink($chemin_b);
                    @unlink($chemin_m);

                    $requete2 = "UPDATE comptes SET img_compte=NULL WHERE id_compte='" . $_GET['id_compte'] . "'";
                    $resultat2 = mysqli_query($connexion, $requete2);
                    $confirmation = "<p class=\"ok\">L'avatar a bien été supprimé</p>";
                }
                break;


            case "modifier_compte":
                if (isset($_GET['id_compte'])) {
                    //on met à jour la table 
                    if (empty($_POST['nom_compte'])) {
                        $confirmation = "<p class=\"pas_ok\">Le nom du compte est obligatoire</p>";
                    } elseif (empty($_POST['email_compte'])) {
                        $confirmation = "<p class=\"pas_ok\">Le champ email est obligatoire</p>";

                    } elseif (empty($_POST['statut_compte'])) {
                        $confirmation = "<p> class=\"pas_ok\">Le champ statut est obligatoire</p>";

                    } elseif (empty($_POST['login_compte'])) {
                        $confirmation = "<p class=\"pas_ok\">Le champ login est obligatoire</p>";

                    } else {
                        $requete = "UPDATE comptes SET nom_compte='" . security($_POST['nom_compte']) . "',
                        prenom_compte='" . security($_POST['prenom_compte']) . "',
                        email_compte='" . security($_POST['email_compte']) . "',
                        statut_compte='" . $_POST['statut_compte'] . "',
                        login_compte='" . security($_POST['login_compte']) . "'";


                        //CAS 1 : le mot de passe a été ressaisi
                        if (!empty($_POST['pass_compte'])) {

                            $requete .= ",pass_compte=SHA1('" . $_POST['pass_compte'] . "')";

                        }
                        $requete .= " WHERE id_compte='" . $_GET['id_compte'] . "'";
                        $resultat = mysqli_query($connexion, $requete);


                        //si le champ à parcourir est utilisé(pas vide)
                        if (!empty($_FILES['img_compte']['name'])) {
                            $tab_img = pathinfo($_FILES['img_compte']['name']);
                            $extension = $tab_img['extension'];
                            //echo $extension;
                            //on teste si l'extension du fichier est autorisée
                            if ($extension == "png" || $extension == "jpg" || $extension == "gif" || $extension == "webp") {
                                //si le fichier est bien uploadé du local vers le distant
                                if (is_uploaded_file($_FILES['img_compte']['tmp_name'])) {
                                    //on détermine les chemins des 3 images à générer
                                    $chemin_b = "../medias/avatar" . $_GET['id_compte'] . "_b." . $extension;
                                    $chemin_m = "../medias/avatar" . $_GET['id_compte'] . "_m." . $extension;
                                    $chemin_s = "../medias/avatar" . $_GET['id_compte'] . "_s." . $extension;
                                    if (copy($_FILES['img_compte']['tmp_name'], $chemin_b)) {
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
                                        $requete2 = "UPDATE comptes SET img_compte='" . $chemin_s . "' WHERE id_compte='" . $_GET['id_compte'] . "'";
                                        $resultat2 = mysqli_query($connexion, $requete2);
                                        $confirmation = "<p class=\"ok\">Le compte a bien été modifié</p>";
                                    }
                                }
                            } else {
                                $confirmation = "<p class=\"pas_ok\">Ce fichier n'est pas autorisé</p>";
                            }
                        } else {
                            //on confirme l'enregistrement
                            $confirmation = "<p class=\"ok\">Le compte a bien été modifié</p>";
                        }

                    }

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
        $content .= "<div>" . $ligne->statut_compte . "</div>";
        // s'il y a un avatar
        if(!empty($ligne->img_compte)){
            $content.="<div><img src=\"".$ligne->img_compte."\" alt=\"\" /></div>";
        }
        $content .= "<div><a href=\"back.php?action=compte&cas=recharger_compte&id_compte=" . $ligne->id_compte . "\"  ><i style=\"color: goldenrod;\" class=\"fa-solid fa-paintbrush\"></i></a></div>";
        $content .= "<div><a href=\"back.php?action=compte&cas=avertir_compte&id_compte=" . $ligne->id_compte . "\"><i style=\"color: goldenrod;\" class=\"fa-solid fa-x\"></i></a></div>";
        $content .= "</summary>";
        $content .= "<div id=\"nom_compte\">" . $ligne->nom_compte . " " . $ligne->prenom_compte . "</div>";

        $content .= "</details>";
    }

} else {
    //l'utilisateur n'est pas autorisé
    header("Location:../log/login.php");
}
?>