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
// ['message'] - only filled with an error message in case of an error
// ['status']     - 'OK' if no error has occured, 'ERR' in case of an error
// kml file 

// Created: 15.03.2018 - Daniel Leutwyler
// ---------------------------------------------------------------------------------------------
//
// Tasks
// * 


date_default_timezone_set('Europe/Zurich');
include("config.inc.php");                                                  // Include config file

$debugLevel = 3; // 0 = off, 1 = min, 3 = a lot, 5 = all
$imageLoc = "./css/images/";

if ($debugLevel >= 1){
    $logFileLoc = dirname(__FILE__) . "/../log/gen_wayp.log";                // Assign file location
    $logFile = @fopen($logFileLoc,"a");     
    if ( $debugLevel >= 1 ) fputs($logFile, "=================================================================\r\n");
    if ( $debugLevel >= 1 ) fputs($logFile, date("Ymd-H:i:s", time()) . "-Line 59: gen_wayp.php opened \r\n"); 
};
    
// variables passed on by client (as JSON object)
$receivedData = json_decode ( file_get_contents('php://input'), true );
$sessionid = $receivedData["sessionid"];                                    
$sqlWhere = $receivedData["sqlWhere"];                          // where statement to select tracks to be displayed
$objectName = $receivedData["objectName"];

if ($debugLevel >= 3){
    fputs($logFile, 'Line 72: Received parameters:' . "\r\n");
    fputs($logFile, 'sessionid:       ' . $sessionid . "\r\n");
    fputs($logFile, 'sqlWhere:        ' . $sqlWhere . "\r\n");
};

// create upload dir / file name
$kml_dir = '../tmp/kml_disp/' . $sessionid . '/';                           // Session id used to create unique directory
if (!is_dir ( $kml_dir )) {                                                 // Create directory with name = session id
    mkdir($kml_dir, 0777);
}

// Select waypoints for output
$sql = "SELECT tbl_waypoints.waypID";
$sql .= ", tbl_waypoints.waypNameLong";
$sql .= ", tbl_waypoints.waypTypeFID";
$sql .= ", tbl_waypoints.waypAltitude";
$sql .= ", tbl_waypoints.waypCountry";
$sql .= ", tbl_waypoints.waypCoordWGS84E";
$sql .= ", tbl_waypoints.waypCoordWGS84N";
$sql .= ", s1.trkLoginName";
$sql .= ", s1.trwpWaypID";
$sql .= ", sum(s1.saison) ";
$sql .= "FROM (";
$sql .= "SELECT tbl_track_wayp.trwpWaypID";
$sql .= ", tbl_tracks.trkId";
$sql .= ", tbl_tracks.trkLoginName";
$sql .= ", CASE tbl_tracks.trkSubType WHEN 'Alpinklettern' THEN 1000 WHEN 'Alpintour' THEN 1000 WHEN 'Hochtour' THEN 1000 WHEN 'Joggen' THEN 1000 WHEN 'Mehrseilklettern' THEN 1000 WHEN 'Sportklettern' THEN 1000 WHEN 'Velotour' THEN 1000 WHEN 'Wanderung' THEN 1000 WHEN 'Schneeschuhwanderung' THEN 1 WHEN 'Skihochtour' THEN 1 WHEN 'Skitour' THEN 1 WHEN 'Winterwanderung' THEN 1 ELSE 0 END ";
$sql .= " as 'saison' ";
$sql .= ", tbl_track_wayp.trwpReached_f ";
$sql .= "FROM tbl_tracks ";
$sql .= "JOIN tbl_track_wayp on tbl_tracks.trkId = tbl_track_wayp.trwpWaypId ";
//$sql .= "WHERE tbl_tracks.trkLoginName = '" . $loginname . "'";
$sql .= ") AS s1 ";
$sql .= "RIGHT JOIN tbl_waypoints ON s1.trwpWaypID = tbl_waypoints.waypID ";
$sql .= $sqlWhere; 
$sql .= " GROUP BY waypID, waypNameLong, waypTypeFID, waypAltitude, waypCoordWGS84E, waypCoordWGS84N, trkLoginName, s1.trwpWaypID ";
$sql .= "LIMIT 70";

