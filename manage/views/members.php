<!DOCTYPE>
<html>

<head>
	<title></title>
</head>

<body>
	
	<div class="row">
	
			<div class="card">
		
			<div class="btn-group pull-right">
				<button type="button" class="btn btn-success" onclick="__Members.openAddDialog();"><i class='fa fa-user-plus fa-fw'></i> Neues Mitglied</button>
				<button type="button" class="btn btn-primary" onclick="reloadAll();"><i class='fa fa-refresh fa-fw'></i> Alle Aktualisieren</button>
			</div>
				
			<table class="table table-striped">
				
				<thead>
					<th>Name</th>
					<th>Server</th>
					<th></th>
					<th></th>
					<th></th>
				</thead>
				
				<tbody id="table-members">
				</tbody>
				
			</table>
		
		</div>
		
		<div id="modal"></div>
	
	</div>
	
	<script>
		var zones = [];
		
		var __Modal = {
		
			__anchor: "#modal",
			__modal: null,
			
			load: function(url) {
				var __this = this;
				
				$.get(url).done(function(data) {
					$(__this.__anchor).html($.parseHTML(data));
					__this.__modal = $(__this.__anchor).find('.modal').modal({
						backdrop: 'static',
						keyboard: false
					});
				});
			}
		};
		
		var __Members = {
		
			__anchor: '#table-members',
			__members: [],
			__reloading: false,
			__modal: {
				state: 0,
				visible: false
			},
		
			print: function(members) {
				$(this.__anchor).empty();
				
				for(var i = 0; i < members.length; i++) {
					var member = members[i];
					var tr = $('<tr></tr>');
					
					tr.append('<td><i class="icon icon-' + member.class.replace(" ", "").toLowerCase() + '" title="' + member.class + '"></i>' + member.name.substr(0, 1).toUpperCase() + member.name.substr(1).toLowerCase() + '</td>');
					
					tr.append('<td>' + member.server.substr(0, 1).toUpperCase() + member.server.substr(1).toLowerCase() + '</td>');
					
					tr.append('<td><a target="_blank" tabindex="-1" href="http://' + member.region + '.battle.net/wow/character/' + member.server + '/' + member.name + '/simple">Arsenal-Link</a></td>');
					
					tr.append('<td><button type="button" class="btn btn-xs btn-danger" onclick="__Members.remove(\'' + member.name + '\', \'' + member.server + '\', \'' + member.region + '\');"><i class="fa fa-times fa-fw"></i></button></td>');
					
					tr.append('<td><button type="button" class="btn btn-xs btn-primary" onclick="__Members.reload(\'' + member.name + '\', \'' + member.server + '\', \'' + member.region + '\');"><i class="fa fa-refresh fa-fw"></i></button></td>');

					$(this.__anchor).append(tr);
				}
			},
			
			refresh: function() {
				if( $.type(this.__members) === "array" && this.__members.length > 0 ) {
					this.print(this.__members);
				}
			},
			
			load: function() {
				var __this = this;
				
				webi.loadJSON("../controller/members/index.php").done(function(data) {
					if(data.status == 0) {
						data = data.msg;
					}
					__this.__members = data.members;
					__this.refresh();
				});
			},
			
			openAddDialog: function() {
				__Modal.load('views/modal/member.php');
			},
			
			add: function() {
				var __form = $(arguments[0]);
				var __this = this;
				
				$.getJSON("controller/members/create.php?" + __form.serialize()).done(function(data) {
					if( data.status == 0) {
						__this.load();
						$Toasts.success("Erfolgreich!");
					}
				}).fail(function() {
					$Toasts.alert("Fehlgeschlagen!");
				});
			},
			
			remove: function() {
				if(arguments.length !== 3)
					return;
				
				if( !confirm("Wollen sie dieses Mitglied wirklich löschen?") )
					return;
				
				var __this = this;
				
				$.getJSON("controller/members/delete.php?character="+arguments[0]+"&server="+arguments[1]+"&region="+arguments[2]).done(function(data) {
					if( data.status == 0) {
						__this.load();
						$Toasts.success("Erfolgreich!");
					}
				}).fail(function() {
					$Toasts.alert("Fehlgeschlagen!");
				});
			},
			
			reload: function() {
				if(arguments.length !== 3)
					return;
					
				$.get("controller/rankings/reload.php", {
					character: arguments[0],
					server: arguments[1],
					region: arguments[2],
					raid: 8
				}).done(function(data) {
					$('#reloadmodal').modal('hide');
				});
			}
		
		}
		
		$(document).ready(function() {

			webi.loadJSON("../controller/zones/index.php").done(function(data) {
				if(data.status == 0) {
					data = data.msg;
				}
				
				var id = 0;
			
				if(data.total > 0) {
					zones = data.zones;

					for(var i = 0; i < zones.length; i++) {
						var zone = zones[i];
						id = parseInt(zone['id']);
						$('#zone-selector').append("<option value=" + zone['id'] + ">" + zone['name'] + "</option>");
					}

				}
				
				$('#zone-selector').val(id);
			});
			
			__Members.load();
		});

		function openreloadModal(character, server, region) {
			$('#reloadmodal').modal({
				backdrop: 'static',
				keyboard: false
			});
			
			$('#reload-myform').show();
			$('#modal-status').hide();
			$('#reloadmodal').find('.modal-footer > button').each(function() {
				$(this).prop('disabled', false);
			});
			$('#reloadmodal').find('.modal-header > button').each(function() {
				$(this).prop('disabled', false);
			});
			
			$('#reloadmodal').find('#reload-input_name').val(character);
			$('#reloadmodal').find('#reload-input_server').val(server);
			$('#reloadmodal').find('#reload-input_region').val(region);
		}
		
		function reload() {
			var __character = $('#reloadmodal').find('#reload-input_name').val();
			var __server = $('#reloadmodal').find('#reload-input_server').val();
			var __region = $('#reloadmodal').find('#reload-input_region').val();
		
			$('#reload-myform').hide();
			$('#modal-status').show();
			
			$('#modal-status').html("<div class='content-loader'><i class='fa fa-spinner fa-pulse'></i><strong>Lädt</strong></div>");
			$('#reloadmodal').find('.modal-footer > button').each(function() {
				$(this).prop('disabled', true);
			});
			$('#reloadmodal').find('.modal-header > button').each(function() {
				$(this).prop('disabled', true);
			});
			
			$.get("controller/rankings/reload.php", {
				character: __character,
				server: __server,
				region: __region,
				raid: 8
			}).done(function(data) {
				$('#reloadmodal').modal('hide');
			});
		}
		
		function reloadAll() {
			if(reloading === true)
				return false;
				
			this.queue = [];
			
			for(var i = 0; i < __Members.__members.length; i++) {
				this.queue.push(__Members.__members[i]);
			}
			
			this.reloadC = function() {
				var url = "controller/rankings/reload.php";
				
				if(this.queue.length <= 0)
					return false;
					
				var character = this.queue.shift();
				
				$.get(url, {
					character: character['name'],
					server: character['server'],
					region: character['region'],
					raid: 8
				}).done(function() {
					reloadC();
				}).fail(function() {
					reloadC();
				});
			}
			
			this.reloadC();
		}
		
		var p = {};
	
	</script>
	
</body>

</html>