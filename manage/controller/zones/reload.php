<?php

set_time_limit(10);

include_once(dirname(__FILE__) . "/../../../modules/database/database.php");
include_once(dirname(__FILE__) . "/../../../modules/crawl/fetchdata.php");
include_once(dirname(__FILE__) . "/../../../modules/utils.php");

$db = Database::getInstance();

global $api_key;
$zones = fetch_zones($api_key);

try {
	$db->init_zones($zones);
	SEND_OK();
	
} catch(Exception $e) {
	SEND_ERROR($e->getMessage());
}

?>