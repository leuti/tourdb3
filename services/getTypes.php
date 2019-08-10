<?php

// ---------------------------------------------------------------------------------------------
// This php script is called to generate all UI elements dependent on types and subtypes
//
// Parameters:
// purpose: trk, wayp, seg --> which kind of type is requested
// type: type or subtype is requested
// parent: ID of partent ID if subtype is delivered
//
// Function:
// Retrieved appropriate types from tbl_types and generates code for <ol> elements for
// type / subtype selectable
// 
// Created: 2.8.2019 - Daniel Leutwyler
// ---------------------------------------------------------------------------------------------
// Action:
// 
// 

// Set timezone (otherwise warnings are written to log)
date_default_timezone_set("Europe/Zurich");
include("tourdb_config.php");                                                  // Include config file

if ($debugLevel >= 1){
    $logFileLoc = dirname(__FILE__) . "/../log/getTypes.log";                // Assign file location
    $logFile = @fopen($logFileLoc,"a");     
    fputs($logFile, "=================================================================\r\n");
    fputs($logFile, date("Ymd-H:i:s", time()) . " getTypes.php opened \r\n"); 
};

$purpose = $_POST["purpose"];                                       // trk, wayp, seg
$type = $_POST["type"];                                             // type or subtype
$parent = $_POST["parent"];                                         // it of parent

// continue only if $_POST is set and it is a Ajax request
if ($debugLevel >= 3){
    fputs($logFile, "Line " . __LINE__ . ": purpose :  ". $purpose . "\r\n");
    fputs($logFile, "Line " . __LINE__ . ": type    :  ". $type . "\r\n");
    fputs($logFile, "Line " . __LINE__ . ": parent  :  ". $parent . "\r\n");
};

// requested subject is tracks
if ( $purpose == "trk") {

    // Type is requested
    if ( $type == "type" ) {
        $where = "WHERE typParentId is null AND typPurpose = '". $purpose . "'"; 
    
    // Subtype is requested
    } else if ( $parent == "" ) {
        $where = "WHERE typPurpose = '". $purpose . "' AND typParentId is not null"; 
    } else {
        $where = "WHERE typParentId = " . $parent . " AND typPurpose = '". $purpose . "'"; 
    }

    $sql = "SELECT typId, typCode, typName, typParentId, typType, typPurpose FROM tbl_types ";
    $sql .= $where;
    $sql .= " ORDER BY typName";

    if ($debugLevel >= 1) fputs($logFile, "Line " . __LINE__ . ": sql for $purpose/$type: " . $sql . "\r\n");

    $records = mysqli_query($conn, $sql);

    if ( $type == "type" ) {
        echo '<label for="dispFilTrk_type" class="labelFirst">Type (CTRL+Left-click for multi-select)</label>';
        echo '<ol id="dispFilTrk_type" class="selectable filterItems">';    
    } else if ( $type == "subtype" ) {
        echo '<label for="dispFilTrk_subtype" class="labelFirst">Type (CTRL+Left-click for multi-select)</label>';
        echo '<ol id="dispFilTrk_subtype" class="selectable filterItems">';    
    }
    
    // Write for each waypoint one Line 
    while($singleRecord = mysqli_fetch_assoc($records)) {

        echo '<li id="dispFilTrk_' . $type . '_' . $singleRecord["typCode"] . ' class="ui-widget-content" value="' . 
            $singleRecord["typId"] . '">' . $singleRecord["typName"] . '</li>';
        $first = "";
    }

    // Write remaining HTML code
    echo '</ol>';
}
if ($debugLevel >= 1) fclose($logFile);
?>