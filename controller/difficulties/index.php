<?php

set_time_limit(10);

include_once(dirname(__FILE__) . "/../../modules/database/database.php");
include_once(dirname(__FILE__) . "/../../modules/utils.php");

$difficulties = Difficulty::asArray();

$result = array(
	"total" => sizeof($difficulties),
	"difficulties" => $difficulties
);

SEND_OK($result);

?>