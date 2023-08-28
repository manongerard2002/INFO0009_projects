<?php
session_start();
include('fonctions.php');
$bdd = getBDD();
if ($bdd == NULL)
    return NULL;
if (!isset($_POST['login']) && !isset($_SESSION['login']))
{
    echo "Cette page n'est pas censé être accessible sans connexion !";
    bouton_page_connexion();
    return NULL;
}
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Menu</title>
    </head>
    <body>
        <?php
        if (isset($_POST["login"]) && isset($_POST["pass"]))
        {
            $query = 'SELECT * FROM USERS WHERE Login = :login AND Pass = :pass';
            $req = $bdd->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
            if($req->execute(array('login' => $_POST["login"], 'pass' => $_POST["pass"])) === FALSE)
            {
                echo "Problème dans l'insertion <br><br>";
            }
            $tuple = $req->fetch();
            if ($tuple)
            {
                $_SESSION['login'] = $tuple["Login"];
                $_SESSION['pass'] = $tuple["Pass"];
            }
            else
            {
                echo "Votre login/mot de passe est incorrect<br><br>";
                bouton_page_connexion();
            }
        }

        if (isset($_SESSION['login']))
        {
            ?>
            <h1>Menu</h1>
            <ul>
                <li><a href="./affichage_contraintes.php">Recherche et sélection dans les tables</a></li>
                <li><a href="./modif_location.php">Modification des lieux</a></li>
                <li><a href="./modif_CD.php">Modification des CDs</a></li>
                <li><a href="./tableau_event.php">Tableau de bord des événements</a></li>
                <li><a href="./disponibilite_CD.php">Disponibilité des CDs</a></li>
                <li><a href="./modif_event.php">Modification des événements</a></li>
                <li><a href="./tableau_CD.php">Tableau de bord des CDs</a></li>
            </ul>
            <?php
            bouton_deconnexion();
        }
        ?>
    </body>
</html>