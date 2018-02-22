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

var peakArray = new Array();
var waypArray = new Array();
var locArray = new Array();
var partArray = new Array();
var peakNr = 0;
var waypNr = 0;
var locNr = 0;
var partNr = 0;

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
            source: "services/autoComplete.php?field=peak",
            minLength: 2,
            select: function( event, ui ) {
                $( "" ).val( ui.item.id );
                id = ui.item.id;
                value = ui.item.value;
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

        $( "#uiAdmTrk_waypSrch" ).autocomplete({
            source: "services/autoComplete.php?field=wayp",
            minLength: 2,
            select: function( event, ui ) {
                $( "" ).val( ui.item.id );
                id = ui.item.id;
                value = ui.item.value;
            }
        });

        $( "#uiAdmTrk_locSrch" ).autocomplete({
            source: "services/autoComplete.php?field=loc",
            minLength: 2,
            select: function( event, ui ) {
                $( "" ).val( ui.item.id );
                id = ui.item.id;
                value = ui.item.value;
            }
        });

        $( "#uiAdmTrk_partSrch" ).autocomplete({
            source: "services/autoComplete.php?field=part",
            minLength: 2,
            select: function( event, ui ) {
                $( "" ).val( ui.item.id );
                id = ui.item.id;
                value = ui.item.value;
            }
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
                    map = drawMapEmpty('displayMap-ResMap');         // Draw empty map (without additional layers) 
                }
            }
        }

        var jsonObject = {};
        $loginName = ($('#loginName').val());
        phpLocation = "services/login.php";          // Variable to store location of php file
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
                    map.getLayers().forEach(function(el) {
                        map.removeLayer(el);
                    })
                    mapSTlayer_grau = ga.layer.create('ch.swisstopo.pixelkarte-grau');
                    //mapSTlayer_grau.set('name', 'mapSTlayer_grau');
                    map.addLayer(mapSTlayer_grau);
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
                    map.addLayer(trackKMLlayer);

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
                    map.addLayer(segKMLlayer);
                    
                    // Popup showing the position the user clicked
                }

                var popup = new ol.Overlay({                                        // popup to display track details
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
                        'content': feature.get('name')
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
        phpLocation = "services/gen_kml.php";          // Variable to store location of php file
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

// Upon click on the 'Upload GPX File' button --> call importGps.php in temp mode
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
                $('#statusMessage').text(responseObject.errMessage);
                $("#statusMessage").show().delay(5000).fadeOut();
                document.getElementById("inputFile").value = "";
            } 

        }
    }
    var fileName = document.getElementById('inputFile').files[0];   // assign selected file var
});

$(document).on('click', '#buttonUploadFileJSON', function (e) {
    e.preventDefault();
    var xhr = new XMLHttpRequest();   
    var jsonObject = {};
        
    // Execute following code JSON object is received from importGpsTmp.php - TEMP service
    xhr.onload = function() {
        if (xhr.status === 200) {                                                                       // when all OK
            responseObject = JSON.parse(xhr.responseText);                                              // transfer JSON into response object array
            if ( responseObject.status == 'OK') {
            }
        }
    } 
    var fileName = $('#inputFileJSON').val();
    phpLocation = "services/importGps.php";          // Variable to store location of php file
    jsonObject["sessionid"] = sessionid;                             // append parameter session ID
    jsonObject["request"] = 'json';                              // temp request to create track temporarily
    jsonObject["filename"] = fileName;                              // send track object
    jsonObject["loginname"] = $loginName;
    xhr.open ('POST', phpLocation, true);                           // open  XMLHttpRequest 
    xhr.setRequestHeader( "Content-Type", "application/json" );
    jsn = JSON.stringify(jsonObject);
    xhr.send( jsn );                                           // send formData object to service using xhr  
});

$(document).on('click', '#uiAdmTrk_btnPeakAdd', function (e) {
    
    // Initialise peakList array
    var peaksList =  new Object();

    // Add new peak to array
    peaksList.id = "#peakDel_" + id;                 // 0
    peaksList.itemId = id;                               // 1
    peaksList.itemName = value;                            // 2
    peaksList.itemType = 5;                                // 3
    peaksList.disp_f = true;                             // 4

    peakArray.push(peaksList);

    drawTrackTable ( peakArray, "peak" ); 

    peakNr++;

    // Reset peak array on click on save or cancel

});

$(document).on('click', '#uiAdmTrk_btnWaypAdd', function (e) {
    
    // Initialise waypList array
    var waypList =  new Object();

    // Add new wayp to array
    waypList.id = "#waypDel_" + id;                 // 0
    waypList.itemId = id;                               // 1
    waypList.itemName = value;                            // 2
    waypList.itemType = 3;                                // 3
    waypList.disp_f = true;                             // 4

    waypArray.push(waypList);

    drawTrackTable ( waypArray, "wayp" ); 

    waypNr++;

    // Reset wayp array on click on save or cancel

});

