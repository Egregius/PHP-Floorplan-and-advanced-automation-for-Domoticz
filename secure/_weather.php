<?php
$user='weather';
$prevwind=$d['wind']['s'];
//lg ('Weather');
$wind=$prevwind;
$maxtemp=-100;
$mintemp=100;
$temps=array();
$winds=array();
$rains=array();
$hums=array();
$temps['buiten_temp']=$d['buiten_temp']['s'];
$hums['buiten_temp']=$d['buiten_temp']['m'];
$winds['prev_wind']=$d['wind']['s'];

//lg(__LINE__.' https://api.openweathermap.org/data/3.0/onecall?lat='.$lat.'&lon='.$lon.'&exclude=minutely,daily,alerts&units=metric&appid='.$owappid);
$ow=json_decode(curl('https://api.openweathermap.org/data/3.0/onecall?lat='.$lat.'&lon='.$lon.'&exclude=minutely,daily,alerts&units=metric&appid='.$owappid),true);
if (isset($ow['current'])) {
	$temps['ow3']=$ow['current']['temp'];
	$hums['ow3']=$ow['current']['humidity'];
	$temps['ow_feel3']=$ow['current']['feels_like'];
	$winds['ow_speed']=$ow['current']['wind_speed'] * 3.6;
	if (isset($ow['current']['wind_gust'])) $winds['ow_gust']=$ow['current']['wind_gust'] * 3.6;
	if ($d['icon']['s']!=$ow['current']['weather'][0]['icon']) store('icon', $ow['current']['weather'][0]['icon']);
	if (isset($ow['rain']['1h'])) $rains['ow']=$ow['rain']['1h']*10;
	foreach ($ow['hourly'] as $i) {
		if ($i['dt']<$time+(12*3600)) {
//			lg(__LINE__.' '.print_r($i,true));
			if ($i['temp']<$mintemp) $mintemp=$i['temp'];
			elseif ($i['temp']>$maxtemp) $maxtemp=$i['temp'];
			if ($i['feels_like']<$mintemp) $mintemp=$i['feels_like'];
			elseif ($i['feels_like']>$maxtemp) $maxtemp=$i['feels_like'];
		} else break;
	}
}
//lg(__LINE__.print_r($temps,true));
//lg(__LINE__.print_r($winds,true));
//lg(__LINE__.print_r($rains,true));
//lg($mintemp.' '.$maxtemp);

//lg(__LINE__.' https://api.weatherapi.com/v1/current.json?q='.$lat.','.$lon.'&key='.$waappid);
$wa=json_decode(curl('https://api.weatherapi.com/v1/current.json?q='.$lat.','.$lon.'&key='.$waappid),true);
if (isset($wa['current']['temp_c'])) {
	$temps['wa']=$wa['current']['temp_c'];
	$hums['wa']=$ow['current']['humidity'];
	$temps['wa_feel']=$wa['current']['feelslike_c'];
	$winds['wa_speed']=$wa['current']['wind_kph'];
	$winds['wa_gust']=$wa['current']['gust_kph'];
	$rains['wa']=$wa['current']['precip_mm'];
}

//lg(__LINE__.' https://observations.buienradar.nl/1.0/actual/weatherstation/10006414');
$ob=json_decode(curl('https://observations.buienradar.nl/1.0/actual/weatherstation/10006414'), true);
if (isset($ob['temperature'])&&isset($ob['feeltemperature'])) {
	$temps['ob']=$ob['temperature'];
	$temps['ob_feel']=$ob['feeltemperature'];
	$winds['ob_wind']=$ob['windspeed'] * 1.609344;
	if (isset($ob['windgusts'])) $winds['ob_gust']=$ob['windgusts'] * 1.609344;
}


//lg(__LINE__.' https://api.open-meteo.com/v1/forecast?latitude='.$lat.'&longitude='.$lon.'&current_weather=true');
$om=json_decode(curl('https://api.open-meteo.com/v1/forecast?latitude='.$lat.'&longitude='.$lon.'&current_weather=true'), true);
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
if (isset($vc['currentConditions']['temp'])) {
	$temps['vc']=$vc['currentConditions']['temp'];
	$temps['vc_feel']=$vc['currentConditions']['feelslike'];
	$hums['vc']=$vc['currentConditions']['humidity'];
	$winds['vc_wind']=$vc['currentConditions']['windgust'];
	$rains['vc']=$vc['currentConditions']['precip'];
	
}

//lg(__LINE__.' https://www.yr.no/api/v0/locations/2-2787889/forecast/currenthour');
$yr=json_decode(curl('https://www.yr.no/api/v0/locations/2-2787889/forecast/currenthour'), true);
if (isset($yr['temperature']['value'])) {
	$temps['yr']=$yr['temperature']['value'];
	$temps['yr_feel']=$yr['temperature']['feelsLike'];
	$winds['yr_wind']=$yr['wind']['speed'] * 1.609344;
	$rains['yr']=$yr['precipitation']['value']*100;
	
}

