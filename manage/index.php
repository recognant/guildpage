<!DOCTYPE html>

<html lang="en">

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
	<link href="../css/guidesite.css" rel="stylesheet"></link>
	
	<script src="http://static.wowhead.com/widgets/power.js"></script>
	<script>
		var wowhead_tooltips = { "colorlinks": true, "iconizelinks": true, "renamelinks": true };
	</script>

</head>


<body onhashchange="onhashchange();">

	<header class="page-bg">
	
		<div class="welcome">

			<img class="welcome-logo" src="http://eu.battle.net/forums/static/images/game-logos/game-logo-wow.png" />
			<p class="welcome-text">Willkommen bei der Gilde Natas.</p>
			
		</div>
	
	</header>

    <div class="container" style="margin-top: -60px;">

		<div class="row">
			<nav id="sticky-nav" class="navbar navbar-fel navbar-default">
				<div class="container-fluid">
				
					<div class="navbar-header">
						<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar">
							<span class="sr-only">Toggle navigation</span>
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
						</button>
						<a class="navbar-brand visible-xs-block" href="#"><i class="fa fa-tree fa-fw"></i>Seelenwanderer</a>
					</div>
					
					<div id="navbar" class="collapse navbar-collapse">
						<ul class="nav nav-collapse navbar-nav">
							<li id="nav-videos"><a href="#videos"><i class="fa fa-youtube fa-fw fa-text-icon fa-lg"></i>Videos</a></li>
							<li id="nav-author"><a href="#author"><i class="fa fa-pencil fa-fw fa-text-icon fa-lg"></i>Editor</a></li>
							<li id="nav-members"><a href="#members"><i class="fa fa-users fa-fw fa-text-icon fa-lg"></i>Mitglieder</a></li>
							<li id="nav-rankings"><a href="#rankings"><i class="fa fa-line-chart fa-fw fa-text-icon fa-lg"></i>Rankings</a></li>
						</ul>
					</div><!--/.nav-collapse -->
				</div>
			</nav>
		</div>
	
		<div class="row">
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" id="content"></div>
		</div>
		
    </div><!-- /.container -->
	
	<div style="position: fixed; top: 0; padding: 5px; width: 100%;">
		<div id="toasts" align="center"></div>
	</div>

    <script src="../js/jquery-2.1.4.min.js"></script>
    <script src="../js/bootstrap.min.js"></script>
	<script src="../js/highcharts.js"></script>
	<script src="js/index.js"></script>
	<script src="../js/webi.js"></script>
	<script src="../js/toasts.js"></script>
	
	<script>
		$(document).ready(function() {
			onhashchange();
		});
	</script>
	
</body>




</html>