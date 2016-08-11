<!DOCTYPE html>

<html>

<head>

</head>


<body>

<div class="row">

<?php

include_once(dirname(__FILE__) . "/../modules/database/database.php");
include_once(dirname(__FILE__) . "/../modules/utils.php");

try {
	
	$db = Database::getInstance();
	
	$members = $db->get_members();
	$specs = $db->get_specs();
	//$zones = $db->get_zones();
	$brackets = $db->get_brackets(8);
	$encounters = $db->get_encounters(8);
	
	?>
	
	<h2 style="color: #fff;"><i class="fa fa-line-chart fa-fw"></i>Rankings</h2>
	
	<div class="card">
		
		<div class="alert alert-warning">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			<h4>Erläuterung:</h4>
			<p>Die neuste Errungenschaft der Menschheit: der Bucketrank! Mittels Dropdown-Menüs lassen sich Klasse und Spezialisierung, Boss, Bracket und Schwierigkeitsstufe einstellen. Zusätzlich könnt ihr Euren Namen aus der Liste auswählen, damit das Tool Euch in den Rankings findet. Ihr seid in einem Bucket, wenn der dazugehörige Leistungswert auf der X-Achse des Graphen durch * hervorgehoben und fett geschrieben ist.</p>
			<p>Der zu sehende Graph teilt die Spieler in verschiedene Buckets ein. Die Einfärbung gibt an, ob ihr unterdurchschnittlich (<font style="color: red;">rot</font>), fast unterdurchschnittlich (<font style="color: orange;">orange</font>), durchschnittlich (<font style="color: rgb(149, 206, 255);">blau</font>), fast überdurchschnittlich (<font style="color: green;">dunkelgrün</font>) oder überdurchschnittlich (<font style="color: rgb(25, 255, 25);">hellgrün</font>) gespielt habt. Dabei zählt immer nur euer <b>bestes Ergebnis</b>! Der vertikale rote Strich gibt die zu erwartende Leistung (<i>DPS, HPS oder KRSI</i>) an.</p>
			<p><b>Achtung!</b> Die Färbung ist für Tanks getauscht!</p>
		</div>
		
		<form id="myform" class="form-inline" role="form" onsubmit="searchRank(this); return false;">
		
			<div class="form-group">
				
				<select id="member" class="form-control">
					<?php foreach($members as $member) {
						echo "<option value='" . $member['name'] . "' server='" . $member['server'] . "' region='" . $member['region'] . "'>" . mb_strtoupper(mb_substr($member['name'], 0, 1), 'UTF-8').mb_substr($member['name'], 1) . "</option>";
					}
					?>
				</select>
				
			</div>
			
			<div class="form-group">
			
				<select id="spec" class="form-control">
					<?php foreach($specs as $spec) {
						echo "<option value='" . $spec['id'] . "' class='" . $spec['class_id'] . "'>" . mb_strtoupper(mb_substr($spec['class'], 0, 1), 'UTF-8').mb_substr($spec['class'], 1) . " ". mb_strtoupper(mb_substr($spec['name'], 0, 1), 'UTF-8').mb_substr($spec['name'], 1) . "</option>";
					}
					?>
				</select>
				
				<select id="bracket" class="form-control">
					<?php foreach($brackets as $bracket) {
						echo "<option value='" . $bracket['id'] . "' raid='" . $bracket['raid_id'] . "'>" . $bracket['name'] . "</option>";
					}
					?>
				</select>
				
				<select id="encounter" class="form-control">
					<?php foreach($encounters as $encounter) {
						echo "<option value='" . $encounter['id'] . "' raid='" . $encounter['raid'] . "'>" . $encounter['name'] . "</option>";
					}
					?>
				</select>
				
				<select id="difficulty" class="form-control">
					<option value="3">Normal</option>
					<option value="4">Heroic</option>
					<option value="5">Mythic</option>
				</select>
				
				<button type="button" class="btn btn-primary" onclick="$('#myform').submit();"><i class="fa fa-search"></i> Rank Me!</button>
				
			</div>
		
		
		</form>
		
		<div id="highcharts"></div>
			
	</div>
	
	<?php
	
} catch ( Exception $e) {
	
}


?>

	
	
</div>

<script>

	$(document).ready(function() {
		searchRank($('#myform'));
	});

	function searchRank() {
		var __form = $(arguments[0]);
		
		var __character = __form.find("#member :selected").val();
		var __server = __form.find("#member :selected").attr('server');
		var __region = __form.find("#member :selected").attr('region');
		
		var __spec = __form.find("#spec :selected").val();
		var __class = __form.find("#spec :selected").attr('class');
		
		var __bracket = __form.find("#bracket :selected").val();
		var __raid = __form.find("#bracket :selected").attr('raid');
		
		var __encounter = __form.find("#encounter :selected").val();

		var __difficulty = __form.find("#difficulty :selected").val();
		
		loadRank(__character, __server, __region, __raid, __encounter, __class, __spec, __bracket, __difficulty);
	}

	function loadRank(character, server, region, raid, encounter, _class, spec, bracket, difficulty) {
		
		webi.content.loadJSON($('#highcharts'), "controller/rankings/bucketrank.php", {
			character: character,
			server: server,
			region: region,
			raid: raid,
			encounter: encounter,
			class: _class,
			spec: spec,
			bracket: bracket,
			difficulty: difficulty,
		}).done(function(data) {
			
			if( data.total > 0) {
				var __lloyd = data.lloyd;
				
				var __clusters = __lloyd.clusters;
				var __E = __lloyd.E;
				var __E_index = 0;
				var __E_dist = Number.MAX_VALUE;
				var __SDp = __lloyd['sigma2+'];
				var __SDm = __lloyd['sigma2-'];
				
				var __categories = [];
				var __data = [];
			
				for(var i = 0; i < __clusters.length; i++) {
					var __cluster = __clusters[i];

					if( __cluster.is_in ) {
						__categories.push("<b>*" + __cluster.centroid.round(2) + "*</b>");
					} else {
						__categories.push(__cluster.centroid.round(2));
					}
					
					var __color = Highcharts.getOptions().colors[0];
					if( __cluster.min < __SDm && __cluster.max > __SDm ) {
						__color = 'orange';
					}
					if( __cluster.max < __SDm ) {
						__color = 'red';
					}
					if( __cluster.min < __SDp && __cluster.max > __SDp ) {
						__color = 'green';
					}
					if( __cluster.min > __SDp ) {
						__color = '#00ff00';
					}
											
					__data.push({ y: __cluster.total, color: __color });

					if( Math.abs(__cluster.centroid - __E) < __E_dist ) {
						__E_dist = Math.abs(__cluster.centroid - __E);
						__E_index = i;
					}
					
				}

				$Chart.plotRank($('#highcharts'), __E_index, __categories, [{ name: encounter, data: __data }]);
			}
			
		}).fail(function() {
			$('#highcharts').html('<div class="alert alert-danger" style="margin-top: 20px;"><i class="fa fa-exclamation"></i><b>Fehler!</b> Leider ging etwas schief! Versuchen Sie es erneut.</div>');
		});
		
	}

</script>
    
	
</body>




</html>