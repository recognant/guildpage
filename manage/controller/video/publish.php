<?php

include_once(dirname(__FILE__) . "/../../../modules/database/database.php");
include_once(dirname(__FILE__) . "/../../../modules/utils.php");

$tag = array_key_exists("tag", $_GET) ? $_GET['tag'] : "";

if( empty($tag) ) {
	INTERNAL_SERVER_ERROR("something is off...");
}

try {
	$db = Database::getInstance();
	$result = $db->publish_video($tag);
	$db->disconnect();
	
	if( $result ) {
		SEND_OK();
	}
	else {
		INTERNAL_SERVER_ERROR();
	}

} catch (Exception $e) {
	INTERNAL_SERVER_ERROR($e->getMessage());
}
?>