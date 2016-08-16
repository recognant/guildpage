<html>

<head>
</head>

<body>


<?php
include_once(dirname(__FILE__) . "/../modules/database/database.php");
include_once(dirname(__FILE__) . "/../modules/utils.php");

$tag = array_key_exists("tag", $_GET) ? $_GET['tag'] : "";

if( empty($tag) ) {
	INTERNAL_SERVER_ERROR();
}

$db = Database::getInstance();
$video = $db->get_video($tag);

if( $video == false ) {
	INTERNAL_SERVER_ERROR();
}

?>

<div class="row">
	
	<!--h2><i class="fa fa-youtube-square fa-fw"></i>Video</h2-->

	<div class="card">
		
		<div class="container-fluid">
			
			<div class="row">

				<div class="embed-responsive embed-responsive-16by9">
					<iframe src="https://www.youtube.com/embed/<?php echo $video['ytid']; ?>" allowfullscreen></iframe>
				</div>
				
			</div>
		
		</div>
	
	</div>  

</div>

</body>

</html>