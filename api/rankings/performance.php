<?php

include_once(dirname(__FILE__) . "/../../modules/database/database.php");
include_once(dirname(__FILE__) . "/../../modules/crawl/fetchdata.php");
include_once(dirname(__FILE__) . "/../../modules/utils.php");

$character = array_key_exists("character", $_GET) ? mb_strtolower($_GET['character'], 'UTF-8') : "";
$server = array_key_exists("server", $_GET) ? mb_strtolower($_GET['server'], 'UTF-8') : "";
$region = array_key_exists("region", $_GET) ? mb_strtolower($_GET['region'], 'UTF-8') : "";
$raid = array_key_exists("raid", $_GET) ? (empty($_GET['raid']) ? null : intval($_GET['raid'])) : null;
$metric = array_key_exists("metric", $_GET) ? mb_strtolower($_GET['metric'], 'UTF-8') : "";

if( empty($character) || empty($server) || empty($region) || ( !empty($metric) && !Metric::is_metric($metric) ) ) {
	INTERNAL_SERVER_ERROR("something is off...");
}

try {
	$db = Database::getInstance();
	
	if(!$db->is_member($character, $server, $region)) {
		INTERNAL_SERVER_ERROR("Character not found!");
	}
	
	switch($metric) {
	case Metric::$DPS:
		$rankings = $db->get_dps_performance($character, $server, $region, $raid, null);
		break;
	case Metric::$HPS:
		$rankings = $db->get_hps_performance($character, $server, $region, $raid, null);
		break;
	case Metric::$KRSI:
		break;
	case "":
		$rankings = $db->get_dps_performance($character, $server, $region, $raid, null);
		$rankings = array_merge($rankings, $db->get_hps_performance($character, $server, $region, $raid, null));
		break;
	default:
		INTERNAL_SERVER_ERROR("something is off...");
	}

} catch (Exception $e) {
	INTERNAL_SERVER_ERROR($e->getMessage());
}

$result = array(
	"metric" => $metric,
	"character" => $character,
	"server" => $server,
	"region" => $region,
	"total" => sizeof($rankings),
	"rankings" => $rankings
);

SEND_OK($result);

?>