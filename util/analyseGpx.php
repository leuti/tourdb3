<!DOCTYPE HTML><html><head>	<meta charset="utf-8"><title>import gpx</title></head>
<body>
  <?php
      //---------------------------------------------------------------------------------------------
      // PHP script writting the date, file name, track name and number of track points 
      // for each track into file
      // Echos  number of gpx file and the number of track points  
      // Source directory: git_projects\tourdb3\import\gpx
      //
      // Utility
      //
      // Created: 7.12.2017 - Daniel Leutwyler
      //---------------------------------------------------------------------------------------------

      // Test Cases
      // Compare against number of records in DB
      // * Defect: äöü are not correctly displayed

      // -----------------------------------
      // Set variables and parameters    
      date_default_timezone_set('Europe/Zurich');    
      $debug = 1;
      $countFiles = 0;
      $countTrkPoints = 0;
      
      // Open directory where the Strave GPX are stored
      $verz = dirname(__FILE__) . "\..\import\gpx";   
      chdir($verz);   
      $dirHandle = opendir($verz);                                        // Open handle
      
      $out_file_name = dirname(__FILE__) . "\..\out\analyseGpx.csv";

      $out_file = @fopen($out_file_name,"w");   
      echo "<h2>Directory $verz</h2>";
      
      // Loop through each file in directory
      while ($fileName = readdir($dirHandle))
      {
        $fullFileName = $verz . "\\" . $fileName;
        
        // Perform following statements only for gpx files
        if (substr($fileName, strlen($fileName)-4) == ".gpx") 
        {
          $gpx = simplexml_load_file($fullFileName);  // Load XML structure
          
          fputs($out_file, "D" . strftime("%Y%m%d", strtotime($gpx->metadata->time)) . ";");
          fputs($out_file, strftime("%d.%m.%Y", strtotime($gpx->metadata->time)) . ";");
          //fwrite($out_file, mb_convert_encoding( $fileName, 'UTF-16LE', 'UTF-8'). ";");            // mb_convert_encoding makes sure äöü are correctly displayed
          fputs($out_file, $fileName . ";"); 
          fputs($out_file, '"');
          fputs($out_file, $gpx->trk->name);
          fputs($out_file, '";');
          fputs($out_file, count($gpx->trk->trkseg->trkpt) ."\r\n");

          $countFiles++;                                                      // Increase counter for number of files
          $countTrkPoints=$countTrkPoints+count($gpx->trk->trkseg->trkpt);    // Increase counter for number of track points
          
          // Echo details if debug is > 1
          if ($debug >1) {
            echo "D" . strftime("%Y%m%d", strtotime($gpx->metadata->time)) . ";";
            echo $fileName . ";";
            echo $gpx->trk->name . ";";
            echo count($gpx->trk->trkseg->trkpt);
            echo "<br>";    
          }
        }
      } 
      if ($debug >0) {
        echo "$countFiles Files are available to import and $countTrkPoints Track Points in Total<br>";    
      }
      /* Schliesst Handle */
      fclose($out_file);
      closedir($dirHandle);
  ?>
	
</body>
</html>
