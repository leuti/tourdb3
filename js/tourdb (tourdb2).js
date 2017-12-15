debug = true;

// =================================================
// ====== G L O B A L   V A R   S E C T I O N ======
// =================================================

    // Create unique file name for KML
    var today = new Date();

    // file location & name for KML output
    segKmlFileName = "kml/seg-" + today.getTime() + ".kml";
    segKmlFileNameURL = document.URL + segKmlFileName;
    waypKmlFileName = "kml/wayp-" + today.getTime() + ".kml";
    waypKmlFileNameURL = document.URL + waypKmlFileName;

    // Parameters for Where Clause defined in Display Options form of map panel
    drawHangneigung = false;
    drawWanderwege = false; 
    drawHaltestellen = false;
    drawKantonsgrenzen = false;
    drawSacRegion = false;
    optionWhereStmt = ""; // Var for Where Clause 

    // Array contains all segments with coordinates
    // Fields: 0:segId - 1:Currently displayed in panelSegments - 2:Selected as tour - 3:hasCoordinates Flag
    segDisplayArray = [];

    // Set to true when function Map needs to be called; set after fetch_pages function
    mapMapNeedsLoad = true; // map of panel map needs load

    segFetchPagesHasRun = false; // will be set if the list of Segements has been fetched for first time (not yet used)
    waypFetchPagesHasRun = false; // will be set if the list of Waypoints has been fetched for first time
    tourFetchPagesHasRun = false; // will be set if the list of Segments has been fetched for panelTour first time

    countries = ["AT", "CH", "DE", "FR", "IT", "LI", "n/a"];
    cantons = ["AG", "AI", "AR", "BE", "BL", "BS", "FR", "GE", "GL", "GR", "JU", "LU", "NE",
            "NW", "OW", "SG", "SH", "SO", "SZ", "TG", "TI", "UR", "VD", "VS", "ZG", "ZH", "n/a"];
