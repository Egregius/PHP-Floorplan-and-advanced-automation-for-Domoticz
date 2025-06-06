#!/usr/bin/php
<?php
declare(strict_types=1);
ini_set('error_reporting', E_ALL);
ini_set('display_errors', true);
$user='MQTT';
$time=time();
$startloop=$time;
// Using https://github.com/php-mqtt/client

require '/var/www/vendor/autoload.php';
require '/var/www/html/secure/functions.php';

$d=fetchdata(0,'mqtt:'.__LINE__);
$d['lastfetch']=$time;

lg(' Starting MQTT loop...',1);

use PhpMqtt\Client\MqttClient;
use PhpMqtt\Client\ConnectionSettings;

$connectionSettings = (new ConnectionSettings)
    ->setUsername('mqtt')
    ->setPassword('mqtt');

$mqtt=new MqttClient('192.168.2.26',1883,'pass4mqtt',MqttClient::MQTT_3_1,null,null);
$mqtt->connect($connectionSettings, true);

$mqtt->subscribe('homeassistant/switch/+/state', function (string $topic, string $status) use (&$d) {
	try {
		$path=explode('/', $topic);
		$device=$path[2];
		if (!file_exists("/var/www/html/secure/pass2php/$device.php")) return;
		if (alreadyProcessed($device, $topic, $status, $d)) return;

		$d['time']=time();
		$d=fetchdata($d['lastfetch'],'mqtt:'.__LINE__);
		$d['lastfetch']=$d['time']-30;
		$status=ucfirst($status);
		if ($d[$device]['s']!=$status) {
			lg('mqtt	'.__LINE__.'	|switch	|state	|'.$device.'|'.$status);
			include "/var/www/html/secure/pass2php/$device.php";
			store($device, $status);
		}
		stoploop($d);
	} catch(Throwable $e) {
		lg("Fout in MQTT: ".__LINE__.' '.$topic.' '.$e->getMessage());
	}
}, MqttClient::QOS_AT_LEAST_ONCE);

$mqtt->subscribe('homeassistant/light/+/brightness', function (string $topic, string $status) use (&$d) {
	try {
		$path=explode('/', $topic);
		$device=$path[2];
		if (!file_exists("/var/www/html/secure/pass2php/$device.php")) return;
		if (alreadyProcessed($device, $topic, $status, $d)) return;

		$d['time']=time();
		$d=fetchdata($d['lastfetch'],'mqtt:'.__LINE__);
		$d['lastfetch']=$d['time']-30;
		$status = ($status === 'null') ? 0 : round($status/2.55);
		if ($d[$device]['s']!=$status) {
			lg('mqtt	'.__LINE__.'	|bright	|state	|'.$device.'	|'.$status.'|');
			include "/var/www/html/secure/pass2php/$device.php";
			store($device, $status);
		}
		stoploop($d);
	} catch(Throwable $e) {
		lg("Fout in MQTT: ".__LINE__.' '.$topic.' '.$e->getMessage());
	}
}, MqttClient::QOS_AT_LEAST_ONCE);

$mqtt->subscribe('homeassistant/sensor/+/state', function (string $topic, string $status) use (&$d, &$startloop) {
	try {
		$path=explode('/', $topic);
		$device=$path[2];
		if (!file_exists("/var/www/html/secure/pass2php/$device.php")) return;
		if (alreadyProcessed($device, $topic, $status, $d)) return;

		$d['time']=time();
		if (($d['time']-$startloop)>5) {
			lg('mqtt	'.__LINE__.'	|sensor	|state	|'.$device.'	|'.$status.'|');
			$d=fetchdata($d['lastfetch'],'mqtt:'.__LINE__);
			$d['lastfetch']=$d['time']-30;

			if ($device=='powermeter_kwh') {
				include '/var/www/html/secure/pass2php/powermeter_kwh.php';
			} elseif ($device=='powermeter_power') {
				if ($d['powermeter_kwh']['s']!=$status) store('powermeter_kwh',$status);
			} elseif ($device=='kookplaatpower_power') {
				if ($d['kookplaatpower_kwh']['s']!=$status) store('kookplaatpower_kwh',$status);
			} elseif (substr($device, -4)=='_hum') {
				$tdevice=str_replace('_hum','_temp',$device);
				$hum=(float)$status;
				if ($hum>100) $hum=100;
				elseif($hum>$d[$tdevice]['m']+1) $hum=$d[$tdevice]['m']+1;
				elseif($hum<$d[$tdevice]['m']-1) $hum=$d[$tdevice]['m']-1;
				if($hum!=$d[$tdevice]['m']) storemode($tdevice, $hum, '', 1);
			} elseif (substr($device, -5)=='_temp') {
				$status=(float)$status;
				if ($status>$d[$device]['s']+0.1) $status=$d[$device]['s']+0.1;
				elseif ($status<$d[$device]['s']-0.1) $status=$d[$device]['s']-0.1;
				if ($d[$device]['s']!=$status) store($device, $status);
			}
		}
		stoploop($d);
	} catch(Throwable $e) {
		lg("Fout in MQTT: ".__LINE__.' '.$topic.' '.$e->getMessage());
	}
}, MqttClient::QOS_AT_LEAST_ONCE);

