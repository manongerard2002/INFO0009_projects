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
        <title>Disponibilité des CDs</title>
    </head>

    <body>
        
        <h1>Disponibilité des CDs</h1>
        <?php

        $reqOrder = 'ORDER BY';
        $reqColumn = '';
  
        // Nom des colonnes qui seront sélectionnables pour le tri
        $columnName = array(
          '0' => 'Date des événements',
          '1' => 'Numéro de CD',
          '2' => 'Titre de CD',
          '3' => 'Nombre de copies du CD',
          '4' => 'Nombre de réservations du CD pour cette date'
        );

        // Requête pour faire correspondre les colonnes avec les tris possibles
        $columnOrder = array(  
          '0' => 'ORDER BY DATE',
          '1' => 'ORDER BY CD_NUMBER',
          '2' => 'ORDER BY TITLE',
          '3' => 'ORDER BY COPIES',
          '4' => 'ORDER BY BOOKING'
        );

        // Ordre du tri (ascendant ou descendant)
        $columnSorting = array(
          '0' => ' ASC',
          '1' => ' DESC'
        );
    
        echo 'Veuillez sélectionner vos choix de tri dans la liste déroulante ci-dessous puis cliquer sur le bouton "Submit" :';
        ?>
        </br>
        <?php

        // Choix du critère de tri
        echo '<form method="POST" action="disponibilite_CD.php">';
        echo '<select name="column">',"\n";

        // Par défaut, le tableau est trié par date dans l'ordre décroissant.
        for($i = 0; $i < count($columnName); $i++){  
            $selectedColumn='';
            if($i == "DATE"){
                $selectedColumn = ' selected="selected"';
        }
        // Affichage de la ligne
        echo "\t",'<option value="', $i ,'"', $selectedColumn ,'>', $columnName[$i] ,'</option>',"\n";
        }

        echo '</select>';
    
        // Choix de l'ordre d'affichage
        echo '<select name="order">';
        echo '<option value="0" selected="selected"> Ascendant </option>';
        echo '<option value="1"> Descendant </option>';
        echo '</select>';
        echo '<input type="submit" name="orderChoice">';
        echo '</form>';
        ?>
        </br>
        <?php
        // Affichage de la table 

        if (isset($_POST['orderChoice'])){
            $SC = $_POST['column'];
            $SO = $_POST['order'];
            if (($SC == 0 || $SC == 1 || $SC == 2 || $SC == 3 || $SC == 4) && ($SO == 0 || $SO == 1))
            {
            if($SC == 0 and $SO == 0){

                $req = $bdd->prepare('SELECT DATE, CD_NUMBER, TITLE, COPIES, COUNT(*) AS BOOKING FROM 
                (SELECT PLAYLIST, CD_NUMBER, COPIES FROM CONTAINS NATURAL JOIN CD GROUP BY CD_NUMBER, PLAYLIST, COPIES) AS T1
                NATURAL JOIN
                (SELECT PLAYLIST, DATE FROM EVENT) AS T2
                NATURAL JOIN
                (SELECT * FROM CD) AS T3
                GROUP BY DATE, CD_NUMBER, COPIES, TITLE
                ORDER BY DATE
                ');
                $req->execute();
            }
    
            if($SC == 0 and $SO == 1){
    
                $req = $bdd->prepare('SELECT DATE, CD_NUMBER, TITLE, COPIES, COUNT(*) AS BOOKING FROM 
                (SELECT PLAYLIST, CD_NUMBER, COPIES FROM CONTAINS NATURAL JOIN CD GROUP BY CD_NUMBER, PLAYLIST, COPIES) AS T1
                NATURAL JOIN
                (SELECT PLAYLIST, DATE FROM EVENT) AS T2
                NATURAL JOIN
                (SELECT * FROM CD) AS T3
                GROUP BY DATE, CD_NUMBER, COPIES, TITLE
                ORDER BY DATE DESC
                ');
                $req->execute();
            }
    
            if($SC == 1 and $SO == 0){
    
                $req = $bdd->prepare('SELECT DATE, CD_NUMBER, TITLE, COPIES, COUNT(*) AS BOOKING FROM 
                (SELECT PLAYLIST, CD_NUMBER, COPIES FROM CONTAINS NATURAL JOIN CD GROUP BY CD_NUMBER, PLAYLIST, COPIES) AS T1
                 NATURAL JOIN
                (SELECT PLAYLIST, DATE FROM EVENT) AS T2
                NATURAL JOIN
                (SELECT * FROM CD) AS T3
                GROUP BY DATE, CD_NUMBER, COPIES, TITLE
                ORDER BY CD_NUMBER 
                ');
                $req->execute();
            }
    
            if($SC == 1 and $SO == 1){
    
                $req = $bdd->prepare('SELECT DATE, CD_NUMBER, TITLE, COPIES, COUNT(*) AS BOOKING FROM 
                (SELECT PLAYLIST, CD_NUMBER, COPIES FROM CONTAINS NATURAL JOIN CD GROUP BY CD_NUMBER, PLAYLIST, COPIES) AS T1
                NATURAL JOIN
                (SELECT PLAYLIST, DATE FROM EVENT) AS T2
                NATURAL JOIN
                (SELECT * FROM CD) AS T3
                GROUP BY DATE, CD_NUMBER, COPIES, TITLE
                ORDER BY CD_NUMBER DESC
                ');
                $req->execute();
            }
    
            if($SC == 2 and $SO == 0){
    
                $req = $bdd->query('SELECT DATE, CD_NUMBER, TITLE, COPIES, COUNT(*) AS BOOKING FROM 
                (SELECT PLAYLIST, CD_NUMBER, COPIES FROM CONTAINS NATURAL JOIN CD GROUP BY CD_NUMBER, PLAYLIST, COPIES) AS T1
                NATURAL JOIN
                (SELECT PLAYLIST, DATE FROM EVENT) AS T2
                NATURAL JOIN
                (SELECT * FROM CD) AS T3
                GROUP BY DATE, CD_NUMBER, COPIES, TITLE
                ORDER BY TITLE
                ');
                $req->execute();
            }

            if($SC == 2 and $SO == 1){
    
                $req = $bdd->query('SELECT DATE, CD_NUMBER, TITLE, COPIES, COUNT(*) AS BOOKING FROM 
                (SELECT PLAYLIST, CD_NUMBER, COPIES FROM CONTAINS NATURAL JOIN CD GROUP BY CD_NUMBER, PLAYLIST, COPIES) AS T1
                NATURAL JOIN
                (SELECT PLAYLIST, DATE FROM EVENT) AS T2
                NATURAL JOIN
                (SELECT * FROM CD) AS T3
                GROUP BY DATE, CD_NUMBER, COPIES, TITLE
                ORDER BY TITLE DESC
                ');
                $req->execute();
            }

            if($SC == 3 and $SO == 0){
    
                $req = $bdd->query('SELECT DATE, CD_NUMBER, TITLE, COPIES, COUNT(*) AS BOOKING FROM 
                (SELECT PLAYLIST, CD_NUMBER, COPIES FROM CONTAINS NATURAL JOIN CD GROUP BY CD_NUMBER, PLAYLIST, COPIES) AS T1
                NATURAL JOIN
                (SELECT PLAYLIST, DATE FROM EVENT) AS T2
                NATURAL JOIN
                (SELECT * FROM CD) AS T3
                GROUP BY DATE, CD_NUMBER, COPIES, TITLE
                ORDER BY COPIES
                ');
                $req->execute();
            }

            if($SC == 3 and $SO == 1){
    
                $req = $bdd->query('SELECT DATE, CD_NUMBER, TITLE, COPIES, COUNT(*) AS BOOKING FROM 
                (SELECT PLAYLIST, CD_NUMBER, COPIES FROM CONTAINS NATURAL JOIN CD GROUP BY CD_NUMBER, PLAYLIST, COPIES) AS T1
                NATURAL JOIN
                (SELECT PLAYLIST, DATE FROM EVENT) AS T2
                NATURAL JOIN
                (SELECT * FROM CD) AS T3
                GROUP BY DATE, CD_NUMBER, COPIES, TITLE
                ORDER BY COPIES DESC
                ');
                $req->execute();
            }

            if($SC == 4 and $SO == 0){
    
                $req = $bdd->query('SELECT DATE, CD_NUMBER, TITLE, COPIES, COUNT(*) AS BOOKING FROM 
                (SELECT PLAYLIST, CD_NUMBER, COPIES FROM CONTAINS NATURAL JOIN CD GROUP BY CD_NUMBER, PLAYLIST, COPIES) AS T1
                NATURAL JOIN
                (SELECT PLAYLIST, DATE FROM EVENT) AS T2
                NATURAL JOIN
                (SELECT * FROM CD) AS T3
                GROUP BY DATE, CD_NUMBER, COPIES, TITLE
                ORDER BY BOOKING
                ');
                $req->execute();
            }

            if($SC == 4 and $SO == 1){
    
                $req = $bdd->query('SELECT DATE, CD_NUMBER, TITLE, COPIES, COUNT(*) AS BOOKING FROM 
                (SELECT PLAYLIST, CD_NUMBER, COPIES FROM CONTAINS NATURAL JOIN CD GROUP BY CD_NUMBER, PLAYLIST, COPIES) AS T1
                NATURAL JOIN
                (SELECT PLAYLIST, DATE FROM EVENT) AS T2
                NATURAL JOIN
                (SELECT * FROM CD) AS T3
                GROUP BY DATE, CD_NUMBER, COPIES, TITLE
                ORDER BY BOOKING DESC
                ');
                $req->execute();
            }

        echo "<table>";
		echo  "<thead>
                    <tr> 
                        <th> DATE DES EVENEMENTS </th> 
                        <th> NUMERO DE CD </th> 
                        <th> TITRE DE CD </th> 
                        <th> NBRE DE COPIES DU CD</th>  
                        <th> RESERVATIONS POUR CETTE DATE </th>  
                    </tr> 
                </thead>";
		echo "<tbody>";
  
        while ($tuple = $req->fetch()) {
            echo "</tr>";
            echo "<td>" . $tuple['DATE']. "</td>";
			echo "<td>" . $tuple['CD_NUMBER'] . "</td>";
			echo "<td>" . $tuple['TITLE'] . "</td>";
            echo "<td>" . $tuple['COPIES']. "</td>";
            echo "<td>" . $tuple['BOOKING']. "</td>";
            echo "</tr>";
         }
         echo "</tbody>";
         echo "</table>"; 
        }
        }
        ?>
        </br>

        <?php
        bouton_menu();
        bouton_deconnexion();
        ?>
    </body>
</html>