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
	if ($time>=$t&&$time<strtotime('10:00')) {
		if ($d['RkamerL']['s']>0) sl('RkamerL', 0, basename(__FILE__).':'.__LINE__);
		if ($d['RkamerR']['s']>0) sl('RkamerR', 0, basename(__FILE__).':'.__LINE__);
		if ($d['Weg']['s']<3) {
			if ($dag>0) {
				if ($d['Ralex']['s']==0&&$d['Rwaskamer']['s']>0) sl('Rwaskamer', 0, basename(__FILE__).':'.__LINE__);
				if ($d['Ralex']['s']>0&&/*$time>=strtotime('7:30')&&*/($d['deuralex']['s']=='Open'||$d['alex']['s']>0)) sl('Ralex', 0, basename(__FILE__).':'.__LINE__);
			}
			if ($dag>0&&$d['Media']['s']=='Off') {
				foreach ($beneden as $i) {
					if ($d[$i]['s']>0) sl($i, 0, basename(__FILE__).':'.__LINE__);
				}
				if ($d['Rliving']['s']>0&&($d['Ralex']['s']<=1||$time>=strtotime('8:30')||past('deuralex')<3600)) sl('Rliving', 0, basename(__FILE__).':'.__LINE__);
			}
		} else {
			if ($d['Rwaskamer']['s']>0) sl('Rwaskamer', 0, basename(__FILE__).':'.__LINE__);
			if ($d['Ralex']['s']>0) sl('Ralex', 0, basename(__FILE__).':'.__LINE__);
			foreach ($benedenall as $i) {
				if ($d[$i]['s']>0) sl($i, 0, basename(__FILE__).':'.__LINE__);
			}
		}
	}

	elseif ($time>=strtotime('11:00')&&$time<strtotime('15:00')) {
		if($d['zon']['s']>2000) {
			if ($d['raamwaskamer']['s']=='Closed'&&$d['Rwaskamer']['s']<83) sl('Rwaskamer', 83, basename(__FILE__).':'.__LINE__);
			if ($d['raamalex']['s']=='Closed'&&$d['Ralex']['s']<83) sl('Ralex', 83, basename(__FILE__).':'.__LINE__);
			if ($d['Weg']['s']>1) if ($d['Rliving']['s']<86) sl('Rliving', 86, basename(__FILE__).':'.__LINE__);
		}
	}

	elseif ($time>=strtotime('15:00')&&$time<strtotime('22:00')) {
		if($d['zon']['s']>2000) {
			if ($d['raamwaskamer']['s']=='Closed'&&$d['Ralex']['s']<83) sl('Rwaskamer', 83, basename(__FILE__).':'.__LINE__);
			if ($d['raamalex']['s']=='Closed'&&$d['Ralex']['s']<83) sl('Ralex', 83, basename(__FILE__).':'.__LINE__);
			if ($d['Rbureel']['s']<30) sl('Rbureel', 30, basename(__FILE__).':'.__LINE__);
			if ($d['Weg']['s']>1) if ($d['Rliving']['s']<86) sl('Rliving', 86, basename(__FILE__).':'.__LINE__);
		}
	}

	elseif ($time>=strtotime('22:00')||$time<strtotime('3:00')) {
		if ($d['Weg']['s']>0) {
			foreach ($benedenall as $i) {
				if ($d[$i]['s']<88) sl($i, 100, basename(__FILE__).':'.__LINE__);
			}
			foreach ($boven as $i) {
				if ($i=='Rwaskamer') {
					if ($d['deurwaskamer']['s']=='Closed'&&$d[$i]['s']<83) sl($i, 83, basename(__FILE__).':'.__LINE__);
				} elseif ($i=='RkamerL') {
					if ($d[$i]['s']<100) sl($i, 100, basename(__FILE__).':'.__LINE__);
				} elseif ($i=='RkamerR') {
					if ($d['Weg']['s']>=2&&$d[$i]['s']<83) sl($i, 83, basename(__FILE__).':'.__LINE__);
				} else {
					if ($d[$i]['s']<83) sl($i, 83, basename(__FILE__).':'.__LINE__);
				}
			}
		}
	}
}
require('_TC_badkamer.php');
