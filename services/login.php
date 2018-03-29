<?php
// ---------------------------------------------------------------------------------------------
// Service verifying if the provided login and password are valid
//
// Input Parameters (JSON object):
// loginName: login name entered by user
// loginPasswd: password provided by user
//
// Created: 08.01.2018 - Daniel Leutwyler
// ---------------------------------------------------------------------------------------------
// ACTIONS
// * Move login check to DB

// -----------------------------------
// Set variables and parameters
include("./config.inc.php");                                        // include config file
date_default_timezone_set('Europe/Zurich');                         // must be set when using time functions

// Open file for import log
$importGpxLog = dirname(__FILE__) . "/../log/login.log";            // Assign file location
$logFile = @fopen($importGpxLog,"a");                               // open log file handler 
if ( $debugLevel >= 1 ) fputs($logFile, "\r\n============================================================\r\n");    
if ( $debugLevel >= 1 ) fputs($logFile, "login.php started: " . date("Ymd-H:i:s", time()) . "\r\n");    
if ( $debugLevel >= 1 ) fputs($logFile, "Line 26: debuglevel set to: $debugLevel\r\n");    

// variables passed on by client (as formData object)
$receivedData = json_decode ( file_get_contents('php://input'), true );
$loginName = $receivedData["loginName"];
$loginPasswd = $receivedData["loginPasswd"];

if ( $debugLevel >= 3 ) fputs($logFile, "Line 33: User <$loginName> has logged in.\r\n");    

// Start or restart session 
session_start();

// in case this script is started from login page
if ( isset($loginName) )
{
    if ( $loginName == "leut" && $loginPasswd == "sugus" ||          // check if login in known --> these login must be moved to DB
        $loginName == "admin" && $loginPasswd == "admin" )
    {
        $_SESSION["login"] = $loginName;                            // set session var
    }
}

// check if within session
if ( isset($_SESSION["login"]) )
{    
    $returnObject['sessionid'] = session_id();                      // add session id to return object
    $returnObject['loginstatus'] = "OK";                            // set login status to OK
} else {
    $returnObject['sessionid'] = "";                                // set session id to EMPTY 
    $returnObject['loginstatus'] = "ERROR";                         // Return error
}
echo json_encode($returnObject);                                    // encode return object to JSON
if ( $debugLevel >= 1 ) 
{
    fputs($logFile, "Line 60: Login script completed --> sessionid: " . $returnObject['sessionid'] . " | login: " 
    . $loginName . " | loginstatus: " . $returnObject['loginstatus'] . "\r\n");
}
?>