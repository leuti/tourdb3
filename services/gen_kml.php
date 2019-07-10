<?php
// ---------------------------------------------------------------------------------------------
// This script generates kml files (and potentially also gpx)
// for tracks and segments
// It should be called by all functions expecting a kml and gpx output
//
// INPUT
// It is expecting a JSON object with following content: 
// ["sessionId"], ["sqlWhere"], ["genTrackKml"],["sqlWhereSegments"]["genSegKml"]
//
// OUTPUT
// The script returns a JSON object with following content:
// ["message"] - only filled with an error message in case of an error
// ["status"]     - "OK" if no error has occured, "ERR" in case of an error

// Created: 30.12.2017 - Daniel Leutwyler
// ---------------------------------------------------------------------------------------------
//
// Tasks
// * Error handling to be improved
// * Ability to generate GPX files

// -----------------------------------
// Set variables and parameters    
date_default_timezone_set("Europe/Zurich");
include("config.inc.php");                                                  // Include config file

// Set debug level
$countTracks = 0;                                                           // Internal counter for tracks processed
$countSegments = 0;                                                         // Internal counter for segments processed
$firstRecord = true;

// Array for the styling of the lines in the kml file
$styleArray = array(
    array("Wanderung","FF01EDFF",3,"FF01EDFF",5),                           // gelb - OK
    array("Alpintour","FF00C0FF",3,"FF00C0FF",5),                           // orange - OK
    array("Hochtour","FF0000FF",3,"FF0000FF",5),                            // ROT - OK
    
    array("Sportklettern","ff0086d9",3,"ff0086d9",5),                       // hell braun - OK
    array("Mehrseilklettern","ff006caf",3,"ff006caf",5),                    // mittel braun - OK 
    array("Alpinklettern","ff004f80",3,"ff004f80",5),                       // dunkel braun - OK
        
    array("Velotour","FF01FF86",3,"FF01FF86",5),                            // grün - OK
    
    array("Winterwanderung","ffc6ff63",3,"ffc6ff63",5),                     // türkis - OK
    array("Schneeschuhwanderung","ffc2c200",3,"ffc2c200",5),                // hell blau - OK 
    array("Skitour","ffffaa00",3,"ffffaa00",5),                             // dunkel blau - OK
    array("Skihochtour","ffff0000",3,"ffff0000",5),                         // dunkel blau - OK
    
    array("Schwimmen","ff7f0000",3,"ff7f0000",5),                           // rosa
    array("Joggen","ff7f00aa",3,"ff7f00aa",5),                              // violet
    array("Others","ffff55ff",3,"ffff55ff",5)                               // rosa
);

// Open log file
if ($debugLevel >= 1){
    $logFileLoc = dirname(__FILE__) . "/../log/gen_kml_new.log";                // Assign file location
    //$logFileLoc = dirname(__FILE__) . "/../log/" . basename(__FILE__)";                // Assign file location
    $logFile = @fopen($logFileLoc,"a");     
    if ( $debugLevel >= 1 ) fputs($logFile, "=================================================================\r\n");
    if ( $debugLevel >= 1 ) fputs($logFile, date("Ymd-H:i:s", time()) . "-Line " . __LINE__ . ": " . basename(__FILE__) . " opened \r\n"); 
};

// variables passed on by client (as JSON object)
$receivedData = json_decode ( file_get_contents("php://input"), true );
$sessionId = $receivedData["sessionId"];                                    
$sqlWhere = $receivedData["sqlWhere"];                          // where statement to select tracks to be displayed
$objectName = $receivedData["objectName"];

if ($debugLevel >= 3){
    fputs($logFile, "Line " . __LINE__ . ": Received parameters: \r\n");
    fputs($logFile, "   objectName: " . $objectName . "\r\n");
    fputs($logFile, "   sessionId:  " . $sessionId . "\r\n");
    fputs($logFile, "   sqlWhere:   " . $sqlWhere . "\r\n");
};

// create upload dir / file name
$kml_dir = "../tmp/kml_disp/" . $sessionId . "/";                           // Session id used to create unique directory
if (!is_dir ( $kml_dir )) {                                                 // Create directory with name = session id
    mkdir($kml_dir, 0777);
}

