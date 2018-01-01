<?php
// ---------------------------------------------------------------------------------------------
// PHP script loading gpx files into table track points. When no track exists, a simple track
// record is created. Before loading the gpx track points all previous track points are deleted. 
// When a gpx for an existing track is imported, the track time is updated.
//
// This script is intended for regular usage
//
// Parameters:
// filename: name of file to be uploaded (one at a time); file is expected at import/gpx or import/kml
// filetype: type of file to be imported (gpx or kml)
//
// Actions:
// * error handling if filename/filetype is empty
//
// Created: 13.12.2017 - Daniel Leutwyler
// ---------------------------------------------------------------------------------------------

// -----------------------------------
// Set variables and parameters
include("./config.inc.php");                                        // include config file
date_default_timezone_set('Europe/Zurich');                         // must be set when using time functions

$debugLevel = 0;                                                    // 0 = off, 6 = all
$recordNo = 0;                                                      // No of gpx files processed
$loopSize = 5000;                                                   // Number of trkPts inserted in one go

// Open file for import log
$importGpxLog = dirname(__FILE__) . "\..\out\importGpx.log";        // Assign file location
$logFile = @fopen($importGpxLog,"w");                               // open log file handler 
fputs($logFile, "importGpx.php started: " . date("Ymd-H:i:s", time()) . "\r\n");    

// Assign input parameters
if(isset($_POST["filename"]) && $_POST["filename"] != ''){
    $filename = $_POST["filename"]; 
} else{
                                                                    // Potential error handling if filename is empty
};

if(isset($_POST["filetype"]) && $_POST["filetype"] != ''){
    $filename = $_POST["filetype"]; 
} else{
                                                                    // Potential error handling if filetype is empty
};

// -----------------------------------------
// Main routine
// -----------------------------------------



// Loop through directory with GPX files
while ($fileName = readdir($dirHandle))                             // Loop through each file in directory
{ 
    $fullFileName = $verz . "\\" . $fileName;                       // Generate absolute file name
            
    // Perform following action for gpx files
    if (substr($fileName, strlen($fileName)-4) == ".gpx")           // Perform following statements only for gpx files
    {     
        if ($debugLevel>1) fputs($logFile, "Line 61 - FILENAME: $fileName\r\n");
        $result_array[$recordNo]["no"] = $recordNo;                 // No = Record counter (= number of gpx files) 
        $result_array[$recordNo]["fileName"] = $fileName;           // fileName = gpx file name
        $result_array[$recordNo]["trackFound"] = 1;                 // trackFound = 1=yes, 0=no (new track will be created)

        // ----------------------------------------------------
        // Check if a track exists with given Strava File Name
        // ----------------------------------------------------
        $trkId = getTrackId($conn,$fileName,$fullFileName);         // function checks if track exists (returns -1 if not)
        if ($debugLevel>1) fputs($logFile, "Line 70 - getTrackId: Return value - trkId: $trkId\r\n");
        $result_array[$recordNo]["trkId"] = $trkId;                 // trkId = ID of the track in tbl_tracks
        
        if ($trkId == -1) // If track not exists
        {
            $result_array[$recordNo]["trackFound"] = 0;             // trackFound set to 0 (no)
            
            // ------------------------------------
            // Create new track if not yet exiting
            // ------------------------------------
            $trkId = insertNewTrack($conn,$trkId,$fileName,$fullFileName);   // Insert track based infos in gpx file
            if ($debugLevel>1) fputs($logFile, "Line 81 - insertNewTrack: Return value - trkId: $trkId\r\n");
            $result_array[$recordNo]["trkId"] = $trkId;             // update trkId with newly created trkId
        } else { // If track exists

            // ---------------------------------
            // Delete all existing track points
            // ---------------------------------
            $delTrkPt = delTrkPt($conn,$trkId);                     // Delete track points if already existing in DB 
            if ($debugLevel>1) fputs($logFile, "Line 89 - delTrkPt: Return value - delTrkPt: $delTrkPt\r\n");                                             
        }
        
        // ------------------------------------------------
        // Insert new track points for each file processed
        // ------------------------------------------------
        $insTrkPt = insertTrackPoint($conn,$fullFileName,$trkId);   // Insert new track points; returns number of trkPts inserted (-1 = error)
        if ($debugLevel>1) fputs($logFile, "Line 96 - insertTrackPoint: Return value - insTrkPt: $insTrkPt\r\n");    
        $result_array[$recordNo]["noTrp"] = $insTrkPt;              // noTrp = Number of track points inserted

        // ----------------------------
        // Update tracks with gps info
        // ----------------------------
        $resUpdateTrack = updateTrack($conn,$fullFileName,$trkId);  // Insert new track points (returns 0 = OK / -1 = error)
        if ($debugLevel>6) fputs($logFile, "Line 103 - updateTrack: Return value - resUpdateTrack: $resUpdateTrack\r\n");    

        $recordNo++;                                                // Increased record number by 1
    }
}

