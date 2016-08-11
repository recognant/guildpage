<!DOCTYPE html>

<html lang="en">

<head>

	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	
	<title>Seelenwanderer</title>

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

    <div class="container">
	
		<div class="row">
			<div class="col-xs-12 visible-sm-block visible-md-block visible-lg-block">
				<img src="../img/logo.jpg" class="img-responsive page-logo"></img>
			</div>
		</div>

		<div class="row">
			<nav id="sticky-nav" class="navbar navbar-inverse navbar-default">
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
							<li id="nav-videos"><a href="#videos"><i class="fa fa-youtube fa-fw"></i>Videos</a></li>
							<li id="nav-author"><a href="#author"><i class="fa fa-pencil fa-fw"></i>Editor</a></li>
							<li id="nav-members"><a href="#members"><i class="fa fa-users fa-fw"></i>Mitglieder</a></li>
							<li id="nav-rankings"><a href="#rankings"><i class="fa fa-line-chart fa-fw"></i>Rankings</a></li>
						</ul>
					</div><!--/.nav-collapse -->
				</div>
			</nav>
		</div>
	
		<div class="row">
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" id="content"></div>
		</div>
		
    </div><!-- /.container -->
	
	<footer class="footer">
		<div class="container bg-default">
			<div class="row">
				<div class="col-xs-6">
					<p class="text-muted"><strong>Seelenwanderer:</strong></p>
				</div>
				<div class="col-xs-6">
					<p class="text-muted"><strong>Links:</strong></p>
				</div>
			</div>
			<hr style="margin-top: 5px; margin-bottom: 5px;"/>
			<div class="row">
				<div class="col-xs-6">
					<p><a href="mailto:Glad@Seelenwanderer-bk.de" title="Email-Kontakt" class="text-muted"><i class="fa fa-envelope fa-fw"></i>Email-Kontakt</a></p>
				</div>
				<div class="col-xs-6">
					<p><a href="https://www.youtube.com/channel/UCLmW8m26IVIHmGxEvOI3A5A" title="Youtube.com" class="text-muted"><i class="fa fa-youtube-square fa-fw"></i>Youtube.com</a></p>
					<p><a href="http://www.wowprogress.com/guild/eu/blutkessel/Seelenwanderer" title="WoWProgress.com" class="text-muted"><i class="fa fa-globe fa-fw"></i>WoWProgress.com</a></p>
					<p><a href="https://www.warcraftlogs.com/guilds/25969" title="Warcraftlogs.com" class="text-muted"><i class="fa fa-bar-chart fa-fw"></i>Warcraftlogs.com</a></p>
					<p><a href="http://eu.battle.net/wow/de/guild/blutkessel/Seelenwanderer/" title="World of Warcraft" class="text-muted"><i class="fa fa-gamepad fa-fw"></i>World of Warcraft</a></p>
					<p><a href="http://eu.battle.net/de/" title="battle.net" class="text-muted"><i class="fa fa-globe fa-fw"></i>battle.net</a></p>
				</div>
			</div>
		</div>
	</footer>
	
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