// ==================================================================
// If flag is set to generate tracks KML
//
if ( $objectName == "tracks" ) {

    // open file to store track kml file
    $trackKmlFileURL = $kml_dir . "tracks.kml";
    $trackOutFile = fopen($trackKmlFileURL, "w");                           // Open kml output file

    // Write headern and style section of KML
    $kml[] = '<?xml version="1.0" encoding="UTF-8"?>';
    $kml[] = '<kml xmlns="http://www.opengis.net/kml/2.2" xmlns:gx="http://www.google.com/kml/ext/2.2" xmlns:kml="http://www.opengis.net/kml/2.2" xmlns:atom="http://www.w3.org/2005/Atom">';
    $kml[] = '  <Document>';
    $kml[] = '    <name>tourdb - tracks</name>';

    // Create kml stylemaps
    $i=0;
    for ( $i; $i < sizeof($styleArray); $i++ ) {                            // loop througth style array
        $kml = createStyles($styleArray[$i], $kml);                         // create kml styles
    }

    // Write main section - intro
    $kml[] = "    <Folder>";
    $kml[] = "      <name>tourdb exported tracks</name>";
    $kml[] = "        <visibility>0</visibility>";
    $kml[] = "        <open>1</open>";

    // Select tracks meeting given WHERE clause
    $sql = "SELECT trkId, trkTrackName, trkRoute, trkDateBegin, trkSubType, trkCoordinates, ";
    $sql .= "trkCoordTop, trkCoordBottom, trkCoordLeft, trkCoordRight ";
    $sql .= "FROM tbl_tracks ";
    $sql .= $sqlWhere;
    $sql .= ' AND trkCoordinates <> "" ';

    $records = mysqli_query($conn, $sql);

    if ($debugLevel >= 3) fputs($logFile, "Line " . __LINE__ . ": sql to select track: " . $sql . "\r\n");

    // Loop through each selected track and write main track data
    while($singleRecord = mysqli_fetch_assoc($records))
    { 
        // Set Coord when current record is first
        if ( $firstRecord ) {
            $coordTop = $singleRecord["trkCoordTop"];                               // Max 297000
            $coordBottom = $singleRecord["trkCoordBottom"];                         // Min  74000
            $coordLeft = $singleRecord["trkCoordLeft"];                             // Min 110000
            $coordRight = $singleRecord["trkCoordRight"];                           // Max 840000
        }

        $countTracks++;                                                     // Counter for the number of tracks produced
        $kml[] = '        <Placemark id="linepolygon_' . sprintf("%'05d", $singleRecord["trkId"]) . '">';
        $kml[] = '          <name>' . $singleRecord["trkId"] . ": " .  $singleRecord["trkTrackName"] . " (" . $singleRecord["trkDateBegin"] . ")" . '</name>';
        $kml[] = '          <visibility>1</visibility>';
        $kml[] = '          <description>' . $singleRecord["trkId"] . ' - ' . $singleRecord["trkRoute"] . '</description>';

        // set default stylmap and then loop through style maps
        $styleMapDefault = "          <styleUrl>#stylemap_Others</styleUrl>";   // Set styleUrl to Others in case nothing in found
        $i=0;
        for ($i; $i<sizeof($styleArray); $i++) {                            // 10 is the number of existing subtypes in array (lines)
            if ($styleArray[$i][0] == $singleRecord["trkSubType"])
            {
                $styleMapDefault = '          <styleUrl>#stylemap_' . $singleRecord["trkSubType"] . '</styleUrl>';
                break;
            }
        }
        $kml[] = $styleMapDefault;
        
        $kml[] = "          <ExtendedData>";
        $kml[] = '            <Data name="type">';
        $kml[] = "              <value>linepolygon</value>";
        $kml[] = "            </Data>";
        $kml[] = "          </ExtendedData>";
        $kml[] = "          <LineString>";
        $kml[] = "            <coordinates>";
        $kml[] = '               ' . $singleRecord["trkCoordinates"];
        $kml[] = "            </coordinates>";
        $kml[] = "          </LineString>";
        $kml[] = "        </Placemark>";   

        // evaluate coord boundaries
        if ( !$firstRecord ) {
            if ( $singleRecord["trkCoordTop"] > $coordTop ) {
                $coordTop = $singleRecord["trkCoordTop"];
            }
            if ( $singleRecord["trkCoordBottom"] < $coordBottom ) {
                $coordBottom = $singleRecord["trkCoordBottom"];
            }
            if ( $singleRecord["trkCoordLeft"] < $coordLeft ) {
                $coordLeft = $singleRecord["trkCoordLeft"];
            }
            if ( $singleRecord["trkCoordRight"] > $coordRight ) {
                $coordRight = $singleRecord["trkCoordRight"];
            }
        }
        $firstRecord = false;
    };

    // Write KML trailer
    $kml[] = "    </Folder>";
    $kml[] = "  </Document>";
    $kml[] = "</kml>";

    // Merge kml array into one variable
    $kmlOutput = join("\r\n", $kml);

    // Write kml into file
    fputs($trackOutFile, "$kmlOutput");                                     // Write kml to file
    fclose($trackOutFile);                                                      // close kml file

    $returnMessage = "$countTracks Tracks found"; 

    if ( $countTracks > 0 ) {
    
        // Create return object
        $returnObject["status"] = "OK";                                             // add status field (OK) to trackObj
        $returnObject["message"] = $returnMessage;                                  // add empty error message to trackObj
        $returnObject["coordTop"] = $coordTop;
        $returnObject["coordBottom"] = $coordBottom;
        $returnObject["coordLeft"] = $coordLeft;
        $returnObject["coordRight"] = $coordRight;
        $returnObject["recordcount"] = $countTracks;
        $returnObject["objectName"] = $objectName;
    } else {
        $returnObject["status"] = "NOK";                                             // add status field (OK) to trackObj
        $returnObject["message"] = $returnMessage;                                  // add empty error message to trackObj
    }
    echo json_encode($returnObject);                                            // echo JSON object to client

    if ( $debugLevel >= 1 ) fputs($logFile, "Line " . __LINE__ . ": $countTracks Segments processed\r\n");
    if ( $debugLevel >= 1 ) fputs($logFile, "gen_kml.php $objectName finished: " . date("Ymd-H:i:s", time()) . "\r\n");    

    // Close all files and connections
    if ( $debugLevel >= 1 ) fclose($logFile);                                   // close log file
    mysqli_close($conn);                                                        // close SQL connection 

    exit;
}

