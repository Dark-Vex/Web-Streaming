<?php
include_once('header.php');
?>
<div class="center">
	<h2 class="mainheading">Video List</h2>
</div>

<div id="videoGallery">
	<?php if (count($videos) > 0): ?>
		
	<table>
		<?php 
		$i = 0;
		foreach( $videos as $video):
		
		?>
		
		<?php if ($i == 0):?>
		<tr>
		<?php endif; ?>
		
		<td>
			<div class="videoThumbnail">
				<a href="video.php?video=<?php echo $video['id'];?>">
					<img src="/data/video/thumbnails/<?php echo $video['thumbnail'];?>" 
						title="<?php echo $video['title'];?>" />
				</a>
				
				<div class="videoLinks">
					<a href="video.php?video=<?php echo $video['id'];?>" class="linkButton">
						Length: <?php echo $video['duration'];?>
					</a>  
					<a class="deleteLink linkButton" href="delete.php?video=<?php echo $video['id'];?>">
						Delete
					</a>
				</div>
				
				<?php if ($video['status'] != 'finished'):?>
				<div class="processing">
					<img src="/images/loading.gif" /> <br />
					Video Being Processed
				</div>
				<?php endif;?>
				
			</div>
			
		</td>
		
		<?php $i++; ?>


		<?php if($i % 3 == 0): ?>
		</tr>
		<?php 
		$i = 0; 
		endif; ?>

		
		<?php endforeach; ?>
		

		<?php if ($i != 0):?>
		</tr>
		<?php endif;?>
	</table>
	
	<?php else: ?>
		Sorry, But there are no videos to display!
	<?php endif;?>
</div>
<script>
	document.observe('dom:loaded', function() {
		var deleteLinks = $$('a.deleteLink');
		deleteLinks.each(function(deleteLink) {
			deleteLink.observe('click', function(e) {
				if (!confirm('Do you really want to delete this?')) {
					Event.stop(e);
				}
			})
		});
	});
</script>
<?php include_once('footer.php');?>
