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
	
	if( $guide ) {
		//@unlink("../../node/temp/$tag.gfl");
		@unlink("../../node/temp/$tag.xml");
		@unlink("../../node/temp/$tag.html");
		
		if( $guide['pending'] == false ) {
			@unlink("../../../../node/$tag.html");
		}
		$result = $db->delete_guide($tag);
	}
	$db->disconnect();

	if( $result ) {
		SEND_OK();
	}
	else {
		SEND_ERROR();
	}
} catch (Exception $e) {
	INTERNAL_SERVER_ERROR($e->getMessage());
}
?>