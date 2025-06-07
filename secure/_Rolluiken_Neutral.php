<?php
$user=basename(__FILE__);
$boven=array('rwaskamer','ralex','rkamerl','rkamerr');
$beneden=array('rbureel','rkeukenl','rkeukenr');
if ($d['auto']['s']=='On') {
	if (($time>=$t||($d['weg']['s']==0&&$d['dag']['s']>0))&&$time<strtotime('8:30')) {
		if ($time>=$t) {
			if ($d['rkamerl']['s']>0) sl('rkamerl', 0, basename(__FILE__).':'.__LINE__);
			if ($d['rkamerr']['s']>0) sl('rkamerr', 0, basename(__FILE__).':'.__LINE__);
			if ($d['ralex']['s']==0&&$d['rwaskamer']['s']>0) sl('rwaskamer', 0, basename(__FILE__).':'.__LINE__);
			if ($d['ralex']['s']>0&&$time>=$t+1800&&($d['deuralex']['s']=='Open'||$d['alex']['s']>0)) sl('ralex', 0, basename(__FILE__).':'.__LINE__);
		}
		if ($d['lgtv']['s']!='On'&&$d['rliving']['s']>0&&($d['ralex']['s']<=1||$time>=strtotime('8:30'))) sl('rliving', 0, basename(__FILE__).':'.__LINE__);
		if ($d['dag']['s']>0) {
			if ($d['lgtv']['s']!='On') {
				foreach ($beneden as $i) {
					if ($d[$i]['s']>0&&past($i)>7200) sl($i, 0, basename(__FILE__).':'.__LINE__);
				}
			}
		}
	}
	elseif ($time>=strtotime('17:00')&&$time<strtotime('22:00')) {
		if ($d['dag']['s']<3&&(($d['kamer_temp']['s']<=18&&$d['alex_temp']['s']<=18)||$d['rkamerr']['s']==100)) {
			foreach ($boven as $i) {
				if ($d[$i]['s']<50) sl($i, 100, basename(__FILE__).':'.__LINE__);
			}
		}
		if ($d['dag']['s']<1) {
			foreach ($beneden as $i) {
				if ($d[$i]['s']<50) sl($i, 100, basename(__FILE__).':'.__LINE__);
			}
		}
	}
	elseif ($time>=strtotime('22:00')||$time<strtotime('3:00')) {
		if ($d['weg']['s']>0) {
			foreach ($benedenall as $i) {
				if ($d[$i]['s']<100) sl($i, 100, basename(__FILE__).':'.__LINE__);
			}
			foreach ($boven as $i) {
				if ($d[$i]['s']<50) sl($i, 100, basename(__FILE__).':'.__LINE__);
			}
		}
	}
}