#!/usr/bin/php
<?php
require '/var/www/html/secure/functions.php';
lg('Starting CRON loop...',9);
$t = null;
$weekend = null;

$d=fetchdata(0,basename(__FILE__).':'.__LINE__);
$time=time();
define('LOOP_START', $time);

$lastfetch=$time-20;
$user='CRONstart';
$items=array('badkamervuur2','badkamervuur1','water');
foreach ($items as $i) {
	sw($i, 'Off', basename(__FILE__).':'.__LINE__,true);
}
if ($d['weg']['s']>0) {
	$items=array('boseliving','bosekeuken','ipaddock','mac','media','zetel');
	foreach ($items as $i) {
		sw($i, 'Off', basename(__FILE__).':'.__LINE__,true);
	}
}

$last10 = $last60 = $last120 = $last180 = $last240 = $last300 = $last3600 = $last450 = $last100 = 0;

while (true) {
	$start = microtime(true);
	$time = time();
	if ((($time % 10 === 0) && $last10 !== $time) || $last10 <= $time - 10) {
		$last10 = $time;
		$d = fetchdata($lastfetch, basename(__FILE__).':'.__LINE__);
		$d['time'] = $time;
		$lastfetch = $time - 20;
		include '_cron10.php';
		updateWekker($t, $weekend);
		if ($time % 20 === 0) {
			$user='heating';
			if ($d['heating']['s']==-2) include '_TC_cooling_airco.php';
			elseif ($d['heating']['s']==-1) include '_TC_cooling_passive.php';
			elseif ($d['heating']['s']==0) include '_TC_neutral.php';
			elseif ($d['heating']['s']>0) include '_TC_heating.php';
		}
	}
	if (checkInterval($last60, 60, $time)) include '_cron60.php';
	if (checkInterval($last120, 120, $time)) include '_cron120.php';
	if (checkInterval($last180, 180, $time)) include '_cron180.php';
	if (checkInterval($last240, 240, $time)) include '_cron240.php';
	if (checkInterval($last300, 300, $time)) include '_cron300.php';
	if (checkInterval($last3600, 3600, $time)) include '_cron3600.php';
	if (checkInterval($last450, 450, $time)) include '_cron450.php';
	if (checkInterval($last100, 100, $time)) include '_weather.php';
	stoploop($d);
	$nextSecond = ceil(microtime(true));
	$sleep = $nextSecond - microtime(true);
	if ($sleep > 0) usleep((int)($sleep * 1000000));
}

function checkInterval(&$last, $interval, $time) {
    if (($time % $interval === 0 && $last !== $time) || $last <= $time - $interval) {
        $last = $time;
        return true;
    }
    return false;
}

function stoploop($d) {
	global $db;
	$script = __FILE__;
	if (filemtime(__DIR__ . '/functions.php') > LOOP_START) {
		lg('functions.php gewijzigd → restarting cron loop...');
		exec("$script > /dev/null 2>&1 &");
		exit;
	}
	if (filemtime(__DIR__ . '/cron.php') > LOOP_START) {
		lg('cron.php gewijzigd → restarting cron loop...');
		exec("$script > /dev/null 2>&1 &");
		exit;
	}
	if ($d['weg']['m']==2) {
		lg('Stopping CRON Loop...');
		$db->query("UPDATE devices SET m=0 WHERE n ='weg';");
		exit('Stop');
	}
}