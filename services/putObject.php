<?php
// ---------------------------------------------------------------------------------------------
// This service inserts, updates and deletes all kind of entities into the tourdb
// Refer to the service getObject.php for the data retrieval
// 
// Parameters:
// usrId: user      usrId
// objectType:      type of object to be worked (put) - currently track
// putObj:          Object to be put
// requestType:     type of request (ins, upd, del)
// sessionId:       Session ID
// trackWaypArray:  Array with waypoints assigned to tracks
// trackPartArray:  Array with participants assigned to tracks

// Return object
// status:          OK / NOK
// message:         Message about performed action
// trkId:           track ID

// Set variables and parameters
include("tourdb_config.php");                                              // include config file
date_default_timezone_set("Europe/Zurich");                             // must be set when using time functions

// Open file to write log
$logFileName = dirname(__FILE__) . "/../log/putObject.log";             // Assign file location
if ( $debugLevel >= 1 ) {
    $logFile = @fopen($logFileName,"a");                                // open log file handler 
    fputs($logFile, "\r\n============================================================\r\n");    
    fputs($logFile, "putObject.php started: " . date("Ymd-H:i:s", time()) . "\r\n");    
}

//Receive the RAW post data.
$receivedData = json_decode ( file_get_contents("php://input"), true );

// read received INPUT object
$usrId = $receivedData["usrId"];
$objectType = $receivedData["objectType"];
$receivedObj = $receivedData["putObj"];
$requestType = $receivedData["requestType"];                            // temp = temporary creation; save = final storage; cancel = cancel operation / delete track & track points
$sessionId = $receivedData["sessionId"];                                // ID of current user session - required to make site multiuser capable
$trackWaypArray = $receivedData["trackWaypArray"];                      // Array of waypoiunts selected
$trackPartArray = $receivedData["trackPartArray"];                      // Array of participants selected

if ($debugLevel >= 3) fputs($logFile, "Line " . __LINE__ . ": Input Params:\r\n");
if ($debugLevel >= 3) fputs($logFile, "   usrId: $usrId\r\n");
if ($debugLevel >= 3) fputs($logFile, "   objectType: $objectType\r\n");
if ($debugLevel >= 3) fputs($logFile, "   logrequestTypein: $requestType\r\n");
if ($debugLevel >= 3) fputs($logFile, "   sessionId: $sessionId\r\n");

