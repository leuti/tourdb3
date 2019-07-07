<?php
	// Start session before ending it (for users coming back from sub pages)
	session_start();
	
	// End session
	session_destroy();
	$_SESSION = array();								// empty SESSION variable
?>

<!DOCTYPE HTML>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<meta http-equiv="Cache-control" content="no-cache">
    <meta http-equiv="Expires" content="-1">
	<title>tourdb 3.0</title>

	<link type="text/css" rel="stylesheet" href="css/jquery-ui.css">
	<link type="text/css" rel="stylesheet" href="css/tourdb.css">	
</head>
<body>
	<!-- load jquery sources -->
	<script type="text/javascript" src="js/jquery-3.3.1.min.js"></script> <!-- JQuery from local server -->  <!-- local -->
	<script type="text/javascript" src="js/jquery-ui.min.js"></script> <!-- JQuery UI from local server --> <!-- local -->
	
	<!-- load swissmap sources -->
	<script src="http://api3.geo.admin.ch/loader.js?lang=en" type="text/javascript"></script>
	
	<script type="text/javascript" src="js/bootstrap.min.js"></script> <!-- local -->
	<script type="text/javascript" src="js/tourdb.js"></script> <!-- tourdb code -->

	<!-- ========================================================================== -->
	<!-- ========================== header ======================================== -->
	<!-- ========================================================================== -->

	<header id="header">
		<div id="logo">
			<h1>tour<em>DB</em></h1>
		</div>
		
		<!-- Below the the main tabs for Home, Segments, Waypoints and Routes are located -->
		<ul class="mainButtons">
			<li id="uiLogin_loginBtn" class="mainButtons_li active" style="display:none;">
				<a id="uiLogin_loginBtn_a" class="mainButtons_a" href="#panelLogin"><h2>Login</h2></a>
			</li>
			<li id="mainButtons_mapBtn" class="mainButtons_li loginReq">
				<a id="mainButtons_mapBtn_a" class="mainButtons_a" href="#panelMap"><h2>Map</h2></a>
			</li>
			<li id="mainButtons_listBtn" class="mainButtons_li loginReq">
				<a id="mainButtons_listBtn_a" class="mainButtons_a" href="#panelLists"><h2>Lists</h2></a> 
			</li>			
			<li id="mainButtons_importBtn" class="mainButtons_li loginReq">
				<a id="mainButtons_importBtn_a" class="mainButtons_a" href="#panelImport"><h2>Import</h2></a> 
			</li>
			<li id="mainButtons_exportBtn" class="mainButtons_li loginReq">
				<a id="mainButtons_exportBtn_a" class="mainButtons_a" href="#panelExport"><h2>Export</h2></a> 
			</li>
		</ul>
	</header> 

    <!-- In the div #main all page content is diplayed -->
	<section id="main"> 
		
		<!-- ========================================================================== -->
		<!-- ======================== panelLogin ========================================== -->
		<!-- ========================================================================== -->
		<div id="panelLogin" class="tourdbPanel active">
			<div id=uiLogin class="formCenter">
				<fieldset>
        		
					<!-- Import Tracks -->
					<legend class="filterHeader">Enter you login credentials</legend>

					<div>
						<label for="uiLogin_login" class="labelFirst">User Login</label>
						<input id="uiLogin_login" class="loginFields" type="text" size="50">
					</div>
					<div>
						<label for="uiLogin_password" class="labelFirst">Password</label>
						<input id="uiLogin_password" class="loginFields" type="password" size="50">
					</div>

					<div class="uiLogin_loginBtn">
						<input type="submit" class="button" id="uiLogin_loginBtn" value="Login" />
					</div>
					
					<div id="uiLogin_status" class="statusMessage">	
					</div>
				</fieldset>
				
			</div>
			
		</div> <!-- End div panelLogin -->

		<!-- ========================================================================== -->
		<!-- ========================== panelMap ================================== -->
		<!-- ========================================================================== -->
		<div id="panelMap" class="tourdbPanel">
			
			<!-- Div for Menu buttons -->
			<div id="dispObjectSelector">
				<!-- This div shows only filter button > onclick opens the full accordion -->
				<div id="dispObjMenuMini" class="dispObjMini hidden">
					<a id="dispObjMenuMiniOpen" href="#dispObjMenuMiniOpen">
						<img id="dispObjMenuOpenImg" src="css/images/filterLightBlue.png">
					</a> 
				</div>
				<div id="dispObjMenuLarge" class="dispObjOpen visible">
					<a id="dispObjMenuLargeClose" href="#dispObjMenuLargeClose">
						<img id="dispObjMenuCloseImg" src="css/images/arrowLeftLightBlue.png">
					</a> 
					<p class="dispObjMenuText">Select objects to be displayed</p>
								
					<!-- this div shows the jquery accordion for the display selection -->
					<div id="dispObjAccordion" class="dispObjOpen visible"> 

						<!-- Segment Section of Accordion -->
						<h2>Tracks</h2>
						<div class="accordionBackground">
							<fieldset>
								<!-- SEGMENT filter -->
								<legend class="filterHeader">Tracks</legend>

								<!-- Track ID -->
								<div class="dispObjCriteria">	
									<label for="dispFilTrk_trackIdFrom" class="labelFirst">Track ID From</label>
									<input type="text" name="dispFilTrk_trackIdFrom" id="dispFilTrk_trackIdFrom" size="10" class="text ui-widget-content ui-corner-all">
									<label for="dispFilTrk_trackIdTo" class="labelNext">Track ID To</label>
									<input type="text" name="dispFilTrk_trackIdTo" id="dispFilTrk_trackIdTo" size="10" class="text ui-widget-content ui-corner-all">
								</div>

								<!-- Track name contains (standard text field) -->
								<div class="dispObjCriteria">	
									<label for="dispFilTrk_trackName" class="labelFirst">Track Names contains</label>
									<input type="text" name="dispFilTrk_trackName" id="dispFilTrk_trackName" size="32" class="dispInput text ui-widget-content ui-corner-all">
								</div>

								<!-- Route contains (standard text field) -->
								<div class="dispObjCriteria">
									<label for="dispFilTrk_route" class="labelFirst">Route contains</label>
									<input name="dispFilTrk_route" id="dispFilTrk_route" size="32" class="dispInput text ui-widget-content ui-corner-all">
								</div>

								<!-- Date witin range -->
								<div class="dispObjCriteria">
									<label for="dispFilTrk_dateFrom" class="labelFirst">From Date</label>
									<input name="dispFilTrk_dateFrom" id="dispFilTrk_dateFrom" size="10" class="text ui-widget-content ui-corner-all">
									<label for="dispFilTrk_dateTo" class="labelNext">To Date</label>
									<input name="dispFilTrk_dateTo" id="dispFilTrk_dateTo" size="10" class="text ui-widget-content ui-corner-all">
								</div>

								<!-- Type as select items (selectable) -->
								<div class="dispObjCriteria">
									<label for="dispFilTrk_type" class="labelFirst">Type (CTRL+Left-click for multi-select)</label>
									<ol id="dispFilTrk_type" class="selectable filterItems">
										<li id="dispFilTrk_type_Klettern" class="ui-widget-content">Klettern</li>
										<li id="dispFilTrk_type_Ski" class="ui-widget-content">Ski</li>
										<li id="dispFilTrk_type_Sport" class="ui-widget-content">Sport</li>
										<li id="dispFilTrk_type_Velo" class="ui-widget-content">Velo</li>
										<li id="dispFilTrk_type_Wasser" class="ui-widget-content">Wasser</li>
										<li id="dispFilTrk_type_Zufuss" class="ui-widget-content first">Zufuss</li>
									</ol>
								</div>

								<!-- Subtype as select items (selectable) -->
								<div class="dispObjCriteria">
									<label for="dispFilTrk_subtype" class="labelFirst">Subtype (CTRL+Left-click for multi-select)</label>
									<ol id="dispFilTrk_subtype" class="selectable filterItems">
										<li id="dispFilTrk_subtype_alpinklettern" class="ui-widget-content">Alpinklettern</li>
										<li id="dispFilTrk_subtype_alpintour" class="ui-widget-content">Alpintour</li>
										<li id="dispFilTrk_subtype_Hochtour" class="ui-widget-content">Hochtour</li>
										<li id="dispFilTrk_subtype_Joggen" class="ui-widget-content">Joggen</li>
										<li id="dispFilTrk_subtype_Mehrseilklettern" class="ui-widget-content">Mehrseilklettern</li>
										<li id="dispFilTrk_subtype_Schneeschuhwanderung" class="ui-widget-content first">Schneeschuhwanderung</li>
										<li id="dispFilTrk_subtype_Schwimmen" class="ui-widget-content">Schwimmen</li>
										<li id="dispFilTrk_subtype_Skihochtour" class="ui-widget-content">Skihochtour</li>
										<li id="dispFilTrk_subtype_Skitour" class="ui-widget-content">Skitour</li>
										<li id="dispFilTrk_subtype_Sportklettern" class="ui-widget-content">Sportklettern</li>
										<li id="dispFilTrk_subtype_Velotour" class="ui-widget-content">Velotour</li>
										<li id="dispFilTrk_subtype_Wanderung" class="ui-widget-content first">Wanderung</li>
										<li id="dispFilTrk_subtype_Winterwanderung" class="ui-widget-content first">Winterwanderung</li>
									</ol>
								</div>
								
								<!-- participants like (standard text field) -->
								<div class="dispObjCriteria">	
									<label for="dispFilTrk_participants" class="labelFirst">Participants contains</label>
									<input type="text" name="dispFilTrk_participants" id="dispFilTrk_participants" size="32" class="dispInput text ui-widget-content ui-corner-all">
								</div>

								<!-- Country (standard text field) -->
								<div class="dispObjCriteria">	
									<label for="dispFilTrk_country" class="labelFirst">Country like</label>
									<input type="text" name="dispFilTrk_country" id="dispFilTrk_country" size="20" class="dispInput text ui-widget-content ui-corner-all">
								</div>

								<!-- Button to newly display selection -->
								<div class="dispObjCriteria">
									<input type="submit" class="button applyFilterButton" id="dispFilTrk_NewLoadButton" value="Load" />
								</div>
								<!-- Button to add selection to existing objects -->
								<!--
									<div class="dispObjCriteria">
									<input type="submit" class="button applyFilterButton" id="dispFilTrk_addObjButton" value="Add Tracks" />
								</div>
								-->
								<!-- Button to reset filter -->
								<!--
								<div class="dispObjCriteria">
									<input type="submit" class="button filterResetButton" id="dispFilTrk_ResetButton" value="Reset Filter" />
								</div>-->
								
							</fieldset>
						</div>
						
						<!-- Segment Section of Accordion -->
						<h2>Segments</h2>
						<div class="accordionBackground">
							<fieldset>

								<!-- SEGMENT filter -->
								<legend class="filterHeader">Segments</legend>

								<!-- segTypeFID -->
								<div class="dispObjCriteria">
									<label for="dispFilSeg_mapType" class="labelFirst">Type (CTRL+Left-click for multi-select)</label>
									<ol id="dispFilSeg_segTypeFID" class="selectable filterItems">
										<li id="segTypeFID_WA" class="ui-widget-content first">Wanderung</li>
										<li id="segTypeFID_AW" class="ui-widget-content">Alpinwanderung</li>
										<li id="segTypeFID_HT" class="ui-widget-content">Hochtour</li>
										<li id="segTypeFID_ST" class="ui-widget-content">Skitour</li>
										<li id="segTypeFID_SS" class="ui-widget-content">Schneeschuhtour</li>
									</ol>
								</div>

								<!-- segName -->
								<div class="dispObjCriteria">
									<label for="dispFilSeg_segName" class="labelFirst">Segment Name contains</label>
									<input id="dispFilSeg_segName" class="filterItems" type="text" size="50">
								</div>

								<!--startLocName -->
								<div class="dispObjCriteria">
									<label for="dispFilSeg_startLocName" class="labelFirst">Select Start Location</label>
									<input id="dispFilSeg_startLocName" class="filterItems" type="text" size="50">
									<input type="hidden" id="dispFilSeg_startLocID">
								</div>
								
								<!--startLocAlt
								<div class="dispObjCriteria">
									<label for="dispFilSeg_startLocAlt_slider_values" class="labelFirst">Start Altitude</label>
									<input type="text" id="dispFilSeg_startLocAlt_slider_values" class="filterItems sliderValue" readonly>
								</div>
								<div id="dispFilSeg_startLocAlt_slider"></div>
								-->

								<!-- startLocType -->
								<div class="dispObjCriteria">
									<label for="dispFilSeg_startLocType" class="labelFirst">Start Type (CTRL+Left-click for multi-select)</label>
									<ol id="dispFilSeg_startLocType" class="selectable filterItems">
										<li id="startLocType_1" class="ui-widget-content first">Bergstation</li>
										<li id="startLocType_5" class="ui-widget-content">Gipfel</li>
										<li id="startLocType_4" class="ui-widget-content">Hütte</li>
										<li id="startLocType_2" class="ui-widget-content">Talort</li>
										<li id="startLocType_3" class="ui-widget-content">Wegpunkt</li>
									</ol>
								</div>
								
								<!--TargetLocName -->
								<div class="dispObjCriteria">
									<label for="dispFilSeg_targetLocName" class="labelFirst">Target Location</label>
									<input id="dispFilSeg_targetLocName" class="filterItems" type="text" size="50">
									<input type="hidden" id="dispFilSeg_targetLocID">
								</div>

								<!--targetLocAlt 
								<div class="dispObjCriteria">
									<label for="dispFilSeg_targetLocAlt_slider_values" class="labelFirst">Target Altitude</label>
									<input type="text" id="dispFilSeg_targetLocAlt_slider_values" class="filterItems sliderValue" readonly>
								</div>
								<div id="dispFilSeg_targetLocAlt_slider"></div>
								-->

								<!-- targetLocType -->
								<div class="dispObjCriteria">
									<label for="dispFilSeg_targetLocType" class="labelFirst">Target Type (CTRL+Left-click for multi-select)</label>
									<ol id="dispFilSeg_targetLocType" class="selectable filterItems">
										<li id="targetLocType_1" class="ui-widget-content first">Bergstation</li>
										<li id="targetLocType_5" class="ui-widget-content">Gipfel</li>
										<li id="targetLocType_4" class="ui-widget-content">Hütte</li>
										<li id="targetLocType_2" class="ui-widget-content">Talort</li>
										<li id="targetLocType_3" class="ui-widget-content">Wegpunkt</li>
									</ol>
								</div>

								<!-- region -->
								<div class="dispObjCriteria">
									<label for="dispFilSeg_segRegion" class="labelFirst">Region</label>
									<input id="dispFilSeg_segRegion" class="filterItems" type="text" size="50">
									<input type="hidden" id="dispFilSeg_segRegionID">
								</div>
								
								<!-- Gebiet -->
								<div class="dispObjCriteria">
									<label for="dispFilSeg_segArea" class="labelFirst">Area</label>
									<input id="dispFilSeg_segArea" class="filterItems" type="text" size="50">
									<input type="hidden" id="dispFilSeg_segAreaID">
								</div>
					
								<!-- grade -->
								<div class="dispObjCriteria">
									<label for="dispFilSeg_grade" class="labelFirst">Grade (CTRL+Left-click for multi-select)</label>
									<ol id="dispFilSeg_grade" class="selectable filterItems">
										<li id="grade_T1" class="ui-widget-content first">T1</li>
										<li id="grade_T2" class="ui-widget-content">T2</li>
										<li id="grade_T3" class="ui-widget-content">T3</li>
										<li id="grade_T4" class="ui-widget-content">T4</li>
										<li id="grade_T5" class="ui-widget-content">T5</li>
										<li id="grade_T6" class="ui-widget-content">T6</li>
										<li id="grade_L" class="ui-widget-content first">L</li>
										<li id="grade_WS" class="ui-widget-content">WS</li>
										<li id="grade_ZS" class="ui-widget-content">ZS</li>
										<li id="grade_S" class="ui-widget-content">S</li>
										<li id="grade_SS" class="ui-widget-content">SS</li>
										<li id="grade_aS" class="ui-widget-content">AS</li>
										<li id="grade_EX" class="ui-widget-content">EX</li>
									</ol>
								</div>
								
								<!-- climbGrade -->
								<div class="dispObjCriteria">
									<label for="dispFilSeg_climbGrade" class="labelFirst">Climbing Grade (CTRL+Left-click for multi-select)</label>
									<ol id="dispFilSeg_climbGrade" class="selectable filterItems">
										<li id="climbGrade_I" class="ui-widget-content first">I</li>
										<li id="climbGrade_II" class="ui-widget-content">II</li>
										<li id="climbGrade_III" class="ui-widget-content">III</li>
										<li id="climbGrade_IV" class="ui-widget-content">IV</li>
										<li id="climbGrade_V" class="ui-widget-content">V</li>
										<li id="climbGrade_VI" class="ui-widget-content">VI</li>
										<li id="climbGrade_>VI" class="ui-widget-content">>VI</li>
									</ol>
								</div>

								<!--tStartTarget -->
								<!-- WAITING FOR JQUI WIDGET
								<div class="dispObjCriteria">
									<br>t St-Zi  min. <input type="text" name="tStartTargetMin">
									max. <input type="text" name="tStartTargetMax">
								</div>
								-->
									
								<!--mUStartTarget -->
								<!-- WAITING FOR JQUI WIDGET
								<div class="dispObjCriteria">
									<br>Aufstieg  min. <input type="text" name="mUStartTargetMin">
									max. <input type="text" name="mUStartTargetMax"><br>
								</div>
								-->
								
								<!-- Button to newly display selection -->
								<div class="dispObjCriteria">
									<input type="submit" class="button applyFilterButton" id="dispFilSeg_NewLoadButton" value="New Load" />
								</div>
								<!-- Button to add selection to existing objects -->
								<div class="dispObjCriteria">
									<input type="submit" class="button applyFilterButton" id="dispFilSeg_addObjButton" value="Add Segments" />
								</div>
								<!-- Button to reset filter -->
								<!--
								<div class="dispObjCriteria">
									<input type="submit" class="button filterResetButton" id="dispFilSeg_ResetButton" value="Reset Filter" />
								</div>-->

							</fieldset>
						</div>

						<!-- Segment Section of Accordion -->
						<h2>Additional Items to display</h2>
						<div class="accordionBackground">
							<fieldset>

								<!-- SEGMENT filter -->
								<legend class="filterHeader">Peaks</legend>

								<p>
									<label for="dispObjPeaks_100" class="labelFirst">Peaks < 1000m</label>
									<input type="checkbox" class="dispObjSel" id="dispObjPeaks_100" class="text ui-widget-content ui-corner-all">
								</p>
								<p>
									<label for="dispObjPeaks_1000" class="labelFirst">Peaks (1000m - 1999m)</label>
									<input type="checkbox" class="dispObjSel" id="dispObjPeaks_1000" class="text ui-widget-content ui-corner-all">
								</p>
								<p>
									<label for="dispObjPeaks_2000" class="labelFirst">Peaks (2000m - 2999m)</label>
									<input type="checkbox" class="dispObjSel" id="dispObjPeaks_2000" class="text ui-widget-content ui-corner-all">
								</p>
								<p>
									<label for="dispObjPeaks_3000" class="labelFirst">Peaks (3000m - 3999m)</label>
									<input type="checkbox" class="dispObjSel" id="dispObjPeaks_3000" class="text ui-widget-content ui-corner-all">
								</p>
								<p>
									<label for="dispObjPeaks_4000" class="labelFirst">Peaks (4000m - 4999m)</label>
									<input type="checkbox" class="dispObjSel" id="dispObjPeaks_4000" class="text ui-widget-content ui-corner-all">
								</p>
								<p>
									<label for="dispObjPeaks_cant" class="labelFirst">Peaks (Top of Cantons)</label>
									<input type="checkbox" class="dispObjSel" id="dispObjPeaks_cant" class="text ui-widget-content ui-corner-all">
								</p>
							</fieldset>				

							<fieldset>

								<!-- SEGMENT filter -->
								<legend class="filterHeader">Others</legend>

								<p>
									<label for="dispObjHuts" class="dispObjSel labelFirst">Hütten</label>
									<input type="checkbox" name="dispObjHuts" id="dispObjHuts" class="text ui-widget-content ui-corner-all">
								</p>
							</fieldset>

							<!-- Button to newly display selection -->
							<div class="dispObjCriteria">
								<input type="submit" class="button applyFilterButton" id="dispFilWayp_NewLoadButton" value="Load" />
							</div>
							
						</div>

					</div>
				</div>
			</div>

			<!-- This div shows the output as map -->
			<div id="displayMap" class="visible">
				<div id="displayMap-ResMap">	<!-- Here the map of the mapments is displayed -->
				</div> <!-- End mapResultMap -->
				<!--
					<div id="displayMap-ResMap2">
				</div> --><!-- End mapResultMap -->
			
			</div> <!-- End displayMap -->

		</div> <!-- End div panelLists -->

		<!-- ========================================================================== -->
		<!-- ======================== panelLists =================================== -->
		<!-- ========================================================================== -->		
		<div id="panelLists" class="tourdbPanel">

			<!-- This div shows the filter UI -->
			<div id="listTracksSelector">
				<!-- This div shows only filter button > onclick opens the full accordion -->
				<div id="dispListTrkMenuMini" class="dispListTrkMini visible">
					<a id="dispListTrkMenuMiniOpen" href="#dispListTrkMenuMiniOpen">
						<img id="dispListTrkMenuOpenImg" src="css/images/filterLightBlue.png">
					</a> 
				</div>
				<div id="dispListTrkMenuLarge" class="dispListTrkOpen hidden">
					<a id="dispListTrkMenuLargeClose" href="#dispListTrkMenuLargeClose">
						<img id="dispListTrkMenuCloseImg" src="css/images/arrowLeftLightBlue.png">
					</a> 
					<p class="dispListTrkMenuText">Select tracks to be displayed</p>
								
					<!-- this div shows the jquery accordion for the display selection -->
					<div id="dispListTrkAccordion" class="dispListTrkOpen visible"> 

						<!-- Segment Section of Accordion -->
						<h2>Tracks</h2>
						<div class="accordionBackground">
							<fieldset>
								<!-- SEGMENT filter -->
								<legend class="filterHeader">Tracks</legend>

								<!-- Track ID -->
								<div class="dispListTrkCriteria">	
									<label for="dispListTrk_trackIdFrom" class="labelFirst">Track ID From</label>
									<input type="text" name="dispListTrk_trackIdFrom" id="dispListTrk_trackIdFrom" size="10" class="text ui-widget-content ui-corner-all">
									<label for="dispListTrk_trackIdTo" class="labelNext">Track ID To</label>
									<input type="text" name="dispListTrk_trackIdTo" id="dispListTrk_trackIdTo" size="10" class="text ui-widget-content ui-corner-all">
								</div>

								<!-- Track name contains (standard text field) -->
								<div class="dispListTrkCriteria">	
									<label for="dispListTrk_trackName" class="labelFirst">Track Names contains</label>
									<input type="text" name="dispListTrk_trackName" id="dispListTrk_trackName" size="32" class="dispInput text ui-widget-content ui-corner-all">
								</div>

								<!-- Route contains (standard text field) -->
								<div class="dispListTrkCriteria">
									<label for="dispListTrk_route" class="labelFirst">Route contains</label>
									<input name="dispListTrk_route" id="dispListTrk_route" size="32" class="dispInput text ui-widget-content ui-corner-all">
								</div>

								<!-- Date witin range -->
								<div class="dispListTrkCriteria">
									<label for="dispListTrk_dateFrom" class="labelFirst">From Date</label>
									<input name="dispListTrk_dateFrom" id="dispListTrk_dateFrom" size="10" class="text ui-widget-content ui-corner-all">
									<label for="dispListTrk_dateTo" class="labelNext">To Date</label>
									<input name="dispListTrk_dateTo" id="dispListTrk_dateTo" size="10" class="text ui-widget-content ui-corner-all">
								</div>

								<!-- Type as select items (selectable) -->
								<div class="dispListTrkCriteria">
									<label for="dispListTrk_type" class="labelFirst">Type (CTRL+Left-click for multi-select)</label>
									<ol id="dispListTrk_type" class="selectable filterItems">
										<li id="dispListTrk_type_Klettern" class="ui-widget-content">Klettern</li>
										<li id="dispListTrk_type_Ski" class="ui-widget-content">Ski</li>
										<li id="dispListTrk_type_Sport" class="ui-widget-content">Sport</li>
										<li id="dispListTrk_type_Velo" class="ui-widget-content">Velo</li>
										<li id="dispListTrk_type_Wasser" class="ui-widget-content">Wasser</li>
										<li id="dispListTrk_type_Zufuss" class="ui-widget-content first">Zufuss</li>
									</ol>
								</div>

								<!-- Subtype as select items (selectable) -->
								<div class="dispListTrkCriteria">
									<label for="dispListTrk_subtype" class="labelFirst">Subtype (CTRL+Left-click for multi-select)</label>
									<ol id="dispListTrk_subtype" class="selectable filterItems">
										<li id="dispListTrk_subtype_alpinklettern" class="ui-widget-content">Alpinklettern</li>
										<li id="dispListTrk_subtype_alpintour" class="ui-widget-content">Alpintour</li>
										<li id="dispListTrk_subtype_Hochtour" class="ui-widget-content">Hochtour</li>
										<li id="dispListTrk_subtype_Joggen" class="ui-widget-content">Joggen</li>
										<li id="dispListTrk_subtype_Mehrseilklettern" class="ui-widget-content">Mehrseilklettern</li>
										<li id="dispListTrk_subtype_Schneeschuhwanderung" class="ui-widget-content first">Schneeschuhwanderung</li>
										<li id="dispListTrk_subtype_Schwimmen" class="ui-widget-content">Schwimmen</li>
										<li id="dispListTrk_subtype_Skihochtour" class="ui-widget-content">Skihochtour</li>
										<li id="dispListTrk_subtype_Skitour" class="ui-widget-content">Skitour</li>
										<li id="dispListTrk_subtype_Sportklettern" class="ui-widget-content">Sportklettern</li>
										<li id="dispListTrk_subtype_Velotour" class="ui-widget-content">Velotour</li>
										<li id="dispListTrk_subtype_Wanderung" class="ui-widget-content first">Wanderung</li>
										<li id="dispListTrk_subtype_Winterwanderung" class="ui-widget-content first">Winterwanderung</li>
									</ol>
								</div>
								
								<!-- participants like (standard text field) -->
								<div class="dispListTrkCriteria">	
									<label for="dispListTrk_participants" class="labelFirst">Participants contains</label>
									<input type="text" name="dispListTrk_participants" id="dispListTrk_participants" size="32" class="dispInput text ui-widget-content ui-corner-all">
								</div>

								<!-- Country (standard text field) -->
								<div class="dispListTrkCriteria">	
									<label for="dispListTrk_country" class="labelFirst">Country like</label>
									<input type="text" name="dispListTrk_country" id="dispListTrk_country" size="20" class="dispInput text ui-widget-content ui-corner-all">
								</div>

								<!-- Button to newly display selection -->
								<div class="dispListTrkCriteria">
									<input type="submit" class="button" id="dispListTrk_NewLoadButton" value="Load" />
								</div>
								<!-- Button to add selection to existing objects -->
								<!--
									<div class="dispListTrkCriteria">
									<input type="submit" class="button applyFilterButton" id="dispListTrk_addObjButton" value="Add Tracks" />
								</div>
								-->
								<!-- Button to reset filter -->
								<!--
								<div class="dispListTrkCriteria">
									<input type="submit" class="button filterResetButton" id="dispListTrk_ResetButton" value="Reset Filter" />
								</div>-->
								
							</fieldset>
						</div>
					</div>
				</div>
			</div>

			<!-- Accordion to display different lists -->
			<div id="tabDispLists" class="tabDispLists">
				<ul>
					<li><a href="#tabDispLists_trks">Tracks</a></li>
					<li><a href="#tabDispLists_segs">Segments</a></li>
					<li><a href="#tabDispLists_part">Participants</a></li>
				</ul>
				<div id="tabDispLists_trks"></div>
				<div id="tabDispLists_segs"></div>
				<div id="tabDispLists_part"></div>
			</div>
			
		</div> <!-- End div panelLists -->

		<!-- ========================================================================== -->
		<!-- ====================== panelImport ======================================= -->
		<!-- ========================================================================== -->		
		<div id="panelImport" class="tourdbPanel">
			
			<div id="uiUplFileGps" class="uiDiv active">
				<fieldset>
					<legend class="filterHeader">Select GPX file to upload</legend>
					<p>Select a single file to upload to the tourdb</p>
					<form id="formInputFile" enctype="multipart/form-data">
						<input type="hidden" name="MAX_FILE_SIZE" value="300000" />
						<input id="inputFile" class="buttonSelectFile" name="userfile" type="file" />
					</form>

					<div class="buttonUpload">
						<input type="submit" class="button" id="buttonUploadFile" value="Upload GPX File" />
					</div>
				</fieldset>
			</div>

		</div> <!-- End div panelImport -->

		<!-- ========================================================================== -->
		<!-- ===================== panelExport ======================================== -->
		<!-- ========================================================================== -->

		<div id="panelExport" class="tourdbPanel">
			<div id="uiExport" class="uiDiv active">
				<fieldset>
					<legend class="filterHeader">Export all Objects</legend>
					<!--
					<p>Select a single file to upload to the tourdb</p>
					<form id="formInputFile" enctype="multipart/form-data">
						<input type="hidden" name="MAX_FILE_SIZE" value="300000" />
						<input id="inputFile" class="buttonSelectFile" name="userfile" type="file" />
					</form>
					-->

					<div class="buttonUpload">
						<input type="submit" class="button" id="mainButtons_exportBtnTracks01JSON" value="Export Tracks as JSON" />
					</div>
					<div class="buttonUpload">
						<input type="submit" class="button" id="mainButtons_exportBtnTracks01CSV" value="Export Tracks as CSV" />
					</div>

				</fieldset>
			</div>		

		</div> <!-- End div panelExport -->

		<!-- ========================================================================== -->
		<!-- ======================== panelMaintain =================================== -->
		<!-- ========================================================================== -->		
		<div id="panelMaintain" class="tourdbPanel">
			<p>Ich bin das panel panelMaintain</p>
		</div> <!-- End div panelMaintain -->

		<!-- ========================================================================== -->
		<!-- ======================== panelAdmin ========================================== -->
		<!-- ========================================================================== -->
		
		<div id="panelAdmin" class="tourdbPanel">
			<p>Ich bin das panel panelAdmin</p>		
		</div> <!-- End div panelAdmin -->

		<!-- ========================================================================== -->
		<!-- ======================== UI Import/Edit Track ============================ -->
		<!-- ========================================================================== -->
		
		<!-- Form to display different lists -->
		<div id="uiTrack" class="uiTrack formCenter uiDiv">
			<ul>
				<li><a href="#uiTrack_tabMain">Main</a></li>
				<li><a href="#uiTrack_tabWayp">Waypoints</a></li>
				<li><a href="#uiTrack_tabOth">Others</a></li>
			</ul>
			<p id="validateComments">Please fill / update fields.</p>
			<div id="uiTrack_tabMain">
				<fieldset>
				
					<!-- Import Tracks -->
					<legend class="filterHeader">Import Track</legend>

					<!-- Track ID -->
					<div>
						<!--<label for="uiTrack_fld_trkId" class="updTrackLabelFirst">Track ID</label>-->
						<input type="hidden" name="uiTrack_fld_trkId" id="uiTrack_fld_trkId" size="20" class="updTrackInput text ui-widget-content ui-corner-all" readonly>
					</div>

					<!-- Track Name -->
					<div>
						<label for="uiTrack_fld_trkTrackName" class="updTrackLabelFirst">Track Name</label>
						<input type="text" name="uiTrack_fld_trkTrackName" id="uiTrack_fld_trkTrackName" size="50" class="updTrackInput text ui-widget-content ui-corner-all">
					</div>
						
					<!-- Route -->
					<div>
						<label for="uiTrack_fld_trkRoute" class="updTrackLabelFirst">Route</label>
						<input type="text" name="uiTrack_fld_trkRoute" id="uiTrack_fld_trkRoute" size="50" class="updTrackInput text ui-widget-content ui-corner-all">
					</div>

					<!-- Date Begin -->
					<div>
						<label for="uiTrack_fld_trkDateBegin" class="updTrackLabelFirst">Date Begin</label>
						<input type="text" name="uiTrack_fld_trkDateBegin" id="uiTrack_fld_trkDateBegin" size="20" class="updTrackInput text ui-widget-content ui-corner-all">
					</div>

					<!-- Date Finish -->
					<div>
						<label for="uiTrack_fld_trkDateFinish" class="updTrackLabelFirst">Date Finish</label>
						<input type="text" name="uiTrack_fld_trkDateFinish" id="uiTrack_fld_trkDateFinish" size="20" class="updTrackInput text ui-widget-content ui-corner-all">
					</div>

					<!-- Saison -->
					<!--
					<div>
						<label for="uiTrack_fld_trkSaison" class="updTrackLabelFirst">Saison</label>
						<select name="trkSaison" id="uiTrack_fld_trkSaison">
							<option>2016/17 Wi</option>
							<option>2017 So</option>
							<option selected="selected">2017/18 Wi</option>
							<option>2018 So</option>
						</select>
					</div>
					-->

					<!-- Type -->
					<div>
						<label for="uiTrack_fld_trkType" class="updTrackLabelFirst">Type</label>
						<!-- <input type="text" name="uiTrack_fld_trkType" id="uiTrack_fld_trkType" size="50" class="updTrackInput text ui-widget-content ui-corner-all">-->
						<select name="trkType" id="uiTrack_fld_trkType">
							<option>Zufuss</option>
							<option selected="selected">Ski</option>
							<option>Velo</option>
							<option>Wasser</option>
							<option>Klettern</option>
							<option>Sport</option>
						</select>
					</div>

					<!-- SubType -->
					<div>
						<label for="uiTrack_fld_trkSubType" class="updTrackLabelFirst">SubType</label>
						<select name="trkSubType" id="uiTrack_fld_trkSubType">
							<option>Alpinklettern</option>
							<option>Alpintour</option>
							<option>Hochtour</option>
							<option>Joggen</option>
							<option>Mehrseilklettern</option>
							<option>Schneeschuhwanderung</option>
							<option>Schwimmen</option>
							<option>Skihochtour</option>
							<option selected="selected">Skitour</option>
							<option>Sportklettern</option>
							<option>Velotour</option>
							<option>Wanderung</option>
							<option>Winterwanderung</option>
						</select>
					</div>

					<!-- Distance -->
					<div>
						<label for="uiTrack_fld_trkDistance" class="updTrackLabelFirst">Distance</label>
						<input type="text" name="uiTrack_fld_trkDistance" id="uiTrack_fld_trkDistance" size="50" class="updTrackInput text ui-widget-content ui-corner-all">
					</div>

					<!-- Time Overall -->
					<div>
						<label for="uiTrack_fld_trkTimeOverall" class="updTrackLabelFirst">Overall Time</label>
						<input type="text" name="uiTrack_fld_trkTimeOverall" id="uiTrack_fld_trkTimeOverall" size="50" class="updTrackInput text ui-widget-content ui-corner-all">
					</div>

					<!-- TimeToPeak -->
					<div>
						<label for="uiTrack_fld_trkTimeToPeak" class="updTrackLabelFirst">Time to Peak</label>
						<input type="text" name="uiTrack_fld_trkTimeToPeak" id="uiTrack_fld_trkTimeToPeak" size="50" class="updTrackInput text ui-widget-content ui-corner-all">
					</div>

					<!-- TimeToFinish -->
					<div>
						<label for="uiTrack_fld_trkTimeToFinish" class="updTrackLabelFirst">Time To Finish</label>
						<input type="text" name="uiTrack_fld_trkTimeToFinish" id="uiTrack_fld_trkTimeToFinish" size="50" class="updTrackInput text ui-widget-content ui-corner-all">
					</div>

					<!-- Grade -->
					<div>
						<label for="uiTrack_fld_trkGrade" class="updTrackLabelFirst">Grade</label>
						<input type="text" name="uiTrack_fld_trkGrade" id="uiTrack_fld_trkGrade" size="50" class="updTrackInput text ui-widget-content ui-corner-all">
					</div>

					<!-- MeterUp -->
					<div>
						<label for="uiTrack_fld_trkMeterUp" class="updTrackLabelFirst">Meter Up</label>
						<input type="text" name="uiTrack_fld_trkMeterUp" id="uiTrack_fld_trkMeterUp" size="50" class="updTrackInput text ui-widget-content ui-corner-all">
					</div>

					<!-- MeterDown -->
					<div>
						<label for="uiTrack_fld_trkMeterDown" class="updTrackLabelFirst">Meter Down</label>
						<input type="text" name="uiTrack_fld_trkMeterDown" id="uiTrack_fld_trkMeterDown" size="50" class="updTrackInput text ui-widget-content ui-corner-all">
					</div>

					<!-- Country -->
					<div>
						<label for="uiTrack_fld_trkCountry" class="updTrackLabelFirst">Country</label>
						<input type="text" name="uiTrack_fld_trkCountry" id="uiTrack_fld_trkCountry" size="50" class="updTrackInput text ui-widget-content ui-corner-all">
					</div>

					<!-- Hidden Fields -->
					<div>
						<input type="hidden" name="uiTrack_fld_trkCoordinates" id="uiTrack_fld_trkCoordinates" size="50" class="updTrackInput text ui-widget-content ui-corner-all" readonly>
						<input type="hidden" name="uiTrack_fld_trkStartEle" id="uiTrack_fld_trkStartEle" size="50" class="updTrackInput text ui-widget-content ui-corner-all" readonly>
						<input type="hidden" name="uiTrack_fld_trkPeakEle" id="uiTrack_fld_trkPeakEle" size="50" class="updTrackInput text ui-widget-content ui-corner-all" readonly>
						<input type="hidden" name="uiTrack_fld_trkPeakTime" id="uiTrack_fld_trkPeakTime" size="50" class="updTrackInput text ui-widget-content ui-corner-all" readonly>
						<input type="hidden" name="uiTrack_fld_trkLowEle" id="uiTrack_fld_trkLowEle" size="50" class="updTrackInput text ui-widget-content ui-corner-all" readonly>
						<input type="hidden" name="uiTrack_fld_trkLowTime" id="uiTrack_fld_trkLowTime" size="50" class="updTrackInput text ui-widget-content ui-corner-all" readonly>
						<input type="hidden" name="uiTrack_fld_trkFinishEle" id="uiTrack_fld_trkFinishEle" size="50" class="updTrackInput text ui-widget-content ui-corner-all" readonly>
						<input type="hidden" name="uiTrack_fld_trkFinishTime" id="uiTrack_fld_trkFinishTime" size="50" class="updTrackInput text ui-widget-content ui-corner-all" readonly>
						<input type="hidden" name="uiTrack_fld_trkCoordTop" id="uiTrack_fld_trkCoordTop" size="50" class="updTrackInput text ui-widget-content ui-corner-all" readonly>
						<input type="hidden" name="uiTrack_fld_trkCoordBottom" id="uiTrack_fld_trkCoordBottom" size="50" class="updTrackInput text ui-widget-content ui-corner-all" readonly>
						<input type="hidden" name="uiTrack_fld_trkCoordLeft" id="uiTrack_fld_trkCoordLeft" size="50" class="updTrackInput text ui-widget-content ui-corner-all" readonly>
						<input type="hidden" name="uiTrack_fld_trkCoordRight" id="uiTrack_fld_trkCoordRight" size="50" class="updTrackInput text ui-widget-content ui-corner-all" readonly>
					</div>
				</fieldset>
			</div>
			<div id="uiTrack_tabWayp">
				<fieldset>
					<legend class="filterHeader">Add Peaks</legend>
					<div class="ui-widget" style="float:left;">
						<label for="uiTrack_peakSrch" class="updTrackLabelWayp">Peak</label>
						<input id="uiTrack_peakSrch" size="25">
					</div>
					<div id="uiTrack_peakList" style="float:left;">
					<table class="itemsTable" cellspacing="0" cellpadding="0">
						<tr>
							<td>Peak</td>
							<td></td> 
							<td></td>
						</tr>
					</table>
					</div>
				</fieldset>

				<fieldset>
					<legend class="filterHeader">Add Waypoints</legend>
					<div class="ui-widget" style="float:left;">
						<label for="uiTrack_waypSrch" class="updTrackLabelWayp">Waypoint</label>
						<input id="uiTrack_waypSrch" size="25">
					</div>
					<div id="uiTrack_waypList" style="float:left;">
					<table class="itemsTable" cellspacing="0" cellpadding="0">
						<tr>
							<td>Waypoint</td>
							<td></td>
						</tr>
					</table>
					</div>
				</fieldset>

				<fieldset>
					<legend class="filterHeader">Add Overnight Location</legend>		
					<!-- Overnight Location -->
					<div class="ui-widget" style="float:left;">
						<label for="uiTrack_locaSrch" class="updTrackLabelWayp">Overnight Loc.</label>
						<input id="uiTrack_locaSrch" size="25">
					</div>
					<div id="uiTrack_locaList" style="float:left;">
					<table class="itemsTable" cellspacing="0" cellpadding="0">
						<tr>
							<td>Location</td>
							<td></td>
						</tr>
					</table>
					</div>				
				</fieldset>

			</div>
			<div id="uiTrack_tabOth">
				<fieldset>
					<legend class="filterHeader">Participants</legend>	

					<!-- Participants -->
					<div class="ui-widget" style="float:left;">
						<label for="uiTrack_partSrch" class="updTrackLabelWayp">Participants</label>
						<input id="uiTrack_partSrch" size="25">
					</div>
					<div id="uiTrack_partList" style="float:left;">
					<table class="itemsTable" cellspacing="0" cellpadding="0">
						<tr>
							<td>Participant</td>
							<td></td>
						</tr>
					</div>	
				</table>	
				</fieldset>

				<fieldset>
					<legend class="filterHeader">Other Information</legend>
					
					<!-- Org -->
					<div>
						<label for="uiTrack_fld_trkOrg" class="updTrackLabelWayp">Organisation</label>
						<input type="text" name="uiTrack_fld_trkOrg" id="uiTrack_fld_trkOrg" size="50" class="updTrackInput text ui-widget-content ui-corner-all">
					</div>

					<!-- Event -->
					<div>
						<label for="uiTrack_fld_trkEvent" class="updTrackLabelWayp">Event</label>
						<input type="text" name="uiTrack_fld_trkEvent" id="uiTrack_fld_trkEvent" size="50" class="updTrackInput text ui-widget-content ui-corner-all">
					</div>

					<!-- Remarks -->
					<div>
						<label for="uiTrack_fld_trkRemarks" class="updTrackLabelWayp">Remarks</label>
						<input type="text" name="uiTrack_fld_trkRemarks" id="uiTrack_fld_trkRemarks" size="50" class="updTrackInput text ui-widget-content ui-corner-all">
					</div>

				</fieldset>
			</div>
			<div id="updTrk_btn" class="formCenter">
				<input type="submit" class="button" id="uiTrack_fld_save" value="Save Track" />
				<input type="submit" class="button" id="uiTrack_fld_cancel" value="Cancel import" />
			</div>
		</div>

	</section> <!-- End main -->

	<footer id="footer">
        <div id=footerText>&copy; tourdb - 2019 leuti - Version Built 1907.001</div>
		<div id="statusMessage"></div>
    </footer>
	
</body>
</html>