// -------------------------------------
// After all data is processed
// -------------------------------------

displayResultArray($result_array);                                  // Displays log on screen
fputs($logFile, "importGpx.php finished: " . date("Ymd-H:i:s", time()) . "\r\n");    

$conn->close();                                                     // Close DB connection
closedir($dirHandle);                                               // Close directory handle

// ------------------------------------------------------
// Function searches for trkId with Strava File name
// ------------------------------------------------------
function getTrackId($conn,$fileName,$fullFileName)
{
    if ($GLOBALS['debugLevel']>2) fputs($GLOBALS['logFile'], "Line 124 - Function getTrackId entered\r\n");

    $sql = "SELECT trkId FROM tbl_tracks ";                         // Search if a track with given gpx file name exists in db
    $sql .= "WHERE trkSourceFileName = '" . $fileName . "' ";
    $sql .= " LIMIT 1";

    if ($GLOBALS['debugLevel']>5) fputs($GLOBALS['logFile'], "Line 130 - sql: $sql\r\n");

    if ($tableTrackConn = mysqli_query($conn, $sql))                // sends sql statement to db
    {
        while ($tableTrack = mysqli_fetch_object($tableTrackConn))  // loops through result of query (only 1 record expected)
        {
            $trkId = $tableTrack->trkId;                            // assigns trkId of found track to variable
            if ($GLOBALS['debugLevel']>3) fputs($GLOBALS['logFile'], "Line 137 - Select trkId is true ($trkId)\r\n");
            return $trkId;                                          // trkId of found track returned
        }
        if ($GLOBALS['debugLevel']>0) fputs($GLOBALS['logFile'], "Line 140 - No track found with Strava name $fileName\r\n");
            return -1;                                              // -1 = Error
    } else
    {
        if ($GLOBALS['debugLevel']>0) fputs($GLOBALS['logFile'], "Line 144 - Error selecting trkId. Error Message: $conn->error\r\n");
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
    $trackName = $gpx->trk->name;                                // Assign track name to variable
    $trackTime = strftime("%Y.%m.%d %H:%M:%S", strtotime($newTrackTime));   // format track time

    $sql =  "UPDATE `tourdb2`.`tbl_tracks` ";                       // create sql statement to update track gps start time and track name
    $sql .= "SET `trkGPSStartTime` = '$trackTime', ";
    $sql .= "`trkTrackName` = '$trackName' ";
    $sql .= "WHERE `trkId`=$trkId";
    
    if ($GLOBALS['debugLevel']>5) fputs($GLOBALS['logFile'], "Line 163 - sql: $sql\r\n");
    if ($conn->query($sql) === TRUE) {
        if ($GLOBALS['debugLevel']>3) fputs($GLOBALS['logFile'], "Line 165 - Track name and time updated in tbl_tracks\r\n");
        return 0;
    } else {
        if ($GLOBALS['debugLevel']>0) fputs($GLOBALS['logFile'], "Line 168 - Error updating track name and time! Error Message: $conn->error\r\n");
        if ($GLOBALS['debugLevel']>2) fputs($GLOBALS['logFile'], "Line 169 - sql: $sql\r\n");
        return -1;
    }
}

// ----------------------------------------------------------
// Delete existing track points and insert new track points
// ----------------------------------------------------------
function delTrkPt($conn,$trkId) {
    if ($GLOBALS['debugLevel']>2) fputs($GLOBALS['logFile'], "Line 178 - function delTrkPt entered\r\n");
    $sql = "DELETE FROM `tourdb2`.`tbl_trackpoints` ";              // Delete all track points for given trkID
    $sql .= "WHERE `tbl_trackpoints`.`tptTrackFID` = $trkId";

    if ($GLOBALS['debugLevel']>5) fputs($GLOBALS['logFile'], "Line 182 - sql: $sql\r\n");

    if ($conn->query($sql) === TRUE) {
        if ($GLOBALS['debugLevel']>4) fputs($GLOBALS['logFile'], "Line 184 - All track points for Track ID ($trkId) successfully deleted\r\n");
        return 0;
    } else {
        if ($GLOBALS['debugLevel']>0) fputs($GLOBALS['logFile'], "Line 188 - Error deleting trkPt! Error Message: $conn->error\r\n");
        if ($GLOBALS['debugLevel']>0) fputs($GLOBALS['logFile'], "Line 189 - sql: $sql\r\n");
        return -1; 
    }
}

// ----------------------------------------------------------
// Create new track (for gpx files without existing track)
// ----------------------------------------------------------
function insertNewTrack($conn,$trkId,$fileName,$fullFileName)
{
    if ($GLOBALS['debugLevel']>4) fputs($GLOBALS['logFile'], "Line 199 - Function insertNewTrack entered\r\n");
    
    $gpx = simplexml_load_file($fullFileName);                      // Load XML structure
    $newTrackTime = $gpx->metadata->time;                           // Assign track time from gpx file to variable
    $trackTime = strftime("%Y.%m.%d %H:%M:%S", strtotime($newTrackTime));    // convert track time 
    $DateBegin = strftime("%Y.%m.%d", strtotime($newTrackTime));    // convert track time 
    $DateFinish = strftime("%Y.%m.%d", strtotime($newTrackTime));   // convert track time 
    $trackName = $gpx->trk->name;                                   // Track name
            
    $sql = "INSERT INTO `tourdb2`.`tbl_tracks`";                    // Insert Source file name, gps start time and toReview flag
    $sql .= " (`trkSourceFileName`, `trkGPSStartTime`, `trkDateBegin`, `trkDateFinish`, `trkToReview`) VALUES "; 

    // trkSourceFileName
    $sql .= "('" . $fileName . "', ";                               // create value bracket statement
    $sql .= "'" . $trackName . "', ";
    $sql .= "'" . $trackTime . "', ";
    $sql .= "'" . $DateBegin . "', ";
    $sql .= "'" . $DateFinish . "', ";
    $sql .= "'1') ";                                                // trkToReview = 1 means that this track needs to be reviewed
                        
    if ($conn->query($sql) === TRUE)                                // run sql against DB
    {
        if ($GLOBALS['debugLevel']>5) fputs($GLOBALS['logFile'], "Line 219 - sql: $sql\r\n");
        if ($GLOBALS['debugLevel']>4) fputs($GLOBALS['logFile'], "Line 220 - New track inserted successfully\r\n");
    } else {
        if ($GLOBALS['debugLevel']>0) fputs($GLOBALS['logFile'], "Line 222 - Error inserting trkPt: $conn->error\r\n");
        if ($GLOBALS['debugLevel']>4) fputs($GLOBALS['logFile'], "Line 223 - sql: $sql\r\n");
        return -1;
    } 

    $sql = "SELECT max(`trkId`) FROM `tourdb2`.`tbl_tracks` ";      // Search for trkId of record just created

    if ($stmt = mysqli_prepare($conn, $sql)) 
    {
        mysqli_stmt_execute($stmt);                                 // execute select statement
        mysqli_stmt_bind_result($stmt, $trkId);                     // bind result variables

        while (mysqli_stmt_fetch($stmt)) {                          // Fetch result of sql statement (one result expeced)
            if ($GLOBALS['debugLevel']>4) fputs($GLOBALS['logFile'], "Line 235 - sql: $sql\r\n");
            return $trkId;
        }
        mysqli_stmt_close($stmt);                                   // Close statement
    } else {
        if ($GLOBALS['debugLevel']>0) fputs($GLOBALS['logFile'], "Line 240 - Error selecting max(trkId): $conn->error\r\n");
        if ($GLOBALS['debugLevel']>4) fputs($GLOBALS['logFile'], "Line 241 - sql: $stmt\r\n");
        return -1;
    } 
}

// ----------------------------------------------------------
// Insert track points into table
// ----------------------------------------------------------
function insertTrackPoint($conn,$fullFileName,$trkId) 
{
    if ($GLOBALS['debugLevel']>2) fputs($GLOBALS['logFile'], "Line 251 - Function insertTrackPoint entered\r\n");
    $tptNumber = 1;                                                 // Set counter for tptNumber to 1
    $loopCumul = $GLOBALS['loopSize'];                              // loopCumul is the sum of loop sizes processed
    $gpx = simplexml_load_file($fullFileName);                      // Load XML structure
    
    $totalTrkPts = count($gpx->trk->trkseg->trkpt);                 // total number of track points in file
    $loop = 0;                                                      // set current loop to 0 (only required for debug purposes)

    $sqlBase = "INSERT INTO `tourdb2`.`tbl_trackPoints`";           // create first part of insert statement 
    $sqlBase .= " (`tptNumber`, `tptTrackFID`, `tptLat`, `tptLon`, ";
    $sqlBase .= "  `tptEle`, `tptTime`) VALUES "; 
    
    $firstRec = 1;                                                  // flag first record as all other records need to be treated slightly different 

    foreach ($gpx->trk->trkseg->trkpt as $trkpt)                    // loop through each trkpt XML element in the gpx file
    {                  
        if ($firstRec == 1)                                         // if record is not first, a comma is written
            {
                $sql = $sqlBase;                                    // Add first part of sql to variable $sql
                $firstRec = 0;
        } else
        {
            $sql .= ",";
        }
        
        $sql .= "('" . $tptNumber . "', ";                          // write tptNumber - a continuous counter for the track points
        $sql .= "'" . $trkId . "', ";                               // tptTrackFID - reference to the track         
        $sql .= "'" . $trkpt["lat"] . "', ";                        // tptLat - latitude value 
        $sql .= "'" . $trkpt["lon"] . "', ";                        // tptLon - longitude value
        $sql .= "'" . $trkpt->ele . "', ";                          // tptEle - elevation of track point
        $sql .= "'" . strftime("%Y.%m.%d %H:%M:%S", strtotime($trkpt->time)) . "')";     // tptTime - time of track point
        
        if($tptNumber == $loopCumul || $tptNumber == $totalTrkPts)  // If current loop size or last track is reached
        {        
            $loop++;
            if ($GLOBALS['debugLevel']>2) fputs($GLOBALS['logFile'], "Line 286 - loop: $loop\r\n");
            
            if ($conn->query($sql) === TRUE) {                      // execute query
                if ($GLOBALS['debugLevel']>6) fputs($GLOBALS['logFile'],"Line 289 - Sql: " . $sqldebug . "\r\n"); 
                if ($GLOBALS['debugLevel']>1) fputs($GLOBALS['logFile'],"Line 290 - New track points inserted successfully\r\n");
                $loopCumul = $loopCumul + $GLOBALS['loopSize'];     // Raise current loop size by overall loop size
                $firstRec = 1;                                      // Next record will be 'first'
                
            } else {
                if ($GLOBALS['debugLevel']>2) fputs($GLOBALS['logFile'],"Line 295 - Sql: " . $sql); 
                if ($GLOBALS['debugLevel']>1) fputs($GLOBALS['logFile'],"Line 296 - Error inserting trkPt! Error Message: $conn->error\r\n");
                return -1;
            }
        }
        
        $tptNumber++;                                               // increase track point counter by 1
        
    }
    $resUpdateTrack = updateTrack($conn,$fullFileName,$trkId);      // Update track with track time      
    if ($GLOBALS['debugLevel']>1) fputs($GLOBALS['logFile'],"Line 305 - Return value - resUpdateTrack: $resUpdateTrack \r\n");    
    return $tptNumber-1;                                            // reduce tptNumber by one (as increase some line before)
}

// ----------------------------------------------------------
// Function to display overall result
// ----------------------------------------------------------
function displayResultArray($result_array)
{
    if ($GLOBALS['debugLevel']>2) fputs($GLOBALS['logFile'], "Line 314 - Function displayResultArray entered\r\n");     
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

<!DOCTYPE HTML><html><head>	<meta charset="utf-8"><title>import gpx</title></head>
<body>
  <?php
    $debug = 3;
    $count_err = 0;
    $count_success = 0;
    $count_total = 0;
    date_default_timezone_set('Europe/Zurich'); 
    include("./config.inc.php");                                        // include config file
  
    $err_file_name = dirname(__FILE__) . "\out\importGpxErrors.csv";
    $err_file = @fopen($err_file_name,"w");

    $verz = dirname(__FILE__) . "\\import\\gpx";                       // Open directory where the Strave GPX are stored
    chdir($verz);   
    $dirHandle = opendir($verz);                                        // Open handle
    
    if ($debug >0) echo "<h2>Directory $verz</h2>";
    
    // Loop through directory with GPX files
    while ($fileName = readdir($dirHandle))                             // Loop through each file in directory
    {
      $fullFileName = $verz . "\\" . $fileName;
      
      // Perform following action for files
      if (is_file ($fullFileName))                                      // Perform following statements only for files (an not for . and ..)
      {
        if ($debug >1) echo "<br>Filename: $fileName;";
        $count_total++;
        // Search if a track with given Strava File Name exists
        $sql = "SELECT trkId FROM tbl_tracks ";
        $sql .= "WHERE trkStravaFileName = '" . $fileName . "' ";
        $sql .= " LIMIT 1";
        
        $tableTrackConn = mysqli_query($conn, $sql);
        
        if ($debug >2) echo "sql: $sql<br>";                
        // When track existed already in DB
        //echo "tableTrackRecs: $tableTrackRecs";
        //if ($tableTrackRecs>0) // ACTION: Funktioniert dies wenn keine Track Points vorhanden sind?
        if ($tableTrackConn = mysqli_query($conn, $sql))
        {
          while ($tableTrack = mysqli_fetch_object($tableTrackConn))
          {
            //echo "select hat true geliefert<br>";
          
            $tptNumber = 1;                                               // Check location of statement
            $trkId = $tableTrack->trkId;

            if ($debug >1) echo "trkId: $trkId;";
            
            // Delete existing track points and insert new track points
            $sql_del = "DELETE FROM `tourdb2`.`tbl_trackpoints` ";
            $sql_del .= "WHERE `tbl_trackpoints`.`tptTrackFID` = $trkId";

            if ($debug >2) echo "sql_del: $sql_del<br>";

            if ($conn->query($sql_del) === TRUE) {
              if ($debug >2) echo "All track points for Track ID ($trkId) successfully deleted<br>";
            } else {
              echo "Error: " . $sql_del . "<br>" . $conn->error;
            }
          
            $gpx = simplexml_load_file($fullFileName);                      // Load XML structure
            $tptInsertStm = "INSERT INTO `tourdb2`.`tbl_trackPoints`";
            $tptInsertStm .= " (`tptNumber`, `tptTrackFID`, `tptLat`, `tptLon`, ";
            $tptInsertStm .= "  `tptEle`, `tptTime`) VALUES "; 

            $firstRec = 1;
            //echo "firstRec set<br>";
            foreach ($gpx->trk->trkseg->trkpt as $trkpt) // ACTION: Through error message when number of trk / trkseg > 1
            {                  
              //echo "foreach started<br>";
              if ($firstRec == 1) 
                {
                  //echo "firstRec $firstRec<br>";
                  $firstRec = 0;
                } else
                {
                  //echo "firstRec $firstRec<br>";
                  $tptInsertStm .= ",";
                }
              // tptNumber
              $tptInsertStm .= "('" . $tptNumber . "', ";
              if ($debug >3) echo "tptNumber: $tptNumber<br>";
                    
              // tptTrackFID
              $tptInsertStm .= "'" . $trkId . "', ";
              if ($debug >3) echo "trkId: $trkId<br>";
              
              // tptLat 
              $tptInsertStm .= "'" . $trkpt["lat"] . "', ";
              if ($debug >3) echo "lat: " . $trkpt["lat"] . "<br>";

              // tptLon
              $tptInsertStm .= "'" . $trkpt["lon"] . "', ";
              if ($debug >3) echo "lon: " . $trkpt["lon"] . "<br>";

              // tptEle
              $tptInsertStm .= "'" . $trkpt->ele . "', ";
              if ($debug >3) echo "Elevation: $trkpt->ele<br><br>";

              // tptTime
              $tptInsertStm .= "'" . strftime("%Y.%m.%d %H:%M:%S", strtotime($trkpt->time)) . "')";
              if ($debug >3) echo "Time: $trkpt->time<br><br>";

              $tptNumber++;
              if ($debug >3) echo "tptInsertStm: $tptInsertStm<br><br>";
            }
            if ($conn->query($tptInsertStm) === TRUE) {
              if ($debug >2) echo "tptInsertStm: $tptInsertStm<br>New record created successfully<br>";
              $count_success++;
              if ($debug >1) echo "Success Count: $count_success;";
              if ($debug >1) echo "Total Count: $count_total;";
              $is_success = 1;
            } else {
              //echo "Error: " . $tptInsertStm . "<br>" . $conn->error . "<br>";
              $count_err++;
              if ($debug >1) echo "Error Count: $count_err;";
              if ($debug >1) echo "Total Count: $count_total;";
              fputs($err_file, "$fileName \r\n");
            }
          }
          
          if ($is_success == 1)
          {
            $is_success = 0;
          } else {
            echo "Kein Track;";
            fputs($err_file, "$fileName \r\n");
            $count_err++;
            if ($debug >1) echo "Error Count: $count_err;";
            if ($debug >1) echo "Total Count: $count_total;";
            $is_success = 1;
          }

        } else
        {
          echo "No track found in DB for $fileName;";
          $count_err++;
          if ($debug >1) echo "Error Count: $count_err;";
          if ($debug >1) echo "Total Count: $count_total;";
        }
      }
      //if ($debug >1) echo ";";
      // trkTime ==> ACTION: Write to Track
      // $tptTime = strtotime($gpx->metadata->time);
      // if ($debug >2) echo "trkTime: $tptTime";
    }
       
    echo "$count_total Zeilen verarbeitet ($count_success erfolgreich eingefügt / $count_err Fehler) <br>"; 
    if ($debug >2) echo "done<br>";
    
    $conn->close();
    closedir($dirHandle);
  ?>
	
</body>
</html>
