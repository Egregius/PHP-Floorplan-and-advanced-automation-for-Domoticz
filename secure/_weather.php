<?php
$user='WEATHER';
//lg ('Weather');
$maxtemp=-100;
$mintemp=100;
$temps=[];
$winds=[];
$rains=[];
$hums=[];
$uvs=[];
$temps['prev']=$d['buiten_temp']['s'];
$hums['prev']=$d['buiten_temp']['m'];
if(!isset($weather)) {
	$weather=[
		'b'=>0,
		'i'=>'',
		'w'=>0,
		'uv'=>0,
		'uvm'=>0,
		'mint'=>10,
		'maxt'=>15,
	];
	$wind=0;
} else {
	$wind=$weather['w'];
	$winds['prev']=$wind;
	$uvs['prev']=$weather['uv'];
}
//lg(__LINE__.' https://api.openweathermap.org/data/3.0/onecall?lat='.$lat.'&lon='.$lon.'&exclude=minutely,daily,alerts&units=metric&appid='.$owappid);
$ow=json_decode(curl('https://api.openweathermap.org/data/3.0/onecall?lat='.$lat.'&lon='.$lon.'&exclude=minutely,daily,alerts&units=metric&appid='.$owappid),true);
//lg('$ow='.print_r($ow,true));
if (isset($ow['current'])) {
	$temps['ow3']=$ow['current']['temp'];
	$temps['ow_feel3']=$ow['current']['feels_like'];
	$hums['ow3']=$ow['current']['humidity'];
	$uvs['ow3']=$ow['current']['uvi'];
	if($uvs['ow3']>$weather['uvm'])$weather['uvm']=$uvs['ow3'];
	$winds['ow_speed']=$ow['current']['wind_speed'] * 3.6;
	if (isset($ow['current']['wind_gust'])) $winds['ow_gust']=$ow['current']['wind_gust'] * 3.6;
	$weather['i']=$ow['current']['weather'][0]['icon'];
	if (isset($ow['current']['rain']['1h'])) $rains['ow']=$ow['current']['rain']['1h']*100;
	foreach ($ow['hourly'] as $i) {
//		lg(print_r($i,true));
		if ($i['dt']<$time+(12*3600)) {
			if ($i['temp']<$mintemp) $mintemp=$i['temp'];
			elseif ($i['temp']>$maxtemp) $maxtemp=$i['temp'];
			if ($i['dt']<$time+3600) {
				if(isset($i['rain']['1h'])) $rains['ow1h']=$i['rain']['1h']*100;
			}
			if($i['uvi']>$weather['uvm'])$weather['uvm']=$i['uvi'];
		} else break;
	}
}

//lg(__LINE__.' https://api.weatherapi.com/v1/current.json?q='.$lat.','.$lon.'&key='.$waappid);
$wa=json_decode(curl('https://api.weatherapi.com/v1/current.json?q='.$lat.','.$lon.'&key='.$waappid),true);
//lg('$wa='.print_r($wa,true));
if (isset($wa['current']['temp_c'])) {
	$temps['wa']=$wa['current']['temp_c'];
	$temps['wa_feel']=$wa['current']['feelslike_c'];
	$hums['wa']=$ow['current']['humidity'];
	$winds['wa_speed']=$wa['current']['wind_kph'];
	$winds['wa_gust']=$wa['current']['gust_kph'];
	$rains['wa']=$wa['current']['precip_mm']*100;
	$uvs['wa']=$wa['current']['uv'];
	if($uvs['wa']>$weather['uvm'])$weather['uvm']=$uvs['wa'];
}

//lg(__LINE__.' https://observations.buienradar.nl/1.0/actual/weatherstation/10006414');
$ob=json_decode(curl('https://observations.buienradar.nl/1.0/actual/weatherstation/10006414'), true);
//lg('$ob='.print_r($ob,true));
if (isset($ob['temperature'])&&isset($ob['feeltemperature'])) {
	$temps['ob']=$ob['temperature'];
	$temps['ob_feel']=$ob['feeltemperature'];
	$hums['ob']=$ob['humidity'];
	$rains['ob']=min(100,$ob['rainFallLastHour']*10);
	$winds['ob_wind']=$ob['windspeed'] * 1.609344;
	if (isset($ob['windgusts'])) $winds['ob_gust']=$ob['windgusts'] * 1.609344;
}

