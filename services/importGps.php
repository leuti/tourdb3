<?php
// ---------------------------------------------------------------------------------------------
// PHP script loading gpx files into table track points. When no track exists, a simple track
// record is created. Before loading the gpx track points all previous track points are deleted. 
// When a gpx for an existing track is imported, the track time is updated.
//
// This service is called in two different manner: 
// A) for the request 'temp' the parameters are // passed as dataForm object. This is due to the 
//    fact that the file to be uploaded has to be transferred to this service. 
// B) for the other requests a JSON is passed
//
// Parameters:
// sessionid: id of user session; used to ensure multi-user capabilities
// filename: name of file to be uploaded (one at a time); file is expected at import/gpx or import/kml
// filetype: type of file to be imported (gpx or kml)

// Created: 13.12.2017 - Daniel Leutwyler
// ---------------------------------------------------------------------------------------------
// Action:
// * Eigentlich müsste im temp mode noch nichts in die DB geschrieben werden (zumindest nicht für die tblTracks)
// * Improve error handling
// * Return same JSON return object as gen_kml.php
// * Put insert track and select trackId in same transaction
// 
// Return object
// status
// message
// trackObj

// Set variables and parameters
include("config.inc.php");                                        // include config file
include("coord_funct.inc.php");                                    // include coord calc functions
date_default_timezone_set('Europe/Zurich');                         // must be set when using time functions

$loopSize = 5000;                                                   // Number of trkPts inserted in one go

// Open file to write log
$importGpxLog = dirname(__FILE__) . "/../log/importGpx.log";        // Assign file location
if ( $debugLevel >= 1 ) {
    $logFile = @fopen($importGpxLog,"a");                               // open log file handler 
    fputs($logFile, "\r\n============================================================\r\n");    
    fputs($logFile, "importGpx.php started: " . date("Ymd-H:i:s", time()) . "\r\n");    
}

// Evaluate request type
if ( isset($_REQUEST["request"]) && $_REQUEST["request"] != '' )    // if call to this service was done with dataForm (temp)
{
    $request = $_REQUEST["request"];                                // evaluate type of request
    if ( $debugLevel >= 3 ) fputs($logFile, "Line 48: Request (_REQUEST): $request\r\n");    
} else {
    // variables passed on by client (as formData object)

    //Make sure that it is a POST request.
    if(strcasecmp($_SERVER['REQUEST_METHOD'], 'POST') != 0){
        throw new Exception('Request method must be POST!');
    }
    
    if ( $debugLevel >= 3 ) fputs($logFile, 'Line 57: contentType:' . $_SERVER["CONTENT_TYPE"] . '\r\n');
    //Make sure that the content type of the POST request has been set to application/json
    $contentType = isset($_SERVER["CONTENT_TYPE"]) ? substr($_SERVER["CONTENT_TYPE"],0,16) : '';
    if ($debugLevel >= 3) fputs($logFile, "Line 59: contentType: <$contentType>\r\n");
    if(strcasecmp($contentType, 'application/json') != 0){
        throw new Exception('Content type must be: application/json');
    }
    
    //Receive the RAW post data.
    $content = trim(file_get_contents("php://input"));
    
    //Attempt to decode the incoming RAW post data from JSON.
    $receivedData = json_decode($content, true);
    
    //If json_decode failed, the JSON is invalid.
    //if(!is_array($decoded)){
    //    throw new Exception('Received content contained invalid JSON!');
    //}

    $request = $receivedData["request"];                            // temp = temporary creation; save = final storage; cancel = cancel operation / delete track & track points
    if ($debugLevel >= 3) fputs($logFile, "Line 77: Request (JSON): $request\r\n");    
}

