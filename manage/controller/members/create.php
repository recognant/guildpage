<?php

include_once(dirname(__FILE__) . "/../../../modules/database/database.php");
include_once(dirname(__FILE__) . "/../../../modules/utils.php");

$character = array_key_exists("character", $_GET) ? mb_strtolower($_GET['character'], 'UTF-8') : "";
$server = array_key_exists("server", $_GET) ? mb_strtolower($_GET['server'], 'UTF-8') : "";
$region = array_key_exists("region", $_GET) ? mb_strtolower($_GET['region'], 'UTF-8') : "";
$class = array_key_exists("class", $_GET) ? intval($_GET['class']) : 0;

if( empty($character) || empty($server) || empty($region) || $class === 0 ) {
	INTERNAL_SERVER_ERROR("Missing or wrong parameters given!");
}

try {
	$db = Database::getInstance();
	$db->insert_member($character, $server, $region, $class);
	$db->disconnect();
} catch (Exception $e) {
	INTERNAL_SERVER_ERROR($e->getMessage());
}

SEND_OK();

?>