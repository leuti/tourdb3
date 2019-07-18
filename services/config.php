<?php

    $debugLevel = 0;    // set debug level for all services                                                            // 0 = off, 1 = min, 3 = a lot, 5 = all 

    $db_username 		= "leuti"; //database username
    $db_password 		= "yTBgve0xgIAgaGyF8nXj"; //database password
    //$db_username 		= "root"; //database username
    //$db_password 		= "root"; //database password
    $db_name 			= "tourdb2_prod"; //database name
    $db_host 			= ""; //hostname or IP
    $item_per_page 		= 20; //item to display per page

    $conn = new mysqli($db_host, $db_username, $db_password, $db_name);

    //Output any connection error
    if ($conn->connect_error) {
        die("Connection failed : " . $conn->connect_errno );
    };
    mysqli_set_charset( $conn, "utf8");
    
?>