<?php
// ---------------------------------------------------------------------------------------------
// This script generates kml files for waypoints (peaks, locations (huts), waypoints)
// It evaluates if peaks, huts have been reached (so, wi and so&wi) and displays the corresponding 
// icon in a predefined colour 
// 4000er is CH are especially highlighted
//
// INPUT
// 
// OUTPUT
// The script returns a JSON object with following content:
// ["message"] - only filled with an error message in case of an error
// ["status"]     - "OK" if no error has occured, "ERR" in case of an error
// kml file 

// Created: 15.03.2018 - Daniel Leutwyler
// ---------------------------------------------------------------------------------------------
//
// Tasks
// * 

date_default_timezone_set("Europe/Zurich");
include("config.php");                                                  // Include config file

$imgLoc = "./css/images/";
$recordCount = 0;

if ($debugLevel >= 1){
    $logFileLoc = dirname(__FILE__) . "/../log/gen_wayp.log";                // Assign file location
    $logFile = @fopen($logFileLoc,"a");     
    if ( $debugLevel >= 1 ) fputs($logFile, "=================================================================\r\n");
    if ( $debugLevel >= 1 ) fputs($logFile, date("Ymd-H:i:s", time()) . "-Line " . __LINE__ . ": gen_wayp.php opened \r\n"); 
};
    
// variables passed on by client (as JSON object)
$receivedData = json_decode ( file_get_contents("php://input"), true );
$sessionId = $receivedData["sessionId"];                                    
$sqlWhere = $receivedData["sqlWhere"];                          // where statement to select tracks to be displayed
$objectName = $receivedData["objectName"];
$usrId = $receivedData["usrId"];

if ($debugLevel >= 3){
    fputs($logFile, "<$objectName> Line " . __LINE__ . ": Received parameters:\r\n");
    fputs($logFile, "<$objectName> sessionId:       $sessionId\r\n");
    fputs($logFile, "<$objectName> sqlWhere:        $sqlWhere\r\n");
    fputs($logFile, "<$objectName> objectName:      $objectName\r\n");
};

// create upload dir / file name
$kml_dir = "../tmp/kml_disp/" . $sessionId . "/";                           // Session id used to create unique directory
if (!is_dir ( $kml_dir )) {                                                 // Create directory with name = session id
    mkdir($kml_dir, 0777);
}

// Select waypoints for output
$sql = "SELECT tbl_waypoints.waypID, tbl_waypoints.waypNameLong, tbl_waypoints.waypTypeFID, tbl_waypoints.waypAltitude, tbl_waypoints.waypCountry, tbl_waypoints.waypCoordWGS84E, tbl_waypoints.waypCoordWGS84N, sum(s1.saison) as saisonkey ";
$sql .= "FROM ( SELECT tbl_track_wayp.trwpWaypID, tbl_tracks.trkId, ";
$sql .= "SUM(CASE tbl_tracks.trkSubtypeFid WHEN 'Alpinklettern' THEN 1000 WHEN 'Alpintour' THEN 1000 WHEN 'Hochtour' THEN 1000 WHEN 'Joggen' THEN 1000 WHEN 'Mehrseilklettern' THEN 1000 WHEN 'Radfahren' THEN 1000 WHEN 'Sportklettern' THEN 1000 WHEN 'Velotour' THEN 1000 WHEN 'Wanderung' THEN 1000 WHEN 'Schwimmen' THEN 1000 WHEN 'Rennrad' THEN 1000 ";
$sql .= "WHEN 'Schneeschuhwanderung' THEN 1 WHEN 'Skihochtour' THEN 1 WHEN 'Skitour' THEN 1 WHEN 'Winterwanderung' THEN 1 WHEN 'Alpinski' THEN 1 ELSE 0 END) as 'saison' ";
$sql .= "FROM tbl_tracks ";
$sql .= "JOIN tbl_track_wayp on tbl_tracks.trkId = tbl_track_wayp.trwpTrkId ";
$sql .= "WHERE tbl_track_wayp.trwpReached_f = 1 ";
$sql .= "AND trkUsrId= '$usrId' "; 
$sql .= "GROUP BY tbl_track_wayp.trwpWaypID, tbl_tracks.trkId";
$sql .= ") AS s1 ";
$sql .= "RIGHT JOIN tbl_waypoints ON s1.trwpWaypID = tbl_waypoints.waypID ";
$sql .= $sqlWhere;
$sql .= " AND ( tbl_waypoints.waypCoordWGS84E is not null OR tbl_waypoints.waypCoordWGS84N is not null ) ";
$sql .= "GROUP BY waypID, waypNameLong, waypTypeFID, waypAltitude, waypCoordWGS84E, waypCoordWGS84N, s1.trwpWaypID ";
//$sql .= "LIMIT 70 ";

if ($debugLevel >= 3){
    fputs($logFile, date("Ymd-H:i:s", time()) . "-Line " . __LINE__ . ": sql for waypoints: " . $sql ."\r\n");
};

$records = mysqli_query($conn, $sql);

