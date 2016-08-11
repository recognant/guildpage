<!DOCTYPE>
<html>

<head>
	<title></title>
</head>

<body>
		
	<div id="mymodal" class="modal fade">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">Neues Mitglied</h4>
				</div>
				<div class="modal-body">
				
					<div class="modal-page">
				
						<form class="form" onsubmit="__Members.add(this); return false;">
							<div class="form-group">
								<input class="form-control" id="input_name" name="character" placeholder="Name" required>
							</div>
							
							<div class="form-group">
								<input class="form-control" id="input_server" name="server" placeholder="Server" required>
							</div>
							
							<div class="form-group">
								<select class="form-control" id="input_region" name="region" placeholder="Region">
									<option value="eu">EU</option>
									<option value="us">US</option>
								</select>
							</div>
							
							<div class="form-group">
								<select class="form-control" id="input_class" name="class">
									<?php 
										include_once(dirname(__FILE__) . "/../../../modules/database/database.php");
										include_once(dirname(__FILE__) . "/../../../modules/utils.php");
										
										$db = Database::getInstance();
										$classes = $db->get_classes();
										
										foreach($classes as $class) {
											echo '<option value="' . $class['id'] . '">' . $class['name'] . '</option>';
										}
									?>
								</select>
							</div>
							
							<button type="submit" id="form-submit" hidden></button>
						</form>
					
					</div>
					
					<div class="modal-page in">
						<div class='center'><i class='fa fa-spinner fa-pulse fa-4x'></i></div>
					</div>
					
					<div class="modal-page in">
						<div class='center'><i class='fa fa-check fa-lg'></i>Erfolgreich</div>
					</div>
					
				</div>
		  
				<div class="modal-footer">
					<button type="button" class="btn btn-success" onclick="$('#form-submit').click();">Erstellen</button>
					<button type="button" class="btn btn-default" data-dismiss="modal">Abbrechen</button>
				</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
	
</body>

</html>