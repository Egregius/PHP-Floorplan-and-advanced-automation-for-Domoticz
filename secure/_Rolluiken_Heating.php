<?php
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
		if ($d['Media']['s']=='Off'&&$d['Rliving']['s']>0&&($d['Ralex']['s']<=1||$time>=strtotime('8:30'))) sl('Rliving', 0, basename(__FILE__).':'.__LINE__);
		if ($d['dag']>0) {
			if ($d['Media']['s']=='Off') {
				foreach ($beneden as $i) {
					if ($d[$i]['s']>0&&past($i)>7200) sl($i, 0, basename(__FILE__).':'.__LINE__);
				}
			}
		}
	}
	elseif ($time>=strtotime('15:00')&&$time<strtotime('18:00')&&$d['buiten_temp']['s']<3) {
		foreach (array('waskamer', 'alex') as $i) if ($d['raam'.$i]['s']=='Open'&&$d[$i.'_temp']['s']<12&&$d['R'.$i]['s']<70) sl('R'.$i, 100, basename(__FILE__).':'.__LINE__);
		foreach (array('kamerL', 'kamerR') as $i) if ($d['raamkamer']['s']=='Open'&&$d['kamer_temp']['s']<12&&$d['R'.$i]['s']<70) sl('R'.$i, 100, basename(__FILE__).':'.__LINE__);
	}
	elseif ($time>=strtotime('18:00')&&$time<strtotime('22:00')) {
		if ($d['dag']==0) {
			foreach (array('Rwaskamer','Ralex','RkamerL','RkamerR') as $i) if ($d[$i]['s']<100) sl($i, 100, basename(__FILE__).':'.__LINE__);
			if ($d['Weg']['s']>=1) {
				foreach (array('Rliving','Rbureel','RkeukenL','RkeukenR') as $i) if ($d[$i]['s']<100) sl($i, 100, basename(__FILE__).':'.__LINE__);
			} else {
				foreach (array('Rbureel','RkeukenL','RkeukenR') as $i) if ($d[$i]['s']<100) sl($i, 100, basename(__FILE__).':'.__LINE__);
			}
		} elseif ($d['buiten_temp']['s']<3) {
			foreach (array('waskamer', 'alex') as $i) if ($d['raam'.$i]['s']=='Open'&&$d[$i.'_temp']['s']<13&&$d['R'.$i]['s']<71) sl('R'.$i, 100, basename(__FILE__).':'.__LINE__);
			foreach (array('kamerL', 'kamerR') as $i) if ($d['raamkamer']['s']=='Open'&&$d['kamer_temp']['s']<13&&$d['R'.$i]['s']<71) sl('R'.$i, 100, basename(__FILE__).':'.__LINE__);
		}
	}
	elseif ($time>=strtotime('22:00')||$time<strtotime('3:00')) {
		if ($d['Weg']['s']>0&&$d['dag']==0) {
			foreach (array('Rliving','Rbureel','RkeukenL','RkeukenR') as $i) if ($d[$i]['s']<100) sl($i, 100, basename(__FILE__).':'.__LINE__);
			foreach (array('Rwaskamer','Ralex','RkamerL','RkamerR') as $i) if ($d[$i]['s']<100) sl($i, 100, basename(__FILE__).':'.__LINE__);
		} elseif ($d['dag']==0) {
			foreach (array('Rbureel','RkeukenL','RkeukenR') as $i) if ($d[$i]['s']<100) sl($i, 100, basename(__FILE__).':'.__LINE__);
			foreach (array('Rwaskamer','Ralex','RkamerL','RkamerR') as $i) if ($d[$i]['s']<100) sl($i, 100, basename(__FILE__).':'.__LINE__);
		}
	}
}
