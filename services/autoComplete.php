<?php

// ---------------------------------------------------------------------------------------------
// This php script is called to search and return the values when the user types letters
// into the autocomplete fields
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
//$idField = $sqlStmts[$searchObject]["id"];
//$nameField = $sqlStmts[$searchObject]["name"];
//$tableField = $sqlStmts[$searchObject]["table"];

// ================= Generic

//fputs($fp, 'Line 41: array: ' . $idField . "-" . $nameField . "-" . $tableField . "-" . $term ."\r\n");

if ( $searchObject == "peak") {
    $sql = "SELECT waypID, waypNameShort FROM tbl_waypoints ";
    $sql .= "WHERE waypNameShort LIKE '%" . $_GET["term"] . "%' ";
    $sql .= "AND waypTypeFID = 5 ORDER BY waypNameShort";
} else if ( $searchObject == "wayp") {
    $sql = "SELECT waypID, waypNameShort FROM tbl_waypoints ";
    $sql .= "WHERE waypNameShort LIKE '%" . $_GET["term"] . "%' ";
    $sql .= "AND waypTypeFID in (1,2,3) ORDER BY waypNameShort";
} else if ( $searchObject == "loca") {
    $sql = "SELECT waypID, waypNameShort FROM tbl_waypoints ";
    $sql .= "WHERE waypNameShort LIKE '%" . $_GET["term"] . "%' ";
    $sql .= "AND waypTypeFID = 4 ORDER BY waypNameShort";
} else if ( $searchObject == "part") {
    $sql = "SELECT prtId, CONCAT(prtFirstName, ' ', prtLastName) AS participant FROM tbl_part ";
    $sql .= "WHERE prtLastName LIKE '%" . $_GET["term"] . "%' ";
    $sql .= "OR prtFirstName LIKE '%" . $_GET["term"] . "%' ";   
} 

fputs($fp, "Line 54: sql: $sql\r\n");

$results = $conn->prepare($sql);
$results->execute(); //Execute prepared Query
$results->bind_result($ID, $Short); //bind variables to prepared statement

$json = array();
while($results->fetch()) {
    $res = array(
        'id' => $ID,
        'value' => $Short,
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

