<?php

include_once(dirname(__FILE__) . "/../../../modules/database/database.php");
include_once(dirname(__FILE__) . "/../../../modules/utils.php");

$db = Database::getInstance();
$members = $db->get_members();

$result = array(
	"total" => sizeof($members),
	"members" => $members
);

Utils::SEND_JSON($result);


?>