$(document).on('click', '#uiAdmTrk_btnLocAdd', function (e) {
    
    // Initialise locList array
    var locList =  new Object();

    // Add new loc to array
    locList.id = "#locDel_" + id;                 // 0
    locList.itemId = id;                               // 1
    locList.itemName = value;                            // 2
    locList.itemType = 4;                                // 3
    locList.disp_f = true;                             // 4

    locArray.push(locList);

    drawTrackTable ( locArray, "loc" ); 

    locNr++;

    // Reset loc array on click on save or cancel

});

$(document).on('click', '#uiAdmTrk_btnpartAdd', function (e) {
    
    // Initialise partList array
    var partList =  new Object();

    // Add new part to array
    partList.id = "#partDel_" + id;                 // 0
    partList.itemId = id;                               // 1
    partList.itemName = value;                            // 2
    partList.itemType = 4;                                // 3
    partList.disp_f = true;                             // 4

    partArray.push(partList);

    drawTrackTable ( partArray, "part" ); 

    partNr++;

    // Reset part array on click on save or cancel

});

$(document).on('click', '.peakDel', function (e) {
    console.info("clicked on del")
    e.preventDefault();                                             // Prevent link behaviour
    var $activeButtonA = $(this)                                    // Store the current link <a> element
    var peakDelId = this.hash;                                       // Get div class of selected topic (e.g #panelDisplay)
    
    for (var i = 0; i < peakArray.length; i++) {
        if ( peakArray[i]["id"] == peakDelId ) {
            peakArray[i]["disp_f"] = false;
        }    
    }
    drawTrackTable ( peakArray, "peak" );
    
    // Run following block if selected topic is currently not active
    /*if (buttonId && !$activeButtonA.is('.active')) {
        $topicButton.removeClass('active');                         // Make current panel inactive
        $activeButton.removeClass('active');                        // Make current tab inactive

        $topicButton = $(buttonId).addClass('active');              // Make new panel active
        $activeButton = $activeButtonA.parent().addClass('active'); // Make new tab active
    }*/

});

$(document).on('click', '.waypDel', function (e) {
    console.info("clicked on del")
    e.preventDefault();                                             // Prevent link behaviour
    var $activeButtonA = $(this)                                    // Store the current link <a> element
    var waypDelId = this.hash;                                       // Get div class of selected topic (e.g #panelDisplay)
    
    for (var i = 0; i < waypArray.length; i++) {
        if ( waypArray[i]["id"] == waypDelId ) {
            waypArray[i]["disp_f"] = false;
        }    
    }
    drawTrackTable ( waypArray, "wayp" );
    
    // Run following block if selected topic is currently not active
    /*if (buttonId && !$activeButtonA.is('.active')) {
        $topicButton.removeClass('active');                         // Make current panel inactive
        $activeButton.removeClass('active');                        // Make current tab inactive

        $topicButton = $(buttonId).addClass('active');              // Make new panel active
        $activeButton = $activeButtonA.parent().addClass('active'); // Make new tab active
    }*/

});

$(document).on('click', '.locDel', function (e) {
    console.info("clicked on del")
    e.preventDefault();                                             // Prevent link behaviour
    var $activeButtonA = $(this)                                    // Store the current link <a> element
    var locDelId = this.hash;                                       // Get div class of selected topic (e.g #panelDisplay)
    
    for (var i = 0; i < locArray.length; i++) {
        if ( locArray[i]["id"] == locDelId ) {
            locArray[i]["disp_f"] = false;
        }    
    }
    drawTrackTable ( locArray, "loc" );
    
    // Run following block if selected topic is currently not active
    /*if (buttonId && !$activeButtonA.is('.active')) {
        $topicButton.removeClass('active');                         // Make current panel inactive
        $activeButton.removeClass('active');                        // Make current tab inactive

        $topicButton = $(buttonId).addClass('active');              // Make new panel active
        $activeButton = $activeButtonA.parent().addClass('active'); // Make new tab active
    }*/

});

$(document).on('click', '.partDel', function (e) {
    console.info("clicked on del")
    e.preventDefault();                                             // Prevent link behaviour
    var $activeButtonA = $(this)                                    // Store the current link <a> element
    var partDelId = this.hash;                                       // Get div class of selected topic (e.g #panelDisplay)
    
    for (var i = 0; i < partArray.length; i++) {
        if ( partArray[i]["id"] == partDelId ) {
            partArray[i]["disp_f"] = false;
        }    
    }
    drawTrackTable ( partArray, "part" );
});

