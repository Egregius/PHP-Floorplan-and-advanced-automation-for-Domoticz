#!/usr/bin/php
<?php
declare(strict_types=1);
ini_set( 'error_reporting', E_ALL );
ini_set( 'display_errors', true );

$user='MQTT';
$time=time();
$file=basename(__FILE__);
// Using https://github.com/php-mqtt/client
require '/var/www/vendor/autoload.php';
require '/var/www/html/secure/functions.php';

//Setting some temp variables
$d=fetchdata(0,basename(__FILE__).':'.__LINE__);
$lastfetch=$time;

lg(' Starting MQTT loop...',1);
updatefromdomoticz();
use PhpMqtt\Client\MqttClient;

$mqtt=new MqttClient('127.0.0.1',1883,$file,MqttClient::MQTT_3_1,null,null);
$mqtt->connect();
$mqtt->subscribe('#', function (string $topic, string $message, bool $retained) use ($mqtt) {
	$topic=explode('/', $topic);
	if ($topic[0]=='domoticz') {
		if ($topic[1]=='out') {
			global $db,$dbname,$dbuser,$dbpass,$user,$domoticzurl,$d,$time,$lastfetch,$xlight;
			$time=time();
			$d=fetchdata($lastfetch,basename(__FILE__).':'.__LINE__);
			$lastfetch=$time;
			$message=json_decode($message, true);
			if (isset($message['name'])) {
				$device=$message['name'];
				$status=$message['svalue1'];
				if (file_exists('/var/www/html/secure/pass2php/'.$device.'.php')) {
	//				if (isset($message['switchType'])) lg(__LINE__.' device='.$device. '	type='.$message['switchType']);
	//				else lg(__LINE__.' device='.$device. '	message='.print_r($message, true));
					if ($message['dtype']=='Light/Switch') {
						if ($message['switchType']=='Dimmer') {
							$type='DIMMER';
							$name=$device;
							if ($message['nvalue']==0) $status=0;
							else $status=$message['svalue1'];
						} elseif ($message['switchType']=='Blinds Percentage') {
							$type='ROLLERS	';
							$name=substr($device, 1);
							if ($message['nvalue']==0) $status=0;
							elseif ($message['nvalue']==1) $status=100;
							else $status=$message['svalue1'];
						} elseif ($message['switchType']=='Contact') {
							$type='CONTACT	';
							$name=$device;
							if ($message['nvalue']==0) $status='Closed';
							elseif ($message['nvalue']==1) $status='Open';
						} elseif ($message['switchType']=='Door Contact') {
							$type='CONTACT	';
							$name=$device;
							if ($device=='achterdeur') {
								if ($message['nvalue']==0) $status='Open';
								elseif ($message['nvalue']==1) $status='Closed';
							} else {
								if ($message['nvalue']==0) $status='Closed';
								elseif ($message['nvalue']==1) $status='Open';
							}
						} elseif ($message['switchType']=='On/Off') {
							$type='SWITCH	';
							$name=$device;
							if ($message['nvalue']==0) $status='Off';
							elseif ($message['nvalue']==1) $status='On';
						} elseif ($message['switchType']=='Motion Sensor') {
							$type='PIR	';
							$name=substr($device, 3);
							if ($message['nvalue']==0) $status='Off';
							elseif ($message['nvalue']==1) $status='On';
						} else {
							$type=$message['switchType'];
							$name=$device;
							if ($message['nvalue']==0) $status='Off';
							elseif ($message['nvalue']==1) $status='On';
						}
						if ($status!=$d[$device]['s']||substr($device,0,1)=='$') {
							lg('(MQTT) '.$type.$name.' => '.$status, 4);
							store($device, $status, ' (MQTT) Switch <> ');
						}
					} elseif ($message['dtype']=='Lighting 2') {
						if ($message['nvalue']==0) $status='Off';
						elseif ($message['nvalue']==1) $status='On';
						if ($status!=$d[$device]['s']) {
							lg('(MQTT) Lighting 2 '.$device.' => '.$status,4);
							store($device, $status, ' (MQTT) Switch ');
						}
					} elseif ($message['dtype']=='Temp') {
						$status=$message['svalue1'];
						if ($status!=$d[$device]['s']) {
							if ($status>$d[$device]['s']+0.1) $status=$d[$device]['s']+0.1;
							elseif ($status<$d[$device]['s']-0.1) $status=$d[$device]['s']-0.1;
							lg('(MQTT) Temp	'.$device.' => '.$status,9);	
							store($device, $status,' (MQTT) Temp ');
						}
					} elseif ($message['dtype']=='General') {
						if ($message['stype']=='kWh') {
							$status=$message['svalue1'];
							if ($status!=$d[$device]['s']) {
								lg('(MQTT) kWh	'.$device.' = '.$status,8);	
								store($device, $status,' (MQTT) kWh ');
							}
						}
					} elseif ($message['dtype']=='Usage') {
						
						$status=$message['svalue1'];
						if ($status!=$d[$device]['s']) {
							lg('(MQTT) Usage '.$device.' = '.$status,8);	
							store($device, $status,' (MQTT) Usage ');
						}
					} elseif ($message['dtype']=='Color Switch') {
						$status=$message['nvalue'];
						if ($status!=$d[$device]['s']) {
							lg('(MQTT) Colorswitch '.$device.' = '.$status,4);	
							store($device, $status,' (MQTT) Color ');
						}
					} elseif ($message['dtype']=='Setpoint') {
						$status=(float)$message['svalue1'];
						if ($status!=$d[$device]['s']) {
							lg('(MQTT) Setpoint '.$device.' = '.$status,5);	
							store($device, $status,' (MQTT) Setpoint ');
						}
					} else {
	//					store($device, $message['nvalue']);
						lg('(MQTT) else '.print_r($message,true),1);	
					}
	//				if ($device=='$ remoteauto') lg(' (MQTT)		'.print_r($message,true));	
					include '/var/www/html/secure/pass2php/'.$device.'.php';
	
				} elseif ($device=='buiten_hum') { // 1
					$temp=$message['svalue1'];
					$hum=$message['svalue2']+1;
					if ($hum>100) $hum=100;
					elseif($hum>$d['buiten_temp']['m']+1) $hum=$d['buiten_temp']['m']+1;
					elseif($hum<$d['buiten_temp']['m']-1) $hum=$d['buiten_temp']['m']-1;
					if($hum!=$d['buiten_temp']['m']) storemode('buiten_temp', $hum, '', 1);
					if ($temp!=$d['minmaxtemp']['icon']) storeicon('minmaxtemp', $temp);
					if ($status!=$d['buiten_hum']['s']) store('buiten_hum', $hum);
				} elseif ($device=='kamer_hum') { // 2
					$hum=$message['svalue2']-7;
					if ($hum>100) $hum=100;
					elseif($hum>$d['kamer_temp']['m']+1) $hum=$d['kamer_temp']['m']+1;
					elseif($hum<$d['kamer_temp']['m']-1) $hum=$d['kamer_temp']['m']-1;
					if ($hum!=$d['kamer_temp']['m']) storemode('kamer_temp', $hum, '', 1);
					if ($hum!=$d['kamer_hum']['s']) store('kamer_hum', $hum);
				} elseif ($device=='alex_hum') { // 3
					$hum=$message['svalue2']-9;
					if ($hum>100) $hum=100;
					elseif($hum>$d['alex_temp']['m']+1) $hum=$d['alex_temp']['m']+1;
					elseif($hum<$d['alex_temp']['m']-1) $hum=$d['alex_temp']['m']-1;
					if ($hum!=$d['alex_temp']['m']) storemode('alex_temp', $hum, '', 1);
					if ($hum!=$d['alex_hum']['s']) store('alex_hum', $hum);
				} elseif ($device=='waskamer_hum') { // 4
					$status=$message['svalue1'];
					if ($status!=$d['waskamer_temp']['s']) store('waskamer_temp', $status);
					$hum=$message['svalue2']+3;
					if ($hum>100) $hum=100;
					elseif($hum>$d['waskamer_temp']['m']+1) $hum=$d['waskamer_temp']['m']+1;
					elseif($hum<$d['waskamer_temp']['m']-1) $hum=$d['waskamer_temp']['m']-1;
					if ($hum!=$d['waskamer_temp']['m']) storemode('waskamer_temp', $hum, '', 1);
					if ($hum!=$d['waskamer_hum']['s']) store('waskamer_hum', $hum);
				} elseif ($device=='badkamer_hum') { // 5
					$hum=$message['svalue2']-7;
					if ($hum>100) $hum=100;
					elseif($hum>$d['badkamer_temp']['m']+1) $hum=$d['badkamer_temp']['m']+1;
					elseif($hum<$d['badkamer_temp']['m']-1) $hum=$d['badkamer_temp']['m']-1;
					if ($hum!=$d['badkamer_temp']['m']) storemode('badkamer_temp', $hum, '', 1);
					if ($hum!=$d['badkamer_hum']['s']) store('badkamer_hum', $hum);
				} elseif ($device=='living_hum') { // 6
					$hum=$message['svalue2']-3;
					if ($hum>100) $hum=100;
					elseif($hum>$d['living_temp']['m']+1) $hum=$d['living_temp']['m']+1;
					elseif($hum<$d['living_temp']['m']-1) $hum=$d['living_temp']['m']-1;
					if ($hum!=$d['living_temp']['m']) storemode('living_temp', $hum, '', 1);
					if ($hum!=$d['living_hum']['s']) store('living_hum', $hum);
				}// else lg('no file found for '.$device);
			}
			if ($d['Weg']['m']==1) {
				lg('Stopping MQTT Loop...');
				storemode('Weg', 0, '', 1);
				$mqtt->disconnect();
				exec('kill -9 ' . getmypid());
			} elseif ($d['Weg']['m']==3) {
				lg('Stopping MQTT Loop...');
				storemode('Weg', 2, '', 1);
				$mqtt->disconnect();
				exec('kill -9 ' . getmypid());
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
			} //else lg(print_r($message,true));
		}
	} elseif ($topic[0]=='homeassistant') {
		if (isset($topic[3])&&$topic[3]=='state') {
			global $db,$dbname,$dbuser,$dbpass,$d,$user,$domoticzurl,$time,$lastfetch;
			$d=fetchdata($lastfetch,basename(__FILE__).':'.__LINE__);
			$lastfetch=$time;
			$device=$topic[2];
			if (file_exists('/var/www/html/secure/pass2php/'.$device.'.php')) {
				$status=ucfirst($message);
				if (isset($d[$device]['s'])&&$status!=$d[$device]['s']) {
					store($device, $status, ' (HASS) State <> ');
					include '/var/www/html/secure/pass2php/'.$device.'.php';
				}
				
			}// else lg('no file found for '.$device.' '.print_r($topic, true).'	'.print_r($message,true));
		}// else lg(__LINE__.':'.print_r($topic, true).'	'.print_r($message,true));
	} // else lg(__LINE__.':'.print_r($topic, true).'	'.print_r($message,true));
}, MqttClient::QOS_AT_MOST_ONCE);
$mqtt->loop(true);
$mqtt->disconnect();

function floorToFraction($number, $denominator = 1) {
	return floor($number*$denominator)/$denominator;
}
function lonToFraction($lat) {
	return round((90-$lat)*50, 0);
}
