<?php

include_once(dirname(__FILE__) . "/../../../../modules/database/database.php");
include_once(dirname(__FILE__) . "/../../../../modules/utils.php");

$tag = array_key_exists("tag", $_GET) ? $_GET['tag'] : "";

if( empty($tag) ) {
	INTERNAL_SERVER_ERROR("something is off...");
}

try {
	$db = Database::getInstance();
	$guide = $db->get_guide($tag);
	$db->disconnect();
	
	SEND_OK($guide);
} catch (Exception $e) {
	INTERNAL_SERVER_ERROR($e->getMessage());
}
?>