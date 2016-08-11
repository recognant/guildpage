<html>

<head>
</head>

<body>


<?php

include_once(dirname(__FILE__) . "/../modules/database/database.php");
include_once(dirname(__FILE__) . "/../modules/utils.php");

$tag = array_key_exists("tag", $_GET) ? $_GET['tag'] : "";

if( empty($tag) ) {
	Error::INTERNAL_SERVER_ERROR();
}

$db = Database::getInstance();
$guide = $db->get_guide($tag);

if( !$guide ) {
	Error::INTERNAL_SERVER_ERROR();
}

?>

<div class="row">
	
	<div class="panel panel-inverse">
		<div class="panel-heading">
			<h2><i class="fa fa-compass fa-fw"></i>Guides</h2>
		</div>
		
		<div class="panel-body">
			<div class="text-muted" align="right" style="margin: -10px;"><i><?php echo "<b>" . $guide['author'] . "</b>"; ?> @ <?php echo $guide['update_date']; ?></i></div>
			<?php readfile(dirname(__FILE__) . "/../node/" . $tag . ".html"); ?>
		
		</div>  

	</div>

</body>

</html>