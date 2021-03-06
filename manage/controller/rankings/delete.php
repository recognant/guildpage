<?php

include_once(dirname(__FILE__) . "/../../../modules/database/database.php");
include_once(dirname(__FILE__) . "/../../../modules/utils.php");

$character = array_key_exists("character", $_GET) ? mb_strtolower($_GET['character'], 'UTF-8') : "";
$server = array_key_exists("server", $_GET) ? mb_strtolower($_GET['server'], 'UTF-8') : "";
$region = array_key_exists("region", $_GET) ? mb_strtolower($_GET['region'], 'UTF-8') : "";

if( empty($character) || empty($server) || empty($region) ) {
	INTERNAL_SERVER_ERROR("something is off...");
}

try {
	$db = Database::getInstance();
	$db->delete_rankings($character, $server, $region);
	$db->disconnect();
	
	SEND_OK();

} catch (Exception $e) {
	INTERNAL_SERVER_ERROR($e->getMessage());
}

?>