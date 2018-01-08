<?php
	// Start session before ending it (for users coming back from sub pages)
	session_start();
	
	// End session
	session_destroy();
	$_SESSION = array();								// empty SESSION variable
?>
	
<!DOCTYPE HTML>
<html>
<head>
	<meta charset="utf8">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<!-- next three lines added with goal to apply css changes immediately -->
	<meta http-equiv="Cache-control" content="no-cache">
	<meta http-equiv="pragma" content="no-cache">
	<meta http-equiv="Expires" content="0">
	<title>Tour DB 3.0 - Login</title>
</head>
<body>
	<h3>Login-Page</h3>
	<form action="/tourdb3/introPage.php" method="post">
		<p><input name="login"> Login</p>
		<p><input name="passwd"> Password</p>	 
		<p><input type="submit" value="login"></p>
	</form>
</body>
</html>
