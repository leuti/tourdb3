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
