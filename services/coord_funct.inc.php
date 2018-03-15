<?php
function haversineGreatCircleDistance(
$latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo, $earthRadius = 6371000)
/**
 * Calculates the great-circle distance between two points, with
 * the Haversine formula. Formula from internet
 * @param float $latitudeFrom Latitude of start point in [deg decimal]
 * @param float $longitudeFrom Longitude of start point in [deg decimal]
 * @param float $latitudeTo Latitude of target point in [deg decimal]
 * @param float $longitudeTo Longitude of target point in [deg decimal]
 * @param float $earthRadius Mean earth radius in [m]
 * @return float Distance between points in [m] (same as earthRadius)
 */
{
    $latFrom = deg2rad($latitudeFrom);
    $lonFrom = deg2rad($longitudeFrom);
    $latTo = deg2rad($latitudeTo);
    $lonTo = deg2rad($longitudeTo);

    $latDelta = $latTo - $latFrom;
    $lonDelta = $lonTo - $lonFrom;

    $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
    cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));

    return $angle * $earthRadius; 
}

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
       
    if ( $y > 840000 ) {
        return 840000;
    } else if ( $y < 110000 ) {
        return 110000;
    } else {
        return $y;
    }
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
         
    if ( $x > 297000 ) {
        return 297000;
    } else if ( $x < 74000 ) {
        return 74000;
    } else {
        return $x;
    }
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