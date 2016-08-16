<?php

include_once(dirname(__FILE__) . "/../modules/database/database.php");
include_once(dirname(__FILE__) . "/../modules/utils.php");

$character = array_key_exists("character", $_GET) ? mb_strtolower($_GET['character'], 'UTF-8') : "";
$server = array_key_exists("server", $_GET) ? mb_strtolower($_GET['server'], 'UTF-8') : "";
$region = array_key_exists("region", $_GET) ? mb_strtolower($_GET['region'], 'UTF-8') : "";
$metric = array_key_exists("metric", $_GET) ? ( !empty($_GET['metric']) ? mb_strtolower($_GET['metric'], 'UTF-8') : Metric::$DPS ) : Metric::$DPS;

if( empty($character) || empty($server) || empty($region) || !Metric::is_metric($metric) ) {
	INTERNAL_SERVER_ERROR("Missing or wrong parameters given!");
}

?>

<!DOCTYPE>
<html>

<head>
	<title></title>
</head>

<body>

<div class="row">

	<!--h2 style="color: #fff;"><i class="fa fa-users fa-fw fa-text-icon"></i>Profil</h2-->

	<div class="card">

		<h1 id="character-name"></h1>
		<select onchange="changeMetric(this);" class="form-control">
			<option value="<?php echo Metric::$DPS; ?>" <?php if($metric === Metric::$DPS) { echo "selected"; } ?>><?php echo Metric::$DPS; ?></option>
			<option value="<?php echo Metric::$HPS; ?>" <?php if($metric === Metric::$HPS) { echo "selected"; } ?>><?php echo Metric::$HPS; ?></option>
			<option value="<?php echo Metric::$KRSI; ?>" <?php if($metric === Metric::$KRSI) { echo "selected"; } ?>><?php echo Metric::$KRSI; ?></option>
		</select>
		<div id="character-profile"></div>
<?php

$db = Database::getInstance();

if(!$db->is_member($character, $server, $region)) {
	INTERNAL_SERVER_ERROR("Character not found!");
}

try {
	switch($metric) {
	case Metric::$DPS:
		$rankings = $db->get_dps_rank($character, $server, $region, null, null, null);
		$progress = $db->get_dps_ranking($character, $server, $region, null, null, null);
		break;
	case Metric::$HPS:
		$rankings = $db->get_hps_rank($character, $server, $region, null, null, null);
		$progress = $db->get_hps_ranking($character, $server, $region, null, null, null);
		break;
	case Metric::$KRSI:
		$rankings = array();
		$progress = array();
		break;
	default:
		INTERNAL_SERVER_ERROR("Missing or wrong parameters given!");
	}
	
	$raids = array();

	foreach($rankings as $ranking) {
		if(!array_key_exists($ranking['raid'], $raids)) {
			$raids[$ranking['raid']] = array();
		}
		if(!array_key_exists($ranking['difficulty'], $raids[$ranking['raid']])) {
			$raids[$ranking['raid']][$ranking['difficulty']] = array();
		}
		$raids[$ranking['raid']][$ranking['difficulty']][] = $ranking;
	}
	
	$info = $db->get_member_info($character, $server, $region);
	$class = is_null($info) ? "" : $info['class'];
	
} catch (Exception $e) {
	INTERNAL_SERVER_ERROR($e->getMessage());
}

?>
		
	</div>

</div>
	
</body>

<script>
	var raids = <?php echo json_encode($raids); ?>;
	var progress = <?php echo json_encode($progress); ?>;
	$('#character-name').html("<i class='icon icon-<?php echo mb_strtolower(str_replace(' ', '', $class), 'UTF-8'); ?>'></i><?php echo mb_strtoupper(mb_substr($character, 0, 1), 'UTF-8').mb_substr($character, 1); ?>");
	
	for(var r in raids) {
		var raid = raids[r];

		if( $.type(raid.Normal) !== "undefined" || $.type(raid.Heroic) !== "undefined" || $.type(raid.Mythic) !== "undefined" ) {
			
			for(var d in raid) {
				$('#character-profile').append("<h3>" + r + " (" + d + ")</h3>");
				var rankings = raid[d];
				var encounters = [];
				var min = [];
				var avg = [];
				var max = [];
			
				for(var i = 0; i < rankings.length; i++) {
					var ranking = rankings[i];
					encounters.push(ranking['encounter']);
					min.push(parseFloat(ranking['min'])*100);
					avg.push(parseFloat(ranking['avg'])*100);
					max.push(parseFloat(ranking['max'])*100);
				}
				
				var chart = $('<div id="chart-' + r + '-' + d + '"></div>');
				$('#character-profile').append(chart);
				$Chart.plotGuildranks(chart, encounters, [{ name: 'Max', data: max }, { name: 'AVG', data: avg }, { name: 'Min', data: min }]);
			}
		
		}
		
	}
	
	var encounters = [];
	var rank = [];
	var total = [];
	var itemLevel = [];
	
	$('#character-profile').append("<h3>Alle Rankings</h3>");
	
	for(var i = 0; i < progress.length; i++) {
		var ranking = progress[i];
		encounters.push(ranking['encounter'] + " (" + ranking['difficulty'] + ")");
		rank.push(parseFloat(ranking['rank'])*100);
		total.push(parseFloat(ranking['total']));
		itemLevel.push(parseFloat(ranking['itemLevel']));
	}
	
	var height = 200 + progress.length * 75;
	var chart = $('<div id="chart-progress"></div>');
	chart.css('height', height + 'px');
	$('#character-profile').append(chart);
	
	var series = [{
			name: 'Total',
            type: 'column',
            yAxis: 1,
            data: total,
			dataLabels: {
				enabled: true,
				color: '#fff',
				shadow: false
			}
        }, {
            name: 'Item Level',
            type: 'column',
			yAxis: 2,
            data: itemLevel,
			dataLabels: {
				enabled: true,
				color: '#fff',
				shadow: false
			}
        }, {
			name: 'Rank',
            type: 'column',
            data: rank,
            tooltip: {
                valueSuffix: '%'
            },
			dataLabels: {
				enabled: true,
				color: '#fff',
				shadow: false
			}
        }];
	
	if( progress.length > 0)
		$Chart.plotCharranks(chart, encounters, series);
		
	function changeMetric(selector) {
		var metric = $(selector).val();
		var hash = "<?php echo "#profile@$region/$server/$character/"; ?>" + metric;
		window.location.hash = hash;
	}
	
</script>

</html>