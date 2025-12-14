#!/usr/bin/php
<?php
require '/var/www/html/secure/functions.php';
lg('🟢 Starting cron2 loop...');
$time=time();
$d=fetchdata(0,basename(__FILE__).':'.__LINE__);
$lastcheck=$time;
$lastfetch=$time;
define('LOOP_START', $time);
$invalidcounter=0;
$ctx=stream_context_create(array('http'=>array('timeout' =>1.5)));
$devices = [
    101 => 14,
    102 => 22,
    103 => 32,
    104 => 32,
    105 => 32,
    106 => 22,
    107 => 32,
];
$user='cron2B';
$boses=array(
	101=>'Living',
	102=>'102',
	103=>'Boven',
	104=>'Garage',
	105=>'10-Wit',
	106=>'Buiten20',
	107=>'Keuken',
);
while (1) {
    $time = time();
    $d['time'] = $time;
    $d = fetchdata($lastfetch);
    $lastfetch=$time;
    include 'cron2B.php';
    $time_elapsed_secs = microtime(true) - $time;
    $sleep = 10 - $time_elapsed_secs;
    if ($sleep > 0) {
        $sleep = round($sleep * 1000000);
        usleep($sleep);
    }
    
    if ($lastcheck < $time - 300) {
        $lastcheck = $time;
        stoploop();
        $d=fetchdata(0);
    }
}

function stoploop() {
    $script = __FILE__;
    if (filemtime(__DIR__ . '/functions.php') > LOOP_START) {
        lg('🛑 functions.php gewijzigd → restarting cron2 loop...');
        exec("$script > /dev/null 2>&1 &");
        exit;
    }
    if (filemtime(__DIR__ . '/cron2.php') > LOOP_START) {
        lg('🛑 cron2.php gewijzigd → restarting cron2 loop...');
        exec("$script > /dev/null 2>&1 &");
        exit;
    }
}
