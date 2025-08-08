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
lg('Starting MQTT loop...',-1);
$user='MQTT';

$d=fetchdata(0,'mqtt:'.__LINE__);
$startloop=microtime(true);
$d['lastfetch']=$startloop;
$d['time']=$startloop;
$lastEvent=$startloop;
$connectionSettings=(new ConnectionSettings)
	->setUsername('mqtt')
	->setPassword('mqtt');
$mqtt=new MqttClient('192.168.2.26',1883,'pass4mqtt',MqttClient::MQTT_3_1,null,null);
$mqtt->connect($connectionSettings,true);
$alreadyProcessed=[];
$validDevices = [];
foreach (glob('/var/www/html/secure/pass2php/*.php') as $file) {
	$basename = basename($file, '.php');
	$validDevices[$basename] = true;
}

// Subscribe switch states
$mqtt->subscribe('homeassistant/switch/+/state',function (string $topic,string $status) use ($startloop,$validDevices,&$d,&$alreadyProcessed) {
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
			if (!is_null($status)&&($status=='on'||$status=='off')) {
				$status=ucfirst($status);
				if ($d[$device]['s']!=$status) {
					lg('mqtt '.__LINE__.' |switch |state |'.$device.'|'.$status);
					include '/var/www/html/secure/pass2php/'.$device.'.php';
					store($device,$status,'',1);
				}
			}
		}
		stoploop($d);
	} catch (Throwable $e) {
		lg("Fout in MQTT: ".__LINE__.' '.$topic.' '.$e->getMessage());
	}
},MqttClient::QOS_AT_LEAST_ONCE);

// Subscribe light brightness
$mqtt->subscribe('homeassistant/light/+/brightness',function (string $topic,string $status) use ($startloop,$validDevices,&$d,&$alreadyProcessed) {
	try {
		$path=explode('/',$topic);
		$device=$path[2];
		if (isset($validDevices[$device])) {
			$d['time']=microtime(true);
			if (($d['time'] - $startloop) <= 3) return;
			if (isProcessed($topic,$status,$alreadyProcessed)) return;
			if (($d[$device]['s'] ?? null) === $status) return;
			if (isset($status)) {
				$d=fetchdata($d['lastfetch'],'mqtt:'.__LINE__);
				$d['lastfetch']=$d['time'] - 300;
				if ($status === 'null') $status=0;
				else $status=round((float)$status / 2.55);
				if ($d[$device]['s']!=$status) {
					lg('mqtt '.__LINE__.' |bright |state |'.$device.'|'.$status);
					include '/var/www/html/secure/pass2php/'.$device.'.php';
					store($device,$status,'',1);
				}
			}
		}
		stoploop($d);
	} catch (Throwable $e) {
		lg("Fout in MQTT: ".__LINE__.' '.$topic.' '.$e->getMessage());
	}
},MqttClient::QOS_AT_LEAST_ONCE);

// Subscribe covers
$mqtt->subscribe('homeassistant/cover/+/current_position',function (string $topic,string $status) use ($startloop,$validDevices,&$d,&$alreadyProcessed) {
	try {
		$path=explode('/',$topic);
		$device=$path[2];
		if (isset($validDevices[$device])) {
			$d['time']=microtime(true);
//			if (($d['time'] - $startloop) <= 3) return;
//			if (isProcessed($topic,$status,$alreadyProcessed)) return;
//			if (($d[$device]['s'] ?? null) === $status) {lg(__LINE__);return;}
			if (isset($status)) {
				$d=fetchdata($d['lastfetch'],'mqtt:'.__LINE__);
				$d['lastfetch']=$d['time'] - 300;
				if ($status === 'null') $status=0;
				if ($d[$device]['s']!=$status) {
//				include '/var/www/html/secure/pass2php/'.$device.'.php';
					lg('mqtt '.__LINE__.' |cover |pos |'.$device.'|'.$status);
					store($device,$status,'',1);
				}
			}
		}
		stoploop($d);
	} catch (Throwable $e) {
		lg("Fout in MQTT: ".__LINE__.' '.$topic.' '.$e->getMessage());
	}
},MqttClient::QOS_AT_LEAST_ONCE);

