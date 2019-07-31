<?php
/*
	additional functions for upload.php, index.php, video.php and processVideo.php
*/

require_once(realpath(dirname(__FILE__)) . '/../config/config.ini.php');

/**
 * Function dbConnect()
 * creates link to mysql database with info from 
 * configuration file
 *
 * @return bool
 */
function dbConnect()
{
	$link = mysql_connect(HOST_NAME, USER_NAME, USER_PASS);
	
	if (!$link) {
		return false;
	}
	
	if (!mysql_select_db(DB_NAME)) {
		mysql_close($link);
		return false;
	}
	
	return true;
}

/**
 * Function videoExists()
 * Checks whether a specific video filename already exists in DB
 * should no longer be needed if files renamed with time() func.
 * unless simultaneous upload occurs
 *
 * @param array $details data returned by VideoConverter::getDetails() method
 * @return bool
 */
function videoExists($details)
{
	$query = sprintf('select * from videos where filename = "%s" and format = "%s"',
		basename($details['outputFile']),
		$details['outputFormat']
	);
	
	$result = mysql_query($query) or die(mysql_error());
	
	return mysql_num_rows($result) > 0;
}

/**
 * Function insertVideo()
 * Inserts video data into db table
 *
 * @param array $details data returned by VideoConverter::getDetails() method
 * @return mysql_insert_id
 */
function insertVideo($details = array())
{
	if (count($details) == 0) {
		return false;
	}
	
	$query = sprintf('insert into videos (filename, title, duration, format, width, height) values ("%s", "%s", "%s", "%s", %s, %s)',
		basename($details['filename']),
		$details['title'],
		$details['duration'],
		$details['format'],
		$details['width'],
		$details['height']
	);
		
	$result = mysql_query($query) or die(mysql_error());
	
	return mysql_insert_id();
}

/**
 * Function getVideos()
 * Selects multiple videos from table, can limit results with optional $where
 *
 * @param array $where optional array containing column/value pairs
 * @return array $items result set from db
 */
function getVideos($where = array())
{
	$query = 'select * from videos ';
	
	if (count($where) > 0) {
		
		$i = 0;
		foreach ($where as $column => $value) {
			
			if ($i == 0) {
				$query .= sprintf('where %s = "%s" ', $column, $value);
			} else {
				$query .= sprintf('and %s = "%s" ', $column, $value);
			}
			
			$i++;
		}
	}	
	
	$query .= 'order by ts_uploaded DESC';
	
	$result = mysql_query($query);
	
	// format the return data, allows us to create key for thumbname
	$items = array();
	while ($row = mysql_fetch_object($result)) {
		$items[$row->id]['id'] 			= $row->id;
		$items[$row->id]['filename'] 	= $row->filename;
		$items[$row->id]['status'] 		= $row->status;
		$items[$row->id]['format'] 		= $row->format;
		$items[$row->id]['title'] 		= $row->title;
		$items[$row->id]['duration'] 	= $row->duration;
		$items[$row->id]['thumbnail'] 	= getThumbname($row->filename);
		$items[$row->id]['width'] 		= $row->width;
		$items[$row->id]['height'] 		= $row->height;
	}
		
	return $items;
}

/**
 * Function getThumbname()
 * returns video thumbnail filename
 *
 * @param string $filename 
 * @return string 
 */
function getThumbname($filename)
{
	return getBasename($filename) . '.jpg';
}

/**
 * Function getBasename()
 * strips extension from filename
 *
 * @param string $filename 
 * @return string
 */
function getBasename($filename)
{
	return substr($filename, 0, strrpos($filename, '.'));
}

/**
 * Function getVideoById()
 * retrieves video data by reference id
 *
 * @param string $id 
 * @return array result row
 */
function getVideoById($id)
{
	$query = sprintf('select * from videos where id = %d', $id);
	
	$result = mysql_query($query);
	
	return mysql_fetch_assoc($result);
}

/**
 * Function deleteVideoById()
 * deletes video data from db by id
 *
 * @param string $id 
 * @return bool success of query
 */
function deleteVideoById($id)
{
	$query = sprintf('delete from videos where id = "%s" limit 1',
		$id
	);
	
	$result = mysql_query($query);
	
	return mysql_affected_rows() > 0;
}

/**
 * Function updateVideo()
 * updates a videos data by referencing the id
 * only key value fields supplied by $update array will be updated
 *
 * @param array $update 
 * @return bool 
 */
function updateVideo($update = array()) 
{
	
	if (count($update) == 0) {
		return false;
	}
	
	$updates = array();
	
	$query = 'update videos set ';
	
	foreach ($update as $key => $value) {
		if ($key != 'id') {
			$updates[] = sprintf('%s = "%s" ', $key, $value);
			// $query .= implode(', ', $updates);
		}
	}
	
	$query .= implode(', ', $updates);
	$query .= sprintf(' where id = "%s"', $update['id']);
	$query .= ' limit 1';
	
	$result = mysql_query($query);
	
	return mysql_affected_rows() > 0;
}

/**
 * Function buildFfmpegCommand()
 * builds FFmpeg command line command for video conversion to either
 * mp4 or flv format
 *
 * @param array $config configuration data from include/config/config.ini.php
 * @param array $video video result from db
 * @return string FFmpeg command
 */
function buildFfmpegCommand($config = array(), $video = array())
{
	if (count($video) == 0 || count($config) == 0) {
		return false;
	}
	
	if ($video['format'] == 'flv') {
		
		$command = FFMPEG_BINARY . " -y -i " . $config['uploadPath'] . getBasename($video['filename']) . '.* ';
		$command .= "-s " . $video['width'] . "x" . $video['height'] . " ";
		$command .= " -sameq -ab " . $config['bitRate'];
		$command .= " -ar " . $config['sampleRate'];
		$command .= " -f flv " . $config['outputPath'] . $video['filename'] . ' ';
		$command .= '> ' . $config['conversionLog'] . ' 2>&1';
		
	} else if ($video['format'] == 'mp4') {
		
		$command = FFMPEG_BINARY . " -y -i ". $config['uploadPath'] . getBasename($video['filename']) . '.* ';
		$command .= "-acodec libfaac -ab " . $config['bitRate'] . " ";
		$command .= "-s " . $video['width'] . "x" . $video['height'] . " ";
		$command .= "-vcodec libx264 -vpre fast -crf 22 -threads 0 -g 15 ";
		$command .= $config['outputPath'] . "temp-" . $video['filename'] . " ";
		$command .= "> " . $config['conversionLog'] . " 2>&1";
		
	}
	
	return $command;	
}

/**
 * Function buildQtFaststartCommand()
 * Builds "qt-faststart" command for mooving "moov atom" meta data from
 * end of mp4 file to the front of mp4 file for progressive downloading (streaming)
 *
 * @param array $config 
 * @param array $video 
 * @return string qt-faststart command
 */
function buildQtFaststartCommand($config = array(), $video = array())
{
	if (count($video) == 0 || count($config) == 0) {
		return false;
	}
	
	return QT_FASTSTART_BINARY . ' ' . $config['outputPath'] . "temp-" . $video['filename'] 
		. ' ' . $config['outputPath'] . $video['filename'];
}

/**
 * Function build Flvtool2Command()
 * creates and returns command for adding meta data to 'flv' files
 * with flvtool2
 *
 * @param array $config 
 * @param array $video 
 * @return string flvtool2 command
 */
function buildFlvtool2Command($config = array(), $video = array())
{
	if (count($video) == 0 || count($config) == 0) {
		return false;
	}
	
	return FLVTOOL2_BINARY . ' -U ' . $config['outputPath'] . $video['filename'];
}
?>