if ( $requestType == "ins") {
// -----------------------------------------------------------------------
    // request type is "insert" meaning that the record has to be inserted
    // -------------------------------------------------------------------

    if ( $debugLevel >= 3) fputs($logFile, "Line " . __LINE__ . ": sessionId: $sessionId - requestType: $requestType - usrId: $usrId\r\n");  

    // Part 1: insert tracks record into database
    // ----------------------------------------
    
    if ( $debugLevel >= 3) fputs($logFile, "Line " . __LINE__ . ": Part 1 ins / insert tbl_tracks entered\r\n");

    // Create SQL statement to update track 
    $sql = " INSERT INTO `tourdb2_prod`.`tbl_tracks` (";

    // Loop through received track object and add to SQL statement
    foreach ($receivedObj as $dbField => $content) {                    // Generate update statement
        if ( $dbField != "trkId" ) {
            $sql .= "`$dbField`, ";
        } 
    }    

    $sql = substr($sql,0,strlen($sql)-2);                               // remove last ,
    $sql .= ") VALUES (";

    foreach ($receivedObj as $dbField => $content) {                    // Generate update statement
        if ( $dbField != "trkId" ) {
            $sql .= "'$content', ";
        }
    }    

    $sql = substr($sql,0,strlen($sql)-2);                               // remove last ,
    $sql .= ")";

    if ($debugLevel >= 3) fputs($logFile, "Line " . __LINE__ . ": Update Track - sql: $sql\r\n");

    // run SQL and handle error
    if ($conn->query($sql) === TRUE)                                    // run sql against DB
    {
        $trkId = $conn->insert_id;
        if ( $debugLevel >= 3) fputs($logFile, "Line " . __LINE__ . ": New track inserted\r\n");
    } else {
        fputs($logFile, "Line " . __LINE__ . ": Error inserting trkPt: $conn->error\r\n");
        $message = "Error inserting Track: $conn->error";

        $outObject = array (
            "status"=>"NOK",                                            // add err status to return object
            "message"=> $message  
        );
        echo json_encode($outObject); 
        return;
    } 
    
    // Part 2: Insert records to tbl_track_wayp for wayp
    // --------------------------------------------------

    if ( $debugLevel >= 3) fputs($logFile, "Line " . __LINE__ . ": Part 2 ins / insert tbl_track_wayp entered\r\n");
    
    // count items to be inserted ( items with disp_f set to 0 are not counted/inserted )
    $countItems = 0;
    for ( $i=0; $i < sizeof($trackWaypArray); $i++ ) {                  // loop through records in array
        if ( $trackWaypArray[$i]["itemType"] == "peak" && ( $trackWaypArray[$i]["disp_f"] == 1 ) || 
                $trackWaypArray[$i]["itemType"] == "wayp" && ( $trackWaypArray[$i]["disp_f"] == 1 ) || 
                $trackWaypArray[$i]["itemType"] == "loca" && ( $trackWaypArray[$i]["disp_f"] == 1 ) ) {
            $countItems += 1;  
        }
    }
    
    if ( $debugLevel >= 3) fputs($logFile, "Line " . __LINE__ . ": trkId: " . $trkId . "\r\n");
    
    if ( $countItems > 0 ) { 
        //create SQL statement  
        $sql = "INSERT INTO tbl_track_wayp (trwpTrkId, trwpWaypID, trwpReached_f) VALUES ";
        for ( $i=0; $i < sizeof($trackWaypArray); $i++ ) {              // loop through records in array
            if ( $trackWaypArray[$i]["disp_f"] == 1 && ( $trackWaypArray[$i]["itemType"] == "peak"  || 
                $trackWaypArray[$i]["itemType"] == "loca" || $trackWaypArray[$i]["itemType"] == "wayp" )) {                 // disp_f = 1 (true) when user has not deleted peak on UI
                if ( $trackWaypArray[$i]["reached_f"] == true ) {
                    $sql .= "(" . $trkId . "," . $trackWaypArray[$i]["itemId"] . "," . 1 . "),";  
                } else {
                    $sql .= "(" . $trkId . "," . $trackWaypArray[$i]["itemId"] . "," . 0 . "),";  
                }
            }
        }
        $sql = substr( $sql, 0, strlen($sql)-1 );                       // trim last unnecessary ,
        
        if ( $debugLevel >= 3) fputs($logFile, "Line " . __LINE__ . ": Insert tbl_track_wayp - sql: " . $sql . "\r\n");
        
        // run SQL and handle error
        if ( $conn->query($sql) === TRUE ) {                             // run sql against DB
            if ( $debugLevel >= 6) fputs($logFile, "Line " . __LINE__ . ": New record in tbl_track_wayp for peaks successfully inserted \r\n");
        } else {
            fputs($logFile, "Line " . __LINE__ . ": Error inserting trkPt: $conn->error\r\n");
            fputs($logFile, "Line " . __LINE__ . ": sql: $sql\r\n");
            // write output array
            $outObject = array (
                "status"=>"NOK",                                        // add err status to return object
                "message"=>"Error inserting tbl_track_wayp for peaks: " . $conn->error,  
            );                                                          // add error message to return object
            echo json_encode($outObject); 
            return;
        }
    }

    // Part 3: Insert records to tbl_track_part
    // ----------------------------------------

    if ( $debugLevel >= 3) fputs($logFile, "Line " . __LINE__ . ": Part 3 ins / insert tbl_track_part entered\r\n");

    // count items to be inserted (items where disp_f is set to 0 are not counted / inserted)
    if ( $debugLevel >= 3 ) fputs($logFile, "Line " . __LINE__ . ": countItems(wayp): $countItems\r\n");

    $countItems = 0;
    for ( $i=0; $i < sizeof($trackPartArray); $i++ ) {                  // loop through records in array
        if ( $trackPartArray[$i]["itemType"] == "part" && $trackPartArray[$i]["disp_f"] == 1 ) {   // disp_f = true when user has not deleted peak on UI
            $countItems += 1;  
            if ( $debugLevel >= 3 ) fputs($logFile, "Line " . __LINE__ . ": itemName: " . $trackPartArray[$i]["itemName"] . "\r\n");
        }
    }

    if ( $countItems > 0 ) { 
        //create SQL statement  
        $sql = "INSERT INTO tbl_track_part (trpaTrkId, trpaPartID) VALUES ";
        $i=0;
        for ( $i; $i < sizeof($trackPartArray); $i++ ) {                // loop through records in array
            if ( $trackPartArray[$i]["disp_f"] == 1 && $trackPartArray[$i]["itemType"] == "part" ) {      // disp_f = true when user has not deleted peak on UI
                $sql .= "(" . $trkId . "," . $trackPartArray[$i]["itemId"] . ")," ;
            }
        }
        $sql = substr( $sql, 0, strlen($sql)-1 );                       // trim last unnecessary ,
        
        if ( $debugLevel >= 3) fputs($logFile, "Line " . __LINE__ . ": Insert tbl_track_wyp - sql: " . $sql . "\r\n");
        
        // run SQL and handle error
        if ( $conn->query($sql) === TRUE )                              // run sql against DB
        {
            if ( $debugLevel >= 3) fputs($logFile, "Line " . __LINE__ . ": New record in tbl_track_wayp for peaks successfully inserted \r\n");
        } else {
            fputs($logFile, "Line " . __LINE__ . ": Error inserting trkPt: $conn->error\r\n");
            fputs($logFile, "Line " . __LINE__ . ": sql: $sql\r\n");
            // write output array
            $outObject = array (
                "status"=>"NOK",                                        // add err status to return object
                "message"=>"Error inserting tbl_track_wayp for peaks: " . $conn->error,  
            );                                                          // add error message to return object
            echo json_encode($outObject); 
            return;
        }
    }

    // write output array
    $outObject = array (
        "status"=>"OK",                                                 // add err status to return object
        "message"=>"New track successfully  inserted: ID = $trkId",     // add error message to return object
        "trkId"=>$trkId 
    );

    // Echo output array to client
    echo json_encode($outObject);  
    exit;  
     
}

