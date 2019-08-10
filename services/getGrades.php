<?php

// ---------------------------------------------------------------------------------------------
// This php script is called to generate all UI elements dependent on grades and ehaft (Ernsthaftigkeit)
//
// Parameters:
// ele: name of element concerned
// grdType: grade, climbGrade, ehaft (Ernsthaftigkeit)
//
// Function:
// Retrieved appropriate grades from tbl_types and generates code for <ol> elements for
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
    $logFileLoc = dirname(__FILE__) . "/../log/getGrades.log";                // Assign file location
    $logFile = @fopen($logFileLoc,"a");     
    fputs($logFile, "=================================================================\r\n");
    fputs($logFile, date("Ymd-H:i:s", time()) . " getGrades.php opened \r\n"); 
};

$ele = $_POST["ele"];                                               // element id
$grdType = $_POST["grdType"];                                           // grade type

// continue only if $_POST is set and it is a Ajax request
if ($debugLevel >= 3){
    fputs($logFile, "Line " . __LINE__ . ": ele     :  ". $ele . "\r\n");
    fputs($logFile, "Line " . __LINE__ . ": grdType  :  ". $grdType . "\r\n");
};

// Type is requested
if ( $grdType == "grade" ) {
    $where = "WHERE grdType = 'Schwierigkeitsgrad' "; 
    $grdLabel = "Schwierigkeitsgrad"; 

// Subtype is requested
} else if ( $grdType == "climbGrade" ) {
    $where = "WHERE grdType = 'Klettergrad' "; 
    $grdLabel = "Klettergrad"; 

} else {
    $where = "WHERE grdType = 'Ernsthaftigkeit' ";  
    $grdLabel = "Ernsthaftigkeit";  
}

$sql = "SELECT grdGroup, MIN(grdSort) AS sort FROM tbl_grades ";
$sql .= $where;
$sql .= "GROUP BY grdGroup ";
$sql .= "ORDER BY sort asc";

if ($debugLevel >= 1) fputs($logFile, "Line " . __LINE__ . ": sql for $grdType: " . $sql . "\r\n");

$records = mysqli_query($conn, $sql);

echo '<label for="' . $ele . '" class="labelFirst">' . $grdLabel . ' - (CTRL+Left-click for multi-select)</label>';
echo '<ol id="' . $ele . '_ol" class="selectable filterItems">';    

// Write for each waypoint one Line 
while($singleRecord = mysqli_fetch_assoc($records)) {
    echo '<li id="' . $grdType . '_' . $singleRecord["grdGroup"] .  '" class="ui-widget-content first">' . $singleRecord["grdGroup"] . '</li>';
    $first = "";
}

// Write remaining HTML code
echo '</ol>';

if ($debugLevel >= 1) fclose($logFile);
?>