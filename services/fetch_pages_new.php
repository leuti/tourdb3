<?php

// ---------------------------------------------------------------------------------------------
// This script selects tracks, segments, waypoints and sends HTML code to display table 
//
// INPUT
//
// OUTPUT

// Created: 2.4.2018 - Daniel Leutwyler
// ---------------------------------------------------------------------------------------------
//
// Tasks
// * 

// Set timezone (otherwise warnings are written to log)
date_default_timezone_set('Europe/Zurich');
include("config.inc.php");                                                  // Include config file

if ($debugLevel >= 1){
    $logFileLoc = dirname(__FILE__) . "/../log/fetch_pages.log";                // Assign file location
    $logFile = @fopen($logFileLoc,"a");     
    fputs($logFile, "=================================================================\r\n");
    fputs($logFile, date("Ymd-H:i:s", time()) . "-Line 11: fetch_pages.php opened \r\n"); 
};

// continue only if $_POST is set and it is a Ajax request
if ($debugLevel >= 3){
    fputs($logFile, "Line 16: _SERVER:  ". $_SERVER['HTTP_X_REQUESTED_WITH'] . "\r\n");
    fputs($logFile, "Line 17: _POST[page]:  " . $_POST["page"] . "\r\n");
};
if(isset($_POST) && isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'){
       
    // Get page number from Ajax POST and set to 1 if not delivered
	if(isset($_POST["page"])){
		$page_number = filter_var($_POST["page"], FILTER_SANITIZE_NUMBER_INT, FILTER_FLAG_STRIP_HIGH); //filter number
		if(!is_numeric($page_number)){die('Invalid page number!');} //in case of invalid page number
	}else{
		$page_number = 1; // If there's no page number, set it to 1
	}

    // Sets WHERE string if sqlFilterString has been provided	
    if(isset($_POST["sqlFilterString"]) && $_POST["sqlFilterString"] != ''){
    	$sqlFilterString = $_POST["sqlFilterString"]; // Set sqlFilterString to string passed by XHR
    }else{
		$sqlFilterString = ''; // If there's no sql search string delivered, set it to ''
    };
    if ($debugLevel >= 3){
        fputs($logFile, 'Line 37: $page: ' . $page_number . "\r\n");
        fputs($logFile, 'Line 38: $sqlSearchString: ' . $sqlFilterString . "\r\n");
    };

    // Get total number of records from database for pagination
    $sql = "SELECT COUNT(*) FROM tbl_tracks WHERE " . $sqlFilterString; // select to count number of records for current filter
    
    if ($debugLevel >= 1) fputs($logFile, 'Line 64: sql: ' . $sql . "\r\n");
    
    $results = $conn->query($sql);  // Open sql connection 
    $get_total_rows = $results->fetch_row(); // Get sql result
  	$total_pages = ceil($get_total_rows[0]/$item_per_page); // Calc total pages
	$page_position = (($page_number-1) * $item_per_page); // Get starting page position to fetch the records
    if ($debugLevel >= 3){
        fputs($logFile, 'Line 49: $total_pages: ' . $total_pages . ' | current $page_position: ' . 
            $page_position . "\r\n");
    };   	
	
    // Select statement to read data from vw_waypoints
    $sql = "SELECT trkId, trkTrackName, trkDateBegin  
            FROM tbl_tracks 
            WHERE ";
    $sql .= $sqlFilterString;  
    $sql .= " ORDER BY trkDateBegin DESC, trkId DESC LIMIT $page_position, $item_per_page";

    if ($debugLevel >= 1) fputs($logFile, 'Line 64: sql: ' . $sql . "\r\n");

    $records = mysqli_query($conn, $sql);

    // Start writing HTML for output - header
    echo '<table>';
    echo '<tr class="header">';
    echo '<th>ID</th>';                           // 1
    echo '<th>Date</th>';                         // 2
    echo '<th>Name</th>';                         // 3
    echo '<th>Edit</th>';                         // 4
    echo '<th>Del</th>';                          // 5
    echo '</tr>';

    // Write for each waypoint one line
    while($singleRecord = mysqli_fetch_assoc($records)) {

        echo '<tr>';
        echo '<td>'.$singleRecord["trkId"].'</td>';                       // 1
        echo '<td>'.$singleRecord["trkDateBegin"].'</td>';               // 2
        echo '<td>'.$singleRecord["trkTrackName"].'</td>';                // 2
        echo '<td>';
        echo '<ul>';
        echo '<li class="button_Li">';
        echo '<a class="uiTrack uiTrackEditBtn " href="#trkEdit_' . $singleRecord["trkId"] . '">';
        echo '<img id="trkEdit_' . $singleRecord["trkId"] . '" src="css/images/edit16.png">';
        echo '</a>';
        echo '</li>';
        echo '</ul>';
        echo '</td>';
        echo '<td>';
        echo '<ul>';
        echo '<li class="button_Li">';
        echo '<a class="trkDel uiTrackEditBtn " href="#trkDel_' . $singleRecord["trkId"] . '">';
        echo '<img id="trkDel_' . $singleRecord["trkId"] . '" src="css/images/delete.png">';
        echo '</a>';
        echo '</li>';
        echo '</ul>';
        echo '</td>';
        echo '</tr>';
    }
    // Write remaining HTML code
    echo '</table>'; 

    // Write the div to store the pagination data 
    echo '<div id="pag_div">';

    // We call the pagination function here to generate Pagination link for us. 
    $pag = paginate_function($item_per_page, $page_number, $get_total_rows[0], $total_pages);
    echo $pag;
    if ($debugLevel >= 5){
            fputs($logFile, "Line 124: pagination: " . $pag . "\r\n");
        };
    echo '</div>';
    
    exit;
    
};

