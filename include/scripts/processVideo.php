<?php
/**
 * This script will be called by VideoConverter.php class
 * and ran through command line exec() function. It will be responsible for the
 * actual conversion of video files
 *
 * @author Eric Akkerman
 */


require_once(realpath(dirname(__FILE__)) . '/../config/config.ini.php');
require_once(BASE_PATH . 'include/functions/functions.php');

if (!dbConnect()) {
	echo 'Err establishing connection';
}

// we want to make sure we only process videos that haven't already
// been or are being processed
$where = array(
	'status' => 'queued'
);

$videos = getVideos($where);

foreach($videos as $video) {
	
	// update database to show that these videos are being processed
	$update = array(
		'id' => $video['id'],
		'status' 		=> 'started'
	);
	
	// execute update
	updateVideo($update);
	
	// generate FFmpeg command with video and configuration information
	$command = buildFfmpegCommand($config, $video);
	
	// execute FFmpeg command
	$result = exec($command, $output, $ret_var);
	
	// if command successful (shells return "0" for success), continue
	if ($ret_var == 0) {
		// update to show converted, awaiting meta-data
		$update = array(
			'id' 		=> $video['id'],
			'status' 	=> 'converted',
		);
		// execute update
		updateVideo($update);
		
		// depending on format switch/type, generate appropriate command
		if ($video['format'] == 'flv') {
			// flvtool2 will add appropriate meta data to our flv video
			$command = buildFlvtool2Command($config, $video);
			
		} else if ($video['format'] == 'mp4') {
			// mp4's need meta data at begining of the file, qt-faststart does this
			$command = buildQtFaststartCommand($config, $video);
			
		}
		
		// execute command 
		$result = exec($command, $output, $ret_var);
		
		// if successfulle
		if ($ret_var == 0) {
			
			// update database to show video conversion and meta data is complete
			$update = array(
				'id' 		=> $video['id'],
				'status' 	=> 'finished',
			);
			// execute db update
			updateVideo($update);
			
			// remove temporary files created (mp4) and the 
			// original uploaded file to save space on HD
			exec('rm ' . $config['outputPath'] . 'temp-' . $video['filename']);
			exec('rm ' . $config['uploadPath'] . getBasename($video['filename']) . '*');
		}
		
	} 
}
// all finished, lets exit!
exit;

?>