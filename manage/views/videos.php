<!DOCTYPE>
<html>

<head>
	<title></title>
</head>

<body>
	
	<div class="row">
	
		<div class="card">
		
			<form id="myform" class="form form-inline" role="form" onsubmit="__Videos.add(this); return false;">
			
				<div class="pull-right">
					<input class="form-control" name="yt" id="ytid" placeholder="Youtube-ID" required />
					<button type="submit" id="form-submit" hidden></button>
					<button type="button" class="btn btn-success" onclick="$('#form-submit').click();"><i class='fa fa-youtube fa-fw'></i> Neues Video</button>
				</div>
			
			</form>
				
			<table class="table table-striped">
				
				<thead>
					<th>Name</th>
					<th>Tag</th>
					<th>Letzte Aktualisierung</th>
					<th></th>
					<th></th>
				</thead>
				
				<tbody id="table-videos">
				</tbody>
				
			</table>
		
		</div>
	
	</div>
	
	<script>
	
		var __Videos = {
		
			__anchor: '#table-videos',
			__videos: [],
			__reloading: false,
		
			print: function(videos) {
				$(this.__anchor).empty();
				
				for(var i = 0; i < videos.length; i++) {
					var video = videos[i];
					var tr = $('<tr></tr>');
					
					tr.append('<td>' + (video.pending ? '<i class="fa fa-eye-slash fa-fw"></i>' : '<i class="fa fa-eye fa-fw"></i>') + video.title + '</td>');
					
					tr.append('<td>' + video.tag + '</td>');
					
					tr.append('<td>' + video.update_date + '</td>');
					
					if( video.pending ) {
						tr.append('<td><button type="button" class="btn btn-xs btn-warning" title="publizieren" onclick="__Videos.publish(\'' + video.tag + '\')"><i class="fa fa-globe fa-fw"></i></button></td>');
					} else {
						tr.append('<td><button type="button" class="btn btn-xs btn-danger" title="widerrufen" onclick="__Videos.unpublish(\'' + video.tag + '\')"><i class="fa fa-archive fa-fw"></i></button></td>');
					}
					
					tr.append('<td><button type="button" class="btn btn-xs btn-danger" onclick="__Videos.remove(\'' + video.tag + '\');"><i class="fa fa-times fa-fw"></i></button></td>');

					$(this.__anchor).append(tr);
				}
			},
			
			refresh: function() {
				if( $.type(this.__videos) === "array" && this.__videos.length > 0 ) {
					this.print(this.__videos);
				}
			},
			
			load: function() {
				var __this = this;
				
				webi.loadJSON("controller/videos/index.php").done(function(data) {
					if(data.status == 0) {
						data = data.msg;
					}
					
					__this.__videos = data.videos;
					__this.refresh();
				});
			},
			
			add: function() {
				var __form = $(arguments[0]);
				var __this = this;
				
				$.getJSON("controller/video/create.php?" + __form.serialize()).done(function(data) {
					if( data.status == 0) {
						__this.load();
						$Toasts.success("Erfolgreich!");
						$('#ytid').val('');
					}
				}).fail(function() {
					$Toasts.alert("Fehlgeschlagen!");
				});
			},
			
			remove: function() {
				if(arguments.length !== 1)
					return;
				
				if( !confirm("Wollen sie dieses Video wirklich löschen?") )
					return;
				
				var __this = this;
				
				$.getJSON("controller/video/delete.php?tag="+arguments[0]).done(function(data) {
					if( data.status == 0) {
						__this.load();
						$Toasts.success("Erfolgreich!");
					}
				}).fail(function() {
					$Toasts.alert("Fehlgeschlagen!");
				});
			},
			
			publish: function(tag) {
				var __this = this;
				
				$.getJSON("controller/video/publish.php?tag=" + encodeURIComponent(tag)).done(function(data) {
					if( data.status == 0) {
						__this.load();
						$Toasts.success("Erfolgreich!");
					}
				}).fail(function() {
					$Toasts.alert("Fehlgeschlagen!");
				});
			},
			
			unpublish: function(tag) {
				var __this = this;
				
				$.getJSON("controller/video/unpublish.php?tag=" + encodeURIComponent(tag)).done(function(data) {
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
			__Videos.load();
		});
	
	</script>
	
</body>

</html>