$waypKmlFileURL = $kml_dir . $objectName . ".kml";
$waypOutFile = fopen($waypKmlFileURL, "w");     

//Write document header
/*$kml[] = "<?xml version="1.0" encoding="UTF-8"?>";*/
$kml[] = '<kml xmlns="http://www.opengis.net/kml/2.2" xmlns:gx="http://www.google.com/kml/ext/2.2" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" '; 
$kml[] = 'xsi:schemaLocation="http://www.opengis.net/kml/2.2 https://developers.google.com/kml/schema/kml22gx.xsd">';
$kml[] = "<Document>";
$kml[] = "<name>Waypoints</name>";
//$kml[] = "<open>1</open>";

while($singleRecord = mysqli_fetch_assoc($records)){ // loop through each waypoint with coordinates

    $saisonkey = $singleRecord["saisonkey"];            // set to value from DB

    if ( is_null($saisonkey) ) {                            // saisonkey --> tausender = Anzahl Besuche Sommer / einer = Anzahl Besuche Winter
        $saisonkey = 0;                                     // set to 0 when NULL
        if ( $debugLevel >= 3 )  fputs($logFile, "is_null\r\n");
    } 
    $sommer = floor(intval($saisonkey) / 1000);                    // devide saison key by 1000 and extract number before comma
    $winter = $saisonkey - $sommer * 1000;                  // 
    
    $recordCount++;
    
    // wayp reached in sommer and winter
    if ( $sommer > 0 && $winter > 0 ) {
        $imgFile = $objectName . "-sowi.png";                // _sowi_16.png
    
    // wayp reached only in sommer
    } else if ( $sommer > 0 && $winter == 0 ) {
        $imgFile = $objectName . "-so.png";                  // _so_16.png

    // wayp reached only in winter
    } else if ( $sommer == 0 && $winter > 0 ) {
        $imgFile = $objectName . "-wi.png";                  // _wi_16.png
    } else {
        $imgFile = $objectName . "-none.png";
    }

    if ( $debugLevel >= 4 ) {
        fputs($logFile, "<$objectName> waypNameLong: ". $singleRecord["waypNameLong"] . "-->");
        fputs($logFile, "saisonkey: $saisonkey || sommer: $sommer || winter: $winter || icon: $imgFile\r\n");
    }

    $kml[] = '<Placemark id="marker_' . $singleRecord["waypID"] .'">';
    $kml[] = "   <name>" . $singleRecord["waypNameLong"] . "</name>";
    $kml[] = "   <description>" . $singleRecord["waypID"] . ": ". $singleRecord["waypNameLong"] . " (" . $singleRecord["waypAltitude"] . "m)</description>";
    $kml[] = "   <Style>";
    $kml[] = "      <IconStyle>";
    $kml[] = "          <Icon>";
    $kml[] = "              <href>" . $imgLoc . $imgFile . "</href>";
    //$kml[] = "              <href>https://api3.geo.admin.ch/color/255,0,0/marker-24@2x.png</href>";
    $kml[] = "              <gx:w>48</gx:w>";
    $kml[] = "              <gx:h>48</gx:h>";   
    $kml[] = "          </Icon>";
    $kml[] = '          <hotSpot x="24" y="24" xunits="pixels" yunits="pixels"/>';
    $kml[] = "      </IconStyle>";
    $kml[] = "      <LabelStyle>";
    $kml[] = "          <color>ff0000ff</color>";
    $kml[] = "     	</LabelStyle>";
    $kml[] = "   </Style>";
    $kml[] = "   <Point>";
    $kml[] = "      <coordinates>" . $singleRecord["waypCoordWGS84E"] . "," . $singleRecord["waypCoordWGS84N"] . "," . $singleRecord["waypAltitude"] . "</coordinates>";
    $kml[] = "   </Point>";
    $kml[] = "</Placemark>";
};

$kml[] = "</Document>";
$kml[] = "</kml>";

// Merge kml array into one variable
$kmlOutput = join("\r\n", $kml);

// write kml output to file
fputs($waypOutFile, "$kmlOutput");                                       // Write kml to file
fclose($waypOutFile);

// Create return object
$returnObject["status"] = "OK";                                             // add status field (OK) to trackObj
$returnObject["message"] = "kml file generated with " . $recordCount . " " . $objectName;                            // add empty error message to trackObj
$returnObject["recordcount"] = $recordCount;
$returnObject["objectName"] = $objectName;
echo json_encode($returnObject);                                            // echo JSON object to client

if ( $debugLevel >= 1 ) fputs($logFile, "Line " . __LINE__ . ": $recordCount $objectName items inserted into KML filer\r\n");    
if ( $debugLevel >= 1 ) fputs($logFile, "gen_wayp.php finished: " . date("Ymd-H:i:s", time()) . "\r\n");    

// Close all files and connections
if ( $debugLevel >= 1 ) fclose($logFile);                                   // close log file
mysqli_close($conn);                                                        // close SQL connection 
exit;

?>