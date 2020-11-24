<?php
/**
 * Pass2PHP
 * php version 7.3
 *
 * @category Home_Automation
 * @package  Pass2PHP
 * @author   Guy Verschuere <guy@egregius.be>
 * @license  GNU GPLv3
 * @link	 https://egregius.be
 **/
//require '/var/www/html/secure/functions.php';
$db=dbconnect();

$domoticz=json_decode(
	file_get_contents(
		$domoticzurl.'/json.htm?type=devices&used=true'
	),
	true
);
if ($domoticz) {
	foreach ($domoticz['result'] as $dom) {
		if ($dom['idx']!=1366) {
			$name=$dom['Name'];
			$idx=(int)$dom['idx'];
			if (isset($dom['SwitchType'])) {
				$switchtype=$dom['SwitchType'];
			} elseif (isset($dom['SubType'])) {
				$switchtype=$dom['SubType'];
			} else {
				$switchtype='none';
			}
			if($switchtype=='On/Off')$type='switch';
			elseif($switchtype=='Contact'){$type='contact';$idx=null;}
			elseif($switchtype=='Door Contact'){$type='contact';$idx=null;}
			elseif($switchtype=='Motion Sensor'){$type='pir';$idx=null;}
			elseif($switchtype=='Push On Button'){$type='';$idx=null;}
			elseif($switchtype=='X10 Siren')$type='';
			elseif($switchtype=='Smoke Detector')$type='';
			elseif($switchtype=='Selector')$type='';
			elseif($switchtype=='Blinds Inverted')$type='';
			else $type=$switchtype;
			if ($dom['Type']=='Temp') {
				$status=$dom['Temp'];
				$type='thermometer';
				$idx=0;
			} elseif ($dom['Type']=='Temp + Humidity') {
				$status=$dom['Temp'];
				$type='thermometer';
				$idx=0;
			} elseif ($dom['TypeImg']=='current') {
				$status=str_replace(' Watt', '', $dom['Data']);
				$idx=0;
			} elseif ($name=='luifel') {
				$status=str_replace('%', '', $dom['Level']);
				$type='luifel';
			} elseif ($switchtype=='Dimmer') {
				if ($dom['Data']=='Off') {
					$status=0;
				} elseif ($dom['Data']=='On') {
					$status=100;
				} else {
					$status=filter_var($dom['Data'], FILTER_SANITIZE_NUMBER_INT);
				}
				$type='dimmer';
			} elseif ($switchtype=='Blinds Percentage') {
				if ($dom['Data']=='Open') {
					$status=0;
				} elseif ($dom['Data']=='Closed') {
					$status=100;
				} else {
					$status=filter_var($dom['Data'], FILTER_SANITIZE_NUMBER_INT);
				}
				$type='rollers';
			} elseif ($name=='achterdeur') {
				if ($dom['Data']=='Open') {
					$status='Closed';
				} else {
					$status='Open';
				}
			} else {
				$status=$dom['Data'];
			}
			if (isset($dom['LastUpdate'])) {
				$time=strtotime($dom['LastUpdate']);
			} else {
				$time=TIME;
			}
			echo $idx.' '.$name.' = ';
			$db->query("INSERT INTO devices (n,i,s,t,dt) VALUES ('$name','$idx','$status','$time','$type') ON DUPLICATE KEY UPDATE s='$status',i='$idx',t='$time',dt='$type';");
			if (php_sapi_name() === 'cli') $status.PHP_EOL;
			else $status.'<br>';
		}
	}
}

function shutdownHandler()
{
    $error = error_get_last();
    if ($error['type'] == E_ERROR) {
        your code goes here;
    }
}
register_shutdown_function('shutdownHandler');