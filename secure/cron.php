#!/usr/bin/php
<?php
require '/var/www/html/secure/functions.php';
lg('Starting CRON loop...',9);
$t = null;
$weekend = null;

$d=fetchdata(0,basename(__FILE__).':'.__LINE__);
$time=time();
define('LOOP_START', $time);

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

$last10= $last60 = $last300 = $last3600 = $last90 = $time-60;

while (true) {
    $time = microtime(true);
    $timeint = (int)$time;
    if ($timeint % 10 === 0 && $timeint !== $last10) {
        $last10 = $timeint;
        $d = fetchdata($timeint - 60, basename(__FILE__).':'.__LINE__);
        $d['time'] = $time;
        include '_cron10.php';

        $user = 'heating';
        if ($d['heating']['s'] == -2) include '_TC_cooling_airco.php';
        elseif ($d['heating']['s'] == -1) include '_TC_cooling_passive.php';
        elseif ($d['heating']['s'] == 0)  include '_TC_neutral.php';
        elseif ($d['heating']['s'] > 0)   include '_TC_heating.php';
    }
    if (checkInterval($last60,   60, $timeint)) { include '_cron60.php'; stoploop($d); }
    if (checkInterval($last300, 300, $timeint)) include '_cron300.php';
    if (checkInterval($last3600,3600, $timeint)) {include '_cron3600.php';updateWekker($t, $weekend);}
    if (checkInterval($last90,  90, $timeint)) include '_weather.php';

    $next = floor($time / 10) * 10 + 10;
    $sleep = $next - microtime(true);
    $sleep = (int)round($sleep * 1e6)-1400;
    if ($sleep > 0) usleep($sleep);
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