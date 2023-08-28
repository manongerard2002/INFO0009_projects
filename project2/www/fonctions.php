<?php
session_start();

function getBDD()
{
	try
	{
        $bdd = new PDO('mysql:host=ms8db;dbname=group05;charset=utf8', 'group05', 'secret');
	}
	catch(PDOException $e)
	{
		echo "Problème de connexion <br>";
        //bouton_deconnexion();

    	return NULL;
	}

    return $bdd;
}

function bouton_page_connexion()
{
    ?>
    <!-- Formulaire pour aller à la page de connexion -->
    <form method="post" action="index.php">
        <p>
            <input type="hidden" name="disconnect" value="yes">
            <input type="submit" value="Retour à la page connexion">
        </p>
    </form>
    <?php

    return NULL;
}

function bouton_menu()
{
	?>
    <!-- Formulaire pour retourner au menu -->
    <form method="post" action="menu.php">
        <p>
            <input type="submit" value="Retour au menu">
        </p>
    </form>
    <?php

    return NULL;
}

function bouton_deconnexion()
{
    ?>
    <!-- Formulaire pour se déconnecter -->
    <form method="post" action="index.php">
        <p>
            <input type="hidden" name="disconnect" value="yes">
            <input type="submit" value="Déconnexion">
        </p>
    </form>
    <?php
        return NULL;
}

function verification_id($query, $new_id, $bdd)
{
    $id_existante = true;
    $req = $bdd->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
    if($req->execute(array('id' => $new_id)) === FALSE)
    {
        echo "Problème lors de la vérification de l'identifiant.<br><br>";
        $id_existante = NULL;
    }
    else
    {
        $id_existante = ($req->fetchColumn() > 0);
    }
    return $id_existante;
}

function verif_positive_int($int)
{
    if (!is_numeric($int) || $int <= 0)
    {
        echo "L'entrée aurait du être un entier positif.<br><br>";
        return false;
    }
    return true;
}

function verif_varchar_length($varchar, $length)
{
    if (strlen($varchar) > $length)
    {
        echo "La longueur de l'entrée n'aurait pas dû dépassée ".$length.".<br><br>";
        return false;
    }
    return true;
}

function verif_date($date)
{
    $parsedDate = date_parse_from_format('Y-m-d', $date);
    if ($parsedDate['error_count'] > 0 || !checkdate($parsedDate['month'], $parsedDate['day'], $parsedDate['year']))
    {
        echo "L'entrée aurait dû être une date.<br><br>";
        return false;
    }
    return true;
}
?>