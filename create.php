<?php
require_once('config.php');
require_once('functions.php');
$errorStr = null;
$COMPANYNAME = null;
$LOGONMESSAGE = null;
$COMPANYLOGO = null;
$COMPANYLOGOIMAGETYPE = null;
if($allow_create) {
	if(isset($_POST["username"]) && isset($_POST["password"]) && isset($_POST["displayname"]) && isset($_POST["email"])) {
		$USERNAME = bin2hex($_POST["username"]);
		$PASSWORD = bin2hex(password_hash($_POST["password"], PASSWORD_BCRYPT));
		$DISPLAYNAME = bin2hex($_POST["displayname"]);
		$EMAIL = bin2hex($_POST["email"]);
		try {
			$conn = new PDO("mysql:host=$db_host;dbname=$db_name", $db_username, $db_password);
			$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$result = $conn->query("SELECT id, username FROM accounts WHERE username = X'$USERNAME'")->fetch();
			if($result != null)
			{
				$errorStr = "Account Already Exists";
				goto endlogin;
			} else {
				$conn->query("INSERT INTO accounts(isadmin, username, password, email, site, displayname, disabled) VALUES(1, X'$USERNAME', X'$PASSWORD', X'$EMAIL', 0, X'$DISPLAYNAME', 0)");
				$errorStr = "{$_POST["username"]} Created.";
				goto endlogin;
			}
			$conn = null;
			header("Location: /checkin");
			die();
			endlogin:
		}
		catch(PDOException $e) {
			echo $e;
		}
	}
	{
		$result = getConfiguration();
		if($result != null)
		{
			$COMPANYNAME = $result['companyname'];
			$LOGONMESSAGE = $result['logonmessage'];
			$COMPANYLOGO = base64_encode($result['logo']);
			$COMPANYLOGOIMAGETYPE = $result['logoimagetype'];
		}
	}
}
else {
	header("Location: /checkin");
	die();
}
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Check-in: Create Account</title>
		<link rel="stylesheet" href="css/checkin.css">
		<link rel="stylesheet" href="css/onsenui.css">
		<link rel="stylesheet" href="css/onsen-css-components.min.css">
		<script src="js/onsenui.min.js"></script>
		<script src="js/core.functions.js"></script> 
	</head>
<body>
<ons-page class="logonpage">   
    <ons-toolbar>  
        <div class="center">Check-in: Create Account</div>  
    </ons-toolbar>  
    <div style="text-align: center; margin-top: 30px;">  
		<p class='logonmessage' style="color: red;">This is only to be used to create <b>admin</b> accounts if you have been locked out of the system. Set allow_create in config to enable and ensure that this is disabled on a live server. Use the <a href="./admin/">admin portal</a> to create further accounts.</p>
        <p>  
            <ons-input id="username" modifier="underbar" placeholder="Username" float></ons-input>  
        </p>  
        <p>  
            <ons-input id="password" modifier="underbar" placeholder="Password" float></ons-input>  
        </p>
		<p>  
            <ons-input id="email" modifier="underbar" placeholder="Email Address" float></ons-input>  
        </p>
		<p>  
            <ons-input id="displayname" modifier="underbar" placeholder="Display Name" float></ons-input>  
        </p>
        <p style="margin-top: 30px;">  
            <ons-button onclick="createAccount()">Create</ons-button>  
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