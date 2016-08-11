<?php

header('Access-Control-Allow-Origin: *');

$view = json_decode( file_get_contents('temp.txt'), true );

if( $view['data']['lastUpdated'] + 0 * 60 > time() ) {
	die( json_encode($view) );
}

$data = array(
	"raw" => array(),
	"lastUpdated" => time(),
	"query" => array()
);

$socket = @fsockopen('62.104.20.114', 10011);

if($socket) {
	stream_set_blocking($socket, false);
} else {
	die( json_encode($view) );
}

fputs($socket, "use port=10025\n");
$res = getResponse($socket);

fputs($socket, "channellist\n");
$res = getResponse($socket);
$res = toJSON($res);
$data['raw']['channels'] = $res;

fputs($socket, "clientlist\n");
$res = getResponse($socket);
$res = toJSON($res);
$data['players'] = $res;

fclose($socket);

$content = json_encode(array("data" => $data));

file_put_contents('temp.txt', $content);

die( $content );

function getResponse($socket) {
	$timeout = 3;
	$done = false;
	$data = "";
	
	while( !$done ) {
		if( $line = trim(rtrim(fgetss($socket))) ) {
			$done = preg_match('/(error id\=\d+ msg\=ok)$/i', $line);
			if(!$done) {
				$data .= $line;
			}
		} else {
			$timeout = $timeout - 1;
			if($timeout < 1) {
				$done = true;
			}
			sleep(1);
		}
		
	}
	
	return $data;
}

function toJSON($data) {
	$channels = split('\|', $data);
	
	for($i = 0; $i < count($channels); $i++) {
		$channels[$i] = split(' ', $channels[$i]);
		
		$temp = [];
		for($j = 0; $j < count($channels[$i]); $j++) {
			$vars = split('=', $channels[$i][$j]);
			$key = $vars[0];
			$val = $vars[1];
			$val = preg_replace('/\\\\s/i', ' ', $val);
			$val = preg_replace('/\\\\\//i', '/', $val);
			$val = preg_replace('/\\\\p/i', ' \ ', $val);
			//$val = preg_replace('/\\\\/i', '', $val);
			$temp[$key] = $val;
		}
		
		$channels[$i] = $temp;
	}
	
	return $channels;
}

?>