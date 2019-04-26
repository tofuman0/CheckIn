<?php
require_once('config.php');
require_once('functions.php');
$result = checkToken();
if(!isset($result))
{
	setcookie('loginToken', '', time()-3600);
login:
	include 'checkin.php';
	die();
}
session_start();
global $siteadmin_timeout;
if(!isset($_SESSION['adminaccess']) || $_SESSION['adminaccess'] != 1 || $_SESSION['admintimeout'] < time())
{
	$_SESSION = array();
	session_unset();
	session_destroy();
	session_write_close();
	goto login;
}
$_SESSION['admintimeout'] = time() + $siteadmin_timeout;
session_write_close();
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Check-in: Admin</title>
		<link rel="stylesheet" href="css/onsenui.css">
		<link rel="stylesheet" href="css/onsen-css-components.min.css">
		<link rel="stylesheet" href="css/checkin.css">
		<script src="js/onsenui.min.js"></script>
		<script src="js/functions.js"></script> 
		<script src="js/core.functions.js"></script>
		<script>
			function idleTimer() {
				var t;
				window.onload = resetTimer;
				window.onmousemove = resetTimer;
				window.onmousedown = resetTimer;  // catches touchscreen presses as well      
				window.ontouchstart = resetTimer; // catches touchscreen swipes as well 
				window.onclick = resetTimer;      // catches touchpad clicks as well
				window.onkeypress = resetTimer;   
				window.addEventListener('scroll', resetTimer, true);
				
				get("get.php?type=admstaff", getAdmStaff);
				get("get.php?type=admvisitor", getAdmVisitors);

				function isIdle() {
					post('checkin.php', {});
				}

				function resetTimer() {
					clearTimeout(t);
				<?php
					global $siteadmin_timeout;
					$timeout = $siteadmin_timeout * 1000;
					echo "t = setTimeout(isIdle, $timeout);";
				?>
				}
			};
			idleTimer();
		</script>
	</head>
<body>
<ons-page id="adminpage" class="adminpage">   
	<ons-splitter>
		<ons-splitter-side id="menu" side="left" width="220px" collapse swipeable>
			<ons-page>
			<ons-list>
				<ons-list-header>Site Administration</ons-list-header>
				<ons-list-item onclick="fn.load('admstaff.html')" tappable>
					Manage Staff
				</ons-list-item>
				<ons-list-item onclick="fn.load('admvisitor.html')" tappable>
					Manage Visitors
				</ons-list-item>
				<ons-list-item onclick="exitAdmin()" tappable>
					Leave Admin
				</ons-list-item>
			</ons-list>
			</ons-page>
			</ons-splitter-side>
		<ons-splitter-content id="content" page="admstaff.html"></ons-splitter-content>
	</ons-splitter>

	<template id="admstaff.html">
		<!-- Dynamically populated -->
	</template>
	<template id="admvisitor.html">
		<!-- Dynamically Populated -->
	</template>
	<template id="alert-dialog.html">
		<ons-alert-dialog id="adm-alert-dialog" modifier="rowfooter">
			<div class="alert-dialog-title">Confirm</div>
			<div class="alert-dialog-content">Are you sure you would like to remove this user?</div>
			<div class="alert-dialog-footer">
				<ons-alert-dialog-button onclick="admConfirmDelete()">Yes</ons-alert-dialog-button>
				<ons-alert-dialog-button onclick="admConfirmClose()">No</ons-alert-dialog-button>
			</div>
		</ons-alert-dialog>
	</template>
	<ons-dialog id="addvisitor">
	<div style="text-align: center; padding: 10px;">
		<h3>Visitor details:</h3>
		<ons-input id="va-Title" modifier="underbar" placeholder="Title" float></ons-input> <br />
		<ons-input required id="va-Firstname" modifier="underbar" placeholder="First Name" float></ons-input> <br />
		<ons-input required id="va-Lastname" modifier="underbar" placeholder="Last Name" float></ons-input> <br />
		<ons-input id="va-Company" modifier="underbar" placeholder="Company" float></ons-input> <br />
		<ons-input id="va-Reg" modifier="underbar" placeholder="Vehicle Registration" float></ons-input> <br /><br />
		<?php
			$SITEID = $_SESSION['adminsiteid'];
			echo "<ons-button onclick=\"addAdmVisitor($SITEID)\">Add</ons-button>";
		?>
		<ons-button onclick="closeAddVisitor()">Cancel</ons-button>
	  </p>
	</div>
	</ons-dialog>
	<ons-dialog id="addstaff">
	<div style="text-align: center; padding: 10px;">
		<h3>Staff details:</h3>
		<ons-input id="sa-Title" modifier="underbar" placeholder="Title" float></ons-input> <br />
		<ons-input required id="sa-Firstname" modifier="underbar" placeholder="First Name" float></ons-input> <br />
		<ons-input required id="sa-Lastname" modifier="underbar" placeholder="Last Name" float></ons-input> <br />
		<ons-input id="sa-Company" modifier="underbar" placeholder="Company" float></ons-input> <br />
		<ons-input id="sa-Reg" modifier="underbar" placeholder="Vehicle Registration" float></ons-input> <br /><br />
		<?php
			$SITEID = $_SESSION['adminsiteid'];
			echo "<ons-button onclick=\"addStaff($SITEID)\">Add</ons-button>";
		?>
		<ons-button onclick="closeAddStaff()">Cancel</ons-button>
	  </p>
	</div>
	</ons-dialog>
</ons-page>
</body>
</html>