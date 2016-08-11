<?php

include_once(dirname(__FILE__) . "/../../modules/database/database.php");
include_once(dirname(__FILE__) . "/../../modules/crawl/fetchdata.php");
include_once(dirname(__FILE__) . "/../../modules/utils.php");

$character = array_key_exists("character", $_GET) ? mb_strtolower($_GET['character'], 'UTF-8') : "";
$server = array_key_exists("server", $_GET) ? mb_strtolower($_GET['server'], 'UTF-8') : "";
$region = array_key_exists("region", $_GET) ? mb_strtolower($_GET['region'], 'UTF-8') : "";
$raid = array_key_exists("raid", $_GET) ? (empty($_GET['raid']) ? null : intval($_GET['raid'])) : null;
$metric = array_key_exists("metric", $_GET) ? mb_strtolower($_GET['metric'], 'UTF-8') : "";
$encounter = array_key_exists("encounter", $_GET) ? (empty($_GET['encounter']) ? null : intval($_GET['encounter'])) : null;

if( empty($character) || empty($server) || empty($region) || $raid === null || $encounter === null || !Metric::is_metric($metric) ) {
	Error::INTERNAL_SERVER_ERROR("something is off...");
}

try {

	$db = Database::getInstance();
	
	if(!$db->is_member($character, $server, $region)) {
		Error::INTERNAL_SERVER_ERROR("Character not found!");
	}
	
	$boss = $db->get_encounter($encounter);
	$boss = isset($boss) && is_array($boss) && array_key_exists("name", $boss) ? $boss['name'] : "";

	switch($metric) {
	case Metric::$DPS:
		$rankings = $db->get_dps_ranking($character, $server, $region, $raid, $encounter, null);
		break;
	case Metric::$HPS:
		$rankings = $db->get_hps_ranking($character, $server, $region, $raid, $encounter, null);
		break;
	case Metric::$KRSI:
		break;
	default:
		Error::INTERNAL_SERVER_ERROR("something is off...");
	}

} catch (Exception $e) {
	Error::INTERNAL_SERVER_ERROR($e->getMessage());
}

$result = array(
	"metric" => $metric,
	"character" => $character,
	"server" => $server,
	"region" => $region,
	"total" => sizeof($rankings),
	"boss" => $boss,
	"rankings" => $rankings
);

Utils::SEND_JSON($result);

?>