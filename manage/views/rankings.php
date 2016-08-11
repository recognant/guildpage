<!DOCTYPE>
<html>

<head>
	<title></title>
</head>

<body>
	
	<div class="row">
			
		<table class="table table-striped">
			
			<thead>
				<th>Name</th>
				<th>Server</th>
				<th>Region</th>
				<th></th>
				<th></th>
				<th></th>
				<th></th>
			</thead>
			
			<tbody id="table-rankings">
			</tbody>
			
		</table>
	
	</div>
	
	<script>
		
		var __Rankings = {
		
			__anchor: '#table-rankings',
			__rankings: [],
			__reloading: false,
		
			print: function(rankings) {
				$(this.__anchor).empty();
				
				for(var i = 0; i < rankings.length; i++) {
					var ranking = rankings[i];
					var tr = $('<tr></tr>');
					
					tr.append('<td>' + ranking.name.substr(0, 1).toUpperCase() + ranking.name.substr(1).toLowerCase() + '</td>');
					
					tr.append('<td>' + ranking.server.substr(0, 1).toUpperCase() + ranking.server.substr(1).toLowerCase() + '</td>');
					
					tr.append('<td>' + ranking.region.toUpperCase() + '</td>');
					
					tr.append('<td><span class="badge">' + ranking.total + '</span></td>');
					
					tr.append('<td><i class="fa fa-tachometer"></i> ' + ranking.metric + '</td>');
					
					tr.append('<td><a target="_blank" tabindex="-1" href="http://' + ranking.region + '.battle.net/wow/character/' + ranking.server + '/' + ranking.name + '/simple">Arsenal-Link</a></td>');
					
					tr.append('<td><button type="button" class="btn btn-xs btn-danger" onclick="__Rankings.remove(\'' + ranking.name + '\', \'' + ranking.server + '\', \'' + ranking.region + '\');"><i class="fa fa-times fa-fw"></i></button></td>');

					$(this.__anchor).append(tr);
				}
			},
			
			refresh: function() {
				if( $.type(this.__rankings) === "array" && this.__rankings.length > 0 ) {
					this.print(this.__rankings);
				}
			},
			
			load: function() {
				var __this = this;
				
				webi.loadJSON("controller/rankings/index.php").done(function(data) {
					__this.__rankings = data.rankings;
					__this.refresh();
				});
			},
			
			remove: function() {
				if(arguments.length !== 3)
					return;
				
				if( !confirm("Wollen Sie die Rankings dieses Spielers wirklich löschen?") )
					return;
			
				var __this = this;
				
				$.getJSON("controller/rankings/delete.php?character="+arguments[0]+"&server="+arguments[1]+"&region="+arguments[2]).done(function(data) {
					if( data.status == 0) {
						__this.load();
						$Toasts.success("Erfolgreich!");
					}
				}).fail(function() {
					$Toasts.alert("Fehlgeschlagen!");
				});
			}
		
		}
		
		$(document).ready(function() {
			__Rankings.load();
		});
	
	</script>
	
</body>

</html>