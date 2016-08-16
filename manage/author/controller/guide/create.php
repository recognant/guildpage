<?php

include_once(dirname(__FILE__) . "/../../../../modules/database/database.php");
include_once(dirname(__FILE__) . "/../../../../modules/utils.php");

$name = array_key_exists("name", $_GET) ? $_GET['name'] : "";
$author = array_key_exists("author", $_GET) ? $_GET['author'] : "";
$path = array_key_exists("path", $_GET) ? $_GET['path'] : "";

if( empty($name) || empty($author) || empty($path) ) {
	INTERNAL_SERVER_ERROR("something is off...");
}

try {
	$db = Database::getInstance();
	$tag = $db->insert_guide($name, $author, $path);
	$db->disconnect();
	
	SEND_OK($tag);
} catch (Exception $e) {
	INTERNAL_SERVER_ERROR($e->getMessage());
}
?>