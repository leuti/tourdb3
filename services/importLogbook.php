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
header("Content-Type: text/html; charset=utf-8");

$zaehler = 0;                                                       // used to count overall number of records
$corr_insert = 0;                                                   // counts number of correctly imported records
$err_insert = 0;                                                    // counts number of error records

$logbook = @fopen("../import/logbook/logbook.csv","r");        // Opens csv file
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
            $spalten = explode(",", $zeile);                          // Zeile in Felder aufteilen

            // Zuweisung der csv Felder
            $trkLogbookId = $spalten[0];                              // column  0/ID
            $trkPeakRef = $spalten[1];                                // column  1/RefGipfel
            $trkSourceFileName = $spalten[2];                         // column  2/FileName Strava
            $trkDateBegin = strftime("%Y.%m.%d", strtotime($spalten[3]));  // column  3/Datum Start
            $trkDateFinish = strftime("%Y.%m.%d", strtotime($spalten[4])); // column  4/Datum End
            $trkSaison = $spalten[7];                                 // column  7/Saison
            $trkTyp = $spalten[9];                                    // column  9/Art
            $trkSubType = $spalten[10];                               // column 10/Unterart 
            $trkOrg = $spalten[11];                                   // column 11/Org
            $trkTrackName = $spalten[13];                             // column 13/Ziel
            $trkRoute = $spalten[17];                                 // column 15/Route
            $trkOvernightLoc = $spalten[18];                          // column 18/Übernachtung
            $trkParticipants = $spalten[23];                          // column 23/Teilnehmer (ACTION: Zusammensetzen)
            $trkEvent = $spalten[24];                                 // column 24/Anlass
            $trkRemarks = $spalten[25];                               // column 25/Bemerkung
            $trkDistance = (int)$spalten[26];                         // column 26/Distanz
            $trkTimeOverall = $spalten[27];                           // column 27/Dauer
            $trkGrade = $spalten[28];                                 // column 28/Schwierigkeit
            $trkMeterUp = (int)$spalten[29];                          // column 29/Aufstieg
            $trkMeterDown = (int)$spalten[30];                        // column 30/Abstieg
            $trkCountry = $spalten[31];                               // column 31/Country

            // Create Select Statement
            $sql = "INSERT INTO `tourdb2`.`tbl_tracks` (";
            $sql .= "`trkLogbookId`, `trkSourceFileName`, `trkPeakRef`, `trkDateBegin`, ";
            $sql .= "`trkDateFinish`, `trkSaison`, `trkTyp`, `trkSubType`, ";
            $sql .= "`trkOrg`, `trkTrackName`, `trkRoute`, `trkOvernightLoc`, `trkParticipants`, ";
            $sql .= "`trkEvent`, `trkRemarks`, `trkDistance`, `trkTimeOverall`, `trkTimeToEnd`, ";
            $sql .= "`trkGrade`, `trkMeterUp`, `trkMeterDown`, `trkCountry`) VALUES (";
            $sql .= "'" . $trkLogbookId . "', ";
            $sql .= "'" . $trkSourceFileName . "', "; 
            $sql .= "'" . $trkPeakRef . "', "; 
            $sql .= "'" . $trkDateBegin . "', ";
            $sql .= "'" . $trkDateFinish . "', "; 
            $sql .= "'" . $trkSaison . "', "; 
            $sql .= "'" . $trkTyp . "', "; 
            $sql .= "'" . $trkSubType . "', ";
            $sql .= "'" . $trkOrg . "', "; 
            $sql .= "'" . $trkTrackName . "', "; 
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
            $sql .= "'" . $trkMeterDown . "', ";
            $sql .= "'" . $trkCountry . "')";
            
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