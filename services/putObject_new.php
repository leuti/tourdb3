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

// required file information
$importFileName = basename($_FILES['filename']['name']);                  // file name of gps file to be processed
$fileinfo = pathinfo($importFileName);                                    // evaluate file extension 
$filetype = $fileinfo['extension'];
    
// read received INPUT object
$login = $receivedData["login"];
$objectType = $receivedData["objectType"];
$putObj = $receivedData["putObj"];
$requestType = $receivedData["requestType"];                                // temp = temporary creation; save = final storage; cancel = cancel operation / delete track & track points
$sessionId = $receivedData["sessionId"];                            // ID of current user session - required to make site multiuser capable
$trackObj = $receivedData["putObj"];                              // Array of track data 
$trackWaypArray = $receivedData["trackWaypArray"];            // Array of waypoiunts selected
$trackPartArray = $receivedData["trackPartArray"];            // Array of participants selected

if ($debugLevel >= 3) fputs($logFile, __LINE__ . ": sessionId: $sessionId\r\n");    

if ( $requestType == "imp") {

    // ---------------------------------------------------------------------------------
    // request type is import meaning that tracks and related arrays need to be inserted
    // ---------------------------------------------------------------------------------
    
    if ($debugLevel >= 3) {
        fputs($logFile, "Line " . __LINE__ . ": Parameters: sessionId:$sessionId | importFileName:$importFileName | filetype:$filetype | login:$login\r\n");    
    }

    // if file type = gpx or kml --> create directory and copy file 
    if ( $filetype == "gpx" ) {
        
        // create upload dir / file name
        $uploaddir = '../tmp/gps_uploads/' . $sessionId . '/';          // Session id used to create unique directory
        $uploadfile = $uploaddir . $importFileName;           
        if (!is_dir ( $uploaddir )) {                                   // Create directory with name = session id
            mkdir($uploaddir, 0777);
        }

        if ( $debugLevel >= 3 ) fputs($logFile, "Line " . __LINE__ . " - uploadfile: $uploadfile\r\n");

        // move file to upload dir
        if (move_uploaded_file($_FILES['filename']['tmp_name'], $uploadfile)) {         // move uploaded file to target dir
            if ( $debugLevel >= 3) fputs($logFile, "Line " . __LINE__ . ": file " . $_FILES['filename']['name'] . " successfully uploaded to: $uploaddir\r\n");    
        } else {
            fputs($logFile, "Line " . __LINE__ . " - error uploading file " . $_FILES['filename']['name'] . " to: $uploaddir\r\n"); 
        }  

        // read gpx file structure & content
        $gpx = simplexml_load_file($uploadfile);                        // Load XML structure
        $newTrackTime = $gpx->trk->trkseg->trkpt->time;
        $GpsStartTime = strftime("%Y.%m.%d %H:%M:%S", strtotime($newTrackTime));    // convert track time 
        $DateBegin = strftime("%Y.%m.%d", strtotime($newTrackTime));    // convert track time 
        $DateFinish = strftime("%Y.%m.%d", strtotime($newTrackTime));   // convert track time 
        $trackName = $gpx->trk->name;                                   // Track name  
        //$trackName = $trackName[0];  
 
        if ( $debugLevel >= 3 ) fputs($logFile, "Line " . __LINE__ . " - trackName: $trackName\r\n"); 

        // -------------------------------------------------
        // Calculate times, meters up/down, distances

        // define variables
        $tptNumber = 1;                                                 // Set counter for tptNumber to 1
        $loopCumul = $GLOBALS['loopSize'];                              // loopCumul is the sum of loop sizes processed
        $coordArray = array();                                          // initialize array to store coordinates in kml style
        $loop = 0;                                                      // set current loop to 0 (only required for debug purposes)
        $firstInLoop = 1;                                               // flag first record within loop 
        $firstTrackPoint = 1;                                           // flag first record within loop 
        $eleFound = false;                                              // True when an ele <> 0 or "" has be read
        $overallDistance = (float) 0;
        $distance = (float) 0;
        /*
        $WGS_top_lat = 45;                                              // variables to define min/max lon/lat to diplay track in center of map, focused
        $WGS_top_lon = 0;
        $WGS_left_lat = 0;
        $WGS_left_lon = 10.35;
        $WGS_right_lat = 0;
        $WGS_right_lon = 5.50 ;
        $WGS_bottom_lat = 47.5;
        $WGS_bottom_lon = 0;
        */

        $totalTrkPts = count($gpx->trk->trkseg->trkpt);                 // total number of track points in file

        // loop through each trkpt XML element in the gpx file
        foreach ($gpx->trk->trkseg->trkpt as $trkpt)                        
        {               
            // read content of file
            $lat = $trkpt["lat"];
            $lon = $trkpt["lon"];
            settype($lat,"float");
            settype($lon,"float");

            if ( $firstTrackPoint == 1 ) {
                $WGS_top_lat = $lat;
                $WGS_top_lon = $lon;
                $WGS_left_lat = $lat;
                $WGS_left_lon = $lon;
                $WGS_right_lat = $lat;
                $WGS_right_lon = $lon;
                $WGS_bottom_lat = $lat;
                $WGS_bottom_lon = $lon;
            }

            //$CH03_top_lat = WGStoCHy($lat, $lon);
            //$CH03_top_lon = WGStoCHx($lat, $lon); 

            if ($debugLevel >= 5) fputs($logFile, "Line " . __LINE__ . " - INPUT --> lat: $lat | lon: $lon \r\n"); 

            if( $lat > $WGS_top_lat ) {                                 // This is the top most point
                $WGS_top_lat = $lat;
                $WGS_top_lon = $lon;
            } else if ( $lat < $WGS_bottom_lat ) {                      // This is the bottom most point
                $WGS_bottom_lat = $lat;
                $WGS_bottom_lon = $lon;
            }           
            if( $lon > $WGS_right_lon ) {                               // This is the right most point
                $WGS_right_lat = $lat;                               
                $WGS_right_lon = $lon;                               
            } else if ( $lon < $WGS_left_lon ) {                        // This is the left most point
                $WGS_left_lat = $lat;                               
                $WGS_left_lon = $lon;                               
            }

            if ($debugLevel >= 4) {
                fputs($logFile, "Line " . __LINE__ . " - WGS_top_lat: $WGS_top_lat | WGS_top_lon: $WGS_top_lon\r\n"); 
                fputs($logFile, "Line " . __LINE__ . " - WGS_bottom_lat: $WGS_bottom_lat | WGS_top_lon: $WGS_bottom_lon\r\n"); 
                fputs($logFile, "Line " . __LINE__ . " - WGS_left_lat: $WGS_left_lat | WGS_top_lon: $WGS_left_lon\r\n"); 
                fputs($logFile, "Line " . __LINE__ . " - WGS_right_lat: $WGS_right_lat | WGS_top_lon: $WGS_right_lon\r\n"); 
                fputs($logFile, "====================================================================\r\n"); 
            }

            if ( $trkpt->ele == "" || $trkpt->ele == 0 ) {
                $ele = 0;
            } else {
                $ele = $trkpt->ele;
                $eleFound = true;
            }
            $ele = $ele * 1;
            $time = strftime("%Y-%m-%d %H:%M:%S", strtotime($trkpt->time));

            if ($firstInLoop == 1)  {                                   // if record is not first, a comma is written

                // set sql string
                $firstInLoop = 0;
                
                if ( $firstTrackPoint == 1 ) {
                    // initialise variables
                    $startTime = $time;
                    $startEle = $ele;
                    $peakTime = $time;
                    $peakEle = $ele;
                    $lowEle = $ele;
                    $lowTime = $time;
                    $meterUp = 0;
                    $meterDown = 0;
                    $distance = 0;
                    $firstTrackPoint = 0;
                }
            } else {

                // calc gain values
                $eleGain = $ele - $previousEle;
                $eleGainVsPeak = $ele - $peakEle;
                
                // calc distance to previous waypoint
                $distance = haversineGreatCircleDistance(
                    floatval($previousLat), floatval($previousLon), floatval($lat), floatval($lon), 6371000);
                $overallDistance = $overallDistance + $distance;
                
                // calculate different variable
                if ( $eleGain > 0 ) {                                   // elevation gained
                    if ( $eleGainVsPeak > 0 ) {
                        $peakTime = $time;
                        $peakEle = $ele;
                        $meterUp = $meterUp + $eleGain; 
                    } else {
                        $meterUp = $meterUp + $eleGain;
                    }
                } else {
                    if ( $eleGainVsPeak < 0 ) {
                        $lowTime = $time;
                        $lowEle = $ele;
                        $meterDown = $meterDown + $eleGain;
                    } else {
                        $meterDown = $meterDown + $eleGain;
                    }
                }
            }
            
            if ($GLOBALS['debugLevel']>8) {
                fputs($GLOBALS['logFile'],"Line " . __LINE__ . ">tpNr:$tptNumber|ele:$ele|peakEle:$peakEle|lowEle:$lowEle|mU:$meterUp|mD|$meterDown|dist|$distance\r\n");
            }
            
            $previousEle = $ele;
            $previousLat = $lat;
            $previousLon = $lon;
            
            // Create coordinate string
            $coordPoint = "$lon,$lat,$ele ";
            array_push( $coordArray, $coordPoint );                     // write Lon, Lat and Ele into coordArray array

            $tptNumber++;                                               // increase track point counter by 1
        }

        if ($GLOBALS['debugLevel']>=3) {
            fputs($GLOBALS['logFile'],"Line " . __LINE__ . ": WGS_top_lat:$WGS_top_lat|WGS_top_lon:$WGS_top_lon\r\n");
            //fputs($GLOBALS['logFile'],"Line " . __LINE__ . ">tpNr:$tptNumber|ele:$ele|peakEle:$peakEle|lowEle:$lowEle|mU:$meterUp|mD|$meterDown|dist|$distance\r\n");
        }

        // set elevation and time for last point
        $trkFinishEle = $ele;
        $trkFinishTime = $time;

        if ( $meterDown == 0 ) {
            $meterDown = "-0";
            fputs($GLOBALS['logFile'],"Line " . __LINE__ . " - meter down: $meterDown\r\n");
        }

        // calculate times 
        $datetime1 = new DateTime($peakTime);                           //start time
        $datetime2 = new DateTime($startTime);                          //end time
        $interval = $datetime1->diff($datetime2);
        $timeToPeak = $interval->format('%H:%i:%s');

        $datetime1 = new DateTime($time);                               //start time
        $datetime2 = new DateTime($startTime);                          //end time
        $interval = $datetime1->diff($datetime2);
        $overallTime = $interval->format('%H:%i:%s');

        $datetime1 = new DateTime($time);                               //start time
        $datetime2 = new DateTime($peakTime);                           //end time
        $interval = $datetime1->diff($datetime2);
        $timeToFinish = $interval->format('%H:%i:%s');
     
        $trkCoordTop = WGStoCHx($WGS_top_lat, $WGS_top_lon);            // variables to define min/max lon/lat to diplay track in center of map, focused
        //$trkCoordTop = WGStoCHx($WGS_top_lat, $WGS_top_lon); 
        //$trkCoordLeft = WGStoCHy($WGS_left_lat, $WGS_left_lon);
        $trkCoordLeft = WGStoCHy($WGS_left_lat, $WGS_left_lon);
        //$trkCoordRight = WGStoCHy($WGS_right_lat, $WGS_right_lon);
        $trkCoordRight = WGStoCHy($WGS_right_lat, $WGS_right_lon);
        $trkCoordBottom = WGStoCHx($WGS_bottom_lat, $WGS_bottom_lon);
        //$trkCoordBottom = WGStoCHx($WGS_bottom_lat, $WGS_bottom_lon);

        if ($debugLevel >= 3) {
            fputs($logFile, "Line " . __LINE__ . " - trkCoordTop: $trkCoordTop --> WGS_top_lat: $WGS_top_lat | WGS_top_lon: $WGS_top_lon\r\n"); 
            fputs($logFile, "Line " . __LINE__ . " - trkCoordBottom: $trkCoordBottom --> WGS_bottom_lat: $WGS_bottom_lat | WGS_top_lon: $WGS_bottom_lon\r\n"); 
            fputs($logFile, "Line " . __LINE__ . " - trkCoordLeft: $trkCoordLeft --> WGS_left_lat: $WGS_left_lat | WGS_top_lon: $WGS_left_lon\r\n"); 
            fputs($logFile, "Line " . __LINE__ . " - trkCoordRight: $trkCoordRight --> WGS_right_lat: $WGS_right_lat | WGS_top_lon: $WGS_right_lon\r\n"); 
            fputs($logFile, "=============================================================================================================\r\n"); 
        }
        //$coordCenterY = ( $CH03_top_Y + $CH03_bottom_Y ) / 2;
        //$coordCenterX = ( $CH03_right_X + $CH03_left_X ) / 2;
        
        // join array $coordArray into a string
        $coordString = "";
        foreach ( $coordArray as $coordPoint) {                         // Create string containing the coordinates
            $coordString = $coordString . $coordPoint; 
        };

        // write var to track obj
        $trackObj = array (
            "trkSourceFileName"=>$importFileName,
            "trkTrackName"=>"$trackName",
            "trkRoute"=>"$trackName",
            "trkDateBegin"=>$DateBegin,
            "trkDateFinish"=>$DateFinish,
            "trkGPSStartTime"=>$GpsStartTime,
            "trkStartEle"=>$startEle,
            "trkPeakEle"=>$peakEle,
            "trkPeakTime"=>$peakTime,
            "trkLowEle"=>$lowEle,
            "trkLowTime"=>$lowTime,
            "trkFinishEle"=>$trkFinishEle,
            "trkFinishTime"=>$trkFinishTime,
            "trkTimeToPeak"=>$timeToPeak,
            "trkTimeToFinish"=>$timeToFinish,
            "trkTimeOverall"=>$overallTime,
            "trkMeterDown"=>$meterDown,
            "trkMeterUp"=>$meterUp,
            "trkDistance"=>round($overallDistance/1000, 2),
            "trkCoordinates"=>$coordString,
            "trkCountry"=>"CH",
            "trkSaison"=>"2017/18 Wi",
            "trkType"=>"Ski",
            "trkSubType"=>"Skitour",
            "trkCoordTop"=>round($trkCoordTop, 0),
            "trkCoordBottom"=>round($trkCoordBottom, 0),
            "trkCoordLeft"=>round($trkCoordLeft, 0),
            "trkCoordRight"=>round($trkCoordRight, 0)
        );

        $returnObject = array (
            "status"=>"OK",
            "message"=>"",
            "trackObj"=>$trackObj
            );

        // return 
        echo json_encode($returnObject);                                // echo JSON object to client
        
        // remove imported file & close connections
        //fclose($uploadfile);
        if ( file_exists($uploadfile) ) unlink($uploadfile);            // remove file if existing
        rmdir($uploaddir);                                              // remove upload directory          
    } else {

        // if filetype is not GPX
        fputs($logFile, "Line " . __LINE__ . ": File type is $filetype - only GPX can be processed\r\n");  

        // prepare JSON return object
        $outObject = array (
            'status'=>'ERR',                                            // add err status to return object
            'message'=>"File type is $filetype - only GPX can be processed",                   // add error message to return object
        );
        echo json_encode($outObject);                                   // echo track object to client
        exit;                                                           // exit from php
    }
}

