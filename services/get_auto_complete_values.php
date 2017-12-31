<?php
date_default_timezone_set('Europe/Zurich');

//DEBUG
$debugLevel = 5; // 0 = off, 1 = min, 3 = a lot, 5 = all 

if ($debugLevel >= 1){
    $fp = @fopen("log/getValues.log","a"); // TASK: Add Date/Time
    fputs($fp, "=================================================================\r\n");
    fputs($fp, date("Ymd-H:i:s", time()) . "-Line 8: seg_getValues.php opened \r\n"); 
};
    include("config.inc.php");  //include config file

    if ($debugLevel >= 3){
        fputs($fp, 'Line 14: field: ' . $_GET["field"] . "\r\n");
        fputs($fp, 'Line 15: term: ' . $_GET["term"] . "\r\n");
    };

    // ================= segTypeFID
    if ($_GET["field"] == "segTypeFID") {
        $whereClause = " WHERE stypName LIKE '%" . $_GET["term"] . "%'";
        $sql = "SELECT stypCode, stypName FROM tbl_segmenttypes";
        $sql .= $whereClause;

        if ($debugLevel >= 3){
            fputs($fp, 'Line 20: sql: ' . $sql . "\r\n");
        };
        $results = $conn->prepare($sql);
        $results->execute(); //Execute prepared Query
        $results->bind_result($ID, $Short); //bind variables to prepared statement

        $json = array();
        while($results->fetch()) {
            $res = array(
                'label' => $Short,
                'id' => $ID,
            );
            array_push($json, $res);
        }

        $jsonstring = json_encode($json);
        if ($debugLevel >= 5){
            fputs($fp, 'Line 38: jsonstring: ' . $jsonstring . "\r\n");
        };
        echo $jsonstring;
        die();
    };

    // ================= segSourceFID
    if ($_GET["field"] == "segSourceFID") {
        $whereClause = " WHERE srcName LIKE '%" . $_GET["term"] . "%'";
        $sql = "SELECT srcCode, srcName FROM tbl_sources";
        $sql .= $whereClause;

        if ($debugLevel >= 3){
            fputs($fp, 'Line 55: sql: ' . $sql . "\r\n");
        };
        $results = $conn->prepare($sql);
        $results->execute(); //Execute prepared Query
        $results->bind_result($ID, $Short); //bind variables to prepared statement

        $json = array();
        while($results->fetch()) {
            $res = array(
                'label' => $Short,
                'id' => $ID,
            );
            array_push($json, $res);
        }

        $jsonstring = json_encode($json);
        if ($debugLevel >= 5){
            fputs($fp, 'Line 69: jsonstring: ' . $jsonstring . "\r\n");
        };
        echo $jsonstring;
        die();
    };

    // ================= getWaypShortShort
    if ($_GET["field"] == "getWaypShort") {
        $whereClause = " WHERE waypNameShort LIKE '%" . $_GET["term"] . "%'";
        $sql = "SELECT waypID, waypNameShort FROM tbl_waypoints";
        $sql .= $whereClause;

        if ($debugLevel >= 3){
            fputs($fp, 'Line 86: sql: ' . $sql . "\r\n");
        };
        $results = $conn->prepare($sql);
        $results->execute(); //Execute prepared Query
        $results->bind_result($ID, $Short); //bind variables to prepared statement

        $json = array();
        while($results->fetch()) {
            $res = array(
                'label' => $Short,
                'id' => $ID,
            );
            array_push($json, $res);
        }

        $jsonstring = json_encode($json);
        if ($debugLevel >= 5){
            fputs($fp, 'Line 103: jsonstring: ' . $jsonstring . "\r\n");
        };
        echo $jsonstring;
        die();
    };

    // ================= getWaypLong
    if ($_GET["field"] == "getWaypLong") {
        $whereClause = " WHERE waypNameLong LIKE '%" . $_GET["term"] . "%'";
        $sql = "SELECT waypID, waypNameLong FROM tbl_waypoints";
        $sql .= $whereClause;

        if ($debugLevel >= 3){
            fputs($fp, 'Line 86: sql: ' . $sql . "\r\n");
        };
        $results = $conn->prepare($sql);
        $results->execute(); //Execute prepared Query
        $results->bind_result($ID, $Short); //bind variables to prepared statement

        $json = array();
        while($results->fetch()) {
            $res = array(
                'label' => $Short,
                'id' => $ID,
            );
            array_push($json, $res);
        }

        $jsonstring = json_encode($json);
        if ($debugLevel >= 5){
            fputs($fp, 'Line 103: jsonstring: ' . $jsonstring . "\r\n");
        };
        echo $jsonstring;
        die();
    };

