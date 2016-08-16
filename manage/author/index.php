<!DOCTYPE html>

<html>
	<head></head>
	
	<body>
	
		<div class="row">
		
			<div class="card">
		
				<div class="btn-group pull-right">
					<button type="button" class="btn btn-default" onclick="__Guides.openModal();"><i class='fa fa-pencil-square-o fa-fw'></i> Neuer Guide</button>
				</div>

				<table class="table table-striped" role="table">
						
					<thead>
						<tr>
							<th>Name</th>
							<th>Autor</th>
							<th>Pfad</th>
							<th>Letzte Aktualisierung</th>
							<th></th>
							<th></th>
							<th></th>
						</tr>
					</thead>

					<tbody id="guides">
					</tbody>
					
				</table>
				
			</div>
			
			<div id="mymodal" class="modal fade">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-header">
							<h4 class="modal-title">Neuer Guide</h4>
						</div>
						<div class="modal-body" style="height: 220px;">
							
							<form id="myform" class="form" onsubmit="__Guides.create(this); return false;">
							
								<div class="form-group">
									<input class="form-control" name="name" placeholder="Name" required>
								</div>
								
								<div class="form-group">
									<input class="form-control" name="author" placeholder="Autor" required>
								</div>
								
								<div class="form-group">
									<input class="form-control" name="path" placeholder="Pfad">
								</div>
								
							</form>
							
							<div id="modal-status"></div>
							
						</div>
				  
						<div class="modal-footer">
							<button type="button" onclick="$('#myform').submit();" class="btn btn-success">Ok</button>
							<button type="button" class="btn btn-default" data-dismiss="modal">Abbrechen</button>
						</div>
					</div><!-- /.modal-content -->
				</div><!-- /.modal-dialog -->
			</div><!-- /.modal -->
			
		</div>

	</body>
	<script>
		
		var __Guides = {
		
			__guides: {},
			__size: 0,
			
			__modal: null,
			
			load: function() {
				var __this = this;
			
				webi.content.loadJSON($('#guides'), "author/controller/guides/index.php").done(function(data) {
					if(data.status == 0) {
						data = data.msg;
					}
				
					if( $.isPlainObject(data) && data.total > 0 ) {
						__this.__size = data.total;
						__this.__guides = data.guides;
						
						__this.print();
					}
				});
			},
			
			print: function() {
				for(var i = 0; i < this.__guides.length; i++) {
					var guide = this.__guides[i];
					
					var tr = $('<tr></tr>');
					tr.append('<td>' + (guide['pending'] ? '<i class="fa fa-eye-slash fa-fw"></i>' : '<i class="fa fa-eye fa-fw"></i>') + guide['name'] + '</td>');
					tr.append('<td>' + guide['author'] + '</td>');
					tr.append('<td>' + guide['path'] + '</td>');
					tr.append('<td>' + guide['update_date'] + '</td>');
					tr.append('<td><button type="button" class="btn btn-xs btn-primary" title="editieren" onclick="__Guides.edit(\'' + guide['tag'] + '\')"><i class="fa fa-pencil fa-fw"></i></button></td>');
					if( guide['pending'] ) {
						tr.append('<td><button type="button" class="btn btn-xs btn-warning" title="publizieren" onclick="__Guides.publish(\'' + guide['tag'] + '\')"><i class="fa fa-globe fa-fw"></i></button></td>');
					} else {
						tr.append('<td><button type="button" class="btn btn-xs btn-danger" title="widerrufen" onclick="__Guides.unpublish(\'' + guide['tag'] + '\')"><i class="fa fa-archive fa-fw"></i></button></td>');
					}
					tr.append('<td><button type="button" class="btn btn-xs btn-danger" title="löschen" onclick="__Guides.remove(\'' + guide['tag'] + '\')"><i class="fa fa-times fa-fw"></i></button></td>');
					$('#guides').append(tr);
				}
			},
			
			openModal: function() {
				this.__modal = $('#mymodal').modal({
					backdrop: 'static',
					keyboard: false
				});
			},
			
			closeModal: function() {
				if( this.__modal !== null ) {
					this.__modal.modal('hide');
					this.__modal = null;
				}
			},
			
			edit: function(tag) {
				window.location.hash = "node@" + tag;
			},
			
			remove: function(tag) {
				
				if( !confirm("Wollen sie diesen Guide wirklich löschen?") )
					return;

				var __this = this;
				
				$.getJSON("author/controller/guide/delete.php?tag=" + encodeURIComponent(tag)).done(function(data) {
					if( data.status == 0) {
						__this.load();
						$Toasts.success("Erfolgreich!");
					}
				}).fail(function() {
					$Toasts.alert("Fehlgeschlagen!");
				});
			},
			
			create: function() {
				var __form = $(arguments[0]);
				var __this = this;
				
				$.getJSON("author/controller/guide/create.php?" + __form.serialize()).done(function(data) {
					if( data.status == 0) {
						__this.load();
						__this.closeModal();
						$Toasts.success("Erfolgreich!");
					}
				}).fail(function() {
					__this.closeModal();
					$Toasts.alert("Fehlgeschlagen!");
				});
			},
			
			publish: function(tag) {
				var __this = this;
				
				$.getJSON("author/controller/guide/publish.php?tag=" + encodeURIComponent(tag)).done(function(data) {
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
				
				$.getJSON("author/controller/guide/unpublish.php?tag=" + encodeURIComponent(tag)).done(function(data) {
					if( data.status == 0) {
						__this.load();
						$Toasts.success("Erfolgreich!");
					}
				}).fail(function() {
					$Toasts.alert("Fehlgeschlagen!");
				});
			}
		
		
		};
	
	
		$(document).ready(function() {
			__Guides.load();
		})

	</script>
	
</html>