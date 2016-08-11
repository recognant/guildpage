<?php

include_once(dirname(__FILE__) . "/../../../modules/database/database.php");
include_once(dirname(__FILE__) . "/../../../modules/utils.php");

$tag = array_key_exists("tag", $_GET) ? $_GET['tag'] : "";

if( empty($tag) ) {
	Error::INTERNAL_SERVER_ERROR("something is off...");
}

try {
	$db = Database::getInstance();
	$result = $db->unpublish_video($tag);
	
	if( $result ) {
		Utils::SEND_OK();
	}
	else {
		Error::INTERNAL_SERVER_ERROR();
	}
} catch (Exception $e) {
	Error::INTERNAL_SERVER_ERROR($e->getMessage());
}
?>