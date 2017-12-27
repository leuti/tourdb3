<!DOCTYPE HTML>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<meta http-equiv="Cache-control" content="no-cache">
    <meta http-equiv="Expires" content="-1">
	<title>Tour DB</title>
	<!-- from https://www.jqwidgets.com/ -->
    <link rel="stylesheet" href="jqw/jqwidgets/styles/jqx.base.css" type="text/css" />
	
	<link type="text/css" rel="stylesheet" href="css/tourdb_main.css">
	<link type="text/css" rel="stylesheet" href="css/jquery-ui.css">
        
</head>
<body style="height: 100%;">
	<script type="text/javascript" src="js/jquery-3.1.0.js"></script> <!-- JQuery from local server -->  <!-- local -->

	<!-- from https://www.jqwidgets.com/ -->
    <script type="text/javascript" src="jqw/jqwidgets/jqxcore.js"></script>
    <script type="text/javascript" src="jqw/jqwidgets/jqxsplitter.js"></script>
    <script type="text/javascript" src="jqw/scripts/demos.js"></script>

	<script type="text/javascript" src="js/jquery-ui.js"></script> <!-- JQuery UI from local server --> <!-- local -->
	<script src="//api3.geo.admin.ch/loader.js"></script> <!-- Swissmap javascript -->
	<script type="text/javascript" src="js/bootstrap.min.js"></script> <!-- local -->
	<script type="text/javascript" src="js/tourdb.js"></script> <!-- tourdb code -->

	<!--<script src="//code.jquery.com/jquery-2.2.0.min.js"></script> -->  <!-- CDN -->
	<!-- <script src="https://code.jquery.com/ui/1.12.0/jquery-ui.js"></script> -->  <!-- CDN -->   
	<!-- <script src="//netdna.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script> --> <!-- CDN -->
	
	<header id="header">
		<div id="logo">
			<h1>tour<em>DB</em></h1>
		</div>
		
		<!-- Below the the main tabs for Home, Segments, Waypoints and Routes are located -->
		<ul class="topicTabs active">
			<li id="tabDisplay" class="topicTabsLi">
				<a class="tabMain" href="#panelDisplay"><h3>Display</h3></a>
			</li>
			<li id="tabMaintain" class="topicTabsLi">
				<a class="tabMain" href="#panelMaintain"><h3>Maintain</h3></a> 
			</li>
			<li id="tabImport" class="topicTabsLi">
				<a class="tabMain" href="#panelImport"><h3>Import</h3></a> 
			</li>
			<li id="tabExport" class="topicTabsLi">
				<a class="tabMain" href="#panelExport"><h3>Export</h3></a> 
			</li>
			<li id="tabAdmin" class="topicTabsLi">
				<a class="tabMain" href="#panelAdmin"><h3>Admin</h3></a>
			</li>
			<li id="tabLogin" class="topicTabsLi">
				<a class="tabMain" href="#panelLogin"><h3>Login</h3></a>
			</li>
		</ul>
	</header> 

    <!-- In the div #main all page content is diplayed -->
	<section id="main" style="height: 100%;">
		
		<!-- ========================================================================== -->
		<!-- ========================== panelDisplay ================================== -->
		<!-- ========================================================================== -->

		<div id="panelDisplay" class="tourdbPanel active">
			<p>Ich bin das panel panelDisplay</p>
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
			<p>Ich bin das panel panelImport</p>
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
		
		<div id="panelAdmin" class="panelAdmin">
			<p>Ich bin das panel panelExport</p>		
		</div> <!-- End div panelAdmin -->

		<!-- ========================================================================== -->
		<!-- ======================== panelLogin ========================================== -->
		<!-- ========================================================================== -->
		
		<div id="panelLogin" class="tourdbPanel">
			<p>Ich bin das panel panelLogin</p>
		</div> <!-- End div panelLogin -->

	</section> <!-- End main -->

	<footer id="footer">
        <p>&copy; tourdb 3 - 2017 leuti - Version 20171227</p>
    </footer>
	
</body>
</html>