else if ( $requestType == "upd") {
    // ---------------------------------------------------------------------------------
    // request type is "update" meaning that the user has modified a record
    // ---------------------------------------------------------------------------------

    fputs($logFile, "Line " . __LINE__ . ": upd entered\r\n");  

    if ( $debugLevel >= 3) fputs($logFile, "Line " . __LINE__ . ": sessionId: $sessionId - requestType: $requestType - login: $login\r\n");  

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

    if ($debugLevel >= 3) fputs($logFile, "Line " . __LINE__ . " Update Track - sql: $sql\r\n");

    // run SQL and handle error
    if ($conn->query($sql) === TRUE)                                    // run sql against DB
    {
        if ( $debugLevel >= 3) fputs($logFile, "Line " . __LINE__ . " - New track updated successfully: ID = $trkId\r\n");
    } else {
        fputs($logFile, "Line " . __LINE__ . " - Error inserting trkPt: $conn->error\r\n");
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
    if ( $debugLevel >= 3 ) fputs($logFile, "Line " . __LINE__ . " - trackWaypArray: ". sizeof($trackWaypArray). "\r\n");

    for ( $i=0; $i < sizeof($trackWaypArray); $i++ ) {               // loop through records in array
        if ( $trackWaypArray[$i]["itemType"] == "peak" || 
             $trackWaypArray[$i]["itemType"] == "wayp" || 
             $trackWaypArray[$i]["itemType"] == "loca" ) {
            $countItems += 1;  
        }
    }

    if ( $debugLevel >= 3 ) fputs($logFile, "Line " . __LINE__ . " - countItems(wayp): $countItems\r\n");

    // only enter into code section when at least one item 
    if ( $countItems > 0 ) {    
    */
        $sql = "DELETE FROM `tourdb2_prod`.`tbl_track_wayp` ";
        $sql .= "WHERE `tbl_track_wayp`.`trwpTrkId` = $trkId";

        // run SQL and handle error
        if ( $conn->query($sql) === TRUE )                              // run sql against DB
        {
            if ( $debugLevel >= 6) fputs($logFile, "Line " . __LINE__ . " - Records in tbl_track_wayp for waypoints successfully deleted \r\n");
        } else {
            fputs($logFile, "Line " . __LINE__ . " - Error deleting trkPt: $conn->error\r\n");
            fputs($logFile, "Line " . __LINE__ . " - sql: $sql\r\n");
            // write output array
            $outObject = array (
                'status'=>'NOK',                                        // add err status to return object
                'message'=>'Error deleting tbl_track_wayp for peaks: ' . $conn->error,  
            );                                                          // add error message to return object
            echo json_encode($outObject); 
            return;
        }

        if ($debugLevel >= 3) fputs($logFile, "Line " . __LINE__ . " Delete tbl_track_wayp - sql: $sql\r\n");
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
        
        if ( $debugLevel >= 3) fputs($logFile, "Line " . __LINE__ . " Insert tbl_track_wayp - sql: " . $sql . "\r\n");
        
        // run SQL and handle error
        if ( $conn->query($sql) === TRUE )                              // run sql against DB
        {
            if ( $debugLevel >= 6) fputs($logFile, "Line " . __LINE__ . " - New record in tbl_track_wayp for peaks successfully inserted \r\n");
        } else {
            fputs($logFile, "Line " . __LINE__ . " - Error inserting trkPt: $conn->error\r\n");
            fputs($logFile, "Line " . __LINE__ . " - sql: $sql\r\n");
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
            fputs($logFile, "Line " . __LINE__ . " - itemName: " . $trackPartArray[$i]["itemName"] . "\r\n");
        }
    }

    if ( $debugLevel >= 3) fputs($logFile, "Line " . __LINE__ . " - countItems(part): $countItems\r\n");

    // only enter into code section when at least one item 
    if ( $countItems > 0 ) {  
        
        if ( $debugLevel >= 3) fputs($logFile, "Line " . __LINE__ . " - countItems grösser null \r\n");
    */
        $sql = "DELETE FROM `tourdb2_prod`.`tbl_track_part` ";
        $sql .= "WHERE `tbl_track_part`.`trpaTrkId` = $trkId";

        // run SQL and handle error
        if ( $conn->query($sql) === TRUE )                              // run sql against DB
        {
            if ( $debugLevel >= 3) fputs($logFile, "Line " . __LINE__ . " - Records in tbl_track_part successfully deleted \r\n");
        } else {
            fputs($logFile, "Line " . __LINE__ . " - Error deleting tbl_track_part: $conn->error\r\n");
            fputs($logFile, "Line " . __LINE__ . " - sql: $sql\r\n");
            // write output array
            $outObject = array (
                'status'=>'NOK',                                        // add err status to return object
                'message'=>'Error deleting tbl_track_part: ' . $conn->error,  
            );                                                          // add error message to return object
            echo json_encode($outObject); 
            return;
        }
    //}

    if ($debugLevel >= 3) fputs($logFile, "Line " . __LINE__ . " Delete tbl_track_part - sql: $sql\r\n");

    // Part 5: Insert records to tbl_track_part
    // ----------------------------------------

    // count items to be inserted (items where disp_f is set to 0 are not counted / inserted)

    if ( $debugLevel >= 3 ) fputs($logFile, "Line " . __LINE__ . " - countItems(wayp): $countItems\r\n");

    $countItems = 0;
    for ( $i=0; $i < sizeof($trackPartArray); $i++ ) {               // loop through records in array
        if ( $trackPartArray[$i]["itemType"] == "part" && $trackPartArray[$i]["disp_f"] == 1 ) {                 // disp_f = true when user has not deleted peak on UI
            $countItems += 1;  
            fputs($logFile, "Line " . __LINE__ . " - itemName: " . $trackPartArray[$i]["itemName"] . "\r\n");
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
        
        if ( $debugLevel >= 3) fputs($logFile, "Line " . __LINE__ . " Insert tbl_track_wyp - sql: " . $sql . "\r\n");
        
        // run SQL and handle error
        if ( $conn->query($sql) === TRUE )                              // run sql against DB
        {
            if ( $debugLevel >= 3) fputs($logFile, "Line " . __LINE__ . " - New record in tbl_track_wayp for peaks successfully inserted \r\n");
        } else {
            fputs($logFile, "Line " . __LINE__ . " - Error inserting trkPt: $conn->error\r\n");
            fputs($logFile, "Line " . __LINE__ . " - sql: $sql\r\n");
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