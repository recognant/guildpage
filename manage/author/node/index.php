<?php

include_once(dirname(__FILE__) . "/../../../modules/database/database.php");
include_once(dirname(__FILE__) . "/../../../modules/utils.php");


$tag = isset($_GET['tag']) ? $_GET['tag'] : "";

if( empty($tag) ) {
	Error::INTERNAL_SERVER_ERROR("No valid tag given!");
}

try {

	$db = Database::getInstance();
	$guide = $db->get_guide($tag);

	if( $guide ) {
		if( !file_exists("temp/" . $tag . ".gfl") ) {

			$file = fopen("temp/" . $tag . ".gfl", "w"); 

			if( !$file) {
				Error::INTERNAL_SERVER_ERROR("File not found!");
			}

			fwrite($file, "");
			fclose($file);
		}
	} else {
		Error::INTERNAL_SERVER_ERROR("No valid tag given!");
	}
	
	include_once(dirname(__FILE__) . "/editor/index.php");

} catch (Exception $e) {
	Error::INTERNAL_SERVER_ERROR($e->getMessage());
}

?>