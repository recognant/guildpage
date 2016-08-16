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
$guide = $db->get_guide($tag);

if( !$guide ) {
	INTERNAL_SERVER_ERROR();
}

?>

<div class="row">

	<div class="card header">
		<div class="text-muted" align="right"><i><?php echo "<b>" . $guide['author'] . "</b>"; ?> @ <?php echo $guide['update_date']; ?></i></div>
	</div>
	
	<div class="card extra-padding">
		<?php readfile(dirname(__FILE__) . "/../node/" . $tag . ".html"); ?>
	</div>

</body>

</html>