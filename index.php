<?php
	// Start session before ending it (for users coming back from sub pages)
	session_start();
	
	// End session
	session_destroy();
	$_SESSION = array();								// empty SESSION variable
?>
	
<!DOCTYPE HTML>
<html>
<head><meta charset="utf8"><title>Tour DB 3.0 - Login</title></head></head>
<body>
	<h3>Login-Page</h3>
	<form action="/tourdb3/services/introPage.php" method="post">
		<p><input name="login"> Login</p>
		<p><input name="passwd"> Password</p>	 
		<p><input type="submit" value="login"></p>
		</form>
</body>
</html>
