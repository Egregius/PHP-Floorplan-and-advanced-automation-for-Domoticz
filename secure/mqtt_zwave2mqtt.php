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
$user='ZWAVE';
lg('🟢 Starting '.$user.' loop ',-1);
$time=time();
$lastcheck=$time;
$t = null;
$weekend = null;
$dow = null;
$d=fetchdata(0,'mqtt_zwave2mqtt');
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
$mqtt->subscribe('zwave2mqtt/#',function (string $topic,string $status) use ($startloop, $validDevices, &$d, /*&$alreadyProcessed, &$lastEvent, */&$t, &$weekend, &$dow, &$lastcheck, &$time, $user) {
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
			if (isset($d[$device]['d'])) {
				if ($d[$device]['d']=='p') {
					if($path[2]=='sensor_binary') {
						if($status==1) $status='On';
						else $status='Off';
						if ($d[$device]['s']!=$status) {
//							lg('🌊 PIR '.$device.' '.$status);
							store($device, $status);
							include '/var/www/html/secure/pass2php/'.$device.'.php';
						}
					} else return;
				} elseif ($d[$device]['d']=='c') {
					if (isset($path[2])&&$path[2]=='sensor_binary') {
						if ($status==1) {
							if($device=='achterdeur') $status='Closed';
							else $status='Open';
						} else {
							if($device=='achterdeur') $status='Open';
							else $status='Closed';
						}
						if ($d[$device]['s']!=$status) {
//							lg('🌊 Z2M ['.$d[$device]['d'].']	'.$device.'	'.$status);
							store($device, $status);
							include '/var/www/html/secure/pass2php/'.$device.'.php';
						}
					}
				} elseif ($d[$device]['d']=='hsw') {
					if($path[2]=='switch_binary'&&$path[4]=='currentValue') {
						if ($status==1) $status='On';
						else $status='Off';
						if ($d[$device]['s']!=$status) {
//								lg('🌊 Z2M [HSW]	'.$device.'	'.$status);
							store($device, $status);
							include '/var/www/html/secure/pass2php/'.$device.'.php';
						}
					} elseif(isset($d[$device]['p'])&&$path[2]=='sensor_multilevel'&&$path[4]=='Power') {
						$val = (int)$status;
						$old = (int)($d[$device]['p'] ?? 0);
						$oldt = (int)($d[$device]['t'] ?? 0);
						if ($oldt === 0) {
							store($device, $val, '', 1);
							return;
						}
						$upd_power = false;
						$abs_diff = abs($val - $old);
						if ($old < 10) {
							if ($abs_diff >= 2) {
								$upd_power = true;
							}
						} elseif ($old < 100) {
							if ($abs_diff >= 10) {
								$upd_power = true;
							}
						} else {
							$rel_diff = abs(($val - $old) / $old);
							if ($rel_diff >= 0.40 && $abs_diff >= 50) {
								$upd_power = true;
							}
						}
						if($upd_power==true) {
//							lg($device.' '.__LINE__.' '.$status);
//							lg('🌊 Z2M Power '.$device.'	'.$status);
							storep($device,$val);
							if ($device=='dysonlader'&&$val<10&&$d['dysonlader']['s']=='On'&&past('dysonlader')>600) sw('dysonlader','Off',basename(__FILE__).':'.__LINE__);
						}
					} //else lg('🌊 Z2M METER ['.$d[$device]['d'].']	'.$device.'	'.print_r($path,true).'	'.$status);
				} elseif ($d[$device]['d']=='d') {
					if($path[2]=='switch_multilevel') {
						if($status>40&&$status<100)$status+=1;
						if($d[$device]['s']!=$status) {
							store($device, $status);
							include '/var/www/html/secure/pass2php/'.$device.'.php';
						}
					}
				} else {
//					lg('🌊 Z2M ['.$d[$device]['d'].']	'.$device.'	'.print_r($path,true).'	'.print_r($status,true));
				}
			} else { // Devices die niet in tabel bestaan
				if(str_starts_with($device, '8')) {
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
				} elseif ($device=='inputliving') {
					if(isset($path[2])&&$path[2]=='sensor_binary') {
						if ($status==1) {
							$knop=substr($path[3],-1);
							lg('🌊 '.$device.' '.$knop.' '.$status);
							if ($device=='inputliving') {
								if ($status==1) $status=='On';
								else $status='Off';
								$map=[
									0=>1,
									1=>2,
									2=>3
								];
								$knop=$map[$knop];
								lg('🌊 '.$device.' '.$knop.' '.$status);
							}
							include '/var/www/html/secure/pass2php/'.$device.$knop.'.php';
						}
					} elseif(isset($path[2])&&$path[2]=='switch_multilevel') {
							
							lg('🌊 '.$device.' '.$knop.' '.$status.' '.print_r($path,true));
							include '/var/www/html/secure/pass2php/'.$device.'1.php';
					}
				} elseif ($device=='remotealex') {
					$status=$status->action;
					include '/var/www/html/secure/pass2php/'.$device.'.php';
				} elseif ($device=='zbadkamer') {
					if($path[2]=='battery'&&$path[4]=='level') {
						if ($status<40) alert('bat'.$device,"Batterij {$device} bijna leeg: {$status}",1440);
					}
				} else {
//					lg('🌊 NO DT '.$device.'	'.$topic.'	=> '.$status);
				}
			}
		}// else lg('🌊 Z2M NO FILE '.$device.' '.$topic.'	=> '.$status);
	} catch (Throwable $e) {
		lg("Fout in MQTT {$user}: " . __LINE__ . ' ' . $topic . ' ' . print_r($status,true) . ' ' . $e->getMessage());
	}
	if ($lastcheck < $time - $d['rand']) {
        $lastcheck = $time;
        stoploop();
        updateWekker($t, $weekend, $dow, $d);
    }
},MqttClient::QOS_AT_LEAST_ONCE);
while (true) {
	$result=$mqtt->loop(true);
	usleep(5000);
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