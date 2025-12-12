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
$user='ZIGBEE';
lg('🟢 Starting '.$user.' loop ',-1);
$time=time();
$lastcheck=$time;
$t = null;
$weekend = null;
$dow = null;
$d=fetchdata(0,'mqtt_zigbee2mqtt');
$startloop=time();
define('LOOP_START', $startloop);
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
$mqtt->subscribe('zigbee2mqtt/+',function (string $topic,string $status) use ($startloop, $validDevices, &$d, /*&$alreadyProcessed, &$lastEvent, */&$t, &$weekend, &$dow, &$lastcheck, &$time) {
	try {
		$path=explode('/',$topic);
		$device=$path[1];
		if (isset($validDevices[$device])) {
			$time=time();
			$d['time']=$time;
			if (($time - $startloop) <= 2) return;
//			if (isset($lastEvent) && ($d['time'] - $lastEvent) < 1) return;
//			$lastEvent = $d['time'];
			$d=fetchdata();
			$status=json_decode($status);
			if (isset($d[$device]['dt'])) {
				$current_device_file = $device;
				if ($d[$device]['dt']=='zbtn') {
					lg('ⓩ ZBTN'.$device.' '.print_r($status,true));
				} elseif ($d[$device]['dt']=='remote') {
					if (isset($status->action)) {
						lg('ⓩ Remote '.$device.' '.print_r($status,true));
						$status=$status->action;
						lg('ⓩ Remote '.$device.' '.$status);
						include '/var/www/html/secure/pass2php/'.$device.'.php';
					}
				} elseif ($d[$device]['dt']=='c') {
					if ($status->contact==1) $status='Closed';
					else $status='Open';
					if ($d[$device]['s']!=$status) {
						lg('ⓩ Contact '.$device.' '.$status);
						store($device,$status);
						include '/var/www/html/secure/pass2php/'.$device.'.php';
					}
				} elseif ($d[$device]['dt']=='pir') {
					if ($status->occupancy==1) $status='On';
					else $status='Off';
					if ($d[$device]['s']!=$status) {
						lg('ⓩ PIR '.$device.' '.$status);
						store($device,$status);
						include '/var/www/html/secure/pass2php/'.$device.'.php';
						
					}
				} else {
//					lg('ⓩ ZIGBEE ['.$d[$device]['dt'].']	'.$device.'	'.print_r($status,true));
				}
			}// else lg('ⓩ ZIGBEE [!dt!] '.$device.' '.print_r($status,true));
		}// else lg('ⓩ Z2M '.$device.' '.$status);
	} catch (Throwable $e) {
		lg("Fout in ZIGBEE MQTT: ".__LINE__.' '.$topic.' '.$e->getMessage());
	}
	if ($lastcheck < $d['time'] - $d['rand']) {
        $lastcheck = $d['time'];
        stoploop();
        updateWekker($t, $weekend, $dow, $d);
    }
},MqttClient::QOS_AT_LEAST_ONCE);

$sleepMicroseconds=5000;
$maxSleep=30000;
while (true) {
	$result=$mqtt->loop(true);
	if ($result === 0) {
		$sleepMicroseconds=min($sleepMicroseconds + 5000,$maxSleep);
		usleep($sleepMicroseconds);
	} else {
		$sleepMicroseconds=5000;
	}
}

$mqtt->disconnect();
lg('Zigbee MQTT loop stopped '.__FILE__,1);

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
        exec("nice -n 15 php $script > /dev/null 2>&1 &");
        exit;
    }
    if (filemtime($script) > LOOP_START) {
        lg('🛑 '.basename($script) . ' gewijzigd → restarting ...');
        $mqtt->disconnect();
        exec("nice -n 15 php $script > /dev/null 2>&1 &");
        exit;
    }
}