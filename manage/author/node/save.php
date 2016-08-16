<?php

include_once(dirname(__FILE__) . "/../../../modules/database/database.php");
include_once(dirname(__FILE__) . "/../../../modules/utils.php");

$data = isset($_POST['data']) ? $_POST['data'] : "";
$tag = isset($_POST['tag']) ? $_POST['tag'] : "";

try {

	$db = Database::getInstance();
	$guide = $db->get_guide($tag);

	if( $guide ) {
		if( file_exists("temp/" . $tag . ".gfl") ) {
			$filename = "temp/" . $tag . ".gfl";
			
			$file = fopen($filename, "w"); 

			if( !$file) {
				INTERNAL_SERVER_ERROR("File not found!");
			}

			fwrite($file, $data);
			fclose($file);
			
			$db->unpublish_guide($tag);

			include_once(dirname(__FILE__) . "/../processor/index.php");
			process($filename);
		}
		
	}
	else {
		INTERNAL_SERVER_ERROR("Guide not found!");
	}
	
} catch (Exception $e) {
	INTERNAL_SERVER_ERROR($e->getMessage());
}

?>