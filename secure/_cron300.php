<?php
$d=fetchdata();
$user='cron300';
if(isset($db)) $db=dbconnect();


// BEGIN EERSTE BLOK INDIEN ZWEMBAD
/*if ($d['steenterras']['s']=='On') {
	if (past('steenterras')>10700
		&&$time>strtotime("16:00")
		&&$d['houtterras']['s']=='Off'
		&&$d['buiten_temp']['s']<27
	) {
		sw('steenterras','Off', basename(__FILE__).':'.__LINE__);
	}
}else{
	if (
			(past('steenterras')>10700&&$time>strtotime("12:59")&&$time<strtotime("15:59"))
			||
			(past('steenterras')>10700&&$d['buiten_temp']['s']>27)
	   ) {
	   	sw('steenterras','On', basename(__FILE__).':'.__LINE__);
	}
}
if ($d['houtterras']['s']=='On') {
	if (past('houtterras')>86398) {
		sw('houtterras','Off', basename(__FILE__).':'.__LINE__);
	}
	if ($d['steenterras']['s']=='Off') {
		sw('steenterras','On', basename(__FILE__).':'.__LINE__);
	}
}*/
//EINDE EERSTE BLOK INDIEN ZWEMBAD

// BEGIN TWEEDE BLOK INDIEN GEEN ZWEMBAD
//if ($d['achterdeur']['s']=='Open') {
//	if ($d['steenterras']['s']=='Off') sw('steenterras','On', basename(__FILE__).':'.__LINE__);
//	if ($d['houtterras']['s']=='Off') sw('houtterras','On', basename(__FILE__).':'.__LINE__);
//} else {
//	if ($d['steenterras']['s']=='On') sw('steenterras','Off', basename(__FILE__).':'.__LINE__);
//	if ($d['houtterras']['s']=='On') sw('houtterras','Off', basename(__FILE__).':'.__LINE__);
//}
//EINDE TWEEDE BLOK INDIEN GEEN ZWEMBAD

if ($d['kookplaat']['s']=='On') {
	if ($d['kookplaatpower_kWh']['s']<40&&past('kookplaatpower_kWh')>600) sw('kookplaat', 'Off', basename(__FILE__).':'.__LINE__);
}
if ($d['dysonlader']['s']=='On') {
	if ($d['dysonlader_kWh']['s']<10&&past('dysonlader')>1800) sw('dysonlader', 'Off', basename(__FILE__).':'.__LINE__);
}
if ($d['Weg']['s']>0) {
	if ($d['Media']['s']=='On'&&past('Weg')>900) sw('Media', 'Off', basename(__FILE__).':'.__LINE__);
	if (ping('192.168.2.7')==true) sw('Media', 'Off', basename(__FILE__).':'.__LINE__, true);
	if ($d['kookplaat']['s']=='On') sw('kookplaat', 'Off', basename(__FILE__).':'.__LINE__);
	if ($d['dysonlader']['s']=='On') sw('dysonlader', 'Off', basename(__FILE__).':'.__LINE__);
	if ($d['GroheRed']['s']=='On') sw('GroheRed', 'Off', basename(__FILE__).':'.__LINE__);
//	if ($d['steenterras']['s']=='On') sw('steenterras','Off', basename(__FILE__).':'.__LINE__);
	if ($d['houtterras']['s']=='On') sw('houtterras','Off', basename(__FILE__).':'.__LINE__);
} else {
	if ($d['Media']['s']=='On'&&$d['shield']['s']=='Off'&&past('shield')>900) sw('Media', 'Off', basename(__FILE__).':'.__LINE__);
}
/*
if ($d['auto']['s']!='On'&&past('auto')>86400) sw('auto', 'On', basename(__FILE__).':'.__LINE__);
if (past('Weg')>18000&& $d['Weg']['s']==0&& past('pirliving')>18000&& past('pirkeuken')>18000&& past('pirinkom')>18000&& past('pirhall')>18000&& past('pirgarage')>18000) {
	store('Weg', 1, basename(__FILE__).':'.__LINE__);
	telegram('Slapen ingeschakeld na 5 uur geen beweging', false, 2);
} elseif (past('Weg')>36000&& $d['Weg']['s']==1&& past('pirliving')>36000&& past('pirkeuken')>36000&& past('pirinkom')>36000&& past('pirhall')>36000&& past('pirgarage')>36000) {
	store('Weg', 2, basename(__FILE__).':'.__LINE__);
	telegram('Weg ingeschakeld na 10 uur geen beweging', false, 2);
}*/
if ($d['zolderg']['s']=='On'&&past('zolderg')>7200&&past('pirgarage')>7200) sw('zolderg', 'Off', basename(__FILE__).':'.__LINE__);

