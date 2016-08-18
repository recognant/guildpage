<?php

set_time_limit(10);

include_once(dirname(__FILE__) . "/../../modules/database/database.php");
include_once(dirname(__FILE__) . "/../../modules/utils.php");

$db = Database::getInstance();
$zones = $db->get_zones();
$db->disconnect();

$result = array(
	"total" => sizeof($zones),
	"zones" => $zones
);

SEND_OK($result);

?>