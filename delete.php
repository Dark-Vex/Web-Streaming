<?php
//richiedo login
require_once("./include/membersite_config.php");
if(!$fgmembersite->CheckLogin())
{
    $fgmembersite->RedirectToURL("login.php");
    exit;
}
//original
require_once('include/classes/VideoConverter.php');
require_once('include/config/config.ini.php');

$return = 'index.php';

if (!dbConnect()) {
	header('location: '. $return);
	exit;
}

$videoId = isset($_GET['video']) ? $_GET['video'] : null;

if ($videoId == null) {
	header('location: '. $return);
	exit;
}

$video = getVideoById($videoId);

if ($video != null) {
	if (deleteVideoById($videoId)){
		unlink($config['outputPath'] . $video['filename']);
		unlink($config['thumbPath'] . getThumbName($video['filename']));
	}
}

header('location: '. $return);
exit;
