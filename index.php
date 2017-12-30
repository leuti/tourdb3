<!DOCTYPE HTML>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<meta http-equiv="Cache-control" content="no-cache">
    <meta http-equiv="Expires" content="-1">
	<title>Tour DB</title>
	<!-- from https://www.jqwidgets.com/ -->
    <!-- <link rel="stylesheet" href="jqw/jqwidgets/styles/jqx.base.css" type="text/css" />-->
	
	<link type="text/css" rel="stylesheet" href="css/tourdb_main.css">
	<link type="text/css" rel="stylesheet" href="css/jquery-ui.css">
        
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
			<li id="buttonDisplay" class="topicButtonsLi active">
				<a class="mainButtonsA" href="#panelDisplay"><h3>Display</h3></a>
			</li>
			<li id="buttonMaintain" class="topicButtonsLi">
				<a class="mainButtonsA" href="#panelMaintain"><h3>Maintain</h3></a> 
			</li>
			<li id="buttonImport" class="topicButtonsLi">
				<a class="mainButtonsA" href="#panelImport"><h3>Import</h3></a> 
			</li>
			<li id="buttonExport" class="topicButtonsLi">
				<a class="mainButtonsA" href="#panelExport"><h3>Export</h3></a> 
			</li>
			<li id="buttonAdmin" class="topicButtonsLi">
				<a class="mainButtonsA" href="#panelAdmin"><h3>Admin</h3></a>
			</li>
			<li id="buttonLogin" class="topicButtonsLi">
				<a class="mainButtonsA" href="#panelLogin"><h3>Login</h3></a>
			</li>
		</ul>
	</header> 

    <!-- In the div #main all page content is diplayed -->
	<section id="main" style="height: 100%;">
		
		<!-- ========================================================================== -->
		<!-- ========================== panelDisplay ================================== -->
		<!-- ========================================================================== -->

		<div id="panelDisplay" class="tourdbPanel active">
			
			<!-- this div shows the jquery accordion for the display selectioni -->
			<div id="displayOptionsAccordion"> 
				<h3>Tracks</h3>
				<div class="accordionBackground">
					<fieldset>

						<!-- SEGMENT filter -->
						<legend class="UFLegend">Tracks to be displayed</legend>

						<!-- Track name contains (standard text field) -->
						<div class="mapFilterCriteria">	
							<label for="dispFilTrk_trackName" class="labelFirst">Track Names contains</label>
							<input type="text" name="dispFilTrk_trackName" id="dispFilTrk_trackName" size="40" class="text ui-widget-content ui-corner-all">
						</div>

						<!-- Route contains (standard text field) -->
						<div class="mapFilterCriteria">
							<label for="dispFilTrk_route" class="labelFirst">Route contains</label>
							<input name="dispFilTrk_route" id="dispFilTrk_route" size="40" class="text ui-widget-content ui-corner-all">
						</div>

						<!-- Date witin range -->
						<div class="mapFilterCriteria"> 
							<p>From Date: <input type="text" id="dispFilTrk_dateFrom" class="labelFirst" size="10"></p>
							<p>To Date: <input type="text" id="dispFilTrk_dateTo"class="labelFirst" size="10"></p>
						</div>

						<!-- Type as select items (selectable) -->
						<div class="mapFilterCriteria">
							<label for="dispFilTrk_type" class="labelFirst">Type</label>
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
						<div class="mapFilterCriteria">
							<label for="dispFilTrk_subtype" class="labelFirst">Subtype</label>
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
						<div class="mapFilterCriteria">	
							<label for="dispFilTrk_participants" class="labelFirst">Participants contains</label>
							<input type="text" name="dispFilTrk_participants" id="dispFilTrk_participants" size="40" class="text ui-widget-content ui-corner-all">
						</div>

						<!-- Country (standard text field) -->
						<div class="mapFilterCriteria">	
							<label for="dispFilTrk_country" class="labelFirst">Country like</label>
							<input type="text" name="dispFilTrk_country" id="dispFilTrk_country" size="40" class="text ui-widget-content ui-corner-all">
						</div>

						<div class="mapFilterCriteria">
							<input type="submit" class="button" id="dispFilTrk_ApplyButton" value="Load Tracks" />
						</div>
						
					</fieldset>
				</div>
				<h3>Section 2</h3>
				<div class="accordionBackground">
					<p>Sed non urna. Donec et ante. Phasellus eu ligula. Vestibulum sit amet purus. Vivamus hendrerit, dolor at aliquet laoreet, mauris turpis porttitor velit, faucibus interdum tellus libero ac justo. Vivamus non quam. In suscipit faucibus urna. </p>
				</div>
				<h3>Section 3</h3>
				<div>
					<p>Nam enim risus, molestie et, porta ac, aliquam ac, risus. Quisque lobortis. Phasellus pellentesque purus in massa. Aenean in pede. Phasellus ac libero ac tellus pellentesque semper. Sed ac felis. Sed commodo, magna quis lacinia ornare, quam ante aliquam nisi, eu iaculis leo purus venenatis dui. </p>
					<ul>
					<li>List item one</li>
					<li>List item two</li>
					<li>List item three</li>
					</ul>
				</div>
				<h3>Section 4</h3>
				<div class="accordionBackground">
					<p>Cras dictum. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Aenean lacinia mauris vel est. </p><p>Suspendisse eu nisl. Nullam ut libero. Integer dignissim consequat lectus. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. </p>
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
		
		<div id="panelAdmin" class="tourdbPanel">
			<p>Ich bin das panel panelAdmin</p>		
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
