#!/usr/bin/php
<?php
declare(strict_types=1);
$user='MQTT';
// Using https://github.com/php-mqtt/client
require '/var/www/vendor/autoload.php';
require '/var/www/html/secure/functions.php';
lg('Starting MQTT loop...');
updatefromdomoticz();
use PhpMqtt\Client\MqttClient;

$client = new MqttClient('127.0.0.1', 1883, 'pass4mqtt', MqttClient::MQTT_3_1, null, null);
$client->connect(null, true);
$client->subscribe('#', function (string $topic, string $message, bool $retained) use ($client) {
	$user='MQTT';
	$time=time();
	$topic=explode('/', $topic);
	if ($topic[0]=='domoticz') {
		if ($topic[1]=='out') {
			global $dbname,$dbuser,$dbpass,$user,$domoticzurl;
			$d=fetchdata();
			$message=json_decode($message, true);
			$device=$message['name'];
			$status=$message['svalue1'];
			if (file_exists('/var/www/html/secure/pass2php/'.$device.'.php')) {
				if ($message['dtype']=='Light/Switch') {
					if ($message['switchType']=='Dimmer') {
						if ($message['nvalue']==0) $status=0;
						else $status=$message['svalue1'];
					} elseif ($message['switchType']=='Blinds Percentage') {
						if ($message['nvalue']==0) $status=0;
						elseif ($message['nvalue']==1) $status=100;
						else $status=$message['svalue1'];
					} elseif ($message['switchType']=='Contact') {
						if ($message['nvalue']==0) $status='Closed';
						elseif ($message['nvalue']==1) $status='Open';
					} elseif ($message['switchType']=='Door Contact') {
						if ($device=='achterdeur') {
							if ($message['nvalue']==0) $status='Open';
							elseif ($message['nvalue']==1) $status='Closed';
						} else {
							if ($message['nvalue']==0) $status='Closed';
							elseif ($message['nvalue']==1) $status='Open';
						}
					} else {
						if ($message['nvalue']==0) $status='Off';
						elseif ($message['nvalue']==1) $status='On';
					}
					lg(' (MQTT) Switch '.$device.' => '.$status);
					store($device, $status, ' (MQTT) Switch <> ');
				} elseif ($message['dtype']=='Lighting 2') {
					if ($message['nvalue']==0) $status='Off';
					elseif ($message['nvalue']==1) $status='On';
					lg(' (MQTT) Lighting 2 '.$device.' => '.$status);
					store($device, $status, ' (MQTT) Switch ');
				} elseif ($message['dtype']=='Temp') {
					$status=$message['svalue1'];
					lg(' (MQTT) Temp '.$device.' => '.$status);	
					store($device, $status,' (MQTT) Temp ');
				} elseif ($message['dtype']=='General') {
					if ($message['stype']=='kWh') {
						$status=$message['svalue1'];
						lg(' (MQTT) kWh '.$device.' => '.$status);	
						store($device, $status,' (MQTT) kWh ');
					}
				} elseif ($message['dtype']=='Usage') {
					$status=$message['svalue1'];
					lg(' (MQTT) Usage '.$device.' => '.$status);	
					store($device, $status,' (MQTT) Usage ');
				} elseif ($message['dtype']=='Color Switch') {
					$status=$message['nvalue'];
					lg(' (MQTT) Colorswitch '.$device.' => '.$status);	
					store($device, $status,' (MQTT) Color ');
				} else {
//					store($device, $message['nvalue']);
					lg(' (MQTT) else '.print_r($message,true));	
				}
//				if ($device=='$ remoteauto') lg(' (MQTT)		'.print_r($message,true));	
				include '/var/www/html/secure/pass2php/'.$device.'.php';
			} elseif ($device=='buiten_hum') { // 1
				$temp=$message['svalue1'];
				$hum=$message['svalue2']+1;
				if ($hum>100) $hum=100;
				if($hum!=$d['buiten_temp']['m']) storemode('buiten_temp', $hum, '', 1);
				if ($temp!=$d['minmaxtemp']['icon']) storeicon('minmaxtemp', $temp);
				store('buiten_hum', $temp);
			} elseif ($device=='kamer_hum') { // 2
				$status=$message['svalue2']-2;
				if ($status!=$d['kamer_temp']['m']) storemode('kamer_temp', $status, '', 1);
				store('kamer_hum', $status);
			} elseif ($device=='alex_hum') { // 3
				$status=$message['svalue2'];
				if ($status!=$d['alex_temp']['m']) storemode('alex_temp', $status, '', 1);
				store('alex_hum', $status);
			} elseif ($device=='waskamer_hum') { // 4
				$status=$message['svalue2'];
				if ($status!=$d['waskamer_temp']['m']) storemode('waskamer_temp', $status, '', 1);
				store('waskamer_hum', $status);
			} elseif ($device=='badkamer_hum') { // 5
				$status=$message['svalue2']+1;
				if ($status>100) $status=100;
				if ($status!=$d['badkamer_temp']['m']) storemode('badkamer_temp', $status, '', 1);
				store('badkamer_hum', $status);
			} elseif ($device=='living_hum') { // 6
				$status=$message['svalue2']-1;
				if ($status!=$d['living_temp']['m']) storemode('living_temp', $status, '', 1);
				store('living_hum', $status);
			} //else lg('no file found for '.$device);
			if ($d['Weg']['m']==1) {
				$db->query("UPDATE devices SET m=0 WHERE n ='Weg';");
				die();
			}
		} elseif ($topic[1]=='in') {
			global $dbname,$dbuser,$dbpass,$user,$domoticzurl;
			$message=json_decode($message, true);
			if ($message['command']=='switchlight') {
				$d=fetchdataidx();
				if ($d[$message['idx']]['dt']=='dimmer') {
					if ($message['switchcmd']=='On') store(null,100,'domoticz in from Homebridge/Homekit',$message['idx']);
					else if ($message['switchcmd']=='Off') store(null,0,'domoticz in from Homebridge/Homekit',$message['idx']);
					else if ($message['switchcmd']=='Set Level') store(null,$message['level'],'domoticz in from Homebridge/Homekit',$message['idx']);
					else lg(print_r($message,true));
				} else {
					if ($message['switchcmd']=='On') store(null,'On','domoticz in from Homebridge/Homekit',$message['idx']);
					else if ($message['switchcmd']=='Off') store(null,'Off','domoticz in from Homebridge/Homekit',$message['idx']);
					else if ($message['switchcmd']=='Set Level') store(null,$message['level'],'domoticz in from Homebridge/Homekit',$message['idx']);
					else lg(print_r($message,true));
				}
			} else lg(print_r($message,true));
		}
	} elseif ($topic[0]=='homeassistant') {
		if (isset($topic[3])&&$topic[3]=='state') {
			global $dbname,$dbuser,$dbpass,$user,$domoticzurl;
			$d=fetchdata();
			$device=$topic[2];
			if (file_exists('/var/www/html/secure/pass2php/'.$device.'.php')) {
				$status=ucfirst($message);
				lg(' (MQTT HASS) Switch	'.$device.'	=> '.$status);
				include '/var/www/html/secure/pass2php/'.$device.'.php';
				//$db->query("INSERT INTO devices (n,s,t) VALUES ('$name','$status','$time') ON DUPLICATE KEY UPDATE s='$status',t='$time';");
			} else lg('no file found for '.$device.' '.print_r($topic, true).'	'.print_r($message,true));
		} else lg(print_r($topic, true).'	'.print_r($message,true));
	} //else lg(print_r($topic, true).'	'.print_r($message,true));
}, MqttClient::QOS_AT_MOST_ONCE);
$client->loop(true);
$client->disconnect();