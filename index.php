<?php
require_once('include/config/config.ini.php');

if (!dbConnect()) {
	echo mysql_error();
}

// un-comment if you dont want to show videos that haven't finished processing
// $where = array(
// 	'status' => 'finished'
// );

$videos = getVideos(/*$where*/);

$pageTitle = 'WSP Web Streaming';

require_once('templates/index.php');
?>
