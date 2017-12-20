<!DOCTYPE HTML><html><head>	<meta charset="utf-8"><title>import gpx</title></head>
<body>
  <?php
    // ---------------------------------------------------------------------------------------------
    // PHP script loading gpx files into table track points. When no track exists, a simple track
    // record is created. Before loading the gpx track points all previous track points are deleted. 
    // When a gpx for an existing track is imported, the track time is updated.
    //
    // This script is intended for regular usage
    //
    // Created: 13.12.2017 - Daniel Leutwyler
    // ---------------------------------------------------------------------------------------------

    // Test Cases
    // Track exists: track time is updated; correct number of track points inserted, track is update - will be eliminate later
    // No related track exists:  track is created with time, source file ref and track name
    // # recs of tbl_tracks = total number of strava files
    // # recs of tbl_tracks with flag toReview + # recs tracks created = total number of strava files
    // # recs of tbl_tracks have increased by # of tracks with review flag = 1
    // Total number of track points = to import log

    // ACTIONS
    // * Update Line number
    // * Run full import incl. logbook
    // * Test
    // * Turn function updTrkName off --> target is that tourdb is always in the lead 
    // * Write debug text into file
    // * Document in word document
    // * Update table (make fields unique, set index)

    // -----------------------------------
    // Set variables and parameters
    include("./config.inc.php");                                        // include config file
    date_default_timezone_set('Europe/Zurich');                         // must be set when using time functions
    ini_set ("display_error", 1);                                       // Switch errors on

    $debugLevel = 0;                                                    // 0 = off, 6 = all
    $recordNo = 0;                                                      // No of gpx files processed
    
    // -----------------------------------------
    // Main routine
    // -----------------------------------------

    $importGpxLog = dirname(__FILE__) . "\..\out\importGpx.log";         // Assign file location
    $err_file = @fopen($importGpxLog,"w");                              // open log file handler 

    // Open Directory for gxp import
    $verz = dirname(__FILE__) . "\..\import\gpx";                        // Open directory where the GPX are stored
    chdir($verz);                                                       // Change to directory
    $dirHandle = opendir($verz);                                        // Open file handle

    // Loop through directory with GPX files
    while ($fileName = readdir($dirHandle))                             // Loop through each file in directory
    { 
        $fullFileName = $verz . "\\" . $fileName;                       // Generate absolute file name
             
        // Perform following action for gpx files
        if (substr($fileName, strlen($fileName)-4) == ".gpx")           // Perform following statements only for gpx files
        {     
            echo "<br>--------------------<br>Filename: $fileName<br>";
            $result_array[$recordNo]["no"] = $recordNo;                 // No = Record counter (= number of gpx files) 
            $result_array[$recordNo]["fileName"] = $fileName;           // fileName = gpx file name
            $result_array[$recordNo]["trackFound"] = 1;                 // trackFound = 1=yes, 0=no (new track will be created)

            // ----------------------------------------------------
            // Check if a track exists with given Strava File Name
            // ----------------------------------------------------
            $trkId = getTrackId($conn,$fileName,$fullFileName);         // function checks if track exists (returns -1 if not)
            debugEcho(1,60,"Return value - trkId: $trkId ");
            $result_array[$recordNo]["trkId"] = $trkId;                 // trkId = ID of the track in tbl_tracks
            
            if ($trkId == -1) // If track not exists
            {
                $result_array[$recordNo]["trackFound"] = 0;             // trackFound set to 0 (no)
                
                // ------------------------------------
                // Create new track if not yet exiting
                // ------------------------------------
                $trkId = insertNewTrack($conn,$trkId,$fileName,$fullFileName);   // Insert track based infos in gpx file
                debugEcho(1,71,"Return value - trkId: $trkId ");
                $result_array[$recordNo]["trkId"] = $trkId;             // update trkId with newly created trkId
            } else { // If track exists

                // ---------------------------------
                // Delete all existing track points
                // ---------------------------------
                $delTrkPt = delTrkPt($conn,$trkId);                     // Delete track points if already existing in DB 
                debugEcho(1,78,"Return value - delTrkPt: $delTrkPt ");                                             
            }
            
            // ------------------------------------------------
            // Insert new track points for each file processed
            // ------------------------------------------------
            $insTrkPt = insertTrackPoint($conn,$fullFileName,$trkId); // Insert new track points; returns number of trkPts inserted (-1 = error)
            debugEcho(1,85,"Return value - insTrkPt: $insTrkPt ");    
            $result_array[$recordNo]["noTrp"] = $insTrkPt;            // noTrp = Number of track points inserted

            // ----------------------------
            // Update tracks with gps info
            // ----------------------------
            $resUpdateTrack = updateTrack($conn,$fullFileName,$trkId);            // Insert new track points (returns 0 = OK / -1 = error)
            debugEcho(1,92,"Return value - resUpdateTrack: $resUpdateTrack ");    

            $recordNo++;                                              // Increased record number by 1
        }
    }
    
    // -------------------------------------
    // After all data is processed
    // -------------------------------------
    
    displayResultArray($result_array);                                // Displays log on screen
    
    $conn->close();                                                   // Close DB connection
    closedir($dirHandle);                                             // Close directory handle

    // ------------------------------------------------------
    // Function searches for trkId with Strava File name
    // ------------------------------------------------------
    function getTrackId($conn,$fileName,$fullFileName)
    {
        debugEcho(2,114,"Function getTrackId entered");

        $sql = "SELECT trkId FROM tbl_tracks ";                         // Search if a track with given gpx file name exists in db
        $sql .= "WHERE trkSourceFileName = '" . $fileName . "' ";
        $sql .= " LIMIT 1";

        debugEcho(5,123,"sql: $sql");

        if ($tableTrackConn = mysqli_query($conn, $sql))                // sends sql statement to db
        {
            while ($tableTrack = mysqli_fetch_object($tableTrackConn))  // loops through result of query (only 1 record expected)
            {
                $trkId = $tableTrack->trkId;                            // assigns trkId of found track to variable
                debugEcho(3,130,"Select trkId is true ($trkId)");
                return $trkId;                                          // trkId of found track returned
            }
                debugEcho(0,138,"No track found with Strava name $fileName");
                return -1;                                              // -1 = Error
        } else
        {
            debugEcho(0,143,"Error selecting trkId. Error Message: $conn->error");
            return -1;                                                  // -1 = Error
        }
    }

    // -----------------------------------------------------------
    // Update field track time and trkTrackName from table Tracks 
    // -----------------------------------------------------------
    function updateTrack($conn,$fullFileName,$trkId)
    {
        $gpx = simplexml_load_file($fullFileName);                      // Load XML structure
        $newTrackTime = $gpx->metadata->time;
        // ACTION turn function updTrkName off --> target is that tourdb is always in the lead
        $newTrackName = $gpx->trk->name;                                // Assign track name to variable
        $trackTime = strftime("%Y.%m.%d %H:%M:%S", strtotime($newTrackTime));   // format track time

        $sql =  "UPDATE `tourdb2`.`tbl_tracks` ";                       // create sql statement to update track gps start time and track name
        $sql .= "SET `trkGPSStartTime` = '$trackTime', ";
        $sql .= "`trkTrackName`= '$newTrackName' WHERE `trkId`=$trkId";
        
        debugEcho (5,326,"sql: $sql");
        if ($conn->query($sql) === TRUE) {
            debugEcho(3,328,"Track name and time updated in tbl_tracks");
            return 0;
        } else {
            debugEcho(0,331,"Error updating track name and time! Error Message: $conn->error");
            debugEcho(4,332,"sql: $sql");
            return -1;
        }
    }

    // ----------------------------------------------------------
    // Delete existing track points and insert new track points
    // ----------------------------------------------------------
    function delTrkPt($conn,$trkId) {
        debugEcho(2,175,"function delTrkPt entered");
        $sql = "DELETE FROM `tourdb2`.`tbl_trackpoints` ";              // Delete all track points for given trkID
        $sql .= "WHERE `tbl_trackpoints`.`tptTrackFID` = $trkId";

        debugEcho(5,180,"sql: $sql");

        if ($conn->query($sql) === TRUE) {
            debugEcho(4,183,"All track points for Track ID ($trkId) successfully deleted");
            return 0;
        } else {
            debugEcho(0,187,"Error deleting trkPt! Error Message: $conn->error");
            debugEcho(4,188,"sql: $sql");
            return -1; 
        }
    }

    // ----------------------------------------------------------
    // Create new track (for gpx files without existing track)
    // ----------------------------------------------------------
    function insertNewTrack($conn,$trkId,$fileName,$fullFileName)
    {
        $gpx = simplexml_load_file($fullFileName);                               // Load XML structure
        $newTrackTime = $gpx->metadata->time;                                    // Assign track time from gpx file to variable
        $trackTime = strftime("%Y.%m.%d %H:%M:%S", strtotime($newTrackTime));    // convert track time 
        
        debugEcho(2,203,"Function insertNewTrack entered");
        $sql = "INSERT INTO `tourdb2`.`tbl_tracks`";                             // Insert Source file name, gps start time and toReview flag
        $sql .= " (`trkSourceFileName`, `trkGPSStartTime`, `trkToReview`) VALUES "; 

        // trkSourceFileName
        $sql .= "('" . $fileName . "', ";                               // create value bracket statement
        $sql .= "'" . $trackTime . "', ";
        $sql .= "'1') ";                                                // trkToReview = 1 means that this track needs to be reviewed
                           
        if ($conn->query($sql) === TRUE)                                // run sql against DB
        {
            debugEcho(5,215,"sql: $sql");
            debugEcho(3,216,"New track inserted successfully");
        } else {
            debugEcho(0,218,"Error inserting trkPt: $conn->error");
            debugEcho(0,219,"sql: $sql");
            return -1;
        } 

        $sql = "SELECT max(`trkId`) FROM `tourdb2`.`tbl_tracks` ";      // Search for trkId of record just created

        if ($stmt = mysqli_prepare($conn, $sql)) 
        {
            mysqli_stmt_execute($stmt);                                 // execute select statement
            mysqli_stmt_bind_result($stmt, $trkId);                     // bind result variables

            while (mysqli_stmt_fetch($stmt)) {                          // Fetch result of sql statement (one result expeced)
                debugEcho(4,235,"sql: $sql");
                return $trkId;
            }
            mysqli_stmt_close($stmt);                                   // Close statement
        } else {
            debugEcho(0,242,"Error selecting max(trkId): $conn->error");
            debugEcho(4,243,"sql: $stmt");
            return -1;
        } 
    }

    // ----------------------------------------------------------
    // Insert track points into table
    // ----------------------------------------------------------
    function insertTrackPoint($conn,$fullFileName,$trkId) 
    {
        debugEcho(2,253,"Function insertTrackPoint entered");
        $tptNumber = 1;
        $gpx = simplexml_load_file($fullFileName);                      // Load XML structure
        $sql = "INSERT INTO `tourdb2`.`tbl_trackPoints`";               // create first part of insert statement 
        $sql .= " (`tptNumber`, `tptTrackFID`, `tptLat`, `tptLon`, ";
        $sql .= "  `tptEle`, `tptTime`) VALUES "; 

        $firstRec = 1;                                                  // flag first record as all other records need to be treated slightly different 

        foreach ($gpx->trk->trkseg->trkpt as $trkpt)                    // loop through each trkpt XML element in the gpx file
        {                  
            if ($firstRec == 1)                                         // if record is not first, a comma is written
                {
                $firstRec = 0;
            } else
            {
                $sql .= ",";
            }
            
            $sql .= "('" . $tptNumber . "', ";                          // write tptNumber - a continuous counter for the track points
            debugEcho(6,272,"tptNumber: $tptNumber");
                    
            $sql .= "'" . $trkId . "', ";                               // tptTrackFID - reference to the track
            debugEcho(6,276,"trkId: $trkId");
                        
            $sql .= "'" . $trkpt["lat"] . "', ";                        // tptLat - latitude value 
            $message = "lat: " . $trkpt["lat"];
            debugEcho(6,281,$message);
            
            $sql .= "'" . $trkpt["lon"] . "', ";                        // tptLon - longitude value
            $message = "lon: " . $trkpt["lon"];
            debugEcho(6,286,$message);

            $sql .= "'" . $trkpt->ele . "', ";                          // tptEle - elevation of track point
            debugEcho(6,290,"Elevation: $trkpt->ele");
            
            $sql .= "'" . strftime("%Y.%m.%d %H:%M:%S", strtotime($trkpt->time)) . "')";     // tptTime - time of track point
            debugEcho(6,294,"Time: $trkpt->time");
            
            $tptNumber++;                                               // increase track point counter by 1
        }            
                   
        if ($conn->query($sql) === TRUE) {                              // execute query
            debugEcho(6,300,"sql: $sql");
            debugEcho(3,301,"New track points inserted successfully");

            $resUpdateTrack = updateTrack($conn,$fullFileName,$trkId);                          // Insert new track points
            debugEcho(1,304,"Return value - resUpdateTrack: $resUpdateTrack ");    

            return $tptNumber-1;                                        // reduce tptNumber by une (as increase some line before)
        } else {
            debugEcho(6,308,"sql: $sql");
            debugEcho(0,309,"Error inserting trkPt! Error Message: $conn->error ");
            return -1;
        }    
    }

    // ----------------------------------------------------------
    // Function to display overall result
    // ----------------------------------------------------------
    function displayResultArray($result_array)
    {
        debugEcho(2,339,"Function displayResultArray entered");     
        echo "<br>********* IMPORT COMPLETED *********<br>";
        echo "<br>no;fileName;trkId;trackFound;noTrp<br>";
        $noNewTrks = 0;                                                 // variable for number of newly created tracks
        $noNewTrkPts = 0;                                               // variable for number of newly created track points
        $rows = count($result_array);                                   // variable for number of gpx files processed
        for ($i=0;$i<$rows;$i++) 
        {
            echo $result_array[$i]["no"]+1 . ";";                       // Nr of gps file processes
            echo $result_array[$i]["fileName"] . ";";                   // file name of gpx file
            echo $result_array[$i]["trkId"] . ";";                      // related track id (can be existing or created)
            echo $result_array[$i]["trackFound"] . ";";                 // If yes, the related track has already been existing
            echo $result_array[$i]["noTrp"] . "<br>";                   // number of track points created

            $noNewTrks = $noNewTrks + $result_array[$i]["trackFound"];  // increase counters
            $noNewTrkPts = $noNewTrkPts + $result_array[$i]["noTrp"];
        }

        $noNewTrks = $rows - $noNewTrks;                                // calculate number of newly created tracks
        echo "<br>Total file processed: $rows<br>";
        echo "Number of new tracks created: $noNewTrks <br>";
        echo "Number of track points inserted: $noNewTrkPts<br>";
    }

    // ----------------------------------------------------------
    // Function to display error messages on the screen
    // ----------------------------------------------------------
    function debugEcho($debug,$line,$message)
    {
        if ($GLOBALS['debugLevel'] >= $debug) 
        {
            echo "Line: $line - Message: $message<br>";
        }
    }
  ?>
	
</body>
</html>