// ================= regionID
    if ($_GET["field"] == "regionID") {
        fputs($fp, "dinne \r\n");
        $whereClause = " WHERE regNameLong LIKE '%" . $_GET["term"] . "%'";
        $sql = "SELECT regID, regNameLong FROM tbl_regions";
        $sql .= $whereClause;

        if ($debugLevel >= 3){
            fputs($fp, 'Line 116: sql: ' . $sql . "\r\n");
        };
        $results = $conn->prepare($sql);
        $results->execute(); //Execute prepared Query
        $results->bind_result($ID, $Short); //bind variables to prepared statement

        $json = array();
        while($results->fetch()) {
            $res = array(
                'label' => $Short,
                'id' => $ID,
            );
            array_push($json, $res);
        }

        $jsonstring = json_encode($json);
        if ($debugLevel >= 5){
            fputs($fp, 'Line 133: jsonstring: ' . $jsonstring . "\r\n");
        };
        echo $jsonstring;
        die();
    }; 

    // ================= areaID
    if ($_GET["field"] == "areaID") {
        $whereClause = " WHERE areaNameLong LIKE '%" . $_GET["term"] . "%'";
        $sql = "SELECT areaID, areaNameLong FROM tbl_areas";
        $sql .= $whereClause;
        $sql .= " ORDER BY areaNameLong";

        if ($debugLevel >= 3){
            fputs($fp, 'Line 146: sql: ' . $sql . "\r\n");
        };
        $results = $conn->prepare($sql);
        $results->execute(); //Execute prepared Query
        $results->bind_result($ID, $label); //bind variables to prepared statement

        $json = array();
        while($results->fetch()) {
            $res = array(
                'label' => $label,
                'id' => $ID,
            );
            array_push($json, $res);
        }

        $jsonstring = json_encode($json);
        if ($debugLevel >= 5){
            fputs($fp, 'Line 163: jsonstring: ' . $jsonstring . "\r\n");
        };
        echo $jsonstring;
        die();
    };    

    // ================= gradeID
    if ($_GET["field"] == "gradeID") {
        $whereClause = " WHERE grdCodeID LIKE '%" . $_GET["term"] . "%' and grdType ='Schwierigkeitsgrad' 
            order by grdSort";
        $sql = "SELECT grdCodeID, grdGroup FROM tbl_grades ";
        $sql .= $whereClause;

        if ($debugLevel >= 3){
            fputs($fp, 'Line 177: sql: ' . $sql . "\r\n");
        };
        $results = $conn->prepare($sql);
        $results->execute(); //Execute prepared Query
        $results->bind_result($ID, $Short); //bind variables to prepared statement

        $json = array();
        while($results->fetch()) {
            $res = array(
                'label' => $ID,
                'id' => $ID,
            );
            array_push($json, $res);
        }

        $jsonstring = json_encode($json);
        if ($debugLevel >= 5){
            fputs($fp, 'Line 194: jsonstring: ' . $jsonstring . "\r\n");
        };
        echo $jsonstring;
        die();
    };    

    // ================= climbGradeID
    if ($_GET["field"] == "climbGradeID") {
        $whereClause = " WHERE grdCodeID LIKE '%" . $_GET["term"] . "%' and grdType ='Klettergrad' 
            order by grdSort";
        $sql = "SELECT grdCodeID, grdGroup FROM tbl_grades ";
        $sql .= $whereClause;

        if ($debugLevel >= 3){
            fputs($fp, 'Line 177: sql: ' . $sql . "\r\n");
        };
        $results = $conn->prepare($sql);
        $results->execute(); //Execute prepared Query
        $results->bind_result($ID, $Short); //bind variables to prepared statement

        $json = array();
        while($results->fetch()) {
            $res = array(
                'label' => $ID,
                'id' => $ID,
            );
            array_push($json, $res);
        }

        $jsonstring = json_encode($json);
        if ($debugLevel >= 5){
            fputs($fp, 'Line 194: jsonstring: ' . $jsonstring . "\r\n");
        };
        echo $jsonstring;
        die();
    };    

    // ================= eHaft
    if ($_GET["field"] == "eHaft") {
        $whereClause = " WHERE grdCodeID LIKE '%" . $_GET["term"] . "%' and grdType ='Ernsthaftigkeit' 
            order by grdSort";
        $sql = "SELECT grdCodeID, grdGroup FROM tbl_grades ";
        $sql .= $whereClause;

        if ($debugLevel >= 3){
            fputs($fp, 'Line 240: sql: ' . $sql . "\r\n");
        };
        $results = $conn->prepare($sql);
        $results->execute(); //Execute prepared Query
        $results->bind_result($ID, $Short); //bind variables to prepared statement

        $json = array();
        while($results->fetch()) {
            $res = array(
                'label' => $ID,
                'id' => $ID,
            );
            array_push($json, $res);
        }

        $jsonstring = json_encode($json);
        if ($debugLevel >= 5){
            fputs($fp, 'Line 257: jsonstring: ' . $jsonstring . "\r\n");
        };
        echo $jsonstring;
        die();
    };    
?>

