<?php

include_once(dirname(__FILE__) . "/../../../modules/database/database.php");
include_once(dirname(__FILE__) . "/../../../modules/utils.php");

$db = Database::getInstance();
$classes = $db->get_classes();
$db->disconnect();

$result = array(
	"total" => sizeof($classes),
	"classes" => $classes
);

SEND_OK($result);


?>