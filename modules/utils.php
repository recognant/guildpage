<?php 

function SEND_MSG($msg = "") {
	die($msg);
}

function SEND_JSON($msg) {
	die(json_encode($msg));
}

function SEND_ERROR($error = "", $msg = "error") {
	$json = array("status" => 1, "error" => $error, "msg" => $msg);
	SEND_JSON($json);
}

function SEND_OK($msg = "") {
	$json = array("status" => 0, "msg" => $msg);
	SEND_JSON($json);
}

function INTERNAL_SERVER_ERROR($msg = "") {
	header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
	SEND_ERROR($msg);
}

function FORBIDDEN($msg = "") {
	header($_SERVER['SERVER_PROTOCOL'] . ' 403 Forbidden', true, 403);
	SEND_ERROR($msg);
}

class Links {

	public static function BNET_CHARACTER($character="", $server="", $region="") {
		if( empty($character) || empty($server) || empty($region) ) {
			return "";
		}
		return "http://" . $region . ".battle.net/wow/character/" . $server . "/" . $character . "/simple";
	}

}

class Metric {

	public static $DPS;
	public static $HPS;
	public static $KRSI;
	
	public static function is_metric($p) {
		return in_array($p, array(Metric::$DPS, Metric::$HPS, Metric::$KRSI));
	}
	
}

Metric::$DPS = "bossdps";
Metric::$HPS = "hps";
Metric::$KRSI = "krsi";

/* 1 = LFR, 2 = Flex, 3 = Normal, 4 = Heroic, 5 = Mythic, 10 = Challenge Mode */
class Difficulty {

	public static $LFR;
	public static $FLEX;
	public static $NORMAL;
	public static $HEROIC;
	public static $MYTHIC;
	public static $CM;

	public static function asArray() {
		return array(
			"Normal" => Difficulty::$NORMAL,
			"Heroic" => Difficulty::$HEROIC,
			"Mythic" => Difficulty::$MYTHIC,
		);
	}
	
}

Difficulty::$LFR = 1;
Difficulty::$FLEX = 2;
Difficulty::$NORMAL = 3;
Difficulty::$HEROIC = 4;
Difficulty::$MYTHIC = 5;
Difficulty::$CM = 10;

function __binary_search($array, $needle, $left, $right) {
	if( $left > $right ) {
		return -1;
	}
	if( $left == $right ) {
		return array_keys($array)[$left];
	}
	if( $left+1 == $right ) {
		$dist = abs($needle - current(array_slice($array, $left, 1)) );
		if( $dist > abs($needle - current(array_slice($array, $right, 1)) )) {
			return array_keys($array)[$right];
		}
		else {
			return array_keys($array)[$left];
		}
	}
	
	$index = round( ($right + $left) / 2 );
	$value = current(array_slice($array, $index, 1));
	
	/*
	var_dump($left);
	var_dump($right);
	var_dump($index);
	*/
	
	if( $needle == $value) {
		return array_keys($array)[$index];
	} else if( $needle > $value) {
		return __binary_search($array, $needle, $index, $right);
	} else if( $needle < $value) {
		return __binary_search($array, $needle, $left, $index);
	}
}

function binary_search($array=array(), $needle) {
	return __binary_search($array, $needle, 0, count($array)-1);
}

?>