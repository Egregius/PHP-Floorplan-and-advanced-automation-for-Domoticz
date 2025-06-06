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

$user='MQTT';
$d=fetchdata(0,'mqtt:'.__LINE__);
$startloop=microtime(true);
$d['lastfetch']=$startloop;
$d['time']=$startloop;

lg('Starting MQTT loop...',1);

$connectionSettings=(new ConnectionSettings)
	->setUsername('mqtt')
	->setPassword('mqtt');

$mqtt=new MqttClient('192.168.2.26',1883,'pass4mqtt',MqttClient::MQTT_3_1,null,null);
$mqtt->connect($connectionSettings,true);

// Central array to track processed messages per run
$alreadyProcessed=[];

// Subscribe switch states
$mqtt->subscribe('homeassistant/switch/+/state',function (string $topic,string $status) use (&$d,&$startloop,&$alreadyProcessed) {
	try {
		$d['time']=microtime(true);
		if (($d['time'] - $startloop) <= 3) return;
		if (isProcessed($topic,$status,$alreadyProcessed)) return;
		$path=explode('/',$topic);
		$device=$path[2];
		if (($d[$device]['s'] ?? null) === $status) return;
		if (file_exists('/var/www/html/secure/pass2php/'.$device.'.php')) {
			$d=fetchdata($d['lastfetch'],'mqtt:'.__LINE__);
			$d['lastfetch']=$d['time'] - 30;
			$status=ucfirst($status);
			lg('mqtt '.__LINE__.' |switch |state |'.$device.'|'.$status);
			include '/var/www/html/secure/pass2php/'.$device.'.php';
			store($device,$status);
		}
		stoploop($d);
	} catch (Throwable $e) {
		lg("Fout in MQTT: ".__LINE__.' '.$topic.' '.$e->getMessage());
	}
},MqttClient::QOS_AT_LEAST_ONCE);

// Subscribe light brightness
$mqtt->subscribe('homeassistant/light/+/brightness',function (string $topic,string $status) use (&$d,&$startloop,&$alreadyProcessed) {
	try {
		$d['time']=microtime(true);
		if (($d['time'] - $startloop) <= 3) return;
		if (isProcessed($topic,$status,$alreadyProcessed)) return;
		$path=explode('/',$topic);
		$device=$path[2];
		if (($d[$device]['s'] ?? null) === $status) {
			lg(__LINE__.' '.$topic.' '.$status);
			return;
		}
		if (file_exists('/var/www/html/secure/pass2php/'.$device.'.php')) {
			$d=fetchdata($d['lastfetch'],'mqtt:'.__LINE__);
			$d['lastfetch']=$d['time'] - 30;
			if ($status === 'null') $status=0;
			else $status=round((float)$status / 2.55);
			lg('mqtt '.__LINE__.' |bright |state |'.$device.'|'.$status);
			include '/var/www/html/secure/pass2php/'.$device.'.php';
			store($device,$status);
		}
		stoploop($d);
	} catch (Throwable $e) {
		lg("Fout in MQTT: ".__LINE__.' '.$topic.' '.$e->getMessage());
	}
},MqttClient::QOS_AT_LEAST_ONCE);

