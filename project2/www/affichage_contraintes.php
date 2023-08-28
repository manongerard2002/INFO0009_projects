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
            h2 {
                font-size: 19px;
            }
    </style>

    
   
    <head>
        <title>Affichage des tables avec contraintes</title>
    </head>
    <body>
        <h1>Affichage des tables avec contraintes</h1>
        <?php
        //Retirer les variables de session si on s'est déconnectés
        if (isset($_POST['disconnect']))
        {
            session_unset();
        }
        ?>

        <h2> Cliquez sur la table afin de faire une recherche </h2>
        <?php  

        $req = $bdd->query('SELECT   TABLE_NAME FROM   information_schema.TABLES WHERE   TABLE_TYPE=\'BASE TABLE \' AND TABLE_NAME != \'users \' AND TABLE_SCHEMA =\'group05\';');                
        while ($tuple = $req->fetch()){
            if($tuple[0] == 'CLIENT' || $tuple[0] == 'EMPLOYEE' || $tuple[0] == 'LOCATION' || $tuple[0] == 'EVENT' || $tuple[0] == 'SONG' || $tuple[0]=='CD')
                echo '<li><a href="affichage_contraintes.php?table='.$tuple[0].'">'.$tuple[0].'</a></li>';
        }

        if (isset($_GET['table'])){

            if($_GET['table'] == 'CLIENT'){
                echo "La table sélectionnée est la table CLIENT, entrez les champs que vous désirez rechercher";?> <br>

                <form method="post" action="affichage_contraintes.php">
                    <input type="number" name="CLIENT_NUMBER" placeholder="Numéro de client"  min="0" >
                    <input type="text" name="FIRST_NAME" placeholder="Prénom" >
                    <input type="text" name="LAST_NAME" placeholder="Nom" >
                    <input type="text" name="EMAIL_ADDRESS" placeholder="Adresse e-mail" >
                    <input type="text" name="PHONE_NUMBER" placeholder="Numéro de téléphone">
                    <input type="hidden" name="table" value="CLIENT">
                    <input type="submit" value="Rechercher">
                </form>
               
            <?php
            }
       
            if($_GET['table'] == 'EMPLOYEE'){
                echo "La table sélectionnée est la table EMPLOYEE, entrez les champs que vous désirez rechercher";?><br>
            
                <form method="post" action="affichage_contraintes.php">       
                    <input type="number" name="ID" placeholder="Id"  min="0">
                    <input type="text" name="FIRSTNAME" placeholder="Prénom" >
                    <input type="text" name="LASTNAME" placeholder="Nom" >
                    <input type="hidden" name="table" value="EMPLOYEE">
                    <input type="submit" value="Rechercher">
                </form>
            <?php 
            }

            if($_GET['table'] == 'LOCATION'){
                echo "La table sélectionnée est la table LOCATION, entrez les champs que vous désirez rechercher";?><br>
                
                <form method="post" action="affichage_contraintes.php">       
                    <input type="number" name="ID" placeholder="Id"  min="0">
                    <input type="text" name="STREET" placeholder="Rue" >
                    <input type="text" name="CITY" placeholder="Ville" >
                    <input type="number" name="POSTAL_CODE" placeholder="Code postal"  min="0">
                    <input type="text" name="COUNTRY" placeholder="Pays" >
                    <input type="text" name="COMMENT" placeholder="Commentaire" >
                    <input type="hidden" name="table" value="LOCATION">
                    <input type="submit" value="Rechercher">
                </form>
           <?php 
            }

            if($_GET['table'] == 'EVENT'){
                echo "La table sélectionnée est la table EVENT, entrez les champs que vous désirez rechercher";?><br>
                
                <form method="post" action="affichage_contraintes.php">       
                    <input type="number" name="ID" placeholder="Id"  min="0">
                    <input type="text" name="NAME" placeholder="Nom" >
                    <input type="date" name="DATE" placeholder="Date" >
                    <input type="text" name="DESCRIPTION" placeholder="Description">
                    <input type="number" name="CLIENT" placeholder="Client"  min="0">
                    <input type="number" name="MANAGER" placeholder="Manager"  min="0">
                    <input type="number" name="EVENT_PLANNER" placeholder="Planificateur"  min="0">
                    <input type="number" name="DJ" placeholder="DJ"  min="0">
                    <input type="text" name="THEME" placeholder="Thème">
                    <input type="text" name="TYPE" placeholder="Type">
                    <input type="number" name="LOCATION" placeholder="Lieu"  min="0">
                    <input type="number" name="RENTAL_FEE" placeholder="Frais de location">
                    <input type="text" name="PLAYLIST" placeholder="Nom playlist">
                    <input type="hidden" name="table" value="EVENT">
                    <input type="submit" value="Rechercher">
                </form>
            <?php
            }

            if($_GET['table'] == 'SONG'){
                echo "La table sélectionnée est la table SONG, entrez les champs que vous désirez rechercher";?><br>
            
                <form method="post" action="affichage_contraintes.php">  

                    <input type="number" name="CD_NUMBER" placeholder="Numéro du CD"  min="0">
                    <input type="number" name="TRACK_NUMBER" placeholder="Numéro de la chanson" min="0">
                    <input type="text" name="TITLE" placeholder="Titre" >
                    <input type="text" name="ARTIST" placeholder="Artiste" >
                    <input type="time" name="DURATION" step="1"  placeholder="Durée" >
                    <input type="text" name="GENRE" placeholder="Genre" >
                    <input type="hidden" name="table" value="SONG">
                    <input type="submit" value="Rechercher"> 
                </form>
            <?php
            }

            if($_GET['table'] == 'CD'){
                echo "La table sélectionnée est la table CD, entrez les champs que vous désirez rechercher";?> <br>
        
                <form method="post" action="affichage_contraintes.php">       
                    <input type="number" name="CD_NUMBER" placeholder="Numéro du CD" >
                    <input type="text" name="TITLE" placeholder="Titre" >
                    <input type="text" name="PRODUCER" placeholder="Producteur" >
                    <input type="number" name="YEAR" step="1"  placeholder="Année"  min="0" >
                    <input type="number" name="COPIES" placeholder="Copies" >
                    <input type="hidden" name="table" value="CD">
                    <input type="submit" value="Rechercher"> 
                </form>
            <?php
            }
        }
       
        if(isset($_POST['table']))
        {

            if($_POST['table'] == 'CLIENT'){

                $query = "SELECT * FROM CLIENT WHERE FIRST_NAME LIKE :FIRST_NAME AND LAST_NAME LIKE :LAST_NAME AND EMAIL_ADDRESS LIKE :EMAIL_ADDRESS AND PHONE_NUMBER LIKE :PHONE_NUMBER "; //  AND CLIENT_NUMBER = :CLIENT_NUMBER";
                $param = array();

                $param['FIRST_NAME']= '%'.$_POST['FIRST_NAME'].'%';
                $param['LAST_NAME']= '%'.$_POST['LAST_NAME'].'%';
                $param['EMAIL_ADDRESS']= '%'.$_POST['EMAIL_ADDRESS'].'%';
                $param['PHONE_NUMBER']= '%'.$_POST['PHONE_NUMBER'].'%';
               
                if(!empty($_POST["CLIENT_NUMBER"])){
                    $query .= " AND `CLIENT_NUMBER` = :CLIENT_NUMBER";
                    $param['CLIENT_NUMBER']= $_POST['CLIENT_NUMBER'];
                } 

                $req = $bdd->prepare($query,  array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY)) ; 
                if($req->execute($param)){

                    echo "La recherche a donnée la table suivante : ";
            
                    echo "<table>";
		            echo  "<thead>
                        <tr> 
                            <th> NUMERO DE CLIENT </th> 
                            <th> PRENOM </th> 
                            <th> NOM </th> 
                            <th> ADRESSE EMAIL </th>
                            <th> NUMERO DE TELEPHONE </th>   
                        </tr> 
                        </thead>";
		            echo "<tbody>";

                    while ($tuple = $req->fetch())
                    {
                        echo "<tr>";
                        echo "<td>" . $tuple['CLIENT_NUMBER'] . "</td>";
                        echo "<td>" . $tuple['FIRST_NAME'] . "</td>";
                        echo "<td>" . $tuple['LAST_NAME'] . "</td>";
                        echo "<td>" . $tuple['EMAIL_ADDRESS'] . "</td>";
                        echo "<td>" . $tuple['PHONE_NUMBER'] . "</td>";
                        echo "</tr>";
                    }
                    echo "</tbody>";
                    echo "</table>";
                }
            }

            if($_POST['table'] == 'EMPLOYEE'){

                $query = "SELECT * FROM EMPLOYEE WHERE FIRSTNAME LIKE :FIRSTNAME AND LASTNAME LIKE :LASTNAME ";
                $param = array();
                $param['FIRSTNAME']= '%'.$_POST['FIRSTNAME'].'%';
                $param['LASTNAME']= '%'.$_POST['LASTNAME'].'%';

                if(!empty($_POST["ID"])){
                    $query .= " AND `ID` = :ID";
                    $param['ID'] = $_POST['ID'];    
                } 

                $req = $bdd->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY)) ; 
                
                if($req->execute($param)){
                    echo "La recherche a donnée la table suivante : ";
                    echo "<table>";
                    echo  "<thead>
                        <tr> 
                            <th> ID </th> 
                            <th> PRENOM </th> 
                            <th> NOM </th>   
                        </tr> 
                        </thead>";
                    echo "<tbody>";

                    while ($tuple = $req->fetch())
                    {
                        echo "<tr>";
                        echo "<td>" . $tuple['ID'] . "</td>";
                        echo "<td>" . $tuple['FIRSTNAME'] . "</td>";
                        echo "<td>" . $tuple['LASTNAME'] . "</td>";
                        echo "</tr>";
                    }
                    echo "</tbody>";
                    echo "</table>";
            }
        }

        if($_POST['table'] == 'LOCATION'){
                
                $query = "SELECT * FROM LOCATION WHERE STREET LIKE :STREET AND CITY LIKE :CITY AND COUNTRY LIKE :COUNTRY  AND POSTAL_CODE LIKE :POSTAL_CODE";
                $param = array();

                $param['STREET'] ='%'.$_POST['STREET'].'%';
                $param['CITY'] ='%'.$_POST['CITY'].'%';
                $param['COUNTRY'] ='%'.$_POST['COUNTRY'].'%';
                $param['POSTAL_CODE'] = '%'.$_POST['POSTAL_CODE'].'%';

                //Pour gérer le cas où un lieu n'a pas de commentaire 
                if(!empty($_POST["COMMENT"])){
                    $query .= " AND `COMMENT` LIKE :COMMENT";
                    $param['COMMENT'] = '%'.$_POST['COMMENT'].'%';
                }

                if(!empty($_POST["ID"])){
                    $query .= " AND `ID` = :ID";
                    $param['ID'] =$_POST['ID'];
                }
               
            
    
                $req = $bdd->prepare($query,array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY)) ; 

                if($req->execute($param)){
                    
                    echo "La recherche a donnée la table suivante : ";
                    echo "<table>";
                    echo  "<thead>
                        <tr> 
                            <th> ID </th> 
                            <th> RUE </th> 
                            <th> VILLE </th>  
                            <th> CODE POSTAL </th>  
                            <th> PAYS </th>  
                            <th> COMMENTAIRE </th>   
                        </tr> 
                        </thead>";
                    echo "<tbody>";

                    while ($tuple = $req->fetch())
                    {
                        echo "<tr>";
                        echo "<td>" . $tuple['ID'] . "</td>";
                        echo "<td>" . $tuple['STREET'] . "</td>";
                        echo "<td>" . $tuple['CITY'] . "</td>";
                        echo "<td>" . $tuple['POSTAL_CODE'] . "</td>";
                        echo "<td>" . $tuple['COUNTRY'] . "</td>";
                        echo "<td>" . $tuple['COMMENT'] . "</td>";
                        echo "</tr>";
                    }
                    echo "</tbody>";
                    echo "</table>";
            }
        }

    
        if($_POST['table'] == 'EVENT'){

                
            $query = "SELECT * FROM EVENT WHERE NAME LIKE :NAME AND THEME LIKE :THEME AND TYPE LIKE :TYPE ";
            
            $param=array();

            $param['NAME'] = '%'.$_POST['NAME'].'%';
            $param['THEME'] = '%'.$_POST['THEME'].'%' ;
            $param['TYPE'] = '%'.$_POST['TYPE'].'%' ;
    
            //Permet de gérer le cas où le l'événement n'a pas de description
            if(!empty($_POST["DESCRIPTION"])){
                $query .= " AND `DESCRIPTION` LIKE :DESCRIPTION";
                $param['DESCRIPTION'] = '%'.$_POST['DESCRIPTION'].'%';
            }

            //Permet de gérer le cas où le l'événement n'a pas de playlist associé
            if(!empty($_POST["PLAYLIST"])){
                echo "ici";
                $query .= " AND `PLAYLIST` LIKE :PLAYLIST";
                $param['PLAYLIST'] = '%'.$_POST['PLAYLIST'].'%';
            }

            if(!empty($_POST["ID"])){
                $query .= " AND `ID` = :ID";
                $param['ID'] = $_POST['ID']; 
            } 

            if(!empty($_POST["DATE"])){
                $query .= " AND `DATE` = :DATE";
                $param['DATE'] = $_POST['DATE'];
            } 

            if(!empty($_POST["CLIENT"])){
                $query .= " AND `CLIENT` = :CLIENT";
                $param['CLIENT'] = $_POST['CLIENT'];
            } 

            if(!empty($_POST["MANAGER"])){
                $query .= " AND `MANAGER` = :MANAGER";
                $param['MANAGER'] = $_POST['MANAGER'];
            } 

            if(!empty($_POST["EVENT_PLANNER"])){
                $query .=  " AND `EVENT_PLANNER` = :EVENT_PLANNER";
                $param['EVENT_PLANNER'] = $_POST['EVENT_PLANNER'];
            } 

            if(!empty($_POST["DJ"])){
                $query .= " AND `DJ` = :DJ";
                $param['DJ'] = $_POST['DJ'];
            } 

            if(!empty($_POST["LOCATION"])){
                $query .= " AND `LOCATION` = :LOC";
                $param['LOC'] = $_POST['LOCATION'];
            } 

            if(!empty($_POST["RENTAL_FEE"])){
                $query .= " AND `RENTAL_FEE` = :RENTAL_FEE";
                $param['RENTAL_FEE'] = $_POST['RENTAL_FEE'];
            } 

            $req = $bdd->prepare($query,array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY)) ; 
    
            if($req->execute($param)){
        
                echo "La recherche a donnée la table suivante : ";
                echo "<table>";
                echo  "<thead>
                    <tr> 
                        <th> ID </th> 
                        <th> NOM </th> 
                        <th> DATE </th>  
                        <th> DESCRIPTION </th>  
                        <th> CLIENT </th>  
                        <th> MANAGER </th>   
                        <th> PLANIFICATEUR </th> 
                        <th> DJ </th>  
                        <th> THEME </th>  
                        <th> TYPE </th> 
                        <th> LIEU </th> 
                        <th> FRAIS DE LOCATION </th> 
                        <th> NOM PLAYLIST </th> 
                    </tr> 
                    </thead>";
                echo "<tbody>";

                while ($tuple = $req->fetch())
                {
                    echo "<tr>";
                    echo "<td>" . $tuple['ID'] . "</td>";
                    echo "<td>" . $tuple['NAME'] . "</td>";
                    echo "<td>" . $tuple['DATE'] . "</td>";
                    echo "<td>" . $tuple['DESCRIPTION'] . "</td>";
                    echo "<td>" . $tuple['CLIENT'] . "</td>";
                    echo "<td>" . $tuple['MANAGER'] . "</td>";
                    echo "<td>" . $tuple['EVENT_PLANNER'] . "</td>";
                    echo "<td>" . $tuple['DJ'] . "</td>";
                    echo "<td>" . $tuple['THEME'] . "</td>";
                    echo "<td>" . $tuple['TYPE'] . "</td>";
                    echo "<td>" . $tuple['LOCATION'] . "</td>";
                    echo "<td>" . $tuple['RENTAL_FEE'] . " €" ."</td>";
                    echo "<td>" . $tuple['PLAYLIST'] . "</td>";
                    echo "</tr>";
                }
                echo "</tbody>";
                echo "</table>";
            }
        }

        
        if($_POST['table'] == 'SONG'){
                
            $query = "SELECT * FROM SONG WHERE TITLE LIKE :TITLE AND ARTIST LIKE :ARTIST AND GENRE LIKE :GENRE ";
            $param = array();
            $param['TITLE'] = '%'.$_POST['TITLE']. '%';
            $param['ARTIST'] ='%'.$_POST['ARTIST']. '%';
            $param['GENRE'] = '%'.$_POST['GENRE']. '%';

        
            if(!empty($_POST["CD_NUMBER"])){
                $query .= " AND `CD_NUMBER` = :CD_NUMBER";
                $param['CD_NUMBER'] = $_POST['CD_NUMBER'];
            } 

            if(!empty($_POST["TRACK_NUMBER"])){
                $query .= " AND `TRACK_NUMBER` = :TRACK_NUMBER";
                $param['TRACK_NUMBER'] = $_POST['TRACK_NUMBER'];
            } 

            if(!empty($_POST["DURATION"])){
                $query .= " AND `DURATION` = :DURATION";
                $param['DURATION'] = $_POST['DURATION'];
            } 

          
            $req = $bdd->prepare($query,array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY)) ; 

            if($req->execute($param)){
        
                echo "La recherche a donnée la table suivante : ";
                echo "<table>";
                echo  "<thead>
                    <tr> 
                        <th> NUMERO DU CD </th>
                        <th> NUMERO DE CHANSON </th>   
                        <th> TITRE </th> 
                        <th> ARTISTE </th> 
                        <th> DUREE </th>   
                        <th> GENRE </th>   

                    </tr> 
                    </thead>";
                echo "<tbody>";

                while ($tuple = $req->fetch())
                {
                    echo "<tr>";
                    echo "<td>" . $tuple['CD_NUMBER'] . "</td>";
                    echo "<td>" . $tuple['TRACK_NUMBER'] . "</td>";
                    echo "<td>" . $tuple['TITLE'] . "</td>";
                    echo "<td>" . $tuple['ARTIST'] . "</td>";
                    echo "<td>" . $tuple['DURATION'] . "</td>";
                    echo "<td>" . $tuple['GENRE'] . "</td>";
                    echo "</tr>";
                }
                echo "</tbody>";
                echo "</table>";
            }
        }

        
        if($_POST['table'] == 'CD'){
                
            $query = "SELECT * FROM CD WHERE TITLE LIKE :TITLE AND PRODUCER LIKE :PRODUCER  ";
            $param = array();
            $param['TITLE']= '%'.$_POST['TITLE']. '%';
            $param['PRODUCER']= '%'.$_POST['PRODUCER']. '%';

        
            if(!empty($_POST["CD_NUMBER"])){
                $query .= " AND `CD_NUMBER` = :CD_NUMBER";
                $param['CD_NUMBER'] = $_POST['CD_NUMBER'];
            } 

            
            if(!empty($_POST["YEAR"])){
                $query .= " AND `YEAR` = :YEAR";
                $param['YEAR'] = $_POST['YEAR'];
            }

          
            if(!empty($_POST["COPIES"])){
                $query .= " AND `COPIES` = :COPIES";
                $param['COPIES'] = $_POST['COPIES'];
            } 


            $req = $bdd->prepare($query,array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY)) ; 

            if($req->execute($param)){
        
                echo "La recherche a donnée la table suivante : ";
                echo "<table>";
                echo  "<thead>
                    <tr> 
                        <th> NUMERO DU CD </th>  
                        <th> TITRE </th> 
                        <th> PRODUCTEUR </th> 
                        <th> ANNEE </th>   
                        <th> NOMBRE DE COPIE </th>   

                    </tr> 
                    </thead>";
                echo "<tbody>";

                while ($tuple = $req->fetch())
                {
                    echo "<tr>";
                    echo "<td>" . $tuple['CD_NUMBER'] . "</td>";
                    echo "<td>" . $tuple['TITLE'] . "</td>";
                    echo "<td>" . $tuple['PRODUCER'] . "</td>";
                    echo "<td>" . $tuple['YEAR'] . "</td>";
                    echo "<td>" . $tuple['COPIES'] . "</td>";
                    echo "</tr>";
                }
                echo "</tbody>";
                echo "</table>";
            }
        }
    }
        bouton_menu();
        bouton_deconnexion();
        ?>
    </body>
</html>