// ==================================================================
// If flag is set to generate segments KML
//
if ( $objectName == "segments" ) {
    
    // open file to store segment kml file
    $segKmlFileURL = $kml_dir . "segments.kml";
    $segOutFile = fopen($segKmlFileURL, "w");                               // Open kml output file
    
    // re-initialise kml variable
    $kml = [];

    // Write headern and style section of KML
    $kml[] = '<?xml version="1.0" encoding="UTF-8"?>';
    $kml[] = '<kml xmlns="http://www.opengis.net/kml/2.2" xmlns:gx="http://www.google.com/kml/ext/2.2" xmlns:kml="http://www.opengis.net/kml/2.2" xmlns:atom="http://www.w3.org/2005/Atom">';
    $kml[] = '  <Document>';
    $kml[] = '    <name>tourdb - segments</name>';

    // Create kml stylemaps
    $kml[] = '    <StyleMap id="stylemap_Others">';
    
    // StyleMap
    $kml[] = "      <Pair>";
    $kml[] = "        <key>normal</key>";
    $kml[] = "        <styleUrl>#style_Others_norm</styleUrl>";
    $kml[] = "      </Pair>";
    $kml[] = "      <Pair>";
    $kml[] = "        <key>highlight</key>";
    $kml[] = "        <styleUrl>#style_Others_hl</styleUrl>";
    $kml[] = "      </Pair>";
    $kml[] = "    </StyleMap>";

    // Style
    $kml[] = '    <Style id="style_Others_norm">';
    $kml[] = "      <LineStyle>";
    $kml[] = "        <color>#FFCC66FF</color>";
    $kml[] = "        <width>3</width>";
    $kml[] = "      </LineStyle>";
    $kml[] = "      <PolyStyle>";
    $kml[] = "        <color>#FFCC66FF</color>";
    $kml[] = "        <width>3</width>";
    $kml[] = "      </PolyStyle>";
    $kml[] = "    </Style>";
    $kml[] = '    <Style id="style_Others_hl">';
    $kml[] = "      <LineStyle>";
    $kml[] = "        <color>#FFCC66FF</color>";
    $kml[] = "        <width>5</width>";
    $kml[] = "      </LineStyle>";
    $kml[] = "      <PolyStyle>";
    $kml[] = "        <color>#FFCC66FF</color>";
    $kml[] = "        <width>5</width>";
    $kml[] = "      </PolyStyle>";
    $kml[] = "    </Style>";

    // Write main section - intro
    $kml[] = "    <Folder>";
    $kml[] = "      <name>tourdb exported segments</name>";
    $kml[] = "        <visibility>0</visibility>";
    $kml[] = "        <open>1</open>";

    // Select tracks meeting given WHERE clause
    $sql = "SELECT tbl_segments.segId";
    $sql .= ", tbl_segments.segName";
    $sql .= ", tbl_segments.segTypeFID as segType";
    $sql .= ", tbl_segments.segSourceFID";
    $sql .= ", tbl_segments.segSourceRef";
    $sql .= ", tbl_segments.segGradeFID";
    $sql .= ", tbl_segments.segCoordinates ";
    $sql .= ", tbl_segments.segCoordTop ";
    $sql .= ", tbl_segments.segCoordBottom ";
    $sql .= ", tbl_segments.segCoordLeft ";
    $sql .= ", tbl_segments.segCoordRight ";
    $sql .= "FROM tbl_segments ";
    $sql .= $sqlWhere;
    $sql .= ' AND segCoordinates <> "" ';

    // execute sql query and store results in variable $records
    $records = mysqli_query($conn, $sql);
    
    if ($debugLevel >= 3){
        fputs($logFile, "Line " . __LINE__ . ": sql: " . $sql . "\r\n");
    };

    // Loop through each selected track and write main track data
    while($singleRecord = mysqli_fetch_assoc($records))
    { 
        
        // Set Coord when current record is first
        if ( $firstRecord ) {
            $coordTop = $singleRecord["segCoordTop"];                               // Max 297000
            $coordBottom = $singleRecord["segCoordBottom"];                         // Min  74000
            $coordLeft = $singleRecord["segCoordLeft"];                             // Min 110000
            $coordRight = $singleRecord["segCoordRight"];                           // Max 840000
        }

        $countSegments++;                                                     // Counter for the number of tracks produced
        $kml[] = '        <Placemark id="linepolygon_' . sprintf("%'05d", $singleRecord["segId"]) . '">';
        $kml[] = "          <name>" . $singleRecord["segName"] . "</name>";
        $kml[] = "          <visibility>1</visibility>";
        $kml[] = "          <description>" . $singleRecord["segSourceFID"] . "-" . $singleRecord["segSourceRef"] . " " .
                $singleRecord["segName"] . " (" . $singleRecord["segGradeFID"] . ")</description>";
        $kml[] = "          <styleUrl>#stylemap_Others</styleUrl>";         // Set styleUrl to Others in case nothing in found
        $kml[] = "          <ExtendedData>";
        $kml[] = '            <Data name="type">';
        $kml[] = "              <value>linepolygon</value>";
        $kml[] = "            </Data>";
        $kml[] = "          </ExtendedData>";
        $kml[] = "          <LineString>";
        $kml[] = "            <coordinates>" . $singleRecord["segCoordinates"];
        $kml[] = "            </coordinates>";
        $kml[] = "          </LineString>";
        $kml[] = "        </Placemark>";   
    
        // evaluate if current record needs to extend coord boundaries
        if ( !$firstRecord ) {
            if ( $singleRecord["segCoordTop"] > $coordTop ) {
                $coordTop = $singleRecord["segCoordTop"];
            }
            if ( $singleRecord["segCoordBottom"] < $coordBottom ) {
                $coordBottom = $singleRecord["segCoordBottom"];
            }
            if ( $singleRecord["segCoordLeft"] < $coordLeft ) {
                $coordLeft = $singleRecord["segCoordLeft"];
            }
            if ( $singleRecord["segCoordRight"] > $coordRight ) {
                $coordRight = $singleRecord["segCoordRight"];
            }
        }
        $firstRecord = false;
        if ( $debugLevel >= 3 ) {
            fputs($logFile, "=============================================\r\n");
            fputs($logFile, "Line " . __LINE__ . ": segId: " . $singleRecord["segId"] . $singleRecord["segName"] . "\r\n");
            fputs($logFile, "Line " . __LINE__ . ": segcoordTop: " . $singleRecord["segCoordTop"] ."\r\n");
            fputs($logFile, "Line " . __LINE__ . ": segcoordBottom: " . $singleRecord["segCoordBottom"] ."\r\n");
            fputs($logFile, "Line " . __LINE__ . ": segcoordLeft: " . $singleRecord["segCoordLeft"] ."\r\n");
            fputs($logFile, "Line " . __LINE__ . ": segcoordRight: " . $singleRecord["segCoordRight"] ."\r\n");  
            fputs($logFile, "Line " . __LINE__ . ": coordTop: $coordTop\r\n");
            fputs($logFile, "Line " . __LINE__ . ": coordBottom: $coordBottom\r\n");
            fputs($logFile, "Line " . __LINE__ . ": coordLeft: $coordLeft\r\n");
            fputs($logFile, "Line " . __LINE__ . ": coordRight: $coordRight\r\n");    
        }
    };

    // Write KML trailer
    $kml[] = "    </Folder>";
    $kml[] = "  </Document>";
    $kml[] = "</kml>";

    // Merge kml array into one variable
    $kmlOutput = join("\r\n", $kml);

    // write kml output to file
    fputs($segOutFile, "$kmlOutput");                                       // Write kml to file
    fclose($segOutFile);
}

