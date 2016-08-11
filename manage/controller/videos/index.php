<?php

include_once(dirname(__FILE__) . "/../../../modules/database/database.php");
include_once(dirname(__FILE__) . "/../../../modules/utils.php");

$pending = array_key_exists("pending", $_GET) ? ( isset($_GET['pending']) ? boolval($_GET['pending']) : false ) : null;

try {
	$db = Database::getInstance();
	$videos = $db->get_videos($pending);

	$result = array(
		"total" => sizeof($videos),
		"videos" => $videos
	);
	
	Utils::SEND_JSON($result);
} catch (Exception $e) {
	Error::INTERNAL_SERVER_ERROR($e->getMessage());
}
?>