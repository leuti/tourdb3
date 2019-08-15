<?php

// ---------------------------------------------------------------------------------------------
// This php script is called to generate HTML code for the Filter UI masks (currently only mapp panel)
// Only the static part of the code is generated. Dynamic elements like types / subtypes are generated
// by other services called later (e.g. getTypes.php)
//
// Parameters:
// none
//
// Return Object:
// HTML code (echo to calling object and to file in case debugLevel >= 3)
// 
// Created: 3.8.2019 - Daniel Leutwyler
// ---------------------------------------------------------------------------------------------
// Action:
// 

// Set timezone (otherwise warnings are written to log)
date_default_timezone_set("Europe/Zurich");
include("tourdb_config.php");                                              // Include config file

$debugLevel = 0;

if ($debugLevel >= 1){
    $logFileLoc = dirname(__FILE__) . "/../log/getMapFilterUI.log";    // Assign file location
    $logFile = @fopen($logFileLoc,"a");     
    fputs($logFile, "=================================================================\r\n");
    fputs($logFile, date("Ymd-H:i:s", time()) .  "getMapFilterUI.php opened \r\n"); 
};

// Read JSON file containing all UI settings
$json = file_get_contents('./UISettingsMapFil.json');

//Decode JSON
$UISettings = json_decode($json,true);

// Variables
$tab_h_opened = false;                                              // indicates if top tab div is opened or not
$fieldset_h_opened = false;                                         // indicates if fieldset tab div is opened or not
$fieldset_heading = "";
$tab_heading = "";

// Write Initial statements

// This is the div containing the icon to open large menu mask
$outArray[] = '<div id="mapMenuMini" class="dispObjectSelector dispObjMini hidden">';
$outArray[] = '<a id="mapMenuMiniOpen" href="#mapMenuMiniOpen">';
$outArray[] = '<img id="mapMenuOpenImg" src="css/images/filterLightBlue.png">';
$outArray[] = '</a>';
$outArray[] = '</div>';

// This is the start of the filter UI 
$outArray[] = '<div id="mapFilMenuLarge" class="dispObjectSelector dispObjOpen visible">';
$outArray[] = '  <a id="mapMenuLargeClose" href="#mapMenuLargeClose">';
$outArray[] = '    <img id="mapMenuCloseImg" src="css/images/arrowLeftLightBlue.png">';
$outArray[] = '  </a>';
$outArray[] = '  <p class="dispObjMenuText">Select objects to be displayed</p>';
$outArray[] = '  <div id="mapFilAccordion" class="dispObjOpen visible">';

