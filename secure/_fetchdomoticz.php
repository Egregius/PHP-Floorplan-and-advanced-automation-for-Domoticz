<?php
$user=basename(__FILE__);
if (!isset($db)) $db=dbconnect(basename(__FILE__).':'.__LINE__);
$domoticz=json_decode(
	file_get_contents(
		$domoticzurl.'/json.htm?type=command&param=getdevices&used=true'
	),
	true
);
if ($domoticz) {
	foreach ($domoticz['result'] as $dom) {
//		if ($dom['idx']>2000) {
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
			elseif($switchtype=='Contact'){$type='contact';$idx=0;}
			elseif($switchtype=='Door Contact'){$type='contact';$idx=0;}
			elseif($switchtype=='Motion Sensor'){$type='pir';$idx=0;}
			elseif($switchtype=='Push On Button'){$type='';$idx=0;}
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
			}
			echo $idx.' '.$name.' = ';
			$query="INSERT INTO devices (n,i,s,dt) VALUES ('$name','$idx','$status','$type') ON DUPLICATE KEY UPDATE i='$idx',s='$status',dt='$type';";
			lg($query);
			$db->query($query);
			if (php_sapi_name() === 'cli') $status.PHP_EOL;
			else $status.'<br>';
			echo php_sapi_name();
//		}
	}
}
