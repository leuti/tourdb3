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

// -----------------------------------
// Set variables and parameters
include("./config.inc.php");                                        // include config file
date_default_timezone_set('Europe/Zurich');                         // must be set when using time functions

$debugLevel = 3;                                                    // 0 = off, 6 = all

// Open file for import log
$importGpxLog = dirname(__FILE__) . "/../log/login.log";        // Assign file location
$logFile = @fopen($importGpxLog,"a");                               // open log file handler 
fputs($logFile, "\r\n============================================================\r\n");    
fputs($logFile, "login.php started: " . date("Ymd-H:i:s", time()) . "\r\n");    

// variables passed on by client (as formData object)
$receivedData = json_decode ( file_get_contents('php://input'), true );
$loginName = $receivedData["loginName"];
$loginPasswd = $receivedData["loginPasswd"];

if( $debugLevel > 1) fputs($logFile, "Line 30: Request (JSON): $loginName & $loginPasswd\r\n");    

// Start or restart session 
session_start();

    // in case this script is started from login page
    if(isset($loginName))
    {
        if( $loginName == "leut" && $loginPasswd == "sugus" ||
            $loginName == "admin" && $loginPasswd == "admin" )
        {
            $_SESSION["login"] = $loginName;
            if( $debugLevel > 3) fputs($logFile, "Line 42: $loginName\r\n");   
        }
    }

    // check if within sessioin
    if(isset($_SESSION["login"]))
    {    
        $returnObject['sessionid'] = session_id();                   // add field coordinates to track object
        $returnObject['loginstatus'] = "OK";
                                // echo track object to client
    } else {
        $returnObject['sessionid'] = "";                   // add field coordinates to track object
        $returnObject['loginstatus'] = "ERROR";
    }
    echo json_encode($returnObject); 
    if( $debugLevel > 3) {
        fputs($logFile, "Line 58: sessionid: " . $returnObject['sessionid'] . " || " . $returnObject['loginstatus'] . "\r\n");
    }
?>