// =====================================
// ====== M A I N   S E C T I O N ======
// =====================================
$(document).ready(function() {

    // Initial drawing of map
    if ( navigator.onLine ) {
        drawMapEmpty('mapPanel_Map-ResMap');         // Draw empty map (without additional layers) 
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

    // ==========================================================================
    // ========================== panelDisplay ==================================
    // ==========================================================================
    
    // *********************************************
    // Initialse all jquery functional fields

    // Initialise filter area as JQUERY Accordion
    $( function() {
        $( "#displayOptionsAccordion" ).accordion({
          collapsible: true
        });
    } );

    // Initalise field to select start date as JQUERY datepicker
    $( "#dispFilTrk_dateFrom" ).datepicker({
        dateFormat: 'yy-mm-dd', 
        changeMonth: true,
        changeYear: true,
        showOn: "button",
        buttonImage: "css/images/calendar.gif",
        buttonImageOnly: true,
        buttonText: "Select date"
    });
    
    // Initalise field to select to date as JQUERY datepicker
    $( "#dispFilTrk_dateTo" ).datepicker({
        dateFormat: 'yy-mm-dd',
        changeMonth: true,
        changeYear: true,
        showOn: "button",
        buttonImage: "css/images/calendar.gif",
        buttonImageOnly: true,
        buttonText: "Select date"
    });

    // Initialse field 'type' as JQUERY selectable
    $( "#dispFilTrk_type" ).selectable({});

    // Initialse field 'subtype' as JQUERY selectable
    $( "#dispFilTrk_subtype" ).selectable({});

    // ******************************************************************
    // Executes code below when user clicks the 'Apply' filter button

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
            whereString = "trkTyp in (" + whereString + ")";                        // complete SELECT IN statement
            whereStatement.push( whereString );                                     // Add to where Statement array
        }

        // Field subtype
        var whereString = "";                                                       
        $('#dispFilTrk_subtype .ui-selected').each(function() {                     // loop through each selected type item
            var itemId = this.id                                                    // Extract id of selected item
            whereString = whereString + "'" + itemId.slice(19) + "',";              // Substring tyye from id
        });
        if ( whereString.length > 0 ) {
            whereString = whereString.slice(0,whereString.length-1);                // remove last comma
            whereString = "trkSubTyp in (" + whereString + ")";                     // complete SELECT IN statement
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
            var sql = "WHERE ";

            for (var i=0; i<whereStatement.length; i++) {
                sql += whereStatement[i];
                sql += " AND ";
            }
            sql = sql.slice(0,sql.length-5);
        }
        
        // ****************************************************
        // Generate KML & draw Map 

        callGenKml("tracks",sql);                       // Generate KML file; file stored in file defined by global var segKmlFileNameURL
        callGenWaypKml(optionWhereStmt);                // Generate KML file; file stored in file defined by global var segKmlFileNameURL 
        
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
    // ***************************

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

function callGenKml(kmlType, sqlWhere) {
    // call gen_kml.php using XMLHttpRequest POST     
    var xhr = new XMLHttpRequest();
    phpLocation = document.URL + "gen_kml.php";          // Variable to store location of php file
    xhrParams = "sqlFilterString=" + segSqlFilterString;   // Variable for POST parameters
    xhrParams += "&segKmlFileName=" + segKmlFileName ;
    xhr.open ('POST', phpLocation, false);                // Make XMLHttpRequest - in asynchronous mode to avoid wrong data display in map (map displayed before KML file is updated)
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");  // Set header to encode special characters like %
    xhr.send(encodeURI(xhrParams));
    if (debug) { console.info("callGenSegKml completed"); }; 
}   


    