//lg(__LINE__.' https://www.yr.no/api/v0/locations/2-2787889/forecast');
$yr=json_decode(curl('https://www.yr.no/api/v0/locations/2-2787889/forecast'), true);
if (isset($yr['shortIntervals'])) {
	foreach ($yr['shortIntervals'] as $i) {
		if (strtotime($i['start'])<$time+(12*3600)) {
			if ($i['temperature']['value']<$mintemp) $mintemp=$i['temperature']['value'];
			elseif ($i['temperature']['value']>$maxtemp) $maxtemp=$i['temperature']['value'];
			if ($i['feelsLike']['value']<$mintemp) $mintemp=$i['feelsLike']['value'];
			elseif ($i['feelsLike']['value']>$maxtemp) $maxtemp=$i['feelsLike']['value'];
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

if (count($temps)>=2) $temp=round(array_sum($temps)/count($temps), 1);
if (count($hums)>=1) $hum=round(array_sum($hums)/count($hums), 1);
//lg(print_r($temps, true). ' => temp = '.$temp);
foreach ($temps as $i) {
	if ($i>-30&&$i<50) {
		if ($i>$maxtemp) $maxtemp=$i;
		elseif ($i<$mintemp) $mintemp=$i;
	}
}
if ($mintemp>-30&&$mintemp<50) $mintemp=floor($mintemp*10)/10; else $mintemp=-10;
$maxtemp=ceil($maxtemp*10)/10;
if ($temp>$d['buiten_temp']['s']+0.1) $temp=$d['buiten_temp']['s']+0.1;
elseif ($temp<$d['buiten_temp']['s']-0.1) $temp=$d['buiten_temp']['s']-0.1;


if ($d['minmaxtemp']['s']!=$mintemp||$d['minmaxtemp']['m']!=$maxtemp) storesm('minmaxtemp', $mintemp, $maxtemp);
//lg('Updated weather data with '.count($temps).' temperature, '.count($winds).' wind and '.count($rains).' rain data');
if ($hum>(float)$d['buiten_temp']['m']+0.5) $hum=(float)$d['buiten_temp']['m']+0.5;
elseif ($hum<(float)$d['buiten_temp']['m']-0.5) $hum=(float)$d['buiten_temp']['m']-0.5;
if ($d['buiten_temp']['s']!=$temp||$d['buiten_temp']['m']!=$hum) storesm('buiten_temp', $temp, $hum);


if (count($winds)>=4) {
	$wind=round(array_sum($winds)/count($winds), 1);
	if ($d['wind']['s']!=$wind) store('wind', $wind);
}

if (count($rains)>=2) {
	$rain=floor(array_sum($rains)/count($rains));
	if ($d['buien']['s']!=$rain) store('buien', $rain);
	if ($rain>0) {
		$past=(86400/$rain)*2;
		mset('buien',$time);
	} else $past=86400;
	if ($past<1200) $past=1200;
//EGREGIUS	if ($d['regenpomp']['s']=='Off'&&past('regenpomp')>$past) sw('regenpomp', 'On', basename(__FILE__).':'.__LINE__);
}

//lg('temps = '.print_r($temps,true).' => '.$temp);//lg('winds = '.print_r($winds,true).' => '.$wind);lg('rains = '.print_r($rains,true).' => '.$rain);

$db=new PDO("mysql:host=localhost;dbname=$dbname;",$dbuser,$dbpass);
$result=$db->query("SELECT AVG(temp) as AVG FROM (SELECT buiten as temp FROM `temp` ORDER BY `temp`.`stamp` DESC LIMIT 0,20) as A");
while ($row = $result->fetch(PDO::FETCH_ASSOC)) $avg=$row['AVG'];
if ($d['buiten_temp']['s']>$avg+0.5) {
	if ($d['buiten_temp']['icon']!='red5') storeicon('buiten_temp', 'red5');
} elseif ($d['buiten_temp']['s']>$avg+0.4) {
	if ($d['buiten_temp']['icon']!='red4') storeicon('buiten_temp', 'red4');
} elseif ($d['buiten_temp']['s']>$avg+0.3) {
	if ($d['buiten_temp']['icon']!='red3') storeicon('buiten_temp', 'red3');
} elseif ($d['buiten_temp']['s']>$avg+0.2) {
	if ($d['buiten_temp']['icon']!='red') storeicon('buiten_temp', 'red');
} elseif ($d['buiten_temp']['s']>$avg+0.1) {
	if ($d['buiten_temp']['icon']!='up') storeicon('buiten_temp', 'up');
} elseif ($d['buiten_temp']['s']<$avg-0.5) {
	if ($d['buiten_temp']['icon']!='blue5') storeicon('buiten_temp', 'blue5');
} elseif ($d['buiten_temp']['s']<$avg-0.4) {
	if ($d['buiten_temp']['icon']!='blue4') storeicon('buiten_temp', 'blue4');
} elseif ($d['buiten_temp']['s']<$avg-0.3) {
	if ($d['buiten_temp']['icon']!='blue3') storeicon('buiten_temp', 'blue3');
} elseif ($d['buiten_temp']['s']<$avg-0.2) {
	if ($d['buiten_temp']['icon']!='blue') storeicon('buiten_temp', 'blue');
} elseif ($d['buiten_temp']['s']<$avg-0.1) {
	if ($d['buiten_temp']['icon']!='down') storeicon('buiten_temp', 'down');
} else {
	if ($d['buiten_temp']['icon']!='') storeicon('buiten_temp', '');
}
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
