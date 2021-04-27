<?php
/**
 * Pass2PHP
 * php version 7.3.4-2
 *
 * @category Home_Automation
 * @package  Pass2PHP
 * @author   Guy Verschuere <guy@egregius.be>
 * @license  GNU GPLv3
 * @link	 https://egregius.be
 **/
require '../secure/functions.php';
require '/var/www/authentication.php';
if ($home===true) {
	$ctx=stream_context_create(array('http'=>array('timeout' =>1)));
	$boundary="PIderman";
	header("Content-type: multipart/x-mixed-replace; boundary=$boundary");
	header("Cache-Control: no-cache");
	header("Pragma: no-cache");
	header("Connection: close");
	ob_flush();
	set_time_limit(0);
	ob_start();
	echo "--$boundary\r\n";
	echo "Content-type: image/jpeg\r\n";
	$fileContents=@file_get_contents("http://192.168.2.11/mjpeg_read.php", false, $ctx);
	$fileLength=strlen($fileContents);
	echo "Content-Length:".$fileLength."\r\n";
	echo "\r\n";
	echo $fileContents;
	echo "\r\n";
	ob_end_flush();
}
