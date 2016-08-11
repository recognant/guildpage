<?php

include_once(dirname(__FILE__) . "/../../../modules/database/database.php");
include_once(dirname(__FILE__) . "/../../../modules/utils.php");

$tag = array_key_exists("tag", $_GET) ? $_GET['tag'] : "";

if( empty($tag) ) {
	Error::INTERNAL_SERVER_ERROR("something is off...");
}

try {
	$db = Database::getInstance();
	$video = $db->get_video($tag);
	
	if( $video ) {
		$result = $db->delete_video($tag);
	}

	if( isset($result) && $result ) {
		Utils::SEND_OK();
	}
	else {
		Utils::SEND_ERROR();
	}
} catch (Exception $e) {
	Error::INTERNAL_SERVER_ERROR($e->getMessage());
}
?>