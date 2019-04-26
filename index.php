<?php
require_once('config.php');
require_once('functions.php');

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