// Subscribe sensor states
$mqtt->subscribe('homeassistant/sensor/+/state',function (string $topic,string $status) use (&$d,&$startloop,&$alreadyProcessed) {
	try {
		$d['time']=microtime(true);
		if (($d['time'] - $startloop) <= 3) return;
		if (isProcessed($topic,$status,$alreadyProcessed)) return;
		$path=explode('/',$topic);
		$device=$path[2];
		if (($d[$device]['s'] ?? null) === $status) {
			lg(__LINE__.' '.$topic.' '.$status);
			return;
		}
		if (file_exists('/var/www/html/secure/pass2php/'.$device.'.php')) {		
			lg('mqtt '.__LINE__.' |sensor |state |'.$device.'|'.$status.'|');
			$d=fetchdata($d['lastfetch'],'mqtt:'.__LINE__);
			$d['lastfetch']=$d['time'] - 30;
			if ($device === 'powermeter_kwh') {
				include '/var/www/html/secure/pass2php/powermeter_kwh.php';
			} elseif ($device === 'powermeter_power') {
				if (($d['powermeter_kwh']['s'] ?? null) !== $status) store('powermeter_kwh',$status);
			} elseif ($device === 'kookplaatpower_power') {
				if (($d['kookplaatpower_kwh']['s'] ?? null) !== $status) store('kookplaatpower_kwh',$status);
			} elseif (substr($device,-4) === '_hum') {
				$tdevice=str_replace('_hum','_temp',$device);
				$hum=(float)$status;
				if ($hum > 100) $hum=100;
				elseif ($hum > ($d[$tdevice]['m'] ?? 0) + 1) $hum=($d[$tdevice]['m'] ?? 0) + 1;
				elseif ($hum < ($d[$tdevice]['m'] ?? 0) - 1) $hum=($d[$tdevice]['m'] ?? 0) - 1;
				if ($hum !== ($d[$tdevice]['m'] ?? null)) storemode($tdevice,$hum,'',1);
			} elseif (substr($device,-5) === '_temp') {
				$st=(float)$status;
				if ($st > (($d[$device]['s'] ?? 0) + 0.1)) $st=($d[$device]['s'] ?? 0) + 0.1;
				elseif ($st < (($d[$device]['s'] ?? 0) - 0.1)) $st=($d[$device]['s'] ?? 0) - 0.1;
				if (($d[$device]['s'] ?? null) !== $st) store($device,$st);
			}
		}
		stoploop($d);
	} catch (Throwable $e) {
		lg("Fout in MQTT: ".__LINE__.' '.$topic.' '.$e->getMessage());
	}
},MqttClient::QOS_AT_LEAST_ONCE);

// Subscribe binary_sensor states
$lastKeypress = [];

$mqtt->subscribe('homeassistant/binary_sensor/+/state', function (string $topic, string $status) use (&$d, &$alreadyProcessed, &$lastKeypress, $startloop) {
	try {
		$d['time']=microtime(true);
		if (($d['time'] - $startloop) <= 5) return;
		$path = explode('/', $topic);
		$device = $path[2];
		$statusNorm = strtolower(trim($status, '"'));
		$isStateless = in_array($statusNorm, ['keypressed', 'keypressed2x', 'on', 'off']);
		if ($isStateless) {
			// Debounce op stateless events
			if (isset($lastKeypress[$topic]) && ($d['time'] - $lastKeypress[$topic]) < 1) {
				return;
			}
			$lastKeypress[$topic] = $d['time'];
		} else {
			// Voor 'on' en 'off': check op duplicaten
			if (isProcessed($topic, $status, $alreadyProcessed)) return;
			if (($d[$device]['s'] ?? null) === $status) return;
		}
		if (file_exists('/var/www/html/secure/pass2php/' . $device . '.php')) {
			$d = fetchdata($d['lastfetch'], 'mqtt:' . __LINE__);
			$d['lastfetch'] = $d['time'] - 30;
			$status = ucfirst(strtolower($statusNorm)); // Consistente hoofdlettergebruik
			if ($device === 'achterdeur') {
				$status = ($status === 'Off') ? 'Open' : 'Closed';
			} elseif (isset($d[$device]['dt']) && $d[$device]['dt'] === 'c') {
				$status = ($status === 'On') ? 'Open' : 'Closed';
			}

			lg('mqtt ' . __LINE__ . ' |binary |state |' . $device . '|' . $status . '|');
			include '/var/www/html/secure/pass2php/' . $device . '.php';
			store($device, $status);
		}
		stoploop($d);
	} catch (Throwable $e) {
		lg("Fout in MQTT: " . __LINE__ . ' ' . $topic . ' ' . $e->getMessage());
	}
}, MqttClient::QOS_AT_LEAST_ONCE);




// Subscribe event types
$mqtt->subscribe('homeassistant/event/+/event_type',function (string $topic,string $status) use (&$d,&$startloop,&$alreadyProcessed) {
	try {
		$d['time']=microtime(true);
		if (($d['time'] - $startloop) <= 3) return;
		if (isProcessed($topic,$status,$alreadyProcessed)) return;
		$path=explode('/',$topic);
		$device=$path[2];
		if (($d[$device]['s'] ?? null) === $status) return;
		if (file_exists('/var/www/html/secure/pass2php/'.$device.'.php')) {
			lg('mqtt '.__LINE__.' |event |e_type |'.$device.'|'.$status.'|');

			$d=fetchdata($d['lastfetch'],'mqtt:'.__LINE__);
			$d['lastfetch']=$d['time'] - 30;

			if (substr($device,0,1) === '8') {
				if ($status === '"KeyPressed"') {
					$status='On';
					include '/var/www/html/secure/pass2php/'.$device.'.php';
					store($device,$status);
				}
			}
		}
		stoploop($d);
	} catch (Throwable $e) {
		lg("Fout in MQTT: ".__LINE__.' '.$topic.' '.$e->getMessage());
	}
},MqttClient::QOS_AT_LEAST_ONCE);