// evaluate how many tracks and segments were found and add this info to return message
$returnMessage = "$countSegments Segments found";

// Create return object
$returnObject["status"] = "OK";                                             // add status field (OK) to trackObj
$returnObject["message"] = $returnMessage;                                  // add empty error message to trackObj
$returnObject["coordTop"] = $coordTop;
$returnObject["coordBottom"] = $coordBottom;
$returnObject["coordLeft"] = $coordLeft;
$returnObject["coordRight"] = $coordRight;
$returnObject["recordcount"] = $countSegments;
$returnObject["objectName"] = $objectName;
echo json_encode($returnObject);                                            // echo JSON object to client

if ( $debugLevel >= 1 ) fputs($logFile, "Line " . __LINE__ . ": $countTracks Segments processed\r\n");
if ( $debugLevel >= 1 ) fputs($logFile, "gen_kml.php $objectName finished: " . date("Ymd-H:i:s", time()) . "\r\n");    

// Close all files and connections
if ( $debugLevel >= 1 ) fclose($logFile);                                   // close log file
mysqli_close($conn);                                                        // close SQL connection 

exit;

// function to create styles in KML file
function createStyles ($styleArray,$kml) {
    
    // Generates style map and style for each subtype                       // variable aus $styleArray lesen und einsetzen
    $styleMapId = "stylemap_" . $styleArray[0];
    $styleUrlNorm = "style_" . $styleArray[0] . "_norm";
    $styleUrlHl = "style_" . $styleArray[0] . "_hl";

    // StyleMap tourdb
    $kml[] = '    <StyleMap id="' . $styleMapId . '">';

    // StyleMap
    $kml[] = "      <Pair>";
    $kml[] = "        <key>normal</key>";
    $kml[] = "        <styleUrl>#" . $styleUrlNorm . "</styleUrl>";
    $kml[] = "      </Pair>";
    $kml[] = "      <Pair>";
    $kml[] = "        <key>highlight</key>";
    $kml[] = "        <styleUrl>#" . $styleUrlHl . "</styleUrl>";
    $kml[] = "      </Pair>";
    $kml[] = "    </StyleMap>";

    // Style
    $kml[] = '    <Style id="' . $styleUrlNorm . '">';
    $kml[] = "      <LineStyle>";
    $kml[] = "        <color>" . $styleArray[1] . "</color>";
    $kml[] = "        <width>" . $styleArray[2] . "</width>";
    $kml[] = "      </LineStyle>";
    $kml[] = "      <PolyStyle>";
    $kml[] = "        <color>" . $styleArray[1] . "</color>";
    $kml[] = "        <width>" . $styleArray[2]. "</width>";
    $kml[] = "      </PolyStyle>";
    $kml[] = "    </Style>";
    $kml[] = '    <Style id="' . $styleUrlHl . '">';
    $kml[] = "      <LineStyle>";
    $kml[] = "        <color>" . $styleArray[3] . "</color>";
    $kml[] = "        <width>" . $styleArray[4] . "</width>";
    $kml[] = "      </LineStyle>";
    $kml[] = "      <PolyStyle>";
    $kml[] = "        <color>" . $styleArray[3] . "</color>";
    $kml[] = "        <width>" . $styleArray[4] . "</width>";
    $kml[] = "      </PolyStyle>";
    $kml[] = "    </Style>";

    return $kml;
}
?>

