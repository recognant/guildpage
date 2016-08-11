<?php

include_once(dirname(__FILE__) . "/../../../modules/database/database.php");
include_once(dirname(__FILE__) . "/../../../modules/utils.php");

$db = Database::getInstance();
$classes = $db->get_classes();

$result = array(
	"total" => sizeof($classes),
	"classes" => $classes
);

Utils::SEND_JSON($result);


?>