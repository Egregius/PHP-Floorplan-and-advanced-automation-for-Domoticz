<?php
if ($d['brander']['s']!='Off') sw('brander', 'Off', basename(__FILE__).':'.__LINE__);
if ($d['daikin']['s']=='On'&&$d['daikin']['m']==1) {
	foreach (array('living', 'kamer', 'alex') as $k) {
		$daikin=json_decode($d['daikin'.$k]['s']);
		if (isset($daikin->pow)&&$daikin->pow!=0&&$daikin->mode!=2) {
			daikinset($k, 0, 3, 20, basename(__FILE__).':'.__LINE__);
			storemode('daikin'.$k, 0);
			storeicon($k.'_set', 'Off');
		}
	}
}

$boven=array('Rwaskamer','Ralex','RkamerL','RkamerR');
$beneden=array('Rbureel','RkeukenL','RkeukenR');
$benedenall=array('Rliving','Rbureel','RkeukenL','RkeukenR');
if (TIME>=$d['civil_twilight']['s']&&TIME<=$d['civil_twilight']['m']) $dag=true; else $dag=false;
$zon=$d['zon']['s'];
$heating=$d['heating']['s'];
if ($d['auto']['s']=='On') {
	if (TIME>=$t&&TIME<strtotime('10:00')) {
		if ($d['RkamerL']['s']>0) sl('RkamerL', 0, basename(__FILE__).':'.__LINE__);
		if ($d['RkamerR']['s']>0) sl('RkamerR', 0, basename(__FILE__).':'.__LINE__);
		if ($dag==true) {
			if ($d['Ralex']['s']==0&&$d['Rwaskamer']['s']>0) sl('Rwaskamer', 0, basename(__FILE__).':'.__LINE__);
			if ($d['Ralex']['s']>0&&TIME>=strtotime('7:30')&&($d['deuralex']['s']=='Open'||$d['alex']['s']>0)) sl('Ralex', 0, basename(__FILE__).':'.__LINE__);
		}
		if ($dag==true&&$d['lgtv']['s']=='Off') {
			foreach ($beneden as $i) {
				if ($d[$i]['s']>0) sl($i, 0, basename(__FILE__).':'.__LINE__);
			}
			if ($d['Rliving']['s']>0&&$d['lgtv']['s']=='Off'&&($d['Ralex']['s']<=1||TIME>=strtotime('8:30')||past('deuralex')<3600)) sl('Rliving', 0, basename(__FILE__).':'.__LINE__);
		}
	}

	elseif (TIME>=strtotime('11:00')&&TIME<strtotime('15:00')) {
		if($zon>2000) {
			if ($d['raamwaskamer']['s']=='Closed'&&$d['Rwaskamer']['s']<82) sl('Rwaskamer', 82, basename(__FILE__).':'.__LINE__);
			if ($d['raamalex']['s']=='Closed'&&$d['Ralex']['s']<82) sl('Ralex', 82, basename(__FILE__).':'.__LINE__);
		}
	}

	elseif (TIME>=strtotime('15:00')&&TIME<strtotime('22:00')) {
		if($zon>2000) {
			if ($d['raamwaskamer']['s']=='Closed'&&$d['Ralex']['s']<82) sl('Rwaskamer', 82, basename(__FILE__).':'.__LINE__);
			if ($d['raamalex']['s']=='Closed'&&$d['Ralex']['s']<82) sl('Ralex', 82, basename(__FILE__).':'.__LINE__);
			if ($d['Rbureel']['s']<40) sl('Rbureel', 40, basename(__FILE__).':'.__LINE__);
		}
	}

	elseif (TIME>=strtotime('22:00')||TIME<strtotime('3:00')) {
		if ($d['Weg']['s']>0) {
			foreach ($benedenall as $i) {
				if ($d[$i]['s']<88) sl($i, 88, basename(__FILE__).':'.__LINE__);
			}
			foreach ($boven as $i) {
				if ($i=='Rwaskamer') {
					if ($d['deurwaskamer']['s']=='Closed'&&$d[$i]['s']<82) sl($i, 82, basename(__FILE__).':'.__LINE__);
				} elseif ($i=='RkamerL') {
					if ($d[$i]['s']<100) sl($i, 100, basename(__FILE__).':'.__LINE__);
				} elseif ($i=='RkamerR') {
					if ($d['Weg']['s']>=2&&$d[$i]['s']<82) sl($i, 82, basename(__FILE__).':'.__LINE__);
				} else {
					if ($d[$i]['s']<82) sl($i, 82, basename(__FILE__).':'.__LINE__);
				}
			}
		}
	}
}
require('_TC_badkamer.php');
