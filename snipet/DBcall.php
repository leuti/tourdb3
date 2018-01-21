<?php

// Variante 1
if ($stmt = mysqli_prepare($conn, $sql)) 
{
    mysqli_stmt_execute($stmt);                                     // execute select statement
    mysqli_stmt_bind_result($stmt, $trackid);                       // bind result variables

    while (mysqli_stmt_fetch($stmt)) {                              // Fetch result of sql statement (one result expeced)
        
        $trackObj = array (
            "trkId"=>$trackid,
            "trkSourceFileName"=>"$filename",
            "trkTrackName"=>"$trackName",
            "trkDateBegin"=>"$DateBegin",
            "trkDateFinish"=>"$DateFinish",
            "trkGPSStartTime"=>"$GpsStartTime"
        );
    }
    $returnObject = array (
        "status"=>"OK",
        "erressage"=>"",
        "trackObj"=>$trackObj
    );
    return $returnObject;                 // return tmp trackId, track name and coordinate array in array
    mysqli_stmt_close($stmt);                                       // Close statement
} else {
    if ($GLOBALS['debugLevel']>0) fputs($GLOBALS['logFile'], "Line 195 - Error selecting max(trkId): $conn->error\r\n");
    if ($GLOBALS['debugLevel']>4) fputs($GLOBALS['logFile'], "Line 196 - sql: $stmt\r\n");
    $returnObject = array (
        "status"=>"ERR",
        "errmessage"=>"Error finding trackId"
    );
    return $returnObject;
}

// Version 2
$records = mysqli_query($conn, $sql);

// Loop through each selected track and write main track data
while($singleRecord = mysqli_fetch_assoc($records))
{ 
    $kml[] = '        <Placemark id="linepolygon_' . sprintf("%'05d", $singleRecord["trkId"]) . '">';
    $kml[] = '          <name>' . $singleRecord["trkTrackName"] . '</name>';
    $kml[] = '          <visibility>1</visibility>';
    $kml[] = '      <description>' . $segment["sourceFID"] . '-' . $segment["sourceRef"] . ' ' .  $segment["segName"] . 
    
}
?>