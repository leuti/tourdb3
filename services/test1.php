<?php
// ---------------------------------------------------------------------------------------------
// This script returns an empty OK message. This is because I currently have no solution for the 
// $.ajax().when for multiple calls (calls only required when genKml = true)

// Created: 21.03.2018 - Daniel Leutwyler
// ---------------------------------------------------------------------------------------------
//
// Tasks
// * 

include("config.inc.php");                                                  // Include config file
$debugLevel = 3;

if ($debugLevel >= 1){
    $logFileLoc = dirname(__FILE__) . "/../log/test1.log";                // Assign file location
    $logFile = @fopen($logFileLoc,"a");     
    if ( $debugLevel >= 1 ) fputs($logFile, "=================================================================\r\n");
    if ( $debugLevel >= 1 ) fputs($logFile, date("Ymd-H:i:s", time()) . "-Line 59: test1.php opened \r\n"); 
};

$receivedData = json_decode ( file_get_contents('php://input'), true );
$objectName = $receivedData["objectName"];

if ( $debugLevel >= 1 ) fputs($logFile, "objectName: $objectName\r\n"); 

// Create return object
$returnObject['status'] = 'OK';                                             // add status field (OK) to trackobj
$returnObject['message'] = 'This php returns always an empty OK message';   // add empty error message to trackobj
$returnObject['recordcount'] = 0;
$returnObject['objectName'] = $objectName;
echo json_encode($returnObject);                                            // echo JSON object to client

if ( $debugLevel >= 1 ) fclose($logFile);                                   // close log file

exit;

?>

