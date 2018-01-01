// ---------------------------------------------------------------------------------------------
// This is the main javascript file controling the behaviour of the tourdb page 
// 
// Created: 30.12.2017 - Daniel Leutwyler
// ---------------------------------------------------------------------------------------------
//
// Actions:
// * Create login page (using session)
// * kml file names must contain session specific id (ensure multi-user capabilities)
// * Is function drawMapEmpty required?
// * can/should $(document) be replaced in this statement? $(document).on('click', '#dispObjMenuLargeClose', function(e)
// * remove unnecessary where statement for filters (e.g. between dates, altitues)


// =================================================
// ====== G L O B A L   V A R   S E C T I O N ======
// =================================================
// Create unique file name for KML
var today = new Date();

    // file location & name for KML output
    trackFileName = "track_" + today.getTime() + ".kml";
    trackKmlFileNameURL = document.URL + "tmpout/" + trackFileName;

// =====================================
// ====== M A I N   S E C T I O N ======
// =====================================
$(document).ready(function() {

    // Initial drawing of map
    if ( navigator.onLine ) {
        drawMapEmpty('displayMap-ResMap');         // Draw empty map (without additional layers) 
    };

    // Manages the behaviour when clicking on the main topic buttons
    $('.topicButtons').each(function() {
        var $thisTopicButton = $(this);                                     // $thisTopicButton becomes ul.topicButtons
        $activeButton = $thisTopicButton.find('li.active');                 // Find and store current active li element
        var $activeButtonA = $activeButton.find('a');                       // Get link <a> from active li element 
        $topicButton = $($activeButtonA.attr('href'));                      // Get active panel

        $(this).on('click', '.mainButtonsA', function(e) {                  // When click on a topic tab (li item)
            e.preventDefault();                                             // Prevent link behaviour
            var $activeButtonA = $(this)                                    // Store the current link <a> element
            var buttonId = this.hash;                                       // Get div class of selected topic (e.g #panelDisplay)
            
            // Run following block if selected topic is currently not active
            if (buttonId && !$activeButtonA.is('.active')) {
                $topicButton.removeClass('active');                         // Make current panel inactive
                $activeButton.removeClass('active');                        // Make current tab inactive

                $topicButton = $(buttonId).addClass('active');              // Make new panel active
                $activeButton = $activeButtonA.parent().addClass('active'); // Make new tab active
            }
        }); 
    }); 

    $(document).on('click', '#dispObjMenuLargeClose', function(e) {
        e.preventDefault();
        var $activeButton = $(this);
        $activeButton.parent().removeClass('visible');
        $activeButton.parent().addClass('hidden');
        $('.dispObjMini').removeClass('hidden');
        $('.dispObjMini').addClass('visible');

    })

    $(document).on('click', '#dispObjMenuMiniOpen', function(e) {
        e.preventDefault();
        var $activeButton = $(this);
        $activeButton.parent().removeClass('visible');
        $activeButton.parent().addClass('hidden');
        $('.dispObjOpen').removeClass('hidden');
        $('.dispObjOpen').addClass('visible');

    })

    // ==========================================================================
    // ========================== panelDisplay ==================================
    // ==========================================================================
    
    // *********************************************
    // Initialse all jquery functional fields

    // For Object Filter Routes
    // ------------------------
    $( function() {                                                         // Initialise filter area as JQUERY Accordion
        $( "#dispObjAccordion" ).accordion({
          collapsible: true,                                                // makes sections collapse 
          heightStyle: "content"                                            // hight of section dependent on content of section
        });
    } );

    $( "#dispFilTrk_dateFrom" ).datepicker({                                // Initalise field to select start date as JQUERY datepicker
        dateFormat: 'yy-mm-dd', 
        changeMonth: true,
        changeYear: true,
        showOn: "button",
        buttonImage: "css/images/calendar.gif",
        buttonImageOnly: true,
        buttonText: "Select date"
    });
    
    $( "#dispFilTrk_dateTo" ).datepicker({                                  // Initalise field to select to date as JQUERY datepicker
        dateFormat: 'yy-mm-dd',
        changeMonth: true,
        changeYear: true,
        showOn: "button",
        buttonImage: "css/images/calendar.gif",
        buttonImageOnly: true,
        buttonText: "Select date"
    });

    $( "#dispFilTrk_type" ).selectable({});                                 // Initialse field 'type' as JQUERY selectable

    $( "#dispFilTrk_subtype" ).selectable({});                              // Initialse field 'subtype' as JQUERY selectable

    // For object filter segments
    // --------------------------

    // mapUF_sourceName
    $( "#dispFilSeg_sourceName" ).autocomplete({
        source: "services/get_auto_complete_values.php?field=segSourceFID",
        minLength: 2,
        select: function( event, ui ) {
            $( "#dispFilSeg_sourceFID" ).val( ui.item.id );
        },
        change: function( event, ui ) {
            if ( $( "#dispFilSeg_sourceName" ).val() == '' ) {
                    $( "#dispFilSeg_sourceFID" ).val( '' );
            }
        }
    });
    var mapUF_sourceFID = $( "#dispFilSeg_sourceFID" );

    // mapUF_segType
    $( "#dispFilSeg_segType" ).selectable({});
    
    // startLocName
    $( "#dispFilSeg_startLocName" ).autocomplete({
        source: "services/get_auto_complete_values.php?field=getWaypLong",
        minLength: 1,
        select: function( event, ui ) {
            $( "#dispFilSeg_startLocID" ).val( ui.item.id );
        },
        change: function( event, ui ) {
            if ( $( "#dispFilSeg_startLocName" ).val() == '' ) {
                    $( "#dispFilSeg_startLocID" ).val( '' );
            }
        }
    });
    var mapUF_startLocID = $( "#dispFilSeg_startLocID" ); 
    
    // startLocAlt 
    $( "#dispFilSeg_startLocAlt_slider" ).slider({
        range: true,
        min: 0,
        max: 5000,
        values: [ 400, 5000 ],
        slide: function( event, ui ) {
            $( "#dispFilSeg_startLocAlt_slider_values" ).val( "min. " + ui.values[ 0 ] + "m - max. " + ui.values[ 1 ] + "m" );
        }
    });
    $( "#dispFilSeg_startLocAlt_slider_values" ).val( "min. " + $( "#dispFilSeg_startLocAlt_slider" ).slider( "values", 0 ) +
    "m - max. " + $( "#dispFilSeg_startLocAlt_slider" ).slider( "values", 1 ) +"m" );
    
    // startLocType
    $( "#dispFilSeg_startLocType" ).selectable({});

    // targetLocName
    $( "#dispFilSeg_targetLocName" ).autocomplete({
        source: "services/get_auto_complete_values.php?field=getWaypLong",
        minLength: 1,
        select: function( event, ui ) {
            $( "#dispFilSeg_targetLocID" ).val( ui.item.id );
        },
        change: function( event, ui ) {
            if ( $( "#dispFilSeg_targetLocName" ).val() == '' ) {
                    $( "#dispFilSeg_targetLocID" ).val( '' );
            }
        }
    });
    var mapUF_targetLocID = $( "#dispFilSeg_targetLocID" ); 

    // targetLocAlt
    $( "#dispFilSeg_targetLocAlt_slider" ).slider({
        range: true,
        min: 0,
        max: 5000,
        values: [ 400, 5000 ],
        slide: function( event, ui ) {
            $( "#dispFilSeg_targetLocAlt_slider_values" ).val( "min. " + ui.values[ 0 ] + "m - max. " + ui.values[ 1 ] + "m" );
        }
    });
    $( "#dispFilSeg_targetLocAlt_slider_values" ).val( "min. " + $( "#dispFilSeg_targetLocAlt_slider" ).slider( "values", 0 ) +
    "m - max. " + $( "#dispFilSeg_targetLocAlt_slider" ).slider( "values", 1 ) +"m" );

    // targetLocType
    $( "#dispFilSeg_targetLocType" ).selectable({});

    // Region
    $( "#dispFilSeg_segRegion" ).autocomplete({
        source: "services/get_auto_complete_values.php?field=regionID",
        minLength: 1,
        select: function( event, ui ) {
            $( "#dispFilSeg_segRegionID" ).val( ui.item.id );
        },
        change: function( event, ui ) {
            if ( $( "#dispFilSeg_segRegion" ).val() == '' ) {
                    $( "#dispFilSeg_segRegionID" ).val( '' );
            }
        }
    });
    var mapUF_segRegionID = $( "#dispFilSeg_segRegionID" ); 

    // Area
    $( "#dispFilSeg_segArea" ).autocomplete({
        source: "services/get_auto_complete_values.php?field=areaID",
        minLength: 1,
        select: function( event, ui ) {
            $( "#dispFilSeg_segAreaID" ).val( ui.item.id );
        },
        change: function( event, ui ) {
            if ( $( "#dispFilSeg_segArea" ).val() == '' ) {
                    $( "#dispFilSeg_segAreaID" ).val( '' );
            }
        }
    });
    var mapUF_segAreaID = $( "#dispFilSeg_segAreaID" ); 

    $( "#dispFilSeg_grade" ).selectable({});
    $( "#dispFilSeg_climbGrade" ).selectable({});
    $( "#dispFilSeg_ehaft" ).selectable({});   



    // ******************************************************************
    // Executes code below when user clicks the 'Apply' filter button for tracks

    $(document).on('click', '#dispFilTrk_ApplyButton', function (e) {
        e.preventDefault();
        
        // *****************************************************
        // Build SQL WHERE statement for segments
        
        var whereStatement = [];
        var whereString = "";

        // Field track name
        if ( ($('#dispFilTrk_trackName').val()) != "" ) {                           
            whereString = "trkTrackName like '%" + $('#dispFilTrk_trackName').val() + "%'";
            whereStatement.push( whereString );
        };

        // Field route
        if ( ($('#dispFilTrk_route').val()) != "" ) {
            whereString = "trkRoute like '%" + $('#dispFilTrk_route').val() + "%'";
            whereStatement.push( whereString );
        };

        // Field date begin (date finished not used)
        fromDate = "1968-01-01";                                                    // Set from date in case no date is entered
        var today = new Date();                                                     // Set to date to today in case no date is entered
        month = today.getMonth()+1;                                                 // Extract month (January = 0)
        toDate = today.getFullYear() + '-' + month + '-' + today.getDate();         // Set to date to today (format yyyy-mm-dd)

        if ( ($('#dispFilTrk_dateFrom').val()) != "" ) {                            // Overwrite fromDate with value entered by user
            fromDate = ($('#dispFilTrk_dateFrom').val());
        };

        if ( ($('#dispFilTrk_dateTo').val()) != "" ) {                              // Overwrite toDate with value entered by user
            toDate = ($('#dispFilTrk_dateTo').val())                                // Add to where Statement array
        };

        whereString = "trkDateBegin BETWEEN '" + fromDate + "' AND '" + toDate + "'";   // complete WHERE BETWEEN statement
        whereStatement.push( whereString );                                         // Add to where Statement array

        // Field type
        var whereString = "";
        $('#dispFilTrk_type .ui-selected').each(function() {                        // loop through each selected type item
            var itemId = this.id                                                    // Extract id of selected item
            whereString = whereString + "'" + itemId.slice(16) + "',";              // Substring tyye from id
        });
        if ( whereString.length > 0 ) {
            whereString = whereString.slice(0,whereString.length-1);                // remove last comma
            whereString = "trkType in (" + whereString + ")";                        // complete SELECT IN statement
            whereStatement.push( whereString );                                     // Add to where Statement array
        };

        // Field subtype
        var whereString = "";                                                       
        $('#dispFilTrk_subtype .ui-selected').each(function() {                     // loop through each selected type item
            var itemId = this.id                                                    // Extract id of selected item
            whereString = whereString + "'" + itemId.slice(19) + "',";              // Substring tyye from id
        });
        if ( whereString.length > 0 ) {
            whereString = whereString.slice(0,whereString.length-1);                // remove last comma
            whereString = "trkSubType in (" + whereString + ")";                     // complete SELECT IN statement
            whereStatement.push( whereString );                                     // Add to where Statement array
        }           

        // Field participants
        if ( ($('#dispFilTrk_participants').val()) != "" ) {
            whereString = "trkParticipants like '%" + $('#dispFilTrk_participants').val() + "%'";
            whereStatement.push( whereString );
        };

        // Field country
        if ( ($('#dispFilTrk_country').val()) != "" ) {
            whereString = "trkCountry like '%" + $('#dispFilTrk_country').val() + "%'";
            whereStatement.push( whereString );
        };
        
        // ************************************
        // Put all where statements together

        if ( whereStatement.length > 0 ) {
            var sqlWhere = "WHERE ";

            for (var i=0; i<whereStatement.length; i++) {
                sqlWhere += whereStatement[i];
                sqlWhere += " AND ";
            }
            sqlWhere = sqlWhere.slice(0,sqlWhere.length-5);
        }
        
        // ****************************************************
        // Generate KML & draw Map 

        callGenKml(trackFileName,"tracks",sqlWhere);                       // Generate KML file (file name to be used,type of kml = "tracks", sql where clause)
        
        // Close filter panels at the end
        $('#mapPanelFilter').removeClass('visible');

        // Panel MAP: Remove map div and redraw map if mapMapNeedsLoad is true
        var removeEl = document.getElementById('displayMap-ResMap');  // delete div .map
        var containerEl = removeEl.parentNode;          // Get its containing element
        containerEl.removeChild(removeEl);              // Remove the elements
        var newDiv = document.createElement('div');     // create new div element
        containerEl.appendChild(newDiv);                // Add to parent element
        newDiv.id = 'displayMap-ResMap';
        newDiv.className = 'displayMap-ResMap'; 
        drawMapOld('displayMap-ResMap', trackKmlFileNameURL, "", 0, 0, 0, 1, 0); // Draw map to panel
        mapMapNeedsLoad = false;                             // No need to load map
    });

    // ***************************
    // Executes code below when user clicks the 'Apply' filter button for segments
    $(document).on('click', '#dispFilSeg_ApplyButton', function (e) {
        e.preventDefault();
        var whereStatement = [];

        // ==================================================
        // ===== Build SQL WHERE statement for segments =====
        // ==================================================
        
        // Field segment type selected
        var whereString = "";
        $('#dispFilSeg_segType .ui-selected').each(function() {
            var itemId = this.id;
            var sqlName = "segType";
            var lenCriteria = itemId.length;
            var startCriteria = sqlName.length + 1;
            whereString = whereString + "'" + itemId.slice(startCriteria,lenCriteria) + "',";
            if ( whereString.length > 0 ) {
                whereString = whereString.slice(0,whereString.length-1);                // remove last comma
                whereString = "segType in (" + whereString + ")";                       // complete SELECT IN statement
                whereStatement.push( whereString );                                     // Add to where Statement array
            };          
        });

        // Field segment name
        var whereString = "";
        if ( ($('#dispFilSeg_segName').val()) != "" ) {
            whereString = "segName like '%" + ($('#dispFilSeg_segName').val()) + "%'";      
            whereStatement.push( whereString );
        };

        // Field Start Location (ID selected)
        var whereString = "";
        if ( ($('#dispFilSeg_startLocID').val()) != "" ) {
            whereString = "segStartLocationFID = " + ($('#dispFilSeg_startLocID').val()); 
            whereStatement.push( whereString );
        };

        // Field Altitude of start location 
        var whereString = "";
        whereString = " startLocAlt >= " + $( "#dispFilSeg_startLocAlt_slider" ).slider( "values", 0 );
        whereString += " AND startLocAlt <= " + $( "#dispFilSeg_startLocAlt_slider" ).slider( "values", 1 );
        whereStatement.push( whereString );     

        // Field type of start location
        var whereString = "";
        $('#dispFilSeg_startLocType .ui-selected').each(function() {
            var itemId = this.id;
            var sqlName = "startLocType";
            var lenCriteria = itemId.length;
            var startCriteria = sqlName.length + 1;
            whereString = whereString + itemId.slice(startCriteria,lenCriteria) + "',";  
            if ( whereString.length > 0 ) {
                whereString = whereString.slice(0,whereString.length-1);                // remove last comma
                whereString = sqlName + " in (" + whereString + ")";                       // complete SELECT IN statement
                whereStatement.push( whereString );                                     // Add to where Statement array
            };       
        });

        // Field target location (ID selected)
        var whereString = "";
        if ( ($('#dispFilSeg_targetLocID').val()) != "" ) {
            whereString = "segTargetLocationFID = " + ($('#dispFilSeg_startLocID').val()); 
            whereStatement.push( whereString );
        };

        // Field target location altitude
        var whereString = "";
        whereString = " targetLocAlt >= " + $( "#dispFilSeg_targetLocAlt_slider" ).slider( "values", 0 );
        whereString += " AND startLocAlt <= " + $( "#dispFilSeg_targetLocAlt_slider" ).slider( "values", 1 );
        whereStatement.push( whereString );            

        // Field target location type
        var whereString = "";
        var itemId = this.id;
        var sqlName = "targetLocType";
        var lenCriteria = itemId.length;
        var startCriteria = sqlName.length + 1;
        whereString = whereString + itemId.slice(startCriteria,lenCriteria) + "',";  
        if ( whereString.length > 0 ) {
            whereString = whereString.slice(0,whereString.length-1);                // remove last comma
            whereString = sqlName + " in (" + whereString + ")";                       // complete SELECT IN statement
            whereStatement.push( whereString );                                     // Add to where Statement array
        };

        // Field region
        var whereString = "";
        if ( ($('#dispFilSeg_segRegionID').val()) != "" ) {
            whereString = "regionId = " + ($('#dispFilSeg_segRegionID').val()); 
            whereStatement.push( whereString );
        };
        

        // Field area
        var whereString = "";
        if ( ($('#dispFilSeg_segAreaID').val()) != "" ) {
            whereString = "areaId = " + ($('#dispFilSeg_segAreaID').val()); 
            whereStatement.push( whereString );
        };
       
        // Field grade
        var whereString = "";
        $('#dispFilSeg_grade .ui-selected').each(function() {
            var itemId = this.id;
            var sqlName = "grade";
            var lenCriteria = itemId.length;
            var startCriteria = sqlName.length + 1;
            whereString = whereString + itemId.slice(startCriteria,lenCriteria) + "',";  
            if ( whereString.length > 0 ) {
                whereString = whereString.slice(0,whereString.length-1);                // remove last comma
                whereString = sqlName + " in (" + whereString + ")";                       // complete SELECT IN statement
                whereStatement.push( whereString );                                     // Add to where Statement array
            };
        });
        
        // Field climbGrade
        var whereString = "";
        $('#dispFilSeg_climbGrade .ui-selected').each(function() {
            var itemId = this.id;
            var sqlName = "climbGrade";
            var lenCriteria = itemId.length;
            var startCriteria = sqlName.length + 1;
            whereString = whereString + itemId.slice(startCriteria,lenCriteria) + "',";  
            if ( whereString.length > 0 ) {
                whereString = whereString.slice(0,whereString.length-1);                // remove last comma
                whereString = sqlName + " in (" + whereString + ")";                       // complete SELECT IN statement
                whereStatement.push( whereString );                                     // Add to where Statement array
            };
        });

        // Field Ernsthaftigkeit
        var whereString = "";
        $('#dispFilSeg_ehaft .ui-selected').each(function() {
            var itemId = this.id;
            var sqlName = "ehaft";
            var lenCriteria = itemId.length;
            var startCriteria = sqlName.length + 1;
            whereString = whereString + itemId.slice(startCriteria,lenCriteria) + "',";  
            if ( whereString.length > 0 ) {
                whereString = whereString.slice(0,whereString.length-1);                // remove last comma
                whereString = sqlName + " in (" + whereString + ")";                       // complete SELECT IN statement
                whereStatement.push( whereString );                                     // Add to where Statement array
            };
        });

        // =================================================
        // ============ generate KML & draw Map ============
        // =================================================

        callGenSegKml(segSqlFilterString); // Generate KML file; file stored in file defined by global var segKmlFileNameURL
        callGenWaypKml(optionWhereStmt); // Generate KML file; file stored in file defined by global var segKmlFileNameURL 
        
        // Close filter panels at the end
        $('#mapPanelFilter').removeClass('visible');

        // Panel MAP: Remove map div and redraw map if mapMapNeedsLoad is true
        var removeEl = document.getElementById('mapPanel_Map-ResMap');  // delete div .map
        var containerEl = removeEl.parentNode;          // Get its containing element
        containerEl.removeChild(removeEl);              // Remove the elements
        var newDiv = document.createElement('div');     // create new div element
        containerEl.appendChild(newDiv);                // Add to parent element
        newDiv.id = 'mapPanel_Map-ResMap';
        newDiv.className = 'mapPanel_Map-ResMap'; 
        drawMapOld('mapPanel_Map-ResMap', segKmlFileNameURL, waypKmlFileNameURL, 
            drawHangneigung, drawWanderwege, drawHaltestellen, 
            drawKantonsgrenzen, drawSacRegion); // Draw map to panel
        mapMapNeedsLoad = false;                             // No need to load map
        
    });
});    

