<?php
//--------------------------------------------
// PHP script converting logbuchberge.csv file
// and loading records to database tbltracks
//--------------------------------------------

date_default_timezone_set('Europe/Zurich');   // Required?
include("./config.inc.php");  //include config file

$logbook = @fopen("./import/logbook/logbook.csv","r"); // Opens logbuchberge.csv file
if(!$logbook) 
{
    exit("Datei nicht gefunden");
}
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

$zaehler = 1;

while (!feof($logbook))
{
    $zeile = fgets($logbook);                                   // 2. Parameter: Anzahl Zeichen?
    while(ord(substr($zeile, strlen($zeile)-1)) == 13           // solange am Zeilenende ein Carrage Return steht ODER
            || ord(substr($zeile, strlen($zeile)-1)) == 10 )    // ein Line Feed
    {
        $zeile = substr($zeile, 0, strlen($zeile)-1);           // Ausgabe der Zeile ohne Carrage Return oder Line Feed

        if(!(feof($logbook) && $zeile == ""))
        {
            $worte = explode(",", $zeile);                          // Zeile in Felder aufteilen
            
            $trkLogbookId = $worte[1];                              // column ID
            echo "trkLogbookId: $trkLogbookId <br>";
            $trkPeakRef = $worte[2];                                // column RefGipfel
            $trkStravaFileName = $worte[3];                         // column FileName Strava
            $trkDateBegin = $worte[4];                              // column Datum Start
            $trkDateFinish = $worte[5];                             // column Datum End
            $trkSaison = $worte[8];                                 // column Saison
            $trkTyp = $worte[10];                                   // column Art
            $trkSubType = $worte[11];                               // column Unterart 
            $trkOrg = $worte[12];                                   // column Org
            $trkTarget = $worte[16];                                // column Ziel
            $trkRoute = $worte[17];                                 // column Ziel Typ
            $trkOvernightLoc = $worte[18];                          // column Route
            $trkParticipants = "Teilnehmer";                        // column Teilnehmer (Zusammensetzen)
            $trkEvent = $worte[25];                                 // column Anlass
            $trkRemarks = $worte[26];                               // column Bemerkung
            $trkDistance = $worte[27];                              // column Distanz
            $trkTimeToTarget = $worte[28];                          // column Dauer
            $trkGrade = $worte[29];                                 // column Schwierigkeit
            $trkMeterUp = $worte[30];                               // column Aufstieg
            $trkMeterDown = $worte[31];                             // column Abstieg
            
            // Create Select Statement
            $sql = "INSERT INTO `tourdb3`.`tbl_tracks` (`trkId`, ";
            $sql .= "`trkLogbookId`, `trkStravaFileName`, `trkPeakRef`, `trkDateBegin`, ";
            $sql .= "`trkDateFinish`, `trkGPSStartTime`, `trkSaison`, `trkTyp`, `trkSubType`, ";
            $sql .= "`trkOrg`, `trkTarget`, `trkRoute`, `trkOvernightLoc`, `trkParticipants`, ";
            $sql .= "`trkEvent`, `trkRemarks`, `trkDistance`, `trkTimeToTarget`, `trkTimeToEnd`, ";
            $sql .= "`trkGrade`, `trkMeterUp`, `trkMeterDown`) VALUES (NULL, ";
            $sql .= "'" . $trkLogbookId . "', ";
            $sql .= "'" . $trkStravaFileName . "', "; 
            $sql .= "'" . $trkPeakRef . "', "; 
            $sql .= "'" . $trkDateBegin . "', ";
            $sql .= "'" . $trkDateFinish . "', "; 
            $sql .= "'" . $trkGPSStartTime . "', "; 
            $sql .= "'" . $trkSaison . "', "; 
            $sql .= "'" . $trkTyp . "', "; 
            $sql .= "'" . $trkSubType . "', ";
            $sql .= "'" . $trkOrg . "', "; 
            $sql .= "'" . $trkTarget . "', "; 
            $sql .= "'" . $trkRoute . "', "; 
            $sql .= "'" . $trkOvernightLoc . "', "; 
            $sql .= "'" . $trkParticipants . "', "; 
            $sql .= "'" . $trkEvent . "', "; 
            $sql .= "'" . $trkRemarks . "', "; 
            $sql .= "'" . $trkDistance . "', "; 
            $sql .= "'" . $trkTimeToTarget . "', ";
            $sql .= "'" . $trkTimeToEnd . "', ";
            $sql .= "'" . $trkGrade . "', "; 
            $sql .= "'" . $trkMeterUp . "', "; 
            $sql .= "'" . $trkMeterDown . "')";
            
            echo "-------------------- <br>";
            echo "Zeile: $zaehler <br>";

            if ($conn->query($sql) === TRUE) {
                echo "Erfolgreich: <br> $sql <br>";
            } else {
                echo "Fehler: <br>";
                echo "Sql Statement:  <br>" . $sql . "<br>";
                echo "Fehlermeldung: " . $conn->error;
                echo "<br>";
            }
            echo "-------------------- <br>";
            $sql = "";

            $zaehler++;
        }
    }

    $conn->close();

}