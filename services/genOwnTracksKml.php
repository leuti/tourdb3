<?php
date_default_timezone_set('Europe/Zurich');

include("config.inc.php");  // Include config file
    
// Set WHERE string if sqlFilterString has been provided	
if(isset($_POST["whereGenKml"]) && $_POST["whereGenKml"] != ''){
    $whereGenKml = $_POST["whereGenKml"]; // Filter number
} else{
    $whereGenKml = ''; // If there's no sql search string delivered, set it to ''
};

$kml[] = '<?xml version="1.0" encoding="UTF-8"?>';
$kml[] = '<kml xmlns="http://www.opengis.net/kml/2.2" xmlns:gx="http://www.google.com/kml/ext/2.2" xmlns:kml="http://www.opengis.net/kml/2.2" xmlns:atom="http://www.w3.org/2005/Atom">';
$kml[] = '  <Document>';
$kml[] = '    <name>KmlFile</name>';
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
$kml[] = '        <color>ff0000ff</color>';
$kml[] = '        <width>3</width>';
$kml[] = '      </LineStyle>';
$kml[] = '      <PolyStyle>';
$kml[] = '        <color>660000ff</color>';
$kml[] = '        <width>3</width>';
$kml[] = '      </PolyStyle>';
$kml[] = '    </Style>';
$kml[] = '    <Style id="style_highlight">';
$kml[] = '      <LineStyle>';
$kml[] = '        <color>ff0000ff</color>';
$kml[] = '        <width>5</width>';
$kml[] = '      </LineStyle>';
$kml[] = '      <PolyStyle>';
$kml[] = '        <color>000000ff</color>';
$kml[] = '        <width>5</width>';
$kml[] = '      </PolyStyle>';
$kml[] = '    </Style>';
$kml[] = '    <Folder>';
$kml[] = '      <name>tourdb exported KML</name>';
$kml[] = '        <visibility>0</visibility>';
$kml[] = '        <open>1</open>';

// Select tracks meeting given WHERE clause

$sql = "SELECT trkId, trkTrackName, trkRoute, trkParticipants ";
$sql .= "FROM tbl_tracks ";
$sql .= $whereGenKml;

$tracks = mysqli_query($conn, $sql);

// For each trkId loop track point and create coordinates string

// Write track specific data to kml
while($SingleTrack = mysqli_fetch_assoc($tracks))
{ 
    $kml[] = '        <Placemark id="linepolygon_1488120598327">';
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

  
    // For each trkId loop track point and create coordinates string
    $sqlTrkPt  = "SELECT tptLat, tptLon, tptEle ";
    $sqlTrkPt .= "FROM tbl_trackPoints WHERE tptTrackFID = ";
    $sqlTrkPt .= $SingleTrack["trkId"] . " ORDER BY tptNumber"; 
    
    $trackPoints = mysqli_query($conn, $sqlTrkPt);
   
    // Write track specific data to kml
    $first = 1;
    while($trackPoint = mysqli_fetch_assoc($trackPoints))
    {
        if ($first==1) 
        {
            $coord = $trackPoint["tptLon"] . ',' . $trackPoint["tptLat"] . ',' . $trackPoint["tptEle"];
            $first = 0;
        } else 
        {
            $coord .= ' ' . $trackPoint["tptLon"] . ',' . $trackPoint["tptLat"] . ',' . $trackPoint["tptEle"];
        }
    };
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

$outFile = @fopen("../out/ownTracksKml.kml","w"); // TASK: Add Date/Time
fputs($outFile, "$kmlOutput"); 
mysql_close($conn);
fclose($outFile);
?>