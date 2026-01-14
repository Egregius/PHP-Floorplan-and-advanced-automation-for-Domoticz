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
$user='MEDIA';
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
$validDevices = [];
foreach (glob('/var/www/html/secure/pass2php/*.php') as $file) {
	$basename = basename($file, '.php');
	$validDevices[$basename] = true;
}
$d=fetchdata();
$d['rand']=rand(100,200);
updateWekker($t, $weekend, $dow, $d);
$mqtt->subscribe('homeassistant/media_player/+/state',function (string $topic,string $status) use ($startloop,$validDevices,&$d, &$lastcheck, &$time, $user) {
	try {	
		$path=explode('/',$topic);
		$device=$path[2];
		if (isset($validDevices[$device])) {
			$time=time();
			if (($time - LOOP_START) <= 2) return;
			$d['time']=$time;
//			$d=fetchdata();
			$status = ucfirst(strtolower($status));
			if ($d[$device]['s']!=$status) {
	//			lg('mqtt '.__LINE__.' |media |state |'.$device.'|'.$status.'|');
				include '/var/www/html/secure/pass2php/'.$device.'.php';
				store($device,$status);
			}
		}
	} catch (Throwable $e) {
		lg("Fout in {$user}: ".__LINE__.' '.$topic.' '.$e->getMessage());
	}
	if ($lastcheck < $time - $d['rand']) {
        $lastcheck = $time;
        stoploop();
        updateWekker($t, $weekend, $dow, $d);
    }
},MqttClient::QOS_AT_LEAST_ONCE);


$mqtt->subscribe('homeassistant/media_player/+/source',function (string $topic,string $status) use ($startloop,$validDevices,&$d, &$lastcheck, $user) {
	try {	
		$path=explode('/',$topic);
		$device=$path[2];
		if ($device=='nvidia') {
			$time=time();
			$d['time']=$time;
//			$d=fetchdata();
			$status = ucfirst(strtolower(trim($status, '"')));
			if ($d[$device]['m']!=$status) {
				storemode($device,$status);
			}
		}
	} catch (Throwable $e) {
		lg("Fout in MQTT {$user}: " . __LINE__ . ' ' . $topic . ' ' . $e->getMessage());
	}
},MqttClient::QOS_AT_LEAST_ONCE);

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

$mqtt->publish(
    'homeassistant/sensor/kodi/last_action/config',
    json_encode([
        'name'        => 'Kodi Last Action',
        'state_topic' => 'kodi/last_action',
        'unique_id'   => 'kodi_last_action',
    ], JSON_UNESCAPED_SLASHES),
    0,
    true
);
while (true) {
	$mqtt->loop(true,false,null,10000);
}
$mqtt->disconnect();
lg("🛑 MQTT {$user} loop stopped ".__FILE__,1);

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