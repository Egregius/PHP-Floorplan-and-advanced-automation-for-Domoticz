#!/usr/bin/php
<?php
require '/var/www/html/secure/functions.php';
lg('Starting cron10B loop...');
while (1){
	$start = microtime(true);
	include '_cron10B.php';
	$time_elapsed_secs=microtime(true)-$start;
	$sleep=10-$time_elapsed_secs;
	if ($sleep<0) $sleep=0;
	$sleep=round($sleep*1000000);
	usleep($sleep);
}

