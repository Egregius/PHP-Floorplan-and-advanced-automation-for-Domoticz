#!/usr/bin/php
<?php
declare(strict_types=1);
ini_set('error_reporting',E_ALL);
ini_set('display_errors',true);
gc_enable();
// Using https://github.com/php-mqtt/client
use PhpMqtt\Client\MqttClient;
use PhpMqtt\Client\ConnectionSettings;
require_once '/var/www/vendor/autoload.php';
require '/var/www/html/secure/functions.php';
$user='LIGHT';
lg('🟢 Starting '.$user.' loop ','light');
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
    ->setPassword('mqtt')
    ->setKeepAliveInterval(60);
$mqtt=new MqttClient('192.168.30.22',1883,basename(__FILE__) . '_' . getmypid().VERSIE,MqttClient::MQTT_3_1);
$mqtt->connect($connectionSettings,true);
$alreadyProcessed=[];
$validDevices = [];
foreach (glob('/var/www/html/secure/pass2php/*.php') as $file) {
	$basename = basename($file, '.php');
	$validDevices[$basename] = true;
}
$d=fetchdata();
$d['rand']=rand(100,200);
$d['rand']=5;
updateWekker($t, $weekend, $dow, $d);
$mqtt->subscribe('homeassistant/light/+/brightness',function (string $topic,string $status) use ($startloop,$validDevices,&$d,/*&$alreadyProcessed, */&$lastcheck, &$time, $user) {
	try {
		$path=explode('/',$topic);
		$device=$path[2];
		if (isset($validDevices[$device])) {
			$time=time();
			if (($time - LOOP_START) <= 2) return;
			$d['time']=$time;
			if (isset($status)) {
				$d=fetchdata();
				if ($status === 'null') $status=0;
				elseif ($status > 0 ) $status=round((int)$status / 2.55);
				else $status=0;
//				if($status>40&&$status<100)$status+=1;
//				lg('💡 mqtt '.__LINE__.' |bright |state |'.$device.'|'.$status);
				if ($d[$device]->s!=$status) {
					store($device,$status);
					include '/var/www/html/secure/pass2php/'.$device.'.php';
				}
			}
		}
	} catch (Throwable $e) {
		lg("Fout in MQTT {$user}: " . __LINE__ . ' ' . $topic . ' ' . $e->getMessage(),'light');
	}
	if ($lastcheck < $time - $d['rand']) {
        $lastcheck = $time;
        stoploop();
        updateWekker($t, $weekend, $dow, $d);
    }
},MqttClient::QOS_AT_LEAST_ONCE);

while (true) {
	try {
		if (!$mqtt->isConnected()) {
			lg('🟡 Reconnecting '.$user.' loop ');
			$mqtt->connect($connectionSettings, true);
		}
		$time = time();
		$mqtt->loopOnce($time);
		usleep(100000);
	} catch (MqttClientException $e) {
		lg("🟡 MQTT Cliënt fout in {$user}: " . $e->getMessage() . " (code " . $e->getCode() . ")");
		sleep(2);
	} catch (\Throwable $e) {
		lg("🔴 Onverwachte fout in MQTT {$user}: " . $e->getMessage() . " in " . $e->getFile() . ":" . $e->getLine());
		sleep(2);
	}
}
$mqtt->disconnect();
lg("🛑 MQTT {$user} loop stopped ".__FILE__,'light');

function isProcessed(string $topic,string $status,array &$alreadyProcessed): bool {
	if (isset($alreadyProcessed[$topic]) && $alreadyProcessed[$topic] === $status) return true;
	$alreadyProcessed[$topic]=$status;
	return false;
}

function stoploop() {
    global $mqtt;
    $script = __FILE__;
    if (filemtime(__DIR__ . '/functions.php') > LOOP_START) {
        lg('🛑 functions.php gewijzigd → restarting '.basename($script).' loop...','light');
        $mqtt->disconnect();
        exit;
    }
    if (filemtime($script) > LOOP_START) {
        lg('🛑 '.basename($script) . ' gewijzigd → restarting ...','light');
        $mqtt->disconnect();
        exit;
    }
	static $cycles=0;
	if($cycles>=50) {
		gc_collect_cycles();
		$cycles=0;
	} else $cycles++;
}
