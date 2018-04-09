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

fputs($logFile, "itemType: $itemType | requestType: $requestType | itemId: $itemId\r\n");    

if ( $itemType == "trk" ) {

    // ---------------------------------------------------------------------------------
    // get track request 
    // ---------------------------------------------------------------------------------
  
    // if file type = gpx or kml --> create directory and copy file 
    if ( $requestType == "select" ) {
        
        if ($debugLevel >= 3) fputs($logFile, "Line 39: select section entered: \r\n");

        // Select tracks meeting given WHERE clause
        $sql = "SELECT * FROM tbl_tracks ";
        $sql .= "WHERE trkId = " . $itemId;

        if ($debugLevel >= 3) fputs($logFile, 'Line 117: sql to select track: ' . $sql . "\r\n");

        $records = mysqli_query($conn, $sql);
        
        // select single line result
        $trackRecord = mysqli_fetch_assoc($records);

        // Select records from tbl_trk_wayp
        $sql =  "SELECT trwp.trwpId, trwp.trwpTrkId, trwp.trwpWaypId, ";
        $sql .= "wp.waypNameLong, wp.waypTypeFID, trwp.trwpReached_f ";
        $sql .= "FROM tbl_track_wayp trwp ";
        $sql .= "INNER JOIN tbl_waypoints wp ON trwp.trwpWaypId = wp.waypID ";
        $sql .= "WHERE trwpTrkId = " . $itemId;

        if ($debugLevel >= 3) fputs($logFile, 'Line 117: sql to select track: ' . $sql . "\r\n");

        $records = mysqli_query($conn, $sql);
        
        $trWpArray = array();
        
        // select single line result
        while ( $trkwpRecord = mysqli_fetch_assoc($records) ) {
            
            // Evaluate type of waypoint
            if ( $trkwpRecord["waypTypeFID"] == "5" ) {
                $itemType = "peaks";
            } else if ( $trkwpRecord["waypTypeFID"] == "4" ) {
                $itemType = "loca";
            } else {
                $itemType = "wayp";
            } 

            if ($debugLevel >= 3) fputs($logFile, "Line 79: waypNameLong: " . $trkwpRecord["waypNameLong"] . "\r\n");

            $trkWpLine = array (
                "disp_f"=>'true',
                "itemId"=>$trkwpRecord["trwpWaypId"],
                "itemName"=>$trkwpRecord["waypNameLong"],                                             // add inner join
                "itemType"=>$itemType,
                "reached_f"=>$trkwpRecord["trwpReached_f"]
            );
            array_push ($trWpArray, $trkWpLine);
        }

        if ($debugLevel >= 3) fputs($logFile, 'Line 90: trWpArray: ' . print_r ( $trWpArray ) . "\r\n");

        $returnObject = array (
            "status"=>"OK",
            "message"=>"",
            "trackObj"=>$trackRecord,
            "trWpArray"=>$trkWpLine
            );

        // return 
        echo json_encode($returnObject);                           // echo JSON object to client
        
    } else {

        // if filetype is not GPX
        fputs($logFile, "Line 158: File type is $filetype - only GPX can be processed\r\n");  

        // prepare JSON return object
        $outObject = array (
            'status'=>'ERR',                                        // add err status to return object
            'message'=>"File type is $filetype - only GPX can be processed",                   // add error message to return object
        );
        echo json_encode($outObject);                               // echo track object to client
        exit;                                                       // exit from php
    }
} else if ( $request == "save") {
   
    // ---------------------------------------------------------------------------------
    // request type is "SAVE" meaning that track records are updated and finalised
    // ---------------------------------------------------------------------------------

    // Part 1: Update temporarily created track record
    // ----------------------------------------------------

    // read received INPUT object
    $trackobj = array();                                          // array storing track data in array
    $sessionid = $receivedData["sessionid"];                        // ID of current user session - required to make site multiuser capable
    $loginname = $receivedData["loginname"];
    $trackobj = $receivedData["trackobj"];                        // Array of track data 
    
    if ( $debugLevel >= 3) fputs($logFile, "Line 183: sessionid: $sessionid - request: $request - loginname: $loginname\r\n");  
    
    // Create SQL statement to insert track 
    $sql = " INSERT INTO `tourdb2_prod`.`tbl_tracks` (";

    // Loop through received track object and add to SQL statement
    foreach ($trackobj as $dbField => $content) {                 // Generate update statement
        $sql .= "`$dbField`,";
    }
    $sql = substr($sql,0,strlen($sql)-1);                           // remove last ,
    $sql .= ") VALUES (";
    
    // Loop through received track object and add to SQL statement
    foreach ($trackobj as $dbField => $content) {                 // Generate update statement
        $sql .= "'$content',";
    }
    $sql = substr($sql,0,strlen($sql)-1);                           // remove last ,
    $sql .= ")";

    if ($debugLevel >= 3) fputs($logFile, "Line 196 - sql: $sql\r\n");

    // run SQL and handle error
    if ($conn->query($sql) === TRUE)                                // run sql against DB
    {
        $trkId = $conn->insert_id;
        if ( $debugLevel >= 3) fputs($logFile, "Line 201 - New track inserted successfully: ID = $trkId\r\n");
    } else {
        fputs($logFile, "Line 203 - Error inserting trkPt: $conn->error\r\n");
        $message = "Error inserting Track: $conn->error";

        $outObject = array (
            'status'=>'NOK',                                             // add err status to return object
            'message'=> $message  
        );
        echo json_encode($outObject); 
        return;
    } 
   
    // Part 2: Insert records to tbl_track_wayp for peaks
    // --------------------------------------------------

    $itemsArray = $receivedData["itemsArray"];                        // Array of peaks selected
    $waypRun = false;                                                 // True when at least one item to insert
    $partRun = false;                                                 // True when at least one item to insert

    if ( $debugLevel >= 6) fputs($logFile, "Line 216 - Part II entered\r\n");
 
    if ( sizeof($itemsArray) > 0 ) {

        //create SQL statement  
        $sql = "INSERT INTO tbl_track_wayp (trwpTrkId, trwpWaypID, trwpReached_f) VALUES ";
        $i=0;
        for ( $i; $i < sizeof($itemsArray); $i++ ) {                   // loop through records in array
            if ( $itemsArray[$i]["disp_f"] == true && ( $itemsArray[$i]["itemType"] == "peak"  || 
            $itemsArray[$i]["itemType"] == "loca" || $itemsArray[$i]["itemType"] == "wayp" )) {                 // disp_f = true when user has not deleted peak on UI
                $waypRun = true;
                $sql .= "(" . $trkId . "," . $itemsArray[$i]["itemId"] . "," . $itemsArray[$i]["reached_f"] . "),";  
            }
        }
        if ( $debugLevel >= 3) fputs($logFile, "Line 377 - wayp sql: " . $sql . "\r\n");
        
        // run SQL and handle error
        $sql = substr( $sql, 0, strlen($sql)-1 );                       // trim last unnecessary ,
        if ( $waypRun ) {
            if ( $conn->query($sql) === TRUE )                                // run sql against DB
            {
                if ( $debugLevel >= 6) fputs($logFile, "Line 234 - New record in tbl_track_wayp for peaks successfully inserted \r\n");
            } else {
                fputs($logFile, "Line 236 - Error inserting trkPt: $conn->error\r\n");
                fputs($logFile, "Line 237 - sql: $sql\r\n");
                // write output array
                $outObject = array (
                    'status'=>'NOK',                                             // add err status to return object
                    'message'=>'Error inserting tbl_track_wayp for peaks: ' . $conn->error,  
                );                                         // add error message to return object
                echo json_encode($outObject); 
                return;
            }
        } else {
            if ( $debugLevel >= 6) fputs($logFile, "Line 254 - Nothing to insert into tbl_tracks_wayp\r\n");
        }

        // Insert items into tbl_track_part

        // create SQL statement  
        $sql = "INSERT INTO tbl_track_part (trpaTrkId, trpaPartId) VALUES ";

        $i=0;
        for ( $i; $i < sizeof($itemsArray); $i++ ) {                   // loop through records in array
            if ( $itemsArray[$i]["disp_f"] == true && $itemsArray[$i]["itemType"] == "part" ) {                 // disp_f = true when user has not deleted part on UI
                $sql .= "(" . $trkId . "," . $itemsArray[$i]["itemId"] . "),";  
                $partRun = true;
            }
        }
        if ( $debugLevel >= 3) fputs($logFile, "Line 256 - part sql: $sql\r\n");
        
        // run SQL and handle error
        $sql = substr( $sql, 0, strlen($sql) - 1 );                       // trim last unnecessary ,
        if ( $partRun ) {
            if ( $conn->query($sql) === TRUE )                                // run sql against DB
            {
                if ( $debugLevel >= 3) fputs($logFile, "Line 343 - New record in tbl_track_part inserted successfully\r\n");
            } else {
                fputs($logFile, "Line 345 - Error inserting trkPt: $conn->error\r\n");
                fputs($logFile, "Line 346 - sql: $sql\r\n");
                // write output array
                $outObject = array (
                    'status'=>'NOK',                                             // add err status to return object
                    'message'=>'Error inserting tbl_track_wayp : ' . $conn->error,  
                );                                         // add error message to return object
                echo json_encode($outObject); 
                return;
            }
        } else {
            if ( $debugLevel >= 6) fputs($logFile, "Line 254 - Nothing to insert into tbl_tracks_wayp\r\n");
        }
    }

    // write output array
    $outObject = array (
        'status'=>'OK',                                             // add err status to return object
        'message'=>"New track inserted successfully: ID = $trkId",                                           // add error message to return object
        'trkId'=>$trkId 
    );

    // Echo output array to client
    echo json_encode($outObject);  
    exit;      
} 
?>