//
// =====================================
// ====== M A I N   S E C T I O N ======
// =====================================

    // WHAT must belong to document ready section?
    $(document).ready(function() {

        // Initial load on page load --> Loads segments 
        var segSqlFilterString ; 
        var waypSqlFilterString ; 
        var tourSqlFilterString ;
        // Initial drawing of map
        if ( navigator.onLine ) {
            drawMapEmpty('mapPanel_Map-ResMap');         // Draw empty map (without additional layers) 
        };
    
//
// =========================================================
// ====== I N I T I A L I S A T I O N   S E C T I O N ======
// =========================================================
//
    // ----------- MAP FILTER FIELDS  -------------
        // Initialise JQUI elements fields for SEGMENT Filter in Panel Map

        // mapUF_sourceName
        $( "#mapUF_sourceName" ).autocomplete({
            source: "get_auto_complete_values.php?field=segSourceFID",
            minLength: 2,
            select: function( event, ui ) {
                $( "#mapUF_sourceFID" ).val( ui.item.id );
            },
            change: function( event, ui ) {
                if ( $( "#mapUF_sourceName" ).val() == '' ) {
                        $( "#mapUF_sourceFID" ).val( '' );
                }
            }
        });
        var mapUF_sourceFID = $( "#mapUF_sourceFID" );

        // mapUF_segType
        $( "#mapUF_segType" ).selectable({});
        
        // startLocName
        $( "#mapUF_startLocName" ).autocomplete({
            source: "get_auto_complete_values.php?field=getWaypLong",
            minLength: 1,
            select: function( event, ui ) {
                $( "#mapUF_startLocID" ).val( ui.item.id );
            },
            change: function( event, ui ) {
                if ( $( "#mapUF_startLocName" ).val() == '' ) {
                        $( "#mapUF_startLocID" ).val( '' );
                }
            }
        });
        var mapUF_startLocID = $( "#mapUF_startLocID" ); 
        
        // startLocAlt 
        $( "#mapUF_startLocAlt_slider" ).slider({
            range: true,
            min: 0,
            max: 5000,
            values: [ 400, 5000 ],
            slide: function( event, ui ) {
                $( "#mapUF_startLocAlt_slider_values" ).val( "min. " + ui.values[ 0 ] + "m - max. " + ui.values[ 1 ] + "m" );
            }
        });
        $( "#mapUF_startLocAlt_slider_values" ).val( "min. " + $( "#mapUF_startLocAlt_slider" ).slider( "values", 0 ) +
        "m - max. " + $( "#mapUF_startLocAlt_slider" ).slider( "values", 1 ) +"m" );
        
        // startLocType
        $( "#mapUF_startLocType" ).selectable({});

        // targetLocName
        $( "#mapUF_targetLocName" ).autocomplete({
            source: "get_auto_complete_values.php?field=getWaypLong",
            minLength: 1,
            select: function( event, ui ) {
                $( "#mapUF_targetLocID" ).val( ui.item.id );
            },
            change: function( event, ui ) {
                if ( $( "#mapUF_targetLocName" ).val() == '' ) {
                        $( "#mapUF_targetLocID" ).val( '' );
                }
            }
        });
        var mapUF_targetLocID = $( "#mapUF_targetLocID" ); 

        // targetLocAlt
        $( "#mapUF_targetLocAlt_slider" ).slider({
            range: true,
            min: 0,
            max: 5000,
            values: [ 400, 5000 ],
            slide: function( event, ui ) {
                $( "#mapUF_targetLocAlt_slider_values" ).val( "min. " + ui.values[ 0 ] + "m - max. " + ui.values[ 1 ] + "m" );
            }
        });
        $( "#mapUF_targetLocAlt_slider_values" ).val( "min. " + $( "#mapUF_targetLocAlt_slider" ).slider( "values", 0 ) +
        "m - max. " + $( "#mapUF_targetLocAlt_slider" ).slider( "values", 1 ) +"m" );

        // targetLocType
        $( "#mapUF_targetLocType" ).selectable({});

        // Region
        $( "#mapUF_segRegion" ).autocomplete({
            source: "get_auto_complete_values.php?field=regionID",
            minLength: 1,
            select: function( event, ui ) {
                $( "#mapUF_segRegionID" ).val( ui.item.id );
            },
            change: function( event, ui ) {
                if ( $( "#mapUF_segRegion" ).val() == '' ) {
                        $( "#mapUF_segRegionID" ).val( '' );
                }
            }
        });
        var mapUF_segRegionID = $( "#mapUF_segRegionID" ); 

        // Area
        $( "#mapUF_segArea" ).autocomplete({
            source: "get_auto_complete_values.php?field=areaID",
            minLength: 1,
            select: function( event, ui ) {
                $( "#mapUF_segAreaID" ).val( ui.item.id );
            },
            change: function( event, ui ) {
                if ( $( "#mapUF_segArea" ).val() == '' ) {
                        $( "#mapUF_segAreaID" ).val( '' );
                }
            }
        });
        var mapUF_segAreaID = $( "#mapUF_segAreaID" ); 

        $( "#mapUF_grade" ).selectable({});
        $( "#mapUF_climbGrade" ).selectable({});
        $( "#mapUF_ehaft" ).selectable({});   

    // ----------- SEGMENT FILTER FIELDS ----------
        // Prepare JQUI elements fields for Segment User Filter Panel
    
        // segUF_sourceName
        $( "#segUF_sourceName" ).autocomplete({
            source: "get_auto_complete_values.php?field=segSourceFID",
            minLength: 2,
            select: function( event, ui ) {
                $( "#segUF_sourceFID" ).val( ui.item.id );
            },
            change: function( event, ui ) {
                if ( $( "#segUF_sourceName" ).val() == '' ) {
                     $( "#segUF_sourceFID" ).val( '' );
                }
            }
        });
        var segUF_sourceFID = $( "#segUF_sourceFID" );

        $( "#segUF_segType" ).selectable({});
        
        // startLocName
        $( "#segUF_startLocName" ).autocomplete({
            source: "get_auto_complete_values.php?field=getWaypLong",
            minLength: 1,
            select: function( event, ui ) {
                $( "#segUF_startLocID" ).val( ui.item.id );
            },
            change: function( event, ui ) {
                if ( $( "#segUF_startLocName" ).val() == '' ) {
                     $( "#segUF_startLocID" ).val( '' );
                }
            }
        });
        var segUF_startLocID = $( "#segUF_startLocID" ); 
        
        // startLocAlt 
        $( "#segUF_startLocAlt_slider" ).slider({
            range: true,
            min: 0,
            max: 5000,
            values: [ 400, 5000 ],
            slide: function( event, ui ) {
                $( "#segUF_startLocAlt_slider_values" ).val( "min. " + ui.values[ 0 ] + "m - max. " + ui.values[ 1 ] + "m" );
            }
        });
        $( "#segUF_startLocAlt_slider_values" ).val( "min. " + $( "#segUF_startLocAlt_slider" ).slider( "values", 0 ) +
        "m - max. " + $( "#segUF_startLocAlt_slider" ).slider( "values", 1 ) +"m" );
        
        $( "#segUF_startLocType" ).selectable({});

        // targetLocName
        $( "#segUF_targetLocName" ).autocomplete({
            source: "get_auto_complete_values.php?field=getWaypLong",
            minLength: 1,
            select: function( event, ui ) {
                $( "#segUF_targetLocID" ).val( ui.item.id );
            },
            change: function( event, ui ) {
                if ( $( "#segUF_targetLocName" ).val() == '' ) {
                     $( "#segUF_targetLocID" ).val( '' );
                }
            }
        });
        var segUF_targetLocID = $( "#segUF_targetLocID" ); 

        // targetLocAlt
        $( "#segUF_targetLocAlt_slider" ).slider({
            range: true,
            min: 0,
            max: 5000,
            values: [ 400, 5000 ],
            slide: function( event, ui ) {
                $( "#segUF_targetLocAlt_slider_values" ).val( "min. " + ui.values[ 0 ] + "m - max. " + ui.values[ 1 ] + "m" );
            }
        });
        $( "#segUF_targetLocAlt_slider_values" ).val( "min. " + $( "#segUF_targetLocAlt_slider" ).slider( "values", 0 ) +
        "m - max. " + $( "#segUF_targetLocAlt_slider" ).slider( "values", 1 ) +"m" );

        $( "#segUF_targetLocType" ).selectable({});

        // Region
        $( "#segUF_region" ).autocomplete({
            source: "get_auto_complete_values.php?field=regionID",
            minLength: 1,
            select: function( event, ui ) {
                $( "#segUF_regionID" ).val( ui.item.id );
            },
            change: function( event, ui ) {
                if ( $( "#segUF_region" ).val() == '' ) {
                     $( "#segUF_regionID" ).val( '' );
                }
            }
        });
        var segUF_regionID = $( "#segUF_regionID" ); 

        // Area
        $( "#segUF_area" ).autocomplete({
            source: "get_auto_complete_values.php?field=areaID",
            minLength: 1,
            select: function( event, ui ) {
                $( "#segUF_areaID" ).val( ui.item.id );
            },
            change: function( event, ui ) {
                if ( $( "#segUF_area" ).val() == '' ) {
                     $( "#segUF_areaID" ).val( '' );
                }
            }
        });
        var segUF_areaID = $( "#segUF_areaID" ); 

        $( "#segUF_grade" ).selectable({});
        $( "#segUF_climbGrade" ).selectable({});
        $( "#segUF_ehaft" ).selectable({});
     

    // ----------- WAYPOINTS FILTER FIELDS -------- 
        // Prepare JQUI elements fields for Waypoint User Filter Panel  

        // Selectable 
        $( "#waypUF_wtypCode" ).selectable({});

        // Field waypUF_country
        $( "#waypUF_country" ).autocomplete({
            source: countries,
            minLength: 1,
        });
        
        // Field waypUF_region
        $( "#waypUF_region" ).autocomplete({
            source: "get_auto_complete_values.php?field=regionID",
            minLength: 1,
            select: function( event, ui ) {
                $( "#waypFilter_regionID" ).val( ui.item.id );
            },
            change: function( event, ui ) {
                if ( $( "#waypUF_region" ).val() == '' ) {
                     $( "#waypFilter_regionID" ).val( '' );
                }
            }
        });
        var waypFilter_regionID = $( "#waypFilter_regionID" ); 

        // Field waypUF_area
        $( "#waypUF_area" ).autocomplete({
            source: "get_auto_complete_values.php?field=areaID",
            minLength: 1,
            select: function( event, ui ) {
                $( "#waypFilter_areaID" ).val( ui.item.id );
            },
            change: function( event, ui ) {
                if ( $( "#waypUF_area" ).val() == '' ) {
                     $( "#waypFilter_areaID" ).val( '' );
                }
            }
        });
        var waypFilter_areaID = $( "#waypFilter_areaID" ); 

        // waypUF_alt_slider
        $( "#waypUF_alt_slider" ).slider({
            range: true,
            min: 0,
            max: 5000,
            values: [ 400, 5000 ],
            slide: function( event, ui ) {
                $( "#waypUF_alt_slider_values" ).val( "min. " + ui.values[ 0 ] + "m - max. " + ui.values[ 1 ] + "m" );
            }
        });
        $( "#waypUF_alt_slider_values" ).val( "min. " + $( "#waypUF_alt_slider" ).slider( "values", 0 ) +
        "m - max. " + $( "#waypUF_alt_slider" ).slider( "values", 1 ) +"m" );


        

    // ----------- MAP FILTER FIELDS  -------------
        // Initialise JQUI elements fields for SEGMENT Filter in Panel Map

            // tourUF_sourceName
            $( "#tourUF_sourceName" ).autocomplete({
                source: "get_auto_complete_values.php?field=segSourceFID",
                minLength: 2,
                select: function( event, ui ) {
                    $( "#tourUF_sourceFID" ).val( ui.item.id );
                },
                change: function( event, ui ) {
                    if ( $( "#tourUF_sourceName" ).val() == '' ) {
                            $( "#tourUF_sourceFID" ).val( '' );
                    }
                }
            });
            var tourUF_sourceFID = $( "#tourUF_sourceFID" );

            // tourUF_segType
            $( "#tourUF_segType" ).selectable({});
            
            // startLocName
            $( "#tourUF_startLocName" ).autocomplete({
                source: "get_auto_complete_values.php?field=getWaypLong",
                minLength: 1,
                select: function( event, ui ) {
                    $( "#tourUF_startLocID" ).val( ui.item.id );
                },
                change: function( event, ui ) {
                    if ( $( "#tourUF_startLocName" ).val() == '' ) {
                            $( "#tourUF_startLocID" ).val( '' );
                    }
                }
            });
            var tourUF_startLocID = $( "#tourUF_startLocID" ); 
            
            // startLocAlt 
            $( "#tourUF_startLocAlt_slider" ).slider({
                range: true,
                min: 0,
                max: 5000,
                values: [ 400, 5000 ],
                slide: function( event, ui ) {
                    $( "#tourUF_startLocAlt_slider_values" ).val( "min. " + ui.values[ 0 ] + "m - max. " + ui.values[ 1 ] + "m" );
                }
            });
            $( "#tourUF_startLocAlt_slider_values" ).val( "min. " + $( "#tourUF_startLocAlt_slider" ).slider( "values", 0 ) +
            "m - max. " + $( "#tourUF_startLocAlt_slider" ).slider( "values", 1 ) +"m" );
            
            // startLocType
            $( "#tourUF_startLocType" ).selectable({});

            // targetLocName
            $( "#tourUF_targetLocName" ).autocomplete({
                source: "get_auto_complete_values.php?field=getWaypLong",
                minLength: 1,
                select: function( event, ui ) {
                    $( "#tourUF_targetLocID" ).val( ui.item.id );
                },
                change: function( event, ui ) {
                    if ( $( "#tourUF_targetLocName" ).val() == '' ) {
                            $( "#tourUF_targetLocID" ).val( '' );
                    }
                }
            });
            var tourUF_targetLocID = $( "#tourUF_targetLocID" ); 

            // targetLocAlt
            $( "#tourUF_targetLocAlt_slider" ).slider({
                range: true,
                min: 0,
                max: 5000,
                values: [ 400, 5000 ],
                slide: function( event, ui ) {
                    $( "#tourUF_targetLocAlt_slider_values" ).val( "min. " + ui.values[ 0 ] + "m - max. " + ui.values[ 1 ] + "m" );
                }
            });
            $( "#tourUF_targetLocAlt_slider_values" ).val( "min. " + $( "#tourUF_targetLocAlt_slider" ).slider( "values", 0 ) +
            "m - max. " + $( "#tourUF_targetLocAlt_slider" ).slider( "values", 1 ) +"m" );

            // targetLocType
            $( "#tourUF_targetLocType" ).selectable({});

            // Region
            $( "#tourUF_segRegion" ).autocomplete({
                source: "get_auto_complete_values.php?field=regionID",
                minLength: 1,
                select: function( event, ui ) {
                    $( "#tourUF_segRegionID" ).val( ui.item.id );
                },
                change: function( event, ui ) {
                    if ( $( "#tourUF_segRegion" ).val() == '' ) {
                            $( "#tourUF_segRegionID" ).val( '' );
                    }
                }
            });
            var tourUF_segRegionID = $( "#tourUF_segRegionID" ); 

            // Area
            $( "#tourUF_segArea" ).autocomplete({
                source: "get_auto_complete_values.php?field=areaID",
                minLength: 1,
                select: function( event, ui ) {
                    $( "#tourUF_segAreaID" ).val( ui.item.id );
                },
                change: function( event, ui ) {
                    if ( $( "#tourUF_segArea" ).val() == '' ) {
                            $( "#tourUF_segAreaID" ).val( '' );
                    }
                }
            });
            var tourUF_segAreaID = $( "#tourUF_segAreaID" ); 

            $( "#tourUF_grade" ).selectable({});
            $( "#tourUF_climbGrade" ).selectable({});
            $( "#tourUF_ehaft" ).selectable({});   
//
// =============================================
// ====== U S E R   I N T E R A C T I O N ======
// =============================================
//
// ----------- GENERAL ---------
    // ----------- Topics tab Control -----------  
        // Switch to relevant topics tab/panel and perform initiating tasks
            $('.topicTabs').each(function() {
                if (debug) { console.info(".topicTabs: each function entered"); };
                var $topicThis = $(this);                   // $topicThis becomes ul.topicTabs
                $topicTab = $topicThis.find('li.active');   // Find and store current active li element (home at start up)
                var $topicLink = $topicTab.find('a');       // Get link <a> from active li element 
                $topicPanel = $($topicLink.attr('href'));   // Get active panel (home at start up)

                $(this).on('click', '.topic-control', function(e) {     // When click on a topic tab (li item)
                    if (debug) { console.info(".topic-control: onclick function entered"); };
                    e.preventDefault();                     // Prevent link behaviour
                    var $topicLink = $(this)                // Store the current link <a> element
                    var topicId = this.hash;                // Get div class of selected topic (e.g #panelSegments)

                    // Run following block if selected topic is currently not active
                    if (topicId && !$topicLink.is('.active')) {
                        $topicPanel.removeClass('active');          // Make current panel inactive
                        $topicTab.removeClass('active');            // Make current tab inactive

                        $topicPanel = $(topicId).addClass('active'); // Make new panel active
                        $topicTab = $topicLink.parent().addClass('active'); // Make new tab active
                        
                        // Load List of Segments if not yet loaded
                        if ( topicId == "#panelSegments" && segFetchPagesHasRun == false) {
                            page = 1;                       // Set page number for display to 1
                            $("#segPanel_List-ResList").load("seg_fetch_pages.php",{"sqlFilterString":segSqlFilterString,"page":page}); 
                            segFetchPagesHasRun = true;     // Indicate that fetch_pages has run
                        }

                        // Load List of Waypoints if not yet loaded
                        if ( topicId == "#panelWaypoints" && waypFetchPagesHasRun == false) {
                            page = 1;                       // Set page number for display to 1
                            waypSqlFilterString = " ";      // set sql where clause to empty
                            $("#waypPanel_List-ResList").load("wayp_fetch_pages.php",{"waypSqlFilterString":waypSqlFilterString,"page":page}); 
                            waypFetchPagesHasRun = true;    // Indicate that fetch_pages has run
                        }

                        // Display Map if not yet loaded
                        if ( topicId == "#panelMap" && mapMapNeedsLoad == true ) {
                            var removeEl = document.getElementById('mapPanel_Map-ResMap');  // Identify div to be removed
                            var containerEl = removeEl.parentNode;          // Get its parent node
                            containerEl.removeChild(removeEl);              // Remove the the child of the parent
                            var newDiv = document.createElement('div');     // Create new div element
                            containerEl.appendChild(newDiv);                // Append new div to parent node
                            newDiv.id = 'mapPanel_Map-ResMap';              // Give new div an ID
                            newDiv.className = 'mapPanel_Map-ResMap';       // Give new div a class
                            $('#mapPanel_Map').addClass('visible');         // Make map panel visible
                            drawMapEmpty('mapPanel_Map-ResMap');            // Draw map to panel (without additional layers)                   
                            mapMapNeedsLoad = false;
                        }
                        // Display and manage Touren Panel
                        if ( topicId == "#panelTour" ) {

                            // Draw panel for Available Segments
                            var width = document.getElementById('panelTour').offsetWidth;
                            var height = document.getElementById('panelTour').offsetHeight - document.getElementById('tourdbHeader').offsetHeight;
                            $('#panelTourenMainSplitter').jqxSplitter({ width: width - 20 , height: height - 50, orientation: 'vertical', panels: [{ size: '50%', collapsible: false }] });
                            $('#panelTourenNestedSplitter').jqxSplitter({ width: '100%', height: '100%',  orientation: 'horizontal', panels: [{ size: '50%', collapsible: false}] });
                            $('#panelTourTour-sortable').sortable();
                            $('#panelTourTour-sortable').disableSelection(); 
                            drawMapEmpty('panelTourenMap-Map');            // Draw map to panel (without additional layers)
                        }
                    }
                    if (debug) { console.info(".topic-control: onclick function completed"); };
                }); 
                if (debug) { console.info(".topicTabs: each function completed"); };
            });  

//
// ----------- MAP -------------
    // Makes the Filter appear or disappear
        $(this).on('click', '.mapUserFilterControl', function(e) { 
            if (debug) { console.info(".mapUserFilterControl: onclick function entered"); };
            e.preventDefault(); // Prevent link behaviour
        
            var $filterLink = $(this) // Store the current link
            var filterId = this.hash; // Get filter ID
            $filterPanel = $($filterLink.attr('href')); // Get current panel

            if (filterId && !$filterPanel.is('.visible')) { // if filter ID is available and a panel is not open
                $('#mapPanelFilter').removeClass('visible'); // make panel not visible
                $filterPanel = $(filterId).addClass('visible'); // Make current panel visible
            } else {
                $filterPanel.removeClass('visible'); // Make current panel invisible           
            }
            if (debug) { console.info(".mapUserFilterControl: onclick function completed"); };
        });

    // Executes code below when user clicks the 'Apply' filter button
        $(document).on('click', '#mapApplyFilterUser', function (e) {
            e.preventDefault();
            if (debug) { console.info('#mapApplyFilter click function entered'); };      
            
            // ==================================================
            // ===== Build SQL WHERE statement for segments =====
            // ==================================================

            // var segSqlFilterString = "";
            // var waypSqlFilterString = "";
            var veryFirst = true;  // flags that very first criteria has not yet been processed
            
            // Field segUF_sourceName
            if ( ($('#mapUF_sourceFID').val()) != "" ) {
                if ( veryFirst ) {
                    segSqlFilterString = " WHERE sourceFID in (";
                    segSqlFilterString += "'" + ($('#mapUF_sourceFID').val()) + "')";
                    veryFirst = false;      
                } else if ( !veryFirst ) {
                    segSqlFilterString += " AND sourceFID in (";
                    segSqlFilterString += "'" + ($('#mapUF_sourceFID').val()) + "')";    
                } 
            };

            // Field mapUF_sourceRef
            var firstInCriteria = true;
            if ( ($('#mapUF_sourceRef').val()) != "" ) {
                if ( veryFirst ) {
                    segSqlFilterString = " WHERE sourceRef like ";
                    segSqlFilterString += "'%" + ($('#mapUF_sourceRef').val()) + "%'";      
                    veryFirst = false;
                } else if ( !veryFirst ) {
                    segSqlFilterString += " AND sourceRef like ";
                    segSqlFilterString += "'%" + ($('#mapUF_sourceRef').val()) + "%'";    
                } 
            };

            // Field segType
            var firstInCriteria = true; // indicates that the first element of a criteria has not been processed yet
            $('#mapUF_segType .ui-selected').each(function() {
                var recordId = this.id;
                var sqlName = "segType";
                var lenCriteria = recordId.length;
                var startCriteria = sqlName.length + 1;
                if ( veryFirst && firstInCriteria ) {
                    segSqlFilterString = " WHERE " + sqlName + " in (";
                    segSqlFilterString += "'" + recordId.slice(startCriteria,lenCriteria) + "'";
                    veryFirst = false;
                    firstInCriteria = false;     
                } else if (!veryFirst && !firstInCriteria) {
                    segSqlFilterString += ",'" + recordId.slice(startCriteria,lenCriteria) + "'";
                } else if (!veryFirst && firstInCriteria) {
                    segSqlFilterString += " AND " + sqlName + " in ('" + recordId.slice(startCriteria,lenCriteria) + "'";
                    firstInCriteria = false;
                }
            });
            if ( $('#mapUF_segType .ui-selected').length > 0 ) {
                segSqlFilterString += ")";
            } 

            // Field segName
            var firstInCriteria = true;
            if ( ($('#mapUF_segName:text').val()) != "" ) {
                if ( veryFirst ) {
                    segSqlFilterString = " WHERE segName like ";
                    segSqlFilterString += "'%" + ($('#mapUF_segName:text').val()) + "%'";      
                    veryFirst = false;
                } else if ( !veryFirst ) {
                    segSqlFilterString += " AND segName like ";
                    segSqlFilterString += "'%" + ($('#mapUF_segName:text').val()) + "%'";    
                } 
            };
                        
            // Field startLoc
            if ( ($('#mapUF_startLocID').val()) != "" ) {
                if ( veryFirst ) {
                    segSqlFilterString = " WHERE segStartLocationFID in (";
                    segSqlFilterString += ($('#mapUF_startLocID').val()) + ")";
                    veryFirst = false;      
                } else if ( !veryFirst ) {
                    segSqlFilterString += " AND segStartLocationFID in (";
                    segSqlFilterString += ($('#mapUF_startLocID').val()) + ")";    
                } 
            };

            // Field startLocAlt
            if ( veryFirst ) {
                segSqlFilterString = " WHERE startLocAlt >= ";
                segSqlFilterString += $( "#mapUF_startLocAlt_slider" ).slider( "values", 0 );
                segSqlFilterString += " AND startLocAlt <= ";
                segSqlFilterString += $( "#mapUF_startLocAlt_slider" ).slider( "values", 1 );
                veryFirst = false;      
            } else if ( !veryFirst ) {
                segSqlFilterString += " AND startLocAlt >= ";
                segSqlFilterString += $( "#mapUF_startLocAlt_slider" ).slider( "values", 0 );
                segSqlFilterString += " AND startLocAlt <= ";
                segSqlFilterString += $( "#mapUF_startLocAlt_slider" ).slider( "values", 1 );    
            }            

            // Field startLocType
            var firstInCriteria = true;
            $('#mapUF_startLocType .ui-selected').each(function() {
                var recordId = this.id;
                var sqlName = "startLocType";
                var lenCriteria = recordId.length;
                var startCriteria = sqlName.length + 1;
                if ( veryFirst && firstInCriteria ) {
                    segSqlFilterString = " WHERE " + sqlName + " in (";
                    segSqlFilterString += "'" + recordId.slice(startCriteria,lenCriteria) + "'";  
                    veryFirst = false;
                    firstInCriteria = false;    
                } else if (!veryFirst && !firstInCriteria) {
                    segSqlFilterString += ",'" + recordId.slice(startCriteria,lenCriteria) + "'";
                } else if (!veryFirst && firstInCriteria) {
                    segSqlFilterString += " AND " + sqlName + " in ('" + recordId.slice(startCriteria,lenCriteria) + "'";
                    firstInCriteria = false;
                }
            });
            if ( $('#mapUF_startLocType .ui-selected').length >0 ) {
                segSqlFilterString += ")";
            }

            // Field targetLoc
            if ( ($('#mapUF_targetLocID').val()) != "" ) {
                if ( veryFirst ) {
                    segSqlFilterString = " WHERE segTargetLocationFID in (";
                    segSqlFilterString += ($('#mapUF_targetLocID').val()) + ")";
                    veryFirst = false;      
                } else if ( !veryFirst ) {
                    segSqlFilterString += " AND segTargetLocationFID in (";
                    segSqlFilterString += ($('#mapUF_targetLocID').val()) + ")";    
                } 
            };

            // Field targetLocAlt
            if ( veryFirst ) {
                segSqlFilterString = " WHERE targetLocAlt >= ";
                segSqlFilterString += $( "#mapUF_targetLocAlt_slider" ).slider( "values", 0 );
                segSqlFilterString += " AND targetLocAlt <= ";
                segSqlFilterString += $( "#mapUF_targetLocAlt_slider" ).slider( "values", 1 );
                veryFirst = false;      
            } else if ( !veryFirst ) {
                segSqlFilterString += " AND targetLocAlt >= ";
                segSqlFilterString += $( "#mapUF_targetLocAlt_slider" ).slider( "values", 0 );
                segSqlFilterString += " AND targetLocAlt <= ";
                segSqlFilterString += $( "#mapUF_targetLocAlt_slider" ).slider( "values", 1 );    
            }            

            // Field targetLocType
            var firstInCriteria = true;
            $('#mapUF_targetLocType .ui-selected').each(function() {
                var recordId = this.id;
                var sqlName = "targetLocType";
                var lenCriteria = recordId.length;
                var startCriteria = sqlName.length + 1;
                if ( veryFirst && firstInCriteria ) {
                    segSqlFilterString = " WHERE " + sqlName + " in (";
                    segSqlFilterString += "'" + recordId.slice(startCriteria,lenCriteria) + "'";  
                    veryFirst = false;    
                    firstInCriteria = false;
                } else if (!veryFirst && !firstInCriteria) {
                    segSqlFilterString += ",'" + recordId.slice(startCriteria,lenCriteria) + "'";
                } else if (!veryFirst && firstInCriteria) {
                    segSqlFilterString += " AND " + sqlName + " in ('" + recordId.slice(startCriteria,lenCriteria) + "'";
                    firstInCriteria = false;
                }               
            });
            if ( $('#mapUF_targetLocType .ui-selected').length >0 ) {
                segSqlFilterString += ")";
            }

            // Field region
            if ( ($('#mapUF_segRegionID').val()) != "" ) {
                if ( veryFirst ) {
                    segSqlFilterString = " WHERE regionId in (";
                    segSqlFilterString += ($('#mapUF_segRegionID').val()) + ")"; 
                    veryFirst = false;     
                } else if ( !veryFirst ) {
                    segSqlFilterString += " AND regionId in (";
                    segSqlFilterString += ($('#mapUF_segRegionID').val()) + ")";    
                } 
            };

            // Field area
            if ( ($('#mapUF_segAreaID').val()) != "" ) {
                if ( veryFirst ) {
                    segSqlFilterString = " WHERE areaId in(";
                    segSqlFilterString += ($('#mapUF_segAreaID').val()) + ")";  
                    veryFirst = false;    
                } else if ( !veryFirst ) {
                    segSqlFilterString += " AND areaId in (";
                    segSqlFilterString += ($('#mapUF_segAreaID').val()) + ")";    
                } 
            };

            // Field grade
            var firstInCriteria = true;
            $('#mapUF_grade .ui-selected').each(function() {
                var recordId = this.id;
                var sqlName = "grade";
                var lenCriteria = recordId.length;
                var startCriteria = sqlName.length + 1;
                if ( veryFirst && firstInCriteria ) {
                    segSqlFilterString = " WHERE " + sqlName + " in (";
                    segSqlFilterString += "'" + recordId.slice(startCriteria,lenCriteria) + "'"; 
                    veryFirst = false;     
                    firstInCriteria = false;
                } else if (!veryFirst && !firstInCriteria) {
                    segSqlFilterString += ",'" + recordId.slice(startCriteria,lenCriteria) + "'";
                } else if (!veryFirst && firstInCriteria) {
                    segSqlFilterString += " AND " + sqlName + " in ('" + recordId.slice(startCriteria,lenCriteria) + "'";
                    firstInCriteria = false;
                }
            });
            if ( $('#mapUF_grade .ui-selected').length >0 ) {
                segSqlFilterString += ")";
            }

            // Field climbGrade
            var firstInCriteria = true;
            $('#mapUF_climbGrade .ui-selected').each(function() {
                var recordId = this.id;
                var sqlName = "climbGrade";
                var lenCriteria = recordId.length;
                var startCriteria = sqlName.length + 1;
                if ( veryFirst && firstInCriteria ) {
                    segSqlFilterString = " WHERE " + sqlName + " in (";
                    segSqlFilterString += "'" + recordId.slice(startCriteria,lenCriteria) + "'";   
                    veryFirst = false;   
                    firstInCriteria = false;
                } else if (!veryFirst && !firstInCriteria) {
                    segSqlFilterString += ",'" + recordId.slice(startCriteria,lenCriteria) + "'";
                } else if (!veryFirst && firstInCriteria) {
                    segSqlFilterString += " AND " + sqlName + " in ('" + recordId.slice(startCriteria,lenCriteria) + "'";
                    firstInCriteria = false;
                }
            });
            if ( $('#mapUF_climbGrade .ui-selected').length >0 ) {
                segSqlFilterString += ")";
            }

            // Field ehaft
            var firstInCriteria = true;
            $('#mapUF_ehaft .ui-selected').each(function() {
                var recordId = this.id;
                var sqlName = "ehaft";
                var lenCriteria = recordId.length;
                var startCriteria = sqlName.length + 1;
                if ( veryFirst && firstInCriteria ) {
                    segSqlFilterString = " WHERE " + sqlName + " in (";
                    segSqlFilterString += "'" + recordId.slice(startCriteria,lenCriteria) + "'";    
                    veryFirst = false;  
                    firstInCriteria = false;
                } else if (!veryFirst && !firstInCriteria) {
                    segSqlFilterString += ",'" + recordId.slice(startCriteria,lenCriteria) + "'";
                } else if (!veryFirst && firstInCriteria) {
                    segSqlFilterString += " AND " + sqlName + " in ('" + recordId.slice(startCriteria,lenCriteria) + "'";
                    firstInCriteria = false;
                }
            });
            if ( $('#mapUF_ehaft .ui-selected').length >0 ) {
                segSqlFilterString += ")";
            }

            // =================================================
            // ============ generate KML & draw Map ============
            // =================================================

            if (debug) { console.info('segSqlFilterString: ' + segSqlFilterString); };
            // if (debug) { console.info('waypSqlFilterString: ' + waypSqlFilterString); };
            if (debug) { console.info('optionWhereStmt: ' + optionWhereStmt); };
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
            
            if (debug) { console.info('#mapApplyFilter click function completed'); }; 
        });
    
    // Manage Click on Display Option Button
        $(this).on('click', '.option-control', function(e) { 
            
            // preparation tasks?
            e.preventDefault(); // Prevent link behaviour
            var dialog, form;
            
            // mapOpt_wtypCode
            $( "#mapOpt_wtypCode" ).selectable({});
            
            // Create Dialog modal
            function saveOptions() {
                var veryFirst = true;
                var firstInCriteria = true;

                // Field segType
                var firstInCriteria = true; // indicates that the first element of a criteria has not been processed yet
                optionWhereStmt = ' WHERE 1=2'; // set where statement to empty
                $('#mapOpt_wtypCode .ui-selected').each(function() {
                    var recordId = this.id;
                    var sqlName = "wtypId";
                    var lenCriteria = recordId.length;
                    var startCriteria = sqlName.length + 1;
                    if ( veryFirst && firstInCriteria ) {
                        optionWhereStmt = " WHERE " + sqlName + " in (";
                        optionWhereStmt += "'" + recordId.slice(startCriteria,lenCriteria) + "'";
                        veryFirst = false;
                        firstInCriteria = false;     
                    } else if (!veryFirst && !firstInCriteria) {
                        optionWhereStmt += ",'" + recordId.slice(startCriteria,lenCriteria) + "'";
                    } else if (!veryFirst && firstInCriteria) {
                        optionWhereStmt += " AND " + sqlName + " in ('" + recordId.slice(startCriteria,lenCriteria) + "'";
                        firstInCriteria = false;
                    }
                });
                if ( $('#mapOpt_wtypCode .ui-selected').length > 0 ) {
                    optionWhereStmt += ")";
                } 
                
                // check setting of check box
                drawHangneigung = document.getElementById("mapOptHangneigung").checked;
                drawWanderwege = document.getElementById("mapOptWanderwege").checked;
                drawHaltestellen = document.getElementById("mapOptHaltestellen").checked;
                drawKantonsgrenzen = document.getElementById("mapOptKantonsgrenzen").checked;
                drawSacRegion = document.getElementById("mapOptSacAreas").checked;
                // Store state of checkbox (not working) 
                if ( drawSacRegion ) {
                    $('#mapOptSacAreas').prop('checked', true);
                };
                
                if (debug) { console.info('optionWhereStmt: ' + optionWhereStmt); };
                
                //optionWhereStmt = " WHERE wtypId = '4' ";
                callGenWaypKml(optionWhereStmt); // Generate KML file; file stored in file defined by global var segKmlFileNameURL 
                dialog.dialog( "close" );
                var removeEl = document.getElementById('mapPanel_Map-ResMap');  // Identify div to be removed
                var containerEl = removeEl.parentNode;          // Get its parent node
                containerEl.removeChild(removeEl);              // Remove the the child of the parent
                var newDiv = document.createElement('div');     // Create new div element
                containerEl.appendChild(newDiv);                // Append new div to parent node
                newDiv.id = 'mapPanel_Map-ResMap';              // Give new div an ID
                newDiv.className = 'mapPanel_Map-ResMap';       // Give new div a class
                $('#mapPanel_Map').addClass('visible');         // Make map panel visible
                drawMapOld('mapPanel_Map-ResMap', segKmlFileNameURL, waypKmlFileNameURL, 
                    drawHangneigung, drawWanderwege, drawHaltestellen, 
                    drawKantonsgrenzen, drawSacRegion); // Draw map to panel
            } 

            dialog = $( "#mapOptDialog" ).dialog({
                autoOpen: false,
                height: "auto",
                width: 600,
                modal: true,
                buttons: {
                    "Speichern": saveOptions,
                    Cancel: function() {
                    dialog.dialog( "close" );
                    }
                },
                close: function() {
                    form[ 0 ].reset();
                    //allFields.removeClass( "ui-state-error" );
                }
            });
            form = dialog.find( '#mapOptDialogForm' );
            
            dialog.dialog( "open" ); 
        });

    
//
// ----------- SEGMENTS --------
    // Makes the Filter appear or disappear
        $(this).on('click', '.segUserFilterControl', function(e) { // When click on a tab
            if (debug) { console.info(".segUserFilterControl: onclick function entered"); };
            e.preventDefault(); // Prevent link behaviour
        
            var $filterLink = $(this) // Store the current link
            var filterId = this.hash; // Get filter ID
            $filterPanel = $($filterLink.attr('href')); // Get current panel

            if (filterId && !$filterPanel.is('.visible')) { // if filter ID is available and a panel is not open
                $('#segPanelFilter').removeClass('visible'); // make panel not visible
                $filterPanel = $(filterId).addClass('visible'); // Make current panel visible
            } else {
                $filterPanel.removeClass('visible'); // Make current panel invisible           
            }
            if (debug) { console.info(".segUserFilterControl: onclick function completed"); };
        });

    // Executes code below when user click on pagination links
        $("#segPanel_List-ResList").on( "click", ".pagination a", function (e){
            e.preventDefault();
            if (debug) { console.info('#segPanel_List-ResList / pagination a on click function entered'); };
            $(".loading-div").show(); //show loading element
            var page = $(this).attr("data-page"); //get page number from link
            if (debug) { console.info('segSqlFilterString: ' + segSqlFilterString); };       
            $("#segPanel_List-ResList").load("seg_fetch_pages.php",{"sqlFilterString":segSqlFilterString,"page":page}, function(){ //get content from PHP page
                $(".loading-div").hide(); //once done, hide loading element
            });
            if (debug) { console.info("3/5: .#segPanel_List-ResList click function completed"); };
        });

    // Executes code below when user clicks the 'Apply' filter button
        $(document).on('click', '#segApplyFilterUser', function (e) {
            e.preventDefault();
            if (debug) { console.info('#applyFilter click function entered'); };      
            page = 1; // Set page number for display to 1
            
            // ===== Build SQL WHERE statement =====
            
            var veryFirst = true;  // flags that very first criteria has not yet been processed

            // Field segUF_sourceFID
            if ( ($('#segUF_sourceFID').val()) != "" ) {
                if ( veryFirst ) {
                    segSqlFilterString = " WHERE sourceFID in (";
                    segSqlFilterString += "'" + ($('#segUF_sourceFID').val()) + "')";
                    veryFirst = false;      
                } else if ( !veryFirst ) {
                    segSqlFilterString += " AND sourceFID in (";
                    segSqlFilterString += "'" + ($('#segUF_sourceFID').val()) + "')";    
                } 
            };

            // Field segUF_SourceRef
            var firstInCriteria = true;
            if ( ($('#segUF_SourceRef').val()) != "" ) {
                if ( veryFirst ) {
                    segSqlFilterString = " WHERE sourceRef like ";
                    segSqlFilterString += "'%" + ($('#segUF_SourceRef').val()) + "%'";      
                    veryFirst = false;
                } else if ( !veryFirst ) {
                    segSqlFilterString += " AND sourceRef like ";
                    segSqlFilterString += "'%" + ($('#segUF_SourceRef').val()) + "%'";    
                } 
            };

            // Field segType
            var firstInCriteria = true; // indicates that the first element of a criteria has not been processed yet
            $('#segUF_segType .ui-selected').each(function() {
                var recordId = this.id;
                var sqlName = "segType";
                var lenCriteria = recordId.length;
                var startCriteria = sqlName.length + 1;
                if ( veryFirst && firstInCriteria ) {
                    segSqlFilterString = " WHERE " + sqlName + " in (";
                    segSqlFilterString += "'" + recordId.slice(startCriteria,lenCriteria) + "'";
                    veryFirst = false;
                    firstInCriteria = false;     
                } else if (!veryFirst && !firstInCriteria) {
                    segSqlFilterString += ",'" + recordId.slice(startCriteria,lenCriteria) + "'";
                } else if (!veryFirst && firstInCriteria) {
                    segSqlFilterString += " AND " + sqlName + " in ('" + recordId.slice(startCriteria,lenCriteria) + "'";
                    firstInCriteria = false;
                }
            });
            if ( $('#segUF_segType .ui-selected').length > 0 ) {
                segSqlFilterString += ")";
            } 

            // Field segName
            var firstInCriteria = true;
            if ( ($('#segUF_segName:text').val()) != "" ) {
                if ( veryFirst ) {
                    segSqlFilterString = " WHERE segName like ";
                    segSqlFilterString += "'%" + ($('#segUF_segName:text').val()) + "%'";      
                    veryFirst = false;
                } else if ( !veryFirst ) {
                    segSqlFilterString += " AND segName like ";
                    segSqlFilterString += "'%" + ($('#segUF_segName:text').val()) + "%'";    
                } 
            };
                        
            // Field startLoc
            if ( ($('#segUF_startLocID').val()) != "" ) {
                if ( veryFirst ) {
                    segSqlFilterString = " WHERE segStartLocationFID in (";
                    segSqlFilterString += ($('#segUF_startLocID').val()) + ")";
                    veryFirst = false;      
                } else if ( !veryFirst ) {
                    segSqlFilterString += " AND segStartLocationFID in (";
                    segSqlFilterString += ($('#segUF_startLocID').val()) + ")";    
                } 
            };

            // Field startLocAlt
            if ( veryFirst ) {
                segSqlFilterString = " WHERE startLocAlt >= ";
                segSqlFilterString += $( "#segUF_startLocAlt_slider" ).slider( "values", 0 );
                segSqlFilterString += " AND startLocAlt <= ";
                segSqlFilterString += $( "#segUF_startLocAlt_slider" ).slider( "values", 1 );
                veryFirst = false;      
            } else if ( !veryFirst ) {
                segSqlFilterString += " AND startLocAlt >= ";
                segSqlFilterString += $( "#segUF_startLocAlt_slider" ).slider( "values", 0 );
                segSqlFilterString += " AND startLocAlt <= ";
                segSqlFilterString += $( "#segUF_startLocAlt_slider" ).slider( "values", 1 );    
            }            

            // Field startLocType
            var firstInCriteria = true;
            $('#segUF_startLocType .ui-selected').each(function() {
                var recordId = this.id;
                var sqlName = "startLocType";
                var lenCriteria = recordId.length;
                var startCriteria = sqlName.length + 1;
                if ( veryFirst && firstInCriteria ) {
                    segSqlFilterString = " WHERE " + sqlName + " in (";
                    segSqlFilterString += "'" + recordId.slice(startCriteria,lenCriteria) + "'";  
                    veryFirst = false;
                    firstInCriteria = false;    
                } else if (!veryFirst && !firstInCriteria) {
                    segSqlFilterString += ",'" + recordId.slice(startCriteria,lenCriteria) + "'";
                } else if (!veryFirst && firstInCriteria) {
                    segSqlFilterString += " AND " + sqlName + " in ('" + recordId.slice(startCriteria,lenCriteria) + "'";
                    firstInCriteria = false;
                }
            });
            if ( $('#segUF_startLocType .ui-selected').length >0 ) {
                segSqlFilterString += ")";
            }

            // Field targetLoc
            if ( ($('#segUF_targetLocID').val()) != "" ) {
                if ( veryFirst ) {
                    segSqlFilterString = " WHERE segTargetLocationFID in (";
                    segSqlFilterString += ($('#segUF_targetLocID').val()) + ")";
                    veryFirst = false;      
                } else if ( !veryFirst ) {
                    segSqlFilterString += " AND segTargetLocationFID in (";
                    segSqlFilterString += ($('#segUF_targetLocID').val()) + ")";    
                } 
            };
            
            // Field targetLocAlt
            if ( veryFirst ) {
                segSqlFilterString = " WHERE targetLocAlt >= ";
                segSqlFilterString += $( "#segUF_targetLocAlt_slider" ).slider( "values", 0 );
                segSqlFilterString += " AND targetLocAlt <= ";
                segSqlFilterString += $( "#segUF_targetLocAlt_slider" ).slider( "values", 1 );
                veryFirst = false;      
            } else if ( !veryFirst ) {
                segSqlFilterString += " AND targetLocAlt >= ";
                segSqlFilterString += $( "#segUF_targetLocAlt_slider" ).slider( "values", 0 );
                segSqlFilterString += " AND targetLocAlt <= ";
                segSqlFilterString += $( "#segUF_targetLocAlt_slider" ).slider( "values", 1 );    
            }            

            // Field targetLocType
            var firstInCriteria = true;
            $('#segUF_targetLocType .ui-selected').each(function() {
                var recordId = this.id;  
                var sqlName = "targetLocType";
                var lenCriteria = recordId.length;
                var startCriteria = sqlName.length + 1;
                if ( veryFirst && firstInCriteria ) {
                    segSqlFilterString = " WHERE " + sqlName + " in (";
                    segSqlFilterString += "'" + recordId.slice(startCriteria,lenCriteria) + "'";  
                    veryFirst = false;    
                    firstInCriteria = false;
                } else if (!veryFirst && !firstInCriteria) {
                    segSqlFilterString += ",'" + recordId.slice(startCriteria,lenCriteria) + "'";
                } else if (!veryFirst && firstInCriteria) {
                    segSqlFilterString += " AND " + sqlName + " in ('" + recordId.slice(startCriteria,lenCriteria) + "'";
                    firstInCriteria = false;
                }               
            });
            if ( $('#segUF_targetLocType .ui-selected').length >0 ) {
                segSqlFilterString += ")";
            }

            // Field region
            if ( ($('#segUF_region').val()) != "" ) {
                if ( veryFirst ) {
                    segSqlFilterString = " WHERE regionId in (";
                    segSqlFilterString += ($('#segUF_regionID').val()) + ")"; 
                    veryFirst = false;     
                } else if ( !veryFirst ) {
                    segSqlFilterString += " AND regionId in (";
                    segSqlFilterString += ($('#segUF_regionID').val()) + ")";    
                } 
            };

            // Field area
            if ( ($('#segUF_area').val()) != "" ) {
                if ( veryFirst ) {
                    segSqlFilterString = " WHERE areaId in(";
                    segSqlFilterString += ($('#segUF_areaID').val()) + ")";  
                    veryFirst = false;    
                } else if ( !veryFirst ) {
                    segSqlFilterString += " AND areaId in (";
                    segSqlFilterString += ($('#segUF_areaID').val()) + ")";    
                } 
            };

            // Field grade
            var firstInCriteria = true;
            $('#segUF_grade .ui-selected').each(function() {
                var recordId = this.id;
                var sqlName = "grade";
                var lenCriteria = recordId.length;
                var startCriteria = sqlName.length + 1;
                if ( veryFirst && firstInCriteria ) {
                    segSqlFilterString = " WHERE " + sqlName + " in (";
                    segSqlFilterString += "'" + recordId.slice(startCriteria,lenCriteria) + "'"; 
                    veryFirst = false;     
                    firstInCriteria = false;
                } else if (!veryFirst && !firstInCriteria) {
                    segSqlFilterString += ",'" + recordId.slice(startCriteria,lenCriteria) + "'";
                } else if (!veryFirst && firstInCriteria) {
                    segSqlFilterString += " AND " + sqlName + " in ('" + recordId.slice(startCriteria,lenCriteria) + "'";
                    firstInCriteria = false;
                }
            });
            if ( $('#segUF_grade .ui-selected').length >0 ) {
                segSqlFilterString += ")";
            }

            // Field climbGrade
            var firstInCriteria = true;
            $('#segUF_climbGrade .ui-selected').each(function() {
                var recordId = this.id;
                var sqlName = "climbGrade";
                var lenCriteria = recordId.length;
                var startCriteria = sqlName.length + 1;
                if ( veryFirst && firstInCriteria ) {
                    segSqlFilterString = " WHERE " + sqlName + " in (";
                    segSqlFilterString += "'" + recordId.slice(startCriteria,lenCriteria) + "'";   
                    veryFirst = false;   
                    firstInCriteria = false;
                } else if (!veryFirst && !firstInCriteria) {
                    segSqlFilterString += ",'" + recordId.slice(startCriteria,lenCriteria) + "'";
                } else if (!veryFirst && firstInCriteria) {
                    segSqlFilterString += " AND " + sqlName + " in ('" + recordId.slice(startCriteria,lenCriteria) + "'";
                    firstInCriteria = false;
                }
            });
            if ( $('#segUF_climbGrade .ui-selected').length >0 ) {
                segSqlFilterString += ")";
            }

            // Field ehaft
            var firstInCriteria = true;
            $('#segUF_ehaft .ui-selected').each(function() {
                var recordId = this.id;
                var sqlName = "ehaft";
                var lenCriteria = recordId.length;
                var startCriteria = sqlName.length + 1;
                if ( veryFirst && firstInCriteria ) {
                    segSqlFilterString = " WHERE " + sqlName + " in (";
                    segSqlFilterString += "'" + recordId.slice(startCriteria,lenCriteria) + "'";    
                    veryFirst = false;  
                    firstInCriteria = false;
                } else if (!veryFirst && !firstInCriteria) {
                    segSqlFilterString += ",'" + recordId.slice(startCriteria,lenCriteria) + "'";
                } else if (!veryFirst && firstInCriteria) {
                    segSqlFilterString += " AND " + sqlName + " in ('" + recordId.slice(startCriteria,lenCriteria) + "'";
                    firstInCriteria = false;
                }
            });
            if ( $('#segUF_ehaft .ui-selected').length >0 ) {
                segSqlFilterString += ")";
            }

            // ============ fetch_pages + generate KML ============
            
            if (debug) { console.info('segSqlFilterString: ' + segSqlFilterString); };
            if (debug) { console.info('page: ' + page); };
            $("#segPanel_List-ResList").load("seg_fetch_pages.php",{"sqlFilterString":segSqlFilterString,"page":page}); //get content from PHP page
            callGenSegKml(segSqlFilterString); // Generate KML file; file stored in file defined by global var segKmlFileNameURL 

            // Close filter panels at the end
            $('#segPanelFilter').removeClass('visible');

            if (debug) { console.info('#segApplyFilter click function completed'); }; 
        });
        
    
//
// ----------- WAYPOINTS -------        
    // Makes the Filter appear or disappear
        $(this).on('click', '.waypUserFilterControl', function(e) { // When click on a tab
            if (debug) { console.info(".waypUserFilterControl: onclick function entered"); };
            e.preventDefault(); // Prevent link behaviour
        
            var $filterLink = $(this) // Store the current link
            var filterId = this.hash; // Get filter ID
            $filterPanel = $($filterLink.attr('href')); // Get current panel

            if (filterId && !$filterPanel.is('.visible')) { // if filter ID is available and a panel is not open
                $('#waypPanelFilter').removeClass('visible'); // make panel not visible
                $filterPanel = $(filterId).addClass('visible'); // Make current panel visible
            } else {
                $filterPanel.removeClass('visible'); // Make current panel invisible           
            }
            if (debug) { console.info(".waypUserFilterControl: onclick function completed"); };
        }); 
        
    // Executes code below when user click on pagination links
        $("#waypPanel_List-ResList").on( "click", ".pagination a", function (e){
            e.preventDefault();
            if (debug) { console.info('#waypPanel_List-ResList / pagination a on click function entered'); };
            $(".loading-div").show(); //show loading element
            var page = $(this).attr("data-page"); //get page number from link
            if (debug) { console.info('waypSqlFilterString: ' + waypSqlFilterString); };       
            $("#waypPanel_List-ResList").load("wayp_fetch_pages.php",{"sqlFilterString":waypSqlFilterString,"page":page}, function(){ //get content from PHP page
                $(".loading-div").hide(); //once done, hide loading element
            });
            if (debug) { console.info("3/5: .#waypPanel_List-ResList click function completed"); };
        });

    // Executes code below when user clicks the 'Apply' filter button
        $(document).on('click', '#waypApplyFilterUser', function (e) {
            e.preventDefault();
            if (debug) { console.info('#waypApplyFilterUser click function entered'); };      
            page = 1; // Set page number for display to 1
            
            var veryFirst = true;        // Special formatting ing SQL String applies for very first criteria
            var firstInCriteria = true;  // Special formatting ing SQL String applies for first element in criteria

            // Field waypUF_wtypCode
            $('#waypUF_wtypCode .ui-selected').each(function() {
                var recordId = this.id;
                var sqlName = "wtypCode";
                var lenCriteria = recordId.length;
                var startCriteria = sqlName.length + 1;
                if ( veryFirst && firstInCriteria ) {
                    waypSqlFilterString = " WHERE " + sqlName + " in (";
                    waypSqlFilterString += "'" + recordId.slice(startCriteria,lenCriteria) + "'";
                    veryFirst = false;
                    firstInCriteria = false;     
                } else if (!veryFirst && !firstInCriteria) {
                    waypSqlFilterString += ",'" + recordId.slice(startCriteria,lenCriteria) + "'";
                } else if (!veryFirst && firstInCriteria) {
                    waypSqlFilterString += " AND " + sqlName + " in ('" + recordId.slice(startCriteria,lenCriteria) + "'";
                    firstInCriteria = false;
                }
            });
            if ( $('#waypUF_wtypCode .ui-selected').length > 0 ) {
                waypSqlFilterString += ")";
            } 

            // Field waypNameShort
            if ( ($('#waypUF_waypNameLong:text').val()) != "" ) {
                if ( veryFirst ) {
                    waypSqlFilterString = " WHERE waypNameShort like ";
                    waypSqlFilterString += "'%" + ($('#waypUF_waypNameLong:text').val()) + "%'";      
                    veryFirst = false;
                } else if ( !veryFirst ) {
                    waypSqlFilterString += " AND waypNameShort like ";
                    waypSqlFilterString += "'%" + ($('#waypUF_waypNameLong:text').val()) + "%'";    
                } 
            }; 

            // Field waypUF_region 
            if ( ($('#waypFilter_regionID').val()) != "" ) {
                if ( veryFirst ) {
                    waypSqlFilterString = " WHERE regId = ";
                    waypSqlFilterString += ($('#waypFilter_regionID').val());      
                    veryFirst = false;
                } else if ( !veryFirst ) {
                    waypSqlFilterString += " AND regId = ";
                    waypSqlFilterString += ($('#waypFilter_regionID').val());    
                } 
            }; 

            // Field waypUF_area 
            if ( ($('#waypFilter_areaID').val()) != "" ) {
                if ( veryFirst ) {
                    waypSqlFilterString = " WHERE areaId = ";
                    waypSqlFilterString += ($('#waypFilter_areaID').val());      
                    veryFirst = false;
                } else if ( !veryFirst ) {
                    waypSqlFilterString += " AND areaId = ";
                    waypSqlFilterString += ($('#waypFilter_areaID').val());  
                } 
            }; 

            // Field waypoint altitude
            if ( veryFirst ) {
                waypSqlFilterString = " WHERE waypAltitude >= ";
                waypSqlFilterString += $( "#waypUF_alt_slider" ).slider( "values", 0 );
                waypSqlFilterString += " AND waypAltitude <= ";
                waypSqlFilterString += $( "#waypUF_alt_slider" ).slider( "values", 1 );
                veryFirst = false;      
            } else if ( !veryFirst ) {
                waypSqlFilterString += " AND waypAltitude >= ";
                waypSqlFilterString += $( "#waypUF_alt_slider" ).slider( "values", 0 );
                waypSqlFilterString += " AND waypAltitude <= ";
                waypSqlFilterString += $( "#waypUF_alt_slider" ).slider( "values", 1 );    
            }          

            if (debug) { console.info('waypSqlFilterString: ' + waypSqlFilterString); };
            if (debug) { console.info('page: ' + page); };
            $("#waypPanel_List-ResList").load("wayp_fetch_pages.php",{"sqlFilterString":waypSqlFilterString,"page":page}); //get content from PHP page
            $('#waypPanelFilter').removeClass('visible'); // make panel not visible
            callGenWaypKml(waypSqlFilterString); // Generate KML file; file stored in file defined by global var segKmlFileNameURL 
            waypLoadMap = true; // Maps needs to be newly loaded
            waypLoadListMap = true;
            if (debug) { console.info('#waypApplyFilter click function completed'); }; 
        }); 
    

        
        if (debug) { console.info("ready function completed"); };

        // ===================================
        // ======== R O U T E S ==============
        // ===================================

        


//
// ----------- TOUREN ----------
    // Makes the general Segment Filter appear or disappear
        $(this).on('click', '.tourenUserFilterControl', function(e) { 
            if (debug) { console.info(".tourenUserFilterControl: onclick function entered"); };
            e.preventDefault(); // Prevent link behaviour

            $('#tourPanelInfo').removeClass('visible'); // make into panel not visible
            
            var $filterLink = $(this) // Store the current link
            var filterId = this.hash; // Get filter ID
            $filterPanel = $($filterLink.attr('href')); // Get current panel

            if (filterId && !$filterPanel.is('.visible')) { // if filter ID is available and a panel is not open
                $('#tourenPanelFilter').removeClass('visible'); // make panel not visible
                $filterPanel = $(filterId).addClass('visible'); // Make current panel visible
            } else {
                $filterPanel.removeClass('visible'); // Make current panel invisible           
            }
            if (debug) { console.info(".tourenUserFilterControl: onclick function completed"); };
        });

    // Executes code below when user clicks the 'Apply' filter button
        $(document).on('click', '#tourApplyFilterUser', function (e) {
            e.preventDefault();
            
            if (debug) { console.info('#mapApplyFilter click function entered'); };      
            
            // Build SQL WHERE statement for segments =====

                var veryFirst = true;  // flags that very first criteria has not yet been processed
                
                // Field segUF_sourceName
                if ( ($('#tourUF_sourceFID').val()) != "" ) {
                    if ( veryFirst ) {
                        tourSqlFilterString = "WHERE sourceFID in (";
                        tourSqlFilterString += "'" + ($('#tourUF_sourceFID').val()) + "')";
                        veryFirst = false;      
                    } else if ( !veryFirst ) {
                        tourSqlFilterString += " AND sourceFID in (";
                        tourSqlFilterString += "'" + ($('#tourUF_sourceFID').val()) + "')";    
                    } 
                };

                // Field tourUF_sourceRef
                var firstInCriteria = true;
                if ( ($('#tourUF_sourceRef').val()) != "" ) {
                    if ( veryFirst ) {
                        tourSqlFilterString = "WHERE sourceRef like ";
                        tourSqlFilterString += "'%" + ($('#tourUF_sourceRef').val()) + "%'";      
                        veryFirst = false;
                    } else if ( !veryFirst ) {
                        tourSqlFilterString += " AND sourceRef like ";
                        tourSqlFilterString += "'%" + ($('#tourUF_sourceRef').val()) + "%'";    
                    } 
                };

                // Field segType
                var firstInCriteria = true; // indicates that the first element of a criteria has not been processed yet
                $('#tourUF_segType .ui-selected').each(function() {
                    // var recordId = this.id;
                    var sqlName = "segType";
                    // var lenCriteria = recordId.length;
                    // var startCriteria = sqlName.length + 1;
                    var criteria = $( this ).attr("tourID")
                    if ( veryFirst && firstInCriteria ) {
                        tourSqlFilterString = "WHERE " + sqlName + " in (";
                        tourSqlFilterString += "'" + criteria + "'";
                        veryFirst = false;
                        firstInCriteria = false;     
                    } else if (!veryFirst && !firstInCriteria) {
                        tourSqlFilterString += ",'" + criteria + "'";
                    } else if (!veryFirst && firstInCriteria) {
                        tourSqlFilterString += " AND " + sqlName + " in ('" + criteria + "'";
                        firstInCriteria = false;
                    }
                });
                if ( $('#tourUF_segType .ui-selected').length > 0 ) {
                    tourSqlFilterString += ")";
                } 

                // Field segName
                var firstInCriteria = true;
                if ( ($('#tourUF_segName:text').val()) != "" ) {
                    if ( veryFirst ) {
                        tourSqlFilterString = "WHERE segName like ";
                        tourSqlFilterString += "'%" + ($('#tourUF_segName:text').val()) + "%'";      
                        veryFirst = false;
                    } else if ( !veryFirst ) {
                        tourSqlFilterString += " AND segName like ";
                        tourSqlFilterString += "'%" + ($('#tourUF_segName:text').val()) + "%'";    
                    } 
                };
                            
                // Field startLoc
                if ( ($('#tourUF_startLocID').val()) != "" ) {
                    if ( veryFirst ) {
                        tourSqlFilterString = "WHERE segStartLocationFID in (";
                        tourSqlFilterString += ($('#tourUF_startLocID').val()) + ")";
                        veryFirst = false;      
                    } else if ( !veryFirst ) {
                        tourSqlFilterString += " AND segStartLocationFID in (";
                        tourSqlFilterString += ($('#tourUF_startLocID').val()) + ")";    
                    } 
                };

                // Field startLocAlt
                if ( veryFirst ) {
                    tourSqlFilterString = "WHERE startLocAlt >= ";
                    tourSqlFilterString += $( "#tourUF_startLocAlt_slider" ).slider( "values", 0 );
                    tourSqlFilterString += " AND startLocAlt <= ";
                    tourSqlFilterString += $( "#tourUF_startLocAlt_slider" ).slider( "values", 1 );
                    veryFirst = false;      
                } else if ( !veryFirst ) {
                    tourSqlFilterString += " AND startLocAlt >= ";
                    tourSqlFilterString += $( "#tourUF_startLocAlt_slider" ).slider( "values", 0 );
                    tourSqlFilterString += " AND startLocAlt <= ";
                    tourSqlFilterString += $( "#tourUF_startLocAlt_slider" ).slider( "values", 1 );    
                }            

                // Field startLocType
                var firstInCriteria = true;
                $('#tourUF_startLocType .ui-selected').each(function() {
                    //var recordId = this.id;
                    var sqlName = "startLocType";
                    //var lenCriteria = recordId.length;
                    //var startCriteria = sqlName.length + 1;
                    var criteria = $( this ).attr("tourID")
                    if ( veryFirst && firstInCriteria ) {
                        tourSqlFilterString = "WHERE " + sqlName + " in (";
                        tourSqlFilterString += "'" + criteria + "'";  
                        veryFirst = false;
                        firstInCriteria = false;    
                    } else if (!veryFirst && !firstInCriteria) {
                        tourSqlFilterString += ",'" + criteria + "'";
                    } else if (!veryFirst && firstInCriteria) {
                        tourSqlFilterString += " AND " + sqlName + " in ('" + criteria + "'";
                        firstInCriteria = false;
                    }
                });
                if ( $('#tourUF_startLocType .ui-selected').length >0 ) {
                    tourSqlFilterString += ")";
                }

                // Field targetLoc
                if ( ($('#tourUF_targetLocID').val()) != "" ) {
                    if ( veryFirst ) {
                        tourSqlFilterString = "WHERE segTargetLocationFID in (";
                        tourSqlFilterString += ($('#tourUF_targetLocID').val()) + ")";
                        veryFirst = false;      
                    } else if ( !veryFirst ) {
                        tourSqlFilterString += " AND segTargetLocationFID in (";
                        tourSqlFilterString += ($('#tourUF_targetLocID').val()) + ")";    
                    } 
                };

                // Field targetLocAlt
                if ( veryFirst ) {
                    tourSqlFilterString = "WHERE targetLocAlt >= ";
                    tourSqlFilterString += $( "#tourUF_targetLocAlt_slider" ).slider( "values", 0 );
                    tourSqlFilterString += " AND targetLocAlt <= ";
                    tourSqlFilterString += $( "#tourUF_targetLocAlt_slider" ).slider( "values", 1 );
                    veryFirst = false;      
                } else if ( !veryFirst ) {
                    tourSqlFilterString += " AND targetLocAlt >= ";
                    tourSqlFilterString += $( "#tourUF_targetLocAlt_slider" ).slider( "values", 0 );
                    tourSqlFilterString += " AND targetLocAlt <= ";
                    tourSqlFilterString += $( "#tourUF_targetLocAlt_slider" ).slider( "values", 1 );    
                }            

                // Field targetLocType
                var firstInCriteria = true;
                $('#tourUF_targetLocType .ui-selected').each(function() {
                    //var recordId = this.id;
                    var sqlName = "targetLocType";
                    //var lenCriteria = recordId.length;
                    //var startCriteria = sqlName.length + 1;
                    var criteria = $( this ).attr("tourID")
                    if ( veryFirst && firstInCriteria ) {
                        tourSqlFilterString = "WHERE " + sqlName + " in (";
                        tourSqlFilterString += "'" + criteria + "'";  
                        veryFirst = false;    
                        firstInCriteria = false;
                    } else if (!veryFirst && !firstInCriteria) {
                        tourSqlFilterString += ",'" + criteria + "'";
                    } else if (!veryFirst && firstInCriteria) {
                        tourSqlFilterString += " AND " + sqlName + " in ('" + criteria + "'";
                        firstInCriteria = false;
                    }               
                });
                if ( $('#tourUF_targetLocType .ui-selected').length >0 ) {
                    tourSqlFilterString += ")";
                }

                // Field region
                if ( ($('#tourUF_segRegionID').val()) != "" ) {
                    if ( veryFirst ) {
                        tourSqlFilterString = "WHERE regionId in (";
                        tourSqlFilterString += ($('#tourUF_segRegionID').val()) + ")"; 
                        veryFirst = false;     
                    } else if ( !veryFirst ) {
                        tourSqlFilterString += " AND regionId in (";
                        tourSqlFilterString += ($('#tourUF_segRegionID').val()) + ")";    
                    } 
                };

                // Field area
                if ( ($('#tourUF_segAreaID').val()) != "" ) {
                    if ( veryFirst ) {
                        tourSqlFilterString = "WHERE areaId in(";
                        tourSqlFilterString += ($('#tourUF_segAreaID').val()) + ")";  
                        veryFirst = false;    
                    } else if ( !veryFirst ) {
                        tourSqlFilterString += " AND areaId in (";
                        tourSqlFilterString += ($('#tourUF_segAreaID').val()) + ")";    
                    } 
                };

                // Field grade
                var firstInCriteria = true;
                $('#tourUF_grade .ui-selected').each(function() {
                    //var recordId = this.id;
                    var sqlName = "grade";
                    //var lenCriteria = recordId.length;
                    //var startCriteria = sqlName.length + 1;
                    var criteria = $( this ).attr("tourID")
                    if ( veryFirst && firstInCriteria ) {
                        tourSqlFilterString = "WHERE " + sqlName + " in (";
                        tourSqlFilterString += "'" + criteria + "'"; 
                        veryFirst = false;     
                        firstInCriteria = false;
                    } else if (!veryFirst && !firstInCriteria) {
                        tourSqlFilterString += ",'" + criteria + "'";
                    } else if (!veryFirst && firstInCriteria) {
                        tourSqlFilterString += " AND " + sqlName + " in ('" + criteria + "'";
                        firstInCriteria = false;
                    }
                });
                if ( $('#tourUF_grade .ui-selected').length >0 ) {
                    tourSqlFilterString += ")";
                }

                // Field climbGrade
                var firstInCriteria = true;
                $('#tourUF_climbGrade .ui-selected').each(function() {
                    // var recordId = this.id;
                    var sqlName = "climbGrade";
                    //var lenCriteria = recordId.length;
                    //var startCriteria = sqlName.length + 1;
                    var criteria = $( this ).attr("tourID")
                    if ( veryFirst && firstInCriteria ) {
                        tourSqlFilterString = "WHERE " + sqlName + " in (";
                        tourSqlFilterString += "'" + criteria + "'";   
                        veryFirst = false;   
                        firstInCriteria = false;
                    } else if (!veryFirst && !firstInCriteria) {
                        tourSqlFilterString += ",'" + criteria + "'";
                    } else if (!veryFirst && firstInCriteria) {
                        tourSqlFilterString += " AND " + sqlName + " in ('" + criteria + "'";
                        firstInCriteria = false;
                    }
                });
                if ( $('#tourUF_climbGrade .ui-selected').length >0 ) {
                    tourSqlFilterString += ")";
                }

                // Field ehaft
                var firstInCriteria = true;
                $('#tourUF_ehaft .ui-selected').each(function() {
                    var recordId = this.id;
                    var sqlName = "ehaft";
                    var lenCriteria = recordId.length;
                    var startCriteria = sqlName.length + 1;
                    if ( veryFirst && firstInCriteria ) {
                        tourSqlFilterString = "WHERE " + sqlName + " in (";
                        tourSqlFilterString += "'" + recordId.slice(startCriteria,lenCriteria) + "'";    
                        veryFirst = false;  
                        firstInCriteria = false;
                    } else if (!veryFirst && !firstInCriteria) {
                        tourSqlFilterString += ",'" + recordId.slice(startCriteria,lenCriteria) + "'";
                    } else if (!veryFirst && firstInCriteria) {
                        tourSqlFilterString += " AND " + sqlName + " in ('" + recordId.slice(startCriteria,lenCriteria) + "'";
                        firstInCriteria = false;
                    }
                });
                if ( $('#tourUF_ehaft .ui-selected').length >0 ) {
                    tourSqlFilterString += ")";
                }
                if (debug) { console.info('#tourSqlFilterString ' + tourSqlFilterString ); };

            // Get segments from DB and draw map
                //if ( tourFetchPagesHasRun == false ) {
                var xhr = new XMLHttpRequest();
                xhr.onload = function() {
                    if (xhr.status === 200) {
                        responseObject = JSON.parse(xhr.responseText);

                        // Outsource as function
                        var panelTourSegTable = '';
                        panelTourSegTable += '<table>';
                        panelTourSegTable += '<tr class="header">';
                        panelTourSegTable += '<th>Sel.</th>';                           // 1
                        panelTourSegTable += '<th>Name</th>';                           // 2
                        panelTourSegTable += '<th>Type</th>';                           // 3
                        panelTourSegTable += '<th>SGrad</th>';                          // 4
                        panelTourSegTable += '<th>Zeit</th>';                           // 5
                        panelTourSegTable += '<th>Hm+</th>';                            // 6
                        panelTourSegTable += '<th>Ref</th>';                                // 7
                        panelTourSegTable += '</tr>';

                        for (var i = 0; i < responseObject.length; i++) {
                            panelTourSegTable += '<tr>';  
                            panelTourSegTable += '<td><input type="checkbox" name="segid_'
                                                    + responseObject[i].segKmlId 
                                                    + '" id="segId_' + i
                                                    + '" class="tourSel"></td>'; 
                            panelTourSegTable += '<td>' + responseObject[i].segName + '</td>';               // 1
                            panelTourSegTable += '<td>' + responseObject[i].segType + '/' + responseObject[i].segKmlId + '</td>';               // 2
                            panelTourSegTable += '<td>' + responseObject[i].grade + '/' +
                                                    responseObject[i].climbGrade + '</td>';
                            panelTourSegTable += '<td>' + responseObject[i].tStartTarget + '</td>';          // 4
                            panelTourSegTable += '<td>' + responseObject[i].mUStartTarget + '</td>';        
                            panelTourSegTable += '<td>' + responseObject[i].source + '</td>';          // 6
                            panelTourSegTable += '</tr>';

                            var segDisplayArrayLine = [responseObject[i].segKmlId, 1, 0, responseObject[i].hasCoordinates];
                            segDisplayArray.push(segDisplayArrayLine);     
                        };
                    
                        // Write remainder of HTML code
                        panelTourSegTable += '</table>';                        
                    }

                    document.getElementById('panelTourSeg-content').innerHTML = panelTourSegTable;
                    // Function called when check box in Verfügbare Segmente is checked/unchecked
                    $( '.tourSel' ).change(function() {
                        arrayId = $(this).attr('id');       // arrayId is the id of the segment in the responseObject array
                        arrayId = arrayId.substring(6, arrayId.length);
                        
                        if ( $(this).prop("checked") ) {
                            createSortable ( arrayId );
                            /*
                            console.info("Checked " + arrayId );
                            sortableString  = '<li id="sorLi_' + arrayId + '" class="ui-state-default">';
                            sortableString += '<a id="sorA_' + arrayId;
                            sortableString += '" class="tourDelSortable" href="#sorH_' + arrayId + '"><img src="images/delete.png"></a>';
                            sortableString += responseObject[arrayId].segName + ' | ';
                            sortableString += responseObject[arrayId].segType + ' | ';
                            sortableString += responseObject[arrayId].grade + '/' +  responseObject[arrayId].climbGrade + ' | ';
                            sortableString += responseObject[arrayId].tStartTarget + ' | ';
                            sortableString += responseObject[arrayId].mUStartTarget + ' | ';
                            sortableString += responseObject[arrayId].source ;
                            sortableString += '</li>';
                            $( '#panelTourTour-sortable' ).append(sortableString);
                            segDisplayArray[i][2] = 1;
                            */
                        } else {
                            console.info("UNChecked " + arrayId );
                            var removeId = "sorLi_" + arrayId ;
                            var removeEl = document.getElementById(removeId);  // Identify div to be removed
                            var containerEl = removeEl.parentNode;          // Get its parent node
                            containerEl.removeChild(removeEl);              // Remove the the child of the parent
                            var uncheckSegId = "segId_" + arrayId ; 
                            $(uncheckSegId).prop("unchecked");
                            for (var i = 0; i < segDisplayArray.length; i++) {
                                if ( i == arrayId) {
                                    segDisplayArray[i][2] = 0;
                                }
                            }
                        }
                        /*
                        ==> FOR DEBUGGIING
                        for (var i = 0; i < segDisplayArray.length; i++) {
                                if ( segDisplayArray[i][2] == 1 ) {
                                    console.info("Currently selected routes: " + segDisplayArray[i][0]); 
                                }
                            }
                        */
                    });

                    // Draw map
                    var removeEl = document.getElementById('panelTourenMap-Map');  // Identify div to be removed
                    var containerEl = removeEl.parentNode;          // Get its parent node
                    containerEl.removeChild(removeEl);              // Remove the the child of the parent
                    var newDiv = document.createElement('div');     // Create new div element
                    containerEl.appendChild(newDiv);                // Append new div to parent node
                    newDiv.id = 'panelTourenMap-Map';              // Give new div an ID
                    newDiv.className = 'panelTourenMap-Map';       // Give new div a class
                    drawMapSingle('panelTourenMap-Map', segDisplayArray);         // Draw empty map (without additional layers)                
                    // ??? mapMapNeedsLoad = false;
                }
                var phpLocation = document.URL + "tour_fetch_pages.php";          // Variable to store location of php file
                var xhrParams = "&sqlFilterString=" + tourSqlFilterString;
                xhr.open ('POST', phpLocation, true);                // Make XMLHttpRequest 
                //xhr.open ('GET', phpLocation, true);                // Make XMLHttpRequest 
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");  // Set header to encode special characters like %
                xhr.send(encodeURI(xhrParams)); 
                //tourFetchPagesHasRun = true;   
                //}
                $( '#tourenPanelFilter' ).removeClass('visible'); // Make current panel invisible
            if (debug) { console.info('#tourApplyFilter click function completed'); }; 
        });
        
        // On click on delete icon in sortable
            $(document).on('click', '.tourDelSortable', function (e) {
                e.preventDefault();
                var $sortableLink = this.hash.substring(6, this.hash.length); // Store the current link
                console.info("UNChecked on selectable " + $sortableLink );
                var removeId = "sorLi_" + $sortableLink ;
                var removeEl = document.getElementById(removeId);  // Identify div to be removed
                var containerEl = removeEl.parentNode;          // Get its parent node
                containerEl.removeChild(removeEl);              // Remove the the child of the parent
                var uncheckSegId = '#segId_' + $sortableLink ; 
                $( uncheckSegId ).prop('checked', false);
                for (var i = 0; i < segDisplayArray.length; i++) {
                    if ( i == $sortableLink) {
                        segDisplayArray[i][2] = 0;
                    }
                }
            })    
                
//
// ----------- ADMIN -------
    // Generate single KML files - admin function 
        $(document).on('click', '#segGenKml', function (e) {
            var xhr = new XMLHttpRequest();
            phpLocation = document.URL + "seg_gen_single_kml.php";          // Variable to store location of php file
            xhr.open ('POST', phpLocation, true);                // Make XMLHttpRequest - in asynchronous mode to avoid wrong data display in map (map displayed before KML file is updated)
            xhr.send(encodeURI());            
            console.info("seg_gen_single_kml.php completed");
        });

    // Calculate WGS84 coordinates - admin function
        $(document).on('click', '#waypCalcWgs84', function (e) {
            var xhr = new XMLHttpRequest();
            phpLocation = document.URL + "wayp_calc_WGS84.php";          // Variable to store location of php file
            xhr.open ('POST', phpLocation, true);                // Make XMLHttpRequest - in asynchronous mode to avoid wrong data display in map (map displayed before KML file is updated)
            xhr.send(encodeURI());         
            console.info("Function waypCalcWgs84 completed");
        });

    
    // Create Add Waypoint Dialog Box
        // Source from JQuery UI home page - dialog
        $( function() {
            var dialog, form;
            
            // waypDialogNameShort
            $( "#waypDialogNameShort" ).autocomplete({
                source: "get_auto_complete_values.php?field=getWaypShort",
                minLength: 2,
            });
            var waypDialogNameShort = $( "#waypDialogNameShort" );

            // waypDialogNameLong
            $( "#waypDialogNameLong" ).autocomplete({
                source: "get_auto_complete_values.php?field=getWaypLong",
                minLength: 2,
            });
            var waypDialogNameLong = $( "#waypDialogNameLong" );

            var waypDialogTypeCode = $( "#waypDialogTypeCode" );

            var waypDialogCountry = $( "#waypDialogCountry" );

            // Field waypDialogCanton
            $( "#waypDialogCanton" ).autocomplete({
                source: cantons
                // TASK: typing shows available options in dropdown
            });
            var waypDialogCanton = $( "#waypDialogCanton" );
            
            $( "#waypDialogRegion" ).autocomplete({
                source: "get_auto_complete_values.php?field=regionID",
                minLength: 2,
                select: function( event, ui ) {
                    $( "#waypRegionID" ).val( ui.item.id );
                },
                change: function( event, ui ) {
                    if ( $( "#waypDialogRegion" ).val() == '' ) {
                        $( "#waypRegionID" ).val( '' );
                    }
                }
            });
            var waypRegionID = $( "#waypRegionID" ); // --> Region is not stored in Waypoints

            $( "#waypDialogArea" ).autocomplete({
                source: "get_auto_complete_values.php?field=areaID",
                minLength: 2,
                select: function( event, ui ) {
                    $( "#waypAreaID" ).val( ui.item.id );
                },
                change: function( event, ui ) {
                    if ( $( "#waypDialogArea" ).val() == '' ) {
                        $( "#waypAreaID" ).val( '' );
                    }
                }
            });
            var waypAreaID = $( "#waypAreaID" );
            
            var waypDialogAltitude = $( "#waypDialogAltitude" ),
                waypCoordLV3Est = $( "#waypCoordLV3Est" ),
                waypCoordLV3Nord = $( "#waypCoordLV3Nord" ),
                waypDialogOwner = $( "#waypDialogOwner" ),
                waypDialogWebsite = $( "#waypDialogWebsite" ),
                waypDialogRemarks = $( "#waypDialogRemarks" )

            waypAllFields = $( [] ).add( waypDialogNameShort ).add( waypDialogNameLong ).add( waypDialogTypeCode ).add( waypDialogCountry ).add( waypDialogArea ).add( waypDialogRegion ).add( waypDialogAltitude ).add( waypCoordLV3Est ).add( waypCoordLV3Nord ).add( waypDialogOwner ).add ( waypDialogWebsite ).add( waypDialogRemarks ),
            waypTips = $( ".waypValidateTips" );

            // Move to function area
            function addWaypoint() {
                tips = waypTips; 
                var valid = true;
                var waypInsertStmt = "";
                var waypTypeFID;
                waypAllFields.removeClass( "ui-state-error" );
                switch(waypDialogTypeCode.val()) {
                    case "Bergstation":
                        waypTypeFID = "1";
                        break;
                    case "Talort":
                        waypTypeFID = "2";
                        break;
                    case "Wegpunkt":
                        waypTypeFID = "3";
                        break;
                    case "Hütte":
                        waypTypeFID = "4";
                        break;
                    case "Gipfel":
                        waypTypeFID = "5";
                        break;
                }

                valid = valid && checkLength ( waypDialogNameShort, "Name Short", 1, 50 );
                valid = valid && checkLength ( waypDialogNameLong, "Name Long", 1, 255 );
                // waypDialogTypeCode
                // waypDialogCountry
                valid = valid && checkValidData ( waypDialogCanton, cantons );
                valid = valid && checkMandatory ( waypRegionID, "Region" );
                valid = valid && checkMandatory ( waypAreaID, "Area" );
                valid = valid && checkRegexp ( waypDialogAltitude, /^(?:[0-9]{4}|[0-9]{3}|)$/, "Enter valid Altitude." );
                valid = valid && checkMandatory ( waypCoordLV3Est, "Coord LV3 Est" );
                valid = valid && checkRegexp ( waypCoordLV3Est, /^(?:[0-9]{6}|)$/, "Coord LV3 Ost field must be a 6-digit number" );
                valid = valid && checkMandatory ( waypCoordLV3Nord, "Coord LV3 Nord" );
                valid = valid && checkRegexp ( waypCoordLV3Nord, /^(?:[0-9]{6}|)$/, "Coord LV3 Nord field must be a 6-digit number" );
                // waypDialogOwner
                // waypDialogWebsite
                // waypDialogWebsite

                /*
                valid = valid && checkRegexp( name, /^[a-z]([0-9a-z_\s])+$/i, "Username may consist of a-z, 0-9, underscores, spaces and must begin with a letter." );
                valid = valid && checkRegexp( email, emailRegex, "eg. ui@jquery.com" );
                valid = valid && checkRegexp( password, /^([0-9])+$/, "Password field only allow : a-z 0-9" );
                */      
                
                if ( valid ) {
                    // Convert LV3 into WGS82
                    var coordLV3Est = $( "#waypCoordLV3Est" ).val();
                    var coordLV3Nord = $( "#waypCoordLV3Nord" ).val();
                    
                    var y_strich = (coordLV3Est-600000) / 1000000;
                    var x_strich = (coordLV3Nord-200000) / 1000000;
                    var a_strich = 2.6779094 + 4.728982 * y_strich + 0.791484 * y_strich * x_strich + 0.1306 * y_strich * Math.pow(x_strich, 2) - 0.0436 * Math.pow(y_strich, 3);
                    var b_strich = 16.9023892 + 3.238272 * x_strich - 0.270978 * Math.pow(y_strich, 2) - 0.002528 * Math.pow(x_strich, 2) -0.047 * Math.pow(y_strich, 2) * x_strich - 0.014 * Math.pow(x_strich, 3);
                    
                    var waypWGS84E = a_strich*100/36;
                    var waypWGS84N = b_strich*100/36;
                    waypInsertStmt = "INSERT INTO `tourdb2`.`tbl_waypoints`";
                    waypInsertStmt += " (`waypID`, `waypNameShort`, `waypNameLong`, `waypTypeFID`, ";
                    waypInsertStmt += "  `waypCountry`, `waypCanton`, `waypRegionFID`, `waypAreaFID`, `waypAltitude`, ";
                    waypInsertStmt += "  `waypCoordLV3Est`, `waypCoordLV3Nord`, `waypCoordWGS84E`, `waypCoordWGS84N`, `waypOwner`, ";
                    waypInsertStmt += "  `waypWebsite`, `waypRemarks`) VALUES (NULL, "; 
                    waypInsertStmt += "'" + waypDialogNameShort.val() + "', ";
                    waypInsertStmt += "'" + waypDialogNameLong.val() + "', ";
                    waypInsertStmt += "'" + waypTypeFID + "', ";
                    waypInsertStmt += "'" + waypDialogCountry.val() + "', ";
                    waypInsertStmt += "'" + waypDialogCanton.val() + "', ";
                    waypInsertStmt += "'" + waypRegionID.val() + "', ";
                    waypInsertStmt += "'" + waypAreaID.val() + "', ";
                    waypInsertStmt += "'" + waypDialogAltitude.val() + "', ";
                    waypInsertStmt += "'" + waypCoordLV3Est.val() + "', ";
                    waypInsertStmt += "'" + waypCoordLV3Nord.val() + "', ";
                    waypInsertStmt += "'" + waypWGS84E + "', ";
                    waypInsertStmt += "'" + waypWGS84N + "', ";
                    waypInsertStmt += "'" + waypDialogOwner.val() + "', ";
                    waypInsertStmt += "'" + waypDialogWebsite.val() + "', ";
                    waypInsertStmt += "'" + waypDialogRemarks.val() + "')";
                    
                    var xhr = new XMLHttpRequest();
                    var phpLocation = document.URL + "db_ops.php";          // Variable to store location of php file
                    var xhrParams = "&stm=" + waypInsertStmt;
                    xhr.open ('POST', phpLocation, true);                // Make XMLHttpRequest
                    if ( debug ) { console.info (waypInsertStmt)};
                    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");  // Set header to encode special characters like %
                    xhr.send(encodeURI(xhrParams));

                    //dialog.dialog( "close" );
                    console.info("Waypoint written to DB");
                
                    $ ( '#waypDialogNameShort' ).val('');
                    $ ( '#waypDialogNameLong' ).val('');
                    $ ( '#waypDialogAltitude' ).val('');
                    $ ( '#waypCoordLV3Est' ).val('');
                    $ ( '#waypCoordLV3Nord' ).val('');
                    $ ( '#waypDialogOwner' ).val('');
                    $ ( '#waypDialogWebsite' ).val('');
                    $ ( '#waypDialogRemarks' ).val('');

                }   
                return valid;
            }      
        
            dialog = $( "#waypDialog" ).dialog({
                autoOpen: false,
                height: "auto",
                width: 600,
                modal: true,
                buttons: {
                    "Speichern": addWaypoint,
                    Cancel: function() {
                    dialog.dialog( "close" );
                    }
                },
                close: function() {
                    form[ 0 ].reset();
                    waypAllFields.removeClass( "ui-state-error" );
                }
            });
        
            //form = dialog.find( "#wayDialogForm" ).on( "submit", function( event ) {
            form = dialog.find( "form" ).on( "submit", function( event ) {
                event.preventDefault();
                addWaypoint();
            });

            $( "#waypBtnOpenDialog" ).button().on( "click", function() {
                dialog.dialog( "open" );
            });
        });

    // Create Add Segment Dialog Box
        $( function() {
        // Defining dialog and form var
            var dialog, form;

        // Prepare JQUI elements fields for Add Segment Dialog

            // segDialogSegType
            $( "#segDialogSegType" ).autocomplete({
                source: "get_auto_complete_values.php?field=segTypeFID",
                minLength: 1,
                select: function( event, ui ) {
                    $( "#segTypeFID" ).val( ui.item.id );
                },
                change: function( event, ui ) {
                    if ( $( "#segDialogSegType" ).val() == '' ) {
                        $( "#segTypeFID" ).val( '' );
                    }
                }
            });
            var segDialogSegType = $( "#segDialogSegType" );
            var segTypeFID = $( "#segTypeFID" ); 

            // segDialogSourceFID
            $( "#segDialogSourceFID" ).autocomplete({
                source: "get_auto_complete_values.php?field=segSourceFID",
                minLength: 1,
                select: function( event, ui ) {
                    $( "#segSourceFID" ).val( ui.item.id );
                },
                change: function( event, ui ) {
                    if ( $( "#segDialogSourceFID" ).val() == '' ) {
                        $( "#segSourceFID" ).val( '' );
                    }
                }
            });
            var segDialogSourceFID = $( "#segDialogSourceFID" );
            var segSourceFID = $( "#segSourceFID" ); 

            var segDialogSourceRef = $( "#segDialogSourceRef" );
            var segDialogSegName = $( "#segDialogSegName" );;
            var segDialogRouteName = $( "#segDialogRouteName" );; 

            // segDialogStartLocName
            $( "#segDialogStartLocName" ).autocomplete({
                source: "get_auto_complete_values.php?field=getWaypLong",
                minLength: 2,
                select: function( event, ui ) {
                    $( "#segDialogStartLocID" ).val( ui.item.id );
                },
                change: function( event, ui ) {
                    if ( $( "#segDialogStartLocName" ).val() == '' ) {
                        $( "#segDialogStartLocID" ).val( '' );
                    }
                }
            });
            var segDialogStartLocName = $( "#segDialogStartLocName");
            var segDialogStartLocID = $( "#segDialogStartLocID" ); 

            // segDialogTargetLocName
            $( "#segDialogTargetLocName" ).autocomplete({
                source: "get_auto_complete_values.php?field=getWaypLong",
                minLength: 2,
                select: function( event, ui ) {
                    $( "#segDialogTargetLocID" ).val( ui.item.id );
                },
                change: function( event, ui ) {
                    if ( $( "#segDialogTargetLocName" ).val() == '' ) {
                        $( "#segDialogTargetLocID" ).val( '' );
                    }
                }
            });
            var segDialogTargetLocName = $( "#segDialogTargetLocName" );
            var segDialogTargetLocID = $( "#segDialogTargetLocID" ); 
            
            // Field segDialogCountry
            $( "#segDialogCountry" ).autocomplete({
                source: countries,
                minLength: 1
            });
            var segDialogCountry = $( "#segDialogCountry" );

            // Field segDialogCanton
            $( "#segDialogCanton" ).autocomplete({
                source: cantons,
                minLength: 1
            });
            var segDialogCanton = $( "#segDialogCanton" );

            // segDialogRegion
            $( "#segDialogRegion" ).autocomplete({
                source: "get_auto_complete_values.php?field=regionID",
                minLength: 2,
                select: function( event, ui ) {
                    $( "#segRegionID" ).val( ui.item.id );
                },
                change: function( event, ui ) {
                    if ( $( "#segDialogRegion" ).val() == '' ) {
                        $( "#segRegionID" ).val( '' );
                    }
                }
            });
            var segDialogRegion = $( "#segDialogRegion" );
            var segRegionID = $( "#segDialogRegion" ); // --> Region is not stored in Waypoints

            // segDialogArea
            $( "#segDialogArea" ).autocomplete({
                source: "get_auto_complete_values.php?field=areaID",
                minLength: 2,
                select: function( event, ui ) {
                    $( "#segAreaID" ).val( ui.item.id );
                },
                change: function( event, ui ) {
                    if ( $( "#segDialogArea" ).val() == '' ) {
                        $( "#segAreaID" ).val( '' );
                    }
                }
            });
            var segDialogArea = $( "#segDialogArea");
            var segAreaID = $( "#segAreaID" );

            // segDialogGrade
            $( "#segDialogGrade" ).autocomplete({
                source: "get_auto_complete_values.php?field=gradeID",
                minLength: 1
            });
            var segDialogGrade = $( "#segDialogGrade" );

            // segDialogClimbGrade
            $( "#segDialogClimbGrade" ).autocomplete({
                source: "get_auto_complete_values.php?field=climbGradeID",
                minLength: 1
            });
            var segDialogClimbGrade = $( "#segDialogClimbGrade" );
            
            var segDialogFirn = $( "#segDialogFirn" );

            // segDialogEHaft
            $( "#segDialogEHaft" ).autocomplete({
                source: "get_auto_complete_values.php?field=eHaft",
                minLength: 1
            });
            var segDialogEHaft = $( "#segDialogEHaft" );

            $( "#segDialogExpo" ).selectable({});

            var segDialogTStartTarget = $( "#segDialogTStartTarget" );
            var segDialogMUStartTarget = $( "#segDialogMUStartTarget" );
            var segDialogDescent = $( "#segDialogDescent" );
            var segDialogRemarks = $( "#segDialogRemarks" );
            var segDialogCoordinates = $( "#segDialogCoordinates" );

        // Check validity of entered data
            segAllFields = $( [] ).add( segDialogSegType ).add( segDialogSourceFID ).add( segDialogSourceRef ).add( segDialogSegName ).add( segDialogRouteName ).add( segDialogStartLocName ).add( segDialogTargetLocName ).add( segDialogCountry ).add( segDialogCanton ).add( segDialogRegion ).add ( segDialogArea ).add( segDialogGrade ).add( segDialogTStartTarget ),
            segTips = $( ".segValidateTips" );
        
        // Function called when Add is clicked in Add Segement Dialog
            function addSegment() {
                tips = segTips;
                var valid = true;
                var segInsertStmt = "";
                segAllFields.removeClass( "ui-state-error" );
                valid = valid && checkExistance ( segDialogSegType, "Seg Type" );
                valid = valid && checkExistance ( segDialogSourceFID, "Source" );
                valid = valid && checkLength ( segDialogSourceRef, "Source Ref", 1, 10 );
                valid = valid && checkLength ( segDialogRouteName, "Route Name", 1, 255 );
                valid = valid && checkExistance ( segDialogStartLocName, "Startort" );
                valid = valid && checkExistance ( segDialogTargetLocName, "Zielort" );
                valid = valid && checkLength ( segDialogCountry, "Land", 2, 2 );
                valid = valid && checkLength ( segDialogCanton, "Kanton", 2, 2 );
                valid = valid && checkExistance ( segDialogRegion, "Region" );
                valid = valid && checkExistance ( segDialogArea, "Region" );
                valid = valid && checkLength ( segDialogGrade, "Schwierigkeit", 1, 10 );
                valid = valid && checkLength ( segDialogTStartTarget, "Zeit Start - Ziel", 1, 10 ); // Change to validation of correct content
                //valid = valid && checkRegexp ( waypDialogAltitude, /^(?:[0-9]{4}|[0-9]{3}|)$/, "Enter valid Altitude." );
                //valid = valid && checkRegexp ( waypCoordLV3Est, /^(?:[0-9]{6}|)$/, "Coord LV3 Ost field must be a 6-digit number" );
                //valid = valid && checkRegexp ( waypCoordLV3Nord, /^(?:[0-9]{6}|)$/, "Coord LV3 Nord field must be a 6-digit number" );

                // Field Expo
                segExpo = '';
                firstInCriteria = true;
                $('#segDialogExpo .ui-selected').each(function() {
                    var recordId = this.id;
                    var sqlName = "expo";
                    var lenCriteria = recordId.length;
                    var startCriteria = sqlName.length + 1;
                    if ( !firstInCriteria ) { 
                        segExpo += ",";
                    } else {
                        firstInCriteria = false;
                    };
                    segExpo += recordId.slice(startCriteria,lenCriteria); 
                });
                console.info("segExpo: " + segExpo);

                if ( valid ) {
                    segInsertStmt = "INSERT INTO `tourdb2`.`tbl_segments` ";
                    segInsertStmt += "(`segId`, `segTypeFID`, `segSourceFID`, ";
                    segInsertStmt += "`segSourceRef`, `segName`, `segRouteName`, ";
                    segInsertStmt += "`segStartLocationFID`, `segTargetLocationFID`, ";
                    segInsertStmt += "`segCountry`, `segCanton`, ";
                    segInsertStmt += "`segAreaFID`, `segGradeFID`, `segClimbGradeFID`, ";
                    segInsertStmt += "`segFirn`, `segEhaft`, `segExpo`, `segTStartTarget`, ";
                    segInsertStmt += "`segMUStartTarget`, ";
                    segInsertStmt += "`segDescent`, `segRemarks`, `segCoordinates`) ";
                    segInsertStmt += "VALUES (NULL,";
                    segInsertStmt += "'" + segTypeFID.val() + "', ";
                    segInsertStmt += "'" + segSourceFID.val() + "', ";
                    segInsertStmt += "'" + segDialogSourceRef.val() + "', ";
                    segInsertStmt += "'" + segDialogSegName.val() + "', ";
                    segInsertStmt += "'" + segDialogRouteName.val() + "', ";
                    segInsertStmt += "'" + segDialogStartLocID.val() + "', ";
                    segInsertStmt += "'" + segDialogTargetLocID.val() + "', ";
                    segInsertStmt += "'" + segDialogCountry.val() + "', ";
                    segInsertStmt += "'" + segDialogCanton.val() + "', ";
                    segInsertStmt += "'" + segAreaID.val() + "', ";
                    segInsertStmt += "'" + segDialogGrade.val() + "', ";
                    segInsertStmt += "'" + segDialogClimbGrade.val() + "', ";
                    segInsertStmt += "'" + segDialogFirn.val() + "', ";
                    segInsertStmt += "'" + segDialogEHaft.val() + "', ";
                    segInsertStmt += "'" + segExpo + "', ";
                    segInsertStmt += "'" + segDialogTStartTarget.val() + "', ";
                    segInsertStmt += "'" + segDialogMUStartTarget.val() + "', ";
                    segInsertStmt += "'" + segDialogDescent.val() + "', ";
                    segInsertStmt += "'" + segDialogRemarks.val() + "', ";
                    segInsertStmt += "'" + segDialogCoordinates.val() + "') ";
                    
                    var xhr = new XMLHttpRequest();
                    var phpLocation = document.URL + "db_ops.php";          // Variable to store location of php file
                    var xhrParams = "&stm=" + segInsertStmt;
                    xhr.open ('POST', phpLocation, true);                // Make XMLHttpRequest 
                    if ( debug ) { console.info (segInsertStmt)};
                    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");  // Set header to encode special characters like %
                    xhr.send(encodeURI(xhrParams)); 
                                
                    //dialog.dialog( "close" );
                    $ ( '#segDialogSourceRef' ).val('');
                    $ ( '#segDialogRouteName' ).val('');
                    $ ( '#segDialogGrade' ).val('');
                    $ ( '#segDialogClimbGrade' ).val('');
                    $ ( '#segDialogFirn' ).val('');
                    $ ( '#segDialogEHaft' ).val('');
                    $ ( '#segDialogExpo .ui-selected').each(function() {
                        var $record = $(this);
                        //var $topicThis = $(this); 
                        //this.removeClass( "ui-selected" );
                        $record.removeClass( "ui-selected" ); 
                    });
                    $ ( '#segDialogMUStartTarget' ).val('');
                    //$ ( '#segDialogDescent' ).val('');
                    $ ( '#segDialogTStartTarget' ).val('');
                    $ ( '#segDialogRemarks' ).val('');
                    $ ( '#segDialogCoordinates' ).val('');
                    console.info("Segment written to DB");
                }
                return valid;
            }

        // Initialize Add Segement Dialog
            dialog = $( "#segDialog" ).dialog({
                autoOpen: false,
                height: "auto",
                width: 750,
                modal: true,
                buttons: {
                    "Speichern": addSegment,
                    Cancel: function() {
                    dialog.dialog( "close" );
                    }
                },
                close: function() {
                    form[ 0 ].reset();
                    segAllFields.removeClass( "ui-state-error" );
                }
            });
        
        // Initialize add segment form
            form = dialog.find( "form" ).on( "submit", function( event ) {
                event.preventDefault();
                addSegment();
            });

        // Create Dialog Box
            $( "#segBtnOpenDialog" ).button().on( "click", function() {
                dialog.dialog( "open" );
            });
        });
    }); // end of document.ready function