// Subscribe sensor states
$mqtt->subscribe('homeassistant/sensor/+/state',function (string $topic,string $status) use ($startloop,$validDevices,&$d,&$alreadyProcessed) {
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
			if ($device === 'powermeter_kwh') {
				include '/var/www/html/secure/pass2php/powermeter_kwh.php';
			} elseif ($device === 'powermeter_power') {
				if (($d['powermeter_kwh']['s'] ?? null) !== $status) store('powermeter_kwh',$status);
			} elseif ($device === 'kookplaatpower_power') {
				if (($d['kookplaatpower_kwh']['s'] ?? null) !== $status) store('kookplaatpower_kwh',$status);
			}  elseif (substr($device,-4) === '_hum') {
				$tdevice=str_replace('_hum','_temp',$device);
				$hum=(int)$status;
				if ($hum > 100) $hum=100;
				if ($hum>(int)$d[$tdevice]['m']+1) $hum=(int)$d[$tdevice]['m']+1;
				elseif ($hum<(int)$d[$tdevice]['m']-1) $hum=(int)$d[$tdevice]['m']-1;
				if ($hum !== $d[$tdevice]['m']) storemode($tdevice,$hum,'',1); 
			} elseif (substr($device,-5) === '_temp') {
				$st=(float)$status;
				if ($st>$d[$device]['s']+0.1) $st=$d[$device]['s']+0.1;
				elseif ($st<$d[$device]['s']-0.1) $st=$d[$device]['s']-0.1;
				if ($d[$device]['s']!=$st) store($device,$st,'',1);
			} else {
				include '/var/www/html/secure/pass2php/'.$device.'.php';
				if ($d[$device]['s']!=$status) store($device,$status,'',1);
			}
		} elseif ($device === 'sun_solar_elevation') {
			if ($status>=10) $status=round((float)$status,0);
			else $status=round((float)$status,1);
			if ($d['dag']['s']!=$status) store('dag',$status,'',1);
		} elseif ($device === 'sun_solar_azimuth') {
			if ($d['dag']['m']!=$status) storemode('dag',$status,'',1);
		} elseif ($device === 'weg') {
			telegram('Weg ingesteld op '.$status.' door Home Assistant');
			if ($status==0) {
				store('weg',0,'',1);
				huisthuis();
			} elseif ($status==2) {
				store('weg',2,'',1);
				huisslapen(true);
			} elseif ($status==3) {
				store('weg',3,'',1);
				huisslapen(true);
			}
		}
		stoploop($d);
	} catch (Throwable $e) {
		lg("Fout in MQTT: ".__LINE__.' '.$topic.' '.$e->getMessage());
	}
},MqttClient::QOS_AT_LEAST_ONCE);

// Subscribe binary_sensor states
$mqtt->subscribe('homeassistant/binary_sensor/+/state', function (string $topic, string $status) use ($startloop, $validDevices, &$d, &$alreadyProcessed) {
	try {
		$path = explode('/', $topic);
		$device = $path[2];
		if (isset($validDevices[$device])) {
			$d['time']=microtime(true);
			if (($d['time'] - $startloop) <= 5) return;
			if ($status=='unavailable') return;
			$status = ucfirst(strtolower(trim($status, '"')));
			$d = fetchdata($d['lastfetch'], 'mqtt:' . __LINE__);
			$d['lastfetch'] = $d['time'] - 30;
			if ($device === 'achterdeur') {
				if ($status=='Off') $status='Open';
				elseif ($status=='On') $status='Closed';
				else unset($status);
			} elseif (isset($d[$device]['dt']) && $d[$device]['dt'] === 'c') {
				if ($status=='On') $status='Open';
				elseif ($status=='Off') $status='Closed';
				else unset($status);
			}
			if (isset($status)&&$d[$device]['s']!=$status) {
				lg('mqtt ' . __LINE__ . ' |binary |state |' . $device . '|' . $status . '|');
				include '/var/www/html/secure/pass2php/' . $device . '.php';
				store($device, $status,'',1);
			}
		}
		stoploop($d);
	} catch (Throwable $e) {
		lg("Fout in MQTT: " . __LINE__ . ' ' . $topic . ' ' . $e->getMessage());
	}
}, MqttClient::QOS_AT_LEAST_ONCE);

