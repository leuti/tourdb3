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

// file location & name for KML outputs
trackFileName = "track_" + today.getTime() + ".kml";
//trackFileNameURLtrackFileNameURL = document.URL + "tmp/kml_disp/" + trackFileName;

segmentFileName = "seg_" + today.getTime() + ".kml";
segmentFileNameURL = document.URL + "tmp/kml_disp/" + segmentFileName;

sqlWhereTracksPrev = "";                                            // variable to store previous sql where statement
sqlWhereSegmentsPrev = "";                                          // the gen_kml.php is only called if statement has changed

var trackKMLlayer;
var segKMLlayer;
var mapSTlayer_grau;

var peaksArray = new Array();
var peakNr = 0;

// ======================================================
// ====== Perform these actions when page is ready ======
// ======================================================
$(document).ready(function() {

    // Initialse all jquery functional fields for the mask display objects
    $( function() {  
        // ================================================
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

        // =====================================
        // ====== UI to Admin Tracks
        $( "#uiAdmTrk" ).tabs();


        valComments = $( "#validateComments" );

        $( "#uiAdmTrk_fld_trkDateBegin" ).datepicker({                                // Initalise field to select start date as JQUERY datepicker
            dateFormat: 'yy-mm-dd', 
            changeMonth: true,
            changeYear: true,
            showOn: "button",
            buttonImage: "css/images/calendar.gif",
            buttonImageOnly: true,
            buttonText: "Select date"
        });

        $( "#uiAdmTrk_fld_trkDateFinish" ).datepicker({                                // Initalise field to select start date as JQUERY datepicker
            dateFormat: 'yy-mm-dd', 
            changeMonth: true,
            changeYear: true,
            showOn: "button",
            buttonImage: "css/images/calendar.gif",
            buttonImageOnly: true,
            buttonText: "Select date"
        });

        $( "#uiAdmTrk_fld_trkSaison" ).selectmenu();
        $( "#uiAdmTrk_fld_trkType" ).selectmenu();
        $( "#uiAdmTrk_fld_trkSubType" ).selectmenu();

        $( "#uiAdmTrk_peakSrch" ).autocomplete({
            source: "services/autoComplete.php?field=wayp",
            minLength: 2,
            select: function( event, ui ) {
                $( "" ).val( ui.item.id );
                id = ui.item.id;
                value = ui.item.value;
                
                console.info("Line 228: select event on autocomplete bird detected: " + value + " - " + id);
            }/*,
            change: function( event, ui ) {
                if ( $( "#uiAdmTrk_peakSrch" ).val() == '' ) {
                    $( "#uiAdmTrk_peakSrch" ).val( '' );
                    $( "" ).val( ui.item.id );
                }
                    value = ui.item.id;
                    id = ui.item.value;
                    
                    console.info("Line 240: select event on autocomplete bird detected: " + value + " - " + id);
            }*/
        });
    } );

    // Evaluate which button/panel is active
    $('.navBtns_btns').each(function() {
        var $thisTopicButton = $(this);                                     // $thisTopicButton becomes ul.navBtns_btns
        $activeButton = $thisTopicButton.find('li.active');                 // Find and store current active li element
        var $activeButtonA = $activeButton.find('a');                       // Get link <a> from active li element 
        $topicButton = $($activeButtonA.attr('href'));                      // Get active panel      
    });

    // Evaluate which button/panel is active
    $('.uiAdmTrk_btns').each(function() {
        var $clickedUpdTrkBtn = $(this);                                     // $clickedUpdTrkBtn becomes ul.uiAdmTrk_btns
        $actUpdTrkBtn = $clickedUpdTrkBtn.find('li.active');                 // Find and store current active li element
        var $clickedUpdTrkButton_liA = $actUpdTrkBtn.find('a');                       // Get link <a> from active li element 
        $actUpdTrkTab = $($clickedUpdTrkButton_liA.attr('href'));                      // Get active panel      
    });

    // Change to selected panel
    $(this).on('click', '.navBtns_btns_a', function(e) {                  
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
    $(document).on('click', '#navBtns_btn_login', function (e) {
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
                    var $activeButtonA = $('#navBtns_btn_diplay_a');                                    // Store the current link <a> element
                    //$topicButton = $($activeButtonA.attr('href'));
                    buttonId = $activeButtonA.attr('href'); 
                    
                    // Run following block if selected topic is currently not active
                    $topicButton.removeClass('active');                         // Make current panel inactive
                    $activeButton.removeClass('active');                        // Make current tab inactive
                    $topicButton = $(buttonId).addClass('active');              // Make new panel active
                    $activeButton = $activeButtonA.parent().addClass('active'); // Make new tab active
                    $('.loginReq').removeClass('loginReq');
                    $('#navBtns_btn_login').addClass('loginReq');
                    $('#statusMessage').text('Login successful');
                    $("#statusMessage").show().delay(5000).fadeOut(); 
                    $map = drawMapEmpty('displayMap-ResMap');         // Draw empty map (without additional layers) 
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

// Executes code below when user clicks the 'Apply' filter button for tracks
$(document).on('click', '.applyFilterButton', function (e) {
    e.preventDefault();
    sqlWhereTracks = "";
    sqlWhereSegments = "";
  
    $clickedButton = this.id;

    // *****************************************************
    // Build SQL WHERE statement for tracks
    var whereStatement = [];
    
    // Field track ID
    var whereString = "";
    trackIdFrom = "";
    trackIdTo = "";
    if ( ($('#dispFilTrk_trackIdFrom').val()) != "" ) {                           
        trackIdFrom = $('#dispFilTrk_trackIdFrom').val();
    } else {
        trackIdFrom = "";
    };
    if ( ($('#dispFilTrk_trackIdTo').val()) != "" ) {                           
        trackIdTo = $('#dispFilTrk_trackIdTo').val();
    } else {
        trackIdTo = "";
    };

    if ( trackIdFrom != "" && trackIdTo != "" ) {
        whereString = "trkID >= " + trackIdFrom + " AND trkId <= " + trackIdTo;   // complete WHERE BETWEEN statement
    } else if ( trackIdFrom != "" ) {
        whereString = "trkID >= " + trackIdFrom;                                  // complete WHERE BETWEEN statement
    } else if ( trackIdTo != "" ) {
        whereString = "trkId <= " + trackIdTo;                                    // complete WHERE BETWEEN statement
    }
    
    if ( whereString.length > 0 ) {
        whereStatement.push( whereString );                                       // Add to where Statement array
    }

    // Field track name
    var whereString = "";
    if ( ($('#dispFilTrk_trackName').val()) != "" ) {                           
        whereString = "trkTrackName like '%" + $('#dispFilTrk_trackName').val() + "%'";
        whereStatement.push( whereString );
    };

    // Field route
    var whereString = "";
    if ( ($('#dispFilTrk_route').val()) != "" ) {
        whereString = "trkRoute like '%" + $('#dispFilTrk_route').val() + "%'";
        whereStatement.push( whereString );
    };

    // Field date begin (date finished not used)
    var whereString = "";                                                       // clear where string
    fromDateArt = "1968-01-01";                                                 // Set from date in case no date is entered
    var today = new Date();                                                     // Set to date to today in case no date is entered
    month = today.getMonth()+1;                                                 // Extract month (January = 0)
    toDateArt = today.getFullYear() + '-' + month + '-' + today.getDate();      // Set to date to today (format yyyy-mm-dd)
    
    if ( ($('#dispFilTrk_dateFrom').val()) != "" ) {                            // Overwrite fromDate with value entered by user
        fromDate = ($('#dispFilTrk_dateFrom').val());
    } else {
        fromDate = "";
    }

    if ( ($('#dispFilTrk_dateTo').val()) != "" ) {                              // Overwrite toDate with value entered by user
        toDate = ($('#dispFilTrk_dateTo').val())                                // Add to where Statement array
    } else {
        toDate = "";
    }

    if ( fromDate != "" && toDate != "" ) {
        whereString = "trkDateBegin BETWEEN '" + fromDate + "' AND '" + toDate + "'";   // complete WHERE BETWEEN statement
    } else if ( fromDate != "" ) {
        whereString = "trkDateBegin BETWEEN '" + fromDate + "' AND '" + toDateArt + "'";                      // complete WHERE BETWEEN statement
    } else if ( toDate != "" ) {
        whereString = "trkDateBegin BETWEEN '" + fromDateArt + "' AND '" + toDate + "'";                      // complete WHERE BETWEEN statement
    }
    if ( whereString.length > 0 ) {
        whereStatement.push( whereString );                                         // Add to where Statement array
    }

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
    var whereString = "";
    if ( ($('#dispFilTrk_participants').val()) != "" ) {
        whereString = "trkParticipants like '%" + $('#dispFilTrk_participants').val() + "%'";
        whereStatement.push( whereString );
    };

    // Field country
    var whereString = "";
    if ( ($('#dispFilTrk_country').val()) != "" ) {
        whereString = "trkCountry like '%" + $('#dispFilTrk_country').val() + "%'";
        whereStatement.push( whereString );
    };
    
    // ========== Put all where statements together
    if ( whereStatement.length > 0 ) {
        var sqlWhereTracks = "WHERE ";

        for (var i=0; i < whereStatement.length; i++) {
            sqlWhereTracks += whereStatement[i];
            sqlWhereTracks += " AND ";
        }
        sqlWhereTracks = sqlWhereTracks + " trkLoginName ='" + $loginName + "'";
    } 
  
    // ********************************************************************************************
    // ****** Build SQL WHERE statement for segments *******
    var whereStatement = [];

    // Field segment type selected
    var selected = [];
    var sqlName;
    var whereString = "";
    $('#dispFilSeg_segType .ui-selected').each(function() {
        var itemId = this.id;
        sqlName = "segType";
        var criteria = itemId.slice(sqlName.length+1,itemId.length);                // +1 to remove _
        selected.push( criteria );
    });
    if ( selected.length > 0 ) {
        whereString = sqlName + " in (";
        var i;
        for (i=0; i<selected.length; ++i) {
            whereString += "'" + selected[i] + "',";
        }
    whereString = whereString.slice(0,whereString.length-1) + ")"; 
    whereStatement.push( whereString );                                     // Add to where Statement array
    }

    // Field segment name
    var whereString = "";
    if ( ($('#dispFilSeg_segName').val()) != "" ) {
        whereString = "segName like '%" + ($('#dispFilSeg_segName').val()) + "%'";      
        whereStatement.push( whereString );
    };

    // Field Start Location (ID selected)
    var whereString = "";
    if ( ($('#dispFilSeg_startLocID').val()) != "" ) {
        whereString = "startLocType = " + ($('#dispFilSeg_startLocID').val()); 
        whereStatement.push( whereString );
    };

    // Field Altitude of start location 
    /*
    var whereString = "";
    whereString = " startLocAlt >= " + $( "#dispFilSeg_startLocAlt_slider" ).slider( "values", 0 );
    whereString += " AND startLocAlt <= " + $( "#dispFilSeg_startLocAlt_slider" ).slider( "values", 1 );
    whereStatement.push( whereString );     
    */

    // Field type of start location
    var selected = [];
    var sqlName;
    var whereString = "";    
    $('#dispFilSeg_startLocType .ui-selected').each(function() {
        var itemId = this.id;
        sqlName = itemId.slice(0,itemId.length-2);
        var criteria = itemId.slice(sqlName.length+1,itemId.length);                // +1 to remove _
        selected.push( criteria );
    });
    if ( selected.length > 0 ) {
        whereString = sqlName + " in (";
        var i;
        for (i=0; i<selected.length; ++i) {
            whereString += "'" + selected[i] + "',";
        }
    whereString = whereString.slice(0,whereString.length-1) + ")"; 
    whereStatement.push( whereString );                                     // Add to where Statement array
    }
    
    // Field target location (ID selected)
    var whereString = "";
    if ( ($('#dispFilSeg_targetLocID').val()) != "" ) {
        whereString = "targetLocType = " + ($('#dispFilSeg_startLocID').val()); 
        whereStatement.push( whereString );
    };

    // Field target location altitude
    /*
    var whereString = "";
    whereString = " targetLocAlt >= " + $( "#dispFilSeg_targetLocAlt_slider" ).slider( "values", 0 );
    whereString += " AND startLocAlt <= " + $( "#dispFilSeg_targetLocAlt_slider" ).slider( "values", 1 );
    whereStatement.push( whereString );            
    */

    // Field target location type
    var selected = [];
    var sqlName;
    var whereString = "";
    $('#dispFilSeg_targetLocType .ui-selected').each(function() {
        var itemId = this.id;
        sqlName = "targetLocType";
        var criteria = itemId.slice(sqlName.length+1,itemId.length);                // +1 to remove _
        selected.push( criteria );
    });
    if ( selected.length > 0 ) {
        whereString = sqlName + " in (";
        var i;
        for (i=0; i<selected.length; ++i) {
            whereString += "'" + selected[i] + "',";
        }
    whereString = whereString.slice(0,whereString.length-1) + ")"; 
    whereStatement.push( whereString );                                     // Add to where Statement array
    }

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
    var selected = [];
    var sqlName;
    var whereString = "";    
    $('#dispFilSeg_grade .ui-selected').each(function() {
        var itemId = this.id;
        sqlName = "grade";
        var criteria = itemId.slice(sqlName.length+1,itemId.length);                // +1 to remove _
        selected.push( criteria );
    });
    if ( selected.length > 0 ) {
        whereString = sqlName + " in (";
        var i;
        for (i=0; i<selected.length; ++i) {
            whereString += "'" + selected[i] + "',";
        }
    whereString = whereString.slice(0,whereString.length-1) + ")"; 
    whereStatement.push( whereString );                                     // Add to where Statement array
    }
    
    // Field climbGrade
    var selected = [];
    var sqlName;
    var whereString = "";    
    $('#dispFilSeg_climbGrade .ui-selected').each(function() {
        var itemId = this.id;
        sqlName = "climbGrade";
        var criteria = itemId.slice(sqlName.length+1,itemId.length);                // +1 to remove _
        selected.push( criteria );
    });
    if ( selected.length > 0 ) {
        whereString = sqlName + " in (";
        var i;
        for (i=0; i<selected.length; ++i) {
            whereString += "'" + selected[i] + "',";
        }
    whereString = whereString.slice(0,whereString.length-1) + ")"; 
    whereStatement.push( whereString );                                     // Add to where Statement array
    }

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

    // *******************************************
    // ========= Put all where statements together
    //
    if ( whereStatement.length > 0 ) {
        var sqlWhereSegments = "WHERE ";

        for (var i=0; i < whereStatement.length; i++) {
            sqlWhereSegments += whereStatement[i];
            sqlWhereSegments += " AND ";
        }
        sqlWhereSegments = sqlWhereSegments.slice(0,sqlWhereSegments.length-5);
        sqlWhereSegments = sqlWhereSegments + " AND coordinates is not null"
    }

    // ****************************************************
    // Generate KML for tracks and segments  

    // evaluate if sqlWhereTracks and sqlWhereSegments have changed
    if ( sqlWhereTracks == "" || sqlWhereTracks == sqlWhereTracksPrev ) {
        genTrackKml = false;
    } else {
        genTrackKml = true;
    }

    if ( sqlWhereSegments == "" || sqlWhereSegments == sqlWhereSegmentsPrev ) {
        genSegKml = false;
    } else {
        genSegKml = true;
    }

    var xhr = new XMLHttpRequest();
    xhr.onload = function() {
        if (xhr.status === 200) {  
            responseObject = JSON.parse(xhr.responseText);                                              // transfer JSON into response object array
            
            if ( responseObject["status"] == "OK") {

                if ( ( trackKMLlayer || segKMLlayer )
                    && ( $clickedButton == 'dispFilTrk_NewLoadButton' ||
                    $clickedButton == 'dispFilSeg_NewLoadButton' )) { 
                    $map.getLayers().forEach(function(el) {
                        $map.removeLayer(el);
                    })
                    mapSTlayer_grau = ga.layer.create('ch.swisstopo.pixelkarte-grau');
                    //mapSTlayer_grau.set('name', 'mapSTlayer_grau');
                    $map.addLayer(mapSTlayer_grau);
                }

                if ( genTrackKml ) {
                    $trackFile = document.URL + "tmp/kml_disp/" + sessionid + "/tracks.kml";
                    //$trackFile = document.URL + "tmp/kml_disp/" + sessionid + "/segments.kml";
                    // Create the KML Layer for tracks
                    trackKMLlayer = new ol.layer.Vector({
                        source: new ol.source.Vector({
                            url: $trackFile,
                            format: new ol.format.KML({
                                projection: 'EPSG:21781'
                            })
                        })
                    });
                    $map.addLayer(trackKMLlayer);
                }

                if ( genSegKml ) {
                    //$segFile = document.URL + "tmp/kml_disp/" + sessionid + "/test.kml";
                    $segFile = document.URL + "tmp/kml_disp/" + sessionid + "/segments.kml";
                    // Create the KML Layer for segments
                    segKMLlayer = new ol.layer.Vector({
                        source: new ol.source.Vector({
                            url: $segFile,
                            format: new ol.format.KML({
                                projection: 'EPSG:21781'
                            })
                        })
                    });
                    $map.addLayer(segKMLlayer);
                }

                $('.dispObjOpen').removeClass('visible');
                $('.dispObjOpen').addClass('hidden');
                $('.dispObjMini').addClass('visible');
                $('.dispObjMini').removeClass('hidden');
            
                sqlWhereTracksPrev = sqlWhereTracks;
                sqlWhereSegmentsPrev = sqlWhereSegments;
            }
        }
    }

    if ( ( $clickedButton == 'dispFilTrk_addObjButton' || $clickedButton == 'dispFilTrk_NewLoadButton' || 
        $clickedButton == 'dispFilSeg_addObjButton' || $clickedButton == 'dispFilSeg_NewLoadButton' ) && 
        genTrackKml || genSegKml ) {
        // send required parameters to gen_kml.php
        var jsonObject = {};
        phpLocation = document.URL + "services/gen_kml.php";          // Variable to store location of php file
        jsonObject["sessionid"] = sessionid;                             // send session ID
        jsonObject["sqlWhereTracks"] = sqlWhereTracks;                             // append parameter session ID
        jsonObject["genTrackKml"] = genTrackKml;                             //  
        jsonObject["sqlWhereSegments"] = sqlWhereSegments;                             
        jsonObject["genSegKml"] = genSegKml;                             // append parameter session ID
        xhr.open ('POST', phpLocation, true);                           // open  XMLHttpRequest 
        xhr.setRequestHeader( "Content-Type", "application/json" );
        jsn = JSON.stringify(jsonObject);
        xhr.send( jsn );                                           // send formData object to service using xhr   
    } else {
        $('#statusMessage').text('No selection criteria defined');
        $("#statusMessage").show().delay(5000).fadeOut();
        document.getElementById("inputFile").value = "";
    }
});

// ==========================================================================
// ========================== panelImport ===================================
// ==========================================================================

// Change to selected panel
$(document).on('click', '.uiAdmTrk_btns_a', function(e) {                  
    e.preventDefault();                                             // Prevent link behaviour
    
    var $activeButtonA = $(this)                                    // Store the current link <a> element
    var buttonId = this.hash;                                       // Get div class of selected topic (e.g #panelDisplay)
    
    // Run following block if selected topic is currently not active
    if (buttonId && !$activeButtonA.is('.active')) {
        $actUpdTrkTab.removeClass('active');                         // Make current panel inactive
        $actUpdTrkBtn.removeClass('active');                        // Make current tab inactive
        
        $actUpdTrkTab = $(buttonId).addClass('active');              // Make new panel active
        $actUpdTrkBtn = $activeButtonA.parent().addClass('active'); // Make new tab active
    }
}); 

// Upon click on the 'Upload File' button --> call importGps.php in temp mode
$(document).on('click', '#buttonUploadFile', function (e) {
    e.preventDefault();                                                                                 
    var xhr = new XMLHttpRequest();                                                                     // create new xhr object
    
    // Execute following code JSON object is received from importGpsTmp.php - TEMP service
    xhr.onload = function() {
        if (xhr.status === 200) {                                                                       // when all OK
            responseObject = JSON.parse(xhr.responseText);                                              // transfer JSON into response object array
            if ( responseObject.status == 'OK') {
                trackobj = responseObject.trackObj;
                $('#uiAdmTrk_fld_trkId').val(trackobj.trkId); 
                $('#uiAdmTrk_fld_trkTrackName').val(trackobj.trkTrackName);                    // assign values of JSON to input fields
                $('#uiAdmTrk_fld_trkRoute').val(trackobj.trkRoute);
                $('#uiAdmTrk_fld_trkDateBegin').val(trackobj.trkDateBegin);
                $('#uiAdmTrk_fld_trkDateFinish').val(trackobj.trkDateFinish);
                $('#uiAdmTrk_fld_trkSaison').val(trackobj.trkSaison);
                $('#uiAdmTrk_fld_trkType').val(trackobj.trkType);
                $('#uiAdmTrk_fld_trkSubType').val(trackobj.trkSubType);
                $('#uiAdmTrk_fld_trkOrg').val(trackobj.trkOrg);
                $('#uiAdmTrk_fld_trkOvernightLoc').val(trackobj.trkOvernightLoc);
                $('#uiAdmTrk_fld_trkParticipants').val(trackobj.trkParticipants);
                $('#uiAdmTrk_fld_trkEvent').val(trackobj.trkEvent);
                $('#uiAdmTrk_fld_trkRemarks').val(trackobj.trkRemarks);
                $('#uiAdmTrk_fld_trkDistance').val(trackobj.trkDistance);
                $('#uiAdmTrk_fld_trkTimeOverall').val(trackobj.trkTimeOverall);
                $('#uiAdmTrk_fld_trkTimeToPeak').val(trackobj.trkTimeToPeak);
                $('#uiAdmTrk_fld_trkTimeToFinish').val(trackobj.trkTimeToFinish);
                $('#uiAdmTrk_fld_trkGrade').val(trackobj.trkGrade);
                $('#uiAdmTrk_fld_trkMeterUp').val(trackobj.trkMeterUp);
                $('#uiAdmTrk_fld_trkMeterDown').val(trackobj.trkMeterDown);
                $('#uiAdmTrk_fld_trkCountry').val(trackobj.trkCountry);
                $('#uiAdmTrk_fld_trkCoordinates').val(trackobj.trkCoordinates);
                
                // not displayed fields
                $trkStartEle = trackobj.trkStartEle;                        // new db field
                $trkPeakEle = trackobj.trkPeakEle;                          // new db field
                $trkPeakTime = trackobj.trkPeakTime;                        // new db field
                $trkLowEle = trackobj.trkLowEle;                            // new db field
                $trkLowTime = trackobj.trkLowTime;                          // new db field
                $trkFinishEle = trackobj.trkFinishEle;
                $trkFinishTime = trackobj.trkFinishTime;
                
                // Close upload file div and open form to update track data
                $('#uiUplFileGps').removeClass('active');
                $('#uiAdmTrk').addClass('active');
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
        formData.append('loginname', $loginName);                             // append parameter file type
        xhr.open ('POST', phpLocation, true);                           // open  XMLHttpRequest 
        xhr.send(formData);                                             // send formData object to service using xhr
    } else {
        $('#statusMessage').text('No file selected');
        $("#statusMessage").show().delay(5000).fadeOut();
    }
});

$(document).on('click', '#uiAdmTrk_btnPeakAdd', function (e) {
    
    // Initialise peakList array
    var peaksList =  new Object();

    // Add new peak to array
    peaksList.id = "#peakDel_" + id;                 // 0
    peaksList.waypId = id;                               // 1
    peaksList.waypName = value;                            // 2
    peaksList.waypType = 5;                                // 3
    peaksList.disp_f = true;                             // 4

    peaksArray.push(peaksList);

    drawPeakTable ( peaksArray ); 

    peakNr++;

    // Reset peak array on click on save or cancel

});

$(document).on('click', '.peakDel', function (e) {
    console.info("clicked on del")
    e.preventDefault();                                             // Prevent link behaviour
    // var $activeButtonA = $(this)                                    // Store the current link <a> element
    var peakDelId = this.hash;                                       // Get div class of selected topic (e.g #panelDisplay)
    
    for (var i = 0; i < peaksArray.length; i++) {
        if ( peaksArray[i][0] == peakDelId ) {
            peaksArray[i][4] = false;
        }    
    }
    drawPeakTable ( peaksArray );

    
    // Run following block if selected topic is currently not active
    if (buttonId && !$activeButtonA.is('.active')) {
        $topicButton.removeClass('active');                         // Make current panel inactive
        $activeButton.removeClass('active');                        // Make current tab inactive

        $topicButton = $(buttonId).addClass('active');              // Make new panel active
        $activeButton = $activeButtonA.parent().addClass('active'); // Make new tab active
    }

});

// Upon click on the 'Save' button --> call importGps.php in save mode
$(document).on('click', '#uiAdmTrk_fld_save', function (e) {
    e.preventDefault();
    var valid = true;                                                                 // true when field check are passed

    //var field2Chk = $('#uiAdmTrk_fld_trkId').val()
    //  valid = valid && checkExistance ( $('#uiAdmTrk_fld_trkId'), "Seg Type" );
    //valid = valid && checkExistance ( segDialogSegType, "Seg Type" );

    var trackobj = {};
    var jsonObject = {};

    $('#uiAdmTrk_fld_trkId').removeClass( "ui-state-error" );
    valid = valid && checkExistance ( $('#uiAdmTrk_fld_trkId'), "Track ID" );
    trackobj.trkId = $('#uiAdmTrk_fld_trkId').val();

    $('#uiAdmTrk_fld_trkTrackName').removeClass( "ui-state-error" );
    valid = valid && checkExistance ( $('#uiAdmTrk_fld_trkTrackName'), "Track Name" );
    trackobj.trkTrackName = $('#uiAdmTrk_fld_trkTrackName').val();
    
    $('#uiAdmTrk_fld_trkRoute').removeClass( "ui-state-error" );
    valid = valid && checkExistance ( $('#uiAdmTrk_fld_trkRoute'), "Route" );
    trackobj.trkRoute = $('#uiAdmTrk_fld_trkRoute').val();
    
    $('#uiAdmTrk_fld_trkDateBegin').removeClass( "ui-state-error" );
    valid = valid && checkExistance ( $('#uiAdmTrk_fld_trkDateBegin'), "Date Begin" );
    trackobj.trkDateBegin = $('#uiAdmTrk_fld_trkDateBegin').val();     

    $('#uiAdmTrk_fld_trkDateFinish').removeClass( "ui-state-error" );
    valid = valid && checkExistance ( $('#uiAdmTrk_fld_trkDateFinish'), "Date Finish" );
    trackobj.trkDateFinish = $('#uiAdmTrk_fld_trkDateFinish').val();
    
    trackobj.trkSaison = $('#uiAdmTrk_fld_trkSaison').val();
    trackobj.trkType = $('#uiAdmTrk_fld_trkType').val();
    trackobj.trkSubType = $('#uiAdmTrk_fld_trkSubType').val();
    trackobj.trkOrg = $('#uiAdmTrk_fld_trkOrg').val();    
    trackobj.trkOvernightLoc = $('#uiAdmTrk_fld_trkOvernightLoc').val();
    trackobj.trkParticipants = $('#uiAdmTrk_fld_trkParticipants').val();
    trackobj.trkEvent = $('#uiAdmTrk_fld_trkEvent').val();
    trackobj.trkRemarks = $('#uiAdmTrk_fld_trkRemarks').val();
    trackobj.trkDistance = $('#uiAdmTrk_fld_trkDistance').val();
    trackobj.trkTimeOverall = $('#uiAdmTrk_fld_trkTimeOverall').val();
    trackobj.trkTimeToPeak = $('#uiAdmTrk_fld_trkTimeToPeak').val();
    trackobj.trkTimeToFinish = $('#uiAdmTrk_fld_trkTimeToFinish').val();
    trackobj.trkGrade = $('#uiAdmTrk_fld_trkGrade').val();
    trackobj.trkMeterUp = $('#uiAdmTrk_fld_trkMeterUp').val();
    trackobj.trkMeterDown = $('#uiAdmTrk_fld_trkMeterDown').val();
    trackobj.trkCountry = $('#uiAdmTrk_fld_trkCountry').val();      
    trackobj.trkCoordinates = $('#uiAdmTrk_fld_trkCoordinates').val();  
    //trackobj.trkLoginName = $loginName;    

    // not displayed fields
    trackobj.trkStartEle = $trkStartEle;                        // new db field
    trackobj.trkPeakEle = $trkPeakEle;                          // new db field
    trackobj.trkPeakTime = $trkPeakTime;                        // new db field
    trackobj.trkLowEle = $trkLowEle;                            // new db field
    trackobj.trkLowTime = $trkLowTime;                          // new db field
    trackobj.trkFinishEle = $trkFinishEle;                      // new db field
    trackobj.trkFinishTime = $trkFinishTime;                    // new db field

    if ( valid ) { 
        //phpLocation = document.URL + "services/importGps2.php";          // Variable to store location of php file
        phpLocation = "services/importGps.php";          // Variable to store location of php file
        jsonObject.sessionid = sessionid;                             // append parameter session ID
        jsonObject.request = 'save';                              // temp request to create track temporarily
        jsonObject.loginname = $loginName; 
        trackobj.peaksArray = peaksArray;                     // Array containing selected peaks
        jsonObject.trackobj = trackobj;                              // send track object

        $.ajax({
            url: phpLocation,
            type: "POST",
            data: jsonObject
        })
        .done(function ( data ) {
            if ( responseObject.status == 'OK') {
                // Make panelImport disappear and panelDisplay appear
                $('#statusMessage').text('Track successfully saved');
                $('#statusMessage').show().delay(5000).fadeOut();

                //$('.updTrackInput').value = "";

                // Open Panel Display
                var $activeButtonA = $('#navBtns_btn_diplay_a');                                    // Store the current link <a> element
                buttonId = $activeButtonA.attr('href'); 
                
                // Run following block if selected topic is currently not active
                $topicButton.removeClass('active');                         // Make current panel inactive
                $activeButton.removeClass('active');                        // Make current tab inactive
                $topicButton = $(buttonId).addClass('active');              // Make new panel active
                $activeButton = $activeButtonA.parent().addClass('active'); // Make new tab active
                
                // Close upload file div and open form to update track data
                $('#uiUplFileGps').addClass('active');
                $('#uiAdmTrk').removeClass('active');
            }
        });
    }                                           // send formData object to service using xhr
});

// On click on the 'cancel' button --> cancel update & delete temp track
$(document).on('click', '#uiAdmTrk_fld_cancel', function (e) {
    e.preventDefault();
    
    var xhr = new XMLHttpRequest();                                 // create new xhr object
    // Execute following code JSON object is received from importGpsTmp.php service
    xhr.onload = function() {
        if (xhr.status === 200) {                                   // when all OK
            if ( responseObject.status == 'OK') {
                $('#uiUplFileGps').addClass('active');                 // Make File upload div visible
                $('#uiAdmTrk').removeClass('active');                   // hide update form
                $('#statusMessage').text('Import cancelled');
                $("#statusMessage").show().delay(5000).fadeOut();
            }
        }
    }

    var trackobj = {};
    var jsonObject = {};
    trackobj["trkId"] = $('#uiAdmTrk_fld_trkId').val();
    
    phpLocation = document.URL + "services/importGps.php";          // Variable to store location of php file
    //var formData = new FormData();                                  // create new formData object
    jsonObject["sessionid"] = sessionid;                             // append parameter session ID
    jsonObject["request"] = 'cancel';                              // temp request to create track temporarily
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
    //var lyr1 = ga.layer.create('ch.swisstopo.pixelkarte-farbe');
    mapSTlayer_grau = ga.layer.create('ch.swisstopo.pixelkarte-grau');
    //mapSTlayer_grau.set('name', 'mapSTlayer_grau');
    map.addLayer(mapSTlayer_grau);
    return map;
}

function drawTrackLayer(map, kmlObject) {

    /*
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
    */
    //kmlObject = '<?xml version="1.0" encoding="UTF-8"?><kml xmlns="http://www.opengis.net/kml/2.2" xmlns:gx="http://www.google.com/kml/ext/2.2" xmlns:kml="http://www.opengis.net/kml/2.2" xmlns:atom="http://www.w3.org/2005/Atom">  <Document>    <name>tourdb - KmlFile</name>    <StyleMap id="stylemap_Hochtour">      <Pair>        <key>normal</key>        <styleUrl>#style_Hochtour_norm</styleUrl>      </Pair>      <Pair>        <key>highlight</key>        <styleUrl>#style_Hochtour_hl</styleUrl>      </Pair>    </StyleMap>    <Style id="style_Hochtour_norm">      <LineStyle>        <color>FF0000FF</color>        <width>3</width>      </LineStyle>      <PolyStyle>        <color>FF0000FF</color>        <width>3</width>      </PolyStyle>    </Style>    <Style id="style_Hochtour_hl">      <LineStyle>        <color>FF0000FF</color>        <width>5</width>      </LineStyle>      <PolyStyle>        <color>FF0000FF</color>        <width>5</width>      </PolyStyle>    </Style>    <Folder>      <name>tourdb exported KML</name>        <visibility>0</visibility>        <open>1</open>        <Placemark id="linepolygon_00291">          <name>Hochtour Mönch via SW-Grat</name>          <visibility>1</visibility>          <description>291 - Jungfraujoch - SE-Grat Mönch - Normalroute im Abstieg (mit Danny;Urs Gisler)</description>          <styleUrl>#stylemap_Hochtour</styleUrl>          <ExtendedData>            <Data name="type">              <value>linepolygon</value>            </Data>          </ExtendedData>          <LineString>            <coordinates>7.990885,46.549476,3476 7.990886,46.549479,3476 7.9908,46.549738,3477 7.990774,46.55002,3481 7.990816,46.550291,3486 7.990759,46.550542,3491 7.990685,46.550724,3495 7.990569,46.550858,3500 7.990458,46.551078,3517 7.990369,46.551192,3526 7.990219,46.551374,3537 7.990092,46.551484,3542 7.989926,46.551586,3539 7.989849,46.551702,3535 7.989972,46.551913,3544 7.988669,46.551484,3496 7.988512,46.550915,3475 7.98923,46.551699,3526 7.989048,46.55171,3509 7.989906,46.551931,3544 7.990219,46.551868,3561 7.990367,46.552093,3563 7.990387,46.552214,3557 7.990544,46.552206,3568 7.990715,46.552113,3568 7.990481,46.552345,3564 7.990391,46.552474,3566 7.990396,46.552622,3574 7.991012,46.552872,3597 7.990644,46.552856,3580 7.990335,46.552812,3575 7.990729,46.552922,3587 7.99053,46.552888,3577 7.990262,46.552836,3573 7.990414,46.552684,3576 7.990093,46.552678,3561 7.990408,46.552906,3575 7.990137,46.552881,3563 7.990322,46.55276,3576 7.990372,46.552872,3575 7.990352,46.552983,3569 7.990437,46.552837,3577 7.990365,46.552923,3572 7.990572,46.553105,3582 7.990647,46.553262,3591 7.9904,46.553131,3569 7.990212,46.553072,3560 7.990805,46.553326,3605 7.990964,46.553351,3613 7.990815,46.553325,3605 7.99109,46.553467,3632 7.990922,46.553408,3615 7.990667,46.55311,3593 7.990982,46.552491,3604 7.990392,46.552784,3577 7.990722,46.552767,3583 7.991078,46.552278,3571 7.990624,46.553048,3588 7.990562,46.552827,3578 7.990943,46.553412,3618 7.990977,46.553432,3623 7.990241,46.553233,3561 7.990674,46.55341,3599 7.991137,46.553657,3649 7.990574,46.553025,3581 7.990696,46.553181,3594 7.990892,46.553415,3615 7.991244,46.553685,3655 7.991094,46.553584,3643 7.990924,46.553503,3626 7.990725,46.553272,3598 7.990271,46.553274,3560 7.990441,46.553252,3573 7.990329,46.553179,3566 7.990437,46.55295,3574 7.992992,46.554627,3753 7.990335,46.552689,3576 7.990286,46.552795,3575 7.990508,46.552725,3576 7.990287,46.552895,3572 7.99081,46.553391,3607 7.990204,46.553196,3558 7.990305,46.552769,3576 7.990273,46.552937,3567 7.990223,46.552819,3571 7.990329,46.552594,3573 7.990451,46.552732,3576 7.990277,46.552724,3575 7.990396,46.55273,3577 7.990399,46.552525,3570 7.990554,46.552784,3577 7.99048,46.552864,3577 7.990643,46.552929,3583 7.990468,46.552919,3576 7.989263,46.552462,3507 7.990794,46.553065,3597 7.990323,46.55351,3561 7.990505,46.552915,3578 7.990671,46.552958,3586 7.990792,46.553058,3597 7.991063,46.553254,3613 7.990824,46.553023,3598 7.990733,46.552855,3584 7.990789,46.55308,3598 7.991128,46.552986,3602 7.990729,46.553214,3597 7.991115,46.553277,3614 7.990961,46.553082,3605 7.990613,46.553532,3594 7.990794,46.553003,3594 7.990832,46.553225,3603 7.990821,46.553214,3602 7.990704,46.552872,3583 7.990732,46.553671,3616 7.991186,46.553243,3614 7.99069,46.552936,3586 7.990867,46.553125,3603 7.990685,46.552919,3584 7.990652,46.552571,3583 7.990705,46.552923,3584 7.990827,46.553058,3599 7.990652,46.552636,3581 7.991122,46.553448,3630 7.99123,46.553833,3664 7.991013,46.553146,3608 7.99145,46.553759,3674 7.991195,46.553276,3617 7.991192,46.553739,3657 7.99129,46.553315,3623 7.991269,46.554082,3671 7.990151,46.5538,3542 7.99178,46.55433,3696 7.991961,46.554531,3701 7.991645,46.55371,3684 7.991747,46.553943,3681 7.992442,46.554198,3715 7.992565,46.554388,3732 7.992575,46.554501,3737 7.992533,46.554656,3745 7.992266,46.554638,3726 7.992597,46.554798,3756 7.992755,46.554667,3754 7.992732,46.554782,3759 7.992551,46.55473,3750 7.99286,46.554998,3766 7.992814,46.555105,3767 7.992917,46.555209,3775 7.992995,46.55533,3785 7.992938,46.555453,3790 7.993034,46.555544,3801 7.993131,46.55563,3810 7.992989,46.55568,3805 7.992842,46.5557,3795 7.992936,46.555821,3808 7.993176,46.555983,3842 7.993142,46.555871,3828 7.993243,46.555942,3847 7.993287,46.556051,3857 7.99326,46.556152,3862 7.993343,46.556279,3873 7.993379,46.556497,3884 7.993361,46.556398,3879 7.99352,46.556333,3882 7.993652,46.556421,3890 7.993525,46.556351,3882 7.993832,46.556468,3902 7.993863,46.556582,3916 7.994063,46.556622,3931 7.994002,46.556526,3916 7.994259,46.556831,3952 7.994153,46.556693,3938 7.994361,46.556963,3969 7.994533,46.557094,3981 7.994686,46.557228,3990 7.994815,46.557368,3998 7.994992,46.557363,4008 8.007529,46.554139,3607 7.995134,46.557348,4012 7.994865,46.554839,3742 7.995243,46.557355,4013 7.99548,46.557424,4020 7.995534,46.557525,4017 7.995526,46.557758,4020 7.99574,46.557881,4024 7.996011,46.557985,4026 7.996151,46.558064,4028 7.996475,46.558185,4041 7.996314,46.558331,4044 7.996621,46.558389,4054 7.996894,46.558361,4065 7.997057,46.558474,4068 7.99725,46.558439,4070 7.997566,46.558437,4071 7.997907,46.558391,4066 7.998212,46.558238,4056 7.998529,46.558135,4045 7.998719,46.558007,4033 7.99888,46.558016,4032 7.999248,46.558017,4034 7.999563,46.557857,4033 8.00002,46.557842,4019 8.000288,46.557793,3986 8.000179,46.557706,4000 8.000381,46.557712,3978 8.000363,46.557862,3985 8.000409,46.557653,3974 8.000472,46.557514,3969 8.000549,46.557421,3959 8.000524,46.557299,3946 8.000691,46.557249,3923 8.000469,46.557231,3936 8.000758,46.557178,3907 8.00085,46.557048,3898 8.002427,46.541208,3302 8.000948,46.556995,3897 8.000894,46.556891,3896 8.001031,46.556767,3888 8.001301,46.556539,3871 8.00142,46.556397,3858 8.001544,46.556303,3856 7.998796,46.554722,3689 8.001829,46.556135,3851 8.001945,46.555971,3843 8.002102,46.555875,3835 8.002448,46.555711,3823 8.002369,46.555619,3817 8.002302,46.555475,3807 8.002356,46.555301,3796 8.00237,46.555144,3791 8.002347,46.554957,3779 8.002482,46.554839,3767 8.002319,46.554845,3766 8.002399,46.554732,3759 8.002225,46.554766,3759 8.002436,46.554685,3755 8.002285,46.554715,3755 8.001403,46.55504,3744 8.002337,46.554701,3755 8.002321,46.554565,3745 8.002506,46.554424,3743 8.002345,46.55441,3741 8.002362,46.554294,3738 8.002244,46.554182,3728 8.002286,46.554041,3715 8.002135,46.554016,3710 8.002163,46.553913,3700 8.002266,46.553768,3688 8.002354,46.553687,3688 8.002381,46.553539,3684 8.002416,46.553416,3676 8.002305,46.553547,3682 8.002331,46.553317,3670 7.994412,46.551412,3524 8.002381,46.553274,3666 8.002305,46.553132,3650 8.002236,46.553254,3665 8.002421,46.552938,3635 8.002487,46.552786,3623 8.002477,46.552684,3618 8.002641,46.552533,3617 8.00281,46.552507,3616 8.003171,46.552655,3616 8.003557,46.552769,3616 8.003812,46.552929,3615 8.004209,46.553202,3617 8.004405,46.553367,3618 8.000801,46.55516,3741 8.004933,46.55397,3625 8.004949,46.554406,3652 8.005476,46.554592,3649 8.005852,46.554847,3642 8.005877,46.554694,3639 8.005744,46.554781,3649 8.005873,46.554731,3644 8.005447,46.554853,3666 8.005972,46.554692,3644 8.005847,46.554743,3644 8.005398,46.554769,3646 8.005664,46.554734,3647 8.005926,46.554818,3647 8.005818,46.554543,3646 8.005887,46.554735,3646 8.005376,46.554981,3676 8.006197,46.554357,3634 8.005082,46.555172,3710 8.005395,46.554759,3672 8.006149,46.554694,3641 8.005805,46.554749,3655 8.005513,46.5548,3668 8.005854,46.554753,3652 8.005634,46.554792,3662 8.005868,46.554758,3649 8.005722,46.554745,3656 8.005691,46.554647,3654 8.005592,46.554592,3657 8.005584,46.554716,3659 8.006033,46.55452,3633 8.005397,46.554993,3633            </coordinates>          </LineString>        </Placemark>    </Folder>  </Document></kml>';
    
    var kmlString = kmlObject;
    
    var features = new ol.format.KML().readFeatures(kmlString, {
        dataProjection: 'EPSG:21781'
        });
    
    var KMLvectorSource = new ol.source.Vector({});
    
    var KMLvector = new ol.layer.Vector({
        source: KMLvectorSource,
        visible: true
        });
    
    KMLvectorSource.addFeatures(features);
    map.addLayer(KMLvector);
    /*
    var vectorSource = new ol.source.Vector({});
    var vector = new ol.layer.Vector({source: vectorSource}); 
    var features = new ol.format.KML().readFeatures(kmlObject ,{
                           dataProjection:'EPSG:21781',
                           featureProjection:'EPSG:21781'
                        });
    vectorSource.addFeatures(features);
    map.addLayer(vector);
                        */
}

function addMapFeatures(map) {
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

// Function generating KML file for segments
function callGenKml(outFileName, kmlType, sqlWhere) {   
    var xhrTracks = new XMLHttpRequest();
    var xhrSegments = new XMLHttpRequest();
    xhrTracks.onload = function() {
        if (xhr.status === 200) {  
        }
    }
    phpLocation = document.URL + "services/gen_kml.php";          // Variable to store location of php file
    xhrParams =  "outFileName=" + outFileName;
    xhrParams += "&kmlType=" + kmlType;   // Variable for POST parameters
    xhrParams += "&sqlWhere=" + sqlWhere ;
    xhrParams += "&loginName" + loginName
    xhr.open ('POST', phpLocation, true);                // Make XMLHttpRequest - in synchronous mode
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");  // Set header to encode special characters like %
    xhr.send(encodeURI(xhrParams));
}

// Function generating KML file for segments
function callGenSegKml(sqlWhere) {

    // call gen_kml.php using XMLHttpRequest POST      
    var xhr = new XMLHttpRequest();
    // Execute following code JSON object is received from importGpsTmp.php service
    xhr.onload = function() {
        if (xhr.status === 200) {  
        }
    }
    phpLocation = document.URL + "services/seg_gen_kml.php";             // Variable to store location of php file
    xhrParams = "sqlFilterString=" + sqlWhere;                  // Variable for POST parameters
    xhrParams += "&segmentFileName=" + segmentFileName ;
    xhr.open ('POST', phpLocation, false);                      // Make XMLHttpRequest - in asynchronous mode to avoid wrong data display in map (map displayed before KML file is updated)
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");  // Set header to encode special characters like %
    xhr.send(encodeURI(xhrParams));
        
};

// =================================================================================================================================
// ============================ Functions to validate fields at insert & update ====================================================

// Function updating Validation Comments    
function updateValComments( text ) {
    valComments
        .text( text )
        .addClass( "ui-state-highlight" );
    setTimeout(function() {
        valComments.removeClass( "ui-state-highlight", 1500 );
    }, 500 );
}

// Funtion checking existance of file content in ADD dialog
function checkExistance( origin, name ) {
    if ( origin.val().length == 0 ) {
        origin.addClass( "ui-state-error" );
        updateValComments( "Field " + name + " must be entered" );
        return false;
    } else {
        return true;
    }
}

// Draws the table that list the selected waypoints
function drawPeakTable ( peaksArray ) {
    // If array.length = 1
    // create new html table with value returned by autocomplete
    var peakTable = '';
        peakTable += '<table cellspacing="0" cellpadding="0">';
        /*peakTable += '<tr class="tblWayp">';
        peakTable += '<th>Name</th>';                           // 2
        peakTable += '<th></th>';                           // 3
        peakTable += '</tr>';*/

    for (var i = 0; i < peaksArray.length; i++) {
        if ( peaksArray[i]["disp_f"] == true ) {
            peakTable += '<tr class="tblWayp">';  
            peakTable += '<td>' + peaksArray[i]["waypName"] + '</td>';               // 1    
            peakTable += '<td><ul class="tblWayp"><li class="button_Li"><a class="peakDel button_A"' 
                    + ' href="#peakDel_' + peaksArray[i]["waypId"] + '">'
                    + '<img id="btnPeakDelImg" src="css/images/delete.png"></a></li></ul></tr>';
            peakTable += '</tr>';
        }               
    }
    peakTable += '</table>';   

    document.getElementById('uiAdmTrk_peakList').innerHTML = peakTable;
}