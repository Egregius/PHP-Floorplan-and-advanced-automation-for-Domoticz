#!/usr/bin/php
<?php
require '/var/www/html/secure/functions.php';
lg('Starting cron2 loop...');
$d=fetchdata(0,basename(__FILE__).':'.__LINE__);
$time=time();
$lastcheck=$time;
define('LOOP_START', $time);
$invalidcounter=0;
$ctx=stream_context_create(array('http'=>array('timeout' =>1.5)));
while (1){
	$start=microtime(true);
	$d=fetchdata($time-20,basename(__FILE__).':'.__LINE__);
	include 'cron2B.php';
	$time_elapsed_secs=microtime(true)-$start;
	$sleep=10-$time_elapsed_secs;
	if ($sleep>0) {
		$sleep=round($sleep*1000000);
		lg('cron2 sleeping '.round($sleep/1000000,3));
		usleep($sleep);
	}
	$time=time();
	$d['time']=$time;
	if ($lastcheck<$time-300) {
		$lastcheck=$time;
		stoploop();
	}
	
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
