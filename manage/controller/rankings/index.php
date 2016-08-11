<?php

include_once(dirname(__FILE__) . "/../../../modules/database/database.php");
include_once(dirname(__FILE__) . "/../../../modules/utils.php");

try {
	$db = Database::getInstance();
	$rankings = $db->get_rankings();
	
	$result = array(
		"total" => count($rankings),
		"rankings" => $rankings
	);

	Utils::SEND_JSON($result);

} catch (Exception $e) {
	Error::INTERNAL_SERVER_ERROR($e->getMessage());
}

?>