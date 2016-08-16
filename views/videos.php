<html>

<head>
</head>

<body>


<?php
include_once(dirname(__FILE__) . "/../modules/database/database.php");
include_once(dirname(__FILE__) . "/../modules/utils.php");

$db = Database::getInstance();
$videos = $db->get_videos(false);
$db->disconnect();

?>

<div class="row">

	<div class="card">
		
		<div class="container-fluid">
			
			<div class="row">
			<?php
			foreach($videos as $video) {
				$tn=$video['thumbnail'];
				$title=$video['title'];
				$author=$video['author'];
				$owner=$video['owner'];
			?>
				<div class="col-xs-3">
					<div class="video-wrapper">
						<img style="cursor: pointer;" onclick="showmodal('<?php echo $video['ytid']; ?>', '<?php echo $title; ?>');" src="<?php echo $tn; ?>" class="img-responsive video-thumbnail">
						<a style="cursor: pointer;" onclick="showmodal('<?php echo $video['ytid']; ?>', '<?php echo $title; ?>');" title="<?php echo $title; ?>" class="video-title text-ellipsis text-ellipsis-2"><?php echo $title; ?></a>
						<p class="video-author">von <a tab-index="-1" target="_blank" href="<?php echo $owner; ?>" title="<?php echo $author; ?>"><?php echo $author; ?></a></p>
					</div>
				</div>
			<?php
			}
			?>
			</div>
		
		</div>
	
	</div>
	
	<div class="modal fade" id="yt-modal" tabindex="-1" role="dialog">

		<div class="modal-dialog modal-lg">

			<div class="modal-content">

				<button type="button" class="close-overlay close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button
		  
				<div class="modal-body">

					<div class='embed-responsive embed-responsive-16by9'><iframe id="yt-modal-iframe" src='' allowfullscreen></iframe></div>

				</div>

			</div><!-- /.modal-content -->

		</div><!-- /.modal-dialog -->

	</div><!-- /.modal -->

</div>

<script>
	function showmodal(ytid, yttitle) {
		//$('#yt-modal-title').html(yttitle);
		$("#yt-modal-iframe").attr('src', 'https://www.youtube.com/embed/' + ytid);
		$('#yt-modal').modal({ backdrop:'static', keyboard: false });
		$("#yt-modal").on('hidden.bs.modal', function (e) { 
			$("#yt-modal-iframe").attr("src", $("#yt-modal-iframe").attr("src"));
		});
	}
	
	/**
	 * Vertically center Bootstrap 3 modals so they aren't always stuck at the top
	 */
	$(function() {
		function reposition() {
			var modal = $(this),
				dialog = modal.find('.modal-dialog');
			modal.css('display', 'block');
			
			// Dividing by two centers the modal exactly, but dividing by three 
			// or four works better for larger screens.
			dialog.css("margin-top", Math.max(0, ($(window).height() - dialog.height()) / 2));
		}
		// Reposition when a modal is shown
		$('.modal').on('show.bs.modal', reposition);
		// Reposition when the window is resized
		$(window).on('resize', function() {
			$('.modal:visible').each(reposition);
		});
	});
</script>

</body>

</html>