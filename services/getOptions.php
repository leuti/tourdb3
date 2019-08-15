<?php

// ---------------------------------------------------------------------------------------------
// This php script is called to generate options tag 
//
// Parameters:
// type_purpose: purpose of type (trk, seg, wayp)
// type_type:    Type of type (type, subtype)
// type_parent:  Partent ID if subtype
// ele_id:       element id
// ele_class:    element class
// ele_label:    element label
//
// Function:
// Retrieved appropriate entries in requested table and generates code HTML options tag
// 
// Created: 15.8.2019 - Daniel Leutwyler
// ---------------------------------------------------------------------------------------------
// Action:
// 
// 

// Set timezone (otherwise warnings are written to log)
date_default_timezone_set("Europe/Zurich");
include("tourdb_config.php");                                       // Include config file

if ($debugLevel >= 1){
    $logFileLoc = dirname(__FILE__) . "/../log/getOptions.log";     // Assign file location
    $logFile = @fopen($logFileLoc,"a");     
    fputs($logFile, "=================================================================\r\n");
    fputs($logFile, date("Ymd-H:i:s", time()) . " getOptions.php opened \r\n"); 
};

$type_purpose = $_POST["type_purpose"];                             // element purpose 
$type_type = $_POST["type_type"];                                   // Type or subtype
$type_parent = $_POST["type_parent"];                               // Parent ID if subtype
$ele_id = $_POST["ele_id"];                                         // element id
$ele_class = $_POST["ele_class"];                                   // element class
$ele_label = $_POST["ele_label"];                                   // element label

// continue only if $_POST is set and it is a Ajax request
if ($debugLevel >= 3){
    fputs($logFile, "Line " . __LINE__ . ": type_type    :  ". $type_type . "\r\n");
    fputs($logFile, "Line " . __LINE__ . ": ele_id       :  ". $ele_id . "\r\n");
    fputs($logFile, "Line " . __LINE__ . ": ele_class    :  ". $ele_class . "\r\n");
};

// Type is requested
if ( $type_type == "type" ) {

    $sql = 'SELECT DISTINCT typID, typName FROM tbl_types ';
    $sql .= 'WHERE typPurpose ="' . $type_purpose . '" ';
    $sql .= 'AND typType = "' . $type_type . '" ';
    $sql .= 'ORDER BY typName';

// Subtype is requested
} else if ( $type_type == "subtype" ) {
    $sql = 'SELECT DISTINCT typID, typName FROM tbl_types ';
    $sql .= 'WHERE typPurpose ="' . $type_purpose . '" ';
    $sql .= 'AND typType = "' . $type_type . '" ';
    //$sql .= 'AND typParentId = "' . $type_parent . '" ';          // Parent ID should delivered dynamically when type is updated
    $sql .= 'ORDER BY typName';
}

if ($debugLevel >= 1) fputs($logFile, "Line " . __LINE__ . ": sql for $type_type: " . $sql . "\r\n");

$records = mysqli_query($conn, $sql);

echo '<label for="' . $ele_id . '" class="' . $ele_class . '">' . $ele_label . '</label>';
echo '<select id="' . $ele_id . '" style="display: none;">';
//echo '<select name="trkTypeFid" id="uiTrack_fld_trkTypeFid">';
echo '<option value="0" selected="selected">select ' . $ele_label . '</option>';

// Write for each waypoint one Line 
while($singleRecord = mysqli_fetch_assoc($records)) {
    echo '<option value="' . $singleRecord["typID"] . '">' . $singleRecord["typName"] . '</option>';
}

// Write remaining HTML code
echo '</select>';

if ($debugLevel >= 1) fclose($logFile);
?>