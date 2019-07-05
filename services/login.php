<?php
// ---------------------------------------------------------------------------------------------
// Service verifying if the provided login and password are valid
//
// Input Parameters (JSON object):
// -------------------------------
// login: login name entered by user
// password: password provided by user
// 
// Output Parameter (JSON object):
// -------------------------------
// login: login name
// loginStatus: OK or ERR
// loginTime: time of login (attempt)
// message: Message about login result
// sessionId: ID of active session
// 
//
// Created: 24.06.2019 - Daniel Leutwyler
// ---------------------------------------------------------------------------------------------


// -----------------------------------
// Set variables and parameters
include("./config.inc.php");                                        // include config file
date_default_timezone_set('Europe/Zurich');                         // must be set when using time functions

// Open file for import log
$importGpxLog = dirname(__FILE__) . "/../log/login.log";            // Assign file location
if ( $debugLevel >= 1) $logFile = @fopen($importGpxLog,"a");        // open log file handler 
if ( $debugLevel >= 1 ) fputs($logFile, "\r\n============================================================\r\n");    
if ( $debugLevel >= 1 ) fputs($logFile, "login.php started: " . date("Ymd-H:i:s", time()) . "\r\n");    

// variables passed on by client (as formData object)
$receivedData = json_decode ( file_get_contents('php://input'), true );
$login = $receivedData["login"];
$password = $receivedData["password"];

if ( $debugLevel >= 3 ) fputs($logFile, "Line 32: User <$login> has logged in.\r\n");    

// Start or restart session 
session_start();

// check if login and session is set
if ( isset($login) ) {
    if ( $login == "leut" && $password == "sugus" ||                // check if login in known --> these login must be moved to DB
        $login == "admin" && $password == "20Rh5530rpHqiEpfc6Is" )
    {
        // If login is successful return login & session details
        $returnObject['login'] = $login;                            // set session var
        $returnObject['sessionId'] = session_id();                  // add session id to return object
        $returnObject['loginStatus'] = "OK";                        // set login status to OK
        $returnObject['message'] = "Login successful";
        $returnObject['loginTime'] = date("Ymd-H:i:s", time());
    } else {
        // If login failed return empty session and error
        $returnObject['login'] = $login;
        $returnObject['sessionId'] = session_id();                      // set session id to EMPTY 
        $returnObject['loginStatus'] = "ERR";                           // Return error    
        $returnObject['message'] = "Login failed";                      // Return error    
        $returnObject['loginTime'] = date("Ymd-H:i:s", time());
    }
}

echo json_encode($returnObject);                                    // encode return object to JSON
if ( $debugLevel >= 1 ) 
{
    fputs($logFile, "Line 57: Login script completed --> sessionId: " . $returnObject['sessionId'] . " | login: " 
    . $login . " | loginStatus: " . $returnObject['loginStatus'] . "\r\n");
    fclose($logFile);                                               // close log file
}
?>