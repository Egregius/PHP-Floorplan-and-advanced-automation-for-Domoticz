<?php //voordeur
require('../secure/settings.php');
if($home===true){
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
	$fileContents=file_get_contents("http://192.168.2.11/mjpeg_read.php");
	//$fileContents=@file_get_contents("http://[2a02:1811:c533:8f00::11]/mjpeg_read.php");
	$fileLength=strlen($fileContents);
	echo "Content-Length:".$fileLength."\r\n";
	echo "\r\n";
	echo $fileContents;
	echo "\r\n";
	ob_end_flush();
}