if ($debugLevel >= 1){
    fputs($logFile, date("Ymd-H:i:s", time()) . "-Line 42: sql for waypoints: " . $sql ."\r\n");
};

$records = mysqli_query($conn, $sql);

$waypKmlFileURL = $kml_dir . $objectName . '.kml';
$waypOutFile = fopen($waypKmlFileURL, "w");     

//Write document header
/*$kml[] = '<?xml version="1.0" encoding="UTF-8"?>';*/
$kml[] = '<kml xmlns="http://www.opengis.net/kml/2.2" xmlns:gx="http://www.google.com/kml/ext/2.2" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" '; 
$kml[] = 'xsi:schemaLocation="http://www.opengis.net/kml/2.2 https://developers.google.com/kml/schema/kml22gx.xsd">';
$kml[] = '<Document>';
$kml[] = '<name>Waypoints</name>';
//$kml[] = '<open>1</open>';

while($singleRecord = mysqli_fetch_assoc($records)){ // loop through each waypoint with coordinates

    $kml[] = '<Placemark id="marker_' . $singleRecord["waypID"] .'">';
    $kml[] = '   <name>' . $singleRecord["waypNameLong"] . '</name>';
    $kml[] = '   <description>' . $singleRecord["trwpWaypID"] . ': '. $singleRecord["waypNameLong"] . ' (' . $singleRecord["waypAltitude"] . 'm)</description>';
    $kml[] = '   <Style>';
    $kml[] = '      <IconStyle>';
    $kml[] = '          <Icon>';
    $kml[] = '              <href>' . $imageLoc . $objectName . '_16.png</href>';
    //$kml[] = '              <href>https://api3.geo.admin.ch/color/255,0,0/marker-24@2x.png</href>';
    $kml[] = '              <gx:w>48</gx:w>';
    $kml[] = '              <gx:h>48</gx:h>';   
    $kml[] = '          </Icon>';
    $kml[] = '          <hotSpot x="24" y="24" xunits="pixels" yunits="pixels"/>';
    $kml[] = '      </IconStyle>';
    $kml[] = '      <LabelStyle>';
    $kml[] = '          <color>ff0000ff</color>';
    $kml[] = '     	</LabelStyle>';
    $kml[] = '   </Style>';
    $kml[] = '   <Point>';
    $kml[] = '      <coordinates>' . $singleRecord["waypCoordWGS84E"] . ',' . $singleRecord["waypCoordWGS84N"] . ',' . $singleRecord["waypAltitude"] . '</coordinates>';
    $kml[] = '   </Point>';
    $kml[] = '</Placemark>';
};

$kml[] = '</Document>';
$kml[] = '</kml>';

// Merge kml array into one variable
$kmlOutput = join("\r\n", $kml);

// write kml output to file
fputs($waypOutFile, "$kmlOutput");                                       // Write kml to file
fclose($waypOutFile);

/*
// evaluate how many tracks and segments were found and add this info to return message
if ( $countTracks && $countSegments ) {
    $returnMessage = "$countTracks Tracks and $countSegments Segments found"; 
} else if ( $countTracks ) {
    $returnMessage = "$countTracks Tracks found"; 
} else {
    $returnMessage = "$countSegments Segments found";
}
*/

// Create return object
$returnObject['status'] = 'OK';                                             // add status field (OK) to trackobj
$returnObject['message'] = 'kml file generated';                            // add empty error message to trackobj
echo json_encode($returnObject);                                            // echo JSON object to client

if ( $debugLevel >= 1 ) fputs($logFile, "gen_wayp.php finished: " . date("Ymd-H:i:s", time()) . "\r\n");    

// Close all files and connections
if ( $debugLevel >= 1 ) fclose($logFile);                                   // close log file
mysqli_close($conn);                                                        // close SQL connection 
exit;

?>