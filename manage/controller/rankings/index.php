<?php

include_once(dirname(__FILE__) . "/../../../modules/database/database.php");
include_once(dirname(__FILE__) . "/../../../modules/utils.php");

try {
	$db = Database::getInstance();
	$rankings = $db->get_rankings();
	$db->disconnect();
	
	$result = array(
		"total" => count($rankings),
		"rankings" => $rankings
	);

	SEND_OK($result);

} catch (Exception $e) {
	INTERNAL_SERVER_ERROR($e->getMessage());
}

?>