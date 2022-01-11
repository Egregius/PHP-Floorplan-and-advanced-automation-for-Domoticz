<?php
require('/var/www/vendor/autoload.php');
require '/var/www/html/secure/functions.php';
use \PhpMqtt\Client\MqttClient;
use \PhpMqtt\Client\ConnectionSettings;
$clean_session = false;

$connectionSettings  = new ConnectionSettings();
$connectionSettings
  ->setKeepAliveInterval(60)
  ->setLastWillTopic('#')
  ->setLastWillMessage('client disconnect')
  ->setLastWillQualityOfService(1);
$mqtt = new MqttClient('192.168.2.28', 1883, rand(5,15));
$mqtt->connect($connectionSettings, $clean_session);
printf("client connected\n");

$mqtt->subscribe('#', function ($topic, $message) {
//    printf("Received message on topic [%s]: %s\n", $topic, $message);
	$device=str_replace('domoticz/', '', $topic);
	$message=json_decode($message, true);
	if ($message['dtype']=='Light/Switch') {
		if ($message['nvalue']==1) $status='On';
		else $status='Off';
	} elseif (in_array($message['dtype'], array('Lux'))) {
		// IGNORING
	} elseif (in_array($message['dtype'], array('Temp','Thermostat'))) {
		$status=$message['svalue1'];
	} else {
		echo $device.' >>> ';print_r($message);
	}

	if (isset($status)) {
		echo $device.' = '.$status.PHP_EOL;
		$d=fetchdata();
		if (isset($d[$device])) {
			if ($d[$device]['dt']=='dimmer'||$d[$device]['dt']=='rollers'||$d[$device]['dt']=='luifel') {
				if ($status=='Off'||$status=='Open') {
					$status=0;
				} elseif ($status=='On'||$status=='Closed') {
					$status=100;
				} else {
					$status=filter_var($status, FILTER_SANITIZE_NUMBER_INT);
				}
				if ($device=='Xlight') {
					if (!is_int($status)) exit;
				}
			} elseif (in_array($device, array('garage_temp'))) {
				$status=explode(';', $status);
				$status=$status[0];
				$old=$status;
				if ($status>$d[$device]['s']+0.5) $status=$d[$device]['s']+0.5;
				elseif ($status<$d[$device]['s']-0.5) $status=$d[$device]['s']-0.5;
			} elseif ($device=='achterdeur') {
				if ($status=='Open') {
					$status='Closed';
				} else {
					$status='Open';
				}
			} elseif ($device=='sirene') {
				if ($status=='Group On') {
					$status='On';
				} else {
					$status='Off';
				}
			} elseif ($d[$device]['dt']=='thermometer') {
				$old=$status;
				if ($status>$d[$device]['s']+0.5) $status=$d[$device]['s']+0.5;
				elseif ($status<$d[$device]['s']-0.5) $status=$d[$device]['s']-0.5;
			}
			if (file_exists('/var/www/html/secure/pass2php/'.$device.'.php')) {
				store($device, $status, 'Pass2PHP', $message['idx']);
				if (@include '/var/www/html/secure/pass2php/'.$device.'.php') {
					if (isset($old)&&$old!=$status) lg($device.' = '.$status.' orig = '.$old);
			//		else lg($device.' = '.$status);
				}
			} else lg('			>>>	IGNORING	>>>	'.$device.' = '.$status);
		}

	}
}, 0);
$mqtt->loop(true);
$mqtt->disconnect();
