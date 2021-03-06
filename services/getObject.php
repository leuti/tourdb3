<?php
// ---------------------------------------------------------------------------------------------
// PHP script is extracting requested object from the database and returns all  
// its fields to the UI
//
// Input Params:
// -------------
// objectID: Unique identifier of the object to be retrieved
// objectType: Type of DB object to be retrieved (currently only trk)
// requestType: Type of request (get, upd, del)
// 
// Output Params:
// --------------
// status: OK or ERR
// message: Message about usrId result
// returnObj: Object to be returned (currently only track with trackPartArray and trackWaypArray) 
//
// Created: 25.6.2019 - Daniel Leutwyler
// ---------------------------------------------------------------------------------------------

// Set variables and parameters
include("tourdb_config.php");                                        // include config file
date_default_timezone_set("Europe/Zurich");                         // must be set when using time functions

// Open file to write log
$importGpxLog = dirname(__FILE__) . "/../log/getObject.log";        // Assign file location
if ( $debugLevel >= 1 ) {
    $logFile = @fopen($importGpxLog,"a");                               // open log file handler 
    fputs($logFile, "\r\n============================================================\r\n");    
    fputs($logFile, "getObject.php started: " . date("Ymd-H:i:s", time()) . "\r\n");    
}

// variables passed on by client (as JSON object)
$receivedData = json_decode ( file_get_contents("php://input"), true );
$objectType = $receivedData["objectType"];        
$requestType = $receivedData["requestType"];                            
$objectID = $receivedData["objectId"];                          // where statement to select tracks to be displayed

if ( $debugLevel >= 1 ) {
    fputs($logFile, "Request params: objectType: $objectType | requestType: $requestType | objectID: $objectID\r\n");    
}

if ( $objectType == "trk" ) {

    if ( $requestType == "get" ) {

        $sql = "SELECT trkId, trkTrackName, trkRoute, 
            DATE_FORMAT ( trkDateBegin, '%Y-%m-%d %H:%i:%s') AS trkDateBegin, 
            DATE_FORMAT ( trkDateFinish, '%Y-%m-%d %H:%i:%s') AS trkDateFinish, 
            trkTypeFid, trkSubtypeFid, trkOrg, trkEvent, 
            trkRemarks, trkDistance, trkTimeOverall, trkTimeToPeak, 
            trkTimeToFinish, trkStartEle, trkPeakEle, trkPeakTime, 
            trkLowEle, trkLowTime, trkFinishEle, trkFinishTime, 
            trkGrade, trkMeterUp, trkMeterDown, trkCountry, 
            trkLoginName, trkCoordinates, trkCoordTop, trkCoordBottom, 
            trkCoordLeft, trkCoordRight 
            FROM tbl_tracks "; 
        $sql .= "WHERE trkId = " . $objectID;
        
        if ($debugLevel >= 3) fputs($logFile, "Line " . __LINE__ . ": sql: " . $sql . "\r\n");

        $records = mysqli_query($conn, $sql);                       // run query against DB and store results in $records
        $objectRecord = mysqli_fetch_assoc($records);               // create array containing $results

        // Select records from tbl_trk_wayp
        $sql =  "SELECT trwp.trwpId, trwp.trwpTrkId, trwp.trwpWaypId, ";
        $sql .= "wp.waypNameLong, wp.waypTypeFid, trwp.trwpReached_f ";
        $sql .= "FROM tbl_track_wayp trwp ";
        $sql .= "INNER JOIN tbl_waypoints wp ON trwp.trwpWaypId = wp.waypID ";
        $sql .= "WHERE trwpTrkId = " . $objectID;

        if ($debugLevel >= 3) fputs($logFile, "Line " . __LINE__ . ": sql: " . $sql . "\r\n");

        $records = mysqli_query($conn, $sql);                       // run query against DB and store results in $records

        $trackWaypArray = array();
        // select single line result
        while ( $trackWaypRecord = mysqli_fetch_assoc($records) ) {
            
            // Evaluate type of waypoint
            if ( $trackWaypRecord["waypTypeFid"] == "37" ) {
                $itemType = "peak";
            } else if ( $trackWaypRecord["waypTypeFid"] == "36" ) {
                $itemType = "loca";
            } else {
                $itemType = "wayp";
            } 

            $trkWpLine = array (
                "disp_f" => 1,
                "itemId" => $trackWaypRecord["trwpWaypId"],
                "itemName" => $trackWaypRecord["waypNameLong"],                                             // add inner join
                "itemType" => $itemType,
                "reached_f" => intval($trackWaypRecord["trwpReached_f"])
            );
            array_push ($trackWaypArray, $trkWpLine);
        }

        // Select records from tbl_trk_part
        // --------------------------------
        $sql =  "SELECT trpa.trpaId, trpa.trpaTrkId, trpa.trpaPartId, ";
        $sql .= "part.prtFirstName, part.prtLastName ";
        $sql .= "FROM tbl_track_part trpa ";
        $sql .= "INNER JOIN tbl_part part ON trpa.trpaPartId = part.prtID ";
        $sql .= "WHERE trpaTrkId = " . $objectID;

        if ($debugLevel >= 3) fputs($logFile, "Line " . __LINE__. " sql: " . $sql . "\r\n");

        $records = mysqli_query($conn, $sql);
        
        $trackPartArray = array();
        // select single line result
        while ( $trackPartRecord = mysqli_fetch_assoc($records) ) {
            
            if ($debugLevel >= 3) fputs($logFile, "Line " . __LINE__ . ": trkId:" . $trackPartRecord["trpaTrkId"] 
                . "FirstName: " . $trackPartRecord["prtFirstName"] . "\r\n");

            $trpaLine = array (
                "disp_f" => 1,
                "itemType" => "part",
                "itemId" => $trackPartRecord["trpaPartId"],
                "itemName" => $trackPartRecord["prtFirstName"] . " " . $trackPartRecord["prtLastName"]
            );
            array_push ($trackPartArray, $trpaLine);
        }

        $returnObject = array (
            "status"=>"OK",
            "message"=>"",
            "trackObj"=>$objectRecord,
            "trackWaypArray"=>$trackWaypArray,
            "trackPartArray"=>$trackPartArray
            );

        echo json_encode($returnObject);                           // echo JSON object to client
    }
} 
?>