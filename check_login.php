<?php
    // Start or restart session 
    session_start();

    // in case this script is started from login page
    if(isset($_POST["login"]))
    {
        if($_POST["login"] == "leuti" && $_POST["passwd"] == "a")
        {
            $_SESSION["login"] = $_POST["login"];
        }
    }

    // check if within sessioin
    if(!isset($_SESSION["login"]))
        exit("<p>No access<br><a href='index.php'>"
            . "Back to Login</a></p>)");
?>
<!DOCTYPE HTML>
<html>
<head><meta charset="utf8"><title>Tour DB 3.0</title></head></head>
<body>
    <h3>Intro Page</h3>
    <?php
        echo "<p>Hello " . $_SESSION["login"] . "</p><br>";
    ?>
    <p><a href="beliebige.php">Go to beliebige Page</a></p>
    <p><a href="index.php">Log-off</a></p>
</body></html>