// Subscribe event types
$mqtt->subscribe('homeassistant/event/+/event_type',function (string $topic,string $status) use ($startloop, $validDevices, &$d, &$alreadyProcessed, &$lastEvent) {
	try {
		$path=explode('/',$topic);
		$device=$path[2];
		if (isset($validDevices[$device])) {
			$d['time']=microtime(true);
			if (($d['time'] - $startloop) <= 3) return;
			$status = ucfirst(strtolower(trim($status, '"')));
			if (isset($lastEvent) && ($d['time'] - $lastEvent) < 1) return;
//			else lg($device.' '.$lastEvent.' >>> OK, meer dan 2 seconden geleden');
			$lastEvent = $d['time'];
			lg('mqtt '.__LINE__.' |event |e_type |'.$device.'|'.$status.'|');
			$d=fetchdata($d['lastfetch'],'mqtt:'.__LINE__);
			$d['lastfetch']=$d['time'] - 300;
			if (substr($device,0,1) === '8') {
				if ($status === 'Keypressed') {
					$status='On';
					include '/var/www/html/secure/pass2php/'.$device.'.php';
					store($device,$status,'',1);
				} elseif ($status === 'Keypressed2x') {
					$status='On';
					include '/var/www/html/secure/pass2php/'.$device.'d.php';
					store($device,$status,'',1);
				}
			} else {
				include '/var/www/html/secure/pass2php/'.$device.'.php';
				store($device,$status,'',1);
			}
		}
		stoploop($d);
	} catch (Throwable $e) {
		lg("Fout in MQTT: ".__LINE__.' '.$topic.' '.$e->getMessage());
	}
},MqttClient::QOS_AT_LEAST_ONCE);

// Subscribe sensor states
$mqtt->subscribe('homeassistant/media_player/+/state',function (string $topic,string $status) use ($startloop,$validDevices,&$d,&$alreadyProcessed) {
	try {	
		$path=explode('/',$topic);
		$device=$path[2];
		if (isset($validDevices[$device])) {
			$d['time']=microtime(true);
			$d=fetchdata($d['lastfetch'],'mqtt:'.__LINE__);
			$d['lastfetch']=$d['time'] - 300;
			$status = ucfirst(strtolower(trim($status, '"')));
			if ($d[$device]['s']!=$status) {
				lg('mqtt '.__LINE__.' |media |state |'.$device.'|'.$status.'|');
				store($device,$status,'',1);
			}
		}
		stoploop($d);
	} catch (Throwable $e) {
		lg("Fout in MQTT: ".__LINE__.' '.$topic.' '.$e->getMessage());
	}
},MqttClient::QOS_AT_LEAST_ONCE);


// Main loop with incremental sleep
$sleepMicroseconds=10000;
$maxSleep=500000;
$quietCounter=0;
while (true) {
	$result=$mqtt->loop(true);
	if ($result === 0) {
		$quietCounter++;
		$sleepMicroseconds=min($sleepMicroseconds + 10000,$maxSleep);
		usleep($sleepMicroseconds);
	} else {
		$quietCounter=0;
		$sleepMicroseconds=10000;
	}
}

$mqtt->disconnect();
lg('MQTT loop stopped.',1);

function isProcessed(string $topic,string $status,array &$alreadyProcessed): bool {
	if (isset($alreadyProcessed[$topic]) && $alreadyProcessed[$topic] === $status) return true;
	$alreadyProcessed[$topic]=$status;
	return false;
}

function stoploop($d) {
	global $mqtt;
	if ($d['weg']['m']==1) {
		lg('Stopping MQTT Loop...');
		storemode('weg',0,'',1);
		$mqtt->disconnect();
		exit;
	} elseif ($d['weg']['m']==3) {
		lg('Stopping MQTT Loop...');
		storemode('weg',2,'',1);
		$mqtt->disconnect();
		exit;
	}
}