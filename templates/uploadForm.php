<?php 
//get unique id
$up_id = uniqid(); 
?>

<!--Progress Bar and iframe Styling-->
<link href="style_progress.css" rel="stylesheet" type="text/css" />

<!--Get jQuery-->
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.0/jquery.js" type="text/javascript"></script>

<!--display bar only if file is chosen-->
<script>

$(document).ready(function() { 
//

//show the progress bar only if a file field was clicked
	var show_bar = 0;
    $('input[type="file"]').click(function(){
		show_bar = 1;
    });

//show iframe on form submit
    $("#form1").submit(function(){

		if (show_bar === 1) { 
			$('#upload_frame').show();
			function set () {
				$('#upload_frame').attr('src','upload_frame.php?up_id=<?php echo $up_id; ?>');
			}
			setTimeout(set);
		}
    });
//

});

</script>

<div>
  <?php if (isset($_GET['success'])) { ?>
  <span class="notice">Your file has been uploaded.</span>
  <?php } ?>
  
  <form action="upload.php" method="post" enctype="multipart/form-data" name="form1" id="form1">

                <div class="formRow">

                        <label for="video">Select a Video to Upload</label>
<!--APC hidden field-->
    <input type="hidden" name="APC_UPLOAD_PROGRESS" id="progress_key" value="<?php echo $up_id; ?>"/>

                        <input id="file" type="file" name="video" />

                        <?php if(isset($errors['video'])):?>
                                <div class="error">
                                        <?php echo $errors['video'];?>
                                </div>
                        <?php endif;?>

                </div>

    <br />
    <iframe id="upload_frame" name="upload_frame" frameborder="0" border="0" src="" scrolling="no" scrollbar="no" > </iframe>
    <br />
        <div class="submit">
                <input type="submit" name="submit" value="Upload Video!" />
        </div>
  </form>
  </div>
