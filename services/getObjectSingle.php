<?php
// ---------------------------------------------------------------------------------------------
// PHP script is extracting requested object from the database and returns all  
// its fields to the UI
//

// Created: 4.4.2018 - Daniel Leutwyler
// ---------------------------------------------------------------------------------------------
// Action:
// * 

// Set variables and parameters
include("config.inc.php");                                        // include config file
date_default_timezone_set('Europe/Zurich');                         // must be set when using time functions

// Open file to write log
$importGpxLog = dirname(__FILE__) . "/../log/getObjectSingle.log";        // Assign file location
if ( $debugLevel >= 1 ) {
    $logFile = @fopen($importGpxLog,"a");                               // open log file handler 
    fputs($logFile, "\r\n============================================================\r\n");    
    fputs($logFile, "getObjectSingle.php started: " . date("Ymd-H:i:s", time()) . "\r\n");    
}

// variables passed on by client (as JSON object)
$receivedData = json_decode ( file_get_contents('php://input'), true );
$itemType = $receivedData["itemType"];        
$requestType = $receivedData["requestType"];                            
$itemId = $receivedData["itemId"];                          // where statement to select tracks to be displayed

if ( $debugLevel >= 1 ) {
    fputs($logFile, "Request params: itemType: $itemType | requestType: $requestType | itemId: $itemId\r\n");    
}

if ( $itemType == "trk" ) {

    if ( $requestType == "select" ) {

        //if ($debugLevel >= 3) fputs($logFile, 'Line 90: gugus\r\n');

        // Select tracks 
        // -------------
        $sql = "SELECT * FROM tbl_tracks ";
        $sql .= "WHERE trkId = " . $itemId;

        $records = mysqli_query($conn, $sql);
        
        // select single line result
        $trackRecord = mysqli_fetch_assoc($records);

        // Select records from tbl_trk_wayp
        // --------------------------------
        $sql =  "SELECT trwp.trwpId, trwp.trwpTrkId, trwp.trwpWaypId, ";
        $sql .= "wp.waypNameLong, wp.waypTypeFID, trwp.trwpReached_f ";
        $sql .= "FROM tbl_track_wayp trwp ";
        $sql .= "INNER JOIN tbl_waypoints wp ON trwp.trwpWaypId = wp.waypID ";
        $sql .= "WHERE trwpTrkId = " . $itemId;

        $records = mysqli_query($conn, $sql);
        
        $trWpArray = array();
        // select single line result
        while ( $trkwpRecord = mysqli_fetch_assoc($records) ) {
            
            // Evaluate type of waypoint
            if ( $trkwpRecord["waypTypeFID"] == "5" ) {
                $itemType = "peak";
            } else if ( $trkwpRecord["waypTypeFID"] == "4" ) {
                $itemType = "loca";
            } else {
                $itemType = "wayp";
            } 

            if ($debugLevel >= 3) fputs($logFile, "Line 79: waypNameLong: " . $trkwpRecord["waypNameLong"] . "\r\n");

            $trkWpLine = array (
                "disp_f"=>"1",
                "itemId"=>$trkwpRecord["trwpWaypId"],
                "itemName"=>$trkwpRecord["waypNameLong"],                                             // add inner join
                "itemType"=>$itemType,
                "reached_f"=>$trkwpRecord["trwpReached_f"]
            );
            array_push ($trWpArray, $trkWpLine);
        }

        // Select records from tbl_trk_part
        // --------------------------------
        $sql =  "SELECT trpa.trpaId, trpa.trpaTrkId, trpa.trpaPartId, ";
        $sql .= "part.prtFirstName, part.prtLastName ";
        $sql .= "FROM tbl_track_part trpa ";
        $sql .= "INNER JOIN tbl_part part ON trpa.trpaPartId = part.prtID ";
        $sql .= "WHERE trpaTrkId = " . $itemId;

        if ($debugLevel >= 3) fputs($logFile, "Line 93: sql: " . $sql . "\r\n");

        $records = mysqli_query($conn, $sql);
        
        $trpaArray = array();
        // select single line result
        while ( $trpaRecord = mysqli_fetch_assoc($records) ) {
            
            if ($debugLevel >= 3) fputs($logFile, "Line 101: trkId: " . $trpaRecord["trpaTrkId"] 
                . "FirstName: " . $trpaRecord["prtFirstName"] . "\r\n");

            $trpaLine = array (
                "disp_f"=>"1",
                "itemType"=>"part",
                "itemId"=>$trpaRecord["trpaPartId"],
                "itemName"=>$trpaRecord["prtFirstName"] . " " . $trpaRecord["prtLastName"]
            );
            array_push ($trpaArray, $trpaLine);
        }

        $returnObject = array (
            "status"=>"OK",
            "message"=>"",
            "trackObj"=>$trackRecord,
            "trWpArray"=>$trWpArray,
            "trpaArray"=>$trpaArray
            );

        echo json_encode($returnObject);                           // echo JSON object to client
    }
} 
?>