//lg(__LINE__.' https://api.open-meteo.com/v1/forecast?latitude='.$lat.'&longitude='.$lon.'&current_weather=true');
$om=json_decode(curl('https://api.open-meteo.com/v1/forecast?latitude='.$lat.'&longitude='.$lon.'&current_weather=true'), true);
//lg('$om='.print_r($om,true));
if (isset($om['current_weather']['temperature'])) {
	$temps['om']=$om['current_weather']['temperature'];
	$winds['om_wind']=$om['current_weather']['windspeed'];
}
if (isset($om['hourly']['temperature_2m'])) {
	$x=1;
	foreach ($om['hourly']['temperature_2m'] as $i) {
		if ($i<$mintemp) $mintemp=$i;
		elseif ($i>$maxtemp) $maxtemp=$i;
		$x++;
		if ($x>=12) break;
	}
}

//lg(__LINE__.' https://weather.visualcrossing.com/VisualCrossingWebServices/rest/services/timeline/'.$lat.'%2C%20'.$lon.'?unitGroup=metric&include=current&key='.$visualcrossing.'&contentType=json');
$vc=json_decode(curl('https://weather.visualcrossing.com/VisualCrossingWebServices/rest/services/timeline/'.$lat.'%2C%20'.$lon.'?unitGroup=metric&include=current&key='.$visualcrossing.'&contentType=json'), true);
//lg('$vc='.print_r($vc,true));
if (isset($vc['currentConditions']['temp'])) {
	$temps['vc']=$vc['currentConditions']['temp'];
	$hums['vc']=$vc['currentConditions']['humidity'];
	$uvs['vc']=$vc['currentConditions']['uvindex'];
	if($uvs['vc']>$weather['uvm'])$weather['uvm']=$uvs['vc'];
	$winds['vc_wind']=$vc['currentConditions']['windgust'];
	$rains['vc']=$vc['currentConditions']['precip']*100;
}

//lg(__LINE__.' https://www.yr.no/api/v0/locations/2-2787889/forecast/currenthour');
$yr=json_decode(curl('https://www.yr.no/api/v0/locations/2-2787889/forecast/currenthour'), true);
//lg('$yr='.print_r($yr,true));
if (isset($yr['temperature']['value'])) {
	$temps['yr']=$yr['temperature']['value'];
	$temps['yr_feels']=$yr['temperature']['feelsLike'];
	$winds['yr_wind']=$yr['wind']['speed'] * 1.609344;
	$rains['yr']=$yr['precipitation']['value']*100;
}

//lg(__LINE__.' https://www.yr.no/api/v0/locations/2-2787889/forecast');
$yr=json_decode(curl('https://www.yr.no/api/v0/locations/2-2787889/forecast'), true);
//lg('$yr='.print_r($yr,true));
if (isset($yr['shortIntervals'])) {
	foreach ($yr['shortIntervals'] as $i) {
		if (strtotime($i['start'])<$time+(12*3600)) {
			if ($i['temperature']['value']<$mintemp) $mintemp=$i['temperature']['value'];
			elseif ($i['temperature']['value']>$maxtemp) $maxtemp=$i['temperature']['value'];
		} else break;
	}
}

$buienradar=0;
//lg(__LINE__.' https://graphdata.buienradar.nl/2.0/forecast/geo/Rain3Hour?lat='.$lat.'&lon='.$lon);
$data=json_decode(curl('https://graphdata.buienradar.nl/2.0/forecast/geo/Rain3Hour?lat='.$lat.'&lon='.$lon), true);

if (isset($data['forecasts'])) {
	$x=1;
	foreach ($data['forecasts'] as $i) {
		$buienradar=$buienradar+($i['precipitation']*10);
		$x++;
		if ($x==7) break;
	}
	$buienradar=round($buienradar/7, 0);
	if ($buienradar>20) $maxrain=$buienradar;
	$rains['buienradar']=$buienradar;
}

