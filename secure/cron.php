#!/usr/bin/php
<?php
require '/var/www/html/secure/functions.php';
lg('🟢 Starting CRON loop...');
$t = null;
$weekend = null;
$dow = null;
$time=time();
$d=fetchdata(0,basename(__FILE__).':'.__LINE__);
$d['time'] = $time;
define('LOOP_START', $time);
$user='CRONstart';
foreach (['badkamervuur2','badkamervuur1','water'] as $i) {
	sw($i, 'Off', basename(__FILE__).':'.__LINE__,true);
}
if ($d['weg']['s']>0) {
	foreach (['boseliving','bosekeuken','ipaddock','mac','media','zetel'] as $i) sw($i, 'Off', basename(__FILE__).':'.__LINE__,true);
}
$last10= $last60 = $last300 = $last3600 = $last90 = $time-3600;
updateWekker($t, $weekend, $dow, $d);
if (getCache('sunrise')==false) {
	$url = "https://api.sunrise-sunset.org/json?lat=$lat&lng=$lon&formatted=0";
	$response = @file_get_contents($url);
	$data = json_decode($response, true);
	if (isset($data['results'])) {
		$results = $data['results'];
		$CivTwilightStart = isoToLocalTimestamp($results['civil_twilight_begin']);
		$CivTwilightEnd = isoToLocalTimestamp($results['civil_twilight_end']);
		$Sunrise = isoToLocalTimestamp($results['sunrise']);
		$Sunset = isoToLocalTimestamp($results['sunset']);
		setCache('sunrise', json_encode(array(
			'CivTwilightStart' => date('G:i', $CivTwilightStart),
			'CivTwilightEnd' => date('G:i', $CivTwilightEnd),
			'Sunrise' => date('G:i', $Sunrise),
			'Sunset' => date('G:i', $Sunset),
		)));
	}
}


while (true) {
	$time = microtime(true);
	$d['time'] = $time;
	$timeint = (int)$time;
	if ($timeint % 10 === 0 && $timeint !== $last10) {
		$last10 = $timeint;
		$d = fetchdata($timeint - 60, basename(__FILE__).':'.__LINE__);
		$d['time'] = $time;
		include '_cron10.php';

		$user = 'HEATING';
		if ($d['heating']['s'] == -2) include '_TC_cooling_airco.php';
		elseif ($d['heating']['s'] == -1) include '_TC_cooling_passive.php';
		elseif ($d['heating']['s'] == 0) include '_TC_neutral.php';
		elseif ($d['heating']['s'] > 0)  include '_TC_heating.php';
	}
	if (checkInterval($last60, 60, $timeint)) include '_cron60.php' ;
	if (checkInterval($last300, 300, $timeint)) {include '_cron300.php';stoploop($d);updateWekker($t, $weekend, $dow, $d);}
	if (checkInterval($last3600, 3600, $timeint)) include '_cron3600.php';
	if (checkInterval($last90, 90, $timeint)) include '_weather.php';
	
	$next = floor($time / 10) * 10 + 10;
	$sleep = $next - microtime(true);
	$sleep = (int)round($sleep * 1e6)-1800;
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
		lg('🛑 functions.php gewijzigd → restarting cron loop...');
		exec("$script > /dev/null 2>&1 &");
		exit;
	}
	if (filemtime(__DIR__ . '/cron.php') > LOOP_START) {
		lg('🛑 cron.php gewijzigd → restarting cron loop...');
		exec("$script > /dev/null 2>&1 &");
		exit;
	}
	if ($d['weg']['m']==2) {
		lg('🛑 Stopping CRON Loop...');
		$db->query("UPDATE devices SET m=0 WHERE n ='weg';");
		exit;
	}
}