$ctx=stream_context_create(array('http'=>array('timeout'=>10)));
$data=json_decode(file_get_contents('https://verbruik.egregius.be/tellerjaar.php',false,$ctx),true);
if (!empty($data)) {
	if ($data['zonpercent']!=$d['zonvandaag']['m']) storemode('zonvandaag', $data['zonpercent'], basename(__FILE__).':'.__LINE__);
}
if ($d['daikin']['s']=='On'&&past('daikin')>178) {
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
	}
	if ($data[0]=='ret=OK'&&isset($livingheat)&&isset($kamerheat)&&isset($kamerheat)&&isset($kamercool)&&isset($alexheat)&&isset($alexcool)) {
		$date=strftime('%F', $time);
		if (!isset($db)) $db=dbconnect();
		$db->query("INSERT INTO daikin (date,livingheat,livingcool,kamerheat,kamercool,alexheat,alexcool) VALUES ('$date','$livingheat','$livingcool','$kamerheat','$kamercool','$alexheat','$alexcool') ON DUPLICATE KEY UPDATE date='$date',livingheat='$livingheat',livingcool='$livingcool',kamerheat='$kamerheat',kamercool='$kamercool',alexheat='$alexheat',alexcool='$alexcool';");
		$date=strftime('%F', $time-86400);
		$db->query("INSERT INTO daikin (date,livingheat,livingcool,kamerheat,kamercool,alexheat,alexcool) VALUES ('$date','$livingprevheat','$livingprevcool','$kamerprevheat','$kamerprevcool','$alexprevheat','$alexprevcool') ON DUPLICATE KEY UPDATE date='$date',livingheat='$livingprevheat',livingcool='$livingprevcool',kamerheat='$kamerprevheat',kamercool='$kamerprevcool',alexheat='$alexprevheat',alexcool='$alexprevcool';");
	}
}
if ($time>strtotime('0:10')) {
	$chauth = curl_init('https://app1pub.smappee.net/dev/v1/oauth2/token?grant_type=password&client_id='.$smappeeclient_id.'&client_secret='.$smappeeclient_secret.'&username='.$smappeeusername.'&password='.$smappeepassword.'');
	curl_setopt($chauth, CURLOPT_AUTOREFERER, true);
	curl_setopt($chauth, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($chauth, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($chauth, CURLOPT_VERBOSE, 0);
	curl_setopt($chauth, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($chauth, CURLOPT_SSL_VERIFYPEER, false);
	$objauth=json_decode(curl_exec($chauth));
	if (!empty($objauth)) {
		echo __LINE__.PHP_EOL;
		$access=$objauth->{'access_token'};
		curl_close($chauth);
		$timefrom=strtotime(date("Y-m-d", strtotime("-185 days")));
		$ch=curl_init('');
		curl_setopt($ch, CURLOPT_HEADER, 0);
		$headers=array('Authorization: Bearer '.$access);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_AUTOREFERER, true);
		curl_setopt($ch, CURLOPT_URL, 'https://app1pub.smappee.net/dev/v1/servicelocation/'.$smappeeserviceLocationId.'/consumption?aggregation=5&from='.$timefrom.'000&to='.$time.'000');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_VERBOSE, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		$data=json_decode(curl_exec($ch), true);
		if (!empty($data['consumptions'])) {
			echo __LINE__.PHP_EOL;
			foreach ($data['consumptions'] as $i) {
				$timestamp=$i['timestamp']/1000;
				$consumption=$i['consumption'];
				$solar=$i['solar'];
				$alwaysOn=$i['alwaysOn'];
				$gridImport=$i['gridImport'];
				$gridExport=$i['gridExport'];
				$selfConsumption=$i['selfConsumption'];
				$selfSufficiency=$i['selfSufficiency'];
				$sql="INSERT INTO smappee_kwartaal (timestamp, consumption,solar,alwaysOn,gridImport,gridExport,selfConsumption,selfSufficiency) VALUES ('$timestamp', '$consumption', '$solar', '$alwaysOn', '$gridImport', '$gridExport', '$selfConsumption', '$selfSufficiency') ON DUPLICATE KEY UPDATE consumption='$consumption', solar='$solar', alwaysOn='$alwaysOn', gridImport='$gridImport', gridExport='$gridExport', selfConsumption='$selfConsumption', selfSufficiency='$selfSufficiency';";
				echo __LINE__.' '.$sql.PHP_EOL;
				$db->query($sql);
			}
		}
		curl_close($ch);
		$timefrom=strtotime(date("Y-m-d", strtotime("-60 days")));
		$ch=curl_init('');
		curl_setopt($ch, CURLOPT_HEADER, 0);
		$headers=array('Authorization: Bearer '.$access);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_AUTOREFERER, true);
		curl_setopt($ch, CURLOPT_URL, 'https://app1pub.smappee.net/dev/v1/servicelocation/'.$smappeeserviceLocationId.'/consumption?aggregation=4&from='.$timefrom.'000&to='.$time.'000');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_VERBOSE, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		$data=json_decode(curl_exec($ch), true);
		if (!empty($data['consumptions'])) {
			echo __LINE__.PHP_EOL;
			foreach ($data['consumptions'] as $i) {
				$timestamp=$i['timestamp']/1000;
				$consumption=$i['consumption'];
				$solar=$i['solar'];
				$alwaysOn=$i['alwaysOn'];
				$gridImport=$i['gridImport'];
				$gridExport=$i['gridExport'];
				$selfConsumption=$i['selfConsumption'];
				$selfSufficiency=$i['selfSufficiency'];
				$sql="INSERT INTO smappee_maand (timestamp, consumption,solar,alwaysOn,gridImport,gridExport,selfConsumption,selfSufficiency) VALUES ('$timestamp', '$consumption', '$solar', '$alwaysOn', '$gridImport', '$gridExport', '$selfConsumption', '$selfSufficiency') ON DUPLICATE KEY UPDATE consumption='$consumption', solar='$solar', alwaysOn='$alwaysOn', gridImport='$gridImport', gridExport='$gridExport', selfConsumption='$selfConsumption', selfSufficiency='$selfSufficiency';";
				echo __LINE__.' '.$sql.PHP_EOL;
				$db->query($sql);
			}
		}
		curl_close($ch);
		$timefrom=strtotime(date("Y-m-d", strtotime("-2 days")));
		$ch=curl_init('');
		curl_setopt($ch, CURLOPT_HEADER, 0);
		$headers=array('Authorization: Bearer '.$access);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_AUTOREFERER, true);
		curl_setopt($ch, CURLOPT_URL, 'https://app1pub.smappee.net/dev/v1/servicelocation/'.$smappeeserviceLocationId.'/consumption?aggregation=3&from='.$timefrom.'000&to='.$time.'000');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_VERBOSE, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		$data=json_decode(curl_exec($ch), true);
		if (!empty($data['consumptions'])) {
			foreach ($data['consumptions'] as $i) {
				$timestamp=$i['timestamp']/1000;
				$consumption=$i['consumption'];
				$solar=$i['solar'];
				$alwaysOn=$i['alwaysOn'];
				$gridImport=$i['gridImport'];
				$gridExport=$i['gridExport'];
				$selfConsumption=$i['selfConsumption'];
				$selfSufficiency=$i['selfSufficiency'];
				$sql="INSERT IGNORE INTO smappee_dag (timestamp, consumption,solar,alwaysOn,gridImport,gridExport,selfConsumption,selfSufficiency) VALUES ('$timestamp', '$consumption', '$solar', '$alwaysOn', '$gridImport', '$gridExport', '$selfConsumption', '$selfSufficiency') ON DUPLICATE KEY UPDATE consumption='$consumption', solar='$solar', alwaysOn='$alwaysOn', gridImport='$gridImport', gridExport='$gridExport', selfConsumption='$selfConsumption', selfSufficiency='$selfSufficiency';";
				echo __LINE__.' '.$sql.PHP_EOL;
				$db->query($sql);
			}
//			echo __LINE__.PHP_EOL;
//			print_r($data);
			if (isset($data['consumptions'][2]['consumption'])) $vv=round($data['consumptions'][2]['consumption']/1000, 1);
			elseif (isset($data['consumptions'][1]['consumption'])) $vv=round($data['consumptions'][1]['consumption']/1000, 1);
			else $vv=0.001;
			if ($d['el']['m']!=$vv) {
				echo __LINE__.PHP_EOL;
				storemode('el', $vv, basename(__FILE__).':'.__LINE__);
			}
			if (isset($data['consumptions'][2]['solar'])) $zonvandaag=round($data['consumptions'][2]['solar']/1000, 1); else $zonvandaag=0;
			if ($d['zonvandaag']['s']!=$zonvandaag) store('zonvandaag', $zonvandaag, basename(__FILE__).':'.__LINE__);
			$gas=$d['gasvandaag']['s']/100;
			$water=$d['watervandaag']['s']/1000;
//			@file_get_contents($vurl."verbruik=$vv&gas=$gas&water=$water&zon=$zonvandaag");
			if (strftime("%M", $time)%15==0) {
				echo __LINE__.PHP_EOL;
				$prev=array();
				$stmt=$db->query("SELECT import, kwhimport, export, kwhexport FROM smappee_kwartier WHERE stamp LIKE '".strftime("%F", $time)."%' ORDER BY stamp DESC LIMIT 0,1;");
				while ($row=$stmt->fetch(PDO::FETCH_ASSOC)) $prev=$row;
				if(!isset($prev['import'])) {$prev['import']=0;$prev['export']=0;}
				$kwhimport=($gridImport/1000)-$prev['import'];
				$kwhexport=($gridExport/1000)-$prev['export'];
				if ($kwhimport<0) $kwhimport=0;
				$sql="INSERT IGNORE INTO smappee_kwartier (stamp, import, kwhimport, export, kwhexport) VALUES ('".strftime("%F %H:%M:00", $time)."', '".round($gridImport/1000, 3) ."', '".round($kwhimport,3)."', '".round($gridExport/1000, 3) ."', '".round($kwhexport,3)."');";
				echo __LINE__.' '.$sql.PHP_EOL;
				$db->query($sql);
				if ($kwhimport>(4/4)) telegram ('Kwartierpiek = '.$kwhimport.' kWH'.PHP_EOL.'= '.$kwhimport*4 .' kWh / uur');

				$stmt=$db->query("SELECT LEFT(stamp, 7) AS maand, MAX(kwhimport)*4 AS max FROM smappee_kwartier GROUP BY LEFT(stamp, 7) ORDER BY stamp DESC LIMIT 0,20;");
				while ($row=$stmt->fetch(PDO::FETCH_ASSOC)) {
					$sql="INSERT INTO smappee_maandpiek (maand, kWh) VALUES ('".$row['maand']."', '".$row['max']."') ON DUPLICATE KEY UPDATE kWh='".$row['max']."';";
					echo __LINE__.' '.$sql.PHP_EOL;
					$db->query($sql);
				}
			}
		}
		curl_close($ch);
		$timefrom=$time-600;
		$ch=curl_init('');
		curl_setopt($ch, CURLOPT_HEADER, 0);
		$headers=array('Authorization: Bearer '.$access);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_AUTOREFERER, true);
		curl_setopt($ch, CURLOPT_URL, 'https://app1pub.smappee.net/dev/v1/servicelocation/'.$smappeeserviceLocationId.'/consumption?aggregation=1&from='.$timefrom.'000&to='.$time.'000');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_VERBOSE, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		$data=json_decode(curl_exec($ch), true);
		if (!empty($data['consumptions'])) {
			foreach ($data['consumptions'] as $i) {
				if ($d['el']['icon']!=$i['alwaysOn']) storeicon('el', $i['alwaysOn']);
			}
		}
		curl_close($ch);
		unset($data);
	}
}
if ($d['zon']['s']>0) {
	if (past('uv')>1100) {
		$uv=json_decode(shell_exec("curl -X GET 'https://api.openuv.io/api/v1/uv?lat=".$lat."&lng=".$lon."' -H 'x-access-token: ".$openuv."'"),true);
		echo 'UV=';print_r($uv);
		if (isset($uv['result'])) {
			if (round($uv['result']['uv'], 1)!=$d['uv']['s']) store('uv', round($uv['result']['uv'], 1), basename(__FILE__).':'.__LINE__);
			if (round($uv['result']['uv_max'], 1)!=$d['uv']['m']) storemode('uv', round($uv['result']['uv_max'], 1), basename(__FILE__).':'.__LINE__);
		}
	}
} else {
	if ($d['uv']['s']>0) store('uv', 0, basename(__FILE__).':'.__LINE__);
	if ($d['uv']['m']>0) storemode('uv', 0, basename(__FILE__).':'.__LINE__);
}
if ($d['luchtdroger']['s']=='On') {
	$data=json_decode(file_get_contents('http://192.168.2.2:8080/json.htm?type=command&param=getopenzwavenodes&idx=3'),true);
	if (isset($data['result'])) {
		foreach ($data['result'] as $i) {
			if (!in_array($i['Name'], array('waskamervuur')) && $i['State']=='Dead') {
				telegram('Node '.$i['Name'].' dead, restarting...');
				sleep(3);
				echo shell_exec('sudo /sbin/reboot');
			}
		}
	}
}
if ($d['Weg']['s']==0) {
	foreach (array('living_temp','kamer_temp','waskamer_temp','alex_temp','badkamer_temp','zolder_temp','buiten_hum','living_hum','kamer_hum','waskamer_hum','alex_hum','badkamer_hum') as $i) {
		if (past($i)>43150) alert($i,$i.' not updated since '.strftime("%k:%M:%S", $d[$i]['t']),7200);
	}
}	