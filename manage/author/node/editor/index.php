<?php
	
	// $guide
	$title = $guide['name'] . " <small>von " . $guide['author'] . "</small>";
	
?>

<!DOCS>

<html>
	<head>
		<link href="author/css/styles.css" rel="stylesheet" type="text/css">
	</head>
	
	<body>
	
		<div class="row">
		
			<div class="card">
		
				<h1><?php echo isset($title) ? $title : ""; ?></h1>

				<div id="preview" class="container-fluid" style="padding: 25px; border:1px solid #ccc;"></div>
				
				<h1>Editor <button class="btn btn-success pull-right" onclick="save();"><i class="fa fa-file-text"></i>Preview</button></h1>
				<div id="editor" align="center">
				
					<div id="toolbox" style="padding: 10px;">
					
						<div class="btn-group">
							
							<button class="btn btn-default btn-lg" id="tool-title" title="Title"><i class="fa fa-header"></i></button>
							<button class="btn btn-default btn-lg" id="tool-icon" title="Icons"><i class="fa fa-smile-o"></i></button>
							<button class="btn btn-default btn-lg" id="tool-list" title="Unordered List"><i class="fa fa-list"></i></button>
							<button class="btn btn-default btn-lg" id="tool-list-ordered" title="Ordered List"><i class="fa fa-list-ol"></i></button>
						
						</div>
						
						<div class="btn-group">
						
							<button class="btn btn-default btn-lg" id="tool-size" title="Text Size"><i class="fa fa-text-height"></i></button>
							<button class="btn btn-default btn-lg" id="tool-bold" title="Bold"><i class="fa fa-bold"></i></button>
							<button class="btn btn-default btn-lg" id="tool-italic" title="Italic"><i class="fa fa-italic"></i></button>
							<button class="btn btn-default btn-lg" id="tool-underline" title="Underline"><i class="fa fa-underline"></i></button>
							<button class="btn btn-default btn-lg" id="tool-chapter" title="Chapter"><i class="fa fa-paragraph"></i></button>
							<button class="btn btn-default btn-lg" id="tool-subchapter" title="SubChapter"><i class="fa fa-paragraph"></i><span style="vertical-align: super; font-size: 8px;">2</span></button>
							<button class="btn btn-default btn-lg" id="tool-subsubchapter" title="SubSubChapter"><i class="fa fa-paragraph"></i><span style="vertical-align: super; font-size: 8px;">3</span></button>
						
						</div>
						
						<div class="btn-group">
						
							<button class="btn btn-default btn-lg" id="tool-code" title="Code"><i class="fa fa-code"></i></button>
							<button class="btn btn-default btn-lg" id="tool-block" title="Block"><i class="fa fa-square-o"></i></button>
							<button class="btn btn-default btn-lg" id="tool-img" title="Image"><i class="fa fa-picture-o"></i></button>
							<button class="btn btn-default btn-lg" id="tool-video" title="Video"><i class="fa fa-youtube"></i></button>
							<button class="btn btn-default btn-lg" id="tool-link" title="Link"><i class="fa fa-link"></i></button>
							<button class="btn btn-default btn-lg" id="tool-quote" title="Quote"><i class="fa fa-quote-left"></i></button>
							<button class="btn btn-default btn-lg" id="tool-alert" title="Alert"><i class="fa fa-exclamation-triangle"></i></button>
							<button class="btn btn-default btn-lg" id="tool-table" title="Table"><i class="fa fa-table"></i></button>
							<button class="btn btn-default btn-lg" id="tool-panel" title="Panel"><i class="fa fa-list-alt"></i></button>
							<button class="btn btn-default btn-lg" id="tool-columns" title="Column Layout"><i class="fa fa-columns"></i></button>
							<button class="btn btn-default btn-lg" id="tool-rows" title="Row Layout"><i class="fa fa-bars"></i></button>
							<button class="btn btn-default btn-lg" id="tool-tabs" title="Tabs"><i class="fa fa-th-large"></i></button>
						
						</div>
					
					</div>
				
					<textarea id="codearea" class="form-control" style="resize:none; min-height:500px;"></textarea>
				
				</div>
				
			</div>
		
		</div>

	</body>
	<script>
		var tag = "<?php echo $tag; ?>";
		
		function load(file) {
			$.get("author/node/temp/" + file, {
				cache: false
			}).done(function(data) {
				$("#codearea").html(data);
			});
		}
		
		function save() {
			console.log($("#codearea").val());
			$.post("author/node/save.php", { 
				data: $("#codearea").val(),
				tag: tag
			}).done(function(data) {
				var html = $($.parseHTML(data));
				$("#preview").html(html);
				$WowheadPower.refreshLinks();
			});
		}
		
		$("#toolbox").on("click", "button", function() { 
			switch($(this).attr("id")) {
			case "tool-title":
				insert("[title]", "[/title]");
				break;
			case "tool-icon":
				insert("[icon=]", "[/icon]");
				break;
			case "tool-list":
				insert("[list]\n[*]", "\n[/list]");
				break;
			case "tool-list-ordered":
				insert("[list=numeric]\n[*]", "\n[/list]");
				break;
			case "tool-size":
				insert("[size=]", "[/size]");
				break;
			case "tool-bold":
				insert("[b]", "[/b]");
				break;
			case "tool-italic":
				insert("[i]", "[/i]");
				break;
			case "tool-underline":
				insert("[u]", "[/u]");
				break;
			case "tool-code":
				insert("[code]", "[/code]");
				break;
			case "tool-block":
				insert("[important]", "[/important]");
				break;
			case "tool-img":
				insert("[img=]", "[/img]");
				break;
			case "tool-video":
				insert("[video=]", "[/video]");
				break;
			case "tool-link":
				insert("[url,uri=]", "[/url]");
				break;
			case "tool-quote":
				insert("[quote=]", "[/quote]");
				break;
			case "tool-alert":
				insert("[alert=]", "[/alert]");
				break;
			case "tool-table":
				insert("[list=table,title=;]\n[*]", "\n[/list]");
				break;
			case "tool-panel":
				insert("[panel=]", "[/panel]");
				break;
			case "tool-columns":
				insert("[list=column]", "[/list]");
				break;
			case "tool-rows":
				insert("[list=row]\n[*]", "\n[/list]");
				break;
			case "tool-tabs":
				insert("[list=tab]\n[*,title=]", "\n[/list]");
				break;
			case "tool-chapter":
				insert("[chapter]", "[/chapter]");
				break;
			case "tool-subchapter":
				insert("[subchapter]", "[/subchapter]");
				break;
			case "tool-subsubchapter":
				insert("[subsubchapter]", "[/subsubchapter]");
				break;
			default:
			}
		});
		
		function insert(aTag, eTag) {
		
			var input = document.getElementById('codearea');
			input.focus();
			
			/* für Internet Explorer */
			if(typeof document.selection != 'undefined') {
				/* Einfügen des Formatierungscodes */
				var range = document.selection.createRange();
				var insText = range.text;
				range.text = aTag + insText + eTag;
				/* Anpassen der Cursorposition */
				range = document.selection.createRange();
				if (insText.length == 0) {
				  range.move('character', -eTag.length);
				} else {
				  range.moveStart('character', aTag.length + insText.length + eTag.length);      
				}
				range.select();
			}
		 
			/* für neuere auf Gecko basierende Browser */
			else if(typeof input.selectionStart != 'undefined') {
				/* Einfügen des Formatierungscodes */
				var start = input.selectionStart;
				var end = input.selectionEnd;
				var insText = input.value.substring(start, end);
				input.value = input.value.substr(0, start) + aTag + insText + eTag + input.value.substr(end);
				/* Anpassen der Cursorposition */
				var pos;
				if (insText.length == 0) {
				  pos = start + aTag.length;
				} else {
				  pos = start + aTag.length + insText.length + eTag.length;
				}
				input.selectionStart = pos;
				input.selectionEnd = pos;
			}
			
			/* für die übrigen Browser */
			else {
				/* Abfrage der Einfügeposition */
				var pos;
				var re = new RegExp('^[0-9]{0,3}$');
				while(!re.test(pos)) {
				  pos = prompt("Einfügen an Position (0.." + input.value.length + "):", "0");
				}
				if(pos > input.value.length) {
				  pos = input.value.length;
				}
				/* Einfügen des Formatierungscodes */
				var insText = prompt("Bitte geben Sie den zu formatierenden Text ein:");
				input.value = input.value.substr(0, pos) + aTag + insText + eTag + input.value.substr(pos);
			}
		}
	</script>

	<script>
		$(document).ready(function() {
			if( tag.length && tag ) {
				load(tag + ".gfl");
			}
		});
	</script>
	
</html>