<?php
//---------------------------------------------------------------------------------------------
// PHP script converting Logbuch Berge.csv file and loading records to database tbl_tracks
//
// This script is intended as for one-time use at initial migration
//
// Created: 7.12.2017 - Daniel Leutwyler
//---------------------------------------------------------------------------------------------

// Test Cases
// Total number of tracks = number of lines in logbook
// All attributes are correctly inserted

// -----------------------------------
// Set variables and parameters    
include("./config.inc.php");                                        //include config file

$zaehler = 0;                                                       // used to count overall number of records
$corr_insert = 0;                                                   // counts number of correctly imported records
$err_insert = 0;                                                    // counts number of error records


$logbook = @fopen("./import/logbook/Logbuch Berge.csv","r");        // Opens csv file
if(!$logbook) 
{
    exit("Datei nicht gefunden");
}

while (!feof($logbook))                                             // loop through each line of the logbook file
{
    $zeile = fgets($logbook,1000);                                  // read each line --> set to 1000
        $zeile = substr($zeile, 0, strlen($zeile)-1);               // Ausgabe der Zeile ohne Carrage Return oder Line Feed
        if(!(feof($logbook) && $zeile == ""))                       // Verarbeitet nur Zeilen mit Inhalt
        {
            $worte = explode(",", $zeile);                          // Zeile in Felder aufteilen
            
            // Zuweisung der csv Felder
            $trkLogbookId = $worte[1];                              // column ID
            $trkPeakRef = $worte[2];                                // column RefGipfel
            $trkStravaFileName = $worte[3];                         // column FileName Strava
            $trkDateBegin = $worte[4];                              // column Datum Start
            $trkDateFinish = $worte[5];                             // column Datum End
            $trkSaison = $worte[8];                                 // column Saison
            $trkTyp = $worte[10];                                   // column Art
            $trkSubType = $worte[11];                               // column Unterart 
            $trkOrg = $worte[12];                                   // column Org
            $trkTarget = $worte[16];                                // column Ziel
            $trkRoute = $worte[18];                                 // column Ziel Typ
            $trkOvernightLoc = $worte[19];                          // column Route
            $trkParticipants = $worte[24];                          // column Teilnehmer (ACTION: Zusammensetzen)
            $trkEvent = $worte[25];                                 // column Anlass
            $trkRemarks = $worte[26];                               // column Bemerkung
            $trkDistance = (int)$worte[27];                         // column Distanz
            $trkTimeOverall = $worte[28];                           // column Dauer
            $trkGrade = $worte[29];                                 // column Schwierigkeit
            $trkMeterUp = (int)$worte[30];                          // column Aufstieg
            $trkMeterDown = (int)$worte[31];                        // column Abstieg
            
            // Create Select Statement
            $sql = "INSERT INTO `tourdb2`.`tbl_tracks` (`trkId`, ";
            $sql .= "`trkLogbookId`, `trkStravaFileName`, `trkPeakRef`, `trkDateBegin`, ";
            $sql .= "`trkDateFinish`, `trkGPSStartTime`, `trkSaison`, `trkTyp`, `trkSubType`, ";
            $sql .= "`trkOrg`, `trkTarget`, `trkRoute`, `trkOvernightLoc`, `trkParticipants`, ";
            $sql .= "`trkEvent`, `trkRemarks`, `trkDistance`, `trkTimeOverall`, `trkTimeToEnd`, ";
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
            $sql .= "'" . $trkTimeOverall . "', ";
            $sql .= "'" . $trkTimeToEnd . "', ";
            $sql .= "'" . $trkGrade . "', "; 
            $sql .= "'" . $trkMeterUp . "', "; 
            $sql .= "'" . $trkMeterDown . "')";
            
            // Schreiben in die Datenbank
            if ($conn->query($sql) === TRUE) {                      // Schreibt in die DB            
                $corr_insert++;                                     // Wenn korrekt wird Zähler für korrekte Records erhöht
                $zaehler++;                                         // Gesamtzähler wird erhöht
            } else {
                echo "96: Fehler: <br>";
                echo "97: Sql Statement:  <br>" . $sql . "<br>";
                echo "98: Fehlermeldung: " . $conn->error . "<br>";
                $err_insert++;                                      // Wenn falsch wird Zähler für falsche Records erhöht
                $zaehler++;                                         // Gesamtzähler wird erhöht
            }
            $sql = "";                                              // Setzt die Variable für das SQL statement wieder auf leer         
        }
}
echo "$zaehler Zeilen verarbeitet ($corr_insert erfolgreich eingefügt / $err_insert Fehler) <br>";
$conn->close();                                                     // Schliesst DB connection
fclose($logbook);                                                   // Schliesst File
?>