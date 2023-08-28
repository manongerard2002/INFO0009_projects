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
        <title>Modification des CDs</title>
    </head>
    <body>
        <h1>Modification des CDs</h1>
        <?php
      
        //Ajout d'une chanson
        if (isset($_POST["TRACK_NUMBER"]) && isset($_POST["TITLE"]) && isset($_POST["ARTIST"]) && isset($_POST["DURATION"]) && isset($_POST["GENRE"]))
        {
            if(empty($_POST['TRACK_NUMBER']) && empty($_POST['TITLE']) && empty($_POST['ARTIST']) && empty($_POST['DURATION']) && empty($_POST['GENRE']))
            {
                    echo "Aucun chanson n'a été modifiée car tous les champs requis n'ont pas été rempli.<br><br>";
            }

            else{

                $entree_correct = true;
                $entree_correct = verif_varchar_length($_POST['TITLE'], 100) && verif_varchar_length($_POST['ARITST'], 50) && verif_varchar_length($_POST['GENRE'], 50);
                $entree_correct = $entree_correct && verif_positive_int($_POST["CD_NUMBER"]) && verif_positive_int($_POST["TRACK_NUMBER"]);
                // Vérifier que le genre existe
                $genre_existant = verification_id('SELECT COUNT(*) FROM `GENRE` WHERE `NAME` = :id', $_POST['GENRE'], $bdd);
                if (!$genre_existant)
                {
                    echo "Le genre ".htmlspecialchars($_POST['genre'])." ne correspond pas à un genre disponible.<br><br>";
                }
                elseif ($genre_existant === NULL)
                {
                    $entree_NULL = true;
                }
                $entree_correct = $entree_correct && $genre_existant;

                if(!$entree_NULL &&  $entree_correct){

                    $query_ajout = "INSERT INTO SONG(CD_NUMBER, TRACK_NUMBER,TITLE, ARTIST, DURATION, GENRE) VALUES(:CD_NUMBER, :TRACK_NUMBER, :TITLE, :ARTIST, :DURATION, :GENRE)";
                    $param_ajout=array();
                    $param_ajout['CD_NUMBER'] = $_POST["CD_NUMBER"];
                    $param_ajout['TRACK_NUMBER'] = $_POST["TRACK_NUMBER"];
                    $param_ajout['TITLE'] = htmlspecialchars($_POST["TITLE"]);
                    $param_ajout['ARTIST'] = htmlspecialchars($_POST["ARTIST"]);
                    $param_ajout['DURATION'] = gmdate("H:i:s",$_POST['DURATION']);
                    $param_ajout['GENRE'] = htmlspecialchars($_POST["GENRE"]);
    
    
                    $req = $bdd->prepare($query_ajout,array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
                  

                    if($req->execute($param_ajout) == FALSE)
                    {
                        echo "Problème lors de l'ajout de la chanson";

                        $query_existe = "SELECT * FROM SONG WHERE `TRACK_NUMBER` = :TRACK_NUM AND  `CD_NUMBER`= :CD_NUM";
                        $param = array();
                       
                        $param['CD_NUM'] = $_POST['CD_NUMBER'];
                        $param['TRACK_NUM'] = $_POST['TRACK_NUMBER'];
                       
                    
                        $req_in_cd = $bdd->prepare($query_existe, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
                        if($req_in_cd->execute($param)){
                            echo ", ce numéro de chanson existe déjà.";
                        }
                    }
                    else
                    {
                        echo "La chanson a été ajoutée avec succès! ";
                    }
                }
                else
                {
                    echo "Aucune chanson n'a été ajoutée.<br><br>";
                }
            }
        }?>


        <?php

        //Modification d'une chanson
        if (isset($_POST['modification']))
        {
                if(empty($_POST['track_number']) && empty($_POST['title']) && empty($_POST['artist']) && empty($_POST['duration']) && empty($_POST['genre']))
                {
                    echo "Aucune chanson n'a été modifiée car aucun champ n'a été rempli.<br><br>";
                }
                else
                {
                    $track_number_before = $_POST['track_number_before'];
                    $track_number = $_POST['track_number'];
                    $cd_number = $_POST['cd_number'];
                    
                    $entree_correct = true;
                    $param = array();
                    $entree_correct = true;
                    $entree_NULL = false;
                    
                    $query_modif = "UPDATE `SONG` SET ";

                    $query_modif .= "`CD_NUMBER` = :cd_number, ";
                    $param['cd_number']= $_POST['cd_number'];

                    $query_modif .= "`TRACK_NUMBER` = :track_number_before, ";
                    $param['track_number_before']= $_POST['track_number_before'];

                
                    if (!empty($_POST['track_number']))
                    {
                        $entree_correct = $entree_correct && verif_positive_int($_POST['track_number']);
                        $query_modif .= "`TRACK_NUMBER` = :track_number, ";
                        $param['track_number']= $_POST['track_number'];
                    }
                    
            
                    if (!empty($_POST['title']))
                    {
                        $entree_correct = $entree_correct && verif_varchar_length($_POST['title'], 100);
                        $query_modif .= "`TITLE` = :title, ";
                        $param['title']= $_POST['title'];
                    }
                    
        
                    if (!empty($_POST['artist']))
                    {
                        $entree_correct = $entree_correct && verif_varchar_length($_POST['artist'], 50);
                        $query_modif .= "`ARTIST` = :artist, ";
                        $param['artist']= $_POST['artist'];
                    }

                    if (!empty($_POST['genre']))
                    {
                        $entree_correct = $entree_correct && verif_varchar_length($_POST['genre'], 50);
                        $query_modif .= "`GENRE` = :genre, ";
                        $param['genre']= $_POST['genre'];
                        // Vérifier que le genre existe
                        $genre_existant = verification_id('SELECT COUNT(*) FROM `GENRE` WHERE `NAME` = :id', $_POST['genre'], $bdd);
                        if (!$genre_existant)
                        {
                            echo "Le genre ".htmlspecialchars($_POST['genre'])." ne correspond pas à un genre disponible.<br><br>";
                        }
                        elseif ($genre_existant === NULL)
                        {
                            $entree_NULL = true;
                        }
                        $entree_correct = $entree_correct && $genre_existant;
                    }

                    if (!empty($_POST['duration']))
                    {
                        $entree_correct = $entree_correct && verif_positive_int($_POST['duration']);
                        $query_modif .= "`DURATION` = :duration ";
                        $param['duration']= gmdate("H:i:s",$_POST['duration']);
                    }
                    
                
                    // suppression du dernier ", "
                    $query_modif = rtrim($query_modif, ", ");
            
                    $query_modif .= " WHERE `TRACK_NUMBER` = :track_number_before AND  `CD_NUMBER`=:cd_number";
                  
                    if(!$entree_NULL && $entree_correct)
                    {
                        $req = $bdd->prepare($query_modif, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
                  
                        if($req->execute($param) == FALSE)
                        {
                            $query_existe = "SELECT * FROM SONG WHERE `TRACK_NUMBER` = :TRACK_NUMBER AND  `CD_NUMBER`= :CD_NUMBER";
                            $param = array();
                            $param['TRACK_NUMBER'] = $track_number;
                            $param['CD_NUMBER'] = $cd_number;
                         
                            $req_in = $bdd->prepare($query_existe, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
                            if($req_in->execute($param) == FALSE){
                                echo "Problème dans la modification <br><br>";
                            }
                            else{
                                echo "La chanson n'a pas été modifiée, ce numéro de chanson existe déjà";
                            }
                        }
                        else
                        {
                            echo "La chanson a été modifiée avec succès dans ce CD ";

                            //Modification dans toutes les listes de lecture
                            if(!empty($_POST['track_number']))
                            {

                                $entree_correct = true;
                                $param = array();

                                $query_in = "UPDATE `CONTAINS` SET ";
                            
                                $query_in .= "`CD_NUMBER` = :cd_number, ";
                                $param['cd_number']= $_POST['cd_number'];

                                $query_in .= "`TRACK_NUMBER` = :track_number_before, ";
                                $param['track_number_before']= $_POST['track_number_before'];
                    
                                $entree_correct = $entree_correct && verif_positive_int($_POST['track_number']);
                                $query_in .= "`TRACK_NUMBER` = :track_number, ";
                                $param['track_number']= $_POST['track_number'];

                                $query_in = rtrim($query_in, ", ");
            
                                $query_in .= " WHERE `TRACK_NUMBER` = :track_number_before AND  `CD_NUMBER`=:cd_number";
                     
                                if($entree_correct)
                                {
                                    $req_playlist_track = $bdd->prepare($query_in, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
                                    
                                    if($req_playlist_track->execute($param) == FALSE)
                                    {
                                        echo "Problème de modification dans les listes de lecture<br><br>";
                                    }
                                    else
                                    {
                                        echo "et dans toutes les listes de lecture !<br><br>";
                                    } 
                                }          
                               
                            }
                        }

                    }
                }
            
        }

        //Suppresion chanson
        if (isset($_POST['suppression']))
        {
            $query = "DELETE FROM `SONG` WHERE `TRACK_NUMBER` = :TRACK_NUMBER AND  `CD_NUMBER`= :CD_NUMBER";
            $param= array();
            $param['CD_NUMBER'] = $_POST['cd_number'];
            $param['TRACK_NUMBER'] = $_POST['track_number'];

            $req = $bdd->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
            if($req->execute($param) == FALSE)
            {
                echo "Problème dans la suppression <br><br>";
            }
            else
            {
                echo "La chanson a été supprimée avec succès du CD ";
            
        
                $query_play = "DELETE FROM CONTAINS WHERE `TRACK_NUMBER` = :TRACK_NUMBER AND  `CD_NUMBER`= :CD_NUMBER";
                $param= array();
                $param['CD_NUMBER'] = $_POST['cd_number'];
                $param['TRACK_NUMBER'] = $_POST['track_number'];
            
                $req_play = $bdd->prepare($query_play, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
                if($req_play->execute($param) == FALSE)
                {
                    echo "Problème de suppression dans les listes de lectures <br><br>";
                }
                else
                {
                    echo "et de toutes les listes de lectures <br><br>";
                }
            }
        } ?>
        
        <!--Sélection CD -->
        <?php       
        $query = "SELECT * FROM `CD` ";
        $req = $bdd->prepare($query);
        if($req->execute() == FALSE)
        {
            echo "Problème dans l'affichage des CDs <br><br>";
        }?>

        <form method='post' action="modif_CD.php">
            <label>Sélectionnez un CD:</label>
            <select name='cd'>
            <?php
            while ($tuple = $req->fetch(PDO::FETCH_ASSOC)) {
                echo "<option value='".$tuple['CD_NUMBER']."'>"."CD numéro ". $tuple['CD_NUMBER']. "," .$tuple['TITLE'].", ".$tuple['PRODUCER']." ".$tuple['YEAR'].", ".$tuple['COPIES']." "."</option>";
            }?>

            </select>
            <input type="submit" name='submit' value="Choisir">
        </form>

        <?php
        if (isset($_POST['cd']))
        {
        // Vérification de lieu
        $cd_existant = verification_id('SELECT `CD_NUMBER` FROM `CD` WHERE `CD_NUMBER` = :id', $_POST['cd'], $bdd);
                
        if (!$cd_existant)
        {
            echo "L'identifiant ".$_POST['lieu']." ne correspond pas à un cd.<br>";
            echo "Vous pouvez pas compléter ou mettre à jour ses informations.<br><br>";
        }
        else if ($cd_existant !== NULL)
        {
            $query_cd = "SELECT * FROM `CD` WHERE `CD_NUMBER` = :CD_NUMBER";
            $param = array();
            $param['CD_NUMBER'] = $_POST['cd'];
          
            $req_cd= $bdd->prepare($query_cd, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
            if($req_cd->execute($param) == FALSE)
            {
                echo "Problème dans l'affichage du CD choisi <br><br>";
            }
            $tuple_cd = $req_cd->fetch();
            

            $query_genre = "SELECT DISTINCT `GENRE` FROM `SONG` ";
            $req_genre = $bdd->prepare($query_genre);
            if($req_genre->execute() == FALSE)
            {
                    echo "Problème dans l'affichage du choix des genres <br><br>";
            }?>

            <!--Formulaire de sélection de chanson -->
            <h2> Ajouter une chanson sur le CD <?php echo "numéro ".$tuple_cd['CD_NUMBER'] ;?> </h2>
            <form method="post" action="modif_CD.php">
            <input type="hidden" name="CD_NUMBER" value="<?php echo $tuple_cd['CD_NUMBER']; ?>">
            <p>
                <label><b>Numéro</label>
                <input type="number" name="TRACK_NUMBER" required>
                <br>
                <br>
                <label><b>Titre de la chanson</label>
                <input type="text" name="TITLE" maxlength="100" required>
                <br>
                <br>
                <label><b>Artiste</label>
                <input type="text" name="ARTIST" maxlength="50" required>
                <br>
                <br>
                <label><b>Durée(en secondes)</label>
                <input type="number" name="DURATION" max=86359 min=0 required>
                <br>
                <br>
                
                <label><b>Genre</label>
                <select name='GENRE'>
                    
                <?php 
                     echo '<option value= "" selected="selected"></option>';
                     while ($tuple_genre = $req_genre->fetch(PDO::FETCH_ASSOC)) {
                         echo "<option value='".$tuple_genre['GENRE']."'>". $tuple_genre['GENRE'].""."<br> </option>";
                      }?>

                     </select> 
                     <br>
                <input type="submit" value="Ajouter">
            </p>
            </form>


             <!-- Sélection de chanson-->
            <?php 
            $query_s = "SELECT * FROM `SONG` WHERE CD_NUMBER = :CD_NUMBER";
            $param = array();
            $cd_number=$_POST['cd'];
            $param['CD_NUMBER'] = $cd_number;
            $req_s = $bdd->prepare($query_s, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));

            if($req_s->execute($param) == FALSE)
            {
                echo "Problème dans l'affichage des chansons <br><br>";
            }

            ?>
            <form method='post' action="modif_CD.php">
                <input type="hidden" name="CD_NUMBER" value="<?php echo $cd_number; ?>">
                <label>Sélectionnez une chanson du CD afin de la modifier ou de la supprimer:</label>
                <select name='chanson'>
                <?php
            
                while ($tuple_s = $req_s->fetch(PDO::FETCH_ASSOC)) {
                    echo "<option value='".$tuple_s['TRACK_NUMBER']."'> ". $tuple_s['CD_NUMBER']. "," .$tuple_s['TRACK_NUMBER'].", ".$tuple_s['TITLE']." ".$tuple_s['ARTIST'].", ".$tuple_s['DURATION'].",".$tuple_s['GENRE']." "."</option>";
                }
                ?>
                </select>
                <input type="submit" name='submit' value="Choisir">
            </form>

            <?php
            }
            }

            if (isset($_POST['chanson'])) 
            {
                $query_song = "SELECT * FROM `SONG` WHERE `TRACK_NUMBER` = :TRACK_NUMBER AND `CD_NUMBER` = :CD_NUMBER";
                $param = array();
                $param['TRACK_NUMBER'] = $_POST['chanson'];
                $track_number = $param['TRACK_NUMBER']; 

                $cd_number= $_POST['CD_NUMBER'];
                $param['CD_NUMBER'] = $cd_number;
             
                $req_song= $bdd->prepare($query_song, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
                if($req_song->execute($param) == FALSE)
                {
                    echo "Problème dans l'affichage de la chanson choisie <br><br>";
                }
                else if($req_song->rowCount() == 0)
                {
                    echo "Il n'existe pas cette association de CD et track number.<br><br>";
                }
                else
                {

                $tuple_song = $req_song->fetch();
                echo "Vous avez sélectionné la chanson : ".$cd_number." ".$tuple_song['TRACK_NUMBER']."  ".$tuple_song['TITLE']." ".$tuple_song['ARTIST']. " ".$tuple_song['DURATION']. " ".$tuple_song['GENRE'];

                $query_genre = "SELECT DISTINCT `GENRE` FROM `SONG` ";
                $req_genre = $bdd->prepare($query_genre);
                if($req_genre->execute() == FALSE)
                {
                    echo "Problème dans l'affichage du choix des genres <br><br>";
                }
                ?>
 
                <!-- Formulaire de modification-->
                <h2>Entrez les champs que vous souhaitez modifier</h2>
                <form method="post" action="modif_CD.php">
                    <input type="hidden" name="cd_number" value="<?php echo $cd_number; ?>"> 
                    <input type="hidden" name="track_number_before" value="<?php echo $track_number; ?>">
                    <p>
                    
                        <label><b>Numéro de la chanson</label>
                        <input type="number" name="track_number">
                        <br>
                        <br>
                        <label><b>Titre de la chanson</label>
                        <input type="text" name="title" maxlength="100" >
                        <br>
                        <br>
                        <label><b>Artiste</label>
                        <input type="text" name="artist" maxlength="50">
                        <br>
                        <br>
                        <label><b>Durée(en secondes)</label>
                        <input type="number" name="duration" max=86359 min=0>
                        <br>
                        <br>
                        
                        <label><b>Genre</label>
                        <select name='genre'>  -->
                        <?php
                        
                        echo '<option value= "" selected="selected"></option>';
                        while ($tuple_genre = $req_genre->fetch(PDO::FETCH_ASSOC)) {
                            echo "<option value='".$tuple_genre['GENRE']."'>". $tuple_genre['GENRE'].""."<br> </option>";
                         }?>

                        </select>
                        <br>
           
                        <input type="hidden" name="modification" value="yes">
                        <input type="submit" value="Modifier">
                    </p>
                </form>


            <p>Vous pouvez supprimer la chanson:</p>
            <form method="post" action="modif_cd.php">
                <input type="hidden" name="track_number" value="<?php echo $track_number; ?>">
                <input type="hidden" name="cd_number" value="<?php echo $cd_number; ?>">
                <p>
                    <input type="hidden" name="suppression" value="yes">
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

