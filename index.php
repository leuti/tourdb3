<!DOCTYPE HTML>
<html>
<head>
	<meta charset="utf-8">
	<title>tourdb3</title>
</head>
<body>
  <?php
      // Open directory where the Strave GPX are stored
      $verz = "C:\Users\Danny\OneDrive\Dokumente\_tourdb\gps\Strava";   // ACTION: Move to include file
      chdir($verz);   
      $dirHandle = opendir($verz);   // Open handle
      
      echo "<h2>Directory $verz</h2>";
            
      // Loop through each file in directory
      while ($fileName = readdir($dirHandle))
      {
        $fullFileName = $verz . "\\" . $fileName;
        
        // Perform following statements only for files (an not for . and ..)
        if (is_file ($fullFileName)) 
        {
          $gpx = simplexml_load_file($fullFileName);  // Load XML structure
          echo " Track Name: " . $gpx->trk->name;
          echo " --> Anzahl trk: " . count($gpx->trk) . "<br>";   // ACTION: Through error message when number of trk > 1 
          /*foreach ($gpx->trk->trkseg->trkpt as $trkpt)
          {
            echo "lat: " . $trkpt["lat"] . "<br>";
            echo "lon: " . $trkpt["lon"] . "<br>";
            echo "Time: $trkpt->time<br><br>";
            echo "Elevation: $trkpt->ele<br><br>";
          }*/
         }
      } 
      
      /* Schliesst Handle */
      closedir($dirHandle);
  ?>
	
</body>
</html>
