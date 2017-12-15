<!DOCTYPE HTML><html><head>	<meta charset="utf-8"><title>import gpx</title></head>
<body>
  <?php
      // Open directory where the Strave GPX are stored
      date_default_timezone_set('Europe/Zurich');    
      $debug = 1;
      $countFiles = 0;
      $countTrkPoints = 0;
      $verz = dirname(__FILE__) . "\import\gpx";   // ACTION: Move to include file
      chdir($verz);   
      $dirHandle = opendir($verz);   // Open handle
      
      $out_file_name = dirname(__FILE__) . "\out\analyseGpx.csv";

      $out_file = @fopen($out_file_name,"w");   // was @fopen
      echo "<h2>Directory $verz</h2>";
      echo "TrackTime;FileName;TrackName;NoTrackPoints<br>";      
      // Loop through each file in directory
      while ($fileName = readdir($dirHandle))
      {
        $fullFileName = $verz . "\\" . $fileName;
        
        // Perform following statements only for files (an not for . and ..)
        if (is_file ($fullFileName)) 
        {
          $gpx = simplexml_load_file($fullFileName);  // Load XML structure
          
          fputs($out_file, "D" . strftime("%Y%m%d", strtotime($gpx->metadata->time)) . ";");
          fputs($out_file, strftime("%d.%m.%Y", strtotime($gpx->metadata->time)) . ";");
          //fwrite($out_file, mb_convert_encoding( $fileName, 'UTF-16LE', 'UTF-8'). ";");            // mb_convert_encoding makes sure äöü are correctly displayed
          fputs($out_file, $fileName . ";"); 
          fputs($out_file, '"');
          //fwrite($out_file, mb_convert_encoding( $gpx->trk->name, 'UTF-16LE', 'UTF-8'). ";");
          fputs($out_file, $gpx->trk->name);
          fputs($out_file, '";');
          fputs($out_file, count($gpx->trk->trkseg->trkpt) ."\r\n");

          $countFiles++;
          $countTrkPoints=$countTrkPoints+count($gpx->trk->trkseg->trkpt);
          
          if ($debug >1) {
            echo "D" . strftime("%Y%m%d", strtotime($gpx->metadata->time)) . ";";
            echo $fileName . ";";
            echo $gpx->trk->name . ";";
            echo count($gpx->trk->trkseg->trkpt);
            echo "<br>";    
          }
          /*  foreach ($gpx->trk->trkseg->trkpt as $trkpt) // ACTION: Through error message when number of trk / trkseg > 1
          {
            echo "lat: " . $trkpt["lat"] . "<br>";
            echo "lon: " . $trkpt["lon"] . "<br>";
            echo "Time: $trkpt->time<br><br>";
            echo "Elevation: $trkpt->ele<br><br>";
          }*/
        }
      } 
      if ($debug >0) {
        echo "$countFiles Files are available to import and $countTrkPoints in Total<br>";    
      }
      /* Schliesst Handle */
      fclose($out_file);
      closedir($dirHandle);
  ?>
	
</body>
</html>
