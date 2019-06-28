<?php
// ---------------------------------------------------------------------------------------------
// This service inserts, updates and deletes all kind of entities into the tourdb
// Refer to the service getObject.php for the data retrieval
// 
// Parameters:
// login: user  login name
// objectType:  type of object to be worked (put) - currently track
// putObj:      Object to be put
// requestType: type of request (ins, upd, del)
// sessionId:   Session ID
// trackWaypArray: Array with waypoints assigned to tracks
// trackPartArray: Array with participants assigned to tracks

// Return object
// status
// message
// 
$debugLevel = 3; 

// Set variables and parameters
include("config.inc.php");                                              // include config file
include("coord_funct.inc.php");                                         // include coord calc functions
date_default_timezone_set('Europe/Zurich');                             // must be set when using time functions

$loopSize = 5000;                                                       // Number of trkPts inserted in one go

// Open file to write log
$logFileName = dirname(__FILE__) . "/../log/putObject.log";            // Assign file location
if ( $debugLevel >= 1 ) {
    $logFile = @fopen($logFileName,"a");                               // open log file handler 
    fputs($logFile, "\r\n============================================================\r\n");    
    fputs($logFile, "putObject.php started: " . date("Ymd-H:i:s", time()) . "\r\n");    
}

//Receive the RAW post data.
$receivedData = json_decode ( file_get_contents('php://input'), true );

// read received INPUT object
//$trackobj = array();                                                // array storing track data in array
$login = $receivedData["login"];
$objectType = $receivedData["objectType"];
$putObj = $receivedData["putObj"];
$requestType = $receivedData["requestType"];                                // temp = temporary creation; save = final storage; cancel = cancel operation / delete track & track points
$sessionId = $receivedData["sessionId"];                            // ID of current user session - required to make site multiuser capable
$trackObj = $receivedData["putObj"];                              // Array of track data 
$trackWaypArray = $receivedData["trackWaypArray"];            // Array of waypoiunts selected
$trackPartArray = $receivedData["trackPartArray"];            // Array of participants selected

if ($debugLevel >= 3) fputs($logFile, "Line 50: sessionId: $sessionId\r\n");    

