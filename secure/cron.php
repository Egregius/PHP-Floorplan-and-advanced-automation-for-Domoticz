#!/usr/bin/php
<?php
declare(strict_types=1);
$lock_file = fopen('/run/lock/'.basename(__FILE__).'.pid', 'c');
$got_lock = flock($lock_file, LOCK_EX | LOCK_NB, $wouldblock);
if ($lock_file === false || (!$got_lock && !$wouldblock)) {
    throw new Exception("Unexpected error opening or locking lock file.");
} else if (!$got_lock && $wouldblock) {
    exit("Another instance is already running; terminating.\n");
}
require '/var/www/html/secure/functions.php';
lg('🟢 Starting CRON loop...');
$t = null;
$weekend = null;
$dow = null;
$time=time();
$d=fetchdata();
$d['time'] = $time;
define('LOOP_START', $time);
$user='CRONstart';
// Using https://github.com/php-mqtt/client
use PhpMqtt\Client\MqttClient;
use PhpMqtt\Client\ConnectionSettings;
require '/var/www/vendor/autoload.php';
$connectionSettings=(new ConnectionSettings)
	->setUsername('mqtt')
	->setPassword('mqtt')
	->setKeepAliveInterval(60);
$mqtt=new MqttClient('192.168.2.22',1883,basename(__FILE__),MqttClient::MQTT_3_1,null,null);
$mqtt->connect($connectionSettings,true);
foreach (['badkamervuur2','badkamervuur1','water'] as $i) {
	sw($i,'Off');
}
if ($d['weg']['s']>0) {
	foreach (['boseliving','bosekeuken','ipaddock','mac','media','zetel'] as $i) sw($i, 'Off');
}
$last10 = $last20 = $last60 = $last300 = $last3600 = $last90 = $time-3600;
updateWekker($t, $weekend, $dow, $d);
$url = "https://api.sunrise-sunset.org/json?lat=$lat&lng=$lon&formatted=0";
$response = @file_get_contents($url);
$data = json_decode($response, true);
if (isset($data['results'])) {
	$results = $data['results'];
	$CivTwilightStart = isoToLocalTimestamp($results['civil_twilight_begin']);
	$CivTwilightEnd = isoToLocalTimestamp($results['civil_twilight_end']);
	$Sunrise = isoToLocalTimestamp($results['sunrise']);
	$Sunset = isoToLocalTimestamp($results['sunset']);
	
	publishmqtt('d/Tstart',date('G:i', $CivTwilightStart));
	publishmqtt('d/Srise',date('G:i', $Sunrise));
	publishmqtt('d/Sset',date('G:i', $Sunset));
	publishmqtt('d/Tend',date('G:i', $CivTwilightEnd));
}
$db = Database::getInstance();
//$db->exec("TRUNCATE TABLE devices");
//$db->exec("INSERT INTO devices SELECT * FROM devices_mem");
foreach ($d as $k=>$v) {
	if (isset($v['f'])) {
		unset($v['f']);
		if(!isset($v['rt'])) unset($v['t']);
//		lg ($k.' ==> '.print_r($v,true));
		publishmqtt('d/'.$k,json_encode($v));
	}
}

while (true) {
	$time = time();
	$d['time'] = $time;
	if ($time % 10 === 0 && $time !== $last10) {
		$last10 = $time;
		$d = fetchdata();
		include '_cron10.php';
	}
	if ($time % 20 === 0 && $time !== $last20) {
		$user = 'HEATING';
		if ($d['heating']['s'] == -2) include '_TC_cooling_airco.php';
		elseif ($d['heating']['s'] == -1) include '_TC_cooling_passive.php';
		elseif ($d['heating']['s'] == 0) include '_TC_neutral.php';
		elseif ($d['heating']['s'] > 0)  include '_TC_heating.php';
		$mqtt->publish('p', 'p');
	}
	if (checkInterval($last60, 60, $time)) {include '_cron60.php' ;stoploop();}
	if (checkInterval($last300, 300, $time)) {include '_cron300.php';updateWekker($t, $weekend, $dow, $d);}
	if (checkInterval($last3600, 3600, $time)) include '_cron3600.php';
	if (checkInterval($last90, 90, $time)) include '_weather.php';
	
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
function stoploop() {
	global $db,$lock_file;
	$script = __FILE__;
	if (filemtime(__DIR__ . '/functions.php') > LOOP_START) {
		lg('🛑 functions.php gewijzigd → restarting cron loop...');
		ftruncate($lock_file, 0);
		flock($lock_file, LOCK_UN);
		exec("nice -n 15 php $script > /dev/null 2>&1 &");
		exit;
	}
	if (filemtime(__DIR__ . '/cron.php') > LOOP_START) {
		lg('🛑 cron.php gewijzigd → restarting cron loop...');
		ftruncate($lock_file, 0);
		flock($lock_file, LOCK_UN);
		exec("nice -n 15 php $script > /dev/null 2>&1 &");
		exit;
	}
}