//DEBUG
if ($debugLevel >= 1){
    fclose($logFile);
};

################ pagination function #########################################
function paginate_function($item_per_page, $current_page, $total_records, $total_pages)
{
    $pagination = '';
    if($total_pages > 0 && $total_pages != 1 && $current_page <= $total_pages){ // Verify total pages and current page number
        $pagination .= '<ul class="pagination">';
        
        $right_links    = $current_page + 3; 
        $previous       = $current_page - 3; // Previous link 
        $next           = $current_page + 1; // Next link
        $first_link     = true; // Boolean var to decide our first link
        
        if($current_page > 1){
			$previous_link = ($previous==0)? 1: $previous;
            $pagination .= '<li class="first"><a href="#" data-page="1" title="First">&laquo;</a></li>'; // First link
            $pagination .= '<li><a href="#" data-page="'.$previous_link.'" title="Previous">&lt;</a></li>'; // Previous link
                for($i = ($current_page-2); $i < $current_page; $i++){ // Create left-hand side links
                    if($i > 0){
                        $pagination .= '<li><a href="#" data-page="'.$i.'" title="Page'.$i.'">'.$i.'</a></li>';
                    }
                }   
            $first_link = false; // Set first link to false
        }
        
        if($first_link){ // If current active page is first link
            $pagination .= '<li class="first active">'.$current_page.'</li>';
        }elseif($current_page == $total_pages){ // If it's the last active link
            $pagination .= '<li class="last active">'.$current_page.'</li>';
        }else{ // Regular current link
            $pagination .= '<li class="active">'.$current_page.'</li>';
        }
                
        for($i = $current_page+1; $i < $right_links ; $i++){ // Create right-hand side links
            if($i<=$total_pages){
                $pagination .= '<li><a href="#" data-page="'.$i.'" title="Page '.$i.'">'.$i.'</a></li>';
            }
        }
        if($current_page < $total_pages){ 
				$next_link = ($i > $total_pages) ? $total_pages : $i;
                $pagination .= '<li><a href="#" data-page="'.$next_link.'" title="Next">&gt;</a></li>'; // Next link
                $pagination .= '<li class="last"><a href="#" data-page="'.$total_pages.'" title="Last">&raquo;</a></li>'; // Last link
        }
        $pagination .= '</ul>'; 
    }
    return $pagination; // Return pagination links
}

?>