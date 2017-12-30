<?php
// ---------------------------------------------------------------------------------------------
// This script reads the track point for the selected tracks and writes them 
// as kml coordinate string into the table trk_tracks.trkCoordinates
//
// This script will be reused in the importGpx.php
//
// Created: 30.12.2017 - Daniel Leutwyler
// ---------------------------------------------------------------------------------------------
//
// * Close all files and connections

// -----------------------------------
// Set variables and parameters    
include("config.inc.php");                              // Include config file
date_default_timezone_set('Europe/Zurich');             // must be set when using time functions
$debugLevel = 3;                                        // 0 = off, 6 = all
$countTracks = 0;                                       // Internal counter for tracks processed
$countTrkPts =0;                                        // Internal counter for trackpoints processed

// --------------------------------------------------

// Open file for import log
$logFile = @fopen(dirname(__FILE__) . "\\..\\log\\fillTrkCoord.log","w");                                       // open log file handler 
fputs($logFile, "importGpx.php started: " . date("Ymd-H:i:s", time()) . "\r\n");    

// Select tracks meeting given WHERE clause
$whereGenKml = "WHERE 1";
$sql = "SELECT trkId FROM tbl_tracks $whereGenKml ORDER BY trkId";

fputs($logFile, "Line 31  sql: $sql\r\n");

$tracks = mysqli_query($conn, $sql);

// Loop through each selected track and write main track data
while($SingleTrack = mysqli_fetch_assoc($tracks))
{ 
    $trkId = $SingleTrack["trkId"]; 
    fputs($logFile, "Line 39  trkId: $trkId\r\n");

    $countTracks++;
    // Select all track points for the current track
    $sqlTrkPt  = "SELECT tptLat, tptLon, tptEle ";
    $sqlTrkPt .= "FROM tbl_trackPoints WHERE tptTrackFID = ";
    $sqlTrkPt .= $trkId . " ORDER BY tptNumber"; 
    
    fputs($logFile, "Line 47  sqlTrkPt: $sqlTrkPt\r\n");

    $trackPoints = mysqli_query($conn, $sqlTrkPt);
   
    // For each trkId loop track point and create coordinates string
    $first = 1;                                                             
    while($trackPoint = mysqli_fetch_assoc($trackPoints))
    {
        $countTrkPts++;
        if ($first==1)                                                          // When first don't print the space between coordinate points
        {
            $coord = $trackPoint["tptLon"] . ',' . $trackPoint["tptLat"] . ',' . $trackPoint["tptEle"];
            $first = 0;
        } else 
        {
            $coord .= ' ' . $trackPoint["tptLon"] . ',' . $trackPoint["tptLat"] . ',' . $trackPoint["tptEle"];
        }
    }
    $sqlUpdTrk =  "UPDATE `tourdb2`.`tbl_tracks` ";                       // create sql statement to update track gps start time and track name
    $sqlUpdTrk .= "SET `trkCoordinates` = '$coord' ";
    $sqlUpdTrk .= "WHERE `trkId`=$trkId";

    fputs($logFile, "Line 47  sqlUpdTrk: $sqlUpdTrk\r\n");

    if ($conn->query($sqlUpdTrk) === TRUE) {
    } else {
        fputs($logFile, "Error updating trkCoordinates\r\n");
    }
    fputs($logFile, "trkId: $trkId"); 
    fputs($logFile, "$countTracks Tracks processed and $countTrkPts Trackpoints processed\r\n");
    //fputs($logFile, "sql\r\n$sql\r\n");    
}

fputs($logFile, "importGpx.php finished: " . date("Ymd-H:i:s", time()) . "\r\n");    

fputs($logFile, "$countTracks Tracks processed and $countTrkPts Trackpoints processed\r\n");

// Close all files and connections
fclose($logFile);                                               // close log file
//mysql_close($trackPoints);                                             // close SQL connection 

?>