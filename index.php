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
	<!--<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>-->
	<script type="text/javascript" src="js/jquery-3.3.1.min.js"></script>
	<script type="text/javascript" src="js/jquery-ui.min.js"></script> <!-- JQuery UI from local server --> <!-- local -->
	
	<!-- load swissmap sources -->
	<script src="http://api3.geo.admin.ch/loader.js?lang=en" type="text/javascript"></script>
	
	<script type="text/javascript" src="js/bootstrap.min.js"></script> <!-- local -->
	<script type="text/javascript" src="js/tourdb.js"></script> <!-- tourdb code -->

	<!-- ========================================================================== -->
	<!-- ========= header - containing logo and navigation buttons ================ -->
	<!-- ========================================================================== -->

	<header id="header">
		<div id="logo">
			<h1>tour<em>DB</em></h1>
		</div>
		
		<!-- Below the the main tabs for Home, Segments, Waypoints and Routes are located -->
		<ul class="mainButtons">
			<li id="mainButtons_loginBtn" class="mainButtons_li active" style="display:none;">
				<a id="mainButtons_loginBtn_a" class="mainButtons_a" href="#panelLogin"><h2>Login</h2></a>
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
		</div> <!-- End div panelLists -->

		<!-- ========================================================================== -->
		<!-- ======================== panelLists =================================== -->
		<!-- ========================================================================== -->		
		<div id="panelLists" class="tourdbPanel">

			<!-- This div shows only filter button > onclick opens the full accordion -->
			<div id="dispListTrkMenuMini" class="dispObjectSelector dispListTrkMini visible">
				<a id="dispListTrkMenuMiniOpen" href="#dispListTrkMenuMiniOpen">
					<img id="dispListTrkMenuOpenImg" src="css/images/filterLightBlue.png">
				</a> 
			</div>

			<!-- Main filter UI -->
			<div id="dispListTrkMenuLarge" class="dispObjectSelector dispListTrkOpen hidden">
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
									<li id="dispListTrk_type_Klettern" class="ui-widget-content" value="5">Klettern</li>
									<li id="dispListTrk_type_Ski" class="ui-widget-content" value="1">Ski</li>
									<li id="dispListTrk_type_Sport" class="ui-widget-content" value="6">Sport</li>
									<li id="dispListTrk_type_Velo" class="ui-widget-content" value="3">Velo</li>
									<li id="dispListTrk_type_Wasser" class="ui-widget-content" value="4">Wasser</li>
									<li id="dispListTrk_type_Zufuss" class="ui-widget-content first" value="2">Zufuss</li>
								</ol>
							</div>

							<!-- Subtype as select items (selectable) -->
							<div class="dispListTrkCriteria">
								<label for="dispListTrk_subtype" class="labelFirst">Subtype (CTRL+Left-click for multi-select)</label>
								<ol id="dispListTrk_subtype" class="selectable filterItems">
									<li id="dispListTrk_subtype_alpinklettern" class="ui-widget-content" value="13">Alpinklettern</li>
									<li id="dispListTrk_subtype_alpintour" class="ui-widget-content" value="23">Alpintour</li>
									<li id="dispListTrk_subtype_Hochtour" class="ui-widget-content" value="24">Hochtour</li>
									<li id="dispListTrk_subtype_Joggen" class="ui-widget-content" value="19">Joggen</li>
									<li id="dispListTrk_subtype_Mehrseilklettern" class="ui-widget-content" value="14">Mehrseilklettern</li>
									<li id="dispListTrk_subtype_Schneeschuhwanderung" class="ui-widget-content first" value="25">Schneeschuhwanderung</li>
									<li id="dispListTrk_subtype_Schwimmen" class="ui-widget-content" value="22">Schwimmen</li>
									<li id="dispListTrk_subtype_Skihochtour" class="ui-widget-content" value="16">Skihochtour</li>
									<li id="dispListTrk_subtype_Skitour" class="ui-widget-content" value="17">Skitour</li>
									<li id="dispListTrk_subtype_Sportklettern" class="ui-widget-content" value="15">Sportklettern</li>
									<li id="dispListTrk_subtype_Velotour" class="ui-widget-content" value="21">Velotour</li>
									<li id="dispListTrk_subtype_Wanderung" class="ui-widget-content first" value="26">Wanderung</li>
									<li id="dispListTrk_subtype_Winterwanderung" class="ui-widget-content first" value="27">Winterwanderung</li>
								</ol>
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

						</fieldset>
					</div>
				</div>
			</div>			

			<!-- Accordion to display different lists (content is generated by fetch_lists.php ) -->
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
						<input type="hidden" name="uiTrack_fld_trkId" id="uiTrack_fld_trkId" size="20" class="uiTrackValidate text ui-widget-content ui-corner-all" readonly>
					</div>

					<!-- Track Name -->
					<div>
						<label for="uiTrack_fld_trkTrackName" class="updTrackLabelFirst">Track Name</label>
						<input type="text" name="uiTrack_fld_trkTrackName" id="uiTrack_fld_trkTrackName" size="50" class="uiTrackValidate text ui-widget-content ui-corner-all">
					</div>
						
					<!-- Route -->
					<div>
						<label for="uiTrack_fld_trkRoute" class="updTrackLabelFirst">Route</label>
						<input type="text" name="uiTrack_fld_trkRoute" id="uiTrack_fld_trkRoute" size="50" class="uiTrackValidate text ui-widget-content ui-corner-all">
					</div>

					<!-- Date Begin -->
					<div>
						<label for="uiTrack_fld_trkDateBegin" class="updTrackLabelFirst">Date Begin</label>
						<input type="text" name="uiTrack_fld_trkDateBegin" id="uiTrack_fld_trkDateBegin" size="20" class="uiTrackValidate text ui-widget-content ui-corner-all">
					</div>

					<!-- Date Finish -->
					<div>
						<label for="uiTrack_fld_trkDateFinish" class="updTrackLabelFirst">Date Finish</label>
						<input type="text" name="uiTrack_fld_trkDateFinish" id="uiTrack_fld_trkDateFinish" size="20" class="uiTrackValidate text ui-widget-content ui-corner-all">
					</div>
					
					<!-- Type -->
					<div>
						<label for="uiTrack_fld_trkTypeFid" class="updTrackLabelFirst">Type</label>
						<!-- <input type="text" name="uiTrack_fld_trkTypeFid" id="uiTrack_fld_trkTypeFid" size="50" class="uiTrackValidate text ui-widget-content ui-corner-all">-->
						<select name="trkTypeFid" id="uiTrack_fld_trkTypeFid">
							<option value="0" selected="selected">select type</option>
							<option value="5">Klettern</option>
							<option value="1">Ski</option>
							<option value="6">Sport</option>
							<option value="2">Zufuss</option>
							<option value="3">Velo</option>
							<option value="4">Wasser</option>
						</select>
					</div>

					<!-- SubType -->
					<div>
						<label for="uiTrack_fld_trkSubtypeFid" class="updTrackLabelFirst">SubType</label>
						<select name="trkSubtypeFid" id="uiTrack_fld_trkSubtypeFid">
							<option value="0" selected="selected">select subtype</option>
							<option value="13">Alpinklettern</option>
							<option value="18">Alpinski</option>
							<option value="23">Alpintour</option>
							<option value="24">Hochtour</option>
							<option value="19">Joggen</option>
							<option value="14">Mehrseilklettern</option>
							<option value="20">Rennrad</option>
							<option value="25">Schneeschuhtour</option>
							<option value="22">Schwimmen</option>
							<option value="16">Skihochtour</option>
							<option value="17">Skitour</option>
							<option value="15">Sportklettern</option>
							<option value="21">Velotour</option>
							<option value="26">Wanderung</option>
							<option value="27">Winterwanderung</option>
						</select>
					</div>

					<!-- Distance -->
					<div>
						<label for="uiTrack_fld_trkDistance" class="updTrackLabelFirst">Distance</label>
						<input type="text" name="uiTrack_fld_trkDistance" id="uiTrack_fld_trkDistance" size="50" class="uiTrackValidate text ui-widget-content ui-corner-all">
					</div>

					<!-- Time Overall -->
					<div>
						<label for="uiTrack_fld_trkTimeOverall" class="updTrackLabelFirst">Overall Time</label>
						<input type="text" name="uiTrack_fld_trkTimeOverall" id="uiTrack_fld_trkTimeOverall" size="50" class="uiTrackValidate text ui-widget-content ui-corner-all">
					</div>

					<!-- TimeToPeak -->
					<div>
						<label for="uiTrack_fld_trkTimeToPeak" class="updTrackLabelFirst">Time to Peak</label>
						<input type="text" name="uiTrack_fld_trkTimeToPeak" id="uiTrack_fld_trkTimeToPeak" size="50" class="uiTrackValidate text ui-widget-content ui-corner-all">
					</div>

					<!-- TimeToFinish -->
					<div>
						<label for="uiTrack_fld_trkTimeToFinish" class="updTrackLabelFirst">Time To Finish</label>
						<input type="text" name="uiTrack_fld_trkTimeToFinish" id="uiTrack_fld_trkTimeToFinish" size="50" class="uiTrackValidate text ui-widget-content ui-corner-all">
					</div>

					<!-- Grade -->
					<div>
						<label for="uiTrack_fld_trkGrade" class="updTrackLabelFirst">Grade</label>
						<input id="uiTrack_fld_trkGrade" size="50" class="uiTrackValidate text ui-widget-content ui-corner-all">
					</div>

					<!--<div>
						<label for="uiTrack_fld_trkGrade" class="updTrackLabelFirst">Grade</label>
						<input type="text" name="uiTrack_fld_trkGrade" id="uiTrack_fld_trkGrade" size="50" class="uiTrackValidate text ui-widget-content ui-corner-all">
					</div>-->

					<!-- MeterUp -->
					<div>
						<label for="uiTrack_fld_trkMeterUp" class="updTrackLabelFirst">Meter Up</label>
						<input type="text" name="uiTrack_fld_trkMeterUp" id="uiTrack_fld_trkMeterUp" size="50" class="uiTrackValidate text ui-widget-content ui-corner-all">
					</div>

					<!-- MeterDown -->
					<div>
						<label for="uiTrack_fld_trkMeterDown" class="updTrackLabelFirst">Meter Down</label>
						<input type="text" name="uiTrack_fld_trkMeterDown" id="uiTrack_fld_trkMeterDown" size="50" class="uiTrackValidate text ui-widget-content ui-corner-all">
					</div>

					<!-- Country -->
					<div>
						<label for="uiTrack_fld_trkCountry" class="updTrackLabelFirst">Country</label>
						<input type="text" name="uiTrack_fld_trkCountry" id="uiTrack_fld_trkCountry" size="50" class="uiTrackValidate text ui-widget-content ui-corner-all">
					</div>

					<!-- Hidden Fields -->
					<div>
						<input type="hidden" name="uiTrack_fld_trkCoordinates" id="uiTrack_fld_trkCoordinates" size="50" class="uiTrackValidate text ui-widget-content ui-corner-all" readonly>
						<input type="hidden" name="uiTrack_fld_trkStartEle" id="uiTrack_fld_trkStartEle" size="50" class="uiTrackValidate text ui-widget-content ui-corner-all" readonly>
						<input type="hidden" name="uiTrack_fld_trkPeakEle" id="uiTrack_fld_trkPeakEle" size="50" class="uiTrackValidate text ui-widget-content ui-corner-all" readonly>
						<input type="hidden" name="uiTrack_fld_trkPeakTime" id="uiTrack_fld_trkPeakTime" size="50" class="uiTrackValidate text ui-widget-content ui-corner-all" readonly>
						<input type="hidden" name="uiTrack_fld_trkLowEle" id="uiTrack_fld_trkLowEle" size="50" class="uiTrackValidate text ui-widget-content ui-corner-all" readonly>
						<input type="hidden" name="uiTrack_fld_trkLowTime" id="uiTrack_fld_trkLowTime" size="50" class="uiTrackValidate text ui-widget-content ui-corner-all" readonly>
						<input type="hidden" name="uiTrack_fld_trkFinishEle" id="uiTrack_fld_trkFinishEle" size="50" class="uiTrackValidate text ui-widget-content ui-corner-all" readonly>
						<input type="hidden" name="uiTrack_fld_trkFinishTime" id="uiTrack_fld_trkFinishTime" size="50" class="uiTrackValidate text ui-widget-content ui-corner-all" readonly>
						<input type="hidden" name="uiTrack_fld_trkCoordTop" id="uiTrack_fld_trkCoordTop" size="50" class="uiTrackValidate text ui-widget-content ui-corner-all" readonly>
						<input type="hidden" name="uiTrack_fld_trkCoordBottom" id="uiTrack_fld_trkCoordBottom" size="50" class="uiTrackValidate text ui-widget-content ui-corner-all" readonly>
						<input type="hidden" name="uiTrack_fld_trkCoordLeft" id="uiTrack_fld_trkCoordLeft" size="50" class="uiTrackValidate text ui-widget-content ui-corner-all" readonly>
						<input type="hidden" name="uiTrack_fld_trkCoordRight" id="uiTrack_fld_trkCoordRight" size="50" class="uiTrackValidate text ui-widget-content ui-corner-all" readonly>
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
						<input type="text" name="uiTrack_fld_trkOrg" id="uiTrack_fld_trkOrg" size="50" class="uiTrackValidate text ui-widget-content ui-corner-all">
					</div>

					<!-- Event -->
					<div>
						<label for="uiTrack_fld_trkEvent" class="updTrackLabelWayp">Event</label>
						<input type="text" name="uiTrack_fld_trkEvent" id="uiTrack_fld_trkEvent" size="50" class="uiTrackValidate text ui-widget-content ui-corner-all">
					</div>

					<!-- Remarks -->
					<div>
						<label for="uiTrack_fld_trkRemarks" class="updTrackLabelWayp">Remarks</label>
						<input type="text" name="uiTrack_fld_trkRemarks" id="uiTrack_fld_trkRemarks" size="50" class="uiTrackValidate text ui-widget-content ui-corner-all">
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
        <div id=footerText>&copy; tourdb - 2019 leuti - Version Built 1907.009.2</div>
		<div id="statusMessage"></div>
    </footer>
	
</body>
</html>
