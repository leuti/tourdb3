// =====================================
// ====== M A I N   S E C T I O N ======
// =====================================
$(document).ready(function() {
    // ----------- ADMIN -------
    // Generate single KML files - admin function 
    /*    $(document).on('click', '#segGenKml', function (e) {
            var xhr = new XMLHttpRequest();
            phpLocation = document.URL + "seg_gen_single_kml.php";          // Variable to store location of php file
            xhr.open ('POST', phpLocation, true);                // Make XMLHttpRequest - in asynchronous mode to avoid wrong data display in map (map displayed before KML file is updated)
            xhr.send(encodeURI());            
            console.info("seg_gen_single_kml.php completed");
        });*/

    // Create Add Waypoint Dialog Box
    // Source from JQuery UI home page - dialog
    $( function() {
        var $divGenKml = $('#divGenKml');
        var $frmGenKml = $('#frmGenKml');
        var $whereGenKml = $('input:text');

        $divGenKml.show();
        $frmGenKml.hide();

        $('#btnGenKml').on('click', function() {
            //$divGenKml.hide();
            $frmGenKml.show();
        });
        
        $frmGenKml.on('submit', function(e) {
            e.preventDefault();                                             // check if required
            var xhr = new XMLHttpRequest();
            var xhrParams = "&whereGenKml=" + $whereGenKml.val();
            
            //var phpLocation = window.location.host + "/services/genOwnTracksKml.php";          // Variable to store location of php file
            //var phpLocation = "http://localhost:8888/tourdb2/services/genOwnTracksKml.php";          // Variable to store location of php filevar xhrParams = "&sqlWhereClause=" + $whereGenKml;
            var phpLocation = "./services/genOwnTracksKml.php";          // Variable to store location of php file
            xhr.open ('POST', phpLocation, true);                // Make XMLHttpRequest 
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");  // Set header to encode special characters like %
            xhr.send(encodeURI(xhrParams)); 
            console.info("genOwnTracksKml.php completed");
            $frmGenKml.hide();
        });
        
    });

    
});    


    