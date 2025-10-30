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
$user='COVER';
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

$mqtt->subscribe('homeassistant/cover/+/current_position',function (string $topic,string $status) use ($startloop,$validDevices,&$d,&$alreadyProcessed, &$counter) {
	try {
		$path=explode('/',$topic);
		$device=$path[2];
		if (isset($validDevices[$device])) {
			$d['time']=microtime(true);
			if (isset($status)) {
				$d=fetchdata($d['lastfetch'],'mqtt:'.__LINE__);
				$d['lastfetch']=$d['time'] - 300;
				if ($status === 'null') $status=0;
				if ($d[$device]['s']!=$status&&strlen($status)>0) {
					lg('mqtt '.__LINE__.' |cover |pos |'.$device.'|'.$status);
					store($device,$status,'',1);
				}
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