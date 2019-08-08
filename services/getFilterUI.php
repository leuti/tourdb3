<?php

// ---------------------------------------------------------------------------------------------
// This php script is called to generate all UI elements dependent on types and subtypes
//
// Parameters:
// 
//
// Function:
// 
// Created: 3.8.2019 - Daniel Leutwyler
// ---------------------------------------------------------------------------------------------
// Action:
// 
// 

// Set timezone (otherwise warnings are written to log)
date_default_timezone_set("Europe/Zurich");
include("config.php");                                                  // Include config file

if ($debugLevel >= 1){
    $logFileLoc = dirname(__FILE__) . "/../log/getFilterUI.log";                // Assign file location
    $logFile = @fopen($logFileLoc,"a");     
    fputs($logFile, "=================================================================\r\n");
    fputs($logFile, date("Ymd-H:i:s", time()) .  "getFilterUI.php opened \r\n"); 
};

// Are these variable required?
$item="";
$tab_heading="";
$fieldset_heading="";
$rank="";
$div_id="";
$ele_type="";
$ele_name="";
$ele_id="";
$label="";
$label_class="";
$ele_size="";
$ele_class="";

// remove after development =============================================================================================================
$debugLevel = 3;

// Read JSON file
$json = file_get_contents('./UISettings.json');

//Decode JSON
$UISettings = json_decode($json,true);

// Variables
$tab_h_opened = false;
$fieldset_h_opened = false;

// Write Initial statements
$outArray[] = '  <a id="dispObjMenuLargeClose" href="#dispObjMenuLargeClose">';
$outArray[] = '    <img id="dispObjMenuCloseImg" src="css/images/arrowLeftLightBlue.png">';
$outArray[] = '  </a>';
$outArray[] = '  <p class="dispObjMenuText">Select objects to be displayed</p>';
$outArray[] = '  <div id="dispObjAccordion" class="dispObjOpen visible">';

foreach ( $UISettings as $key => $record ) {
    if ( $debugLevel >= 3 ) $outArray[] = "##--------------------------------------------------------------------------------------------------------";
    if ( $debugLevel >= 3 ) $outArray[] = "##key: " . $key . " | item:   " . $UISettings[$key]["ele_id"];
    if ( $debugLevel >= 3 ) $outArray[] = "##tab_heading:                " . $tab_heading;
    if ( $debugLevel >= 3 ) $outArray[] = "##UISetting_tab_heading:      " . $UISettings[$key]["tab_heading"];
    if ( $debugLevel >= 3 ) $outArray[] = "##fieldset_heading:           " . $fieldset_heading;
    if ( $debugLevel >= 3 ) $outArray[] = "##UISetting_fieldset_heading: " . $UISettings[$key]["fieldset_heading"];
    if ( $debugLevel >= 3 ) $outArray[] = "##tab_h_opened:               " . ( $UISettings[$key]["tab_h_opened"] == true ? "true" : "false" );
    if ( $debugLevel >= 3 ) $outArray[] = "##fieldset_h_opened:          " . ( $UISettings[$key]["fieldset_h_opened"] == true ? "true" : "false" ); 
    if ( $debugLevel >= 3 ) $outArray[] = "##"; 

    // Close fieldset tag
    if ( $fieldset_heading <> $UISettings[$key]["fieldset_heading"] && $fieldset_h_opened == true ) {
        $outArray[] = '      </fieldset>';    
        $fieldset_h_opened = false;
    }
    
    // Close main tab tags
    if ( $tab_heading <> $UISettings[$key]["tab_heading"] && $tab_h_opened == true ) {
        $outArray[] = '    </div>';    
        $tab_h_opened = false;
    }

    // Open main tab tags
    if ( $tab_heading <> $UISettings[$key]["tab_heading"] && $tab_h_opened == false && $UISettings[$key]["type"] != "dummy" ) {
        $outArray[] = '    <h2>' . $UISettings[$key]["tab_heading"] . '</h2>';
        $outArray[] = '    <div class="accordionBackground">';
        $tab_h_opened = true;
        $tab_heading = $UISettings[$key]["tab_heading"];
    }

    // Open fieldset tags
    if ( $fieldset_heading <> $UISettings[$key]["fieldset_heading"] && $fieldset_h_opened == false  && $UISettings[$key]["type"] != "dummy" ) {
        $outArray[] = '      <fieldset>';
        $outArray[] = '        <legend class="filterHeader">' . $UISettings[$key]["fieldset_heading"] .'</legend>';
        $fieldset_h_opened = true;
        $fieldset_heading = $UISettings[$key]["fieldset_heading"];
    }

    if ( $UISettings[$key]["tag_open"] <> "" ) $outArray[] = '        ' . $UISettings[$key]["tag_open"];

    // Output for different lines in UISettings.json
    // ---------------------------------------------

    // Text elements
    if ( $UISettings[$key]["type"] == "text" ) {
        $div_id = $UISettings[$key]["div_id"];
        $outArray[] = '          <label for="' . $UISettings[$key]["ele_id"] . '" class="' . $UISettings[$key]["label_class"] . '">' . $UISettings[$key]["label"] . '</label>';
        $outArray[] = '          <input type="' . $UISettings[$key]["type"] . '" name="' . $UISettings[$key]["ele_name"] . '" id="' . $UISettings[$key]["ele_id"] . '" size="' . $UISettings[$key]["ele_size"] . '" class="' . $UISettings[$key]["ele_class"] . '">';
    
    // Hidden elements
    } else if ( $UISettings[$key]["type"] == "hidden" ) {
        $outArray[] = '          <input type="hidden" name="' . $UISettings[$key]["ele_name"] . '" id="' . $UISettings[$key]["ele_id"] . '">';

    // Selectable elements
    } else if ( $UISettings[$key]["type"] == "selectable" ) {
        $outArray[] = '        <div id="' . $UISettings[$key]["div_id"] . '" class="' . $UISettings[$key]["ele_class"] . '">';
        $outArray[] = '        </div>';
    
    // Buttons 
    } else if ( $UISettings[$key]["type"] == "submit" ) {
        $outArray[] = '          <input type="submit" name="' . $UISettings[$key]["ele_name"] . '" id="' . $UISettings[$key]["ele_id"] . '" class="' . $UISettings[$key]["ele_class"] . '" value="' . $UISettings[$key]["label"] . '" />';
    
    // Checkboxes
    } else if ( $UISettings[$key]["type"] == "checkbox" ) {
        $outArray[] = '          <label for="' . $UISettings[$key]["ele_id"] . '" class="' . $UISettings[$key]["label_class"] . '">' . $UISettings[$key]["label"] . '</label>';
        $outArray[] = '          <input type="' . $UISettings[$key]["type"] . '" name="' . $UISettings[$key]["ele_name"] . '" id="' . $UISettings[$key]["ele_id"] . '" size="' . $UISettings[$key]["ele_size"] . '" class="' . $UISettings[$key]["ele_class"] . '">';
    }
    
    if ( $UISettings[$key]["tag_close"] <> "" ) $outArray[] = '        ' . $UISettings[$key]["tag_close"];

}

$outArray[] = '  </div>';

$htmlOut = join("\r\n", $outArray);

// remove after development
$tempOut = dirname(__FILE__) . "/../log/getFilterUI.out";                // Assign file location
$tempOutFile = fopen($tempOut,"w");     
fputs( $tempOutFile, $htmlOut );
// remove after development

fclose( $tempOutFile );

                          

if ($debugLevel >= 1) fclose($logFile);
?>