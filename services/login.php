<?php
// ---------------------------------------------------------------------------------------------
// Service verifying if the provided login and password are valid
//
// Possible cases and their reaction
// ---------------------------------
//
// Case 1: login is not provided                                        | No login provided
// Case 2: login und pw are correct                                     | give access to site
// Case 3: no record with login found --> user id not in DB             | return ERR "login failed"
// Case 4: user record found, but password provided <> db password      | return ERR "login failed"
// Case 5: Error in SQL                                                 | return ERR "SQL Error message"
// 
//
// Input Parameters (JSON object):
// -------------------------------
// login: login name entered by user
// password: password provided by user
// 
// Output Parameter (JSON object):
// -------------------------------
// login: login name
// loginStatus: OK or ERR
// loginTime: time of login (attempt)
// message: Message about login result
// sessionId: ID of active session
// 
//
// Created: 24.06.2019 - Daniel Leutwyler
// ---------------------------------------------------------------------------------------------


// -----------------------------------
// Set variables and parameters
include("./config.php");                                            // include config file
date_default_timezone_set("Europe/Zurich");                             // must be set when using time functions

// Open file for import log
$logFile = dirname(__FILE__) . "/../log/" . basename(__FILE__, ".php") . ".log";                   // Assign file location
if ( $debugLevel >= 1) $logFileName = @fopen($logFile,"a");             // open log file handler 
if ( $debugLevel >= 1 ) fputs($logFileName, "\r\n============================================================\r\n");    
if ( $debugLevel >= 1 ) fputs($logFileName, "login.php started: " . date("Ymd-H:i:s", time()) . "\r\n");    

// variables passed on by client (as formData object)
$receivedData = json_decode ( file_get_contents("php://input"), true );
$login = $receivedData["login"];
$password = $receivedData["password"];

if ( $debugLevel >= 3 ) fputs($logFileName, "Line " . __LINE__ . ": User <$login> has logged in.\r\n");    

// Start or restart session 
session_start();

// check if login and session is set
if ( isset($login) && $login != "" ) {

    // retrieve password and decrypt
    $sql = "SELECT `usrId`, AES_DECRYPT(`usrPasswd`, 'vjLzGfqxnOFEWCpIbeXdFjnPWTKcjo9a') AS `usrPasswd` ";
    $sql .= "FROM `tbl_users` WHERE `usrLogin` = '$login' ";
  
    if ($debugLevel >= 3){
        fputs($logFileName, "Line " . __LINE__ . ": sql: " . $sql . "\r\n");
    };

    if ( $result = mysqli_query( $conn, $sql ) ) {                              // run SQL and store result in $result
        $num = mysqli_num_rows($result);                                    // count number of results 
        if ( $num > 0 ) {                                                   // 1 result is expected 
            while ( $resultLine = mysqli_fetch_assoc( $result ) ) {         // loop through result set line by line
                $passwordDb = $resultLine["usrPasswd"];
                $usrId = $resultLine["usrId"];

                // Loop through result (only one expected)
                if ($debugLevel >= 3) fputs($logFileName, "Line " . __LINE__ . ": num: $num | password $password | passwordDb: $passwordDb\r\n");

                if ( $passwordDb == $password ) {
                    // Case 2: login und pw are correct    
                    if ($debugLevel >= 3) fputs($logFileName, "Line " . __LINE__ . ": Case 2: login und pw are correct\r\n");
                    $returnObject["loginStatus"] = "OK";                        // set login status to OK
                    $returnObject["message"] = "Login successful";
                    $returnObject["usrId"] = $usrId;
                } else {
                    // Case 4: user record found, but password provided <> db password
                    if ($debugLevel >= 3) fputs($logFileName, "Line " . __LINE__ . ": Case 4: user record found, but password provided <> db password\r\n"); 
                    $returnObject["loginStatus"] = "ERR";                        // set login status to OK
                    $returnObject["message"] = "Login failed";
                }
            }
        } else {
            // Case 3: no record with login found --> user id not in DB 
            if ($debugLevel >= 3) fputs($logFileName, "Line " . __LINE__ . ": Case 3: no record with login found --> user id not in DB \r\n");
            $returnObject["loginStatus"] = "ERR";                               // Return error    
            $returnObject["message"] = "Login failed";                     // Return error            
        }
    } else {
        // Case 5: Error in SQL
        if ($debugLevel >= 3) fputs($logFileName, "Line " . __LINE__ . ": Case 5: Error in SQL\r\n");
        $returnObject["loginStatus"] = "ERR";                               // Return error    
        $returnObject["message"] = "SQL Error";                     // Return error        
    }
   
} else {
    // Case 1: login is not provided
    if ($debugLevel >= 3) fputs($logFileName, "Line " . __LINE__ . ": Case 1: login is not provided\r\n") ;

    $returnObject["loginStatus"] = "ERR";                               // Return error    
    $returnObject["message"] = "Login failed";                     // Return error    
}
// add common fields to object
$returnObject["login"] = $login;                            // set session var
$returnObject["sessionId"] = session_id();                  // add session id to return object
$returnObject["loginTime"] = date("Ymd-H:i:s", time());

echo json_encode($returnObject);                                        // encode return object to JSON
if ( $debugLevel >= 1 ) 
{
    fputs($logFileName, "Line " . __line__ . ": Login script completed --> sessionId: " . $returnObject["sessionId"] . 
    " | login: " . $login . " | loginStatus: " . $returnObject["loginStatus"] . "\r\n");
    fclose($logFileName);                                               // close log file
}
?>