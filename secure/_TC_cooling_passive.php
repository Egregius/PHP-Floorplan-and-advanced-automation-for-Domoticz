<?php
if ($d['brander']['s']!='Off') sw('brander', 'Off', basename(__FILE__).':'.__LINE__);
if ($d['daikin']['s']=='On'&&$d['daikin']['m']==1) {
	foreach (array('living', 'kamer', 'alex') as $k) {
		$daikin=json_decode($d['daikin'.$k]['s']);
		if ($daikin->power!=0&&$daikin->mode!=3) {
			daikinset($k, 0, 3, 20, basename(__FILE__).':'.__LINE__);
			storemode('daikin'.$k, 0);
			storeicon($k.'_set', 'Off');
		}
	}
}

$boven=array('Rwaskamer','Ralex','RkamerL','RkamerR');
$beneden=array('Rbureel','RkeukenL','RkeukenR');
$benedenall=array('Rliving','Rbureel','RkeukenL','RkeukenR');

if ($d['auto']['s']=='On') {
	if (($time>=$t||($d['Weg']['s']==0&&$d['dag']>0))&&$time<strtotime('8:30')) {
		if ($time>=$t) {
			if ($d['RkamerL']['s']>0) sl('RkamerL', 0, basename(__FILE__).':'.__LINE__);
			if ($d['RkamerR']['s']>0) sl('RkamerR', 0, basename(__FILE__).':'.__LINE__);
			if ($d['Ralex']['s']==0&&$d['Rwaskamer']['s']>50) sl('Rwaskamer', 0, basename(__FILE__).':'.__LINE__);
			if ($d['Ralex']['s']>0&&$time>=$t+1800&&($d['deuralex']['s']=='Open'||$d['alex']['s']>0)) sl('Ralex', 0, basename(__FILE__).':'.__LINE__);
		}
		if ($d['Media']['s']=='Off'&&$d['Rliving']['s']>0&&($d['Ralex']['s']<=1||$time>=strtotime('8:30'))) sl('Rliving', 0, basename(__FILE__).':'.__LINE__);
		if ($d['dag']>0) {
			if ($d['Media']['s']=='Off') {
				foreach ($beneden as $i) {
					if ($d[$i]['s']>0&&past($i)>7200) sl($i, 0, basename(__FILE__).':'.__LINE__);
				}
			}
		}
	}

	elseif ($time>=strtotime('11:00')&&$time<strtotime('15:00')) {
		if($d['zon']['s']>2000) {
			if ($d['raamwaskamer']['s']=='Closed'&&$d['Rwaskamer']['s']<83) sl('Rwaskamer', 83, basename(__FILE__).':'.__LINE__);
			if ($d['raamalex']['s']=='Closed'&&$d['Ralex']['s']<83) sl('Ralex', 83, basename(__FILE__).':'.__LINE__);
			if ($d['Weg']['s']>1&&$d['Rliving']['s']<86&&$d['living_temp']['s']>21) sl('Rliving', 86, basename(__FILE__).':'.__LINE__);
		}
	}

	elseif ($time>=strtotime('15:00')&&$time<strtotime('22:00')) {
		if($d['zon']['s']>2000) {
			if ($d['raamwaskamer']['s']=='Closed'&&$d['Ralex']['s']<50) sl('Rwaskamer', 83, basename(__FILE__).':'.__LINE__);
			if ($d['raamalex']['s']=='Closed'&&$d['Ralex']['s']<83) sl('Ralex', 83, basename(__FILE__).':'.__LINE__);
			if ($d['Rbureel']['s']<30&&$d['living_temp']['s']>=20.5) sl('Rbureel', 30, basename(__FILE__).':'.__LINE__);
			if ($d['Weg']['s']>1&&$d['Rliving']['s']<86&&$d['living_temp']['s']>21) sl('Rliving', 86, basename(__FILE__).':'.__LINE__);
		}
	}

	elseif ($time>=strtotime('22:00')||$time<strtotime('3:00')) {
		if ($d['Weg']['s']>0) {
			foreach ($benedenall as $i) {
				if ($d[$i]['s']<88) sl($i, 100, basename(__FILE__).':'.__LINE__);
			}
			foreach ($boven as $i) {
				if ($i=='Rwaskamer') {
					if ($d['deurwaskamer']['s']=='Closed'&&$d[$i]['s']<50) sl($i, 83, basename(__FILE__).':'.__LINE__);
				} elseif ($i=='RkamerL') {
					if ($d['Weg']['s']>=2&&$d[$i]['s']<50) sl($i, 100, basename(__FILE__).':'.__LINE__);
				} elseif ($i=='RkamerR') {
					if ($d['Weg']['s']>=2&&$d[$i]['s']<50) sl($i, 83, basename(__FILE__).':'.__LINE__);
				} else {
					if ($d[$i]['s']<50) sl($i, 83, basename(__FILE__).':'.__LINE__);
				}
			}
		}
	}
}
require('_TC_badkamer.php');
