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
		$result = $conn->query("SELECT accounts.id, accounts.username, accounts.disabled, accounts.site, session.token, session.accountid, session.expire FROM session JOIN accounts ON accounts.id = session.accountid WHERE accounts.username = '$TOKEN[0]' AND session.token = '$TOKEN[1]' AND accounts.disabled = '0'")->fetch();
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
		$result = $conn->query("SELECT id, firstname, lastname, jobtitle, signedin, thumbnail FROM staff WHERE siteid = $SITEID ORDER BY signedin DESC, firstname ASC")->fetchall();
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
		$result = $conn->query("SELECT id, firstname, lastname, title, company, vehiclereg, signedin FROM visitors WHERE siteid = $SITEID ORDER BY signedin DESC, firstname ASC")->fetchall();
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

function addVisitor($SITEID, $TITLE, $FN, $LN, $CM, $RG) {
	global $db_name;
	global $db_username;
	global $db_password;
	global $db_host;
	global $db_port;
	if((strlen($FN) != 0) && (strlen($LN) != 0)) {
		try {
			$conn = new PDO("mysql:host=$db_host;dbname=$db_name", $db_username, $db_password);
			$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$result = $conn->query("SELECT * FROM sites WHERE id = $SITEID")->fetch();
			if($result != null) {
				$query = "SELECT * FROM visitors WHERE siteid = $SITEID ";
				if(strlen($TITLE) > 0) $query .= "AND title = X'$TITLE' ";
				if(strlen($FN) > 0) $query .= "AND firstname = X'$FN' ";
				if(strlen($LN) > 0) $query .= "AND lastname = X'$LN' ";
				if(strlen($CM) > 0) $query .= "AND company = X'$CM' ";
				$result = $conn->query($query)->fetch();
				if($result == null) {
					$query = "INSERT INTO visitors (siteid";
					if(strlen($TITLE) > 0) $query .= ", title";
					if(strlen($FN) > 0) $query .= ", firstname";
					if(strlen($LN) > 0) $query .= ", lastname";
					if(strlen($CM) > 0) $query .= ", company";
					if(strlen($RG) > 0) $query .= ", vehiclereg";
					$query .= ") VALUES ($SITEID";
					if(strlen($TITLE) > 0) $query .= ", X'$TITLE'";
					if(strlen($FN) > 0) $query .= ", X'$FN'";
					if(strlen($LN) > 0) $query .= ", X'$LN'";
					if(strlen($CM) > 0) $query .= ", X'$CM'";
					if(strlen($RG) > 0) $query .= ", X'$RG'";
					$query .= ")";
					$conn->query($query);
				}
			}
			$conn = null;
		}
		catch(PDOException $e) {}
	}
}
function admDeleteVisitor($ID, $SITEID) {
	global $db_name;
	global $db_username;
	global $db_password;
	global $db_host;
	global $db_port;
	try {
		$conn = new PDO("mysql:host=$db_host;dbname=$db_name", $db_username, $db_password);
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$result = $conn->query("SELECT * FROM sites WHERE id = $SITEID")->fetch();
		if($result != null) {
			$result = $conn->query("DELETE FROM visitors WHERE siteid = $SITEID AND id = $ID");
		}
		$conn = null;
	}
	catch(PDOException $e) {}
}
function admDeleteStaff($ID, $SITEID) {
	global $db_name;
	global $db_username;
	global $db_password;
	global $db_host;
	global $db_port;
	try {
		$conn = new PDO("mysql:host=$db_host;dbname=$db_name", $db_username, $db_password);
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$result = $conn->query("SELECT * FROM sites WHERE id = $SITEID")->fetch();
		if($result != null) {
			$result = $conn->query("DELETE FROM staff WHERE siteid = $SITEID AND id = $ID");
		}
		$conn = null;
	}
	catch(PDOException $e) {}
}
?>