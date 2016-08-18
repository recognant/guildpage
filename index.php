<!DOCTYPE html>

<html lang="en">

<head>

	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	
	<title>Natas</title>

	<link href="css/normalize.css" rel="stylesheet"></link>
	<link href="css/bootstrap.min.css" rel="stylesheet"></link>
	<link href="css/bootstrap-theme.min.css" rel="stylesheet"></link>
	<link href="css/font-awesome.min.css" rel="stylesheet"></link>
	<link href="css/style.css" rel="stylesheet"></link>
	<link href="css/guidesite.css" rel="stylesheet"></link>
	
	<script src="http://static.wowhead.com/widgets/power.js"></script>
	<script>
		var wowhead_tooltips = { "colorlinks": true, "iconizelinks": true, "renamelinks": true };
	</script>

</head>


<body onhashchange="onhashchange();">
	
	<div id="wrap">
	
		<header class="page-bg">
	
			<div class="welcome">

				<img class="welcome-logo" src="http://eu.battle.net/forums/static/images/game-logos/game-logo-wow.png" />
				<p class="welcome-text">Willkommen bei der Gilde Natas.</p>
				
			</div>
	
		</header>
		

		<div class="container" style="margin-top: -60px;">

			<div class="row">
				<nav id="sticky-nav" class="navbar navbar-fel navbar-default">
					<div class="container-fluid" style="padding: 0;">
					
						<div class="navbar-header">
							<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar">
								<span class="sr-only">Toggle navigation</span>
								<span class="icon-bar"></span>
								<span class="icon-bar"></span>
								<span class="icon-bar"></span>
							</button>
							<a class="navbar-brand visible-xs-block" href="#"><i class="fa fa-tree"></i>Natas</a>
						</div>
						
						<div id="navbar" class="collapse navbar-collapse">
							<ul class="nav nav-collapse navbar-nav">
								<li id="nav-home" class="active"><a href="#home"><i class="fa fa-home fa-fw fa-lg fa-text-icon"></i>Startseite</a></li>
								<!--li id="nav-about"><a href="#about"><i class="fa fa-info fa-fw fa-lg fa-text-icon"></i>Über uns</a></li-->
								<li id="nav-videos"><a href="#videos"><i class="fa fa-youtube fa-fw fa-lg fa-text-icon"></i>Videos</a></li>
								<!--li id="nav-stream"><a href="#stream"><i class="fa fa-twitch fa-fw"></i>Livestream</a></li-->
								<!--li id="nav-apply"><a href="#apply"><i class="fa fa-pencil fa-fw fa-lg fa-text-icon"></i>Bewerben</a></li-->
								<li id="nav-members"><a href="#members"><i class="fa fa-users fa-fw fa-lg fa-text-icon"></i>Mitglieder</a></li>
								<!--li><a href="http://seelenwanderer.forumprofi.de/index.php" target="_blank"><i class="fa fa-comments fa-fw fa-lg fa-text-icon"></i>Forum</a></li-->
								<li id="nav-guides" class="dropdown">
									<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><i class="fa fa-compass fa-fw fa-lg fa-text-icon"></i>Guides <span class="caret"></span></a>
									<ul class="dropdown-menu">
										<li class="dropdown-header">Allgemeines</li>
										<?php	
											include_once(dirname(__FILE__) . "/modules/database/database.php");
											include_once(dirname(__FILE__) . "/modules/utils.php");
										
											$db = Database::getInstance();
											$guides = $db->get_guides(false);
											
											if( $guides ) {
												foreach($guides as $guide) {
													echo '<li><a href="#node@' . $guide['tag'] . '">' . $guide['name'] . (strtotime($guide['creation_date']) + 259200 > getdate()[0] ? ' <i class="fa fa-star" style="color: #f00;"> NEW</i>' : '') . '</a></li>';
												}
											}
										?>
										<!---->
										<li role="separator" class="divider"></li>
										<li class="dropdown-header">Bosse</li>
										<li class="dropdown-submenu"><a href="#" tabindex="-1">Schwarzfelsgießerei</a>
											<ul class="dropdown-menu">
												<li><a href="#">Normal</a></li>
												<li class="dropdown-submenu">
													<a href="#" tabindex="-1">Heroisch</a>
													<ul class="dropdown-menu">
														<li><a href="#node@temp_1433867843">Schmelzofen</a></li>
													</ul>
												</li>
											</ul>
										</li>
										<li class="dropdown-submenu"><a href="#" tabindex="-1">Höllenfeuerzitadelle</a>
											<ul class="dropdown-menu">
												<li class="dropdown-submenu">
													<a href="#">Normal</a>
													<ul class="dropdown-menu">
														<li><a href="#node@hfc-hc-hellfire_assault">Höllenfeuerangriff</a></li>
														<li><a href="#node@hfc-hc-iron_reaver">Eiserner Häscher</a></li>
														<li><a href="#node@hfc-hc-kormrok">Kormrok</a></li>
														<li><a href="#node@hfc-hc-kilrogg_deadeye">Kilrogg Todauge</a></li>
														<li><a href="#node@hfc-hc-hellfire_council">Höllenfeuerrat</a></li>
														<li><a href="#node@hfc-hc-gorefiend">Blutschatten</a></li>
													</ul>
												</li>
												<li class="dropdown-submenu">
													<a href="#" tabindex="-1">Heroisch</a>
													<ul class="dropdown-menu">
														<li><a href="#node@hfc-hc-hellfire_assault">Höllenfeuerangriff</a></li>
														<li><a href="#node@hfc-hc-iron_reaver">Eiserner Häscher</a></li>
														<li><a href="#node@hfc-hc-kormrok">Kormrok</a></li>
														<li><a href="#node@hfc-hc-kilrogg_deadeye">Kilrogg Todauge</a></li>
														<li><a href="#node@hfc-hc-hellfire_council">Höllenfeuerrat</a></li>
														<li><a href="#node@hfc-hc-gorefiend">Blutschatten</a></li>
													</ul>
												</li>
											</ul>
										</li>
										<!---->
									</ul>
								</li>
								<li id="nav-statistics"><a href="#statistics"><i class="fa fa-area-chart fa-lg fa-fw fa-text-icon"></i>Statistiken</a></li>
								<li id="nav-rank"><a href="#rank"><i class="fa fa-line-chart fa-lg fa-fw fa-text-icon"></i>Rankings</a></li>
							</ul>
						</div><!--/.nav-collapse -->
					</div>
				</nav>
			</div>
		
			<div class="row">
				<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" id="content"></div>
			</div>
			
		</div><!-- /.container -->
	
	</div>
	
	<div id="modal-dialog" class="modal fade">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title"></h4>
				</div>
				<div class="modal-body">
				</div>
		  
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">schließen</button>
				</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
	
	<footer class="footer">
	
		<div class="footer-pane">
			<div class="footer-logo">
				Links:
			</div>
			<div class="footer-logo">
				<a href="https://www.warcraftlogs.com/guilds/25969" target="_blank">
					<img src="https://www.warcraftlogs.com/img/warcraft/header-logo.png?v=2" />
				</a>
			</div>
			<div class="footer-line"></div>
			<div class="footer-logo">
				<a href="http://eu.battle.net/" target="_blank">
					<img src="http://eu.battle.net/static/local-common/images/logos/bnet-default.png" />
				</a>
			</div>
			<div class="footer-line"></div>
			<div class="footer-logo">
				<a href="http://eu.battle.net/wow/de/guild/blutkessel/Natas/" target="_blank">
					<img src="https://worldofwarcraft.com/static/components/Logo/Logo-wowIcon-01e2c44379.png" />
				</a>
			</div>
			<div class="footer-line"></div>
			<div class="footer-logo">
				<a href="http://youtube.de" target="_blank">
					<img src="https://www.youtube.com/yt/brand/media/image/YouTube-icon-full_color.png" />
				</a>
			</div>
		</div>

    </footer>

    <script src="js/jquery-2.1.4.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
	<script src="js/highcharts.js"></script>
	<script src="js/webi.js"></script>
	<script src="js/index.js"></script>
	<script src="js/chart.js"></script>
	
	<script>
		$(document).ready(function() {
			onhashchange();
		});
	</script>
	
</body>




</html>