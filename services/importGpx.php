<?php
// ---------------------------------------------------------------------------------------------
// PHP script loading gpx files into table track points. When no track exists, a simple track
// record is created. Before loading the gpx track points all previous track points are deleted. 
// When a gpx for an existing track is imported, the track time is updated.
//
// This script is intended for regular usage
//
// Parameters:
// sessionid: id of user session; used to ensure multi-user capabilities
// filename: name of file to be uploaded (one at a time); file is expected at import/gpx or import/kml
// filetype: type of file to be imported (gpx or kml)
//
// Actions:
// * Process file upload: https://www.w3schools.com/php/php_file_upload.asp
// * error handling if filename/filetype is empty
// * Impl. solution to have unique sessionids (in function insertTmpTrackPoint)
// * remove upload directory not yet working

// Created: 13.12.2017 - Daniel Leutwyler
// ---------------------------------------------------------------------------------------------

// -----------------------------------
// Set variables and parameters
include("./config.inc.php");                                        // include config file
date_default_timezone_set('Europe/Zurich');                         // must be set when using time functions

$debugLevel = 3;                                                    // 0 = off, 6 = all
$recordNo = 0;                                                      // No of gpx files processed
$loopSize = 5000;                                                   // Number of trkPts inserted in one go
$newTrack = array();
$coordArray = array();                                              // initialize array to store coordinates in kml style

// Open file for import log
$importGpxLog = dirname(__FILE__) . "\..\log\importGpx.log";        // Assign file location
$logFile = @fopen($importGpxLog,"w");                               // open log file handler 
fputs($logFile, "importGpx.php started: " . date("Ymd-H:i:s", time()) . "\r\n");    

$sessionid = $_REQUEST['sessionid'];                                // ID of current user session - required to make site multiuser capable
$filetype = $_REQUEST['filetype'];                                  // Type of upload file (gpx or kml)

$uploaddir = '../import/gpx/uploads/' . $sessionid . '/';           // Session id used to create unique directory
$uploadfile = $uploaddir . basename($_FILES['filename']['name']);

if ( $debugLevel > 2) fputs($logFile, "Line 45 - sessionid: $sessionid\r\n");    
if ( $debugLevel > 2) fputs($logFile, "Line 46 - filetype: $filetype\r\n");   
if ( $debugLevel > 2) fputs($logFile, "Line 48 - uploaddir: $uploaddir\r\n");  
if ( $debugLevel > 2) fputs($logFile, "Line 49 - uploadfile: $uploadfile\r\n");  
if ( $debugLevel > 2) fputs($logFile, "Line 49 - tmp_name: " . $_FILES['filename']['tmp_name'] . "\r\n");  

if (!is_dir ( $uploaddir )) {
    mkdir($uploaddir, 0777);
}

if (move_uploaded_file($_FILES['filename']['tmp_name'], $uploadfile)) {
    if ( $debugLevel > 2) fputs($logFile, "Line 51 - file " . $_FILES['filename']['name'] . " successfully uploaded to: $uploaddir\r\n");    
} else {
    fputs($logFile, "Line 53 - error uploading file " . $_FILES['filename']['name'] . " to: $uploaddir\r\n"); 
}  

// -----------------------------------------
// Main routine
// -----------------------------------------

if ( $filetype == "gpx") {
    // insert track points found in file in table tmp_trackpoints with unique FID
    $returnArray = insertTmpTrackPoint($conn,$sessionid, $uploadfile);       // Insert new track points; returns temp ID for track
    
    $tmpTrkId = $returnArray[0];
    $trackName = $returnArray[1];
    $coordArray = $returnArray[2];
    $coordString = "";
    if ($debugLevel>1) fputs($logFile, "Line 73 - tmpTrkId: $tmpTrkId\r\n");   
    if ($debugLevel>1) fputs($logFile, "Line 74 - trackName: $trackName\r\n");   
    //if ($debugLevel>1) fputs($logFile, "Line 71 - coordArray: $coordArray\r\n");   
    if ($debugLevel>1) fputs($logFile, "Line 76 - insertTmpTrackPoint: Return value - tmpTrkId: $tmpTrkId\r\n");    

    // join array $coordArray into a string
    foreach ( $coordArray as $coordLine) {
        $coordString = $coordString . $coordLine; 
    };
    if ($debugLevel>1) fputs($logFile, "Line 69 - coordString: $coordString\r\n");    

    // calculate distance based on gpx data

    // calculate time based on gpx data

    // calcuate meters up and down based on gpx data

    // create JSON object with known gpx data
    $newTrack = array (
        "trkId"=>$tmpTrkId,
        "trkLogbookId"=>"",
        "trkSourceFileName"=>"",
        "trkPeakRef"=>"",
        "trkTrackName"=>"$trackName",
        "trkRoute"=>"",
        "trkDateBegin"=>"",
        "trkDateFinish"=>"",
        "trkGPSStartTime"=>"",
        "trkSaison"=>"",
        "trkType"=>"",
        "trkSubType"=>"",
        "trkOrg"=>"",
        "trkOvernightLoc"=>"",
        "trkParticipants"=>"",
        "trkEvent"=>"",
        "trkRemarks"=>"",
        "trkDistance"=>"",
        "trkTimeOverall"=>"",
        "trkTimeToTarget"=>"",
        "trkTimeToEnd"=>"",
        "trkGrade"=>"",
        "trkMeterUp"=>"",
        "trkMeterDown"=>"",
        "trkCountry"=>"",
        "trkToReview"=>"",
        "trkCoordinates"=>$coordString
    );
    // 

    // return JSON object to client
    echo json_encode($newTrack);

    // remove imported file
    $conn->close();                                                             // Close DB connection
    //rmdir($uploaddir, 0777);                                                    // remove upload directory          
} else {
    fputs($logFile, "Filetype $filetype not supported. Please import as gpx file.\r\n");    
}