$mqtt->subscribe('homeassistant/binary_sensor/+/state', function (string $topic, string $status) use (&$d, &$startloop) {
	try {
		$path=explode('/', $topic);
		$device=$path[2];
		if (!file_exists("/var/www/html/secure/pass2php/$device.php")) return;
		if (alreadyProcessed($device, $topic, $status, $d)) return;

		$d['time']=time();
		if (($d['time']-$startloop)>5) {
			$d=fetchdata($d['lastfetch'],'mqtt:'.__LINE__);
			$d['lastfetch']=$d['time']-30;
			$status=ucfirst($status);

			if ($device=='achterdeur') {
				$status=($status=='Off')?'Open':'Closed';
			} elseif (isset($d[$device]['dt']) && $d[$device]['dt']=='c') {
				$status=($status=='On')?'Open':'Closed';
			} else {
				$status=($status=='on')?'On':(($status=='off')?'Off':$status);
			}
			lg('mqtt	'.__LINE__.'	|binary	|state	|'.$device.'	|'.$status.'|');
			include "/var/www/html/secure/pass2php/$device.php";
			store($device, $status);
		}
		stoploop($d);
	} catch(Throwable $e) {
		lg("Fout in MQTT: ".__LINE__.' '.$topic.' '.$e->getMessage());
	}
}, MqttClient::QOS_AT_LEAST_ONCE);

$mqtt->subscribe('homeassistant/event/+/event_type', function (string $topic, string $status) use (&$d, &$startloop) {
	try {
		$path=explode('/', $topic);
		$device=$path[2];
		if (!file_exists("/var/www/html/secure/pass2php/$device.php")) return;
		if (alreadyProcessed($device, $topic, $status, $d)) return;

		$d['time']=time();
		if (($d['time']-$startloop)>5) {
			lg('mqtt	'.__LINE__.'	|event	|e_type	|'.$device.'	|'.$status.'|');
			$d=fetchdata($d['lastfetch'],'mqtt:'.__LINE__);
			$d['lastfetch']=$d['time']-30;

			if (substr($device,0,1)=='8') {
				if ($status=='"KeyPressed"') {
					$status='On';
					include "/var/www/html/secure/pass2php/$device.php";
				} elseif ($status=='"KeyPressed2x"') {
					$status='On';
					include "/var/www/html/secure/pass2php/{$device}d.php";
				}
			} else {
				if ($status=='"on"') {
					$status='On';
					include "/var/www/html/secure/pass2php/$device.php";
				} elseif ($status=='"off"') {
					$status='Off';
					include "/var/www/html/secure/pass2php/$device.php";
				}
			}
		}
		stoploop($d);
	} catch(Throwable $e) {
		lg("Fout in MQTT: ".__LINE__.' '.$topic.' '.$e->getMessage());
	}
}, MqttClient::QOS_AT_LEAST_ONCE);

$mqtt->subscribe('homeassistant/cover/+/current_position', function (string $topic, string $status) use (&$d) {
	try {
		$path=explode('/', $topic);
		$device=$path[2];
		if (!file_exists("/var/www/html/secure/pass2php/$device.php")) return;
		if (alreadyProcessed($device, $topic, $status, $d)) return;

		$d['time']=time();
		$d=fetchdata($d['lastfetch'],'mqtt:'.__LINE__);
		$d['lastfetch']=$d['time']-30;
		if ($d[$device]['s']!=$status) {
			lg('mqtt	'.__LINE__.'	|cover	|state	|'.$device.'	|'.$status.'|');
			include "/var/www/html/secure/pass2php/$device.php";
			store($device, $status);
		}
		stoploop($d);
	} catch(Throwable $e) {
		lg("Fout in MQTT: ".__LINE__.' '.$topic.' '.$e->getMessage());
	}
}, MqttClient::QOS_AT_LEAST_ONCE);

while (true) {
    $mqtt->loopOnce(microtime(true),true,10);
}
$mqtt->disconnect();

function alreadyProcessed(string $device, string $topic, string $status, array &$d): bool {
	$hash = md5($topic . '|' . $status);
	if (isset($d['lasthash'][$device]) && $d['lasthash'][$device] === $hash) return true;
	$d['lasthash'][$device] = $hash;
	return false;
}

function stoploop($d) {
	global $mqtt;
	if ($d['weg']['m']==1) {
		lg('Stopping MQTT Loop...');
		storemode('weg', 0, '', 1);
		$mqtt->disconnect();
		exec('kill -9 ' . getmypid());
	} elseif ($d['weg']['m']==3) {
		lg('Stopping MQTT Loop...');
		storemode('weg', 2, '', 1);
		$mqtt->disconnect();
		exec('kill -9 ' . getmypid());
	}
}

/*
$mqtt->subscribe('homeassistant/media_player/+/state', function (string $topic, string $status) use ($mqtt, &$d, &$startloop) {
	try{
		$path=explode('/', $topic);
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
					store($device, $status);
				}
			}
		}
		stoploop($d);
	}catch(Throwable $e){lg("Fout in MQTT: ".__LINE__.' '.$topic.' '.$e->getMessage());}
}, MqttClient::QOS_AT_LEAST_ONCE);
*/

/*
$mqtt->subscribe('domoticz/out/#', function (string $topic, string $status) use ($mqtt, &$d) {
	try{
		$d['time']=time();
		$d=fetchdata($d['lastfetch'],'mqtt:'.__LINE__);
		$d['lastfetch']=$d['time']-30;
		$status=json_decode($status, true);
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
}, MqttClient::QOS_AT_LEAST_ONCE);
*/
/*
$mqtt->subscribe('homeassistant/event/+/state', function (string $topic, string $status) use ($mqtt, &$d) {
	try{
		$path=explode('/', $topic);
		$device=$path[2];
		if (file_exists('/var/www/html/secure/pass2php/'.$device.'.php')) {
			lg('mqtt	'.__LINE__.'	|event	|state	|'.$device.'	|'.$status.'|');
			$d['lastseen'][$device]=strtotime($status);
		}
		stoploop($d);
	}catch(Throwable $e){
		lg("Fout in MQTT: ".__LINE__.' '.$topic.' '.$e->getMessage());
	}
}, MqttClient::QOS_AT_LEAST_ONCE);
*/