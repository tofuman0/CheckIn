<?php
require_once('config.php');
require_once('functions.php');
$account = checkToken();
if($account == null)
{
	die();
}
if(isset($_POST['type']) && isset($_POST['state']) && isset($_POST['id'])) {
	$TYPE = $_POST['type'] == "visitor" ? "visitors" : "staff";
	$STATE = $_POST['state'] == "true" ? "1" : "0";
	$ID = intval($_POST['id']);
	setState($TYPE, $STATE, $ID);
}
else if(isset($_POST['type']) && isset($_POST['title']) && isset($_POST['fn']) && isset($_POST['ln']) && isset($_POST['cm']) && isset($_POST['rg']) && isset($_POST['sid'])) {
	if($_POST['type'] == "addvisitor") {
		$SITEID = intval($_POST['sid']);
		if($SITEID == intval($account['site']))
		{
			$TITLE = bin2hex($_POST['title'] == NULL ? "" : $_POST['title']);
			$FN = bin2hex($_POST['fn'] == NULL ? "" : $_POST['fn']);
			$LN = bin2hex($_POST['ln'] == NULL ? "" : $_POST['ln']);
			$CM = bin2hex($_POST['cm'] == NULL ? "" : $_POST['cm']);
			$RG = bin2hex($_POST['rg'] == NULL ? "" : $_POST['rg']);
			addVisitor($SITEID, $TITLE, $FN, $LN, $CM, $RG);
		}
	}
}
else if(isset($_POST['type']) && $_POST['type'] == "admdeletevisitor") {
	session_start();
	if(!isset($_SESSION['adminaccess']) || $_SESSION['adminaccess'] != 1 || $_SESSION['admintimeout'] < time())
	{
		$_SESSION = array();
		session_unset();
		session_destroy();
		session_write_close();
		die();
	}
	global $siteadmin_timeout;
	$_SESSION['admintimeout'] = time() + $siteadmin_timeout;
	session_write_close();
	$VISITORID = intval($_POST['id']);
	$SITEID = intval($_SESSION['adminsiteid']);
	admDeleteVisitor($VISITORID, $SITEID);
}
else if(isset($_POST['type']) && $_POST['type'] == "admdeletestaff") {
	session_start();
	if(!isset($_SESSION['adminaccess']) || $_SESSION['adminaccess'] != 1 || $_SESSION['admintimeout'] < time())
	{
		$_SESSION = array();
		session_unset();
		session_destroy();
		session_write_close();
		die();
	}
	global $siteadmin_timeout;
	$_SESSION['admintimeout'] = time() + $siteadmin_timeout;
	session_write_close();
	$STAFFID = intval($_POST['id']);
	$SITEID = intval($_SESSION['adminsiteid']);
	admDeleteStaff($STAFFID, $SITEID);
}
?>