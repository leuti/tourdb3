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
// * Return -1 ist wohl nicht das korrekte Verhalten
// * Improve error handling
// * Return same JSON return object as gen_kml.php
// * Put insert track and select trackId in same transaction
// 
// Return object
// status
// errmessage
// trackObj - trkCoordinates
// trackObj - trkDateBegin
// trackObj - trkDateFinish
// trackObj - trkDistance
// trackObj - trkGPSStartTime
// trackObj - trkId
// trackObj - trkLowEle
// trackObj - trkLowTime
// trackObj - trkMeterDown
// trackObj - trkMeterUp
// trackObj - trkPeakEle
// trackObj - trkPeakTime
// trackObj - trkSourceFileName
// trackObj - trkStartEle
// trackObj - trkStartTime
// trackObj - trkTimeOverall
// trackObj - trkTimeToFinish
// trackObj - trkTimeToPeak
// trackObj - trkTrackName


// Set variables and parameters
include("./config.inc.php");                                        // include config file
date_default_timezone_set('Europe/Zurich');                         // must be set when using time functions

$debugLevel = 3;                                                    // 0 = off, 6 = all
$loopSize = 5000;                                                   // Number of trkPts inserted in one go

// Open file to write log
$importGpxLog = dirname(__FILE__) . "/../log/importGpx.log";        // Assign file location
$logFile = @fopen($importGpxLog,"a");                               // open log file handler 
fputs($logFile, "\r\n============================================================\r\n");    
fputs($logFile, "importGpx.php started: " . date("Ymd-H:i:s", time()) . "\r\n");    

