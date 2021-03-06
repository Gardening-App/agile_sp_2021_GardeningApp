<?php
    if (!session_id()) {
        session_start();
      }

	require("dbConnect.php");
	require("callQuery.php");

    function getLastID($pdo) {
        $sql = "SELECT max(layoutID) AS ID FROM layout";
        $errorMessage = "Error fetching ID";
        $response = callQuery($pdo, $sql, $errorMessage);
        while ($row = $response->fetch()) {
            $layoutID = $row['ID'];
        }

        return $layoutID;

    function deleteFromShapes($pdo, $layoutID) {
        echo ($layoutID);
        $sql = "DELETE FROM shape
        WHERE layoutID = $layoutID";
        //echo("<script>alert($layoutID)</script>");
        $errorMessage = "Error removing shapes";
        callQuery($pdo, $sql, $errorMessage);
    }
    }

    //INSERT INTO `gardengnomes`.`layout` (`userID`, `layoutName`) VALUES ('1', 'Test3')

    if ($_POST['operation'] == 'write') {

        $layoutID = $_POST['oldID'];

        // Create entry for layout if it's new
        if ($_POST['oldID'] == '-1') {

            $sql = "INSERT INTO layout (userID, layoutName, layoutWidth, layoutHeight) VALUES (?, ?, ?, ?)";

            $pdo->beginTransaction();
            $preppedSql = $pdo->prepare($sql);
            $preppedSql->execute([$_SESSION[userID], $_POST[name], $_POST[width], $_POST[height]]);
            $pdo->commit();

            // Get most recent entry to get ID 
            $layoutID = getLastID($pdo);

        } else {
            // Set width in height incase changed
           // UPDATE layout SET layoutWidth = 44, layoutHeight = 44 WHERE (`layoutID` = '6');
            $sql = "UPDATE layout SET layoutWidth = $_POST[width], layoutHeight = $_POST[height] WHERE
            (layoutID = $layoutID);";

            $pdo->beginTransaction();
            $preppedSql = $pdo->prepare($sql);
            $preppedSql->execute();
            $pdo->commit();



            // Clear shapes before saving new ones to id
            $sql = "DELETE FROM shape
            WHERE layoutID = $layoutID";
            $errorMessage = "Error removing shapes";
            callQuery($pdo, $sql, $errorMessage);
        }
        

        // Create entries for each crop in layout
        foreach($_POST[crops] as $crop) {
            $sql = "INSERT INTO shape (x1, y1, x2, y2, layoutID, cropID)
                    VALUES (?, ?, ?, ?, ?, ?)";
            $pdo->beginTransaction();
            $preppedSql = $pdo->prepare($sql);
            $preppedSql->execute([$crop[0], $crop[1], $crop[2], $crop[3], $layoutID, $crop[4]]);
            $pdo->commit(); 
        }

    }
    
    if ($_POST['operation'] == 'lastID') {
        $lastID = getLastID($pdo);
        echo("$lastID");
        return $lastID;
    }

    if ($_POST['operation'] == 'delete') {
        $layoutID = $_POST['layoutID'];
        
        echo ($layoutID);
        $sql = "DELETE FROM layout
        WHERE layoutID = $layoutID";
        $errorMessage = "Error removing shapes";
        callQuery($pdo, $sql, $errorMessage);

        $sql = "DELETE FROM shape
        WHERE layoutID = $layoutID";
        $errorMessage = "Error removing shapes";
        callQuery($pdo, $sql, $errorMessage);

        //deleteFromShapes($pdo, $layoutID);
    }

    if ($_POST['operation'] == 'saveHarvest') {
        $sql = "INSERT INTO harvest (userID, cropID, sqFeet, poundsProduced)
                    VALUES (?, ?, ?, ?);";
        $pdo->beginTransaction();
        $preppedSql = $pdo->prepare($sql);
        $preppedSql->execute([$_POST['userID'], $_POST['cropID'], $_POST['sqFeet'], $_POST['poundsProduced']]);
        $pdo->commit(); 
    }

    if ($_POST['operation'] == 'deleteComment') {

        $commentId = $_POST['commentId'];
        
        $sqlDelete = "DELETE FROM social 
                      WHERE socialID = (?)";

        $preppedSql = $pdo->prepare($sqlDelete);

        $preppedSql->execute([$commentId]);
    }
?>
