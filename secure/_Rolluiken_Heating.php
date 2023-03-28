<?php
$boven=array('Rwaskamer','Ralex','RkamerL','RkamerR');
$beneden=array('Rbureel','RkeukenL','RkeukenR');
if ($d['auto']['s']=='On') {
	if (TIME>=$t&&TIME<strtotime('10:00')) {
		if ($d['RkamerL']['s']>0) sl('RkamerL', 0, basename(__FILE__).':'.__LINE__);
		if ($d['RkamerR']['s']>0) sl('RkamerR', 0, basename(__FILE__).':'.__LINE__);
		if ($dag>0) {
			if ($d['Ralex']['s']==0&&$d['Rwaskamer']['s']>0) sl('Rwaskamer', 0, basename(__FILE__).':'.__LINE__);
			if ($d['Ralex']['s']>0&&/*TIME>=strtotime('7:30')&&*/($d['deuralex']['s']=='Open'||$d['alex']['s']>0)) sl('Ralex', 0, basename(__FILE__).':'.__LINE__);
			if ($d['lgtv']['s']=='Off') {
				foreach ($beneden as $i) {
					if ($d[$i]['s']>0) sl($i, 0, basename(__FILE__).':'.__LINE__);
				}
				if ($d['Rliving']['s']>0&&$d['lgtv']['s']=='Off'&&($d['Ralex']['s']<=1||TIME>=strtotime('8:30')||past('deuralex')<3600)) sl('Rliving', 0, basename(__FILE__).':'.__LINE__);
			}
		}
	}
	elseif (TIME>=strtotime('15:00')&&TIME<strtotime('17:00')) {
		if ($d['buiten_temp']['s']<8) {
			foreach (array('waskamer', 'alex') as $i) if ($d['raam'.$i]['s']=='Open'&&$d[$i.'_temp']['s']<12&&$d['R'.$i]['s']<70&&past('raam'.$i)>14400&&past('R'.$i)>14400) sl('R'.$i, 100, basename(__FILE__).':'.__LINE__);
			foreach (array('kamerL', 'kamerR') as $i) if ($d['raamkamer']['s']=='Open'&&$d['kamer_temp']['s']<12&&$d['R'.$i]['s']<70&&past('raamkamer')>14400&&past('R'.$i)>14400) sl('R'.$i, 100, basename(__FILE__).':'.__LINE__);
		}
	}
	elseif (TIME>=strtotime('17:00')&&TIME<strtotime('22:00')) {
		if ($d['zon']['s']==0) {
			foreach (array('Rwaskamer','Ralex','RkamerL','RkamerR') as $i) if ($d[$i]['s']<70&&past($i)>14400) sl($i, 100, basename(__FILE__).':'.__LINE__);
			if ($d['Weg']['s']==1) {
				foreach (array('Rliving','Rbureel','RkeukenL','RkeukenR') as $i) if ($d[$i]['s']<70&&past($i)>14400) sl($i, 100, basename(__FILE__).':'.__LINE__);
			} else {
				if (TIME<$d['civil_twilight']['s']||TIME>$d['civil_twilight']['m']) {
					foreach (array('Rbureel','RkeukenL','RkeukenR') as $i) if ($d[$i]['s']<70&&past($i)>14400) sl($i, 100, basename(__FILE__).':'.__LINE__);
				}
			}
		} elseif ($d['buiten_temp']['s']<10) {
			foreach (array('waskamer', 'alex') as $i) if ($d['raam'.$i]['s']=='Open'&&$d[$i.'_temp']['s']<13&&$d['R'.$i]['s']<71&&past('R'.$i)>14400&&past('raam'.$i)>14400) sl('R'.$i, 100, basename(__FILE__).':'.__LINE__);
			foreach (array('kamerL', 'kamerR') as $i) if ($d['raamkamer']['s']=='Open'&&$d['kamer_temp']['s']<13&&$d['R'.$i]['s']<71&&past('R'.$i)>14400&&past('raamkamer')>14400) sl('R'.$i, 100, basename(__FILE__).':'.__LINE__);
		}
	}
	elseif (TIME>=strtotime('22:00')||TIME<strtotime('3:00')) {
		if ($d['Weg']['s']>0) {
			foreach (array('Rliving','Rbureel','RkeukenL','RkeukenR') as $i) if ($d[$i]['s']<71&&past($i)>7200) sl($i, 100, basename(__FILE__).':'.__LINE__);
			foreach (array('Rwaskamer','Ralex','RkamerL','RkamerR') as $i) if ($d[$i]['s']<71&&past($i)>7200) sl($i, 100, basename(__FILE__).':'.__LINE__);
		}
	}
}
