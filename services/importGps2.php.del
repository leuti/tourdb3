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
// trackObj

// Set variables and parameters
//include("./config.inc.php");                                        // include config file
//date_default_timezone_set('Europe/Zurich');                         // must be set when using time functions

$debugLevel = 5;                                                    // 0 = off, 6 = all

// Open file to write log
$importGpxLog = dirname(__FILE__) . "/../log/importGpx2.log";        // Assign file location
$logFile = @fopen($importGpxLog,"a");                               // open log file handler 
fputs($logFile, "\r\n============================================================\r\n");    
fputs($logFile, "importGpx.php started: " . date("Ymd-H:i:s", time()) . "\r\n");    
echo "gopf";
/*
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
*/

?>