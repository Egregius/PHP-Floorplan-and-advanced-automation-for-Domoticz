#!/usr/bin/php
<?php
require '/var/www/html/secure/functions.php';
lg('Starting cron2 loop...');
$d=fetchdata(0,basename(__FILE__).':'.__LINE__);
$lastfetch=time();
while (1){
	$start=microtime(true);
	$time=time();
	$d=fetchdata($lastfetch,basename(__FILE__).':'.__LINE__);
	$lastfetch=$time;
	include 'cron2B.php';
	$time_elapsed_secs=microtime(true)-$start;
	$sleep=10-$time_elapsed_secs;
	if ($sleep<0) $sleep=0;
	$sleep=round($sleep*1000000);
	usleep($sleep);
}

