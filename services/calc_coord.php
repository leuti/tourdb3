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
include("./config.inc.php");                                        // include config file
date_default_timezone_set('Europe/Zurich');                         // must be set when using time functions

$debugLevel = 3;                                                    // 0 = off, 6 = all

// Open file to write log
$importGpxLog = dirname(__FILE__) . "/../log/calc_coord.log";        // Assign file location
$logFile = @fopen($importGpxLog,"a");                               // open log file handler 
fputs($logFile, "\r\n============================================================\r\n");    
fputs($logFile, "calc_coord.php started: " . date("Ymd-H:i:s", time()) . "\r\n");    

// Select tracks from db
$sql = "SELECT trkId, trkCoordinates ";
$sql .= "FROM tbl_tracks ";
$sql .= "WHERE trkId in ('2') AND trkCoordinates <> '' ";

fputs($logFile, "Line 29: sql: $sql\r\n");    
// loop through each track
$records = mysqli_query($conn, $sql);

// write field content
//while( $row = mysqli_fetch_row( $result ) ) {                           // loop through each row

while($singleRecord = mysqli_fetch_assoc($records)) {    
    $coordArray = explode ( " ", $singleRecord["trkCoordinates"]);
    $firstRecord = true;
    $count = 0;

    $i=0;
    for ($i; $i<sizeof($coordArray); $i++) {                            // 10 is the number of existing subtypes in array (lines)
        fputs($logFile, "Line 43: Firstrecord: $firstRecord\r\n"); 

        $line = explode ( ",", $coordArray[$i]);
        if ( $firstRecord == true ) { 
            $WGS_top_lat = $line[1];
            $WGS_top_lon = $line[0];
            $WGS_left_lat = $line[1];
            $WGS_left_lon = $line[0];
            $WGS_right_lat = $line[1];
            $WGS_right_lon = $line[0];
            $WGS_bottom_lat = $line[1];
            $WGS_bottom_lon = $line[0];
        }
        
        fputs($logFile, "Line 54 - INPUT --> lat: $line[1] | lon: $line[0] | count: $count\r\n"); 

        if ( $firstRecord == false )  {
            if ( $line[1] > $WGS_top_lat ) {                                     // This is the top most point
                $WGS_top_lat = $line[1];
                $WGS_top_lon = $line[0];
                fputs($logFile, "Line 59 - Option 1 \r\n"); 
            } else if ( $line[1] < $WGS_bottom_lat ) {                          // This is the bottom most point
                $WGS_bottom_lat = $line[1];
                $WGS_bottom_lon = $line[0];
                fputs($logFile, "Line 63 - Option 2\r\n"); 
            }           
            if ( $line[0] > $WGS_right_lon ) {                                  // This is the right most point
                $WGS_right_lat = $line[1];                               
                $WGS_right_lon = $line[0];                               
                fputs($logFile, "Line 69 - Option 3\r\n"); 
            } else if ( $line[0] < $WGS_left_lon ) {                          // This is the left most point
                $WGS_left_lat = $line[1];                               
                $WGS_left_lon = $line[0];                               
                fputs($logFile, "Line 72 - Option 4\r\n"); 
            }
        }
        $firstRecord = false ;
        $count++;

        $coordTop = WGStoCHy($WGS_top_lat, $WGS_top_lon);                                               // variables to define min/max lon/lat to diplay track in center of map, focused
        $coordBottom = WGStoCHy($WGS_bottom_lat, $WGS_bottom_lon);
        $coortLeft = WGStoCHx($WGS_left_lat, $WGS_left_lon);
        $coordRight = WGStoCHx($WGS_right_lat, $WGS_right_lon);
                
        fputs($logFile, "Line 78: trkCoordTop: " . round($CH03_top_Y, 0) . "\r\n");
        fputs($logFile, "Line 79: trkCoordBottom: " . round($CH03_bottom_Y, 0) . "\r\n");
        fputs($logFile, "Line 80: trkCoordLeft: " . round($CH03_left_X, 0) . "\r\n");
        fputs($logFile, "Line 81: trkCoordRight: ". round($CH03_right_X, 0) . "\r\n");
        echo "Finito";

        //create SQL statement  
        $sql = "INSERT INTO tbl_tracks (trkCoordTop, trkCoordBottom, trkCoordLeft, trkCoordRight ) VALUES ";
        $sql .= "$coordBottom, $coordBottom, $coordLeft, $coordRight ";
        $sql .= 'WHERE trkId = $singleRecord["trkCoordinates"]';
            $conn->query($sql);
    }
}


fclose($logFile);                                                      // close log file
mysqli_close($conn);                                                   // close SQL connection 

function WGStoCHy($lat, $long) {

    //fputs($GLOBALS['logFile'], "Line 534 - lat: $lat | long: $long \r\n"); 

    // Converts decimal degrees sexagesimal seconds
    $lat = DECtoSEX($lat);
    $long = DECtoSEX($long);
    
    // Auxiliary values (% Bern)
    $lat_aux = ($lat - 169028.66)/10000;
    $long_aux = ($long - 26782.5)/10000;
    
    // Process Y
    $y = 600072.37 
       + 211455.93 * $long_aux 
       -  10938.51 * $long_aux * $lat_aux
       -      0.36 * $long_aux * pow($lat_aux,2)
       -     44.54 * pow($long_aux,3);
       
    return $y;
  }
  
  // Convert WGS lat/long (° dec) to CH x
  function WGStoCHx($lat, $long) {

    // fputs($GLOBALS['logFile'], "Line 557 - lat: $lat | long: $long \r\n"); 

    // Converts decimal degrees sexagesimal seconds
    $lat = DECtoSEX($lat);
    $long = DECtoSEX($long);
    // Auxiliary values (% Bern)
    $lat_aux = ($lat - 169028.66)/10000;
    $long_aux = ($long - 26782.5)/10000;
    
    // Process X
    $x = 200147.07
       + 308807.95 * $lat_aux 
       +   3745.25 * pow($long_aux,2)
       +     76.63 * pow($lat_aux,2)
       -    194.56 * pow($long_aux,2) * $lat_aux
       +    119.79 * pow($lat_aux,3);
         
    return $x;
  }

// Convert DEC angle to SEX DMS
function DECtoSEX($angle) {
    
    // fputs($GLOBALS['logFile'], "Line 580 - angle: $angle \r\n"); 
    
    // Extract DMS
    $deg = intval( $angle );
    $min = intval( ($angle-$deg)*60 );
    $sec =  ((($angle-$deg)*60)-$min)*60;   
    // Result in sexagesimal seconds
    return $sec + $min*60 + $deg*3600;
  }

?>