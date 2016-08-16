<!DOCTYPE>
<html lang="en">

<head>
	<title></title>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
</head>

<body>

<div class="row">

	<!--h2 style="color: #fff;"><i class="fa fa-users fa-fw"></i>Mitglieder</h2-->

	<div class="card">

		<table class="table table-striped">
	
			<thead>
				<th>Name</th>
				<th>Server</th>
				<th></th>
			</thead>
			
			<tbody>

<?php

include_once(dirname(__FILE__) . "/../modules/database/database.php");
include_once(dirname(__FILE__) . "/../modules/utils.php");

$db = Database::getInstance();

$members = $db->get_members();

foreach($members as $member) {

	echo "<tr>";
	
	echo "<td>";
	echo "<i class='icon icon-" . mb_strtolower(str_replace(" ", "", $member['class']), 'UTF-8') . "' title='" . $member['class'] . "'></i><a href='./#profile@" .$member['region'] . "/" . $member['server'] . "/" . $member['name'] ."'>" . mb_strtoupper(mb_substr($member['name'], 0, 1), 'UTF-8').mb_substr($member['name'], 1) . "</a>";
	echo "</td>";
	
	echo "<td>";
	echo mb_strtoupper(mb_substr($member['server'], 0, 1), 'UTF-8').mb_substr($member['server'], 1);
	echo "</td>";
	
	echo "<td>";
	echo "<a target='_blank' tabindex='-1' href='" . Links::BNET_CHARACTER($member['name'], $member['server'], $member['region']). "'>Arsenal-Link</a>";
	echo "</td>";
	
	echo "</tr>";
}

?>

			</tbody>
	
		</table>
		
	</div>

</div>
	
</body>

</html>