//lg(__LINE__.print_r($temps,true));
//lg(__LINE__.print_r($winds,true));
//lg(__LINE__.print_r($rains,true));
//lg(__LINE__.print_r($hums,true));
//lg($mintemp.' '.$maxtemp);

if ($d['z']>100&&$d['dag']['s']>12) {
	if (!isset($lastuv)||$lastuv<$time-1100) {
		$lastuv=$time;
		$uv=json_decode(shell_exec("curl -X GET 'https://api.openuv.io/api/v1/uv?lat=".$lat."&lng=".$lon."' -H 'x-access-token: ".$openuv."'"),true);
		if (isset($uv['result'])) {
//			lg(print_r($uv,true));
			$uvs['openuv']=round($uv['result']['uv'], 1);
			$uvm=round($uv['result']['uv_max'], 1);
			if($uvm>$weather['uvm'])$weather['uvm']=$uvm;
		}
	}
} 


if (count($temps)>=2) $temp=round(array_sum($temps)/count($temps), 1);
if (count($hums)>=2) $hum=round(array_sum($hums)/count($hums), 0);
if (count($uvs)>=2) $uv=round(array_sum($uvs)/count($uvs), 1);
$weather['uv']=$uv??0;
//lg(print_r($temps, true). ' => temp = '.$temp);
foreach ($temps as $i) {
	if ($i>-30&&$i<50) {
		if ($i>$maxtemp) $maxtemp=$i;
		elseif ($i<$mintemp) $mintemp=$i;
	}
}
$mintemp=round($mintemp,1);
$maxtemp=round($maxtemp,1);
$ref = round((float)$d['buiten_temp']['s'],1);
$temp = round($temp,1);
$temp = round(max($ref - 0.1, min($temp, $ref + 0.1)),1);
$weather['mint']=$mintemp;
$weather['maxt']=$maxtemp;

//lg('Updated weather data with '.count($temps).' temperature, '.count($winds).' wind and '.count($rains).' rain data');

//lg(basename(__FILE__) . ':' . __LINE__. ' = '.$d['buiten_temp']['m']);
$ref = (int)$d['buiten_temp']['m'];
//lg(basename(__FILE__) . ':' . __LINE__.' = '.$ref);
$hum = (int)max($ref - 1, min($hum, $ref + 1));
//lg(basename(__FILE__) . ':' . __LINE__.' = '.$hum);

if ($d['buiten_temp']['s']!=$temp&&$d['buiten_temp']['m']!=$hum) storesm('buiten_temp', $temp, $hum);
elseif ($d['buiten_temp']['s']!=$temp) store('buiten_temp', $temp);
elseif ($d['buiten_temp']['m']!=$hum) storemode('buiten_temp', $hum);
//storemode('buiten_temp',85);


if (count($winds)>=4) {
	$wind=round(array_sum($winds)/count($winds), 0);
	$weather['w']=$wind;
}
if (count($rains) >= 2) {
//	lg('$rains='.print_r($rains,true));
    $rain = min(100,array_sum($rains) / count($rains));
    $weather['b']= floor($rain);

    if (!isset($rainhist)) {
        $rainhistJson = getCache('rainhist');
        $rainhist = $rainhistJson ? json_decode($rainhistJson, true) : array();
    }
    $rainhist[] = round($rain,2);
    $rainhist = array_slice($rainhist, -480);
    setCache('rainhist', json_encode($rainhist));
    $sum = array_sum($rainhist);
    $avg = count($rainhist) > 0 ? $sum / count($rainhist) : 0;
    $past = ($sum > 0) ? max(500, round((1 / $avg) * 30000,0)) : 86400;
//    lg('$rainhist = ' . $past . ' = ' . print_r($rainhist, true));
    if ($d['regenpomp']['s'] === 'Off' && past('regenpomp') > $past) {
        sw('regenpomp', 'On', basename(__FILE__) . ':' . __LINE__.' $past='.$past.' $rain='.$rain.' $avg='.$avg);
    }
}
if($weather['uvm']>$weather['uvm'])$weather['uvm']=$weather['uv'];
$weather['uv']=round($weather['uv'],1);
$weather['uvm']=round($weather['uvm'],1);
//lg(print_r($uvs,true));

