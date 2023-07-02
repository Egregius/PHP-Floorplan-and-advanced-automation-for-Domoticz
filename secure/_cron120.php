<?php
$d=fetchdata();
dag();
$user='cron120';
$stamp=strftime("%F %T", $time-900);
$sql="SELECT AVG(buiten) AS buiten, AVG(living) AS living, AVG(badkamer) AS badkamer, AVG(kamer) AS kamer, AVG(waskamer) AS waskamer, AVG(alex) AS alex, AVG(zolder) AS zolder FROM `temp` WHERE stamp>='$stamp'";
if(isset($db)) $db=dbconnect();
$result=$db->query($sql);
while ($row = $result->fetch(PDO::FETCH_ASSOC)) $avg=$row;
foreach (array('buiten', 'living', 'badkamer', 'kamer', 'waskamer', 'alex', 'zolder') as $i) {
	$diff=$d[$i.'_temp']['s']-$avg[$i];
	if ($d[$i.'_temp']['icon']!=$diff) storeicon($i.'_temp', $diff, basename(__FILE__).':'.__LINE__);
	if ($d[$i.'_temp']['m']==1&&past($i.'_temp')>21600) storemode($i.'_temp', 0, basename(__FILE__).':'.__LINE__);
}


$domoticz=json_decode(file_get_contents($domoticzurl.'/json.htm?type=devices&used=true'),true);
if ($domoticz) {
	foreach ($domoticz['result'] as $dom) {
		$update=false;
		$name=$dom['Name'];
		if (isset($dom['SwitchType'])) $switchtype=$dom['SwitchType'];
		elseif (isset($dom['SubType'])) $switchtype=$dom['SubType'];
		if($switchtype=='On/Off') $update=true;
		elseif($switchtype=='Switch') $update=true;
		elseif($switchtype=='Contact') $update=true;
		elseif($switchtype=='Door Contact') $update=true;
		elseif($switchtype=='Motion Sensor') $update=true;
		elseif($switchtype=='Push On Button') $update=true;
		elseif($switchtype=='X10 Siren') $update=true;
		elseif($switchtype=='Smoke Detector') $update=true;
		elseif($switchtype=='Selector') $update=true;
		elseif($switchtype=='Blinds Inverted') $update=true;
		if ($dom['Type']=='Temp') {
			$status=$dom['Temp'];
			 $update=false;
		} elseif ($dom['Type']=='Temp + Humidity') {
			$status=$dom['Temp'];
			 $update=false;
		} elseif ($dom['TypeImg']=='current') {
			$status=str_replace(' Watt', '', $dom['Data']);
			 $update=false;
		} elseif ($name=='luifel') {
			$status=str_replace('%', '', $dom['Level']);
			 $update=true;
		} elseif ($switchtype=='Dimmer') {
			if ($dom['Data']=='Off') $status=0;
			elseif ($dom['Data']=='On') $status=100;
			else $status=filter_var($dom['Data'], FILTER_SANITIZE_NUMBER_INT);
			 $update=true;
		} elseif ($switchtype=='Blinds Percentage') {
			if ($dom['Data']=='Open') $status=0;
			elseif ($dom['Data']=='Closed') $status=100;
			else $status=filter_var($dom['Data'], FILTER_SANITIZE_NUMBER_INT);
			$update=true;
		} elseif ($name=='achterdeur') {
			if ($dom['Data']=='Open') $status='Closed';
			else $status='Open';
		} else $status=$dom['Data'];
		if ($update==true) {
			if ($status!=$d[$name]['s']) {
				echo $name.'	= '.$status.'<br>';
				$query="UPDATE devices SET s=:status WHERE n=:name;";
				$stmt=$db->prepare($query);
				$stmt->execute(array(':status'=>$status, ':name'=>$name));
			}
		}
	}
}
