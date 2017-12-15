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
	
	<header id="tourdbHeader">
		<div class="logo">
			<h1>tour<em>DB</em></h1>
		</div>
		
		<!-- Below the the main tabs for Home, Segments, Waypoints and Routes are located -->
		<ul class="topicTabs">
			<!--
			<li id="tabHome">
				<a class="topic-control" href="#panelHome"><h3>Home</h3></a>
			</li>
			-->
			<li id="tabMap" class="active">
				<a class="topic-control" href="#panelMap"><h3>Karte</h3></a> 
			</li>
			<li id="tabSegments">
				<a class="topic-control" href="#panelSegments"><h3>Routen</h3></a> 
			</li>
			<li id="tabWp">
				<a class="topic-control" href="#panelWaypoints"><h3>Wegpunkte</h3></a> 
			</li>
			<li id="tabTouren">
				<a class="topic-control" href="#panelTour"><h3>Touren</h3></a>
			</li>
			<li id="tabAdmin">
				<a class="topic-control" href="#panelAdmin"><h3>Admin</h3></a>
			</li>
		</ul>

	</header> 

    <!-- In the div #main all page content is diplayed -->
	<section id="main" style="height: 100%;">
		
		<!-- ========================================================================== -->
		<!-- ========================== HOME ========================================== -->
		<!-- ========================================================================== -->

		<div id="panelHome" class="tourdbPanel">
			<p>panelHome: Lorem ipsum dolor sit amet, consetetur 
			sadipscing elitr, sed diam nonumy eirmod tempor invidunt 
			ut labore et dolore magna aliquyam erat, sed diam voluptua. 
			At vero eos et accusam et justo duo dolores et ea rebum. 
			Stet clita kasd gubergren, no sea takimata sanctus est Lorem 
			ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur 
			sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut 
			labore et dolore magna aliquyam erat, sed diam voluptua. At 
			vero eos et accusam et justo duo dolores et ea rebum. Stet 
			clita kasd gubergren, no sea takimata sanctus est Lorem 
			ipsum dolor sit amet.</p>
		</div> <!-- End div panelHome -->

		<!-- ========================================================================== -->
		<!-- ======================== MAP ========================================== -->
		<!-- ========================================================================== -->
		
		<div id="panelMap" class="tourdbPanel active">

			<!-- This div contains the tabs to open the option modal window -->
			<ul class="mapOptionTabs">
				<li id="mapOptionTabs"> 
					<a class="option-control" href="#mapPanelOption">Optionen</a> 
				</li>
			</ul>

			<!-- This div contains the tabs to open filter criteria -->
			<ul class="mapFilterTabs">
				<li id="mapUserFilter">
					<a class="mapUserFilterControl" href="#mapPanelFilter">Routen auswählen</a> 
				</li>
			</ul>

			<!-- This div shows the Filter form (can also be a modal window) -->
			<div id="mapPanelFilter" class="mapPanelFilter">
				<fieldset>

					<!-- SEGMENT filter -->
    				<legend class="UFLegend">Routen Filter</legend>

					<div class="mapFilterCriteria">
						<label for="mapUF_sourceName" class="labelNext">Quelle</label>
						<input type="text" name="mapUF_sourceName" id="mapUF_sourceName" size="40" class="text ui-widget-content ui-corner-all">
						<input type="hidden" id="mapUF_sourceFID">

						<label for="mapUF_sourceRef" class="labelNext">Quellref.</label>
						<input name="mapUF_sourceRef" id="mapUF_sourceRef" size="10" class="text ui-widget-content ui-corner-all">
					</div>
						
					<!-- segType -->
					<div class="mapFilterCriteria">
						<label for="mapUF_mapType" class="labelFirst">Seg. Type: </label>
						<ol id="mapUF_segType" class="selectable filterItems">
							<li id="segType_WA" class="ui-widget-content first">Wanderung</li>
							<li id="segType_AW" class="ui-widget-content">Alpinwanderung</li>
							<li id="segType_HT" class="ui-widget-content">Hochtour</li>
							<li id="segType_ST" class="ui-widget-content">Skitour</li>
							<li id="segType_SS" class="ui-widget-content">Schneeschuhtour</li>
							<!--<li class="ui-widget-content">alle</li>-->
						</ol>
					</div>

					<!-- segName -->
					<div class="mapFilterCriteria">
						<label for="mapUF_segName" class="labelFirst">Seg. Name: </label>
						<input id="mapUF_segName" class="filterItems" type="text" size="50">
					</div>

					<!--startLocName -->
					<div class="mapFilterCriteria">
						<label for="mapUF_startLocName" class="labelFirst">Startort: </label>
						<input id="mapUF_startLocName" class="filterItems" type="text" size="50">
						<input type="hidden" id="mapUF_startLocID">
					</div>
					
					<!--startLocAlt -->
					<div class="mapFilterCriteria">
						<label for="mapUF_startLocAlt_slider_values" class="labelFirst">Starthöhe:</label>
  						<input type="text" id="mapUF_startLocAlt_slider_values" class="filterItems sliderValue" readonly>
					</div>
					<div id="mapUF_startLocAlt_slider"></div>

					<!-- startLocType -->
					<div class="mapFilterCriteria">
						<label for="mapUF_startLocType" class="labelFirst">Starttyp: </label>
						<ol id="mapUF_startLocType" class="selectable filterItems">
							<li id="startLocType_1" class="ui-widget-content first">Bergstation</li>
							<li id="startLocType_5" class="ui-widget-content">Gipfel</li>
							<li id="startLocType_4" class="ui-widget-content">Hütte</li>
							<li id="startLocType_2" class="ui-widget-content">Talort</li>
							<li id="startLocType_3" class="ui-widget-content">Wegpunkt</li>
						</ol>
					</div>
					
					<!--TargetLocName -->
					<div class="mapFilterCriteria">
						<label for="mapUF_targetLocName" class="labelFirst">Zielort: </label>
						<input id="mapUF_targetLocName" class="filterItems" type="text" size="50">
						<input type="hidden" id="mapUF_targetLocID">
					</div>
					
					<!--targetLocAlt -->
					<div class="mapFilterCriteria">
						<label for="mapUF_targetLocAlt_slider_values" class="labelFirst">Zielhöhe:</label>
  						<input type="text" id="mapUF_targetLocAlt_slider_values" class="filterItems sliderValue" readonly>
					</div>
					<div id="mapUF_targetLocAlt_slider"></div>

					<!-- targetLocType -->
					<div class="mapFilterCriteria">
						<label for="mapUF_targetLocType" class="labelFirst">Zieltyp: </label>
						<ol id="mapUF_targetLocType" class="selectable filterItems">
							<li id="targetLocType_1" class="ui-widget-content first">Bergstation</li>
							<li id="targetLocType_5" class="ui-widget-content">Gipfel</li>
							<li id="targetLocType_4" class="ui-widget-content">Hütte</li>
							<li id="targetLocType_2" class="ui-widget-content">Talort</li>
							<li id="targetLocType_3" class="ui-widget-content">Wegpunkt</li>
						</ol>
					</div>

					<!-- region -->
					<div class="mapFilterCriteria">
						<label for="mapUF_segRegion" class="labelFirst">Region: </label>
						<input id="mapUF_segRegion" class="filterItems" type="text" size="50">
						<input type="hidden" id="mapUF_segRegionID">
					</div>
					
					<!-- Gebiet -->
					<div class="mapFilterCriteria">
						<label for="mapUF_segArea" class="labelFirst">Gebiet: </label>
						<input id="mapUF_segArea" class="filterItems" type="text" size="50">
						<input type="hidden" id="mapUF_segAreaID">
					</div>
		
					<!-- grade -->
					<div class="mapFilterCriteria">
						<label for="mapUF_grade" class="labelFirst">Schwierigkeit: </label>
						<ol id="mapUF_grade" class="selectable filterItems">
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
					<div class="mapFilterCriteria">
						<label for="mapUF_climbGrade" class="labelFirst">Klettergrad: </label>
						<ol id="mapUF_climbGrade" class="selectable filterItems">
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
					<div class="mapFilterCriteria">
						<br>t St-Zi  min. <input type="text" name="tStartTargetMin">
						max. <input type="text" name="tStartTargetMax">
					</div>
					-->
						
					<!--mUStartTarget -->
					<!-- WAITING FOR JQUI WIDGET
					<div class="mapFilterCriteria">
						<br>Aufstieg  min. <input type="text" name="mUStartTargetMin">
						max. <input type="text" name="mUStartTargetMax"><br>
					</div>
					-->

					<br><br>
					<input type="submit" class="button" id="mapApplyFilterUser" value="Daten filtern" />
				</fieldset>
				<!-- </div> -->
				
				<br class="clear"></br>
			</div>

			<!-- This div shows the output as map -->
			<div id="mapPanel_Map" class="visible">
				<div id="mapPanel_Map-ResMap">	<!-- Here the map of the mapments is displayed -->
				</div> <!-- End mapResultMap -->

				<!-- dialog form for Display Options -->
				<div id="mapOptDialog" title="Anzeige Optionen">
					<form id="mapOptDialogForm">
						<fieldset>
													
						<!-- Waypoint wtypCode -->
						<p class="filterElement">
							<label for="mapOpt_wtypCode" class="labelFirst">Waypoint Typ: </label>
							<ol id="mapOpt_wtypCode" class="selectable filterItems">
								<li id="wtypId_1" class="ui-widget-content first">Bergstation</li>
								<li id="wtypId_5" class="ui-widget-content">Gipfel</li>
								<li id="wtypId_4" class="ui-widget-content">Hütte</li>
								<li id="wtypId_2" class="ui-widget-content">Talort</li>
								<li id="wtypId_3" class="ui-widget-content">Wegpunkt</li>
							</ol>
						</p>
						<p>
							<label for="mapOptHangneigung" class="labelNext">SAC Hangneigung</label>
							<input type="checkbox" name="mapOptHangneigung" id="mapOptHangneigung" class="text ui-widget-content ui-corner-all">
						</p>
						<p>
							<label for="mapOptWanderwege" class="labelNext">Wanderwege</label>
							<input type="checkbox" name="mapOptWanderwege" id="mapOptWanderwege" class="text ui-widget-content ui-corner-all">
						</p>
						<p>
							<label for="mapOptHaltestellen" class="labelNext">ÖV-Haltestellen</label>
							<input type="checkbox" name="mapOptHaltestellen" id="mapOptHaltestellen" class="text ui-widget-content ui-corner-all">
						</p>
						<p>
							<label for="mapOptKantonsgrenzen" class="labelNext">Kantonsgrenzen</label>
							<input type="checkbox" name="mapOptKantonsgrenzen" id="mapOptKantonsgrenzen" class="text ui-widget-content ui-corner-all">
						</p>
						<p>
							<label for="mapOptSacAreas" class="labelNext">SAC Gebiete einblenden</label>
							<input type="checkbox" name="mapOptSacAreas" id="mapOptSacAreas" class="text ui-widget-content ui-corner-all">
						</p>
						<br><br>
						
						<!-- Allow form submission with keyboard without duplicating the dialog button -->
						<input type="submit" tabindex="-1" style="position:absolute; top:-1000px">
						</fieldset>
					</form>
				</div>

			</div> <!-- End mapPanel_Map -->

		</div> <!-- End div panelMap -->

		<!-- ========================================================================== -->
		<!-- ====================== SEGMENTS ========================================== -->
		<!-- ========================================================================== -->
		
		<div id="panelSegments" class="tourdbPanel">
			<!-- This div contains the tabs to open filter criteria -->
			<ul class="segFilterTabs">
				<li id="segUserFilter">
					<a class="segUserFilterControl" href="#segPanelFilter">Routen filtern</a> 
				</li>
			</ul>

			<!-- This div shows the Filter form (can also be a modal window) -->
			<div id="segPanelFilter" class="segPanelFilter">
				<div id="segPanelFilterLeft" class="segPanelFilter">

					<!-- segUF_sourceName -->
					<div class="segFilterCriteria">
						<label for="segUF_sourceName" class="labelNext">Quelle</label>
						<input type="text" name="segUF_sourceName" id="segUF_sourceName" size="40" class="text ui-widget-content ui-corner-all">
						<input type="hidden" id="segUF_sourceFID">

						<label for="segUF_SourceRef" class="labelNext">Quellref.</label>
						<input name="segUF_SourceRef" id="segUF_SourceRef" size="10" class="text ui-widget-content ui-corner-all">
					</div>

					<!-- segType -->
					<div class="segFilterCriteria">
						<label for="segUF_segType" class="labelFirst">Seg. Type: </label>
						<ol id="segUF_segType" class="selectable filterItems">
							<li id="segType_WA" class="ui-widget-content first">Wanderung</li>
							<li id="segType_AW" class="ui-widget-content">Alpinwanderung</li>
							<li id="segType_HT" class="ui-widget-content">Hochtour</li>
							<li id="segType_ST" class="ui-widget-content">Skitour</li>
							<li id="segType_SS" class="ui-widget-content">Schneeschuhtour</li>
							<!--<li class="ui-widget-content">alle</li>-->
						</ol>
					</div>

					<!-- segName -->
					<div class="segFilterCriteria">
						<label for="segUF_segName" class="labelFirst">Seg. Name: </label>
						<input id="segUF_segName" class="filterItems" type="text" size="50">
					</div>

					<!--startLocName -->
					<div class="segFilterCriteria">
						<label for="segUF_startLocName" class="labelFirst">Startort: </label>
						<input id="segUF_startLocName" class="filterItems" type="text" size="50">
						<input type="hidden" id="segUF_startLocID">
					</div>
					
					<!--startLocAlt -->
					<div class="segFilterCriteria">
						<label for="segUF_startLocAlt_slider_values" class="labelFirst">Starthöhe:</label>
  						<input type="text" id="segUF_startLocAlt_slider_values" class="filterItems sliderValue" readonly>
					</div>
					<div id="segUF_startLocAlt_slider"></div>

					<!-- startLocType -->
					<div class="segFilterCriteria">
						<label for="segUF_startLocType" class="labelFirst">Starttyp: </label>
						<ol id="segUF_startLocType" class="selectable filterItems">
							<li id="startLocType_1" class="ui-widget-content first">Bergstation</li>
							<li id="startLocType_5" class="ui-widget-content">Gipfel</li>
							<li id="startLocType_4" class="ui-widget-content">Hütte</li>
							<li id="startLocType_2" class="ui-widget-content">Talort</li>
							<li id="startLocType_3" class="ui-widget-content">Wegpunkt</li>
						</ol>
					</div>
					
					<!--TargetLocName -->
					<div class="segFilterCriteria">
						<label for="segUF_targetLocName" class="labelFirst">Zielort: </label>
						<input id="segUF_targetLocName" class="filterItems" type="text" size="50">
						<input type="hidden" id="segUF_targetLocID">
					</div>
					
					<!--targetLocAlt -->
					<div class="segFilterCriteria">
						<label for="segUF_targetLocAlt_slider_values" class="labelFirst">Zielhöhe:</label>
  						<input type="text" id="segUF_targetLocAlt_slider_values" class="filterItems sliderValue" readonly>
					</div>
					<div id="segUF_targetLocAlt_slider"></div>

					<!-- targetLocType -->
					<div class="segFilterCriteria">
						<label for="segUF_targetLocType" class="labelFirst">Zieltyp: </label>
						<ol id="segUF_targetLocType" class="selectable filterItems">
							<li id="targetLocType_1" class="ui-widget-content first">Bergstation</li>
							<li id="targetLocType_5" class="ui-widget-content">Gipfel</li>
							<li id="targetLocType_4" class="ui-widget-content">Hütte</li>
							<li id="targetLocType_2" class="ui-widget-content">Talort</li>
							<li id="targetLocType_3" class="ui-widget-content">Wegpunkt</li>
						</ol>
					</div>

					<!-- region -->
					<div class="segFilterCriteria">
						<label for="segUF_region" class="labelFirst">Region: </label>
						<input id="segUF_region" class="filterItems" type="text" size="50">
						<input type="hidden" id="segUF_regionID">
					</div>
					
					<!-- Gebiet -->
					<div class="segFilterCriteria">
						<label for="segUF_area" class="labelFirst">Gebiet: </label>
						<input id="segUF_area" class="filterItems" type="text" size="50">
						<input type="hidden" id="segUF_areaID">
					</div>
		
				</div>
				<div id="segPanelFilterRight" class="segPanelFilter">
					<!-- grade -->
					<div class="segFilterCriteria">
						<label for="segUF_grade" class="labelFirst">Schwierigkeit: </label>
						<ol id="segUF_grade" class="selectable filterItems">
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
					<div class="segFilterCriteria">
						<label for="segUF_climbGrade" class="labelFirst">Klettergrad: </label>
						<ol id="segUF_climbGrade" class="selectable filterItems">
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
					<div class="segFilterCriteria">
						<br>t St-Zi  min. <input type="text" name="tStartTargetMin">
						max. <input type="text" name="tStartTargetMax">
					</div>
					-->
						
					<!--mUStartTarget -->
					<!-- WAITING FOR JQUI WIDGET
					<div class="segFilterCriteria">
						<br>Aufstieg  min. <input type="text" name="mUStartTargetMin">
						max. <input type="text" name="mUStartTargetMax"><br>
					</div>
					-->

					<br><br>
					<input type="submit" class="button" id="segApplyFilterUser" value="Daten filtern" />
					<!--<input type="submit" class="button" id="segClearFilterUser" value="Clear Filter" />-->
						
				</div>
				<br class="clear"></br>
			</div>

			<!-- In the div #segPanel all elements for the segments are displaed -->
			<div id="segPanel_List" class='visible'>
				<!-- Displays the records -->
				<div id="segPanel_List-ResList"> <!-- Here the table of segments are displayed -->
				</div> <!-- End segPanel_List-ResList-->

				<!-- dialog-form element ==> to be adjusted --> 
				<div id="segDialog" title="Segment hinzufügen">
					<p class="segValidateTips">Bitte Felder befüllen.</p>
				
					<form id="segDialogForm">
						<fieldset>
							<p> 
							<label for="segDialogSegType" class="labelFirst">Type</label>
							<input type="text" name="segDialogSegType" id="segDialogSegType" size="15" class="text ui-widget-content ui-corner-all">
							<input type="hidden" id="segTypeFID">

							<label for="segDialogSourceFID" class="labelNext">Quelle</label>
							<input type="text" name="segDialogSourceFID" id="segDialogSourceFID" size="40" class="text ui-widget-content ui-corner-all">
							<input type="hidden" id="segSourceFID">

							<label for="segDialogSourceRef" class="labelNext">Quellref.</label>
							<input name="segDialogSourceRef" id="segDialogSourceRef" size="10" class="text ui-widget-content ui-corner-all">
							</p>
							
							<p>			
							<label for="segDialogSegName" class="labelFirst">Segment Name</label>
							<input name="segDialogSegName" id="segDialogSegName" size="80" class="text ui-widget-content ui-corner-all">
							</p>

							<p>
							<label for="segDialogRouteName" class="labelFirst">Route Name</label>
							<input name="segDialogRouteName" id="segDialogRouteName" size="80" class="text ui-widget-content ui-corner-all">
							<p>

							</p>
							<label for="segDialogStartLocName" class="labelFirst">Startort</label>
							<input type="text" name="segDialogStartLocName" id="segDialogStartLocName" size="40" class="text ui-widget-content ui-corner-all"> 
							<input type="hidden" id="segDialogStartLocID">
							<!--
							<label for="segDialogStartLocAlt" class="labelNext">Starthöhe</label>
							<input type="text" name="segDialogStartLocAlt" id="segDialogStartLocAlt" size="15" class="text ui-widget-content ui-corner-all">	
							<label for="segDialogStartLocType" class="labelNext">Starttyp</label>
							<input type="text" name="segDialogStartLocType" id="segDialogStartLocType" size="15" class="text ui-widget-content ui-corner-all">
							</p>
	
							<p>
							-->
							<label for="segDialogTargetLocName" class="labelNext">Zielort</label>
							<input type="text" name="segDialogTargetLocName" id="segDialogTargetLocName" size="40" class="text ui-widget-content ui-corner-all">
							<input type="hidden" id="segDialogTargetLocID">
							<!--
							<label for="segDialogTargetLocAlt" class="labelNext">Zielhöhe</label>
							<input type="text" name="segDialogTargetLocAlt" id="segDialogTargetLocAlt" size="15" class="text ui-widget-content ui-corner-all">
							<label for="segDialogTargetLocType" class="labelNext">Zieltyp</label>
							<input type="text" name="segDialogTargetLocType" id="segDialogTargetLocType" size="15" class="text ui-widget-content ui-corner-all">
							</p>
							-->

							<p>
							<label for="segDialogCountry" class="labelFirst">Land</label>
							<input type="text" name="segDialogCountry" id="segDialogCountry" size="5" class="text ui-widget-content ui-corner-all">
							<label for="segDialogCanton" class="labelNext">Kanton</label>
							<input type="text" name="segDialogCanton" id="segDialogCanton" size="5" class="text ui-widget-content ui-corner-all">
							<label for="segDialogRegion" class="labelNext">Region</label>
							<input type="text" name="segDialogRegion" id="segDialogRegion" size="30" class="text ui-widget-content ui-corner-all">
							<input type="hidden" id="regionID">
							<label for="segDialogArea" class="labelNext">Gebiet</label>
							<input type="text" name="segDialogArea" id="segDialogArea" size="<30></30>" class="text ui-widget-content ui-corner-all">
							<input type="hidden" id="segAreaID">
							</p>

							<p>
							<label for="segDialogGrade" class="labelFirst">Schwierigkeit</label>
							<input type="text" name="segDialogGrade" id="segDialogGrade" size="10" class="text ui-widget-content ui-corner-all">
							<label for="segDialogClimbGrade" class="labelNext">Klettergrad</label>
							<input type="text" name="segDialogClimbGrade" id="segDialogClimbGrade" size="10" class="text ui-widget-content ui-corner-all">
							<label for="segDialogFirn" class="labelNext">Firnsteilheit</label>
							<input type="text" name="segDialogFirn" id="segDialogFirn" size="10" class="text ui-widget-content ui-corner-all">
							<label for="segDialogEHaft" class="labelNext">Ernsthaftigkeit</label>
							<input type="text" name="segDialogEHaft" id="segDialogEHaft" size="10" class="text ui-widget-content ui-corner-all">
							</p>

							<!-- Expo -->
							<p>
							<label for="segDialogExpo" class="labelFirst">Expo: </label>
							<ol id="segDialogExpo" class="selectable filterItems">
								<li id="expo_N" class="ui-widget-content">N</li>
								<li id="expo_NE" class="ui-widget-content">NE</li>
								<li id="expo_E" class="ui-widget-content">E</li>
								<li id="expo_SE" class="ui-widget-content">SE</li>
								<li id="expo_S" class="ui-widget-content">S</li>
								<li id="expo_SW" class="ui-widget-content">SW</li>
								<li id="expo_W" class="ui-widget-content">W</li>
								<li id="expo_NW" class="ui-widget-content">NW</li>
							</ol>
							</p>
					

							<p>
							<label for="segDialogTStartTarget" class="labelFirst">Zeit Start - Ziel</label>
							<input type="text" name="segDialogTStartTarget" id="segDialogTStartTarget" size="20" class="text ui-widget-content ui-corner-all">
							<label for="segDialogMUStartTarget" class="labelNext">Aufstiegsmeter Start - Ziel</label>
							<input type="text" name="segDialogMUStartTarget" id="segDialogMUStartTarget" size="20" class="text ui-widget-content ui-corner-all">
							<label for="segDialogDescent" class="labelNext">Geeignet für Abstieg</label>
							<input type="checkbox" name="segDialogDescent" id="segDialogDescent" class="text ui-widget-content ui-corner-all">
							</p>

							<p>
							<label for="segDialogRemarks" class="labelFirst">Bemerkungen</label>
							<input type="text" name="segDialogRemarks" id="segDialogRemarks" size="40" class="text ui-widget-content ui-corner-all">
							</p>
							
							<p>
							<label for="segDialogCoordinates" class="labelFirst">Koordinaten</label>
							<input type="text" name="segDialogCoordinates" id="segDialogCoordinates" size="40" class="text ui-widget-content ui-corner-all">
							</p>
							
							<!-- Allow form submission with keyboard without duplicating the dialog button -->
							<input type="submit" tabindex="-1" style="position:absolute; top:-1000px">
						</fieldset>
					</form>
				</div>
				<!-- dialog-form element ==> to be adjusted --> 
				
			</div> <!-- End segPanel_List -->
					
		</div> <!-- End div panelSegments -->

		<!-- ========================================================================== -->
		<!-- ===================== WAYPOINTS ========================================== -->
		<!-- ========================================================================== -->

		<div id="panelWaypoints" class="tourdbPanel">
			<!-- This div contains the tabs to open filter criteria -->
			<ul class="waypFilterTabs">
				<li id="waypUserFilter">
					<a class="waypUserFilterControl" href="#waypPanelFilter">Wegpunkte Filter</a> 
				</li>
			</ul>
			
			<!-- This div shows the Filter form (can also be a modal window) -->
			<div id="waypPanelFilter" class="waypPanelFilter">
				<br>
				<!-- Waypoint Name -->
				<p class="filterElement">
					<label for="waypUF_waypNameLong" class="labelFirst">Name (Long): </label>
					<input id="waypUF_waypNameLong" class="filterItems" type="text" size="50">
				</p>

				<!-- Waypoint wtypCode -->
				<p class="filterElement">
					<label for="waypUF_wtypCode" class="labelFirst">Typ: </label>
					<ol id="waypUF_wtypCode" class="selectable filterItems">
						<li id="wtypCode_1" class="ui-widget-content first">Bergstation</li>
						<li id="wtypCode_5" class="ui-widget-content">Gipfel</li>
						<li id="wtypCode_4" class="ui-widget-content">Hütte</li>
						<li id="wtypCode_2" class="ui-widget-content">Talort</li>
						<li id="wtypCode_3" class="ui-widget-content">Wegpunkt</li>
					</ol>
				</p>

				<!-- country -->
				<p class="filterElement">
					<label for="waypUF_country" class="labelFirst">Land: </label>
					<input id="waypUF_country" class="filterItems" type="text" size="50">
				</p>

				<!-- region -->
				<p class="filterElement">
					<label for="waypUF_region" class="labelFirst">Region: </label>
					<input id="waypUF_region" class="filterItems" type="text" size="50">
					<input type="hidden" id="waypFilter_regionID">
				</p>
				
				<!-- Gebiet -->
				<p class="filterElement">
					<label for="waypUF_area" class="labelFirst">Gebiet: </label>
					<input id="waypUF_area" class="filterItems" type="text" size="50">
					<input type="hidden" id="waypFilter_areaID">
				</p>

				<!--Waypoint Altitude -->
				<p class="filterElement">
					<label for="waypUF_alt_slider_values" class="labelFirst">Höhe:</label>
					<input type="text" id="waypUF_alt_slider_values" class="filterItems sliderValue" readonly>
				</p>
				<div id="waypUF_alt_slider"></div>

				<br><br>
				<input type="submit" class="button" id="waypApplyFilterUser" value="Daten filtern" />
				<!--<input type="submit" class="button" id="waypClearFilterUser" value="Clear Filter" />-->
		
				<br class="clear"></br>
			</div>

			<!-- In the div #waypPanel all elements for the segments are displaed -->
			<div id="waypPanel_List" class='visible'>
				<!-- Displays the records -->
				<div id="waypPanel_List-ResList"> <!-- Here the table of segments are displayed -->
				</div> <!-- End waypPanel_List-ResList-->
 
				<!-- dialog-form element ==> to be adjusted --> 
				<div id="waypDialog" title="Waypoints hinzufügen">
					<p class="waypValidateTips">Bitte Felder befüllen.</p>
    
					<form>
						<fieldset>
						<p>
						<label for="waypDialogNameShort" class="labelFirst">Name (kurz)</label>
						<input type="text" name="waypDialogNameShort" id="waypDialogNameShort" size="40" class="text ui-widget-content ui-corner-all">
						</p>

						<p>
						<label for="waypDialogNameLong" class="labelFirst">Name (lang)</label>
						<input type="text" name="waypDialogNameLong" id="waypDialogNameLong" size="60" class="text ui-widget-content ui-corner-all">
						</p>

						<p>			
						<label for="waypDialogTypeCode" class="labelFirst">Typ</label>
						<select name="waypDialogTypeCode" id="waypDialogTypeCode" class="text ui-widget-content ui-corner-all">
							<option>Bergstation</option>
							<option>Talort</option>
							<option>Wegpunkt</option>
							<option>Hütte</option>
							<option>Gipfel</option>
						</select>
						</p>

						<p>
						<label for="waypDialogCountry" class="labelFirst">Land</label>
						<select name="waypDialogCountry" id="waypDialogCountry" class="text ui-widget-content ui-corner-all">
							<option>AT</option>
							<option selected="selected">CH</option>
							<option>FR</option>
							<option>IT</option>
							<option>LI</option>
						</select>
						</p>

						<p>
						<!-- replace by autocomplete -->
						<label for="waypDialogCanton" class="labelFirst">Kanton</label>
						<input name="waypDialogCanton" id="waypDialogCanton" size="10" class="text ui-widget-content ui-corner-all">
						<p>

						</p>
						<label for="waypDialogRegion" class="labelFirst">Region</label>
						<input type="text" name="waypDialogRegion" id="waypDialogRegion" size="40" class="text ui-widget-content ui-corner-all"> 
						<input type="hidden" id="waypRegionID">
						</p>

						<p>
						<label for="waypDialogArea" class="labelFirst">Gebiet</label>
						<input type="text" name="waypDialogArea" id="waypDialogArea" size="40" class="text ui-widget-content ui-corner-all">
					    <input type="hidden" id="waypAreaID">
						</p>

						<p>
						<label for="waypDialogAltitude" class="labelFirst">Höhe</label>
						<input type="text" name="waypDialogAltitude" id="waypDialogAltitude" size="10" class="text ui-widget-content ui-corner-all">
						</p>

						<p>
						<label for="waypCoordLV3Est" class="labelFirst">Coord LV3 Ost</label>
						<input type="text" name="waypCoordLV3Est" id="waypCoordLV3Est" size="15" class="text ui-widget-content ui-corner-all">
						</p>

						<p>
						<label for="waypCoordLV3Nord" class="labelFirst">Coord LV3 Nord</label>
						<input type="text" name="waypCoordLV3Nord" id="waypCoordLV3Nord" size="15" class="text ui-widget-content ui-corner-all">
						</p>

						<p>
						<label for="waypDialogOwner" class="labelFirst">Besitzer</label>
						<input type="text" name="waypDialogOwner" id="waypDialogOwner" size="40" class="text ui-widget-content ui-corner-all">
						</p>

						<p>
						<label for="waypDialogWebsite" class="labelFirst">Website</label>
						<input type="text" name="waypDialogWebsite" id="waypDialogWebsite" size="40" class="text ui-widget-content ui-corner-all">
						</p>

						<p>
						<label for="waypDialogRemarks" class="labelFirst">Bemerkungen</label>
						<input type="text" name="waypDialogRemarks" id="waypDialogRemarks" size="80" class="text ui-widget-content ui-corner-all">
						</p>

						<!-- Allow form submission with keyboard without duplicating the dialog button -->
						<input type="submit" tabindex="-1" style="position:absolute; top:-1000px">
						</fieldset>
					</form>
				</div>
				<!-- dialog-form element ==> to be adjusted --> 
				
				
			</div> <!-- End waypPanel_List -->
					
		</div> <!-- End div panelWaypoints -->


		<!-- ========================================================================== -->
		<!-- ======================== Touren ========================================== -->
		<!-- ========================================================================== -->
		
		<div id="panelTour" class="tourdbPanel" style="height: 100%;">

			<!-- This div contains the tabs to open filter criteria -->
			<ul class="tourenFilterTabs">
				<li id="tourenUserFilter">
					<a class="tourenUserFilterControl" href="#tourenPanelFilter">F</a> 
				</li>
			</ul>

			<!-- This div shows the Filter form (can also be a modal window) -->
			<div id="tourenPanelFilter" class="tourenPanelFilter">
				<fieldset>

					<!-- SEGMENT filter -->
					<legend class="UFLegend">Routen Filter</legend>

					<div class="mapFilterCriteria">
						<label for="tourUF_sourceName" class="labelNext">Quelle</label>
						<input type="text" name="tourUF_sourceName" id="tourUF_sourceName" size="40" class="text ui-widget-content ui-corner-all">
						<input type="hidden" id="tourUF_sourceFID">

						<label for="tourUF_sourceRef" class="labelNext">Quellref.</label>
						<input name="tourUF_sourceRef" id="tourUF_sourceRef" size="10" class="text ui-widget-content ui-corner-all">
					</div>
						
					<!-- segType -->
					<div class="mapFilterCriteria">
						<label for="tourUF_mapType" class="labelFirst">Seg. Type: </label>
						<ol id="tourUF_segType" class="selectable filterItems">
							<li id="tourUFsegType_WA" tourId="WA" class="ui-widget-content first">Wanderung</li>
							<li id="tourUFsegType_AW" tourId="AW" class="ui-widget-content">Alpinwanderung</li>
							<li id="tourUFsegType_HT" tourId="HT" class="ui-widget-content">Hochtour</li>
							<li id="tourUFsegType_ST" tourId="ST" class="ui-widget-content">Skitour</li>
							<li id="tourUFsegType_SS" tourId="SS" class="ui-widget-content">Schneeschuhtour</li>
						</ol>
					</div>

					<!-- segName -->
					<div class="mapFilterCriteria">
						<label for="tourUF_segName" class="labelFirst">Seg. Name: </label>
						<input id="tourUF_segName" class="filterItems" type="text" size="50">
					</div>

					<!--startLocName -->
					<div class="mapFilterCriteria">
						<label for="tourUF_startLocName" class="labelFirst">Startort: </label>
						<input id="tourUF_startLocName" class="filterItems" type="text" size="50">
						<input type="hidden" id="tourUF_startLocID">
					</div>
					
					<!--startLocAlt -->
					<div class="mapFilterCriteria">
						<label for="tourUF_startLocAlt_slider_values" class="labelFirst">Starthöhe:</label>
						<input type="text" id="tourUF_startLocAlt_slider_values" class="filterItems sliderValue" readonly>
					</div>
					<div id="tourUF_startLocAlt_slider"></div>

					<!-- startLocType -->
					<div class="mapFilterCriteria">
						<label for="tourUF_startLocType" class="labelFirst">Starttyp: </label>
						<ol id="tourUF_startLocType" class="selectable filterItems">
							<li id="tourUFstartLocType_1" tourId="1" class="ui-widget-content first">Bergstation</li>
							<li id="tourUFstartLocType_5" tourId="5" class="ui-widget-content">Gipfel</li>
							<li id="tourUFstartLocType_4" tourId="4" class="ui-widget-content">Hütte</li>
							<li id="tourUFstartLocType_2" tourId="2" class="ui-widget-content">Talort</li>
							<li id="tourUFstartLocType_3" tourId="3" class="ui-widget-content">Wegpunkt</li>
						</ol>
					</div>
					
					<!--TargetLocName -->
					<div class="mapFilterCriteria">
						<label for="tourUF_targetLocName" class="labelFirst">Zielort: </label>
						<input id="tourUF_targetLocName" class="filterItems" type="text" size="50">
						<input type="hidden" id="tourUF_targetLocID">
					</div>
					
					<!--targetLocAlt -->
					<div class="mapFilterCriteria">
						<label for="tourUF_targetLocAlt_slider_values" class="labelFirst">Zielhöhe:</label>
						<input type="text" id="tourUF_targetLocAlt_slider_values" class="filterItems sliderValue" readonly>
					</div>
					<div id="tourUF_targetLocAlt_slider"></div>

					<!-- targetLocType -->
					<div class="mapFilterCriteria">
						<label for="tourUF_targetLocType" class="labelFirst">Zieltyp: </label>
						<ol id="tourUF_targetLocType" class="selectable filterItems">
							<li id="tourUFtargetLocType_1" tourId="1" class="ui-widget-content first">Bergstation</li>
							<li id="tourUFtargetLocType_5" tourId="5" class="ui-widget-content">Gipfel</li>
							<li id="tourUFtargetLocType_4" tourId="4" class="ui-widget-content">Hütte</li>
							<li id="tourUFtargetLocType_2" tourId="2" class="ui-widget-content">Talort</li>
							<li id="tourUFtargetLocType_3" tourId="3" class="ui-widget-content">Wegpunkt</li>
						</ol>
					</div>

					<!-- region -->
					<div class="mapFilterCriteria">
						<label for="tourUF_segRegion" class="labelFirst">Region: </label>
						<input id="tourUF_segRegion" class="filterItems" type="text" size="50">
						<input type="hidden" id="tourUF_segRegionID">
					</div>
					
					<!-- Gebiet -->
					<div class="mapFilterCriteria">
						<label for="tourUF_segArea" class="labelFirst">Gebiet: </label>
						<input id="tourUF_segArea" class="filterItems" type="text" size="50">
						<input type="hidden" id="tourUF_segAreaID">
					</div>

					<!-- grade -->
					<div class="mapFilterCriteria">
						<label for="tourUF_grade" class="labelFirst">Schwierigkeit: </label>
						<ol id="tourUF_grade" class="selectable filterItems">
							<li id="tourUFgrade_T1" tourId="T1" class="ui-widget-content first">T1</li>
							<li id="tourUFgrade_T2" tourId="T2" class="ui-widget-content">T2</li>
							<li id="tourUFgrade_T3" tourId="T3" class="ui-widget-content">T3</li>
							<li id="tourUFgrade_T4" tourId="T4" class="ui-widget-content">T4</li>
							<li id="tourUFgrade_T5" tourId="T5" class="ui-widget-content">T5</li>
							<li id="tourUFgrade_T6" tourId="T6" class="ui-widget-content">T6</li>
							<li id="tourUFgrade_L" tourId="L" class="ui-widget-content first">L</li>
							<li id="tourUFgrade_WS" tourId="WS" class="ui-widget-content">WS</li>
							<li id="tourUFgrade_ZS" tourId="ZS" class="ui-widget-content">ZS</li>
							<li id="tourUFgrade_S" tourId="S" class="ui-widget-content">S</li>
							<li id="tourUFgrade_SS" tourId="SS" class="ui-widget-content">SS</li>
							<li id="tourUFgrade_AS" tourId="AS" class="ui-widget-content">AS</li>
							<li id="tourUFgrade_EX" tourId="EX" class="ui-widget-content">EX</li>
						</ol>
					</div>
					
					<!-- climbGrade -->
					<div class="mapFilterCriteria">
						<label for="tourUF_climbGrade" class="labelFirst">Klettergrad: </label>
						<ol id="tourUF_climbGrade" class="selectable filterItems">
							<li id="tourUFclimbGrade_I" tourId="I" class="ui-widget-content first">I</li>
							<li id="tourUFclimbGrade_II" tourId="II" class="ui-widget-content">II</li>
							<li id="tourUFclimbGrade_III" tourId="III" class="ui-widget-content">III</li>
							<li id="tourUFclimbGrade_IV" tourId="IV" class="ui-widget-content">IV</li>
							<li id="tourUFclimbGrade_V" tourId="V" class="ui-widget-content">V</li>
							<li id="tourUFclimbGrade_VI" tourId="VI" class="ui-widget-content">VI</li>
							<li id="tourUFclimbGrade_>VI" tourId=">VI" class="ui-widget-content">>VI</li>
						</ol>
					</div>

					<!--tStartTarget -->
					<!-- WAITING FOR JQUI WIDGET
					<div class="mapFilterCriteria">
						<br>t St-Zi  min. <input type="text" name="tStartTargetMin">
						max. <input type="text" name="tStartTargetMax">
					</div>
					-->
						
					<!--mUStartTarget -->
					<!-- WAITING FOR JQUI WIDGET
					<div class="mapFilterCriteria">
						<br>Aufstieg  min. <input type="text" name="mUStartTargetMin">
						max. <input type="text" name="mUStartTargetMax"><br>
					</div>
					-->

					<br><br>
					<input type="submit" class="button" id="tourApplyFilterUser" value="Daten filtern" />
				</fieldset>
				<!-- </div> -->
				
				<br class="clear"></br>
			</div>
			<div id="tourPanelInfo" class="visible">
				Mit Filter (F) mögliche Routen anzeigen
			</div>
			<div id="panelTourenMainSplitter">
				<div>
					<div id="panelTourenNestedSplitter">
						<div id="panelTourTouren">
							<div id="panelTourTour-content">
								<h2>Ausgewählte Routen</h2>
								<ul id="panelTourTour-sortable">
								</ul>
							</div>
						</div>
						<div id="panelTourSeg">
							<h2>Verfügbare Routen</h2>
							<div id="panelTourSeg-content">
							</div>
						</div>
					</div>
				</div>
				<div>	
					<div id="panelTourenMap">
						<h2>Verfügbare Routen</h2>
						<div id="panelTourenMap-Map"></div>
						</div>
				</div>
    		</div>
			
		</div> <!-- End div panelTour -->


		<!-- ========================================================================== -->
		<!-- ======================== ADMIN ========================================== -->
		<!-- ========================================================================== -->
		
		<div id="panelAdmin" class="tourdbPanel">
			<p></p>
			<br><br>
			<input type="submit" class="button" id="segGenKml" value="Create Single KML Files" />

			<br><br>
			<input type="submit" class="button" id="waypCalcWgs84" value="Calculate WGS84" />

			<br><br>
			<input type="submit" class="button" id="segBtnOpenDialog" value="Neues Segment hinzufügen" />

			<br><br>
			<input type="submit" class="button" id="waypBtnOpenDialog" value="Waypoint hinzufügen" />

		</div> <!-- End div panelAdmin -->

	</section> <!-- End main -->

	<footer id="footer">
        <p>&copy; 2016 leuti - Version 1.09.2016</p>
    </footer>
	
</body>
</html>
