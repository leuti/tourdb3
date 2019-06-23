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

$loopSize = 5000;                                                   // Number of trkPts inserted in one go

// Open file to write log
$log = dirname(__FILE__) . "/../log/retriggerTrackCalc.log";        // Assign file location
$logFile = @fopen($log,"a");                               // open log file handler 
fputs($logFile, "\r\n============================================================\r\n");    
fputs($logFile, "retriggerTrackCalc.php started: " . date("Ymd-H:i:s", time()) . "\r\n");    

// Select tracks to be recalculated --> store trackId in array

$sql = "SELECT DISTINCT tptTrackFID FROM tbl_trackpoints ";
$sql .= "INNER JOIN tbl_tracks ON tbl_tracks.trkId = tbl_trackpoints.tptTrackFID ";
$sql .= "WHERE ( tbl_tracks.trkMeterUp is null OR tbl_tracks.trkMeterUp = 0 ) ";
//$sql .= "AND tbl_tracks.trkId < 5";

fputs($logFile, "Line 34: sql: $sql \r\n");  

$trackIdArray = array();
$result = mysqli_query($conn, $sql);

while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
    array_push($trackIdArray,  $row[0]);  
}

mysqli_free_result($result);

// for each trackId in array --> trigger calculation of meters & time
foreach ($trackIdArray as $trackId)
{
    // define variables
    $firstInLoop = 1;                                               // flag first record within loop 
    $firstTrackPoint = 1;                                           // flag first record within loop 
    $overallDistance = (float) 0;
    $distance = (float) 0;

    echo "Proessing TrackID: $trackId ...\r\n";

    $sql =  "SELECT `tptId`, `tptNumber`, `tptTrackFID`,`tptLon`,`tptLat`,`tptEle`,`tptTime` ";
    $sql .= " FROM `tbl_trackpoints` WHERE `tptTrackFID` = $trackId";

    fputs($logFile, "Line 59: sql: $sql \r\n");  

    $result = mysqli_query($conn, $sql);

    while ($trkpt = mysqli_fetch_array($result, MYSQLI_ASSOC)) 
    {
        // read content of file
        $lat = $trkpt["tptLat"];
        $lon = $trkpt["tptLon"];
        if ( $trkpt["tptEle"] == "" ) {
            $ele = 0;
        } else {
            $ele = $trkpt["tptEle"];
        }
        $ele = $ele * 1;
        $time = strftime("%Y-%m-%d %H:%M:%S", strtotime($trkpt["tptTime"]));

        if ($firstInLoop == 1)  {                                   // if record is not first, a comma is written

            // set sql string
            //$sql = $sqlBase;                                        // Add first part of sql to variable $sql
            $firstInLoop = 0;
            
            if ( $firstTrackPoint == 1 ) {
                // initialise variables
                $startTime = $time;
                $startEle = $ele;
                $peakTime = $time;
                $peakEle = $ele;
                $lowEle = $ele;
                $lowTime = $time;
                $meterUp = 0;
                $meterDown = 0;
                $distance = 0;
                $firstTrackPoint = 0;
            }
        } else {

            // if not first record: set comma as separator
            $sql .= ",";
            
            // calc gain values
            $eleGain = $ele - $previousEle;
            $eleGainVsPeak = $ele - $peakEle;
            
            // calc distance to previous waypoint
            $distance = haversineGreatCircleDistance(
                floatval($previousLat), floatval($previousLon), floatval($lat), floatval($lon), 6371000);
            $overallDistance = $overallDistance + $distance;
            
            // calculate different variable
            if ( $eleGain > 0 ) {                                   // elevation gained
                if ( $eleGainVsPeak > 0 ) {
                    $peakTime = $time;
                    $peakEle = $ele;
                    $meterUp = $meterUp + $eleGain; 
                } else {
                    $meterUp = $meterUp + $eleGain;
                }
            } else {
                if ( $eleGainVsPeak < 0 ) {
                    $lowTime = $time;
                    $lowEle = $ele;
                    $meterDown = $meterDown + $eleGain;
                } else {
                    $meterDown = $meterDown + $eleGain;
                }
            }
        }
        
        if ( $debugLevel>8 ) {
            fputs($logFile,"Line 130>tpNr:$tptNumber|ele:$ele|peakEle:$peakEle|lowEle:$lowEle|mU:$meterUp|mD|$meterDown|dist|$distance\r\n");
        }
        
        $previousEle = $ele;
        $previousLat = $lat;
        $previousLon = $lon;
    }
    // set elevation and time for last point
    $trkFinishEle = $ele;
    $trkFinishTime = $time;

    // calculate times 
    $datetime1 = new DateTime($peakTime);                           //start time
    $datetime2 = new DateTime($startTime);                          //end time
    $interval = $datetime1->diff($datetime2);
    $timeToPeak = $interval->format('%H:%i:%s');

    $datetime1 = new DateTime($time);                               //start time
    $datetime2 = new DateTime($startTime);                          //end time
    $interval = $datetime1->diff($datetime2);
    $overallTime = $interval->format('%H:%i:%s');
    
    $datetime1 = new DateTime($time);                               //start time
    $datetime2 = new DateTime($peakTime);                           //end time
    $interval = $datetime1->diff($datetime2);
    $timeToFinish = $interval->format('%H:%i:%s');

    $updSql = "UPDATE `tbl_tracks` SET ";                              // Insert Source file name, gps start time and toReview flag
    $updSql .= "`trkStartEle` = '$startEle',";
    $updSql .= "`trkPeakEle` = '$peakEle',";
    $updSql .= "`trkPeakTime` = '$peakTime',";
    $updSql .= "`trkLowEle` = '$lowEle',";
    $updSql .= "`trkLowTime` = '$lowTime',";
    $updSql .= "`trkFinishEle` = '$trkFinishEle',";
    $updSql .= "`trkFinishTime` = '$trkFinishTime',";
    $updSql .= "`trkTimeToPeak` = '$timeToPeak',";
    $updSql .= "`trkTimeToFinish` = '$timeToFinish',";
    $updSql .= "`trkTimeOverall` = '$overallTime',";
    $updSql .= "`trkMeterDown` = '$meterDown',";
    $updSql .= "`trkMeterUp` = '$meterUp',";
    $updSql .= "`trkDistance` = 'round($overallDistance/1000, 2)' ";
    $updSql .= "WHERE `trkId` = $trackId";

    fputs($logFile, "Line 172- sql: $updSql\r\n");    

    // run SQL and handle error
    if ($conn->query($updSql) === TRUE)                                // run sql against DB
    {
        if ( $debugLevel > 3) fputs($logFile, "Line 178 - Track successfully updated\r\n");
    } else {
        fputs($logFile, "Line 180 - Error updating Track: $conn->error\r\n");
    } 

    mysqli_free_result($result);
}

fputs($logFile, "retriggerTrackCalc.php finished: " . date("Ymd-H:i:s", time()) . "\r\n");    

// Close all files and connections
if ( $debugLevel >= 1 ) fclose($logFile);                                   // close log file
//$result->close();                                                        // close SQL connection 
exit;

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

?>