<?php
$user='cron300';
$d=fetchdata(0,basename(__FILE__).':'.__LINE__);
//lg($user);

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

if ($d['weg']['s']>0) {
	if ($d['kookplaat']['s']=='On') sw('kookplaat', 'Off', basename(__FILE__).':'.__LINE__);
	if ($d['dysonlader']['s']=='On') sw('dysonlader', 'Off', basename(__FILE__).':'.__LINE__);
	if ($d['steenterras']['s']=='On') sw('steenterras','Off', basename(__FILE__).':'.__LINE__);
	if ($d['tuintafel']['s']=='On') sw('tuintafel','Off', basename(__FILE__).':'.__LINE__);
} 

/*
if ($d['auto']['s']!='On'&&past('auto')>86400) sw('auto', 'On', basename(__FILE__).':'.__LINE__);
if (past('weg')>18000&& $d['weg']['s']==0&& past('pirliving')>18000&& past('pirkeuken')>18000&& past('pirinkom')>18000&& past('pirhall')>18000&& past('pirgarage')>18000) {
	store('weg', 1, basename(__FILE__).':'.__LINE__);
	telegram('Slapen ingeschakeld na 5 uur geen beweging', false, 2);
} elseif (past('weg')>36000&& $d['weg']['s']==1&& past('pirliving')>36000&& past('pirkeuken')>36000&& past('pirinkom')>36000&& past('pirhall')>36000&& past('pirgarage')>36000) {
	store('weg', 2, basename(__FILE__).':'.__LINE__);
	telegram('weg ingeschakeld na 10 uur geen beweging', false, 2);
}*/
if ($d['zolderg']['s']=='On'&&past('zolderg')>7200&&past('pirgarage')>7200) sw('zolderg', 'Off', basename(__FILE__).':'.__LINE__);

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
		$db->query("INSERT INTO daikin (date,livingheat,livingcool,kamerheat,kamercool,alexheat,alexcool) VALUES ('$date','$livingheat','$livingcool','$kamerheat','$kamercool','$alexheat','$alexcool') ON DUPLICATE KEY UPDATE date='$date',livingheat='$livingheat',livingcool='$livingcool',kamerheat='$kamerheat',kamercool='$kamercool',alexheat='$alexheat',alexcool='$alexcool';");
		$date=date('Y-m-d', $time-86400);
		$db->query("INSERT INTO daikin (date,livingheat,livingcool,kamerheat,kamercool,alexheat,alexcool) VALUES ('$date','$livingprevheat','$livingprevcool','$kamerprevheat','$kamerprevcool','$alexprevheat','$alexprevcool') ON DUPLICATE KEY UPDATE date='$date',livingheat='$livingprevheat',livingcool='$livingprevcool',kamerheat='$kamerprevheat',kamercool='$kamerprevcool',alexheat='$alexprevheat',alexcool='$alexprevcool';");
	}
}
if ($d['z']>0) {
	if (past('uv')>1100) {
		$uv=json_decode(shell_exec("curl -X GET 'https://api.openuv.io/api/v1/uv?lat=".$lat."&lng=".$lon."' -H 'x-access-token: ".$openuv."'"),true);
		echo 'UV=';print_r($uv);
		if (isset($uv['result'])) {
			if (round($uv['result']['uv'], 1)!=$d['uv']['s']||round($uv['result']['uv_max'], 1)!=$d['uv']['m']) storesm('uv', round($uv['result']['uv'], 1), round($uv['result']['uv_max'], 1), basename(__FILE__).':'.__LINE__);
		}
	}
} else {
	if ($d['uv']['s']>0||$d['uv']['m']>0) storesm('uv', 0, 0, basename(__FILE__).':'.__LINE__);
}


if ($d['regenpomp']['s']=='Off'&&past('regenpomp')>1700&&getCache('buien')>$time-14400) {
	sw('regenpomp', 'On', basename(__FILE__).':'.__LINE__);
	sleep(2);
}

republishmqtt();