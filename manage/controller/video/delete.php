<?php

include_once(dirname(__FILE__) . "/../../../modules/database/database.php");
include_once(dirname(__FILE__) . "/../../../modules/utils.php");

$tag = array_key_exists("tag", $_GET) ? $_GET['tag'] : "";

if( empty($tag) ) {
	INTERNAL_SERVER_ERROR("something is off...");
}

try {
	$db = Database::getInstance();
	$video = $db->get_video($tag);
	
	if( $video ) {
		$result = $db->delete_video($tag);
	}
	$db->disconnect();

	if( isset($result) && $result ) {
		SEND_OK();
	}
	else {
		SEND_ERROR();
	}
} catch (Exception $e) {
	INTERNAL_SERVER_ERROR($e->getMessage());
}
?>