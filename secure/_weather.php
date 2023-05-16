<?php
$user='weather';
$prevwind=$d['wind']['s'];

$wind=$prevwind;
$maxtemp=-100;
$mintemp=100;
$temps=array();
$winds=array();
$rains=array();
$temps['buiten_temp']=$d['buiten_temp']['s'];
$temps['buiten_temp_hum']=$d['minmaxtemp']['icon'];
$winds['prev_wind']=$d['wind']['s'];

//lg(__LINE__.' https://api.openweathermap.org/data/2.5/weather?id='.$owid.'&units=metric&APPID='.$owappid);
$ow=json_decode(curl('https://api.openweathermap.org/data/2.5/weather?id='.$owid.'&units=metric&APPID='.$owappid),true);
if (isset($ow['main']['temp'])) {
	$temps['ow']=$ow['main']['temp'];
	$temps['ow_feel']=$ow['main']['feels_like'];
	if ($ow['main']['temp_min']<$mintemp) $mintemp=$ow['main']['temp_min'];
	if ($ow['main']['temp_max']>$maxtemp) $maxtemp=$ow['main']['temp_max'];
	$winds['ow_speed']=$ow['wind']['speed'] * 3.6;
	$winds['ow_gust']=$ow['wind']['gust'] * 3.6;
	if ($d['icon']['s']!=$ow['weather'][0]['icon']) store('icon', $ow['weather'][0]['icon']);
}

//lg(__LINE__.' https://api.openweathermap.org/data/2.5/forecast?lat='.$lat.'&lon='.$lon.'&units=metric&appid='.$owappid);
$owf=json_decode(curl('https://api.openweathermap.org/data/2.5/forecast?lat='.$lat.'&lon='.$lon.'&units=metric&appid='.$owappid),true);
if (isset($owf['list'])) {
	foreach ($owf['list'] as $i) {
		if ($i['dt']<TIME+(12*3600)) {
			if ($i['main']['temp']<$mintemp) $mintemp=$i['main']['temp'];
			elseif ($i['main']['temp']>$maxtemp) $maxtemp=$i['main']['temp'];
			if ($i['main']['feels_like']<$mintemp) $mintemp=$i['main']['feels_like'];
			elseif ($i['main']['feels_like']>$maxtemp) $maxtemp=$i['main']['feels_like'];
			if ($i['main']['temp_min']<$mintemp) $mintemp=$i['main']['temp_min'];
			if ($i['main']['temp_max']>$maxtemp) $maxtemp=$i['main']['temp_max'];
		}
	}
}