//
// =================================
// ====== F U N C T I O N S ========
// =================================
    // Function generating KML file for segments
        function callGenSegKml(segSqlFilterString) {
            // call gen_kml.php using XMLHttpRequest POST  
            if (debug) { console.info("callGenSegKml entered"); };      
            var xhr = new XMLHttpRequest();
            phpLocation = document.URL + "seg_gen_kml.php";          // Variable to store location of php file
            xhrParams = "sqlFilterString=" + segSqlFilterString;   // Variable for POST parameters
            xhrParams += "&segKmlFileName=" + segKmlFileName ;
            xhr.open ('POST', phpLocation, false);                // Make XMLHttpRequest - in asynchronous mode to avoid wrong data display in map (map displayed before KML file is updated)
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");  // Set header to encode special characters like %
            xhr.send(encodeURI(xhrParams));
            if (debug) { console.info("callGenSegKml completed"); }; 
        }   

    // Function generating KML file for Waypoints
        function callGenWaypKml(waypSqlFilterString) {
            // call gen_kml.php using XMLHttpRequest POST  
            if (debug) { console.info("callGenWaypKml entered: ") + waypSqlFilterString };      
            var xhr = new XMLHttpRequest();
            phpLocation = document.URL + "wayp_gen_kml.php";          // Variable to store location of php file
            xhrParams = "sqlFilterString=" + waypSqlFilterString;   // Variable for POST parameters
            xhrParams += "&waypKmlFileName=" + waypKmlFileName ;
            xhr.open ('POST', phpLocation, false);                // Make XMLHttpRequest - in asynchronous mode to avoid wrong data display in map (map displayed before KML file is updated)
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");  // Set header to encode special characters like %
            xhr.send(encodeURI(xhrParams));
            if (debug) { console.info("callGenWaypKml completed"); }; 
        }

    // Function drawing empty map
        function drawMapEmpty(targetDiv) {
            var map = new ga.Map({
                target: targetDiv,
                view: new ol.View({resolution: 650, center: [660000, 190000]})
            });

            // Create a background layer
            var lyr1 = ga.layer.create('ch.swisstopo.pixelkarte-farbe');
            map.addLayer(lyr1);
        }

    // Function drawing tracks based on single KML files
        function drawMapSingle(targetDiv, segDisplayArray) {
            if (debug) { console.info("drawMapSingle entered"); }; 
            
            // Create a GeoAdmin Map
            var mapPanelTouren = new ga.Map({
                target: targetDiv,
                view: new ol.View({resolution: 650, center: [660000, 190000]})
            });

            // Create a background layer and add to map
            var lyr1 = ga.layer.create('ch.swisstopo.pixelkarte-farbe');
            mapPanelTouren.addLayer(lyr1);
            
            for (var i = 0; i < segDisplayArray.length; i++) {
                if ( segDisplayArray[i][3] == 1 ) {
                    singleKml = './kml/single/segKml_' + segDisplayArray[i][0] + '.kml';
                    var segVector = new ol.layer.Vector({
                        source: new ol.source.Vector({
                            url: singleKml, 
                            format: new ol.format.KML({
                                projection: 'EPSG:21781'
                            })
                        })
                    });
                    mapPanelTouren.addLayer(segVector);
                }
            };
            
            // Popup showing the position the user clicked
            var popup = new ol.Overlay({
                element: $('<div title="KML"></div>')[0]
            });
            mapPanelTouren.addOverlay(popup);

            // On click we display the feature informations
            mapPanelTouren.on('singleclick', function(evt) {     
                var pixel = evt.pixel;
                var coordinate = evt.coordinate;
                var feature = mapPanelTouren.forEachFeatureAtPixel(pixel, function(feature, layer) {
                    return feature;
                });
                
                var strStart = feature.get('description').indexOf("[") + 1;   // evaluate start position to extract segment id
                var strEnd = feature.get('description').indexOf("]");         // evaluate end position to extract segment id
                var kmlId = feature.get('description').substring(strStart, strEnd);    // extract segment id from kml track

                // Search selected segment in responseObject (JSON object received from fetch_pages.php) 
                for (var i = 0; i < segDisplayArray.length; i++) {   // loop through reponseObject
                    if ( segDisplayArray[i][0] == kmlId ) {

                        // create sortable line
                        createSortable ( i );
        
                        // Check checkbox in segments panel
                        var strChckbox = "[name='segid_" + kmlId + "']"; 
                        $( strChckbox ).prop('checked', true);
                        break;
                    }
                }
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
            mapPanelTouren.on('pointermove', function(evt) {
                var feature = mapPanelTouren.forEachFeatureAtPixel(evt.pixel, function(feature, layer) {
                    return feature;
                });
                mapPanelTouren.getTargetElement().style.cursor = feature ? 'pointer' : '';
                
            });
            if (debug) { console.info("drawMapSingle completed"); };
        }
    // Function drawing maps old style
        function drawMapOld(targetDiv, segKmlFile, waypKmlFile, drawHangneigung, drawWanderwege, drawHaltestellen, 
                        drawKantonsgrenzen, drawSacRegion) {
            if (debug) { console.info("drawMapOld entered"); }; 
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

            if (debug) { console.info("drawMapOld completed"); };
        }

    // Function updating Tips    
        function updateTips( t ) {
            tips
                .text( t )
                .addClass( "ui-state-highlight" );
            setTimeout(function() {
                tips.removeClass( "ui-state-highlight", 1500 );
            }, 500 );
        }

    // Funtion checking existance of file content in ADD dialog
        function checkExistance( o, n ) {
            if ( o.val().length == 0 ) {
                o.addClass( "ui-state-error" );
                updateTips( "Field " + n + " must be entered" );
                return false;
            } else {
                return true;
            }
        }

    // Function checking the min / max length of field content of ADD dialog 
        function checkLength( o, n, min, max ) {
            if ( o.val().length > max || o.val().length < min ) {
                o.addClass( "ui-state-error" );
                updateTips( "Length of " + n + " must be between " +
                min + " and " + max + "." );
                return false;
            } else {
                return true;
            }
        }

    // Function checking if mandatory field of ADD dialog
        function checkMandatory( o, n ) {
            if ( o.val().length < 1 ) {
                o.addClass( "ui-state-error" );
                updateTips( "Field " + n + " must be filled " );
                return false;
            } else {
                return true;
            }
        }

    // Function checking if string is in array in ADD dialog
        function checkValidData(o, arr) {
            found = false; 
            for ( i=0; i < arr.length; i++ ) {
                if ( arr[i] == o.val() ) {
                    var found = true;
                    break;    
                }
            }
            if ( !found ) {
                o.addClass( "ui-state-error" );
                updateTips( "Please enter a valid abbr. for Canton or 'n/a' if not available.");
                return false;
            } else {
                return true;
            }
        }
    // function checking of field content is consisting of numbers
        function checkRegexp( o, regexp, n ) {
            if ( !( regexp.test( o.val() ) ) ) {
                o.addClass( "ui-state-error" );
                updateTips( n );
                return false;
            } else {
                return true;
            }
        }

    // function to create sortable in tab tourenPanelFilter
        function createSortable( i ) {
            console.info("Checked " + i );
            sortableString  = '<li id="sorLi_' + i + '" class="ui-state-default">';
            sortableString += '<a id="sorA_' + i;
            sortableString += '" class="tourDelSortable" href="#sorH_' + i + '"><img src="images/delete.png" class="delImage"></a>';
            sortableString += responseObject[i].segName + ' | ';
            sortableString += responseObject[i].segType + ' | ';
            sortableString += responseObject[i].grade + '/' +  responseObject[i].climbGrade + ' | ';
            sortableString += responseObject[i].tStartTarget + ' | ';
            sortableString += responseObject[i].mUStartTarget + ' | ';
            sortableString += responseObject[i].source ;
            sortableString += '</li>';
            $( '#panelTourTour-sortable' ).append(sortableString);
            segDisplayArray[i][2] = 1;
        }
    