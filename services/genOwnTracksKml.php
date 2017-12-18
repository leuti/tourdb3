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

$sql = "SELECT trkId, trkTrackName, trkRoute ";
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
    $kml[] = '            <coordinates>';
    $kml[] = '             8.505288351801079,47.33652743186154,0 8.505585623549026,47.33617387209225,0 8.5047957900375,47.33637906418235,0 8.50407724631536,47.33617880936397,0 8.503584511594617,47.33602144296602,0 8.503379602542626,47.33569950649844,0 8.503205283301982,47.33558417687862,0 8.503189002458406,47.33543140882657,0 8.50331842662418,47.33528629209979,0 8.503168952690865,47.3350897765492,0 8.50332411707385,47.33490844164079,0 8.503225228650079,47.33459452206319,0 8.503182848253658,47.33445998506657,0 8.503561652660359,47.33421362064634,0 8.503673354622503,47.33384378710456,0 8.503961344283187,47.33368821113609,0 8.504210722701018,47.33358696205518,0 8.504484222434984,47.3333685521373,0 8.504849604273018,47.33311331203102,0 8.504411788094075,47.33305438911223,0 8.503936441023351,47.33310375329116,0 8.502829686604496,47.33333884261586,0 8.501641250298707,47.33412338011304,0 ';
    $kml[] = '            </coordinates>';
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