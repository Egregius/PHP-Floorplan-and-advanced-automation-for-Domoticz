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
$user='SENSOR';
lg('Starting '.$user.' loop ',-1);
$counter=0;
$t = null;
$weekend = null;
$d=fetchdata(0,'mqtt:'.__LINE__);
$startloop=microtime(true);
define('LOOP_START', $startloop);
$d['lastfetch']=$startloop;
$d['time']=$startloop;
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
$mqtt->subscribe('homeassistant/sensor/+/state',function (string $topic,string $status) use ($startloop,$validDevices,&$d,&$alreadyProcessed, &$t, &$weekend, &$dow, &$counter) {
	try {	
		$path=explode('/',$topic);
		$device=$path[2];
		if (isset($validDevices[$device])) {
			$d['time']=microtime(true);
			if (($d['time'] - $startloop) <= 3) return;
			if (isProcessed($topic,$status,$alreadyProcessed)) return;
			if (($d[$device]['s'] ?? null) === $status) return;
			$d=fetchdata($d['lastfetch'],'mqtt:'.__LINE__);
			$d['lastfetch']=$d['time'] - 300;
			if (substr($device,-4) === '_hum') {
				$tdevice=str_replace('_hum','_temp',$device);
				$hum=(int)$status;
				if ($hum !== $d[$tdevice]['m']) storemode($tdevice,$hum,'',1); 
			} elseif (substr($device,-5) === '_temp') {
				$st=(float)$status;
				if ($d[$device]['s']!=$st) store($device,$st,'',1);
			} else {
				include '/var/www/html/secure/pass2php/'.$device.'.php';
				if ($d[$device]['s']!=$status) store($device,$status,'',1);
			}
		} elseif ($device === 'sun_solar_elevation') {
			$status=(float)$status;
			if ($status>=10) $status=round($status,0);
			else $status=round($status,1);
			if ($d['dag']['s']!=$status) store('dag',$status,'',1);
			stoploop($d);
			updateWekker($t, $weekend, $dow, $d);
		} elseif ($device === 'sun_solar_azimuth') {
			$status=(int)$status;
			if ($d['dag']['m']!=$status) storemode('dag',$status,'',1);
		} elseif ($device === 'weg') {
			if ($status==0) {
				store('weg',0,'',1);
				huisthuis();
			} elseif ($status==2) {
				store('weg',2,'',1);
				huisslapen(true);
			} elseif ($status==3) {
				store('weg',3,'',1);
				huisslapen(3);
			}
		}
	} catch (Throwable $e) {
		lg("Fout in MQTT: ".__LINE__.' '.$topic.' '.$e->getMessage());
	}
	$counter++;
	if ($counter>1000) {
		stoploop();
		$counter=0;
	}
},MqttClient::QOS_AT_LEAST_ONCE);

$sleepMicroseconds=10000;
$maxSleep=1000000;
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
        lg('functions.php gewijzigd → restarting pass4mqtt loop...');
        $mqtt->disconnect();
        exec("$script > /dev/null 2>&1 &");
        exit;
    }
    if (filemtime($script) > LOOP_START) {
        lg(basename($script) . ' gewijzigd → restarting ...');
        $mqtt->disconnect();
        exec("$script > /dev/null 2>&1 &");
        exit;
    }
}