else if ( $requestType == "upd") {
    // ---------------------------------------------------------------------------------
    // request type is "update" meaning that the user has modified a record
    // ---------------------------------------------------------------------------------

    if ( $debugLevel >= 3) fputs($logFile, "Line " . __LINE__ . ": sessionId: $sessionId - requestType: $requestType - usrId: $usrId\r\n");  

    // Part 1: Update tracks record in database
    // ----------------------------------------
    
    if ( $debugLevel >= 3) fputs($logFile, "Line " . __LINE__ . ": Part 1 upd / update tbl_tracks entered\r\n");

    // Create SQL statement to update track 
    $sql = " UPDATE `tourdb2_prod`.`tbl_tracks` SET ";

    // Loop through received track object and add to SQL statement
    foreach ($receivedObj as $dbField => $content) {                    // Generate update statement
        if ( $dbField == "trkId" ) {
            $trkId = $content;
        } else {
            $sql .= "`$dbField` = '$content',";
        }
    }
    
    $sql = substr($sql,0,strlen($sql)-1);                               // remove last ,
    $sql .= " WHERE trkId = $trkId";

    if ($debugLevel >= 3) fputs($logFile, "Line " . __LINE__ . ": Update Track - sql: $sql\r\n");

    // run SQL and handle error
    if ($conn->query($sql) === TRUE)                                    // run sql against DB
    {
        if ( $debugLevel >= 3) fputs($logFile, "Line " . __LINE__ . ": New track updated successfully: ID = $trkId\r\n");
    } else {
        fputs($logFile, "Line " . __LINE__ . ": Error inserting trkPt: $conn->error\r\n");
        $message = "Error inserting Track: $conn->error";

        $outObject = array (
            "status"=>"NOK",                                            // add err status to return object
            "message"=> $message  
        );
        echo json_encode($outObject); 
        return;
    } 
    
    // Part 2: Delete tbl_track_wayp records before insert
    // ---------------------------------------------------
    
    if ( $debugLevel >= 3) fputs($logFile, "Line " . __LINE__ . ": Part 2 upd / delete tbl_track_wayp entered\r\n");

    $sql = "DELETE FROM `tourdb2_prod`.`tbl_track_wayp` ";
    $sql .= "WHERE `tbl_track_wayp`.`trwpTrkId` = $trkId";

    // run SQL and handle error
    if ( $conn->query($sql) === TRUE )                                  // run sql against DB
    {
        if ( $debugLevel >= 6) fputs($logFile, "Line " . __LINE__ . ": Records in tbl_track_wayp for waypoints successfully deleted \r\n");
    } else {
        fputs($logFile, "Line " . __LINE__ . ": Error deleting trkPt: $conn->error\r\n");
        fputs($logFile, "Line " . __LINE__ . ": sql: $sql\r\n");
        // write output array
        $outObject = array (
            "status"=>"NOK",                                            // add err status to return object
            "message"=>"Error deleting tbl_track_wayp for peaks: " . $conn->error,  
        );                                                              // add error message to return object
        echo json_encode($outObject); 
        return;
    }

    if ($debugLevel >= 3) fputs($logFile, "Line " . __LINE__ . ": Delete tbl_track_wayp - sql: $sql\r\n");

    // Part 3: Insert records to tbl_track_wayp for wayp
    // --------------------------------------------------

    if ( $debugLevel >= 3) fputs($logFile, "Line " . __LINE__ . ": Part 2 upd / insert tbl_track_wayp entered\r\n");

    // count items to be inserted ( items with disp_f set to 0 are not counted/inserted )
    $countItems = 0;
    for ( $i=0; $i < sizeof($trackWaypArray); $i++ ) {                  // loop through records in array
        if ( $trackWaypArray[$i]["itemType"] == "peak" && ( $trackWaypArray[$i]["disp_f"] == 1 ) || 
                $trackWaypArray[$i]["itemType"] == "wayp" && ( $trackWaypArray[$i]["disp_f"] == 1 ) || 
                $trackWaypArray[$i]["itemType"] == "loca" && ( $trackWaypArray[$i]["disp_f"] == 1 ) ) {
            $countItems += 1;  
        }
    }
    
    if ( $countItems > 0 ) { 
        //create SQL statement  
        $sql = "INSERT INTO tbl_track_wayp (trwpTrkId, trwpWaypID, trwpReached_f) VALUES ";
        for ( $i=0; $i < sizeof($trackWaypArray); $i++ ) {              // loop through records in array
            if ( $trackWaypArray[$i]["disp_f"] == 1 && ( $trackWaypArray[$i]["itemType"] == "peak"  || 
                $trackWaypArray[$i]["itemType"] == "loca" || $trackWaypArray[$i]["itemType"] == "wayp" )) {   // disp_f = 1 (true) when user has not deleted peak on UI
                if ( $trackWaypArray[$i]["reached_f"] == true ) {
                    $sql .= "(" . $trkId . "," . $trackWaypArray[$i]["itemId"] . "," . 1 . "),";  
                } else {
                    $sql .= "(" . $trkId . "," . $trackWaypArray[$i]["itemId"] . "," . 0 . "),";  
                }
            }
        }
        $sql = substr( $sql, 0, strlen($sql)-1 );                       // trim last unnecessary ,
        
        if ( $debugLevel >= 3) fputs($logFile, "Line " . __LINE__ . ": Insert tbl_track_wayp - sql: " . $sql . "\r\n");
        
        // run SQL and handle error
        if ( $conn->query($sql) === TRUE )                              // run sql against DB
        {
            if ( $debugLevel >= 6) fputs($logFile, "Line " . __LINE__ . ": New record in tbl_track_wayp for peaks successfully inserted \r\n");
        } else {
            fputs($logFile, "Line " . __LINE__ . ": Error inserting trkPt: $conn->error\r\n");
            fputs($logFile, "Line " . __LINE__ . ": sql: $sql\r\n");
            // write output array
            $outObject = array (
                "status"=>"NOK",                                        // add err status to return object
                "message"=>"Error inserting tbl_track_wayp for peaks: " . $conn->error,  
            );                                                          // add error message to return object
            echo json_encode($outObject); 
            return;
        }
    }

    // Part 4: Delete trb_track_part before insert
    // -------------------------------------------
    
    if ( $debugLevel >= 3) fputs($logFile, "Line " . __LINE__ . ": Part 4 upd / delete tbl_track_part entered\r\n");

    $sql = "DELETE FROM `tourdb2_prod`.`tbl_track_part` ";
    $sql .= "WHERE `tbl_track_part`.`trpaTrkId` = $trkId";

    // run SQL and handle error
    if ( $conn->query($sql) === TRUE )                                  // run sql against DB
    {
        if ( $debugLevel >= 3) fputs($logFile, "Line " . __LINE__ . ": Records in tbl_track_part successfully deleted \r\n");
    } else {
        fputs($logFile, "Line " . __LINE__ . ": Error deleting tbl_track_part: $conn->error\r\n");
        fputs($logFile, "Line " . __LINE__ . ": sql: $sql\r\n");
        // write output array
        $outObject = array (
            "status"=>"NOK",                                            // add err status to return object
            "message"=>"Error deleting tbl_track_part: " . $conn->error,  
        );                                                              // add error message to return object
        echo json_encode($outObject); 
        return;
    }

    if ($debugLevel >= 3) fputs($logFile, "Line " . __LINE__ . ": Delete tbl_track_part - sql: $sql\r\n");

    // Part 5: Insert records to tbl_track_part
    // ----------------------------------------

    if ( $debugLevel >= 3) fputs($logFile, "Line " . __LINE__ . ": Part 5 upd / insert tbl_track_part entered\r\n");

    // count items to be inserted (items where disp_f is set to 0 are not counted / inserted)

    if ( $debugLevel >= 3 ) fputs($logFile, "Line " . __LINE__ . ": countItems(wayp): $countItems\r\n");

    $countItems = 0;
    for ( $i=0; $i < sizeof($trackPartArray); $i++ ) {                  // loop through records in array
        if ( $trackPartArray[$i]["itemType"] == "part" && $trackPartArray[$i]["disp_f"] == 1 ) {  // disp_f = true when user has not deleted peak on UI
            $countItems += 1;  
            if ( $debugLevel >= 3 ) fputs($logFile, "Line " . __LINE__ . ": itemName: " . $trackPartArray[$i]["itemName"] . "\r\n");
        }
    }

    if ( $countItems > 0 ) { 
        //create SQL statement  
        $sql = "INSERT INTO tbl_track_part (trpaTrkId, trpaPartID) VALUES ";
        $i=0;
        for ( $i; $i < sizeof($trackPartArray); $i++ ) {                // loop through records in array
            if ( $trackPartArray[$i]["disp_f"] == 1 && $trackPartArray[$i]["itemType"] == "part" ) {  // disp_f = true when user has not deleted peak on UI
                $sql .= "(" . $trkId . "," . $trackPartArray[$i]["itemId"] . ")," ;
            }
        }
        $sql = substr( $sql, 0, strlen($sql)-1 );                       // trim last unnecessary ,
        
        if ( $debugLevel >= 3) fputs($logFile, "Line " . __LINE__ . ": Insert tbl_track_wyp - sql: " . $sql . "\r\n");
        
        // run SQL and handle error
        if ( $conn->query($sql) === TRUE )                              // run sql against DB
        {
            if ( $debugLevel >= 3) fputs($logFile, "Line " . __LINE__ . ": New record in tbl_track_wayp for peaks successfully inserted \r\n");
        } else {
            fputs($logFile, "Line " . __LINE__ . ": Error inserting trkPt: $conn->error\r\n");
            fputs($logFile, "Line " . __LINE__ . ": sql: $sql\r\n");
            // write output array
            $outObject = array (
                "status"=>"NOK",                                        // add err status to return object
                "message"=>"Error inserting tbl_track_wayp for peaks: " . $conn->error,  
            );                                                          // add error message to return object
            echo json_encode($outObject); 
            return;
        }
    }

    // write output array
    $outObject = array (
        "status"=>"OK",                                                 // add err status to return object
        "message"=>"Track successfully updated: ID = $trkId",           // add error message to return object
        "trkId"=>$trkId 
    );

    // Echo output array to client
    echo json_encode($outObject);  
    exit;      
}
?>