// Evaluate request type
if ( isset($_REQUEST["request"]) && $_REQUEST["request"] != '' )    // if call to this service was done with dataForm (temp)
{
    $request = $_REQUEST["request"];                                // evaluate type of request
    if ($debugLevel > 2) fputs($logFile, "Line 38: Request (_REQUEST): $request\r\n");    
} else {
    // variables passed on by client (as formData object)
    $receivedData = json_decode ( file_get_contents('php://input'), true );
    $request = $receivedData["request"];                            // temp = temporary creation; save = final storage; cancel = cancel operation / delete track & track points
    if ($debugLevel > 2) fputs($logFile, "Line 43: Request (JSON): $request\r\n");    
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
    if ($debugLevel > 2) fputs($logFile, "Line 58: Parameters: sessionid:$sessionid | filename:$filename | filetype:$filetype | loginname:$loginname\r\n");    

    // if file type = gpx or kml --> create directory and copy file 
    if ( $filetype == "gpx" || $filetype == "kml" ) {
        
        // create upload dir / file name
        $uploaddir = '../tmp/gps_uploads/' . $sessionid . '/';      // Session id used to create unique directory
        $uploadfile = $uploaddir . $filename;           
        if (!is_dir ( $uploaddir )) {                               // Create directory with name = session id
            mkdir($uploaddir, 0777);
        }

        // move file to upload dir
        if (move_uploaded_file($_FILES['filename']['tmp_name'], $uploadfile)) {         // move uploaded file to target dir
            if ( $debugLevel > 2) fputs($logFile, "Line 71 - file " . $_FILES['filename']['name'] . " successfully uploaded to: $uploaddir\r\n");    
        } else {
            fputs($logFile, "Line 73 - error uploading file " . $_FILES['filename']['name'] . " to: $uploaddir\r\n"); 
        }  

        // steps processed with gpx files
        if ( $filetype == "gpx") {

            // Call function to insert track data
            $returnObject = insertTrack($conn,$filename,$uploadfile,$loginname);
            $trackObj = $returnObject["trackObj"];
            $trackid = $trackObj["trkId"];                          // return id of newly created track                              // track object with all know track data derived from file
            
            if ($debugLevel > 2) fputs($logFile, "Line 114 - trackid: $trackid\r\n");
        
            // write content of trackobj1 to log file
            foreach ($trackObj as $dbField => $value) {
                $trackObjOut["$dbField"]=$value; 
            }
            
            // insert track points found in file into table trackpoints with given track id
            $returnObject = insertTrackPoints($conn,$trackid,$uploadfile);  // Insert new track points; returns array

            // extract track object from return variable and add to variable
            $trackObj = $returnObject['trackObj'];                  // add status field (OK) to trackobj
            
            // write content of trackobj to log file
            foreach ($trackObj as $dbField => $value) {
                $trackObjOut["$dbField"]=$value; 
            }

            // prepare return JSON object
            $outObject = array (
                "status"=>"OK",
                "erressage"=>"",
                "trackObj"=>$trackObjOut
            );
            echo json_encode($outObject);                           // echo JSON object to client
            
            // remove imported file & close connections
            fclose($uploadfile);
            if (file_exists) unlink ($uploadfile);                  // remove file if existing
            rmdir($uploaddir, 0777);                                // remove upload directory          

            $conn->close();                                         // Close DB connection

        } else {
            fputs($logFile, "Filetype $filetype not supported. Please import as gpx file.\r\n");    
        }
    } else {
        fputs($logFile, "Line 126: extension is NOT kml or gpx: $filetype \r\n");  

        // prepare JSON return object
        $outObject = array (
            'status'=>'ERR',                                        // add err status to return object
            'errmessage'=>'Wrong file extension',                   // add error message to return object
        );
        echo json_encode($outObject);                               // echo track object to client
        exit;                                                       // exit from php
    }
} else if ( $request == "save") {

    // ---------------------------------------------------------------------------------
    // request type is "SAVE" meaning that track records are updated and finalised
    // ---------------------------------------------------------------------------------

    // read received INPUT object
    $trackObjIn = array();                                          // array storing track data in array
    $sessionid = $receivedData["sessionid"];                        // ID of current user session - required to make site multiuser capable
    $request = $receivedData["request"];                            // temp = temporary creation; save = final storage; cancel = cancel operation / delete track & track points
    $loginname = $receivedData["loginname"];
    $trackObjIn = $receivedData["trackobj"];                        // Array of track data 

    if ( $debugLevel > 2) fputs($logFile, "Line 169: sessionid: $sessionid - request: $request - loginname: $loginname\r\n");  
    
    // Create SQL statement to update temporary track 
    $sql = "UPDATE `tbl_tracks` SET ";                              // Insert Source file name, gps start time and toReview flag
    $sql .= "`trkLoginName`='$loginname',";

    // Looop through received track object and add to SQL statement
    foreach ($trackObjIn as $dbField => $content) {                 // Generate update statement
        $sql .= "`$dbField`='$content',";
    }
    $sql = substr($sql,0,strlen($sql)-1);                           // remove last ,
    $sql .= " WHERE `tbl_tracks`.`trkId` = " . $trackObjIn["trkId"];      
    
    if ($debugLevel>3) fputs($logFile, "Line 164 - sql: $sql\r\n");
    
    // run SQL and handle error
    if ($conn->query($sql) === TRUE)                                // run sql against DB
    {
        if ( $debugLevel > 3) fputs($logFile, "Line 163 - New track inserted successfully\r\n");
    } else {
        fputs($logFile, "Line 165 - Error inserting trkPt: $conn->error\r\n");
        return -1;
    } 

    // Prepare JSON out object
    $outObject = array (
        'status'=>'OK',                                             // add err status to return object
        'errmessage'=>'',                                           // add error message to return object
    );
    echo json_encode($outObject);    
    
} else if ( $request == "cancel") {

    // ---------------------------------------------------------------------------------
    // request type is "CANCEL" meaning that track records are updated and finalised
    // ---------------------------------------------------------------------------------

    // read relevant INPUT parameters
    $trackObjIn = array();                                          // array storing track data in array
    $sessionid = $receivedData["sessionid"];                        // ID of current user session - required to make site multiuser capable
    $trackObjIn = $receivedData["trackobj"];                        // Array of track data 

    // create SQL delete file
    $sql = "DELETE FROM `tbl_tracks` ";                             // Insert Source file name, gps start time and toReview flag
    $sql .= "WHERE `tbl_tracks`.`trkId` = " . $trackObjIn["trkId"]; 
    
    fputs($logFile, "Line 186 - sql: $sql\r\n");
    
    // run SQL statement
    if ($conn->query($sql) === TRUE)                                // run sql against DB
    {
        if ( $debugLevel > 3) fputs($logFile, "Line 163 - New track inserted successfully\r\n");
    } else {
        fputs($logFile, "Line 165 - Error inserting trkPt: $conn->error\r\n");
        return -1;
    } 
    $outObject = array (
        'status'=>'OK',                                             // add err status to return object
        'errmessage'=>'',                                           // add error message to return object
    );
    echo json_encode($outObject);    
} 

