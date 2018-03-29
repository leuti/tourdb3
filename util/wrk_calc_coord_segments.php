<?php
// ---------------------------------------------------------------------------------------------
// PHP to calculate the coordinate boundaries of existing segmentss
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
$importGpxLog = dirname(__FILE__) . "/../log/calc_coord_segments.log";        // Assign file location
$logFile = @fopen($importGpxLog,"a");                               // open log file handler 
fputs($logFile, "\r\n============================================================\r\n");    
fputs($logFile, "calc_coord_segments.php started: " . date("Ymd-H:i:s", time()) . "\r\n");    

// Select segments from db
$sql = "SELECT segId, segCoordinates ";
$sql .= "FROM tbl_segments ";
$sql .= "WHERE segCoordinates <> '' ";

fputs($logFile, "Line 29: sql: $sql\r\n");    

// loop through each segments
$records = mysqli_query($conn, $sql);

while($singleRecord = mysqli_fetch_assoc($records)) {    
    $firstRecord = 1;
    $coordArray = explode ( " ", $singleRecord["segCoordinates"]);

    $i=0;
    for ($i; $i<sizeof($coordArray)-1; $i++) {                            // 10 is the number of existing subtypes in array (lines)

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
    }

    $coordTop = round( WGStoCHx($WGS_top_lat, $WGS_top_lon), 0);                                               // variables to define min/max lon/lat to diplay segments in center of map, focused
    $coordLeft = round( WGStoCHy($WGS_left_lat, $WGS_left_lon), 0);
    $coordRight = round( WGStoCHy($WGS_right_lat, $WGS_right_lon), 0);
    $coordBottom = round( WGStoCHx($WGS_bottom_lat, $WGS_bottom_lon), 0);
    
    //create SQL statement  
    $sql = "UPDATE `tourdb2_prod`.`tbl_segments` ";
    $sql .= "SET `segCoordTop` = '$coordTop', `segCoordBottom` = '$coordBottom', ";
    $sql .= "`segCoordLeft` = '$coordLeft', `segCoordRight` = '$coordRight' ";
    $sql .= "WHERE `tbl_segments`.`segId` = " . $singleRecord["segId"];

    //fputs($logFile, "Line 29: sql: $sql\r\n");   

    if ($conn->query($sql) === TRUE)                                // run sql against DB
    {
        $count++;
    } else {
        echo "Error - could not update segments " . $singleRecord["segId"] . "\r\n";
    } 
}

echo "Finito --> $count segments updated\r\n";

fclose($logFile);                                                      // close log file
mysqli_close($conn);                                                   // close SQL connection 
?>