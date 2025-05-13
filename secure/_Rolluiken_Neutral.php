<?php
$user=basename(__FILE__);
$boven=array('Rwaskamer','Ralex','RkamerL','RkamerR');
$beneden=array('Rbureel','RkeukenL','RkeukenR');
if ($d['auto']['s']=='On') {
	if (($time>=$t||($d['Weg']['s']==0&&$d['dag']>0))&&$time<strtotime('8:30')) {
		if ($time>=$t) {
			if ($d['RkamerL']['s']>0) sl('RkamerL', 0, basename(__FILE__).':'.__LINE__);
			if ($d['RkamerR']['s']>0) sl('RkamerR', 0, basename(__FILE__).':'.__LINE__);
			if ($d['Ralex']['s']==0&&$d['Rwaskamer']['s']>0) sl('Rwaskamer', 0, basename(__FILE__).':'.__LINE__);
			if ($d['Ralex']['s']>0&&$time>=$t+1800&&($d['deuralex']['s']=='Open'||$d['alex']['s']>0)) sl('Ralex', 0, basename(__FILE__).':'.__LINE__);
		}
		if ($d['lgtv']['s']!='On'&&$d['Rliving']['s']>0&&($d['Ralex']['s']<=1||$time>=strtotime('8:30'))) sl('Rliving', 0, basename(__FILE__).':'.__LINE__);
		if ($d['dag']>0) {
			if ($d['lgtv']['s']!='On') {
				foreach ($beneden as $i) {
					if ($d[$i]['s']>0&&past($i)>7200) sl($i, 0, basename(__FILE__).':'.__LINE__);
				}
			}
		}
	}
	elseif ($time>=strtotime('17:00')&&$time<strtotime('22:00')) {
		if ($d['dag']<3&&(($d['kamer_temp']['s']<=18&&$d['alex_temp']['s']<=18)||$d['RkamerR']['s']==100)) {
			foreach ($boven as $i) {
				if ($d[$i]['s']<50) sl($i, 100, basename(__FILE__).':'.__LINE__);
			}
		}
		if ($d['dag']<1) {
			foreach ($beneden as $i) {
				if ($d[$i]['s']<50) sl($i, 100, basename(__FILE__).':'.__LINE__);
			}
		}
	}
	elseif ($time>=strtotime('22:00')||$time<strtotime('3:00')) {
		if ($d['Weg']['s']>0) {
			foreach ($benedenall as $i) {
				if ($d[$i]['s']<100) sl($i, 100, basename(__FILE__).':'.__LINE__);
			}
			foreach ($boven as $i) {
				if ($d[$i]['s']<50) sl($i, 100, basename(__FILE__).':'.__LINE__);
			}
		}
	}
}