<?php

    $db_username 		= 'leuti'; //database username
    $db_password 		= 'yTBgve0xgIAgaGyF8nXj'; //database password
    $db_name 			= 'tourdb3'; //database name
    $db_host 			= ''; //hostname or IP
    $item_per_page 		= 20; //item to display per page

    $conn = new mysqli($db_host, $db_username, $db_password, $db_name);

    //Output any connection error
    if ($conn->connect_error) {
        die('Connection failed : ' . $conn->connect_errno );
    };
    mysqli_set_charset( $conn, 'utf8');
    //mysql_query("SET character_set_results = 'utf8', character_set_client = 'utf8', character_set_connection = 'utf8', character_set_database = 'utf8', character_set_server = 'utf8'", $con);
    //$con = mysqli_connect("", $db_username,$db_password,$db_name);

?>