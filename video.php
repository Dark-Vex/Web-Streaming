<?php
require_once('include/config/config.ini.php');

if (!dbConnect()) {
	echo 'Error connecting to DB';
	exit;
}

$videoId = isset($_GET['video']) ? $_GET['video'] : null;

if ($videoId == null) {
	// no video requested
	header('location: index.php');
	exit;
}

$video = getVideoById($videoId);

$pageTitle = 'Watching: ' . $video['title'];

include_once('templates/video-test.php');
?>
