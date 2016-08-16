<!DOCTYPE>

<html>

<head>

	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	
	<title>Natas</title>

	<link href="../css/normalize.css" rel="stylesheet"></link>
	<link href="../css/bootstrap.min.css" rel="stylesheet"></link>
	<link href="../css/bootstrap-theme.min.css" rel="stylesheet"></link>
	<link href="../css/font-awesome.min.css" rel="stylesheet"></link>
	<link href="../css/style.css" rel="stylesheet"></link>
	
	<script src="http://static.wowhead.com/widgets/power.js"></script>
	<script>
		var wowhead_tooltips = { "colorlinks": true, "iconizelinks": true, "renamelinks": true };
	</script>

</head>

<body onhashchange="onhashchange();">

	<div class="container" id="content"></div>

	<script src="../js/jquery-2.1.4.min.js"></script>
    <script src="../js/bootstrap.min.js"></script>
	<script src="../js/highcharts.js"></script>
	<script src="../js/webi.js"></script>
	
	<script>
	
		function onhashchange() {
			var hash = window.location.hash;
			hash = hash.replace(/^#/i, "");
			hash = hash.replace(/(\@.*)/i, "");
			hash = hash.replace(/\.\.\//ig, "");
		
			$("#content").html("<div class='content-loader'><i class='fa fa-spinner fa-pulse'></i><strong>Lädt</strong></div>");
			$("#content").load("" + encodeURIComponent(hash) + ".html", function(response, status, xhr) {
				if( status === "error") {
					$("#content").html("");
				}
				$WowheadPower.refreshLinks();
			});
		}
	
		$(document).ready(function() {
			onhashchange();
		});
	</script>

</body>

</html>