// Loop through UIsettings line by line
foreach ( $UISettings as $key => $record ) {

    // Print key variables into log
    if ( $debugLevel >= 3 ) $outArray[] = "##--------------------------------------------------------------------------------------------------------";
    if ( $debugLevel >= 3 ) $outArray[] = "##key: " . $key . " | item:   " . $UISettings[$key]["ele_id"];
    if ( $debugLevel >= 3 ) $outArray[] = "##tab_heading:                " . $tab_heading;
    if ( $debugLevel >= 3 ) $outArray[] = "##UISetting_tab_heading:      " . $UISettings[$key]["tab_heading"];
    if ( $debugLevel >= 3 ) $outArray[] = "##fieldset_heading:           " . $fieldset_heading;
    if ( $debugLevel >= 3 ) $outArray[] = "##UISetting_fieldset_heading: " . $UISettings[$key]["fieldset_heading"];
    if ( $debugLevel >= 3 ) $outArray[] = "##tab_h_opened:               " . ( $UISettings[$key]["tab_h_opened"] == true ? "true" : "false" );
    if ( $debugLevel >= 3 ) $outArray[] = "##fieldset_h_opened:          " . ( $UISettings[$key]["fieldset_h_opened"] == true ? "true" : "false" ); 
    if ( $debugLevel >= 3 ) $outArray[] = "##"; 

    // Close fieldset tag when fieldset heading differs from previous record and fieldset tag was previously opened
    if ( $fieldset_heading <> $UISettings[$key]["fieldset_heading"] && $fieldset_h_opened == true ) {
        $outArray[] = '      </fieldset>';    
        $fieldset_h_opened = false;
    }
    
    // Close main tab tags when tab heading differs from previous record and tag for main div was previously opened
    if ( $tab_heading <> $UISettings[$key]["tab_heading"] && $tab_h_opened == true ) {
        $outArray[] = '    </div>';    
        $tab_h_opened = false;
    }

    // Open main tab tags when tab tab heading differs from previous record, tab was previously not opened and current record is not a dummy
    if ( $tab_heading <> $UISettings[$key]["tab_heading"] && $tab_h_opened == false && $UISettings[$key]["ele_type"] != "dummy" ) {
        $outArray[] = '    <h2>' . $UISettings[$key]["tab_heading"] . '</h2>';
        $outArray[] = '    <div class="accordionBackground">';
        $tab_h_opened = true;
        $tab_heading = $UISettings[$key]["tab_heading"];
    }

    // Open fieldset tags when fieldset heading differs from previous record, tab was previously not opened and current record is not a dummy
    if ( $fieldset_heading <> $UISettings[$key]["fieldset_heading"] && $fieldset_h_opened == false  && $UISettings[$key]["ele_type"] != "dummy" ) {
        $outArray[] = '      <fieldset>';
        $outArray[] = '        <legend class="filterHeader">' . $UISettings[$key]["fieldset_heading"] .'</legend>';
        $fieldset_h_opened = true;
        $fieldset_heading = $UISettings[$key]["fieldset_heading"];
    }

    // Echo opening tag for current UI element
    if ( $UISettings[$key]["tag_open"] <> "" ) $outArray[] = '        ' . $UISettings[$key]["tag_open"];

    // Generate output if element type is "Text"
    if ( $UISettings[$key]["ele_type"] == "text" ) {
        $outArray[] = '          <label for="' . $UISettings[$key]["ele_id"] . '" class="' . $UISettings[$key]["label_class"] . '">' . $UISettings[$key]["ele_label"] . '</label>';
        $outArray[] = '          <input type="' . $UISettings[$key]["ele_type"] . '" id="' . $UISettings[$key]["ele_id"] . '" size="' . $UISettings[$key]["ele_size"] . '" class="' . $UISettings[$key]["ele_class"] . '">';
    
    // Generate output if element type is "Hidden"
    } else if ( $UISettings[$key]["ele_type"] == "hidden" ) {
        $outArray[] = '          <input type="hidden" id="' . $UISettings[$key]["ele_id"] . '">';

    // Generate output if element type is "Selectable"
    } else if ( $UISettings[$key]["ele_type"] == "selectable" ) {
        $outArray[] = '        <div id="' . $UISettings[$key]["ele_id"] . '" class="' . $UISettings[$key]["ele_class"] . '">';
        $outArray[] = '        </div>';
    
    // Generate output if element type is "Buttons" (submit) 
    } else if ( $UISettings[$key]["ele_type"] == "submit" ) {
        $outArray[] = '          <input type="submit" id="' . $UISettings[$key]["ele_id"] . '" class="' . $UISettings[$key]["ele_class"] . '" value="' . $UISettings[$key]["ele_label"] . '" />';
    
    // Generate output if element type is "Checkboxe"
    } else if ( $UISettings[$key]["ele_type"] == "checkbox" ) {
        $outArray[] = '          <label for="' . $UISettings[$key]["ele_id"] . '" class="' . $UISettings[$key]["label_class"] . '">' . $UISettings[$key]["ele_label"] . '</label>';
        $outArray[] = '          <input type="' . $UISettings[$key]["ele_type"] . '" id="' . $UISettings[$key]["ele_id"] . '" size="' . $UISettings[$key]["ele_size"] . '" class="' . $UISettings[$key]["ele_class"] . '">';
    }
    
    // Echo closing tag for current UI element
    if ( $UISettings[$key]["tag_close"] <> "" ) $outArray[] = '        ' . $UISettings[$key]["tag_close"];
}

// Write trailing lines
$outArray[] = '  </div>';
$outArray[] = '</div>';

// Echo Div for map display
$outArray[] = '<div id="displayMap" class="visible">';
$outArray[] = '  <div id="displayMap-ResMap">';
$outArray[] = '  </div>';
$outArray[] = '</div>';

$htmlOut = join("\r\n", $outArray);                                 // covert array to text string  

echo $htmlOut;                                                      // echo HTML to calling object

// Write HTML to file "log/getMapFilterUI.out"
if ( $debugLevel >= 3 ) {
    $tempOut = dirname(__FILE__) . "/../log/getMapFilterUI.out";       // Assign file location
    $tempOutFile = fopen($tempOut,"w");     
    fputs( $tempOutFile, $htmlOut );
    fclose( $tempOutFile );
}

if ($debugLevel >= 1) fclose($logFile);                             // Close debug file
?>