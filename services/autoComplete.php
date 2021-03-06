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

header("content-type: application/json; charset=utf-8");
include("tourdb_config.php");                                              //include config file
date_default_timezone_set("Europe/Zurich");

if ($debugLevel >= 1){
    $fp = @fopen("../log/autoComplete.log","a");
    fputs($fp, "=================================================================\r\n");
    fputs($fp, date("Ymd-H:i:s", time()) . "- 8: autoComplete.php opened \r\n"); 
};

if ($debugLevel >= 3){
    fputs($fp, "Line " . __LINE__ . ": field: " . $_GET["field"] . "\r\n");
    fputs($fp, "Line " . __LINE__ . ": term: " . $_GET["term"] . "\r\n");
};

$searchObject = $_GET["field"];
$term = $_GET["term"];

if ( $searchObject == "peak" ) {
    $sql = "SELECT waypID, waypNameLong FROM tbl_waypoints ";
    $sql .= "WHERE waypNameLong LIKE '%" . $_GET["term"] . "%' ";
    $sql .= "AND waypTypeFid = 37 ORDER BY waypNameLong";
} else if ( $searchObject == "wayp" ) {
    $sql = "SELECT waypID, waypNameLong FROM tbl_waypoints ";
    $sql .= "WHERE waypNameLong LIKE '%" . $_GET["term"] . "%' ";
    $sql .= "AND waypTypeFid in (33,34,35) ORDER BY waypNameLong";
} else if ( $searchObject == "loca" ) {
    $sql = "SELECT waypID, waypNameLong FROM tbl_waypoints ";
    $sql .= "WHERE waypNameLong LIKE '%" . $_GET["term"] . "%' ";
    $sql .= "AND waypTypeFid = 36 ORDER BY waypNameLong";
} else if ( $searchObject == "part" ) {
    $sql = "SELECT prtId, CONCAT(prtFirstName, ' ', prtLastName) AS participant FROM tbl_part ";
    $sql .= "WHERE prtLastName LIKE '%" . $_GET["term"] . "%' ";
    $sql .= "OR prtFirstName LIKE '%" . $_GET["term"] . "%' ";   
} else if ( $searchObject == "grades" ) {
    $sql = "SELECT grdCodeID, grdCodeID FROM tbl_grades ";
    $sql .= "WHERE grdCodeID LIKE '%" . $_GET["term"] . "%' "; 
    $sql .= "ORDER BY grdCodeID";
}

if ($debugLevel >= 3){
    fputs($fp, "Line " . __LINE__ . ": sql: " . $sql . "\r\n");
};

$results = $conn->prepare($sql);
$results->execute();                                                    // Execute prepared Query
$results->bind_result($ID, $Short);                                     // Bind variables to prepared statement

$json = array();
while($results->fetch()) {
    $res = array(
        "id" => $ID,
        "value" => $Short,
    );
    array_push($json, $res);
}

$jsonstring = json_encode($json);

if ($debugLevel >= 3){
    fputs($fp, "Line " . __LINE__ . ": jsonstring: " . $jsonstring . "\r\n");
};

echo $jsonstring;

die();

?>

