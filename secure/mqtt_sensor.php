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
lg('🟢 Starting '.$user.' loop ',-1);
$time=time();
$lastcheck=$time;
$t = null;
$weekend = null;
$dow = null;
$d=fetchdata(0,'mqtt_sensor:'.__LINE__);
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
$mqtt->subscribe('homeassistant/sensor/+/state',function (string $topic,string $status) use ($startloop,$validDevices,&$d,&$alreadyProcessed, &$t, &$weekend, &$dow, &$lastcheck) {
	try {	
		$path=explode('/',$topic);
		$device=$path[2];
		if (isset($validDevices[$device])) {
			$time=microtime(true);
			$d['time']=$time;
			if (($d['time'] - $startloop) <= 2) return;
			if (isProcessed($topic,$status,$alreadyProcessed)) return;
			if (($d[$device]['s'] ?? null) === $status) return;
			$d=fetchdata($d['lastfetch'],'mqtt_sensor:'.__LINE__);
			$d['lastfetch']=$d['time'] - 300;
			if (substr($device,-4) === '_hum') {
				if (!is_numeric($status)) return;
				$tdevice=str_replace('_hum','_temp',$device);
				$hum=(int)$status;
				if ($hum !== $d[$tdevice]['m']) storemode($tdevice,$hum,'',1); 
			} elseif (substr($device,-5) === '_temp') {
				if (!is_numeric($status)) return;
				$st=(float)$status;
				if ($d[$device]['s']!=$st) store($device,$st,'',1);
			} elseif ($device=='daikin_kwh') {
				if (!is_numeric($status)) return;
				$val = (int)$status; // echte waarde
				$old = (int)($d[$device]['s'] ?? 0);
				$oldt = (int)($d[$device]['t'] ?? 0);
				if ($oldt === 0) {
					store($device, $val, '', 1);
					return;
				}
				$rel_increase = ($old > 0) ? (($val - $old) / $old) : 1;
				$time_passed = ($time - $oldt) >= 30;
				if ($rel_increase >= 0.40 || $rel_increase <= -0.40 || $time_passed) store($device, $val, '', 1);
			} else {
				if ($d[$device]['s']!=$status) {
					include '/var/www/html/secure/pass2php/'.$device.'.php';
					store($device,$status,'',1);
				}
			}
		} elseif ($device === 'sun_solar_elevation') {
			$status=(float)$status;
			if ($status>=10) $status=round($status,0);
			elseif ($status<=-10) $status=round($status,0);
			else $status=round($status,1);
			if ($d['dag']['s']!=$status) store('dag',$status,'',1);
			stoploop($d);
			updateWekker($t, $weekend, $dow, $d);
		} elseif ($device === 'sun_solar_azimuth') {
			$status=(int)$status;
			if ($d['dag']['m']!=$status) {
				storemode('dag',$status,'',1);
				setCache('dag',$status);
			}
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
	if ($lastcheck < $d['time'] - $d['rand']) {
        $lastcheck = $d['time'];
        stoploop();
        updateWekker($t, $weekend, $dow, $d);
    }
},MqttClient::QOS_AT_LEAST_ONCE);

$sleepMicroseconds=1000;
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