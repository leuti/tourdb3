<?php

// ---------------------------------------------------------------------------------------------
// blabla
//
// Parameters:
// blabla
//
// Created: 3.2.2018 - Daniel Leutwyler
// ---------------------------------------------------------------------------------------------
// Action:
// * blabla
// 

header('content-type: application/json; charset=utf-8');
include("config.inc.php");  //include config file
date_default_timezone_set('Europe/Zurich');

$sqlStmts = array (
    "wayp"  => array("table" => "tbl_waypoints", "id" => "waypID", "name" => "waypNameShort"),
    "trk"  => array("table" => "tbl_tracks", "id" => "trkId", "name" => "trkTrackName"),
    "segTypeFID"  => array("table" => "tbl_segmenttypes", "id" => "stypCode", "name" => "stypName")
);

$sqlStmts["field1"] = "SELECT id, name FROM tbl_segmenttypes WHERE var1 LIKE '%" . $_GET["term"] . "%'";  

//DEBUG
$debugLevel = 5; // 0 = off, 1 = min, 3 = a lot, 5 = all 

if ($debugLevel >= 1){
    $fp = @fopen("../log/autoComplete.log","a"); // TASK: Add Date/Time
    fputs($fp, "=================================================================\r\n");
    fputs($fp, date("Ymd-H:i:s", time()) . "-Line 8: autoComplete.php opened \r\n"); 
};

if ($debugLevel >= 3){
    fputs($fp, 'Line 14: field: ' . $_GET["field"] . "\r\n");
    fputs($fp, 'Line 15: term: ' . $_GET["term"] . "\r\n");
};

$searchObject = $_GET["field"];
$term = $_GET["term"];
$idField = $sqlStmts[$searchObject]["id"];
$nameField = $sqlStmts[$searchObject]["name"];
$tableField = $sqlStmts[$searchObject]["table"];


// ================= Generic

fputs($fp, 'Line 41: array: ' . $idField . "-" . $nameField . "-" . $tableField . "-" . $term ."\r\n");


$whereClause = 
$sql = "SELECT $idField, $nameField FROM $tableField";
$sql .= " WHERE $nameField LIKE '%" . $_GET["term"] . "%'";

fputs($fp, "Line 54: sql: $sql\r\n");

$results = $conn->prepare($sql);
$results->execute(); //Execute prepared Query
$results->bind_result($ID, $Short); //bind variables to prepared statement

$json = array();
while($results->fetch()) {
    $res = array(
        'value' => $Short,
        'id' => $ID,
    );
    array_push($json, $res);
}

$jsonstring = json_encode($json);
if ($debugLevel >= 5){
    fputs($fp, 'Line 38: jsonstring: ' . $jsonstring . "\r\n");
};
echo $jsonstring;
die();

?>

