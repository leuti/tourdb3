<?php
    // Start or restart session 
    session_start();

    // in case this script is started from login page
    if(isset($_POST["login"]))
    {
        if($_POST["login"] == "" && $_POST["passwd"] == "")
        {
            $_SESSION["login"] = $_POST["login"];
        }
    }

    // check if within sessioin
    if(!isset($_SESSION["login"]))
        exit("<p>No access<br><a href='/tourdb3/index.php'>"
            . "Back to Login</a></p>");
?>
<!DOCTYPE HTML>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>Tour DB 3.0</title>
    <link type="text/css" rel="stylesheet" href="/tourdb3/css/tourdb_main.css?v=1">   
    <link type="text/css" rel="stylesheet" href="/tourdb3/css/jquery-ui.css">
</head>
<body style="height: 100%;">
    <script type="text/javascript" src="/tourdb3/js/jquery-3.1.0.js"></script> <!-- JQuery from local server -->
	<script type="text/javascript" src="/tourdb3/js/tourdb.js"></script> <!-- tourdb code -->
	<header id="tourdbHeader">
		<div class="logo">
			<h1>tour<em>DB</em></h1>
		</div>
		
		<!-- ========================================================================== -->
		<!-- ======================== ADMIN ========================================== -->
		<!-- ========================================================================== -->
		
		<div id="panelAdmin" class="tourdbPanel, active">
			<div id="containerAdmin">
				<div id="divGenKml">
					<button href="#" id="btnGenKml">Generate KML</button>
				</div>
				
				<form id="frmGenKml">
					<input type="text" id="whereGenKml" placeholder="Enter WHERE clause..." />
					<input type="submit" id="submGenKml" value="GO" />
				</form>
			</div>
		</div> <!-- End div panelAdmin -->

	<footer id="footer">
        <p>&copy; 2016 leuti - Version 1.09.2016</p>
        <p><a href="/tourdb3/index.php">Log-off</a></p>
    </footer>
	
</body>
</html>