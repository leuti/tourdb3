// =====================================
// ====== M A I N   S E C T I O N ======
// =====================================
$(document).ready(function() {
    // ----------- ADMIN -------
    // Generate single KML files - admin function 
        $(document).on('click', '#segGenKml', function (e) {
            var xhr = new XMLHttpRequest();
            phpLocation = document.URL + "seg_gen_single_kml.php";          // Variable to store location of php file
            xhr.open ('POST', phpLocation, true);                // Make XMLHttpRequest - in asynchronous mode to avoid wrong data display in map (map displayed before KML file is updated)
            xhr.send(encodeURI());            
            console.info("seg_gen_single_kml.php completed");
        });
});    


    