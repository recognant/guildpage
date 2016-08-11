<?php

function curl_get($url, array $get = NULL, array $options = array()) {
	set_time_limit(0);
	
	$opts = array( 
        CURLOPT_URL => $url. (strpos($url, '?') === FALSE ? '?' : ''). ( $get === NULL ? "" : http_build_query($get)), 
        CURLOPT_HEADER => 0, 
        CURLOPT_RETURNTRANSFER => TRUE, 
        CURLOPT_TIMEOUT => 600,
		CURLOPT_CONNECTTIMEOUT => 0
    );
	
	if( preg_match('/^(https:\/\/.*)$/', $url) > 0 ) {
		$opts[CURLOPT_SSL_VERIFYPEER] = 0;
		$opts[CURLOPT_SSL_VERIFYHOST] = 0;
	}
	
    $ch = curl_init(); 
    curl_setopt_array($ch, ($options + $opts)); 
    if( ! $result = curl_exec($ch)) { 
        trigger_error(curl_error($ch)); 
    } 
    curl_close($ch);
    return $result; 
}

?>