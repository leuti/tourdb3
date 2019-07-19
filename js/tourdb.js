// ---------------------------------------------------------------------------------------------
// This is the main javascript file controling the behaviour of the tourdb page 
// 
// Created: 30.12.2017 - Daniel Leutwyler
// ---------------------------------------------------------------------------------------------
//
// Actions:
// * remove unnecessary where statement for filters (e.g. between dates, altitues)

// =================================================
// ====== G L O B A L   V A R   S E C T I O N ======
// =================================================

// Confirmed global vars 
var SESSION_OBJ = {                                                 // this object stores all relevant session infos
    login: "",
    usrId: "",
    loginTime: "",
    loginStatus: "",
    sessionId: "",
    activePanel: "",
    currentFunction: ""
};
TRACK_WAYP_ARRAY = new Array();
TRACK_PART_ARRAY = new Array();

// temporaray global vars
//var TOURDBURL = "http://localhost";                                 // URL of tourdb (only required because I develop on tourdbnew.php)
var TOURDBURL = document.URL;                                  // activate this when deploying to PROD

// unconfirmed global vars

//var today = new Date();                                              // Create unique file name for KML
sqlWherePrev = "";                                                   // variable to store previous sql where statement
sqlWhereSegmentsPrev = "";                                           // the gen_kml.php is only called if statement has changed
var fetch_pages_filterString;                                        // variable to store where clause for list view
var trackKMLlayer;                                                   // map layer object containing all tracks
var segKMLlayer;                                                     // map layer object containing all segments
var mapSTlayer_grau;                                                 // map layer object containing the b/w swiss map

//itemsTrkImp = new Array();                                            // array to store selected peaks, waypoints, locations and participants

// ======================================================
// ====== Perform these actions when page is ready ======
// ======================================================
$(document).ready(function() {

    // Initialse all jquery functional fields for the mask display objects
    // ===================================================================
    initJqueryItems();

    // Evaluate which button/panel is active
    $('.mainButtons').each(function() {
        var $thisTopicButton = $(this);                              // $thisTopicButton becomes ul.mainButtons
        $activeButton = $thisTopicButton.find('li.active');          // Find and store current active li element
        var $activeButtonA = $activeButton.find('a');                // Get link <a> from active li element 
        $topicButton = $($activeButtonA.attr('href'));               // Get active panel      
    });

    // Evaluate which button/panel is active (CAN THIS FUNCTION BE DELETED???)
    $('.uiTrack_btns').each(function() {
        console.log( " Function each on uiTrack_btns activated");  
        var $clickedUpdTrkBtn = $(this);                             // $clickedUpdTrkBtn becomes ul.uiTrack_btns
        $actUpdTrkBtn = $clickedUpdTrkBtn.find('li.active');         // Find and store current active li element
        var $clickedUpdTrkButton_liA = $actUpdTrkBtn.find('a');      // Get link <a> from active li element 
        $actUpdTrkTab = $($clickedUpdTrkButton_liA.attr('href'));    // Get active panel      
    });

    // Change to selected panel
    $(this).on('click', '.mainButtons_a', function(e) {                  
        e.preventDefault();                                          // Prevent link behaviour
        var $activeButtonA = $(this)                                 // Store the current link <a> element
        var buttonId = this.hash;                                    // Get div class of selected topic (e.g #panelLists)
        
        // Run following block if selected topic is currently not active
        if (buttonId && !$activeButtonA.is('.active')) {
            $topicButton.removeClass('active');                      // Make current panel inactive
            $activeButton.removeClass('active');                     // Make current tab inactive

            $topicButton = $(buttonId).addClass('active');           // Make new panel active
            $activeButton = $activeButtonA.parent().addClass('active'); // Make new tab active
        }
    }); 
      
    // ==========================================================================
    // ========================== panelLogin ====================================
    // ==========================================================================

    // ............................................................................
    // Authenticate user, display empty map and load first portion of track list
    $(document).on('click', '#uiLogin_loginBtn', function (e) {
        e.preventDefault();

        // prepare json to post to service
        var jsonOut = {};                                           // Initialise JSON Object for server 
        jsonOut["login"] = ($('#uiLogin_login').val());                 // read login name from mask
        jsonOut["password"] = ($('#uiLogin_password').val());            // append password to JSON object
        phpLocation = "services/login.php";                     // Variable to store location of php file
        jsn = JSON.stringify(jsonOut);                              // Convert jsonn object to JSON formated string
        
        $.ajax({
            url: phpLocation,
            type: "POST",
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            data: jsn
        })
        .done(function ( jsonIn ) {
            if ( jsonIn.loginStatus == "OK"){                   // Login successful
                
                // set session object vars
                // -----------------------
                SESSION_OBJ.login = jsonIn.login;
                SESSION_OBJ.usrId = jsonIn.usrId;
                SESSION_OBJ.loginTime = jsonIn.loginTime;
                SESSION_OBJ.loginStatus = jsonIn.loginStatus;
                SESSION_OBJ.sessionId = jsonIn.sessionId;
                SESSION_OBJ.activePanel = "map";
                SESSION_OBJ.currentFunction = "";

                // Manage displayed items
                // ----------------------

                // Open Panel Display
                var $activeButtonA = $('#mainButtons_mapBtn_a')            // Store the current link <a> element
                buttonId = $activeButtonA.attr('href'); 
                
                // Run following block if selected topic is currently not active
                $topicButton.removeClass('active');                         // Make current panel inactive
                $activeButton.removeClass('active');                        // Make current tab inactive
                $topicButton = $(buttonId).addClass('active');              // Make new panel active
                $activeButton = $activeButtonA.parent().addClass('active'); // Make new tab active
                $('.loginReq').removeClass('loginReq');                     // Free deactivated menues
                $('#uiLogin_loginBtn').addClass('loginReq');

                // Display status message
                // ----------------------
                $('#statusMessage').text('Login successful');
                $("#statusMessage").show().delay(5000).fadeOut(); 
                
                // Draw empty map (why empty???)
                // --------------
                if ( typeof(ga) != 'undefined' ) {
                    tourdbMap = drawMapEmpty('displayMap-ResMap');          // Draw empty map (without additional layers) 
                }

                // Load first set of tracks to be displayed in the List panel
                // ----------------------------------------------------------
                var page = 1;
                fetch_pages_filterString = " trkUsrId= '" + SESSION_OBJ.usrId + "'";      // where string for list view (fetch_lists.php)
                $("#tabDispLists_trks").load("services/fetch_lists.php",
                    {"sqlFilterString":fetch_pages_filterString,"page":page}); //get content from PHP page    

            } else {                                            // Login failed
                // Display status message
                // ----------------------
                $('#statusMessage').text(jsonIn.message);
                $('#statusMessage').show().delay(5000).fadeOut();
            }
        });                                                                                 
    });   
});

// ==========================================================================
// ========================== panelMap ===============================
// ==========================================================================

// Minimize the map filter UI
$(document).on('click', '#dispObjMenuLargeClose', function(e) {
    e.preventDefault();
    var $activeButton = $(this);
    $activeButton.parent().removeClass('visible');
    $activeButton.parent().addClass('hidden');
    $('.dispObjMini').removeClass('hidden');
    $('.dispObjMini').addClass('visible');
})

// Open the map filter UI
$(document).on('click', '#dispObjMenuMiniOpen', function(e) {
    e.preventDefault();
    var $activeButton = $(this);
    $activeButton.parent().removeClass('visible');
    $activeButton.parent().addClass('hidden');
    $('.dispObjOpen').removeClass('hidden');
    $('.dispObjOpen').addClass('visible');
})

