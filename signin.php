<?php
require_once('config.php');
require_once('functions.php');
$errorStr = null;
$COMPANYNAME = null;
$LOGONMESSAGE = null;
$COMPANYLOGO = null;
$COMPANYLOGOIMAGETYPE = null;
if(isset($_POST["username"]) && isset($_POST["password"])) {
	$USERNAME = bin2hex($_POST["username"]);
	#$PASSWORD = bin2hex(password_hash($_POST["password"], PASSWORD_BCRYPT));
	$PASSWORD = $_POST["password"];
	$conn = new PDO("mysql:host=$db_host;dbname=$db_name", $db_username, $db_password);
	$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$query = "SELECT id, username, password, disabled FROM accounts WHERE username = X'$USERNAME' AND disabled = 0";
	$result = $conn->query($query)->fetch();
	if($result != null)
	{
		$PWHASH = $result['password'];
		if(password_verify($PASSWORD, $PWHASH) == FALSE) {
			setcookie('loginToken', '', time()-3600);
			$errorStr = "Invalid Username or Password";
			goto endlogin;
		}
		$ID = $result['id'];
		$HWID = getHWID();
		$TOKEN = getToken(hex2bin($USERNAME), $HWID);
		$TIME = time() + (86400 * 30);
		setCookie('loginToken', $TOKEN, $TIME);
		$stmt = $conn->prepare("INSERT INTO session(accountid, token, expire) VALUES(?, ?, DATE(FROM_UNIXTIME(?)))");
		$stmt->execute([$ID, $HWID, $TIME]);
	} else {
		setcookie('loginToken', '', time()-3600);
		$errorStr = "Invalid Username or Password";
		goto endlogin;
	}
	$conn = null;
	header("Location: /checkin");
	die();
	endlogin:
} else if (isset($_POST["logout"])){
	if($_POST["logout"] == 'true') {
		setcookie('loginToken', '', time()-3600);
	}	
} 
{
	try {
		$result = getConfiguration();
		if($result != null)
		{
			$COMPANYNAME = $result['companyname'];
			$LOGONMESSAGE = $result['logonmessage'];
			$COMPANYLOGO = base64_encode($result['logo']);
			$COMPANYLOGOIMAGETYPE = $result['logoimagetype'];
		}
	}
	catch(PDOException $e) {
	}
}
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Check-in: Sign in</title>
		<link rel="stylesheet" href="css/onsenui.css">
		<link rel="stylesheet" href="css/onsen-css-components.min.css">
		<link rel="stylesheet" href="css/checkin.css">
		<script src="js/onsenui.min.js"></script>
		<script src="js/core.functions.js"></script> 
	</head>
<body>
<ons-page class="logonpage">   
    <ons-toolbar>  
        <div class="center">Check-in: Sign in</div>  
    </ons-toolbar>  
    <div style="text-align: center; margin-top: 30px;">  
		<?php
			if($COMPANYNAME != null) {
				echo "<h1 class='companyname'>$COMPANYNAME</h1>";
			}
			if($COMPANYLOGO != null) {
				echo "<img src=\"data:image/$COMPANYLOGOIMAGETYPE;base64,$COMPANYLOGO\" class='companylogo'/>";
			}
			if($LOGONMESSAGE != null) {
				echo "<p class='logonmessage'>$LOGONMESSAGE</p>";
			}
		?>
        <p>  
            <ons-input id="username" modifier="underbar" placeholder="Username" float></ons-input>  
        </p>  
        <p>  
            <ons-input id="password" modifier="underbar" type="password" placeholder="Password" float></ons-input>  
        </p>  
        <p style="margin-top: 30px;">  
            <ons-button onclick="login()">Sign in</ons-button>  
        </p>  
		<?php
		if($errorStr != null) {
			echo "<p style=\"color: red;\">$errorStr</p>";
		}
		?>
    </div>  
</ons-page> 
</body>
</html>