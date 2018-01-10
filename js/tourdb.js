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

// ======================================================
// ====== Perform these actions when page is ready ======
// ======================================================
$(document).ready(function() {

    // Initial drawing of map
    if ( navigator.onLine ) {
        drawMapEmpty('displayMap-ResMap');         // Draw empty map (without additional layers) 
    };

    // Evaluate which button/panel is active
    $('.topicButtons').each(function() {
        var $thisTopicButton = $(this);                                     // $thisTopicButton becomes ul.topicButtons
        $activeButton = $thisTopicButton.find('li.active');                 // Find and store current active li element
        var $activeButtonA = $activeButton.find('a');                       // Get link <a> from active li element 
        $topicButton = $($activeButtonA.attr('href'));                      // Get active panel      
    });

    // Change to selected panel
    $(this).on('click', '.mainButtonsA', function(e) {                  
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
      
    // ==========================================================================
    // ========================== panelLogin ====================================
    // ==========================================================================

    // ............................................................................
    // Executes code below when user clicks the 'Login' button
    $(document).on('click', '#buttonLogin', function (e) {
        e.preventDefault();                                                                                 
        var xhr = new XMLHttpRequest();                                                                     // create new xhr object
        
        // Execute following code JSON object is received from importGpsTmp.php service
        xhr.onload = function() {
            if (xhr.status === 200) {                                                                       // when all OK
                responseObject = JSON.parse(xhr.responseText);                                              // transfer JSON into response object array

                sessionid = responseObject.sessionid; 
                loginstatus = responseObject.loginstatus;
                if ( loginstatus == "ERROR")
                { 
                    $('#statusMessage').text('Login failed');
                    $('#statusMessage').show().delay(5000).fadeOut();
                } else {
                    // Open Panel Display
                    var $activeButtonA = $('#a_panelDisplay');                                    // Store the current link <a> element
                    //$topicButton = $($activeButtonA.attr('href'));
                    buttonId = $activeButtonA.attr('href'); 
                    
                    // Run following block if selected topic is currently not active
                    $topicButton.removeClass('active');                         // Make current panel inactive
                    $activeButton.removeClass('active');                        // Make current tab inactive
                    $topicButton = $(buttonId).addClass('active');              // Make new panel active
                    $activeButton = $activeButtonA.parent().addClass('active'); // Make new tab active
                    $('.loginReq').removeClass('loginReq');
                    $('#buttonLogin').addClass('loginReq');
                    $('#statusMessage').text('Login successful');
                    $("#statusMessage").show().delay(5000).fadeOut();
                }
            }
        }

        var jsonObject = {};
        $loginName = ($('#loginName').val());
        phpLocation = document.URL + "services/login.php";          // Variable to store location of php file
        jsonObject["loginName"] = $loginName;                             // append parameter session ID
        jsonObject["loginPasswd"] = ($('#loginPasswd').val());                              // temp request to create track temporarily
        xhr.open ('POST', phpLocation, true);                           // open  XMLHttpRequest 
        xhr.setRequestHeader( "Content-Type", "application/json" );
        jsn = JSON.stringify(jsonObject);
        xhr.send( jsn );                                           // send formData object to service using xhr   
    });
});

// ==========================================================================
// ========================== panelDisplay ==================================
// ==========================================================================

// On click the minimize large display objects icon to minimized
$(document).on('click', '#dispObjMenuLargeClose', function(e) {
    e.preventDefault();
    var $activeButton = $(this);
    $activeButton.parent().removeClass('visible');
    $activeButton.parent().addClass('hidden');
    $('.dispObjMini').removeClass('hidden');
    $('.dispObjMini').addClass('visible');
})

// On click of minimized filter icon --> open large display objects mask
$(document).on('click', '#dispObjMenuMiniOpen', function(e) {
    e.preventDefault();
    var $activeButton = $(this);
    $activeButton.parent().removeClass('visible');
    $activeButton.parent().addClass('hidden');
    $('.dispObjOpen').removeClass('hidden');
    $('.dispObjOpen').addClass('visible');
})

// Initialse all jquery functional fields for the mask display objects
$( function() {  
    // Initialise filter area as JQUERY Accordion                                                       
    $( "#dispObjAccordion" ).accordion({
        heightStyle: "content",                                            // hight of section dependent on content of section
        autoHeight: false,
        collapsible: true
    });

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

} );

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

// Executes code below when user clicks the 'Apply' filter button for segments
$(document).on('click', '#dispFilSeg_ApplyButton', function (e) {
    e.preventDefault();
    var whereStatement = [];

    // ===== Build SQL WHERE statement for segments =====
    
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

// ==========================================================================
// ========================== panelImport ===================================
// ==========================================================================

// Upon click on the 'Upload File' button --> call importGps.php in temp mode
$(document).on('click', '#buttonUploadFile', function (e) {
    e.preventDefault();                                                                                 
    var xhr = new XMLHttpRequest();                                                                     // create new xhr object
    
    // Execute following code JSON object is received from importGpsTmp.php service
    xhr.onload = function() {
        if (xhr.status === 200) {                                                                       // when all OK
            responseObject = JSON.parse(xhr.responseText);                                              // transfer JSON into response object array
            if ( responseObject.status == 'OK') {
                $('#impUpdTrk_trkId').attr('value', responseObject.trkId); 
                $('#impUpdTrk_trkTrackName').attr('value', responseObject.trkTrackName);                    // assign values of JSON to input fields
                $('#impUpdTrk_trkRoute').attr('value', responseObject.trkRoute);
                $('#impUpdTrk_trkDateBegin').attr('value', responseObject.trkDateBegin);
                $('#impUpdTrk_trkDateFinish').attr('value', responseObject.trkDateFinish);
                $('#impUpdTrk_trkDistance').attr('value', responseObject.trkDistance);
                $('#impUpdTrk_trkTimeOverall').attr('value', responseObject.trkTimeOverall);
                $('#impUpdTrk_trkMeterUp').attr('value', responseObject.trkMeterUp);
                $('#impUpdTrk_trkMeterDown').attr('value', responseObject.trkMeterDown);
                $('#impUpdTrk_trkCountry').attr('value', responseObject.trkCountry);
                $('#impUpdTrk_trkCoordinates').attr('value', responseObject.trkCoordinates);

                // Close upload file div and open form to update track data
                $('#pImpFileUpload').removeClass('active');
                $('#pImpUpdateTrack').addClass('active');
                document.getElementById("inputFile").value = "";
            } else {
                $('#statusMessage').text('Invalid file extension');
                $("#statusMessage").show().delay(5000).fadeOut();
                document.getElementById("inputFile").value = "";
            } 

        }
    }

    var fileName = document.getElementById('inputFile').files[0];   // assign selected file var
    if ( fileName ) {
        phpLocation = document.URL + "services/importGps.php";          // Variable to store location of php file
        var formData = new FormData();                                  // create new formData object
        formData.append('sessionid', sessionid);                           // append parameter session ID
        formData.append('request', 'temp')                              // temp request to create track temporarily
        formData.append('filename', fileName);                          // append parameter filename
        formData.append('filetype', "gpx");                             // append parameter file type
        formData.append('loginname', $loginName);                             // append parameter file type
        xhr.open ('POST', phpLocation, true);                           // open  XMLHttpRequest 
        xhr.send(formData);                                             // send formData object to service using xhr
    } else {
        $('#statusMessage').text('No file selected');
        $("#statusMessage").show().delay(5000).fadeOut();
    }
});

// Upon click on the 'Save' button --> call importGps.php in save mode
$(document).on('click', '#impUpdTrk_save', function (e) {
    e.preventDefault();
    
    var xhr = new XMLHttpRequest();                                 // create new xhr object
    // Execute following code JSON object is received from importGpsTmp.php service
    xhr.onload = function() {
        if (xhr.status === 200) {                                   // when all OK
            // Make panelImport disappear and panelDisplay appear
            $('#statusMessage').text('Track successfully saved');
            $('#statusMessage').show().delay(5000).fadeOut();

            // Open Panel Display
            var $activeButtonA = $('#a_panelDisplay');                                    // Store the current link <a> element
            buttonId = $activeButtonA.attr('href'); 
            
            // Run following block if selected topic is currently not active
            $topicButton.removeClass('active');                         // Make current panel inactive
            $activeButton.removeClass('active');                        // Make current tab inactive
            $topicButton = $(buttonId).addClass('active');              // Make new panel active
            $activeButton = $activeButtonA.parent().addClass('active'); // Make new tab active
            
            // Close upload file div and open form to update track data
            $('#pImpFileUpload').addClass('active');
            $('#pImpUpdateTrack').removeClass('active');
        }
    }

    var trackobj = {};
    var jsonObject = {};
    trackobj["trkId"] = $('#impUpdTrk_trkId').val();
    trackobj["trkTrackName"] = $('#impUpdTrk_trkTrackName').val();
    trackobj["trkRoute"] = $('#impUpdTrk_trkRoute').val();
    trackobj["trkDateBegin"] = $('#impUpdTrk_trkDateBegin').val();     
    trackobj["trkDateFinish"] = $('#impUpdTrk_trkDateFinish').val();
    trackobj["trkSaison"] = $('#impUpdTrk_trkSaison').val();
    trackobj["trkType"] = $('#impUpdTrk_trkType').val();
    trackobj["trkSubType"] = $('#impUpdTrk_trkSubType').val();
    trackobj["trkOrg"] = $('#impUpdTrk_trkOrg').val();
    trackobj["trkOvernightLoc"] = $('#impUpdTrk_trkOvernightLoc').val();
    trackobj["trkParticipants"] = $('#impUpdTrk_trkParticipants').val();
    trackobj["trkEvent"] = $('#impUpdTrk_trkEvent').val();
    trackobj["trkRemarks"] = $('#impUpdTrk_trkRemarks').val();
    trackobj["trkDistance"] = $('#impUpdTrk_trkDistance').val();
    trackobj["trkTimeOverall"] = $('#impUpdTrk_trkTimeOverall').val();
    trackobj["trkTimeToTarget"] = $('#impUpdTrk_trkTimeToTarget').val();
    trackobj["trkTimeToEnd"] = $('#impUpdTrk_trkTimeToEnd').val();
    trackobj["trkGrade"] = $('#impUpdTrk_trkGrade').val();
    trackobj["trkMeterUp"] = $('#impUpdTrk_trkMeterUp').val();
    trackobj["trkMeterDown"] = $('#impUpdTrk_trkMeterDown').val();
    trackobj["trkCountry"] = $('#impUpdTrk_trkCountry').val();      
    trackobj["trkCoordinates"] = $('#impUpdTrk_trkCoordinates').val();  
    trackobj["trkLoginName"] = $loginName;    

    phpLocation = document.URL + "services/importGps.php";          // Variable to store location of php file
    jsonObject["sessionid"] = sessionid;                             // append parameter session ID
    jsonObject["request"] = 'save';                              // temp request to create track temporarily
    jsonObject["filetype"] = "gpx";                                 // append parameter file type
    jsonObject["trackobj"] = trackobj;                              // send track object
    xhr.open ('POST', phpLocation, true);                           // open  XMLHttpRequest 
    console.info(jsonObject);
    xhr.setRequestHeader( "Content-Type", "application/json" );
    jsn = JSON.stringify(jsonObject);
    xhr.send( jsn );                                           // send formData object to service using xhr
});

// On click on the 'cancel' button --> cancel update & delete temp track
$(document).on('click', '#impUpdTrk_cancel', function (e) {
    e.preventDefault();
    
    var xhr = new XMLHttpRequest();                                 // create new xhr object
    // Execute following code JSON object is received from importGpsTmp.php service
    xhr.onload = function() {
        if (xhr.status === 200) {                                   // when all OK
            $('#pImpFileUpload').addClass('active');                 // Make File upload div visible
            $('#pImpUpdateTrack').removeClass('active');                   // hide update form
            $('#statusMessage').text('Import cancelled');
            $("#statusMessage").show().delay(5000).fadeOut();
        }
    }

    var trackobj = {};
    var jsonObject = {};
    trackobj["trkId"] = $('#impUpdTrk_trkId').val();
    
    phpLocation = document.URL + "services/importGps.php";          // Variable to store location of php file
    //var formData = new FormData();                                  // create new formData object
    jsonObject["sessionid"] = sessionid;                             // append parameter session ID
    jsonObject["request"] = 'cancel';                              // temp request to create track temporarily
    jsonObject["filetype"] = "gpx";                                 // append parameter file type
    jsonObject["trackobj"] = trackobj;                              // send track object
    xhr.open ('POST', phpLocation, true);                           // open  XMLHttpRequest 
    xhr.setRequestHeader( "Content-Type", "application/json" );
    jsn = JSON.stringify(jsonObject);
    xhr.send( jsn );                                           // send formData object to service using xhr   
});

// =============================================
// ============ F U N C T I O N S ==============
// =============================================

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