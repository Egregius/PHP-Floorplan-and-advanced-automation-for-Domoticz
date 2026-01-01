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
require '/var/www/vendor/autoload.php';
require '/var/www/html/secure/functions.php';
$user='ALL';
lg('🟢 Starting '.$user.' loop ',-1);
$time=time();
$lastcheck=$time;
$t = null;
$weekend = null;
$dow = null;
$d=fetchdata(0,'mqtt_binary:'.__LINE__);
$startloop=time();
define('LOOP_START', $startloop);
$d['time']=$startloop;
$d['rand']=rand(100,200);
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
$mqtt->subscribe('homeassistant/binary_sensor/+/state', function (string $topic, string $status) use ($startloop, $validDevices, &$d, &$alreadyProcessed, &$t, &$weekend, &$dow, &$lastcheck, &$time, $user) {
	try {
		$path = explode('/', $topic);
		$device = $path[2];
		if (isset($validDevices[$device])) {
			$time=time();
			$d['time']=$time;
			if (($time - $startloop) <= 2) return;
			if ($status=='unavailable') return;
			$status = ucfirst(strtolower(trim($status, '"')));
			$d = fetchdata();
			if ($device === 'achterdeur') {
				if ($status=='Off') $status='Open';
				elseif ($status=='On') $status='Closed';
				else unset($status);
			} elseif (isset($d[$device]['d']) && $d[$device]['d'] === 'c') {
				if ($status=='On') $status='Open';
				elseif ($status=='Off') $status='Closed';
				else unset($status);
			} elseif ($device=='pirgarage') {
				if ($status=='Off'&&$d['pirgarage2']['s']=='On') $status='On';
			} elseif ($device=='pirgarage2') {
				if($d[$device]['s']!=$status) store($device, $status);
				if ($status=='Off'&&$d['pirgarage']['s']=='On') $status='On';
				$device='pirgarage';
			}
			if (isset($status)&&$d[$device]['s']!=$status) {
				lg('ⓗ		'.str_pad($user??'', 9, ' ', STR_PAD_RIGHT).' '.str_pad($device, 13, ' ', STR_PAD_RIGHT).' '.$status);
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

$mqtt->subscribe('homeassistant/cover/+/current_position',function (string $topic,string $status) use ($startloop,$validDevices,&$d,/*&$alreadyProcessed, */&$lastcheck, &$time, $user) {
	try {
		$path=explode('/',$topic);
		$device=$path[2];
		if (isset($validDevices[$device])) {
			if (isset($status)) {
				$time=time();
				$d['time']=$time;
				if (($time - $startloop) <= 2) return;
				if ($status === 'null') $status=0;
				elseif($status==1) $status=0;
				elseif($status==99) $status=100;
				if ($device=='rbureel') $status=100-$status;
				$d=fetchdata();
				if ($d[$device]['s']!=$status) {
					lg('📜 mqtt '.__LINE__.' |cover |pos |'.$device.'|'.$status);
					store($device,$status,'',1);
				}
			}
		}
	} catch (Throwable $e) {
		lg("Fout in {$user}: ".__LINE__.' '.$topic.' '.$e->getMessage());
	}
	if ($lastcheck < $d['time'] - $d['rand']) {
        $lastcheck = $d['time'];
        stoploop();
        updateWekker($t, $weekend, $dow, $d);
    }
},MqttClient::QOS_AT_LEAST_ONCE);

$mqtt->subscribe('homeassistant/event/+/event_type',function (string $topic,string $status) use ($startloop, $validDevices, &$d, /*&$alreadyProcessed, */&$lastEvent, &$t, &$weekend, &$dow, &$lastcheck, &$time, $user) {
	try {
		$path=explode('/',$topic);
		$device=$path[2];
		if (isset($validDevices[$device])) {
			$time=time();
			$d['time']=$time;
			if (($time - $startloop) <= 2) return;
			$status = ucfirst(strtolower(trim($status, '"')));
			if (isset($lastEvent) && ($d['time'] - $lastEvent) < 1) return;
			$lastEvent = $d['time'];
//			lg('👉🏻 mqtt '.__LINE__.' |event |e_type |'.$device.'|'.$status.'|');
			$d=fetchdata();
			if (str_starts_with($device,'8')) {
				if ($status === 'Keypressed') {
					$status='On';
					include '/var/www/html/secure/pass2php/'.$device.'.php';
					if (isset($d[$device]['t'])) store($device,$status,'',1);
				} elseif ($status === 'Keypressed2x') {
					$status='On';
					include '/var/www/html/secure/pass2php/'.$device.'d.php';
					if (isset($d[$device]['t'])) store($device,$status,'',1);
				}
			} else {
				include '/var/www/html/secure/pass2php/'.$device.'.php';
				if (isset($d[$device]['t'])) store($device,$status,'',1);
			}
		}// else lg($device);
	} catch (Throwable $e) {
		lg("Fout in {$user}: ".__LINE__.' '.$topic.' '.$e->getMessage());
	}
	if ($lastcheck < $d['time'] - $d['rand']) {
        $lastcheck = $d['time'];
        stoploop();
        updateWekker($t, $weekend, $dow, $d);
    }
},MqttClient::QOS_AT_LEAST_ONCE);

$mqtt->subscribe('homeassistant/switch/+/state',function (string $topic,string $status) use ($startloop,$validDevices,&$d,&$alreadyProcessed, &$lastcheck, &$time, $user) {
	try {	
		$path=explode('/',$topic);
		$device=$path[2];
		if (isset($validDevices[$device])) {
			$time=time();
			$d['time']=$time;
			if (($time - $startloop) <= 2) return;
			if (isProcessed($topic,$status,$alreadyProcessed)) return;
//			if (($d[$device]['s'] ?? null) === $status) return;
			$d=fetchdata();
			if (!is_null($status)&&strlen($status)>0&&$status!='Uknown'/*&&($status=='on'||$status=='off')*/) {
				$status=ucfirst($status);
				if ($d[$device]['s']!=$status) {
//					lg('💡 mqtt '.__LINE__.' |switch |state |'.$device.'|'.$status);
					include '/var/www/html/secure/pass2php/'.$device.'.php';
					store($device,$status,'',1);
				}
			}
		}
	} catch (Throwable $e) {
		lg("Fout in {$user}: ".__LINE__.' '.$topic.' '.$e->getMessage());
	}
	if ($lastcheck < $d['time'] - $d['rand']) {
        $lastcheck = $d['time'];
        stoploop();
        updateWekker($t, $weekend, $dow, $d);
   }
},MqttClient::QOS_AT_LEAST_ONCE);

$mqtt->subscribe('homeassistant/light/+/brightness',function (string $topic,string $status) use ($startloop,$validDevices,&$d,/*&$alreadyProcessed, */&$lastcheck, &$time, $user) {
	try {
		$path=explode('/',$topic);
		$device=$path[2];
		if (isset($validDevices[$device])) {
			$time=time();
			$d['time']=$time;
			if (($time - $startloop) <= 2 && in_array($device,['inputliving1'])) return;
//			if (isProcessed($topic,$status,$alreadyProcessed)) return;
//			if (($d[$device]['s'] ?? null) === $status) return;
			if (isset($status)) {
				$d=fetchdata();
				if ($status === 'null') $status=0;
				elseif ($status > 0 ) $status=round((float)$status / 2.55);
				else $status=0;
				if($status>40&&$status<100)$status+=1;
//				lg('💡 mqtt '.__LINE__.' |bright |state |'.$device.'|'.$status);
				if ($d[$device]['s']!=$status) {
					store($device,$status);
					include '/var/www/html/secure/pass2php/'.$device.'.php';
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

$mqtt->subscribe('homeassistant/media_player/+/state',function (string $topic,string $status) use ($startloop,&$d, &$lastcheck, &$time, $user) {
	try {	
		$path=explode('/',$topic);
		$device=$path[2];
		if (isset($validDevices[$device])) {
			$time=time();
			$d['time']=$time;
			$d=fetchdata();
			$status = ucfirst(strtolower($status));
			if ($d[$device]['s']!=$status) {
	//			lg('mqtt '.__LINE__.' |media |state |'.$device.'|'.$status.'|');
				include '/var/www/html/secure/pass2php/'.$device.'.php';
				store($device,$status,'',1);
			}
		}
	} catch (Throwable $e) {
		lg("Fout in {$user}: ".__LINE__.' '.$topic.' '.$e->getMessage());
	}
	if ($lastcheck < $time - $d['rand']) {
        $lastcheck = $time;
        stoploop();
        updateWekker($t, $weekend, $dow, $d);
    }
},MqttClient::QOS_AT_LEAST_ONCE);

$mqtt->subscribe('homeassistant/media_player/+/source',function (string $topic,string $status) use ($startloop,&$d, &$lastcheck, $user) {
	try {	
		$path=explode('/',$topic);
		$device=$path[2];
		if ($device=='nvidia') {
			$time=time();
			$d['time']=$time;
			$d=fetchdata();
			$status = ucfirst(strtolower(trim($status, '"')));
			if ($d[$device]['m']!=$status) {
				storemode($device,$status,'',1);
			}
		}
	} catch (Throwable $e) {
		lg("Fout in MQTT {$user}: " . __LINE__ . ' ' . $topic . ' ' . $e->getMessage());
	}
},MqttClient::QOS_AT_LEAST_ONCE);

$mqtt->subscribe('homeassistant/sensor/+/state',function (string $topic,string $status) use ($startloop,$validDevices,&$d,&$alreadyProcessed, &$t, &$weekend, &$dow, &$lastcheck, &$time, $user) {
	try {	
		$path=explode('/',$topic);
		$device=$path[2];
		if (isset($validDevices[$device])) {
			$time=time();
			$d['time']=$time;
			if (($time - $startloop) <= 2) return;
			if (isProcessed($topic,$status,$alreadyProcessed)) return;
			if (($d[$device]['s'] ?? null) === $status) return;
			$d=fetchdata();
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
				return;
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
		lg("Fout in {$user}: ".__LINE__.' '.$topic.' '.$e->getMessage());
	}
	if ($lastcheck < $d['time'] - $d['rand']) {
        $lastcheck = $d['time'];
        stoploop();
        updateWekker($t, $weekend, $dow, $d);
    }
},MqttClient::QOS_AT_LEAST_ONCE);

while (true) {
	$result=$mqtt->loop(true);
	usleep(33333);
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
		exec("nice -n 10 /usr/bin/php $script > /dev/null 2>&1 &");
        exit;
    }
    if (filemtime($script) > LOOP_START) {
        lg('🛑 '.basename($script) . ' gewijzigd → restarting ...');
        $mqtt->disconnect();
        ftruncate($lock_file, 0);
		flock($lock_file, LOCK_UN);
		exec("nice -n 10 /usr/bin/php $script > /dev/null 2>&1 &");
        exit;
    }
}