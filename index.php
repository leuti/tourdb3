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
	<title>Tour DB</title>
	<!-- from https://www.jqwidgets.com/ -->
    <!-- <link rel="stylesheet" href="jqw/jqwidgets/styles/jqx.base.css" type="text/css" />-->

	<link type="text/css" rel="stylesheet" href="css/jquery-ui.css">
	<link type="text/css" rel="stylesheet" href="css/tourdb_main.css">	
</head>
<body> <!--style="height: 100%;">-->
	<!-- load jquery sources --> 
	<script type="text/javascript" src="js/jquery-3.1.0.js"></script> <!-- JQuery from local server -->  <!-- local -->
	<script type="text/javascript" src="js/jquery-ui.js"></script> <!-- JQuery UI from local server --> <!-- local -->
	<!-- <script type="text/javascript" src="jqw/scripts/demos.js"> --> </script>  <!-- uncelear if the whole jqw folder is required for jquery -->

	<!-- load swissmap sources -->
	<!-- <script src="//api3.geo.admin.ch/loader.js"></script> --> <!-- Swissmap javascript --> 
	<script src="http://api3.geo.admin.ch/loader.js?lang=en" type="text/javascript"></script>

	<!-- from https://www.jqwidgets.com/ -->
    <!-- <script type="text/javascript" src="jqw/jqwidgets/jqxcore.js"></script>-->
    <!--<script type="text/javascript" src="jqw/jqwidgets/jqxsplitter.js"></script>-->
   
	<!--<script type="text/javascript" src="js/bootstrap.min.js"></script>--> <!-- local -->
	<script type="text/javascript" src="js/tourdb.js"></script> <!-- tourdb code -->

	<!--<script src="//code.jquery.com/jquery-2.2.0.min.js"></script> -->  <!-- CDN -->
	<!-- <script src="https://code.jquery.com/ui/1.12.0/jquery-ui.js"></script> -->  <!-- CDN -->   
	<!-- <script src="//netdna.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script> --> <!-- CDN -->
	
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
			<li id="navBtns_btn_diplay" class="navBtns_btn_li loginReq">
				<a id="navBtns_btn_diplay_a" class="navBtns_btns_a" href="#panelDisplay"><h2>Display</h2></a>
			</li>
			<li id="navBtns_btn_import" class="navBtns_btn_li loginReq">
				<a id="navBtns_btn_import_a" class="navBtns_btns_a" href="#panelImport"><h2>Import</h2></a> 
			</li>
			<!--
			<li id="buttonExport" class="navBtns_btn_li loginReq">
				<a id="a_panelExport" class="navBtns_btns_a" href="#panelExport"><h2>Export</h2></a> 
			</li>

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
		<!-- ========================== panelDisplay ================================== -->
		<!-- ========================================================================== -->
		<div id="panelDisplay" class="tourdbPanel">
			
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
									<label for="dispFilTrk_type" class="labelFirst">Type (CTRL+Right-click for multi-select)</label>
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
									<label for="dispFilTrk_subtype" class="labelFirst">Subtype (CTRL+Right-click for multi-select)</label>
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
									<input type="submit" class="button applyFilterButton" id="dispFilTrk_NewLoadButton" value="New Load" />
								</div>
								<!-- Button to add selection to existing objects -->
								<div class="dispObjCriteria">
									<input type="submit" class="button applyFilterButton" id="dispFilTrk_addObjButton" value="Add Tracks" />
								</div>
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

								<!-- segType -->
								<div class="dispObjCriteria">
									<label for="dispFilSeg_mapType" class="labelFirst">Type (CTRL+Right-click for multi-select)</label>
									<ol id="dispFilSeg_segType" class="selectable filterItems">
										<li id="segType_WA" class="ui-widget-content first">Wanderung</li>
										<li id="segType_aW" class="ui-widget-content">Alpinwanderung</li>
										<li id="segType_HT" class="ui-widget-content">Hochtour</li>
										<li id="segType_ST" class="ui-widget-content">Skitour</li>
										<li id="segType_SS" class="ui-widget-content">Schneeschuhtour</li>
										<!--<li class="ui-widget-content">alle</li>-->
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
									<label for="dispFilSeg_startLocType" class="labelFirst">Start Type (CTRL+Right-click for multi-select)</label>
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
									<label for="dispFilSeg_targetLocType" class="labelFirst">Target Type (CTRL+Right-click for multi-select)</label>
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
									<label for="dispFilSeg_grade" class="labelFirst">Grade (CTRL+Right-click for multi-select)</label>
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
									<label for="dispFilSeg_climbGrade" class="labelFirst">Climbing Grade (CTRL+Right-click for multi-select)</label>
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
						<h2>Waypoints</h2>
						<div class="accordionBackground">
							<p>Cras dictum. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Aenean lacinia mauris vel est. </p><p>Suspendisse eu nisl. Nullam ut libero. Integer dignissim consequat lectus. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. </p>
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

		</div> <!-- End div panelDisplay -->

		<!-- ========================================================================== -->
		<!-- ======================== panelMaintain =================================== -->
		<!-- ========================================================================== -->		
		<div id="panelMaintain" class="tourdbPanel">
			<p>Ich bin das panel panelMaintain</p>
		</div> <!-- End div panelMaintain -->

		<!-- ========================================================================== -->
		<!-- ====================== panelImport ======================================= -->
		<!-- ========================================================================== -->		
		<div id="panelImport" class="tourdbPanel">
			
			<div id="uiUplFileGps" class="uiDiv active">
				<fieldset>
					<legend class="filterHeader">Select File to upload</legend>
					<p>Select a single file to upload to the tourdb</p>
					<form id="formInputFile" enctype="multipart/form-data">
						<input type="hidden" name="MAX_FILE_SIZE" value="300000" />
						<input id="inputFile" class="buttonSelectFile" name="userfile" type="file" />
					</form>

					<div class="buttonUpload">
						<input type="submit" class="button" id="buttonUploadFile" value="Upload File" />
					</div>
				</fieldset>
			</div>
			
			<div id="uiAdmTrk" class="formCenter">
				<ul>
					<li><a href="#uiAdmTrk_tabMain">Main</a></li>
					<li><a href="#uiAdmTrk_tabWayp">Waypoints</a></li>
					<li><a href="#uiAdmTrk_tabOth">Others</a></li>
				</ul>
				<div id="uiAdmTrk_tabMain">
					<fieldset>
					
						<!-- Import Tracks -->
						<legend class="filterHeader">Import Track</legend>

						<!-- Track Name -->
						<div>
							<label for="uiAdmTrk_fld_trkId" class="updTrackLabelFirst">Track ID</label>
							<input type="text" name="uiAdmTrk_fld_trkId" id="uiAdmTrk_fld_trkId" size="40" class="updTrackInput text ui-widget-content ui-corner-all">
						</div>

						<!-- Track Name -->
						<div>
							<label for="uiAdmTrk_fld_trkTrackName" class="updTrackLabelFirst">Track Name</label>
							<input type="text" name="uiAdmTrk_fld_trkTrackName" id="uiAdmTrk_fld_trkTrackName" size="40" class="updTrackInput text ui-widget-content ui-corner-all">
						</div>
							
						<!-- Route -->
						<div>
							<label for="uiAdmTrk_fld_trkRoute" class="updTrackLabelFirst">Route</label>
							<input type="text" name="uiAdmTrk_fld_trkRoute" id="uiAdmTrk_fld_trkRoute" size="40" class="updTrackInput text ui-widget-content ui-corner-all">
						</div>

						<!-- Date Begin -->
						<div>
							<label for="uiAdmTrk_fld_trkDateBegin" class="updTrackLabelFirst">Date Begin</label>
							<input type="text" name="uiAdmTrk_fld_trkDateBegin" id="uiAdmTrk_fld_trkDateBegin" size="40" class="updTrackInput text ui-widget-content ui-corner-all">
						</div>

						<!-- Date Finish -->
						<div>
							<label for="uiAdmTrk_fld_trkDateFinish" class="updTrackLabelFirst">Date Finish</label>
							<input type="text" name="uiAdmTrk_fld_trkDateFinish" id="uiAdmTrk_fld_trkDateFinish" size="40" class="updTrackInput text ui-widget-content ui-corner-all">
						</div>

						<!-- Saison -->
						<div>
							<label for="uiAdmTrk_fld_trkSaison" class="updTrackLabelFirst">Saison</label>
							<input type="text" name="uiAdmTrk_fld_trkSaison" id="uiAdmTrk_fld_trkSaison" size="40" class="updTrackInput text ui-widget-content ui-corner-all">
						</div>

						<!-- Type -->
						<div>
							<label for="uiAdmTrk_fld_trkType" class="updTrackLabelFirst">Type</label>
							<input type="text" name="uiAdmTrk_fld_trkType" id="uiAdmTrk_fld_trkType" size="40" class="updTrackInput text ui-widget-content ui-corner-all">
						</div>

						<!-- SubType -->
						<div>
							<label for="uiAdmTrk_fld_trkSubType" class="updTrackLabelFirst">SubType</label>
							<input type="text" name="uiAdmTrk_fld_trkSubType" id="uiAdmTrk_fld_trkSubType" size="40" class="updTrackInput text ui-widget-content ui-corner-all">
						</div>

						<!-- Org -->
						<div>
							<label for="uiAdmTrk_fld_trkOrg" class="updTrackLabelFirst">Organisation</label>
							<input type="text" name="uiAdmTrk_fld_trkOrg" id="uiAdmTrk_fld_trkOrg" size="40" class="updTrackInput text ui-widget-content ui-corner-all">
						</div>

						<!-- Overnight Location -->
						<div>
							<label for="uiAdmTrk_fld_trkOvernightLoc" class="updTrackLabelFirst">Location</label>
							<input type="text" name="uiAdmTrk_fld_trkOvernightLoc" id="uiAdmTrk_fld_trkOvernightLoc" size="40" class="text ui-widget-content ui-corner-all">
						</div>

						<!-- Participants -->
						<div>
							<label for="uiAdmTrk_fld_trkParticipants" class="updTrackLabelFirst">Participants</label>
							<input type="text" name="uiAdmTrk_fld_trkParticipants" id="uiAdmTrk_fld_trkParticipants" size="40" class="updTrackInput text ui-widget-content ui-corner-all">
						</div>

						<!-- Event -->
						<div>
							<label for="uiAdmTrk_fld_trkEvent" class="updTrackLabelFirst">Event</label>
							<input type="text" name="uiAdmTrk_fld_trkEvent" id="uiAdmTrk_fld_trkEvent" size="40" class="updTrackInput text ui-widget-content ui-corner-all">
						</div>

						<!-- Remarks -->
						<div>
							<label for="uiAdmTrk_fld_trkRemarks" class="updTrackLabelFirst">Remarks</label>
							<input type="text" name="uiAdmTrk_fld_trkRemarks" id="uiAdmTrk_fld_trkRemarks" size="40" class="updTrackInput text ui-widget-content ui-corner-all">
						</div>

						<!-- Distance -->
						<div>
							<label for="uiAdmTrk_fld_trkDistance" class="updTrackLabelFirst">Distance</label>
							<input type="text" name="uiAdmTrk_fld_trkDistance" id="uiAdmTrk_fld_trkDistance" size="40" class="updTrackInput text ui-widget-content ui-corner-all">
						</div>

						<!-- Time Overall -->
						<div>
							<label for="uiAdmTrk_fld_trkTimeOverall" class="updTrackLabelFirst">Overall Time</label>
							<input type="text" name="uiAdmTrk_fld_trkTimeOverall" id="uiAdmTrk_fld_trkTimeOverall" size="40" class="updTrackInput text ui-widget-content ui-corner-all">
						</div>

						<!-- TimeToPeak -->
						<div>
							<label for="uiAdmTrk_fld_trkTimeToPeak" class="updTrackLabelFirst">Time to Peak</label>
							<input type="text" name="uiAdmTrk_fld_trkTimeToPeak" id="uiAdmTrk_fld_trkTimeToPeak" size="40" class="updTrackInput text ui-widget-content ui-corner-all">
						</div>

						<!-- TimeToFinish -->
						<div>
							<label for="uiAdmTrk_fld_trkTimeToFinish" class="updTrackLabelFirst">Time To Finish</label>
							<input type="text" name="uiAdmTrk_fld_trkTimeToFinish" id="uiAdmTrk_fld_trkTimeToFinish" size="40" class="updTrackInput text ui-widget-content ui-corner-all">
						</div>

						<!-- Grade -->
						<div>
							<label for="uiAdmTrk_fld_trkGrade" class="updTrackLabelFirst">Grade</label>
							<input type="text" name="uiAdmTrk_fld_trkGrade" id="uiAdmTrk_fld_trkGrade" size="40" class="updTrackInput text ui-widget-content ui-corner-all">
						</div>

						<!-- MeterUp -->
						<div>
							<label for="uiAdmTrk_fld_trkMeterUp" class="updTrackLabelFirst">Meter Up</label>
							<input type="text" name="uiAdmTrk_fld_trkMeterUp" id="uiAdmTrk_fld_trkMeterUp" size="40" class="updTrackInput text ui-widget-content ui-corner-all">
						</div>

						<!-- MeterDown -->
						<div>
							<label for="uiAdmTrk_fld_trkMeterDown" class="updTrackLabelFirst">Meter Down</label>
							<input type="text" name="uiAdmTrk_fld_trkMeterDown" id="uiAdmTrk_fld_trkMeterDown" size="40" class="updTrackInput text ui-widget-content ui-corner-all">
						</div>

						<!-- Country -->
						<div>
							<label for="uiAdmTrk_fld_trkCountry" class="updTrackLabelFirst">Country</label>
							<input type="text" name="uiAdmTrk_fld_trkCountry" id="uiAdmTrk_fld_trkCountry" size="40" class="updTrackInput text ui-widget-content ui-corner-all">
						</div>

						<!-- Coordinates -->
						<div>
							<label for="uiAdmTrk_fld_trkCoordinates" class="updTrackLabelFirst">Coordinates</label>
							<input type="text" name="uiAdmTrk_fld_trkCoordinates" id="uiAdmTrk_fld_trkCoordinates" size="40" class="updTrackInput text ui-widget-content ui-corner-all">
						</div>

					</fieldset>
				</div>
				<div id="uiAdmTrk_tabWayp">
					<fieldset>
						<p>Fugus ich bin das div uiAdmTrk_btn_wayp</p>
					</fieldset>
				</div>
				<div id="uiAdmTrk_tabOth">
					<fieldset>
						<p>Fugus ich bin das div uiAdmTrk_btn_others</p>
					</fieldset>
				</div>
				<div  id="updTrk_btn">
					<input type="submit" class="button" id="uiAdmTrk_fld_save" value="Save Track" />
					<input type="submit" class="button" id="uiAdmTrk_fld_cancel" value="Cancel import" />
				</div>
			</div>
		</div> <!-- End div panelImport -->

		<!-- ========================================================================== -->
		<!-- ===================== panelExport ======================================== -->
		<!-- ========================================================================== -->

		<div id="panelExport" class="tourdbPanel">
			<p>Ich bin das panel panelExport</p>		
		</div> <!-- End div panelExport -->

		<!-- ========================================================================== -->
		<!-- ======================== panelAdmin ========================================== -->
		<!-- ========================================================================== -->
		
		<div id="panelAdmin" class="tourdbPanel">
			<p>Ich bin das panel panelAdmin</p>		
		</div> <!-- End div panelAdmin -->

	</section> <!-- End main -->

	<footer id="footer">
        <div id=footerText>&copy; tourdb 3 - 2017 leuti - Version 20171227</div>
		<div id="statusMessage"></div>
    </footer>
	
</body>
</html>
