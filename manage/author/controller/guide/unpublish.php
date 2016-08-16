<?php

include_once(dirname(__FILE__) . "/../../../../modules/database/database.php");
include_once(dirname(__FILE__) . "/../../../../modules/utils.php");

$tag = array_key_exists("tag", $_GET) ? $_GET['tag'] : "";

if( empty($tag) ) {
	INTERNAL_SERVER_ERROR("something is off...");
}

try {
	$db = Database::getInstance();

	if( $db->get_guide($tag) ) {
		$path = "../../../../node/$tag.html";
		
		if( !file_exists($path) )
			INTERNAL_SERVER_ERROR();

		if( @unlink($path) ) {
			$db->unpublish_guide($tag);
		}
		
		SEND_OK();
	}
	$db->disconnect();
} catch (Exception $e) {
	INTERNAL_SERVER_ERROR($e->getMessage());
}
?>