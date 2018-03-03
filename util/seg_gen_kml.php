<?php
date_default_timezone_set('Europe/Zurich');

$debugLevel = 1; // 0 = off, 1 = min, 3 = a lot, 5 = all

if ($debugLevel >= 1){
    $fp = @fopen("log/gen_seg_kml.log","a"); 
    fputs($fp, "\r\n====================================================================================\r\n");
    fputs($fp, date("Ymd-H:i:s", time()) . "-Line 8: seg_gen_kml.php opened \r\n");
};

//continue only if $_GET is set (and it is a Ajax request)
//if(isset($_POST) && array_key_exists('segmentFileName', $_POST) && isset($_SERVER['HTTP_X_REQUESTED_WITH']) 
if(isset($_POST) && array_key_exists('segmentFileName', $_POST)){
    
    if ($debugLevel >= 3){
        fputs($fp, date("Ymd-H:i:s", time()) . "-Line 17: sqlFilterString: " . $_POST["sqlFilterString"]);
        fputs($fp, " --- segmentFileName: " . $_POST['segmentFileName'] . "\r\n" );
    };
    include("config.inc.php");  //include config file
    //sets WHERE string if $sqlFilterString has been provided	
    if(isset($_POST["sqlFilterString"]) && $_POST["sqlFilterString"] != ''){
        $sqlFilterString = $_POST['sqlFilterString'] . " AND coordinates <> '' "; //filter number
    }else{
		$sqlFilterString = " WHERE 1=2 "; //if there's no sql search string delivered, set WHERE to ''
	};

    // Select required Track Styles
    $sql = "SELECT segType, grdTracksGroup FROM vw_segments ";
    $sql .= $sqlFilterString . "GROUP BY segType, grdTracksGroup ";
    $sql .= "ORDER BY segType, grdTracksGroup";
    if ($debugLevel >= 1){
        fputs($fp, date("Ymd-H:i:s", time()) . "-Line 33: sql for required track styles: " . $sql ."\r\n");
    };
    //mysqli_query($conn,"SELECT * FROM vw_segments");

    $trackStyleReq = mysqli_query($conn, $sql);
    
    // Create WHERE IN string
    $first = true; 
    
    while($reqStyle = mysqli_fetch_assoc($trackStyleReq)){ //fetch values
        if ($debugLevel >= 5){
            fputs($fp, date("Ymd-H:i:s", time()) . "-Line 44: in while loop for trackStyleReq\r\n");
        };
        if ($first){
            $sqlIn = " WHERE styCode IN ('" . $reqStyle["segType"] . $reqStyle["grdTracksGroup"] . "'";
            $first = false;
        } else {
	        $sqlIn .= ", '" . $reqStyle["segType"] . $reqStyle["grdTracksGroup"] . "'"; 
        }
    }
    $sqlIn .= ")";

    if ($debugLevel >= 5){
        fputs($fp, date("Ymd-H:i:s", time()) . "-Line 55: $sqlIn: " . $sqlIn ."\r\n");
    };
      
    // Select available Track Styles
    $sql = "SELECT styCode, styColorNormal, styWidthNormal, styLineNormal, ";
    $sql .= "styColorHighlighted, styWidthHighlighted, styLineHighlighted ";
    $sql .= "FROM tbl_kmlstyle";
    $sql .= $sqlIn;
    $sql .= " ORDER BY styCode";
    if ($debugLevel >= 1){
        fputs($fp, date("Ymd-H:i:s", time()) . "-Line 62: sql for available track styles: " . $sql ."\r\n");
    };
    // mySqli procedural method
    $trackStyleAvail = mysqli_query($conn, $sql);
   
    // Select segments for output
    $sql = "SELECT ID, segType, sourceFID, sourceRef, ";
    $sql .= "segName, area, region, country, grade, grdTracksGroup, ";
    $sql .= "climbGrade, firn, TIME_FORMAT(tStartTarget, '%h:%i') AS timeUp, coordinates ";
    $sql .= "FROM vw_segments ";
    $sql .= $sqlFilterString;  
    $sql .= "ORDER BY area "; 
    if ($debugLevel >= 1){
        fputs($fp, date("Ymd-H:i:s", time()) . "-Line 79: sql for segments: " . $sql ."\r\n");
    };

    $allSegments = mysqli_query($conn, $sql);
	    
    //Write document header
    $kml[] = '<?xml version="1.0" encoding="UTF-8"?>';
    $kml[] = '<kml xmlns="http://www.opengis.net/kml/2.2" '; 
    $kml[] = 'xmlns:gx="http://www.google.com/kml/ext/2.2" ';
    $kml[] = 'xmlns:kml="http://www.opengis.net/kml/2.2" '; 
    $kml[] = 'xmlns:atom="http://www.w3.org/2005/Atom">';

    //Write Document Level 1 --> Extend to 3 dynamic Levels
    $kml[] = '<Document>';
    $kml[] = '   <name>TourDB KML Tracks</name>';
    $kml[] = '   <open>1</open>';
    
    $styColorNormal_sel = 'ffff55aa'; // Default values
    $styWidthNormal_sel = '3'; // Default values
    if ($debugLevel >= 5){
        $i = 1;
    };
    while($segment = mysqli_fetch_assoc($allSegments)){ // loop through each segment with coordinates
        $segmentStyle = $segment["segType"] . $segment["grdTracksGroup"]; // segmentStyle = id for tbl_kmlstyle
        if ($debugLevel >= 5){
            fputs($fp, date("Ymd-H:i:s", time()) . "-Line 105: segmentStyle: " . $segmentStyle."\r\n");
            fputs($fp, date("Ymd-H:i:s", time()) . "-Line 106: tStartTarget: " . $segment["timeUp"] ."\r\n");
        };
        if ($debugLevel >= 5){
            fputs($fp, "Vor data_seek -->" . $segmentStyle . "\r\n" );
        };
        mysqli_data_seek($trackStyleAvail, 0); // reset the pointer for trackStyleAvail
        if ($debugLevel >= 5){
            fputs($fp, "Nach data_seek -->" . $segment["segType"] . $segment["grdTracksGroup"] . "\r\n" );
        };
        
        while ($trackStyle = mysqli_fetch_assoc($trackStyleAvail)) {
            if ($debugLevel >= 5){
                fputs($fp, date("Ymd-H:i:s", time()) . "-Line 113: styCode: " . $trackStyle["styCode"] );
                fputs($fp, " segmentStyle: " . $segmentStyle . "\r\n" );
            };
            if ($segmentStyle== $trackStyle["styCode"]) {
                $styColorNormal_sel = $trackStyle["styColorNormal"];
                $styWidthNormal_sel = $trackStyle["styWidthNormal"];
                break;
            }  
        };
        
        if ($debugLevel >= 5){
            fputs($fp, date("Ymd-H:i:s", time()) . "-Line 125: styColorNormal_sel: " . $styColorNormal_sel . "\r\n");
            fputs($fp, date("Ymd-H:i:s", time()) . "-Line 126: styWidthNormal_sel: " . $styWidthNormal_sel . "\r\n");
        };
        $kml[] = '   <Placemark>';
        if ($debugLevel >= 5){
            fputs($fp, "DEBUG:segName: " . $segment["segName"] . "\r\n");
        };
        $kml[] = '      <name>' . $segment["segName"] . '</name>';
        $kml[] = '      <description>' . $segment["sourceFID"] . '-' . $segment["sourceRef"] . ' ' .  $segment["segName"] . 
            ' (' . $segment["grade"] . '/' . $segment["timeUp"] . ')</description>';
        $kml[] = '      <Style>';
        $kml[] = '         <LineStyle>';
        $kml[] = '            <color>' . $styColorNormal_sel . '</color>';
        $kml[] = '            <width>' . $styWidthNormal_sel . '</width>';
        $kml[] = '         </LineStyle>';
        $kml[] = '         <PolyStyle>';
        $kml[] = '            <color>' . $styColorNormal_sel . '</color>';
        $kml[] = '         </PolyStyle>';
        $kml[] = '      </Style>';
        $kml[] = '      <LineString>';
        $kml[] = '         <coordinates>';
        $kml[] = '            ' . $segment["coordinates"];
        $kml[] = '         </coordinates>';
        $kml[] = '      </LineString>';
        $kml[] = '   </Placemark>';
        if ($debugLevel >= 5){
            fputs($fp, date("Ymd-H:i:s", time()) . "-Line 142: kml line: " . $i . " written\r\n");
            $i += 1;
        };
    };

    // Write KML trailer
    $kml[] = '</Document>';
    $kml[] = '</kml>';

    // Merge kml array into one variable
    $kmlOutput = join("\n", $kml);

    echo $kmlOutput;

    // Define header so that the PHP file generates a KML
    fputs($fp, "segmentFileName " . $_POST["segmentFileName"] . "\r\n");
    if(isset($_POST["segmentFileName"])){
        $fp_kml = @fopen($_POST["segmentFileName"],"w");
        if(!$fp_kml){
            fputs($fp, "Error opening files\r\n");
        };
    }else{
		$fp_kml = @fopen("tourDbSedments.kml","w");
        fputs($fp, "File name not set\r\n");
	};
    
    if ($debugLevel >= 5){
        fputs($fp, $kmlOutput);
    }; 

    fputs($fp_kml, $kmlOutput);
    fclose($fp_kml);
    
    if ($debugLevel >= 1){
        fputs($fp, date("Ymd-H:i:s", time()) . "-Line 187: KML File written to: " . $_POST["segmentFileName"] . "\r\n");
        fclose($fp);
    }; 
}else{
    if ($debugLevel >= 1){
        fputs($fp, date("Ymd-H:i:s", time()) . "-Line 192: Invalid parameters!!\r\n");
    };
};
?>