// Function drawing empty map -- for documentation see: https://api3.geo.admin.ch/
function drawMapEmpty(targetDiv) {
    var map = new ga.Map({
        target: targetDiv,
        view: new ol.View({resolution: 100, center: [670000, 160000]})
    });

    // Create a background layer
    var lyr1 = ga.layer.create('ch.swisstopo.pixelkarte-farbe');
    map.addLayer(lyr1);
}

// =============================================
// ============ F U N C T I O N S ==============
// =============================================

// Function generating KML file for segments

function callGenKml(outFileName, kmlType, sqlWhere) {   
    var xhr = new XMLHttpRequest();
    phpLocation = document.URL + "services/gen_kml.php";          // Variable to store location of php file
    xhrParams =  "outFileName=" + outFileName;
    xhrParams += "&kmlType=" + kmlType;   // Variable for POST parameters
    xhrParams += "&sqlWhere=" + sqlWhere ;
    xhr.open ('POST', phpLocation, false);                // Make XMLHttpRequest - in asynchronous mode to avoid wrong data display in map (map displayed before KML file is updated)
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");  // Set header to encode special characters like %
    xhr.send(encodeURI(xhrParams));
}   

function drawMapOld(targetDiv, segKmlFile, waypKmlFile, drawHangneigung, drawWanderwege, drawHaltestellen, 
    drawKantonsgrenzen, drawSacRegion) {
    // ==> map.admin.ch code from here

    // Create a GeoAdmin Map
    var map = new ga.Map({
    target: targetDiv,
    view: new ol.View({resolution: 650, center: [660000, 190000]})
    });

    // Create a background layer
    var lyr1 = ga.layer.create('ch.swisstopo.pixelkarte-farbe');

    // Add the background layer in the map
    map.addLayer(lyr1);

    // Create  KML Layer for Hangneigung
    if ( drawHangneigung ) { 
    var lyrHangneigung = ga.layer.create('ch.swisstopo.hangneigung-ueber_30');
    map.addLayer(lyrHangneigung);
    }

    // Create  KML Layer for Wanderwege
    if ( drawWanderwege ) { 
    var lyrWanderwege = ga.layer.create('ch.swisstopo.swisstlm3d-wanderwege');
    map.addLayer(lyrWanderwege);
    }

    // Create  KML Layer for ÖV-Haltestellen
    if ( drawHaltestellen ) { 
    var lyrHaltestellen = ga.layer.create('ch.bav.haltestellen-oev');
    map.addLayer(lyrHaltestellen);
    }

    // Create  KML Layer for Kantonsgrenzen
    if ( drawKantonsgrenzen ) { 
    var lyrKantonsgrenzen = ga.layer.create('ch.swisstopo.swissboundaries3d-kanton-flaeche.fill');
    map.addLayer(lyrKantonsgrenzen);
    }

    // Create  KML Layer for SAC regions 
    if ( drawSacRegion ) { 
    var segVector = new ol.layer.Vector({
    source: new ol.source.Vector({
        url: "./images/SacRegions.kml", 
        format: new ol.format.KML({
            projection: 'EPSG:21781'
        })
    })
    });
    map.addLayer(segVector);
    }

    // Create the KML Layer for segments
    var segVector = new ol.layer.Vector({
    source: new ol.source.Vector({
    url: segKmlFile, 
    format: new ol.format.KML({
        projection: 'EPSG:21781'
    })
    })
    });
    map.addLayer(segVector);
    console.info('Layer segments drawn using kml file: ' + segKmlFile );   

    // Create the KML Layer for waypoints
    var waypVector = new ol.layer.Vector({
    source: new ol.source.Vector({
    url: waypKmlFile, 
    format: new ol.format.KML({
        projection: 'EPSG:21781'
    })
    })
    });
    map.addLayer(waypVector);
    console.info('Layer waypoints drawn using kml file: ' + waypKmlFile );

    // Popup showing the position the user clicked
    var popup = new ol.Overlay({
    element: $('<div title="KML"></div>')[0]
    });
    map.addOverlay(popup);

    // On click we display the feature informations
    map.on('singleclick', function(evt) {
    var pixel = evt.pixel;
    var coordinate = evt.coordinate;
    var feature = map.forEachFeatureAtPixel(pixel, function(feature, layer) {
    return feature;
    });
    var element = $(popup.getElement());
    element.popover('destroy');
    if (feature) {
    popup.setPosition(coordinate);
    element.popover({
    'placement': 'top',
    'animation': false,
    'html': true,
    'content': feature.get('description')
    });
    element.popover('show');
    }
    });

    // Change cursor style when cursor is hover a feature
    map.on('pointermove', function(evt) {
    var feature = map.forEachFeatureAtPixel(evt.pixel, function(feature, layer) {
    return feature;
    });
    map.getTargetElement().style.cursor = feature ? 'pointer' : '';
    });
}
    