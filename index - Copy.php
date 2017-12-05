<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8"></head><body>
<?php>
  $verz = "C:\Users\Danny\OneDrive\Dokumente\_tourdb\gps\Strava";
  chdir $verz;

  echo "<h2>Verzeichnis $verz</h2>";
  echo "<table border='1'";

  /* Überschrift */
  echo "<td>Name</td>";
  echo "<td>Datei /<br>Verz.</td>";

  /* Öffnet Handle */
  //$handle = opendir($verz);

  /* Liste alle Objktnamen */
  /* while ($dname = readdir($handle))
  {
    echo "<tr>";
    echo "<td>$dname</td>";

    /* Datei oder Verzeichnis */
    /*if (is_file($dname))
      echo "<td>D</td>";
    elseif (is_dir($dname))
      echo "<td>V</td>";
    else
      echo "<td>&nbsp;</td>";
  } */
  /* Schliesst Handle */
  //closedir($handle);
?>
</body></html>
