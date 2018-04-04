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
	<title>tourdb 1.0</title>

	<link type="text/css" rel="stylesheet" href="css/jquery-ui.css">
	<link type="text/css" rel="stylesheet" href="css/tourdb_main.css">	
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
		<ul class="navBtns_btns">
			<li id="navBtns_btn_login" class="navBtns_btn_li active" style="display:none;">
				<a id="navBtns_btn_login_a" class="navBtns_btns_a" href="#panelLogin"><h2>Login</h2></a>
			</li>
			<li id="navBtns_btn_map" class="navBtns_btn_li loginReq">
				<a id="navBtns_btn_dispmap_a" class="navBtns_btns_a" href="#panelDisplayMap"><h2>Map</h2></a>
			</li>
			<li id="buttonDisplayLists" class="navBtns_btn_li loginReq">
				<a id="navBtns_btn_displists_a" class="navBtns_btns_a" href="#panelDisplayLists"><h2>Lists</h2></a> 
			</li>			
			<li id="navBtns_btn_import" class="navBtns_btn_li loginReq">
				<a id="navBtns_btn_import_a" class="navBtns_btns_a" href="#panelImport"><h2>Import</h2></a> 
			</li>
			
			<li id="buttonExport" class="navBtns_btn_li loginReq">
				<a id="a_panelExport" class="navBtns_btns_a" href="#panelExport"><h2>Export</h2></a> 
			</li>

			<!--
			<li id="buttonMaintain" class="navBtns_btn_li loginReq">
				<a id="a_panelMaintain" class="navBtns_btns_a" href="#panelMaintain"><h2>Maintain</h2></a> 
			</li>			
			<li id="buttonAdmin" class="navBtns_btn_li loginReq">
				<a id="" class="navBtns_btns_a" href="#panelAdmin"><h2>Admin</h2></a>
			</li>
			-->
		</ul>
	</header> 

    <!-- In the div #main all page content is diplayed -->
	<section id="main"> <!-- style="height: 100%;">-->
		
		<!-- ========================================================================== -->
		<!-- ======================== panelLogin ========================================== -->
		<!-- ========================================================================== -->
		<div id="panelLogin" class="tourdbPanel active">
			<div id=loginForm class="formCenter">
				<fieldset>
        		
					<!-- Import Tracks -->
					<legend class="filterHeader">Enter you login credentials</legend>

					<div>
						<label for="loginName" class="labelFirst">User Login</label>
						<input id="loginName" class="loginFields" type="text" size="50">
					</div>
					<div>
						<label for="loginPasswd" class="labelFirst">Password</label>
						<input id="loginPasswd" class="loginFields" type="password" size="50">
					</div>

					<div class="navBtns_btn_login">
						<input type="submit" class="button" id="navBtns_btn_login" value="Login" />
					</div>
					
					<div id="loginStatus" class="statusMessage">	
					</div>
				</fieldset>
				
			</div>
			
		</div> <!-- End div panelLogin -->

		<!-- ========================================================================== -->
		<!-- ========================== panelDisplayMap ================================== -->
		<!-- ========================================================================== -->
		<div id="panelDisplayMap" class="tourdbPanel">
			
			<!-- Div for Menu buttons -->
			<div id="contObjectSelector">
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

		</div> <!-- End div panelDisplayLists -->

		<!-- ========================================================================== -->
		<!-- ======================== panelDisplayLists =================================== -->
		<!-- ========================================================================== -->		
		<div id="panelDisplayLists" class="tourdbPanel">

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

			<!-- Form to display different lists -->
			<div id="uiEditTrk" class="formCenter uiDiv">
				<ul>
					<li><a href="#uiEditTrk_tabMain">Main</a></li>
					<li><a href="#uiEditTrk_tabWayp">Waypoints</a></li>
					<li><a href="#uiEditTrk_tabOth">Others</a></li>
				</ul>
				<p id="validateComments">Please fill / update fields.</p>
				<div id="uiEditTrk_tabMain">
					<fieldset>
					
						<!-- Import Tracks -->
						<legend class="filterHeader">Import Track</legend>

						<!-- Track Name -->
						<div>
							<label for="uiEditTrk_fld_trkId" class="updTrackLabelFirst">Track ID</label>
							<input type="text" name="uiEditTrk_fld_trkId" id="uiEditTrk_fld_trkId" size="20" class="updTrackInput text ui-widget-content ui-corner-all" readonly>
						</div>

						<!-- Track Name -->
						<div>
							<label for="uiEditTrk_fld_trkTrackName" class="updTrackLabelFirst">Track Name</label>
							<input type="text" name="uiEditTrk_fld_trkTrackName" id="uiEditTrk_fld_trkTrackName" size="50" class="updTrackInput text ui-widget-content ui-corner-all">
						</div>
							
						<!-- Route -->
						<div>
							<label for="uiEditTrk_fld_trkRoute" class="updTrackLabelFirst">Route</label>
							<input type="text" name="uiEditTrk_fld_trkRoute" id="uiEditTrk_fld_trkRoute" size="50" class="updTrackInput text ui-widget-content ui-corner-all">
						</div>

						<!-- Date Begin -->
						<div>
							<label for="uiEditTrk_fld_trkDateBegin" class="updTrackLabelFirst">Date Begin</label>
							<input type="text" name="uiEditTrk_fld_trkDateBegin" id="uiEditTrk_fld_trkDateBegin" size="20" class="updTrackInput text ui-widget-content ui-corner-all">
						</div>

						<!-- Date Finish -->
						<div>
							<label for="uiEditTrk_fld_trkDateFinish" class="updTrackLabelFirst">Date Finish</label>
							<input type="text" name="uiEditTrk_fld_trkDateFinish" id="uiEditTrk_fld_trkDateFinish" size="20" class="updTrackInput text ui-widget-content ui-corner-all">
						</div>

						<!-- Saison -->
						<div>
							<label for="uiEditTrk_fld_trkSaison" class="updTrackLabelFirst">Saison</label>
							<select name="trkSaison" id="uiEditTrk_fld_trkSaison">
								<option>2016/17 Wi</option>
								<option>2017 So</option>
								<option selected="selected">2017/18 Wi</option>
								<option>2018 So</option>
							</select>
						</div>

						<!-- Type -->
						<div>
							<label for="uiEditTrk_fld_trkType" class="updTrackLabelFirst">Type</label>
							<!-- <input type="text" name="uiEditTrk_fld_trkType" id="uiEditTrk_fld_trkType" size="50" class="updTrackInput text ui-widget-content ui-corner-all">-->
							<select name="trkType" id="uiEditTrk_fld_trkType">
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
							<label for="uiEditTrk_fld_trkSubType" class="updTrackLabelFirst">SubType</label>
							<select name="trkSubType" id="uiEditTrk_fld_trkSubType">
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
							<label for="uiEditTrk_fld_trkDistance" class="updTrackLabelFirst">Distance</label>
							<input type="text" name="uiEditTrk_fld_trkDistance" id="uiEditTrk_fld_trkDistance" size="50" class="updTrackInput text ui-widget-content ui-corner-all">
						</div>

						<!-- Time Overall -->
						<div>
							<label for="uiEditTrk_fld_trkTimeOverall" class="updTrackLabelFirst">Overall Time</label>
							<input type="text" name="uiEditTrk_fld_trkTimeOverall" id="uiEditTrk_fld_trkTimeOverall" size="50" class="updTrackInput text ui-widget-content ui-corner-all">
						</div>

						<!-- TimeToPeak -->
						<div>
							<label for="uiEditTrk_fld_trkTimeToPeak" class="updTrackLabelFirst">Time to Peak</label>
							<input type="text" name="uiEditTrk_fld_trkTimeToPeak" id="uiEditTrk_fld_trkTimeToPeak" size="50" class="updTrackInput text ui-widget-content ui-corner-all">
						</div>

						<!-- TimeToFinish -->
						<div>
							<label for="uiEditTrk_fld_trkTimeToFinish" class="updTrackLabelFirst">Time To Finish</label>
							<input type="text" name="uiEditTrk_fld_trkTimeToFinish" id="uiEditTrk_fld_trkTimeToFinish" size="50" class="updTrackInput text ui-widget-content ui-corner-all">
						</div>

						<!-- Grade -->
						<div>
							<label for="uiEditTrk_fld_trkGrade" class="updTrackLabelFirst">Grade</label>
							<input type="text" name="uiEditTrk_fld_trkGrade" id="uiEditTrk_fld_trkGrade" size="50" class="updTrackInput text ui-widget-content ui-corner-all">
						</div>

						<!-- MeterUp -->
						<div>
							<label for="uiEditTrk_fld_trkMeterUp" class="updTrackLabelFirst">Meter Up</label>
							<input type="text" name="uiEditTrk_fld_trkMeterUp" id="uiEditTrk_fld_trkMeterUp" size="50" class="updTrackInput text ui-widget-content ui-corner-all">
						</div>

						<!-- MeterDown -->
						<div>
							<label for="uiEditTrk_fld_trkMeterDown" class="updTrackLabelFirst">Meter Down</label>
							<input type="text" name="uiEditTrk_fld_trkMeterDown" id="uiEditTrk_fld_trkMeterDown" size="50" class="updTrackInput text ui-widget-content ui-corner-all">
						</div>

						<!-- Country -->
						<div>
							<label for="uiEditTrk_fld_trkCountry" class="updTrackLabelFirst">Country</label>
							<input type="text" name="uiEditTrk_fld_trkCountry" id="uiEditTrk_fld_trkCountry" size="50" class="updTrackInput text ui-widget-content ui-corner-all">
						</div>

						<!-- Coordinates -->
						<div>
							<label for="uiEditTrk_fld_trkCoordinates" class="updTrackLabelFirst">Coordinates</label>
							<input type="text" name="uiEditTrk_fld_trkCoordinates" id="uiEditTrk_fld_trkCoordinates" size="50" class="updTrackInput text ui-widget-content ui-corner-all" readonly>
						</div>

					</fieldset>
				</div>
				<div id="uiEditTrk_tabWayp">
					<fieldset>
						<legend class="filterHeader">Add Peaks</legend>
						<div class="ui-widget" style="float:left;">
							<label for="uiEditTrk_peakSrch" class="updTrackLabelWayp">Peak</label>
							<input id="uiEditTrk_peakSrch" size="25">
				  		</div>
						<div id="uiEditTrk_peakList" style="float:left;">
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
							<label for="uiEditTrk_waypSrch" class="updTrackLabelWayp">Waypoint</label>
							<input id="uiEditTrk_waypSrch" size="25">
				  		</div>
						<div id="uiEditTrk_waypList" style="float:left;">
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
							<label for="uiEditTrk_locaSrch" class="updTrackLabelWayp">Overnight Loc.</label>
							<input id="uiEditTrk_locaSrch" size="25">
				  		</div>
						<div id="uiEditTrk_locaList" style="float:left;">
						<table class="itemsTable" cellspacing="0" cellpadding="0">
							<tr>
            					<td>Location</td>
            					<td></td>
							</tr>
						</table>
						</div>				
					</fieldset>

				</div>
				<div id="uiEditTrk_tabOth">
					<fieldset>
						<legend class="filterHeader">Participants</legend>	

						<!-- Participants -->
						<div class="ui-widget" style="float:left;">
							<label for="uiEditTrk_partSrch" class="updTrackLabelWayp">Participants</label>
							<input id="uiEditTrk_partSrch" size="25">
				  		</div>
						<div id="uiEditTrk_partList" style="float:left;">
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
							<label for="uiEditTrk_fld_trkOrg" class="updTrackLabelWayp">Organisation</label>
							<input type="text" name="uiEditTrk_fld_trkOrg" id="uiEditTrk_fld_trkOrg" size="50" class="updTrackInput text ui-widget-content ui-corner-all">
						</div>

						<!-- Event -->
						<div>
							<label for="uiEditTrk_fld_trkEvent" class="updTrackLabelWayp">Event</label>
							<input type="text" name="uiEditTrk_fld_trkEvent" id="uiEditTrk_fld_trkEvent" size="50" class="updTrackInput text ui-widget-content ui-corner-all">
						</div>

						<!-- Remarks -->
						<div>
							<label for="uiEditTrk_fld_trkRemarks" class="updTrackLabelWayp">Remarks</label>
							<input type="text" name="uiEditTrk_fld_trkRemarks" id="uiEditTrk_fld_trkRemarks" size="50" class="updTrackInput text ui-widget-content ui-corner-all">
						</div>

					</fieldset>
				</div>
				<div id="updTrk_btn" class="formCenter">
					<input type="submit" class="button" id="uiEditTrk_fld_save" value="Save Track" />
					<input type="submit" class="button" id="uiEditTrk_fld_cancel" value="Cancel import" />
				</div>
			</div>
			
		</div> <!-- End div panelDisplayLists -->

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

				<!--
				<fieldset>
					<legend class="filterHeader">Select JSON file to upload</legend>
					<p>Select a JSON file to upload to the tourdb</p>
					<form id="formInputFileJSON" enctype="multipart/form-data">
						<input type="hidden" name="MAX_FILE_SIZE" value="300000" />
						<input id="inputFileJSON" class="buttonSelectFile" name="userfileJSON" type="file" />
					</form>

					<div class="buttonUpload">
						<input type="submit" class="button" id="buttonUploadFileJSON" value="Upload JSON File" />
					</div>
				</fieldset>
				-->
			</div>
			
			<div id="uiImpTrk" class="formCenter uiDiv">
				<ul>
					<li><a href="#uiImpTrk_tabMain">Main</a></li>
					<li><a href="#uiImpTrk_tabWayp">Waypoints</a></li>
					<li><a href="#uiImpTrk_tabOth">Others</a></li>
				</ul>
				<p id="validateComments">Please fill / update fields.</p>
				<div id="uiImpTrk_tabMain">
					<fieldset>
					
						<!-- Import Tracks -->
						<legend class="filterHeader">Import Track</legend>

						<!-- Track Name -->
						<div>
							<label for="uiImpTrk_fld_trkId" class="updTrackLabelFirst">Track ID</label>
							<input type="text" name="uiImpTrk_fld_trkId" id="uiImpTrk_fld_trkId" size="20" class="updTrackInput text ui-widget-content ui-corner-all" readonly>
						</div>

						<!-- Track Name -->
						<div>
							<label for="uiImpTrk_fld_trkTrackName" class="updTrackLabelFirst">Track Name</label>
							<input type="text" name="uiImpTrk_fld_trkTrackName" id="uiImpTrk_fld_trkTrackName" size="50" class="updTrackInput text ui-widget-content ui-corner-all">
						</div>
							
						<!-- Route -->
						<div>
							<label for="uiImpTrk_fld_trkRoute" class="updTrackLabelFirst">Route</label>
							<input type="text" name="uiImpTrk_fld_trkRoute" id="uiImpTrk_fld_trkRoute" size="50" class="updTrackInput text ui-widget-content ui-corner-all">
						</div>

						<!-- Date Begin -->
						<div>
							<label for="uiImpTrk_fld_trkDateBegin" class="updTrackLabelFirst">Date Begin</label>
							<input type="text" name="uiImpTrk_fld_trkDateBegin" id="uiImpTrk_fld_trkDateBegin" size="20" class="updTrackInput text ui-widget-content ui-corner-all">
						</div>

						<!-- Date Finish -->
						<div>
							<label for="uiImpTrk_fld_trkDateFinish" class="updTrackLabelFirst">Date Finish</label>
							<input type="text" name="uiImpTrk_fld_trkDateFinish" id="uiImpTrk_fld_trkDateFinish" size="20" class="updTrackInput text ui-widget-content ui-corner-all">
						</div>

						<!-- Saison -->
						<div>
							<label for="uiImpTrk_fld_trkSaison" class="updTrackLabelFirst">Saison</label>
							<select name="trkSaison" id="uiImpTrk_fld_trkSaison">
								<option>2016/17 Wi</option>
								<option>2017 So</option>
								<option selected="selected">2017/18 Wi</option>
								<option>2018 So</option>
							</select>
						</div>

						<!-- Type -->
						<div>
							<label for="uiImpTrk_fld_trkType" class="updTrackLabelFirst">Type</label>
							<!-- <input type="text" name="uiImpTrk_fld_trkType" id="uiImpTrk_fld_trkType" size="50" class="updTrackInput text ui-widget-content ui-corner-all">-->
							<select name="trkType" id="uiImpTrk_fld_trkType">
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
							<label for="uiImpTrk_fld_trkSubType" class="updTrackLabelFirst">SubType</label>
							<select name="trkSubType" id="uiImpTrk_fld_trkSubType">
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
							<label for="uiImpTrk_fld_trkDistance" class="updTrackLabelFirst">Distance</label>
							<input type="text" name="uiImpTrk_fld_trkDistance" id="uiImpTrk_fld_trkDistance" size="50" class="updTrackInput text ui-widget-content ui-corner-all">
						</div>

						<!-- Time Overall -->
						<div>
							<label for="uiImpTrk_fld_trkTimeOverall" class="updTrackLabelFirst">Overall Time</label>
							<input type="text" name="uiImpTrk_fld_trkTimeOverall" id="uiImpTrk_fld_trkTimeOverall" size="50" class="updTrackInput text ui-widget-content ui-corner-all">
						</div>

						<!-- TimeToPeak -->
						<div>
							<label for="uiImpTrk_fld_trkTimeToPeak" class="updTrackLabelFirst">Time to Peak</label>
							<input type="text" name="uiImpTrk_fld_trkTimeToPeak" id="uiImpTrk_fld_trkTimeToPeak" size="50" class="updTrackInput text ui-widget-content ui-corner-all">
						</div>

						<!-- TimeToFinish -->
						<div>
							<label for="uiImpTrk_fld_trkTimeToFinish" class="updTrackLabelFirst">Time To Finish</label>
							<input type="text" name="uiImpTrk_fld_trkTimeToFinish" id="uiImpTrk_fld_trkTimeToFinish" size="50" class="updTrackInput text ui-widget-content ui-corner-all">
						</div>

						<!-- Grade -->
						<div>
							<label for="uiImpTrk_fld_trkGrade" class="updTrackLabelFirst">Grade</label>
							<input type="text" name="uiImpTrk_fld_trkGrade" id="uiImpTrk_fld_trkGrade" size="50" class="updTrackInput text ui-widget-content ui-corner-all">
						</div>

						<!-- MeterUp -->
						<div>
							<label for="uiImpTrk_fld_trkMeterUp" class="updTrackLabelFirst">Meter Up</label>
							<input type="text" name="uiImpTrk_fld_trkMeterUp" id="uiImpTrk_fld_trkMeterUp" size="50" class="updTrackInput text ui-widget-content ui-corner-all">
						</div>

						<!-- MeterDown -->
						<div>
							<label for="uiImpTrk_fld_trkMeterDown" class="updTrackLabelFirst">Meter Down</label>
							<input type="text" name="uiImpTrk_fld_trkMeterDown" id="uiImpTrk_fld_trkMeterDown" size="50" class="updTrackInput text ui-widget-content ui-corner-all">
						</div>

						<!-- Country -->
						<div>
							<label for="uiImpTrk_fld_trkCountry" class="updTrackLabelFirst">Country</label>
							<input type="text" name="uiImpTrk_fld_trkCountry" id="uiImpTrk_fld_trkCountry" size="50" class="updTrackInput text ui-widget-content ui-corner-all">
						</div>

						<!-- Coordinates -->
						<div>
							<label for="uiImpTrk_fld_trkCoordinates" class="updTrackLabelFirst">Coordinates</label>
							<input type="text" name="uiImpTrk_fld_trkCoordinates" id="uiImpTrk_fld_trkCoordinates" size="50" class="updTrackInput text ui-widget-content ui-corner-all" readonly>
						</div>

					</fieldset>
				</div>
				<div id="uiImpTrk_tabWayp">
					<fieldset>
						<legend class="filterHeader">Add Peaks</legend>
						<div class="ui-widget" style="float:left;">
							<label for="uiImpTrk_peakSrch" class="updTrackLabelWayp">Peak</label>
							<input id="uiImpTrk_peakSrch" size="25">
				  		</div>
						<div id="uiImpTrk_peakList" style="float:left;">
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
							<label for="uiImpTrk_waypSrch" class="updTrackLabelWayp">Waypoint</label>
							<input id="uiImpTrk_waypSrch" size="25">
				  		</div>
						<div id="uiImpTrk_waypList" style="float:left;">
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
							<label for="uiImpTrk_locaSrch" class="updTrackLabelWayp">Overnight Loc.</label>
							<input id="uiImpTrk_locaSrch" size="25">
				  		</div>
						<div id="uiImpTrk_locaList" style="float:left;">
						<table class="itemsTable" cellspacing="0" cellpadding="0">
							<tr>
            					<td>Location</td>
            					<td></td>
							</tr>
						</table>
						</div>				
					</fieldset>

				</div>
				<div id="uiImpTrk_tabOth">
					<fieldset>
						<legend class="filterHeader">Participants</legend>	

						<!-- Participants -->
						<div class="ui-widget" style="float:left;">
							<label for="uiImpTrk_partSrch" class="updTrackLabelWayp">Participants</label>
							<input id="uiImpTrk_partSrch" size="25">
				  		</div>
						<div id="uiImpTrk_partList" style="float:left;">
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
							<label for="uiImpTrk_fld_trkOrg" class="updTrackLabelWayp">Organisation</label>
							<input type="text" name="uiImpTrk_fld_trkOrg" id="uiImpTrk_fld_trkOrg" size="50" class="updTrackInput text ui-widget-content ui-corner-all">
						</div>

						<!-- Event -->
						<div>
							<label for="uiImpTrk_fld_trkEvent" class="updTrackLabelWayp">Event</label>
							<input type="text" name="uiImpTrk_fld_trkEvent" id="uiImpTrk_fld_trkEvent" size="50" class="updTrackInput text ui-widget-content ui-corner-all">
						</div>

						<!-- Remarks -->
						<div>
							<label for="uiImpTrk_fld_trkRemarks" class="updTrackLabelWayp">Remarks</label>
							<input type="text" name="uiImpTrk_fld_trkRemarks" id="uiImpTrk_fld_trkRemarks" size="50" class="updTrackInput text ui-widget-content ui-corner-all">
						</div>

					</fieldset>
				</div>
				<div id="updTrk_btn" class="formCenter">
					<input type="submit" class="button" id="uiImpTrk_fld_save" value="Save Track" />
					<input type="submit" class="button" id="uiImpTrk_fld_cancel" value="Cancel import" />
				</div>
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
						<input type="submit" class="button" id="buttonExportTracks01JSON" value="Export Tracks as JSON" />
					</div>
					<div class="buttonUpload">
						<input type="submit" class="button" id="buttonExportTracks01CSV" value="Export Tracks as CSV" />
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

	</section> <!-- End main -->

	<footer id="footer">
        <div id=footerText>&copy; tourdb - 2018 leuti - Version 1.0 - 20180331</div>
		<div id="statusMessage"></div>
    </footer>
	
</body>
</html>
