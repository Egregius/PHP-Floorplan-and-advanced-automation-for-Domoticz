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
$user='ZWAVE';
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
$mqtt->subscribe('zwave2mqtt/#',function (string $topic,string $status) use ($startloop, $validDevices, &$d, /*&$alreadyProcessed, &$lastEvent, */&$t, &$weekend, &$dow, &$lastcheck) {
	try {
		$path=explode('/',$topic);
		$device=$path[1];
		
		if (isset($validDevices[$device])) {
			$d['time']=microtime(true);
			$time=$d['time'];
			if (($d['time'] - $startloop) <= 3) return;
//			if (isset($lastEvent) && ($d['time'] - $lastEvent) < 1) return;
//			$lastEvent = $d['time'];
			$d=fetchdata($d['lastfetch'],'mqtt_zwave:'.__LINE__);
			$d['lastfetch']=$d['time'] - 5;
			$status=json_decode($status);
			if (isset($d[$device]['dt'])) {
				if ($d[$device]['dt']=='8knop') {
					if(isset($path[4])&&$path[4]=='scene') {
						$knop=(int)$path[5];
						if ($status===0) {
							$file=$device.'_'.$knop;
						} elseif ($status===3) {
							$file=$device.'_'.$knop.'d';
						} else return;
						$status='On';
//						lg('📲 '.$file);
						include '/var/www/html/secure/pass2php/'.$file.'.php';
						if (isset($d[$file]['t'])) store($file,null,'',1);
					}
				} elseif ($d[$device]['dt']=='zbtn') {
					if(isset($path[2])&&$path[2]=='sensor_binary') {
						if ($status==1) {
							$knop=substr($path[3],-1);
//							lg('🌊 '.$device.' '.$knop.' '.$status);
							if ($device=='inputliving') {
								if ($status==1) $status=='On';
								else $status='Off';
								$map=[
									0=>1,
									1=>2,
									2=>3
								];
								$knop=$map[$knop];
//								lg('🌊 '.$device.' '.$knop.' '.$status);
							}
							include '/var/www/html/secure/pass2php/'.$device.$knop.'.php';
						}
					} elseif(isset($path[2])&&$path[2]=='switch_multilevel') {
//							lg('🌊 '.$device.' '.$knop.' '.$status);
							include '/var/www/html/secure/pass2php/'.$device.'1.php';
					}
				} elseif ($d[$device]['dt']=='remote') {
					$status=$status->action;
					include '/var/www/html/secure/pass2php/'.$device.'.php';
				} elseif ($d[$device]['dt']=='pir') {
					if($path[2]=='sensor_binary') {
						if($status==1) $status='On';
						else $status='Off';
						lg('🌊 PIR '.$device.' '.$status);
						store($device, $status);
						include '/var/www/html/secure/pass2php/'.$device.'.php';
					} else return;
				} elseif ($d[$device]['dt']=='c') {
					lg('🌊 Z2M ['.$d[$device]['dt'].']	'.$device.'	'.print_r($path,true).'	'.print_r($status,true));
				} else {
//					lg('🌊 Z2M ['.$d[$device]['dt'].']	'.$device.'	'.print_r($path,true).'	'.print_r($status,true));
				}
			}// else lg('🌊 '.$device.'	'.$topic.'	=> '.$status);
		}// else lg('🌊 Z2M '.$device.' '.$topic.'	=> '.$status);
	} catch (Throwable $e) {
		lg("Fout in ZWAVE MQTT: ".__LINE__.' '.$topic.' '.$e->getMessage());
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
lg('Zwave MQTT loop stopped '.__FILE__,1);

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