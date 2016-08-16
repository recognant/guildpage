<?php

include_once(dirname(__FILE__) . "/rest.php");
include_once(dirname(__FILE__) . "/../utils.php");

try {
	$props = parse_ini_file("properties.ini");
	global $api_key;
	$api_key = $props['api_key'];
}
catch(Exception $e) {
	INTERNAL_SERVER_ERROR();
}

function fetch_zones() {
	global $api_key;
	$result = curl_get("https://www.warcraftlogs.com:443/v1/zones?api_key=" . $api_key);
	$result = decode_data($result);
	return $result;
}

function fetch_classes() {
	global $api_key;
	$result = curl_get("https://www.warcraftlogs.com:443/v1/classes?api_key=" . $api_key);
	$result = decode_data($result);
	return $result;
}

/* 1 = LFR, 2 = Flex, 3 = Normal, 4 = Heroic, 5 = Mythic, 10 = Challenge Mode */
function fetch_encounter($id, $metric, $difficulty, $size=0, $region=1, $class, $spec, $bracket_id=0, $limit=5000, $page=1) {
	global $api_key;
	
	$opts = array(
		"metric" => $metric,
		"difficulty" => $difficulty,
		"size" => $size,
		"region" => $region,
		"class" => $class,
		"spec" => $spec,
		"bracket" => $bracket_id,
		"limit" => $limit,
		"page" => $page,
		"api_key" => $api_key
	);
	
	$result = curl_get("https://www.warcraftlogs.com:443/v1/rankings/encounter/" . $id, $opts);
	$result = decode_data($result);
	return $result;
}

function fetch_encounter_rankings($id, $metric, $difficulty, $size=0, $region="eu", $class, $spec, $bracket_id=0, $limit=5000) {
	global $api_key;
	
	$parts = array();
	$parts[] = fetch_encounter($id, $metric, $difficulty, $size, $region, $class, $spec, $bracket_id, $limit, 1, $api_key);
	
	$count = intval($parts[0]['total']) - $limit;
	$page = 2;
	while($count > 0) {
		$parts[] = fetch_encounter($id, $metric, $difficulty, $size, $region, $class, $spec, $bracket_id, $limit, $page, $api_key);
		$count -= $limit;
		$page++;
	}
	
	$data = array();
	foreach($parts as $part) {
		$data = array_merge($data, $part['rankings']);
	}

	return $data;
}

/* has to be done for all brackets in a raid */
function fetch_character($name, $server, $region, $raid_id, $metric, $bracket_id=0, $limit=5000) {
	global $api_key;
	
	$opts = array(
		"zone" => $raid_id,
		"metric" => $metric,
		"bracket" => $bracket_id,
		"limit" => $limit,
		"api_key" => $api_key
	);
	
	$result = curl_get("https://www.warcraftlogs.com:443/v1/rankings/character/" . $name . "/" . $server . "/" . $region, $opts);
	$result = decode_data($result);
	return $result;
}

function decode_data($data) {
	$result = (array) json_decode($data);
	if( array_key_exists('status', $result) || array_key_exists('error', $result) ) {
		throw new Exception('' . $result['error'], $result['status']);
	}
	return $result;
}

function fetch_yt_metadata($ytid) {
	$result = curl_get("http://www.youtube.com/oembed?url=http://www.youtube.com/watch?v=" . $ytid . "&format=json");
	$result = decode_data($result);
	return $result;
}

?>