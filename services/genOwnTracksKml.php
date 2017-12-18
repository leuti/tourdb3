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
include("config.inc.php");  // Include config file
$stlyeNormalColour = '#ff0000ff';                       // Colour of line in normal mode
$stlyeNormalWidth = '3';                                // Width of line in normal mode
$stlyeHighlightColour = '#fff000ff';                    // Colour of line in normal mode
$stlyeHighlightWidth = '5';                             // Width of line in normal mode


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
$kml[] = '    <StyleMap id="stylemap_tourdb">';
$kml[] = '      <Pair>';
$kml[] = '        <key>normal</key>';
$kml[] = '        <styleUrl>#style_normal</styleUrl>';
$kml[] = '      </Pair>';
$kml[] = '      <Pair>';
$kml[] = '        <key>highlight</key>';
$kml[] = '        <styleUrl>#style_highlight</styleUrl>';
$kml[] = '      </Pair>';
$kml[] = '    </StyleMap>';
$kml[] = '    <Style id="style_normal">';
$kml[] = '      <LineStyle>';
$kml[] = '        <color>' . $stlyeNormalColour . '</color>';
$kml[] = '        <width>' . $stlyeNormalWidth . '</width>';
$kml[] = '      </LineStyle>';
$kml[] = '      <PolyStyle>';
$kml[] = '        <color>' . $stlyeNormalColour . '</color>';
$kml[] = '        <width>' . $stlyeNormalWidth . '</width>';
$kml[] = '      </PolyStyle>';
$kml[] = '    </Style>';
$kml[] = '    <Style id="style_highlight">';
$kml[] = '      <LineStyle>';
$kml[] = '        <color>' . $stlyeHighlightColour . '</color>';
$kml[] = '        <width>' . $stlyeHighlightWidth . '</width>';
$kml[] = '      </LineStyle>';
$kml[] = '      <PolyStyle>';
$kml[] = '        <color>' . $stlyeHighlightColour . '</color>';
$kml[] = '        <width>' . $stlyeHighlightWidth . '</width>';
$kml[] = '      </PolyStyle>';
$kml[] = '    </Style>';

// Write main section - intro
$kml[] = '    <Folder>';
$kml[] = '      <name>tourdb exported KML</name>';
$kml[] = '        <visibility>0</visibility>';
$kml[] = '        <open>1</open>';

// Select tracks meeting given WHERE clause
$sql = "SELECT trkId, trkTrackName, trkRoute, trkParticipants ";
$sql .= "FROM tbl_tracks ";
$sql .= $whereGenKml;
$tracks = mysqli_query($conn, $sql);

// Loop through each selected track and write main track data
while($SingleTrack = mysqli_fetch_assoc($tracks))
{ 
    $kml[] = '        <Placemark id="linepolygon_' . sprintf("%'05d", $SingleTrack["trkId"]) . '">';
    $kml[] = '          <name>' . $SingleTrack["trkTrackName"] . '</name>';
    $kml[] = '          <visibility>1</visibility>';
    $kml[] = '          <description>' . $SingleTrack["trkId"] . ' - ' . $SingleTrack["trkRoute"] . ' (mit ' .  $SingleTrack["trkParticipants"] . ')</description>';
    $kml[] = '          <styleUrl>#stylemap_tourdb</styleUrl>';
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
mysql_close($conn);                                             // close SQL connection 
fclose($outFile);                                               // close kml file
?>