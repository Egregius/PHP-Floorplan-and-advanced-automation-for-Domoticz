#!/usr/bin/php
<?php
require '/var/www/html/secure/functions.php';
lg('Starting cron2 loop...');
$d=fetchdata(0,basename(__FILE__).':'.__LINE__);
$time=time();
define('LOOP_START', $time);
$lastfetch=$time-20;
while (1){
	$start=microtime(true);
	$time=time();
	$d['time']=$time;
	$d=fetchdata($lastfetch,basename(__FILE__).':'.__LINE__);
	$lastfetch=$time-20;
	include 'cron2B.php';
	$time_elapsed_secs=microtime(true)-$start;
	$sleep=8-$time_elapsed_secs;
	if ($sleep>0) {
		$sleep=round($sleep*1000000);
		usleep($sleep);
	}
	if ($time%300==0) stoploop();
}

function stoploop() {
    $script = __FILE__;
    if (filemtime(__DIR__ . '/functions.php') > LOOP_START) {
        lg('functions.php gewijzigd → restarting cron2 loop...');
        exec("$script > /dev/null 2>&1 &");
        exit;
    }
    if (filemtime(__DIR__ . '/cron2.php') > LOOP_START) {
        lg('cron2.php gewijzigd → restarting cron2 loop...');
        exec("$script > /dev/null 2>&1 &");
        exit;
    }
}
