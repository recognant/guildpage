<?php

ignore_user_abort(true);
set_time_limit(0);

include_once(dirname(__FILE__) . "/../../../modules/database/database.php");
include_once(dirname(__FILE__) . "/../../../modules/crawl/fetchdata.php");
include_once(dirname(__FILE__) . "/../../../modules/utils.php");

$character = array_key_exists("character", $_GET) ? mb_strtolower($_GET['character'], 'UTF-8') : "";
$server = array_key_exists("server", $_GET) ? mb_strtolower($_GET['server'], 'UTF-8') : "";
$region = array_key_exists("region", $_GET) ? mb_strtolower($_GET['region'], 'UTF-8') : "";
$metric = array_key_exists("metric", $_GET) ? (empty($_GET['metric']) ? "" : mb_strtolower($_GET['metric'], 'UTF-8')) : "";
$raid = array_key_exists("raid", $_GET) ? intval($_GET['raid']) : "";

if( empty($character) || empty($server) || empty($region) || empty($raid) ) {
	INTERNAL_SERVER_ERROR();
}

try {
	$db = Database::getInstance();
	
	if(!$db->is_member($character, $server, $region)) {
		INTERNAL_SERVER_ERROR("Character not found!");
	}

	$total = 0;
	try {
		switch($metric) {
		case Metric::$DPS:
			$data = fetch_character($character, $server, $region, $raid, Metric::$DPS);
			if( $data !== false ) {
				$total += $db->insert_dps_rankings($character, $server, $region, $data);
			}
			break;
		case Metric::$HPS:
			$data = fetch_character($character, $server, $region, $raid, Metric::$HPS);
			if( $data !== false ) {
				$total += $db->insert_hps_rankings($character, $server, $region, $data);
			}
			break;
		case Metric::$KRSI:
			break;
		case "":
			$data = fetch_character($character, $server, $region, $raid, Metric::$DPS);
			if( $data !== false ) {
				$total += $db->insert_dps_rankings($character, $server, $region, $data);
			}
			$data = fetch_character($character, $server, $region, $raid, Metric::$HPS);
			if( $data !== false ) {
				$total += $db->insert_hps_rankings($character, $server, $region, $data);
			}
		default:
		}
		
	} catch (Exception $e) {
		INTERNAL_SERVER_ERROR("You suck: " . $e->getMessage());
	}
	
	$db->disconnect();
	
	$result = array(
		"total" => $total
	);
	
	SEND_OK($result);

} catch (Exception $e) {
	INTERNAL_SERVER_ERROR("Even harder: " . $e->getMessage());
}

?>