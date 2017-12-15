<?php
    // Start or restart session
    session_start();

    // Check if within session
    if(!isset($_SESSION["login"]))
        exit("<p>No access<br><a href='/tourdb3/index.php'>"
            . "Back to login</a></p>");
?>
<!DOCTYPE HTML>
<html>
<head><meta charset="utf8"><title>Tour DB 3.0</title></head></head>
<body>
    <h3>Beliebige Page</h3>
    <?php
        echo "<p>Hello " . $_SESSION["login"] . "</p>";
        echo "<br><p>This is the beliebige page</p>";
    ?>
    <p><a href="/tourdb3/services/introPage.php">Intro page</a></p>
    <p><a href="/tourdb3/index.php">Log-off</a></p>
</body></html>