<?php

include_once(dirname(__FILE__) . "/../../../../modules/database/database.php");
include_once(dirname(__FILE__) . "/../../../../modules/utils.php");

$tag = array_key_exists("tag", $_GET) ? $_GET['tag'] : "";

if( empty($tag) ) {
	Error::INTERNAL_SERVER_ERROR("something is off...");
}

try {
	$db = Database::getInstance();

	if( $db->get_guide($tag) ) {
		$path = "../../node/temp/$tag.html";
		$copyTo = "../../../../node/$tag.html";
		
		if( !file_exists($path) )
			Error::INTERNAL_SERVER_ERROR();
		
		$result = copy($path, $copyTo);
		
		if( $result ) {
			$result = $db->publish_guide($tag);
			Utils::SEND_OK();
		}
		else {
			Error::INTERNAL_SERVER_ERROR();
		}
	}
} catch (Exception $e) {
	Error::INTERNAL_SERVER_ERROR($e->getMessage());
}
?>