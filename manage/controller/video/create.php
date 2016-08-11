<?php

include_once(dirname(__FILE__) . "/../../../modules/database/database.php");
include_once(dirname(__FILE__) . "/../../../modules/crawl/fetchdata.php");
include_once(dirname(__FILE__) . "/../../../modules/utils.php");

$ytid = array_key_exists("yt", $_GET) ? $_GET['yt'] : "";

if( empty($ytid) ) {
	Error::INTERNAL_SERVER_ERROR("something is off...");
}

try {
	$metadata = fetch_yt_metadata($ytid);

	if( !is_array($metadata) || count($metadata) === 0 ) {
		Error::INTERNAL_SERVER_ERROR("Video not found!");
	}
	
	$title = $metadata['title'];
	$author = $metadata['author_name'];
	$owner = $metadata['author_url'];
	$thumbnail = $metadata['thumbnail_url'];
	
	$db = Database::getInstance();
	$tag = $db->insert_video($ytid, $author, $thumbnail, $owner, $title);

	Utils::SEND_OK($tag);
} catch (Exception $e) {
	Error::INTERNAL_SERVER_ERROR($e->getMessage());
}
?>