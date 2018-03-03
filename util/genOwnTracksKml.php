<?php
// ---------------------------------------------------------------------------------------------
// This service generates a KML file for the tracks stored in the tourdb
// As input the service expects a SQL WHERE clause in variable $_POST["whereGenKml"] 
//
// This script is intended for regular usage
//
// Created: 18.12.2017 - Daniel Leutwyler
// ---------------------------------------------------------------------------------------------

// Test Cases
// Define test cases here
// Prerequisit for testing is that logbook data and GPX data is correctly imported

// ACTIONS
// * Add actions

// -----------------------------------
// Set variables and parameters    
include("config.inc.php");                              // Include config file
date_default_timezone_set('Europe/Zurich');             // must be set when using time functions
$debugLevel = 3;                                        // 0 = off, 6 = all
$countTracks = 0;                                       // Internal counter for tracks processed

// --------------------------------------------------
// Array for the styling of the lines in the kml file
$styleArray = array(
    array("Wanderung","FF01EDFF",3,"FF01EDFF",5),                           // gelb
    array("Winterwandern","#ff852eff",3,"#ff852eff",5),                     // orange
    array("Alpintour","FF00C0FF",3,"FF00C0FF",5),                           // rot
    array("Hochtour","FF0000FF",3,"FF0000FF",5),                            // schwarz
    
    array("Sportklettern","FFD9D9D9",3,"FFD9D9D9",5),                       // hell grau
    array("Mehrseilklettern","FFA6A6A6",3,"FFA6A6A6",5),                    // mittel grau
    array("Alpinklettern","FF808080",3,"FF808080",5),                       // dunkel grau
        
    array("Velotour","#FF01FF86",3,"#FF01FF86",5),                          // grün 
    
    array("Schneeschuhwanderung","#FFFFCC33",3,"#FFFFCC33",5),              // hell blau
    array("Skitour","#FFC07000",3,"#FFC07000",5),                           // dunkel blau
    
    array("Others","#FFCC66FF",3,"#FFCC66FF",5)                             // rosa
);

// Open file for import log
$importGpxLog = dirname(__FILE__) . "\..\log\genOwnTracksKml.log";          // Assign file location
$logFile = @fopen($importGpxLog,"w");                                       // open log file handler 
fputs($logFile, "importGpx.php started: " . date("Ymd-H:i:s", time()) . "\r\n");    

// Set WHERE string if WHERE clause has been posted
if(isset($_POST["whereGenKml"]) && $_POST["whereGenKml"] != ''){
    $whereGenKml = $_POST["whereGenKml"]; 
} else{
    $whereGenKml = '';                                  // Set to empty when no WHERE clause received
};

// Write headern and style section of KML
$kml[] = '<?xml version="1.0" encoding="UTF-8"?>';
$kml[] = '<kml xmlns="http://www.opengis.net/kml/2.2" xmlns:gx="http://www.google.com/kml/ext/2.2" xmlns:kml="http://www.opengis.net/kml/2.2" xmlns:atom="http://www.w3.org/2005/Atom">';
$kml[] = '  <Document>';
$kml[] = '    <name>tourdb - KmlFile</name>';

// Create kml stylemaps
$i=0;
for ($i; $i<11; $i++) {                                 // 10 is the number of existing subtypes in array (lines)
        $kml = createStyles($styleArray[$i], $kml);    
    }

// Write main section - intro
$kml[] = '    <Folder>';
$kml[] = '      <name>tourdb exported KML</name>';
$kml[] = '        <visibility>0</visibility>';
$kml[] = '        <open>1</open>';

// Select tracks meeting given WHERE clause
$sql = "SELECT trkId, trkTrackName, trkRoute, trkParticipants, trkSubType ";
$sql .= "FROM tbl_tracks ";
$sql .= $whereGenKml;
fputs($logFile, "Line 81  sql: $sql\r\n");
$tracks = mysqli_query($conn, $sql);

