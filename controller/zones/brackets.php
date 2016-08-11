<?php

set_time_limit(10);

include_once(dirname(__FILE__) . "/../../modules/database/database.php");
include_once(dirname(__FILE__) . "/../../modules/utils.php");

$raid = array_key_exists("raid", $_GET) ? (empty($_GET['raid']) ? null : intval($_GET['raid'])) : null;

if( is_null($raid) ) {
	Error::INTERNAL_SERVER_ERROR("something is off...");
}

$db = Database::getInstance();
$brackets = $db->get_brackets($raid);

$result = array(
	"total" => sizeof($brackets),
	"brackets" => $brackets
);

Utils::SEND_JSON($result);

?>