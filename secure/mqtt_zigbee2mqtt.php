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
$user='Z2M ';
lg('🟢 Starting '.$user.' loop ',-1);
$time=time();
$lastcheck=$time;
$t = null;
$weekend = null;
$dow = null;
$d=fetchdata(0,'mqtt_event:'.__LINE__);
$startloop=microtime(true);
define('LOOP_START', $startloop);
$d['lastfetch']=$startloop;
$d['time']=$startloop;
//$d['rand']=rand(300,600);
$d['rand']=5;
updateWekker($t, $weekend, $dow, $d);
$lastEvent=$startloop;
$connectionSettings=(new ConnectionSettings)
	->setUsername('mqtt')
	->setPassword('mqtt');
$mqtt=new MqttClient('192.168.2.26',1883,basename(__FILE__),MqttClient::MQTT_3_1,null,null);
$mqtt->connect($connectionSettings,true);
$alreadyProcessed=[];
$validDevices = [];
foreach (glob('/var/www/html/secure/pass2php/*.php') as $file) {
	$basename = basename($file, '.php');
	$validDevices[$basename] = true;
}
$mqtt->subscribe('zigbee2mqtt/+',function (string $topic,string $status) use ($startloop, $validDevices, &$d, /*&$alreadyProcessed, &$lastEvent, */&$t, &$weekend, &$dow, &$lastcheck) {
	try {
		$path=explode('/',$topic);
		$device=$path[1];
		if (isset($validDevices[$device])) {
			$d['time']=microtime(true);
			$time=$d['time'];
			if (($d['time'] - $startloop) <= 3) return;
//			if (isset($lastEvent) && ($d['time'] - $lastEvent) < 1) return;
//			$lastEvent = $d['time'];
			$d=fetchdata($d['lastfetch'],'mqtt_event:'.__LINE__);
			$d['lastfetch']=$d['time'] - 300;
			$status=json_decode($status);
			if (isset($d[$device]['dt'])) {
				if ($d[$device]['dt']=='remote') {
					$status=$status->action;
					include '/var/www/html/secure/pass2php/'.$device.'.php';
				} elseif ($d[$device]['dt']=='c') {
					if ($status->contact==1) $status='Closed';
					else $status='Open';
					if ($d[$device]['s']!=$status) {
						store($device,$status);
						include '/var/www/html/secure/pass2php/'.$device.'.php';
					}
				} elseif ($d[$device]['dt']=='pir') {
					if ($status->occupancy==1) $status='On';
					else $status='Off';
					if ($d[$device]['s']!=$status) {
						store($device,$status);
						include '/var/www/html/secure/pass2php/'.$device.'.php';
					}
				} elseif ($d[$device]['dt']=='hd') {
					if ($status->state=='OFF') $status=0;
					else $status=$status=round((float)$status->brightness / 2.55);
					if ($d[$device]['s']!=$status) {
						store($device,$status);
						include '/var/www/html/secure/pass2php/'.$device.'.php';
					}
				} elseif ($d[$device]['dt']=='hsw') {
					if ($status->state=='OFF') {
						$status='Off';
						$power=0;
					} else {
						$power=round($status->power);
						$status='On';
					}
					if (isset($d[$device]['p'])) {
						if ($d[$device]['s']!=$status&&$d[$device]['p']!=$power) {
							storesp($device,$status,$power);
							include '/var/www/html/secure/pass2php/'.$device.'.php';
						} elseif ($d[$device]['p']!=$power) {
							storep($device,$power);
							include '/var/www/html/secure/pass2php/'.$device.'.php';
						}
					} elseif ($d[$device]['s']!=$status) {
						store($device,$power);
						include '/var/www/html/secure/pass2php/'.$device.'.php';
					}
				} else {
					lg('🔥 Z2M ['.$d[$device]['dt'].']	'.$device.'	'.print_r($status,true));
				}
			} else lg('🔥 Z2M '.$device.' '.print_r($status,true));
		} // else lg('🔥 Z2M '.$device.' '.$status);
	} catch (Throwable $e) {
		lg("Fout in MQTT: ".__LINE__.' '.$topic.' '.$e->getMessage());
	}
	if ($lastcheck < $d['time'] - $d['rand']) {
        $lastcheck = $d['time'];
        stoploop();
        updateWekker($t, $weekend, $dow, $d);
    }
},MqttClient::QOS_AT_LEAST_ONCE);

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