if (!isset($weathercache)||$weathercache!==$weather) {
	publishmqtt('d/w',json_encode($weather));
	$weathercache=$weather;
}
unset($ow,$wa,$ob,$om,$vc,$yr);
//$avg=null;
//if ($d['buiten_temp']['icon']!=$avg) storeicon('buiten_temp',$avg);
if ($d['auto']['s']=='On') {
	if ($d['heating']['s']==-2&&$d['living_temp']['s']>=19&&$d['dag']['m']>117&&$rain<5) { // Airco Cooling
		if ($wind>=40) 	 $luifel=0;
		elseif ($wind>=30) $luifel=35;
		elseif ($wind>=20) $luifel=45;
		else $luifel=55;
		$luifel=0; // In comment zetten om luifel te activeren.
		if ($d['luifel']['m']==0) {
			if ($d['luifel']['s']<$luifel&&$d['zon']>1500&&past('luifel')>1800) sl('luifel', $luifel, basename(__FILE__).':'.__LINE__);
			elseif ($d['luifel']['s']>$luifel) sl('luifel', $luifel, basename(__FILE__).':'.__LINE__);
		}
	} elseif ($d['heating']['s']==-1	&&$d['living_temp']['s']>=20 &&$d['dag']['m']>117&&$rain<5) { // Passive Cooling
		if ($wind>=40) 	 $luifel=0;
		elseif ($wind>=30) $luifel=35;
		elseif ($wind>=20) $luifel=45;
		else $luifel=55;
		$luifel=0; // In comment zetten om luifel te activeren.
		if ($d['luifel']['m']==0) {
			lg(basename(__FILE__).':'.__LINE__.' $d[luifel][s]='.$d['luifel']['s'].' > $luifel='.$luifel.' zon='.$d['zon'].' past='.past('luifel'));
			if ($d['luifel']['s']<$luifel&&$d['zon']>2000&&past('luifel')>1800) sl('luifel', $luifel, basename(__FILE__).':'.__LINE__);
			elseif ($d['luifel']['s']>$luifel) sl('luifel', $luifel, basename(__FILE__).':'.__LINE__);
		}
	} elseif ($d['heating']['s']==0&&$d['living_temp']['s']>=21&&$d['dag']['m']>117&&$rain<5) { // Neutral
		if ($wind>=40) 	 $luifel=0;
		elseif ($wind>=30) $luifel=35;
		elseif ($wind>=20) $luifel=45;
		else $luifel=55;
		$luifel=0; // In comment zetten om luifel te activeren.
//		lg ('luifel $d='.$d['luifel']['s'].' $luifel='.$luifel);
		if ($d['luifel']['m']==0) {
			if ($d['luifel']['s']<$luifel&&$d['zon']>2500&&past('luifel')>1800) sl('luifel', $luifel, basename(__FILE__).':'.__LINE__, true);
			elseif ($d['luifel']['s']>$luifel) sl('luifel', $luifel, basename(__FILE__).':'.__LINE__);
		}
	} else {
		$luifel=0;
//		lg('luifel='.$luifel.'|$d[luifel][s]='.$d['luifel']['s']);
		if ($d['luifel']['s']>$luifel&&$d['luifel']['m']==0) {
			sl('luifel', $luifel, basename(__FILE__).':'.__LINE__);
		}
	}

	if (isset($rain)&&($rain>=15||$d['weg']['s']==1||$time>=strtotime("20:30"))&&$d['achterdeur']['s']=='Closed'&&$d['luifel']['s']>0) sl('luifel', 0, basename(__FILE__).':'.__LINE__);

	if ($d['luifel']['m']==1) {
		if (past('luifel')>3600&&$luifel<30&&$d['achterdeur']['s']=='Closed') storemode('luifel', 0, basename(__FILE__).':'.__LINE__);
		elseif (past('luifel')>43200) storemode('luifel', 0, basename(__FILE__).':'.__LINE__);
	}
}
