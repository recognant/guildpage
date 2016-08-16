<?php

include_once(dirname(__FILE__) . "/../../modules/database/database.php");
include_once(dirname(__FILE__) . "/../../modules/crawl/fetchdata.php");
include_once(dirname(__FILE__) . "/../../modules/utils.php");

$raid = array_key_exists("raid", $_GET) ? (empty($_GET['raid']) ? null : intval($_GET['raid'])) : null;
$metric = array_key_exists("metric", $_GET) ? (empty($_GET['metric']) ? "" : $_GET['metric']) : "";
$difficulty = array_key_exists("difficulty", $_GET) ? (empty($_GET['difficulty']) ? null : $_GET['difficulty']) : null;

if( !Metric::is_metric($metric) ) {
	INTERNAL_SERVER_ERROR("something is off...");
}

$rankings = array();

try {
	$db = Database::getInstance();

	switch($metric) {
	case Metric::$DPS:
		$rankings = $db->get_guild_dps_performance($raid, $difficulty);
		break;
	case Metric::$HPS:
		$rankings = $db->get_guild_hps_performance($raid, $difficulty);
		break;
	case Metric::$KRSI:
		break;
	default:
		ServerError::INTERNAL_SERVER_ERROR("something is off...");
	}
	$db->disconnect();

} catch (Exception $e) {
	INTERNAL_SERVER_ERROR($e->getMessage());
}


$result = array(
	"metric" => $metric,
	"total" => sizeof($rankings),
	"rankings" => $rankings
);

SEND_OK($result);

?>