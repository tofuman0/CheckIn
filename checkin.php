<?php
require_once('config.php');
require_once('functions.php');
$COMPANYNAME = null;
$SITENAME = null;
$SITEMESSAGE = null;
$SITELOGO = null;
$SITELOGOIMAGETYPE = null;
$SITEID = null;
$USERNAME = null;
if(isset($_POST["id"]) && isset($_POST["pin"])) {
	try {
		clearExpiredSessions();
		$result = checkToken();
		if($result == null)
		{
			goto skip;
		}
		$ID = bin2hex($_POST["id"]);
		$PIN = bin2hex($_POST["pin"]);
		$conn = new PDO("mysql:host=$db_host;dbname=$db_name", $db_username, $db_password);
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$query = "SELECT * FROM sites WHERE CONVERT(id,char) = X'$ID' AND pinnumber = X'$PIN'";
		$result = $conn->query($query)->fetch();
		if($result != null)
		{
			global $siteadmin_timeout;
			session_start();
			$_SESSION['adminaccess'] = 1;
			$_SESSION['adminsiteid'] = $_POST["id"];
			$_SESSION['admintimeout'] = time() + $siteadmin_timeout;
			session_write_close();
			include 'admin.php';
			$conn = null;
			die();
		}
		skip:
			$conn = null;
	}
	catch(PDOException $e) {
		echo $e;
	}
}
session_start();
if (isset($_SESSION['adminaccess']) && $_SESSION['adminaccess'] == 1) {
	if(isset($_POST["leaveadmin"]) || $_SESSION['admintimeout'] < time()) {
		$_SESSION = array();
		session_unset();
		session_destroy();
		session_write_close();
	}
	else {	
		session_write_close();
		include 'admin.php';
		die();
	}
}
session_write_close();
{
	$result = checkToken();
	if(!isset($result))
	{
		login:
		setcookie('loginToken', '', time()-3600);
		include 'signin.php';
		die();
	}
	$result = getSiteDetails();
	if(isset($result))
	{
		$COMPANYNAME = $result['companyname'];
		$SITENAME = $result['sitename'];
		$SITEMESSAGE = $result['sitemessage'];
		$SITEID = $result['id'];
		if(isset($result['siteimage']))
		{
			$SITELOGO = base64_encode($result['siteimage']);
		}
		else
		{
			$SITELOGO = null;
		}
		$SITELOGOIMAGETYPE = $result['siteimagetype'];
	}
	else
	{
		goto login;
	}
}
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Check-in</title>
		<link rel="stylesheet" href="css/onsenui.css">
		<link rel="stylesheet" href="css/onsen-css-components.min.css">
		<link rel="stylesheet" href="css/checkin.css">
		<script src="js/onsenui.min.js"></script>
		<script src="js/core.functions.js"></script> 
		<script src="js/functions.js"></script>  
		<script src="js/checkin.functions.js"></script> 
	</head>
	<body>
		<ons-page>
			<ons-toolbar>
				<div class="left">
					<ons-toolbar-button onclick="prev()" id="leftBtn">
						&lt; Staff
					</ons-toolbar-button>
				</div>
				<div class="center">
				<ons-gesture-detector>
				<div id="title">Check-in: Home</div>
				</ons-gesture-detector>
				</div>
				<div class="right">
					<ons-toolbar-button onclick="next()" id="rightBtn">
						Visitor &gt;</ons-icon>
					</ons-toolbar-button>
				</div>
			</ons-toolbar>
			<ons-carousel var="carousel" fullscreen swipeable auto-scroll auto-refresh id="carousel" initial-index=1>
			<ons-carousel-item class="carouselleft" id="stafflist">
				<!-- Staff List Dynamically Refreshed Here -->
			</ons-carousel-item>
			<ons-carousel-item class="carouselhome">
			<?php
			if($COMPANYNAME != null)
			{
				echo "<div style=\"text-align: center; font-size: 30px; margin-top: 20px; color: #fff;\">
				<h1 style=\"text-shadow: -1px -1px 0 #C0C0C0, 1px -1px 0 #C0C0C0, -1px 1px 0 #C0C0C0, 1px 1px 0 #C0C0C0; font-size: 5.0vw\">$COMPANYNAME</h1>
				</div>";
			}
			?>
			<div>
				<?php
					if($SITELOGO != null) echo "<img src=\"data:image/$SITELOGOIMAGETYPE;base64,$SITELOGO\" class='sitelogo'/>";
					if($COMPANYNAME != null && $SITENAME != null) echo "<h2 style=\"width: 100%; text-align: center; color: #808080\">Welcome to the $COMPANYNAME $SITENAME Depot<br/>$SITEMESSAGE</h2>";
				?>
			</div>
			</ons-carousel-item>
			<ons-carousel-item class="carouselright" id="visitorlist">
				<!-- Visitor List Dynamically Refreshed Here -->
			</ons-carousel-item>
			</ons-carousel>
		</ons-page>
		<ons-modal id="adminpanel" direction="up">
			<div style="position: relative; margin: auto; text-align: center; width: 240px; background: #c0c0c0; padding: 16px">				
				<ons-row>
					<ons-col style="margin: 8px"><ons-button modifier="large" onClick="enterPin(1)">1</ons-button></ons-col><ons-col style="margin: 8px"><ons-button modifier="large" onClick="enterPin(2)">2</ons-button></ons-col><ons-col style="margin: 8px"><ons-button modifier="large" onClick="enterPin(3)">3</ons-button></ons-col>
				</ons-row>
				<ons-row>
					<ons-col style="margin: 8px"><ons-button modifier="large" onClick="enterPin(4)">4</ons-button></ons-col><ons-col style="margin: 8px"><ons-button modifier="large" onClick="enterPin(5)">5</ons-button></ons-col><ons-col style="margin: 8px"><ons-button modifier="large" onClick="enterPin(6)">6</ons-button></ons-col>
				</ons-row>
				<ons-row>
					<ons-col style="margin: 8px"><ons-button modifier="large" onClick="enterPin(7)">7</ons-button></ons-col><ons-col style="margin: 8px"><ons-button modifier="large" onClick="enterPin(8)">8</ons-button></ons-col><ons-col style="margin: 8px"><ons-button modifier="large" onClick="enterPin(9)">9</ons-button></ons-col>
				</ons-row>
				<ons-row>
					<ons-col style="margin: 8px"><ons-button modifier="large" onclick="modalHide('adminpanel')">Back</ons-button></ons-col><ons-col style="margin: 8px"><ons-button modifier="large" onClick="enterPin(0)">0</ons-button></ons-col><ons-col style="margin: 8px"><ons-button modifier="large" onClick="enterAdmin(<?php echo "$SITEID" ?>)">Enter</ons-button></ons-col>
				</ons-row>
			</div>
		</ons-modal>
		<ons-modal id="signoutpanel" direction="up">
			<div style="position: relative; margin: auto; text-align: center; width: 240px; background: #c0c0c0; padding: 16px">				
				<ons-row>
					<ons-col style="margin: 8px"><ons-button modifier="large" onclick="modalHide('signoutpanel')">Back</ons-button></ons-col><ons-col style="margin: 8px"><ons-button modifier="large" onclick="logout()">Logout</ons-button></ons-col>
				</ons-row>
			</div>
		</ons-modal>
		<ons-dialog id="addvisitor">
			<div style="text-align: center; padding: 10px;">
				<h3>Visitor details:</h3>
				<ons-input id="va-Title" modifier="underbar" placeholder="Title" float></ons-input> <br />
				<ons-input required id="va-Firstname" modifier="underbar" placeholder="First Name" float></ons-input> <br />
				<ons-input required id="va-Lastname" modifier="underbar" placeholder="Last Name" float></ons-input> <br />
				<ons-input id="va-Company" modifier="underbar" placeholder="Company" float></ons-input> <br />
				<ons-input id="va-Reg" modifier="underbar" placeholder="Vehicle Registration" float></ons-input> <br /><br />
				<?php
					echo "<ons-button onclick=\"addVisitor($SITEID)\">Add</ons-button>";
				?>
				<ons-button onclick="closeAddVisitor()">Cancel</ons-button>
			  </p>
			</div>
		  </ons-dialog>
	</body>
</html>