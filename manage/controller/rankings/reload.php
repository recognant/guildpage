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
	Error::INTERNAL_SERVER_ERROR();
}

try {
	$db = Database::getInstance();
	
	if(!$db->is_member($character, $server, $region)) {
		Error::INTERNAL_SERVER_ERROR("Character not found!");
	}
	
	
	$info = $db->get_characterinfo($character, $server, $region);

	global $api_key;
	$brackets = array();
	$brackets = $db->get_brackets($raid);
	
	// $rankings = array();

	//foreach($brackets as $bracket) {
		$bracket = array( "id" => 0 );
		try {
			switch($metric) {
			case Metric::$DPS:
				$data = fetch_character($character, $server, $region, $raid, Metric::$DPS, $bracket['id'], 5000, $api_key);
				break;
			case Metric::$HPS:
				$data = fetch_character($character, $server, $region, $raid, Metric::$HPS, $bracket['id'], 5000, $api_key);
				break;
			case Metric::$KRSI:
				$data = false;
				break;
			case "":
				$data = fetch_character($character, $server, $region, $raid, Metric::$DPS, $bracket['id'], 5000, $api_key);
				if( $data !== false ) {
					// $rankings = array_merge($rankings, $data);
					$db->init_ranking($character, $server, $region, $data, Metric::$DPS);
				}
				$data = fetch_character($character, $server, $region, $raid, Metric::$HPS, $bracket['id'], 5000, $api_key);
				if( $data !== false ) {
					// $rankings = array_merge($rankings, $data);
					$db->init_ranking($character, $server, $region, $data, Metric::$HPS);
				}
			default:
				$data = false;
			}
			
			if( $data !== false ) {
				$db->init_ranking($character, $server, $region, $data, $metric);
			}
		} catch (Exception $e) {
			Error::INTERNAL_SERVER_ERROR("You suck: " . $e->getMessage());
			//Utils::SEND_ERROR($e->getMessage());
		}
	//}
	
	// var_dump($rankings);
	
	Utils::SEND_OK();

} catch (Exception $e) {
	Error::INTERNAL_SERVER_ERROR("Even harder: " . $e->getMessage());
	//Utils::SEND_ERROR($e->getMessage());
}

?>