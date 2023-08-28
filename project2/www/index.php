<?php
session_start();
include('fonctions.php');
$bdd = getBDD();
if ($bdd == NULL)
    return NULL;

//Retirer les variables de session si on s'est déconnectés
if (isset($_POST['disconnect']))
{
    session_unset(); //Efface toutes les variables
    session_destroy(); //Retire le cookie
}
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Connexion</title>
    </head>
    <body>
        <h1>Connexion</h1>
        Veuillez entrer vos identifiants
        <form method="post" action="menu.php">
            <p>
                <label><b>Nom d'utilisateur</label>
                <input type="text" name="login" required>
                <br>
                <br>
                <label><b>Mot de passe</label>
                <input type="password" name="pass" required>
                <br>
                <br>
                <input type="submit" value="Se connecter">
            </p>
        </form>
    </body>
</html>