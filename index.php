<!DOCTYPE HTML>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<meta http-equiv="Cache-control" content="no-cache">
  <meta http-equiv="Expires" content="-1">
	<title>tourdb3</title>
</head>
<body>

  <?php
      $verz = "C:\Users\Danny\OneDrive\Dokumente\_tourdb\gps\Strava";
      chdir($verz);
      $dirHandle = opendir($verz);   // Open handle
      
      echo "<h2>Directory $verz</h2>";
            
      /* Liste alle Objktnamen */
      while ($fileName = readdir($dirHandle))
      {
        $fullFileName = $verz . "\\" . $fileName;
        
        if (is_file ($fullFileName)) 
        {
          echo "File Name: $fileName";
          //echo "<h3>Processing $fileName</h3>";
          //echo "<p>Hallo</p>";
          $gpx = simplexml_load_file($fullFileName);
          echo " Track Name: " . $gpx->trk->name . "<br>";
          //echo "<p>Tschüss</p>";
          fclose($fileName);
        }
      } 
      
      /* Schliesst Handle */
      closedir($dirHandle);
  ?>
	
</body>
</html>