if ( $requestType == "upd") {
    // ---------------------------------------------------------------------------------
    // request type is "update" meaning that the user has modified a record
    // ---------------------------------------------------------------------------------

    fputs($logFile, "Line 57: upd entered\r\n");  

    if ( $debugLevel >= 3) fputs($logFile, "Line 197: sessionId: $sessionId - requestType: $requestType - login: $login\r\n");  

    // Part 1: Update tracks record in database
    // ----------------------------------------
    
    // Create SQL statement to update track 
    $sql = " UPDATE `tourdb2_prod`.`tbl_tracks` SET ";

    // Loop through received track object and add to SQL statement
    foreach ($putObj as $dbField => $content) {                       // Generate update statement
        if ( $dbField == 'trkId' ) {
            $trkId = $content;
        } else {
            $sql .= "`$dbField` = '$content',";
        }
    }
    
    $sql = substr($sql,0,strlen($sql)-1);                               // remove last ,
    $sql .= " WHERE trkId = $trkId";

    if ($debugLevel >= 3) fputs($logFile, "Line 79 Update Track - sql: $sql\r\n");

    // run SQL and handle error
    if ($conn->query($sql) === TRUE)                                    // run sql against DB
    {
        if ( $debugLevel >= 3) fputs($logFile, "Line 84 - New track inserted successfully: ID = $trkId\r\n");
    } else {
        fputs($logFile, "Line 86 - Error inserting trkPt: $conn->error\r\n");
        $message = "Error inserting Track: $conn->error";

        $outObject = array (
            'status'=>'NOK',                                            // add err status to return object
            'message'=> $message  
        );
        echo json_encode($outObject); 
        return;
    } 
    
    // Part 2: Delete tbl_track_wayp records before insert
    // ---------------------------------------------------
        
    // count number of items
    /*
    $countItems = 0;
    if ( $debugLevel >= 3 ) fputs($logFile, "Line 102 - trackWaypArray: ". sizeof($trackWaypArray). "\r\n");

    for ( $i=0; $i < sizeof($trackWaypArray); $i++ ) {               // loop through records in array
        if ( $trackWaypArray[$i]["itemType"] == "peak" || 
             $trackWaypArray[$i]["itemType"] == "wayp" || 
             $trackWaypArray[$i]["itemType"] == "loca" ) {
            $countItems += 1;  
        }
    }

    if ( $debugLevel >= 3 ) fputs($logFile, "Line 112 - countItems(wayp): $countItems\r\n");

    // only enter into code section when at least one item 
    if ( $countItems > 0 ) {    
    */
        $sql = "DELETE FROM `tourdb2_prod`.`tbl_track_wayp` ";
        $sql .= "WHERE `tbl_track_wayp`.`trwpTrkId` = $trkId";

        // run SQL and handle error
        if ( $conn->query($sql) === TRUE )                              // run sql against DB
        {
            if ( $debugLevel >= 6) fputs($logFile, "Line 123 - Records in tbl_track_wayp for waypoints successfully deleted \r\n");
        } else {
            fputs($logFile, "Line 125 - Error deleting trkPt: $conn->error\r\n");
            fputs($logFile, "Line 126 - sql: $sql\r\n");
            // write output array
            $outObject = array (
                'status'=>'NOK',                                        // add err status to return object
                'message'=>'Error deleting tbl_track_wayp for peaks: ' . $conn->error,  
            );                                                          // add error message to return object
            echo json_encode($outObject); 
            return;
        }

        if ($debugLevel >= 3) fputs($logFile, "Line 136 Delete tbl_track_wayp - sql: $sql\r\n");
    //}

    // Part 3: Insert records to tbl_track_wayp for wayp
    // --------------------------------------------------

    // count items to be inserted ( items with disp_f set to 0 are not counted/inserted )
    $countItems = 0;
    for ( $i=0; $i < sizeof($trackWaypArray); $i++ ) {               // loop through records in array
        if ( $trackWaypArray[$i]["itemType"] == "peak" && ( $trackWaypArray[$i]["disp_f"] == 1 ) || 
                $trackWaypArray[$i]["itemType"] == "wayp" && ( $trackWaypArray[$i]["disp_f"] == 1 ) || 
                $trackWaypArray[$i]["itemType"] == "loca" && ( $trackWaypArray[$i]["disp_f"] == 1 ) ) {
            $countItems += 1;  
        }
    }
    
    if ( $countItems > 0 ) { 
        //create SQL statement  
        $sql = "INSERT INTO tbl_track_wayp (trwpTrkId, trwpWaypID, trwpReached_f) VALUES ";
        for ( $i=0; $i < sizeof($trackWaypArray); $i++ ) {                   // loop through records in array
            if ( $trackWaypArray[$i]["disp_f"] == 1 && ( $trackWaypArray[$i]["itemType"] == "peak"  || 
                $trackWaypArray[$i]["itemType"] == "loca" || $trackWaypArray[$i]["itemType"] == "wayp" )) {                 // disp_f = 1 (true) when user has not deleted peak on UI
                $waypRun = true;
                $sql .= "(" . $trkId . "," . $trackWaypArray[$i]["itemId"] . "," . $trackWaypArray[$i]["reached_f"] . "),";  
            }
        }
        $sql = substr( $sql, 0, strlen($sql)-1 );                       // trim last unnecessary ,
        
        if ( $debugLevel >= 3) fputs($logFile, "Line 164 Insert tbl_track_wayp - sql: " . $sql . "\r\n");
        
        // run SQL and handle error
        if ( $conn->query($sql) === TRUE )                              // run sql against DB
        {
            if ( $debugLevel >= 6) fputs($logFile, "Line 169 - New record in tbl_track_wayp for peaks successfully inserted \r\n");
        } else {
            fputs($logFile, "Line 171 - Error inserting trkPt: $conn->error\r\n");
            fputs($logFile, "Line 172 - sql: $sql\r\n");
            // write output array
            $outObject = array (
                'status'=>'NOK',                                        // add err status to return object
                'message'=>'Error inserting tbl_track_wayp for peaks: ' . $conn->error,  
            );                                                          // add error message to return object
            echo json_encode($outObject); 
            return;
        }
    }

    // Part 4: Delete trb_track_part before insert
    // -------------------------------------------
    
    // count number of items in track participants array
    /*
    $countItems = 0;
    for ( $i=0; $i < sizeof($trackPartArray); $i++ ) {               // loop through records in array
        if ( $trackPartArray[$i]["itemType"] == "part" ) {           // disp_f = true when user has not deleted peak on UI
            $countItems += 1;  
            fputs($logFile, "Line 190 - itemName: " . $trackPartArray[$i]["itemName"] . "\r\n");
        }
    }

    if ( $debugLevel >= 3) fputs($logFile, "Line 195 - countItems(part): $countItems\r\n");

    // only enter into code section when at least one item 
    if ( $countItems > 0 ) {  
        
        if ( $debugLevel >= 3) fputs($logFile, "Line 681 - countItems grösser null \r\n");
    */
        $sql = "DELETE FROM `tourdb2_prod`.`tbl_track_part` ";
        $sql .= "WHERE `tbl_track_part`.`trpaTrkId` = $trkId";

        // run SQL and handle error
        if ( $conn->query($sql) === TRUE )                              // run sql against DB
        {
            if ( $debugLevel >= 3) fputs($logFile, "Line 210 - Records in tbl_track_part successfully deleted \r\n");
        } else {
            fputs($logFile, "Line 212 - Error deleting tbl_track_part: $conn->error\r\n");
            fputs($logFile, "Line 213 - sql: $sql\r\n");
            // write output array
            $outObject = array (
                'status'=>'NOK',                                        // add err status to return object
                'message'=>'Error deleting tbl_track_part: ' . $conn->error,  
            );                                                          // add error message to return object
            echo json_encode($outObject); 
            return;
        }
    //}

    if ($debugLevel >= 3) fputs($logFile, "Line 224 Delete tbl_track_part - sql: $sql\r\n");

    // Part 5: Insert records to tbl_track_part
    // ----------------------------------------

    // count items to be inserted (items where disp_f is set to 0 are not counted / inserted)

    if ( $debugLevel >= 3 ) fputs($logFile, "Line 231 - countItems(wayp): $countItems\r\n");

    $countItems = 0;
    for ( $i=0; $i < sizeof($trackPartArray); $i++ ) {               // loop through records in array
        if ( $trackPartArray[$i]["itemType"] == "part" && $trackPartArray[$i]["disp_f"] == 1 ) {                 // disp_f = true when user has not deleted peak on UI
            $countItems += 1;  
            fputs($logFile, "Line 232 - itemName: " . $trackPartArray[$i]["itemName"] . "\r\n");
        }
    }

    if ( $countItems > 0 ) { 
        //create SQL statement  
        $sql = "INSERT INTO tbl_track_part (trpaTrkId, trpaPartID) VALUES ";
        $i=0;
        for ( $i; $i < sizeof($trackPartArray); $i++ ) {             // loop through records in array
            if ( $trackPartArray[$i]["disp_f"] == 1 && $trackPartArray[$i]["itemType"] == "part" ) {      // disp_f = true when user has not deleted peak on UI
                $sql .= "(" . $trkId . "," . $trackPartArray[$i]["itemId"] . ")," ;
            }
        }
        $sql = substr( $sql, 0, strlen($sql)-1 );                       // trim last unnecessary ,
        
        if ( $debugLevel >= 3) fputs($logFile, "Line 730 Insert tbl_track_wyp - sql: " . $sql . "\r\n");
        
        // run SQL and handle error
        if ( $conn->query($sql) === TRUE )                              // run sql against DB
        {
            if ( $debugLevel >= 3) fputs($logFile, "Line 735 - New record in tbl_track_wayp for peaks successfully inserted \r\n");
        } else {
            fputs($logFile, "Line 737 - Error inserting trkPt: $conn->error\r\n");
            fputs($logFile, "Line 738 - sql: $sql\r\n");
            // write output array
            $outObject = array (
                'status'=>'NOK',                                        // add err status to return object
                'message'=>'Error inserting tbl_track_wayp for peaks: ' . $conn->error,  
            );                                                          // add error message to return object
            echo json_encode($outObject); 
            return;
        }
    }

    // write output array
    $outObject = array (
        'status'=>'OK',                                                 // add err status to return object
        'message'=>"New track inserted successfully: ID = $trkId",      // add error message to return object
        'trkId'=>$trkId 
    );

    // Echo output array to client
    echo json_encode($outObject);  
    exit;      
}
?>