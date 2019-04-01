<?php
require_once('config.php');
require_once('functions.php');
if(checkToken() == null)
{
	die();
}
if(isset($_POST["type"]) && isset($_POST["state"]) && isset($_POST["id"])) {
	$TYPE = $_POST["type"] == "visitor" ? "visitors" : "staff";
	$STATE = $_POST["state"] == "true" ? "1" : "0";
	$ID = intval($_POST["id"]);
	setState($TYPE, $STATE, $ID);
}
?>