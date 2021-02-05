<?php
/**
 * Pass2PHP
 * php version 8.0
 *
 * @category Home_Automation
 * @package  Pass2PHP
 * @author   Guy Verschuere <guy@egregius.be>
 * @license  GNU GPLv3
 * @link	 https://egregius.be
 **/
//lg(__FILE__.':'.$s);
$user='cron300';
if (TIME<=strtotime('9:00')) {
	if ($d['nas']['s']!='On') {
		if (file_get_contents($urlnas)>0) {
			$k=file_get_contents($urlnas2);
			if ($k<4000000000) {
				shell_exec('./wakenas.sh');
				lg('Wake NAS');
			}
		}
	}
}
if(isset($db)) $db=dbconnect();
$stmt=$db->query("SELECT SUM(`buien`) AS buien FROM regen;");
while ($row=$stmt->fetch(PDO::FETCH_ASSOC)) $rainpast=$row['buien'];
if ($rainpast==0) $rainpast=1;
if ($d['minmaxtemp']['m'] > -3) {
	if ($rainpast>25000) $pomppauze=3310;
	elseif ($rainpast>22000) $pomppauze=5110;
	elseif ($rainpast>19000) $pomppauze=6910;
	elseif ($rainpast>16000) $pomppauze=10510;
	elseif ($rainpast>13000) $pomppauze=21310;
	elseif ($rainpast>10000) $pomppauze=42910;
	elseif ($rainpast>7000) $pomppauze=86110;
	elseif ($rainpast>3000) $pomppauze=129310;
	elseif ($rainpast>1000) $pomppauze=258910;
	else $pomppauze=2591990;
	if ($d['regenpomp']['s']=='Off'&&past('regenpomp')>=$pomppauze) sw('regenpomp', 'On', basename(__FILE__).':'.__LINE__.' '.'Pomp pauze = '.$pomppauze.', maxtemp = '.$d['minmaxtemp']['m'].'°C, rainpast = '.$rainpast);
}

// Eerste blok voor zwembad
/*if ($d['zwembadfilter']['s']=='On') {
	if (past('zwembadfilter')>10700
		&&TIME>strtotime("16:00")
		&&$d['zwembadwarmte']['s']=='Off'
		&&$d['buiten_temp']['s']<27
	) {
		sw('zwembadfilter','Off', basename(__FILE__).':'.__LINE__);
	}
}else{
	if (
			(past('zwembadfilter')>10700&&TIME>strtotime("12:59")&&TIME<strtotime("15:59"))
			||
			(past('zwembadfilter')>10700&&$d['buiten_temp']['s']>27)
	   ) {
	   	sw('zwembadfilter','On', basename(__FILE__).':'.__LINE__);
	}
}
if ($d['zwembadwarmte']['s']=='On') {
	if (past('zwembadwarmte')>86398) {
		sw('zwembadwarmte','Off', basename(__FILE__).':'.__LINE__);
	}
	if ($d['zwembadfilter']['s']=='Off') {
		sw('zwembadfilter','On', basename(__FILE__).':'.__LINE__);
	}
}*/

// Tweede blok indien geen zwembad
if ($d['achterdeur']['s']=='Open') {
	if ($d['zwembadfilter']['s']=='Off') sw('zwembadfilter','On', basename(__FILE__).':'.__LINE__);
	if ($d['zwembadwarmte']['s']=='Off') sw('zwembadwarmte','On', basename(__FILE__).':'.__LINE__);
} else {
	if ($d['zwembadfilter']['s']=='On') sw('zwembadfilter','Off', basename(__FILE__).':'.__LINE__);
	if ($d['zwembadwarmte']['s']=='On') sw('zwembadwarmte','Off', basename(__FILE__).':'.__LINE__);
}