//lg(__LINE__.' https://observations.buienradar.nl/1.0/actual/weatherstation/10006414');
$ob=json_decode(curl('https://observations.buienradar.nl/1.0/actual/weatherstation/10006414'), true);
if (isset($ob['temperature'])&&isset($ob['feeltemperature'])) {
	$temps['ob']=$ob['temperature'];
	$temps['ob_feel']=$ob['feeltemperature'];
	$winds['ob_wind']=$ob['windspeed'] * 1.609344;
	$winds['ob_gust']=$ob['windgusts'] * 1.609344;
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

//lg(__LINE__.' https://www.yr.no/api/v0/locations/2-2787889/forecast/currenthour');
$yr=json_decode(curl('https://www.yr.no/api/v0/locations/2-2787889/forecast/currenthour'), true);
if (isset($yr['temperature']['value'])) {
	$temps['yr']=$yr['temperature']['value'];
	$temps['yr_feel']=$yr['temperature']['feelsLike'];
	$winds['yr_wind']=$yr['wind']['speed'] * 1.609344;
	$rains['yr']=$yr['precipitation']['value'];
	
}

//lg(__LINE__.' https://www.yr.no/api/v0/locations/2-2787889/forecast');
$yr=json_decode(curl('https://www.yr.no/api/v0/locations/2-2787889/forecast'), true);
if (isset($yr['shortIntervals'])) {
	foreach ($yr['shortIntervals'] as $i) {
		if (strtotime($i['start'])<TIME+(12*3600)) {
			if ($i['temperature']['value']<$mintemp) $mintemp=$i['temperature']['value'];
			elseif ($i['temperature']['value']>$maxtemp) $maxtemp=$i['temperature']['value'];
			if ($i['feelsLike']['value']<$mintemp) $mintemp=$i['feelsLike']['value'];
			elseif ($i['feelsLike']['value']>$maxtemp) $maxtemp=$i['feelsLike']['value'];
		} else break;
	}
}

if (TIME>=strtotime('8:00')&&TIME<strtotime('20:00')) {
	//lg(__LINE__.' https://api.tomorrow.io/v4/weather/realtime?location='.$lat.','.$lon.'&fields=temperature&units=metric&apikey='.$tomorrowio);
	$to=json_decode(curl('https://api.tomorrow.io/v4/weather/realtime?location='.$lat.','.$lon.'&fields=temperature&units=metric&apikey='.$tomorrowio), true);
	if (isset($to['data']['values']['temperature'])) {
		$temps['to']=$to['data']['values']['temperature'];
		$winds['to']=$to['data']['values']['windSpeed'] * 1.609344;
		$winds['to_gust']=$to['data']['values']['windGust'] * 1.609344;
	}
		
	//lg(__LINE__.' https://api.tomorrow.io/v4/weather/forecast?location='.$lat.','.$lon.'&fields=temperature&units=metric&apikey='.$tomorrowio2);
	$to=json_decode(curl('https://api.tomorrow.io/v4/weather/forecast?location='.$lat.','.$lon.'&fields=temperature&units=metric&apikey='.$tomorrowio2), true);
	if (isset($to['timelines']['hourly'])) {
		foreach ($to['timelines']['hourly'] as $i) {
			if (strtotime($i['time'])<TIME+(12*3600)) {
				if ($i['values']['temperature']<$mintemp) $mintemp=$i['values']['temperature'];
				elseif ($i['values']['temperature']>$maxtemp) $maxtemp=$i['values']['temperature'];
				if ($i['values']['temperatureApparent']<$mintemp) $mintemp=$i['values']['temperatureApparent'];
				elseif ($i['values']['temperatureApparent']>$maxtemp) $maxtemp=$i['values']['temperatureApparent'];
			} else break;
		}
	}
}

$buienradar=0;
//lg(__LINE__.' https://graphdata.buienradar.nl/2.0/forecast/geo/Rain3Hour?lat='.$lat.'&lon='.$lon);
$data=json_decode(curl('https://graphdata.buienradar.nl/2.0/forecast/geo/Rain3Hour?lat='.$lat.'&lon='.$lon), true);

if (isset($data['forecasts'])) {
	$x=1;
	foreach ($data['forecasts'] as $i) {
		$buienradar=$buienradar+($i['precipitation']*100);
		$x++;
		if ($x==7) break;
	}
	$buienradar=round($buienradar/7, 0);
	if ($buienradar>20) $maxrain=$buienradar;
	$rains['buienradar']=$buienradar;
}

if (count($temps)>=4) $temp=round(array_sum($temps)/count($temps), 1);

foreach ($temps as $i) {
	if ($i>$maxtemp) $maxtemp=$i;
	elseif ($i<$mintemp) $mintemp=$i;
}
$mintemp=floor($mintemp*10)/10;
$maxtemp=ceil($maxtemp*10)/10;

if ($d['buiten_temp']['s']!=$temp) store('buiten_temp', $temp);
if ($d['minmaxtemp']['m']!=$maxtemp) storemode('minmaxtemp', $maxtemp);
if ($d['minmaxtemp']['s']!=$mintemp) store('minmaxtemp', $mintemp);

if (count($winds)>=4) {
	$wind=round(array_sum($winds)/count($winds), 1);
	if ($d['wind']['s']!=$wind) store('wind', $wind);
}

if (count($rains)>=2) {
	$rain=round(array_sum($rains)/count($rains), 1);
	if ($d['buien']['s']!=$rain) store('buien', $rain);
}

//lg('temps = '.print_r($temps,true).' => '.$temp);lg('winds = '.print_r($winds,true).' => '.$wind);lg('rains = '.print_r($rains,true).' => '.$rain);

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
	if ($d['heating']['s']==-2&&$d['living_temp']['s']>20&&TIME>=strtotime("10:00")&&$rain<5) { // Aircocooling
		if ($wind>=30) 	 $luifel=0;
		elseif ($wind>=24) $luifel=45;
		elseif ($wind>=20) $luifel=45;
		else $luifel=45;
		//$luifel=0; // In comment zetten om luifel te activeren.
		if ($d['luifel']['m']==0) {
			if ($d['luifel']['s']<$luifel&&$d['zon']['s']>2000) sl('luifel', $luifel, basename(__FILE__).':'.__LINE__);
			elseif ($d['luifel']['s']>$luifel) sl('luifel', $luifel, basename(__FILE__).':'.__LINE__);
		}
	} elseif ($d['heating']['s']==-1	&&$d['living_temp']['s']>21.5 &&TIME>=strtotime("11:00")&&$rain<5) { // Passive Cooling
		if ($wind>=30)  $luifel=0;
		elseif ($wind>=10) $luifel=35;
		else $luifel=45;
		//$luifel=0; // In comment zetten om luifel te activeren.
		if ($d['luifel']['m']==0) {
			if ($d['luifel']['s']<$luifel&&$d['zon']['s']>2000) sl('luifel', $luifel, basename(__FILE__).':'.__LINE__);
			elseif ($d['luifel']['s']>$luifel) sl('luifel', $luifel, basename(__FILE__).':'.__LINE__);
		}
	} elseif ($d['heating']['s']==0&&$d['living_temp']['s']>23&&TIME>=strtotime("11:00")&&$rain<5) { // Neutral
		if ($wind>=30) 	$luifel=0;
		elseif ($wind>=10) $luifel=30;
		else $luifel=40;
		$luifel=0; // In comment zetten om luifel te activeren.
		if ($d['luifel']['m']==0) {
			if ($d['luifel']['s']<$luifel&&$d['zon']['s']>2500) sl('luifel', $luifel, basename(__FILE__).':'.__LINE__);
			elseif ($d['luifel']['s']>$luifel) sl('luifel', $luifel, basename(__FILE__).':'.__LINE__);
		}
	} else {
		$luifel=0;
		if ($d['luifel']['s']>$luifel&&$d['luifel']['m']==0) {
			sl('luifel', $luifel, basename(__FILE__).':'.__LINE__);
		}
	}

	if (isset($rain)&&($rain>=15||$d['Weg']['s']==1||TIME>=strtotime("20:30"))&&$d['achterdeur']['s']=='Closed'&&$d['luifel']['s']>0) sl('luifel', 0, basename(__FILE__).':'.__LINE__);

	if ($d['luifel']['m']==1) {
		if (past('luifel')>3600&&$luifel<30&&$d['achterdeur']['s']=='Closed') storemode('luifel', 0, basename(__FILE__).':'.__LINE__);
		elseif (past('luifel')>43200) storemode('luifel', 0, basename(__FILE__).':'.__LINE__);
	}
}
