#!/usr/bin/php
<?php
declare(strict_types=1);
ini_set('error_reporting',E_ALL);
ini_set('display_errors',true);
// Using https://github.com/php-mqtt/client
use PhpMqtt\Client\MqttClient;
use PhpMqtt\Client\ConnectionSettings;
require '/var/www/vendor/autoload.php';
require '/var/www/html/secure/functions.php';
$user='BINARY';
lg('🟢 Starting '.$user.' loop ',-1);
$time=time();
$lastcheck=$time;
$t = null;
$weekend = null;
$dow = null;
$d=fetchdata(0,'mqtt_binary:'.__LINE__);
$startloop=microtime(true);
define('LOOP_START', $startloop);
$d['lastfetch']=$startloop;
$d['time']=$startloop;
$d['rand']=rand(10,20);
updateWekker($t, $weekend, $dow, $d);
$lastEvent=$startloop;
$connectionSettings=(new ConnectionSettings)
	->setUsername('mqtt')
	->setPassword('mqtt');
$mqtt=new MqttClient('192.168.2.22',1883,basename(__FILE__),MqttClient::MQTT_3_1,null,null);
$mqtt->connect($connectionSettings,true);
$alreadyProcessed=[];
$validDevices = [];
foreach (glob('/var/www/html/secure/pass2php/*.php') as $file) {
	$basename = basename($file, '.php');
	$validDevices[$basename] = true;
}
$mqtt->subscribe('homeassistant/binary_sensor/+/state', function (string $topic, string $status) use ($startloop, $validDevices, &$d, &$alreadyProcessed, &$t, &$weekend, &$dow, &$lastcheck) {
	try {
		$path = explode('/', $topic);
		$device = $path[2];
		if (isset($validDevices[$device])) {
			$d['time']=microtime(true);
			if (($d['time'] - $startloop) <= 5) return;
			if ($status=='unavailable') return;
			$status = ucfirst(strtolower(trim($status, '"')));
			$d = fetchdata($d['lastfetch'], 'mqtt_binary:' . __LINE__);
			$d['lastfetch'] = $d['time'] - 30;
			if ($device === 'achterdeur') {
				if ($status=='Off') $status='Open';
				elseif ($status=='On') $status='Closed';
				else unset($status);
			} elseif (isset($d[$device]['dt']) && $d[$device]['dt'] === 'c') {
				if ($status=='On') $status='Open';
				elseif ($status=='Off') $status='Closed';
				else unset($status);
			}
			if (isset($status)&&$d[$device]['s']!=$status) {
				include '/var/www/html/secure/pass2php/' . $device . '.php';
				store($device, $status);
			}
		}
	} catch (Throwable $e) {
		lg("Fout in MQTT: " . __LINE__ . ' ' . $topic . ' ' . $e->getMessage());
	}
	if ($lastcheck < $d['time'] - $d['rand']) {
        $lastcheck = $d['time'];
        stoploop();
        updateWekker($t, $weekend, $dow, $d);
    }
}, MqttClient::QOS_AT_LEAST_ONCE);

$sleepMicroseconds=10000;
$maxSleep=100000;
while (true) {
	$result=$mqtt->loop(true);
	if ($result === 0) {
		$sleepMicroseconds=min($sleepMicroseconds + 10000,$maxSleep);
		usleep($sleepMicroseconds);
	} else {
		$sleepMicroseconds=10000;
	}
}

$mqtt->disconnect();
lg('MQTT loop stopped '.__FILE__,1);

function isProcessed(string $topic,string $status,array &$alreadyProcessed): bool {
	if (isset($alreadyProcessed[$topic]) && $alreadyProcessed[$topic] === $status) return true;
	$alreadyProcessed[$topic]=$status;
	return false;
}

function stoploop() {
    global $mqtt;
    $script = __FILE__;
    if (filemtime(__DIR__ . '/functions.php') > LOOP_START) {
        lg('🛑 functions.php gewijzigd → restarting '.basename($script).' loop...');
        $mqtt->disconnect();
        exec("$script > /dev/null 2>&1 &");
        exit;
    }
    if (filemtime($script) > LOOP_START) {
        lg('🛑 '.basename($script) . ' gewijzigd → restarting ...');
        $mqtt->disconnect();
        exec("$script > /dev/null 2>&1 &");
        exit;
    }
}