// Loop through each selected track and write main track data
while($SingleTrack = mysqli_fetch_assoc($tracks))
{ 
    $countTracks++;                                                             // Counter for the number of tracks produced
    $kml[] = '        <Placemark id="linepolygon_' . sprintf("%'05d", $SingleTrack["trkId"]) . '">';
    $kml[] = '          <name>' . $SingleTrack["trkTrackName"] . '</name>';
    $kml[] = '          <visibility>1</visibility>';
    $kml[] = '          <description>' . $SingleTrack["trkId"] . ' - ' . $SingleTrack["trkRoute"] . ' (mit ' .  $SingleTrack["trkParticipants"] . ')</description>';
    
    $styleMapDefault = '          <styleUrl>#stylemap_Others</styleUrl>';       // Set styleUrl to Others in case nothing in found
    $i=0;
    for ($i; $i<11; $i++) {                                                     // 10 is the number of existing subtypes in array (lines)
        if ($styleArray[$i][0] == $SingleTrack["trkSubType"])
        {
            $styleMapDefault = '          <styleUrl>#stylemap_' . $SingleTrack["trkSubType"] . '</styleUrl>';
            break;
        }
    }
    $kml[] = $styleMapDefault;
       
    $kml[] = '          <ExtendedData>';
    $kml[] = '            <Data name="type">';
    $kml[] = '              <value>linepolygon</value>';
    $kml[] = '            </Data>';
    $kml[] = '          </ExtendedData>';
    $kml[] = '          <LineString>';

    // Select all track points for the current track
    $sqlTrkPt  = "SELECT tptLat, tptLon, tptEle ";
    $sqlTrkPt .= "FROM tbl_trackPoints WHERE tptTrackFID = ";
    $sqlTrkPt .= $SingleTrack["trkId"] . " ORDER BY tptNumber"; 
    $trackPoints = mysqli_query($conn, $sqlTrkPt);
   
    // For each trkId loop track point and create coordinates string
    $first = 1;                                                             
    while($trackPoint = mysqli_fetch_assoc($trackPoints))
    {
        if ($first==1)                                                          // When first don't print the space between coordinate points
        {
            $coord = $trackPoint["tptLon"] . ',' . $trackPoint["tptLat"] . ',' . $trackPoint["tptEle"];
            $first = 0;
        } else 
        {
            $coord .= ' ' . $trackPoint["tptLon"] . ',' . $trackPoint["tptLat"] . ',' . $trackPoint["tptEle"];
        }
    };

    // Write coordinates and remainder of track data 
    $kml[] = '            <coordinates>' . $coord . '</coordinates>';
    $kml[] = '          </LineString>';
    $kml[] = '        </Placemark>';   
};

// Write KML trailer
$kml[] = '    </Folder>';
$kml[] = '  </Document>';
$kml[] = '</kml>';

// Merge kml array into one variable
$kmlOutput = join("\r\n", $kml);

$outFile = @fopen("../out/ownTracksKml.kml","w");               // Open KML file for writing
fputs($outFile, "$kmlOutput");                                  // Write kml to file
fputs($logFile, "importGpx.php finished: " . date("Ymd-H:i:s", time()) . "\r\n");    

fputs($logFile, "$countTracks Tracks processed\r\n");

// Close all files and connections
fclose($logFile);                                               // close log file
mysql_close($conn);                                             // close SQL connection 
fclose($outFile);                                               // close kml file

function createStyles ($styleArray,$kml) {

    // Generates style map and style for each subtype                          //// variable aus $styleArray lesen und einsetzen
    $styleMapId = "stylemap_" . $styleArray[0];
    $styleUrlNorm = "style_" . $styleArray[0] . "_norm";
    $styleUrlHl = "style_" . $styleArray[0] . "_hl";

    // StyleMap tourdb
    $kml[] = '    <StyleMap id="' . $styleMapId . '">';

    // StyleMap
    $kml[] = '      <Pair>';
    $kml[] = '        <key>normal</key>';
    $kml[] = '        <styleUrl>#' . $styleUrlNorm . '</styleUrl>';
    $kml[] = '      </Pair>';
    $kml[] = '      <Pair>';
    $kml[] = '        <key>highlight</key>';
    $kml[] = '        <styleUrl>#' . $styleUrlHl . '</styleUrl>';
    $kml[] = '      </Pair>';
    $kml[] = '    </StyleMap>';

    // Style
    $kml[] = '    <Style id="' . $styleUrlNorm . '">';
    $kml[] = '      <LineStyle>';
    $kml[] = '        <color>' . $styleArray[1] . '</color>';
    $kml[] = '        <width>' . $styleArray[2] . '</width>';
    $kml[] = '      </LineStyle>';
    $kml[] = '      <PolyStyle>';
    $kml[] = '        <color>' . $styleArray[1] . '</color>';
    $kml[] = '        <width>' . $styleArray[2]. '</width>';
    $kml[] = '      </PolyStyle>';
    $kml[] = '    </Style>';
    $kml[] = '    <Style id="' . $styleUrlHl . '">';
    $kml[] = '      <LineStyle>';
    $kml[] = '        <color>' . $styleArray[3] . '</color>';
    $kml[] = '        <width>' . $styleArray[4] . '</width>';
    $kml[] = '      </LineStyle>';
    $kml[] = '      <PolyStyle>';
    $kml[] = '        <color>' . $styleArray[3] . '</color>';
    $kml[] = '        <width>' . $styleArray[4] . '</width>';
    $kml[] = '      </PolyStyle>';
    $kml[] = '    </Style>';

    return $kml;
}

?>