// Triggers KML generation for filtered tracks and selected waypoints and segments
$(document).on('click', '.uiMapApplyBtn', function (e) {
    e.preventDefault();
    $clickedButton = this.id;                                       // store id of button clicked

    // Initialise overall variables
    var sqlWhereCurrent = "";                                              // Initialise var for the current where string
    var sqlWherePrev_tracks = "";
    var sqlWherePrev_segments = "";
    var sqlWherePrev_peaks_100 = "";
    var sqlWherePrev_peaks_1000 = "";
    var sqlWherePrev_peaks_2000 = "";
    var sqlWherePrev_peaks_3000 = "";
    var sqlWherePrev_peaks_4000 = "";
    var sqlWherePrev_peaks_cant = "";
    var sqlWherePrev_huts = "";

    // Request KML file for Tracks
    // ---------------------------

    sqlWhereCurrent = createTrkKmlWhere ();                             // call function to generate sql where statement

    // Create new display object for current object
    var objName = "tracks";
    var phpUrl = "services/gen_kml.php";
    var jsonObject = {
        sessionId: SESSION_OBJ.sessionId,
        usrId: SESSION_OBJ.usrId,
        objectName: objName,
        sqlWhere: sqlWhereCurrent
    }
    jsn = JSON.stringify ( jsonObject )
    if ( sqlWhereCurrent != "" ) {
        var genKml = true;
        var ajaxCall = {
            url: phpUrl,
            type: "POST",
            contentType: "application/json; charset=utf-8",
            dataType: 'json',
            data: jsn
        }
    } else {
        var genKml = false;
        var ajaxCall = {
            url: "services/no_kml.php",
            type: "POST",
            contentType: "application/json; charset=utf-8",
            dataType: 'json',
            data: jsn
        };
    }
    var dispObject_tracks = new DisplayObj( objName, sqlWherePrev, sqlWhereCurrent, genKml, jsn, ajaxCall )

    // Request KML file for Segments
    // -----------------------------
 
    sqlWhereCurrent = createSegKmlWhere();                          // call function to generate sql where statement
    
    // Create new display object for current object
    var objName = "segments";
    var phpUrl = "services/gen_kml.php";
    var jsonObject = {
        sessionId: SESSION_OBJ.sessionId,
        usrId: SESSION_OBJ.usrId,
        objectName: objName,
        sqlWhere: sqlWhereCurrent
    }
    jsn = JSON.stringify ( jsonObject )
    if ( sqlWhereCurrent != sqlWherePrev_segments || sqlWhereCurrent != "" ) {
        var genKml = true;
        var ajaxCall = {
            url: phpUrl,
            type: "POST",
            contentType: "application/json; charset=utf-8",
            dataType: 'json',
            data: jsn
        }
    } else {
        var genKml = false;
        var ajaxCall = {
            url: "services/no_kml.php",
            type: "POST",
            contentType: "application/json; charset=utf-8",
            dataType: 'json',
            data: jsn
        };
    }
    var dispObject_segments = new DisplayObj( objName, sqlWherePrev, sqlWhereCurrent, genKml, jsn, ajaxCall )

    // Request KML file for Waypoints
    // -----------------------------------------------------------------------------

    // Peaks < 1000
    // ------------
    var sqlWhereCurrent = "WHERE "
    sqlWhereCurrent += "waypTypeFID = 5 AND ";
    sqlWhereCurrent += "waypAltitude < 1000 ";
    
    // Create new display object for current object
    var objName = "peaks_100";
    var phpUrl = "services/gen_wayp.php";
    var jsonObject = {
        sessionId: SESSION_OBJ.sessionId,
        usrId: SESSION_OBJ.usrId,
        objectName: objName,
        sqlWhere: sqlWhereCurrent
    }
    jsn = JSON.stringify ( jsonObject )
    
    itemChecked = document.getElementById("dispObjPeaks_100").checked;
    if ( itemChecked ) {
        var genKml = true;
        var ajaxCall = {
            url: phpUrl,
            type: "POST",
            contentType: "application/json; charset=utf-8",
            dataType: 'json',
            data: jsn
        }
    } else {
        var genKml = false;
        var ajaxCall = {
            url: "services/no_kml.php",
            type: "POST",
            contentType: "application/json; charset=utf-8",
            dataType: 'json',
            data: jsn
        };
    }
    var dispObject_peaks_100 = new DisplayObj( objName, sqlWherePrev, sqlWhereCurrent, genKml, jsn, ajaxCall )

    // Peaks 1000er
    // ------------
    var sqlWhereCurrent = "WHERE ";                                        // Initialise array for whereStatement
    sqlWhereCurrent += "waypTypeFID = 5 AND ";
    sqlWhereCurrent += "waypAltitude < 2000 AND ";
    sqlWhereCurrent += "waypAltitude >= 1000 ";
    
    var objName = "peaks_1000";
    var phpUrl = "services/gen_wayp.php";
    var jsonObject = {
        sessionId: SESSION_OBJ.sessionId,
        usrId: SESSION_OBJ.usrId,
        objectName: objName,
        sqlWhere: sqlWhereCurrent
    }
    jsn = JSON.stringify ( jsonObject )

    itemChecked = document.getElementById("dispObjPeaks_1000").checked;
    if ( itemChecked ) {
        var genKml = true;
        var ajaxCall = {
            url: phpUrl,
            type: "POST",
            contentType: "application/json; charset=utf-8",
            dataType: 'json',
            data: jsn
        }
    } else {
        var genKml = false;
        var ajaxCall = {
            url: "services/no_kml.php",
            type: "POST",
            contentType: "application/json; charset=utf-8",
            dataType: 'json',
            data: jsn
        };
    }
    var dispObject_peaks_1000 = new DisplayObj( objName, sqlWherePrev, sqlWhereCurrent, genKml, jsn, ajaxCall )

    // Peaks 2000er
    // ------------
    var sqlWhereCurrent = "WHERE ";                                        // Initialise array for whereStatement
    sqlWhereCurrent += "waypTypeFID = 5 AND ";
    sqlWhereCurrent += "waypAltitude < 3000 AND ";
    sqlWhereCurrent += "waypAltitude >= 2000 ";
    
    var objName = "peaks_2000";
    var phpUrl = "services/gen_wayp.php";
    var jsonObject = {
        sessionId: SESSION_OBJ.sessionId,
        usrId: SESSION_OBJ.usrId,
        objectName: objName,
        sqlWhere: sqlWhereCurrent
    }
    jsn = JSON.stringify ( jsonObject )

    itemChecked = document.getElementById("dispObjPeaks_2000").checked;
    if ( itemChecked ) {
        var genKml = true;
        var ajaxCall = {
            url: phpUrl,
            type: "POST",
            contentType: "application/json; charset=utf-8",
            dataType: 'json',
            data: jsn
        }
    } else {
        var genKml = false;
        var ajaxCall = {
            url: "services/no_kml.php",
            type: "POST",
            contentType: "application/json; charset=utf-8",
            dataType: 'json',
            data: jsn
        };
    }
    var dispObject_peaks_2000 = new DisplayObj( objName, sqlWherePrev, sqlWhereCurrent, genKml, jsn, ajaxCall )

    // Peaks 3000er
    // ------------
    var sqlWhereCurrent = "WHERE ";                                        // Initialise array for whereStatement
    sqlWhereCurrent += "waypTypeFID = 5 AND ";
    sqlWhereCurrent += "waypAltitude < 4000 AND ";
    sqlWhereCurrent += "waypAltitude >= 3000 ";
    
    var objName = "peaks_3000";
    var phpUrl = "services/gen_wayp.php";
    var jsonObject = {
        sessionId: SESSION_OBJ.sessionId,
        usrId: SESSION_OBJ.usrId,
        objectName: objName,
        sqlWhere: sqlWhereCurrent
    }
    jsn = JSON.stringify ( jsonObject )
    itemChecked = document.getElementById("dispObjPeaks_3000").checked;
    if ( itemChecked ) {
        var genKml = true;
        var ajaxCall = {
            url: phpUrl,
            type: "POST",
            contentType: "application/json; charset=utf-8",
            dataType: 'json',
            data: jsn
        }
    } else {
        var genKml = false;
        var ajaxCall = {
            url: "services/no_kml.php",
            type: "POST",
            contentType: "application/json; charset=utf-8",
            dataType: 'json',
            data: jsn
        };
    }
    var dispObject_peaks_3000 = new DisplayObj( objName, sqlWherePrev, sqlWhereCurrent, genKml, jsn, ajaxCall )

    // Peaks 4000er
    // ------------
    var sqlWhereCurrent = "WHERE ";                                        // Initialise array for whereStatement
    sqlWhereCurrent += "waypTypeFID = 5 AND ";
    sqlWhereCurrent += "waypUIAA4000 = true ";
    
    var objName = "peaks_4000";
    var phpUrl = "services/gen_wayp.php";
    var jsonObject = {
        sessionId: SESSION_OBJ.sessionId,
        usrId: SESSION_OBJ.usrId,
        objectName: objName,
        sqlWhere: sqlWhereCurrent
    }
    jsn = JSON.stringify ( jsonObject )

    itemChecked = document.getElementById("dispObjPeaks_4000").checked;
    if ( itemChecked ) {
        var genKml = true;
        var ajaxCall = {
            url: phpUrl,
            type: "POST",
            contentType: "application/json; charset=utf-8",
            dataType: 'json',
            data: jsn
        }
    } else {
        var genKml = false;
        var ajaxCall = {
            url: "services/no_kml.php",
            type: "POST",
            contentType: "application/json; charset=utf-8",
            dataType: 'json',
            data: jsn
        };
    }
    var dispObject_peaks_4000 = new DisplayObj( objName, sqlWherePrev, sqlWhereCurrent, genKml, jsn, ajaxCall )

    // Peaks Top of Cantons
    // --------------------
    var sqlWhereCurrent = "WHERE ";                                        // Initialise array for whereStatement
    sqlWhereCurrent += "waypTypeFID = 5 AND ";
    sqlWhereCurrent += "waypToOfCant != '0' ";

    var objName = "peaks_cant";
    var phpUrl = "services/gen_wayp.php";
    var jsonObject = {
        sessionId: SESSION_OBJ.sessionId,
        usrId: SESSION_OBJ.usrId,
        objectName: objName,
        sqlWhere: sqlWhereCurrent
    }
    jsn = JSON.stringify ( jsonObject )

    itemChecked = document.getElementById("dispObjPeaks_cant").checked;
    if ( itemChecked ) {
        var genKml = true;
        var ajaxCall = {
            url: phpUrl,
            type: "POST",
            contentType: "application/json; charset=utf-8",
            dataType: 'json',
            data: jsn
        }
    } else {
        var genKml = false;
        var ajaxCall = {
            url: "services/no_kml.php",
            type: "POST",
            contentType: "application/json; charset=utf-8",
            dataType: 'json',
            data: jsn
        };
    }
    
    var dispObject_peaks_cant = new DisplayObj( objName, sqlWherePrev, sqlWhereCurrent, genKml, jsn, ajaxCall )

    // Huts
    // ----
    var sqlWhereCurrent = "WHERE ";                                        // Initialise array for whereStatement
    sqlWhereCurrent += "waypTypeFID = 4 ";

    var objName = "huts";
    var phpUrl = "services/gen_wayp.php";
    var jsonObject = {
        sessionId: SESSION_OBJ.sessionId,
        usrId: SESSION_OBJ.usrId,
        objectName: objName,
        sqlWhere: sqlWhereCurrent
    }
    jsn = JSON.stringify ( jsonObject )

    itemChecked = document.getElementById("dispObjHuts").checked;
    if ( itemChecked ) {
        var genKml = true;
        var ajaxCall = {
            url: phpUrl,
            type: "POST",
            contentType: "application/json; charset=utf-8",
            dataType: 'json',
            data: jsn
        }
    } else {
        var genKml = false;
        var ajaxCall = {
            url: "services/no_kml.php",
            type: "POST",
            contentType: "application/json; charset=utf-8",
            dataType: 'json',
            data: jsn
        };
    }
    var dispObject_huts = new DisplayObj( objName, sqlWherePrev, sqlWhereCurrent, genKml, jsn, ajaxCall )

    // ********************************************************************************************
    // Draw map fresh

    // delete current map
    var element = document.getElementById('displayMap-ResMap');
    var parent = element.parentNode
    parent.removeChild(element);
    parent.innerHTML = '<div id="displayMap-ResMap"></div>';

    // ********************************************************************************************
    // ***** Loop through display array and assign relevant parameters to jsonObject

    // Prepare ajax call for TRACKS kml
    
    // Wait for each ajax call to complete & continue only when all are finished (regardless if in error)
    $.when( $.ajax(dispObject_tracks.ajaxCall),  
            $.ajax(dispObject_segments.ajaxCall),
            $.ajax(dispObject_peaks_100.ajaxCall),
            $.ajax(dispObject_peaks_1000.ajaxCall),
            $.ajax(dispObject_peaks_2000.ajaxCall),
            $.ajax(dispObject_peaks_3000.ajaxCall),
            $.ajax(dispObject_peaks_4000.ajaxCall),
            $.ajax(dispObject_peaks_cant.ajaxCall),
            $.ajax(dispObject_huts.ajaxCall)
    // resp_xy contain the response array of the ajax call [data, statusText, jqXHR]
    ).then( function ( resp_tracks, resp_segments, resp_peaks_100, resp_peaks_1000, 
                      resp_peaks_2000, resp_peaks_3000, resp_peaks_4000, resp_peaks_cant, resp_huts ) {
        respObj = {};

        // store current where statement as previous where statement
        sqlWherePrev_tracks = dispObject_tracks.sqlWhereCurrent;
        sqlWherePrev_segments = dispObject_segments.sqlWhereCurrent;
        sqlWherePrev_peaks_100 = dispObject_peaks_100.sqlWhereCurrent;
        sqlWherePrev_peaks_1000 = dispObject_peaks_1000.sqlWhereCurrent;
        sqlWherePrev_peaks_2000 = dispObject_peaks_2000.sqlWhereCurrent;
        sqlWherePrev_peaks_3000 = dispObject_peaks_3000.sqlWhereCurrent;
        sqlWherePrev_peaks_4000 = dispObject_peaks_4000.sqlWhereCurrent;
        sqlWherePrev_peaks_cant = dispObject_peaks_cant.sqlWhereCurrent;
        sqlWherePrev_huts = dispObject_huts.sqlWhereCurrent;

        // store responses from php calls
        var phpResponse = new Array();  
        phpResponse[0] = resp_tracks[0];
        phpResponse[1] = resp_segments[0];
        phpResponse[2] = resp_peaks_100[0];
        phpResponse[3] = resp_peaks_1000[0];
        phpResponse[4] = resp_peaks_2000[0];
        phpResponse[5] = resp_peaks_3000[0];
        phpResponse[6] = resp_peaks_4000[0];
        phpResponse[7] = resp_peaks_cant[0];
        phpResponse[8] = resp_huts[0];

        // Derive coordinate boundaries

        // possible values: outside boundary / inside boundary / NaN
        // events: both return value / only one returns value / none return value

        var coordTop_tracks = Number(resp_tracks[0].coordTop);
        var coordTop_segments = Number(resp_segments[0].coordTop);
        if ( isNaN( coordTop_tracks ) && isNaN( coordTop_segments ) ) {                 // both do NOT deliver coordinates
            coordTop = 297000;
        } else if ( isNaN( coordTop_segments ) ) {                                      // segments DOES NOT deliver coordinates
            var coordTop = coordTop_tracks;
        } else if ( isNaN( coordTop_tracks ) ) {                                        // tracks DOES NOT deliver coordinates
            var coordTop = coordTop_segments;
        } else {                                                                        // both deliver coordinates
            var coordTop = Math.max(coordTop_tracks, coordTop_segments);                
        }

        var coordBottom_tracks = Number(resp_tracks[0].coordBottom);
        var coordBottom_segments = Number(resp_segments[0].coordBottom);
        if ( isNaN( coordBottom_tracks ) && isNaN( coordBottom_segments ) ) {                 // both do NOT deliver coordinates
            coordBottom = 74000;
        } else if ( isNaN( coordBottom_segments ) ) {                                      // segments DOES NOT deliver coordinates
            var coordBottom = coordBottom_tracks;
        } else if ( isNaN( coordBottom_tracks ) ) {                                        // tracks DOES NOT deliver coordinates
            var coordBottom = coordBottom_segments;
        } else {                                                                        // both deliver coordinates
            var coordBottom = Math.min(coordBottom_tracks, coordBottom_segments);                
        }

        var coordLeft_tracks = Number(resp_tracks[0].coordLeft);
        var coordLeft_segments = Number(resp_segments[0].coordLeft);
        if ( isNaN( coordLeft_tracks ) && isNaN( coordLeft_segments ) ) {                 // both do NOT deliver coordinates
            coordLeft = 484000;
        } else if ( isNaN( coordLeft_segments ) ) {                                      // segments DOES NOT deliver coordinates
            var coordLeft = coordLeft_tracks;
        } else if ( isNaN( coordLeft_tracks ) ) {                                        // tracks DOES NOT deliver coordinates
            var coordLeft = coordLeft_segments;
        } else {                                                                        // both deliver coordinates
            var coordLeft = Math.min(coordLeft_tracks, coordLeft_segments);                
        }

        var coordRight_tracks = Number(resp_tracks[0].coordRight);
        var coordRight_segments = Number(resp_segments[0].coordRight);
        if ( isNaN( coordRight_tracks ) && isNaN( coordRight_segments ) ) {                 // both do NOT deliver coordinates
            coordRight = 835000;
        } else if ( isNaN( coordRight_segments ) ) {                                      // segments DOES NOT deliver coordinates
            var coordRight = coordRight_tracks;
        } else if ( isNaN( coordRight_tracks ) ) {                                        // tracks DOES NOT deliver coordinates
            var coordRight = coordRight_segments;
        } else {                                                                        // both deliver coordinates
            var coordRight = Math.max(coordRight_tracks, coordRight_segments);                
        }

        // Evluate coord center (if route is outside CH - show empty CH map)
        var coordCenterY = ( coordTop + coordBottom ) / 2;
        var coordCenterX = ( coordRight + coordLeft ) / 2;
        
        // Calculate required resolution
        resolution1 = ( coordTop - coordBottom ) / 200;
        resolution2 = ( coordRight - coordLeft ) / 200;
        if ( resolution1 > resolution2 ) {
            resolution = Math.min(500,resolution1);
        } else {
            resolution = Math.min(500,resolution2);
        }

        // Draw empty map & center to provided coordinate
        if ( typeof(ga) != 'undefined' ) {
            var tourdbMap = new ga.Map({
                target: 'displayMap-ResMap',
                view: new ol.View({resolution: resolution, center: [coordCenterX, coordCenterY]})
            });
            mapSTlayer_grau = ga.layer.create('ch.swisstopo.pixelkarte-grau');
            tourdbMap.addLayer(mapSTlayer_grau);                              // add map layer to map
        }

        // Draw kml file for tracks 
        if ( dispObject_tracks.genKml && tourdbMap ) {                                            // var is true when user has set filter on tracks
            $kmlFile = TOURDBURL + "/tmp/kml_disp/" + SESSION_OBJ.sessionId + "/tracks.kml";
        
            // Create the KML Layer for tracks
            kmlLayer = new ol.layer.Vector({                       // create new vector layer for tracks
                source: new ol.source.Vector({                          // Set source to kml file
                    url: $kmlFile,
                    format: new ol.format.KML({
                        projection: 'EPSG:21781'
                    })
                })
            });
            tourdbMap.addLayer(kmlLayer);                                // add track layer to map
        }

        // Draw kml file for segments 
        if ( dispObject_segments.genKml && tourdbMap ) {                                            // var is true when user has set filter on segments
            $kmlFile = TOURDBURL + "/tmp/kml_disp/" + SESSION_OBJ.sessionId + "/segments.kml";
        
            // Create the KML Layer for segments
            kmlLayer = new ol.layer.Vector({                       // create new vector layer for segments
                source: new ol.source.Vector({                          // Set source to kml file
                    url: $kmlFile,
                    format: new ol.format.KML({
                        projection: 'EPSG:21781'
                    })
                })
            });
            tourdbMap.addLayer(kmlLayer);                                // add track layer to map
        }

        // Draw kml file for peaks_100 
        if ( dispObject_peaks_100.genKml && tourdbMap ) {                                            // var is true when user has set filter on peaks_100
            $kmlFile = TOURDBURL + "/tmp/kml_disp/" + SESSION_OBJ.sessionId + "/peaks_100.kml";
        
            // Create the KML Layer for peaks_100
            kmlLayer = new ol.layer.Vector({                       // create new vector layer for peaks_100
                source: new ol.source.Vector({                          // Set source to kml file
                    url: $kmlFile,
                    format: new ol.format.KML({
                        projection: 'EPSG:21781'
                    })
                })
            });
            tourdbMap.addLayer(kmlLayer);                                // add track layer to map
        }

        // Draw kml file for peaks_1000 
        if ( dispObject_peaks_1000.genKml && tourdbMap ) {                                            // var is true when user has set filter on peaks_1000
            $kmlFile = TOURDBURL + "/tmp/kml_disp/" + SESSION_OBJ.sessionId + "/peaks_1000.kml";
        
            // Create the KML Layer for peaks_1000
            kmlLayer = new ol.layer.Vector({                       // create new vector layer for peaks_1000
                source: new ol.source.Vector({                          // Set source to kml file
                    url: $kmlFile,
                    format: new ol.format.KML({
                        projection: 'EPSG:21781'
                    })
                })
            });
            tourdbMap.addLayer(kmlLayer);                                // add track layer to map
        }

        // Draw kml file for peaks_2000 
        if ( dispObject_peaks_2000.genKml && tourdbMap ) {                                            // var is true when user has set filter on peaks_2000
            $kmlFile = TOURDBURL + "/tmp/kml_disp/" + SESSION_OBJ.sessionId + "/peaks_2000.kml";
        
            // Create the KML Layer for peaks_2000
            kmlLayer = new ol.layer.Vector({                       // create new vector layer for peaks_2000
                source: new ol.source.Vector({                          // Set source to kml file
                    url: $kmlFile,
                    format: new ol.format.KML({
                        projection: 'EPSG:21781'
                    })
                })
            });
            tourdbMap.addLayer(kmlLayer);                                // add track layer to map
        }

        // Draw kml file for peaks_3000 
        if ( dispObject_peaks_3000.genKml && tourdbMap ) {                                            // var is true when user has set filter on peaks_3000
            $kmlFile = TOURDBURL + "/tmp/kml_disp/" + SESSION_OBJ.sessionId + "/peaks_3000.kml";
        
            // Create the KML Layer for peaks_3000
            kmlLayer = new ol.layer.Vector({                       // create new vector layer for peaks_3000
                source: new ol.source.Vector({                          // Set source to kml file
                    url: $kmlFile,
                    format: new ol.format.KML({
                        projection: 'EPSG:21781'
                    })
                })
            });
            tourdbMap.addLayer(kmlLayer);                                // add track layer to map
        }

        // Draw kml file for peaks_4000 
        if ( dispObject_peaks_4000.genKml && tourdbMap ) {                                            // var is true when user has set filter on peaks_4000
            $kmlFile = TOURDBURL + "/tmp/kml_disp/" + SESSION_OBJ.sessionId + "/peaks_4000.kml";
        
            // Create the KML Layer for peaks_4000
            kmlLayer = new ol.layer.Vector({                       // create new vector layer for peaks_4000
                source: new ol.source.Vector({                          // Set source to kml file
                    url: $kmlFile,
                    format: new ol.format.KML({
                        projection: 'EPSG:21781'
                    })
                })
            });
            tourdbMap.addLayer(kmlLayer);                                // add track layer to map
        }

        // Draw kml file for cant 
        if ( dispObject_peaks_cant.genKml && tourdbMap ) {                                            // var is true when user has set filter on cant
            $kmlFile = TOURDBURL + "/tmp/kml_disp/" + SESSION_OBJ.sessionId + "/peaks_cant.kml";
        
            // Create the KML Layer for cant
            kmlLayer = new ol.layer.Vector({                       // create new vector layer for cant
                source: new ol.source.Vector({                          // Set source to kml file
                    url: $kmlFile,
                    format: new ol.format.KML({
                        projection: 'EPSG:21781'
                    })
                })
            });
            tourdbMap.addLayer(kmlLayer);                                // add track layer to map
        }

        // Draw kml file for huts 
        if ( dispObject_huts.genKml && tourdbMap ) {                                            // var is true when user has set filter on huts
            $kmlFile = TOURDBURL + "/tmp/kml_disp/" + SESSION_OBJ.sessionId + "/huts.kml";
        
            // Create the KML Layer for huts
            kmlLayer = new ol.layer.Vector({                       // create new vector layer for huts
                source: new ol.source.Vector({                          // Set source to kml file
                    url: $kmlFile,
                    format: new ol.format.KML({
                        projection: 'EPSG:21781'
                    })
                })
            });
            tourdbMap.addLayer(kmlLayer);                                // add track layer to map
        }
        if ( tourdbMap ) {
            // Popup showing the position the user clicked
            var popup = new ol.Overlay({                                    // popup to display track details
                element: $('<div title="KML"></div>')[0]
            });
            tourdbMap.addOverlay(popup);

            // On click we display the feature informations (code basis from map admin sample library)
            tourdbMap.on('singleclick', function(evt) {
                var pixel = evt.pixel;
                var coordinate = evt.coordinate;
                var feature = tourdbMap.forEachFeatureAtPixel(pixel, function(feature, layer) {
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

            // Change cursor style when cursor is hover over a feature
            tourdbMap.on('pointermove', function(evt) {
                var feature = tourdbMap.forEachFeatureAtPixel(evt.pixel, function(feature, layer) {
                    return feature;
                });
                tourdbMap.getTargetElement().style.cursor = feature ? 'pointer' : '';
            });
        }

        // Display message
        var phpMessage = "";
        var phpObjCount = 0; 
        var phpHasError = false;
        for ( var i = 0; i < phpResponse.length; i++ ) {
            if ( phpResponse[i]["status"] != "OK" ) {
                phpMessage += "-" + phpResponse[i]["message"] + "-";
                phpHasError = true;
            } else {
                phpObjCount = phpObjCount + phpResponse[i]["recordcount"];
            }
        }

        if ( phpHasError ) {
            $('#statusMessage').text(phpMessage);    
        } else {
            $('#statusMessage').text(phpObjCount + " objects are displayed");
        }
        
        $("#statusMessage").show().delay(5000).fadeOut();                       // hide message after 5 seconds
    
        // Hide display filter form
        $('.dispObjOpen').removeClass('visible');
        $('.dispObjOpen').addClass('hidden');
        $('.dispObjMini').addClass('visible');
        $('.dispObjMini').removeClass('hidden');
    
    });
    
});

// ============================================================================
// ========================== panelLists ===============================
// ============================================================================

// Opens the list filter UI
$(document).on('click', '#dispListTrkMenuMiniOpen', function(e) {
    e.preventDefault();
    var $activeButton = $(this);
    $activeButton.parent().removeClass('visible');
    $activeButton.parent().addClass('hidden');
    $('#dispListTrkMenuLarge').removeClass('hidden');
    $('#dispListTrkMenuLarge').addClass('visible');
})

// Minimizes the list filter UI
$(document).on('click', '#dispListTrkMenuLargeClose', function(e) {
    e.preventDefault();
    var $activeButton = $(this);
    $activeButton.parent().removeClass('visible');
    $activeButton.parent().addClass('hidden');
    $('#dispListTrkMenuMini').removeClass('hidden');
    $('#dispListTrkMenuMini').addClass('visible');
})

// Requests the filtered tracks and displays them as a table
$(document).on('click', '#dispListTrk_NewLoadButton', function (e) {
    e.preventDefault();
    $clickedButton = this.id;                                       // store id of button clicked

    // ********************************************************************************************
    // Build SQL WHERE statement for tracks

    // Initialise tracks variables
    var whereStatement = [];                                        // Initialise array for whereStatement
    var whereString = "";                                           // Initialise var for where string
   
    // Field trackID from / to
    var trackIdFrom = "";                                           // Initialse var for track id from 
    var trackIdTo = "";                                             // Initialse var for track id to
    if ( ($('#dispListTrk_trackIdFrom').val()) != "" ) {                           
        trackIdFrom = $('#dispListTrk_trackIdFrom').val();
    } else {
        trackIdFrom = "";
    };

    if ( ($('#dispListTrk_trackIdTo').val()) != "" ) {                           
        trackIdTo = $('#dispListTrk_trackIdTo').val();
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
    if ( ($('#dispListTrk_trackName').val()) != "" ) {                           
        whereString = "trkTrackName like '%" + $('#dispListTrk_trackName').val() + "%'";
        whereStatement.push( whereString );
    };

    // Field route
    var whereString = "";
    if ( ($('#dispListTrk_route').val()) != "" ) {
        whereString = "trkRoute like '%" + $('#dispListTrk_route').val() + "%'";
        whereStatement.push( whereString );
    };

    // Field date begin (date finished not used)
    var whereString = "";                                                       // clear where string
    fromDateArt = "1968-01-01";                                                 // Set from date in case no date is entered
    var today = new Date();                                                     // Set to date to today in case no date is entered
    month = today.getMonth()+1;                                                 // Extract month (January = 0)
    toDateArt = today.getFullYear() + '-' + month + '-' + today.getDate();      // Set to date to today (format yyyy-mm-dd)
    
    if ( ($('#dispListTrk_dateFrom').val()) != "" ) {                            // Overwrite fromDate with value entered by user
        fromDate = ($('#dispListTrk_dateFrom').val());
    } else {
        fromDate = "";
    }

    if ( ($('#dispListTrk_dateTo').val()) != "" ) {                              // Overwrite toDate with value entered by user
        toDate = ($('#dispListTrk_dateTo').val())                                // Add to where Statement array
    } else {
        toDate = "";
    }

    if ( fromDate != "" && toDate != "" ) {
        whereString = "trkDateBegin BETWEEN '" + fromDate + "' AND '" + toDate + "'";           // complete WHERE BETWEEN statement
    } else if ( fromDate != "" ) {
        whereString = "trkDateBegin BETWEEN '" + fromDate + "' AND '" + toDateArt + "'";        // complete WHERE BETWEEN statement
    } else if ( toDate != "" ) {
        whereString = "trkDateBegin BETWEEN '" + fromDateArt + "' AND '" + toDate + "'";        // complete WHERE BETWEEN statement
    }
    if ( whereString.length > 0 ) {
        whereStatement.push( whereString );                                         // Add to where Statement array
    }

    // Field type
    var whereString = "";
    $('#dispListTrk_type .ui-selected').each(function() {                        // loop through each selected type item
        var itemId = this.id                                                    // Extract id of selected item
        whereString = whereString + "'" + itemId.slice(16) + "',";              // Substring tyye from id
    });
    if ( whereString.length > 0 ) {
        whereString = whereString.slice(0,whereString.length-1);                // remove last comma
        whereString = "trkType in (" + whereString + ")";                       // complete SELECT IN statement
        whereStatement.push( whereString );                                     // Add to where Statement array
    };

    // Field subtype
    var whereString = "";                                                       
    $('#dispListTrk_subtype .ui-selected').each(function() {                     // loop through each selected type item
        var itemId = this.id                                                    // Extract id of selected item
        whereString = whereString + "'" + itemId.slice(20) + "',";              // Substring tyye from id
    });
    if ( whereString.length > 0 ) {
        whereString = whereString.slice(0,whereString.length-1);                // remove last comma
        whereString = "trkSubType in (" + whereString + ")";                    // complete SELECT IN statement
        whereStatement.push( whereString );                                     // Add to where Statement array
    }           

    // Field participants
    /*var whereString = "";
    if ( ($('#dispListTrk_participants').val()) != "" ) {
        whereString = "trkParticipants like '%" + $('#dispListTrk_participants').val() + "%'";
        whereStatement.push( whereString );
    };
    */

    // Field country
    var whereString = "";
    if ( ($('#dispListTrk_country').val()) != "" ) {
        whereString = "trkCountry like '%" + $('#dispListTrk_country').val() + "%'";
        whereStatement.push( whereString );
    };
    
    // ========== Put all where statements together
    if ( whereStatement.length > 0 ) {
        var sqlWhereCurrent = "";

        for (var i=0; i < whereStatement.length; i++) {
            sqlWhereCurrent += whereStatement[i];
            sqlWhereCurrent += " AND ";
        }
        sqlWhereCurrent = sqlWhereCurrent + " trkUsrId='" + SESSION_OBJ.usrId + "'";
    } 

    // ***********************************************************************
    // Fetch page for tracks

    var page = 1;
    // delete current map
    var element = document.getElementById("tabDispLists_trks");
    var parent = element.parentNode
    parent.removeChild(element);
    parent.innerHTML = '<div id="tabDispLists_trks"></div>';

    fetch_pages_filterString = sqlWhereCurrent;
    if ( !fetch_pages_filterString ) {
        fetch_pages_filterString = " trkUsrId= '" + SESSION_OBJ.usrId + "'";   
    }
    $("#tabDispLists_trks").load("services/fetch_lists.php",{"sqlFilterString":fetch_pages_filterString,"page":page}); //get content from PHP page
    $('#dispListTrkMenuLarge').removeClass('visible');
    $('#dispListTrkMenuLarge').addClass('hidden');
    $('#dispListTrkMenuMini').removeClass('hidden');
    $('#dispListTrkMenuMini').addClass('visible');

});

// Loads the next portion of filtered tracks
$(document).on('click', '.pagination a', function (e){  // "#tabDispLists_trks"
    e.preventDefault();
    $(".loading-div").show(); //show loading element
    var page = $(this).attr("data-page"); //get page number from link
    $("#tabDispLists_trks").load("services/fetch_lists.php",{"object":"trk","sqlFilterString":fetch_pages_filterString,"page":page}, function(){ //get content from PHP page
        $(".loading-div").hide(); //once done, hide loading element
    });
});

// Opens the edit track UI
$(document).on('click', '.uiTrackEditBtn', function (e) {
    e.preventDefault();                                                 // Prevent link behaviour
    SESSION_OBJ.currentFunction = "upd";
    var jsonObject = {};     

    // evaluate id of object to be deleted
    var $activeButtonA = $(this)                                    // Store the current link <a> element
    var idString = this.hash;                                       // Get div class of selected topic (e.g #panelLists)
    var trackId = idString.substring(9);                            // Extract id of item to be deleted                                                  

    phpLocation = "services/getObject.php";                     // Variable to store location of php file
    jsonObject.objectType = "trk";                                    // Item type = track
    jsonObject.requestType = 'get';                              // Request type = get track data
    jsonObject.objectId = trackId;
    jsn = JSON.stringify ( jsonObject );

    $.ajax({
        url: phpLocation,
        type: "POST",
        contentType: "application/json; charset=utf-8",
        dataType: "json",
        data: jsn
    })
    .done(function ( respObj ) {

        // Track object successfully stored in DB
        if ( respObj.status == 'OK') {
            
            // clear all error states
            clearAllErrorStates ();     
            
            trackObj = respObj.trackObj;
            $('#uiTrack_fld_trkId').val(trackObj.trkId); 
            $('#uiTrack_fld_trkTrackName').val(trackObj.trkTrackName);    
            $('#uiTrack_fld_trkRoute').val(trackObj.trkRoute);
            $('#uiTrack_fld_trkDateBegin').val(trackObj.trkDateBegin);
            //$('#uiTrack_fld_trkDateFinish').val(trackObj.trkDateFinish);
            $('#uiTrack_fld_trkType').val(trackObj.trkType);
            $('#uiTrack_fld_trkType').selectmenu("refresh");                  // refresh to display the value from the DB (without this the html default is shown)
            $('#uiTrack_fld_trkSubType').val(trackObj.trkSubType);
            $('#uiTrack_fld_trkSubType').selectmenu("refresh");
            $('#uiTrack_fld_trkOrg').val(trackObj.trkOrg);
            //$('#uiTrack_fld_trkOvernightLoc').val(trackObj.trkOvernightLoc);      // field removed from DB
            //$('#uiTrack_fld_trkParticipants').val(trackObj.trkParticipants);      // field removed from DB
            $('#uiTrack_fld_trkEvent').val(trackObj.trkEvent);
            $('#uiTrack_fld_trkRemarks').val(trackObj.trkRemarks);
            $('#uiTrack_fld_trkDistance').val(trackObj.trkDistance);
            $('#uiTrack_fld_trkTimeOverall').val(trackObj.trkTimeOverall);
            $('#uiTrack_fld_trkTimeToPeak').val(trackObj.trkTimeToPeak);
            $('#uiTrack_fld_trkTimeToFinish').val(trackObj.trkTimeToFinish);
            $('#uiTrack_fld_trkGrade').val(trackObj.trkGrade);
            $('#uiTrack_fld_trkMeterUp').val(trackObj.trkMeterUp);
            $('#uiTrack_fld_trkMeterDown').val(trackObj.trkMeterDown);
            $('#uiTrack_fld_trkCountry').val(trackObj.trkCountry);

            // hidden fields
            $('#uiTrack_fld_trkStartEle').val(trackObj.trkStartEle);                        
            $('#uiTrack_fld_trkPeakEle').val(trackObj.trkPeakEle);                          
            $('#uiTrack_fld_trkPeakTime').val(trackObj.trkPeakTime);                        
            $('#uiTrack_fld_trkLowEle').val(trackObj.trkLowEle);                            
            $('#uiTrack_fld_trkLowTime').val(trackObj.trkLowTime);                          
            $('#uiTrack_fld_trkFinishEle').val(trackObj.trkFinishEle);                      
            $('#uiTrack_fld_trkFinishTime').val(trackObj.trkFinishTime);  
            $('#uiTrack_fld_trkCoordTop').val(trackObj.trkCoordTop);
            $('#uiTrack_fld_trkCoordBottom').val(trackObj.trkCoordBottom);
            $('#uiTrack_fld_trkCoordLeft').val(trackObj.trkCoordLeft);
            $('#uiTrack_fld_trkCoordRight').val(trackObj.trkCoordRight);
    
            TRACK_WAYP_ARRAY = respObj.trackWaypArray;
            var itemsTable = drawItemsTables ( TRACK_WAYP_ARRAY, "peak", "uiTrack" )
            document.getElementById("uiTrack_peakList").innerHTML = itemsTable;

            var itemsTable = drawItemsTables ( TRACK_WAYP_ARRAY, "wayp", "uiTrack" )
            document.getElementById("uiTrack_waypList").innerHTML = itemsTable;

            var itemsTable = drawItemsTables ( TRACK_WAYP_ARRAY, "loca", "uiTrack" )
            document.getElementById("uiTrack_locaList").innerHTML = itemsTable;

            TRACK_PART_ARRAY = respObj.trackPartArray;
            var itemsTable = drawItemsTables ( TRACK_PART_ARRAY, "part", "uiTrack" )
            document.getElementById("uiTrack_partList").innerHTML = itemsTable;

            $('#uiTrack').addClass('active');

        } else {
            $('#statusMessage').text(respObj.message);
            $('#statusMessage').show().delay(5000).fadeOut();
        }
    });
});

// Delete track
$(document).on('click', '.trkDel', function (e) {
    console.log("Delete track");
})

// ==========================================================================
// ========================== panelImport ===================================
// ==========================================================================

// Change to selected panel (IS THIS FUNCTION REALLY REQUIRED???)
$(document).on('click', '.uiTrack_btns_a', function(e) {                  
    e.preventDefault();                                             // Prevent link behaviour
    
    var $activeButtonA = $(this)                                    // Store the current link <a> element
    var buttonId = this.hash;                                       // Get div class of selected topic (e.g #panelLists)
    
    // Run following block if selected topic is currently not active
    if (buttonId && !$activeButtonA.is('.active')) {
        $actUpdTrkTab.removeClass('active');                        // Make current panel inactive
        $actUpdTrkBtn.removeClass('active');                        // Make current tab inactive
        
        $actUpdTrkTab = $(buttonId).addClass('active');             // Make new panel active
        $actUpdTrkBtn = $activeButtonA.parent().addClass('active'); // Make new tab active
    }
}); 

// Upload file to server and receive the extracted track data (calls importGps.php in temp mode)
$(document).on('click', '#buttonUploadFile', function (e) {
    e.preventDefault();                                                                                 
    var xhr = new XMLHttpRequest();                                            // create new xhr object
    //itemsTrkImp = [];                                              // array to store selected peaks, waypoints, locations and participants

    // Execute following code when JSON object is received from importGpsTmp.php - TEMP service
    xhr.onload = function() {
        if (xhr.status === 200) {                                              // when all OK
            respObj = JSON.parse(xhr.responseText);                     // transfer JSON into response object array
            if ( respObj.status == 'OK') {

                // clear all error states
                clearAllErrorStates ();     

                // assign returned track values to UI fields
                trackObj = respObj.trackObj;
                $('#uiTrack_fld_trkId').val(trackObj.trkId); 
                $('#uiTrack_fld_trkTrackName').val(trackObj.trkTrackName);    
                $('#uiTrack_fld_trkRoute').val(trackObj.trkRoute);
                $('#uiTrack_fld_trkDateBegin').val(trackObj.trkDateBegin);
                //$('#uiTrack_fld_trkDateFinish').val(trackObj.trkDateFinish);
                //$('#uiTrack_fld_trkSaison').val(trackObj.trkSaison);
                $('#uiTrack_fld_trkType').val(trackObj.trkType);
                $('#uiTrack_fld_trkSubType').val(trackObj.trkSubType);
                $('#uiTrack_fld_trkOrg').val(trackObj.trkOrg);
                // $('#uiTrack_fld_trkOvernightLoc').val(trackObj.trkOvernightLoc);     // field removed from DB
                // $('#uiTrack_fld_trkParticipants').val(trackObj.trkParticipants);     // field removed from DB
                $('#uiTrack_fld_trkEvent').val(trackObj.trkEvent);
                $('#uiTrack_fld_trkRemarks').val(trackObj.trkRemarks);
                $('#uiTrack_fld_trkDistance').val(trackObj.trkDistance);
                $('#uiTrack_fld_trkTimeOverall').val(trackObj.trkTimeOverall);
                $('#uiTrack_fld_trkTimeToPeak').val(trackObj.trkTimeToPeak);
                $('#uiTrack_fld_trkTimeToFinish').val(trackObj.trkTimeToFinish);
                $('#uiTrack_fld_trkGrade').val(trackObj.trkGrade);
                $('#uiTrack_fld_trkMeterUp').val(trackObj.trkMeterUp);
                $('#uiTrack_fld_trkMeterDown').val(trackObj.trkMeterDown);
                $('#uiTrack_fld_trkCountry').val(trackObj.trkCountry);
                $('#uiTrack_fld_trkCoordinates').val(trackObj.trkCoordinates);
                
                // not displayed fields
                $('#uiTrack_fld_trkStartEle').val(trackObj.trkStartEle);                        
                $('#uiTrack_fld_trkPeakEle').val(trackObj.trkPeakEle);                          
                $('#uiTrack_fld_trkPeakTime').val(trackObj.trkPeakTime);                        
                $('#uiTrack_fld_trkLowEle').val(trackObj.trkLowEle);                            
                $('#uiTrack_fld_trkLowTime').val(trackObj.trkLowTime);                          
                $('#uiTrack_fld_trkFinishEle').val(trackObj.trkFinishEle);
                $('#uiTrack_fld_trkFinishTime').val(trackObj.trkFinishTime);
                $('#uiTrack_fld_trkCoordTop').val(trackObj.trkCoordTop);
                $('#uiTrack_fld_trkCoordBottom').val(trackObj.trkCoordBottom);
                $('#uiTrack_fld_trkCoordLeft').val(trackObj.trkCoordLeft);
                $('#uiTrack_fld_trkCoordRight').val(trackObj.trkCoordRight);
                
                // Change current function to import
                SESSION_OBJ.currentFunction = "ins";

                // Close upload file div and open form to update track data
                $('#uiUplFileGps').removeClass('active');
                $('#uiTrack').addClass('active');
                document.getElementById("inputFile").value = "";
            } else {
                $('#statusMessage').text(respObj.message);
                $("#statusMessage").show().delay(5000).fadeOut();
                document.getElementById("inputFile").value = "";
            } 

        }
    }
    var fileName = document.getElementById('inputFile').files[0];               // assign selected file var
    if ( fileName ) {
        phpLocation = "services/importGps.php";                                 // Variable to store location of php file
        var formData = new FormData();                                          // create new formData object
        formData.append('sessionId', SESSION_OBJ.sessionId);                                // append parameter session ID
        formData.append('request', 'temp')                                      // temp request to create track temporarily
        formData.append('fileName', fileName);                                  // append parameter fileName
        formData.append('usrId', SESSION_OBJ.usrId);                               // append parameter file type
        xhr.open ('POST', phpLocation, true);                                   // open  XMLHttpRequest 
        xhr.send(formData);                                                     // send formData object to service using xhr
    } else {
        $('#statusMessage').text('No file selected');
        $("#statusMessage").show().delay(5000).fadeOut();
    }
});

// Fires when users clicks the load JSON file button --> JSON file is imported into DB (not public function)
$(document).on('click', '#buttonUploadFileJSON', function (e) {
    e.preventDefault();
    var xhr = new XMLHttpRequest();   
    var jsonObject = {};
        
    // Execute following code JSON object is received from importGpsTmp.php - TEMP service
    xhr.onload = function() {
        if (xhr.status === 200) {                                                                       // when all OK
            respObj = JSON.parse(xhr.responseText);                                              // transfer JSON into response object array
            if ( respObj.status == 'OK') {
            } else {
                $('#statusMessage').text(respObj.message);
                $("#statusMessage").show().delay(5000).fadeOut();
            }
        }
    } 
    var fileName = $('#inputFileJSON').val();
    phpLocation = "services/importGps.php";          // Variable to store location of php file
    jsonObject["sessionId"] = SESSION_OBJ.sessionId;                             // append parameter session ID
    jsonObject["request"] = 'json';                              // temp request to create track temporarily
    jsonObject["fileName"] = fileName;                              // send track object
    jsonObject["usrId"] = SESSION_OBJ.usrId;
    xhr.open ('POST', phpLocation, true);                           // open  XMLHttpRequest 
    xhr.setRequestHeader( "Content-Type", "application/json" );
    jsn = JSON.stringify(jsonObject);
    xhr.send( jsn );                                           // send formData object to service using xhr  
});

// Cancels update
$(document).on('click', '#uiTrack_fld_cancel', function (e) {
    e.preventDefault();
    $('#uiUplFileGps').addClass('active');                 // Make File upload div visible
    $('#uiTrack').removeClass('active');                   // hide update form
    $('#statusMessage').text('Import cancelled');
    $("#statusMessage").show().delay(5000).fadeOut();
});

// ==========================================================================
// ========================== panelExport ===================================
// ==========================================================================

// Export Tracks JSON file to export directory on server
$(document).on('click', '#mainButtons_exportBtnTracks01JSON', function (e) {
    e.preventDefault();
    var xhr = new XMLHttpRequest();   
    var jsonObject = {};
        
    // Execute following code JSON object is received from importGpsTmp.php - TEMP service
    xhr.onload = function() {
        if (xhr.status === 200) {                                               // when all OK
            respObj = JSON.parse(xhr.responseText);                      // transfer JSON into response object array
            $('#statusMessage').text(respObj.message);
            $("#statusMessage").show().delay(5000).fadeOut();
        } 
    } 

    phpLocation = "services/exportData.php";                                    // Variable to store location of php file
    jsonObject["request"] = 'tracks01_JSON';                                    // temp request to create track temporarily
    jsonObject["usrId"] = SESSION_OBJ.usrId;
    xhr.open ('POST', phpLocation, true);                                       // open  XMLHttpRequest 
    xhr.setRequestHeader( "Content-Type", "application/json" );
    jsn = JSON.stringify(jsonObject);
    xhr.send( jsn );                                                            // send formData object to service using xhr  
});

// Export Tracks CSV file to export directory on server
$(document).on('click', '#mainButtons_exportBtnTracks01CSV', function (e) {
    e.preventDefault();
    var xhr = new XMLHttpRequest();   
    var jsonObject = {};
        
    // Execute following code JSON object is received from importGpsTmp.php - TEMP service
    xhr.onload = function() {
        if (xhr.status === 200) {                                               // when all OK
            respObj = JSON.parse(xhr.responseText);                      // transfer JSON into response object array
            $('#statusMessage').text(respObj.message);
            $("#statusMessage").show().delay(5000).fadeOut();
        }
    } 

    phpLocation = "services/exportData.php";                                    // Variable to store location of php file
    jsonObject["request"] = 'tracks01_CSV';                                     // temp request to create track temporarily
    jsonObject["usrId"] = SESSION_OBJ.usrId;
    xhr.open ('POST', phpLocation, true);                                       // open  XMLHttpRequest 
    xhr.setRequestHeader( "Content-Type", "application/json" );
    jsn = JSON.stringify(jsonObject);
    xhr.send( jsn );                                                            // send formData object to service using xhr  
});

// ==========================================================================
// ========================== G E N E R I C =================================
// ==========================================================================

// Sends the track data to the server for update --> calls importGps.php in save mode)
$(document).on('click', '#uiTrack_fld_save', function ( e ) {
    e.preventDefault();
    var valid = true;                                                           // true when field check are passed
    var trackObj = {};
    var jsonObject = {};

    trackObj.trkId = $('#uiTrack_fld_trkId').val();                            // assign field value to track object

    $('#uiTrack_fld_trkTrackName').removeClass( "ui-state-error" );            // same as above
    valid = valid && checkExistance ( $('#uiTrack_fld_trkTrackName'), "Track Name" );
    valid = valid && checkRegexpNot ( $('#uiTrack_fld_trkTrackName'), /[&;,"']/, "No special characters [&;,\"\'] allowed. " );
    trackObj.trkTrackName = $('#uiTrack_fld_trkTrackName').val();
    
    $('#uiTrack_fld_trkRoute').removeClass( "ui-state-error" );                // same as above
    valid = valid && checkExistance ( $('#uiTrack_fld_trkRoute'), "Route" );
    valid = valid && checkRegexpNot ( $('#uiTrack_fld_trkRoute'), /[&;,"']/, "No special characters [&;,\"\'] allowed. " );
    trackObj.trkRoute = $('#uiTrack_fld_trkRoute').val();
    
    $('#uiTrack_fld_trkDateBegin').removeClass( "ui-state-error" );            // same as above
    valid = valid && checkExistance ( $('#uiTrack_fld_trkDateBegin'), "Date Begin" );
    trackObj.trkDateBegin = $('#uiTrack_fld_trkDateBegin').val();     

    //$('#uiTrack_fld_trkDateFinish').removeClass( "ui-state-error" );           // same as above
    //valid = valid && checkExistance ( $('#uiTrack_fld_trkDateFinish'), "Date Finish" );
    //trackObj.trkDateFinish = $('#uiTrack_fld_trkDateFinish').val();
    
    //trackObj.trkSaison = $('#uiTrack_fld_trkSaison').val();
    trackObj.trkType = $('#uiTrack_fld_trkType').val();
    trackObj.trkSubType = $('#uiTrack_fld_trkSubType').val();

    $('#uiTrack_fld_trkOrg').removeClass( "ui-state-error" );           // same as above
    trackObj.trkOrg = $('#uiTrack_fld_trkOrg').val();    
    valid = valid && checkRegexpNot ( $('#uiTrack_fld_trkOrg'), /[&;,"']/, "No special characters [&;,\"\'] allowed. " );
    
    //trackObj.trkOvernightLoc = $('#uiTrack_fld_trkOvernightLoc').val();
    //trackObj.trkParticipants = $('#uiTrack_fld_trkParticipants').val();
    
    $('#uiTrack_fld_trkEvent').removeClass( "ui-state-error" );           // same as above
    trackObj.trkEvent = $('#uiTrack_fld_trkEvent').val();
    valid = valid && checkRegexpNot ( $('#uiTrack_fld_trkEvent'), /[&;,"']/, "No special characters [&;,\"\'] allowed " );

    $('#uiTrack_fld_trkRemarks').removeClass( "ui-state-error" );           // same as above
    trackObj.trkRemarks = $('#uiTrack_fld_trkRemarks').val();
    valid = valid && checkRegexpNot ( $('#uiTrack_fld_trkRemarks'), /[&;,"']/, "No special characters [&;,\"\'] allowed " );

    $('#uiTrack_fld_trkDistance').removeClass( "ui-state-error" );           // same as above
    trackObj.trkDistance = $('#uiTrack_fld_trkDistance').val();
    valid = valid && checkRegexp ( $('#uiTrack_fld_trkDistance'), /^[0-9]{0,3}.[0-9]{0,3}$/, "Enter distance as mmm.nnn " );

    $('#uiTrack_fld_trkTimeOverall').removeClass( "ui-state-error" );                   // remove error state if previously set
    trackObj.trkTimeOverall = $('#uiTrack_fld_trkTimeOverall').val();
    valid = valid && checkRegexp ( $('#uiTrack_fld_trkTimeOverall'), /^[0-9]{0,1}[0-9]:[0-9]{0,1}[0-9]:[0-9]{0,1}[0-9]$/, "Enter time as HH:MM:SS " );
    
    $('#uiTrack_fld_trkTimeToPeak').removeClass( "ui-state-error" );                   // remove error state if previously set
    trackObj.trkTimeToPeak = $('#uiTrack_fld_trkTimeToPeak').val();
    valid = valid && checkRegexp ( $('#uiTrack_fld_trkTimeToPeak'), /^[0-9]{0,1}[0-9]:[0-9]{0,1}[0-9]:[0-9]{0,1}[0-9]$/, "Enter time as HH:MM:SS " );

    $('#uiTrack_fld_trkTimeToFinish').removeClass( "ui-state-error" );                   // remove error state if previously set
    trackObj.trkTimeToFinish = $('#uiTrack_fld_trkTimeToFinish').val();
    valid = valid && checkRegexp ( $('#uiTrack_fld_trkTimeToFinish'), /^[0-9]{0,1}[0-9]:[0-9]{0,1}[0-9]:[0-9]{0,1}[0-9]$/, "Enter time as HH:MM:SS " );

    trackObj.trkGrade = $('#uiTrack_fld_trkGrade').val();

    $('#uiTrack_fld_trkMeterUp').removeClass( "ui-state-error" );                   // remove error state if previously set
    trackObj.trkMeterUp = $('#uiTrack_fld_trkMeterUp').val();
    valid = valid && checkIfNum ( $('#uiTrack_fld_trkMeterUp'), "Enter valid number (mmmm.nnn)");
    valid = valid && checkRegexp ( $('#uiTrack_fld_trkMeterUp'), /^[0-9]{0,4}\.?[0-9]{0,3}$/, "Enter valid negative number (mmmm.nnn)" );

    $('#uiTrack_fld_trkMeterDown').removeClass( "ui-state-error" );                   // remove error state if previously set
    trackObj.trkMeterDown = $('#uiTrack_fld_trkMeterDown').val();
    valid = valid && checkIfNum ( $('#uiTrack_fld_trkMeterDown'), "Enter valid negative number (-mmmm.nnn)");
    valid = valid && ( checkRegexp ( $('#uiTrack_fld_trkMeterDown'), /^.[0-9]{0,4}\.?[0-9]{0,3}$/, "Enter valid negative number (-mmmm.nnn)" ) );
    
    $('#uiTrack_fld_trkCountry').removeClass( "ui-state-error" );                   // remove error state if previously set
    country = $('#uiTrack_fld_trkCountry').val();
    trackObj.trkCountry = country.toUpperCase();
    valid = valid && checkRegexp ( $('#uiTrack_fld_trkCountry'), /^[A-Za-z]{2}$/, "Enter valid country code" );   
    
    // not displayed fields
    trackObj.trkUsrId= SESSION_OBJ.usrId;    
    trackObj.trkCoordinates = $('#uiTrack_fld_trkCoordinates').val();  
    trackObj.trkStartEle = $('#uiTrack_fld_trkStartEle').val();                        
    trackObj.trkPeakEle = $('#uiTrack_fld_trkPeakEle').val();                          
    trackObj.trkPeakTime = $('#uiTrack_fld_trkPeakTime').val();                        
    trackObj.trkLowEle = $('#uiTrack_fld_trkLowEle').val();                            
    trackObj.trkLowTime = $('#uiTrack_fld_trkLowTime').val();                          
    trackObj.trkFinishEle = $('#uiTrack_fld_trkFinishEle').val();                      
    trackObj.trkFinishTime = $('#uiTrack_fld_trkFinishTime').val();  
    trackObj.trkCoordTop = $('#uiTrack_fld_trkCoordTop').val();
    trackObj.trkCoordBottom = $('#uiTrack_fld_trkCoordBottom').val();
    trackObj.trkCoordLeft = $('#uiTrack_fld_trkCoordLeft').val();
    trackObj.trkCoordRight = $('#uiTrack_fld_trkCoordRight').val();
    
    if ( valid ) {                                                      // all validation checks were successful
        phpLocation = "services/putObject.php";                                 // Variable to store location of php file
        jsonObject.usrId = SESSION_OBJ.usrId;
        jsonObject.objectType = 'trk';
        jsonObject.putObj = trackObj;                                         // send track object
        jsonObject.requestType = SESSION_OBJ.currentFunction;
        jsonObject.sessionId = SESSION_OBJ.sessionId;                                       // append parameter session ID
        jsonObject.trackPartArray = TRACK_PART_ARRAY;              // array containing participants associated to track
        jsonObject.trackWaypArray = TRACK_WAYP_ARRAY;              // array containing waypoints associated to track
                        
        jsn = JSON.stringify ( jsonObject );

        // Perform ajax call to php to save trackObject in table Tracks and other tables
        // (JQUERY was necessary because I did not success to send two dimensional array otherwise)
        $.ajax({
            url: phpLocation,
            type: "POST",
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            data: jsn
        })
        .done(function ( respObj ) {

            // Track object successfully stored in DB
            if ( respObj.status == 'OK' ) {

                $('#statusMessage').text(respObj.message);
                $('#statusMessage').show().delay(5000).fadeOut();
                
                // Purge TRACK_PART_ARRAY and TRACK_WAYP_ARRAY (array and UI)
                TRACK_WAYP_ARRAY = new Array();
                var itemsTable = drawItemsTables ( TRACK_WAYP_ARRAY, "peak", "uiTrack" )
                document.getElementById("uiTrack_peakList").innerHTML = itemsTable;
    
                var itemsTable = drawItemsTables ( TRACK_WAYP_ARRAY, "wayp", "uiTrack" )
                document.getElementById("uiTrack_waypList").innerHTML = itemsTable;
    
                var itemsTable = drawItemsTables ( TRACK_WAYP_ARRAY, "loca", "uiTrack" )
                document.getElementById("uiTrack_locaList").innerHTML = itemsTable;
    
                TRACK_PART_ARRAY = new Array();
                var itemsTable = drawItemsTables ( TRACK_PART_ARRAY, "part", "uiTrack" )
                document.getElementById("uiTrack_partList").innerHTML = itemsTable;
    
                // ------------------------------
                // Gen KML for imported File
                var xhr = new XMLHttpRequest();

                // gen_kml.php has generated kml file and returns to js
                // Draw map with imported track in center
                xhr.onload = function() {
                    if (xhr.status === 200) {  
                        respObj = JSON.parse(xhr.responseText);                      // transfer JSON into response object array
                        
                        if ( respObj["status"] == "OK") {

                            // delete current map
                            var element = document.getElementById('displayMap-ResMap');
                            var parent = element.parentNode
                            parent.removeChild(element);
                            parent.innerHTML = '<div id="displayMap-ResMap"></div>';

                            var coordTop = Number(respObj.coordTop);
                            var coordBottom = Number(respObj.coordBottom);
                            var coordLeft = Number(respObj.coordLeft);
                            var coordRight = Number(respObj.coordRight);
                            
                            // Evluate coord center (if route is outside CH - show empty CH map)
                            var coordCenterY = ( coordTop + coordBottom ) / 2;
                            var coordCenterX = ( coordRight + coordLeft ) / 2;
                            
                            // Calculate required resolution
                            resolution1 = ( coordTop - coordBottom ) / 200;
                            resolution2 = ( coordRight - coordLeft ) / 200;
                            if ( resolution1 > resolution2 ) {
                                resolution = Math.min(500,resolution1);
                            } else {
                                resolution = Math.min(500,resolution2);
                            }

                            // Draw empty map & center to provided coordinate
                            var tourdbMap = new ga.Map({
                                target: 'displayMap-ResMap',
                                view: new ol.View({resolution: resolution, center: [coordCenterX, coordCenterY]})
                            });
                            mapSTlayer_grau = ga.layer.create('ch.swisstopo.pixelkarte-grau');
                            tourdbMap.addLayer(mapSTlayer_grau);                              // add map layer to map
                            
                            // Draw kml file for tracks 
                            if ( genTrackKml ) {                                            // var is true when user has set filter on tracks
                                $trackFile = TOURDBURL + "/tmp/kml_disp/" + SESSION_OBJ.sessionId + "/tracks.kml";
                            
                                // Create the KML Layer for tracks
                                kmlLayer = new ol.layer.Vector({                       // create new vector layer for tracks
                                    source: new ol.source.Vector({                          // Set source to kml file
                                        url: $trackFile,
                                        format: new ol.format.KML({
                                            projection: 'EPSG:21781'
                                        })
                                    })
                                });
                                tourdbMap.addLayer(kmlLayer);                                // add track layer to map
                            }
                            
                            // Popup showing the position the user clicked
                            var popup = new ol.Overlay({                                    // popup to display track details
                                element: $('<div title="KML"></div>')[0]
                            });
                            tourdbMap.addOverlay(popup);
            
                            // On click we display the feature informations (code basis from map admin sample library)
                            tourdbMap.on('singleclick', function(evt) {
                                var pixel = evt.pixel;
                                var coordinate = evt.coordinate;
                                var feature = tourdbMap.forEachFeatureAtPixel(pixel, function(feature, layer) {
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
            
                            // Change cursor style when cursor is hover over a feature
                            tourdbMap.on('pointermove', function(evt) {
                                var feature = tourdbMap.forEachFeatureAtPixel(evt.pixel, function(feature, layer) {
                                    return feature;
                                });
                                tourdbMap.getTargetElement().style.cursor = feature ? 'pointer' : '';
                            });
                        }
                    }
                };     

                // Create where statement (to be changed --> trkId to be returned after save)
                sqlWhereCurrent = "WHERE trkId=" + respObj["trkId"];
                genTrackKml = true;
                
                // send required parameters to gen_kml.php
                var jsonObject = {};
                phpLocation = "services/gen_kml.php";                                   // Variable to store location of php file
                jsonObject["sessionId"] = SESSION_OBJ.sessionId;                                    // send session ID
                jsonObject["sqlWhere"] = sqlWhereCurrent;                          // send where statement for tracks
                jsonObject["objectName"] = "tracks";                                // send where statement for segments 
                xhr.open ('POST', phpLocation, true);                                   // open  XMLHttpRequest 
                xhr.setRequestHeader( "Content-Type", "application/json" );
                jsn = JSON.stringify(jsonObject);
                xhr.send( jsn );                                                        // send formData object to service using xhr   
      
                // Load first set of tracks to be displayed in the List panel
                // ----------------------------------------------------------
                var page = 1;
                fetch_pages_filterString = " trkUsrId= '" + SESSION_OBJ.usrId + "'";      // where string for list view (fetch_lists.php)
                $("#tabDispLists_trks").load("services/fetch_lists.php",
                    {"sqlFilterString":fetch_pages_filterString,"page":page}); //get content from PHP page    

                // empty items array and redraw empty items array
                // itemsTrkImp = new Array();
                // drawItemsTables_old ( itemsTrkImp, "peak" ); 
                // drawItemsTables_old ( itemsTrkImp, "wayp" ); 
                // drawItemsTables_old ( itemsTrkImp, "loca" ); 
                // drawItemsTables_old ( itemsTrkImp, "part" ); 

                // $( "#uiTrack_peakSrch" ).val("");
                // $( "#uiTrack_waypSrch" ).val("");
                // $( "#uiTrack_locaSrch" ).val("");
                // $( "#uiTrack_partSrch" ).val("");

                // Open Panel Display
                var $activeButtonA = $('#navBtns_btn_diplay_a');                // Store the current link <a> element
                buttonId = $activeButtonA.attr('href'); 

                // Run following block if selected topic is currently not active
                $topicButton.removeClass('active');                             // Make current panel inactive
                $activeButton.removeClass('active');                            // Make current tab inactive
                $topicButton = $(buttonId).addClass('active');                  // Make new panel active
                $activeButton = $activeButtonA.parent().addClass('active');     // Make new tab active
                
                // Close upload file div and open form to update track data
                $('#uiUplFileGps').addClass('active');
                $('#uiTrack').removeClass('active');

                // Change active tab to main tab
                $( "#uiTrack" ).tabs({
                    active: 0
                  });
            } else {
                // Track and / related tables could not be correctly inserted
                // Task?: Make panelImport disappear and panelLists appear
                $('#statusMessage').text(respObj.message);
                $('#statusMessage').show().delay(5000).fadeOut();
            }
        });
    }
});

// Cancels the edit process and returns to the list view
$(document).on('click', '#uiTrack_fld_cancel', function (e) {
    e.preventDefault();
    //$('#uiUplFileGps').addClass('active');                 // Make File upload div visible
    
    // Purge TRACK_PART_ARRAY and TRACK_WAYP_ARRAY (array and UI)
    TRACK_WAYP_ARRAY = new Array();
    var itemsTable = drawItemsTables ( TRACK_WAYP_ARRAY, "peak", "uiTrack" )
    document.getElementById("uiTrack_peakList").innerHTML = itemsTable;

    var itemsTable = drawItemsTables ( TRACK_WAYP_ARRAY, "wayp", "uiTrack" )
    document.getElementById("uiTrack_waypList").innerHTML = itemsTable;

    var itemsTable = drawItemsTables ( TRACK_WAYP_ARRAY, "loca", "uiTrack" )
    document.getElementById("uiTrack_locaList").innerHTML = itemsTable;

    TRACK_PART_ARRAY = new Array();
    var itemsTable = drawItemsTables ( TRACK_PART_ARRAY, "part", "uiTrack" )
    document.getElementById("uiTrack_partList").innerHTML = itemsTable;

    $('#uiTrack').removeClass('active');                   // hide update form
    $('#statusMessage').text('Edit Track cancelled');
    $("#statusMessage").show().delay(5000).fadeOut();
    $( "#uiTrack" ).tabs({
        active: 0
      });
});

// Deletes items (waypoints, participants, etc.) in the track UI
$(document).on('click', '.itemDel', function (e) {
    console.info("clicked on del")
    e.preventDefault();                                                         // Prevent link behaviour
    var $activeButtonA = $(this)                                                // Store the current link <a> element
    var itemDelId = this.hash;                                                  // Get div class of selected topic (e.g #panelLists)
    var itemType = itemDelId.substring(1,5);                                    // Get type of item to delete
    var itemId = itemDelId.substring(9);                                        // Extract id of item to be deleted

    // Loop through items array and set display flag to false --> these records will not be saved/shown
    // peaks
    for (var i = 0; i < TRACK_WAYP_ARRAY.length; i++) {
        if ( TRACK_WAYP_ARRAY[i]["itemId"] == itemId && TRACK_WAYP_ARRAY[i]["itemType"] == itemType ) {
            TRACK_WAYP_ARRAY[i]["disp_f"] = 0;
        }    
    }
    var itemsTable = drawItemsTables ( TRACK_WAYP_ARRAY, "peak", "uiTrack" )
    document.getElementById("uiTrack_peakList").innerHTML = itemsTable;

    // wayp
    for (var i = 0; i < TRACK_WAYP_ARRAY.length; i++) {
        if ( TRACK_WAYP_ARRAY[i]["itemId"] == itemId && TRACK_WAYP_ARRAY[i]["itemType"] == itemType ) {
            TRACK_WAYP_ARRAY[i]["disp_f"] = 0;
        }    
    }
    var itemsTable = drawItemsTables ( TRACK_WAYP_ARRAY, "wayp", "uiTrack" )
    document.getElementById("uiTrack_waypList").innerHTML = itemsTable;

    // loca
    for (var i = 0; i < TRACK_WAYP_ARRAY.length; i++) {
        if ( TRACK_WAYP_ARRAY[i]["itemId"] == itemId && TRACK_WAYP_ARRAY[i]["itemType"] == itemType ) {
            TRACK_WAYP_ARRAY[i]["disp_f"] = 0;
        }    
    }
    var itemsTable = drawItemsTables ( TRACK_WAYP_ARRAY, "loca", "uiTrack" )
    document.getElementById("uiTrack_locaList").innerHTML = itemsTable;

    // part
    for (var i = 0; i < TRACK_PART_ARRAY.length; i++) {
        if ( TRACK_PART_ARRAY[i]["itemId"] == itemId && TRACK_PART_ARRAY[i]["itemType"] == itemType ) {
            TRACK_PART_ARRAY[i]["disp_f"] = 0;
        }    
    }
    var itemsTable = drawItemsTables ( TRACK_PART_ARRAY, "part", "uiTrack" )
    document.getElementById("uiTrack_partList").innerHTML = itemsTable;
});

// Changes the status of the reached checkbox
$(document).on('click', '.cbReached', function (e) {
    console.info("checkbox ticked")
    e.preventDefault();                                                         // Prevent link behaviour
    var $activeCb = $(this)                                                     // Store the current link <a> element
    //var itemChecked = $activeCb.is(":checked");                                 // Store state of checkbox
    if ( $activeCb.is(":checked") == true ) {
        itemChecked = 1;
    } else {
        itemChecked = 0;
    }
    cbId = $activeCb.attr("id");                                                // Read id of checked checkbox
    var itemType = cbId.substring(3,7);                                         // Extract item type
    var itemId = cbId.substring(7);                                             // Extract item id

    // Loop through items array and set reached flag to false --> these records will not be saved/shown
    for (var i = 0; i < TRACK_WAYP_ARRAY.length; i++) {
        if ( TRACK_WAYP_ARRAY[i]["itemId"] == itemId && TRACK_WAYP_ARRAY[i]["itemType"] == itemType ) {
            TRACK_WAYP_ARRAY[i]["reached_f"] = itemChecked;
        }    
    }
    itemsTable = drawItemsTables ( TRACK_WAYP_ARRAY, "peak", "uiTrack" );                           // WAS OLD
    document.getElementById("uiTrack_peakList").innerHTML = itemsTable;
});

// =============================================
// ============ F U N C T I O N S ==============
// =============================================

// Initialise all JQuery items 
function initJqueryItems () {
    $( "#dispObjAccordion" ).accordion({
        heightStyle: "content",                                      // hight of section dependent on content of section
        autoHeight: false,
        collapsible: true
    });
    $( "#dispFilTrk_dateFrom" ).datepicker({                         // Initalise field to select start date as JQUERY datepicker
        dateFormat: 'yy-mm-dd', 
        changeMonth: true,
        changeYear: true,
        showOn: "button",
        buttonImage: "css/images/calendar.gif",
        buttonImageOnly: true,
        buttonText: "Select date"
    });
    $( "#dispFilTrk_dateTo" ).datepicker({                           // Initalise field to select to date as JQUERY datepicker
        dateFormat: 'yy-mm-dd',
        changeMonth: true,
        changeYear: true,
        showOn: "button",
        buttonImage: "css/images/calendar.gif",
        buttonImageOnly: true,
        buttonText: "Select date"
    });
    $( "#dispFilTrk_type" ).selectable({});                          // Initialse field 'type' as JQUERY selectable
    $( "#dispFilTrk_subtype" ).selectable({});                       // Initialse field 'subtype' as JQUERY selectable
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
    $( "#dispFilSeg_segTypeFID" ).selectable({});
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
    $( "#dispFilSeg_startLocType" ).selectable({});
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
    $( "#dispFilSeg_targetLocType" ).selectable({});
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

    // Initialse all jquery functional fields for the list display for tracks
    // ======================================================================
    $( "#dispListTrkAccordion" ).accordion({
        heightStyle: "content",                                      // hight of section dependent on content of section
        autoHeight: false,
        collapsible: true
    });
    $( "#dispListTrk_dateFrom" ).datepicker({                         // Initalise field to select start date as JQUERY datepicker
        dateFormat: 'yy-mm-dd', 
        changeMonth: true,
        changeYear: true,
        showOn: "button",
        buttonImage: "css/images/calendar.gif",
        buttonImageOnly: true,
        buttonText: "Select date"
    });
    $( "#dispListTrk_dateTo" ).datepicker({                           // Initalise field to select to date as JQUERY datepicker
        dateFormat: 'yy-mm-dd',
        changeMonth: true,
        changeYear: true,
        showOn: "button",
        buttonImage: "css/images/calendar.gif",
        buttonImageOnly: true,
        buttonText: "Select date"
    });
    $( "#dispListTrk_type" ).selectable({});                          // Initialse field 'type' as JQUERY selectable
    $( "#dispListTrk_subtype" ).selectable({});                       // Initialse field 'subtype' as JQUERY selectable   

    // Initialse all jquery functional fields for the import track mask
    // ================================================================
    $( "#uiTrack" ).tabs();                                         // Tabs in UI Track mask
    valComments = $( "#validateComments" );
    $( "#uiTrack_fld_trkDateBegin" ).datepicker({                   // Initalise field to select start date as JQUERY datepicker
        dateFormat: 'yy-mm-dd', 
        changeMonth: true,
        changeYear: true,
        showOn: "button",
        buttonImage: "css/images/calendar.gif",
        buttonImageOnly: true,
        buttonText: "Select date"
    });
    /*
    $( "#uiTrack_fld_trkDateFinish" ).datepicker({                  // Initalise field to select start date as JQUERY datepicker
        dateFormat: 'yy-mm-dd', 
        changeMonth: true,
        changeYear: true,
        showOn: "button",
        buttonImage: "css/images/calendar.gif",
        buttonImageOnly: true,
        buttonText: "Select date"
    });
    */
    //$( "#uiTrack_fld_trkSaison" ).selectmenu();
    $( "#uiTrack_fld_trkType" ).selectmenu();
    $( "#uiTrack_fld_trkSubType" ).selectmenu();
    $( "#uiTrack_fld_trkGrade" ).autocomplete({
        source: "services/autoComplete.php?field=grades",
        minLength: 1,
        select: function( event, ui ) {                              // see above
            $( "" ).val( ui.item.id );
            $( "#uiTrack_fld_trkGrade" ).val( ui.item.id );
        },
        change: function( event, ui ) {
            console.log("Grade Autoselect field changed");
        }
    });
    $( "#uiTrack_peakSrch" ).autocomplete({
        source: "services/autoComplete.php?field=peak",
        minLength: 2,
        select: function( event, ui ) {                              // function fires on select of element   
            $( "" ).val( ui.item.id );                               // Set search field = found content
            id = ui.item.id;                                         // id = id of found item
            value = ui.item.value;                                   // value = name of found item

            // Initialise peakList array
            var itemsList =  new Object();                                  

            // Add new peak to array
            itemsList.itemId = id;                                   // id of item selected
            itemsList.itemName = value;                              // Name of item selected
            itemsList.itemType = "peak";                             // Type of item (must be 4 char)
            itemsList.disp_f = 1;                                 // Set display to true (if false --> item is not shown)
            itemsList.reached_f = 1;                              // Set reached flag to true as default

            TRACK_WAYP_ARRAY.push(itemsList);                              // Push record to array
            var itemsTable = drawItemsTables ( TRACK_WAYP_ARRAY, "peak", "uiTrack" )
            document.getElementById("uiTrack_peakList").innerHTML = itemsTable;
            // not working: $('#uiTrack_peakSrch').val("");                        // clear autocomplete source field

        }
    });
    $( "#uiTrack_waypSrch" ).autocomplete({
        source: "services/autoComplete.php?field=wayp",
        minLength: 2,
        select: function( event, ui ) {                              // see above
            $( "" ).val( ui.item.id );
            id = ui.item.id;
            value = ui.item.value;

            // Initialise peakList array
            var itemsList =  new Object();

            // Add new wayp to array
            itemsList.itemId = id;                                   // id of item selected
            itemsList.itemName = value;                              // Name of item selected
            itemsList.itemType = "wayp";                             // Type of item (must be 4 char)
            itemsList.disp_f = 1;                                 // Set display to true (if false --> item is not shown)
            itemsList.reached_f = 1;                              // Set reached flag to true as default

            TRACK_WAYP_ARRAY.push(itemsList);

            drawItemsTables ( TRACK_WAYP_ARRAY, "wayp", "uiTrack" );            // WAS OLD
        }
    });
    $( "#uiTrack_locaSrch" ).autocomplete({
        source: "services/autoComplete.php?field=loca",
        minLength: 2,
        select: function( event, ui ) {                              // see above
            $( "" ).val( ui.item.id );
            id = ui.item.id;
            value = ui.item.value;
        
            // Initialise peakList array
            var itemsList =  new Object();

            // Add new loc to array
            itemsList.itemId = id;                                   // id of item selected
            itemsList.itemName = value;                              // Name of item selected
            itemsList.itemType = "loca";                             // Type of item (must be 4 char)
            itemsList.disp_f = 1;                                 // Set display to true (if false --> item is not shown)
            itemsList.reached_f = 1;                              // Set reached flag to true as default --> not stored

            TRACK_WAYP_ARRAY.push(itemsList);

            drawItemsTables ( TRACK_PART_ARRAY, "loca", "uiTrack" );        //WAS OLD
        }
    });
    $( "#uiTrack_partSrch" ).autocomplete({
        source: "services/autoComplete.php?field=part",
        minLength: 2,
        select: function( event, ui ) {                              // see above
            $( "" ).val( ui.item.id );
            id = ui.item.id;
            value = ui.item.value;
        
            // Initialise peakList array
            var itemsList =  new Object();

            // Add new part to array
            itemsList.disp_f = 1;                                 // Set display to true (if false --> item is not shown)
            itemsList.itemType = "part";                             // Type of item (must be 4 char)
            itemsList.itemId = id;                                   // id of item selected
            itemsList.itemName = value;                              // Name of item selected
            itemsList.reached_f = 1;                              // Set reached flag to true as default --> not stored

            TRACK_WAYP_ARRAY.push(itemsList);                              // Add selected item to array
            drawItemsTables ( TRACK_PART_ARRAY, "part", "uiTrack" );       // WAS OLD
        }
    });
    
    // =====================================
    // ====== Display List 
    $( "#tabDispLists" ).tabs();                                         // Tabs in UI Track mask
    // form to edit tracks
    $( "#uiTrack" ).tabs();                                         // Tabs in UI Track mask
    valComments = $( "#validateComments" );
    $( "#uiTrack_fld_trkDateBegin" ).datepicker({                   // Initalise field to select start date as JQUERY datepicker
        dateFormat: 'yy-mm-dd', 
        changeMonth: true,
        changeYear: true,
        showOn: "button",
        buttonImage: "css/images/calendar.gif",
        buttonImageOnly: true,
        buttonText: "Select date"
    });
    /*
    $( "#uiTrack_fld_trkDateFinish" ).datepicker({                  // Initalise field to select start date as JQUERY datepicker
        dateFormat: 'yy-mm-dd', 
        changeMonth: true,
        changeYear: true,
        showOn: "button",
        buttonImage: "css/images/calendar.gif",
        buttonImageOnly: true,
        buttonText: "Select date"
    });
    */
    //$( "#uiTrack_fld_trkSaison" ).selectmenu();
    $( "#uiTrack_fld_trkType" ).selectmenu();
    $( "#uiTrack_fld_trkSubType" ).selectmenu();
    $( "#uiTrack_peakSrch" ).autocomplete({
        source: "services/autoComplete.php?field=peak",
        minLength: 2,
        select: function( event, ui ) {                              // function fires on select of element   
            $( "" ).val( ui.item.id );                               // Set search field = found content
            id = ui.item.id;                                         // id = id of found item
            value = ui.item.value;                                   // value = name of found item

            // Initialise peakList array
            var itemsList =  new Object();                                  

            // Add new peak to array
            itemsList.itemId = id;                                   // id of item selected
            itemsList.itemName = value;                              // Name of item selected
            itemsList.itemType = "peak";                             // Type of item (must be 4 char)
            itemsList.disp_f = 1;                                 // Set display to true (if false --> item is not shown)
            itemsList.reached_f = 1;                              // Set reached flag to true as default

            TRACK_WAYP_ARRAY.push(itemsList);                              // Push record to array
            var itemsTable = drawItemsTables ( TRACK_WAYP_ARRAY, "peak", "uiTrack" )
            document.getElementById("uiTrack_peakList").innerHTML = itemsTable;
            // not working:  $('#uiTrack_peakSrch').val("");                        // clear autocomplete source field
        }
    });
    $( "#uiTrack_waypSrch" ).autocomplete({
        source: "services/autoComplete.php?field=wayp",
        minLength: 2,
        select: function( event, ui ) {                              // see above
            $( "" ).val( ui.item.id );
            id = ui.item.id;
            value = ui.item.value;

            // Initialise peakList array
            var itemsList =  new Object();

            // Add new wayp to array
            itemsList.itemId = id;                                   // id of item selected
            itemsList.itemName = value;                              // Name of item selected
            itemsList.itemType = "wayp";                             // Type of item (must be 4 char)
            itemsList.disp_f = 1;                                 // Set display to true (if false --> item is not shown)
            itemsList.reached_f = 1;                              // Set reached flag to true as default

            TRACK_WAYP_ARRAY.push(itemsList);
            var itemsTable = drawItemsTables ( TRACK_WAYP_ARRAY, "wayp", "uiTrack" )
            document.getElementById("uiTrack_waypList").innerHTML = itemsTable;
        }
    });
    $( "#uiTrack_locaSrch" ).autocomplete({
        source: "services/autoComplete.php?field=loca",
        minLength: 2,
        select: function( event, ui ) {                              // see above
            $( "" ).val( ui.item.id );
            id = ui.item.id;
            value = ui.item.value;
        
            // Initialise peakList array
            var itemsList =  new Object();

            // Add new loc to array
            itemsList.itemId = id;                                   // id of item selected
            itemsList.itemName = value;                              // Name of item selected
            itemsList.itemType = "loca";                             // Type of item (must be 4 char)
            itemsList.disp_f = 1;                                 // Set display to true (if false --> item is not shown)
            itemsList.reached_f = 1;                              // Set reached flag to true as default --> not stored

            TRACK_WAYP_ARRAY.push(itemsList);
            var itemsTable = drawItemsTables ( TRACK_WAYP_ARRAY, "loca", "uiTrack" )
            document.getElementById("uiTrack_locaList").innerHTML = itemsTable;
        }
    });
    $( "#uiTrack_partSrch" ).autocomplete({
        source: "services/autoComplete.php?field=part",
        minLength: 2,
        select: function( event, ui ) {                              // see above
            $( "" ).val( ui.item.id );
            id = ui.item.id;
            value = ui.item.value;
        
            // Initialise peakList array
            var itemsList =  new Object();

            // Add new part to array
            itemsList.disp_f = 1;                                 // Set display to true (if false --> item is not shown)
            itemsList.itemType = "part";                             // Type of item (must be 4 char)
            itemsList.itemId = id;                                   // id of item selected
            itemsList.itemName = value;                              // Name of item selected
            
            TRACK_PART_ARRAY.push(itemsList);                              // Push record to array
            var itemsTable = drawItemsTables ( TRACK_PART_ARRAY, "part", "uiTrack" )
            document.getElementById("uiTrack_partList").innerHTML = itemsTable;
        }
    });
}

// Creates the where statement required to select the appropriate tracks 
function createTrkKmlWhere () {
    
    // ********************************************************************************************
    // Build SQL WHERE statement for tracks

    // Initialise tracks variables
    var whereStatement = [];                                        // Initialise array for whereStatement
    var whereString = "";                                           // Initialise var for where string
   
    // Field trackID from / to
    var trackIdFrom = "";                                           // Initialse var for track id from 
    var trackIdTo = "";                                             // Initialse var for track id to
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
        whereString = "trkDateBegin BETWEEN '" + fromDate + "' AND '" + toDate + "'";           // complete WHERE BETWEEN statement
    } else if ( fromDate != "" ) {
        whereString = "trkDateBegin BETWEEN '" + fromDate + "' AND '" + toDateArt + "'";        // complete WHERE BETWEEN statement
    } else if ( toDate != "" ) {
        whereString = "trkDateBegin BETWEEN '" + fromDateArt + "' AND '" + toDate + "'";        // complete WHERE BETWEEN statement
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
        whereString = "trkType in (" + whereString + ")";                       // complete SELECT IN statement
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
        whereString = "trkSubType in (" + whereString + ")";                    // complete SELECT IN statement
        whereStatement.push( whereString );                                     // Add to where Statement array
    }           

    // Field participants
    /*
    var whereString = "";
    if ( ($('#dispFilTrk_participants').val()) != "" ) {
        whereString = "trkParticipants like '%" + $('#dispFilTrk_participants').val() + "%'";
        whereStatement.push( whereString );
    };
    */

    // Field country
    var whereString = "";
    if ( ($('#dispFilTrk_country').val()) != "" ) {
        whereString = "trkCountry like '%" + $('#dispFilTrk_country').val() + "%'";
        whereStatement.push( whereString );
    };
    
    // ========== Put all where statements together
    if ( whereStatement.length > 0 ) {
        var sqlWhereCurrent = "WHERE ";

        for (var i=0; i < whereStatement.length; i++) {
            sqlWhereCurrent += whereStatement[i];
            sqlWhereCurrent += " AND ";
        }
        sqlWhere = sqlWhereCurrent + " trkUsrId='" + SESSION_OBJ.usrId + "'";
    } else {
        sqlWhere = "";
    }
    return sqlWhere;
}

// Creates the where statement required to select the appropriate segments
function createSegKmlWhere () {
    // Initialize segments variables

    //var sqlWhereSegments = "";                                      // Initialise var for where string for segments
    var whereStatement = [];                                        // Initialise array for whereStatement
    var whereString = "";                                           // Initialise var for where string
    var sqlWhereCurrent = "";                                        // Initialise var for where string for tracks
    var selected = [];
    var sqlName;
    
    // Field segment type
    $('#dispFilSeg_segTypeFID .ui-selected').each(function() {
        var itemId = this.id;
        sqlName = "segTypeFID";
        var criteria = itemId.slice(sqlName.length+1,itemId.length);            // +1 to remove _
        selected.push( criteria );
    });
    if ( selected.length > 0 ) {
        whereString = sqlName + " in (";
        var i;
        for (i=0; i<selected.length; ++i) {
            whereString += "'" + selected[i] + "',";
        }
    whereString = whereString.slice(0,whereString.length-1) + ")"; 
    whereStatement.push( whereString );                                         // Add to where Statement array
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
        var criteria = itemId.slice(sqlName.length+1,itemId.length);            // +1 to remove _
        selected.push( criteria );
    });
    if ( selected.length > 0 ) {
        whereString = sqlName + " in (";
        var i;
        for (i=0; i<selected.length; ++i) {
            whereString += "'" + selected[i] + "',";
        }
    whereString = whereString.slice(0,whereString.length-1) + ")"; 
    whereStatement.push( whereString );                                         // Add to where Statement array
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
        var criteria = itemId.slice(sqlName.length+1,itemId.length);            // +1 to remove _
        selected.push( criteria );
    });
    if ( selected.length > 0 ) {
        whereString = sqlName + " in (";
        var i;
        for (i=0; i<selected.length; ++i) {
            whereString += "'" + selected[i] + "',";
        }
    whereString = whereString.slice(0,whereString.length-1) + ")"; 
    whereStatement.push( whereString );                                         // Add to where Statement array
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
        var criteria = itemId.slice(sqlName.length+1,itemId.length);            // +1 to remove _
        selected.push( criteria );
    });
    if ( selected.length > 0 ) {
        whereString = sqlName + " in (";
        var i;
        for (i=0; i<selected.length; ++i) {
            whereString += "'" + selected[i] + "',";
        }
        whereString = whereString.slice(0,whereString.length-1) + ")"; 
        whereStatement.push( whereString );                                         // Add to where Statement array
    }
    
    // Field climbGrade
    var selected = [];
    var sqlName;
    var whereString = "";    
    $('#dispFilSeg_climbGrade .ui-selected').each(function() {
        var itemId = this.id;
        sqlName = "climbGrade";
        var criteria = itemId.slice(sqlName.length+1,itemId.length);            // +1 to remove _
        selected.push( criteria );
    });
    if ( selected.length > 0 ) {
        whereString = sqlName + " in (";
        var i;
        for (i=0; i<selected.length; ++i) {
            whereString += "'" + selected[i] + "',";
        }
        whereString = whereString.slice(0,whereString.length-1) + ")"; 
        whereStatement.push( whereString );                                         // Add to where Statement array
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
            whereString = whereString.slice(0,whereString.length-1);            // remove last comma
            whereString = sqlName + " in (" + whereString + ")";                // complete SELECT IN statement
            whereStatement.push( whereString );                                 // Add to where Statement array
        };
    });
   
    // *******************************************
    // ========= Put all where statements together
    //
    if ( whereStatement.length > 0 ) {
        var sqlWhereCurrent = "WHERE ";

        for (var i=0; i < whereStatement.length; i++) {
            sqlWhereCurrent += whereStatement[i];
            sqlWhereCurrent += " AND ";
        }
        sqlWhereCurrent = sqlWhereCurrent.slice(0,sqlWhereCurrent.length-5);
        sqlWhere = sqlWhereCurrent; 
    } else {
        sqlWhere = "";
    }
    return sqlWhere;
}

// Function drawing empty map -- for documentation see: https://api3.geo.admin.ch/
function drawMapEmpty(targetDiv) {
    
    var tourdbMap = new ga.Map({
        target: targetDiv,
        view: new ol.View({resolution: 500, center: [660000, 190000]})
    });

    // Create a background layer
    mapSTlayer_grau = ga.layer.create('ch.swisstopo.pixelkarte-grau');
    tourdbMap.addLayer(mapSTlayer_grau);
    return tourdbMap;
}

// Draws map
function drawMap( targetDiv, resolution, coordCenterX, coordCenterY, kmlFiles ) {

    // parameters:
    // targetDiv: ID of target div
    // resolution: map resolution 
    // coordCenterX / Y: location of map center
    // kmlFiles: Array of KML files which need to be displayed

    // Delete previously drawn map

    // Delete previously drawn layers 
    tourdbMap.getLayers().forEach(function(el) {                      // Loop through all map layers and remove them
        tourdbMap.removeLayer(el);
    })
    mapSTlayer_grau = ga.layer.create('ch.swisstopo.pixelkarte-grau');
    tourdbMap.addLayer(mapSTlayer_grau);                              // add map layer to map
    
    // Draw empty map & center to provided coordinate
    var tourdbMap = new ga.Map({
        target: targetDiv,
        view: new ol.View({resolution: resolution, center: [coordCenterY, coordCenterX]})
    });

    // Create a background layer
    mapSTlayer_grau = ga.layer.create('ch.swisstopo.pixelkarte-grau');
    tourdbMap.addLayer(mapSTlayer_grau);
    
    // Draw kml file for each file delivered in kmlFiles array
    for (var i = 0; i < kmlFiles.length; i++) {

        kmlFile = kmlFiles[i];

        // Create the KML Layer for tracks
        layer = new ol.layer.Vector({                       // create new vector layer for tracks
            source: new ol.source.Vector({                          // Set source to kml file
                url: kmlFile,
                format: new ol.format.KML({
                    projection: 'EPSG:21781'
                })
            })
        });
        tourdbMap.addLayer(layer);                                // add track layer to map    
    } 
    /*
    // Popup showing the position the user clicked
    var popup = new ol.Overlay({                                    // popup to display track details
        element: $('<div title="KML"></div>')[0]
    });
    tourdbMap.addOverlay(popup);

    // On click we display the feature informations (code basis from map admin sample library)
    tourdbMap.on('singleclick', function(evt) {
        var pixel = evt.pixel;
        var coordinate = evt.coordinate;
        var feature = tourdbMap.forEachFeatureAtPixel(pixel, function(feature, layer) {
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

    // Change cursor style when cursor is hover over a feature
    tourdbMap.on('pointermove', function(evt) {
        var feature = tourdbMap.forEachFeatureAtPixel(evt.pixel, function(feature, layer) {
            return feature;
        });
        tourdbMap.getTargetElement().style.cursor = feature ? 'pointer' : '';
    });
    */
}

// Draws the table that list the selected waypoints
function drawItemsTables ( itemsArray, itemType, elDelClass ) {

    // Parameters:
    //    itemsArray: Array of following content
    //                - disp_f: {1|0|true|false}
    //                - itemId: num (id of item)
    //                - itemName: string (name of item at display)
    //                - reached_f: [1=true|0=false]
    //    itemType: Item which table needs to be changed - string {peak,wayp,loca,part}
    //    elDelClass: string 

    // Assign var
    var itemDelClass = itemType + "Del";                                        // e.g. waypDel
    var itemDelImg = "btn" + itemType + "DelImg";                               // e.g. btnwaypDelImg
    var reachedCheck = "cb_" + itemType;                                        // e.g. cb_peak

    // create new html table with value returned by autocomplete
    var itemsTable = '';
    itemsTable += '<table class="itemsTable" cellspacing="0" cellpadding="0">';
    if ( itemType == "peak" ) {
        itemsTable += '<tr><td>Peak</td><td>   reached</td><td></td></tr>';
    } else if ( itemType == "wayp" ) {
        itemsTable += '<tr><td>Waypoint</td><td></td></tr>';
    } else if ( itemType == "loca" ) {
        itemsTable += '<tr><td>Location</td><td></td></tr>';
    } else if ( itemType == "part" ) {
        itemsTable += '<tr><td>Participant</td><td></td></tr>';
    }
    // loop through items array and draw table content
    for (var i = 0; i < itemsArray.length; i++) {
        if ( ( itemsArray[i]["disp_f"] == 1 || itemsArray[i]["disp_f"] ) && itemsArray[i]["itemType"] == itemType ) {
            itemsTable += '<tr class="tblItems">';  
            itemsTable += '<td>' + itemsArray[i]["itemName"] + '</td>';               // 1    
            // if item = peak the reached flag needs to be displayed
            if ( itemType == "peak" ) {
                itemsTable += '<td><input type="checkbox" name="' + reachedCheck + itemsArray[i]["itemId"]
                    + '" id="' + reachedCheck + itemsArray[i]["itemId"];
                if ( itemsArray[i]["reached_f"] ) {
                    itemsTable += '" class="cbReached" checked></td>'; 
                } else {
                    itemsTable += '" class="cbReached"></td>'; 
                }
            }
            itemsTable += '<td><ul class="tblItems">';
            itemsTable += '<li class="button_Li"><a class="itemDel ' + elDelClass + '"' 
                            + ' href="#' + itemDelClass + '_' + itemsArray[i]["itemId"] + '">'
                            + '<img id="' + itemDelImg + '" src="css/images/delete.png"></a></li></ul></td>';
                            itemsTable += '</tr>';
        }               
    }
    itemsTable += '</table>';   
    return itemsTable;
}

// Draws the table that list the selected waypoints
function drawItemsTables_old ( itemsArray, itemType ) {

    // Assign var
    var itemClass = "tblItems";
    var itemDelClass = itemType + "Del";                                        // e.g. waypDel
    var itemDelImg = "btn" + itemType + "DelImg";                               // e.g. btnwaypDelImg
    var elementId = "uiTrack_" + itemType + "List";                            // e.g. uiTrack_peakList
    var reachedCheck = "cb_" + itemType;                                        // e.g. cb_peak

    // create new html table with value returned by autocomplete
    var itemsTable = '';
    itemsTable += '<table class="itemsTable" cellspacing="0" cellpadding="0">';
    if ( itemType == "peak" ) {
        itemsTable += '<tr><td>Peak</td><td>   reached</td><td></td></tr>';
    } else if ( itemType == "wayp" ) {
        itemsTable += '<tr><td>Waypoint</td><td></td></tr>';
    } else if ( itemType == "loca" ) {
        itemsTable += '<tr><td>Location</td><td></td></tr>';
    } else if ( itemType == "part" ) {
        itemsTable += '<tr><td>Participant</td><td></td></tr>';
    }
    // loop through items array and draw table content
    for (var i = 0; i < itemsArray.length; i++) {
        if ( itemsArray[i]["disp_f"] == 1 && itemsArray[i]["itemType"] == itemType ) {
            itemsTable += '<tr class="' + itemClass + '">';  
            itemsTable += '<td>' + itemsArray[i]["itemName"] + '</td>';               // 1    
            // if item = peak the reached flag needs to be displayed
            if ( itemType == "peak" ) {
                itemsTable += '<td><input type="checkbox" name="' + reachedCheck + itemsArray[i]["itemId"]
                    + '" id="' + reachedCheck + itemsArray[i]["itemId"];
                if ( itemsArray[i]["reached_f"] ) {
                    itemsTable += '" class="cbReached" checked></td>'; 
                } else {
                    itemsTable += '" class="cbReached"></td>'; 
                }
            }
            itemsTable += '<td><ul class="' + itemClass + '">';
            itemsTable += '<li class="button_Li"><a class="itemDel "' 
                            + ' href="#' + itemDelClass + '_' + itemsArray[i]["itemId"] + '">'
                            + '<img id="' + itemDelImg + '" src="css/images/delete.png"></a></li></ul></td>';
                            itemsTable += '</tr>';
        }               
    }
    itemsTable += '</table>';   
    document.getElementById(elementId).innerHTML = itemsTable;
}

// Import validation: Checks Validation Comments    
function updateValComments( text ) {
    valComments
        .text( text )
        .addClass( "ui-state-highlight" );
}

// Import validation: Checks existance of file content in ADD dialog
function checkExistance( origin, name ) {
    if ( origin.val().length == 0 ) {
        origin.addClass( "ui-state-error" );
        updateValComments( "Field " + name + " must be entered" );
        return false;
    } else {
        return true;
    }
}

// Import validation: Checks content of field against REGEX - error when matching
function checkRegexpNot( o, regexp, n ) {
    if ( regexp.test( o.val() ) )  {
        o.addClass( "ui-state-error" );
        updateValComments( n );
        return false;
    } else {
        return true;
    }
}

// Import validation: Checks content of field against REGEX - error when NOT matching
function checkRegexp( o, regexp, n ) {
    if ( !( regexp.test( o.val() ) ) ) {
        o.addClass( "ui-state-error" );
        updateValComments( n );
        return false;
    } else {
        return true;
    }
}  

// Import validation: Checks if content of field is a  number
function checkIfNum( o, n ) {
    if ( isNaN( o.val() ) ) {                                                   // isNaN returns false if value is a number --> 1234 = false
        o.addClass( "ui-state-error" );
        updateValComments( n );
        return false;
    } else {
        return true;
    }
}

// Import valication: Checks the min / max length of field content of ADD dialog 
function checkLength( o, n, min, max ) {
    if ( o.val().length > max || o.val().length < min ) {
        o.addClass( "ui-state-error" );
        updateValComments( "Length of " + n + " must be between " + min + " and " + max + "." );
        return false;
    } else {
        return true;
    }
}

// ???
function DisplayObj ( objectName, sqlWherePrev, sqlWhereCurrent, genKml, jsonObject, ajaxCall ) {
    this.objectName = objectName;
    this.sqlWherePrev = sqlWherePrev;
    this.sqlWhereCurrent = sqlWhereCurrent;
    this.genKml = genKml; 
    this.jsonObject = jsonObject;
    this.ajaxCall = ajaxCall;
}

function clearAllErrorStates () {
    //  remove all potential error meesags
    $('#uiTrack_fld_trkTrackName').removeClass( "ui-state-error" );
    $('#uiTrack_fld_trkRoute').removeClass( "ui-state-error" );    
    $('#uiTrack_fld_trkDateBegin').removeClass( "ui-state-error" );
    //$('#uiTrack_fld_trkDateFinish').removeClass( "ui-state-error" );
    $('#uiTrack_fld_trkOrg').removeClass( "ui-state-error" );
    $('#uiTrack_fld_trkEvent').removeClass( "ui-state-error" );
    $('#uiTrack_fld_trkRemarks').removeClass( "ui-state-error" );
    $('#uiTrack_fld_trkDistance').removeClass( "ui-state-error" );
    $('#uiTrack_fld_trkTimeOverall').removeClass( "ui-state-error" );       
    $('#uiTrack_fld_trkTimeToPeak').removeClass( "ui-state-error" );       
    $('#uiTrack_fld_trkTimeToFinish').removeClass( "ui-state-error" );       
    $('#uiTrack_fld_trkMeterUp').removeClass( "ui-state-error" );       
    $('#uiTrack_fld_trkMeterDown').removeClass( "ui-state-error" );       
    $('#uiTrack_fld_trkCountry').removeClass( "ui-state-error" );  
    $('#validateComments').removeClass( "ui-state-highlight" );
}