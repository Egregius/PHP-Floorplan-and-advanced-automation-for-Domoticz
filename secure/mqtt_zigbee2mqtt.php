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
$mqtt->subscribe('zigbee2mqtt/+',function (string $topic,string $status) use ($startloop, $validDevices, &$d, /*&$alreadyProcessed, &$lastEvent, */&$t, &$weekend, &$dow, &$lastcheck, &$time, $user) {
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
				} elseif ($d[$device]['dt']=='c') {
					if ($status->contact==1) $status='Closed';
					else $status='Open';
					if ($d[$device]['s']!=$status) {
//						lg('ⓩ Contact '.$device.' '.$status);
						include '/var/www/html/secure/pass2php/'.$device.'.php';
						store($device,$status);
					}
				} elseif ($d[$device]['dt']=='pir') {
					if ($status->occupancy==1) $status='On';
					else $status='Off';
					if ($d[$device]['s']!=$status) {
//						lg('ⓩ PIR '.$device.' '.$status);
						include '/var/www/html/secure/pass2php/'.$device.'.php';
						store($device,$status);
						
					}
				} elseif ($d[$device]['dt']=='hsw') {
					if (isset($d[$device]['p'])) {
						$p=$status->power;
						$status=ucfirst(strtolower($status->state));
						$val = (int)$p; // echte waarde
						$old = (int)($d[$device]['p'] ?? 0);
						$oldt = (int)($d[$device]['t'] ?? 0);
						if ($oldt === 0) {
							store($device, $val, '', 1);
							return;
						}
						$rel_increase = ($old > 0) ? (($val - $old) / $old) : 1;
						$time_passed = ($time - $oldt) >= 30;
						if ($rel_increase >= 0.40 || $rel_increase <= -0.40 || ($time_passed&&$d[$device]['s']!=$status)) $upd=true;
						else $upd=false;
						
						if ($d[$device]['s']!=$status&&$upd==true) {
//							lg('ⓩ ZIGBEE [HSW]	'.$device.'	'.$status.' '.$p);
							storesp($device,$status,$p);
						} elseif ($d[$device]['s']!=$status) {
//							lg('ⓩ ZIGBEE [HSW]	'.$device.'	'.$status.' '.$p);
							store($device,$status);
						} elseif ($d[$device]['p']!=$p&&$upd==true) {
//							lg('ⓩ ZIGBEE [HSW]	'.$device.'	'.$status.' '.$p);
							storep($device,$p);
						}
					} else {
						$status=ucfirst(strtolower($status->state));
						if ($d[$device]['s']!=$status) {
//							lg('ⓩ ZIGBEE [HSW]	'.$device.'	'.$status);
							store($device,$status);
						}
					}
				} elseif ($d[$device]['dt']=='hd') {
					if($status->state=='OFF') $status=0;
					else $status=$status=round((float)$status->brightness / 2.55);
					if ($d[$device]['s']!=$status) {
//						lg('ⓩ ZIGBEE [HD]	'.$device.'	'.$status);
						store($device,$status);
					}
				} elseif ($d[$device]['dt']=='t') {
					$h=round($status->humidity);
					$t=$status->temperature;
					if($d[$device]['s']!=$t&&$d[$device]['m']!=$h) storesm($device,$t,$h);
					elseif($d[$device]['s']!=$t) store($device,$t);
					elseif($d[$device]['m']!=$h) storemode($device,$h);
				} else {
//					lg('ⓩ ZIGBEE ['.$d[$device]['dt'].']	'.$device.'	'.print_r($status,true));
				}
			} elseif ($device=='remotealex') {
				if (isset($status->action)) {
					$status=$status->action;
//						lg('ⓩ Remote '.$device.' '.$status);
					include '/var/www/html/secure/pass2php/'.$device.'.php';
				}
			}// else lg('ⓩ ZIGBEE [!dt!] '.$device.' '.print_r($status,true));
		}// else lg('ⓩ Z2M '.$device.' '.$status);
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
	$result=$mqtt->loop(true);
	usleep(4000);
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