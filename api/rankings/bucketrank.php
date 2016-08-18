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
	
	$limit = 5000;
	$size = 0;
	$metric = $db->get_metric($class, $spec);
	
	$parts = array();
	$rankings = fetch_encounter($encounter, $metric, $difficulty, $size, $region, $class, $spec, $bracket, $limit, 1, $api_key);
	$total = intval($rankings['total']);
	$count = $total - $limit;
	$page = 2;
	
	$data = $rankings['rankings'];

	if( $total > 0) {
		$max = (array) reset($data);
		$max = $max['total'];
		if( $metric == Metric::$KRSI ) {
			$min = (array) end($data);
			$min = $min['total'];
			$Lloyd = new Lloyd($character, $server, $max, $min);
		}
		else {
			$Lloyd = new Lloyd($character, $server, 0, $max);
		}
		$Lloyd->compute( $data );
		
		while($count > 0) {
			$rankings = fetch_encounter($encounter, $metric, $difficulty, $size, $region, $class, $spec, $bracket, $limit, $page, $api_key);
			$count -= $limit;
			$page++;
			if( !!$rankings ) {
				$data = $rankings['rankings'];
				$Lloyd->compute( $data );
			}
		}
	
		$lloyd = $Lloyd->get_result();
	
		$min = $lloyd['min'];
		$max = $lloyd['max'];
		
		$E = $lloyd['E'];
		$Ep = $E / abs($max - $min);
		/*if( $metric === Metric::$KRSI) {
			$Ep = 1 - $Ep;
		}*/
		$s = $lloyd['sigma2'];
		$sp = $lloyd['sigma2'] / abs($max - $min);
		$db->insert_challenge($encounter, $difficulty, $class, $spec, $bracket, $E, $Ep, $s, $sp);
		
		$result = array(
			"total" => count($data),
			"lloyd" => $lloyd,
			"metric" => $metric
		);
		
		SEND_OK($result);
	}

	$result = array(
		"total" => 0
	);
	
	SEND_OK($result);
		
} catch (Exception $e) {
	// something bad happened
	INTERNAL_SERVER_ERROR($e->getMessage());
}

class Lloyd {

	const STEPS = 24;
	
	protected $total = 0;
	protected $range = 0;
	protected $min = 0;
	protected $max = 0;

	public $server = "";
	public $character = "";

	protected $clusters = array();
	protected $centroids = array();
	
	function __construct($server, $character, $min=0, $max=0) {
		$this->server = $server;
		$this->character = $character;
		$this->min = $min;
		$this->max = $max;
		$this->range = ($this->max - $this->min) / ($this::STEPS + 1);
		
		foreach(range(1, $this::STEPS) as $n) {
			$this->clusters[$n-1] = array(
				"id" => $n-1,
				"min" => $this->min + $n * $this->range,
				"max" => $this->min + $n * $this->range,
				"centroid" => $this->min + $n * $this->range,
				"total" => 0,
				"is_in" => false
			);
			
			$this->centroids[$n-1] = $this->min + $n * $this->range;
		}

	}

	public function compute( $data = array() ) {
		
		foreach($data as $ranking) {
			$ranking = (array) $ranking;
			$total = $ranking['total'];
			
			$index = binary_search($this->centroids, $total);
			
			if( $index >= 0 && $index < count($this->clusters) ) {
				
				$cluster = &$this->clusters[$index];
				$cluster['total'] = $cluster['total'] + 1;
				
				if( $total < $cluster['min'] ) {
					$cluster['min'] = $total;
				}
				if( $total > $cluster['max'] ) {
					$cluster['max'] = $total;
				}
				
				$cluster['centroid'] = ( ( $cluster['centroid'] * ( $cluster['total'] - 1 ) ) + $total ) / $cluster['total'];
				$this->centroids[$index] = $cluster['centroid'];
				
				if( mb_strtolower($ranking['name'], 'UTF-8') === mb_strtolower($this->character, 'UTF-8') && mb_strtolower($ranking['server'], 'UTF-8') === mb_strtolower($this->server, 'UTF-8') ) {
					$cluster['is_in'] = true;
				}

				unset($cluster);
			}
		}
	}
	
	public function get_result() {	
		$E = 0;
		$deviation = 0;
		$total = 0;
		
		/* remove empty clusters */
		$_clusters = array();
		foreach($this->clusters as $cluster) {
			if( $cluster['total'] > 0 ) {
				$_clusters[] = $cluster;
			}
		}
		
		foreach($_clusters as $cluster) {
			$E = $E + $cluster['centroid'] * $cluster['total'];
			$total = $total + $cluster['total'];
		}
		if( $total > 0) {
			$E = $E / $total;
		}

		foreach($_clusters as $cluster) {
			$deviation = $deviation + pow($E - $cluster['centroid'], 2) * $cluster['total'];
		}
		$deviation = $deviation / ( $total - 1 );
		
		$SD = sqrt($deviation);
		$SDp = $E + $SD;
		$SDm = $E - $SD;
		
		$min = $_clusters[0]['min'];
		
		return array(
			"clusters" => $_clusters,
			"min" => $min,
			"max" => $this->max,
			"E" => $E,
			"sigma2" => $SD,
			"sigma2+" => $SDp,
			"sigma2-" => $SDm,
			"total" => count($_clusters)
		);

	}

}

?>