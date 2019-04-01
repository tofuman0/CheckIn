<?php
require_once('config.php');
require_once('functions.php');
#echo encodeToken("admin,23f2-3j4309g34g09hweg09gh3g480h") . "<br>";
#echo decodeToken("830afab39327091ba51b7b8a07e829f190497490025b2e5f8869632408b048") . "<br>";
#$USERNAME = "admin";
#$ID = getHWID();
#echo getToken($USERNAME, $ID);

if(!isset($_COOKIE['loginToken'])) {
	include 'signin.php';
} else {
	clearExpiredSessions();
	$result = checkToken();
	if($result != null)
	{
		include 'checkin.php';
	}
	else
	{
	login:
		setcookie('loginToken', '', time()-3600);
		include 'signin.php';
	}
}
?>