if ($request == "temp") {

    // ---------------------------------------------------------------------------------
    // request type is "TEMP" meaning that track records are created on temporary basis
    // ---------------------------------------------------------------------------------
  
    // Read posted parameters
    $sessionid = $_REQUEST["sessionid"];                            // ID of current user session - required to make site multiuser capable
    $filename = basename($_FILES['filename']['name']);              // file name of gps file to be processed
    $loginname = $_REQUEST["loginname"];                            // Login name
    $fileinfo = pathinfo($filename);                                // evaluate file extension 
    $filetype = $fileinfo['extension'];
    if ($debugLevel >= 3) fputs($logFile, "Line 92: Parameters: sessionid:$sessionid | filename:$filename | filetype:$filetype | loginname:$loginname\r\n");    

    // if file type = gpx or kml --> create directory and copy file 
    if ( $filetype == "gpx" ) {
        
        // create upload dir / file name
        $uploaddir = '../tmp/gps_uploads/' . $sessionid . '/';      // Session id used to create unique directory
        $uploadfile = $uploaddir . $filename;           
        if (!is_dir ( $uploaddir )) {                               // Create directory with name = session id
            mkdir($uploaddir, 0777);
        }

        if ( $debugLevel >= 3 ) fputs($logFile, "Line 104 - uploadfile: $uploadfile\r\n");

        // move file to upload dir
        if (move_uploaded_file($_FILES['filename']['tmp_name'], $uploadfile)) {         // move uploaded file to target dir
            if ( $debugLevel >= 3) fputs($logFile, "Line 108 - file " . $_FILES['filename']['name'] . " successfully uploaded to: $uploaddir\r\n");    
        } else {
            fputs($logFile, "Line 110 - error uploading file " . $_FILES['filename']['name'] . " to: $uploaddir\r\n"); 
        }  

        // read gpx file structure & content
        $gpx = simplexml_load_file($uploadfile);                        // Load XML structure
        $newTrackTime = $gpx->trk->trkseg->trkpt->time;
        $GpsStartTime = strftime("%Y.%m.%d %H:%M:%S", strtotime($newTrackTime));    // convert track time 
        $DateBegin = strftime("%Y.%m.%d", strtotime($newTrackTime));// convert track time 
        $DateFinish = strftime("%Y.%m.%d", strtotime($newTrackTime)); // convert track time 
        $trackName = $gpx->trk->name;                                   // Track name  
        //$trackName = $trackName[0];  
 
        if ( $debugLevel >= 3 ) fputs($logFile, "Line 120 - trackName: $trackName\r\n"); 

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
        $WGS_top_lat = 45;                                               // variables to define min/max lon/lat to diplay track in center of map, focused
        $WGS_top_lon = 0;
        $WGS_left_lat = 0;
        $WGS_left_lon = 10.35;
        $WGS_right_lat = 0;
        $WGS_right_lon = 5.50 ;
        $WGS_bottom_lat = 47.5;
        $WGS_bottom_lon = 0;
        */

        $totalTrkPts = count($gpx->trk->trkseg->trkpt);                     // total number of track points in file

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

            if ($debugLevel >= 5) fputs($logFile, "Line 156 - INPUT --> lat: $lat | lon: $lon \r\n"); 

            if( $lat > $WGS_top_lat ) {                                     // This is the top most point
                $WGS_top_lat = $lat;
                $WGS_top_lon = $lon;
            } else if ( $lat < $WGS_bottom_lat ) {                          // This is the bottom most point
                $WGS_bottom_lat = $lat;
                $WGS_bottom_lon = $lon;
            }           
            if( $lon > $WGS_right_lon ) {                                  // This is the right most point
                $WGS_right_lat = $lat;                               
                $WGS_right_lon = $lon;                               
            } else if ( $lon < $WGS_left_lon ) {                          // This is the left most point
                $WGS_left_lat = $lat;                               
                $WGS_left_lon = $lon;                               
            }

            if ($debugLevel >= 4) {
                fputs($logFile, "Line 156 - WGS_top_lat: $WGS_top_lat | WGS_top_lon: $WGS_top_lon\r\n"); 
                fputs($logFile, "Line 156 - WGS_bottom_lat: $WGS_bottom_lat | WGS_top_lon: $WGS_bottom_lon\r\n"); 
                fputs($logFile, "Line 156 - WGS_left_lat: $WGS_left_lat | WGS_top_lon: $WGS_left_lon\r\n"); 
                fputs($logFile, "Line 156 - WGS_right_lat: $WGS_right_lat | WGS_top_lon: $WGS_right_lon\r\n"); 
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
                fputs($GLOBALS['logFile'],"Line 589>tpNr:$tptNumber|ele:$ele|peakEle:$peakEle|lowEle:$lowEle|mU:$meterUp|mD|$meterDown|dist|$distance\r\n");
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
            fputs($GLOBALS['logFile'],"Line 630: WGS_top_lat:$WGS_top_lat|WGS_top_lon:$WGS_top_lon\r\n");
            //fputs($GLOBALS['logFile'],"Line 589>tpNr:$tptNumber|ele:$ele|peakEle:$peakEle|lowEle:$lowEle|mU:$meterUp|mD|$meterDown|dist|$distance\r\n");
        }

        // set elevation and time for last point
        $trkFinishEle = $ele;
        $trkFinishTime = $time;

        if ( $meterDown == 0 ) {
            $meterDown = "-0";
            fputs($GLOBALS['logFile'],"Line 280 - meter down: $meterDown\r\n");
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
     
        $trkCoordTop = WGStoCHx($WGS_top_lat, $WGS_top_lon);                                               // variables to define min/max lon/lat to diplay track in center of map, focused
        //$trkCoordTop = WGStoCHx($WGS_top_lat, $WGS_top_lon); 
        //$trkCoordLeft = WGStoCHy($WGS_left_lat, $WGS_left_lon);
        $trkCoordLeft = WGStoCHy($WGS_left_lat, $WGS_left_lon);
        //$trkCoordRight = WGStoCHy($WGS_right_lat, $WGS_right_lon);
        $trkCoordRight = WGStoCHy($WGS_right_lat, $WGS_right_lon);
        $trkCoordBottom = WGStoCHx($WGS_bottom_lat, $WGS_bottom_lon);
        //$trkCoordBottom = WGStoCHx($WGS_bottom_lat, $WGS_bottom_lon);

        if ($debugLevel >= 3) {
            fputs($logFile, "Line 308 - trkCoordTop: $trkCoordTop --> WGS_top_lat: $WGS_top_lat | WGS_top_lon: $WGS_top_lon\r\n"); 
            fputs($logFile, "Line 308 - trkCoordBottom: $trkCoordBottom --> WGS_bottom_lat: $WGS_bottom_lat | WGS_top_lon: $WGS_bottom_lon\r\n"); 
            fputs($logFile, "Line 308 - trkCoordLeft: $trkCoordLeft --> WGS_left_lat: $WGS_left_lat | WGS_top_lon: $WGS_left_lon\r\n"); 
            fputs($logFile, "Line 308 - trkCoordRight: $trkCoordRight --> WGS_right_lat: $WGS_right_lat | WGS_top_lon: $WGS_right_lon\r\n"); 
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
            "trkSourceFileName"=>$filename,
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
        echo json_encode($returnObject);                           // echo JSON object to client
        
        // remove imported file & close connections
        fclose($uploadfile);
        if ( file_exists($uploadfile) ) unlink($uploadfile);                   // remove file if existing
        rmdir($uploaddir, 0777);                                // remove upload directory          
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

    $trkEdit_waypItems = $receivedData["itemsTrkImp"];                        // Array of peaks selected
    $waypRun = false;                                                 // True when at least one item to insert
    $partRun = false;                                                 // True when at least one item to insert

    if ( $debugLevel >= 6) fputs($logFile, "Line 216 - Part II entered\r\n");
 
    if ( sizeof($trkEdit_waypItems) > 0 ) {

        //create SQL statement  
        $sql = "INSERT INTO tbl_track_wayp (trwpTrkId, trwpWaypID, trwpReached_f) VALUES ";
        $i=0;
        for ( $i; $i < sizeof($trkEdit_waypItems); $i++ ) {                   // loop through records in array
            if ( $trkEdit_waypItems[$i]["disp_f"] == true && ( $trkEdit_waypItems[$i]["itemType"] == "peak"  || 
            $trkEdit_waypItems[$i]["itemType"] == "loca" || $trkEdit_waypItems[$i]["itemType"] == "wayp" )) {                 // disp_f = true when user has not deleted peak on UI
                $waypRun = true;
                $sql .= "(" . $trkId . "," . $trkEdit_waypItems[$i]["itemId"] . "," . $trkEdit_waypItems[$i]["reached_f"] . "),";  
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
        for ( $i; $i < sizeof($trkEdit_waypItems); $i++ ) {                   // loop through records in array
            if ( $trkEdit_waypItems[$i]["disp_f"] == true && $trkEdit_waypItems[$i]["itemType"] == "part" ) {                 // disp_f = true when user has not deleted part on UI
                $sql .= "(" . $trkId . "," . $trkEdit_waypItems[$i]["itemId"] . "),";  
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
} else if ( $request == "update") {
    // ---------------------------------------------------------------------------------
    // request type is "update" meaning that the user has modified a record
    // ---------------------------------------------------------------------------------

    // read received INPUT object
    $trackobj = array();                                                            // array storing track data in array
    $sessionid = $receivedData["sessionid"];                                        // ID of current user session - required to make site multiuser capable
    $loginname = $receivedData["loginname"];
    $trackobj = $receivedData["trackobj"];                                          // Array of track data 
    $trkEdit_waypItems = $receivedData["trkEdit_waypItems"];                        // Array of waypoiunts selected
    $trkEdit_partItems = $receivedData["trkEdit_partItems"];                        // Array of participants selected

    if ( $debugLevel >= 3) fputs($logFile, "Line 539: sessionid: $sessionid - request: $request - loginname: $loginname\r\n");  

    // Part 1: Update tracks
    // --------------------------------------------------
    
    // Create SQL statement to update track 
    $sql = " UPDATE `tourdb2_prod`.`tbl_tracks` SET ";

    // Loop through received track object and add to SQL statement
    foreach ($trackobj as $dbField => $content) {                 // Generate update statement
        if ( $dbField == 'trkId' ) {
            $trkId = $content;
        } else {
            $sql .= "`$dbField` = '$content',";
        }
    }
    
    $sql = substr($sql,0,strlen($sql)-1);                           // remove last ,
    $sql .= " WHERE trkId = $trkId";

    if ($debugLevel >= 3) fputs($logFile, "Line 558 Update Track - sql: $sql\r\n");

    // run SQL and handle error
    if ($conn->query($sql) === TRUE)                                // run sql against DB
    {
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
    
    // Part 2: Delete trb_track_wayp before insert
    // --------------------------------------------------
        
    // count number of items
    $countItems = 0;
    // fputs($logFile, "Line 584 - trkEdit_waypItems: ". sizeof($trkEdit_waypItems). "\r\n");

    for ( $i; $i < sizeof($trkEdit_waypItems); $i++ ) {                   // loop through records in array
        if ( ( $trkEdit_waypItems[$i]["itemType"] == "peak" and ( $trkEdit_waypItems[$i]["disp_f"] == "true" or $trkEdit_waypItems[$i]["disp_f"] == "1") ) || 
             ( $trkEdit_waypItems[$i]["itemType"] == "wayp" and ( $trkEdit_waypItems[$i]["disp_f"] == "true" or $trkEdit_waypItems[$i]["disp_f"] == "1") ) || 
             ( $trkEdit_waypItems[$i]["itemType"] == "loca" and ( $trkEdit_waypItems[$i]["disp_f"] == "true" or $trkEdit_waypItems[$i]["disp_f"] == "1") ) ) {                 // disp_f = true when user has not deleted peak on UI
            $countItems += 1;  
        }
    }

    // fputs($logFile, "Line 589 - waypoints found: $countItems\r\n");

    // only enter into code section when at least one item 
    if ( $countItems > 0 ) {    
    
        $sql = "DELETE FROM `tourdb2_prod`.`tbl_track_wayp` ";
        $sql .= "WHERE `tbl_track_wayp`.`trwpTrkId` = $trkId";

        // run SQL and handle error
        if ( $conn->query($sql) === TRUE )                                // run sql against DB
        {
            if ( $debugLevel >= 6) fputs($logFile, "Line 585 - Records in tbl_track_wayp for waypoints successfully deleted \r\n");
        } else {
            fputs($logFile, "Line 587 - Error deleting trkPt: $conn->error\r\n");
            fputs($logFile, "Line 588 - sql: $sql\r\n");
            // write output array
            $outObject = array (
                'status'=>'NOK',                                             // add err status to return object
                'message'=>'Error deleting tbl_track_wayp for peaks: ' . $conn->error,  
            );                                         // add error message to return object
            echo json_encode($outObject); 
            return;
        }

        if ($debugLevel >= 3) fputs($logFile, "Line 583 Delete tbl_track_wayp - sql: $sql\r\n");

        // Part 3: Insert records to tbl_track_wayp for wayp
        // --------------------------------------------------
        //create SQL statement  
        $sql = "INSERT INTO tbl_track_wayp (trwpTrkId, trwpWaypID, trwpReached_f) VALUES ";
        $i=0;
        for ( $i; $i < sizeof($trkEdit_waypItems); $i++ ) {                   // loop through records in array
            if ( $trkEdit_waypItems[$i]["disp_f"] == true && ( $trkEdit_waypItems[$i]["itemType"] == "peak"  || 
            $trkEdit_waypItems[$i]["itemType"] == "loca" || $trkEdit_waypItems[$i]["itemType"] == "wayp" )) {                 // disp_f = true when user has not deleted peak on UI
                $waypRun = true;
                $sql .= "(" . $trkId . "," . $trkEdit_waypItems[$i]["itemId"] . "," . $trkEdit_waypItems[$i]["reached_f"] . "),";  
            }
        }
        $sql = substr( $sql, 0, strlen($sql)-1 );                       // trim last unnecessary ,
        
        if ( $debugLevel >= 3) fputs($logFile, "Line 617 Insert tbl_track_wyp - sql: " . $sql . "\r\n");
        
        // run SQL and handle error
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
    }

    // part
    
    // count number of items
    $countItems = 0;
    for ( $i; $i < sizeof($trkEdit_partItems); $i++ ) {                   // loop through records in array
        if ( $trkEdit_partItems[$i]["itemType"] == "part" and $trkEdit_partItems[$i]["disp_f"] == "true"  ) {                 // disp_f = true when user has not deleted peak on UI
            $countPart += 1;  
        }
    }

    // only enter into code section when at least one item 
    if ( $countItems > 0 ) {    
    
        // Part 4: Delete trb_track_part before insert
        // --------------------------------------------------
        $sql = "DELETE FROM `tourdb2_prod`.`tbl_track_part` ";
        $sql .= "WHERE `tbl_track_part`.`trpaTrkId` = $trkId";

        // run SQL and handle error
        if ( $conn->query($sql) === TRUE )                                // run sql against DB
        {
            if ( $debugLevel >= 6) fputs($logFile, "Line 681 - Records in tbl_track_part for waypoints successfully deleted \r\n");
        } else {
            fputs($logFile, "Line 683 - Error deleting tbl_track_part: $conn->error\r\n");
            fputs($logFile, "Line 684 - sql: $sql\r\n");
            // write output array
            $outObject = array (
                'status'=>'NOK',                                             // add err status to return object
                'message'=>'Error deleting tbl_track_part: ' . $conn->error,  
            );                                         // add error message to return object
            echo json_encode($outObject); 
            return;
        }

        if ($debugLevel >= 3) fputs($logFile, "Line 694 Delete tbl_track_part - sql: $sql\r\n");

        // Part 5: Insert records to tbl_track_wayp for peaks
        // --------------------------------------------------

        //create SQL statement  
        $sql = "INSERT INTO tbl_track_part (trpaTrkId, trpaPartID) VALUES ";
        $i=0;
        for ( $i; $i < sizeof($trkEdit_partItems); $i++ ) {                   // loop through records in array
            if ( $trkEdit_partItems[$i]["disp_f"] == true && $trkEdit_partItems[$i]["itemType"] == "part" ) {                 // disp_f = true when user has not deleted peak on UI
                $sql .= "(" . $trkId . "," . $trkEdit_partItems[$i]["itemId"] . ")," ;
            }
        }
        $sql = substr( $sql, 0, strlen($sql)-1 );                       // trim last unnecessary ,
        
        if ( $debugLevel >= 3) fputs($logFile, "Line 678 Insert tbl_track_wyp - sql: " . $sql . "\r\n");
        
        // run SQL and handle error
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