// =================================================
// Function to insert tracks to DB
function insertTrack($conn,$filename,$uploadfile,$loginname)
{
    if ($GLOBALS['debugLevel']>4) fputs($GLOBALS['logFile'], "Line 231 - Function insertTrack entered\r\n");

    // read gpx file structure & content
    $gpx = simplexml_load_file($uploadfile);                        // Load XML structure

    // convert and assign time if available
    if ( $gpx->metadata->time != "") 
    {
        $newTrackTime = $gpx->metadata->time;                       // Assign track time from gpx file to variable
        $GpsStartTime = strftime("%Y.%m.%d %H:%M:%S", strtotime($newTrackTime));    // convert track time 
        $DateBegin = strftime("%Y.%m.%d", strtotime($newTrackTime));// convert track time 
        $DateFinish = strftime("%Y.%m.%d", strtotime($newTrackTime)); // convert track time 
    } else {
        $GpsStartTime = "";                                         // convert track time 
        $DateBegin = "";                                            // convert track time 
        $DateFinish = "";                                           // convert track time 
    }
    
    $trackName = $gpx->trk->name;                                   // Track name
            
    // create SQL insert statement
    $sql = "INSERT INTO `tbl_tracks`";                              // Insert Source file name, gps start time and toReview flag
    $sql .= " (`trkSourceFileName`, `trkRoute`, `trkTrackName`, `trkGPSStartTime`, ";
    $sql .= " `trkDateBegin`, `trkDateFinish`, `trkLoginName`) VALUES "; 

    // create value section
    $sql .= "('" . $filename . "', ";                               // create value bracket statement
    $sql .= "'" . $trkRoute . "', ";
    $sql .= "'" . $trackName . "', ";
    $sql .= "'" . $GpsStartTime . "', ";
    $sql .= "'" . $DateBegin . "', ";
    $sql .= "'" . $DateFinish . "', ";
    $sql .= "'" . $loginname . "') ";

    if ($GLOBALS['debugLevel']>4) fputs($GLOBALS['logFile'], "Line 263 - sql: $sql\r\n");

    // run insert statement
    if ($conn->query($sql) === TRUE)                                // run sql against DB
    {
        if ($GLOBALS['debugLevel']>3) fputs($GLOBALS['logFile'], "Line 163 - New track inserted successfully\r\n");
    } else {
        if ($GLOBALS['debugLevel']>0) fputs($GLOBALS['logFile'], "Line 165 - Error inserting trkPt: $conn->error\r\n");
        $returnObject = array (
            "status"=>"ERR",
            "errmessage"=>"Error inserting new track"
        );
        return $returnObject;
    } 

    // select ID of currently inserted track
    $sql = "SELECT max(`trkId`) FROM `tbl_tracks` ";                // Search for trkId of record just created
    
    // run select statement to return max track ID
    if ($stmt = mysqli_prepare($conn, $sql)) 
    {
        mysqli_stmt_execute($stmt);                                 // execute select statement
        mysqli_stmt_bind_result($stmt, $trackid);                   // bind result variables

        // Only one line of result is expected
        while (mysqli_stmt_fetch($stmt)) {                          // Fetch result of sql statement (one result expeced)
            if ($GLOBALS['debugLevel']>3) fputs($GLOBALS['logFile'], "Line 300 - sql: $sql\r\n");
            
            // create JSON object with known gpx data
            $trackObj = array (
                "trkId"=>$trackid,
                "trkSourceFileName"=>"$filename",
                "trkTrackName"=>"$trackName",
                "trkDateBegin"=>"$DateBegin",
                "trkDateFinish"=>"$DateFinish",
                "trkGPSStartTime"=>"$GpsStartTime"
            );
        }

        // Create function return object
        $returnObject = array (
            "status"=>"OK",
            "erressage"=>"",
            "trackObj"=>$trackObj
        );

        return $returnObject;                                       // return tmp trackId, track name and coordinate array in array
        mysqli_stmt_close($stmt);                                   // Close statement
    } else {
        if ($GLOBALS['debugLevel']>0) fputs($GLOBALS['logFile'], "Line 195 - Error selecting max(trkId): $conn->error\r\n");
        if ($GLOBALS['debugLevel']>4) fputs($GLOBALS['logFile'], "Line 196 - sql: $stmt\r\n");
        $returnObject = array (
            "status"=>"ERR",
            "errmessage"=>"Error finding trackId"
        );
        return $returnObject;
    } 
}

