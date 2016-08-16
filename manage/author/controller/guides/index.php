<?php

include_once(dirname(__FILE__) . "/../../../../modules/database/database.php");
include_once(dirname(__FILE__) . "/../../../../modules/utils.php");

$pending = array_key_exists("pending", $_GET) ? ( isset($_GET['pending']) ? boolval($_GET['pending']) : false ) : "";

try {
	$db = Database::getInstance();
	$guides = $db->get_guides($pending);
	$db->disconnect();
	
	$result = array(
		"total" => sizeof($guides),
		"guides" => $guides
	);
	
	SEND_OK($result);
} catch (Exception $e) {
	INTERNAL_SERVER_ERROR($e->getMessage());
}
?>