// Upon click on the 'Save' button --> call importGps.php in save mode (call php with JQUERY $AJAX)
$(document).on('click', '#uiAdmTrk_fld_save', function (e) {
    e.preventDefault();
    var valid = true;                                                                 // true when field check are passed
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
    trackobj.trkLoginName = $loginName;    

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
        jsonObject.peakArray = peakArray;                     // Array containing selected peaks
        jsonObject.waypArray = waypArray;                       // Array containing selected waypointsf
        jsonObject.locArray = locArray;                       // Array containing selected waypointsf
        jsonObject.partArray = partArray;                       // Array containing selected waypointsf
        jsonObject.trackobj = trackobj;                              // send track object
        jsn = JSON.stringify ( jsonObject );

        $.ajax({
            url: phpLocation,
            type: "POST",
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            data: jsn
        })
        .done(function ( data ) {
            if ( data.status == 'OK') {
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

// On click on the 'cancel' button --> cancel update & delete temp track (call php with xhr in JSON mode)
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
    
    //phpLocation = document.URL + "services/importGps.php";          // Variable to store location of php file
    phpLocation = "services/importGps.php";          // Variable to store location of php file
    jsonObject["sessionid"] = sessionid;                             // append parameter session ID
    jsonObject["request"] = 'cancel';                              // temp request to create track temporarily
    jsonObject["trackobj"] = trackobj;                              // send track object
    xhr.open ('POST', phpLocation, true);                           // open  XMLHttpRequest 
    xhr.setRequestHeader( "Content-Type", "application/json" );
    jsn = JSON.stringify(jsonObject);
    xhr.send( jsn );                                           // send formData object to service using xhr  
});

// ==========================================================================
// ========================== panelExport ===================================
// ==========================================================================

// Export Tracks JSON Button clicked
$(document).on('click', '#buttonExportTracks01JSON', function (e) {
    e.preventDefault();
    var xhr = new XMLHttpRequest();   
    var jsonObject = {};
        
    // Execute following code JSON object is received from importGpsTmp.php - TEMP service
    xhr.onload = function() {
        if (xhr.status === 200) {                                                                       // when all OK
            responseObject = JSON.parse(xhr.responseText);                                              // transfer JSON into response object array
            $('#statusMessage').text(responseObject.errMessage);
            $("#statusMessage").show().delay(5000).fadeOut();
        }
    } 

    phpLocation = "services/exportData.php";          // Variable to store location of php file
    jsonObject["request"] = 'tracks01_JSON';                              // temp request to create track temporarily
    jsonObject["loginname"] = $loginName;
    xhr.open ('POST', phpLocation, true);                           // open  XMLHttpRequest 
    xhr.setRequestHeader( "Content-Type", "application/json" );
    jsn = JSON.stringify(jsonObject);
    xhr.send( jsn );                                           // send formData object to service using xhr  
});

// Export Tracks CSV Button clicked
$(document).on('click', '#buttonExportTracks01CSV', function (e) {
    e.preventDefault();
    var xhr = new XMLHttpRequest();   
    var jsonObject = {};
        
    // Execute following code JSON object is received from importGpsTmp.php - TEMP service
    xhr.onload = function() {
        if (xhr.status === 200) {                                                                       // when all OK
            responseObject = JSON.parse(xhr.responseText);                                              // transfer JSON into response object array
            $('#statusMessage').text(responseObject.errMessage);
            $("#statusMessage").show().delay(5000).fadeOut();
        }
    } 

    phpLocation = "services/exportData.php";          // Variable to store location of php file
    jsonObject["request"] = 'tracks01_CSV';                              // temp request to create track temporarily
    jsonObject["loginname"] = $loginName;
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
function drawTrackTable ( itemsArray, itemType ) {
    // Assign var
    var itemClass = "tbl" + itemType;
    var itemDelClass = itemType + "Del";
    var itemDelImg = "btn" + itemType + "DelImg";
    var elementId = "uiAdmTrk_" + itemType + "List";
    // create new html table with value returned by autocomplete

    var itemsTable = '';
        itemsTable += '<table cellspacing="0" cellpadding="0">';

    for (var i = 0; i < itemsArray.length; i++) {
        if ( itemsArray[i]["disp_f"] == true ) {
            itemsTable += '<tr class="' + itemClass + '">';  
            itemsTable += '<td>' + itemsArray[i]["itemName"] + '</td>';               // 1    
            itemsTable += '<td><ul class="' + itemClass + '">';
            itemsTable += '<li class="button_Li"><a class="' + itemDelClass + ' button_A"' 
                    + ' href="#' + itemDelClass + '_' + itemsArray[i]["itemId"] + '">'
                    + '<img id="' + itemDelImg + '" src="css/images/delete.png"></a></li></ul></tr>';
            itemsTable += '</tr>';
        }               
    }
    itemsTable += '</table>';   

    document.getElementById(elementId).innerHTML = itemsTable;
}