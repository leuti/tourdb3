debug = true;

// =====================================
// ====== M A I N   S E C T I O N ======
// =====================================
$(document).ready(function() {

    // Manages the behaviour when clicking on the main topic buttons
    $('.topicButtons').each(function() {
        var $thisTopicButton = $(this);                                     // $thisTopicButton becomes ul.topicButtons
        $activeButton = $thisTopicButton.find('li.active');                 // Find and store current active li element
        var $activeButtonA = $activeButton.find('a');                       // Get link <a> from active li element 
        $topicButton = $($activeButtonA.attr('href'));                      // Get active panel

        $(this).on('click', '.mainButtonsA', function(e) {                  // When click on a topic tab (li item)
            if (debug) { console.info(".topic-control: onclick function entered"); };
            e.preventDefault();                                             // Prevent link behaviour
            var $activeButtonA = $(this)                                    // Store the current link <a> element
            var buttonId = this.hash;                                       // Get div class of selected topic (e.g #panelDisplay)
            
            // Run following block if selected topic is currently not active
            if (buttonId && !$activeButtonA.is('.active')) {
                $topicButton.removeClass('active');                         // Make current panel inactive
                $activeButton.removeClass('active');                        // Make current tab inactive

                $topicButton = $(buttonId).addClass('active');              // Make new panel active
                $activeButton = $activeButtonA.parent().addClass('active'); // Make new tab active
            }
            if (debug) { console.info(".topic-control: onclick function completed"); };
        }); 
    }); 

    
});    


    