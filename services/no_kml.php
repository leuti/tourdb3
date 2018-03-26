<?php
// ---------------------------------------------------------------------------------------------
// This script returns an empty OK message. This is because I currently have no solution for the 
// $.ajax().when for multiple calls (calls only required when genKml = true)

// Created: 21.03.2018 - Daniel Leutwyler
// ---------------------------------------------------------------------------------------------
//
// Tasks
// * 

// Create return object
$returnObject['status'] = 'OK';                                             // add status field (OK) to trackobj
$returnObject['message'] = 'This php returns always an empty OK message';   // add empty error message to trackobj
$returnObject['recordcount'] = 0;
echo json_encode($returnObject);                                            // echo JSON object to client
exit;

?>

