<?php
session_start();
include('fonctions.php');
$bdd = getBDD();
if ($bdd == NULL)
    return NULL;
if (!isset($_SESSION['login']))
{
    echo "Cette page n'est pas censé être accessible sans connexion !";
    bouton_page_connexion();
    return NULL;
}
?>
<!DOCTYPE html>
<html>
    <style>
        h2 {
            font-size: 19px;
        }
    </style>
    <head>
        <title>Modification des lieux</title>
    </head>
    <body>
        <h1>Modification des lieux</h1>
        <h2>Ajout de nouveaux lieux</h2>
        <?php
        // ajouter de nouveaux lieux : ID sera donné par default si non rempli
        if (isset($_POST["STREET"]))
        {
            // Verification que les champs obligatoires sont remplis
            if(empty($_POST["STREET"]) || empty($_POST["CITY"]) || empty($_POST["POSTAL_CODE"]) || empty($_POST["COUNTRY"]))
            {
                echo "Aucun lieu n'a été modifié car tous les champs requis n'ont pas été rempli.<br><br>";
            }
            else
            {
                // Variables pour l'affichage
                $id = $_POST['ID'];
                $street = htmlspecialchars($_POST['STREET']);
                $city = htmlspecialchars($_POST['CITY']);
                $postal_code = $_POST['POSTAL_CODE'];
                $country = htmlspecialchars($_POST['COUNTRY']);
                $comment = htmlspecialchars($_POST['COMMENT']);

                $query = 'INSERT INTO `LOCATION` (';
                $value = ') VALUES (';
                $parameters = array();
                $message = "Le lieu '";
                $id_existante = false;
                // Vérification des limites de types des entrées
                $entree_correct = verif_varchar_length($_POST['STREET'], 50) && verif_varchar_length($_POST['CITY'], 50) && verif_varchar_length($_POST['POSTAL_CODE'], 10) && verif_varchar_length($_POST['COUNTRY'], 50);
                // Gestion des champs facultatifs
                if (!empty($_POST['ID']))
                {
                    $id_existante = verification_id('SELECT COUNT(*) FROM `LOCATION` WHERE `ID` = :id', $_POST['ID'], $bdd);
                    $entree_correct = $entree_correct && verif_positive_int($_POST['ID']);
                    $query .= '`ID`, ';
                    $value .= ':ID, ';
                    $parameters['ID'] = $_POST["ID"];
                    $message .= $id." : ";
                }
                $query .= '`STREET`, `CITY`, `POSTAL_CODE`, `COUNTRY`';
                $value .= ':STREET, :CITY, :POSTAL_CODE, :COUNTRY';
                $parameters['STREET'] = $_POST["STREET"];
                $entree_correct = $entree_correct && verif_varchar_length($_POST['STREET'], 50);
                $parameters['CITY'] = $_POST["CITY"];
                $entree_correct = $entree_correct && verif_varchar_length($_POST['CITY'], 50);
                $parameters['POSTAL_CODE'] = $_POST["POSTAL_CODE"];
                $entree_correct = $entree_correct && verif_varchar_length($_POST['POSTAL_CODE'], 10);
                $parameters['COUNTRY'] = $_POST["COUNTRY"];
                $entree_correct = $entree_correct && verif_varchar_length($_POST['COUNTRY'], 50);
                $message .= $street.", ".$postal_code." ".$city.", ".$country;
                if (!empty($_POST['COMMENT']))
                {
                    $entree_correct = $entree_correct && verif_varchar_length($_POST['COMMENT'], 30);
                    $query .= ', `COMMENT`';
                    $value .= ', :COMMENT';
                    $parameters['COMMENT'] = $_POST['COMMENT'];
                    $message .= " : \"".$comment."\"";
                }
                $message .= "' à été ajouté.<br><br>";
                
                if ($id_existante)
                {
                    echo "L'identifiant ".$_POST['id']." correspond déjà à un lieu existant.<br>";
                    echo "Aucun lieu n'a été ajouté.<br><br>";
                }
                else if ($id_existante !== NULL)
                {
                    if ($entree_correct)
                    {
                        $req = $bdd->prepare($query.$value.')', array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
                        if($req->execute($parameters) === FALSE)
                        {
                            echo "Problème dans l'insertion.<br>";
                            echo "Aucun lieu n'a été ajouté.<br><br>";
                        }
                        else
                        {
                            echo $message;
                        }
                    }
                }
            }
        }
        ?>
        <form method="post" action="modif_location.php">
            <p>
                <label><b>Identifiant</label>
                <input type="number" name="ID" min="0" max="2147483647">
                <br>
                <br>
                <label><b>Rue</label>
                <input type="text" name="STREET" maxlength="50" required>
                <br>
                <br>
                <label><b>Ville</label>
                <input type="text" name="CITY" maxlength="50" required>
                <br>
                <br>
                <label><b>Code Postal</label>
                <input type="text" name="POSTAL_CODE" maxlength="10" required>
                <br>
                <br>
                <label><b>Pays</label>
                <input type="text" name="COUNTRY" maxlength="50" required>
                <br>
                <br>
                <label><b>Commentaire</label>
                <input type="text" name="COMMENT" maxlength="30">
                <br>
                <br>
                <input type="submit" value="Ajouter">
            </p>
        </form>
        
        <h2>Sélection d'un lieu existant</h2>
        <?php
        // Modification d'un lieu
        if (isset($_POST['modification_id']))
        {
            if(!empty($_POST['comment']) && isset($_POST['comment_null']))
            {
                echo "Vous ne pouvez pas completer un champ et aussi le vider.<br><br>";
            }
            else
            {
                // Verification de l'entrée modification_id
                $id_modif_existante = verification_id('SELECT `ID` FROM `LOCATION` WHERE `ID` = :id', $_POST['modification_id'], $bdd);

                if (!$id_modif_existante)
                {
                    echo "L'identifiant ".$_POST['modification_id']." ne correspond à aucun lieu.<br>";
                    echo "Aucun lieu n'a été modifié.<br><br>";
                }
                else if ($id_modif_existante !== NULL)
                {
                    $query = 'UPDATE `LOCATION` SET ';
                    $parameters = array();
                    $entree_correct = true;
                    $entree_NULL = false;
                    // Gestion des champs facultatifs
                    if (!empty($_POST['id']))
                    {
                        $entree_correct = $entree_correct && verif_positive_int($_POST['id']);
                        $query .= '`ID` = :id, ';
                        $parameters['id'] = $_POST['id'];
                        $id_existante = false;
                        // Vérifier qu'id est differente de celles déjà dans la BD, sauf si même qu'avant
                        if ($_POST['id'] != $_POST['modification_id'])
                        {
                            $id_existante = verification_id('SELECT COUNT(*) FROM `LOCATION` WHERE `ID` = :id', $_POST['id'], $bdd);
                        }
                        if ($id_existante)
                        {
                            echo "L'identifiant ".$_POST['id']." correspond déjà à un lieu existant.<br>";
                        }
                        elseif ($id_existante === NULL)
                        {
                            $entree_NULL = true;
                        }
                        $entree_correct = $entree_correct && !$id_existante;
                    }
                    if (!empty($_POST['street']))
                    {
                        $entree_correct = $entree_correct && verif_varchar_length($_POST['street'], 50);
                        $query .= '`STREET` = :street, ';
                        $parameters['street'] = $_POST['street'];
                    }
                    if (!empty($_POST['city']))
                    {
                        $entree_correct = $entree_correct && verif_varchar_length($_POST['city'], 50);
                        $query .= '`CITY` = :city, ';
                        $parameters['city'] = $_POST['city'];
                    }
                    if (!empty($_POST['postal_code']))
                    {
                        $entree_correct = $entree_correct && verif_varchar_length($_POST['postal_code'], 10);
                        $query .= '`POSTAL_CODE` = :postal_code, ';
                        $parameters['postal_code'] = $_POST['postal_code'];
                    }
                    if (!empty($_POST['country']))
                    {
                        $entree_correct = $entree_correct && verif_varchar_length($_POST['country'], 50);
                        $query .= '`COUNTRY` = :country, ';
                        $parameters['country'] = $_POST['country'];
                    }
                    if (!empty($_POST['comment']))
                    {
                        $entree_correct = $entree_correct && verif_varchar_length($_POST['comment'], 30);
                        $query .= '`COMMENT` = :comment, ';
                        $parameters['comment'] = $_POST['comment'];
                    }
                    else if (!empty($_POST['comment_null']))
                    {
                        $query .= '`COMMENT` = :comment, ';
                        $parameters['comment'] =  NULL;
                    }
                    // suppresion du dernier ", "
                    $query = rtrim($query, ", ");

                    $query .= ' WHERE `ID` = :modification_id';
                    $parameters['modification_id'] = $_POST['modification_id'];

                    if (!$entree_NULL && $entree_correct)
                    {
                        $req = $bdd->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
                        if($req->execute($parameters) === FALSE)
                        {
                            echo "Problème dans la modification.<br>";
                            echo "Aucun lieu n'a été modifié.<br><br>";
                        }
                        else
                        {
                            echo "Le lieu a été modifié.<br><br>";
                        }
                    }
                    else
                    {
                        echo "Aucun lieu n'a été modifié.<br><br>";
                    }
                }
            }
        }
        // Suppression d'un lieu
        if (isset($_POST['suppression_id']))
        {
            // Vérification que suppression_id est un lieu
            $id_modif_existante = verification_id('SELECT `ID` FROM `LOCATION` WHERE `ID` = :id', $_POST['suppression_id'], $bdd);

            if (!$id_modif_existante)
            {
                echo "L'identifiant ".$_POST['suppression_id']." ne correspond à aucun lieu.<br>";
                echo "Aucun lieu n'a été supprimé.<br><br>";
            }
            else if ($id_modif_existante !== NULL)
            {
                // Vérification que suppression_id n'est pas réservé
                $id_existante = verification_id('SELECT `LOCATION` FROM `EVENT` WHERE `LOCATION` = :id', $_POST['suppression_id'], $bdd);

                if ($id_existante)
                {
                    echo "L'identifiant ".$_POST['suppression_id']." correspond à un lieu réservé.<br>";
                    echo "Aucun lieu n'a été supprimé.<br><br>";
                }
                else if ($id_existante !== NULL)
                {
                    $query = 'DELETE FROM `LOCATION` WHERE `ID` = :id';
                    $req = $bdd->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
                    if($req->execute(array('id' => $_POST["suppression_id"])) === FALSE)
                    {
                        echo "Problème dans la suppression.<br><br>";
                    }
                    else
                    {
                        echo "Le lieu a été supprimé.<br><br>";
                    }
                }
            }
        }

        // Permettre la selection
        $query = 'SELECT * FROM `LOCATION`';
        $req = $bdd->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        if($req->execute() === FALSE)
        {
            echo "Problème dans l'affichage des lieux.<br><br>";
        }
        ?>

        <form method='post' action="modif_location.php">
        <label>Sélectionnez un lieu :</label>
        <select name='lieu'>
        <?php
        while ($tuple = $req->fetch(PDO::FETCH_ASSOC))
        {
            // Variables pour l'affichage
            $id = $tuple['ID'];
            $street = htmlspecialchars($tuple['STREET']);
            $city = htmlspecialchars($tuple['CITY']);
            $postal_code = $tuple['POSTAL_CODE'];
            $country = htmlspecialchars($tuple['COUNTRY']);
            $message = $id." : ".$street.", ".$postal_code." ".$city.", ".$country;
            if (!empty($tuple['COMMENT']))
            {
                $comment = htmlspecialchars($tuple['COMMENT']);
                $message .= " : \"".$comment."\"";
            }
            echo "<option value='".$id."'>".$message."</option>";
        }
        ?>
            </select>
            <input type="submit" name='submit' value="Choisir">
        </form>
        <?php
        // Permettre la modification et suppression
        if (isset($_POST['lieu']))
        {
            // Vérification de lieu
            $id_existante = verification_id('SELECT `ID` FROM `LOCATION` WHERE `ID` = :id', $_POST['lieu'], $bdd);
                
            if (!$id_existante)
            {
                echo "L'identifiant ".$_POST['lieu']." ne correspond pas à un lieu.<br>";
                echo "Vous pouvez pas compléter ou mettre à jour ses informations.<br><br>";
            }
            else if ($id_existante !== NULL)
            {
                $query_lieu = 'SELECT * FROM `LOCATION` WHERE `ID` = :id';
                $req_lieu = $bdd->prepare($query_lieu, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
                if($req_lieu->execute(array('id' => $_POST["lieu"])) === FALSE)
                {
                    echo "Problème dans l'affichage du lieu choisi.<br><br>";
                }
                $tuple_lieu = $req_lieu->fetch();

                // Variables pour l'affichage
                $id = $tuple_lieu['ID'];
                $street = htmlspecialchars($tuple_lieu['STREET']);
                $city = htmlspecialchars($tuple_lieu['CITY']);
                $postal_code = $tuple_lieu['POSTAL_CODE'];
                $country = htmlspecialchars($tuple_lieu['COUNTRY']);
                $message = "Vous avez sélectionné la location suivante : ".$id." : ".$street.", ".$postal_code." ".$city.", ".$country;
                if (!empty($tuple_lieu['COMMENT']))
                {
                    $comment = htmlspecialchars($tuple_lieu['COMMENT']);
                    $message .= " : \"".$comment."\"";
                }
                echo "<option value='".$tuple['ID']."'>".$message."</option>";
                ?>

                <p>Entrez les champs que vous souhaitez modifier</p>
                <form method="post" action="modif_location.php">
                    <input type="hidden" name="modification_id" value="<?php echo $_POST['lieu']; ?>">
                    <p>
                        <label><b>Identifiant</label>
                        <input type="number" name="id" min="0" max="2147483647">
                        <br>
                        <br>
                        <label><b>Rue</label>
                        <input type="text" name="street" maxlength="50">
                        <br>
                        <br>
                        <label><b>Ville</label>
                        <input type="text" name="city" maxlength="50">
                        <br>
                        <br>
                        <label><b>Code Postal</label>
                        <input type="text" name="postal_code" maxlength="10">
                        <br>
                        <br>
                        <label><b>Pays</label>
                        <input type="text" name="country" maxlength="50">
                        <br>
                        <br>
                        <label><b>Commentaire</label>
                        <input type="text" name="comment" maxlength="30">
                        vider le champ <input type="checkbox" name="comment_null" value ="default">
                        <br>
                        <br>
                        <input type="submit" value="Modifier">
                    </p>
                </form>

                <form method="post" action="modif_location.php">
                    <p>Vous pouvez supprimer le lieu :</p>
                    <input type="hidden" name="suppression_id" value="<?php echo $_POST['lieu']; ?>">
                    <p>
                        <input type="submit" value="Supprimer">
                    </p>
                </form>

            <?php
            }
        }
        bouton_menu();
        bouton_deconnexion();
        ?>
    </body>
</html>