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
$x=0;
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
$mqtt->subscribe('homeassistant/event/+/event_type',function (string $topic,string $status) use ($startloop, $validDevices, &$d, &$lastEvent, &$t, &$weekend, &$dow, &$lastcheck, &$time, $user) {
	try {
		$path=explode('/',$topic);
		$device=$path[2];
		if (isset($validDevices[$device])) {
			if (($time - LOOP_START) <= 2) return;
//			$d['time']=$time;
			$status = ucfirst(strtolower(trim($status, '"')));
			if (isset($lastEvent) && ($d['time'] - $lastEvent) < 1) return;
			$lastEvent = $d['time'];
			$d=fetchdata();
			if (str_starts_with($device,'8')) {
				if ($status === 'Keypressed') {
					$status='On';
					include '/var/www/html/secure/pass2php/'.$device.'.php';
					if (isset($d[$device]->t)) store($device,$status);
				} elseif ($status === 'Keypressed2x') {
					$status='On';
					include '/var/www/html/secure/pass2php/'.$device.'d.php';
					if (isset($d[$device]->t)) store($device,$status);
				}
			} else {
				include '/var/www/html/secure/pass2php/'.$device.'.php';
				if (isset($d[$device]->t)) store($device,$status);
			}
		}// else lg($device);
	} catch (Throwable $e) {
		lg("Fout in MQTT {$user}: " . __LINE__ . ' ' . $topic . ' ' . $e->getMessage());
	}
	if ($lastcheck < $time - $d['rand']) {
        $lastcheck = $time;
        stoploop();
        updateWekker($t, $weekend, $dow, $d);
    }
},MqttClient::QOS_AT_LEAST_ONCE);

$mqtt->subscribe('homeassistant/binary_sensor/+/state', function (string $topic, string $status) use ($startloop, $validDevices, &$d, &$alreadyProcessed, &$t, &$weekend, &$dow, &$lastcheck, &$time, $user) {
	try {
		$path = explode('/', $topic);
		$device = $path[2];
		if (isset($validDevices[$device])) {
			if (($time - LOOP_START) <= 2) return;
//			$d['time']=$time;
			if ($status=='unavailable') return;
			$status = ucfirst(strtolower(trim($status, '"')));
			$d = fetchdata();
			if ($device === 'achterdeur') {
				if ($status=='Off') $status='Open';
				elseif ($status=='On') $status='Closed';
				else unset($status);
			} elseif (isset($d[$device]->d) && $d[$device]->d === 'c') {
				if ($status=='On') $status='Open';
				elseif ($status=='Off') $status='Closed';
				else unset($status);
			} elseif ($device=='pirgarage') {
				if ($status=='Off'&&$d['pirgarage2']->s=='On') $status='On';
			} elseif ($device=='pirgarage2') {
				if($d[$device]->s!=$status) store($device, $status);
				if ($status=='Off'&&$d['pirgarage']->s=='On') $status='On';
				$device='pirgarage';
			}
			if (isset($status)&&$d[$device]->s!=$status) {
				include '/var/www/html/secure/pass2php/' . $device . '.php';
				store($device, $status);
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
}, MqttClient::QOS_AT_LEAST_ONCE);

$mqtt->subscribe('homeassistant/sensor/+/state',function (string $topic,string $status) use ($startloop,$validDevices,&$d,&$alreadyProcessed, &$t, &$weekend, &$dow, &$lastcheck, &$time, $user) {
	try {
		$path=explode('/',$topic);
		$device=$path[2];
		if ($device === 'daikin_kwh') {
			if (!is_numeric($status)) return;
			$status = (int)$status;
			if($status<50) $treshold=1;
			else $treshold=0.09;
			$old = (int)($d['daikin']->p ?? 0);
			$oldt = (int)($d['daikin']->t ?? 0);
			$rel_increase = ($old > 0) ? (($status - $old) / $old) : 1;
			$time_passed = ($time - $oldt) >= 60;
			if ($rel_increase > $treshold || $rel_increase < -$treshold || $time_passed) {
				if($status===0&&past('daikin')>10) storesp('daikin','Off',0,$rel_increase.'	> '.$treshold);
				else storep('daikin',$status,$rel_increase.'	> '.$treshold);
			}
		} elseif ($device === 'sun_solar_elevation') {
			$status = (float)$status;
			if ($status >= -10 && $status <= 10) {
				$status = round($status / 0.2) * 0.2;
				$status = round($status, 1);
			} else {
				$status = round($status / 2) * 2;
			}
			if ((float)$d['dag']->s != $status) {
				store('dag', $status);
				setCache('dag', $status);
			}
			stoploop($d);
		} elseif ($device === 'sun_solar_azimuth') {
			$status = (int)$status;
			$status = round($status / 5) * 5;
			if ((int)$d['dag']->m != $status) {
				storemode('dag', $status);
				updateWekker($t, $weekend, $dow, $d);
			}
		} elseif (isset($validDevices[$device])) {
//			$d['time']=$time;
//			if (isProcessed($topic,$status,$alreadyProcessed)) return;
//			if (($d[$device]->s ?? null) === $status) return;
			$d=fetchdata();
			if (substr($device,-4) === '_hum') {
				if (!is_numeric($status)) return;
				$tdevice = str_replace('_hum','_temp',$device);
				$hum = (int)$status;
				$hum = max($d[$tdevice]->m - 5, min($hum, $d[$tdevice]->m + 5));
				if ($hum !== $d[$tdevice]->m && abs($hum - $d[$tdevice]->m) >= 1) {
					storemode($tdevice, $hum);
				}
			} elseif (substr($device,-5) === '_temp') {
				if (!is_numeric($status)) return;
				$st = (float)$status;
				$st = max($d[$device]->s - 0.5, min($st, $d[$device]->s + 0.5));
				if ($d[$device]->s != $st && abs($st - $d[$device]->s) >= 0.1) {
					store($device, $st);
					include '/var/www/html/secure/pass2php/'.$device.'.php';
				}
			} else {
				if ($d[$device]->s!=$status) {
					include '/var/www/html/secure/pass2php/'.$device.'.php';
					store($device,$status);
				}
			}
		} elseif ($device === 'weg') {
			if (($time - LOOP_START) <= 2) return;
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

$mqtt->subscribe('homeassistant/switch/+/state',function (string $topic,string $status) use ($startloop,$validDevices,&$d,&$alreadyProcessed, &$lastcheck, &$time, $user) {
	try {
		$path=explode('/',$topic);
		$device=$path[2];
		if (isset($validDevices[$device])) {
//			$d['time']=$time;
			if (($time - LOOP_START) <= 2) return;
			if (isProcessed($topic,$status,$alreadyProcessed)) return;
			$d=fetchdata();
			if (!is_null($status)&&strlen($status)>0&&$status!='Uknown'/*&&($status=='on'||$status=='off')*/) {
				$status=ucfirst($status);
				if ($d[$device]->s!=$status) {
					include '/var/www/html/secure/pass2php/'.$device.'.php';
					store($device,$status);
				}
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
	$time = time();
	$d['time']=$time;
    $mqtt->loopOnce($time);
    usleep(20000);
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
		exec("nice -n 5 /usr/bin/php8.2 $script > /dev/null 2>&1 &");
        exit;
    }
    if (filemtime($script) > LOOP_START) {
        lg('🛑 '.basename($script) . ' gewijzigd → restarting ...');
        $mqtt->disconnect();
        ftruncate($lock_file, 0);
		flock($lock_file, LOCK_UN);
		exec("nice -n 5 /usr/bin/php8.2 $script > /dev/null 2>&1 &");
        exit;
    }
}
