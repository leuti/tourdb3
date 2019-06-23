<?php
// ---------------------------------------------------------------------------------------------
// This php exports all tracks in JSON format. This JSON file can then be used to reupload the 
// tracks and to trigger the calculation of the times and meters up/down. This script is not 
// intended for regular use.
//
// Parameters:
// 

// Created: 17.2.2018 - Daniel Leutwyler
// ---------------------------------------------------------------------------------------------
// Action:

// Set variables and parameters
include("./config.inc.php");                                        // include config file
date_default_timezone_set('Europe/Zurich');                         // must be set when using time functions

// Open file to write log
$log = dirname(__FILE__) . "/../log/exportTrkFmDB.log";        // Assign file location
$logFile = @fopen($log,"a");                               // open log file handler 
fputs($logFile, "\r\n============================================================\r\n");    
fputs($logFile, "exportTracksFromDb.php started: " . date("Ymd-H:i:s", time()) . "\r\n");    

$sql = "SELECT 
            trkId,
            trkLogbookId,
            trkSourceFileName,
            trkPeakRef,
            trkTrackName,
            trkRoute,
            trkDateBegin,
            trkDateFinish,
            trkGPSStartTime,
            trkSaison,
            trkType,
            trkSubType,
            trkOrg,
            trkOvernightLoc,
            trkParticipants,
            trkEvent,
            trkRemarks,
            trkDistance,
            trkTimeOverall,
            trkTimeToPeak,
            trkTimeToFinish,
            trkStartEle,
            trkPeakEle,
            trkPeakTime,
            trkLowEle,
            trkLowTime,
            trkFinishEle,
            trkFinishTime,
            trkGrade,
            trkMeterUp,
            trkMeterDown,
            trkCountry,
            trkLoginName,
            trkToReview,
            trkCoordinates
        FROM tbl_tracks WHERE trkId in (2,3)";

    $trackArray = array();
    if ($result = $conn->query($sql)) {
        $tempArray = array();
        while($row = $result->fetch_object()) {
                $tempArray = $row;
                array_push($trackArray, $tempArray);
            }
        $out = dirname(__FILE__) . "/../tmp/reimport/track.JSON";        // Assign file location
        $JSONoutFile = @fopen($out,"w");    
        echo json_encode($trackArray);
        echo $out;
        fputs($JSONoutFile, json_encode($trackArray));  
    };

    if ($debugLevel >= 3){
        fputs($logFile, 'Line 80: trackArray: ' . $trackArray . "\r\n");
        print_r($trackArray);
    };

    fputs($logFile, "exportTracksFromDb.php finished: " . date("Ymd-H:i:s", time()) . "\r\n");    
    
    // Close all files and connections
    if ( $debugLevel >= 1 ) fclose($logFile);                                   // close log file
    fclose($JSONoutFile);                                   // close log file
    $result->close();                                                        // close SQL connection 
    exit;
    
?>