// ----------------------------------------------------------
// Insert track points into table
// ----------------------------------------------------------
function insertTmpTrackPoint($conn,$sessionid, $filename) 
{
    if ($GLOBALS['debugLevel']>2) fputs($GLOBALS['logFile'], "Line 251 - Function insertTmpTrackPoint entered\r\n");

    $tmpTrkId = $sessionid;                                            // Impl. solution to have unique sessionids
    $tptNumber = 1;                                                 // Set counter for tptNumber to 1
    $loopCumul = $GLOBALS['loopSize'];                              // loopCumul is the sum of loop sizes processed
    $gpx = simplexml_load_file($filename);                          // Load XML structure
    $trackName = $gpx->trk->name;  
    $coordArray = array();                                          // initialize array to store coordinates in kml style

    $totalTrkPts = count($gpx->trk->trkseg->trkpt);                 // total number of track points in file
    $loop = 0;                                                      // set current loop to 0 (only required for debug purposes)


    $sqlBase = "INSERT INTO `tourdb2`.`tmp_trackPoints`";           // create first part of insert statement 
    $sqlBase .= " (`tptNumber`, `tptTrackFID`, `tptLat`, `tptLon`, ";
    $sqlBase .= "  `tptEle`, `tptTime`) VALUES "; 
    
    $firstRec = 1;                                                  // flag first record as all other records need to be treated slightly different 

    foreach ($gpx->trk->trkseg->trkpt as $trkpt)                    // loop through each trkpt XML element in the gpx file
    {                  
        if ($firstRec == 1)                                         // if record is not first, a comma is written
            {
                $sql = $sqlBase;                                    // Add first part of sql to variable $sql
                $firstRec = 0;
        } else
        {
            $sql .= ",";
        }
        
        $sql .= "('" . $tptNumber . "', ";                          // write tptNumber - a continuous counter for the track points
        $sql .= "'" . $tmpTrkId . "', ";                            // tptTrackFID - reference to the track         
        $sql .= "'" . $trkpt["lat"] . "', ";                        // tptLat - latitude value 
        $sql .= "'" . $trkpt["lon"] . "', ";                        // tptLon - longitude value
        $sql .= "'" . $trkpt->ele . "', ";                          // tptEle - elevation of track point
        $sql .= "'" . strftime("%Y.%m.%d %H:%M:%S", strtotime($trkpt->time)) . "')";     // tptTime - time of track point
        
        $coordString = $trkpt["lon"] . ',' . $trkpt["lat"] . ',' . $trkpt->ele . ' ';

        array_push( $coordArray, $coordString );                    // write Lon, Lat and Ele into coordArray array

        if($tptNumber == $loopCumul || $tptNumber == $totalTrkPts)  // If current loop size or last track is reached
        {        
            $loop++;
            if ($GLOBALS['debugLevel']>2) fputs($GLOBALS['logFile'], "Line 286 - loop: $loop\r\n");
            
            if ($conn->query($sql) === TRUE) {                      // execute query
                if ($GLOBALS['debugLevel']>6) fputs($GLOBALS['logFile'],"Line 289 - Sql: " . $sqldebug . "\r\n"); 
                if ($GLOBALS['debugLevel']>1) fputs($GLOBALS['logFile'],"Line 290 - New track points inserted successfully\r\n");
                $loopCumul = $loopCumul + $GLOBALS['loopSize'];     // Raise current loop size by overall loop size
                $firstRec = 1;                                      // Next record will be 'first'
                
            } else {
                if ($GLOBALS['debugLevel']>2) fputs($GLOBALS['logFile'],"Line 295 - Sql: " . $sql); 
                if ($GLOBALS['debugLevel']>1) fputs($GLOBALS['logFile'],"Line 296 - Error inserting trkPt! Error Message: $conn->error\r\n");
                return -1;
            }
        }       
        $tptNumber++;                                               // increase track point counter by 1
    }

    fputs($logFile,"Line 198 - trackName: $trackName\r\n");    
    return array($tmpTrkId,$trackName,$coordArray);                                                  // return tmp trackId and track name in array
}
?>