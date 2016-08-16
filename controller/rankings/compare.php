<?php

set_time_limit(180);

include_once(dirname(__FILE__) . "/../../modules/database/database.php");
include_once(dirname(__FILE__) . "/../../modules/crawl/fetchdata.php");
include_once(dirname(__FILE__) . "/../../modules/utils.php");

INTERNAL_SERVER_ERROR();

$character = array_key_exists("character", $_GET) ? $_GET['character'] : "";
$server = array_key_exists("server", $_GET) ? $_GET['server'] : "";
$region = array_key_exists("region", $_GET) ? $_GET['region'] : "";
$raid = array_key_exists("raid", $_GET) ? (empty($_GET['raid']) ? null : intval($_GET['raid'])) : null;
$type = array_key_exists("type", $_GET) ? (empty($_GET['type']) ? "" : $_GET['type']) : "";
$encounter = array_key_exists("encounter", $_GET) ? (empty($_GET['encounter']) ? null : intval($_GET['encounter'])) : null;

if( empty($character) || empty($server) || empty($region) || $raid === null || $encounter === null || !in_array($type, array("dps", "hps")) ) {
	INTERNAL_SERVER_ERROR();
}

global $api_key;
try {
		$db = Database::getInstance();
		
		$difficulty = 4; $class = 5; $spec = 3; $total = 42000.0; $duration = 15000; $itemLevel=690; $size=15;
		$data = fetch_encounter_rankings($encounter, "bossdps", $difficulty, 0, 1, $class, $spec, 0, 5000, $api_key);
		$result = $db->temp_rankings($data, $total, $duration-100000, $duration+100000, $itemLevel-2, $itemLevel+2, $size-2, $size+2);
		var_dump($result);
		
} catch (Exception $e) {
	// something bad happened
	INTERNAL_SERVER_ERROR($e->getMessage());
}

?>