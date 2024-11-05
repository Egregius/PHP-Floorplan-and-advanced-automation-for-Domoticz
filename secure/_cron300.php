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
	if ($d['dysonlader_kWh']['s']<10&&past('dysonlader')>2400) sw('dysonlader', 'Off', basename(__FILE__).':'.__LINE__);
}
if ($d['Weg']['s']>0) {
	if ($d['kookplaat']['s']=='On') sw('kookplaat', 'Off', basename(__FILE__).':'.__LINE__);
	if ($d['dysonlader']['s']=='On') sw('dysonlader', 'Off', basename(__FILE__).':'.__LINE__);
	if ($d['GroheRed']['s']=='On') sw('GroheRed', 'Off', basename(__FILE__).':'.__LINE__);
	if ($d['steenterras']['s']=='On') sw('steenterras','Off', basename(__FILE__).':'.__LINE__);
	if ($d['houtterras']['s']=='On') sw('houtterras','Off', basename(__FILE__).':'.__LINE__);
} 
if ($d['Media']['s']=='On'&&$d['lg_webos_tv_cd9e']['s']!='On'&&past('Media')>1800&&past('lg_webos_tv_cd9e')>1800) sw('Media', 'Off', basename(__FILE__).':'.__LINE__);

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
		$date=date('Y-m-d', $time);
		if (!isset($db)) $db=dbconnect();
		$db->query("INSERT INTO daikin (date,livingheat,livingcool,kamerheat,kamercool,alexheat,alexcool) VALUES ('$date','$livingheat','$livingcool','$kamerheat','$kamercool','$alexheat','$alexcool') ON DUPLICATE KEY UPDATE date='$date',livingheat='$livingheat',livingcool='$livingcool',kamerheat='$kamerheat',kamercool='$kamercool',alexheat='$alexheat',alexcool='$alexcool';");
		$date=date('Y-m-d', $time-86400);
		$db->query("INSERT INTO daikin (date,livingheat,livingcool,kamerheat,kamercool,alexheat,alexcool) VALUES ('$date','$livingprevheat','$livingprevcool','$kamerprevheat','$kamerprevcool','$alexprevheat','$alexprevcool') ON DUPLICATE KEY UPDATE date='$date',livingheat='$livingprevheat',livingcool='$livingprevcool',kamerheat='$kamerprevheat',kamercool='$kamerprevcool',alexheat='$alexprevheat',alexcool='$alexprevcool';");
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
if ($d['Weg']['s']==0) {
	foreach (array('living_temp','kamer_temp','waskamer_temp','alex_temp','badkamer_temp','zolder_temp','buiten_hum','living_hum','kamer_hum','waskamer_hum','alex_hum','badkamer_hum') as $i) {
		if (past($i)>43150) alert($i,$i.' not updated since '.date("G:i:s", $d[$i]['t']),7200);
	}
}	