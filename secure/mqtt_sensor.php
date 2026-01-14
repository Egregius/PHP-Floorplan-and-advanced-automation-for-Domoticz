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
$user='SENSOR';
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
$mqtt->subscribe('homeassistant/sensor/+/state',function (string $topic,string $status) use ($startloop,$validDevices,&$d,&$alreadyProcessed, &$t, &$weekend, &$dow, &$lastcheck, &$time, $user) {
	try {	
		$path=explode('/',$topic);
		$device=$path[2];
		if (isset($validDevices[$device])) {
			$time=time();
			$d['time']=$time;
			if (($time - LOOP_START) <= 2) return;
			if (isProcessed($topic,$status,$alreadyProcessed)) return;
			if (($d[$device]['s'] ?? null) === $status) return;
//			$d=fetchdata();
			if (substr($device,-4) === '_hum') {
				if (!is_numeric($status)) return;
				$tdevice=str_replace('_hum','_temp',$device);
				$hum=(int)$status;
				if ($hum !== $d[$tdevice]['m']&&abs($hum-$d[$tdevice]['m'])>1) storemode($tdevice,$hum); 
			} elseif (substr($device,-5) === '_temp') {
				if (!is_numeric($status)) return;
				$st=(float)$status;
				if ($d[$device]['s']!=$st) store($device,$st);
			} elseif ($device=='daikin_kwh') {
				return;
				$val = (int)$status;
				$old = (int)($d[$device]['s'] ?? 0);
				$oldt = (int)($d[$device]['t'] ?? 0);
				if ($oldt === 0) {
					store($device, $val);
					return;
				}
				$rel_increase = ($old > 0) ? (($val - $old) / $old) : 1;
				$time_passed = ($time - $oldt) >= 30;
				if ($rel_increase >= 0.40 || $rel_increase <= -0.40 || $time_passed) store($device,$val);
			} else {
				if ($d[$device]['s']!=$status) {
					include '/var/www/html/secure/pass2php/'.$device.'.php';
					store($device,$status);
				}
			}
		} elseif ($device === 'sun_solar_elevation') {
			$status=(float)$status;
			if ($status>=10) $status=round($status,0);
			elseif ($status<=-10) $status=round($status,0);
			else $status=round($status,1);
			if ((string)$d['dag']['s']!=(string)$status) store('dag',(string)$status);
			stoploop($d);
			updateWekker($t, $weekend, $dow, $d);
		} elseif ($device === 'sun_solar_azimuth') {
			$status=(int)$status;
			if ((string)$d['dag']['m']!=(string)$status) {
				storemode('dag',(string)$status);
				setCache('dag',$status);
			}
		} elseif ($device === 'weg') {
			if ($status==0) {
				store('weg',0);
				huisthuis();
			} elseif ($status==2) {
				store('weg',2);
				huisslapen(true);
			} elseif ($status==3) {
				store('weg',3);
				huisslapen(3);
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
},MqttClient::QOS_AT_LEAST_ONCE);

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