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
// * error handling if filename/filetype is empty
// * Impl. solution to have unique sessionids (in function insertTmpTrackPoint)
//
// Created: 13.12.2017 - Daniel Leutwyler
// ---------------------------------------------------------------------------------------------

// -----------------------------------
// Set variables and parameters
include("./config.inc.php");                                        // include config file
date_default_timezone_set('Europe/Zurich');                         // must be set when using time functions

$debugLevel = 0;                                                    // 0 = off, 6 = all
$recordNo = 0;                                                      // No of gpx files processed
$loopSize = 5000;                                                   // Number of trkPts inserted in one go
$newTrack = array();
$coord = array();                                                   // initialize array to store coordinates in kml style

// Open file for import log
$importGpxLog = dirname(__FILE__) . "\..\out\importGpx.log";        // Assign file location
$logFile = @fopen($importGpxLog,"w");                               // open log file handler 
fputs($logFile, "importGpx.php started: " . date("Ymd-H:i:s", time()) . "\r\n");    

// Assign input parameters
if(isset($_POST["sessionid"]) && $_POST["sessionid"] != ''){
    $sessionid = $_POST["sessionid"]; 
} else{
                                                                    // Potential error handling if filename is empty
};
if ( $debugLevel > 2) fputs($logFile, "Sessionid: $sessionid\r\n");    

if(isset($_POST["filename"]) && $_POST["filename"] != ''){
    $filename = dirname(__FILE__) . "\..\import\gpx\\" . $_POST["filename"]; 
} else{
                                                                    // Potential error handling if filename is empty
};
if ( $debugLevel > 2) fputs($logFile, "filename: $filename\r\n");    

if(isset($_POST["filetype"]) && $_POST["filetype"] != ''){
    $filetype = $_POST["filetype"]; 
} else{
                                                                    // Potential error handling if filetype is empty
};
if ( $debugLevel > 2) fputs($logFile, "filetype: $filetype\r\n");    

// -----------------------------------------
// Main routine
// -----------------------------------------

if ( $filetype == "gpx") {
    // insert track points found in file in table tmp_trackpoints with unique FID
    $returnArray = insertTmpTrackPoint($conn,$sessionid, $filename);       // Insert new track points; returns temp ID for track
    if ($debugLevel>1) fputs($logFile, "Line 96 - insertTmpTrackPoint: Return value - tmpTrkId: $tmpTrkId\r\n");    
    $trkId = $returnArray[0];
    $trackName = $returnArray[1];
    $coord = $returnArray[2];

    // join array $coord into a string
    foreach ( $coord as $coordLine) {
        $coordString .= $coordLine + ' '; 
    };
    if ($debugLevel>1) fputs($logFile, "Line 69 - coordString: $coordString\r\n");    

    // calculate distance based on gpx data

    // calculate time based on gpx data

    // calcuate meters up and down based on gpx data

    // create JSON object with known gpx data
    $newTrack = array (
        "trkId"=>$trkId,
        "trkLogbookId"=>"",
        "trkSourceFileName"=>"",
        "trkPeakRef"=>"",
        "trkTrackName"=>$trackName,
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
        "trkCoordinates"=>$coord
    );
    // 

    // remove imported file


    // return JSON object to client
    echo json_encode($segArray);

    $conn->close();                                                     // Close DB connection

    // ----------------------------------------------------------
    // Insert track points into table
    // ----------------------------------------------------------
    function insertTmpTrackPoint($conn,$sessionid, $filename) 
    {
        if ($GLOBALS['debugLevel']>2) fputs($GLOBALS['logFile'], "Line 251 - Function insertTmpTrackPoint entered\r\n");

        $trkId = $sessionid;                                            // Impl. solution to have unique sessionids
        $tptNumber = 1;                                                 // Set counter for tptNumber to 1
        $loopCumul = $GLOBALS['loopSize'];                              // loopCumul is the sum of loop sizes processed
        $gpx = simplexml_load_file($fullFileName);                      // Load XML structure
        $trackName = $gpx->trk->name;  

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
            $sql .= "'" . $trkId . "', ";                               // tptTrackFID - reference to the track         
            $sql .= "'" . $trkpt["lat"] . "', ";                        // tptLat - latitude value 
            $sql .= "'" . $trkpt["lon"] . "', ";                        // tptLon - longitude value
            $sql .= "'" . $trkpt->ele . "', ";                          // tptEle - elevation of track point
            $sql .= "'" . strftime("%Y.%m.%d %H:%M:%S", strtotime($trkpt->time)) . "')";     // tptTime - time of track point
            
            array_push( $coord, ' ' . $trkpt["tptLon"] . ',' . $trkpt["tptLat"] . ',' . $trkpt["tptEle"]);
                                                                        // write Lon, Lat and Ele into coord array

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
        if ($GLOBALS['debugLevel']>1) fputs($GLOBALS['logFile'],"Line 305 - Return value - resUpdateTrack: $resUpdateTrack \r\n");    
        return array($trkId,$trackName,$coord);                                                  // return tmp trackId and track name in array
    }
} else {
    fputs($logFile, "Filetype $filetype not supported. Please import as gpx file.\r\n");    
}
?>