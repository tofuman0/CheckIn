<?php
include_once('config.php');
function getToken($username, $hwid) {
	return encodeToken($username. ",". $hwid);
}

function resolveToken($token) {
	$values = explode(",", hex2bin(decodeToken($token)));
	if(count($values) != 2)
	{
		return null;
	}
	return array($values[0], $values[1]);
}

function encodeToken($token) {
	global $_key;
	global $_serverLookup;
	$returntoken[] = array();
	for($i = 0; $i < strlen($token); $i++) {
		$tokenchar = ord($token[$i]);
		$tokenchar = $_serverLookup[$tokenchar];
		if($i % 2) {
			$leftNibble = $tokenchar;
			$rightNibble = $tokenchar;
			$leftNibble <<= 4;
			$leftNibble &= 0xF0;
			$rightNibble >>= 4;
			$rightNibble &= 0x0F;
			$tokenchar = ($leftNibble | $rightNibble) & 0xFF;
		}
		else {
			$tokenchar = ~$tokenchar & 0xFF;
		}
		$tokenchar ^= $_key[$i % sizeof($_key)];
		$returntoken[$i] = $tokenchar;
	}
	$chars = array_map("chr", $returntoken);
	$bin = join($chars);
	return bin2hex($bin);
}

function decodeToken($token) {
	global $_key;
	global $_clientLookup;
	$token = hex2bin($token);
	$returntoken[] = array();
	for($i = 0; $i < strlen($token); $i++) {
		$tokenchar = ord($token[$i]);
		$tokenchar ^= $_key[$i % sizeof($_key)];
		if($i % 2) {
			$leftNibble = $tokenchar;
			$rightNibble = $tokenchar;
			$leftNibble <<= 4;
			$leftNibble &= 0xF0;
			$rightNibble >>= 4;
			$rightNibble &= 0x0F;
			$tokenchar = ($leftNibble | $rightNibble) & 0xFF;
		}
		else {
			$tokenchar = ~$tokenchar & 0xFF;
		}
		$tokenchar &= 0xFF;
		$tokenchar = $_clientLookup[$tokenchar];
		$returntoken[$i] = $tokenchar;
	}
	$chars = array_map("chr", $returntoken);
	$bin = join($chars);
	return bin2hex($bin);
}

function getHWID() {
	$first = random_bytes(4);
	$second = random_bytes(8);
	return bin2hex($first) . "-" . bin2hex($second);
}

function clearExpiredSessions() {
	global $db_name;
	global $db_username;
	global $db_password;
	global $db_host;
	global $db_port;
	try {
		$conn = new PDO("mysql:host=$db_host;dbname=$db_name", $db_username, $db_password);
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$conn->query("DELETE FROM session WHERE expire < DATE(NOW())");
		$conn = null;
	}
	catch(PDOException $e) {
	}
}

function checkToken() {
	global $db_name;
	global $db_username;
	global $db_password;
	global $db_host;
	global $db_port;
	if(!isset($_COOKIE['loginToken'])) return null;
	$TOKEN = resolveToken($_COOKIE['loginToken']);
	if($TOKEN == null)
	{
		return null;
	}
	try {
		$conn = new PDO("mysql:host=$db_host;dbname=$db_name", $db_username, $db_password);
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$result = $conn->query("SELECT accounts.id, accounts.username, accounts.disabled, session.token, session.accountid, session.expire FROM session JOIN accounts ON accounts.id = session.accountid WHERE accounts.username = '$TOKEN[0]' AND session.token = '$TOKEN[1]' AND accounts.disabled = '0'")->fetch();
		$conn = null;
		return $result;
	}
	catch(PDOException $e) {
		return null;
	}
}

function getSiteDetails() {
	global $db_name;
	global $db_username;
	global $db_password;
	global $db_host;
	global $db_port;
	if(!isset($_COOKIE['loginToken'])) return null;
	$TOKEN = resolveToken($_COOKIE['loginToken']);
	if($TOKEN == null)
	{
		return null;
	}
	try {
		$USERNAME = bin2hex($TOKEN[0]);
		$conn = new PDO("mysql:host=$db_host;dbname=$db_name", $db_username, $db_password);
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$result = $conn->query("SELECT accounts.site, sites.companyname, sites.sitename, sites.id, sites.siteimage, sites.siteimagetype, sites.sitemessage FROM accounts JOIN sites ON accounts.site = sites.id WHERE accounts.username = X'$USERNAME' LIMIT 1")->fetch();
		$conn = null;
		return $result;
	}
	catch(PDOException $e) {
		return null;
	}
}

function getConfiguration() {
	global $db_name;
	global $db_username;
	global $db_password;
	global $db_host;
	global $db_port;
	try {
		$conn = new PDO("mysql:host=$db_host;dbname=$db_name", $db_username, $db_password);
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$result = $conn->query("SELECT * FROM configuration LIMIT 1")->fetch();
		$conn = null;
		return $result;
	}
	catch(PDOException $e) {
		return null;
	}
}

function getStaff($SITEID) {
	global $db_name;
	global $db_username;
	global $db_password;
	global $db_host;
	global $db_port;
	try {
		$conn = new PDO("mysql:host=$db_host;dbname=$db_name", $db_username, $db_password);
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$result = $conn->query("SELECT id, firstname, lastname, jobtitle, signedin, thumbnail FROM staff WHERE siteid = $SITEID")->fetchall();
		$conn = null;
		return $result;
	}
	catch(PDOException $e) {
		return null;
	}
}

function getVisitor($SITEID) {
	global $db_name;
	global $db_username;
	global $db_password;
	global $db_host;
	global $db_port;
	try {
		$conn = new PDO("mysql:host=$db_host;dbname=$db_name", $db_username, $db_password);
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$result = $conn->query("SELECT id, firstname, lastname, title, company, vehiclereg, signedin FROM visitors WHERE siteid = $SITEID")->fetchall();
		$conn = null;
		return $result;
	}
	catch(PDOException $e) {
		return null;
	}
}

function setState($TYPE, $STATE, $ID) {
	global $db_name;
	global $db_username;
	global $db_password;
	global $db_host;
	global $db_port;
	try {
		$conn = new PDO("mysql:host=$db_host;dbname=$db_name", $db_username, $db_password);
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$conn->query("UPDATE $TYPE SET signedin = $STATE WHERE id = $ID");
		$conn = null;
	}
	catch(PDOException $e) {}
}
?>