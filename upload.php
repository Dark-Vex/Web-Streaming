<?php
//richiedo login
require_once("./include/membersite_config.php");
if(!$fgmembersite->CheckLogin())
{
    $fgmembersite->RedirectToURL("./login.php");
    exit;
}
//original code
require_once('include/classes/VideoConverter.php');
require_once('include/config/config.ini.php');

$errors = array();
$uploadComplete = false;

if (isset($_POST['submit'])) {
	$post = $_POST;
	$file = isset($_FILES['video']) ? $_FILES['video'] : null;
	
	if ($file['name'] == null)
		$errors['video'] = 'you must select a video to upload';
	
	// test Db connection
	if (!dbConnect()) {
		throw new Exception('Database error: ' . mysqli_error());
	}
	
	// if no errors, process that shizzle
	if (count($errors) == 0) {
		
		// instantiate VideoConverter class
		$converter = new VideoConverter($file, $config);
		
		$details = $converter->getDetails();
		
		if ($videoId = insertVideo($details)) {
			
			if ($converter->processVideo()) {
				
				$uploadComplete = true;
				
			} else {
				
				deleteVideoById($videoId);
				
			}
		}
	}
}

$pageTitle = "Upload A Video";

require_once('templates/upload.php');
?>
