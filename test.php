<?php

$logbook = @fopen("./import/logbook/logbook.csv","r"); // Opens logbuchberge.csv file

$zaehler = 1;
$run = 1;

while (!feof($logbook))
{
    $zeile = fgets($logbook,1000);                                   // 2. Parameter: Anzahl Zeichen?
    echo "25: Zeile >" . $zeile . "< <br>";
    //while(ord(substr($zeile, strlen($zeile)-1)) == 13           // solange am Zeilenende ein Carrage Return steht ODER
    //        || ord(substr($zeile, strlen($zeile)-1)) == 10 )    // ein Line Feed
    //{
        echo "===========<br>";
        echo "30: Run: $run <br>";
        $run++;
        $zeile = substr($zeile, 0, strlen($zeile)-1);           // Ausgabe der Zeile ohne Carrage Return oder Line Feed
        echo "33: Zeile >>>" . $zeile . "<<< <br>";

    //}
}
fclose($logbook);
?>
