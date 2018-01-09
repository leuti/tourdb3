<?php
	// Start session before ending it (for users coming back from sub pages)
	session_start();
	
	// End session
	session_destroy();
	$_SESSION = array();								// empty SESSION variable
?>

<!DOCTYPE HTML>
<!-- 
	ACTION:
	* Tool tipps when hoovering over buttons
-->
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
<body style="height: 100%;">
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
		<ul class="topicButtons">
			<li id="buttonLogin" class="topicButtonsLi active">
				<a id="a_panelLogin" class="mainButtonsA" href="#panelLogin"><h3>Login</h3></a>
			</li>
			<li id="buttonDisplay" class="topicButtonsLi">
				<a id="a_panelDisplay" class="mainButtonsA" href="#panelDisplay"><h3>Display</h3></a>
			</li>
			<li id="buttonMaintain" class="topicButtonsLi">
				<a id="a_panelMaintain" class="mainButtonsA" href="#panelMaintain"><h3>Maintain</h3></a> 
			</li>
			<li id="buttonImport" class="topicButtonsLi">
				<a id="a_panelImprt" class="mainButtonsA" href="#panelImport"><h3>Import</h3></a> 
			</li>
			<li id="buttonExport" class="topicButtonsLi">
				<a id="a_panelExport" class="mainButtonsA" href="#panelExport"><h3>Export</h3></a> 
			</li>
			<li id="buttonAdmin" class="topicButtonsLi">
				<a id="" class="mainButtonsA" href="#panelAdmin"><h3>Admin</h3></a>
			</li>
		</ul>
	</header> 

    <!-- In the div #main all page content is diplayed -->
	<section id="main" style="height: 100%;">
		
		<!-- ========================================================================== -->
		<!-- ======================== panelLogin ========================================== -->
		<!-- ========================================================================== -->
		
		<div id="panelLogin" class="tourdbPanel active">
			<div id=loginForm>

				<fieldset>
        		
					<!-- Import Tracks -->
					<legend class="loginHeader">Enter you login credentials</legend>

					<div>
						<label for="loginName" class="labelFirst">User Login</label>
						<input id="loginName" class="loginFields" type="text" size="50">
					</div>
					<div>
						<label for="loginPasswd" class="labelFirst">Password</label>
						<input id="loginPasswd" class="loginFields" type="text" size="50">
					</div>

					<div class="buttonLogin">
						<input type="submit" class="button" id="buttonLogin" value="Login" />
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
						<h3>Tracks</h3>
						<div class="accordionBackground">
							<fieldset>
								<!-- SEGMENT filter -->
								<legend class="filterHeader">Tracks</legend>

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
									<label for="dispFilTrk_dateTo" class="labelNext">From Date</label>
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
										<li id="dispFilTrk_subtype_Alpinklettern" class="ui-widget-content">Alpinklettern</li>
										<li id="dispFilTrk_subtype_Alpintour" class="ui-widget-content">Alpintour</li>
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

								<!-- Button to apply filter -->
								<div class="dispObjCriteria">
									<input type="submit" class="button" id="dispFilTrk_ApplyButton" value="Load Tracks" />
								</div>
								
							</fieldset>
						</div>
						
						<!-- Segment Section of Accordion -->
						<h3>Segments</h3>
						<div class="accordionBackground">
							<fieldset>

								<!-- SEGMENT filter -->
								<legend class="filterHeader">Segments</legend>

								<!-- segType -->
								<div class="dispObjCriteria">
									<label for="dispFilSeg_mapType" class="labelFirst">Type (CTRL+Right-click for multi-select)</label>
									<ol id="dispFilSeg_segType" class="selectable filterItems">
										<li id="segType_WA" class="ui-widget-content first">Wanderung</li>
										<li id="segType_AW" class="ui-widget-content">Alpinwanderung</li>
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
										<li id="grade_AS" class="ui-widget-content">AS</li>
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


								<!-- Button to apply filter -->
								<div class="dispObjCriteria">
									<input type="submit" class="button" id="dispFilSeg_ApplyButton" value="Apply Filter" />
								</div>

							</fieldset>
						</div>

						<!-- Segment Section of Accordion -->
						<h3>Waypoints</h3>
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
			
			<div id="pImpFileUpload" class="pImpDiv active">
				<h3 id=headerUploadFile>Select File to upload </h3>
				<form enctype="multipart/form-data">
					<input type="hidden" name="MAX_FILE_SIZE" value="30000" />
					<input id="inputFile" class="buttonSelectFile" name="userfile" type="file" />
				</form>

				<div class="buttonUpload">
					<input type="submit" class="button" id="buttonUploadFile" value="Upload File" />
				</div>
			</div>

			<div id="pImpUpdateTrack" class="pImpDiv">
				<fieldset>
        		
					<!-- Import Tracks -->
					<legend class="filterHeader">Import Track</legend>

					<!-- Track Name -->
					<div class="impUpdateCriteria">
						<label for="impUpdTrk_trkId" class="updTrackLabelFirst">Track ID</label>
						<input type="text" name="impUpdTrk_trkId" id="impUpdTrk_trkId" size="40" class="updTrackInput text ui-widget-content ui-corner-all">
					</div>

					<!-- Track Name -->
					<div class="impUpdateCriteria">
						<label for="impUpdTrk_trkTrackName" class="updTrackLabelFirst">Track Name</label>
						<input type="text" name="impUpdTrk_trkTrackName" id="impUpdTrk_trkTrackName" size="40" class="updTrackInput text ui-widget-content ui-corner-all">
					</div>
						
					<!-- Route -->
					<div class="impUpdateCriteria">
						<label for="impUpdTrk_trkRoute" class="updTrackLabelFirst">Route</label>
						<input type="text" name="impUpdTrk_trkRoute" id="impUpdTrk_trkRoute" size="40" class="updTrackInput text ui-widget-content ui-corner-all">
					</div>

					<!-- Date Begin -->
					<div class="impUpdateCriteria">
						<label for="impUpdTrk_trkDateBegin" class="updTrackLabelFirst">Date Begin</label>
						<input type="text" name="impUpdTrk_trkDateBegin" id="impUpdTrk_trkDateBegin" size="40" class="updTrackInput text ui-widget-content ui-corner-all">
					</div>

					<!-- Date Finish -->
					<div class="impUpdateCriteria">
						<label for="impUpdTrk_trkDateFinish" class="updTrackLabelFirst">Date Finish</label>
						<input type="text" name="impUpdTrk_trkDateFinish" id="impUpdTrk_trkDateFinish" size="40" class="updTrackInput text ui-widget-content ui-corner-all">
					</div>

					<!-- Saison -->
					<div class="impUpdateCriteria">
						<label for="impUpdTrk_trkSaison" class="updTrackLabelFirst">Saison</label>
						<input type="text" name="impUpdTrk_trkSaison" id="impUpdTrk_trkSaison" size="40" class="updTrackInput text ui-widget-content ui-corner-all">
					</div>

					<!-- Type -->
					<div class="impUpdateCriteria">
						<label for="impUpdTrk_trkType" class="updTrackLabelFirst">Type</label>
						<input type="text" name="impUpdTrk_trkType" id="impUpdTrk_trkType" size="40" class="updTrackInput text ui-widget-content ui-corner-all">
					</div>

					<!-- SubType -->
					<div class="impUpdateCriteria">
						<label for="impUpdTrk_trkSubType" class="updTrackLabelFirst">SubType</label>
						<input type="text" name="impUpdTrk_trkSubType" id="impUpdTrk_trkSubType" size="40" class="updTrackInput text ui-widget-content ui-corner-all">
					</div>

					<!-- Org -->
					<div class="impUpdateCriteria">
						<label for="impUpdTrk_trkOrg" class="updTrackLabelFirst">Organisation</label>
						<input type="text" name="impUpdTrk_trkOrg" id="impUpdTrk_trkOrg" size="40" class="updTrackInput text ui-widget-content ui-corner-all">
					</div>

					<!-- Overnight Location -->
					<div class="impUpdateCriteria">
						<label for="impUpdTrk_trkOvernightLoc" class="updTrackLabelFirst">Location</label>
						<input type="text" name="impUpdTrk_trkOvernightLoc" id="impUpdTrk_trkOvernightLoc" size="40" class="text ui-widget-content ui-corner-all">
					</div>

					<!-- Participants -->
					<div class="impUpdateCriteria">
						<label for="impUpdTrk_trkParticipants" class="updTrackLabelFirst">Participants</label>
						<input type="text" name="impUpdTrk_trkParticipants" id="impUpdTrk_trkParticipants" size="40" class="updTrackInput text ui-widget-content ui-corner-all">
					</div>

					<!-- Event -->
					<div class="impUpdateCriteria">
						<label for="impUpdTrk_trkEvent" class="updTrackLabelFirst">Event</label>
						<input type="text" name="impUpdTrk_trkEvent" id="impUpdTrk_trkEvent" size="40" class="updTrackInput text ui-widget-content ui-corner-all">
					</div>

					<!-- Remarks -->
					<div class="impUpdateCriteria">
						<label for="impUpdTrk_trkRemarks" class="updTrackLabelFirst">Remarks</label>
						<input type="text" name="impUpdTrk_trkRemarks" id="impUpdTrk_trkRemarks" size="40" class="updTrackInput text ui-widget-content ui-corner-all">
					</div>

					<!-- Distance -->
					<div class="impUpdateCriteria">
						<label for="impUpdTrk_trkDistance" class="updTrackLabelFirst">Distance</label>
						<input type="text" name="impUpdTrk_trkDistance" id="impUpdTrk_trkDistance" size="40" class="updTrackInput text ui-widget-content ui-corner-all">
					</div>

					<!-- Time Overall -->
					<div class="impUpdateCriteria">
						<label for="impUpdTrk_trkTimeOverall" class="updTrackLabelFirst">Overall Time</label>
						<input type="text" name="impUpdTrk_trkTimeOverall" id="impUpdTrk_trkTimeOverall" size="40" class="updTrackInput text ui-widget-content ui-corner-all">
					</div>

					<!-- TimeToTarget -->
					<div class="impUpdateCriteria">
						<label for="impUpdTrk_trkTimeToTarget" class="updTrackLabelFirst">Time to Target</label>
						<input type="text" name="impUpdTrk_trkTimeToTarget" id="impUpdTrk_trkTimeToTarget" size="40" class="updTrackInput text ui-widget-content ui-corner-all">
					</div>

					<!-- TimeToEnd -->
					<div class="impUpdateCriteria">
						<label for="impUpdTrk_trkTimeToEnd" class="updTrackLabelFirst">Time To End</label>
						<input type="text" name="impUpdTrk_trkTimeToEnd" id="impUpdTrk_trkTimeToEnd" size="40" class="updTrackInput text ui-widget-content ui-corner-all">
					</div>

					<!-- Grade -->
					<div class="impUpdateCriteria">
						<label for="impUpdTrk_trkGrade" class="updTrackLabelFirst">Grade</label>
						<input type="text" name="impUpdTrk_trkGrade" id="impUpdTrk_trkGrade" size="40" class="updTrackInput text ui-widget-content ui-corner-all">
					</div>

					<!-- MeterUp -->
					<div class="impUpdateCriteria">
						<label for="impUpdTrk_trkMeterUp" class="updTrackLabelFirst">Meter Up</label>
						<input type="text" name="impUpdTrk_trkMeterUp" id="impUpdTrk_trkMeterUp" size="40" class="updTrackInput text ui-widget-content ui-corner-all">
					</div>

					<!-- MeterDown -->
					<div class="impUpdateCriteria">
						<label for="impUpdTrk_trkMeterDown" class="updTrackLabelFirst">Meter Down</label>
						<input type="text" name="impUpdTrk_trkMeterDown" id="impUpdTrk_trkMeterDown" size="40" class="updTrackInput text ui-widget-content ui-corner-all">
					</div>

					<!-- Country -->
					<div class="impUpdateCriteria">
						<label for="impUpdTrk_trkCountry" class="updTrackLabelFirst">Country</label>
						<input type="text" name="impUpdTrk_trkCountry" id="impUpdTrk_trkCountry" size="40" class="updTrackInput text ui-widget-content ui-corner-all">
					</div>

					<!-- Da Coordinates -->
					<div class="impUpdateCriteria">
						<label for="impUpdTrk_trkCoordinates" class="updTrackLabelFirst">Coordinates</label>
						<input type="text" name="impUpdTrk_trkCoordinates" id="impUpdTrk_trkCoordinates" size="40" class="updTrackInput text ui-widget-content ui-corner-all">
					</div>

					<br><br>
					<input type="submit" class="button" id="impUpdTrk_save" value="Save Track" />
					<input type="submit" class="button" id="impUpdTrk_cancel" value="Cancel import" />
				</fieldset>
			</div>

			<div id="pImpSaveStatus" class="statusMessage">
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
        <p>&copy; tourdb 3 - 2017 leuti - Version 20171227</p>
    </footer>
	
</body>
</html>
