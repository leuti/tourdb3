<?php
// ---------------------------------------------------------------------------------------------
// PHP script exports different data from the DB

// Created: 21.2.2018 - Daniel Leutwyler
// ---------------------------------------------------------------------------------------------
// Action:
// 

// Set variables and parameters
include("./config.inc.php");                                        // include config file
date_default_timezone_set('Europe/Zurich');                         // must be set when using time functions

$debugLevel = 5;                                                    // 0 = off, 6 = all

// Open file to write log
$importGpxLog = dirname(__FILE__) . "/../log/exportData.log";        // Assign file location
$logFile = @fopen($importGpxLog,"a");                               // open log file handler 
fputs($logFile, "\r\n============================================================\r\n");    
fputs($logFile, "exportData.php started: " . date("Ymd-H:i:s", time()) . "\r\n");    

// Evaluate request type

//Make sure that it is a POST request.
if(strcasecmp($_SERVER['REQUEST_METHOD'], 'POST') != 0){
    throw new Exception('Request method must be POST!');
}

//Make sure that the content type of the POST request has been set to application/json
$contentType = isset($_SERVER["CONTENT_TYPE"]) ? substr($_SERVER["CONTENT_TYPE"],0,16) : '';

if(strcasecmp($contentType, 'application/json') != 0){
    throw new Exception('Content type must be: application/json');
}

//Receive the RAW post data.
$content = trim(file_get_contents("php://input"));

//Attempt to decode the incoming RAW post data from JSON.
$receivedData = json_decode($content, true);

$request = $receivedData["request"];
$sessionid = $receivedData["sessionid"];

if ($debugLevel > 2) fputs($logFile, "Line 43: Request (JSON): $request\r\n");    

// create upload dir / file name
$outDir = '../tmp/export/' . $sessionid . '/';                           // Session id used to create unique directory
if (!is_dir ( $outDir )) {                                                 // Create directory with name = session id
    mkdir($outDir, 0777);
}
   
$sql  = "SELECT trkId,trkLogbookId,trkSourceFileName,trkPeakRef,trkTrackName,trkRoute,";
$sql .= "trkDateBegin,trkDateFinish,trkGPSStartTime,trkSaison,trkType,trkSubType,trkOrg,";
$sql .= "trkOvernightLoc,trkParticipants,trkEvent,trkRemarks,trkDistance,trkTimeOverall,";
$sql .= "trkTimeToPeak,trkTimeToFinish,trkStartEle,trkPeakEle,trkPeakTime,trkLowEle,trkLowTime,";
$sql .= "trkFinishEle,trkFinishTime,trkGrade,trkMeterUp,trkMeterDown,trkCountry,trkLoginName,";
$sql .= "trkToReview FROM tbl_tracks WHERE trkId in (2,3)";

switch ( $request ) {

    // Export Tracks v01 as JSON
    case "tracks01_JSON":
        fputs($logFile, "Line 89 - export track JSON with SQL: $sql\r\n");  
        $trackArray = array();
        if ($result = $conn->query($sql)) {
            $tempArray = array();
            while($row = $result->fetch_object()) {
                    $tempArray = $row;
                    array_push($trackArray, $tempArray);
                }
            //$out = dirname(__FILE__) . "/../tmp/export/$sessionid/track.json";        // Assign file location
            $out = $outDir . "track.json";        // Assign file location
            $JSONoutFile = @fopen($out,"w");    
            if ($debugLevel >= 3) fputs($logFile, "Line 75: JSONoutFile: $out\r\n");
            fputs($JSONoutFile, json_encode($trackArray));  
        } else {
            fputs($logFile, "Line 78 - SQL failed\r\n");  
        };
        fclose($JSONoutFile);                                   // close log file
        break;

    // Export Tracks v01 as CSV    
    case "tracks01_CSV":
        fputs($logFile, "Line 89 - export track CSV with SQL: $sql\r\n");  
        $out = $outDir . "track.csv";        // Assign file location
        $csvOutFile = @fopen($out,"w");  
        if ($debugLevel >= 3) fputs($logFile, "Line 88: csvOutFile: $out\r\n");  

        // from internet
        $result = mysqli_query ( $conn, $sql ) or die ( "Sql error : " . $conn->error );
        
        $fields = mysqli_num_fields ( $result );
        
        $header = '';
        $data = '';

        for ( $i = 0; $i < $fields; $i++ )
        {
            $fieldinfo = mysqli_fetch_field_direct( $result , $i );
            $header .= $fieldinfo->name . ";";
        }
        
        while( $row = mysqli_fetch_row( $result ) )
        {
            $line = '';
            foreach( $row as $value )
            {                                            
                if ( ( !isset( $value ) ) || ( $value == "" ) )
                {
                    $value = ";";
                }
                else
                {
                    $value = str_replace( '"' , '""' , $value );
                    $value = '"' . $value . '"' . ";";
                }
                $line .= $value;
            }
            $data .= trim( $line ) . "\n";
        }
        $data = str_replace( "\r" , "" , $data );
        
        if ( $data == "" )
        {
            $data = "\n(0) Records Found!\n";                        
        }
        
        fputs($csvOutFile, "$header\n$data");
        break;    
    }

    fputs($logFile, "exportData.php finished: " . date("Ymd-H:i:s", time()) . "\r\n");    
    
    // Close all files and connections
    if ( $debugLevel >= 1 ) fclose($logFile);                                   // close log file
    
    $result->close();                                                        // close SQL connection 
    exit;
    
?>