// ==========================================================
// Insert track points into table

function insertTrackPoints($conn,$trackid,$filename) 
{
    if ($GLOBALS['debugLevel']>2) fputs($GLOBALS['logFile'], "Line 207 - Function insertTrackPoints entered\r\n");
    
    // define variables
    $tptNumber = 1;                                                 // Set counter for tptNumber to 1
    $loopCumul = $GLOBALS['loopSize'];                              // loopCumul is the sum of loop sizes processed
    $coordArray = array();                                          // initialize array to store coordinates in kml style
    $loop = 0;                                                      // set current loop to 0 (only required for debug purposes)
    $firstInLoop = 1;                                               // flag first record within loop 
    $firstTrackPoint = 1;                                           // flag first record within loop 
    $gpx = simplexml_load_file($filename);                          // Load XML structure
    $trackName = $gpx->trk->name;  
    $totalTrkPts = count($gpx->trk->trkseg->trkpt);                 // total number of track points in file
    $overallDistance = (float) 0;
    $distance = (float) 0;

    // prepare insert statement
    $sqlBase = "INSERT INTO `tbl_trackpoints`";                     // create first part of insert statement 
    $sqlBase .= " (`tptNumber`, `tptTrackFID`, `tptLat`, `tptLon`, ";
    $sqlBase .= "  `tptEle`, `tptTime`) VALUES "; 
    
    // loop through each trkpt XML element in the gpx file
    foreach ($gpx->trk->trkseg->trkpt as $trkpt)                        
    {               
        // read content of file
        $lat = $trkpt["lat"];
        $lon = $trkpt["lon"];
        if ( $trkpt->ele == "" ) {
            $ele = 0;
        } else {
            $ele = $trkpt->ele;
        }
        $ele = $ele * 1;
        $time = strftime("%Y-%m-%d %H:%M:%S", strtotime($trkpt->time));
    
        if ($firstInLoop == 1)  {                                   // if record is not first, a comma is written
    
            // set sql string
            $sql = $sqlBase;                                        // Add first part of sql to variable $sql
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
    
            // if not first record: set comma as separator
            $sql .= ",";
            
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
        
        if ($GLOBALS['debugLevel']>4) {
            fputs($GLOBALS['logFile'],"Line 421>tpNr:$tptNumber|ele:$ele|peakEle:$peakEle|lowEle:$lowEle|mU:$meterUp|mD|$meterDown|dist|$distance\r\n");
        }
        
        $previousEle = $ele;
        $previousLat = $lat;
        $previousLon = $lon;

        // generate sql statement
        $sql .= "('" . $tptNumber . "', ";                          // write tptNumber - a continuous counter for the track points
        $sql .= "'" . $trackid . "', ";                             // tptTrackFID - reference to the track         
        $sql .= "'" . $lat . "', ";                                 // tptLat - latitude value 
        $sql .= "'" . $lon . "', ";                                 // tptLon - longitude value
        $sql .= "'" . $ele . "', ";                                 // tptEle - elevation of track point
        $sql .= "'" . $time . "')";                                 // tptTime - time of track point
        
        // Create coordinate string
        $coordPoint = "$lon,$lat,$ele ";
        array_push( $coordArray, $coordPoint );                     // write Lon, Lat and Ele into coordArray array

        // fire insert statement when no. recs have reached loop size
        if($tptNumber == $loopCumul || $tptNumber == $totalTrkPts)  // If current loop size or last track is reached
        {        
            $loop++;
            if ($GLOBALS['debugLevel']>2) fputs($GLOBALS['logFile'], "Line 249 - loop: $loop\r\n");
            
            if ($conn->query($sql) === TRUE) {                      // execute query
                if ($GLOBALS['debugLevel']>6) fputs($GLOBALS['logFile'],"Line 252 - Sql: " . $sql . "\r\n"); 
                if ($GLOBALS['debugLevel']>1) fputs($GLOBALS['logFile'],"Line 253 - New track points inserted successfully\r\n");
                $loopCumul = $loopCumul + $GLOBALS['loopSize'];     // Raise current loop size by overall loop size
                $firstInLoop = 1;                                   // Next record will be 'first'
                
            } else {
                if ($GLOBALS['debugLevel']>2) fputs($GLOBALS['logFile'],"Line 258 - Sql: " . $sql); 
                if ($GLOBALS['debugLevel']>1) fputs($GLOBALS['logFile'],"Line 259 - Error inserting trkPt! Error Message: $conn->error\r\n");
                return -1;
            }
        }       
        $tptNumber++;                                               // increase track point counter by 1
    }

    fputs($GLOBALS['logFile'],"Line 421>ele:$ele|peakEle:$peakEle|lowEle:$lowEle|mU:$meterUp|mD|$meterDown\r\n");

    // set elevation and time for last point
    $trkFinishEle = $ele;
    $trkFinishTime = $time;

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

    // join array $coordArray into a string
    $coordString = "";
    foreach ( $coordArray as $coordPoint) {                         // Create string containing the coordinates
        $coordString = $coordString . $coordPoint; 
    };

    // write var to track obj
    $trackObj = array (
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
        "trkCoordinates"=>$coordString
    );

    $returnObject = array (
        "status"=>"OK",
        "errmessage"=>"",
        "trackObj"=>$trackObj
    );

    return $returnObject;                                           // return tmp trackId, track name and coordinate array in array
}

function haversineGreatCircleDistance(
$latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo, $earthRadius = 6371000)
/**
 * Calculates the great-circle distance between two points, with
 * the Haversine formula. Formula from internet
 * @param float $latitudeFrom Latitude of start point in [deg decimal]
 * @param float $longitudeFrom Longitude of start point in [deg decimal]
 * @param float $latitudeTo Latitude of target point in [deg decimal]
 * @param float $longitudeTo Longitude of target point in [deg decimal]
 * @param float $earthRadius Mean earth radius in [m]
 * @return float Distance between points in [m] (same as earthRadius)
 */
{
    $latFrom = deg2rad($latitudeFrom);
    $lonFrom = deg2rad($longitudeFrom);
    $latTo = deg2rad($latitudeTo);
    $lonTo = deg2rad($longitudeTo);

    $latDelta = $latTo - $latFrom;
    $lonDelta = $lonTo - $lonFrom;

    $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
    cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));

    return $angle * $earthRadius; 
}
?>