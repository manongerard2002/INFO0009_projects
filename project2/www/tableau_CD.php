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
        <title>Tableau de bord des CDs</title>
    </head>

    <body>

        <h1>Tableau de bord des CDs</h1>
        <?php
        //Retirer les variables de session si on s'est déconnecté
        if (isset($_POST['disconnect']))
        {
            session_unset();
        }

		echo "<table>";
		echo  "<thead>
                    <tr> 
                        <th> CD# </th> 
                        <th> DUREE TOTALE </th> 
                        <th> DUREE MOYENNE </th>
                        <th> DUREE MIN </th>  
                        <th> DUREE MAX </th>  
                        <th> PLAYLISTS </th>  
                        <th> GENRES </th>  

                    </tr> 
                </thead>";
		echo "<tbody>";
        
        $req = 'SELECT * FROM
        (SELECT CD_NUMBER, COUNT(*) AS OCCURENCE
        FROM CONTAINS
        GROUP BY CD_NUMBER) AS T1
        NATURAL JOIN
        (SELECT SONG.CD_NUMBER, 
        SUM(TIME_TO_SEC(SONG.DURATION)) AS TOTAL, 
        AVG(TIME_TO_SEC(SONG.DURATION)) AS MOYENNE, 
        MIN(SONG.DURATION) AS MINIMUM, 
        MAX(SONG.DURATION) AS MAXIMUM 
        FROM SONG JOIN CD ON CD.CD_NUMBER = SONG.CD_NUMBER GROUP BY SONG.CD_NUMBER) AS T2
        ';  

        $reqToBdd = $bdd->prepare($req);
        $reqToBdd->execute();

        while ($tuple = $reqToBdd->fetch()) {
            echo "</tr>";
            echo "<td>" . $tuple['CD_NUMBER'] . "</td>";
			echo "<td>" . gmdate("H:i:s",$tuple['TOTAL']) . "</td>";
            echo "<td>" . gmdate("H:i:s",$tuple['MOYENNE']) . "</td>";
            echo "<td>" . $tuple['MINIMUM'] . "</td>";
            echo "<td>" . $tuple['MAXIMUM'] . "</td>";
            echo "<td>" . $tuple['OCCURENCE'] . "</td>";
            
            $req2 = 'SELECT GROUP_CONCAT(GENRES ORDER BY GENRES SEPARATOR ", ") AS LISTE FROM
            (SELECT CD_NUMBER
            FROM CONTAINS
            GROUP BY CD_NUMBER) AS T1
            NATURAL JOIN
            (SELECT SONG.CD_NUMBER, SPECIALIZES.GENRE AS GENRES
            FROM SONG JOIN SPECIALIZES
            ON SONG.GENRE = SPECIALIZES.GENRE OR SONG.GENRE = SPECIALIZES.SUBGENRE
            UNION
            SELECT SONG.CD_NUMBER, SPECIALIZES.SUBGENRE AS GENRES
            FROM SONG JOIN SPECIALIZES
            ON SONG.GENRE = SPECIALIZES.GENRE OR SONG.GENRE = SPECIALIZES.SUBGENRE
            UNION
            SELECT SONG.CD_NUMBER, SONG.GENRE AS GENRES
            FROM SONG ) AS T2
            WHERE CD_NUMBER = '. $tuple['CD_NUMBER']; 
            
            $reqToBdd2 = $bdd->prepare($req2);
            $reqToBdd2->execute();
            $tuple2 = $reqToBdd2->fetch();

            echo "<td>" . $tuple2['LISTE'] . "</td>";

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