if (past('diepvries_temp')>7200) alert('diepvriestemp','Diepvries temp not updated since '.strftime("%k:%M:%S", $d['diepvries_temp']['t']),7200);
if (TIME>=strtotime('9:00')&&TIME<=strtotime('16:00')&&($d['zon']['s']-$d['el']['s'])>300) alert(	'wasmachien','Wasmachien checken'.PHP_EOL.($d['zon']['s']-$d['el']['s']).' W overschot',43200);
if ($d['auto']['s']!='On'&&past('auto')>10795) sw('auto', 'On', basename(__FILE__).':'.__LINE__);
if (past('Weg')>14400&& $d['Weg']['s']==0&& past('pirliving')>14400&& past('pirkeuken')>14400&& past('pirinkom')>14400&& past('pirhall')>14400&& past('pirgarage')>14400) {
	store('Weg', 1, basename(__FILE__).':'.__LINE__);
	telegram('Slapen ingeschakeld na 4 uur geen beweging', false, 2);
} elseif (past('Weg')>36000&& $d['Weg']['s']==1&& past('pirliving')>36000&& past('pirkeuken')>36000&& past('pirinkom')>36000&& past('pirhall')>36000&& past('pirgarage')>36000) {
	store('Weg', 2, basename(__FILE__).':'.__LINE__);
	telegram('Weg ingeschakeld na 10 uur geen beweging', false, 2);
}
if ($d['zolderg']['s']=='On'&&past('zolderg')>7200&&past('pirgarage')>7200) sw('zolderg', 'Off', basename(__FILE__).':'.__LINE__);

if ($d['GroheRed']['m']>0&&$d['GroheRed']['s']=='On'&&past('GroheRed')>3600&&past('pirkeuken')>3600) {
	sw('GroheRed', 'Off', basename(__FILE__).':'.__LINE__);
	storemode('GroheRed', 0, basename(__FILE__).':'.__LINE__);
}
$items=array('Rliving', 'Rbureel', 'RkeukenL', 'RkeukenR');
foreach ($items as $i) {
	if (past($i)>10800&&$d[$i]['m']!=0) storemode($i, 0, basename(__FILE__).':'.__LINE__);
}

if ($d['bose103']['s']=='On'&&$d['Weg']['s']==1) {
	$nowplaying=json_decode(json_encode(simplexml_load_string(file_get_contents('http://192.168.2.103:8090/now_playing'))),true);
	if (!empty($nowplaying)) {
		if (isset($nowplaying['@attributes']['source'])) {
			if ($nowplaying['@attributes']['source']!='STANDBY') {
				$volume=json_decode(json_encode(simplexml_load_string(file_get_contents("http://192.168.2.103:8090/volume")	)),true);
				$cv=$volume['actualvolume']-1;
				if ($cv<=8) {
					bosekey("POWER", 0, 103);
					sw('bose103', 'Off', basename(__FILE__).':'.__LINE__);
				} else bosevolume($cv, 103, basename(__FILE__).':'.__LINE__);
			} else sw('bose103', 'Off', basename(__FILE__).':'.__LINE__);
		}
	}
}
$battery=apcu_fetch('ring-battery');

if ((TIME>=strtotime('11:00')&&TIME<strtotime('19:30'))||$battery<50) {
	if (($d['ringdoorbell']['s']=='Off'&&past('ringdoorbell')>28800)||($d['ringdoorbell']['s']=='Off'&&$battery<50)) sw('ringdoorbell', 'On', basename(__FILE__).':'.__LINE__.' battery='.$battery);
}/* elseif ((TIME<strtotime('6:00')||TIME>=strtotime('22:00')||$battery>=80)&&$battery>60) {
	if ($d['ringdoorbell']['s']=='On'&&past('ringdoorbell')>28800) sw('ringdoorbell', 'Off', basename(__FILE__).':'.__LINE__.' battery='.$battery);
}*/
$ctx=stream_context_create(array('http'=>array('timeout'=>10)));
$data=json_decode(file_get_contents('https://verbruik.egregius.be/tellerjaar.php',false,$ctx),true);
if (!empty($data)) {
	store('jaarteller', $data['jaarteller'], basename(__FILE__).':'.__LINE__);
	if ($data['zonpercent']!=$d['zonvandaag']['m']) storemode('zonvandaag', $data['zonpercent'], basename(__FILE__).':'.__LINE__);
}
if ($d['daikin']['s']=='On'&&past('daikin')>118) {
	foreach (array('living', 'kamer', 'alex') as $k) {
		if ($k=='living') $ip=111;
		elseif ($k=='kamer') $ip=112;
		elseif ($k=='alex') $ip=113;
		sleep(2);
		$data=file_get_contents('http://192.168.2.'.$ip.'/aircon/get_day_power_ex');
		$data=explode(',', $data);
		if ($data[0]=='ret=OK') {
			$curr_day_heat=explode('=', $data[1]);
			${$k.'heat'}=array_sum(explode('/', $curr_day_heat[1]));
			$prev_1day_heat=explode('=', $data[2]);
			${$k.'prevheat'}=array_sum(explode('/', $prev_1day_heat[1]));
			$curr_day_cool=explode('=', $data[3]);
			${$k.'cool'}=array_sum(explode('/', $curr_day_cool[1]));
			$prev_1day_cool=explode('=', $data[4]);
			${$k.'prevcool'}=array_sum(explode('/', $prev_1day_cool[1]));
		}
	//print_r($data);
	}
	$date=strftime('%F', TIME);
	if (!isset($db)) $db=dbconnect();
	$db->query("INSERT INTO daikin (date,livingheat,livingcool,kamerheat,kamercool,alexheat,alexcool) VALUES ('$date','$livingheat','$livingcool','$kamerheat','$kamercool','$alexheat','$alexcool') ON DUPLICATE KEY UPDATE date='$date',livingheat='$livingheat',livingcool='$livingcool',kamerheat='$kamerheat',kamercool='$kamercool',alexheat='$alexheat',alexcool='$alexcool';");
	$date=strftime('%F', TIME-86400);
	$db->query("INSERT INTO daikin (date,livingheat,livingcool,kamerheat,kamercool,alexheat,alexcool) VALUES ('$date','$livingprevheat','$livingprevcool','$kamerprevheat','$kamerprevcool','$alexprevheat','$alexprevcool') ON DUPLICATE KEY UPDATE date='$date',livingheat='$livingprevheat',livingcool='$livingprevcool',kamerheat='$kamerprevheat',kamercool='$kamerprevcool',alexheat='$alexprevheat',alexcool='$alexprevcool';");
}

