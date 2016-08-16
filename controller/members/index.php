<?php

include_once(dirname(__FILE__) . "/../../modules/database/database.php");
include_once(dirname(__FILE__) . "/../../modules/utils.php");

$db = Database::getInstance();
$members = $db->get_members();
$db->disconnect();

$result = array(
	"total" => sizeof($members),
	"members" => $members
);

SEND_OK($result);

?>