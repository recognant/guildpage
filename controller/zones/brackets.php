<?php

set_time_limit(10);

include_once(dirname(__FILE__) . "/../../modules/database/database.php");
include_once(dirname(__FILE__) . "/../../modules/utils.php");

$raid = array_key_exists("raid", $_GET) ? (empty($_GET['raid']) ? null : intval($_GET['raid'])) : null;

if( is_null($raid) ) {
	INTERNAL_SERVER_ERROR("something is off...");
}

$db = Database::getInstance();
$brackets = $db->get_brackets($raid);
$db->disconnect();

$result = array(
	"total" => sizeof($brackets),
	"brackets" => $brackets
);

SEND_OK($result);

?>