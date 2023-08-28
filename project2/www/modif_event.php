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
// Date de demain
$query = "SELECT DATE_ADD(CURDATE(), INTERVAL 1 DAY) AS date_demain";
$req = $bdd->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
if($req->execute() === FALSE)
{
    echo "Problème dans la récupération de la date de demain.<br><br>";
}
$tuple = $req->fetch(PDO::FETCH_ASSOC);
$date_demain = $tuple['date_demain'];
?>
<!DOCTYPE html>
<html>
    <style>
        h2 {
            font-size: 19px;
        }
    </style>
    <head>
        <title>Modification des événements</title>
    </head>
    <body>
        <h1>Modification des événements</h1>
        <h2>Sélection d'un événement existant</h2>
        <?php
        // Modification d'un événement
        if (isset($_POST['modification_id']))
        {
            if(empty($_POST['id']) && empty($_POST['name']) && empty($_POST['date']) && empty($_POST['description']) && empty($_POST['description_null']) && empty($_POST['client']) && empty($_POST['manager']) && empty($_POST['event_planner']) && empty($_POST['dj']) && 
            empty($_POST['theme']) && empty($_POST['type']) && empty($_POST['location']) && empty($_POST['location_null']) && empty($_POST['rental_fee']) && empty($_POST['rental_fee_null']) && empty($_POST['playlist']) && empty($_POST['playlist_null']))
            {
                echo "Aucun événement n'a été modifié car aucun champ n'a été rempli.<br><br>";
            }
            else if((!empty($_POST['description']) && isset($_POST['description_null'])) || (!empty($_POST['location']) && isset($_POST['location_null'])) 
            || (!empty($_POST['rental_fee']) && isset($_POST['rental_fee_null'])) || (!empty($_POST['playlist']) && isset($_POST['playlist_null'])))
            {
                echo "Vous ne pouvez pas completer un champ et aussi le vider.<br><br>";
            }
            else
            {
                // Vérification de l'entrée modification_id
                $id_a_venir = verification_id('SELECT `ID` FROM `EVENT` WHERE `ID` = :id AND `DATE` > CURDATE()', $_POST['modification_id'], $bdd);

                if (!$id_a_venir)
                {
                    echo "L'identifiant ".$_POST['modification_id']." ne correspond pas à un événement à venir.<br>";
                    echo "Aucun événement n'a été modifié.<br><br>";
                }
                else if ($id_a_venir !== NULL)
                {
                    $query = "UPDATE `EVENT` SET ";
                    $parameters = array();
                    $entree_correct = true;
                    $entree_NULL = false;
                    // Gestion des champs facultatifs
                    if (!empty($_POST['id']))
                    {
                        $entree_correct = $entree_correct && verif_positive_int($_POST['id']);
                        $query .= "`ID` = :id, ";
                        $parameters['id'] = $_POST['id'];
                        $id_existante = false;
                        // Vérifier qu'id est differente de celles déjà dans la BD, sauf si même qu'avant
                        if ($_POST['id'] != $_POST['modification_id'])
                        {
                            $id_existante = verification_id('SELECT COUNT(*) FROM `EVENT` WHERE `ID` = :id', $_POST['id'], $bdd);
                        }
                        if ($id_existante)
                        {
                            echo "L'identifiant ".htmlspecialchars($_POST['id'])." correspond déjà à un événement existant.<br><br>";
                        }
                        elseif ($id_existante === NULL)
                        {
                            $entree_NULL = true;
                        }
                        $entree_correct = $entree_correct && !$id_existante;
                    }
                    if (!empty($_POST['name']))
                    {
                        $entree_correct = $entree_correct && verif_varchar_length($_POST['name'], 50);
                        $query .= "`NAME` = :name, ";
                        $parameters['name'] = $_POST['name'];
                    }
                    $newDate = false;
                    if (!empty($_POST['date']))
                    {
                        $newDate = true;
                        // Vérifier que la date est à venir
                        $entree_correct = $entree_correct && verif_date($_POST['date']) && ($date_demain <= $_POST['date']);
                        $query .= "`DATE` = :date, ";
                        $parameters['date'] = $_POST['date'];
                        $date = $_POST['date'];
                    }
                    else
                    {
                        // Chercher la date de l'événement
                        $request = 'SELECT `DATE` FROM `EVENT` WHERE `ID` = :id';
                        $req = $bdd->prepare($request, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
                        if($req->execute(array('id' => $_POST['modification_id'])) === FALSE)
                        {
                            echo "Problème lors de la recherche de la date de l'événement.<br><br>";
                        }
                        else
                        {
                            $tuple = $req->fetch(PDO::FETCH_ASSOC);
                            $date = $tuple['DATE'];
                        }
                    }
                    if (!empty($_POST['description']))
                    {
                        $entree_correct = $entree_correct && verif_varchar_length($_POST['description'], 500);
                        $query .= "`DESCRIPTION` = :description, ";
                        $parameters['description'] = $_POST['description'];
                    }
                    else if (!empty($_POST['description_null']))
                    {
                        $query .= "`DESCRIPTION` = :description, ";
                        $parameters['description'] = NULL;
                    }
                    if (!empty($_POST['client']))
                    {
                        //Verifier que le client existe
                        $entree_correct = $entree_correct && verif_positive_int($_POST['client']);
                        $query .= "`CLIENT` = :client, ";
                        $parameters['client'] = $_POST['client'];
                        // Vérifier que le client existe
                        $id_existante = verification_id('SELECT COUNT(*) FROM `CLIENT` WHERE `CLIENT_NUMBER` = :id', $_POST['client'], $bdd);
                        if (!$id_existante)
                        {
                            echo "L'identifiant ".htmlspecialchars($_POST['client'])." ne correspond pas à un client.<br><br>";
                        }
                        elseif ($id_existante === NULL)
                        {
                            $entree_NULL = true;
                        }
                        $entree_correct = $entree_correct && $id_existante;
                    }
                    $newManager = false;
                    if (!empty($_POST['manager']))
                    {
                        $entree_correct = $entree_correct && verif_positive_int($_POST['manager']);
                        $query .= "`MANAGER` = :manager, ";
                        $parameters['manager'] = $_POST['manager'];
                        $manager = $_POST['manager'];
                        // Vérifier que le manager existe
                        $id_existante = verification_id('SELECT COUNT(*) FROM `MANAGER` WHERE `ID` = :id', $_POST['manager'], $bdd);
                        if (!$id_existante)
                        {
                            echo "L'identifiant ".htmlspecialchars($_POST['manager'])." ne correspond pas à un manager.<br><br>";
                        }
                        elseif ($id_existante === NULL)
                        {
                            $entree_NULL = true;
                        }
                        $entree_correct = $entree_correct && $id_existante;
                    }
                    else
                    {
                        // Chercher le manager de l'événement
                        $request = 'SELECT `MANAGER` FROM `EVENT` WHERE `ID` = :id';
                        $req = $bdd->prepare($request, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
                        if($req->execute(array('id' => $_POST['modification_id'])) === FALSE)
                        {
                            echo "Problème lors de la recherche du manager de l'événement.<br><br>";
                        }
                        else
                        {
                            $tuple = $req->fetch(PDO::FETCH_ASSOC);
                            $manager = $tuple['MANAGER'];
                        }
                    }
                    $newEventPlanner = false;
                    if (!empty($_POST['event_planner']))
                    {
                        $newEventPlanner = true;
                        $entree_correct = $entree_correct && verif_positive_int($_POST['event_planner']);
                        $query .= "`EVENT_PLANNER` = :event_planner, ";
                        $parameters['event_planner'] = $_POST['event_planner'];
                        $event_planner = $_POST['event_planner'];
                        // Vérifier que le planificateur d'événement existe
                        $id_existante = verification_id('SELECT COUNT(*) FROM `EVENTPLANNER` WHERE `ID` = :id', $_POST['event_planner'], $bdd);
                        if (!$id_existante)
                        {
                            echo "L'identifiant ".htmlspecialchars($_POST['event_planner'])." ne correspond pas à un planificateur d'événement.<br><br>";
                        }
                        elseif ($id_existante === NULL)
                        {
                            $entree_NULL = true;
                        }
                        $entree_correct = $entree_correct && $id_existante;                       
                    }
                    else
                    {
                        // Chercher le planificateur d'événement de l'événement
                        $request = 'SELECT `EVENT_PLANNER` FROM `EVENT` WHERE `ID` = :id';
                        $req = $bdd->prepare($request, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
                        if($req->execute(array('id' => $_POST['modification_id'])) === FALSE)
                        {
                            echo "Problème lors de la recherche du planificateur d'événement de l'événement.<br><br>";
                        }
                        else
                        {
                            $tuple = $req->fetch(PDO::FETCH_ASSOC);
                            $event_planner = $tuple['EVENT_PLANNER'];
                        }
                    }
                    $newDJ = false;
                    if (!empty($_POST['dj']))
                    {
                        $newDJ = true;
                        $entree_correct = $entree_correct && verif_positive_int($_POST['dj']);
                        $query .= "`DJ` = :dj, ";
                        $parameters['dj'] = $_POST['dj'];
                        $dj = $_POST['dj'];
                        // Vérifier que le dj existe
                        $id_existante = verification_id('SELECT COUNT(*) FROM `DJ` WHERE `ID` = :id', $_POST['dj'], $bdd);
                        if (!$id_existante)
                        {
                            echo "L'identifiant ".htmlspecialchars($_POST['dj'])." ne correspond pas à un dj.<br><br>";
                        }
                        elseif ($id_existante === NULL)
                        {
                            $entree_NULL = true;
                        }
                        $entree_correct = $entree_correct && $id_existante;
                    }
                    else
                    {
                        // Chercher le dj de l'événement
                        $request = 'SELECT `DJ` FROM `EVENT` WHERE `ID` = :id';
                        $req = $bdd->prepare($request, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
                        if($req->execute(array('id' => $_POST['modification_id'])) === FALSE)
                        {
                            echo "Problème lors de la recherche du dj l'événement.<br><br>";
                        }
                        else
                        {
                            $tuple = $req->fetch(PDO::FETCH_ASSOC);
                            $dj = $tuple['DJ'];
                        }
                    }
                    if (!empty($_POST['theme']))
                    {
                        $entree_correct = $entree_correct && verif_varchar_length($_POST['theme'], 30);
                        $query .= "`THEME` = :theme, ";
                        $parameters['theme'] = $_POST['theme'];
                        // Vérifier que le theme existe
                        $id_existante = verification_id('SELECT COUNT(*) FROM `THEME` WHERE `NAME` = :id', $_POST['theme'], $bdd);
                        if (!$id_existante)
                        {
                            echo "Le nom ".htmlspecialchars($_POST['theme'])." ne correspond pas à un thême.<br><br>";
                        }
                        elseif ($id_existante === NULL)
                        {
                            $entree_NULL = true;
                        }
                        $entree_correct = $entree_correct && $id_existante;
                    }
                    if (!empty($_POST['type']))
                    {
                        $entree_correct = $entree_correct && verif_varchar_length($_POST['type'], 30);
                        $query .= "`TYPE` = :type, ";
                        $parameters['type'] = $_POST['type'];
                    }
                    if (!empty($_POST['location']))
                    {
                        echo "test";
                        $entree_correct = $entree_correct && verif_positive_int($_POST['location']);
                        $query .= "`LOCATION` = :location, ";
                        $parameters['location'] = $_POST['location'];
                        // Vérifier que la location existe
                        $id_existante = verification_id('SELECT COUNT(*) FROM `LOCATION` WHERE `ID` = :id', $_POST['location'], $bdd);
                        if (!$id_existante)
                        {
                            echo "L'identifiant ".htmlspecialchars($_POST['location'])." ne correspond pas à un lieu.<br><br>";
                        }
                        elseif ($id_existante === NULL)
                        {
                            $entree_NULL = true;
                        }
                        $entree_correct = $entree_correct && $id_existante;
                    }
                    else if (isset($_POST['location_null']))
                    {
                        $query .= "`LOCATION` = :location, ";
                        $parameters['location'] = NULL;
                    }
                    if (!empty($_POST['rental_fee']))
                    {
                        $entree_correct = $entree_correct && verif_positive_int($_POST['rental_fee']);
                        $query .= "`RENTAL_FEE` = :rental_fee, ";
                        $parameters['rental_fee'] = $_POST['rental_fee'];
                    }
                    else if (!empty($_POST['rental_fee_null']))
                    {
                        $query .= "`RENTAL_FEE` = :rental_fee, ";
                        $parameters['rental_fee'] = NULL;
                    }
                    if (!empty($_POST['playlist']))
                    {
                        $newPlaylist = true;
                        $entree_correct = $entree_correct && verif_varchar_length($_POST['playlist'], 50);
                        $query .= "`PLAYLIST` = :playlist, ";
                        $parameters['playlist'] = $_POST['playlist'];
                        $playlist = $_POST['playlist'];
                        // Vérifier que le dj existe
                        $id_existante = verification_id('SELECT COUNT(*) FROM `PLAYLIST` WHERE `NAME` = :id', $_POST['playlist'], $bdd);
                        if (!$id_existante)
                        {
                            echo "Le nom ".htmlspecialchars($_POST['playlist'])." ne correspond pas à une playlist.<br><br>";
                        }
                        elseif ($id_existante === NULL)
                        {
                            $entree_NULL = true;
                        }
                        $entree_correct = $entree_correct && $id_existante;
                    }
                    else if (!empty($_POST['playlist_null']))
                    {
                        $query .= "`PLAYLIST` = :playlist, ";
                        $parameters['playlist'] = NULL;
                    }
                    else
                    {
                        // Chercher la liste de lecture de l'événement
                        $request = 'SELECT `PLAYLIST` FROM `EVENT` WHERE `ID` = :id';
                        $req = $bdd->prepare($request, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
                        if($req->execute(array('id' => $_POST['modification_id'])) === FALSE)
                        {
                            echo "Problème lors de la recherche de la liste de lecture de l'événement.<br><br>";
                        }
                        else
                        {
                            $tuple = $req->fetch(PDO::FETCH_ASSOC);
                            $playlist = $tuple['PLAYLIST'];
                        }
                    }
                    // suppresion du dernier ", "
                    $query = rtrim($query, ", ");
                    $query .= " WHERE `ID` = :modification_id";
                    $parameters['modification_id'] = $_POST['modification_id'];

                    // Un responsable ne peut affecter que des employés qu'il supervise
                    if($newManager || $newEventPlanner)
                    {
                        $request = 'SELECT COUNT(*) FROM `SUPERVISION` WHERE `SUPERVISOR_ID` = :manager AND `EMPLOYEE_ID` = :event_planner';
                        $req = $bdd->prepare($request, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
                        if($req->execute(array('manager' => $manager, 'event_planner' => $event_planner)) === FALSE)
                        {
                            echo "Problème lors de la vérification de la supervision entre le manager et le planificateur d'événement.<br>";
                            $entree_NULL = true;
                        }
                        else
                        {
                            $supervision = ($req->fetchColumn() > 0);
                        }
                        if (!$supervision)
                        {
                            echo "Le planificateur d'événement ".htmlspecialchars($event_planner)." n'est pas supervisé par le manager ".htmlspecialchars($manager).".<br>";
                        }
                        $entree_correct = $entree_correct && $supervision;
                    }
                    if($newManager || $newDJ)
                    {
                        $request = 'SELECT COUNT(*) FROM `SUPERVISION` WHERE `SUPERVISOR_ID` = :manager AND `EMPLOYEE_ID` = :dj';
                        $req = $bdd->prepare($request, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
                        if($req->execute(array('manager' => $manager, 'dj' => $dj)) === FALSE)
                        {
                            echo "Problème lors de la vérification de la supervision entre le manager et dj.<br>";
                            $entree_NULL = true;
                        }
                        else
                        {
                            $supervision = ($req->fetchColumn() > 0);
                        }
                        if (!$supervision)
                        {
                            echo "Le dj ".htmlspecialchars($dj)." n'est pas supervisé par le manager ".htmlspecialchars($manager).".<br>";
                        }
                        $entree_correct = $entree_correct && $supervision;
                    }
                    // Vérifier que le planificateur d'événement n'a qu'un seul travail ce jour
                    if($newDate || $newEventPlanner)
                    {
                        $request = 'SELECT COUNT(*) FROM `EVENT` WHERE `DATE` = :date AND `EVENT_PLANNER` = :event_planner';
                        $req = $bdd->prepare($request, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
                        if($req->execute(array('date' => $date, 'event_planner' => $event_planner)) === FALSE)
                        {
                            echo "Problème lors de la vérification de la disponibilité du planificateur d'événement.<br>";
                            $entree_NULL = true;
                        }
                        else
                        {
                            $id_occupee = ($req->fetchColumn() > 0);
                        }
                        if ($id_occupee)
                        {
                            echo "Le planificateur d'événement ".htmlspecialchars($event_planner)." n'est pas disponible ce jour.<br>";
                        }
                        $entree_correct = $entree_correct && !$id_occupee;
                    }
                    // Vérifier que le planificateur d'événement n'a qu'un seul travail ce jour
                    if($newDate || $newDJ)
                    {
                        $request = 'SELECT COUNT(*) FROM `EVENT` WHERE `DATE` = :date AND `DJ` = :dj';
                        $req = $bdd->prepare($request, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
                        if($req->execute(array('date' => $date, 'dj' => $dj)) === FALSE)
                        {
                            echo "Problème lors de la vérification de la disponibilité du dj.<br>";
                            $entree_NULL = true;
                        }
                        else
                        {
                            $id_occupee = ($req->fetchColumn() > 0);
                        }
                        if ($id_occupee)
                        {
                            echo "Le dj ".htmlspecialchars($dj)." n'est pas disponible ce jour.<br>";
                        }

                        $entree_correct = $entree_correct && !$id_occupee;
                    }
                    // les CD doivent être disponibles
                    if($newDate || $newPlaylist)
                    {
                        $cd_disponible = true;
                        $request = 'SELECT CD_NUMBER, nb_available, nb_needed FROM
                        (
                            SELECT * FROM (
                                SELECT CD_NUMBER, COPIES - COUNT(*) AS nb_available FROM 
                                    (
                                        SELECT PLAYLIST, CD_NUMBER, COPIES FROM CONTAINS NATURAL JOIN CD GROUP BY CD_NUMBER, PLAYLIST, COPIES
                                    ) AS T1
                                    NATURAL JOIN
                                    (
                                        SELECT PLAYLIST FROM EVENT
                                        WHERE ID != :id AND DATE = :date
                                    ) AS T2
                                    NATURAL JOIN
                                    (
                                        SELECT CD_NUMBER, COPIES FROM CD
                                    ) AS T3
                                    GROUP BY CD_NUMBER, COPIES
                            ) AS T4
                            UNION
                            (
                                SELECT CD_NUMBER, COPIES AS nb_available FROM CD WHERE CD_NUMBER NOT IN 
                                (
                                    SELECT CD_NUMBER FROM 
                                    (
                                        SELECT PLAYLIST, CD_NUMBER, COPIES FROM CONTAINS NATURAL JOIN CD GROUP BY CD_NUMBER, PLAYLIST, COPIES
                                    ) AS T11
                                    NATURAL JOIN
                                    (
                                        SELECT PLAYLIST FROM EVENT
                                        WHERE ID != :id AND DATE = :date
                                    ) AS T12
                                    NATURAL JOIN
                                    (
                                        SELECT CD_NUMBER, COPIES FROM CD
                                    ) AS T13
                                    GROUP BY CD_NUMBER, COPIES
                                )
                            )
                        ) AS T14
                        NATURAL JOIN
                        (
                            SELECT CD_NUMBER, COUNT(DISTINCT CD_NUMBER) AS nb_needed FROM CONTAINS WHERE PLAYLIST = :playlist GROUP BY CD_NUMBER
                        ) AS T24';
                        $req = $bdd->prepare($request, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
                        if($req->execute(array('playlist' => $playlist, 'date' => $date, 'id' => $_POST['modification_id'])) === FALSE)
                        {
                            echo "Problème lors de la vérification de la disponibilité des CDs.<br>";
                            $entree_NULL = true;
                        }
                        else
                        {
                            while ($tuple = $req->fetch(PDO::FETCH_ASSOC))
                            {
                                if ($tuple['nb_needed'] > $tuple['nb_available'])
                                {
                                    $cd_disponible = false;
                                    echo "Le CD ".htmlspecialchars($tuple['CD_NUMBER'])." de la playlist ".htmlspecialchars($_POST['playlist'])." n'est pas disponible.<br>";
                                }
                            }
                        }
                        $entree_correct = $entree_correct && $cd_disponible;
                    }
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

        // Permettre la sélection
        $query ='SELECT * FROM `EVENT` WHERE `DATE` > CURDATE()';
        $req = $bdd->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        if($req->execute() === FALSE)
        {   
            echo "Problème dans l'affichage des événements <br><br>";
        }   

        ?>  
        <form method='post' action="modif_event.php">
        <label>Sélectionnez un événement :</label>
        <select name='event'>
        <?php
        while ($tuple = $req->fetch(PDO::FETCH_ASSOC))
        {
            // Variables pour l'affichage
            $id = $tuple['ID'];
            $name = htmlspecialchars($tuple['NAME']);
            $date = htmlspecialchars($tuple['DATE']);
            $message = $id." : ".$name.", ".$date;
            if (!empty($tuple['DESCRIPTION']))
            {
                $description = htmlspecialchars($tuple['DESCRIPTION']);
                $message .= ", ".$description;
            }
            $client = $tuple['CLIENT'];
            $manager = $tuple['MANAGER'];
            $event_planner = $tuple['EVENT_PLANNER'];
            $dj = $tuple['DJ'];
            $theme = htmlspecialchars($tuple['THEME']);
            $type = htmlspecialchars($tuple['TYPE']);
            $message .= ", ".$client.", ".$manager.", ".$event_planner.", ".$dj.", ".$theme.", ".$type;
            if (!empty($tuple['LOCATION']))
            {
                $location = $tuple['LOCATION'];
                $message .= ", ".$location;
            }
            if (!empty($tuple['RENTAL_FEE']))
            {
                $rental_fee = $tuple['RENTAL_FEE'];
                $message .= ", ".$rental_fee;
            }
            if (!empty($tuple['PLAYLIST']))
            {
                $playlist = htmlspecialchars($tuple['PLAYLIST']);
                $message .= ", ".$playlist;
            }
            echo "<option value='".$id."'>".$message."</option>";
        }
        ?>
            </select>
            <input type="submit" name='submit' value="Choisir">
        </form>
        <?php
        // Permettre la modification
        if (isset($_POST['event']))
        {
            // Vérification de event
            $id_a_venir = verification_id('SELECT `ID` FROM `EVENT` WHERE `ID` = :id AND `DATE` > CURDATE()', $_POST["event"], $bdd);

            if (!$id_a_venir)
            {
                echo "L'identifiant ".$_POST['event']." ne correspond pas à un événement à venir.<br>";
                echo "Vous ne pouvez pas compléter ou mettre à jour ses informations.<br><br>";
            }
            else if ($id_a_venir !== NULL)
            {
                $query_event = "SELECT * FROM `EVENT` WHERE `ID` = :id";
                $req_event = $bdd->prepare($query_event, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
                if($req_event->execute(array('id' => $_POST["event"])) === FALSE)
                {
                    echo "Problème dans l'affichage de l'événement choisi.<br><br>";
                }
                $tuple_event = $req_event->fetch();

                // Variables pour l'affichage
                $id = $tuple_event['ID'];
                $name = htmlspecialchars($tuple_event['NAME']);
                $date = htmlspecialchars($tuple_event['DATE']);
                $message = $id." : ".$name.", ".$date;
                if (!empty($tuple_event['DESCRIPTION']))
                {
                    $description = htmlspecialchars($tuple_event['DESCRIPTION']);
                    $message .= ", ".$description;
                }
                $client = $tuple_event['CLIENT'];
                $manager = $tuple_event['MANAGER'];
                $event_planner = $tuple_event['EVENT_PLANNER'];
                $dj = $tuple_event['DJ'];
                $theme = htmlspecialchars($tuple_event['THEME']);
                $type = htmlspecialchars($tuple_event['TYPE']);
                $message .= ", ".$client.", ".$manager.", ".$event_planner.", ".$dj.", ".$theme.", ".$type;
                if (!empty($tuple_event['LOCATION']))
                {
                    $location = $tuple_event['LOCATION'];
                    $message .= ", ".$location;
                }
                if (!empty($tuple_event['RENTAL_FEE']))
                {
                    $rental_fee = $tuple_event['RENTAL_FEE'];
                    $message .= ", ".$rental_fee;
                }
                if (!empty($tuple_event['PLAYLIST']))
                {
                    $playlist = htmlspecialchars($tuple_event['PLAYLIST']);
                    $message .= ", ".$playlist;
                }
                echo "<option value='".$id."'>".$message."</option>";
                ?>

                <p>Entrez les champs que vous souhaitez modifier</p>
                <form method="post" action="modif_event.php">
                    <input type="hidden" name="modification_id" value="<?php echo $_POST['event'] ?>">
                    <p>
                        <label><b>Identifiant</label>
                        <input type="number" name="id" value="<?php echo $event_id; ?>" min="0" max="2147483647">
                        <br>
                        <br>
                        <label><b>Nom</label>
                        <input type="text" name="name" maxlength="50">
                        <br>
                        <br>
                        <label><b>Date</label>
                        <input type="date" name="date" min="<?php echo $date_demain; ?>">
                        <br>
                        <br>
                        <label><b>Description</label>
                        <input type="text" name="description" maxlength="200">
                        vider le champ <input type="checkbox" name="description_null" value ="default">
                        <br>
                        <br>
                        <label><b>Identifiant du client</label>
                        <select name='client'>
                        <option value="">Pas de mise à jour du client</option>
                        <?php
                        $query = "SELECT `CLIENT_NUMBER` FROM `CLIENT`";
                        $req = $bdd->prepare($query);
                        if($req->execute() === FALSE)
                        {
                            echo "Problème dans l'affichage des identifiants des clients <br><br>";
                        }
                        while ($tuple = $req->fetch(PDO::FETCH_ASSOC))
                        {
                            $client_id = $tuple['CLIENT_NUMBER'];
                            echo "<option value='".$client_id."'>".$client_id."</option>";
                        }
                        ?>
                        </select>
                        <br>
                        <br>
                        <label><b>Identifiant du manager</label>
                        <select name='manager'>
                        <option value="">Pas de mise à jour du manager</option>
                        <?php
                        $query = "SELECT `ID` FROM `MANAGER`";
                        $req = $bdd->prepare($query);
                        if($req->execute() === FALSE)
                        {
                            echo "Problème dans l'affichage des identifiants des managers <br><br>";
                        }
                        while ($tuple = $req->fetch(PDO::FETCH_ASSOC))
                        {
                            $manager_id = $tuple['ID'];
                            echo "<option value='".$manager_id."'>".$manager_id."</option>";
                        }
                        ?>
                        </select>
                        <br>
                        <br>
                        <label><b>Identifiant du planificateur d'événement</label>
                        <select name='event_planner'>
                        <option value="">Pas de mise à jour du planificateur d'événement</option>
                        <?php
                        $query = "SELECT `ID` FROM `EVENTPLANNER`";
                        $req = $bdd->prepare($query);
                        if($req->execute() === FALSE)
                        {
                            echo "Problème dans l'affichage des identifiants des planificateurs d'événement <br><br>";
                        }
                        while ($tuple = $req->fetch(PDO::FETCH_ASSOC))
                        {
                            $event_planner_id = $tuple['ID'];
                            echo "<option value='".$event_planner_id."'>".$event_planner_id."</option>";
                        }
                        ?>
                        </select>
                        <br>
                        <br>
                        <label><b>Identifiant du DJ</label>
                        <select name='dj'>
                        <option value="">Pas de mise à jour du dj</option>
                        <?php
                        $query = "SELECT `ID` FROM `DJ` WHERE `ID`";
                        $req = $bdd->prepare($query);
                        if($req->execute() === FALSE)
                        {
                            echo "Problème dans l'affichage des identifiants des DJ <br><br>";
                        }
                        while ($tuple = $req->fetch(PDO::FETCH_ASSOC))
                        {
                            $dj_id = $tuple['ID'];
                            echo "<option value='".$dj_id."'>".$dj_id."</option>";
                        }
                        ?>
                        </select>
                        <br>
                        <br>
                        <label><b>Theme</label>
                        <select name='theme'>
                        <option value="">Pas de mise à jour du theme</option>
                        <?php
                        $query = "SELECT `NAME` FROM `THEME`";
                        $req = $bdd->prepare($query);
                        if($req->execute() === FALSE)
                        {
                            echo "Problème dans l'affichage des nomss des thêmes <br><br>";
                        }
                        while ($tuple = $req->fetch(PDO::FETCH_ASSOC))
                        {
                            $theme = $tuple['NAME'];
                            echo "<option value='".$theme."'>".$theme."</option>";
                        }
                        ?>
                        </select>
                        <br>
                        <br>
                        <label><b>Type</label>
                        <input type="text" name="type" maxlength="30">
                        <br>
                        <br>
                        <label><b>Identifiant du lieu</label>
                        <select name='location'>
                        <option value="">Pas de mise à jour du lieu</option>
                        <?php
                        $query = "SELECT `ID` FROM `LOCATION`";
                        $req = $bdd->prepare($query);
                        if($req->execute() === FALSE)
                        {
                            echo "Problème dans l'affichage des identifiants des locations <br><br>";
                        }
                        while ($tuple = $req->fetch(PDO::FETCH_ASSOC))
                        {
                            $lieu_id = $tuple['ID'];
                            echo "<option value='".$lieu_id."'>".$lieu_id."</option>";
                        }
                        ?>
                        </select>
                        vider le champ <input type="checkbox" name="location_null">
                        <br>
                        <br>
                        <label><b>Frais de location</label>
                        <input type="number" name="rental_fee">
                        vider le champ <input type="checkbox" name="rental_fee_null">
                        <br>
                        <br>
                        <label><b>Playlist</label>
                        <select name='playlist'>
                        <option value="">Pas de mise à jour de la playlist</option>
                        <?php
                        $query = "SELECT `NAME` FROM `PLAYLIST`";
                        $req = $bdd->prepare($query);
                        if($req->execute() === FALSE)
                        {
                            echo "Problème dans l'affichage des noms de playlists <br><br>";
                        }
                        while ($tuple = $req->fetch(PDO::FETCH_ASSOC))
                        {
                            $playlist = $tuple['NAME'];
                            echo "<option value='".$playlist."'>".$playlist."</option>";
                        }
                        ?>
                        </select>
                        vider le champ <input type="checkbox" name="playlist_null">
                        <br>
                        <br>
                        <input type="submit" value="Modifier">
                    </p>
                </form>
            <?php
            }
        }
        
        bouton_menu();
        bouton_deconnexion();
        ?>
</html>