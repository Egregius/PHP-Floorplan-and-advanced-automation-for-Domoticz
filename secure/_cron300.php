<?php
$user='cron300';
$d=fetchdata(0,basename(__FILE__).':'.__LINE__);
lg('🕒 '.$user.' ----------------------------------------------------------------------------------------------------------------------------------------------');
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
	if ($d['weg']['s']>1) {
		foreach (array('living_set','alex_set','kamer_set','badkamer_set'/*,'eettafel','zithoek'*/,'luifel') as $i) {
			if ($d[$i]['m']!=0&&$d[$i]['s']!='D'&&past($i)>7200) storemode($i, 0, basename(__FILE__).':'.__LINE__);
		}
	}
} 


if ($d['auto']['s']!='On'&&past('auto')>43200) {
	sw('auto', 'On', basename(__FILE__).':'.__LINE__);
	alert('AUTO','AUTO ingeschakeld na 12 uur',60,false,3);
}
if (past('weg')>18000&& $d['weg']['s']==0&& past('pirliving')>18000&& past('pirkeuken')>18000&& past('pirinkom')>18000&& past('pirhall')>18000&& past('pirgarage')>18000) {
	store('weg', 1, basename(__FILE__).':'.__LINE__);
	alert('WEG','Slapen ingeschakeld na 5 uur geen beweging',60,false,3);
} elseif (past('weg')>36000&& $d['weg']['s']==1&& past('pirliving')>36000&& past('pirkeuken')>36000&& past('pirinkom')>36000&& past('pirhall')>36000&& past('pirgarage')>36000) {
	store('weg', 2, basename(__FILE__).':'.__LINE__);
	alert('WEG','Weg ingeschakeld na 10 uur geen beweging',60,false,3);
}
if ($d['zolderg']['s']=='On'&&past('zolderg')>7200&&past('pirgarage')>7200) sw('zolderg', 'Off', basename(__FILE__).':'.__LINE__);



republishmqtt();