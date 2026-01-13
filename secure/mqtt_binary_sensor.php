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
ini_set('error_reporting',E_ALL);
ini_set('display_errors',true);
// Using https://github.com/php-mqtt/client
use PhpMqtt\Client\MqttClient;
use PhpMqtt\Client\ConnectionSettings;
require_once '/var/www/vendor/autoload.php';
require '/var/www/html/secure/functions.php';
$user='BINARY';
lg('🟢 Starting '.$user.' loop ',-1);
$time=time();
$lastcheck=$time;
$t = null;
$weekend = null;
$dow = null;
$startloop=time();
define('LOOP_START', $startloop);
$lastEvent=$startloop;
$connectionSettings=(new ConnectionSettings)
	->setUsername('mqtt')
	->setPassword('mqtt');
$mqtt=new MqttClient('192.168.2.22',1883,basename(__FILE__) . '_' . getmypid().VERSIE,MqttClient::MQTT_3_1);
$mqtt->connect($connectionSettings,true);
$alreadyProcessed=[];
$validDevices = [];
foreach (glob('/var/www/html/secure/pass2php/*.php') as $file) {
	$basename = basename($file, '.php');
	$validDevices[$basename] = true;
}
$d=fetchdata();
$d['rand']=rand(100,200);
updateWekker($t, $weekend, $dow, $d);
$mqtt->subscribe('homeassistant/binary_sensor/+/state', function (string $topic, string $status) use ($startloop, $validDevices, &$d, &$alreadyProcessed, &$t, &$weekend, &$dow, &$lastcheck, &$time, $user) {
	try {
		$path = explode('/', $topic);
		$device = $path[2];
		if (isset($validDevices[$device])) {
			$time=time();
			if (($time - LOOP_START) <= 2) return;
			$d['time']=$time;
			if (($time - $startloop) <= 2) return;
			if ($status=='unavailable') return;
			$status = ucfirst(strtolower(trim($status, '"')));
//			$d = fetchdata();
			if ($device === 'achterdeur') {
				if ($status=='Off') $status='Open';
				elseif ($status=='On') $status='Closed';
				else unset($status);
			} elseif (isset($d[$device]['d']) && $d[$device]['d'] === 'c') {
				if ($status=='On') $status='Open';
				elseif ($status=='Off') $status='Closed';
				else unset($status);
			} elseif ($device=='pirgarage') {
				if ($status=='Off'&&$d['pirgarage2']['s']=='On') $status='On';
			} elseif ($device=='pirgarage2') {
				if($d[$device]['s']!=$status) store($device, $status);
				if ($status=='Off'&&$d['pirgarage']['s']=='On') $status='On';
				$device='pirgarage';
			}
			if (isset($status)&&$d[$device]['s']!=$status) {
//				lg('ⓗ		'.str_pad($user??'', 9, ' ', STR_PAD_RIGHT).' '.str_pad($device, 13, ' ', STR_PAD_RIGHT).' '.$status);
				include '/var/www/html/secure/pass2php/' . $device . '.php';
				store($device, $status);
			}
		}
	} catch (Throwable $e) {
		lg("Fout in MQTT {$user}: " . __LINE__ . ' ' . $topic . ' ' . $e->getMessage());
	}
	if ($lastcheck < $time - $d['rand']) {
        $lastcheck = $time;
        stoploop();
        updateWekker($t, $weekend, $dow, $d);
    }
}, MqttClient::QOS_AT_LEAST_ONCE);

$mqtt->subscribe('d/#', function (string $topic, string $status) use (&$d,$user) {
    $path = explode('/', $topic, 3);
    $n = $path[1];
    if ($n === 'e') {
        $d[$path[2]] = $status;
    } elseif ($n !== 't') {
//    	lgmqtt("🔙 {$user}	{$n}	{$status}");
        $status = json_decode($status);
        foreach (['s', 't', 'm', 'i'] as $key) {
            if (isset($status->{$key})) $d[$n][$key] = $status->{$key};
        }
        if (isset($status->p)) $d[$n]['p'] = $status->p;
    }
}, MqttClient::QOS_AT_LEAST_ONCE);

while (true) {
	$mqtt->loop(true,false,null,10000);
}

$mqtt->disconnect();
lg("🛑 MQTT {$user} loop stopped ".__FILE__,1);

function isProcessed(string $topic,string $status,array &$alreadyProcessed): bool {
	if (isset($alreadyProcessed[$topic]) && $alreadyProcessed[$topic] === $status) return true;
	$alreadyProcessed[$topic]=$status;
	return false;
}

function stoploop() {
    global $mqtt,$lock_file;
    $script = __FILE__;
    if (filemtime(__DIR__ . '/functions.php') > LOOP_START) {
        lg('🛑 functions.php gewijzigd → restarting '.basename($script).' loop...');
        $mqtt->disconnect();
        ftruncate($lock_file, 0);
		flock($lock_file, LOCK_UN);
		exec("nice -n 5 /usr/bin/php $script > /dev/null 2>&1 &");
        exit;
    }
    if (filemtime($script) > LOOP_START) {
        lg('🛑 '.basename($script) . ' gewijzigd → restarting ...');
        $mqtt->disconnect();
        ftruncate($lock_file, 0);
		flock($lock_file, LOCK_UN);
		exec("nice -n 5 /usr/bin/php $script > /dev/null 2>&1 &");
        exit;
    }
}