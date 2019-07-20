<?php
// ---------------------------------------------------------------------------------------------
// PHP script exports different data from the DB

// Created: 21.2.2018 - Daniel Leutwyler
// ---------------------------------------------------------------------------------------------
// Action:
// 

// Set variables and parameters
include("./config.php");                                                    // include config file
date_default_timezone_set("Europe/Zurich");                                     // must be set when using time functions

// Open file to write log
$importGpxLog = dirname(__FILE__) . "/../log/exportData.log";                   // Assign file location
if ( $debugLevel >=1 ) $logFile = @fopen($importGpxLog,"a");                                           // open log file handler 
if ( $debugLevel >=1 ) fputs($logFile, "\r\n============================================================\r\n");    
if ( $debugLevel >=1 ) fputs($logFile, "exportData.php started: " . date("Ymd-H:i:s", time()) . "\r\n");    

// Evaluate request type
if(strcasecmp($_SERVER["REQUEST_METHOD"], "POST") != 0){                        // Make sure that it is a POST request
    throw new Exception("Request method must be POST!");
}

$contentType = isset($_SERVER["CONTENT_TYPE"]) ? substr($_SERVER["CONTENT_TYPE"],0,16) : "";     // Ensure content type is application/json
if(strcasecmp($contentType, "application/json") != 0){
    throw new Exception("Content type must be: application/json");
}

//Receive the RAW post data.
$content = trim(file_get_contents("php://input"));

//Attempt to decode the incoming RAW post data from JSON.
$receivedData = json_decode($content, true);

// read variables from JSON object
$request = $receivedData["request"];
$loginName = $receivedData["usrId"];

if ($debugLevel > 2) fputs($logFile, "Line " . __LINE__ . ": Request (JSON): $request\r\n");    

// create upload dir / file name
$outDir = "../export/" . $loginName . "/";                                      // Session id used to create unique directory
if (!is_dir ( $outDir )) {                                                      // Create directory with name = session id
    mkdir($outDir, 0777);
}
   
// Create SQL SELECT statement
$sql .= "SELECT trkId, trkTrackName, trkRoute, ";
$sql .= "trkDateBegin, trkGPSStartTime, trkType, trkSubType, trkOrg, trkOvernightLoc, ";
$sql .= "trkEvent, trkRemarks, trkDistance, trkTimeOverall, trkTimeToPeak, trkTimeToFinish, ";
$sql .= "trkStartEle, trkPeakEle, trkPeakTime, trkLowEle, trkLowTime, trkFinishEle, trkFinishTime, trkGrade, ";
$sql .= "trkMeterUp, trkMeterDown, trkCountry, trkUsrId";
$sql .= "FROM tbl_tracks ";

if ($debugLevel > 2) fputs($logFile, "Line " . __LINE__ . ": SQL: $sql\r\n");    

// Evaluate request type
switch ( $request ) {

    // Export Tracks v01 as JSON
    case "tracks01_JSON":

        $out = $outDir . "track.json";                                          // Assign file location
        $JSONoutFile = @fopen($out,"w");                                        // Open file
        $trackArray = array();                                                  // Array to store sql result set

        if ($result = $conn->query($sql)) {                                     // run sql query and assign results to variable $result
            while($row = $result->fetch_object()) {
                array_push($trackArray, $row);                                  // add each row from $result to array
            }
            if ($debugLevel >= 3) fputs($logFile, "Line " . __LINE__ . ": JSONoutFile: $out\r\n");
            fputs($JSONoutFile, json_encode($trackArray));                      // Encode content of trackArray into JSON and write to output file
            $returnObject = array (                                             // Fill return object with message
                "status"=>"OK",
                "message"=>"JSON file stored in $out"
            );
        } else {
            fputs($logFile, "Line " . __LINE__ . ": SQL failed\r\n");  
            $returnObject = array (                                             // Fill return object with message
                "status"=>"NOK",
                "message"=>"Failed to write JSON file to $out"
            );
        };
        fclose($JSONoutFile);                                                   // close JSON output file
        break;

    // Export Tracks v01 as CSV    
    case "tracks01_CSV":

        $out = $outDir . "track.csv";                                           // Assign file location
        $csvOutFile = @fopen($out,"w");                                         // Open file
        $header = "";                                                           // Initialise variables
        $data = "";

        if ($debugLevel >= 3) fputs($logFile, "Line " . __LINE__ . ": csvOutFile: $out\r\n");  

        // run query and evaluate number of result columns
        $result = mysqli_query ( $conn, $sql ) or die ( "Sql error : " . $conn->error );    // run query and store results in $results
        $fields = mysqli_num_fields ( $result );                                // Count number of result columns and assign to $fields

        // write header line
        for ( $i = 0; $i < $fields; $i++ ) {                                    // loop through each column
            $fieldinfo = mysqli_fetch_field_direct( $result , $i );             // mysqli_fetch_field_direct returns info about table column
            $header .= $fieldinfo->name . "\t";                                 // write the column name into the header
        }
        
        // write field content
        while( $row = mysqli_fetch_row( $result ) ) {                           // loop through each row
            $line = "";
            foreach( $row as $value ) {                                         // lopp through each field
                if ( ( !isset( $value ) ) || ( $value == "" ) ) {               // if field is not empty
                    $value = "\t";                                              // write content of empty field and a field separator
                }
                else {
                    $value = str_replace( '"' , '""' , $value );                // mask character "
                    $value = '"' . $value . '"' . "\t";                         // Put field content into "" and add field separator
                }
                $line .= $value;                                                // add field to line
            }
            $data .= trim( $line ) . "\n";                                      // remove white spaces at beginning and end
        }
        $data = str_replace( "\r" , "" , $data );                               // remove RETURN characters
        
        if ( $data == "" ) {
            $data = "\n(0) Records Found!\n";                                   // Evaluate if records were found
        }
        
        fputs($csvOutFile, "$header\n$data");                                   // Write header and data to CSV file handler
        $returnObject = array (                                                 // Fill return object with message
            "status"=>"OK",
            "message"=>"CSV file stored in $out"
        );
        break;    
    }
    
    // Close all files and connections
    if ( $debugLevel >= 1 ) fclose($logFile);                                   // close log file

    $result->close();                                                           // close SQL connection 
    echo json_encode($returnObject);                                            // echo return object to client

    if ( $debugLevel >=1 ) {
        fputs($logFile, "exportData.php finished: " . date("Ymd-H:i:s", time()) . "\r\n");    
        fclose($logFile);                                   // close log file
    }
?>