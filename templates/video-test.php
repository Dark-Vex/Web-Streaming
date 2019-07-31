<?php 
include_once('header.php');
?>

<div id="videoWindow">

	<h2 class="videoTitle">Watching Video: <?php echo $video['title']; ?></h2>
    
			<div id="playlist">
            <?php
require_once('include/config/config.ini.php');

if (!dbConnect()) {
	echo mysql_error();
}
$plavideos = getVideos(/*$where*/);

?>
            
<?php if (count($plavideos) > 0): ?>
		<?php 
		$i = 0;
		foreach( $plavideos as $plavideo):
		
		?>
		<?php if ($i == 0):?>
		<?php endif; ?>
			<div class="Thumbnail">
				<a href="video.php?video=<?php echo $plavideo['id'];?>">
					<img src="/data/video/thumbnails/<?php echo $plavideo['thumbnail'];?>" height="50" width"25" /> 
                    <strong><?php echo $plavideo['title'];?>t</strong><br />
                    <em>Length: <?php echo $plavideo['duration'];?></em>
				</a>
			</div>
		
		<?php $i++; ?>


		<?php if($i % 3 == 0): ?>
		<?php 
		$i = 0; 
		endif; ?>

		
		<?php endforeach; ?>
		

		<?php if ($i != 0):?>
		<?php endif;?>
    	<?php endif;?>	
</div>


	<?php if ($video['status'] != 'finished'):?>
		

	<div class="videoThumbnail">
    
		<a href="video.php?video=<?php echo $video['id'];?>">
			<img src="/data/video/thumbnails/<?php echo getThumbname($video['filename']);?>" 
				title="<?php echo $video['title'];?>" />
		</a>
		
		<div class="processing">
			<img src="/images/loading.gif" /> <br />
			Video Still Being Processed
		</div>

	</div>
	
	<?php else: ?>
    
	<div id="player">
	
	</div>
	
	<?php endif;?>
	
	<div class="videoLinks">
		<a href="index.php" class="linkButton">Video Index</a> 
		<a class="deleteLink linkButton" href="delete.php?video=<?php echo $video['id'];?>">Delete Video</a>
	</div>
</div>

<script>
	<?php if ($video['status'] == 'finished'):?>

	$f("player", "/swf/flowplayer-3.2.15.swf", {

		// configure clip to use "pseudostreaming" plugin below for providing video data
		clip: {
			url: '/data/video/output/<?php echo $video['filename'];?>',
			provider: 'pseudostreaming'
		},

		// streaming plugins are configured normally under the plugins node
		plugins: {
			pseudostreaming: {
				url: '/swf/flowplayer.pseudostreaming-3.2.11.swf',
			}

		}
		
	});

	<?php endif;?>
	
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


<?php 
include_once('footer.php');
?>
