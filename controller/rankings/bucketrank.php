<?php

set_time_limit(180);

include_once(dirname(__FILE__) . "/../../modules/database/database.php");
include_once(dirname(__FILE__) . "/../../modules/crawl/fetchdata.php");
include_once(dirname(__FILE__) . "/../../modules/utils.php");

$character = array_key_exists("character", $_GET) ? $_GET['character'] : "";
$server = array_key_exists("server", $_GET) ? $_GET['server'] : "";
$region = array_key_exists("region", $_GET) ? $_GET['region'] : "";
//$raid = array_key_exists("raid", $_GET) ? (empty($_GET['raid']) ? null : intval($_GET['raid'])) : null;
$encounter = array_key_exists("encounter", $_GET) ? (empty($_GET['encounter']) ? null : intval($_GET['encounter'])) : null;
$class = array_key_exists("class", $_GET) ? (empty($_GET['class']) ? null : intval($_GET['class'])) : null;
$spec = array_key_exists("spec", $_GET) ? (empty($_GET['spec']) ? null : intval($_GET['spec'])) : null;
$bracket = array_key_exists("bracket", $_GET) ? (empty($_GET['bracket']) ? 0 : intval($_GET['bracket'])) : 0;
$difficulty = array_key_exists("difficulty", $_GET) ? (empty($_GET['difficulty']) ? 0 : intval($_GET['difficulty'])) : 0;

if( empty($character) || empty($server) || empty($region) || $encounter === null || $class === null || $spec === null ) {
	INTERNAL_SERVER_ERROR();
}

global $api_key;
try {
		$db = Database::getInstance();

		if(!$db->is_member($character, $server, $region)) {
			INTERNAL_SERVER_ERROR("Character not found!");
		}
		
		$metric = $db->get_metric($class, $spec);
		$data = fetch_encounter_rankings($encounter, $metric, $difficulty, 0, $region, $class, $spec, $bracket, 5000, $api_key);
		
		if( $metric === Metric::$KRSI) {
			$data = array_reverse($data);
		}

		$lloyd = lloyd($data, $character, $server);
		
		if( $metric === Metric::$KRSI) {
			$lloyd['clusters'] = array_reverse($lloyd['clusters']);
		}
		
		if( count($data) > 0 ) {
			$last = (array) end($data);
			$last = $last['total'];
			$first = (array) reset($data);
			$first = $first['total'];
			
			$E = $lloyd['E'];
			$Ep = $E / abs($last - $first);
			if( $metric === Metric::$KRSI) {
				$Ep = 1 - $Ep;
			}
			$s = $lloyd['sigma2'];
			$sp = $lloyd['sigma2'] / abs($last - $first);
			$db->insert_challenge($encounter, $difficulty, $class, $spec, $bracket, $E, $Ep, $s, $sp);
		}
		
		$result = array(
			"total" => count($data),
			"lloyd" => $lloyd,
			//"rankings" => $data
		);
		
		SEND_OK($result);
		
} catch (Exception $e) {
	// something bad happened
	INTERNAL_SERVER_ERROR($e->getMessage());
}

function lloyd($data=array(), $character, $server) {
	$total = count($data);
	
	if($total <= 0)
		return array();
	
	$last = (array) end($data);
	$last = $last['total'];
	$first = (array) reset($data);
	$first = $first['total'];

	$steps = 19;
	$range = ($first - $last) / ($steps + 1);

	$clusters = array();
	$centroids = array();
	
	foreach(range(1, $steps) as $n) {
		$clusters[$n-1] = array(
			"id" => $n-1,
			"min" => $last + $n * $range,
			"max" => $last + $n * $range,
			"centroid" => $last + $n * $range,
			"total" => 0,
			"is_in" => false
		);
		
		$centroids[$n-1] = $last + $n * $range;
	}
	//var_dump($clusters);

	foreach($data as $ranking) {
		$ranking = (array) $ranking;
		$total = $ranking['total'];
		
		$index = binary_search($centroids, $total);
		
		if( $index >= 0 && $index < count($clusters) ) {
			
			$cluster = &$clusters[$index];
			$cluster['total'] = $cluster['total'] + 1;
			
			if( $total < $cluster['min'] ) {
				$cluster['min'] = $total;
			}
			if( $total > $cluster['max'] ) {
				$cluster['max'] = $total;
			}
			
			$cluster['centroid'] = ( ( $cluster['centroid'] * ( $cluster['total'] - 1 ) ) + $total ) / $cluster['total'];
			$centroids[$index] = $cluster['centroid'];
			
			if( mb_strtolower($ranking['name'], 'UTF-8') === mb_strtolower($character, 'UTF-8') && mb_strtolower($ranking['server'], 'UTF-8') === mb_strtolower($server, 'UTF-8') ) {
				$cluster['is_in'] = true;
			}

			unset($cluster);
		}

	}

	$E = 0;
	$deviation = 0;
	$total = 0;
	
	foreach($clusters as $cluster) {
		$E = $E + $cluster['centroid'] * $cluster['total'];
		$total = $total + $cluster['total'];
	}
	if( $total > 0) {
		$E = $E / $total;
	}
	
	/* tightened start */
	/*
	$EE = 0;
	$ttotal = 0;
	
	foreach($clusters as $cluster) {
		if( ($cluster['min'] < $E && $cluster['max'] > $E) || $cluster['min'] > $E ) {
			$EE = $EE + $cluster['centroid'] * $cluster['total'];
			$ttotal = $ttotal + $cluster['total'];
		}
	}
	if( $ttotal > 0) {
		$EE = $EE / $ttotal;
	}
	
	foreach($clusters as $cluster) {
		if( ($cluster['min'] < $E && $cluster['max'] > $E) || $cluster['min'] > $E ) {
			$deviation = $deviation + pow($EE - $cluster['centroid'], 2) * $cluster['total'];
		}
	}
	$deviation = $deviation / ( $ttotal - 1 );
	
	$SD = sqrt($deviation);
	$SDp = $EE + $SD;
	$SDm = $EE - $SD;
	
	*/
	/* tightened end */
	
	
	foreach($clusters as $cluster) {
		$deviation = $deviation + pow($E - $cluster['centroid'], 2) * $cluster['total'];
	}
	$deviation = $deviation / ( $total - 1 );
	
	
	$SD = sqrt($deviation);
	$SDp = $E + $SD;
	$SDm = $E - $SD;
	
	
	
	/* remove empty clusters */
	$_clusters = array();
	foreach($clusters as $cluster) {
		if( $cluster['total'] > 0 ) {
			$_clusters[] = $cluster;
		}
	}
	
	
	return array(
		"clusters" => $_clusters,
		"E" => $E,
		"sigma2" => $SD,
		"sigma2+" => $SDp,
		"sigma2-" => $SDm,
		"total" => count($clusters)
	);
	/*
	return array(
		"clusters" => $_clusters,
		"E" => $EE,
		"sigma2" => $SD,
		"sigma2+" => $SDp,
		"sigma2-" => $SDm,
		"total" => count($_clusters)
	);
	*/
}


?>