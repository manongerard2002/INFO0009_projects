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
            table, th, td {
			border: 1px solid black;
            border-collapse: collapse;
		}
    </style>
    <head>
        <title>Tableau de bord des événements</title>
    </head>
    <body>
        <h1>Tableau de bord des événements</h1>
        <?php
        //Retirer les variables de session si on s'est déconnecté
        if (isset($_POST['disconnect']))
        {
            session_unset();
        }

        $today = date("Y-m-d"); 
        $fee = 1500;
        echo "<p>"."Nous sommes le " .$today."</p>";
        
		echo "<table>";
		echo  "<thead>
                    <tr> 
                        <th> STATUT </th> 
                        <th> DATE </th> 
                        <th> NOM </th> 
                        <th> COÛT TOTAL </th>  
                        <th> THEME </th>
                        <th> PLAYLIST </th>
                    </tr> 
                </thead>";
		echo "<tbody>";

        $req = $bdd->prepare('SELECT DATE, NAME, RENTAL_FEE + :fee AS TOTAL_FEE, THEME, PLAYLIST
        FROM EVENT WHERE DATE > :today ORDER BY DATE DESC, NAME');
        $req->execute(array('today' => $today, 'fee' => $fee));
        while ($tuple = $req->fetch()) {
            echo "</tr>";
            echo "<td>" . "FUTUR". "</td>";
			echo "<td>" . $tuple['DATE'] . "</td>";
			echo "<td>" . $tuple['NAME'] . "</td>";
            if( $tuple['TOTAL_FEE']){
                echo "<td>" . $tuple['TOTAL_FEE'] . " €" ."</td>";
            }
            else{
                echo "<td>" . $fee. " €" ."</td>";
            }
            echo "<td>" . $tuple['THEME'] . "</td>";
            echo "<td>" . $tuple['PLAYLIST'] . "</td>";
            echo "</tr>";
        }
        $req = $bdd->prepare('SELECT DATE, NAME, RENTAL_FEE  + :fee AS TOTAL_FEE, THEME, PLAYLIST
        FROM EVENT WHERE DATE = :today ORDER BY DATE DESC, NAME');
        $req->execute(array('today' => $today, 'fee' => $fee));
        while ($tuple = $req->fetch()) {
            echo "</tr>";
            echo "<td>" . "PRESENT" . "</td>";
			echo "<td>" . $tuple['DATE'] . "</td>";
			echo "<td>" . $tuple['NAME'] . "</td>";
            if( $tuple['TOTAL_FEE']){
                echo "<td>" . $tuple['TOTAL_FEE'] . " €" ."</td>";
            }
            else{
                echo "<td>" . $fee. " €" ."</td>";
            }
            echo "<td>" . $tuple['THEME'] . "</td>";
            echo "<td>" . $tuple['PLAYLIST'] . "</td>";
            echo "</tr>";
        }
        $req = $bdd->prepare('SELECT DATE, NAME, RENTAL_FEE  + :fee AS TOTAL_FEE, THEME, PLAYLIST
        FROM EVENT WHERE DATE < :today ORDER BY DATE DESC, NAME');
        $req->execute(array('today' => $today, 'fee' => $fee));
        while ($tuple = $req->fetch()) {
            echo "</tr>";
            echo "<td>" . "PASSE" . "</td>";
			echo "<td>" . $tuple['DATE'] . "</td>";
			echo "<td>" . $tuple['NAME'] . "</td>";
            if( $tuple['TOTAL_FEE']){
                echo "<td>" . $tuple['TOTAL_FEE'] . " €" ."</td>";
            }
            else{
                echo "<td>" . $fee. " €" ."</td>";
            };
            echo "<td>" . $tuple['THEME'] . "</td>";
            echo "<td>" . $tuple['PLAYLIST'] . "</td>";
            echo "</tr>";
        }
		echo "</tbody>";
		echo "</table>";
        ?>
    </br>

         <!-- Formulaire pour retourner au menu -->
         <form method="post" action="menu.php">
            <p>
                <input type="submit" value="Retour au menu">
            </p>
        </form>
        
        <!-- Formulaire pour se déconnecter -->
        <form method="post" action="index.php">
            <p>
                <input type="hidden" name="disconnect" value="yes">
                <input type="submit" value="Déconnexion">
            </p>
        </form>
    </body>
</html>