<?php
$user=basename(__FILE__);
$zonelevatie=-5.5;
if ($d['auto']['s']=='On') {
	if (($time>=$t||($d['weg']['s']==0&&$d['dag']['s']>$zonelevatie))&&$time<strtotime('8:30')) {
		if ($time>=$t) {
			if ($d['rkamerl']['s']>0) sl('rkamerl', 0, basename(__FILE__).':'.__LINE__);
			if ($d['rkamerr']['s']>0) sl('rkamerr', 0, basename(__FILE__).':'.__LINE__);
			if ($d['ralex']['s']==0&&$d['rwaskamer']['s']>0) sl('rwaskamer', 0, basename(__FILE__).':'.__LINE__);
			if (alexslaapt()==false) sl('ralex', 0, basename(__FILE__).':'.__LINE__);
		}
		if ($d['lgtv']['s']!='On'&&$d['rliving']['s']>0&&($d['ralex']['s']<=1||$time>=strtotime('8:30'))) sl('rliving', 0, basename(__FILE__).':'.__LINE__);
		if ($d['dag']['s']>$zonelevatie) {
			if ($d['lgtv']['s']!='On') {
				foreach (array('rliving','rbureel','rkeukenl','rkeukenr') as $i) {
					if ($d[$i]['s']>0&&past($i)>7200) sl($i, 0, basename(__FILE__).':'.__LINE__);
				}
			}
		}
	}
	elseif ($time>=strtotime('15:00')&&$time<strtotime('16:00')&&$d['buiten_temp']['s']<3) {
		foreach (array('waskamer', 'alex') as $i) if ($d['raam'.$i]['s']=='Open'&&$d[$i.'_temp']['s']<12&&$d['R'.$i]['s']<70) sl('R'.$i, 100, basename(__FILE__).':'.__LINE__);
		foreach (array('kamerL', 'kamerR') as $i) if ($d['raamkamer']['s']=='Open'&&$d['kamer_temp']['s']<12&&$d['R'.$i]['s']<70) sl('R'.$i, 100, basename(__FILE__).':'.__LINE__);
	}
	elseif ($d['heating']['m']>0&&$time>=strtotime('16:00')&&$time<strtotime('18:00')) {
		if ($d['dag']['s']<$zonelevatie&&$d['buiten_temp']['s']<10) {
			foreach (array('rwaskamer','ralex','rkamerl','rkamerr') as $i) if ($d[$i]['s']<100) sl($i, 100, basename(__FILE__).':'.__LINE__);
			if ($d['dag']['s']<0&&$d['buiten_temp']['s']<8) {
				foreach (array('rbureel','rkeukenl','rkeukenr') as $i) if ($d[$i]['s']<100) sl($i, 100, basename(__FILE__).':'.__LINE__);
			}
		}
	}
	elseif ($d['heating']['m']>0&&$time>=strtotime('18:00')&&$time<strtotime('22:00')) {
		if ($d['dag']['s']<0) {
			foreach (array('rwaskamer','ralex','rkamerl','rkamerr') as $i) if ($d[$i]['s']<100) sl($i, 100, basename(__FILE__).':'.__LINE__);
			if ($d['weg']['s']>=1) {
				foreach (array('rliving','rbureel','rkeukenl','rkeukenr') as $i) if ($d[$i]['s']<100) sl($i, 100, basename(__FILE__).':'.__LINE__);
			} else {
				foreach (array('rbureel','rkeukenl','rkeukenr') as $i) if ($d[$i]['s']<100) sl($i, 100, basename(__FILE__).':'.__LINE__);
			}
		} elseif ($d['dag']['s']<0) {
			foreach (array('rwaskamer','ralex','rkamerl','rkamerr') as $i) if ($d[$i]['s']<100) sl($i, 100, basename(__FILE__).':'.__LINE__);
			if ($d['weg']['s']>=1) {
				foreach (array('rliving','rbureel','rkeukenl','rkeukenr') as $i) if ($d[$i]['s']<100) sl($i, 100, basename(__FILE__).':'.__LINE__);
			}
		} elseif ($d['buiten_temp']['s']<3) {
			foreach (array('waskamer', 'alex') as $i) if ($d['raam'.$i]['s']=='Open'&&$d[$i.'_temp']['s']<13&&$d['R'.$i]['s']<71) sl('R'.$i, 100, basename(__FILE__).':'.__LINE__);
			foreach (array('kamerL', 'kamerR') as $i) if ($d['raamkamer']['s']=='Open'&&$d['kamer_temp']['s']<13&&$d['R'.$i]['s']<71) sl('R'.$i, 100, basename(__FILE__).':'.__LINE__);
		}
	}
	elseif ($time>=strtotime('22:00')||$time<strtotime('3:00')) {
		if ($d['weg']['s']>0&&$d['dag']['s']<0) {
			foreach (array('rliving','rbureel','rkeukenl','rkeukenr') as $i) if ($d[$i]['s']<100) sl($i, 100, basename(__FILE__).':'.__LINE__);
			foreach (array('rwaskamer','ralex','rkamerl','rkamerr') as $i) if ($d[$i]['s']<100) sl($i, 100, basename(__FILE__).':'.__LINE__);
		} elseif ($d['dag']['s']<0) {
			foreach (array('rbureel','rkeukenl','rkeukenr') as $i) if ($d[$i]['s']<100) sl($i, 100, basename(__FILE__).':'.__LINE__);
			foreach (array('rwaskamer','ralex','rkamerl','rkamerr') as $i) if ($d[$i]['s']<100) sl($i, 100, basename(__FILE__).':'.__LINE__);
		}
	}
}
