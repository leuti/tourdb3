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
		</div>

	</section>

	<footer id="footer">
        <div id=footerText>&copy; tourdb - 2019 leuti - Version Built 1907.009.2</div>
		<div id="statusMessage"></div>
    </footer>
	
</body>
</html>