// Main loop with incremental sleep
$sleepMicroseconds=10000; // start 10ms
$maxSleep=1000000; // max 1s
$quietCounter=0;

while (true) {
	$result=$mqtt->loop(true);
	if ($result === 0) {
		$quietCounter++;
		$sleepMicroseconds=min($sleepMicroseconds + 10000,$maxSleep); // +10ms per quiet iteration max 1s
		usleep($sleepMicroseconds);
	} else {
		// reset sleep if message received
		$quietCounter=0;
		$sleepMicroseconds=10000;
	}

	if (!empty($d['stoploop']) && $d['stoploop'] === true) {
		lg('Stoploop flag detected,exiting.');
		break;
	}
}

$mqtt->disconnect();
lg('MQTT loop stopped.',1);

function isProcessed(string $topic,string $status,array &$alreadyProcessed): bool {
	if (isset($alreadyProcessed[$topic]) && $alreadyProcessed[$topic] === $status) {
		lg('mqtt | alreadyProcessed '.$topic.' | '.$status);
		return true;
	}
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
/*
$mqtt->subscribe('homeassistant/media_player/+/state',function (string $topic,string $status) use ($mqtt,&$d,&$startloop) {
	try{
		$path=explode('/',$topic);
		$device=$path[2];
		if (file_exists('/var/www/html/secure/pass2php/'.$device.'.php')) {
//			lg('mqtt	'.__LINE__.'	|media	|state	|'.$device.'	|'.$status.'|');
			$d['time']=time();
			if (($d['time']-$startloop)>5) {
				$d=fetchdata($d['lastfetch'],'mqtt:'.__LINE__);
				$d['lastfetch']=$d['time']-30;
				$status=ucfirst($status);
				if ($d[$device]['s']!=$status) {
					lg('mqtt	'.__LINE__.'	|media	|state	|'.$device.'	|'.$status.'|');
					include '/var/www/html/secure/pass2php/'.$device.'.php';
					store($device,$status);
				}
			}
		}
		stoploop($d);
	}catch(Throwable $e){lg("Fout in MQTT: ".__LINE__.' '.$topic.' '.$e->getMessage());}
},MqttClient::QOS_AT_LEAST_ONCE);
*/

/*
$mqtt->subscribe('domoticz/out/#',function (string $topic,string $status) use ($mqtt,&$d) {
	try{
		$d['time']=time();
		$d=fetchdata($d['lastfetch'],'mqtt:'.__LINE__);
		$d['lastfetch']=$d['time']-30;
		$status=json_decode($status,true);
		$device=$status['name'];
		$status=$status['nvalue'];
		if (file_exists('/var/www/html/secure/pass2php/'.$device.'.php')) {
			if ($status==0) $status='Closed';
			elseif ($status==1) $status='Open';
			lg('mqtt	'.__LINE__.'	|'.$device.'|'.$status.'|');
			if ($d[$device]['s']!=$status) {
				include '/var/www/html/secure/pass2php/'.$device.'.php';
				store($device,$status);
			}
		}
		stoploop($d);
	}catch(Throwable $e){lg("Fout in MQTT: ".__LINE__.' '.$topic.' '.$e->getMessage());}
},MqttClient::QOS_AT_LEAST_ONCE);
*/
/*
$mqtt->subscribe('homeassistant/event/+/state',function (string $topic,string $status) use ($mqtt,&$d) {
	try{
		$path=explode('/',$topic);
		$device=$path[2];
		if (file_exists('/var/www/html/secure/pass2php/'.$device.'.php')) {
			lg('mqtt	'.__LINE__.'	|event	|state	|'.$device.'	|'.$status.'|');
			$d['lastseen'][$device]=strtotime($status);
		}
		stoploop($d);
	}catch(Throwable $e){
		lg("Fout in MQTT: ".__LINE__.' '.$topic.' '.$e->getMessage());
	}
},MqttClient::QOS_AT_LEAST_ONCE);
*/