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
	<script type="text/javascript" src="js/jquery-3.3.1.min.js"></script>
	<script type="text/javascript" src="js/jquery-ui.min.js"></script> <!-- JQuery UI from local server --> <!-- local -->
	<script src="http://api3.geo.admin.ch/loader.js?lang=en" type="text/javascript"></script>
	<script type="text/javascript" src="js/bootstrap.min.js"></script> <!-- local -->
	<script type="text/javascript" src="js/tourdb.js"></script> <!-- tourdb code -->

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

	<section id="main"> 

		<div id="panelLogin" class="tourdbPanel active">
			<div id=uiLogin class="formCenter">
				<fieldset>
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
		</div>

		<div id="panelMap" class="tourdbPanel">
		</div>

		<div id="panelLists" class="tourdbPanel">
		</div>

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
		</div>

		<div id="panelExport" class="tourdbPanel">
			<div id="uiExport" class="uiDiv active">
				<fieldset>
					<legend class="filterHeader">Export all Objects</legend>
					<div class="buttonUpload">
						<input type="submit" class="button" id="mainButtons_exportBtnTracks01JSON" value="Export Tracks as JSON" />
					</div>
					<div class="buttonUpload">
						<input type="submit" class="button" id="mainButtons_exportBtnTracks01CSV" value="Export Tracks as CSV" />
					</div>
				</fieldset>
			</div>		
		</div>

		<div id="uiTrack" class="uiTrack formCenter uiDiv">
			<ul>
				<li><a href="#uiTrack_tabMain">Main</a></li>
				<li><a href="#uiTrack_tabWayp">Waypoints</a></li>
				<li><a href="#uiTrack_tabOth">Others</a></li>
			</ul>
			<p id="validateComments">Please fill / update fields.</p>
			<div id="uiTrack_tabMain">
				<fieldset>
					<legend class="filterHeader">Import Track</legend>
					<div>
						<input type="hidden" name="uiTrack_fld_trkId" id="uiTrack_fld_trkId" size="20" class="uiTrackValidate text ui-widget-content ui-corner-all" readonly>
					</div>
					<div>
						<label for="uiTrack_fld_trkTrackName" class="updTrackLabelFirst">Track Name</label>
						<input type="text" name="uiTrack_fld_trkTrackName" id="uiTrack_fld_trkTrackName" size="50" class="uiTrackValidate text ui-widget-content ui-corner-all">
					</div>
					<div>
						<label for="uiTrack_fld_trkRoute" class="updTrackLabelFirst">Route</label>
						<input type="text" name="uiTrack_fld_trkRoute" id="uiTrack_fld_trkRoute" size="50" class="uiTrackValidate text ui-widget-content ui-corner-all">
					</div>
					<div>
						<label for="uiTrack_fld_trkDateBegin" class="updTrackLabelFirst">Date Begin</label>
						<input type="text" name="uiTrack_fld_trkDateBegin" id="uiTrack_fld_trkDateBegin" size="20" class="uiTrackValidate text ui-widget-content ui-corner-all">
					</div>
					<div>
						<label for="uiTrack_fld_trkDateFinish" class="updTrackLabelFirst">Date Finish</label>
						<input type="text" name="uiTrack_fld_trkDateFinish" id="uiTrack_fld_trkDateFinish" size="20" class="uiTrackValidate text ui-widget-content ui-corner-all">
					</div>
					<div>
						<label for="uiTrack_fld_trkTypeFid" class="updTrackLabelFirst">Type</label>
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
					<div>
						<label for="uiTrack_fld_trkDistance" class="updTrackLabelFirst">Distance</label>
						<input type="text" name="uiTrack_fld_trkDistance" id="uiTrack_fld_trkDistance" size="50" class="uiTrackValidate text ui-widget-content ui-corner-all">
					</div>
					<div>
						<label for="uiTrack_fld_trkTimeOverall" class="updTrackLabelFirst">Overall Time</label>
						<input type="text" name="uiTrack_fld_trkTimeOverall" id="uiTrack_fld_trkTimeOverall" size="50" class="uiTrackValidate text ui-widget-content ui-corner-all">
					</div>
					<div>
						<label for="uiTrack_fld_trkTimeToPeak" class="updTrackLabelFirst">Time to Peak</label>
						<input type="text" name="uiTrack_fld_trkTimeToPeak" id="uiTrack_fld_trkTimeToPeak" size="50" class="uiTrackValidate text ui-widget-content ui-corner-all">
					</div>
					<div>
						<label for="uiTrack_fld_trkTimeToFinish" class="updTrackLabelFirst">Time To Finish</label>
						<input type="text" name="uiTrack_fld_trkTimeToFinish" id="uiTrack_fld_trkTimeToFinish" size="50" class="uiTrackValidate text ui-widget-content ui-corner-all">
					</div>
					<div>
						<label for="uiTrack_fld_trkGrade" class="updTrackLabelFirst">Grade</label>
						<input id="uiTrack_fld_trkGrade" size="50" class="uiTrackValidate text ui-widget-content ui-corner-all">
					</div>
					<div>
						<label for="uiTrack_fld_trkMeterUp" class="updTrackLabelFirst">Meter Up</label>
						<input type="text" name="uiTrack_fld_trkMeterUp" id="uiTrack_fld_trkMeterUp" size="50" class="uiTrackValidate text ui-widget-content ui-corner-all">
					</div>
					<div>
						<label for="uiTrack_fld_trkMeterDown" class="updTrackLabelFirst">Meter Down</label>
						<input type="text" name="uiTrack_fld_trkMeterDown" id="uiTrack_fld_trkMeterDown" size="50" class="uiTrackValidate text ui-widget-content ui-corner-all">
					</div>
					<div>
						<label for="uiTrack_fld_trkCountry" class="updTrackLabelFirst">Country</label>
						<input type="text" name="uiTrack_fld_trkCountry" id="uiTrack_fld_trkCountry" size="50" class="uiTrackValidate text ui-widget-content ui-corner-all">
					</div>
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
					<div>
						<label for="uiTrack_fld_trkOrg" class="updTrackLabelWayp">Organisation</label>
						<input type="text" name="uiTrack_fld_trkOrg" id="uiTrack_fld_trkOrg" size="50" class="uiTrackValidate text ui-widget-content ui-corner-all">
					</div>
					<div>
						<label for="uiTrack_fld_trkEvent" class="updTrackLabelWayp">Event</label>
						<input type="text" name="uiTrack_fld_trkEvent" id="uiTrack_fld_trkEvent" size="50" class="uiTrackValidate text ui-widget-content ui-corner-all">
					</div>
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

	</section>

	<footer id="footer">
        <div id=footerText>&copy; tourdb - 2019 leuti - Version Built 1907.009.2</div>
		<div id="statusMessage"></div>
    </footer>
	
</body>
</html>
