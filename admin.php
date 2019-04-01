<?php
require_once('config.php');
require_once('functions.php');
try {
	$TOKEN = resolveToken($_COOKIE['loginToken']);
	if($TOKEN == null)
	{
		goto login;
	}
	$USERNAME = bin2hex($TOKEN[0]);
	$TOKENSTR = bin2hex($TOKEN[1]);
	$conn = new PDO("mysql:host=$db_host;dbname=$db_name", $db_username, $db_password);
	$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$result = $conn->query("SELECT accounts.id, accounts.username, session.token, session.accountid, session.expire FROM session JOIN accounts ON accounts.id = session.accountid WHERE accounts.username = X'$USERNAME' and session.token = X'$TOKENSTR'")->fetch();
	if($result == null)
	{
		setcookie('loginToken', '', time()-3600);
	login:
		include 'checkin.php';
		$conn = null;
		die();
	}
	if(!isset($_SESSION['adminaccess']) || $_SESSION['adminaccess'] != 1)
	{
		goto login;
	}
}
catch(PDOException $e) {
	include 'checkin.php';
}
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Check-in: Admin</title>
		<link rel="stylesheet" href="css/checkin.css">
		<link rel="stylesheet" href="css/onsenui.css">
		<link rel="stylesheet" href="css/onsen-css-components.min.css">
		<script src="js/onsenui.min.js"></script>
		<script src="js/core.functions.js"></script> 
	</head>
<body>
<ons-page class="adminpage">   
    <ons-toolbar>  
        <div class="center">Check-in: Admin</div>  
    </ons-toolbar>  
    <div style="text-align: center; margin-top: 30px;">  
		Admin stuff...
    </div>  
</ons-page> 
</body>
</html>