<?php
$zonelevatie=-7;
$boven=array('rwaskamer','ralex','rkamerl','rkamerr');
$beneden=array('rkeukenl','rkeukenr','rbureel');
$benedenall=array('rkeukenl','rkeukenr','rbureel','rliving');
if ($d['auto']['s']=='On') {
	if (($time>=$t||($d['weg']['s']==0&&$d['dag']['s']>=$zonelevatie))&&$time>strtotime('5:00')&&$time<strtotime('8:30')) {
		if ($time>=$t) {
			if ($d['rkamerl']['s']>0) sl('rkamerl', 0, basename(__FILE__).':'.__LINE__);
			if ($d['rkamerr']['s']>0) sl('rkamerr', 0, basename(__FILE__).':'.__LINE__);
			if ($d['ralex']['s']==0&&$d['rwaskamer']['s']>0) sl('rwaskamer', 0, basename(__FILE__).':'.__LINE__);
			if ($d['ralex']['s']>0&&$d['alexslaapt']['s']==0) sl('ralex', 0, basename(__FILE__).':'.__LINE__);
			if ($time>=$t+1800&&$weekend==false) {
				if ($d['ralex']['s']>0) sl('ralex', 0, basename(__FILE__).':'.__LINE__);
			}
		}
		if ($d['dag']['s']>-4) {
			if ($d['lgtv']['s']!='On') {
				foreach ($benedenall as $i) {
					if ($d[$i]['s']>0&&past($i)>7200) sl($i, 0, basename(__FILE__).':'.__LINE__);
				}
			}
		}
	}

	elseif ($d['dag']['m']>118&&$time<strtotime('15:00')) {
		if($d['zon']>1500) {
			if ($d['raamwaskamer']['s']=='Closed'&&$d['rwaskamer']['s']<83) sl('rwaskamer', 83, basename(__FILE__).':'.__LINE__);
			if ($d['raamalex']['s']=='Closed'&&$d['ralex']['s']<83) sl('ralex', 83, basename(__FILE__).':'.__LINE__);
//			if ($d['weg']['s']>1&&$d['rliving']['s']<86&&$d['living_temp']['s']>21) sl('rliving', 86, basename(__FILE__).':'.__LINE__);
		}
	}

	elseif ($d['dag']['m']>220&&$time<strtotime('22:00')) {
		if($d['zon']>1500) {
			if ($d['raamwaskamer']['s']=='Closed'&&$d['ralex']['s']<50) sl('rwaskamer', 83, basename(__FILE__).':'.__LINE__);
			if ($d['raamalex']['s']=='Closed'&&$d['ralex']['s']<83) sl('ralex', 83, basename(__FILE__).':'.__LINE__);
			if ($d['rbureel']['s']<30&&$d['living_temp']['s']>=20) sl('rbureel', 30, basename(__FILE__).':'.__LINE__);
//			if ($d['weg']['s']>1&&$d['rliving']['s']<86&&$d['living_temp']['s']>20) sl('rliving', 86, basename(__FILE__).':'.__LINE__);
		}
	}

	elseif ($d['dag']['s']<$zonelevatie||$time<strtotime('3:00')) {
		if ($d['weg']['s']>0) {
			foreach ($benedenall as $i) {
				if ($d[$i]['s']<88) sl($i, 100, basename(__FILE__).':'.__LINE__);
			}
			foreach ($boven as $i) {
				if ($i=='rwaskamer') {
					if ($d['deurwaskamer']['s']=='Closed'&&$d[$i]['s']<50) sl($i, 83, basename(__FILE__).':'.__LINE__);
				} elseif ($i=='rkamerl') {
					if ($d['weg']['s']>=2&&$d[$i]['s']<50) sl($i, 100, basename(__FILE__).':'.__LINE__);
				} elseif ($i=='rkamerr') {
					if ($d['weg']['s']>=2&&$d[$i]['s']<50) sl($i, 83, basename(__FILE__).':'.__LINE__);
				} else {
					if ($d[$i]['s']<50) sl($i, 83, basename(__FILE__).':'.__LINE__);
				}
			}
		}
	}
}