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

if ($debugLevel >= 1){
    $fp = @fopen("log/gen_wayp_kml.log","a"); 
    fputs($fp, "\r\n====================================================================================\r\n");
    fputs($fp, date("Ymd-H:i:s", time()) . "-Line 9: wayp_gen_kml.php opened \r\n");
};

// variables passed on by client (as JSON object)
$receivedData = json_decode ( file_get_contents('php://input'), true );
$sessionid = $receivedData["sessionid"];                                    
$sqlWhere = $receivedData["sqlWhere"];                              // where statement to select waypoints to be displayed
$genWaypType = $recievedData["genWaypType"];                                // Type of data to be generated

if ($debugLevel >= 3){
    fputs($logFile, 'Line 72: Received parameters:' . "\r\n");
    fputs($logFile, 'sessionid:   ' . $sessionid . "\r\n");
    fputs($logFile, 'sqlWhere:    ' . $sqlWhere . "\r\n");
    fputs($logFile, 'genWaypType: ' . $genWaypType . "\r\n");
};

// create upload dir / file name
$kml_dir = '../tmp/kml_disp/' . $sessionid . '/';                           // Session id used to create unique directory
if (!is_dir ( $kml_dir )) {                                                 // Create directory with name = session id
    mkdir($kml_dir, 0777);
}

// Select waypoints for output
$sql = "SELECT tbl_waypoints.waypNameLong";
$sql .= "      , tbl_waypoints.waypAltitude";
$sql .= "      , tbl_waypoints.wtypCode";
$sql .= "      , tbl_waypoints.waypCoordWGS84E";
$sql .= "      , tbl_waypoints.waypCoordWGS84N";
$sql .= "      , s1.trkLoginName";
$sql .= "      , s1.trwpWaypID";
$sql .= "      , sum(s1.saison)";
$sql .= "FROM ";
$sql .= "(";
$sql .= "SELECT tbl_track_wayp.trwpWaypID";
$sql .= "    , tbl_tracks.trkLoginName";
$sql .= "    , CASE tbl_tracks.trkSubType";
$sql .= "        WHEN 'Alpinklettern' THEN 1000";
$sql .= "        WHEN 'Alpintour' THEN 1000";
$sql .= "        WHEN 'Hochtour' THEN 1000";
$sql .= "        WHEN 'Joggen' THEN 1000";
$sql .= "        WHEN 'Mehrseilklettern' THEN 1000";
$sql .= "        WHEN 'Sportklettern' THEN 1000";
$sql .= "        WHEN 'Velotour' THEN 1000";
$sql .= "        WHEN 'Wanderung' THEN 1000";
$sql .= "        WHEN 'Schneeschuhwanderung' THEN 1";
$sql .= "        WHEN 'Skihochtour' THEN 1";
$sql .= "        WHEN 'Skitour' THEN 1";
$sql .= "        WHEN 'Winterwanderung' THEN 1";
$sql .= "        ELSE 0";
$sql .= "    END as 'saison'";
$sql .= "FROM tbl_tracks";
$sql .= "RIGHT JOIN tbl_track_wayp on tbl_tracks.trkId = tbl_track_wayp.trwpTrkId";
$sql .= "WHERE tbl_track_wayp.trwpWaypID IS NOT Null";
$sql .= "    AND tbl_track_wayp.trwpReached_f = 1";
$sql .= ") AS s1";
$sql .= "LEFT JOIN tbl_waypoints ON tbl_waypoints.waypID = s1.trwpWaypID";
$sql .= "GROUP BY waypNameLong, waypAltitude, wtypCode, waypCoordWGS84E, waypCoordWGS84N, trkLoginName, trwpWaypID";

/*
    $sql  = "SELECT `waypID`, `waypNameShort`, `waypNameLong`, ";
    $sql .= "`wtypCode`, `wtypNameShort`, `waypCountry`, `waypCanton`, ";
    $sql .= "`areaId`, `areaNameShort`, `areaNameLong`, `regId`,";
    $sql .= "`regNameShort`,`regNameLong`,`waypAltitude`, `waypCoordWGS84E`,";
    $sql .= " `waypCoordWGS84N`, `waypOwner`, `waypWebsite`, `waypUIAA4000` ";
    $sql .= " FROM vw_waypoints";
    $sql .= $sqlFilterString;  
    $sql .= " ORDER BY wtypNameShort ";
*/    
    if ($debugLevel >= 1){
        fputs($fp, date("Ymd-H:i:s", time()) . "-Line 42: sql for waypoints: " . $sql ."\r\n");
    };

    $waypoints = mysqli_query($conn, $sql);

    //Write document header
    /*$kml[] = '<?xml version="1.0" encoding="UTF-8"?>';*/
    $kml[] = '<kml xmlns="http://www.opengis.net/kml/2.2" xmlns:gx="http://www.google.com/kml/ext/2.2" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" '; 
    $kml[] = 'xsi:schemaLocation="http://www.opengis.net/kml/2.2 https://developers.google.com/kml/schema/kml22gx.xsd">';
    $kml[] = '<Document>';
	$kml[] = '<name>Waypoints</name>';
	//$kml[] = '<open>1</open>';
    
    while($waypoint = mysqli_fetch_assoc($waypoints)){ // loop through each waypoint with coordinates

        $kml[] = '<Placemark id="marker_' . $waypoint["trwpWaypID"] .'">';
        $kml[] = '   <name>' . $waypoint["waypNameLong"] . '</name>';
        $kml[] = '   <description>' . $waypoint["trwpWaypID"] . ': '. $waypoint["waypNameLong"] . ' (' . $waypoint["waypAltitude"] . 'm)</description>';
        $kml[] = '   <Style>';
        $kml[] = '      <IconStyle>';
        $kml[] = '          <Icon>';
        $kml[] = '              <href>./images/' . $waypoint["wtypCode"] . '16.png</href>';
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
        $kml[] = '      <coordinates>' . $waypoint["waypCoordWGS84E"] . ',' . $waypoint["waypCoordWGS84N"] . ',0</coordinates>';
        $kml[] = '   </Point>';
        $kml[] = '</Placemark>';
    };
        $kml[] = '</Document>';
        $kml[] = '</kml>';
    // Merge kml array into one variable
    $kmlOutput = join("\n", $kml);

    echo $kmlOutput;

    // Define header so that the PHP file generates a KML
    fputs($fp, "waypKmlFileName " . $_POST["waypKmlFileName"] . "\r\n");
    if(isset($_POST["waypKmlFileName"])){
        $fp_kml = @fopen($_POST["waypKmlFileName"],"w");
        if(!$fp_kml){
            fputs($fp, "Error opening files\r\n");
        };
    }else{
		$fp_kml = @fopen("tourDbWaypoints.kml","w");
        fputs($fp, "File name not set\r\n");
	};
    
    if ($debugLevel >= 5){
        fputs($fp, $kmlOutput);
    }; 

    fputs($fp_kml, $kmlOutput);
    fclose($fp_kml);
    
    if ($debugLevel >= 1){
        fputs($fp, date("Ymd-H:i:s", time()) . "-Line 102: KML File written to: " . $_POST["waypKmlFileName"] . "\r\n");
        fclose($fp);
    }; 
}else{
    if ($debugLevel >= 3){
        fputs($fp, date("Ymd-H:i:s", time()) . "-Line 107: Invalid parameters!!\r\n");
    };
};
?>