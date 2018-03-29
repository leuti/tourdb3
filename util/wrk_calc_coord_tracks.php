<?php
// ---------------------------------------------------------------------------------------------
// PHP to calculate the coordinate boundaries of existing tracks
//
// This script is a working script, not intended to be integrated into the site 

// Created: 13.3.2018 - Daniel Leutwyler
// ---------------------------------------------------------------------------------------------
// Action:
// * 

// Set variables and parameters
include("../services/config.inc.php");                                        // include config file
include("../services/coord_funct.inc.php");                                    // include coord calc functions
date_default_timezone_set('Europe/Zurich');                         // must be set when using time functions

$count = 0;

// Open file to write log
$importGpxLog = dirname(__FILE__) . "/../log/calc_coord_tracks.log";        // Assign file location
$logFile = @fopen($importGpxLog,"a");                               // open log file handler 
fputs($logFile, "\r\n============================================================\r\n");    
fputs($logFile, "calccalc_coord_tracks_coord.php started: " . date("Ymd-H:i:s", time()) . "\r\n");    

// Select tracks from db
$sql = "SELECT trkId, trkTrackName, trkCoordinates ";
$sql .= "FROM tbl_tracks ";
$sql .= "WHERE trkCoordinates <> ''";

fputs($logFile, "Line 29: sql: $sql\r\n");    
// loop through each track
$records = mysqli_query($conn, $sql);

// write field content
//while( $row = mysqli_fetch_row( $result ) ) {                           // loop through each row

while($singleRecord = mysqli_fetch_assoc($records)) {    
    $firstRecord = 1;
    $coordArray = explode ( " ", $singleRecord["trkCoordinates"]);

    $i=0;
    for ($i; $i<sizeof($coordArray)-1; $i++) {                            // 10 is the number of existing subtypes in array (lines)

        // fputs($logFile, "Line 44 - line: " . $coordArray[$i] . "\r\n"); 

        $line = explode ( ",", $coordArray[$i]);

        $lat = $line[1];
        $lon = $line[0];
        settype($lat,"float");
        settype($lon,"float");
        
        // fputs($logFile, "Line 54 - INPUT record $count--> lat: $lat | lon: $lon\r\n"); 

        if ( $firstRecord ) { 
            $WGS_top_lat = $lat;
            $WGS_top_lon = $lon;
            $WGS_left_lat = $lat;
            $WGS_left_lon = $lon;
            $WGS_right_lat = $lat;
            $WGS_right_lon = $lon;
            $WGS_bottom_lat = $lat;
            $WGS_bottom_lon = $lon;
        }
        
        if( $lat > $WGS_top_lat ) {                                     // This is the top most point
            $WGS_top_lat = $lat;
            $WGS_top_lon = $lon;
        } else if ( $lat < $WGS_bottom_lat ) {                          // This is the bottom most point
            $WGS_bottom_lat = $lat;
            $WGS_bottom_lon = $lon;
        }           
        if( $lon > $WGS_right_lon ) {                                  // This is the right most point
            $WGS_right_lat = $lat;                               
            $WGS_right_lon = $lon;                               
        } else if ( $lon < $WGS_left_lon ) {                          // This is the left most point
            $WGS_left_lat = $lat;                               
            $WGS_left_lon = $lon;                               
        }

        $firstRecord = 0 ;

        if ( $debugLevel >= 6 ) {
            fputs($logFile, "...........................................\r\n");  
            fputs($logFile, "Track: " . $singleRecord["trkId"] . " - " .  $singleRecord["trkTrackName"] . "\r\n");   
            fputs($logFile, "WGS_top_lat: $WGS_top_lat\r\n");
            fputs($logFile, "WGS_top_lon: $WGS_top_lon\r\n");
            fputs($logFile, "WGS_left_lat: $WGS_left_lat\r\n");
            fputs($logFile, "WGS_left_lon; $WGS_left_lon\r\n");
            fputs($logFile, "WGS_right_lat: $WGS_right_lat\r\n");
            fputs($logFile, "WGS_right_lon: $WGS_right_lon\r\n");
            fputs($logFile, "WGS_bottom_lat: $WGS_bottom_lat\r\n");
            fputs($logFile, "WGS_bottom_lon: $WGS_bottom_lon\r\n");
        }
    }

    $coordTop = round( WGStoCHx($WGS_top_lat, $WGS_top_lon), 0);                                               // variables to define min/max lon/lat to diplay track in center of map, focused
    $coordLeft = round( WGStoCHy($WGS_left_lat, $WGS_left_lon), 0);
    $coordRight = round( WGStoCHy($WGS_right_lat, $WGS_right_lon), 0);
    $coordBottom = round( WGStoCHx($WGS_bottom_lat, $WGS_bottom_lon), 0);

    if ( $debugLevel >= 3 ) {
        fputs($logFile, "--------------------------------------------------------------------------------------\r\n");  
        fputs($logFile, "Track: " . $singleRecord["trkId"] . " - " .  $singleRecord["trkTrackName"] . "\r\n");   
        fputs($logFile, "WGS_top_lat: $WGS_top_lat\r\n");
        fputs($logFile, "WGS_top_lon: $WGS_top_lon\r\n");
        fputs($logFile, "WGS_left_lat: $WGS_left_lat\r\n");
        fputs($logFile, "WGS_left_lon; $WGS_left_lon\r\n");
        fputs($logFile, "WGS_right_lat: $WGS_right_lat\r\n");
        fputs($logFile, "WGS_right_lon: $WGS_right_lon\r\n");
        fputs($logFile, "WGS_bottom_lat: $WGS_bottom_lat\r\n");
        fputs($logFile, "WGS_bottom_lon: $WGS_bottom_lon\r\n");

        fputs($logFile, "Line 78: coordTop: $coordTop\r\n");
        fputs($logFile, "Line 79: coordBottom: $coordBottom\r\n");
        fputs($logFile, "Line 80: coordLeft: $coordLeft\r\n");
        fputs($logFile, "Line 81: coordRight: $coordRight\r\n");        
    }

    //create SQL statement  
    $sql = "UPDATE `tourdb2_prod`.`tbl_tracks` ";
    $sql .= "SET `trkCoordTop` = '$coordTop', `trkCoordBottom` = '$coordBottom', ";
    $sql .= "`trkCoordLeft` = '$coordLeft', `trkCoordRight` = '$coordRight' ";
    $sql .= "WHERE `tbl_tracks`.`trkId` = " . $singleRecord["trkId"];

    fputs($logFile, "Line 29: sql: $sql\r\n");   

    if ($conn->query($sql) === TRUE)                                // run sql against DB
    {
        $count++;
    } else {
        echo "Error - could not update track " . $singleRecord["trkId"] . "\r\n";
    } 
}

echo "Finito --> $count tracks updated\r\n";

fclose($logFile);                                                      // close log file
mysqli_close($conn);                                                   // close SQL connection 
?>