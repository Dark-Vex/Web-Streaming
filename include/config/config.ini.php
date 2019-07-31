<?php
/**
 * This file contains configuration settings for use with the "VideoConverter" class
 * and related scripts.
 *
 * @author Eric Akkerman
 */

// set to allow error messages, comment out for live
ini_set('error_reporting', E_ALL | E_NOTICE | E_STRICT);
ini_set('display_errors', 'On');
ini_set('display_startup_errors', 'On');

// File path and Database settings
define('BASE_PATH', realpath(dirname(__FILE__)) . '/../../');
define('HOST_NAME', 'localhost');
define('USER_NAME', '<db-username>');
define('USER_PASS', '<db-password>');
define('DB_NAME', 'wspdb');

// path to ffmpeg and qt-faststart binary
defined('FFMPEG_BINARY') || define('FFMPEG_BINARY', '/usr/local/bin/ffmpeg');
defined('QT_FASTSTART_BINARY') || define('QT_FASTSTART_BINARY', '/usr/local/bin/qt-faststart');
defined('FLVTOOL2_BINARY') || define('FLVTOOL2_BINARY', '/usr/local/bin/flvtool2');

// require necessary functions for database and conversion scripts
require_once(BASE_PATH . 'include/functions/functions.php');

// configuration options for VideoConverter.php class
$config = array(
	'uploadPath'		=> BASE_PATH . 'data/video/uploads/',
	'outputPath'		=> BASE_PATH . 'data/video/output/',
	'thumbPath'			=> BASE_PATH . 'data/video/thumbnails/',
	'conversionLog'		=> BASE_PATH . 'data/logs/conversionLog.log',
	'errorLog'			=> BASE_PATH . 'data/logs/errorLog.log',
	'conversionScript' 	=> BASE_PATH . 'include/scripts/processVideo.php',
	'outputFormat'		=> 'mp4', // either 'mp4' or 'flv'
	'bitRate'			=> 32000,
	'sampleRate'		=> 22050,
	'videoMaxWidth'		=> 1280,
	'videoMaxHeight' 	=> 720,
	'thumbMaxWidth' 	=> 320,
	'thumbMaxHeight'	=> 240,
	'minDuration'		=> 1,
	'videoThumbDepth'	=> 25, // % into video to get thumbnail
);