$chauth = curl_init('https://app1pub.smappee.net/dev/v1/oauth2/token?grant_type=password&client_id='.$smappeeclient_id.'&client_secret='.$smappeeclient_secret.'&username='.$smappeeusername.'&password='.$smappeepassword.'');
curl_setopt($chauth, CURLOPT_AUTOREFERER, true);
curl_setopt($chauth, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($chauth, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($chauth, CURLOPT_VERBOSE, 0);
curl_setopt($chauth, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($chauth, CURLOPT_SSL_VERIFYPEER, false);
$objauth=json_decode(curl_exec($chauth));
if (!empty($objauth)) {
	echo 'Kwartaal<br>';
	$access=$objauth->{'access_token'};
	curl_close($chauth);
	$timefrom=TIME-(86400*185);
	$ch=curl_init('');
	curl_setopt($ch, CURLOPT_HEADER, 0);
	$headers=array('Authorization: Bearer '.$access);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ch, CURLOPT_AUTOREFERER, true);
	curl_setopt($ch, CURLOPT_URL, 'https://app1pub.smappee.net/dev/v1/servicelocation/'.$smappeeserviceLocationId.'/consumption?aggregation=5&from='.$timefrom.'000&to='.TIME.'000');
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_VERBOSE, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	$data=json_decode(curl_exec($ch), true);
	if (!empty($data['consumptions'])) {
		foreach ($data['consumptions'] as $i) {
			echo strftime("%F %T", $i['timestamp']/1000).'<br>';
			$timestamp=$i['timestamp']/1000;
			$consumption=$i['consumption'];
			$solar=$i['solar'];
			$alwaysOn=$i['alwaysOn'];
			$gridImport=$i['gridImport'];
			$gridExport=$i['gridExport'];
			$selfConsumption=$i['selfConsumption'];
			$selfSufficiency=$i['selfSufficiency'];
			$db->query("INSERT INTO smappee_kwartaal (timestamp, consumption,solar,alwaysOn,gridImport,gridExport,selfConsumption,selfSufficiency) VALUES ('$timestamp', '$consumption', '$solar', '$alwaysOn', '$gridImport', '$gridExport', '$selfConsumption', '$selfSufficiency') ON DUPLICATE KEY UPDATE consumption='$consumption', solar='$solar', alwaysOn='$alwaysOn', gridImport='$gridImport', gridExport='$gridExport', selfConsumption='$selfConsumption', selfSufficiency='$selfSufficiency';");
		}
	}
	curl_close($ch);
	echo '<hr>Maand<br>';
	$timefrom=TIME-(86400*60);
	$ch=curl_init('');
	curl_setopt($ch, CURLOPT_HEADER, 0);
	$headers=array('Authorization: Bearer '.$access);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ch, CURLOPT_AUTOREFERER, true);
	curl_setopt($ch, CURLOPT_URL, 'https://app1pub.smappee.net/dev/v1/servicelocation/'.$smappeeserviceLocationId.'/consumption?aggregation=4&from='.$timefrom.'000&to='.TIME.'000');
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_VERBOSE, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	$data=json_decode(curl_exec($ch), true);
	if (!empty($data['consumptions'])) {
		foreach ($data['consumptions'] as $i) {
			echo strftime("%F %T", $i['timestamp']/1000).'<br>';
			$timestamp=$i['timestamp']/1000;
			$consumption=$i['consumption'];
			$solar=$i['solar'];
			$alwaysOn=$i['alwaysOn'];
			$gridImport=$i['gridImport'];
			$gridExport=$i['gridExport'];
			$selfConsumption=$i['selfConsumption'];
			$selfSufficiency=$i['selfSufficiency'];
			$db->query("INSERT INTO smappee_maand (timestamp, consumption,solar,alwaysOn,gridImport,gridExport,selfConsumption,selfSufficiency) VALUES ('$timestamp', '$consumption', '$solar', '$alwaysOn', '$gridImport', '$gridExport', '$selfConsumption', '$selfSufficiency') ON DUPLICATE KEY UPDATE consumption='$consumption', solar='$solar', alwaysOn='$alwaysOn', gridImport='$gridImport', gridExport='$gridExport', selfConsumption='$selfConsumption', selfSufficiency='$selfSufficiency';");
		}
	}
	curl_close($ch);
	echo '<hr>Dag<br>';
	$timefrom=TIME-(86400*3);
	$ch=curl_init('');
	curl_setopt($ch, CURLOPT_HEADER, 0);
	$headers=array('Authorization: Bearer '.$access);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ch, CURLOPT_AUTOREFERER, true);
	curl_setopt($ch, CURLOPT_URL, 'https://app1pub.smappee.net/dev/v1/servicelocation/'.$smappeeserviceLocationId.'/consumption?aggregation=3&from='.$timefrom.'000&to='.TIME.'000');
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_VERBOSE, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	$data=json_decode(curl_exec($ch), true);
	if (!empty($data['consumptions'])) {
		foreach ($data['consumptions'] as $i) {
			echo strftime("%F %T", $i['timestamp']/1000).'<br>';
			$timestamp=$i['timestamp']/1000;
			$consumption=$i['consumption'];
			$solar=$i['solar'];
			$alwaysOn=$i['alwaysOn'];
			$gridImport=$i['gridImport'];
			$gridExport=$i['gridExport'];
			$selfConsumption=$i['selfConsumption'];
			$selfSufficiency=$i['selfSufficiency'];
			$db->query("INSERT INTO smappee_dag (timestamp, consumption,solar,alwaysOn,gridImport,gridExport,selfConsumption,selfSufficiency) VALUES ('$timestamp', '$consumption', '$solar', '$alwaysOn', '$gridImport', '$gridExport', '$selfConsumption', '$selfSufficiency') ON DUPLICATE KEY UPDATE consumption='$consumption', solar='$solar', alwaysOn='$alwaysOn', gridImport='$gridImport', gridExport='$gridExport', selfConsumption='$selfConsumption', selfSufficiency='$selfSufficiency';");
		}
		$vv=round($data['consumptions'][2]['consumption']/1000, 1);
		if ($d['el']['m']!=$vv) storemode('el', $vv, basename(__FILE__).':'.__LINE__);
		$zonvandaag=round($data['consumptions'][2]['solar']/1000, 1);
		if ($d['zonvandaag']['s']!=$zonvandaag) store('zonvandaag', $zonvandaag, basename(__FILE__).':'.__LINE__);
		$gas=$d['gasvandaag']['s']/100;
		$water=$d['watervandaag']['s']/1000;
		@file_get_contents($vurl."verbruik=$vv&gas=$gas&water=$water&zon=$zonvandaag");
	}
	curl_close($ch);
	$timefrom=TIME-600;
	$ch=curl_init('');
	curl_setopt($ch, CURLOPT_HEADER, 0);
	$headers=array('Authorization: Bearer '.$access);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ch, CURLOPT_AUTOREFERER, true);
	curl_setopt($ch, CURLOPT_URL, 'https://app1pub.smappee.net/dev/v1/servicelocation/'.$smappeeserviceLocationId.'/consumption?aggregation=1&from='.$timefrom.'000&to='.TIME.'000');
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_VERBOSE, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	$data=json_decode(curl_exec($ch), true);
	print_r($data);
	if (!empty($data['consumptions'])) {
		foreach ($data['consumptions'] as $i) {
			echo strftime("%F %T", $i['timestamp']/1000).'<br>';
			storeicon('el', $i['alwaysOn']);
		}
	}
	curl_close($ch);
}
unset($data);
